<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    disastr
 */

/**
 * Hook class.
 */
class Hook_ecommerce_disastr
{
    /**
     * Get the overall categorisation for the products handled by this eCommerce hook.
     *
     * @return ?array A map of product categorisation details (null: disabled)
     */
    public function get_product_category() : ?array
    {
        if (!addon_installed('disastr')) {
            return null;
        }

        require_lang('disastr');

        return [
            'category_name' => do_lang('DISEASES_CURES_IMMUNISATIONS_TITLE'),
            'category_description' => do_lang_tempcode('DISEASES_DESCRIPTION'),
            'category_image_url' => find_theme_image('icons/spare/disaster'),
        ];
    }

    /**
     * Get the products handled by this eCommerce hook.
     *
     * IMPORTANT NOTE TO PROGRAMMERS: This function may depend only on the database, and not on get_member() or any GET/POST values.
     *  Such dependencies will break IPN, which works via a Guest and no dependable environment variables. It would also break manual transactions from the Admin Zone.
     *
     * @param  ?ID_TEXT $search Product being searched for (passed by reference as it may be modified for special cases) (null: none)
     * @return array A map of product name to list of product details
     */
    public function get_products(?string &$search = null) : array
    {
        require_lang('disastr');

        $products = [];

        $rows = $GLOBALS['SITE_DB']->query_select('diseases', ['*'], [], 'ORDER BY name');
        foreach ($rows as $disease) {
            $image_url = $disease['image'];
            if ($image_url != '') {
                if (url_is_local($image_url)) {
                    $image_url = get_custom_base_url() . '/' . $image_url;
                }
            }

            $products['CURE_' . strval($disease['id'])] = [
                'item_name' => do_lang('__CURE', $disease['name'], $disease['cure']),
                'item_description' => do_lang_tempcode('_CURE', escape_html($disease['name']), escape_html($disease['cure'])),
                'item_image_url' => $image_url,

                'type' => PRODUCT_PURCHASE,
                'type_special_details' => [],

                'price' => null,
                'currency' => get_option('currency'),
                'price_points' => $disease['cure_price'],
                'discount_points__num_points' => null,
                'discount_points__price_reduction' => null,
                'discount_points__percentile' => null,

                'tax_code' => '0.0',
                'shipping_cost' => 0.00,
                'product_weight' => null,
                'product_length' => null,
                'product_width' => null,
                'product_height' => null,
                'needs_shipping_address' => false,
            ];

            $products['IMMUNISATION_' . strval($disease['id'])] = [
                'item_name' => do_lang('__IMMUNISATION', $disease['name'], $disease['immunisation']),
                'item_description' => do_lang_tempcode('_IMMUNISATION', escape_html($disease['name']), escape_html($disease['immunisation'])),
                'item_image_url' => $image_url,

                'type' => PRODUCT_PURCHASE,
                'type_special_details' => [],

                'price' => null,
                'currency' => get_option('currency'),
                'price_points' => $disease['immunisation_price'],
                'discount_points__num_points' => null,
                'discount_points__price_reduction' => null,

                'tax_code' => '0.0',
                'shipping_cost' => 0.00,
                'product_weight' => null,
                'product_length' => null,
                'product_width' => null,
                'product_height' => null,
                'needs_shipping_address' => false,
            ];
        }

        return $products;
    }

    /**
     * Check whether the product codename is available for purchase by the member.
     *
     * @param  ID_TEXT $type_code The product codename
     * @param  MEMBER $member_id The member we are checking against
     * @param  integer $req_quantity The number required
     * @param  boolean $must_be_listed Whether the product must be available for public listing
     * @return integer The availability code (a ECOMMERCE_PRODUCT_* constant)
     */
    public function is_available(string $type_code, int $member_id, int $req_quantity = 1, bool $must_be_listed = false) : int
    {
        if (!addon_installed('disastr')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        if (!addon_installed('points')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        if (get_forum_type() != 'cns') {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        if (is_guest($member_id)) {
            return ECOMMERCE_PRODUCT_NO_GUESTS;
        }

        $matches = [];
        if (preg_match('#^(CURE|IMMUNISATION)_(\d+)$#', $type_code, $matches) == 0) {
            fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        $disease_id = intval($matches[2]);

        $rows = $GLOBALS['SITE_DB']->query_select('diseases', ['*'], ['id' => $disease_id], '', 1);
        if (!array_key_exists(0, $rows)) {
            return ECOMMERCE_PRODUCT_MISSING;
        }
        $disease = $rows[0];

        $member_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', ['*'], ['member_id' => $member_id, 'disease_id' => $disease['id']], '', 1);
        if (array_key_exists(0, $member_rows)) {
            $member_row = $member_rows[0];
        } else {
            $member_row = ['sick' => 0, 'cure' => 0, 'immunisation' => 0];
        }

        if ($member_row['cure'] == 1) {
            return ECOMMERCE_PRODUCT_ALREADY_HAS;
        }

        if ($member_row['immunisation'] == 1) {
            return ECOMMERCE_PRODUCT_ALREADY_HAS;
        }

        if ($matches[1] == 'CURE') {
            if ($member_row['sick'] == 0) {
                return ECOMMERCE_PRODUCT_DISABLED;
            }
        }

        if ($matches[1] == 'IMMUNISATION') {
            if ($member_row['sick'] == 1) {
                return ECOMMERCE_PRODUCT_DISABLED;
            }
        }

        return ECOMMERCE_PRODUCT_AVAILABLE;
    }

    /**
     * Get fields that need to be filled in in the purchasing module.
     *
     * @param  ID_TEXT $type_code The product codename
     * @param  boolean $from_admin Whether this is being called from the Admin Zone. If so, optionally different fields may be used, including a purchase_id field for direct purchase ID input.
     * @return array A triple: The fields (use null for none), Hidden fields (use null for none), The text (use null for none), array of JavaScript function calls
     */
    public function get_needed_fields(string $type_code, bool $from_admin = false) : array
    {
        return [null, null, null, []];
    }

    /**
     * Handling of a product purchase change state.
     *
     * @param  ID_TEXT $type_code The product codename
     * @param  ID_TEXT $purchase_id The purchase ID
     * @param  array $details Details of the product, with added keys: TXN_ID, STATUS, ORDER_STATUS
     * @return boolean Whether the product was automatically dispatched (if not then hopefully this function sent a staff notification)
     */
    public function actualiser(string $type_code, string $purchase_id, array $details) : bool
    {
        if ($details['STATUS'] != 'Completed') {
            return false;
        }

        require_lang('disastr');

        $matches = [];
        if (preg_match('#^(CURE|IMMUNISATION)_(\d+)$#', $type_code, $matches) == 0) {
            fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        $disease_id = intval($matches[2]);

        $member_id = intval($purchase_id);

        $rows = $GLOBALS['SITE_DB']->query_select('diseases', ['*'], ['id' => $disease_id], '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        $disease_row = $rows[0];

        $member_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', ['*'], ['member_id' => $member_id, 'disease_id' => $disease_id], '', 1);

        $map = [
            'member_id' => $member_id,
            'disease_id' => $disease_id,
        ];
        if ($matches[1] == 'CURE') {
            $map['cure'] = 1;
            $map['sick'] = 0;
            if (!array_key_exists(0, $member_rows)) {
                $map['immunisation'] = 0; // This code should not actually be runnable
            }
        } elseif ($matches[1] == 'IMMUNISATION') {
            $map['immunisation'] = 1;
            if (!array_key_exists(0, $member_rows)) {
                $map['cure'] = 0;
                $map['sick'] = 0;
            }
        }

        $member_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', ['*'], ['member_id' => $member_id, 'disease_id' => $disease_id], '', 1);
        if (array_key_exists(0, $member_rows)) {
            $GLOBALS['SITE_DB']->query_update('members_diseases', $map, ['member_id' => $member_id, 'disease_id' => $disease_id], '', 1);
        } else {
            $GLOBALS['SITE_DB']->query_insert('members_diseases', $map);
        }

        if ($matches[1] == 'CURE') {
            $GLOBALS['SITE_DB']->query_insert('ecom_sales', ['date_and_time' => time(), 'member_id' => $member_id, 'details' => do_lang('CURE', null, null, null, get_site_default_lang()), 'details2' => $disease_row['cure'], 'txn_id' => $details['TXN_ID']]);
        } elseif ($matches[1] == 'IMMUNISATION') {
            $GLOBALS['SITE_DB']->query_insert('ecom_sales', ['date_and_time' => time(), 'member_id' => $member_id, 'details' => do_lang('IMMUNISATION', null, null, null, get_site_default_lang()), 'details2' => $disease_row['immunisation'], 'txn_id' => $details['TXN_ID']]);
        }

        // There's an urgency, so show an instant message (plus buying via points, so will definitely be seen)
        if ($matches[1] == 'IMMUNISATION') {
            $result = do_lang_tempcode('IMMUNISATION_CONGRATULATIONS');
        } else {
            $result = do_lang_tempcode('CURE_CONGRATULATIONS');
        }
        global $ECOMMERCE_SPECIAL_SUCCESS_MESSAGE;
        $ECOMMERCE_SPECIAL_SUCCESS_MESSAGE = $result;

        return true;
    }

    /**
     * Get the member who made the purchase.
     *
     * @param  ID_TEXT $type_code The product codename
     * @param  ID_TEXT $purchase_id The purchase ID
     * @return ?MEMBER The member ID (null: none)
     */
    public function member_for(string $type_code, string $purchase_id) : ?int
    {
        return intval($purchase_id);
    }
}

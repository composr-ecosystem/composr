<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    classified_ads
 */

/**
 * Hook class.
 */
class Hook_ecommerce_classifieds
{
    /**
     * Get the overall categorisation for the products handled by this eCommerce hook.
     *
     * @return ?array A map of product categorisation details (null: disabled)
     */
    public function get_product_category() : ?array
    {
        if (!addon_installed('classified_ads')) {
            return null;
        }

        require_lang('classifieds');

        return [
            'category_name' => do_lang('CLASSIFIEDS'),
            'category_description' => do_lang_tempcode('CLASSIFIED_ADVERT_BUY_DESCRIPTION'),
            'category_image_url' => find_theme_image('icons/spare/classifieds'),
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
        require_lang('classifieds');

        $num_products_for_sale = $GLOBALS['SITE_DB']->query_select_value('catalogue_entries e JOIN ' . get_table_prefix() . 'ecom_classifieds_prices c ON c.c_catalogue_name=e.c_name', 'COUNT(*)');
        if ($num_products_for_sale == 0) {
            return [];
        }

        $prices = $GLOBALS['SITE_DB']->query_select('ecom_classifieds_prices', ['*'], [], 'ORDER BY c_price');

        $products = [];
        foreach ($prices as $price) {
            if ($price['c_price'] != 0.0) {
                $products['CLASSIFIEDS_ADVERT_' . strval($price['id'])] = automatic_discount_calculation([
                    'item_name' => do_lang('CLASSIFIED_ADVERT_BUY', get_translated_text($price['c_label'])),
                    'item_description' => new Tempcode(),
                    'item_image_url' => '',

                    'type' => PRODUCT_PURCHASE,
                    'type_special_details' => [],

                    'price' => $price['c_price'],
                    'currency' => get_option('currency'),
                    'price_points' => null,
                    'discount_points__num_points' => null,
                    'discount_points__price_reduction' => null,
                    'discount_points__percentile' => null,

                    'tax_code' => get_option('classifieds_tax_code'),
                    'shipping_cost' => 0.00,
                    'product_weight' => null,
                    'product_length' => null,
                    'product_width' => null,
                    'product_height' => null,
                    'needs_shipping_address' => false,
                ]);
            }
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
        if (!addon_installed('classified_ads')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        if (!addon_installed('catalogues')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }
        if (!addon_installed('ecommerce')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        $purchase_id = get_param_string('id', null);

        if (($must_be_listed) && ($purchase_id === null)) {
            return ECOMMERCE_PRODUCT_DISABLED; // Can't do from the 'choose' screen, must be linked from classifieds module
        } else {
            $entry_id = intval($purchase_id);
            $validated = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_entries', 'ce_validated', ['id' => $entry_id]);

            if ($validated === null) {
                return ECOMMERCE_PRODUCT_MISSING;
            }

            if ($validated == 1) {
                return ECOMMERCE_PRODUCT_ALREADY_HAS;
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
        $fields = null;

        if ($from_admin) {
            require_lang('classifieds');
            require_code('catalogues');

            $fields = new Tempcode();

            $list = new Tempcode();
            $rows = $GLOBALS['SITE_DB']->query_select('catalogue_entries e JOIN ' . get_table_prefix() . 'ecom_classifieds_prices c ON c.c_catalogue_name=e.c_name', ['e.*'], [], 'GROUP BY e.id ORDER BY ce_add_date DESC');
            foreach ($rows as $row) {
                $data_map = get_catalogue_entry_map($row, null, 'CATEGORY', 'DEFAULT', null, null, [0]);
                $ad_title = $data_map['FIELD_0'];

                $username = $GLOBALS['FORUM_DRIVER']->get_username($row['ce_submitter']);
                $list->attach(form_input_list_entry(strval($row['id']), get_param_integer('id', null) === $row['id'], do_lang('CLASSIFIED_OF', strval($row['id']), $username, $ad_title)));
            }
            $fields->attach(form_input_list(do_lang_tempcode('ENTRY'), '', 'purchase_id', $list));
        }

        ecommerce_attach_memo_field_if_needed($fields);

        return [null, null, null, []];
    }

    /**
     * Get the filled in fields and do something with them.
     * May also be called from Admin Zone to get a default purchase ID (i.e. when there's no post context).
     *
     * @param  ID_TEXT $type_code The product codename
     * @param  boolean $from_admin Whether this is being called from the Admin Zone. If so, optionally different fields may be used, including a purchase_id field for direct purchase ID input.
     * @return array A pair: The purchase ID, a confirmation box to show (null for no specific confirmation)
     */
    public function process_needed_fields(string $type_code, bool $from_admin = false) : array
    {
        if (($from_admin) && (post_param_string('purchase_id', null) !== null)) {
            return [post_param_string('purchase_id'), null];
        }

        $entry_id = get_param_integer('id', null); // The catalogue entry being paid for
        if ($entry_id === null) {
            if ($from_admin) {
                return ['', null]; // Default is blank
            }

            warn_exit(do_lang_tempcode('MISSING_RESOURCE')); // Can't do from the 'choose' screen, must be linked from classifieds module
        }

        $matches = [];
        if (preg_match('#^CLASSIFIEDS_ADVERT_(\d+)$#', $type_code, $matches) != 0) {
            $entry_catalogue_name = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_entries', 'c_name', ['id' => $entry_id]);
            if ($entry_catalogue_name === null) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }

            // Check this is a valid purchase for the product
            $classified_price_catalogue_name = $GLOBALS['SITE_DB']->query_select_value_if_there('ecom_classifieds_prices', 'c_catalogue_name', ['id' => intval($matches[1])]);
            if ($classified_price_catalogue_name != $entry_catalogue_name) {
                warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('0966c92feac451e5a8805a3278952900')));
            }
        } else { // Bizarre if this happened
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('a68a245bfef6563698d679076aefc560')));
        }

        return [strval($entry_id), null];
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

        $classified_type_id = intval(preg_replace('#^CLASSIFIEDS_ADVERT_#', '', $type_code));

        $entry_id = intval($purchase_id);

        $member_id = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_entries', 'ce_submitter', ['id' => $entry_id]);

        $days = $GLOBALS['SITE_DB']->query_select_value_if_there('ecom_classifieds_prices', 'c_days', ['id' => $classified_type_id]);

        // Make validated, bump up timer
        $time = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_entries', 'ce_last_moved', ['id' => $entry_id]);
        if ($time !== null) {
            $time += $days * 60 * 60 * 24;
            $GLOBALS['SITE_DB']->query_update('catalogue_entries', ['ce_validated' => 1, 'ce_last_moved' => $time], ['id' => $entry_id], '', 1);
            delete_cache_entry('main_cc_embed');
            delete_cache_entry('main_recent_cc_entries');
            require_code('catalogues2');
            $cc_id = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_entries', 'cc_id', ['id' => $entry_id]);
            calculate_category_child_count_cache($cc_id);
        }

        $GLOBALS['SITE_DB']->query_insert('ecom_sales', ['date_and_time' => time(), 'member_id' => $member_id, 'details' => $details['item_name'], 'details2' => strval($entry_id), 'txn_id' => $details['TXN_ID']]);

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
        $entry_id = intval($purchase_id);
        return $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_entries', 'ce_submitter', ['id' => $entry_id]);
    }
}

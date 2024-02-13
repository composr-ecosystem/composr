<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_ecommerce_support_credits
{
    /**
     * Get the overall categorisation for the products handled by this eCommerce hook.
     *
     * @return ?array A map of product categorisation details (null: disabled)
     */
    public function get_product_category() : ?array
    {
        if (!addon_installed('composr_homesite_support_credits')) {
            return null;
        }

        require_lang('customers');

        return [
            'category_name' => do_lang('CREDITS'),
            'category_description' => do_lang_tempcode('CUSTOMER_SUPPORT_CREDITS_DESCRIPTION'),
            'category_image_url' => find_theme_image('icons/help'),
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
        require_lang('customers');

        $products = [];
        $bundles = [1, 2, 3, 4, 5, 6, 9, 20, 25, 35, 50, 90, 180, 550];
        foreach ($bundles as $bundle) {
            $products[strval($bundle) . '_CREDITS'] = [
                'item_name' => do_lang('CUSTOMER_SUPPORT_CREDITS', integer_format($bundle, 0)),
                'item_description' => new Tempcode(),
                'item_image_url' => '',

                'type' => PRODUCT_PURCHASE,
                'type_special_details' => null,

                'price' => $bundle * floatval(get_option('support_credit_price')),
                'currency' => get_option('currency'),
                'price_points' => null,
                'discount_points__num_points' => null,
                'discount_points__price_reduction' => null,

                'tax_code' => get_option('support_credit_tax_code'),
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
        if (!addon_installed('composr_homesite_support_credits')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        if (!addon_installed('tickets')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }
        if (!addon_installed('stats')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }
        if (!addon_installed('points')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        if (strpos(get_db_type(), 'mysql') === false) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        if (get_forum_type() != 'cns') {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        return ($member_id != $GLOBALS['FORUM_DRIVER']->get_guest_id()) ? ECOMMERCE_PRODUCT_AVAILABLE : ECOMMERCE_PRODUCT_NO_GUESTS;
    }

    /**
     * Get the terms and conditions for use in the purchasing module.
     *
     * @param  string $type_code The product in question
     * @return string The message
     */
    public function get_terms(string $type_code) : string
    {
        require_code('textfiles');
        return read_text_file('support_credits_terms', 'EN');
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
        if (!$from_admin) {
            return [null, null, []];
        }

        require_lang('customers');

        // Check if we've already been passed a member ID and use it to pre-populate the field
        $member_id = get_param_integer('member_id', null);
        $username = $GLOBALS['FORUM_DRIVER']->get_username(($member_id === null) ? get_member() : $member_id, false, USERNAME_DEFAULT_BLANK);

        $fields = new Tempcode();
        $fields->attach(form_input_username(do_lang('USERNAME'), do_lang('USERNAME_CREDITS_FOR'), 'member_username', $username, true));

        ecommerce_attach_memo_field_if_needed($fields);

        return [$fields, null, null, []];
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
        $product_array = explode('_', $type_code, 2);
        $num_credits = intval($product_array[0]);
        if ($num_credits == 0) {
            return [null, null];
        }

        $manual = 0;

        $member_id = get_member();

        // Allow admins to specify the member who should receive the credits with the field in get_needed_fields
        if ($from_admin) {
            $url_id = post_param_integer('member_id', null);
            if ($url_id !== null) {
                $manual = 1;
                $member_id = $url_id;
            } else {
                $username = post_param_string('member_username', null, INPUT_FILTER_POST_IDENTIFIER);
                if ($username !== null) {
                    $manual = 1;
                    $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
                    if ($member_id === null) {
                        $member_id = get_member();
                    }
                }
            }
        }

        $id = strval($GLOBALS['SITE_DB']->query_insert('credit_purchases', ['member_id' => $member_id, 'date_and_time' => time(), 'num_credits' => $num_credits, 'is_manual' => $manual, 'purchase_validated' => 0], true));

        return [$id, null];
    }

    /**
     * Handling of a product purchase change state.
     *
     * @param  ID_TEXT $type_code The product codename
     * @param  ID_TEXT $id The purchase ID
     * @param  array $details Details of the product, with added keys: TXN_ID, STATUS, ORDER_STATUS
     * @return boolean Whether the product was automatically dispatched (if not then hopefully this function sent a staff notification)
     */
    public function actualiser(string $type_code, string $id, array $details) : bool
    {
        if ($details['STATUS'] != 'Completed') {
            return false;
        }

        $row = $GLOBALS['SITE_DB']->query_select('credit_purchases', ['member_id', 'num_credits'], ['id' => intval($id)], '', 1);
        if (count($row) != 1) {
            return false;
        }
        $member_id = $row[0]['member_id'];
        if ($member_id === null) {
            return false;
        }
        $num_credits = $row[0]['num_credits'];

        require_code('mantis');
        $cpf_id = get_credits_profile_field_id();
        if ($cpf_id === null) {
            return false;
        }

        // Increment the number of credits this customer has
        require_code('cns_members_action2');
        $fields = cns_get_custom_field_mappings($member_id);
        if ($fields['field_' . strval($cpf_id)] === null) {
            $current_credits = 0;
        } else {
            $current_credits = intval($fields['field_' . strval($cpf_id)]);
        }
        cns_set_custom_field($member_id, $cpf_id, $current_credits + $num_credits);

        // Update the row in the credit_purchases table
        $GLOBALS['SITE_DB']->query_update('credit_purchases', ['purchase_validated' => 1], ['id' => intval($id)]);

        // Award points for the credits
        if (addon_installed('points') && ($num_credits > 0)) {
            $points_support_credits = intval(get_option('points_support_credits'));
            if ($points_support_credits > 0) {
                require_code('points2');
                points_credit_member($member_id, do_lang('CREDITS'), ($num_credits * $points_support_credits), 0, true, 0, 'support_credits', 'purchase', strval($num_credits));
            }
        }

        $GLOBALS['SITE_DB']->query_insert('ecom_sales', ['date_and_time' => time(), 'member_id' => $member_id, 'details' => do_lang('CREDITS', null, null, null, get_site_default_lang()), 'details2' => strval($num_credits), 'txn_id' => $details['TXN_ID']]);

        return true;
    }

    /**
     * Get the member who made the purchase.
     *
     * @param  ID_TEXT $type_code The product codename
     * @param  ID_TEXT $id The purchase ID
     * @return ?MEMBER The member ID (null: none)
     */
    public function member_for(string $type_code, string $id) : ?int
    {
        return $GLOBALS['SITE_DB']->query_select_value_if_there('credit_purchases', 'member_id', ['id' => intval($id)]);
    }
}

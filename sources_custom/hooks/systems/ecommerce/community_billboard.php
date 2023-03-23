<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_ecommerce_community_billboard
{
    /**
     * Get the overall categorisation for the products handled by this eCommerce hook.
     *
     * @return ?array A map of product categorisation details (null: disabled)
     */
    public function get_product_category() : ?array
    {
        if (!addon_installed('community_billboard')) {
            return null;
        }

        require_lang('community_billboard');

        return [
            'category_name' => do_lang('COMMUNITY_BILLBOARD_MESSAGE'),
            'category_description' => do_lang_tempcode('COMMUNITY_BILLBOARD_MESSAGE_DESCRIPTION'),
            'category_image_url' => find_theme_image('icons/menu/adminzone/audit/community_billboard'),
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
        require_lang('community_billboard');

        $products = [];

        $price_points = get_option('community_billboard_price_points');

        foreach ([1, 3, 5, 10, 20, 31, 90] as $days) {
            $products['COMMUNITY_BILLBOARD_' . strval($days)] = automatic_discount_calculation([
                'item_name' => do_lang('COMMUNITY_BILLBOARD_MESSAGE_FOR_DAYS', integer_format($days)),
                'item_description' => new Tempcode(),
                'item_image_url' => '',

                'type' => PRODUCT_PURCHASE,
                'type_special_details' => [],

                'price' => (get_option('community_billboard_price') == '') ? null : (floatval(get_option('community_billboard_price')) * $days),
                'currency' => get_option('currency'),
                'price_points' => ($price_points == '') ? null : (intval($price_points) * $days),
                'discount_points__num_points' => null,
                'discount_points__price_reduction' => null,

                'tax_code' => tax_multiplier(get_option('community_billboard_tax_code'), (float)$days),
                'shipping_cost' => 0.00,
                'product_weight' => null,
                'product_length' => null,
                'product_width' => null,
                'product_height' => null,
                'needs_shipping_address' => false,
            ]);
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
        if (!addon_installed('community_billboard')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        if (!addon_installed('ecommerce')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        if (get_option('is_on_community_billboard_buy') == '0') {
            return ECOMMERCE_PRODUCT_DISABLED;
        }

        if (is_guest($member_id)) {
            return ECOMMERCE_PRODUCT_NO_GUESTS;
        }

        return ECOMMERCE_PRODUCT_AVAILABLE;
    }

    /**
     * Get the message for use in the purchasing module.
     *
     * @param  string $type_code The product in question
     * @return Tempcode The message
     */
    public function get_message(string $type_code) : object
    {
        require_lang('community_billboard');

        $_queue = $GLOBALS['SITE_DB']->query_select_value('community_billboard', 'SUM(days) AS days', ['activation_time' => null]);
        $queue = @intval($_queue);

        $days = intval(preg_replace('#^COMMUNITY_BILLBOARD_#', '', $type_code));

        return do_template('ECOM_PRODUCT_COMMUNITY_BILLBOARD', [
            '_GUID' => '92d51c5b87745c31397d9165595262d3',
            '_QUEUE' => strval($queue),
            'QUEUE' => integer_format($queue),
            '_DAYS' => strval($days),
            'DAYS' => integer_format($days),
        ]);
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
        require_lang('community_billboard');

        $fields = new Tempcode();
        $fields->attach(form_input_line_comcode(do_lang_tempcode('MESSAGE'), do_lang_tempcode('MESSAGE_DESCRIPTION'), 'message', '', true));

        ecommerce_attach_memo_field_if_needed($fields);

        return [$fields, null, do_lang_tempcode('COMMUNITY_BILLBOARD_GUIDE'), []];
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
        $member_id = get_member();
        $message = post_param_string('message', '');

        $e_details = json_encode([$member_id, $message]);

        $purchase_id = strval($GLOBALS['SITE_DB']->query_insert('ecom_sales_expecting', ['e_details' => $e_details, 'e_time' => time()], true));
        return [$purchase_id, null];
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

        require_lang('community_billboard');

        $days = intval(preg_replace('#^COMMUNITY_BILLBOARD_#', '', $type_code));

        $e_details = $GLOBALS['SITE_DB']->query_select_value('ecom_sales_expecting', 'e_details', ['id' => intval($purchase_id)]);
        list($member_id, $message) = json_decode($e_details);

        // Add this to the database
        $map = [
            'notes' => '',
            'activation_time' => null,
            'active_now' => 0,
            'member_id' => $member_id,
            'days' => $days,
            'order_time' => time(),
        ];
        $map += insert_lang_comcode('the_message', $message, 2);
        $GLOBALS['SITE_DB']->query_insert('community_billboard', $map);

        $GLOBALS['SITE_DB']->query_insert('ecom_sales', ['date_and_time' => time(), 'member_id' => $member_id, 'details' => do_lang('COMMUNITY_BILLBOARD_MESSAGE', null, null, null, get_site_default_lang()), 'details2' => strval($days), 'txn_id' => $details['TXN_ID']]);

        // Notification to staff
        require_code('notifications');
        $_url = build_url(['page' => 'admin_community_billboard'], get_module_zone('admin_community_billboard'), [], false, false, true);
        $manage_url = $_url->evaluate();
        $subject = do_lang('SUBJECT_COMMUNITY_BILLBOARD_TEXT', null, null, null, get_site_default_lang());
        $body = do_notification_lang('MAIL_COMMUNITY_BILLBOARD_TEXT', $message, comcode_escape($manage_url), null, get_site_default_lang());
        dispatch_notification('ecom_product_request_community_billboard', null, $subject, $body);

        return false;
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
        $e_details = $GLOBALS['SITE_DB']->query_select_value('ecom_sales_expecting', 'e_details', ['id' => intval($purchase_id)]);
        list($member_id) = json_decode($e_details);
        return $member_id;
    }
}

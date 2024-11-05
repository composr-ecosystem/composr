<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    giftr
 */

/**
 * Hook class.
 */
class Hook_ecommerce_giftr
{
    /**
     * Get the overall categorisation for the products handled by this eCommerce hook.
     *
     * @return ?array A map of product categorisation details (null: disabled)
     */
    public function get_product_category() : ?array
    {
        if (!addon_installed('giftr')) {
            return null;
        }

        require_lang('giftr');

        return [
            'category_name' => do_lang('GIFTR_TITLE'),
            'category_description' => do_lang_tempcode('GIFTS_DESCRIPTION'),
            'category_image_url' => find_theme_image('icons/spare/gifts'),
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
        require_lang('giftr');

        $products = [];

        $map = ['enabled' => 1];

        $max_rows = $GLOBALS['SITE_DB']->query_select_value('giftr', 'COUNT(*)', $map);

        $rows = $GLOBALS['SITE_DB']->query_select('giftr g', ['*', '(SELECT COUNT(*) FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'members_gifts m WHERE m.gift_id=g.id) AS popularity'], $map);
        sort_maps_by($rows, '!popularity');
        $gifts = [];
        foreach ($rows as $gift) {
            $image_url = $gift['image'];
            if ($image_url != '') {
                if (url_is_local($image_url)) {
                    $image_url = get_custom_base_url() . '/' . $image_url;
                }
            }

            $products['GIFTR_' . strval($gift['id'])] = [
                'item_name' => do_lang('_GIFT', $gift['name']),
                'item_description' => do_lang_tempcode('GIFT_DESCRIPTION', escape_html($gift['category']), escape_html(integer_format($gift['popularity'], 0)), escape_html($gift['name'])),
                'item_image_url' => $image_url,

                'type' => PRODUCT_PURCHASE,
                'type_special_details' => [],

                'price' => null,
                'currency' => get_option('currency'),
                'price_points' => $gift['price'],
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
        if (!addon_installed('ecommerce')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }
        if (!addon_installed('points')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }
        if (!addon_installed('giftr')) {
            return ECOMMERCE_PRODUCT_INTERNAL_ERROR;
        }

        $gift_id = intval(preg_replace('#^GIFTR_#', '', $type_code));
        $rows = $GLOBALS['SITE_DB']->query_select('giftr', ['*'], ['id' => $gift_id], '', 1);
        if (!array_key_exists(0, $rows)) {
            return ECOMMERCE_PRODUCT_MISSING;
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
        require_lang('giftr');

        $fields = new Tempcode();
        $fields->attach(form_input_username(do_lang_tempcode('TO_USERNAME'), do_lang_tempcode('DESCRIPTION_MEMBER_TO_GIVE'), 'username', get_param_string('username', '', INPUT_FILTER_GET_IDENTIFIER), true));
        $fields->attach(form_input_text(do_lang_tempcode('MESSAGE'), do_lang_tempcode('DESCRIPTION_GIFT_MESSAGE'), 'gift_message', '', true, false));
        $fields->attach(form_input_tick(do_lang_tempcode('ANON'), do_lang_tempcode('DESCRIPTION_ANONYMOUS'), 'anonymous', false));

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
        $to_member = post_param_string('username', $from_admin ? '' : false, INPUT_FILTER_POST_IDENTIFIER);
        $gift_message = post_param_string('gift_message', '');
        $anonymous = post_param_integer('anonymous', 0);

        if ($to_member == '') {
            return ['', null]; // Default is blank
        }

        $e_details = json_encode([get_member(), $to_member, $gift_message, $anonymous]);
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

        require_lang('giftr');

        $gift_id = intval(preg_replace('#^GIFTR_#', '', $type_code));

        $e_details = $GLOBALS['SITE_DB']->query_select_value('ecom_sales_expecting', 'e_details', ['id' => intval($purchase_id)]);
        list($from_member_id, $to_member, $gift_message, $anonymous) = json_decode($e_details);

        $member_rows = $GLOBALS['FORUM_DB']->query_select('f_members', ['*'], ['m_username' => $to_member], '', 1);
        if (array_key_exists(0, $member_rows)) {
            $member_row = $member_rows[0];
            $to_member_id = $member_row['id'];

            $gift_rows = $GLOBALS['SITE_DB']->query_select('giftr', ['*'], ['id' => $gift_id], '', 1);
            if (array_key_exists(0, $gift_rows)) {
                $gift_row = $gift_rows[0];
                $gift_name = $gift_row['name'];
                $gift_image_url = get_custom_base_url() . '/' . $gift_row['image'];
                $gift_row_id = $GLOBALS['SITE_DB']->query_insert('members_gifts', ['to_member_id' => $to_member_id, 'from_member_id' => $from_member_id, 'gift_id' => $gift_id, 'add_time' => time(), 'is_anonymous' => $anonymous, 'gift_message' => $gift_message], true);

                $GLOBALS['SITE_DB']->query_insert('ecom_sales', ['date_and_time' => time(), 'member_id' => $from_member_id, 'details' => $gift_name, 'details2' => $GLOBALS['FORUM_DRIVER']->get_username($to_member_id), 'txn_id' => $details['TXN_ID']]);

                // Send notification to recipient
                require_code('notifications');
                $subject = do_lang('GOT_GIFT', null, null, null, get_lang($to_member_id));
                if ($anonymous == 0) {
                    $sender_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($from_member_id, false);
                    $sender_displayname = $GLOBALS['FORUM_DRIVER']->get_username($from_member_id, true);
                    $sender_username = $GLOBALS['FORUM_DRIVER']->get_username($from_member_id);
                    $private_topic_url = $GLOBALS['FORUM_DRIVER']->member_pm_url($from_member_id);

                    $body = do_notification_lang('GIFT_EXPLANATION_MAIL', comcode_escape($sender_displayname), comcode_escape($gift_name), [$sender_url, $gift_image_url, $gift_message, $private_topic_url, comcode_escape($sender_username)], get_lang($to_member_id));

                    dispatch_notification('gift', null, $subject, $body, [$to_member_id], $from_member_id, ['use_real_from' => true]);
                } else {
                    $body = do_notification_lang('GIFT_EXPLANATION_ANONYMOUS_MAIL', comcode_escape($gift_name), $gift_image_url, $gift_message, get_lang($to_member_id));

                    dispatch_notification('gift', null, $subject, $body, [$to_member_id], A_FROM_SYSTEM_UNPRIVILEGED);
                }
            } else {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }
        } else {
            warn_exit(do_lang_tempcode('NO_MEMBER_SELECTED'));
        }

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
        return $GLOBALS['SITE_DB']->query_select_value_if_there('ecom_transactions', 't_member_id', ['t_type_code' => $type_code, 't_purchase_id' => intval($purchase_id)]);
    }
}

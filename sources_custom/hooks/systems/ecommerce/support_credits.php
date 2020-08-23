<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Handling of adding support credits to a member's account.
 *
 * @param  ID_TEXT $purchase_id The purchase ID.
 * @param  array $details Details of the product.
 * @param  ID_TEXT $type_code The product codename.
 */
function handle_support_credits($purchase_id, $details, $type_code)
{
    $row = $GLOBALS['SITE_DB']->query_select('credit_purchases', array('member_id', 'num_credits'), array('purchase_validated' => 0, 'purchase_id' => intval($purchase_id)), '', 1);
    if (count($row) != 1) {
        return;
    }
    $member_id = $row[0]['member_id'];
    if (is_null($member_id)) {
        return;
    }
    $num_credits = $row[0]['num_credits'];

    require_code('mantis');
    $cpf_id = get_credits_profile_field_id();
    if (is_null($cpf_id)) {
        return;
    }

    // Increment the number of credits this customer has
    require_code('cns_members_action2');
    $fields = cns_get_custom_field_mappings($member_id);
    cns_set_custom_field($member_id, $cpf_id, strval($fields['field_' . strval($cpf_id)] + $num_credits));

    // Update the row in the credit_purchases table
    $GLOBALS['SITE_DB']->query_update('credit_purchases', array('purchase_validated' => 1), array('purchase_id' => intval($purchase_id)));
}

/**
 * eCommerce product hook.
 */
class Hook_ecommerce_support_credits
{
    /**
     * Get the products handled by this eCommerce hook.
     *
     * @return array A map of product name to list of product details.
     */
    public function get_products()
    {
        if (!$GLOBALS['SITE_DB']->table_exists('credit_purchases')) {
            return array();
        }

        if (get_forum_type() != 'cns') {
            return array();
        }
        require_lang('customers');
        $products = array();
        $bundles = array(1, 2, 3, 4, 5, 6, 9, 20, 25, 35, 50, 90, 180, 550);
        foreach ($bundles as $bundle) {
            $products[strval($bundle) . '_CREDITS'] = array(
                PRODUCT_PURCHASE_WIZARD,
                float_to_raw_string($bundle * floatval(get_option('support_credit_value'))),
                'handle_support_credits',
                null,
                do_lang('customers:CUSTOMER_SUPPORT_CREDITS', integer_format($bundle)),
                get_option('currency'),
            );
        }

        return $products;
    }

    /**
     * Get the message for use in the purchase wizard.
     *
     * @param  string $type_code The product in question.
     * @return Tempcode The message.
     */
    public function get_message($type_code)
    {
        return do_lang('SUPPORT_CREDITS_PRODUCT_DESCRIPTION');
    }

    /**
     * Get the terms and conditions for use in the purchase wizard.
     *
     * @return string The message.
     */
    public function get_terms()
    {
        require_code('textfiles');
        return read_text_file('support_credits_terms', 'EN');
    }

    /**
     * Find the corresponding member to a given purchase ID.
     *
     * @param  ID_TEXT $purchase_id The purchase ID.
     * @return ?MEMBER The member (null: unknown / can't perform operation).
     */
    public function member_for($purchase_id)
    {
        return $GLOBALS['SITE_DB']->query_select_value_if_there('credit_purchases', 'member_id', array('purchase_id' => intval($purchase_id)));
    }

    /**
     * Get fields that need to be filled in in the purchase wizard.
     *
     * @return ?array The fields and message text (null: none).
     */
    public function get_needed_fields()
    {
        if (!has_actual_page_access(get_member(), 'admin_ecommerce', get_module_zone('admin_ecommerce'))) {
            return null;
        }

        // Check if we've already been passed a member ID and use it to pre-populate the field
        $member_id = get_param_integer('member_id', null);
        $username = $GLOBALS['FORUM_DRIVER']->get_username(is_null($member_id) ? get_member() : $member_id);

        return form_input_username(do_lang('USERNAME'), do_lang('USERNAME_CREDITS_FOR'), 'member_username', $username, true);
    }

    /**
     * Get the filled in fields and do something with them.
     *
     * @param  ID_TEXT $type_code The product codename.
     * @return ID_TEXT The purchase ID.
     */
    public function set_needed_fields($type_code)
    {
        $product_array = explode('_', $type_code, 2);
        $num_credits = intval($product_array[0]);
        if ($num_credits == 0) {
            return;
        }
        $manual = 0;
        $member_id = get_member();

        // Allow admins to specify the member who should receive the credits with the field in get_needed_fields
        if (has_actual_page_access(get_member(), 'admin_ecommerce_logs', get_module_zone('admin_ecommerce_logs')) && get_page_name() == 'admin_ecommerce_logs') {
            $id = post_param_integer('member_id', null);
            if (!is_null($id)) {
                $manual = 1;
                $member_id = $id;
            } else {
                $username = post_param_string('member_username', null);
                if (!is_null($username)) {
                    $manual = 1;
                    $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($username);
                    if ($member_id === null) {
                        $member_id = get_member();
                    }
                }
            }
        }

        return strval($GLOBALS['SITE_DB']->query_insert('credit_purchases', array('member_id' => $member_id, 'date_and_time' => time(), 'num_credits' => $num_credits, 'is_manual' => $manual, 'purchase_validated' => 0), true));
    }

    /**
     * Check whether the product codename is available for purchase by the member.
     *
     * @param  ID_TEXT $type_code The product codename.
     * @param  MEMBER $member The member.
     * @return boolean Whether it is.
     */
    public function is_available($type_code, $member)
    {
        return ($member != $GLOBALS['FORUM_DRIVER']->get_guest_id()) ? ECOMMERCE_PRODUCT_AVAILABLE : ECOMMERCE_PRODUCT_NO_GUESTS;
    }
}

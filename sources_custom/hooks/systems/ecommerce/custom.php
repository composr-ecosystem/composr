<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    pointstore_to_main
 */

/**
 * Handling of a purchased custom product.
 *
 * @param  ID_TEXT $purchase_id The purchase ID.
 * @param  array $details Details of the product.
 * @param  ID_TEXT $type_code The product codename.
 */
function handle_custom_purchase($purchase_id, $details, $type_code)
{
    $id = intval(substr($type_code, strlen('CUSTOM_')));

    $rows = $GLOBALS['SITE_DB']->query_select('pstore_customs', array('id', 'c_title', 'c_cost'), array('id' => $id));
    if (!array_key_exists(0, $rows)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    $row = $rows[0];

    $c_title = get_translated_text($row['c_title']);

    $sale_id = $GLOBALS['SITE_DB']->query_insert('sales', array('date_and_time' => time(), 'memberid' => $purchase_id, 'purchasetype' => 'PURCHASE_CUSTOM_PRODUCT', 'details' => $c_title, 'details2' => strval($row['id'])), true);

    require_lang('pointstore');
    require_code('notifications');
    $subject = do_lang('MAIL_REQUEST_CUSTOM', comcode_escape($c_title), null, null, get_site_default_lang());
    $username = $GLOBALS['FORUM_DRIVER']->get_username($purchase_id);
    $message_raw = do_notification_lang('MAIL_REQUEST_CUSTOM_BODY', comcode_escape($c_title), $username, null, get_site_default_lang());
    dispatch_notification('pointstore_request_custom', 'custom' . strval($id) . '_' . strval($sale_id), $subject, $message_raw, null, null, 3, true);

    // Email member
    require_code('mail');
    $row = $rows[0];
    $subject_line = get_translated_text($row['c_mail_subject']);
    if ($subject_line != '') {
        $message_raw = get_translated_text($row['cp_mail_body']);
        $email = $GLOBALS['FORUM_DRIVER']->get_member_email_address($purchase_id);
        $to_name = $GLOBALS['FORUM_DRIVER']->get_username($purchase_id, true);
        mail_wrap($subject_line, $message_raw, array($email), $to_name, '', '', 3, null, false, null, true);
    }
}

/**
 * Points Store product hook.
 */
class Hook_ecommerce_custom
{
    /**
     * Get the products handled by this eCommerce hook.
     *
     * @return array A map of product name to list of product details.
     */
    public function get_products()
    {
        $ppc = floatval(get_option('points_per_currency_unit'));
        if ($ppc <= 0.0) {
            return array();
        }

        $rows = $GLOBALS['SITE_DB']->query_select('pstore_customs', array('*'), array('c_enabled' => 1));

        $products = array();
        foreach ($rows as $row) {
            if ($row['c_cost'] != 0) {
                $cost = floatval($row['c_cost']) / $ppc;
                $products['CUSTOM_' . strval($row['id'])] = array(
                    PRODUCT_PURCHASE_WIZARD,
                    float_to_raw_string($cost),
                    'handle_custom_purchase',
                    array(),
                    get_translated_text($row['c_title']),
                    get_option('currency'),
                );
            }
        }

        return $products;
    }

    /**
     * Whether this product is available.
     *
     * @return integer The availability code (a ECOMMERCE_PRODUCT_* constant).
     */
    public function is_available($type_code, $member)
    {
        if (is_guest($member)) {
            return ECOMMERCE_PRODUCT_NO_GUESTS;
        }

        $id = intval(substr($type_code, strlen('CUSTOM_')));

        $rows = $GLOBALS['SITE_DB']->query_select('pstore_customs', array('*'), array('id' => $id), '', 1);
        if (array_key_exists(0, $rows)) {
            $row = $rows[0];

            if ($row['c_enabled'] == 0) {
                return ECOMMERCE_PRODUCT_DISABLED;
            }

            if ($row['c_one_per_member'] == 1) {
                // Test to see if it's been bought
                $test = $GLOBALS['SITE_DB']->query_select_value_if_there('sales', 'id', array('purchasetype' => 'PURCHASE_CUSTOM_PRODUCT', 'details2' => strval($rows[0]['id']), 'memberid' => $member));
                if (!is_null($test)) {
                    return ECOMMERCE_PRODUCT_ALREADY_HAS;
                }
            }

            return ECOMMERCE_PRODUCT_AVAILABLE;
        }

        return ECOMMERCE_PRODUCT_MISSING;
    }

    /**
     * Get the message for use in the purchase wizard.
     *
     * @param  string $type_code The product in question.
     * @return Tempcode The message.
     */
    public function get_message($type_code)
    {
        $id = intval(substr($type_code, strlen('CUSTOM_')));

        $rows = $GLOBALS['SITE_DB']->query_select('pstore_customs', array('*'), array('id' => $id), '', 1);
        if (array_key_exists(0, $rows)) {
            $row = $rows[0];
            return get_translated_tempcode('pstore_customs', $row, 'c_description');
        }
        return new Tempcode();
    }

    /**
     * Get fields that need to be filled in in the purchase wizard.
     *
     * @return ?array The fields and message text (null: none).
     */
    public function get_needed_fields()
    {
        return null;
    }
}

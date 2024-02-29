<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

// This test will likely fail if using Cloudflare or a proxy

/**
 * Composr test case class (unit testing).
 */
class usergroup_subscriptions_test_set extends cms_test_case
{
    protected $usergroup_subscription_id;

    public function setUp()
    {
        parent::setUp();

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        require_code('ecommerce');
        require_code('ecommerce2');

        $this->usergroup_subscription_id = add_usergroup_subscription('test', 'test', 123.00, '10.00', 12, 'y', 1, 3, 0, 1, ' ', ' ', ' ', []);

        $this->assertTrue(12 == $GLOBALS['FORUM_DB']->query_select_value('f_usergroup_subs', 's_length', ['id' => $this->usergroup_subscription_id]));
    }

    public function testUsergroupPurchaseWorks()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        $session_id = $this->establish_admin_callback_session();

        set_option('payment_gateway', 'paypal');
        set_option('ecommerce_test_mode', '1');
        set_option('payment_gateway_username', 'live=live@example.com,testing=test@example.com');
        set_option('currency', 'USD');

        $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username('test');

        // Test usergroup subscription selection is there
        $url = build_url(['page' => 'purchase', 'type' => 'browse', 'keep_su' => 'test', 'keep_ecommerce_local_test' => 1]);
        $purchase_screen = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'cookies' => [get_session_cookie() => $session_id]]);
        $this->assertTrue(strpos($purchase_screen, 'Usergroup subscription') !== false);

        // Test our usergroup subscription is there
        $url = build_url(['page' => 'purchase', 'type' => 'browse', 'category' => 'usergroup', 'keep_su' => 'test', 'keep_ecommerce_local_test' => 1]);
        $purchase_screen = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'cookies' => [get_session_cookie() => $session_id]]);
        $this->assertTrue(strpos($purchase_screen, 'Subscription: test') !== false);

        // Test button generates
        $subscription_id = strval($GLOBALS['SITE_DB']->query_insert('ecom_subscriptions', [
            's_type_code' => 'USERGROUP' . strval($this->usergroup_subscription_id),
            's_member_id' => $member_id,
            's_state' => 'new',
            's_price' => 123.00,
            's_tax_code' => '0.00',
            's_tax_derivation' => json_encode([]),
            's_tax' => 0.00,
            's_tax_tracking' => json_encode([]),
            's_currency' => 'USD',
            's_purchase_id' => strval($member_id),
            's_time' => time(),
            's_auto_fund_source' => '',
            's_auto_fund_key' => '',
            's_payment_gateway' => 'paypal',
            's_length' => 12,
            's_length_units' => 'y',
        ], true));
        $button = make_subscription_button('USERGROUP' . strval($this->usergroup_subscription_id), 'test', $subscription_id, 123.00, [], 0.00, [], 'USD', 0, 12, 'y', 'paypal');

        // Find custom ID for transaction
        $matches = [];
        preg_match('#<input type="hidden" name="custom" value="([^"]*)" />#', $button->evaluate(), $matches);
        $trans_expecting_id = $matches[1];

        // Make sure test user is not in our subscription usergroup
        $GLOBALS['FORUM_DB']->query_delete('f_group_members', ['gm_member_id' => $member_id, 'gm_group_id' => 3]);

        // Put through fake IPN response
        $ipn_data = [
            'subscr_id' => uniqid('', true),
            'verify_sign' => uniqid('', true),

            'subscr_date' => '14:32:23 Feb 15, 2010 PST',
            'item_name' => 'test',
            'custom' => $trans_expecting_id,
            'recurring' => '1',
            'period3' => '12 Y',
            'mc_amount3' => '123.00',
            'tax' => '0.00',

            'payer_status' => 'verified',
            'payer_id' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'payer_email' => 'john@example.com',
            'residence_country' => 'US',

            'business' => 'test@example.com',
            'receiver_email' => 'test@example.com',

            'charset' => 'windows-1252',
            'notify_version' => '2.9',
            'test_ipn' => '1',
        ];
        $_POST = [
            'txn_type' => 'subscr_signup',
        ] + $ipn_data;
        handle_pdt_ipn_transaction_script(true, false);

        // Test was actioned
        $test = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_group_members', 'gm_member_id', ['gm_member_id' => $member_id, 'gm_group_id' => 3]);
        $this->assertTrue($test !== null);

        // Cancellation of subscription (put through fake IPN response)
        $_POST = [
            'txn_type' => 'subscr_eot',
        ] + $ipn_data;
        handle_pdt_ipn_transaction_script(true, false);

        // Test was actioned
        $test = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_group_members', 'gm_member_id', ['gm_member_id' => $member_id, 'gm_group_id' => 3]);
        $this->assertTrue($test === null);
    }

    public function testEditUsergroupSubscription()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        edit_usergroup_subscription($this->usergroup_subscription_id, 'Edit usergroup subscription', 'new edit', 122.00, '10.00', 3, 'y', 1, 0, 1, 1, ' ', ' ', ' ', []);

        $this->assertTrue(3 == $GLOBALS['FORUM_DB']->query_select_value('f_usergroup_subs', 's_length', ['id' => $this->usergroup_subscription_id]));
    }

    public function tearDown()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        delete_usergroup_subscription($this->usergroup_subscription_id, 'test@example.com');

        parent::tearDown();
    }
}

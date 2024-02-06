<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class points_test_set extends cms_test_case
{
    protected $points_transact_record;
    protected $points_credit_member;
    protected $points_debit_member;
    protected $topic_id;
    protected $post_id;
    protected $enable_gift_points;
    protected $escrow;

    protected $gift_points_sent;
    protected $gift_points_id;

    protected $initial_credit_sender;
    protected $initial_credit_recipient;

    protected $disable_clean_up = true;

    protected $admin_user;
    protected $member_user;

    public function setUp()
    {
        parent::setUp();

        if (!addon_installed('points')) {
            $this->assertTrue(false, 'Test only works with the points addon.');
            return;
        }

        if (get_forum_type() == 'none') {
            $this->assertTrue(false, 'Test does not work with the none forum driver.');
            return;
        }

        require_code('points');
        require_code('points2');

        require_code('cns_topics');
        require_code('cns_posts');
        require_code('cns_forums');
        require_code('cns_posts_action');
        require_code('cns_posts_action2');
        require_code('cns_posts_action3');
        require_code('cns_topics_action');
        require_code('cns_topics_action2');

        $this->admin_user = $this->get_canonical_member_id('admin');
        $this->member_user = $this->get_canonical_member_id('test');

        $this->enable_gift_points = get_option('enable_gift_points');

        set_option('enable_gift_points', '1');

        $this->gift_points_sent = gift_points_sent($this->admin_user);

        $this->establish_admin_session();

        // Credit some points so each member is in the positive (necessary for the tests to succeed)
        $this->initial_credit_sender = points_credit_member($this->admin_user, 'Unit test: Points', 100000, 0, null);
        $this->initial_credit_recipient = points_credit_member($this->member_user, 'Unit test: Points', 100000, 0, null);

        // Also credit some gift points to the admin so we can run our gift point tests
        $this->gift_points_id = points_refund($this->get_canonical_member_id('guest'), $this->admin_user, 'Unit test: Points', 25, 25);
    }

    public function testSendGiftPointsAndReverse()
    {
        if (!addon_installed('points')) {
            return;
        }

        points_flush_runtime_cache();
        $this->_testSendGiftPointsAndReverse('have enough gift points', gift_points_balance($this->admin_user));
        points_flush_runtime_cache();
        $this->_testSendGiftPointsAndReverse('not enough gift points', gift_points_balance($this->admin_user) + 1);
        points_flush_runtime_cache();
        $this->_testSendGiftPointsAndReverse('force use 0 gift points', 10, 0);
        points_flush_runtime_cache();
        $this->_testSendGiftPointsAndReverse('force use 1 gift point', gift_points_balance($this->admin_user) + 1, 1);
        points_flush_runtime_cache();
        $this->_testSendGiftPointsAndReverse('force use more gift points than we have', gift_points_balance($this->admin_user) + 1, gift_points_balance($this->admin_user) + 1);
        points_flush_runtime_cache();
        $this->_testSendGiftPointsAndReverse('refund 2 points with 1 of them being gift points', 2, 1, true);
    }

    private function _testSendGiftPointsAndReverse($test, $points_to_send, $use_gift_points = null, $is_refund = false)
    {
        points_flush_runtime_cache();
        $initial_gift_points_sent = gift_points_sent($this->admin_user);
        $initial_gift_points = gift_points_balance($this->admin_user);
        $initial_points_spent = points_used($this->admin_user);
        $initial_points = points_balance($this->admin_user);
        $initial_points_recipient = points_balance($this->member_user);

        if ($is_refund) {
            $this->points_transact_record = points_refund($this->member_user, $this->admin_user, 'Points unit test: ' . $test, $points_to_send, $use_gift_points, 0, null, null);
        } else {
            $this->points_transact_record = points_transact($this->admin_user, $this->member_user, 'Points unit test: ' . $test, $points_to_send, $use_gift_points, 0, null);
        }

        points_flush_runtime_cache();
        $current_gift_points_sent = gift_points_sent($this->admin_user);
        $current_gift_points = gift_points_balance($this->admin_user);
        $current_points_spent = points_used($this->admin_user);
        $current_points = points_balance($this->admin_user);
        $current_points_recipient = points_balance($this->member_user);

        // We requested to allocate as many gift points as possible
        if ($use_gift_points === null) {
            // We have enough gift points to cover the entire transaction; make sure regular points are untouched
            if (($initial_gift_points >= $points_to_send)) {
                $this->assertTrue($this->points_transact_record !== null, 'Test ' . strval($test) . ': Expected the transaction to process, but it failed.');

                $gift_points_diff = ($initial_gift_points - $current_gift_points);
                $gift_points_diff_expected = $points_to_send;
                $prioritised_gift_points_correctly = ($gift_points_diff === $gift_points_diff_expected);

                $gift_points_sent_diff = ($current_gift_points_sent - $initial_gift_points_sent);
                $gift_points_sent_diff_expected = $points_to_send;

                $points_diff = ($initial_points - $current_points);
                $points_diff_expected = 0;

                $spent_points_diff = ($current_points_spent - $initial_points_spent);
                $spent_points_diff_expected = 0;

            // We do not have enough gift points to cover the entire transaction; make sure all gift points are used and regular points make up the rest
            } else {
                $this->assertTrue($this->points_transact_record !== null, 'Test ' . strval($test) . ': Expected the transaction to process, but it failed.');

                $gift_points_diff = ($initial_gift_points - $current_gift_points);
                $gift_points_diff_expected = $initial_gift_points;
                $prioritised_gift_points_correctly = ($current_gift_points === 0);

                $gift_points_sent_diff = ($current_gift_points_sent - $initial_gift_points_sent);
                $gift_points_sent_diff_expected = $initial_gift_points;

                $points_diff = ($initial_points - $current_points);
                $points_diff_expected = ($points_to_send - $initial_gift_points);

                $spent_points_diff = ($current_points_spent - $initial_points_spent);
                $spent_points_diff_expected = ($points_to_send - $initial_gift_points);
            }

        // We requested a specific number of sent points to be gift points
        } elseif (!$is_refund) {
            // We have enough gift points to cover what was requested; make sure only that amount was sent and the rest taken from regular points
            if (($initial_gift_points >= $use_gift_points)) {
                $this->assertTrue($this->points_transact_record !== null, 'Test ' . strval($test) . ': Expected the transaction to process, but it failed.');

                $gift_points_diff = ($initial_gift_points - $current_gift_points);
                $gift_points_diff_expected = $use_gift_points;
                $prioritised_gift_points_correctly = ($gift_points_diff === $use_gift_points);

                $gift_points_sent_diff = ($current_gift_points_sent - $initial_gift_points_sent);
                $gift_points_sent_diff_expected = $use_gift_points;

                $points_diff = ($initial_points - $current_points);
                $points_diff_expected = ($points_to_send - $use_gift_points);

                $spent_points_diff = ($current_points_spent - $initial_points_spent);
                $spent_points_diff_expected = ($points_to_send - $use_gift_points);

            // We do not have enough gift points. This should result in a failed transaction.
            } else {
                $this->assertTrue($this->points_transact_record === null, 'Test ' . strval($test) . ': Expected the transaction to fail / not process, but it did anyway.');
                return;
            }

        // We are refunding points
        } else {
            $this->assertTrue($this->points_transact_record !== null, 'Test ' . strval($test) . ': Expected the transaction to process, but it failed.');

            $gift_points_diff = ($initial_gift_points - $current_gift_points);
            $gift_points_diff_expected = -$use_gift_points;
            $prioritised_gift_points_correctly = ($gift_points_diff === -$use_gift_points);

            $gift_points_sent_diff = ($current_gift_points_sent - $initial_gift_points_sent);
            $gift_points_sent_diff_expected = -$use_gift_points;

            $points_diff = ($initial_points - $current_points);
            $points_diff_expected = -($points_to_send - $use_gift_points);

            $spent_points_diff = ($current_points_spent - $initial_points_spent);
            $spent_points_diff_expected = -($points_to_send - $use_gift_points);
        }

        $this->assertTrue($prioritised_gift_points_correctly, 'Test ' . strval($test) . ': Gift points and regular points were not correctly sent with gift points taking priority as expected. Expected ' . strval($gift_points_diff_expected) . ' gift points to be sent, but instead got ' . strval($gift_points_diff));
        $this->assertTrue(($gift_points_diff_expected === $gift_points_diff), 'Test ' . strval($test) . ': Gift points balance did not decrease as expected. Expected it to decrease by ' . strval($gift_points_diff_expected) . ' but instead decreased by ' . strval($gift_points_diff));
        $this->assertTrue(($gift_points_sent_diff_expected === $gift_points_sent_diff), 'Test ' . strval($test) . ': Sent gift points did not increase as expected. Expected it to increase by ' . strval($gift_points_sent_diff_expected) . ' but instead increased by ' . strval($gift_points_sent_diff));
        $this->assertTrue(($points_diff_expected === $points_diff), 'Test ' . strval($test) . ': Points balance of sender did not decrease as expected. Expected it to decrease by ' . strval($points_diff_expected) . ' but instead decreased by ' . strval($points_diff));
        $this->assertTrue(($spent_points_diff_expected === $spent_points_diff), 'Test ' . strval($test) . ': Spent points did not increase as expected. Expected it to increase by ' . strval($spent_points_diff_expected) . ' but instead increased by ' . strval($spent_points_diff));

        if (!$is_refund) {
            $points_recipient_diff = ($current_points_recipient - $initial_points_recipient);
            $points_recipient_diff_expected = $points_to_send;
            $this->assertTrue(($points_recipient_diff_expected === $points_recipient_diff), 'Test ' . strval($test) . ': Recipient points balance did not increase as expected. Expected it to increase by ' . strval($points_recipient_diff_expected) . ' but instead increased by ' . strval($points_recipient_diff));
        }

        // Now test reversal
        if ($this->points_transact_record !== null) {
            $id = mixed();
            if ($is_refund) { // Since refunds are irreversible, we must manually reverse them with a transact
                $id = points_transact($this->admin_user, $this->member_user, 'Reverse points unit test: ' . $test, $points_to_send, $use_gift_points, 0, null, 1);
            } else {
                $id = points_transaction_reverse($this->points_transact_record, null);
            }

            points_flush_runtime_cache();
            $reversed_gift_points_sent = gift_points_sent($this->admin_user);
            $reversed_gift_points = gift_points_balance($this->admin_user);
            $reversed_points_spent = points_used($this->admin_user);
            $reversed_points = points_balance($this->admin_user);
            $reversed_points_recipient = points_balance($this->member_user);

            $this->assertTrue(($reversed_gift_points_sent == $initial_gift_points_sent), 'Test ' . strval($test) . ': Points did not reverse as expected for reverse transaction (gift points sent). Expected ' . strval($initial_gift_points_sent) . ' but instead got ' . strval($reversed_gift_points_sent));
            $this->assertTrue(($reversed_gift_points == $initial_gift_points), 'Test ' . strval($test) . ': Points did not reverse as expected for reverse transaction (gift points balance). Expected ' . strval($initial_gift_points) . ' but instead got ' . strval($reversed_gift_points));
            $this->assertTrue(($reversed_points_spent == $initial_points_spent), 'Test ' . strval($test) . ': Points did not reverse as expected for reverse transaction (points spent). Expected ' . strval($initial_points_spent) . ' but instead got ' . strval($reversed_points_spent));
            $this->assertTrue(($reversed_points_recipient == $initial_points_recipient), 'Test ' . strval($test) . ': Points did not reverse as expected for reverse transaction (recipient points balance). Expected ' . strval($initial_points_recipient) . ' but instead got ' . strval($reversed_points_recipient));
            $this->assertTrue(($reversed_points == $initial_points), 'Test ' . strval($test) . ': Points did not reverse as expected for reverse transaction (points balance). Expected ' . strval($initial_points) . ' but instead got ' . strval($reversed_points));

            // Do not keep unit tests in the ledger
            if (!$this->disable_clean_up) {
                $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $this->points_transact_record], '', 1);
                $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => ($is_refund) ? $id : $id[0]], '', 1);
            }
        }
    }

    public function testSendPointsAndReverse()
    {
        if (!addon_installed('points')) {
            return;
        }

        $points_to_send = 10;

        // Disable gift points for this test
        set_option('enable_gift_points', '0');

        points_flush_runtime_cache();
        $initial_points_spent = points_used($this->admin_user);
        $initial_points_to_send = points_balance($this->admin_user);
        $initial_points = points_balance($this->member_user);

        // Test user 2 giving 10 points to user 1 (use null for gift points to ensure the calculation is triggered and accounts for gift points being off)
        $this->points_transact_record = points_transact($this->admin_user, $this->member_user, 'Unit test send points and reverse', $points_to_send, null, 0, null);
        if ($this->points_transact_record === null) {
            $this->assertTrue(false, 'Send points and reverse: points_transact failed (returned null instead of an ID).');
            return;
        }

        points_flush_runtime_cache();
        $current_points_spent = points_used($this->admin_user);
        $current_points_to_send = points_balance($this->admin_user);
        $current_points = points_balance($this->member_user);

        // User 2 should have used +10 points and have -10 points to give. User 1 should have +10 points to spend.
        $used_points_correct = ($current_points_spent == ($initial_points_spent + $points_to_send));
        $to_send_points_correct = ($current_points_to_send == ($initial_points_to_send - $points_to_send));
        $points_correct = ($current_points == ($initial_points + $points_to_send));

        // Now test reversal
        $id = points_transaction_reverse($this->points_transact_record, null);

        points_flush_runtime_cache();
        $reversed_points_spent = points_used($this->admin_user);
        $reversed_points_to_send = points_balance($this->admin_user);
        $reversed_points = points_balance($this->member_user);

        $reversed_correct = (($reversed_points_spent == $initial_points_spent) && ($reversed_points_to_send == $initial_points_to_send) && ($reversed_points == $initial_points));

        $this->assertTrue($used_points_correct, 'Spent points did not increase as expected.');
        $this->assertTrue($to_send_points_correct, 'Points to give did not decrease as expected.');
        $this->assertTrue($points_correct, 'Points to spend did not increase as expected. It was at ' . strval($current_points) . ' when it should have been at ' . strval($initial_points + $points_to_send));
        $this->assertTrue($reversed_correct, 'Points did not reverse as expected for reverse transaction.');

        // Re-enable gift points for the test suite
        set_option('enable_gift_points', '1');

        // Do not keep unit tests in the ledger
        if (!$this->disable_clean_up) {
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $this->points_transact_record], '', 1);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $id[0]], '', 1);
        }
    }

    public function testForumPoints()
    {
        if (get_forum_type() != 'cns' || !addon_installed('points') || get_db_type() == 'xml') {
            return;
        }

        points_flush_runtime_cache();

        $initial_points = points_balance($this->admin_user);

        $this->topic_id = cns_make_topic(db_get_first_id(), 'Test');
        $this->post_id = cns_make_post($this->topic_id, 'Welcome', 'Welcome to the posts', 0, true, null, 0, null, null, null, $this->admin_user, null, null, null, true, true, null, true, '', null, false, false, false);

        $current_points = points_balance($this->admin_user);

        $points_to_earn = intval(get_option('points_posting'));

        $change = ($current_points - $initial_points);

        $this->assertTrue($change == $points_to_earn, 'Points to spend did not increase for a forum post as expected (' . strval($points_to_earn) . '). The change was ' . strval($change));

        // Tear down
        if (!cns_delete_posts_topic($this->topic_id, [$this->post_id], 'Nothing')) {
            cns_delete_topic($this->topic_id);
        }
    }

    public function testPointsCreditMember()
    {
        if (!addon_installed('points')) {
            return;
        }

        $points_to_credit = 1;

        points_flush_runtime_cache();
        $initial_points = points_balance($this->member_user);

        $this->points_credit_member = points_credit_member($this->member_user, 'Points unit test: credit member', $points_to_credit, 0, null);
        if ($this->points_credit_member === null) {
            $this->assertTrue(false, 'points_credit_member failed (returned null instead of an ID).');
            return;
        }

        points_flush_runtime_cache();
        $current_points = points_balance($this->member_user);

        $this->assertTrue(($current_points - $initial_points) == $points_to_credit, 'Points to spend did not increase with points credit member as expected.');

        // Tear down
        $id = points_transaction_reverse($this->points_credit_member, null);
        if (!$this->disable_clean_up) {
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $this->points_credit_member], '', 1);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $id[0]], '', 1);
        }
    }

    public function testPointsDebitMember()
    {
        if (!addon_installed('points')) {
            return;
        }

        $points_to_debit = 1;

        points_flush_runtime_cache();
        $initial_points = points_balance($this->member_user);

        $this->points_debit_member = points_debit_member($this->member_user, 'Points unit test: debit member', $points_to_debit, 0, 0, null);
        if ($this->points_debit_member === null) {
            $this->assertTrue(false, 'points_debit_member failed (returned null instead of an ID).');
            return;
        }

        points_flush_runtime_cache();
        $current_points = points_balance($this->member_user);

        $this->assertTrue(($initial_points - $current_points) == $points_to_debit, 'Points to spend did not decrease as expected with points debit member.');

        // Tear down
        $id = points_transaction_reverse($this->points_debit_member, null);
        if (!$this->disable_clean_up) {
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $this->points_debit_member], '', 1);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $id[0]], '', 1);
        }
    }

    public function testPointsEscrowWithGiftPoints()
    {
        if (!addon_installed('points')) {
            return;
        }

        require_code('points_escrow');

        points_flush_runtime_cache();
        $initial_gift_points_sent = gift_points_sent($this->admin_user);
        $initial_gift_points = gift_points_balance($this->admin_user);
        $initial_points_spent = points_used($this->admin_user);
        $initial_points = points_balance($this->admin_user);
        $initial_points_recipient = points_balance($this->member_user);

        $points_to_escrow = $initial_gift_points + 1;

        $this->escrow = escrow_points($this->admin_user, $this->member_user, $points_to_escrow, 'Unit test: escrow with gift points', 'Unit test', null, false, null);
        if ($this->escrow === null) {
            $this->assertTrue(false, 'escrow_points failed (returned null instead of an ID).');
            return;
        }

        $rows = $GLOBALS['SITE_DB']->query_select('escrow', ['*'], ['id' => $this->escrow], '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        points_flush_runtime_cache();
        $current_gift_points_sent = gift_points_sent($this->admin_user);
        $current_gift_points = gift_points_balance($this->admin_user);
        $current_points_spent = points_used($this->admin_user);
        $current_points = points_balance($this->admin_user);
        $current_points_recipient = points_balance($this->member_user);

        $expected_gift_points_sent = $initial_gift_points;
        $actual_gift_points_sent = ($current_gift_points_sent - $initial_gift_points_sent);
        $this->assertTrue(($actual_gift_points_sent == $expected_gift_points_sent), 'Escrow: Expected current gift points sent to go up by ' . strval($expected_gift_points_sent) . ' but instead was ' . strval($actual_gift_points_sent));
        $this->assertTrue(($current_gift_points == 0), 'Escrow: Expected current gift points balance to be 0 but instead was ' . strval($current_gift_points));

        $expected_points_spent = 1;
        $actual_points_spent = ($current_points_spent - $initial_points_spent);
        $this->assertTrue(($actual_points_spent == $expected_points_spent), 'Escrow: Expected current points spent to go up by ' . strval($expected_points_spent) . ' but instead was ' . strval($actual_points_spent));

        $expected_points = $initial_points - 1;
        $this->assertTrue(($current_points == $expected_points), 'Escrow: Expected current points balance to be ' . strval($expected_points) . ' but instead was ' . strval($current_points));
        $this->assertTrue(($current_points_recipient == $initial_points_recipient), 'Escrow: Expected the recipient to not yet have any change in their points balance, but a change happened.');

        satisfy_escrow($this->escrow, $this->admin_user, null, false, null);

        points_flush_runtime_cache();
        $current_points_recipient2 = points_balance($this->member_user);
        $this->assertTrue(($current_points_recipient == $current_points_recipient2), 'Escrow first member satisfied: Expected the recipient to not yet have any change in their points balance, but a change happened.');

        $data = satisfy_escrow($this->escrow, $this->member_user, null, false, null);
        $current_points_recipient3 = points_balance($this->member_user);

        $actual_points_recipient = ($current_points_recipient3 - $initial_points_recipient);
        $this->assertTrue(($actual_points_recipient == $points_to_escrow), 'Escrow second member satisfied: Expected the recipient to receive ' . strval($points_to_escrow) . ' points but instead received ' . strval($actual_points_recipient));

        // Tear down
        $id = points_transaction_reverse($myrow['original_points_ledger_id'], null);
        $id2 = null;
        if (($data !== null) && (array_key_exists(0, $data))) {
            $id2 = points_transaction_reverse($data[0], null);
        }

        points_flush_runtime_cache();
        $reversed_gift_points_sent = gift_points_sent($this->admin_user);
        $reversed_gift_points = gift_points_balance($this->admin_user);
        $reversed_points_spent = points_used($this->admin_user);
        $reversed_points = points_balance($this->admin_user);
        $reversed_points_recipient = points_balance($this->member_user);

        $this->assertTrue(($reversed_gift_points_sent == $initial_gift_points_sent), 'Escrow: Points did not reverse as expected for reverse transaction (gift points sent). Expected ' . strval($initial_gift_points_sent) . ' but instead got ' . strval($reversed_gift_points_sent));
        $this->assertTrue(($reversed_gift_points == $initial_gift_points), 'Escrow: Points did not reverse as expected for reverse transaction (gift points balance). Expected ' . strval($initial_gift_points) . ' but instead got ' . strval($reversed_gift_points));
        $this->assertTrue(($reversed_points_spent == $initial_points_spent), 'Escrow: Points did not reverse as expected for reverse transaction (points spent). Expected ' . strval($initial_points_spent) . ' but instead got ' . strval($reversed_points_spent));
        $this->assertTrue(($reversed_points_recipient == $initial_points_recipient), 'Escrow: Points did not reverse as expected for reverse transaction (recipient points balance). Expected ' . strval($initial_points_recipient) . ' but instead got ' . strval($reversed_points_recipient));
        $this->assertTrue(($reversed_points == $initial_points), 'Escrow: Points did not reverse as expected for reverse transaction (points balance). Expected ' . strval($initial_points) . ' but instead got ' . strval($reversed_points));

        // Do not keep unit tests in the ledger
        if (!$this->disable_clean_up) {
            $GLOBALS['SITE_DB']->query_delete('escrow_logs', ['escrow_id' => $this->escrow]);
            $GLOBALS['SITE_DB']->query_delete('escrow', ['id' => $this->escrow], '', 1);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $myrow['original_points_ledger_id']], '', 1);
            $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $id[0]], '', 1);
            if ($id2 !== null && array_key_exists(0, $id2)) {
                $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $id2[0]], '', 1);
            }
            if (($data !== null) && (array_key_exists(0, $data))) {
                $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $data[0]], '', 1);
            }
        }
    }

    public function tearDown()
    {
        if (!addon_installed('points')) {
            return;
        }

        // Reverse credited points
        $reverse1 = points_transaction_reverse($this->initial_credit_sender);
        $reverse2 = points_transaction_reverse($this->initial_credit_recipient);
        $reverse3 = points_transaction_reverse($this->gift_points_id);
        if (!$this->disable_clean_up) {
            foreach ([$this->initial_credit_sender, $reverse1[0], $this->initial_credit_recipient, $reverse2[0], $this->gift_points_id, $reverse3[0]] as $id) {
                $GLOBALS['SITE_DB']->query_delete('points_ledger', ['id' => $id], '', 1);
            }
        }

        set_option('enable_gift_points', $this->enable_gift_points);

        parent::tearDown();
    }
}

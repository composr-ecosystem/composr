<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class members_test_set extends cms_test_case
{
    protected $member_id;
    protected $access_mapping;

    public function setUp()
    {
        parent::setUp();

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        require_code('cns_members_action');
        require_code('cns_members_action2');
        require_code('crypt');
        require_lang('cns');

        $this->member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username('testmember');
        if ($this->member_id !== null) {
            cns_delete_member($this->member_id);
        }

        $this->member_id = cns_make_member(
            'testmember', // username
            get_secure_random_password(null, 'testmember', 'test123@example.com'), // password
            'test123@example.com', // email_address
            null, // primary_group
            null, // secondary_groups
            10, // dob_day
            1, // dob_month
            1980, // dob_year
            [], // custom_fields
            null, // timezone
            '', // language
            '', // theme
            '', // title
            '', // photo_url
            null, // avatar_url
            '', // signature
            null, // preview_posts
            1, // reveal_age
            1, // views_signatures
            null, // auto_monitor_contrib_content
            null, // smart_topic_notification
            null, // mailing_list_style
            1, // auto_mark_read
            null, // sound_enabled
            1, // allow_emails
            1, // allow_emails_from_staff
            0, // highlighted_name
            '*', // pt_allow
            '', // pt_rules_text
            1, // validated
            '', // validated_email_confirm_code
            null, // on_probation_until
            '0', // is_perm_banned
            true // check_correctness
        );

        $this->assertTrue('testmember' == $GLOBALS['FORUM_DB']->query_select_value('f_members', 'm_username', ['id' => $this->member_id]));
    }

    public function testEditMember()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_edit_member(
            $this->member_id, // member_id
            null, // username
            null, // password
            'testing123@example.com', // email_address
            null // primary_group
        );

        $this->assertTrue('testing123@example.com' == $GLOBALS['FORUM_DB']->query_select_value('f_members', 'm_email_address', ['id' => $this->member_id]));
    }

    public function tearDown()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        cns_delete_member($this->member_id);

        parent::tearDown();
    }
}

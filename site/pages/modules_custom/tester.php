<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    tester
 */

/*
Tips...

Query to find test sections not yet started:
SELECT s.* FROM `cms_test_sections` s WHERE NOT EXISTS(SELECT * FROM `cms_tests` WHERE t_section=s.id AND t_status<>0)
AND s_assigned_to<>4 AND s_assigned_to<>89
ORDER BY s_assigned_to

UPDATE cms_test_sections SET s_assigned_to=4 WHERE id IN (2,18,19,20,21,22,34,35,36,72,73,77,78,79,80,81,82,83,84,85,86,89,90,91,92,93,94,95,96,97,98,99,100,101,103)
*/

/**
 * Module page class.
 */
class Module_tester
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('test_sections');
        $GLOBALS['SITE_DB']->drop_table_if_exists('tests');

        delete_privilege('perform_tests');
        delete_privilege('add_tests');
        delete_privilege('edit_own_tests');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        $GLOBALS['SITE_DB']->create_table('test_sections', array(
            'id' => '*AUTO',
            's_section' => 'SHORT_TEXT',
            's_notes' => 'LONG_TEXT',
            's_inheritable' => 'BINARY',
            's_assigned_to' => '?MEMBER' // null: no assignee, as it's meant to be inherited
        ));

        $GLOBALS['SITE_DB']->create_table('tests', array(
            'id' => '*AUTO',
            't_section' => 'AUTO_LINK',
            't_test' => 'LONG_TEXT',
            't_assigned_to' => '?MEMBER', // null: section assignee
            't_enabled' => 'BINARY',
            't_status' => 'INTEGER', // 0=not done, 1=success, 2=failure
            't_inherit_section' => '?AUTO_LINK' // null: none
        ));

        add_privilege('TESTER', 'perform_tests', false);
        add_privilege('TESTER', 'add_tests', true);
        add_privilege('TESTER', 'edit_own_tests', true);
    }

    /**
     * Find icon for this module, specifically to find an icon for the module's main sitemap node. Defined when there is no entry-point for a default page call.
     *
     * @return string Icon.
     */
    public function get_wrapper_icon()
    {
        return 'menu/_generic_admin/tool';
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions.
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user).
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name).
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled).
     */
    public function get_entry_points($check_perms = true, $member_id = null, $support_crosslinks = true, $be_deferential = false)
    {
        return array(
            'add_test' => array('ADD_TEST', 'menu/_generic_admin/add_one'),
            'add' => array('ADD_TEST_SECTION', 'menu/_generic_admin/add_one_category'),
            'edit' => array('EDIT_TEST_SECTION', 'menu/_generic_admin/edit_one_category'),
            'go' => array('RUN_THROUGH_TESTS', 'buttons/proceed'),
            'stats' => array('TEST_STATISTICS', 'menu/_generic_admin/view_this'),
        );
    }

    public $title;
    public $id;
    public $test_row;
    public $test;
    public $self_title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $type = get_param_string('type', 'go');

        require_lang('tester');

        if ($type == 'stats') {
            $this->title = get_screen_title('TEST_STATISTICS');
        }

        if ($type == 'go') {
            $this->title = get_screen_title('RUN_THROUGH_TESTS');
        }

        if ($type == '_go') {
            $this->title = get_screen_title('RUN_THROUGH_TESTS');
        }

        if ($type == 'report') {
            $id = get_param_integer('id');
            $test_row = $GLOBALS['SITE_DB']->query_select('tests t LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'test_sections s ON t.t_section=s.id', array('*'), array('t.id' => $id), '', 1);
            if (array_key_exists(0, $test_row)) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }

            $section = $test_row[0]['s_section'];
            $test = $test_row[0]['t_test'];

            $this->id = $id;
            $this->test_row = $test_row;
            $this->test = $test;

            $this->self_title = $section . '/' . substr($test, 0, 20);

            $this->title = get_screen_title('BUG_REPORT_FOR', true, array(escape_html($this->self_title)));
        }

        if ($type == 'add_test') {
            $this->title = get_screen_title('ADD_TEST');
        }

        if ($type == '_add_test') {
            $this->title = get_screen_title('ADD_TEST');
        }

        if ($type == 'add') {
            $this->title = get_screen_title('ADD_TEST_SECTION');
        }

        if ($type == '_add') {
            $this->title = get_screen_title('ADD_TEST_SECTION');
        }

        if ($type == 'edit') {
            $this->title = get_screen_title('EDIT_TEST_SECTION');
        }

        if ($type == '_edit') {
            $this->title = get_screen_title('EDIT_TEST_SECTION');
        }

        if ($type == '__edit') {
            if (post_param_integer('delete', 0) == 1) {
                $this->title = get_screen_title('DELETE_TEST_SECTION');
            } else {
                $this->title = get_screen_title('EDIT_TEST_SECTION');
            }
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        require_css('tester');

        check_privilege('perform_tests');

        // Decide what we're doing
        $type = get_param_string('type', 'go');

        if ($type == 'stats') {
            return $this->stats();
        }
        if ($type == 'go') {
            return $this->go();
        }
        if ($type == '_go') {
            return $this->_go();
        }
        if ($type == 'report') {
            return $this->report();
        }
        if ($type == 'add_test') {
            return $this->add_test();
        }
        if ($type == '_add_test') {
            return $this->_add_test();
        }
        if ($type == 'add') {
            return $this->add();
        }
        if ($type == '_add') {
            return $this->_add();
        }
        if ($type == 'edit') {
            return $this->edit();
        }
        if ($type == '_edit') {
            return $this->_edit();
        }
        if ($type == '__edit') {
            return $this->__edit();
        }

        return new Tempcode();
    }

    /**
     * Show statistics on test progress.
     *
     * @return Tempcode The result of execution.
     */
    public function stats()
    {
        $num_tests_successful = $GLOBALS['SITE_DB']->query_select_value('tests', 'COUNT(*)', array('t_status' => 1, 't_enabled' => 1));
        $num_tests_failed = $GLOBALS['SITE_DB']->query_select_value('tests', 'COUNT(*)', array('t_status' => 2, 't_enabled' => 1));
        $num_tests_incomplete = $GLOBALS['SITE_DB']->query_select_value('tests', 'COUNT(*)', array('t_status' => 0, 't_enabled' => 1));
        $num_tests = $num_tests_successful + $num_tests_failed + $num_tests_incomplete;

        $testers = new Tempcode();
        $_testers1 = collapse_1d_complexity('s_assigned_to', $GLOBALS['SITE_DB']->query_select('test_sections', array('DISTINCT s_assigned_to')));
        $_testers2 = collapse_1d_complexity('t_assigned_to', $GLOBALS['SITE_DB']->query_select('tests', array('DISTINCT t_assigned_to')));
        $_testers = array_unique(array_merge($_testers1, $_testers2));
        foreach ($_testers as $tester) {
            $t_username = $GLOBALS['FORUM_DRIVER']->get_username($tester);
            if (is_null($t_username)) {
                continue;
            }

            $num_tests_successful = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'tests t LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'test_sections s ON t.t_section=s.id WHERE t.t_enabled=1 AND t.t_status=1 AND (t.t_assigned_to=' . strval($tester) . ' OR (t.t_assigned_to IS NULL AND s.s_assigned_to=' . strval($tester) . '))');
            $num_tests_failed = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'tests t LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'test_sections s ON t.t_section=s.id WHERE t.t_enabled=1 AND t.t_status=2 AND (t.t_assigned_to=' . strval($tester) . ' OR (t.t_assigned_to IS NULL AND s.s_assigned_to=' . strval($tester) . '))');
            $num_tests_incomplete = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'tests t LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'test_sections s ON t.t_section=s.id WHERE t.t_enabled=1 AND t.t_status=0 AND (t.t_assigned_to=' . strval($tester) . ' OR (t.t_assigned_to IS NULL AND s.s_assigned_to=' . strval($tester) . '))');
            $num_tests = $num_tests_successful + $num_tests_failed + $num_tests_incomplete;

            $t = do_template('TESTER_STATISTICS_MEMBER', array(
                '_GUID' => '80778fd574859f966686212566ba67bc',
                'TESTER' => $t_username,
                'NUM_TESTS' => integer_format($num_tests),
                'NUM_TESTS_SUCCESSFUL' => integer_format($num_tests_successful),
                'NUM_TESTS_FAILED' => integer_format($num_tests_failed),
                'NUM_TESTS_INCOMPLETE' => integer_format($num_tests_incomplete),
            ));
            $testers->attach($t);
        }

        return do_template('TESTER_STATISTICS_SCREEN', array(
            '_GUID' => '3f4bcbccbdc2e60ad7324cb28ed942b5',
            'TITLE' => $this->title,
            'TESTERS' => $testers,
            'NUM_TESTS' => integer_format($num_tests),
            'NUM_TESTS_SUCCESSFUL' => integer_format($num_tests_successful),
            'NUM_TESTS_FAILED' => integer_format($num_tests_failed),
            'NUM_TESTS_INCOMPLETE' => integer_format($num_tests_incomplete),
        ));
    }

    /**
     * Run through tests.
     *
     * @return Tempcode The result of execution.
     */
    public function go()
    {
        require_code('comcode_renderer');

        $show_for_all = get_param_integer('show_for_all', 0);
        $show_successful = get_param_integer('show_successful', 0);

        $tester = get_member();
        if ($GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) {
            $tester = get_param_integer('tester', get_member());
        }
        if ($show_for_all == 0) {
            $where = '(t.t_assigned_to=' . strval($tester) . ' OR (t.t_assigned_to IS NULL AND s.s_assigned_to=' . strval($tester) . '))';
        } else {
            $where = 's.id IS NOT NULL';
        }
        if ($show_successful == 0) {
            $where .= ' AND t.t_status<>1';
        }
        $where .= ' AND s.s_inheritable=0';

        $sections = new Tempcode();
        $query = 'SELECT *,t.id AS t_id FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'tests t LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'test_sections s ON t.t_section=s.id WHERE ' . $where . ' ORDER BY s.s_section,t.id';
        $_tests = $GLOBALS['SITE_DB']->query($query);
        $current = null;
        $current_2 = null;
        $current_3 = null;
        $tests = new Tempcode();
        foreach ($_tests as $test) {
            if ((!is_null($current)) && ($current != $test['t_section'])) {
                $edit_test_section_url = new Tempcode();
                if ((has_privilege(get_member(), 'edit_own_tests')) && (($test['s_assigned_to'] == get_member()) || ($GLOBALS['FORUM_DRIVER']->is_staff(get_member())))) {
                    $edit_test_section_url = build_url(array('page' => '_SELF', 'type' => '_edit', 'id' => $current), '_SELF');
                }

                $sections->attach(do_template('TESTER_GO_SECTION', array('_GUID' => '5ac788f72b881e403f75f76815706032', 'ID' => strval($current), 'EDIT_TEST_SECTION_URL' => $edit_test_section_url, 'NOTES' => $current_3, 'SECTION' => $current_2, 'TESTS' => $tests)));
                $tests = new Tempcode();
            }
            $current = $test['t_section'];
            $current_2 = $test['s_section'];
            $current_3 = $test['s_notes'];

            $a_test = make_string_tempcode(escape_html($test['t_test']));
            if (!is_null($test['t_inherit_section'])) {
                $_tests_2 = $GLOBALS['SITE_DB']->query_select('tests', array('*'), array('t_section' => $test['t_inherit_section']), 'ORDER BY id');
                if (count($_tests_2) != 0) {
                    $section_notes = $GLOBALS['SITE_DB']->query_select_value('test_sections', 's_notes', array('id' => $test['t_inherit_section']));
                    if ($section_notes != '') {
                        $a_test->attach(paragraph(escape_html($section_notes)));
                    }

                    $a_test->attach(do_template('TESTER_TEST_SET', array('_GUID' => '9f1b9f814c1e5c8dfbc051feffced72a', 'TESTS' => $this->map_keys_to_upper($_tests_2))));
                }
            }

            $bug_report_url = build_url(array('page' => '_SELF', 'type' => 'report', 'id' => $test['t_id']), '_SELF');
            $tests->attach(do_template('TESTER_GO_TEST', array('_GUID' => '1e719a51201d27eff7aed58b7f730251', 'BUG_REPORT_URL' => $bug_report_url, 'TEST' => $a_test, 'ID' => strval($test['t_id']), 'VALUE' => strval($test['t_status']))));
        }
        if (($tests->is_empty()) && ($sections->is_empty())) {
            $sections = paragraph(do_lang_tempcode('NO_ENTRIES'), '4tregerg344');
        } else {
            $edit_test_section_url = new Tempcode();
            if ((has_privilege(get_member(), 'edit_own_tests')) && (($test['s_assigned_to'] == get_member()) || ($GLOBALS['FORUM_DRIVER']->is_staff(get_member())))) {
                $edit_test_section_url = build_url(array('page' => '_SELF', 'type' => '_edit', 'id' => $test['t_section']), '_SELF');
            }

            $sections->attach(do_template('TESTER_GO_SECTION', array('_GUID' => '9bd53d8b0f0aab1a683660fac2b6ad85', 'ID' => strval($test['t_section']), 'EDIT_TEST_SECTION_URL' => $edit_test_section_url, 'NOTES' => $test['s_notes'], 'SECTION' => $test['s_section'], 'TESTS' => $tests)));
        }

        $add_test_section_url = new Tempcode();
        if (has_privilege(get_member(), 'add_tests')) {
            $add_test_section_url = build_url(array('page' => '_SELF', 'type' => 'add'), '_SELF');
        }

        $post_url = build_url(array('page' => '_SELF', 'type' => '_go'), '_SELF');

        return do_template('TESTER_GO_SCREEN', array(
            '_GUID' => '22b3b626cb510e64a795d95acc0ad8a2',
            'ADD_TEST_SECTION_URL' => $add_test_section_url,
            'SHOW_SUCCESSFUL' => strval($show_successful),
            'SHOW_FOR_ALL' => strval($show_for_all),
            'TITLE' => $this->title,
            'SECTIONS' => $sections,
            'URL' => $post_url,
        ));
    }

    /**
     * A bug report for a test.
     *
     * @return Tempcode The result of execution.
     */
    public function report()
    {
        $id = $this->id;
        $test = $this->test;
        $test_row = $this->test_row;

        require_code('feedback');

        $self_url = get_self_url();
        $forum = get_option('tester_forum_name');
        actualise_post_comment(true, 'bug_report', strval($id), $self_url, $this->self_title, $forum);

        $comment_text = str_replace('{1}', $this->test, get_option('bug_report_text'));
        $comments = get_comments('bug_report', true, strval($id), false, $forum, $comment_text);

        return do_template('TESTER_REPORT', array('_GUID' => '0c223a0a29a2c5289d71fbb69b0fe40d', 'TITLE' => $this->title, 'TEST' => $test, 'COMMENTS' => $comments));
    }

    /**
     * Save test run through results.
     *
     * @return Tempcode The result of execution.
     */
    public function _go()
    {
        foreach ($_POST as $key => $val) {
            if ((substr($key, 0, 5) == 'test_') && (is_numeric(substr($key, 5))) && (is_numeric($val))) {
                $id = intval(substr($key, 5));
                $GLOBALS['SITE_DB']->query_update('tests', array('t_status' => intval($val)), array('id' => $id), '', 1);
            }
        }

        // Show it worked / Refresh
        $show_for_all = post_param_integer('show_for_all', 0);
        $show_successful = post_param_integer('show_successful', 0);
        $url = build_url(array('page' => '_SELF', 'type' => 'go', 'show_for_all' => $show_for_all, 'show_successful' => $show_successful), '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }

    /**
     * Get a list to choose a section.
     *
     * @param  ?AUTO_LINK $it The section to select by default (null: no specific default)
     * @param  boolean $unassigned Whether to only select inheritable sections
     * @return Tempcode The list
     */
    public function get_section_list($it = null, $unassigned = false)
    {
        $list2 = new Tempcode();

        $where = null;
        if ($unassigned) {
            $where = array('s_inheritable' => 1);
        }
        $sections = $GLOBALS['SITE_DB']->query_select('test_sections', array('*'), $where, 'ORDER BY s_assigned_to,s_section');
        foreach ($sections as $_section) {
            $section = $_section['s_section'];
            $id = $_section['id'];
            $count = $GLOBALS['SITE_DB']->query_select_value('tests', 'COUNT(*)', array('t_section' => $id));
            $extra = new Tempcode();
            if (!$unassigned) {
                $username = $GLOBALS['FORUM_DRIVER']->get_username($_section['s_assigned_to']);
                if (is_null($username)) {
                    $username = do_lang('UNKNOWN');
                }
                $extra = (is_null($_section['s_assigned_to'])) ? do_lang_tempcode('TEST_UNASSIGNED') : make_string_tempcode($username);
            }
            $list2->attach(form_input_list_entry(strval($id), $it == $id, do_lang_tempcode('TEST_SECTION_ASSIGNMENT', make_string_tempcode(escape_html($section)), make_string_tempcode(integer_format($count)), $extra)));
        }

        return $list2;
    }

    /**
     * Get a list to choose a tester.
     *
     * @param  ?MEMBER $it The member to select by default (null: Select N/A)
     * @return Tempcode The list
     */
    public function get_tester_list($it)
    {
        $tester_groups = collapse_1d_complexity('group_id', $GLOBALS['SITE_DB']->query_select('group_privileges', array('group_id'), array('privilege' => 'perform_tests')));
        $admin_groups = $GLOBALS['FORUM_DRIVER']->get_super_admin_groups();
        $moderator_groups = $GLOBALS['FORUM_DRIVER']->get_moderator_groups();

        $groups = array_unique(array_merge($tester_groups, $admin_groups, $moderator_groups));

        $members = $GLOBALS['FORUM_DRIVER']->member_group_query($groups, 2000);

        $list = form_input_list_entry('-1', is_null($it), do_lang_tempcode('NA_EM'));
        foreach ($members as $member => $details) {
            $username = $GLOBALS['FORUM_DRIVER']->mrow_username($details);
            $list->attach(form_input_list_entry(strval($member), $member == $it, $username));
        }

        return $list;
    }

    /**
     * Get Tempcode for a test adding/editing form.
     *
     * @param  string $stub A short stub to prefix the field name
     * @param  SHORT_TEXT $test The text of the test
     * @param  ?MEMBER $assigned_to The member the test is assigned to (null: test section member)
     * @param  BINARY $enabled Whether the test is enabled
     * @param  string $inherit_from The section this test inherits from (blank: none)
     * @return Tempcode The Tempcode for the visible fields
     */
    public function get_test_form_fields($stub, $test = '', $assigned_to = null, $enabled = 1, $inherit_from = '')
    {
        require_code('form_templates');

        $fields = new Tempcode();
        $fields->attach(form_input_line(do_lang_tempcode('DESCRIPTION'), do_lang_tempcode('DESCRIPTION_DESCRIPTION'), $stub . '_test', $test, true));
        $list = $this->get_tester_list($assigned_to);
        $fields->attach(form_input_list(do_lang_tempcode('TESTER'), do_lang_tempcode('DESCRIPTION_TESTER_2'), $stub . '_assigned_to', $list));
        $fields->attach(form_input_tick(do_lang_tempcode('ENABLED'), do_lang_tempcode('DESCRIPTION_ENABLED'), $stub . '_enabled', $enabled == 1));
        $list2 = form_input_list_entry('-1', is_null($inherit_from), do_lang_tempcode('NA_EM'));
        $list2->attach($this->get_section_list($inherit_from, true));
        $fields->attach(form_input_list(do_lang_tempcode('INHERIT_FROM'), do_lang_tempcode('DESCRIPTION_INHERIT_FROM'), $stub . '_inherit_section', $list2));

        return $fields;
    }

    /**
     * Get Tempcode for a test section adding/editing form.
     *
     * @param  SHORT_TEXT $section The name of the section
     * @param  LONG_TEXT $notes Notes for the section
     * @param  ?MEMBER $assigned_to The member the tests are assigned to (null: not a normal section, one that gets inherited into tests)
     * @param  BINARY $inheritable Whether this test section is intended to be inherited, not used by itself
     * @return Tempcode The Tempcode for the visible fields
     */
    public function get_test_section_form_fields($section = '', $notes = '', $assigned_to = null, $inheritable = 0)
    {
        require_code('form_templates');

        $fields = new Tempcode();

        $fields->attach(form_input_line(do_lang_tempcode('NAME'), do_lang_tempcode('DESCRIPTION_NAME'), 'section', $section, true));
        $fields->attach(form_input_text(do_lang_tempcode('NOTES'), do_lang_tempcode('DESCRIPTION_NOTES'), 'notes', $notes, false));
        $list = $this->get_tester_list($assigned_to);
        $fields->attach(form_input_list(do_lang_tempcode('TESTER'), do_lang_tempcode('DESCRIPTION_TESTER_1'), 'assigned_to', $list));
        $fields->attach(form_input_tick(do_lang_tempcode('INHERITABLE'), do_lang_tempcode('DESCRIPTION_INHERITABLE'), 'inheritable', $inheritable == 1));

        return $fields;
    }

    /**
     * Interface to add a test.
     *
     * @return Tempcode The result of execution.
     */
    public function add_test()
    {
        check_privilege('add_tests');

        $list = $this->get_section_list(get_param_integer('id', -1));
        if ($list->is_empty()) {
            inform_exit(do_lang_tempcode('NO_CATEGORIES'));
        }

        require_code('form_templates');
        $fields = form_input_list(do_lang_tempcode('CATEGORY'), '', 'id', $list, null, true);
        $fields->attach($this->get_test_form_fields('add_1'));

        $post_url = build_url(array('page' => '_SELF', 'type' => '_add_test'), '_SELF');

        return do_template('FORM_SCREEN', array('_GUID' => '133ed356bc7cf270d9763f8cdc7f1d41', 'TITLE' => $this->title, 'SUBMIT_ICON' => 'menu___generic_admin__add_one', 'SUBMIT_NAME' => do_lang_tempcode('ADD_TEST'), 'URL' => $post_url, 'FIELDS' => $fields, 'TEXT' => '', 'HIDDEN' => '', 'SUPPORT_AUTOSAVE' => true));
    }

    /**
     * Actualiser to add a test.
     *
     * @return Tempcode The result of execution.
     */
    public function _add_test()
    {
        check_privilege('add_tests');

        $section_id = post_param_integer('id');
        $this->_add_new_tests($section_id);

        require_code('autosave');
        clear_cms_autosave();

        // Show it worked / Refresh
        $url = build_url(array('page' => '_SELF', 'type' => 'add_test', 'id' => $section_id), '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }

    /**
     * Inteface to add a test section.
     *
     * @return Tempcode The result of execution.
     */
    public function add()
    {
        check_privilege('add_tests');

        require_code('form_templates');

        url_default_parameters__enable();

        $fields = $this->get_test_section_form_fields();
        $add_template = do_template('TESTER_TEST_GROUP_NEW', array('_GUID' => '8a7642944a36d2f9d1ee8c076a516f43', 'ID' => 'add_-REPLACEME-', 'FIELDS' => $this->get_test_form_fields('add_-REPLACEME-')));

        url_default_parameters__disable();

        $post_url = build_url(array('page' => '_SELF', 'type' => '_add'), '_SELF');

        $tests = '';

        return do_template('TESTER_ADD_SECTION_SCREEN', array('_GUID' => '49172fc2c5ace05a632f9a5fdd91abd0', 'TITLE' => $this->title, 'SUBMIT_ICON' => 'menu___generic_admin__add_one_category', 'SUBMIT_NAME' => do_lang_tempcode('ADD_TEST_SECTION'), 'TESTS' => $tests, 'URL' => $post_url, 'FIELDS' => $fields, 'ADD_TEMPLATE' => $add_template));
    }

    /**
     * Add in any new tests added in the form.
     *
     * @param  AUTO_LINK $section_id The section to put the tests in.
     */
    public function _add_new_tests($section_id)
    {
        foreach (array_keys($_POST) as $key) {
            $matches = array();
            if (preg_match('#add_(\d+)_test#A', $key, $matches) != 0) {
                $id = intval($matches[1]);

                $assigned_to = post_param_integer('add_' . strval($id) . '_assigned_to');
                if ($assigned_to == -1) {
                    $assigned_to = null;
                }

                $inherit_section = post_param_integer('add_' . strval($id) . '_inherit_section');
                if ($inherit_section == -1) {
                    $inherit_section = null;
                }

                $GLOBALS['SITE_DB']->query_insert('tests', array(
                    't_section' => $section_id,
                    't_test' => post_param_string('add_' . strval($id) . '_test'),
                    't_assigned_to' => $assigned_to,
                    't_enabled' => post_param_integer('add_' . strval($id) . '_enabled', 0),
                    't_status' => 0,
                    't_inherit_section' => $inherit_section
                ));
            }
        }
    }

    /**
     * Actualiser to add a test section.
     *
     * @return Tempcode The result of execution.
     */
    public function _add()
    {
        check_privilege('add_tests');

        $assigned_to = post_param_integer('assigned_to');
        if ($assigned_to == -1) {
            $assigned_to = null;
        }

        $section_id = $GLOBALS['SITE_DB']->query_insert('test_sections', array(
            's_section' => post_param_string('section'),
            's_notes' => post_param_string('notes'),
            's_inheritable' => post_param_integer('inheritable', 0),
            's_assigned_to' => $assigned_to
        ), true);

        $this->_add_new_tests($section_id);

        // Show it worked / Refresh
        $url = build_url(array('page' => '_SELF', 'type' => 'add'), '_SELF');
        return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
    }

    /**
     * Choose a test section to edit.
     *
     * @return Tempcode The result of execution.
     */
    public function edit()
    {
        check_privilege('edit_own_tests');
        if (!$GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
            access_denied('STAFF_ONLY');
        }

        $list = $this->get_section_list();
        if ($list->is_empty()) {
            inform_exit(do_lang_tempcode('NO_ENTRIES'));
        }

        $text = paragraph(do_lang_tempcode('CHOOSE_EDIT_LIST'));
        $post_url = build_url(array('page' => '_SELF', 'type' => '_edit'), '_SELF', null, false, true);
        require_code('form_templates');
        $fields = form_input_list(do_lang_tempcode('NAME'), '', 'id', $list, null, true);
        $submit_name = do_lang_tempcode('PROCEED');

        return do_template('FORM_SCREEN', array('_GUID' => '37f70ba9d23204bceda6e84375b52270', 'GET' => true, 'SKIP_WEBSTANDARDS' => true, 'HIDDEN' => '', 'TITLE' => $this->title, 'TEXT' => $text, 'URL' => $post_url, 'FIELDS' => $fields, 'SUBMIT_ICON' => 'buttons__proceed', 'SUBMIT_NAME' => $submit_name, 'SUPPORT_AUTOSAVE' => true));
    }

    /**
     * Interface to edit a test section.
     *
     * @return Tempcode The result of execution.
     */
    public function _edit()
    {
        check_privilege('edit_own_tests');

        $id = get_param_integer('id');
        $rows = $GLOBALS['SITE_DB']->query_select('test_sections', array('*'), array('id' => $id), '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit('MISSING_RESOURCE');
        }
        $section = $rows[0];

        if (!((has_privilege(get_member(), 'edit_own_tests')) && (($section['s_assigned_to'] == get_member()) || ($GLOBALS['FORUM_DRIVER']->is_staff(get_member()))))) {
            access_denied('ACCESS_DENIED');
        }

        $fields = $this->get_test_section_form_fields($section['s_section'], $section['s_notes'], $section['s_assigned_to'], $section['s_inheritable']);
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => '2ded201b1b60b2bfc21f159ce4e4f3c1', 'TITLE' => do_lang_tempcode('ACTIONS'))));
        $fields->attach(form_input_tick(do_lang_tempcode('DELETE'), do_lang_tempcode('DESCRIPTION_DELETE'), 'delete', false));

        $add_template = do_template('TESTER_TEST_GROUP_NEW', array('_GUID' => '3d0e12fdff0aef8f8aa5818e441238ee', 'ID' => 'add_-REPLACEME-', 'FIELDS' => $this->get_test_form_fields('add_-REPLACEME-')));

        $_tests = $GLOBALS['SITE_DB']->query_select('tests', array('*'), array('t_section' => $id), 'ORDER BY id');
        $tests = new Tempcode();
        foreach ($_tests as $test) {
            $_fields = $this->get_test_form_fields('edit_' . strval($test['id']), $test['t_test'], $test['t_assigned_to'], $test['t_enabled'], $test['t_inherit_section']);
            $_fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => '21f8a24eb794f271137d72360fb78136', 'TITLE' => do_lang_tempcode('ACTIONS'))));
            $_fields->attach(form_input_tick(do_lang_tempcode('DELETE'), do_lang_tempcode('DESCRIPTION_DELETE'), 'edit_' . strval($test['id']) . '_delete', false));
            $_test = do_template('TESTER_TEST_GROUP', array('_GUID' => '620b45c5ff5bf26417442865e6bcb045', 'ID' => 'edit_' . strval($test['id']), 'FIELDS' => $_fields));
            $tests->attach($_test);
        }

        $post_url = build_url(array('page' => '_SELF', 'type' => '__edit', 'id' => $id), '_SELF');

        require_code('autosave');
        clear_cms_autosave();

        return do_template('TESTER_ADD_SECTION_SCREEN', array('_GUID' => 'ee10a568b6dacd8baf1efeac3e7bcb40', 'TITLE' => $this->title, 'SUBMIT_ICON' => 'menu___generic_admin__edit_one_category', 'SUBMIT_NAME' => do_lang_tempcode('SAVE'), 'TESTS' => $tests, 'URL' => $post_url, 'FIELDS' => $fields, 'ADD_TEMPLATE' => $add_template));
    }

    /**
     * Turn keys of a map to upper case, and return modified map.
     *
     * @param  array $array Input map
     * @return array Adjusted map
     */
    public function map_keys_to_upper($array)
    {
        $out = array();
        foreach ($array as $key => $val) {
            $out[strtoupper($key)] = $val;
        }
        return $out;
    }

    /**
     * Actualiser to edit a test section.
     *
     * @return Tempcode The result of execution.
     */
    public function __edit()
    {
        check_privilege('edit_own_tests');

        $id = get_param_integer('id');
        $rows = $GLOBALS['SITE_DB']->query_select('test_sections', array('*'), array('id' => $id), '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit('MISSING_RESOURCE');
        }
        $section = $rows[0];

        if (!((has_privilege(get_member(), 'edit_own_tests')) && (($section['s_assigned_to'] == get_member()) || ($GLOBALS['FORUM_DRIVER']->is_staff(get_member()))))) {
            access_denied('ACCESS_DENIED');
        }

        if (post_param_integer('delete', 0) == 1) {
            $GLOBALS['SITE_DB']->query_delete('test_sections', array('id' => $id), '', 1);
            $GLOBALS['SITE_DB']->query_delete('tests', array('t_section' => $id));

            return inform_screen($this->title, do_lang_tempcode('SUCCESS'));
        } else {
            // New tests
            $this->_add_new_tests($id);

            $assigned_to = post_param_integer('assigned_to');
            if ($assigned_to == -1) {
                $assigned_to = null;
            }

            $GLOBALS['SITE_DB']->query_update('test_sections', array(
                's_section' => post_param_string('section'),
                's_notes' => post_param_string('notes'),
                's_inheritable' => post_param_integer('inheritable', 0),
                's_assigned_to' => $assigned_to
            ), array('id' => get_param_integer('id')), '', 1);

            // Tests that are edited/deleted (or possibly unchanged, but we count that as edited)
            foreach (array_keys($_POST) as $key) {
                $matches = array();
                if (preg_match('#edit_(\d+)_test#', $key, $matches) != 0) {
                    $tid = $matches[1];
                    $delete = post_param_integer('edit_' . $tid . '_delete', 0);
                    if ($delete == 1) {
                        $GLOBALS['SITE_DB']->query_delete('tests', array('id' => $tid), '', 1);
                    } else {
                        $assigned_to = post_param_integer('edit_' . $tid . '_assigned_to');
                        if ($assigned_to == -1) {
                            $assigned_to = null;
                        }

                        $inherit_section = post_param_integer('edit_' . $tid . '_inherit_section');
                        if ($inherit_section == -1) {
                            $inherit_section = null;
                        }

                        $GLOBALS['SITE_DB']->query_update('tests', array(
                            't_test' => post_param_string('edit_' . $tid . '_test'),
                            't_assigned_to' => $assigned_to,
                            't_enabled' => post_param_integer('edit_' . $tid . '_enabled', 0),
                            't_inherit_section' => $inherit_section
                        ), array('id' => $tid), '', 1);
                    }
                }
            }

            // Show it worked / Refresh
            $url = build_url(array('page' => '_SELF', 'type' => 'go'), '_SELF');
            return redirect_screen($this->title, $url, do_lang_tempcode('SUCCESS'));
        }
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class tasks_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('files');
    }

    public function testNewsletterCSV()
    {
        $only = get_param_string('only', null);
        if (($only !== null) && ($only != 'testNewsletterCSV')) {
            return;
        }

        $tmp_path = cms_tempnam();

        cms_file_put_contents_safe($tmp_path, "Email,Name\ntest@example.com,Test");

        require_code('hooks/systems/tasks/import_newsletter_subscribers');
        $ob_import = new Hook_task_import_newsletter_subscribers();
        $ob_import->run(fallback_lang(), db_get_first_id(), 1, $tmp_path);

        $this->establish_admin_session();
        $session_id = $this->establish_admin_callback_session();
        $url = build_url(array('page' => 'admin_newsletter', 'type' => 'subscribers', 'id' => db_get_first_id(), 'lang' => fallback_lang(), 'csv' => 1), 'adminzone');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => $session_id));
        $this->assertTrue(strpos($data, 'test@example.com') !== false);

        file_put_contents($tmp_path, $data);
        $ob_import->run(fallback_lang(), db_get_first_id(), 1, $tmp_path);
    }

    public function testCatalogueCSV()
    {
        $only = get_param_string('only', null);
        if (($only !== null) && ($only != 'testCatalogueCSV')) {
            return;
        }

        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogues', 'c_name', array('c_name' => 'links'));
        if ($test === null) {
            $this->assertTrue(false, 'links catalogue not available, test cannot run');
            return;
        }

        $tmp_path = cms_tempnam();

        cms_file_put_contents_safe($tmp_path, "Title,URL,Description\nTestingABC,http://example.com,Test");

        require_code('hooks/systems/tasks/import_catalogue');
        $ob_import = new Hook_task_import_catalogue();
        $import_result = $ob_import->run('links', 'Title', 'add', 'leave', 'skip', '', '', '', true, true, true, $tmp_path);

        require_code('hooks/systems/tasks/export_catalogue');
        $ob_export = new Hook_task_export_catalogue();
        $results = $ob_export->run('links');
        $c = cms_file_get_contents_safe($results[1][1]);
        $this->assertTrue(strpos($c, 'TestingABC') !== false, 'Did not see our TestingABC record in: ' . $c . "\n\n" . serialize($import_result));

        $ob_import->run('links', 'Title', 'add', 'leave', 'skip', '', '', '', true, true, true, $results[1][1]);
    }

    public function testCalendarICal()
    {
        $only = get_param_string('only', null);
        if (($only !== null) && ($only != 'testCalendarICal')) {
            return;
        }

        if ($GLOBALS['SITE_DB']->query_select_value('calendar_events', 'COUNT(*)') > 1000) {
            $this->assertTrue(false, 'Test will not work on databases with a lot of calendar events');
            return;
        }

        require_code('calendar2');

        // Add complex event with start and recurrence
        $complex_event_id = add_calendar_event(8, 'daily', 3, 0, 'complex event', '', 3, 2010, 1, 10, 'day_of_month', 10, 15, 2010, 1, 10, 'day_of_month', 11, 15, null, 1, null, 1, 1, 1, 1, '', null, 0, null, null, null);

        // Add event with start only
        $simple_event_id = add_calendar_event(8, 'none', null, 0, 'simple event', '', 3, 2010, 1, 10, 'day_of_month', 10, 15, null, null, null, 'day_of_month', null, null, null, 1, null, 1, 1, 1, 1, '', null, 0, null, null, null);

        $last_rows_before = $GLOBALS['SITE_DB']->query_select('calendar_events', array('*'), array(), 'ORDER BY id DESC', 2);
        $this->cleanEventRowsForComparison($last_rows_before);

        require_code('calendar_ical');
        ob_start();
        output_ical(false);
        $ical = ob_get_contents();
        ob_end_clean();

        $temp_path = cms_tempnam();
        file_put_contents($temp_path, $ical);

        /* Not working, saying page expired :(
        $result = http_download_file('https://ical-validator.herokuapp.com/validate/');
        if ($result !== null) {
            $post_params = array('snippet' => $ical, 'Validate' => '');

            $matches = array();
            preg_match('#<form [^<>]*method="post" action="([^"]*snippetForm[^"]*)"#', $result, $matches);
            $rel_url = $matches[1];
            $url = qualify_url(html_entity_decode($rel_url, ENT_QUOTES), 'https://ical-validator.herokuapp.com/validate/');

            preg_match('#jsessionid=(\w+)#', $result, $matches);
            $session_id = $matches[1];
            $cookies = array('JSESSIONID' => $session_id);

            $result = http_download_file($url, null, true, false, 'Composr', $post_params, $cookies);
            if ($debugging) {
                @var_dump($url);
                @var_dump($result);
                exit();
            }
        }
        if ($result === null) {
            $this->assertTrue(false, 'ical validator is down?');
        } else {
            $this->assertTrue((strpos($result, '1 results in 1 components') !== false) && (strpos($result, 'CRLF should be used for newlines') !== false));
        }*/

        delete_calendar_event($complex_event_id);
        delete_calendar_event($simple_event_id);

        $num_events_before = $GLOBALS['SITE_DB']->query_select_value('calendar_events', 'COUNT(*)');

        ical_import($temp_path);

        $num_events_after = $GLOBALS['SITE_DB']->query_select_value('calendar_events', 'COUNT(*)');
        $this->assertTrue($num_events_after > $num_events_before);

        $last_rows_after = $GLOBALS['SITE_DB']->query_select('calendar_events', array('*'), array(), 'ORDER BY id DESC', 2);
        $_last_rows_after = $last_rows_after;
        $this->cleanEventRowsForComparison($last_rows_after);

        $ok = ($last_rows_before == $last_rows_after);
        $this->assertTrue($ok);
        if ((!$ok) && (get_param_integer('debug', 0) == 1)) {
            @var_dump($last_rows_before);
            @var_dump($last_rows_after);
        }

        foreach ($_last_rows_after as $row) {
            delete_calendar_event($row['id']);
        }

        unlink($temp_path);
    }

    protected function cleanEventRowsForComparison(&$rows)
    {
        foreach ($rows as &$row) {
            unset($row['id']);
            unset($row['e_add_date']);
            $row['e_title'] = get_translated_text($row['e_title']);
            $row['e_content'] = get_translated_text($row['e_content']);
            unset($row['e_title__text_parsed']);
            unset($row['e_content__text_parsed']);
        }
    }

    public function testMemberCSV()
    {
        $only = get_param_string('only', null);
        if (($only !== null) && ($only != 'testMemberCSV')) {
            return;
        }

        if ($GLOBALS['FORUM_DB']->query_select_value('f_members', 'COUNT(*)') > 1000) {
            $this->assertTrue(false, 'Test will not work on databases with a lot of users');
            return;
        }

        $tmp_path = cms_tempnam();

        cms_file_put_contents_safe($tmp_path, "Username,E-mail\nTestingABC,test@example.com");

        require_code('hooks/systems/tasks/import_member_csv');
        $ob_import = new Hook_task_import_member_csv();
        $ob_import->run('', false, $tmp_path);

        require_code('hooks/systems/tasks/download_member_csv');
        $ob_export = new Hook_task_download_member_csv();
        $results = $ob_export->run(false, '.csv', '', array('ID', 'Username'), array(), 'ID');
        $this->assertTrue(strpos(cms_file_get_contents_safe($results[1][1]), 'TestingABC') !== false);

        $ob_import->run('', false, $results[1][1]);
    }
}

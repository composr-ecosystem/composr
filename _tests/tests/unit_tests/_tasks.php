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

/*EXTRA FUNCTIONS: sleep*/

/**
 * Composr test case class (unit testing).
 */
class _tasks_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('files');
        require_code('tasks');
    }

    public function testNewsletterSpreadsheet()
    {
        if (($this->only !== null) && ($this->only != 'testNewsletterSpreadsheet')) {
            return;
        }

        if (!addon_installed('newsletter')) {
            $this->assertTrue(false, 'Cannot run test without newsletter addon');
            return;
        }

        $tmp_path = cms_tempnam();

        cms_file_put_contents_safe($tmp_path, "Email,Name\ntest@example.com,Test", FILE_WRITE_BOM);

        require_code('hooks/systems/tasks/import_newsletter_subscribers');
        $ob_import = new Hook_task_import_newsletter_subscribers();
        $ob_import->run(fallback_lang(), db_get_first_id(), true, $tmp_path, 'test.csv');

        $session_id = $this->establish_admin_callback_session();
        $url = build_url(['page' => 'admin_newsletter', 'type' => 'subscribers', 'id' => db_get_first_id(), 'lang' => fallback_lang(), 'spreadsheet' => 1, 'file_type' => 'csv'], 'adminzone');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'cookies' => [get_session_cookie() => $session_id]]);
        $this->assertTrue(strpos($data, 'test@example.com') !== false);

        file_put_contents($tmp_path, $data);
        $ob_import->run(fallback_lang(), db_get_first_id(), true, $tmp_path, 'test.csv');
    }

    public function testCatalogueSpreadsheet()
    {
        if (($this->only !== null) && ($this->only != 'testCatalogueSpreadsheet')) {
            return;
        }

        if (!addon_installed('catalogues')) {
            $this->assertTrue(false, 'Cannot run test without catalogues addon');
        }

        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogues', 'c_name', ['c_name' => 'links']);
        if ($test === null) {
            $this->assertTrue(false, 'links catalogue not available, test cannot run');
            return;
        }

        $tmp_path = cms_tempnam();

        cms_file_put_contents_safe($tmp_path, "Title,URL,Description\nTestingABC,http://example.com,Test", FILE_WRITE_BOM);

        require_code('hooks/systems/tasks/import_catalogue');
        $ob_import = new Hook_task_import_catalogue();
        $import_result = $ob_import->run('links', 'Title', 'add', 'leave', 'skip', '', '', '', true, true, true, $tmp_path, 'test.csv');

        require_code('hooks/systems/tasks/export_catalogue');
        $ob_export = new Hook_task_export_catalogue();
        $results = $ob_export->run('links', 'csv');
        $c = cms_file_get_contents_safe($results[1][1], FILE_READ_LOCK);
        $this->assertTrue(strpos($c, 'TestingABC') !== false, 'Did not see our TestingABC record in: ' . $c . "\n\n" . serialize($import_result));

        $ob_import->run('links', 'Title', 'add', 'leave', 'skip', '', '', '', true, true, true, $results[1][1], 'test.csv');
    }

    public function testCalendarICal()
    {
        if (($this->only !== null) && ($this->only != 'testCalendarICal') && ($this->only != 'testCalendarICalNoValidator')) {
            return;
        }

        if ($GLOBALS['SITE_DB']->query_select_value('calendar_events', 'COUNT(*)') > 1000) {
            $this->assertTrue(false, 'Test will not work on databases with a lot of calendar events');
            return;
        }

        if (!addon_installed('calendar')) {
            $this->assertTrue(false, 'Cannot run test without calendar addon');
            return;
        }

        $this->establish_admin_session();

        require_code('calendar2');

        // Add complex event with start and recurrence
        $complex_event_id = add_calendar_event(8, 'daily', 3, 0, 'complex event', '', 3, 2010, 1, 10, 'day_of_month', 10, 15, 2010, 1, 10, 'day_of_month', 11, 15, null, 1, null, 1, 1, 1, 1, '', null, 0, time() - 1, null, null);

        // Add event with start only
        $simple_event_id = add_calendar_event(8, 'none', null, 0, 'simple event', '', 3, 2010, 1, 10, 'day_of_month', 10, 15, null, null, null, 'day_of_month', null, null, null, 1, null, 1, 1, 1, 1, '', null, 0, time(), null, null);

        $last_rows_before = $GLOBALS['SITE_DB']->query_select('calendar_events', ['*'], [], 'ORDER BY e_add_date DESC,id DESC', 2);
        $this->clean_event_rows_for_comparison($last_rows_before);

        require_code('calendar_ical');
        ob_start();
        output_ical(false);
        $ical = ob_get_contents();
        ob_end_clean();

        $temp_path = cms_tempnam();
        rename($temp_path, $temp_path . '.ics');
        $temp_path .= '.ics';
        file_put_contents($temp_path, $ical);

        /*
        This validator seems to be down now, so we implement a new one below
        $post_params = ['snip' => $ical];
        $url = 'http://severinghaus.org/projects/icv/';
        if ($result === null) {
            $this->assertTrue(false, 'ical validator is down?');
        } else {
            $this->assertTrue(strpos($result, 'Congratulations; your calendar validated!') !== false);
        }
        */

        $result = mixed();
        if ($this->only != 'testCalendarICalNoValidator') {
            $result = http_get_contents('https://ical-validator.herokuapp.com/validate/', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'timeout' => 20.0]);
        } else {
            $result = false;
        }
        if ($result !== null && $result !== false) {
            /* Could not get this working with upload method
            $matches = [];
            preg_match('#<form id="id2" method="post" action="([^"]*)"#', $result, $matches);
            $rel_url = $matches[1];
            preg_match('#jsessionid=(\w+)#', $result, $matches);
            $session_id = $matches[1];
            $files = ['file' => $temp_path];
            $post_params = ['id2_hf_0' => '', 'Validate' => ''];
            $cookies = ['JSESSIONID' => $session_id];
            $extra_headers = [];
            $url = qualify_url(html_entity_decode($rel_url, ENT_QUOTES), 'https://ical-validator.herokuapp.com/validate/');
            $result = http_get_contents($url, ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'ignore_http_status' => $this->debug, 'trigger_error' => false, 'files' => $files, 'post_params' => $post_params, 'cookies' => $cookies, 'extra_headers' => $extra_headers]);
            */

            $matches = [];
            preg_match('#<form [^<>]*method="post" action="([^"]*snippetForm[^"]*)"#', $result, $matches);
            $rel_url = $matches[1];
            require_code('character_sets');
            $ical = convert_to_internal_encoding($ical, get_charset(), 'utf-8');
            $post_params = ['snippet' => $ical];
            $url = qualify_url(html_entity_decode($rel_url, ENT_QUOTES), 'https://ical-validator.herokuapp.com/validate/');
            $result = http_get_contents($url, ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'ignore_http_status' => $this->debug, 'trigger_error' => false, 'post_params' => $post_params]);
            if ($this->debug) {
                @var_dump($url);
                @var_dump($result);
                exit();
            }
        }
        if ($result === false) {
            // Skipped
        } elseif ($result === null) {
            //Validator often down also so show no error $this->assertTrue(false, 'ical validator is down?');
        } else {
            $this->assertTrue((strpos($result, '1 results in 1 components') !== false) && (strpos($result, 'CRLF should be used for newlines')/*bug in validator*/ !== false), $result);
        }

        delete_calendar_event($complex_event_id);
        delete_calendar_event($simple_event_id);

        $num_events_before = $GLOBALS['SITE_DB']->query_select_value('calendar_events', 'COUNT(*)');
        ical_import($temp_path);
        $num_events_after = $GLOBALS['SITE_DB']->query_select_value('calendar_events', 'COUNT(*)');
        $this->assertTrue($num_events_after > $num_events_before, 'Did not appear to import events (' . integer_format($num_events_after) . ' after, ' . integer_format($num_events_before) . ' before)');

        $_last_rows_after = $GLOBALS['SITE_DB']->query_select('calendar_events', ['*'], [], 'ORDER BY e_add_date DESC,id DESC', 2);
        $last_rows_after = $_last_rows_after;
        $this->clean_event_rows_for_comparison($last_rows_after);

        $ok = ($last_rows_before == $last_rows_after);
        $this->assertTrue($ok, 'Our test events changed during the export/import cycle)');
        if ((!$ok) && ($this->debug)) {
            @var_dump($last_rows_before);
            @var_dump($last_rows_after);
        }

        foreach ($_last_rows_after as $row) {
            delete_calendar_event($row['id']);
        }

        unlink($temp_path);
    }

    protected function clean_event_rows_for_comparison(&$rows)
    {
        foreach ($rows as &$row) {
            unset($row['id']);
            unset($row['e_add_date']);
            $row['e_title'] = get_translated_text($row['e_title']);
            unset($row['e_title__text_parsed']);
            $row['e_content'] = get_translated_text($row['e_content']);
            unset($row['e_content__text_parsed']);
        }
    }

    public function testMemberSpreadsheet()
    {
        if (($this->only !== null) && ($this->only != 'testMemberSpreadsheet')) {
            return;
        }

        if ($GLOBALS['FORUM_DB']->query_select_value('f_members', 'COUNT(*)') > 1000) {
            $this->assertTrue(false, 'Test will not work on databases with a lot of users');
        }

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Cannot run test when not running Conversr');
            return;
        }

        $tmp_path = cms_tempnam();

        cms_file_put_contents_safe($tmp_path, "Username,E-mail\nTestingABC,test@example.com", FILE_WRITE_BOM);

        require_code('hooks/systems/tasks/import_members');
        $ob_import = new Hook_task_import_members();
        $ob_import->run('', false, $tmp_path, 'test.csv');

        @unlink($tmp_path);

        require_code('hooks/systems/tasks/export_members');
        $ob_export = new Hook_task_export_members();
        $results = $ob_export->run(false, ['ID', 'Username'], [], 'ID', 'csv');
        $this->assertTrue(strpos(cms_file_get_contents_safe($results[1][1], FILE_READ_LOCK | FILE_READ_BOM), 'TestingABC') !== false);

        $ob_import->run('', false, $results[1][1], 'test.csv');
    }

    public function testUnnecessaryTaskNotifications()
    {
        $files = get_directory_contents(get_file_base(), get_file_base(), null, true, true, ['php']);
        foreach ($files as $path) {
            // Exceptions
            if (in_array(basename($path), [
                '_tasks.php' // Testing ourselves will result in a false positive
            ])) {
                continue;
            }

            $code = cms_file_get_contents_safe($path, FILE_READ_LOCK);
            $task_calls = preg_grep('/call_user_func_array__long_task\(/', explode("\n", $code));
            foreach ($task_calls as $line_number => $line) {
                if (strpos($line, 'function call_user_func_array__long_task') !== false) {
                    continue; // Skip the actual function definition
                }

                $params = $this->extract_parameters($line);
                $title = strtolower($params[1]);
                $send_notification = !isset($params[6]) || strtolower($params[6]) == 'true';

                if (isset($params[6]) && ($params[6] == '$send_notification')) {
                    continue; // No testing in cases where we use the $send_notification variable
                }

                $has_potential_return = ((strpos($line, '= call_user_func_array__long_task') !== false) || strpos($line, 'return call_user_func_array__long_task') !== false);

                $this->assertTrue((($title != 'null') || (!$send_notification) || ($has_potential_return)), 'Sending notifications might not be necessary (or set a title) for call_user_func_array__long_task in ' . $path . ' on line ' . strval($line_number + 1));
                $this->assertTrue(($send_notification || (($title == 'null') && !$has_potential_return)), 'Sending notifications might be necessary (or set title to null) for call_user_func_array__long_task in ' . $path . ' on line ' . strval($line_number + 1));
            }
        }
    }

    private function extract_parameters(string $line) : array
    {
        // Find the position of the opening parenthesis
        $start_pos = strpos($line, '(');

        // Find the position of the closing parenthesis
        $end_pos = strrpos($line, ')');

        // Extract the parameters part of the string
        $params_string = substr($line, $start_pos + 1, $end_pos - $start_pos - 1);

        // Parse parameters manually
        $params = [];
        $current_param = '';
        $in_array = 0;
        for ($i = 0; $i < strlen($params_string); $i++) {
            $char = $params_string[$i];
            if ($char == ',' && $in_array <= 0) {
                $params[] = trim($current_param, ", '");
                $current_param = '';
            } elseif (($char == '[') || ($char == '(')) {
                $in_array++;
            } elseif (($char == ']') || ($char == ')')) {
                $in_array--;
            }
            $current_param .= $char;
        }
        $params[] = trim($current_param, ", '"); // Add the last parameter

        return $params;
    }
}

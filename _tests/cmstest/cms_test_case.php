<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class cms_test_case extends WebTestCase
{
    public $site_closed;
    protected $only = null;
    protected $debug = false;

    public function setUp()
    {
        require_code('config2');

        @ignore_user_abort(false);

        static $done_once = false;
        if (!$done_once) {
            // Make sure the site is open
            $this->site_closed = get_option('site_closed');
            set_option('site_closed', '0');
            $done_once = true;
            cms_register_shutdown_function_if_available([$this, 'reopen_site']);
        }

        $args = isset($_SERVER['argv']) ? array_slice($_SERVER['argv'], 2) : [];

        $this->debug = (get_param_integer('debug', 0) == 1);
        if (in_array('debug', $args)) {
            $this->debug = true;
            unset($args[array_search('debug', $args)]);
            $_GET['debug'] = '1'; // For code that doesn't look at $this->debug
        }

        $this->only = get_param_string('only', null);
        if (($this->only === null) && (array_key_exists(0, $args))) {
            $this->only = $args[0];
        }

        cms_ini_set('ocproducts.type_strictness', '1');

        // We need to be compatible with low memory limits
        if (memory_get_usage() < 30/*a little give*/ * 1024 * 1024) { // If not already in a high-memory (raised-memory) test set
            cms_ini_set('memory_limit', '32M');
            set_value('memory_limit', '32M');
        }
    }

    public function reopen_site()
    {
        if ($this->site_closed !== null) {
            set_option('site_closed', $this->site_closed);
        }
    }

    public function tearDown()
    {
        cms_ini_set('ocproducts.type_strictness', '0');

        delete_value('memory_limit');
    }

    protected function should_filter_cqc_line($line)
    {
        return (
            (trim($line) == '') ||
            (substr($line, 0, 5) == 'SKIP:') ||
            (substr($line, 0, 5) == 'DONE ') ||
            (substr($line, 0, 6) == 'FINAL ') ||
            ((strpos($line, 'comment found') !== false) && (strpos($line, '#') !== false)) ||
            ((strpos($line, 'TODO') !== false) && (strpos($line, 'v' . strval(intval(cms_version_number()) + 1)) !== false)) ||
            (strpos($line, 'LEGACY') !== false) ||
            (strpos($line, 'FUDGE') !== false) ||
            (strpos($line, 'FRAGILE') !== false)
        );
    }

    protected function extend_cqc_call($url)
    {
        $url .= '&api=1&todo=1&somewhat_pedantic=1';
        if ($this->debug) {
            if (strpos(shell_exec('npx eslint -v'), 'v') !== false) {
                $url .= '&codesniffer=1';
            }
        }
        $url .= '&base_path=' . urlencode(get_file_base());
        if ($this->debug) {
            $url .= '&manual_checks=1&pedantic=1&security=1&mixed=1';
        }
        return $url;
    }

    public function get($url, $parameters = null)
    {
        $parts = [];
        if ((preg_match('#([' . URL_CONTENT_REGEXP . ']*):([' . URL_CONTENT_REGEXP . ']+|[^/]|$)((:(.*))*)#', $url, $parts) != 0) && ($parts[1] != 'mailto')) { // Specially encoded page-link. Complex regexp to make sure URLs do not match
            $real_url = page_link_to_url($url);
        } else {
            $real_url = $url;
        }
        $ret = http_get_contents($real_url);

        require_code('files');

        // Save, so we can run webstandards checker on it later
        $path = get_file_base() . '/_tests/html_dump/' . get_class($this);
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        cms_file_put_contents_safe($path . '/' . url_to_filename($url) . '.htm.tmp', $ret, FILE_WRITE_FIX_PERMISSIONS);

        // Save the text so we can run through Word's grammar checker
        $text_content = $ret;
        $text_content = preg_replace('#<[^>]* title="([^"]+)"<[^>]*>#U', '\\1', $text_content);
        $text_content = preg_replace('#<[^>]* alt="([^"]+)"<[^>]*>#U', '\\1', $text_content);
        $text_content = preg_replace('#<style[^>]*>.*</style>#Us', '', $text_content);
        $text_content = preg_replace('#<script[^>]*>.*</script>#Us', '', $text_content);
        $text_content = preg_replace('#<[^>]*>#U', '', $text_content);
        $text_content = preg_replace('#\s\s+#', '. ', $text_content);
        $text_content = str_replace('&ndash;', '-', $text_content);
        $text_content = str_replace('&mdash;', '-', $text_content);
        $text_content = str_replace('&hellip;', '...', $text_content);
        $text_content = @html_entity_decode($text_content, ENT_QUOTES);
        cms_file_put_contents_safe($path . '/' . url_to_filename($url) . '.txt.tmp', $text_content, FILE_WRITE_FIX_PERMISSIONS);

        return $ret;
    }

    // Establishes an admin session for the current web user, NOT for the server to call itself as an admin
    protected function establish_admin_session()
    {
        static $done_once = false;
        if ($done_once) {
            return get_session_id();
        }
        $done_once = true;

        global $MEMBER_CACHED;
        require_code('users_active_actions');
        $MEMBER_CACHED = restricted_manually_enabled_backdoor();

        $this->dump($this->browser->getContent());

        return get_session_id();
    }

    // Establishes an admin session on the server's likely callback IP
    protected function establish_admin_callback_session()
    {
        require_code('users_active_actions');
        require_code('users_inactive_occasionals');

        $ip_address = get_server_external_looparound_ip();

        return create_session(get_first_admin_user(), 1, false, false, $ip_address);
    }

    protected function load_key_options($substring, $substring_is_at_start = true)
    {
        $ret = [];

        $path = get_file_base() . '/_tests/assets/keys.csv';
        if (!is_file($path)) {
            $this->assertTrue(false, 'Cannot proceed, we need _tests/assets/keys.csv, which is not supplied in Git for security reasons');
            exit();
        }

        require_code('config2');

        require_code('files_spreadsheets_read');

        $sheet_reader = spreadsheet_open_read($path);
        while (($row = $sheet_reader->read_row()) !== false) {
            if (!isset($row['Option'])) {
                exit('Option column missing');
            }
            $option_name = $row['Option'];

            if ($option_name[0] == '#') { // Comment
                continue;
            }

            if (!isset($row['Value'])) {
                exit('Value column missing');
            }
            $option_value = $row['Value'];

            if (!isset($row['Type'])) {
                exit('Type column missing');
            }
            $option_type = $row['Type'];

            if ($substring_is_at_start) {
                $does_match = (substr($option_name, 0, strlen($substring)) == $substring);
            } else {
                $does_match = (strpos($option_name, $substring) !== false);
            }

            if ($does_match) {
                switch ($option_type) {
                    case 'option':
                        set_option($option_name, $option_value);
                        break;

                    case 'hidden':
                        set_value($option_name, $option_value);
                        break;

                    case 'hidden_elective':
                        set_value($option_name, $option_value, true);
                        break;

                    case 'return':
                        $ret[$option_name] = $option_value;
                        break;
                }
            }
        }
        $sheet_reader->close();

        return $ret;
    }

    protected function run_health_check($category_label, $section_label, $check_context = 1/*CHECK_CONTEXT__TEST_SITE*/, $use_test_data_for_pass = null)
    {
        if ($this->debug) {
            $GLOBALS['UNIT_TEST_WITH_DEBUG'] = true;
        }

        $sections_run = 0;

        $sections_to_run = [];
        $sections_to_run[] = $category_label . ' \\ ' . $section_label;

        require_code('health_check');
        $hook_obs = find_all_hook_obs('systems', 'health_checks', 'Hook_health_check_');
        foreach ($hook_obs as $ob) {
            list($category_label, $sections) = $ob->run($sections_to_run, $check_context, false, false, $use_test_data_for_pass);

            $sections_run += count($sections);

            $_sections = [];
            foreach ($sections as $section_label => $results) {
                foreach ($results as $_result) {
                    $this->assertTrue($_result[0] == 'PASS', $_result[1]);
                }
            }
        }

        if (($sections_to_run !== null) && ($sections_run < count($sections_to_run))) {
            fatal_exit('Missing Health Check ' . $category_label . '/' . $section_label . '; possibly something needs configuring still for it to run');
        }
    }

    protected function get_canonical_member_id($username)
    {
        return $GLOBALS['FORUM_DRIVER']->get_member_from_username($this->get_canonical_username($username));
    }

    protected function get_canonical_username($username)
    {
        if (get_forum_type() != 'cns') {
            // Dodgy, but may be enough to make it work...
            require_code('users_active_actions');
            $first_admin = get_first_admin_user();

            if ($username == 'test') {
                // Try to find a member who is not the first admin
                $member_id = null;
                $start = null;
                do {
                    $rows = $GLOBALS['FORUM_DRIVER']->get_next_members($start);
                    if (empty($rows)) {
                        break;
                    }

                    $_member_id = $GLOBALS['FORUM_DRIVER']->mrow_member_id($rows[0]);
                    if ($_member_id != $first_admin) {
                        $member_id = $_member_id;
                    }
                    $start = $_member_id;
                } while ((count($rows) > 0) && ($member_id === null));

                if ($member_id === null) {
                    return $GLOBALS['FORUM_DRIVER']->get_username($GLOBALS['FORUM_DRIVER']->get_guest_id());
                }

                return $GLOBALS['FORUM_DRIVER']->get_username($member_id);
            } elseif ($username == 'admin') {
                return $GLOBALS['FORUM_DRIVER']->get_username($first_admin);
            } elseif ($username == 'guest') {
                return $GLOBALS['FORUM_DRIVER']->get_username($GLOBALS['FORUM_DRIVER']->get_guest_id());
            }
        }

        if ($GLOBALS['FORUM_DRIVER']->get_member_from_username($username) === null) {
            if ($username == 'admin') {
                $username = $GLOBALS['FORUM_DB']->query_select_value('f_members', 'm_username', ['m_primary_group' => db_get_first_id() + 1]);
            } elseif ($username == 'test') {
                $username = $GLOBALS['FORUM_DB']->query_value_if_there('SELECT m_username FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE m_primary_group<>' . strval(db_get_first_id()) . ' AND m_primary_group<>' . strval(db_get_first_id() + 1));
            } elseif ($username == 'guest') {
                $username = 'Guest';
            }
        }

        return $username;
    }
}

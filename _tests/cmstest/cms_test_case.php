<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

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
        static $done_once = false;
        if (!$done_once) {
            // Make sure the site is open
            $this->site_closed = get_option('site_closed');
            require_code('config2');
            set_option('site_closed', '0', 0);
            $done_once = true;
            register_shutdown_function([$this, 'reopen_site']);
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
    }

    public function reopen_site()
    {
        if ($this->site_closed !== null) {
            set_option('site_closed', $this->site_closed, 0);
        }
    }

    public function tearDown()
    {
        cms_ini_set('ocproducts.type_strictness', '0');
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
        $url .= '&api=1&todo=1';
        if (strpos(shell_exec('npx eslint -v'), 'v') !== false) {
            $url .= '&codesniffer=1';
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

            $ret = parent::get($real_url, $parameters);
        } else {
            $ret = parent::get($url, $parameters);
        }

        require_code('files');

        // Save, so we can run webstandards checker on it later
        $path = get_file_base() . '/_tests/html_dump/' . get_class($this);
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        $content = $this->browser->getContent();
        cms_file_put_contents_safe($path . '/' . url_to_filename($url) . '.htm.tmp', $content, FILE_WRITE_FIX_PERMISSIONS);

        // Save the text so we can run through Word's grammar checker
        $text_content = $content;
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

    protected function load_key_options($substring)
    {
        $ret = [];

        $path = get_file_base() . '/_tests/assets/keys.csv';
        if (!is_file($path)) {
            $this->assertTrue(false, 'Cannot proceed, we need _tests/assets/keys.csv, which is not supplied in git for security reasons');
            exit();
        }

        require_code('files_spreadsheets_read');

        $sheet_reader = spreadsheet_open_read($path);
        while (($row = $sheet_reader->read_row()) !== false) {
            if (!isset($row['Option'])) {
                exit('Option column missing');
            }
            if (!isset($row['Value'])) {
                exit('Value column missing');
            }
            if (!isset($row['Type'])) {
                exit('Type column missing');
            }

            if (strpos($row['Option'], $substring) !== false) {
                switch ($row['Type']) {
                    case 'option':
                        set_option($row['Option'], $row['Value']);
                        break;

                    case 'hidden':
                        set_value($row['Option'], $row['Value']);
                        break;

                    case 'hidden_elective':
                        set_value($row['Option'], $row['Value'], true);
                        break;

                    case 'return':
                        $ret[$row['Option']] = $row['Value'];
                        break;
                }
            }
        }
        $sheet_reader->close();

        return $ret;
    }

    protected function run_health_check($category_label, $section_label)
    {
        require_code('health_check');
        $hook_obs = find_all_hook_obs('systems', 'health_checks', 'Hook_health_check_');
        foreach ($hook_obs as $ob) {
            $sections_to_run = [];
            $sections_to_run[] = $category_label . ' \\ ' . $section_label;
            list($category_label, $sections) = $ob->run($sections_to_run, CHECK_CONTEXT__TEST_SITE);

            $_sections = [];
            foreach ($sections as $section_label => $results) {
                foreach ($results as $_result) {
                    $this->assertTrue($_result[0], $_result[1]);
                }
            }
        }
    }
}

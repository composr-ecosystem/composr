<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

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
    }

    public function reopen_site()
    {
        if ($this->site_closed !== null) {
            set_option('site_closed', $this->site_closed, 0);
        }
    }

    public function tearDown()
    {
    }

    public function should_filter_cqc_line($line)
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
        $content = $this->_browser->getContent();
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
    public function establish_admin_session()
    {
        static $done_once = false;
        if ($done_once) {
            return;
        }
        $done_once = true;

        global $MEMBER_CACHED;
        require_code('users_active_actions');
        $MEMBER_CACHED = restricted_manually_enabled_backdoor();

        $this->dump($this->_browser->getContent());

        return get_session_id();
    }

    // Establishes an admin session on the server's likely callback IP
    public function establish_admin_callback_session()
    {
        require_code('users_active_actions');
        require_code('users_inactive_occasionals');

        $ip_address = get_server_external_looparound_ip();

        return create_session(get_first_admin_user(), 1, false, false, $ip_address);
    }
}

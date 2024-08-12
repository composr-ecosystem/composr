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
class cms_test_case extends WebTestCase
{
    public $site_closed;

    public function setUp()
    {
        // Make sure the site is open
        $this->site_closed = get_option('site_closed');
        require_code('config2');
        set_option('site_closed', '0', 0);

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        // We need to be compatible with low memory limits
        @ini_set('memory_limit', '32M');
        set_value('memory_limit', '32M');
    }

    public function tearDown()
    {
        if ($this->site_closed !== null) {
            set_option('site_closed', $this->site_closed, 0);
        }
    }

    public function get($url, $parameters = null)
    {
        $parts = array();
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

    public function establish_admin_session()
    {
        if (get_ip_address() == '') { // Running from CLI
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        }

        static $done_once = false;
        if ($done_once) {
            return get_session_id();
        }
        $done_once = true;

        global $MEMBER_CACHED;
        require_code('users_active_actions');
        $MEMBER_CACHED = restricted_manually_enabled_backdoor();

        $this->dump($this->_browser->getContent());

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

    public function get_canonical_username($username)
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

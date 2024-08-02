<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
 */

class ComposrPlugin extends MantisPlugin {
    protected $cms_sc_site_name = 'composr.app';
    protected $cms_sc_product_name = 'Composr';
    protected $cms_sc_business_name_possesive = 'Composr\'s';
    protected $cms_sc_business_name = 'Composr';
    protected $cms_guest_id = 1;
    protected $cms_extra_signin_sql = ''; // TODO: Customise for Composr's antispam
    protected $cms_sc_sourcecode_url = 'https://gitlab.com/composr-foundation/composr';
    protected $cms_sc_home_url = 'https://composr.app';
    protected $cms_updater_groups = array();
    protected $cms_developer_groups = array(22, 30);
    protected $cms_manager_groups = array();
    protected $cms_admin_groups = array(2, 3);

    // These are set in register()
    protected $cms_sc_site_url = '';
    protected $cms_sc_profile_url = '';
    protected $cms_sc_report_guidance_url = '';
    protected $cms_sc_simple_report_url = '';
    protected $cms_sc_login_url = '';
    protected $cms_sc_lostpassword_url = '';
    protected $cms_sc_join_url = '';
    protected $cms_sc_invite_url = '';
    protected $cms_sc_member_view_url = '';
    protected $cms_sc_tracker_url = '';

    protected $cms_sc_db_prefix = 'cms_';
    protected $cms_sc_session_cookie_name = 'cms_session';

    function register()
    {
        // Plugin-specific definitions
        $this->name = 'Composr Plugin';
        $this->description = 'This plugin extends MantisBT to work directly with Composr CMS';
        $this->page = '';
        $this->version = '11.0';
        $this->requires = array(
            'MantisCore' => '2.26, < 2.27', // Set maximum to force us to review this plugin on each major/minor release of MantisBT
        );
        $this->author = 'Christopher Graham / Patrick Schmalstig';
        $this->contact = 'info@composr.app';
        $this->url = 'https://composr.app';

        // Set up Composr-specific variables
        require_once(__DIR__ . '/../../../_config.php');
        global $SITE_INFO;
        $this->cms_sc_site_url = $SITE_INFO['base_url'];
        $this->cms_sc_profile_url = $this->cms_sc_site_url . '/members/view.htm';
        $this->cms_sc_report_guidance_url = $this->cms_sc_site_url . '/docs/tut-software-feedback.htm';
        $this->cms_sc_simple_report_url = $this->cms_sc_site_url . '/report-issue.htm';
        $this->cms_sc_login_url = $this->cms_sc_site_url . '/login.htm';
        $this->cms_sc_lostpassword_url = $this->cms_sc_lostpassword_url . '/lost-password.htm';
        $this->cms_sc_join_url = $this->cms_sc_site_url . '/join.htm';
        $this->cms_sc_invite_url = $this->cms_sc_site_url . '/recommend.htm';
        $this->cms_sc_member_view_url = $this->cms_sc_site_url . '/members/view/%1$d.htm'; // sprintf
        $this->cms_sc_tracker_url = $this->cms_sc_site_url . '/tracker/';
        $this->cms_sc_db_prefix = $SITE_INFO['table_prefix'];
        $this->cms_sc_session_cookie_name = $SITE_INFO['session_cookie'];
    }

    function events()
    {
        return array(
            'EVENT_COMPOSR_USER_CACHE_ARRAY_ROWS' => EVENT_TYPE_CHAIN,
            'EVENT_COMPOSR_USER_GET_ID_BY_NAME' => EVENT_TYPE_FIRST,
        );
    }

    function hooks()
    {
        return array(
            'EVENT_CORE_HEADERS' => 'event_core_headers',
            'EVENT_LAYOUT_CONTENT_BEGIN' => 'event_layout_content_begin',
            'EVENT_MENU_ISSUE_RELATIONSHIP' => 'event_menu_issue_relationship',
            'EVENT_AUTH_USER_FLAGS' => 'event_auth_user_flags',
            'EVENT_CORE_READY' => 'event_core_ready',
            'EVENT_MENU_MAIN' => 'event_menu_main',
            'EVENT_BUGNOTE_ADD_FORM' => 'event_bugnote_add_form',

            // TODO: find a better way that does not involve having to edit original code.
            // CAREFUL! Do not remove these signals from core/user_api.php
            'EVENT_COMPOSR_USER_CACHE_ARRAY_ROWS' => 'event_composr_user_cache_array_rows',
            'EVENT_COMPOSR_USER_GET_ID_BY_NAME' => 'event_composr_user_get_id_by_name',
        );
    }

    function event_core_headers()
    {
        require_api('utility_api.php');
        require_api('gpc_api.php');

        // Redirect the Mantis account page to the Composr members page
        if (is_page_name( 'account_page.php' )) {
            header('Location: ' . sprintf($this->cms_sc_member_view_url, strval(auth_get_current_user_id())));
            exit();
        }

        // LEGACY: Redirect simple project reporting to our new decision tree wizard for reporting issues
        if (is_page_name( 'bug_report_page.php' ) && (gpc_get_int( 'simple', 0 ) != 0)) {
            header('Location: ' . $this->cms_sc_simple_report_url);
            exit();
        }

        // Redirect the Mantis login page to the Composr login page
        if (is_page_name( 'login_page.php' )) {
            header('Location: ' . $this->cms_sc_login_url . '?redirect=' . urlencode($this->cms_sc_tracker_url));
            exit();
        }

        // Redirect the Mantis logout page to the Composr logout page
        if (is_page_name( 'logout_page.php' )) {
            header('Location: ' . $this->cms_sc_login_url . '?type=logout&redirect=' . urlencode($this->cms_sc_tracker_url));
            exit();
        }

        // Redirect the Mantis lost password page to the Composr lost password page
        if (is_page_name( 'lost_pwd_page.php' )) {
            header('Location: ' . $this->cms_sc_lostpassword_url . '?redirect=' . urlencode($this->cms_sc_tracker_url));
            exit();
        }

        // Redirect the Mantis signup page to the Composr join page
        if (is_page_name( 'signup_page.php' )) {
            header('Location: ' . $this->cms_sc_join_url . '?redirect=' . urlencode($this->cms_sc_tracker_url));
            exit();
        }

        // Redirect Mantis create user page to Composr recommend page (it is used for inviting users)
        if (is_page_name( 'manage_user_create_page.php' )) {
            header('Location: ' . $this->cms_sc_invite_url . '?redirect=' . urlencode($this->cms_sc_tracker_url));
            exit();
        }

        // Redirect to the member profile on Composr if not guest
        if (is_page_name( 'view_user_page.php' )) {
            require_api('authentication_api');

            $user_id = gpc_get_int( 'id', auth_get_current_user_id());
            if ($user_id !== $this->cms_guest_id) {
                header('Location: ' . sprintf($this->cms_sc_member_view_url, strval($user_id)));
                exit();
            }
        }
    }

    function event_layout_content_begin()
    {
        require_api('utility_api.php');

        // Bug reporting guidance
        if (is_page_name( 'bug_report_page.php' )) {
            require_api('lang_api.php');
            require_api('current_user_api.php');

            $ret = '
            <p>
            ' . sprintf(plugin_lang_get('bug_report_guidance'), $this->cms_sc_report_guidance_url) . '
            </p>';

            if (current_user_is_anonymous()) {
                $ret .= '<p>' . plugin_lang_get( 'not_logged_in_bad' ) . '</p>';
            }

            return $ret;
        }

        // TODO: bug_sponsorship_list_view_inc; convert to using points escrow

        // Composr specific welcome message
        if (is_page_name( 'my_view_page.php' )) {
            require_api('lang_api.php');

            return '
            <div style="margin: 1em; font-size: 1.2em">
            <p>
            ' . sprintf(plugin_lang_get('my_view_welcome_message'), $this->cms_sc_product_name, $this->cms_sc_business_name_possesive, $this->cms_sc_business_name, $this->cms_sc_report_guidance_url) . '
            </p>
            </div>';
        }

        // Search information
        if (is_page_name( 'view_all_inc.php' )) {
            require_api('lang_api.php');

            return '<p>' . plugin_lang_get('hint_message') . '</p>';
        }

        return '';
    }

    function event_bugnote_add_form()
    {
        require_api('current_user_api.php');

        if (current_user_is_anonymous()) {
            echo '
            <tr>
				<th class="category">
					' . plugin_lang_get('bugnote_not_logged_in_title') . '
				</th>
				<td>
					' . plugin_lang_get('bugnote_not_logged_in_text') . '
				</td>
			</tr>
            ';
        }
    }

    function event_menu_issue_relationship($event, $bug_id)
    {
        // Add a button for searching commits tagged with this issue
        require_api('lang_api.php');
        return [
            plugin_lang_get( 'search_commits' ) => $this->cms_sc_sourcecode_url . '/commits/master?search=MANTIS-' . strval($bug_id)
        ];
    }

    function event_auth_user_flags($p_event_name, $p_args)
    {
        require_api('authentication_api.php');
        require_api('helper_api.php');

        // Only allow authentication via Composr
        $t_flags = new AuthFlags();
        $t_flags->setCanUseStandardLogin( false );
        $t_flags->setPasswordManagedExternallyMessage( 'You must manage your password from the ' . $this->cms_sc_site_name . ' site.');
        //$t_flags->setCredentialsPage(helper_url_combine($this->cms_sc_login_url, 'username=' . urlencode($p_args['username']) . '&redirect=' . urlencode($this->cms_sc_tracker_url)));
        //$t_flags->setLogoutPage(helper_url_combine($this->cms_sc_login_url, 'type=logout&redirect=' . urlencode($this->cms_sc_tracker_url)));

        return $t_flags;
    }

    function event_core_ready()
    {
        // Composr - try session authentication
        require_api('authentication_api.php');
        require_api('database_api.php');
        require_api('user_api.php');

        global $g_script_login_cookie, $g_cache_anonymous_user_cookie_string, $g_db;
        if ((isset($_COOKIE[$this->cms_sc_session_cookie_name])) && (isset($g_db)))
        {
            $query = 'SELECT member_id FROM ' . $this->cms_sc_db_prefix . 'sessions WHERE the_session=\'' . db_prepare_binary_string($_COOKIE[$this->cms_sc_session_cookie_name]) . '\'';
            $result = db_query($query);

            if (1 == db_num_rows($result)) {
                $user = db_result($result);
                user_cache_row($user);

                $t_query = 'SELECT u.id,u.cookie_string
							FROM '.$this->cms_sc_db_prefix.'f_members m
							LEFT JOIN '.$this->cms_sc_db_prefix.'f_member_custom_fields f ON f.mf_member_id=m.id
							LEFT JOIN ' . db_get_table('user') . ' u ON u.username=m.m_username
							WHERE m.id<>1 AND m.id=' . strval($user) . ' AND m.m_is_perm_banned=0' . $this->cms_extra_signin_sql;
                $t_result = db_query($t_query);
                if ($t_row = db_fetch_array($t_result)) {
                    $t_cookie = $t_row['cookie_string'];

                    $g_cache_anonymous_user_cookie_string = $t_cookie;
                    current_user_set((int)$t_row['id']);

                    // This line ensures that the fetched cookie is used in auth_get_current_user_cookie()
                    $g_script_login_cookie = $t_cookie;
                }
            }
        }
    }

    function event_menu_main()
    {
        $t_sidebar_items = [];

        // Composr - Gitlab link
        $t_sidebar_items[] = array(
            'url' => $this->cms_sc_sourcecode_url,
            'title' => plugin_lang_get('sourcecode_link'),
            'icon' => 'fa-git',
        );

        // Composr - Main website link
        $t_sidebar_items[] = array(
            'url' => $this->cms_sc_home_url,
            'title' => plugin_lang_get('home_link'),
            'icon' => 'fa-home',
        );

        return $t_sidebar_items;
    }

    function event_composr_user_cache_array_rows($event, $p_user_id_array)
    {
        require_api('user_api.php');
        require_api('database_api.php');

        // Composr - sync user accounts from CMS
        global $g_cache_user;
        foreach ($p_user_id_array as $t_user_id) {
            $result = db_query("SELECT * FROM " . $this->cms_sc_db_prefix . "f_members WHERE id=" . db_param(), Array($t_user_id));
            if (0 == db_num_rows($result)) {
                $g_cache_user[$t_user_id] = false;
                continue;
            }

            $cms_row = db_fetch_array($result);
            if ($cms_row === false) {
                $g_cache_user[$t_user_id] = false;
                continue;
            }

            // Find access level
            $access_level = ($cms_row['m_primary_group'] == 1) ? VIEWER : REPORTER;
            if (in_array($cms_row['m_primary_group'], $this->cms_updater_groups)) $access_level = UPDATER;
            if (in_array($cms_row['m_primary_group'], $this->cms_developer_groups)) $access_level = DEVELOPER;
            if (in_array($cms_row['m_primary_group'], $this->cms_manager_groups)) $access_level = MANAGER;
            if (in_array($cms_row['m_primary_group'], $this->cms_admin_groups)) $access_level = ADMINISTRATOR;

            // Process additional groups
            $result = db_query("SELECT gm_group_id FROM " . $this->cms_sc_db_prefix . "f_group_members WHERE gm_member_id=" . db_param(), Array($t_user_id));
            $num_groups = db_num_rows($result);
            for ($i = 0; $i < $num_groups; $i++) {
                $group_row = db_fetch_array($result);
                $secondary_group_id = $group_row['gm_group_id'];
                $access_level_2 = ($secondary_group_id == 1) ? VIEWER : REPORTER;
                if (in_array($secondary_group_id, $this->cms_updater_groups)) $access_level_2 = UPDATER;
                if (in_array($secondary_group_id, $this->cms_developer_groups)) $access_level_2 = DEVELOPER;
                if (in_array($secondary_group_id, $this->cms_manager_groups)) $access_level_2 = MANAGER;
                if (in_array($secondary_group_id, $this->cms_admin_groups)) $access_level_2 = ADMINISTRATOR;
                if ($access_level_2 > $access_level) $access_level = $access_level_2;
            }

            $row = array(
                'id' => $t_user_id,
                'username' => $cms_row['m_username'],
                'realname' => '',
                'email' => ($cms_row['m_email_address'] == '') ? '' : $cms_row['m_email_address'],
                'password' => $cms_row['m_pass_hash_salted'] . ':' . $cms_row['m_pass_salt'] . ':' . $cms_row['m_password_compat_scheme'],
                'enabled' => (int)$cms_row['m_validated'],
                'protected' => 0,
                'access_level' => $access_level,
                'login_count' => 0,
                'lost_password_request_count' => 0,
                'failed_login_count' => 0,
                'cookie_string' => empty($cms_row['m_pass_hash_salted']) ? ('erjg9843h9grefjlg' . $cms_row['m_username']) : $cms_row['m_pass_hash_salted'],
                'last_visit' => (int)$cms_row['m_last_visit_time'],
                'date_created' => (int)$cms_row['m_join_time'],
            );

            // Sync with MantisBT table
            $t_user_table = db_get_table('user');
            $query = "REPLACE INTO $t_user_table
					( id, username, email, password, date_created, last_visit, enabled, access_level, login_count, cookie_string, realname, protected, lost_password_request_count, failed_login_count )
					VALUES
					( " . db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . ', ' . db_param() . "," . db_param() . ',' . db_param() . ',' . db_param() . ',' . db_param() . ',' . db_param() . ',' . db_param() . ',' . db_param() . ', ' . db_param() . ')';
            db_query($query, Array($row['id'], $row['username'], $row['email'], $row['password'], $row['date_created'], $row['last_visit'], $row['enabled'], $row['access_level'], $row['login_count'], $row['cookie_string'], $row['realname'], $row['protected'], $row['lost_password_request_count'], $row['failed_login_count']));

            // Populate cache
            $g_cache_user[$t_user_id] = $row;
            unset($p_user_id_array[$t_user_id]);
        }

        return []; // Actually we want to always return empty so the MantisBT code does not execute further.
    }

    function event_composr_user_get_id_by_name($event, $p_username)
    {
        require_api('database_api.php');

        // Composr - sync user accounts from CMS
        db_param_push();
        $t_query = 'SELECT * FROM ' . $this->cms_sc_db_prefix . 'f_members WHERE m_username=' . db_param();
        $t_result = db_query($t_query, array($p_username));
        $t_row = db_fetch_array($t_result);
        if ($t_row) {
            return (int)$t_row['id'];
        }

        return false; // If member not found, return false instead of null so MantisBT does not proceed finding the member.
    }

    // TODO: antispam measure that is more effective than renaming bugnote_text (does not stop paid human spammers)
    // TODO: Prevent guests from editing guest issues
    // TODO: hide user buttons on menu if guest (?)
    // TODO: redirect manage_user_create_page.php to Composr's manage users
    // TODO: disable roadmap
}

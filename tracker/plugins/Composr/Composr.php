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

/*
 * Files with custom overrides for Composr:
 * - tracker/core/config/config_inc.php
 * - tracker/core/config/custom_constants_inc.php
 * - tracker/plugins/Composr (and all files within)
 * - tracker/core/user_api.php
 * - tracker/core/sponsorship_api.php
 * - tracker/bug_sponsorship_list_view_inc.php
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
    protected $cms_updater_groups = [];
    protected $cms_developer_groups = [10]; // TODO: Keep up to date with composr.app's group IDs (member)
    protected $cms_manager_groups = [];
    protected $cms_admin_groups = [2, 3]; // TODO: Keep up to date with composr.app's group IDs (administrator, moderator)

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
    protected $cms_sc_escrow_view_url = '';
    protected $cms_sc_endpoint_url = '';

    protected $cms_sc_db_prefix = 'cms_';
    protected $cms_sc_session_cookie_name = 'cms_session';
    protected $cms_sc_cookie_domain = '';
    protected $cms_sc_cookie_path = '';
    protected $cms_file_base = __DIR__ . '/../../../';

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
        require_once($this->cms_file_base . '_config.php');
        global $SITE_INFO;
        $this->cms_sc_site_url = $SITE_INFO['base_url'];
        $this->cms_sc_endpoint_url = $this->cms_sc_site_url . '/data/endpoint.php/cms_homesite/';
        $this->cms_sc_profile_url = $this->cms_sc_site_url . '/members/view.htm';
        $this->cms_sc_report_guidance_url = $this->cms_sc_site_url . '/docs/tut-software-feedback.htm';
        $this->cms_sc_simple_report_url = $this->cms_sc_site_url . '/report-issue.htm';
        $this->cms_sc_login_url = $this->cms_sc_site_url . '/login.htm';
        $this->cms_sc_lostpassword_url = $this->cms_sc_lostpassword_url . '/lost-password.htm';
        $this->cms_sc_join_url = $this->cms_sc_site_url . '/join.htm';
        $this->cms_sc_invite_url = $this->cms_sc_site_url . '/recommend.htm';
        $this->cms_sc_member_view_url = $this->cms_sc_site_url . '/members/view/%1$d.htm'; // sprintf
        $this->cms_sc_tracker_url = $this->cms_sc_site_url . '/tracker/';
        $this->cms_sc_escrow_view_url = $this->cms_sc_site_url . '/site/points.htm?type=view_escrow&id=%1$d'; // sprintf
        $this->cms_sc_db_prefix = $SITE_INFO['table_prefix'];
        $this->cms_sc_cookie_domain = isset($SITE_INFO['cookie_domain']) ? $SITE_INFO['cookie_domain'] : '';
        $this->cms_sc_cookie_path = isset($SITE_INFO['cookie_path']) ? $SITE_INFO['cookie_path'] : '/';

        $this->cms_sc_session_cookie_name = $this->validate_special_cookie_prefix($SITE_INFO['session_cookie']);
    }

    function events()
    {
        return array(
            // CAREFUL! Do not remove these signals from core/user_api.php
            'EVENT_COMPOSR_USER_CACHE_ARRAY_ROWS' => EVENT_TYPE_CHAIN,
            'EVENT_COMPOSR_USER_GET_ID_BY_NAME' => EVENT_TYPE_FIRST,

            // CAREFUL! Do not remove these signals from core/sponsorship_api.php
            'EVENT_COMPOSR_SPONSORSHIP_CACHE_ROW' => EVENT_TYPE_EXECUTE,
            'EVENT_COMPOSR_SPONSORSHIP_GET_ID' => EVENT_TYPE_FIRST,
            'EVENT_COMPOSR_SPONSORSHIP_GET_ALL_IDS' => EVENT_TYPE_CHAIN,
            'EVENT_COMPOSR_SPONSORSHIP_SET' => EVENT_TYPE_FIRST,
            'EVENT_COMPOSR_SPONSORSHIP_DELETE' => EVENT_TYPE_EXECUTE,
            'EVENT_COMPOSR_SPONSORSHIP_DELETE_ALL' => EVENT_TYPE_EXECUTE,
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
            'EVENT_UPDATE_BUG' => 'event_update_bug',
            'EVENT_BUG_DELETED' => 'event_composr_sponsorship_delete_all', // Shares exact same functionality
            'EVENT_BUG_ACTION' => 'event_bug_action',

            'EVENT_COMPOSR_USER_CACHE_ARRAY_ROWS' => 'event_composr_user_cache_array_rows',
            'EVENT_COMPOSR_USER_GET_ID_BY_NAME' => 'event_composr_user_get_id_by_name',
            'EVENT_COMPOSR_SPONSORSHIP_CACHE_ROW' => 'event_composr_sponsorship_cache_row',
            'EVENT_COMPOSR_SPONSORSHIP_GET_ID' => 'event_composr_sponsorship_get_id',
            'EVENT_COMPOSR_SPONSORSHIP_GET_ALL_IDS' => 'event_composr_sponsorship_get_all_ids',
            'EVENT_COMPOSR_SPONSORSHIP_SET' => 'event_composr_sponsorship_set',
            'EVENT_COMPOSR_SPONSORSHIP_DELETE' => 'event_composr_sponsorship_delete',
            'EVENT_COMPOSR_SPONSORSHIP_DELETE_ALL' => 'event_composr_sponsorship_delete_all',
        );
    }

    function event_core_headers()
    {
        require_api('utility_api.php');
        require_api('gpc_api.php');
        require_api('current_user_api.php');

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

        // Redirect the Mantis select project page (when viewing as Guest) to the Composr login page
        if (is_page_name( 'login_select_proj_page.php' ) && (current_user_get_access_level() <= VIEWER)) {
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

        // Redirect the Mantis login password page to the Composr lost password page
        if (is_page_name( 'login_password_page.php' )) {
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
            require_api('authentication_api.php');

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

        global $g_script_login_cookie, $g_cache_anonymous_user_cookie_string, $g_db, $g_window_title;

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

                    // Update the session
                    $query = 'UPDATE ' . $this->cms_sc_db_prefix . 'sessions SET last_activity_time=' . db_param() . ', the_zone=' . db_param() . ', the_page=' . db_param() . ', the_type=' . db_param() . ', the_id=' . db_param() . ', the_title=' . db_param() . ' WHERE the_session=' . db_param();
                    db_query($query, [time(), '', '', '', '', $g_window_title, $_COOKIE[$this->cms_sc_session_cookie_name]]);

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

    function event_composr_sponsorship_cache_row($event, $c_sponsorship_id)
    {
        // Intercept cache sponsorship rows because we want to use Composr's points escrow instead of MantisBT sponsorship table
        require_api('sponsorship_api.php');

        global $g_cache_sponsorships;

        if( isset( $g_cache_sponsorships[$c_sponsorship_id] ) ) { // Don't need to do anything
            return;
        }

        // Always initialize to false so it is forcefully returned by MantisBT, preventing a Mantis DB query
        $g_cache_sponsorships[$c_sponsorship_id] = false;

        db_param_push();
        $t_query = 'SELECT * FROM ' . $this->cms_sc_db_prefix . 'escrow WHERE status=2 AND content_type=' . db_param() . ' AND id=' . db_param();
        $t_result = db_query($t_query, array('tracker_issue', $c_sponsorship_id));
        $t_row = db_fetch_array($t_result);
        if ($t_row) {
            $g_cache_sponsorships[(int)$t_row['id']] = [
                'id' => (int)$t_row['id'],
                'bug_id' => (int)$t_row['content_id'],
                'user_id' => (int)$t_row['sending_member'],
                'amount' => (int)$t_row['amount'],
                'logo' => '',
                'url' => sprintf($this->cms_sc_escrow_view_url, strval($t_row['id'])),
                'paid' => $t_row['status'],
                'date_submitted' => $t_row['date_and_time'],
                'last_updated' => $t_row['update_date_and_time'],
            ];
        }
    }

    function event_composr_sponsorship_get_id($event, $p_bug_id, $c_user_id)
    {
        require_api('sponsorship_api.php');
        require_api('database_api.php');

        // We use the escrow ID as the sponsorship ID.
        db_param_push();
        $t_query = 'SELECT id FROM ' . $this->cms_sc_db_prefix . 'escrow WHERE status=2 AND content_type=' . db_param() . ' AND content_id=' . db_param() . ' AND sending_member=' . db_param();
        $t_result = db_query($t_query, array('tracker_issue', $p_bug_id, $c_user_id));
        $t_row = db_fetch_array($t_result);
        if ($t_row) {
            return (int)$t_row['id'];
        }

        return false; // If escrow not found, return false instead of null so MantisBT does not proceed in its own database.
    }

    function event_composr_sponsorship_get_all_ids($event, $t_sponsorship_ids, $c_bug_id)
    {
        // Intercept getting sponsorship IDs and use Composr's points escrow instead
        require_api('sponsorship_api.php');
        require_api('database_api.php');

        global $g_cache_sponsorships;

        db_param_push();
        $t_query = 'SELECT * FROM ' . $this->cms_sc_db_prefix . 'escrow WHERE status=2 AND content_type=' . db_param() . ' AND content_id=' . db_param();
        $t_result = db_query($t_query, array('tracker_issue', $c_bug_id));
        for ($i = 0; $i < db_num_rows($t_result); $i++) {
            $t_row = db_fetch_array($t_result);
            $t_sponsorship_ids[] = $t_row['id'];
            $g_cache_sponsorships[(int)$t_row['id']] = [
                'id' => (int)$t_row['id'],
                'bug_id' => $c_bug_id,
                'user_id' => (int)$t_row['sending_member'],
                'amount' => (int)$t_row['amount'],
                'logo' => '',
                'url' => sprintf($this->cms_sc_escrow_view_url, strval($t_row['id'])),
                'paid' => $t_row['status'],
                'date_submitted' => $t_row['date_and_time'],
                'last_updated' => $t_row['update_date_and_time'],
            ];
        }

        return $t_sponsorship_ids;
    }

    function event_composr_sponsorship_set($event, $p_sponsorship)
    {
        // Intercept sponsorships and use Composr's points escrow instead; we have to use an external API call as this requires very complex processing
        require_api('sponsorship_api.php');
        require_api('url_api.php');
        require_api('error_api.php');

        $url = $this->cms_sc_endpoint_url . 'tracker_sponsorship/' . strval($p_sponsorship->id) . '?';
        $map = [
            'keep_session' => $_COOKIE[$this->cms_sc_session_cookie_name],
            'type' => ($p_sponsorship->id == 0) ? 'add' : 'edit',
            'bug_id' => strval($p_sponsorship->bug_id),
            'user_id' => strval($p_sponsorship->user_id),
            'amount' => strval($p_sponsorship->amount),
        ];
        foreach ($map as $key => $value) {
            $url .= $key . '=' . urlencode($value) . '&';
        }
        $url = substr($url, 0, -1);

        $response = $this->url_get($url);
        if ($response === null) {
            trigger_error('Error communicating the sponsorship with ' . $this->cms_sc_site_name, ERROR );
        }

        $data = @json_decode($response, true);
        if (($data === null) || (!$data['success']) || (!isset($data['response_data']['id']))) {
            if (isset($data['error_details'])) {
                trigger_error($data['error_details'], ERROR );
            }
            trigger_error('Error processing the sponsorship with ' . $this->cms_sc_site_name, ERROR );
        }

        // TODO: Still does not correctly update bug with total sponsorship after completed

        return $data['response_data']['id'];
    }

    function event_composr_sponsorship_delete($event, $sponsorship_id, $bug_id)
    {
        // Intercept sponsorships and use Composr's points escrow instead; we have to use an external API call as this requires very complex processing
        require_api('sponsorship_api.php');
        require_api('url_api.php');
        require_api('error_api.php');

        $url = $this->cms_sc_endpoint_url . 'tracker_sponsorship/' . strval($sponsorship_id) . '?';
        $map = [
            'keep_session' => $_COOKIE[$this->cms_sc_session_cookie_name],
            'type' => 'delete',
            'bug_id' => strval($bug_id),
        ];
        foreach ($map as $key => $value) {
            $url .= $key . '=' . urlencode($value) . '&';
        }
        $url = substr($url, 0, -1);

        $response = $this->url_get($url);
        if ($response === null) {
            trigger_error('Error communicating the sponsorship with ' . $this->cms_sc_site_name, ERROR );
        }

        $data = @json_decode($response, true);
        if (($data === null) || (!$data['success'])) {
            if (isset($data['error_details'])) {
                trigger_error($data['error_details'], ERROR );
            }
            trigger_error('Error deleting the sponsorship with ' . $this->cms_sc_site_name, ERROR );
        }
    }

    function event_composr_sponsorship_delete_all($event, $bug_id)
    {
        // Intercept sponsorships and use Composr's points escrow instead; we have to use an external API call as this requires very complex processing
        require_api('sponsorship_api.php');
        require_api('url_api.php');
        require_api('error_api.php');

        $url = $this->cms_sc_endpoint_url . 'tracker_sponsorship/' . strval($bug_id) . '?';
        $map = [
            'keep_session' => $_COOKIE[$this->cms_sc_session_cookie_name],
            'type' => 'delete-all',
            'reason' => 'The issue was deleted'
        ];
        foreach ($map as $key => $value) {
            $url .= $key . '=' . urlencode($value) . '&';
        }
        $url = substr($url, 0, -1);

        $response = $this->url_get($url);
        if ($response === null) {
            trigger_error('Error communicating the deletion of sponsorships with ' . $this->cms_sc_site_name, ERROR );
        }

        $data = @json_decode($response, true);
        if (($data === null) || (!$data['success'])) {
            if (isset($data['error_details'])) {
                trigger_error($data['error_details'], ERROR );
            }
            trigger_error('Error deleting the sponsorships with ' . $this->cms_sc_site_name, ERROR );
        }
    }

    function event_update_bug($event, $old_bug, $new_bug)
    {
        require_api('bug_api.php');
        require_api('sponsorship_api.php');
        require_api('url_api.php');
        require_api('error_api.php');

        if ($old_bug->status != $new_bug->status) { // Status changed, so process sponsorships
            $url = $this->cms_sc_endpoint_url . 'tracker_sponsorship/' . strval($new_bug->id) . '?';

            switch ($new_bug->status) {
                case 80: // Resolved
                    $map = [
                        'keep_session' => $_COOKIE[$this->cms_sc_session_cookie_name],
                        'type' => 'complete-all',
                        'recipient' => strval($new_bug->handler_id),
                        'reporter' => strval($new_bug->reporter_id),
                    ];
                    foreach ($map as $key => $value) {
                        $url .= $key . '=' . urlencode($value) . '&';
                    }
                    $url = substr($url, 0, -1);

                    $response = $this->url_get($url);
                    if ($response === null) {
                        trigger_error('Error communicating the completion of sponsorships with ' . $this->cms_sc_site_name, ERROR );
                    }

                    $data = @json_decode($response, true);
                    if (($data === null) || (!$data['success'])) {
                        if (isset($data['error_details'])) {
                            trigger_error($data['error_details'], ERROR );
                        }
                        trigger_error('Error completing the sponsorships with ' . $this->cms_sc_site_name, ERROR );
                    }

                    break;
                case 90: // Closed
                    $map = [
                        'keep_session' => $_COOKIE[$this->cms_sc_session_cookie_name],
                        'type' => 'delete-all',
                        'reason' => 'The issue was closed'
                    ];
                    foreach ($map as $key => $value) {
                        $url .= $key . '=' . urlencode($value) . '&';
                    }
                    $url = substr($url, 0, -1);

                    $response = $this->url_get($url);
                    if ($response === null) {
                        trigger_error('Error communicating the deletion of sponsorships with ' . $this->cms_sc_site_name, ERROR );
                    }

                    $data = @json_decode($response, true);
                    if (($data === null) || (!$data['success'])) {
                        if (isset($data['error_details'])) {
                            trigger_error($data['error_details'], ERROR );
                        }
                        trigger_error('Error deleting the sponsorships with ' . $this->cms_sc_site_name, ERROR );
                    }
                    break;

                default:
                    $map = [
                        'keep_session' => $_COOKIE[$this->cms_sc_session_cookie_name],
                        'type' => 'reopen-all',
                    ];
                    foreach ($map as $key => $value) {
                        $url .= $key . '=' . urlencode($value) . '&';
                    }
                    $url = substr($url, 0, -1);

                    $response = $this->url_get($url);
                    if ($response === null) {
                        trigger_error('Error communicating the reversal of issue points with ' . $this->cms_sc_site_name, ERROR );
                    }

                    $data = @json_decode($response, true);
                    if (($data === null) || (!$data['success'])) {
                        if (isset($data['error_details'])) {
                            trigger_error($data['error_details'], ERROR );
                        }
                        trigger_error('Error reversing issue points with ' . $this->cms_sc_site_name, ERROR );
                    }
            }
        }
    }

    function event_bug_action($event, $f_action, $t_bug_id)
    {
        require_api('bug_api.php');

        switch ($f_action) {
            case 'CLOSE':
            case 'DELETE':
                $this->event_composr_sponsorship_delete_all($event, $t_bug_id);
                break;
            case 'RESOLVE':
                $old_bug = new BugData(); // We don't know the old bug's info, so use default template
                $new_bug = bug_get($t_bug_id);
                $this->event_update_bug($event, $old_bug, $new_bug);
                break;
        }
    }

    // TODO: antispam measure that is more effective than renaming bugnote_text (does not stop paid human spammers)
    // TODO: Prevent guests from editing guest issues

    /**
     * Retrieve the contents of a remote URL (better than MantisBT's built-in method).
     * First tries using built-in PHP modules (OpenSSL and cURL), then attempts
     * system call as last resort.
     * @param string $p_url The URL to fetch.
     * @return null|string URL contents (NULL in case of errors)
     */
    protected function url_get($p_url) {
        require_api('utility_api.php');

        # Use the PHP cURL extension
        if (function_exists('curl_init')) {
            $t_curl = null;

            try {
                $t_curl = curl_init($p_url);

                $t_curl_opt = array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION  => true, // Follow redirects
                    CURLOPT_MAXREDIRS      => 3,     // Limit the number of redirections
                    CURLOPT_TIMEOUT        => 5,     // Timeout in seconds
                    CURLOPT_CONNECTTIMEOUT => 5      // Connection timeout in seconds
                );

                # Default User Agent (Mantis version + php curl extension version)
                $t_vers = curl_version();
                $t_curl_opt[CURLOPT_USERAGENT] =
                    'mantisbt/' . MANTIS_VERSION . ' php-curl/' . $t_vers['version'];

                # Set the options
                curl_setopt_array($t_curl, $t_curl_opt);

                # Retrieve data
                $t_data = curl_exec($t_curl);
                if ($t_data !== false) {
                    curl_close($t_curl);
                    $t_curl = null;
                    return $t_data;
                }
            } catch (Exception $e) {
                // Ignore errors; try a different method below
                error_log('CURL: ERROR ' . $e->getMessage());
            } finally {
                if ($t_curl !== null) {
                    curl_close($t_curl);
                    $t_curl = null;
                }
            }
        }

        # FSOCK call
        if (function_exists('fsockopen')) {
            // Parse the URL
            $parsed_url = parse_url($p_url);
            if ($parsed_url === false) {
                trigger_error('Invalid URL passed in ComposrPlugin->url_get()', ERROR);
            }

            // Extract components from the parsed URL
            $host = $parsed_url['host'] ?? '';
            $port = $parsed_url['port'] ?? 80; // Default to port 80 if not specified
            $path = $parsed_url['path'] ?? '/';
            $query = $parsed_url['query'] ?? '';
            if ($query) {
                $path .= '?' . $query;
            }

            // Handle secure connections (HTTPS)
            $scheme = $parsed_url['scheme'] ?? 'http';
            if ($scheme === 'https') {
                $host = 'ssl://' . $host;
                $port = 443; // Default port for HTTPS
            }

            // Initialize the output and error variables
            $response = '';
            $errno = null;
            $errstr = '';
            $fp = null;

            try {
                // Create the socket connection
                $fp = @fsockopen($host, $port, $errno, $errstr, 5);

                // Check if the connection was successful
                if (!$fp) {
                    trigger_error('Failed fsock connection: ' . $errstr, ERROR);
                }

                // Create the HTTP GET request
                $out = "GET $path HTTP/1.1\r\n";
                $out .= "Host: {$parsed_url['host']}\r\n";
                $out .= "Connection: Close\r\n\r\n";

                // Send the request
                fwrite($fp, $out);

                // Read the response
                while (!feof($fp)) {
                    $response .= fgets($fp, 128);
                }

                // Separate headers and body
                list($headers, $body) = explode("\n\n", str_replace("\r", '', $response), 2);

                fclose($fp);
                $fp = null;
                return $body;
            } catch (Exception $e) {
                // Ignore; try a different method below
                error_log('fsock: ERROR ' . $e->getMessage());
            } finally {
                // Close the socket connection
                if ($fp !== null) {
                    fclose($fp);
                    $fp = null;
                }
            }
        }

        # Last resort system call
        try {
            $t_url = escapeshellarg($p_url);
            $t_data = shell_exec('curl ' . $t_url);
            if ($t_data !== false) {
                return $t_data;
            }
        } catch (Exception $e) {
            // proceed;
            error_log('CURL (terminal): ERROR ' . $e->getMessage());
        }

        # If all methods fail, return null to indicate an error
        return null;
    }

    /**
     * Ensure that if we are using a special cookie name prefix that we can actually do so, otherwise strip it.
     *
     * @param ID_TEXT $cookie_name The name of the cookie (passed by reference; prefix will be stripped if it cannot be used)
     * @return ID_TEXT The name of the cookie we should use
     */
    protected function validate_special_cookie_prefix(string &$cookie_name)
    {
        // If __Host- prefixed, determine if we can use it
        if (strpos($cookie_name, '__Host-') === 0) {
            if (!empty($this->cms_sc_cookie_domain)) { // Cannot use __Host- if a domain is set
                $cookie_name = substr($cookie_name, 7);
                return $cookie_name;
            }

            if (strpos($this->cms_sc_site_url, 'https://') !== 0) { // Cannot use __Host- if not running securely
                $cookie_name = substr($cookie_name, 7);
                return $cookie_name;
            }

            if ($this->cms_sc_cookie_path != '/') { // Cannot use __Host- if path is not /
                $cookie_name = substr($cookie_name, 7);
                return $cookie_name;
            }
        }

        // If __Secure- prefixed, determine if we can use it
        if (strpos($cookie_name, '__Secure-') === 0) {
            if (strpos($this->cms_sc_site_url, 'https://') !== 0) { // Cannot use __Secure- if not running securely
                $cookie_name = substr($cookie_name, 9);
                return $cookie_name;
            }
        }

        return $cookie_name;
    }
}

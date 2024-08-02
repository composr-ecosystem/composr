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
    protected $cms_sc_site_url = '';
    protected $cms_sc_profile_url = '';
    protected $cms_sc_report_guidance_url = '';
    protected $cms_sc_simple_report_url = '';
    protected $cms_sc_login_url = '';
    protected $cms_sc_lostpassword_url = '';

    function register()
    {
        $this->name = 'Composr Plugin';
        $this->description = 'This plugin extends MantisBT to work directly with Composr CMS';
        $this->page = '';
        $this->version = '11.0';
        $this->requires = array(
            'MantisCore' => '2.16',
        );
        $this->author = 'Christopher Graham / Patrick Schmalstig';
        $this->contact = 'info@composr.app';
        $this->url = 'https://composr.app';

        // Set up Composr-specific URL paths
        require(__DIR__ . '/../../_config.php');
        global $SITE_INFO;
        $this->cms_sc_site_url = $SITE_INFO['base_url'];
        $this->cms_sc_profile_url = $this->cms_sc_site_url . '/members/view.htm';
        $this->cms_sc_report_guidance_url = $this->cms_sc_site_url . '/docs/tut-software-feedback.htm';
        $this->cms_sc_simple_report_url = $this->cms_sc_site_url . '/report-issue.htm';
        $this->cms_sc_login_url = $this->cms_sc_site_url . '/login.htm';
        $this->cms_sc_lostpassword_url = $this->cms_sc_lostpassword_url . '/lost-password.htm';
    }

    function hooks()
    {
        return array(
            'EVENT_CORE_HEADERS' => 'event_core_headers',
            'EVENT_LAYOUT_CONTENT_BEGIN' => 'event_layout_content_begin',
            'EVENT_MENU_ISSUE_RELATIONSHIP' => 'event_menu_issue_relationship',
        );
    }

    function event_core_headers()
    {
        require_api('utility_api');
        require_api('gpc_api');

        // Redirect the Mantis account page to the Composr members page
        if (is_page_name( 'account_page.php' )) {
            header('Location: ' . $this->cms_sc_profile_url);
            exit();
        }

        // LEGACY: Redirect simple project reporting to our new decision tree wizard for reporting issues
        if (is_page_name( 'bug_report_page.php' ) && (gpc_get_int( 'simple', 0 ) != 0)) {
            header('Location: ' . $this->cms_sc_simple_report_url);
            exit();
        }

        // Redirect the Mantis login page to the Composr login page
        if (is_page_name( 'login_page.php' )) {
            header('Location: ' . $this->cms_sc_login_url);
            exit();
        }

        // Redirect the Mantis lost password page to the Composr lost password page
        if (is_page_name( 'lost_pwd_page.php' )) {
            header('Location: ' . $this->cms_sc_lostpassword_url);
            exit();
        }
    }

    function event_layout_content_begin()
    {
        require_api('utility_api');

        if (is_page_name( 'bug_report_page.php' )) {
            // Composr - bug reporting guidance
            require_api('lang_api');
            require_api('current_user_api');
            ?>
            <p>
                <?php echo sprintf(lang_get('cms_bug_report_guidance'), $this->cms_sc_report_guidance_url);?>
            </p>

            <?php if ( current_user_is_anonymous() ) { ?>
            <p>
                <?php echo lang_get( 'cms_not_logged_in_bad' ); ?>
            </p>
            <?php
            }
        }

        // TODO: bug_sponsorship_list_view_inc; convert to using points escrow
    }

    function event_menu_issue_relationship($bug_id)
    {
        // Add a button for searching commits tagged with this issue
        require_api('lang_api');
        return [
            plugin_lang_get( 'search_commits' ) => 'https://gitlab.com/composr-foundation/composr/commits/master?search=MANTIS-' . strval($bug_id)
        ];
    }

    // TODO: bugnote_text antispam validation + JavaScript placeholder if user is anonymous
}

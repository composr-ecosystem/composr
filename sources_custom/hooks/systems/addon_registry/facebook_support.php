<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_addon_registry_facebook_support
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Third Party Integration';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Kamen / Naveen / Chris';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'Icon by leslienayibe',
            'Class by Facebook Inc.',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Substantial Facebook integration for your Composr website.

Features:
 - User\'s can log in to your site using their Facebook profile (for Conversr-sites only)
 - News and calendar actions can be syndicated to a Facebook group/page
 - New Facebook Page block (allows users to like your site, shows those that have, and view page posts)
 - New Facebook \'Like button\' block (liking a page)
 - New Facebook Comments block (comments with a nice Facebook UI)
 - New Facebook Page block (embedding a page)
 - Facebook Like button on the default [tt]main_screen_actions[/tt] block

For this addon to work you need to configure Composr\'s Facebook configuration settings, which includes getting a Facebook app ID.

Please be aware that this addon overrides some common templates to add Facebook functionality to them, such as [tt]LOGIN_SCREEN.tpl[/tt] and [tt]BLOCK_SIDE_PERSONAL_STATS_NO.tpl[/tt].

The documentation for this addon is covered in a [url="' . get_brand_base_url() . '/docs/tut_facebook.htm"]dedicated tutorial[/url].
For a demo, see this [url="video tutorial"]https://www.youtube.com/watch?v=HUZ_O5io0F0[/url].
';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array('tut_facebook');
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'PHP sessions',
                'PHP CuRL extension',
            ),
            'recommends' => array('activity_feed'),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images_custom/icons/48x48/menu/facebook.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images_custom/icons/24x24/menu/facebook.png',
            'themes/default/images_custom/icons/48x48/menu/facebook.png',
            'sources_custom/hooks/systems/addon_registry/facebook_support.php',
            'sources_custom/hooks/systems/startup/facebook.php',
            'sources_custom/facebook/index.html',
            'sources_custom/facebook/jsonwrapper/JSON/index.html',
            'sources_custom/facebook/jsonwrapper/index.html',
            'lang_custom/EN/facebook.ini',
            'themes/default/templates_custom/BLOCK_MAIN_FACEBOOK_PAGE.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_FACEBOOK_LIKE.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_FACEBOOK_COMMENTS.tpl',
            'sources_custom/blocks/main_facebook_page.php',
            'sources_custom/blocks/main_facebook_like.php',
            'sources_custom/blocks/main_facebook_comments.php',
            'sources_custom/facebook_connect.php',
            'sources_custom/hooks/systems/symbols/FB_CONNECT_UID.php',
            'sources_custom/hooks/systems/symbols/FB_CONNECT_ACCESS_TOKEN.php',
            'sources_custom/hooks/systems/symbols/USER_FB_CONNECT.php',
            'sources_custom/hooks/systems/symbols/FB_CONNECT_LOGGED_OUT.php',
            'sources_custom/hooks/systems/symbols/FB_CONNECT_FINISHING_PROFILE.php',
            'sources_custom/users.php',
            'sources_custom/cns_members.php',
            'themes/default/templates_custom/BLOCK_SIDE_PERSONAL_STATS_NO.tpl',
            'themes/default/templates_custom/BLOCK_SIDE_PERSONAL_STATS.tpl',
            'themes/default/templates_custom/FACEBOOK_FOOTER.tpl',
            'themes/default/templates_custom/MEMBER_FACEBOOK.tpl',
            'themes/default/templates_custom/LOGIN_SCREEN.tpl',
            'themes/default/templates_custom/CNS_GUEST_BAR.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_SCREEN_ACTIONS.tpl',
            'themes/default/javascript_custom/facebook.js',
            'themes/default/templates_custom/BLOCK_TOP_LOGIN.tpl',
            'sources_custom/users_active_actions.php',
            'sources_custom/hooks/systems/syndication/facebook.php',
            'sources_custom/hooks/systems/page_groupings/facebook.php',
            'sources_custom/hooks/systems/login_providers/facebook.php',
            'adminzone/pages/minimodules_custom/facebook_oauth.php',
            'sources_custom/facebook/facebook.php',
            'sources_custom/facebook/jsonwrapper/JSON/JSON.php',
            'sources_custom/facebook/jsonwrapper/JSON/LICENSE',
            'facebook_connect.php',
            'sources_custom/hooks/modules/members/facebook.php',
            'sources_custom/hooks/systems/config/facebook_allow_signups.php',
            'sources_custom/hooks/systems/config/facebook_appid.php',
            'sources_custom/hooks/systems/config/facebook_secret_code.php',
            'sources_custom/hooks/systems/config/facebook_uid.php',
            'sources_custom/hooks/systems/config/facebook_sync_avatar.php',
            'sources_custom/hooks/systems/config/facebook_sync_dob.php',
            'sources_custom/hooks/systems/config/facebook_sync_email.php',
            'sources_custom/hooks/systems/config/facebook_sync_username.php',
            'sources_custom/cns_field_editability.php',
            'sources_custom/facebook/.htaccess',
        );
    }
}

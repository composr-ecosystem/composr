<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_addon_registry_facebook_support
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for (used in generating the TAR filename).
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return null;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        // Best to just categorise properly as it's not bundled
        //return is_maintained('facebook') ? 'Third Party Integration' : 'Development';
        return 'Third Party Integration';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Kamen / Naveen / Chris';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'Class by Facebook Inc.',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Substantial {$IS_MAINTAINED,facebook,Facebook integration} for your Composr website.

Features:
 - User\'s can log in to your site using their Facebook profile (for Conversr-sites only, and requires hybridauth addon also)
 - New Facebook Page block (allows users to like your site, shows those that have, and view page posts)
 - New Facebook \'Like button\' block (liking a page)
 - New Facebook Comments block (comments with a nice Facebook UI)
 - New Facebook Page block (embedding a page)
 - Facebook Like button on the default [tt]main_screen_actions[/tt] block

For this addon to work you need to configure Composr\'s Facebook configuration settings, which includes getting a Facebook app ID.

Please be aware that this addon overrides some common templates to add Facebook functionality to them, such as [tt]LOGIN_SCREEN.tpl[/tt] and [tt]BLOCK_SIDE_PERSONAL_STATS_NO.tpl[/tt].

The documentation for this addon is covered in a [url="' . get_brand_base_url() . '/docs/sup_facebook.htm"]dedicated tutorial[/url].
For a demo, see this [url="video tutorial"]https://www.youtube.com/watch?v=HUZ_O5io0F0[/url].
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return ['sup_facebook'];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [
                'PHP curl extension',
                'PHP sessions extension',
                'SSL',
            ],
            'recommends' => [
                'activity_feed',
                'hybridauth',
            ],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/links/facebook.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/blocks/main_facebook_comments.php',
            'sources_custom/blocks/main_facebook_like.php',
            'sources_custom/blocks/main_facebook_page.php',
            'sources_custom/hooks/systems/addon_registry/facebook_support.php',
            'sources_custom/hooks/systems/config/facebook_allow_signups.php',
            'sources_custom/hooks/systems/config/facebook_appid.php',
            'sources_custom/hooks/systems/config/facebook_secret_code.php',
            'sources_custom/hooks/systems/config/facebook_uid.php',
            'lang_custom/EN/facebook.ini',
            'themes/default/templates_custom/BLOCK_MAIN_FACEBOOK_COMMENTS.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_FACEBOOK_LIKE.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_FACEBOOK_PAGE.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_SCREEN_ACTIONS.tpl',
            'sources_custom/hooks/systems/hybridauth/facebook.php',
            'sources_custom/hooks/systems/contentious_overrides/facebook_support.php',
            'sources_custom/hooks/systems/trusted_sites/facebook_support.php',

            'themes/default/javascript_custom/facebook_support.js',
            'sources_custom/hooks/systems/startup/facebook.php',
            'themes/default/templates_custom/FACEBOOK_FOOTER.tpl',
        ];
    }
}

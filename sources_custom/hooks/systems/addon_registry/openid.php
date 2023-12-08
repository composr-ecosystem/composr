<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    openid
 */

/**
 * Hook class.
 */
class Hook_addon_registry_openid
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
        return 'Development';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'Mewp',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'MIT';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'OpenID login support to Composr, so you can log in with, for example, a Google account.

This is based on the work of Martin Conte Mac Donell (OpenID-realselector) and Mewp (LightOpenID).

[b]This addon has now been moved to the Development category because it no longer seems to work properly, and major providers including Google and Facebook have dropped OpenID support[/b]. Ideally it will be rewritten as OpenID Connect, on top of a shared Composr oAuth framework.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
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
                'PHP OpenSSL extension',
            ),
            'recommends' => array(),
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
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/openid.php',
            'sources_custom/hooks/systems/login_providers/openid.php',
            'sources_custom/miniblocks/openid.php',
            'sources_custom/openid.php',
            'sources_custom/users.php',
            'sources_custom/users_active_actions.php',
            'themes/default/css_custom/openid.css',
            'themes/default/images_custom/openid/balloon.png',
            'themes/default/images_custom/openid/indicator.gif',
            'themes/default/images_custom/openid/openid-icons.png',
            'themes/default/javascript_custom/openid.js',
            'themes/default/templates_custom/LOGIN_SCREEN.tpl',
        );
    }
}

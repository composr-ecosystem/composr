<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    git_status
 */

/**
 * Hook class.
 */
class Hook_addon_registry_git_status
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11'; // addon_version_auto_update df36ba4f37a1107a8d5c88d10e580ccf
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
        return 11.9;
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Development';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Adds an administrative Git Status module.

This is useful when using Git for deployment on staging and live servers, where non-development staff are making on-server changes through the Composr UI that regularly need to be re-synched with the main Git repository. It helps you assess on-server and development changes, as well as get server changes back onto a development machine for proper committing through Git.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return ['sup_staging_servers'];
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [],
            'recommends' => ['geshi'],
            'conflicts_with' => []
        ];
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/status/notice.svg';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'adminzone/pages/minimodules_custom/admin_git_status.php',
            'lang_custom/EN/git_status.ini',
            'sources_custom/git_status.php',
            'sources_custom/hooks/systems/addon_registry/git_status.php',
            'sources_custom/hooks/systems/config/git_live_branch.php',
            'sources_custom/hooks/systems/page_groupings/git_status.php',
            'themes/default/css_custom/git_status.css',
            'themes/default/javascript_custom/git_status.js',
            'themes/default/templates_custom/GIT_STATUS_FILE_SCREEN.tpl',
            'themes/default/templates_custom/GIT_STATUS_SCREEN.tpl',
        ];
    }
}

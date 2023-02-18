<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
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
        return 'Licensed on the same terms as Composr';
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
            'sources_custom/hooks/systems/addon_registry/git_status.php',
            'sources_custom/hooks/systems/page_groupings/git_status.php',
            'sources_custom/git_status.php',
            'adminzone/pages/minimodules_custom/admin_git_status.php',
            'themes/default/templates_custom/GIT_STATUS_SCREEN.tpl',
            'themes/default/templates_custom/GIT_STATUS_FILE_SCREEN.tpl',
            'themes/default/javascript_custom/git_status.js',
            'themes/default/css_custom/git_status.css',
            'sources_custom/hooks/systems/config/git_live_branch.php',
            'lang_custom/EN/git_status.ini',
        ];
    }
}

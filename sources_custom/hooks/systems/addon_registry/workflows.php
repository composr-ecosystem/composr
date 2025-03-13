<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    workflows
 */

/**
 * Hook class.
 */
class Hook_addon_registry_workflows
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
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_md5.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.2'; // addon_version_auto_update 3722802745764cc3471076072abeeff3
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
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Admin Utilities';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Warburton';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Extend the simple yes/no validation system of Composr to implement user-defined "workflows". A workflow contains an ordered list of "approval levels", such as \'design\' or \'spelling\', and each of these has a list of usergroups which have permission to approve it.

New content enters the default workflow (unless another is specified) and notifications are sent to those users with permission to approve the next level. This continues until all of the levels are approved, at which point the content goes live.

Note that this addon only affects galleries at the moment, and it requires the "validation" system to be installed (this comes with Composr but may have been uninstalled). Other content types can be added by a programmer as this addon has been implemented in a modular way.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
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
                'validation',
                'galleries',
                //'core_all_icons',
            ],
            'recommends' => [],
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
        return 'themes/default/images/icons/spare/workflows.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'adminzone/pages/modules_custom/admin_workflow.php',
            'lang_custom/EN/workflows.ini',
            'sources_custom/hooks/systems/actionlog/workflows.php',
            'sources_custom/hooks/systems/addon_registry/workflows.php',
            'sources_custom/hooks/systems/contentious_overrides/workflows.php',
            'sources_custom/hooks/systems/notifications/workflow_step.php',
            'sources_custom/hooks/systems/page_groupings/workflows.php',
            'sources_custom/hooks/systems/privacy/workflows.php',
            'sources_custom/workflows.php',
            'sources_custom/workflows2.php',
            'themes/default/templates_custom/WORKFLOW_BOX.tpl',
        ];
    }
}

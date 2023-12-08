<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    workflows
 */

/**
 * Hook class.
 */
class Hook_addon_registry_workflows
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
        return 'New Features';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Warburton';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
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
        return 'Extend the simple yes/no validation system of Composr to allows user-defined "workflows". A workflow contains an ordered list of "approval levels", such as \'design\' or \'spelling\', and each of these has a list of usergroups which have permission to approve it.

New content enters the default workflow (unless another is specified) and notifications are sent to those users with permission to approve the next level. This continues until all of the levels are approved, at which point the content goes live.

Note that this addon only affects galleries at the moment, and it requires the "unvalidated" system to be installed (this comes with Composr but may have been uninstalled). Other content types can be added by a programmer as this addon has been implemented in a modular way.';
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
                'unvalidated',
                'galleries',
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
        return 'themes/default/images_custom/icons/48x48/menu/workflows.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images_custom/icons/24x24/menu/workflows.png',
            'themes/default/images_custom/icons/48x48/menu/workflows.png',
            'sources_custom/hooks/systems/addon_registry/workflows.php',
            'sources_custom/hooks/systems/notifications/workflow_step.php',
            'lang_custom/EN/workflows.ini',
            'cms/pages/modules_custom/cms_galleries.php',
            'adminzone/pages/modules_custom/admin_workflow.php',
            'sources_custom/workflows.php',
            'sources_custom/workflows2.php',
            'sources_custom/galleries2.php',
            'sources_custom/form_templates.php',
            'sources_custom/hooks/systems/page_groupings/workflows.php',
            'sources_custom/hooks/modules/admin_unvalidated/images.php',
            'sources_custom/hooks/modules/admin_unvalidated/videos.php',
            'themes/default/templates_custom/FORM_SCREEN_INPUT_VARIOUS_TICKS.tpl',
            'site/pages/modules_custom/galleries.php',
            'adminzone/pages/modules_custom/admin_unvalidated.php',
            'themes/default/templates_custom/WORKFLOW_BOX.tpl',
        );
    }
}

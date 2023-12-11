<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    tester
 */

/**
 * Hook class.
 */
class Hook_addon_registry_tester
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
        return 'Used for maintaining manual test sets and organising marking things tested.';
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
            'requires' => array(),
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
        return 'themes/default/images/icons/48x48/menu/_generic_admin/tool.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/tester.php',
            'themes/default/css_custom/tester.css',
            'themes/default/templates_custom/TESTER_ADD_SECTION_SCREEN.tpl',
            'themes/default/templates_custom/TESTER_GO_SCREEN.tpl',
            'themes/default/templates_custom/TESTER_GO_SECTION.tpl',
            'themes/default/templates_custom/TESTER_GO_TEST.tpl',
            'themes/default/templates_custom/TESTER_REPORT.tpl',
            'themes/default/templates_custom/TESTER_STATISTICS_MEMBER.tpl',
            'themes/default/templates_custom/TESTER_STATISTICS_SCREEN.tpl',
            'themes/default/templates_custom/TESTER_TEST_GROUP.tpl',
            'themes/default/templates_custom/TESTER_TEST_GROUP_NEW.tpl',
            'themes/default/templates_custom/TESTER_TEST_SET.tpl',
            'lang_custom/EN/tester.ini',
            'site/pages/modules_custom/tester.php',
            'sources_custom/hooks/systems/content_meta_aware/tester.php',
            'sources_custom/hooks/systems/config/bug_report_text.php',
            'sources_custom/hooks/systems/config/tester_forum_name.php',
            'sources_custom/hooks/systems/page_groupings/tester.php',
        );
    }
}

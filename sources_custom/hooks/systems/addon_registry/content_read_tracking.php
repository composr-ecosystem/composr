<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    content_read_tracking
 */

/**
 * Hook class.
 */
class Hook_addon_registry_content_read_tracking
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
        return 'General-purpose has-read tracking. New [tt]MARK_READ[/tt] and [tt]HAS_READ[/tt] symbols allow you to track whether members have read any kind of content. Just place them in the appropriate templates. For example, [tt]{+START,IF,{$NOT,{$HAS_READ,news,{ID}}}}This is unread{+END}[/tt] in [tt]NEWS_BOX.tpl[/tt] and [tt]{$MARK_READ,news,{ID}}[/tt] in [tt]NEWS_ENTRY_SCREEN.tpl[/tt].';
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
            'sources_custom/hooks/systems/addon_registry/content_read_tracking.php',
            'sources_custom/hooks/systems/symbols/MARK_READ.php',
            'sources_custom/hooks/systems/symbols/HAS_READ.php',
        );
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('content_read');
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install($upgrade_from = null)
    {
        if (is_null($upgrade_from)) {
            $GLOBALS['SITE_DB']->create_table('content_read', array(
                'r_content_type' => '*ID_TEXT',
                'r_content_id' => '*ID_TEXT',
                'r_member_id' => '*MEMBER',
                'r_time' => 'TIME',
            ));
            $GLOBALS['SITE_DB']->create_index('content_read', 'content_read', array('r_content_type', 'r_content_id'));
            $GLOBALS['SITE_DB']->create_index('content_read', 'content_read_cleanup', array('r_time'));
        }
    }
}

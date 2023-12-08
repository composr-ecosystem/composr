<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    trickstr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_trickstr
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
        return 'Fun and Games';
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
        return 'A chat bot for your chatroom named Trickstr who will interact with your members. Simply install the addon and chat away to Trickstr. Note that Trickstr is only active if there are no more than 2 members in a chatroom.';
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
                'chat',
                'mysql',
            ),
            'recommends' => array(
            ),
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
            'sources_custom/hooks/systems/addon_registry/trickstr.php',
            'sources_custom/programe/.htaccess',
            'sources_custom/programe/aiml/.htaccess',
            'sources_custom/programe/aiml/index.html',
            'sources_custom/programe/index.html',
            'sources_custom/hooks/modules/chat_bots/knowledge.txt',
            'sources_custom/hooks/modules/chat_bots/trickstr.php',
            'sources_custom/programe/aiml/readme.txt',
            'sources_custom/programe/aiml/startup.xml',
            'sources_custom/programe/aiml/std-65percent.aiml',
            'sources_custom/programe/aiml/std-pickup.aiml',
            'sources_custom/programe/botloaderfuncs.php',
            'sources_custom/programe/customtags.php',
            'sources_custom/programe/db.sql',
            'sources_custom/programe/graphnew.php',
            'sources_custom/programe/respond.php',
            'sources_custom/programe/util.php',
        );
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

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
        return 'Fun and Games';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
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
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'A chat bot for your chatroom named Trickstr who will interact with your members. Simply install the addon and chat away to Trickstr. Note that Trickstr is only active if there are no more than 2 members in a chatroom.';
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
                'chat',
                'MySQL',
            ],
            'recommends' => [
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
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/trickstr.php',
            'sources_custom/hooks/modules/chat_bots/knowledge.txt',
            'sources_custom/hooks/modules/chat_bots/trickstr.php',
            'sources_custom/programe/.htaccess',
            'sources_custom/programe/index.html',
            'sources_custom/programe/aiml/.htaccess',
            'sources_custom/programe/aiml/index.html',
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
        ];
    }
}

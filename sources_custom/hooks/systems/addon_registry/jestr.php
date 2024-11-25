<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    jestr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_jestr
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
     * Get the version of the software this addon is for (used in generating the TAR filename).
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
        return 11.9;
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
        return [
            'Martyr2 (for piglatin)',
        ];
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
        return 'Play tricks on your website members. After installing the addon you can add a number of practical jokes to one or more usergroups using their ID number which can be found under Security > User groups > Edit Usergroups.

The jokes you can play are:
 - String Changes -- Change some words or spellings to others
 - Emoticon Magnet -- Make the emoticons follow the cursor around the screen
 - Name Changer -- Apply a name changer for users within a usergroup
 - Avatar switch -- Make it appear to them that everyone is using their avatar and they don\'t have one
 - 1337 speech changer -- Make the post appear to have been written in 1337 speech to members of that user group
 - Piglatin -- Make the post appear to have been written in Piglatin to members of that user group.';
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
                'Conversr',
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
            'forum/pages/modules_custom/topicview.php',
            'lang_custom/EN/jestr.ini',
            'sources_custom/forum/cns.php',
            'sources_custom/hooks/systems/addon_registry/jestr.php',
            'sources_custom/hooks/systems/config/jestr_avatar_switch_shown_for.php',
            'sources_custom/hooks/systems/config/jestr_emoticon_magnet_shown_for.php',
            'sources_custom/hooks/systems/config/jestr_leet_shown_for.php',
            'sources_custom/hooks/systems/config/jestr_name_changes.php',
            'sources_custom/hooks/systems/config/jestr_name_changes_shown_for.php',
            'sources_custom/hooks/systems/config/jestr_owo_shown_for.php',
            'sources_custom/hooks/systems/config/jestr_piglatin_shown_for.php',
            'sources_custom/hooks/systems/config/jestr_string_changes.php',
            'sources_custom/hooks/systems/config/jestr_string_changes_shown_for.php',
            'sources_custom/hooks/systems/contentious_overrides/jestr.php',
            'themes/default/javascript_custom/jestr.js',
            'themes/default/templates_custom/EMOTICON_IMG_CODE_THEMED.tpl',
        ];
    }
}

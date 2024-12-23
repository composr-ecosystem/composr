<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    buildr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_buildr
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        $ret = [
            'uploads/buildr_addon',
        ];
        if ($runtime) {
            $ret = array_merge($ret, [
                'uploads/buildr_addon/*',
            ]);
        }
        return $ret;
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.1'; // addon_version_auto_update dba4ae1e2cd8c0da9ffae4eb44111298
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
            'Clip art from "Madlantern Arts Clipart" used with permission.',
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
        return 'Buildr -- A world of fun.

A "multi user dungeon" (MUD) environment where members (players) may interact with each other and construct virtual worlds. The system includes an economy based on points.

There is a very carefully selected feature-set that allows interesting world interactions; quests, adventures, simulations, and other things may all be created by clever use of this feature-set.

Buildr is a full zone addon for Composr.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [
            'tut_points',
        ];
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
                'points',
                'ecommerce',
                'chat',
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
        return 'themes/default/images/icons/spare/world.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'buildr/index.php',
            'buildr/map.php',
            'buildr/pages/comcode/.htaccess',
            'buildr/pages/comcode/EN/.htaccess',
            'buildr/pages/comcode/EN/index.html',
            'buildr/pages/comcode/index.html',
            'buildr/pages/comcode_custom/.htaccess',
            'buildr/pages/comcode_custom/EN/.htaccess',
            'buildr/pages/comcode_custom/EN/docs.txt',
            'buildr/pages/comcode_custom/EN/home.txt',
            'buildr/pages/comcode_custom/EN/index.html',
            'buildr/pages/comcode_custom/EN/rules.txt',
            'buildr/pages/comcode_custom/index.html',
            'buildr/pages/html/.htaccess',
            'buildr/pages/html/EN/.htaccess',
            'buildr/pages/html/EN/index.html',
            'buildr/pages/html/index.html',
            'buildr/pages/html_custom/EN/.htaccess',
            'buildr/pages/html_custom/EN/index.html',
            'buildr/pages/html_custom/index.html',
            'buildr/pages/index.html',
            'buildr/pages/minimodules/.htaccess',
            'buildr/pages/minimodules/index.html',
            'buildr/pages/minimodules_custom/.htaccess',
            'buildr/pages/minimodules_custom/index.html',
            'buildr/pages/modules/.htaccess',
            'buildr/pages/modules/index.html',
            'buildr/pages/modules_custom/.htaccess',
            'buildr/pages/modules_custom/buildr.php',
            'buildr/pages/modules_custom/index.html',
            'buildr/wmessages.php',
            'data_custom/modules/buildr/docs/basics2.0.png',
            'data_custom/modules/buildr/docs/basics2.1.png',
            'data_custom/modules/buildr/docs/basics2.2.png',
            'data_custom/modules/buildr/docs/basics2.3.0.png',
            'data_custom/modules/buildr/docs/basics2.3.1.png',
            'data_custom/modules/buildr/docs/basics2.3.2.png',
            'data_custom/modules/buildr/docs/basics2.4.png',
            'data_custom/modules/buildr/docs/basics2.5.0.png',
            'data_custom/modules/buildr/docs/index.html',
            'data_custom/modules/buildr/docs/port0.0.png',
            'data_custom/modules/buildr/docs/port0.1.png',
            'data_custom/modules/buildr/docs/port0.2.png',
            'data_custom/modules/buildr/docs/port0.3.png',
            'data_custom/modules/buildr/docs/port0.4.png',
            'data_custom/modules/buildr/docs/port0.5.png',
            'data_custom/modules/buildr/docs/realm1.1.png',
            'data_custom/modules/buildr/docs/realm1.2.png',
            'data_custom/modules/buildr/docs/realm1.3.png',
            'data_custom/modules/buildr/docs/realm1.4.png',
            'data_custom/modules/buildr/docs/room1.0.png',
            'data_custom/modules/buildr/docs/room1.1.png',
            'data_custom/modules/buildr/docs/room1.2.png',
            'data_custom/modules/buildr/docs/room1.3.png',
            'data_custom/modules/buildr/docs/roompw1.0.png',
            'data_custom/modules/buildr/docs/roompw1.1.png',
            'data_custom/modules/buildr/index.html',
            'lang_custom/EN/buildr.ini',
            'sources_custom/buildr.php',
            'sources_custom/buildr_action.php',
            'sources_custom/buildr_screens.php',
            'sources_custom/buildr_scripts.php',
            'sources_custom/hooks/modules/admin_themewizard/buildr.php',
            'sources_custom/hooks/modules/members/buildr.php',
            'sources_custom/hooks/systems/addon_registry/buildr.php',
            'sources_custom/hooks/systems/member_boxes/buildr.php',
            'sources_custom/hooks/systems/page_groupings/buildr.php',
            'sources_custom/hooks/systems/privacy/buildr.php',
            'themes/default/css_custom/buildr.css',
            'themes/default/images_custom/buildr/additem.png',
            'themes/default/images_custom/buildr/additemcopy.png',
            'themes/default/images_custom/buildr/addportal.png',
            'themes/default/images_custom/buildr/addroom.png',
            'themes/default/images_custom/buildr/delrealm.png',
            'themes/default/images_custom/buildr/delroom.png',
            'themes/default/images_custom/buildr/emergencyteleport.png',
            'themes/default/images_custom/buildr/help.png',
            'themes/default/images_custom/buildr/index.html',
            'themes/default/images_custom/buildr/inventory.png',
            'themes/default/images_custom/buildr/map.png',
            'themes/default/images_custom/buildr/realms.png',
            'themes/default/images_custom/buildr/refresh.png',
            'themes/default/images_custom/buildr/rules.png',
            'themes/default/javascript_custom/buildr.js',
            'themes/default/templates_custom/W_CONFIRM_SCREEN.tpl',
            'themes/default/templates_custom/W_INVENTORY_ITEM.tpl',
            'themes/default/templates_custom/W_INVENTORY_SCREEN.tpl',
            'themes/default/templates_custom/W_ITEMCOPY_SCREEN.tpl',
            'themes/default/templates_custom/W_ITEM_SCREEN.tpl',
            'themes/default/templates_custom/W_MAIN_ITEM.tpl',
            'themes/default/templates_custom/W_MAIN_ITEMS_HELD.tpl',
            'themes/default/templates_custom/W_MAIN_ITEMS_OWNED.tpl',
            'themes/default/templates_custom/W_MAIN_ITEM_OWNED.tpl',
            'themes/default/templates_custom/W_MAIN_ITEM_OWNED_SEP.tpl',
            'themes/default/templates_custom/W_MAIN_MEMBER.tpl',
            'themes/default/templates_custom/W_MAIN_PEOPLE_HERE.tpl',
            'themes/default/templates_custom/W_MAIN_PEOPLE_SEP.tpl',
            'themes/default/templates_custom/W_MAIN_PERSON_HERE.tpl',
            'themes/default/templates_custom/W_MAIN_PORTAL.tpl',
            'themes/default/templates_custom/W_MAIN_SCREEN.tpl',
            'themes/default/templates_custom/W_MESSAGES_HTML_WRAP.tpl',
            'themes/default/templates_custom/W_MESSAGE_ALL.tpl',
            'themes/default/templates_custom/W_MESSAGE_TO.tpl',
            'themes/default/templates_custom/W_PORTAL_SCREEN.tpl',
            'themes/default/templates_custom/W_QUESTION_SCREEN.tpl',
            'themes/default/templates_custom/W_REALLOCATE.tpl',
            'themes/default/templates_custom/W_REALM_LIST_ENTRY.tpl',
            'themes/default/templates_custom/W_REALM_SCREEN.tpl',
            'themes/default/templates_custom/W_REALM_SCREEN_QUESTION.tpl',
            'themes/default/templates_custom/W_ROOM_SCREEN.tpl',
            'themes/default/templates_custom/W_TROLL.tpl',
            'themes/default/templates_custom/W_TROLL_QUESTION.tpl',
            'uploads/buildr_addon/index.html',
        ];
    }
}

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    addon_publish
 */

/**
 * Find existing category ID for a named category. Insert into the database if the category does not exist.
 *
 * @param  SHORT_TEXT $category_name The name of the download category to search or create
 * @param  ?AUTO_LINK $parent_id The parent ID where the category is located (null: under "Addons" category or under root category if Addons)
 * @param  ?LONG_TEXT $description Provide a description for the category if it does not exist (null: use a default description)
 * @return AUTO_LINK The ID of the download category
 */
function find_addon_category_download_category(string $category_name, ?int $parent_id = null, ?string $description = null) : int
{
    static $cache = [];

    if (isset($cache[$category_name][$parent_id])) {
        return $cache[$category_name][$parent_id];
    }

    if ($parent_id === null) {
        if ($category_name == 'Addons') {
            $parent_id = db_get_first_id();
        } else {
            global $COMPOSR_APP_ADDONS_CATEGORY;
            if (isset($COMPOSR_APP_ADDONS_CATEGORY)) {
                $parent_id = $COMPOSR_APP_ADDONS_CATEGORY;
            } else {
                $parent_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => db_get_first_id(), $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Addons']);
                if ($parent_id === null) {
                    $parent_id = find_addon_category_download_category('Addons'); // This will auto-create it
                }
                $COMPOSR_APP_ADDONS_CATEGORY = $parent_id;
            }

            if (isset($cache[$category_name][$parent_id])) {
                return $cache[$category_name][$parent_id];
            }

            if ($description === null) {
                // Copy version category description from parent ("Addons")
                $description = get_translated_text($GLOBALS['SITE_DB']->query_select_value('download_categories', 'the_description', ['id' => $parent_id]));
                $description = str_replace('[title="2"]Choose ' . brand_name() . ' version below[/title]', '[title="2"]Choose addon category below[/title]', $description);
            }
        }
    }

    require_code('downloads2');
    $id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $parent_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => $category_name]);
    if ($id === null) {
        // Missing, add it...

        if ($description === null) {
            $description = '';
            switch ($category_name) {
                // Some categories may have hard-coded default descriptions. Any others will default to blank.
                case 'Themes':
                    $description = "Themes provide a new look to " . brand_name() . ".\n\nThemes are a kind of addon. You can actually install the themes listed here directly from inside your site.\n\nGo to Admin Zone > Structure > Addons. Follow the \"Import non-bundled addon(s)\" link.\n\nThese are themes that have been released for this version of " . brand_name() . ". Themes that have been released for earlier versions would need upgrading -- you may wish to browse through them also, and contact the author if you\'d like them upgraded.";
                    break;
            }
        }

        $has_submit_access = false;

        if (substr($category_name, 0, 8) == 'Version ') {
            $theme_image = 'icons/spare/installation';
        } else {
            switch ($category_name) {
                case (brand_name() . ' Releases'):
                    $theme_image = 'icons/spare/installation';
                    break;

                // ---

                case 'Addons':
                    $theme_image = 'icons/menu/adminzone/structure/addons';
                    break;

                // ---

                case 'Admin Utilities':
                    $theme_image = 'icons/spare/administration';
                    $has_submit_access = true;
                    break;

                case 'Development':
                    $theme_image = 'icons/spare/development';
                    $has_submit_access = true;
                    break;

                case 'Fun and Games':
                    $theme_image = 'icons/spare/fun_and_games';
                    $has_submit_access = true;
                    break;

                case 'Graphical':
                    $theme_image = 'icons/menu/rich_content/galleries';
                    $has_submit_access = true;
                    break;

                case 'Information Display':
                    $theme_image = 'icons/menu/adminzone/setup/config/config';
                    $has_submit_access = true;
                    break;

                case 'New Features':
                    $theme_image = 'icons/menu/adminzone/tools/upgrade';
                    $has_submit_access = true;
                    break;

                case 'Themes':
                    $theme_image = 'icons/menu/adminzone/style';
                    $has_submit_access = true;
                    break;

                case 'Professional Themes':
                    $theme_image = 'icons/menu/adminzone/audit/ecommerce/ecommerce';
                    $has_submit_access = true;
                    break;

                case 'Third Party Integration':
                    $theme_image = 'icons/spare/third_party_integration';
                    $has_submit_access = true;
                    break;

                case 'Translations':
                    $theme_image = 'icons/spare/internationalisation';
                    $has_submit_access = true;
                    break;

                case 'Architecture':
                    $theme_image = 'icons/admin/component';
                    $has_submit_access = true;
                    break;

                case 'Community':
                    $theme_image = 'icons/news/community';
                    $has_submit_access = true;
                    break;

                case 'eCommerce':
                    $theme_image = 'icons/menu/adminzone/audit/ecommerce/ecommerce';
                    $has_submit_access = true;
                    break;

                case 'Uncategorised/Alpha':
                    $theme_image = 'icons/spare/maintenance';
                    $has_submit_access = true;
                    break;

                default:
                    require_code('tutorials');
                    $theme_image = _find_tutorial_image_for_tag($category_name);
                    $has_submit_access = true;
                    break;
            }
        }
        $rep_image = find_theme_image($theme_image, true, true);
        if ($rep_image == '') {
            fatal_exit('Could not find a theme image, ' . $theme_image);
        }

        require_code('cms_homesite');
        $id = add_download_category($category_name, $parent_id, $description, '', $rep_image);
        require_code('permissions2');
        set_global_category_access('downloads', $id);
        if (!$has_submit_access) {
            set_privilege_access('downloads', strval($id), 'submit_midrange_content', false);
        }
    }

    $cache[$category_name][$parent_id] = $id;

    return $id;
}

/**
 * Set privileges on a module and category for all non-admin groups.
 *
 * @param  ?ID_TEXT $permission_module The permission module (null: none required)
 * @param  ?ID_TEXT $category_name The category-name/value for the permission (null: none required)
 * @param  ID_TEXT $permission The codename of the permission
 * @param  boolean $value Whether the groups will have the privilege
 */
function set_privilege_access(?string $permission_module, ?string $category_name, string $permission, bool $value)
{
    require_code('permissions3');

    $admin_groups = $GLOBALS['FORUM_DRIVER']->get_super_admin_groups();
    $groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(false, true);
    foreach (array_keys($groups) as $group_id) {
        if (!in_array($group_id, $admin_groups)) {
            set_privilege($group_id, $permission, $value, '', $permission_module, $category_name);
        }
    }
}

/**
 * Get a list of available addons under a given category which can be published (have a TAR available).
 *
 * @param  ID_TEXT $category_name The name of the addon category
 * @param  ID_TEXT $version_branch The version branch
 * @return array A list of addons available for publishing
 */
function get_addons_list_under_category(string $category_name, string $version_branch) : array
{
    static $addons_in_cats = null;
    if ($addons_in_cats === null) {
        require_code('tar');
        require_code('files');

        foreach (['uploads/downloads', 'exports/addons'] as $dir) {
            $dh = opendir(get_custom_file_base() . '/' . $dir);
            while (($file = readdir($dh)) !== false) {
                $matches = [];
                if (preg_match('#^(\w+)-' . preg_quote($version_branch, '#') . '.tar#', $file, $matches) != 0) {
                    $path = get_custom_file_base() . '/' . $dir . '/' . $file;
                    $tar = tar_open($path, 'rb');
                    $info_file = tar_get_file($tar, 'addon.inf', true);
                    if ($info_file === null) {
                        tar_close($tar);
                        continue;
                    }

                    $ini_info = cms_parse_ini_file_fast(null, $info_file['data']);
                    tar_close($tar);
                    $_category_name = $ini_info['category'];

                    $addon_name = $matches[1];
                    if (!isset($addons_in_cats[$_category_name])) {
                        $addons_in_cats[$_category_name] = [];
                    }
                    $addons_in_cats[$_category_name][] = $addon_name;
                }
            }
            closedir($dh);
        }
    }

    $addons_here = isset($addons_in_cats[$category_name]) ? $addons_in_cats[$category_name] : [];

    // Look in local filesystem too
    // NB: Nope, this will cause errors if the TAR files for these addons do not exist (which is already checked above)
    /*
    $addons = find_all_hooks('systems', 'addon_registry');
    foreach ($addons as $addon_name => $place) {
        if ($place == 'sources_custom') {
            require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($addon_name));
            $ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($addon_name));
            if (method_exists($ob, 'get_category')) {
                $category_name_here = $ob->get_category();
            } else {
                $category_name_here = 'Uncategorised/Alpha';
            }

            if ($category_name_here == $category_name) {
                $addons_here[] = $addon_name;
            }
        }
    }
    */

    return $addons_here;
}

/**
 * Get a list of addon categories defined by non-bundled addons.
 *
 * @return array An array of addon category names
 */
function find_addon_category_list() : array
{
    $categories = [];

    $addons = find_all_hooks('systems', 'addon_registry');
    foreach ($addons as $addon_name => $place) {
        if ($place == 'sources_custom') {
            require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($addon_name));
            $ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($addon_name));
            if (method_exists($ob, 'get_category')) {
                $category_name = $ob->get_category();
            } else {
                $category_name = 'Uncategorised/Alpha';
            }

            $categories[] = $category_name;
        }
    }

    return array_unique($categories);
}

/**
 * Generate addon description Comcode from the given addon info.
 *
 * @param  array $info The addon info
 * @return LONG_TEXT A Comcode description for this addon
 */
function generate_addon_description(array $info) : string
{
    $description = $info['description'];

    $dependencies = implode(', ', $info['dependencies']);
    if ($dependencies != '') {
        $description .= "\n\n[title=\"2\"]System Requirements / Dependencies[/title]\n\n" . $dependencies;
    }

    if (empty($info['min_cms_version'])) {
        $info['min_cms_version'] = 11.0; // FUDGE: Anything without min_cms_version cannot be installed on version 11 and above.
    }

    $description .= "\n\n[title=\"2\"]Website Software Requirements[/title]\n\n";
    $description .= "Minimum software version: [tt]" . $info['min_cms_version'] . "[/tt]\n";
    if (!empty($info['max_cms_version'])) {
        $description .= "Maximum software version: [tt]" . $info['max_cms_version'] . "[/tt]\n";
    }

    $incompatibilities = implode(', ', $info['incompatibilities']);
    if ($incompatibilities != '') {
        $description .= "\n\n[title=\"2\"]Incompatibilities[/title]\n\n" . $incompatibilities;
    }

    $recommendations = implode(', ', $info['recommendations']);
    if ($recommendations != '') {
        $description .= "\n\n[title=\"2\"]Optional recommendations[/title]\n\n" . $recommendations;
    }

    $licence = $info['licence'];
    if ($licence != '') {
        $description .= "\n\n[title=\"2\"]Licence[/title]\n\n" . $licence;
    }

    $copyright_attribution = implode(', ', $info['copyright_attribution']);
    if ($copyright_attribution != '') {
        $description .= "\n\n[title=\"2\"]Additional credits/attributions[/title]\n\n" . $copyright_attribution;
    }

    return $description;
}

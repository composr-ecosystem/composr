<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/*
AJAX tree-list browsing for the whole sitemap, with particular support for permission management.
*/

/**
 * AJAX script for dynamically extended Sitemap.
 */
function sitemap_script()
{
    prepare_for_known_ajax_response();

    require_code('zones2');
    require_code('zones3');
    require_code('xml');
    require_code('sitemap');

    if (!has_actual_page_access(get_member(), 'admin_sitemap')) {
        exit();
    }

    require_code('input_filter_2');
    modsecurity_workaround_enable();

    if (php_function_allowed('set_time_limit')) {
        @set_time_limit(30);
    }

    disable_php_memory_limit(); // Needed for loading large amount of permissions (potentially)

    if (get_param_integer('set_perms', 0) == 1) {
        sitemap_script_saving();
    } else {
        sitemap_script_loading();
    }

    cms_safe_exit_flow();
}

/**
 * AJAX script for dynamically extended Sitemap: loading.
 */
function sitemap_script_loading()
{
    require_code('xml');

    // Usergroups we have
    $admin_groups = $GLOBALS['FORUM_DRIVER']->get_super_admin_groups();
    $groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(false, true);

    $default = get_param_string('default', null, true);

    header('Content-Type: text/xml');
    header("Content-Security-Policy: default-src 'none'"); // Don't allow special execution via a vector of namespace-injected HTML

    $permissions_needed = (get_param_integer('get_perms', 0) == 1); // Whether we are limiting our tree to permission-supporting
    safe_ini_set('ocproducts.xss_detect', '0');

    echo '<' . '?xml version="1.0" encoding="' . get_charset() . '"?' . '>';
    echo "\n" . '<request><result>';
    require_lang('permissions');
    require_lang('zones');
    $page_link = get_param_string('id', null, true);

    $requesting_root = ($page_link === null);

    $options = SITEMAP_GEN_NO_EMPTY_PAGE_LINKS;
    if ($permissions_needed) {
        $options |= SITEMAP_GEN_REQUIRE_PERMISSION_SUPPORT;
    } else {
        $options |= SITEMAP_GEN_CHECK_PERMS;
    }
    if (get_param_integer('label_content_types', 0) == 1) {
        $options |= SITEMAP_GEN_LABEL_CONTENT_TYPES;
    }
    if (get_param_integer('keep_full_structure', 0) == 1) {
        $options |= SITEMAP_GEN_KEEP_FULL_STRUCTURE;
    }

    $max_recurse_depth = get_param_integer('max_recurse_depth', $requesting_root ? 1 : 2/*need children of requested level*/) + 1 /*So we know whether to show expansion option*/;
    $node = retrieve_sitemap_node(
        ($page_link === null) ? '' : $page_link,
        /*$callback=*/null,
        /*$valid_node_types=*/null,
        /*$child_cutoff=*/null,
        $max_recurse_depth,
        $options,
        /*$zone=*/'_SEARCH',
        /*$meta_gather=*/0
    );

    if ($requesting_root) {
        _sitemap_node_to_xml($admin_groups, $groups, $node, $permissions_needed);
    } else {
        if (isset($node['children'])) {
            foreach ($node['children'] as $child_node) {
                _sitemap_node_to_xml($admin_groups, $groups, $child_node, $permissions_needed);
            }
        }
    }

    // Mark parent nodes for pre-expansion (we guess a bit about what there may be, it doesn't matter if we guess some wrong ones)
    if ($requesting_root) {
        echo "\n" . '<expand></expand>';
        echo "\n" . '<expand>:</expand>';
    }
    if ((!is_null($default)) && ($default != '') && (strpos($default, ':') !== false)) {
        $parts = explode(':', $default);
        $buildup = '';
        foreach ($parts as $part) {
            if ($buildup != '') {
                $buildup .= ':';
            }
            $buildup .= $part;
            echo "\n" . '<expand>' . xmlentities($buildup) . '</expand>';
            echo "\n" . '<expand>' . xmlentities($buildup) . ':</expand>';
        }
    }

    echo "\n" . '</result></request>';
}

/**
 * Convert a Sitemap node into an XML representation.
 *
 * @param  array $admin_groups Global list of admin groups.
 * @param  array $groups Global map of usergroups (ID => name).
 * @param  array $node The Sitemap node.
 * @param  boolean $permissions_needed Whether we need selectable nodes to support some selectable permissions.
 * @param  integer $recurse_level How deep in recursion we are.
 *
 * @ignore
 */
function _sitemap_node_to_xml($admin_groups, $groups, $node, $permissions_needed, $recurse_level = 0)
{
    if ($recurse_level >= get_param_integer('max_recurse_depth', 1)) {
        return;
    }

    $filter = get_param_string('filter', '');
    if ($filter != '') {
        list($zone_name, $attributes) = page_link_decode($node['page_link']);
        if (!match_key_match($filter, false, $attributes, $zone_name, $attributes['page'])) {
            return;
        }
    }

    $default = get_param_string('default', null, true);

    if (isset($node['children'])) {
        $has_children = (count($node['children']) > 0);
    } else {
        $has_children = $node['has_possible_children'];
    }

    $selectable = ((!$permissions_needed) || (count($node['permissions']) != 0)) && ($node['page_link'] != '');

    $is_root = (($node['page_link'] == ((get_option('collapse_user_zones') == '0') ? ':' : '')));

    $type = $node['content_type'];
    if ($type == 'root') {
        $type = 'zone';
    }

    $id = $node['content_id'];
    if ($id === null) {
        $id = '';
    }

    // Permissions
    $view_perms = '';
    $privilege_perms = '';
    if ($permissions_needed) {
        // View permissions
        $access = _get_view_access_for_node($admin_groups, $groups, $node);
        foreach ($groups as $group => $group_name) {
            if (!in_array($group, $admin_groups)) {
                $view_perms .= ' g_view_' . strval($group) . '="' . (isset($access[$group]) ? 'true' : 'false') . '"';
            }
        }

        // Privileges
        if (empty($node['privilege_page'])) {
            if ($is_root) {
                $overridable_privileges = _get_overridable_privileges_for_privilege_page(null);
            } else {
                $overridable_privileges = array();
            }
        } else {
            $overridable_privileges = _get_overridable_privileges_for_privilege_page($node['privilege_page']);
            if ($type != 'page') {
                $_overridable_privileges = array();
                foreach ($overridable_privileges as $override => $_cat_support) {
                    if (is_array($_cat_support)) {
                        $cat_support = $_cat_support[0];
                    } else {
                        $cat_support = $_cat_support;
                    }
                    if ($cat_support == 0) {
                        continue;
                    }
                    $_overridable_privileges[$override] = $_cat_support;
                }
                $overridable_privileges = $_overridable_privileges;
            }
        }
        if (count($overridable_privileges) > 0) {
            $privilege_access = _get_privileges_for_node($admin_groups, $groups, $node);

            foreach ($overridable_privileges as $overridable => $cat_support) {
                $lang_string = do_lang('PRIVILEGE_' . $overridable);
                if (is_array($cat_support)) {
                    $lang_string = do_lang($cat_support[1]);
                }
                if ((strlen($lang_string) > 20) && (strpos($lang_string, '(') !== false)) {
                    $lang_string = preg_replace('# \([^\)]*\)#', '', $lang_string); // Shorten long privilege describer
                }
                $privilege_perms .= ' privilege_' . $overridable . '="' . xmlentities($lang_string) . '"';
                foreach (array_keys($groups) as $group) {
                    if (!in_array($group, $admin_groups)) {
                        $override_value = isset($privilege_access[$group][$overridable]) ? $privilege_access[$group][$overridable] : -1;
                        if ($override_value != -1) {
                            $privilege_perms .= ' group_privileges_' . $overridable . '_' . strval($group) . '="' . strval($override_value) . '"';
                        }
                    }
                }
            }
        }
        if (count(array_diff(array_keys($overridable_privileges), array('submit_highrange_content', 'submit_midrange_content', 'submit_lowrange_content'))) != 0) {
            $privilege_perms .= ' inherits_something="1"';
        }

        if (count($overridable_privileges) == 0) {
            $privilege_perms .= ' no_privileges="1"';
        }

        $privilege_perms .= ' img_func_1="permissions_img_func_1" img_func_2="permissions_img_func_2" highlighted="true"';
    }

    $draggable = ($node['content_id'] !== null) && (preg_match('#^adminzone:admin\_#', $node['content_id']) == 0) && (preg_match('#^\w*:\w+$#', $node['content_id']) != 0);

    $serverid = $node['page_link'];

    // To make it more user-friendly, show a page-link as a URL
    if ((get_param_integer('use_urls', 0) == 1) && (!looks_like_url($serverid)) && (strpos($serverid, ':') !== false)) {
        $id = page_link_to_url($serverid, true);
    } else {
        $id = uniqid('', true);
    }

    echo str_replace('  ', str_repeat(' ', $recurse_level + 1), '
    <category
     serverid="' . xmlentities($serverid) . '"
     expanded="false"
     title="' . xmlentities(strip_html($node['title']->evaluate())) . '"
     description_html="' . xmlentities(isset($node['description']) ? $node['description']->evaluate() : '') . '"
     has_children="' . ($has_children ? 'true' : 'false') . '"
     selectable="' . ($selectable ? 'true' : 'false') . '"
     ' . (($node['page_link'] == $default && $default != '') ? 'selected="yes"' : '') . '
     ' . (isset($node['author']) ? ('author="' . xmlentities($node['author']) . '"') : '') . '
     ' . (isset($node['organisation']) ? ('organisation="' . xmlentities($node['organisation']) . '"') : '') . '
     ' . (isset($node['version']) ? ('version="' . xmlentities(integer_format($node['version'])) . '"') : '') . '
     ' . ($permissions_needed ? '' : ('draggable="' . ($draggable ? 'page' : 'false') . '"')) . '
     ' . ($permissions_needed ? '' : ('droppable="' . (($type == 'zone') ? 'page' : 'false') . '"')) . '
     type="' . xmlentities(is_null($type) ? '' : $type) . '"
     id="' . xmlentities($id) . '"' . $view_perms . $privilege_perms . '>
    ');

    if (isset($node['children'])) {
        foreach ($node['children'] as $child_node) {
            _sitemap_node_to_xml($admin_groups, $groups, $child_node, $permissions_needed, $recurse_level + 1);
        }
    }

    echo "\n" . str_repeat('  ', $recurse_level + 1) . '</category>' . "\n";
}

/**
 * Get a mapping of set access for a particular sitemap node.
 *
 * @param  array $admin_groups Global list of admin groups.
 * @param  array $groups Global map of usergroups (ID => name).
 * @param  array $node The sitemap node.
 * @return ?array A map of set access (group => N/A) (null: no view permissions for this node).
 *
 * @ignore
 */
function _get_view_access_for_node($admin_groups, $groups, $node)
{
    $id = $node['content_id'];
    if ($id === null) {
        $id = '';
    }

    $access = mixed();
    switch ($node['content_type']) {
        case 'root':
        case 'zone':
            $where = array('zone_name' => $id);
            $access = $GLOBALS['SITE_DB']->query_select('group_zone_access', array('group_id'), $where);
            $access = array_flip(collapse_1d_complexity('group_id', $access));
            break;

        case 'page':
        case 'comcode_page':
            $where = array('zone_name' => $node['permissions'][1]['zone_name'], 'page_name' => $node['permissions'][1]['page_name']);
            $negative_access = $GLOBALS['SITE_DB']->query_select('group_page_access', array('group_id'), $where);
            $negative_access = array_flip(collapse_1d_complexity('group_id', $negative_access));
            $access = array();
            foreach (array_keys($groups) as $group_id) {
                if (!isset($negative_access[$group_id])) {
                    $access[$group_id] = true;
                }
            }
            break;

        default:
            foreach ($node['permissions'] as $p) {
                if (isset($p['permission_module'])) {
                    $where = array('module_the_name' => $p['permission_module'], 'category_name' => $id);
                    $access = $GLOBALS['SITE_DB']->query_select('group_category_access', array('group_id'), $where);
                    $access = array_flip(collapse_1d_complexity('group_id', $access));
                }
            }
            break;
    }

    return $access;
}

/**
 * Get a mapping of set privileges for a particular sitemap node.
 *
 * @param  array $admin_groups Global list of admin groups.
 * @param  array $groups Global map of usergroups (ID => name).
 * @param  array $node The sitemap node.
 * @return ?array A map of set privileges (group => (privileges => value)) (null: no view permissions for this node).
 *
 * @ignore
 */
function _get_privileges_for_node($admin_groups, $groups, $node)
{
    $id = $node['content_id'];
    if ($id === null) {
        $id = '';
    }

    $is_root = (($node['page_link'] == ((get_option('collapse_user_zones') == '0') ? ':' : '')));

    $privilege_access = mixed();
    switch ($node['content_type']) {
        case 'root':
        case 'zone':
            if ($is_root) {
                $or_list = '';
                foreach (array_keys(_get_overridable_privileges_for_privilege_page(null)) as $privilege) {
                    if ($or_list != '') {
                        $or_list .= ' OR ';
                    }
                    $or_list .= db_string_equal_to('privilege', $privilege);
                }
                $_privilege_access = $GLOBALS['SITE_DB']->query_select('group_privileges', array('*'), array('module_the_name' => '', 'the_page' => ''), ' AND (' . $or_list . ')');
                $privilege_access = _organise_loaded_privileges($admin_groups, $groups, $_privilege_access);
            }
            break;

        case 'page':
            $_privilege_access = $GLOBALS['SITE_DB']->query_select('group_privileges', array('*'), array('the_page' => $node['privilege_page'], 'category_name' => ''));
            $privilege_access = _organise_loaded_privileges($admin_groups, $groups, $_privilege_access);
            break;

        default:
            foreach ($node['permissions'] as $p) {
                if (isset($p['permission_module'])) {
                    $_privilege_access = $GLOBALS['SITE_DB']->query_select('group_privileges', array('*'), array('module_the_name' => $p['permission_module'], 'category_name' => $id));
                    $privilege_access = _organise_loaded_privileges($admin_groups, $groups, $_privilege_access);
                }
            }
            break;
    }

    return $privilege_access;
}

/**
 * Organise loaded privileges into a more searchable structure.
 *
 * @param  array $admin_groups Global list of admin groups.
 * @param  array $groups Global map of usergroups (ID => name).
 * @param  array $_privilege_access Privilege database rows
 * @return array A map of set privileges (group => (privileges => value)).
 *
 * @ignore
 */
function _organise_loaded_privileges($admin_groups, $groups, $_privilege_access)
{
    $privilege_access = array();
    foreach (array_keys($groups) as $group_id) {
        $privilege_access[$group_id] = array();
    }
    foreach ($_privilege_access as $a) {
        $privilege_access[$a['group_id']][$a['privilege']] = $a['the_value'];
    }
    return $privilege_access;
}

/**
 * Get overridable privileges under a particular permission page.
 *
 * @param  ?ID_TEXT $privilege_page The privilege page (null: interesting ones we want to allow specification on the root sitemap node).
 * @return array A map of privileges that are overridable; privilege to 0 or 1. 0 means "not category overridable". 1 means "category overridable".
 *
 * @ignore
 */
function _get_overridable_privileges_for_privilege_page($privilege_page)
{
    if (is_null($privilege_page)) { // For root
        return array(
            'submit_cat_highrange_content' => 0,
            'edit_cat_highrange_content' => 0,
            'edit_own_cat_highrange_content' => 0,
            'delete_cat_highrange_content' => 0,
            'delete_own_cat_highrange_content' => 0,
            'submit_highrange_content' => 1,
            'bypass_validation_highrange_content' => 1,
            'edit_own_highrange_content' => 1,
            'edit_highrange_content' => 1,
            'delete_own_highrange_content' => 1,
            'delete_highrange_content' => 1,
            'submit_cat_midrange_content' => 0,
            'edit_cat_midrange_content' => 0,
            'edit_own_cat_midrange_content' => 0,
            'delete_cat_midrange_content' => 0,
            'delete_own_cat_midrange_content' => 0,
            'submit_midrange_content' => 1,
            'bypass_validation_midrange_content' => 1,
            'edit_own_midrange_content' => 1,
            'edit_midrange_content' => 1,
            'delete_own_midrange_content' => 1,
            'delete_midrange_content' => 1,
            'submit_cat_lowrange_content' => 0,
            'edit_cat_lowrange_content' => 0,
            'edit_own_cat_lowrange_content' => 0,
            'delete_cat_lowrange_content' => 0,
            'delete_own_cat_lowrange_content' => 0,
            'submit_lowrange_content' => 1,
            'bypass_validation_lowrange_content' => 1,
            'edit_own_lowrange_content' => 1,
            'edit_lowrange_content' => 1,
            'delete_own_lowrange_content' => 1,
            'delete_lowrange_content' => 1,
        );
    }

    $_overridables = extract_module_functions_page(get_module_zone($privilege_page, 'modules', null, 'php', true, false), $privilege_page, array('get_privilege_overrides'));
    $overridable_privileges = is_array($_overridables[0]) ? call_user_func_array($_overridables[0][0], $_overridables[0][1]) : eval($_overridables[0]);
    if (!is_array($overridable_privileges)) {
        $overridable_privileges = array();
    }
    return $overridable_privileges;
}

/**
 * AJAX script for dynamically extended Sitemap: saving.
 */
function sitemap_script_saving()
{
    if (!has_actual_page_access(get_member(), 'admin_permissions')) {
        exit();
    }

    // Usergroups we have
    $admin_groups = $GLOBALS['FORUM_DRIVER']->get_super_admin_groups();
    $groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(false, true);

    // Build a map of every page link we are setting permissions for
    $map = array();
    foreach (array_merge($_GET, $_POST) as $i => $page_link) {
        if (substr($i, 0, 4) == 'map_') {
            if (@get_magic_quotes_gpc()) {
                $page_link = stripslashes($page_link);
            }

            $map[intval(substr($i, 4))] = $page_link;
        }
    }

    $changed_view_access = false;
    $changed_privileges = false;

    $guest_groups = $GLOBALS['FORUM_DRIVER']->get_members_groups($GLOBALS['FORUM_DRIVER']->get_guest_id());

    // Read it all in
    foreach ($map as $i => $page_link) { // For everything we're setting at once
        $is_root = (($page_link == ((get_option('collapse_user_zones') == '0') ? ':' : '')));

        $view = post_param_integer(strval($i) . 'g_view_' . $guest_groups[0], -1);
        if ($view != -1) { // -1 means unchanged
            $GLOBALS['SITE_DB']->query_update('sitemap_cache', array('guest_access' => $view), array('page_link' => $page_link), '', 1);
        }

        // Decode page link
        $matches = array();
        $type = '';
        if (preg_match('#^([^:]*):([^:]+):.+$#', $page_link, $matches) != 0) {
            $type = 'cat';
        } elseif (preg_match('#^([^:]*):([^:]+)$#', $page_link, $matches) != 0) {
            $type = 'page';
        } elseif (preg_match('#^([^:]*):$#', $page_link, $matches) != 0) {
            $type = 'zone';
        }

        $overridable_privileges = mixed();
        switch ($type) {
            case '':
            case 'zone':
                $zone = $matches[1];

                if ($is_root) {
                    $overridable_privileges = _get_overridable_privileges_for_privilege_page(null);
                }

                // Insertion
                foreach ($groups as $group => $group_name) { // For all usergroups
                    if (!in_array($group, $admin_groups)) {
                        // View access
                        $view = post_param_integer(strval($i) . 'g_view_' . strval($group), -1);
                        if ($view != -1) { // -1 means unchanged
                            $GLOBALS['SITE_DB']->query_delete('group_zone_access', array('zone_name' => $zone, 'group_id' => $group));
                            if ($view == 1) {
                                $GLOBALS['SITE_DB']->query_insert('group_zone_access', array('zone_name' => $zone, 'group_id' => $group));
                            }

                            $changed_view_access = true;
                        }

                        if ($is_root) {
                            // Privileges
                            foreach (array_keys($overridable_privileges) as $override) { // For all privileges supported here (some will be passed that aren't - so we can't work back from GET params)
                                $val = post_param_integer(strval($i) . 'group_privileges_' . $override . '_' . strval($group), -2);
                                if ($val != -2) {
                                    $GLOBALS['SITE_DB']->query_delete('group_privileges', array('privilege' => $override, 'group_id' => $group, 'the_page' => '', 'module_the_name' => '', 'category_name' => ''));
                                    if ($val != -1) {
                                        $GLOBALS['SITE_DB']->query_insert('group_privileges', array('privilege' => $override, 'group_id' => $group, 'module_the_name' => '', 'category_name' => '', 'the_page' => '', 'the_value' => $val));
                                    }

                                    $changed_privileges = true;
                                }
                            }
                        }
                    }
                }

                break;

            case 'page':
                $zone = $matches[1];
                $page = $matches[2];

                $node = retrieve_sitemap_node($page_link, null, null, null, null, SITEMAP_GEN_NO_EMPTY_PAGE_LINKS | SITEMAP_GEN_REQUIRE_PERMISSION_SUPPORT/*needed to not disable if no entry points*/);

                if ($node === null) {
                    warn_exit('Could not lookup node for ' . $page_link);
                }

                $privilege_page = isset($node['privilege_page']) ? $node['privilege_page'] : $page;
                $overridable_privileges = ($node['content_type'] == 'comcode_page') ? array() : _get_overridable_privileges_for_privilege_page($privilege_page);

                // Insertion
                foreach ($groups as $group => $group_name) { // For all usergroups
                    if (!in_array($group, $admin_groups)) {
                        // View access
                        $view = post_param_integer(strval($i) . 'g_view_' . strval($group), -1);
                        if ($view != -1) { // -1 means unchanged
                            $GLOBALS['SITE_DB']->query_delete('group_page_access', array('zone_name' => $zone, 'page_name' => $page, 'group_id' => $group));
                            if ($view == 0) { // Pages have access by row non-presence, for good reason
                                $GLOBALS['SITE_DB']->query_insert('group_page_access', array('zone_name' => $zone, 'page_name' => $page, 'group_id' => $group));
                            }

                            $changed_view_access = true;
                        }

                        // Privileges
                        foreach (array_keys($overridable_privileges) as $override) { // For all privileges supported here (some will be passed that aren't - so we can't work back from GET params)
                            $val = post_param_integer(strval($i) . 'group_privileges_' . $override . '_' . strval($group), -2);
                            if ($val != -2) {
                                $GLOBALS['SITE_DB']->query_delete('group_privileges', array('privilege' => $override, 'group_id' => $group, 'the_page' => $privilege_page));
                                if ($val != -1) {
                                    $GLOBALS['SITE_DB']->query_insert('group_privileges', array('privilege' => $override, 'group_id' => $group, 'module_the_name' => '', 'category_name' => '', 'the_page' => $privilege_page, 'the_value' => $val));
                                }

                                $changed_privileges = true;
                            }
                        }
                    }
                }

                break;

            case 'cat':
                $zone = $matches[1];
                $page = $matches[2];

                $node = retrieve_sitemap_node($page_link, null, null, null, null, SITEMAP_GEN_NO_EMPTY_PAGE_LINKS);
                $privilege_page = isset($node['privilege_page']) ? $node['privilege_page'] : $page;

                $overridable_privileges = _get_overridable_privileges_for_privilege_page($privilege_page);

                foreach ($node['permissions'] as $p) {
                    if (isset($p['permission_module'])) {
                        break;
                    }
                }
                if (!isset($p['permission_module'])) {
                    fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
                }
                $module = $p['permission_module'];
                $category = $p['category_name'];

                // Insertion
                foreach ($groups as $group => $group_name) { // For all usergroups
                    if (!in_array($group, $admin_groups)) {
                        // View access
                        $view = post_param_integer(strval($i) . 'g_view_' . strval($group), -1);
                        if ($view != -1) { // -1 means unchanged
                            $GLOBALS['SITE_DB']->query_delete('group_category_access', array('module_the_name' => $module, 'category_name' => $category, 'group_id' => $group));
                            if ($view == 1) {
                                $GLOBALS['SITE_DB']->query_insert('group_category_access', array('module_the_name' => $module, 'category_name' => $category, 'group_id' => $group));
                            }

                            $changed_view_access = true;
                        }

                        // Privileges
                        foreach ($overridable_privileges as $override => $cat_support) { // For all privileges supported here (some will be passed that aren't - so we can't work back from GET params)
                            if (is_array($cat_support)) {
                                $cat_support = $cat_support[0];
                            }
                            if ($cat_support == 0) {
                                continue;
                            }

                            $val = post_param_integer(strval($i) . 'group_privileges_' . $override . '_' . strval($group), -2);
                            if ($val != -2) {
                                $GLOBALS['SITE_DB']->query_delete('group_privileges', array('privilege' => $override, 'group_id' => $group, 'module_the_name' => $module, 'category_name' => $category, 'the_page' => ''));
                                if ($val != -1) {
                                    $new_settings = array('privilege' => $override, 'group_id' => $group, 'module_the_name' => $module, 'category_name' => $category, 'the_page' => '', 'the_value' => $val);
                                    $GLOBALS['SITE_DB']->query_insert('group_privileges', $new_settings);
                                }

                                $changed_privileges = true;
                            }
                        }
                    }
                }

                break;
        }
    }

    if ($changed_view_access) {
        log_it('PAGE_ACCESS');
    }
    if ($changed_privileges) {
        log_it('PRIVILEGES');
    }

    // Decache
    decache('menu');
    require_code('caches3');
    erase_block_cache();
    erase_persistent_cache();
}

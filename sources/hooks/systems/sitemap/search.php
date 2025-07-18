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
 * @package    search
 */

/**
 * Hook class.
 */
class Hook_sitemap_search extends Hook_sitemap_base
{
    /**
     * Find if a page-link will be covered by this node.
     *
     * @param  ID_TEXT $page_link The page-link.
     * @return integer A SITEMAP_NODE_* constant.
     */
    public function handles_page_link($page_link)
    {
        $matches = array();
        if (preg_match('#^([^:]*):search(:browse)?(:|$)#', $page_link, $matches) != 0) {
            $zone = $matches[1];
            $page = 'search';

            require_code('site');
            $test = _request_page($page, $zone);
            if (($test !== false) && (($test[0] == 'MODULES_CUSTOM') || ($test[0] == 'MODULES'))) { // Ensure the relevant module really does exist in the given zone
                if ($matches[0] != $page_link) {
                    return SITEMAP_NODE_HANDLED;
                }
                return SITEMAP_NODE_HANDLED_VIRTUALLY;
            }
        }
        return SITEMAP_NODE_NOT_HANDLED;
    }

    /**
     * Find details of a virtual position in the sitemap. Virtual positions have no structure of their own, but can find child structures to be absorbed down the tree. We do this for modularity reasons.
     *
     * @param  ID_TEXT $page_link The page-link we are finding.
     * @param  ?string $callback Callback function to send discovered page-links to (null: return).
     * @param  ?array $valid_node_types List of node types we will return/recurse-through (null: no limit)
     * @param  ?integer $child_cutoff Maximum number of children before we cut off all children (null: no limit).
     * @param  ?integer $max_recurse_depth How deep to go from the sitemap root (null: no limit).
     * @param  integer $recurse_level Our recursion depth (used to limit recursion, or to calculate importance of page-link, used for instance by Google sitemap [deeper is typically less important]).
     * @param  integer $options A bitmask of SITEMAP_GEN_* options.
     * @param  ID_TEXT $zone The zone we will consider ourselves to be operating in (needed due to transparent redirects feature)
     * @param  integer $meta_gather A bitmask of SITEMAP_GATHER_* constants, of extra data to include.
     * @param  boolean $return_anyway Whether to return the structure even if there was a callback. Do not pass this setting through via recursion due to memory concerns, it is used only to gather information to detect and prevent parent/child duplication of default entry points.
     * @return ?array List of node structures (null: working via callback).
     */
    public function get_virtual_nodes($page_link, $callback = null, $valid_node_types = null, $child_cutoff = null, $max_recurse_depth = null, $recurse_level = 0, $options = 0, $zone = '_SEARCH', $meta_gather = 0, $return_anyway = false)
    {
        $nodes = ($callback === null || $return_anyway) ? array() : mixed();

        if (($valid_node_types !== null) && (!in_array('_search', $valid_node_types))) {
            return $nodes;
        }

        if (($options & SITEMAP_GEN_REQUIRE_PERMISSION_SUPPORT) != 0) {
            return $nodes;
        }

        $page = $this->_make_zone_concrete($zone, $page_link);

        $_hooks = find_all_hooks('modules', 'search');

        if ($child_cutoff !== null) {
            if (count($_hooks) > $child_cutoff) {
                return $nodes;
            }
        }

        $hooks = array();
        require_code('database_search');
        require_code('search');
        foreach (array_keys($_hooks) as $hook) {
            require_code('hooks/modules/search/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_search_' . filter_naughty_harsh($hook), true);
            if (is_null($ob)) {
                continue;
            }
            $info = $ob->info(($options & SITEMAP_GEN_CHECK_PERMS) != 0, /*TODO: Fix in v11 (($options & SITEMAP_GEN_AS_GUEST) != 0) ? $GLOBALS['FORUM_DRIVER']->get_guest_id() : */get_member());
            if (($info === null) || ($info === false)) {
                continue;
            }
            if (($hook == 'catalogue_entries') || (array_key_exists('special_on', $info)) || (array_key_exists('special_off', $info)) || (method_exists($ob, 'get_tree')) || (method_exists($ob, 'ajax_tree'))) {
                $hooks[$hook] = $info;
            }
        }

        sort_maps_by($hooks, 'lang');

        foreach ($hooks as $hook => $info) {
            $child_page_link = $zone . ':' . $page . ':browse:' . $hook;
            $node = $this->get_node($child_page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level, $options, $zone, $meta_gather);
            if (($callback === null || $return_anyway) && ($node !== null)) {
                $nodes[] = $node;
            }
        }

        return $nodes;
    }

    /**
     * Find details of a position in the Sitemap.
     *
     * @param  ID_TEXT $page_link The page-link we are finding.
     * @param  ?string $callback Callback function to send discovered page-links to (null: return).
     * @param  ?array $valid_node_types List of node types we will return/recurse-through (null: no limit)
     * @param  ?integer $child_cutoff Maximum number of children before we cut off all children (null: no limit).
     * @param  ?integer $max_recurse_depth How deep to go from the Sitemap root (null: no limit).
     * @param  integer $recurse_level Our recursion depth (used to limit recursion, or to calculate importance of page-link, used for instance by XML Sitemap [deeper is typically less important]).
     * @param  integer $options A bitmask of SITEMAP_GEN_* options.
     * @param  ID_TEXT $zone The zone we will consider ourselves to be operating in (needed due to transparent redirects feature)
     * @param  integer $meta_gather A bitmask of SITEMAP_GATHER_* constants, of extra data to include.
     * @param  ?array $row Database row (null: lookup).
     * @param  boolean $return_anyway Whether to return the structure even if there was a callback. Do not pass this setting through via recursion due to memory concerns, it is used only to gather information to detect and prevent parent/child duplication of default entry points.
     * @return ?array Node structure (null: working via callback / error).
     */
    public function get_node($page_link, $callback = null, $valid_node_types = null, $child_cutoff = null, $max_recurse_depth = null, $recurse_level = 0, $options = 0, $zone = '_SEARCH', $meta_gather = 0, $row = null, $return_anyway = false)
    {
        $matches = array();
        preg_match('#^([^:]*):([^:]*):([^:]*):([^:]*)#', $page_link, $matches);
        $page = $matches[2];
        $hook = $matches[4];

        if (($hook == 'catalogue_entry') && ($matches[0] != $page_link)) {
            preg_match('#^([^:]*):([^:]*):([^:]*):([^:]*):catalogue_name=([^:]*)#', $page_link, $matches);
            $catalogue_name = $matches[5];

            if ($row === null) {
                $rows = $GLOBALS['SITE_DB']->query_select('catalogues', array('*'), array('c_name' => $catalogue_name), '', 1);
                $row = $rows[0];
            }

            $struct = array(
                'title' => get_translated_text($row['c_title']),
                'content_type' => '_search',
                'content_id' => null,
                'modifiers' => array(),
                'only_on_page' => '',
                'page_link' => $page_link,
                'url' => null,
                'extra_meta' => array(
                    'description' => null,
                    'image' => null,
                    'image_2x' => null,
                    'add_date' => null,
                    'edit_date' => null,
                    'submitter' => null,
                    'views' => null,
                    'rating' => null,
                    'meta_keywords' => null,
                    'meta_description' => null,
                    'categories' => null,
                    'validated' => null,
                    'db_row' => null,
                ),
                'permissions' => array(
                    array(
                        'type' => 'category',
                        'permission_module' => 'catalogues_catalogue',
                        'category_name' => $catalogue_name,
                        'page_name' => $page,
                        'is_owned_at_this_level' => false,
                    ),
                ),
                'has_possible_children' => false,
                'children' => array(),

                // These are likely to be changed in individual hooks
                'sitemap_priority' => SITEMAP_IMPORTANCE_MEDIUM,
                'sitemap_refreshfreq' => 'yearly',
            );

            if (!$this->_check_node_permissions($struct)) {
                return null;
            }

            if ($callback !== null) {
                call_user_func($callback, $struct);
            }

            return ($callback === null || $return_anyway) ? $struct : null;
        }

        require_code('database_search');
        require_code('search');
        require_code('hooks/modules/search/' . filter_naughty_harsh($hook));
        $ob = object_factory('Hook_search_' . filter_naughty_harsh($hook), true);
        if (is_null($ob)) {
            return null;
        }
        $info = $ob->info(($options & SITEMAP_GEN_CHECK_PERMS) != 0, /*TODO: Fix in v11 (($options & SITEMAP_GEN_AS_GUEST) != 0) ? $GLOBALS['FORUM_DRIVER']->get_guest_id() : */get_member());
        if (($info === null) || ($info === false)) {
            return null;
        }

        $struct = array(
            'title' => $info['lang'],
            'content_type' => null,
            'content_id' => null,
            'modifiers' => array(),
            'only_on_page' => '',
            'page_link' => $page_link,
            'url' => null,
            'extra_meta' => array(
                'description' => null,
                'image' => null,
                'image_2x' => null,
                'add_date' => null,
                'edit_date' => null,
                'submitter' => null,
                'views' => null,
                'rating' => null,
                'meta_keywords' => null,
                'meta_description' => null,
                'categories' => null,
                'validated' => null,
                'db_row' => null,
            ),
            'permissions' => $info['permissions'],
            'has_possible_children' => ($hook == 'catalogue_entry'),

            // These are likely to be changed in individual hooks
            'sitemap_priority' => SITEMAP_IMPORTANCE_MEDIUM,
            'sitemap_refreshfreq' => 'yearly',

            'privilege_page' => null,
        );

        if (!$this->_check_node_permissions($struct)) {
            return null;
        }

        if ($callback !== null) {
            call_user_func($callback, $struct);
        }

        // Categories done after node callback, to ensure sensible ordering
        if ($hook == 'catalogue_entry') {
            $children = array();
            if (($max_recurse_depth === null) || ($recurse_level < $max_recurse_depth)) {
                $skip_children = false;
                $count = null;
                if ($child_cutoff !== null) {
                    $count = $GLOBALS['SITE_DB']->query_select_value('catalogues', 'COUNT(*)');
                    if ($count > $child_cutoff) {
                        $skip_children = true;
                    }
                }

                if ((!$skip_children) && ($count !== 0)) {
                    $max_rows_per_loop = ($child_cutoff === null) ? SITEMAP_MAX_ROWS_PER_LOOP : min($child_cutoff + 1, SITEMAP_MAX_ROWS_PER_LOOP);

                    $start = 0;
                    do {
                        $rows = $GLOBALS['SITE_DB']->query_select('catalogues', array('*'), null, '', $max_rows_per_loop, $start);
                        foreach ($rows as $row) {
                            if (substr($row['c_name'], 0, 1) == '_') {
                                continue;
                            }

                            $child_page_link = $page_link . ':' . $row['c_name'];
                            $child_node = $this->get_node($child_page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level, $options, $zone, $meta_gather, $row);
                            if ($child_node !== null) {
                                $children[] = $child_node;
                            }
                        }
                        $start += $max_rows_per_loop;
                    } while (count($rows) == $max_rows_per_loop);
                }
            }
            $struct['children'] = $children;
        }

        return ($callback === null || $return_anyway) ? $struct : null;
    }
}

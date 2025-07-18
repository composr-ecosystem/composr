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

/**
 * Hook class.
 */
class Hook_sitemap_root extends Hook_sitemap_base
{
    /**
     * Find if a page-link will be covered by this node.
     *
     * @param  ID_TEXT $page_link The page-link.
     * @return integer A SITEMAP_NODE_* constant.
     */
    public function handles_page_link($page_link)
    {
        if (get_option('collapse_user_zones') == '0') {
            if ($page_link == '' || $page_link == ':') {
                return SITEMAP_NODE_HANDLED; // Imaginery node
            }
        } else {
            if ($page_link == '') {
                return SITEMAP_NODE_HANDLED; // Welcome zone
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

        $node = $this->get_node(':', $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level, $options, $zone, $meta_gather);
        if (($callback === null || $return_anyway) && ($node !== null)) {
            $nodes[] = $node;
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
        $struct = array(
            'title' => do_lang_tempcode('ROOT'),
            'content_type' => 'root',
            'content_id' => null,
            'modifiers' => array(),
            'only_on_page' => '',
            'page_link' => '',
            'url' => null,
            'extra_meta' => array(
                'description' => null,
                'image' => null,
                'image_2x' => null,
                'add_date' => (($meta_gather & SITEMAP_GATHER_TIMES) != 0) ? filemtime(get_file_base() . '/sources/global.php') : null,
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
            'permissions' => array(),
            'has_possible_children' => true,
            'children' => array(),

            // These are likely to be changed in individual hooks
            'sitemap_priority' => SITEMAP_IMPORTANCE_ULTRA,
            'sitemap_refreshfreq' => 'daily',
        );

        if (get_option('collapse_user_zones') == '0') {
            $struct['title'] = do_lang_tempcode('_WELCOME');
            $struct['page_link'] = ':';
            $struct['type'] = 'zone';
            if (($meta_gather & SITEMAP_GATHER_IMAGE) != 0) {
                $struct['extra_meta']['image'] = find_theme_image('icons/24x24/menu/welcome');
                $struct['extra_meta']['image_2x'] = find_theme_image('icons/48x48/menu/welcome');
            }

            if (($options & SITEMAP_GEN_LABEL_CONTENT_TYPES) != 0) {
                $struct['title'] = make_string_tempcode(do_lang('zones:ZONE') . ': ' . $struct['title']->evaluate());
            }

            if (($options & SITEMAP_GEN_USE_PAGE_GROUPINGS) == 0) {
                require_code('hooks/systems/sitemap/zone');
                $ob = object_factory('Hook_sitemap_zone');
                $temp = $ob->get_node(':', $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level, $options, $zone, $meta_gather, null, $return_anyway);
                if ($temp !== null) {
                    $struct['children'] = isset($temp['children']) ? $temp['children'] : array();
                    $struct['extra_meta'] = $temp['extra_meta'];
                    $struct['permissions'] = $temp['permissions'];
                    $struct['privilege_page'] = $temp['privilege_page'];
                    $struct['edit_url'] = $temp['edit_url'];
                }
            }

            if ($callback !== null) {
                call_user_func($callback, $struct);
            }
        }

        // Categories done after node callback, to ensure sensible ordering
        if (($max_recurse_depth === null) || ($recurse_level < $max_recurse_depth)) {
            $zone_sitemap_ob = $this->_get_sitemap_object('zone');

            $children = array();

            $max_rows_per_loop = ($child_cutoff === null) ? SITEMAP_MAX_ROWS_PER_LOOP : min($child_cutoff + 1, SITEMAP_MAX_ROWS_PER_LOOP);

            // Ones going first
            $first_zones = find_all_zones(false, true, false, 0, $max_rows_per_loop);
            foreach ($first_zones as $_zone) {
                list($zone) = $_zone;
                if ($zone == ((get_option('collapse_user_zones') == '0') ? 'site' : '')) {
                    $child_page_link = $zone . ':';
                    $child_node = $zone_sitemap_ob->get_node($child_page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level + 1, $options, $zone, $meta_gather, $_zone);
                    if ($child_node !== null) {
                        $children[] = $child_node;
                    }
                }
            }

            $last_ones = array();

            // Middle ones
            $start = 0;
            do {
                if ($start == 0) {
                    $zones = $first_zones;
                } else {
                    $zones = find_all_zones(false, true, false, $start, $max_rows_per_loop);
                }
                foreach ($zones as $_zone) {
                    list($zone) = $_zone;

                    // We force a certain order for some
                    if ($zone == '') {
                        continue;
                    }
                    if ($zone == 'site') {
                        continue;
                    }
                    if ($zone == 'cms') {
                        array_unshift($last_ones, $_zone);
                        continue;
                    }
                    if ($zone == 'adminzone') {
                        array_push($last_ones, $_zone);
                        continue;
                    }

                    $child_page_link = $zone . ':';
                    $child_node = $zone_sitemap_ob->get_node($child_page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level + 1, $options, $zone, $meta_gather, $_zone);
                    if ($child_node !== null) {
                        $children[] = $child_node;
                    }
                }
                $start += $max_rows_per_loop;
            } while (count($zones) >= $max_rows_per_loop - 2/*2 rows may be abridged from results according to implementation filters*/);

            // Ones going last
            foreach ($last_ones as $_zone) {
                list($zone) = $_zone;
                $child_page_link = $zone . ':';
                $child_node = $zone_sitemap_ob->get_node($child_page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level + 1, $options, $zone, $meta_gather, $_zone);
                if ($child_node !== null) {
                    $children[] = $child_node;
                }
            }

            $struct['children'] = array_merge($struct['children'], $children);
        }

        return ($callback === null || $return_anyway) ? $struct : null;
    }
}

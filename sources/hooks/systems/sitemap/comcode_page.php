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
 * @package    core_comcode_pages
 */

require_code('hooks/systems/sitemap/page');

/**
 * Hook class.
 */
class Hook_sitemap_comcode_page extends Hook_sitemap_page
{
    protected $content_type = 'comcode_page';
    protected $screen_type = '';

    // If we have a different content type of entries, under this content type
    protected $entry_content_type = null;
    protected $entry_sitetree_hook = null;

    /**
     * Find if a page-link will be covered by this node.
     *
     * @param  ID_TEXT $page_link The page-link.
     * @return integer A SITEMAP_NODE_* constant.
     */
    public function handles_page_link($page_link)
    {
        $matches = array();
        if (preg_match('#^([^:]*):([^:]+)$#', $page_link, $matches) != 0) {
            $zone = $matches[1];
            $page = $matches[2];

            $details = $this->_request_page_details($page, $zone);
            if ($details !== false) {
                if (strpos($details[0], 'COMCODE') !== false) {
                    return SITEMAP_NODE_HANDLED;
                }
            }
        }
        return SITEMAP_NODE_NOT_HANDLED;
    }

    /**
     * Get the permission page that nodes matching $page_link in this hook are tied to.
     * The permission page is where privileges may be overridden against.
     *
     * @param  string $page_link The page-link
     * @return ?ID_TEXT The permission page (null: none)
     */
    /*public function get_privilege_page($page_link)    No, this is not done on a per-Comcode-page basis
    {
        return 'cms_comcode_pages';
    }*/

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
        if (!$this->check_for_looping($page_link)) {
            return null;
        }

        // Conventionally $row will be a page-grouping tuple, but it may also be a DB row, so flip the variables if needed
        if (($row !== null) && ((count($row) == 0) || (array_key_exists('cc_page_title', $row)))) {
            $db_row = $row;
            $row = null;
        } else {
            $db_row = null;
        }

        $matches = array();
        preg_match('#^([^:]*):([^:]*)#', $page_link, $matches);
        $page = $matches[2];

        $this->_make_zone_concrete($zone, $page_link);

        $details = $this->_request_page_details($page, $zone, 'comcode_custom'/*will also search 'comcode'*/);
        if (($details === false) && (get_option('collapse_user_zones') == '0')) {
            $zone = ($zone == 'site') ? '' : 'site'; // Try different zone
            $details = $this->_request_page_details($page, $zone);
        }

        if ($details === false) {
            return null;
        }

        $zone_default_page = get_zone_default_page($zone);

        $path = end($details);
        $full_path = get_custom_file_base() . '/' . $path;
        if (!is_file($full_path)) {
            $full_path = get_file_base() . '/' . $path;
        }

        $row = $this->_load_row_from_page_groupings($row, $meta_gather, $zone, $page);

        if ($page == 'start') { // TODO: Change in v11
            $test_icon = find_theme_image('icons/24x24/menu/' . $page, true);
            $test_icon_2x = find_theme_image('icons/48x48/menu/' . $page, true);
        } else {
            $test_icon = find_theme_image('icons/24x24/menu/pages/' . $page, true);
            $test_icon_2x = find_theme_image('icons/48x48/menu/pages/' . $page, true);
        }
        if ($test_icon == '') {
            $test_icon = find_theme_image('icons/24x24/menu/site_meta/' . $page, true);
            $test_icon_2x = find_theme_image('icons/48x48/menu/site_meta/' . $page, true);
            if ($test_icon == '') {
                $test_icon = mixed();
                $test_icon_2x = mixed();
            }
        }

        $struct = array(
            'title' => null,
            'content_type' => 'comcode_page',
            'content_id' => $zone . ':' . $page,
            'modifiers' => array(),
            'only_on_page' => '',
            'page_link' => $page_link,
            'url' => null,
            'extra_meta' => array(
                'description' => null,
                'image' => $test_icon,
                'image_2x' => $test_icon_2x,
                'add_date' => (($meta_gather & SITEMAP_GATHER_TIMES) != 0) ? filectime($full_path) : null,
                'edit_date' => (($meta_gather & SITEMAP_GATHER_TIMES) != 0) ? filemtime($full_path) : null,
                'submitter' => null,
                'views' => null,
                'rating' => null,
                'meta_keywords' => null,
                'meta_description' => null,
                'categories' => null,
                'validated' => null,
                'db_row' => (($meta_gather & SITEMAP_GATHER_DB_ROW) != 0) ? $row : null,
            ),
            'permissions' => array(
                array(
                    'type' => 'zone',
                    'zone_name' => $zone,
                    'is_owned_at_this_level' => false,
                ),
                array(
                    'type' => 'page',
                    'zone_name' => $zone,
                    'page_name' => $page,
                    'is_owned_at_this_level' => true,
                ),
            ),
            'children' => null,
            'has_possible_children' => true,

            // These are likely to be changed in individual hooks
            'sitemap_priority' => ($zone_default_page == $page) ? SITEMAP_IMPORTANCE_ULTRA : SITEMAP_IMPORTANCE_HIGH,
            'sitemap_refreshfreq' => ($zone_default_page == $page) ? 'daily' : 'weekly',

            'privilege_page' => $this->get_privilege_page($page_link),

            'edit_url' => build_url(array('page' => 'cms_comcode_pages', 'type' => '_edit', 'id' => $zone . ':' . $page), get_module_zone('cms_comcode_pages')),
        );

        $this->_ameliorate_with_row($options, $struct, $row, $meta_gather);

        // In the DB?
        $got_title = false;
        if ($db_row === null) {
            $db_rows = $GLOBALS['SITE_DB']->query_select('cached_comcode_pages a LEFT JOIN ' . get_table_prefix() . 'comcode_pages b ON a.the_zone=b.the_zone AND a.the_page=b.the_page', array('cc_page_title', 'p_add_date', 'p_edit_date', 'p_submitter'), array('a.the_zone' => $zone, 'a.the_page' => $page), '', 1);
            $db_row = array_key_exists(0, $db_rows) ? $db_rows[0] : null;
        }
        if (($db_row !== null) && (count($db_row) != 0)) {
            if (isset($db_row['cc_page_title'])) {
                $_title = get_translated_text($db_row['cc_page_title']);
                if ($_title != '') {
                    $struct['title'] = make_string_tempcode(escape_html($_title));
                    $got_title = true;
                }
            }
            if (isset($db_row['p_add_date'])) {
                $struct['extra_meta']['add_date'] = $db_row['p_add_date'];
            }
            if (isset($db_row['p_edit_date'])) {
                $struct['extra_meta']['edit_date'] = $db_row['p_edit_date'];
            }
            if (isset($db_row['p_submitter'])) {
                $struct['extra_meta']['submitter'] = $db_row['p_submitter'];
            }
            if (($meta_gather & SITEMAP_GATHER_DB_ROW) != 0) {
                $struct['extra_meta']['db_row'] = $db_row + (($row === null) ? array() : $struct['extra_meta']['db_row']);
            }
        }
        if (!$got_title) {
            require_code('zones2');
            $struct['title'] = get_comcode_page_title_from_disk($full_path, false, true);
        }

        if ($struct['title'] === null) {
            $struct['title'] = make_string_tempcode(escape_html(titleify($page)));
        }

        if (($options & SITEMAP_GEN_LABEL_CONTENT_TYPES) != 0) {
            $struct['title'] = make_string_tempcode(do_lang('zones:COMCODE_PAGE') . ': ' . $page);
        }

        if (!$this->_check_node_permissions($struct)) {
            return null;
        }

        if ($callback !== null) {
            call_user_func($callback, $struct);
        }

        $consider_validation = (($options & SITEMAP_GEN_CONSIDER_VALIDATION) != 0);

        // Categories done after node callback, to ensure sensible ordering
        if (($max_recurse_depth === null) || ($recurse_level < $max_recurse_depth)) {
            $children = array();
            if (($valid_node_types === null) || (in_array('comcode_page', $valid_node_types))) {
                $where = array('p_parent_page' => $page, 'the_zone' => $zone);
                if ($consider_validation) {
                    $where['p_validated'] = 1;
                }

                // Optimisation: most of the time in practice parent relationships aren't used, but their use is a big performance hit here; so pre-compute if they are per-zone in advance
                static $children_in_zone = null;
                if ($children_in_zone === null) {
                    $_children_in_zone = $GLOBALS['SITE_DB']->query_select('comcode_pages', array('the_zone', 'COUNT(*) AS cnt'), array(), ' AND ' . db_string_not_equal_to('p_parent_page', '') . ' GROUP BY the_zone');
                    $children_in_zone = collapse_2d_complexity('the_zone', 'cnt', $_children_in_zone);
                }

                if ((!array_key_exists($zone, $children_in_zone)) || ($children_in_zone[$zone] == 0)) {
                    $skip_children = true;
                } else {
                    $skip_children = false;
                    $count = null;
                    if ($child_cutoff !== null) {
                        $count = $GLOBALS['SITE_DB']->query_select_value('comcode_pages', 'COUNT(*)', $where);
                        if ($count > $child_cutoff) {
                            $skip_children = true;
                        }
                    }
                }

                if ((!$skip_children) && ($count !== 0)) {
                    $max_rows_per_loop = ($child_cutoff === null) ? SITEMAP_MAX_ROWS_PER_LOOP : min($child_cutoff + 1, SITEMAP_MAX_ROWS_PER_LOOP);

                    static $child_rows = array();
                    $start = 0;
                    do {
                        $sz = serialize($where + array('_start' => $start));

                        if (!isset($child_rows[$sz])) {
                            $child_rows[$sz] = $GLOBALS['SITE_DB']->query_select('comcode_pages', array('the_page'), $where, 'ORDER BY p_order,the_page', $max_rows_per_loop, $start);
                        }
                        foreach ($child_rows[$sz] as $child_row) {
                            $child_page_link = $zone . ':' . $child_row['the_page'];
                            $child_node = $this->get_node($child_page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $recurse_level + 1, $options, $zone, $meta_gather, $child_row);
                            if ($child_node !== null) {
                                $children[] = $child_node;
                            }
                        }
                        $start += $max_rows_per_loop;
                    } while (count($child_rows[$sz]) == $max_rows_per_loop);
                }
            }
            $struct['children'] = $children;
        }

        return ($callback === null || $return_anyway) ? $struct : null;
    }
}

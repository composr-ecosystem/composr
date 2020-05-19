<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    confluence
 */

/**
 * Hook class.
 */
class Hook_search_confluence extends FieldsSearchHook
{
    /**
     * Find details for this search hook.
     *
     * @param  boolean $check_permissions Whether to check permissions
     * @param  ?MEMBER $member_id The member ID to check with (null: current member)
     * @return ~?array Map of search hook details (null: hook is disabled) (false: access denied)
     */
    public function info($check_permissions = true, $member_id = null)
    {
        if (!addon_installed('confluence')) {
            return null;
        }

        if ((get_option('confluence_subdomain') == '') || (get_option('confluence_space') == '')) {
            return null;
        }

        if ($member_id === null) {
            $member_id = get_member();
        }

        if ($check_permissions) {
            if (!has_actual_page_access($member_id, 'docs')) {
                return false;
            }
        }

        require_lang('confluence');

        $info = [];
        $info['lang'] = do_lang_tempcode('CONFLUENCE_DOCUMENTATION');
        $info['default'] = true;

        $info['permissions'] = [
            [
                'type' => 'zone',
                'zone_name' => get_page_zone('docs'),
            ],
            [
                'type' => 'page',
                'zone_name' => get_page_zone('docs'),
                'page_name' => 'docs',
            ],
        ];

        return $info;
    }

    /**
     * Get a list of entries for the content covered by this search hook. In hierarchical list selection format.
     *
     * @param  string $_selected The default selected item
     * @return Tempcode Tree structure
     */
    public function get_tree($_selected)
    {
        $selected = ($_selected == '' || $_selected == '!') ? [] : [intval($_selected)];

        require_code('confluence');

        $tree = create_selection_list_confluence($selected);
        return $tree;
    }

    /**
     * Run function for search results.
     *
     * @param  string $search_query Search query
     * @param  string $content_where WHERE clause that selects the content according to the search query; passed in addition to $search_query to avoid unnecessary reparsing.  ? refers to the yet-unknown field name (blank: full-text search)
     * @param  string $where_clause Initial WHERE clause that already takes $search_under into account (should be nothing else unless it is guaranteed hook will use the global get_search_rows function)
     * @param  string $search_under Comma-separated list of categories to search under
     * @param  boolean $only_search_meta Whether to only do a META (tags) search
     * @param  boolean $only_titles Whether only to search titles (as opposed to both titles and content)
     * @param  integer $max Start position in total results
     * @param  integer $start Maximum results to return in total
     * @param  string $sort The sort type (gets remapped to a field in this function)
     * @param  ID_TEXT $direction Order direction
     * @param  SHORT_TEXT $author Username/Author to match for
     * @param  ?MEMBER $author_id Member-ID to match for (null: unknown)
     * @param  mixed $cutoff Cutoff date (TIME or a pair representing the range)
     * @return array List of maps (template, orderer)
     */
    public function run($search_query, $content_where, $where_clause, $search_under, $only_search_meta, $only_titles, $max, $start, $sort, $direction, $author, $author_id, $cutoff)
    {
        require_code('confluence');
        require_css('confluence');

        global $CONFLUENCE_SPACE;
        $cql_query = 'space.key = ' . $CONFLUENCE_SPACE;

        if ($author != '') {
            if ($cql_query != '') {
                $cql_query .= ' and ';
            }

            $cql_query .= 'contributor = ' . $author;
        }

        if ($cutoff !== null) {
            if ($cql_query != '') {
                $cql_query .= ' and ';
            }

            if (is_integer($cutoff)) {
                $cql_query .= 'lastmodified >= "' . date('Y/m/d H:i', $cutoff) . '"';
            } else {
                $cql_query .= 'lastmodified >= "' . date('Y/m/d H:i', $cutoff[0]) . '" and lastmodified <= "' . date('Y/m/d H:i', $cutoff[1]) . '"';
            }
        }

        if ($search_query != '') {
            if ($cql_query != '') {
                $cql_query .= ' and ';
            }

            if ($only_titles) {
                $cql_query .= 'title ~ "' . $this->cleanup_search_verb($search_query) . '"';
            } else {
                $cql_query .= '(title ~ "' . $this->cleanup_search_verb($search_query) . '" or text ~ "' . $this->cleanup_search_verb($search_query) . '")';
            }
        }

        if ($search_under != '!') {
            if ($cql_query != '') {
                $cql_query .= ' and ';
            }

            $_search_under = explode(',', $search_under);
            $cql_query .= '(ancestor in (';
            foreach ($_search_under as $i => $__search_under) {
                if ($i != 0) {
                    $cql_query .= ', ';
                }
                $cql_query .= $__search_under;
            }
            $cql_query .= ') or id in (';
            foreach ($_search_under as $i => $__search_under) {
                if ($i != 0) {
                    $cql_query .= ', ';
                }
                $cql_query .= $__search_under;
            }
            $cql_query .= '))';
        }

        $remapped_orderer = '';
        switch ($sort) {
            case 'title':
                $remapped_ordered = 'title';
                break;

            case 'add_date':
                $remapped_ordered = 'created';
                break;
        }

        $rows = confluence_query('search?limit=100&cql=' . urlencode($cql_query)/* We can't put on a limit as we need to be able to count results . '&limit=' . strval($limit_to + $start)*/);

        $out = [];
        foreach ($rows['results'] as $i => $row) {
            $out[$i]['data'] = $row;
            unset($rows[$i]);
            if (($remapped_orderer != '') && (array_key_exists($remapped_orderer, $row))) {
                $out[$i]['orderer'] = $row[$remapped_orderer];
            } else {
                $out[$i]['orderer'] = $i;
            }
        }

        $GLOBALS['TOTAL_SEARCH_RESULTS'] += count($rows['results']);

        return $out;
    }

    protected function cleanup_search_verb($in)
    {
        $reps = [
            '"' => '',
            '\\' => '',
        ];
        return str_replace(array_keys($reps), array_values($reps), $in);
    }

    /**
     * Run function for rendering a search result.
     *
     * @param  array $myrow The data row stored when we retrieved the result
     * @return Tempcode The output
     */
    public function render($myrow)
    {
        global $SEARCH_QUERY_TERMS;
        $highlight_bits = ($SEARCH_QUERY_TERMS === null) ? [] : $SEARCH_QUERY_TERMS;

        $text_summary_h = $this->cleanup_text($myrow['excerpt']);
        $text_summary = protect_from_escaping(generate_text_summary($text_summary_h, []));

        $title = $myrow['content']['title'];

        $url = build_url(['page' => 'docs', 'type' => $myrow['content']['id']], '_SEARCH');
        $breadcrumbs = confluence_breadcrumbs(intval($myrow['content']['id']));
        return do_template('SIMPLE_PREVIEW_BOX', ['TITLE' => 'Documentation: ' . $title, 'BREADCRUMBS' => ($breadcrumbs === null) ? null : breadcrumb_segments_to_tempcode($breadcrumbs), 'SUMMARY' => $text_summary, 'URL' => $url]);
    }

    protected function cleanup_text($in)
    {
        $out = $in;
        $out = preg_replace('#\n+#', "\n", $out);
        $out = nl2br($out);
        $out = preg_replace('#@@@hl@@@(.*)@@@endhl@@@#U', '<span class="comcode_highlight">\1</span>', $out);
        return $out;
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

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
     * @param  string $content Search string
     * @param  boolean $only_search_meta Whether to only do a META (tags) search
     * @param  ID_TEXT $direction Order direction
     * @param  integer $max Start position in total results
     * @param  integer $start Maximum results to return in total
     * @param  boolean $only_titles Whether only to search titles (as opposed to both titles and content)
     * @param  string $content_where Where clause that selects the content according to the main search string (SQL query fragment) (blank: full-text search)
     * @param  SHORT_TEXT $author Username/Author to match for
     * @param  ?MEMBER $author_id Member-ID to match for (null: unknown)
     * @param  mixed $cutoff Cutoff date (TIME or a pair representing the range)
     * @param  string $sort The sort type (gets remapped to a field in this function)
     * @set title add_date
     * @param  integer $limit_to Limit to this number of results
     * @param  string $boolean_operator What kind of boolean search to do
     * @set or and
     * @param  string $where_clause Where constraints known by the main search code (SQL query fragment)
     * @param  string $search_under Comma-separated list of categories to search under
     * @param  boolean $boolean_search Whether it is a boolean search
     * @return array List of maps (template, orderer)
     */
    public function run($content, $only_search_meta, $direction, $max, $start, $only_titles, $content_where, $author, $author_id, $cutoff, $sort, $limit_to, $boolean_operator, $where_clause, $search_under, $boolean_search)
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

        if ($content != '') {
            if ($cql_query != '') {
                $cql_query .= ' and ';
            }

            if ($only_titles) {
                $cql_query .= 'title ~ "' . $this->cleanup_search_verb($content) . '"';
            } else {
                $cql_query .= '(title ~ "' . $this->cleanup_search_verb($content) . '" or text ~ "' . $this->cleanup_search_verb($content) . '")';
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

        $rows = confluence_query('search?cql=' . urlencode($cql_query)/* We can't put on a limit as we need to be able to count results . '&limit=' . strval($limit_to + $start)*/);

        $out = [];
        foreach ($rows['results'] as $i => $row) {
            $out[$i]['data'] = $row;
            unset($rows[$i]);
            if (($remapped_orderer != '') && (array_key_exists($remapped_orderer, $row))) {
                $out[$i]['orderer'] = $row[$remapped_orderer];
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
        global $SEARCH__CONTENT_BITS;
        $highlight_bits = ($SEARCH__CONTENT_BITS === null) ? [] : $SEARCH__CONTENT_BITS;

        $text_summary_h = $this->cleanup_text($myrow['excerpt']);
        $text_summary = generate_text_summary($text_summary_h, $highlight_bits);

        $title = $myrow['content']['title'];

        $url = build_url(['page' => 'docs', 'type' => $myrow['content']['id']], '_SEARCH');
        $breadcrumbs = confluence_breadcrumbs($myrow['content']['id']);
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

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    nusearch
 */

// TODO: Not needed in v11
/*EXTRA FUNCTIONS: Composr_fulltext_engine*/

/**
 * Hook class.
 */
class Hook_search_comcode_pages extends FieldsSearchHook
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
        if ($member_id === null) {
            $member_id = get_member();
        }

        if (!$GLOBALS['SITE_DB']->table_exists('cpages_fulltext_index')) {
            //$GLOBALS['SITE_DB']->drop_table_if_exists('cpages_fulltext_index');

            $GLOBALS['SITE_DB']->create_table('cpages_fulltext_index', array(
                'i_zone_name' => '*ID_TEXT',
                'i_page_name' => '*ID_TEXT',

                'i_lang' => '*LANGUAGE_NAME',
                'i_ngram' => '*INTEGER',
                'i_ac' => '*INTEGER',

                'i_occurrence_rate' => 'REAL',
            ));

            //$GLOBALS['SITE_DB']->delete_index_if_exists('cpages_fulltext_index', 'content_id');
            //$GLOBALS['SITE_DB']->delete_index_if_exists('cpages_fulltext_index', 'main');

            $GLOBALS['SITE_DB']->create_index('cpages_fulltext_index', 'content_id', array( // Used for cleanouts and potentially optimising some JOINs if query planner decides to start at the content table
                'i_zone_name',
                'i_page_name',
            ));

            $GLOBALS['SITE_DB']->create_index('cpages_fulltext_index', 'main', array(
                'i_lang',
                'i_ngram',
                'i_ac',
                'i_occurrence_rate', // For sorting
            ));
        }

        require_lang('zones');

        $info = array();
        $info['lang'] = do_lang_tempcode('PAGES');
        $info['default'] = false;
        $info['category'] = 'the_zone';
        $info['integer_category'] = false;
        $info['extra_sort_fields'] = $this->_get_extra_sort_fields('_comcode_page');

        $info['permissions'] = array();

        return $info;
    }

    /**
     * Perform indexing using the Composr fast custom index.
     *
     * @param  ?TIME $since Only index records newer than this (null: no limit)
     * @param  ?integer $total_singular_ngram_tokens Write into a count of singular ngrams (typically, words) in here (null: do not count)
     * @param  ?array $statistics_map Write into this map of singular ngram (typically, words) to number of occurrences (null: do not maintain a map)
     */
    public function index_for_search($since = null, &$total_singular_ngram_tokens = null, &$statistics_map = null)
    {
        $engine = new Composr_fulltext_engine();

        $index_table = 'cpages_fulltext_index';
        $clean_scan = ($GLOBALS['SITE_DB']->query_select_value_if_there($index_table, 'i_ngram') === null);

        $has_custom_fields = ($GLOBALS['FORUM_DB']->query_select_value_if_there('catalogue_fields', 'id', array('c_name' => '_comcode_page')) !== null);

        $fields_to_index = array(
            'page_name' => APPEARANCE_CONTEXT_title,
            'page_content' => APPEARANCE_CONTEXT_body,
            'meta_keywords' => APPEARANCE_CONTEXT_meta,
            'meta_description' => APPEARANCE_CONTEXT_body,
        );
        $key_transfer_map = array(
            'zone_name' => 'i_zone_name',
            'page_name' => 'i_page_name',
        );
        $filter_field_transfer_map = array(
        );

        $db = $GLOBALS['SITE_DB'];

        $zones = find_all_zones();
        foreach ($zones as $zone) {
            $langs = find_all_langs();
            foreach (array_keys($langs) as $lang) {
                $pages = find_all_pages($zone, 'comcode_custom/' . $lang, 'txt', false, $clean_scan ? null : $since, FIND_ALL_PAGES__ALL);
                foreach ($pages as $page => $page_type) {
                    if (is_integer($page)) {
                        $page = strval($page);
                    }

                    if (preg_match('#(^panel_|_)#', $page) == 0) {
                        list($file_base, $file_path) = find_comcode_page($lang, $page, $zone);

                        list($keywords, $description) = seo_meta_get_for('comcode_page', $zone . ':' . $page);
                        $content_fields = array(
                            'zone_name' => $zone,
                            'page_name' => $page,
                            'page_content' => cms_file_get_contents_safe($file_base . '/' . $file_path),
                            'meta_keywords' => $keywords,
                            'meta_description' => $description,
                        );

                        if ($has_custom_fields) {
                            $ce_id = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_entry_linkage', 'catalogue_entry_id', array('content_type' => 'comcode_page', 'content_id' => $zone . ':' . $page));
                            if ($ce_id !== null) {
                                $engine->get_content_fields_from_catalogue_entry($content_fields, $fields_to_index, '_comcode_page', $ce_id, $lang);
                            }
                        }

                        $engine->index_for_search($db, $index_table, $content_fields, $fields_to_index, $key_transfer_map, $filter_field_transfer_map, $total_singular_ngram_tokens, $statistics_map, $lang, $clean_scan);
                    }
                }
            }
        }
    }

    /**
     * Get a list of entries for the content covered by this search hook. In hierarchical list selection format.
     *
     * @param  string $selected The default selected item
     * @return Tempcode Tree structure
     */
    public function get_tree($selected)
    {
        require_code('zones3');
        $tree = create_selection_list_zones($selected);
        return $tree;
    }

    /**
     * Get a list of extra fields to ask for.
     *
     * @return ?array A list of maps specifying extra fields (null: no tree)
     */
    public function get_fields()
    {
        return $this->_get_fields('_comcode_page');
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
     * @set    title add_date
     * @param  integer $limit_to Limit to this number of results
     * @param  string $boolean_operator What kind of boolean search to do
     * @set    or and
     * @param  string $where_clause Where constraints known by the main search code (SQL query fragment)
     * @param  string $search_under Comma-separated list of categories to search under
     * @param  boolean $boolean_search Whether it is a boolean search
     * @return array List of maps (template, orderer)
     */
    public function run($content, $only_search_meta, $direction, $max, $start, $only_titles, $content_where, $author, $author_id, $cutoff, $sort, $limit_to, $boolean_operator, $where_clause, $search_under, $boolean_search)
    {
        $remapped_orderer = '';
        switch ($sort) {
            case 'title':
                $remapped_orderer = 'the_page';
                break;

            case 'add_date':
                $remapped_orderer = 'the_zone'; // Stucked
                break;
        }

        require_lang('zones');

        // Calculate and perform query
        $composr_fulltext_engine = can_use_composr_fulltext_engine('comcode_pages', $content, $cutoff !== null || $author != '' || ($search_under != '-1' && $search_under != '!'));
        if ($composr_fulltext_engine) {
            // This search hook implements the Composr fast custom index, which we use where possible...

            // Calculate our where clause (search)
            $where_clause = '';
            $extra_join_clause = '';
            $sq = build_search_submitter_clauses('p_submitter', $author_id, $author);
            if ($sq === null) {
                return array();
            } else {
                $where_clause .= $sq;
            }

            if ((!is_null($search_under)) && ($search_under != '!')) {
                $extra_join_clause .= ' AND ';
                $extra_join_clause .= '(' . db_string_equal_to('ixxx.i_zone_name', $search_under) . ')';
            }

            // Category filter
            if (($search_under != '!') && ($search_under != '-1')) {
                $cats = explode(',', $search_under);
                $extra_join_clause .= ' AND (';
                foreach ($cats as $i => $cat) {
                    if (trim($cat) == '') {
                        continue;
                    }

                    if ($i != 0) {
                        $extra_join_clause .= ' OR ';
                    }
                    $extra_join_clause .= db_string_equal_to('ixxx.i_zone_name', $cat);
                }
                $extra_join_clause .= ')';
            }

            if ((!has_privilege(get_member(), 'see_unvalidated')) && (addon_installed('unvalidated'))) {
                $where_clause .= ' AND ';
                $where_clause .= 'r.p_validated=1';
            }

            $table = 'comcode_pages r';
            $g_or = _get_where_clause_groups(get_member(), false);
            if ($g_or !== null) {
                $where_clause .= ' AND ';
                $where_clause .= 'EXISTS(SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_zone_access z WHERE (z.zone_name=r.the_zone AND (' . str_replace('group_id', 'z.group_id', $g_or) . ')))';
            }

            $engine = new Composr_fulltext_engine();

            if ($engine->active_search_has_special_filtering()) {
                $trans_fields = array();
                $nontrans_fields = array();
                $this->_get_search_parameterisation_advanced_for_content_type('_comcode_page', $table, $where_clause, $trans_fields, $nontrans_fields, db_function('CONCAT', array('r.the_zone', 'r.the_page')));
                // ^ Nothing done with trans_fields and nontrans_fields
            }

            $db = $GLOBALS['SITE_DB'];
            $index_table = 'cpages_fulltext_index';
            $key_transfer_map = array('the_zone' => 'i_zone_name', 'the_page' => 'i_page_name');
            $rows = $engine->get_search_rows($db, $index_table, $db->get_table_prefix() . $table, $key_transfer_map, $where_clause, $extra_join_clause, $content, $boolean_search, $only_search_meta, $only_titles, $max, $start, $remapped_orderer, $direction);
        } else {
            $sq = build_search_submitter_clauses('q.p_submitter', $author_id, $author);
            if (is_null($sq)) {
                return array();
            } else {
                $where_clause .= $sq;
            }

            if (strpos($content, 'panel_') === false) {
                $where_clause .= ' AND ';
                $where_clause .= '(r.the_page NOT LIKE \'' . db_encode_like('panel\_%') . '\') AND (r.the_page NOT LIKE \'' . db_encode_like('\_%') . '\')';
            }
            if ((!is_null($search_under)) && ($search_under != '!')) {
                $where_clause .= ' AND ';
                $where_clause .= '(' . db_string_equal_to('r.the_zone', $search_under) . ')';
            }

            if ((!has_privilege(get_member(), 'see_unvalidated')) && (addon_installed('unvalidated'))) {
                $where_clause .= ' AND ';
                $where_clause .= 'q.p_validated=1';
            }

            $g_or = _get_where_clause_groups(get_member(), false);
            if ($g_or !== null) {
                $where_clause .= ' AND ';
                $where_clause .= 'EXISTS(SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_zone_access z WHERE (z.zone_name=r.the_zone AND (' . str_replace('group_id', 'z.group_id', $g_or) . ')))';
            }

            $table = 'cached_comcode_pages r LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'comcode_pages q ON (q.the_zone=r.the_zone AND q.the_page=r.the_page)';

            $trans_fields = array('r.cc_page_title' => 'SHORT_TRANS', 'r.string_index' => 'LONG_TRANS__COMCODE');
            $nontrans_fields = array();
            $this->_get_search_parameterisation_advanced_for_content_type('_comcode_page', $table, $where_clause, $trans_fields, $nontrans_fields, db_function('CONCAT', array('r.the_zone', 'r.the_page')));

            $rows = get_search_rows('comcode_page', 'the_zone:the_page', $content, $boolean_search, $boolean_operator, $only_search_meta, $direction, $max, $start, $only_titles, $table, $trans_fields, $where_clause, $content_where, $remapped_orderer, 'r.the_zone,r.the_page', $nontrans_fields);
        }

        if (addon_installed('redirects_editor')) {
            $redirects = $GLOBALS['SITE_DB']->query_select('redirects', array('*'));
        } else {
            $redirects = array();
        }

        $out = array();
        $pages_found = array();
        $out_i = 0;
        foreach ($rows as $row) {
            foreach ($redirects as $redirect) {
                if (($redirect['r_from_page'] == $row['the_page']) && ($redirect['r_from_zone'] == $row['the_zone'])) {
                    continue 2;
                }
            }

            if ($row['the_zone'] == '!') {
                continue;
            }
            if (array_key_exists($row['the_zone'] . ':' . $row['the_page'], $pages_found)) {
                continue;
            }
            $pages_found[$row['the_zone'] . ':' . $row['the_page']] = 1;
            $out[$out_i]['data'] = $row + array('extra' => array($row['the_zone'], $row['the_page'], $limit_to));
            if (($remapped_orderer != '') && (array_key_exists($remapped_orderer, $row))) {
                $out[$out_i]['orderer'] = $row[$remapped_orderer];
            } elseif (strpos($remapped_orderer, '_rating:') !== false) {
                $out[$out_i]['orderer'] = $row[$remapped_orderer];
            }

            if (!has_page_access(get_member(), $row['the_page'], $row['the_zone'])) {
                $out[$out_i]['restricted'] = true;
            }

            $out_i++;
            $GLOBALS['TOTAL_SEARCH_RESULTS']++;
        }

        if (($author == '') && (!$composr_fulltext_engine)) {
            // Make sure we record that for all cached Comcode pages, we know of them (only those not cached would not have been under the scope of the current search)
            $all_pages = $GLOBALS['SITE_DB']->query_select('cached_comcode_pages', array('the_zone', 'the_page', 'string_index'));
            foreach ($all_pages as $row) {
                $pages_found[$row['the_zone'] . ':' . $row['the_page']] = 1;
                if (in_memory_search_match(array('content' => $content, 'conjunctive_operator' => $boolean_operator), get_translated_text($row['string_index']))) {
                    $out[$out_i]['data'] = array('the_zone' => $row['the_zone'], 'the_page' => $row['the_page']) + array('extra' => array($row['the_zone'], $row['the_page'], $limit_to));
                    if ($remapped_orderer == 'the_page') {
                        $out[$out_i]['orderer'] = $row['the_page'];
                    } elseif ($remapped_orderer == 'the_zone') {
                        $out[$out_i]['orderer'] = $row['the_zone'];
                    }

                    if (!has_page_access(get_member(), $row['the_page'], $row['the_zone'])) {
                        $out[$out_i]['restricted'] = true;
                    }

                    $out_i++;
                    $GLOBALS['TOTAL_SEARCH_RESULTS']++;
                }
            }

            // Now, look on disk for non-cached Comcode pages
            $zones = find_all_zones();
            if ((!is_null($search_under)) && ($search_under != '!')) {
                $zones = array($search_under);
            }
            foreach ($zones as $zone) {
                if (!has_zone_access(get_member(), $zone)) {
                    continue;
                }

                $pages = find_all_pages_wrap($zone, false, false, FIND_ALL_PAGES__PERFORMANT, 'comcode');
                foreach ($pages as $page => $dir) {
                    if (!is_string($page)) {
                        $page = strval($page);
                    }

                    if (!array_key_exists($zone . ':' . $page, $pages_found)) {
                        if (!has_page_access(get_member(), $page, $zone)) {
                            continue;
                        }

                        if (strpos($content, 'panel_') === false) {
                            if (substr($page, 0, 6) == 'panel_') {
                                continue;
                            }
                        }

                        // Actually underscores are solely only for hiding from the sitemap; otherwise unvalidated should be used
                        /*
                        if (substr($page, 0, 1) == '_') {
                            continue;
                        }
                        */

                        foreach ($redirects as $redirect) {
                            if (($redirect['r_from_page'] == $page) && ($redirect['r_from_zone'] == $zone)) {
                                continue 2;
                            }
                        }

                        $path = zone_black_magic_filterer((($dir == 'comcode_custom') ? get_custom_file_base() : get_file_base()) . (($zone == '') ? '' : '/') . $zone . '/pages/' . $dir . '/' . $page . '.txt');
                        if (!$this->_handle_date_check_runtime($cutoff, filemtime($path))) {
                            continue;
                        }
                        $contents = file_get_contents($path);
                        if ($only_titles) {
                            $contents = preg_replace('#^.*\[title(="1")?\](.*)\[/title\].*$#Us', '${2}', $contents);
                        }

                        if (in_memory_search_match(array('content' => $content, 'conjunctive_operator' => $boolean_operator), $contents)) {
                            $out[$out_i]['data'] = array('the_zone' => $zone, 'the_page' => $page) + array('extra' => array($zone, $page, $limit_to));
                            if ($remapped_orderer == 'the_page') {
                                $out[$out_i]['orderer'] = $page;
                            } elseif ($remapped_orderer == 'the_zone') {
                                $out[$out_i]['orderer'] = $zone;
                            }

                            if (!has_page_access(get_member(), $row['the_page'], $row['the_zone'])) {
                                $out[$out_i]['restricted'] = true;
                            }

                            $out_i++;
                            $GLOBALS['TOTAL_SEARCH_RESULTS']++;

                            // Let it cache for next time
                            if (get_option('is_on_comcode_page_cache') == '1') {
                                request_page($page, false, $zone, $dir, false, true);
                            }
                        }
                    }
                }
            }
        }

        return $out;
    }

    /**
     * Run function for rendering a search result.
     *
     * @param  array $row The data row stored when we retrieved the result
     * @return Tempcode The output
     */
    public function render($row)
    {
        list($zone, $page, $limit_to) = $row['extra'];
        return $this->decide_template($zone, $page, $limit_to);
    }

    /**
     * Decide how to show a Comcode page in the search results.
     *
     * @param  ID_TEXT $zone The zone for the page
     * @param  ID_TEXT $page The page name
     * @param  string $limit_to What search hooks the search is being limited to (blank: not limited)
     * @return Tempcode The Tempcode showing the Comcode page
     */
    public function decide_template($zone, $page, $limit_to)
    {
        global $SEARCH__CONTENT_BITS;

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(30); // This can be slow.
        }

        require_code('xhtml');

        $url = build_url(array('page' => $page), $zone);

        $_summary = seo_meta_get_for('comcode_page', $zone . ':' . $page);
        $summary = $_summary[1];

        if ($summary == '') {
            $page_request = _request_page($page, $zone);
            if (($page_request === false) || (strpos($page_request[0], 'COMCODE') === false)) {
                return new Tempcode();
            }
            $_zone = $page_request[count($page_request) - 1];
            $comcode_file = get_custom_file_base() . (($_zone == '') ? '' : '/') . $_zone;
            if (!is_file($comcode_file)) {
                $comcode_file = get_file_base() . (($_zone == '') ? '' : '/') . $_zone;
            }

            if (file_exists($comcode_file)) {
                global $LAX_COMCODE;
                $LAX_COMCODE = true;
                /* Tempcode compiler slowed things down so easier just to show full thing
                $temp_summary = comcode_to_tempcode(file_get_contents($comcode_file), null, true);
                $_temp_summary = $temp_summary->evaluate();
                if (strlen($_temp_summary) < 500) {
                    $summary = $_temp_summary;
                } else {
                    $entity = '&hellip;';
                    if (function_exists('ocp_mark_as_escaped')) {
                        ocp_mark_as_escaped($entity);
                    }
                    $pos = false;//strpos($_temp_summary,'<span class="comcode_highlight">');
                    if ($pos === false) {
                        $pos = 0;
                    }
                    $pos2 = max(0, $pos - 250);
                    $summary = (($pos2 == 0) ? '' : $entity) . xhtml_substr($_temp_summary, $pos2, 500) . $entity;
                }
                */
                $GLOBALS['OVERRIDE_SELF_ZONE'] = $zone;
                $backup_search__contents_bits = $SEARCH__CONTENT_BITS;
                $SEARCH__CONTENT_BITS = null; // We do not want highlighting, as it'll result in far too much Comcode being parsed (ok for short snippets, not many full pages!)
                $GLOBALS['TEMPCODE_SETGET']['no_comcode_page_edit_links'] = '1';
                push_output_state();
                $temp_summary = request_page($page, true, $zone, strpos($comcode_file, '/comcode_custom/') ? 'comcode_custom' : 'comcode', true);
                restore_output_state();
                $SEARCH__CONTENT_BITS = $backup_search__contents_bits;
                $GLOBALS['OVERRIDE_SELF_ZONE'] = null;
                $LAX_COMCODE = false;
                $_temp_summary = $temp_summary->evaluate();
                global $PAGES_CACHE;
                $PAGES_CACHE = array(); // Decache this, or we'll eat up a tonne of RAM

                $summary = generate_text_summary($_temp_summary, is_null($SEARCH__CONTENT_BITS) ? array() : $SEARCH__CONTENT_BITS);

                $GLOBALS['TEMPCODE_SETGET']['no_comcode_page_edit_links'] = '0';
            }
        }

        $title = $page;
        global $LAST_COMCODE_PARSED_TITLE;
        if ($LAST_COMCODE_PARSED_TITLE != '') {
            $title = $LAST_COMCODE_PARSED_TITLE;
        }

        $breadcrumbs = breadcrumb_segments_to_tempcode(comcode_breadcrumbs($page, $zone));

        return do_template('COMCODE_PAGE_BOX', array(
            '_GUID' => '4bc89179680eea1c34b904e5c19fccc0',
            'TITLE' => $title,
            'BREADCRUMBS' => $breadcrumbs,
            'PAGE' => $page,
            'ZONE' => $zone,
            'URL' => $url,
            'SUMMARY' => $summary,
            'GIVE_CONTEXT' => true,
        ));
    }
}

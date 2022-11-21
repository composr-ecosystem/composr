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
class Hook_search_catalogue_entries extends FieldsSearchHook
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
        if (!addon_installed('catalogues')) {
            return null;
        }

        if ($member_id === null) {
            $member_id = get_member();
        }

        if (!$GLOBALS['SITE_DB']->table_exists('ce_fulltext_index')) {
            //$GLOBALS['SITE_DB']->drop_table_if_exists('ce_fulltext_index');

            $GLOBALS['SITE_DB']->create_table('ce_fulltext_index', array(
                'i_catalogue_entry_id' => '*AUTO_LINK',

                'i_lang' => '*LANGUAGE_NAME',
                'i_ngram' => '*INTEGER',
                'i_ac' => '*INTEGER',

                'i_occurrence_rate' => 'REAL',

                // De-normalised stuff from main content tables for any major filters that shape the results provided
                //  (other stuff will come in via join back to the main content table)
                'i_add_time' => 'TIME',
                'i_c_name' => 'ID_TEXT',
                'i_category_id' => 'AUTO_LINK',
                'i_submitter' => 'MEMBER',
            ));

            //$GLOBALS['SITE_DB']->delete_index_if_exists('ce_fulltext_index', 'content_id');
            //$GLOBALS['SITE_DB']->delete_index_if_exists('ce_fulltext_index', 'main');

            $GLOBALS['SITE_DB']->create_index('ce_fulltext_index', 'content_id', array( // Used for cleanouts and potentially optimising some JOINs if query planner decides to start at the content table
                'i_catalogue_entry_id',
            ));

            $GLOBALS['SITE_DB']->create_index('ce_fulltext_index', 'main', array(
                'i_lang',
                'i_ngram',
                'i_ac',
                'i_add_time',
                //Disabled in v10 due to index key limit 'i_c_name',
                'i_category_id',
                'i_submitter',
                'i_occurrence_rate', // For sorting
            ));
        }

        if (!module_installed('catalogues')) {
            return null;
        }

        if ($check_permissions) {
            if (!has_actual_page_access($member_id, 'catalogues')) {
                return false;
            }
        }

        if ($GLOBALS['SITE_DB']->query_select_value('catalogue_entries', 'COUNT(*)') == 0) {
            return null;
        }

        require_lang('catalogues');
        require_code('catalogues');

        $info = array();
        $info['lang'] = do_lang_tempcode('CATALOGUE_ENTRIES');
        $info['default'] = false;
        $info['category'] = 'cc_id';
        $info['integer_category'] = true;

        $extra_sort_fields = array();
        $catalogue_name = get_param_string('catalogue_name', null);
        if (!is_null($catalogue_name)) {
            $extra_sort_fields = $this->_get_extra_sort_fields($catalogue_name);
        }
        $info['extra_sort_fields'] = $extra_sort_fields;

        $info['permissions'] = array(
            array(
                'type' => 'zone',
                'zone_name' => get_module_zone('catalogues'),
            ),
            array(
                'type' => 'page',
                'zone_name' => get_module_zone('catalogues'),
                'page_name' => 'catalogues',
            ),
        );

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

        $index_table = 'ce_fulltext_index';
        $clean_scan = ($GLOBALS['SITE_DB']->query_select_value_if_there($index_table, 'i_ngram') === null);

        $fields_to_index = array(
            'meta_keywords' => APPEARANCE_CONTEXT_meta,
            'meta_description' => APPEARANCE_CONTEXT_body,
        );
        $key_transfer_map = array(
            'id' => 'i_catalogue_entry_id',
        );
        $filter_field_transfer_map = array(
            'ce_add_date' => 'i_add_time',
            'cc_id' => 'i_category_id',
            'c_name' => 'i_c_name',
            'ce_submitter' => 'i_submitter',
        );

        $db = $GLOBALS['SITE_DB'];
        $sql = 'SELECT c_name,id,ce_add_date,cc_id,ce_submitter FROM ' . $db->get_table_prefix() . 'catalogue_entries r WHERE 1=1';
        $since_clause = $engine->generate_since_where_clause($db, $index_table, array('ce_add_date' => false, 'ce_edit_date' => true), $since, $statistics_map);
        $sql .= $since_clause;
        $sql .= ' AND r.c_name NOT LIKE \'' . db_encode_like('\_%') . '\''; // Don't want results drawn from the hidden custom-field catalogues
        $max = 100;
        $start = 0;
        do {
            $rows = $db->query($sql, $max, $start);
            foreach ($rows as $row) {
                $langs = find_all_langs();
                foreach (array_keys($langs) as $lang) {
                    $content_fields = $row;

                    $engine->get_content_fields_from_catalogue_entry($content_fields, $fields_to_index, $row['c_name'], $row['id'], $lang);

                    list($keywords, $description) = seo_meta_get_for('catalogue_entry', strval($row['id']));
                    $content_fields += array(
                        'meta_keywords' => $keywords,
                        'meta_description' => $description,
                    );

                    $engine->index_for_search($db, $index_table, $content_fields, $fields_to_index, $key_transfer_map, $filter_field_transfer_map, $total_singular_ngram_tokens, $statistics_map, $lang, $clean_scan);
                }
            }
            $start += $max;
        } while (!empty($rows));
    }

    /**
     * Get details for an ajax-tree-list of entries for the content covered by this search hook.
     *
     * @return ?mixed Either Tempcode of a full screen to show, or a pair: the hook, and the options (null: no tree)
     */
    public function ajax_tree()
    {
        $catalogue_name = get_param_string('catalogue_name', '');
        if ($catalogue_name == '') {
            if (get_param_string('content', '') != '') {
                return null; // Mid-search
            }

            $tree = create_selection_list_catalogues(null, true);
            if ($tree->is_empty()) {
                inform_exit(do_lang_tempcode('NO_ENTRIES', 'catalogue'));
            }

            require_code('form_templates');
            $fields = form_input_list(do_lang_tempcode('NAME'), '', 'catalogue_name', $tree, null, true);
            $post_url = get_self_url(false, false, null, false, true);
            $submit_name = do_lang_tempcode('PROCEED');
            $hidden = build_keep_post_fields();

            $title = get_screen_title('SEARCH');
            return do_template('FORM_SCREEN', array('_GUID' => 'a2812ac8056903811f444682d45ee448', 'TARGET' => '_self', 'GET' => true, 'SKIP_WEBSTANDARDS' => true, 'HIDDEN' => $hidden, 'TITLE' => $title, 'TEXT' => '', 'URL' => $post_url, 'FIELDS' => $fields, 'SUBMIT_ICON' => 'buttons__search', 'SUBMIT_NAME' => $submit_name));
        }

        return array('choose_catalogue_category', array('catalogue_name' => $catalogue_name));
    }

    /**
     * Get a list of extra fields to ask for.
     *
     * @return ?array A list of maps specifying extra fields (null: no tree)
     */
    public function get_fields()
    {
        $catalogue_name = get_param_string('catalogue_name', '');
        if ($catalogue_name == '') {
            return array();
        }
        return $this->_get_fields($catalogue_name);
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
        if (!module_installed('catalogues')) {
            return array();
        }

        $remapped_orderer = '';
        switch ($sort) {
            case 'average_rating':
            case 'compound_rating':
                $remapped_orderer = $sort . ':catalogues:id';
                break;

            case 'title':
                if (get_param_string('catalogue_name', '') != '') {
                    $remapped_orderer = 'b_cv_value'; // short table
                }
                break;

            case 'add_date':
                $remapped_orderer = 'ce_add_date';
                break;

            case 'relevance':
                break;

            default:
                if (preg_match('#^f\d+\_actual\_value$#', $sort) != 0) {
                    $remapped_orderer = str_replace('_actual_value', '.cv_value', $sort);
                }
                break;
        }

        require_code('catalogues');
        require_lang('catalogues');

        // Calculate and perform query
        if (can_use_composr_fulltext_engine('catalogue_entries', $content, $cutoff !== null || $author != '' || ($search_under != '-1' && $search_under != '!'))) {
            // This search hook implements the Composr fast custom index, which we use where possible...

            $table = 'catalogue_entries r';

            // Calculate our where clause (search)
            $where_clause = '';
            $extra_join_clause = '';
            $sq = build_search_submitter_clauses('ixxx.i_submitter', $author_id, $author);
            if ($sq === null) {
                return array();
            } else {
                $extra_join_clause .= $sq;
            }
            $this->_handle_date_check($cutoff, 'ixxx.i_add_time', $extra_join_clause);

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
                    $extra_join_clause .= 'ixxx.i_category_id=' . strval(intval($cat));
                }
                $extra_join_clause .= ')';
            }

            if ((!has_privilege(get_member(), 'see_unvalidated')) && (addon_installed('unvalidated'))) {
                $where_clause .= ' AND ';
                $where_clause .= 'ce_validated=1';
            }

            $g_or = _get_where_clause_groups(get_member());
            if ($g_or !== null) {
                if (get_value('disable_cat_cat_perms') !== '1') {
                    $where_clause .= ' AND EXISTS(SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_category_access z WHERE ' . db_string_equal_to('z.module_the_name', 'catalogues_category') . ' AND z.category_name=i_category_id AND ' . str_replace('group_id', 'z.group_id', $g_or) . ')';
                }
                $where_clause .= ' AND EXISTS(SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_category_access p WHERE ' . db_string_equal_to('p.module_the_name', 'catalogues_catalogue') . ' AND p.category_name=i_c_name AND ' . str_replace('group_id', 'p.group_id', $g_or) . ')';
            }

            if (addon_installed('content_privacy')) {
                require_code('content_privacy');
                list($privacy_join, $privacy_where) = get_privacy_where_clause('catalogue_entry', 'r');
                $table .= $privacy_join;
                $where_clause .= $privacy_where;
            }

            $engine = new Composr_fulltext_engine();

            $catalogue_name = get_param_string('catalogue_name', '');
            if (($catalogue_name != '') && ($engine->active_search_has_special_filtering()) || ($remapped_orderer == 'b_cv_value') || (strpos($remapped_orderer, '.') !== false)) {
                $trans_fields = array();
                $nontrans_fields = array();
                list($sup_table, $_where_clause, $trans_fields, $nontrans_fields, $title_field) = $this->_get_search_parameterisation_advanced($catalogue_name);
                $table .= $sup_table;
                $where_clause .= $_where_clause;
                // ^ Nothing done with trans_fields and nontrans_fields

                if ($remapped_orderer == 'b_cv_value') {
                    $remapped_orderer = $title_field;
                }
            }

            $db = $GLOBALS['SITE_DB'];
            $index_table = 'ce_fulltext_index';
            $key_transfer_map = array('id' => 'i_catalogue_entry_id');
            $rows = $engine->get_search_rows($db, $index_table, $db->get_table_prefix() . $table, $key_transfer_map, $where_clause, $extra_join_clause, $content, $boolean_search, $only_search_meta, $only_titles, $max, $start, $remapped_orderer, $direction);
        } else {
            // Calculate our where clause (search)
            $sq = build_search_submitter_clauses('ce_submitter', $author_id, $author);
            if (is_null($sq)) {
                return array();
            } else {
                $where_clause .= $sq;
            }
            $this->_handle_date_check($cutoff, 'r.ce_add_date', $where_clause);
            if (!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) {
                if (get_value('disable_cat_cat_perms') !== '1') {
                    $where_clause .= ' AND ';
                    $where_clause .= 'z.category_name IS NOT NULL';
                }
                $where_clause .= ' AND ';
                $where_clause .= 'p.category_name IS NOT NULL';
            }
            if ((!has_privilege(get_member(), 'see_unvalidated')) && (addon_installed('unvalidated'))) {
                $where_clause .= ' AND ';
                $where_clause .= 'ce_validated=1';
            }

            $g_or = _get_where_clause_groups(get_member());

            $privacy_join = '';
            if (addon_installed('content_privacy')) {
                require_code('content_privacy');
                list($privacy_join, $privacy_where) = get_privacy_where_clause('catalogue_entry', 'r');
                $where_clause .= $privacy_where;
            }

            $catalogue_name = get_param_string('catalogue_name', '');
            if ($catalogue_name != '') {
                $table = 'catalogue_entries r';

                if ($g_or !== null) {
                    $table .= ((get_value('disable_cat_cat_perms') === '1') ? '' : (' LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_category_access z ON (' . db_string_equal_to('z.module_the_name', 'catalogues_category') . ' AND z.category_name=r.cc_id AND ' . str_replace('group_id', 'z.group_id', $g_or) . ')')) . ' LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_category_access p ON (' . db_string_equal_to('p.module_the_name', 'catalogues_catalogue') . ' AND p.category_name=r.c_name AND ' . str_replace('group_id', 'p.group_id', $g_or) . ')';
                }

                list($sup_table, $where_clause, $trans_fields, $nontrans_fields, $title_field) = $this->_get_search_parameterisation_advanced($catalogue_name);
                $table .= $sup_table;
                $table .= $privacy_join;

                $extra_select = '';

                if (is_null($title_field)) {
                    return array(); // No fields in catalogue -- very odd
                }

                $rows = get_search_rows('catalogue_entry', 'id', $content, $boolean_search, $boolean_operator, $only_search_meta, $direction, $max, $start, $only_titles, $table, $trans_fields, $where_clause, $content_where, $remapped_orderer, 'r.*,' . $title_field . ' AS b_cv_value' . $extra_select, $nontrans_fields);
            } else {
                $table = 'catalogue_fields f LEFT JOIN ' . get_table_prefix() . 'catalogue_entries r ON (r.c_name=f.c_name)';

                if ($g_or !== null) {
                    $table .= ((get_value('disable_cat_cat_perms') === '1') ? '' : (' LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_category_access z ON (' . db_string_equal_to('z.module_the_name', 'catalogues_category') . ' AND z.category_name=r.cc_id AND ' . str_replace('group_id', 'z.group_id', $g_or) . ')')) . ' LEFT JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'group_category_access p ON (' . db_string_equal_to('p.module_the_name', 'catalogues_catalogue') . ' AND p.category_name=r.c_name AND ' . str_replace('group_id', 'p.group_id', $g_or) . ')';
                }

                if (multi_lang_content() && $GLOBALS['SITE_DB']->query_select_value('translate', 'COUNT(*)') > 10000) { // Big sites can't do indiscriminate catalogue translatable searches for performance reasons
                    $trans_fields = array();
                    $join = ' JOIN ' . get_table_prefix() . 'catalogue_efv_short c ON (r.id=c.ce_id AND f.id=c.cf_id) LEFT JOIN ' . get_table_prefix() . 'catalogue_efv_long d ON (r.id=d.ce_id AND f.id=d.cf_id)';
                    $extra_select = '';
                    $non_trans_fields = array('c.cv_value', 'd.cv_value');
                } else {
                    $join = ' LEFT JOIN ' . get_table_prefix() . 'catalogue_efv_short_trans a ON (r.id=a.ce_id AND f.id=a.cf_id) LEFT JOIN ' . get_table_prefix() . 'catalogue_efv_long_trans b ON (r.id=b.ce_id AND f.id=b.cf_id) LEFT JOIN ' . get_table_prefix() . 'catalogue_efv_short c ON (r.id=c.ce_id AND f.id=c.cf_id) LEFT JOIN ' . get_table_prefix() . 'catalogue_efv_long d ON (r.id=d.ce_id AND f.id=d.cf_id)';
                    //' LEFT JOIN ' . get_table_prefix() . 'catalogue_efv_float g ON (r.id=g.ce_id AND f.id=g.cf_id) LEFT JOIN ' . get_table_prefix() . 'catalogue_efv_integer h ON (r.id=h.ce_id AND f.id=h.cf_id)';       No search is done on these unless it's an advanced search
                    $trans_fields = array('a.cv_value' => 'LONG_TRANS__COMCODE', 'b.cv_value' => 'LONG_TRANS__COMCODE');
                    $extra_select = ',b.cv_value AS b_cv_value';
                    $non_trans_fields = array('c.cv_value', 'd.cv_value'/*, 'g.cv_value', 'h.cv_value'*/);
                }

                $where_clause .= ' AND ';
                $where_clause .= 'r.c_name NOT LIKE \'' . db_encode_like('\_%') . '\''; // Don't want results drawn from the hidden custom-field catalogues

                $join .= $privacy_join;

                $rows = get_search_rows('catalogue_entry', 'id', $content, $boolean_search, $boolean_operator, $only_search_meta, $direction, $max, $start, $only_titles, $table . $join, $trans_fields, $where_clause, $content_where, $remapped_orderer, 'r.*' . $extra_select, $non_trans_fields);
            }
        }

        $out = array();
        if (count($rows) == 0) {
            return array();
        }

        global $SEARCH_CATALOGUE_ENTRIES_CATALOGUES_CACHE;
        $query = 'SELECT c.* FROM ' . get_table_prefix() . 'catalogues c';
        if (can_arbitrary_groupby()) {
            $query .= ' JOIN ' . get_table_prefix() . 'catalogue_entries e ON e.c_name=c.c_name GROUP BY c.c_name';
        }
        $_catalogues = $GLOBALS['SITE_DB']->query($query);
        foreach ($_catalogues as $catalogue) {
            $SEARCH_CATALOGUE_ENTRIES_CATALOGUES_CACHE[$catalogue['c_name']] = $catalogue;
        }
        foreach ($rows as $i => $row) {
            $out[$i]['data'] = $row;
            unset($rows[$i]);

            if (($remapped_orderer != '') && (array_key_exists($remapped_orderer, $row))) {
                $out[$i]['orderer'] = $row[$remapped_orderer];
            } elseif (strpos($remapped_orderer, '_rating:') !== false) {
                $out[$i]['orderer'] = $row[$remapped_orderer];
            }
        }

        return $out;
    }

    /**
     * Run function for rendering a search result.
     *
     * @param  array $row The data row stored when we retrieved the result
     * @return ?Tempcode The output (null: compound output)
     */
    public function render($row)
    {
        require_code('catalogues');
        return render_catalogue_entry_box($row, '_SEARCH');
    }
}

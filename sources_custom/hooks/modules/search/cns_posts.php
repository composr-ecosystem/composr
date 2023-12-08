<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class Hook_search_cns_posts extends FieldsSearchHook
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
        if (!addon_installed('cns_forum')) {
            return null;
        }

        if ($member_id === null) {
            $member_id = get_member();
        }

        if (!$GLOBALS['FORUM_DB']->table_exists('f_posts_fulltext_index')) {
            //$GLOBALS['FORUM_DB']->drop_table_if_exists('f_posts_fulltext_index');

            $GLOBALS['FORUM_DB']->create_table('f_posts_fulltext_index', array(
                'i_post_id' => '*AUTO_LINK',

                'i_lang' => '*LANGUAGE_NAME',
                'i_ngram' => '*INTEGER',
                'i_ac' => '*INTEGER',

                'i_occurrence_rate' => 'REAL',

                // De-normalised stuff from main content tables for any major filters that shape the results provided
                //  (other stuff will come in via join back to the main content table)
                'i_add_time' => 'TIME',
                'i_forum_id' => 'AUTO_LINK',
                'i_poster_id' => 'MEMBER',
                'i_open' => 'BINARY',
                'i_pinned' => 'BINARY',
                'i_starter' => 'BINARY',
            ));

            //$GLOBALS['SITE_DB']->delete_index_if_exists('f_posts_fulltext_index', 'content_id');
            //$GLOBALS['SITE_DB']->delete_index_if_exists('f_posts_fulltext_index', 'main');

            $GLOBALS['FORUM_DB']->create_index('f_posts_fulltext_index', 'content_id', array( // Used for cleanouts and potentially optimising some JOINs if query planner decides to start at the content table
                'i_post_id',
            ));

            $GLOBALS['FORUM_DB']->create_index('f_posts_fulltext_index', 'main', array(
                'i_lang',
                'i_ngram',
                'i_ac',
                'i_add_time',
                'i_forum_id',
                /*Disabled in v10 due to index key limit 'i_poster_id',
                'i_open',
                'i_pinned',
                'i_starter',*/
                'i_occurrence_rate', // For sorting
            ));
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        if ($check_permissions) {
            if (!has_actual_page_access($member_id, 'topicview')) {
                return false;
            }
        }

        if ($GLOBALS['FORUM_DRIVER']->get_num_forum_posts() == 0) {
            return null;
        }

        require_lang('cns');

        $info = array();
        $info['lang'] = do_lang_tempcode('FORUM_POSTS');
        $info['default'] = false;
        $info['special_on'] = array();
        $info['special_off'] = array('open' => do_lang_tempcode('POST_SEARCH_OPEN'), 'closed' => do_lang_tempcode('POST_SEARCH_CLOSED'), 'pinned' => do_lang_tempcode('POST_SEARCH_PINNED'));
        if ((has_privilege($member_id, 'see_unvalidated')) && (addon_installed('unvalidated'))) {
            $info['special_off']['unvalidated'] = do_lang_tempcode('POST_SEARCH_UNVALIDATED');
        }
        if (can_use_composr_fulltext_engine('cns_posts')) {
            $info['special_on']['starter'] = do_lang_tempcode('POST_SEARCH_STARTER');
        } else {
            $info['special_off']['starter'] = do_lang_tempcode('POST_SEARCH_STARTER');
        }
        $info['category'] = 'p_cache_forum_id';
        $info['integer_category'] = true;
        $info['extra_sort_fields'] = $this->_get_extra_sort_fields('_post');

        $info['permissions'] = array(
            array(
                'type' => 'zone',
                'zone_name' => get_module_zone('topicview'),
            ),
            array(
                'type' => 'page',
                'zone_name' => get_module_zone('topicview'),
                'page_name' => 'topicview',
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

        $index_table = 'f_posts_fulltext_index';
        $clean_scan = ($GLOBALS['FORUM_DB']->query_select_value_if_there($index_table, 'i_ngram') === null);

        $has_custom_fields = ($GLOBALS['FORUM_DB']->query_select_value_if_there('catalogue_fields', 'id', array('c_name' => '_post')) !== null);

        $fields_to_index = array(
            'p_title' => APPEARANCE_CONTEXT_title,
            'p_post' => APPEARANCE_CONTEXT_body,
        );
        $key_transfer_map = array(
            'id' => 'i_post_id',
        );
        $filter_field_transfer_map = array(
            'p_time' => 'i_add_time',
            'p_cache_forum_id' => 'i_forum_id',
            'p_poster' => 'i_poster_id',
            't_is_open' => 'i_open',
            't_pinned' => 'i_pinned',
            'i_starter' => 'i_starter',
        );

        $db = $GLOBALS['FORUM_DB'];

        // A way to force-resume where we left off, if we're debugging our way through
        if (get_value('fulltext_startup_hack', '0', true) == '1') {
            $last_post_id = $db->query_select_value_if_there('f_posts_fulltext_index', 'MAX(i_post_id)');
            if ($last_post_id !== null) {
                $_since = $db->query_select_value_if_there('f_posts', 'p_time', array('id' => $last_post_id));
                if ($_since !== null) {
                    $since = $_since;
                }
            }
        }


        global $TABLE_LANG_FIELDS_CACHE;
        $lang_fields = $TABLE_LANG_FIELDS_CACHE['f_posts'];

        $sql = 'SELECT p.id,p.p_time,p.p_last_edit_time,p.p_poster,p.p_title,p.p_post,p.p_cache_forum_id,t_is_open,t_pinned,t_cache_first_post_id FROM ' . $db->get_table_prefix() . 'f_posts p JOIN ' . $db->get_table_prefix() . 'f_topics t ON p.p_topic_id=t.id';
        $sql .= ' WHERE p_cache_forum_id IS NOT NULL';
        $since_clause = $engine->generate_since_where_clause($db, $index_table, array('p_time' => false, 'p_last_edit_time' => true), $since, $statistics_map);
        $sql .= $since_clause;

        $max_post_length = intval(get_value('fulltext_max_post_length', '0', true));
        if ($max_post_length > 0) {
            $sql .= ' AND ' . db_function('LENGTH', array($GLOBALS['FORUM_DB']->translate_field_ref('p_post'))) . '<' . strval($max_post_length);
        }

        $max = 100;
        $start_id = -1;
        do {
            $rows = $db->query($sql . ' AND p.id>' . strval($start_id) . ' ORDER BY p.id', $max, 0, false, false, $lang_fields);
            foreach ($rows as $row) {
                $content_fields = $row + array('i_starter' => ($row['t_cache_first_post_id'] == $row['id']) ? 1 : 0);

                if ($has_custom_fields) {
                    $ce_id = $GLOBALS['SITE_DB']->query_select_value_if_there('catalogue_entry_linkage', 'catalogue_entry_id', array('content_type' => 'post', 'content_id' => strval($row['id'])));
                    if ($ce_id !== null) {
                        $engine->get_content_fields_from_catalogue_entry($content_fields, $fields_to_index, '_post', $ce_id);
                    }
                }

                $engine->index_for_search($db, $index_table, $content_fields, $fields_to_index, $key_transfer_map, $filter_field_transfer_map, $total_singular_ngram_tokens, $statistics_map, null, $clean_scan);

                $start_id = $row['id'];
            }
        } while (!empty($rows));
    }

    /**
     * Get details for an ajax-tree-list of entries for the content covered by this search hook.
     *
     * @return array A pair: the hook, and the options
     */
    public function ajax_tree()
    {
        return array('choose_forum', array('compound_list' => true));
    }

    /**
     * Get a list of extra fields to ask for.
     *
     * @return ?array A list of maps specifying extra fields (null: no tree)
     */
    public function get_fields()
    {
        return $this->_get_fields('_post');
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
        if (in_array($content, array(
            do_lang('POSTS_WITHIN_TOPIC'),
            do_lang('SEARCH_POSTS_WITHIN_TOPIC'),
            do_lang('SEARCH_FORUM_POSTS'),
            do_lang('_SEARCH_PRIVATE_TOPICS'),
        ))) {
            return array(); // Search placeholder label, not real search
        }

        if (get_forum_type() != 'cns') {
            return array();
        }
        require_code('cns_forums');
        require_code('cns_posts');
        require_css('cns');

        $remapped_orderer = '';
        switch ($sort) {
            case 'title':
                $remapped_orderer = 'p_title';
                break;

            case 'add_date':
                $remapped_orderer = 'p_time';
                break;
        }

        require_lang('cns');

        // Calculate and perform query
        $permissions_module = 'forums';
        if (can_use_composr_fulltext_engine('cns_posts', $content, $cutoff !== null || $author != '' || ($search_under != '-1' && $search_under != '!') || get_param_integer('option_cns_posts_starter', 0) == 1)) {
            // This search hook implements the Composr fast custom index, which we use where possible...

            $table = 'f_posts r';

            // Calculate our where clause (search)
            $where_clause = '';
            $extra_join_clause = '';
            $sq = build_search_submitter_clauses('ixxx.i_poster_id', $author_id, $author);
            if ($sq === null) {
                return array();
            } else {
                $extra_join_clause .= $sq;
            }
            $this->_handle_date_check($cutoff, 'ixxx.i_add_time', $extra_join_clause);
            if (get_param_integer('option_cns_posts_unvalidated', 0) == 1) {
                $where_clause .= ' AND ';
                $where_clause .= 'r.p_validated=0';
            }
            if (get_param_integer('option_cns_posts_open', 0) == 1) {
                $extra_join_clause .= ' AND ';
                $extra_join_clause .= 'ixxx.i_open=1';
            }
            if (get_param_integer('option_cns_posts_closed', 0) == 1) {
                $extra_join_clause .= ' AND ';
                $extra_join_clause .= 'ixxx.i_open=0';
            }
            if (get_param_integer('option_cns_posts_pinned', 0) == 1) {
                $extra_join_clause .= ' AND ';
                $extra_join_clause .= 'ixxx.i_pinned=1';
            }
            if (get_param_integer('option_cns_posts_starter', 0) == 1) {
                $extra_join_clause .= ' AND ';
                $extra_join_clause .= 'ixxx.i_starter=1';
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
                    $extra_join_clause .= 'ixxx.i_forum_id=' . strval(intval($cat));
                }
                $extra_join_clause .= ')';
            }

            $where_clause .= ' AND ';
            $where_clause .= '(p_intended_solely_for IS NULL';
            if (!is_guest()) {
                $where_clause .= ' OR p_intended_solely_for=' . strval(get_member()) . ' OR p_poster=' . strval(get_member());
            }
            $where_clause .= ')';
            if ((!has_privilege(get_member(), 'see_unvalidated')) && (addon_installed('unvalidated'))) {
                $where_clause .= ' AND ';
                $where_clause .= 'p_validated=1';
            }

            $engine = new Composr_fulltext_engine();

            if ($engine->active_search_has_special_filtering()) {
                $trans_fields = array();
                $nontrans_fields = array();
                $this->_get_search_parameterisation_advanced_for_content_type('_post', $table, $where_clause, $trans_fields, $nontrans_fields);
                // ^ Nothing done with trans_fields and nontrans_fields
            }

            $db = $GLOBALS['FORUM_DB'];
            $index_table = 'f_posts_fulltext_index';
            $key_transfer_map = array('id' => 'i_post_id');
            $index_permissions_field = 'i_forum_id';
            $rows = $engine->get_search_rows($db, $index_table, $db->get_table_prefix() . $table, $key_transfer_map, $where_clause, $extra_join_clause, $content, $boolean_search, $only_search_meta, $only_titles, $max, $start, $remapped_orderer, $direction, $permissions_module, $index_permissions_field);
        } else {
            $table = 'f_posts r JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_topics s ON r.p_topic_id=s.id';

            // Calculate our where clause (search)
            $sq = build_search_submitter_clauses('p_poster', $author_id, $author);
            if (is_null($sq)) {
                return array();
            } else {
                $where_clause .= $sq;
            }
            $this->_handle_date_check($cutoff, 'p_time', $where_clause);
            if (get_param_integer('option_cns_posts_unvalidated', 0) == 1) {
                $where_clause .= ' AND ';
                $where_clause .= 'r.p_validated=0';
            }
            if (get_param_integer('option_cns_posts_open', 0) == 1) {
                $where_clause .= ' AND ';
                $where_clause .= 's.t_is_open=1';
            }
            if (get_param_integer('option_cns_posts_closed', 0) == 1) {
                $where_clause .= ' AND ';
                $where_clause .= 's.t_is_open=0';
            }
            if (get_param_integer('option_cns_posts_pinned', 0) == 1) {
                $where_clause .= ' AND ';
                $where_clause .= 's.t_pinned=1';
            }
            if (get_param_integer('option_cns_posts_starter', 0) == 1) {
                $where_clause .= ' AND ';
                $where_clause .= 's.t_cache_first_post_id=r.id';
            }

            $where_clause .= ' AND ';
            $where_clause .= 'p_cache_forum_id IS NOT NULL AND (r.p_intended_solely_for IS NULL';
            if (!is_guest()) {
                $where_clause .= ' OR r.p_intended_solely_for=' . strval(get_member()) . ' OR r.p_poster=' . strval(get_member());
            }
            $where_clause .= ')';
            if ((!has_privilege(get_member(), 'see_unvalidated')) && (addon_installed('unvalidated'))) {
                $where_clause .= ' AND ';
                $where_clause .= 'r.p_validated=1';
            }

            $trans_fields = array('!' => '!', 'r.p_post' => 'LONG_TRANS__COMCODE');
            $nontrans_fields = array('r.p_title'/*,'s.t_description' Performance problem due to how full text works*/);
            $this->_get_search_parameterisation_advanced_for_content_type('_post', $table, $where_clause, $trans_fields, $nontrans_fields);

            $rows = get_search_rows(null, null, $content, $boolean_search, $boolean_operator, $only_search_meta, $direction, $max, $start, $only_titles, $table, $trans_fields, $where_clause, $content_where, $remapped_orderer, 'r.*,t_forum_id,t_cache_first_title', $nontrans_fields, $permissions_module, 't_forum_id');
        }

        $out = array();
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
     * @return Tempcode The output
     */
    public function render($row)
    {
        global $SEARCH__CONTENT_BITS, $LAX_COMCODE;
        $highlight_bits = ($SEARCH__CONTENT_BITS === null) ? array() : $SEARCH__CONTENT_BITS;
        $LAX_COMCODE = true;
        $summary = get_translated_text($row['p_post']);
        $text_summary_h = comcode_to_tempcode($summary, null, false, null, null, null, false, false, false, false, false, $highlight_bits);
        $LAX_COMCODE = false;
        $text_summary = generate_text_summary($text_summary_h->evaluate(), $highlight_bits);

        require_code('cns_posts2');
        return render_post_box($row, false, true, true, null, '', protect_from_escaping($text_summary));
    }
}

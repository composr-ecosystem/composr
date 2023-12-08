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
class Hook_search_cns_own_pt extends FieldsSearchHook
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

        if (!$GLOBALS['FORUM_DB']->table_exists('f_pposts_fulltext_index')) {
            //$GLOBALS['FORUM_DB']->drop_table_if_exists('f_pposts_fulltext_index');

            $GLOBALS['FORUM_DB']->create_table('f_pposts_fulltext_index', array(
                'i_post_id' => '*AUTO_LINK',
                'i_for' => '*MEMBER',

                'i_lang' => '*LANGUAGE_NAME',
                'i_ngram' => '*INTEGER',
                'i_ac' => '*INTEGER',

                'i_occurrence_rate' => 'REAL',

                // De-normalised stuff from main content tables for any major filters that shape the results provided
                //  (other stuff will come in via join back to the main content table)
                'i_add_time' => 'TIME',
                'i_poster_id' => 'MEMBER',
                'i_starter' => 'BINARY',
            ));

            //$GLOBALS['SITE_DB']->delete_index_if_exists('f_pposts_fulltext_index', 'content_id');
            //$GLOBALS['SITE_DB']->delete_index_if_exists('f_pposts_fulltext_index', 'main');

            $GLOBALS['FORUM_DB']->create_index('f_pposts_fulltext_index', 'content_id', array( // Used for cleanouts and potentially optimising some JOINs if query planner decides to start at the content table
                'i_post_id',
            ));

            $GLOBALS['FORUM_DB']->create_index('f_pposts_fulltext_index', 'main', array(
                'i_lang',
                'i_ngram',
                'i_ac',
                'i_add_time',
                /*Disabled in v10 due to index key limit 'i_poster_id',
                'i_starter',
                'i_for',*/
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

            if ($member_id == $GLOBALS['CNS_DRIVER']->get_guest_id()) {
                return null;
            }

            if ($GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_topics WHERE t_pt_from=' . strval($member_id) . ' OR ' . 't_pt_to=' . strval($member_id)) == 0) {
                return null;
            }
        }

        require_lang('cns');

        $info = array();
        $info['lang'] = do_lang_tempcode('PRIVATE_TOPICS');
        $info['default'] = false;
        $info['special_on'] = array();
        $info['special_off'] = array('starter' => do_lang_tempcode('POST_SEARCH_STARTER'));

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
            array(
                'type' => 'non_guests',
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

        $index_table = 'f_pposts_fulltext_index';
        $clean_scan = ($GLOBALS['FORUM_DB']->query_select_value_if_there($index_table, 'i_ngram') === null);

        $fields_to_index = array(
            'p_title' => APPEARANCE_CONTEXT_title,
            'p_post' => APPEARANCE_CONTEXT_body,
        );
        $key_transfer_map = array(
            'id' => 'i_post_id',
        );
        $filter_field_transfer_map = array(
            'p_time' => 'i_add_time',
            'p_poster' => 'i_poster_id',
            'i_starter' => 'i_starter',
            'i_for' => 'i_for',
        );

        $db = $GLOBALS['FORUM_DB'];
        $sql = 'SELECT p.id,p.p_time,p.p_last_edit_time,p.p_poster,p.p_title,p.p_post,t_cache_first_post_id,t_pt_from,t_pt_to FROM ' . $db->get_table_prefix() . 'f_posts p JOIN ' . $db->get_table_prefix() . 'f_topics t ON p.p_topic_id=t.id';
        $sql .= ' WHERE p_cache_forum_id IS NULL';
        $since_clause = $engine->generate_since_where_clause($db, $index_table, array('p_time' => false, 'p_last_edit_time' => true), $since, $statistics_map);
        $sql .= $since_clause;
        $max = 100;
        $start_id = -1;
        do {
            $rows = $db->query($sql . ' AND p.id>' . strval($start_id) . ' ORDER BY p.id', $max);
            foreach ($rows as $row) {
                $content_fields = $row + array('i_starter' => ($row['t_cache_first_post_id'] == $row['id']) ? 1 : 0);

                foreach (($row['t_pt_from'] == $row['t_pt_to']) ? array('t_pt_from') : array('t_pt_from', 't_pt_to') as $for_field) {
                    $key_transfer_map['i_for'] = 'i_for';
                    $content_fields['i_for'] = $row[$for_field];
                    $engine->index_for_search($db, $index_table, $content_fields, $fields_to_index, $key_transfer_map, $filter_field_transfer_map, $total_singular_ngram_tokens, $statistics_map, null, $clean_scan);
                }

                $start_id = $row['id'];
            }
        } while (!empty($rows));
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
        if (get_forum_type() != 'cns') {
            return array();
        }
        if (get_member() == $GLOBALS['CNS_DRIVER']->get_guest_id()) {
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
        if (can_use_composr_fulltext_engine('cns_own_pt', $content, $cutoff !== null || $author != '' || ($search_under != '-1' && $search_under != '!') || get_param_integer('option_cns_own_pt_starter', 0) == 1)) {
            // This search hook implements the Composr fast custom index, which we use where possible...

            $table = 'f_posts r';

            // Calculate our where clause (search)
            $where_clause = '';
            $extra_join_clause = '';
            $sq = build_search_submitter_clauses('i_poster_id', $author_id, $author);
            if ($sq === null) {
                return array();
            } else {
                $where_clause .= $sq;
            }
            $this->_handle_date_check($cutoff, 'ixxx.i_add_time', $extra_join_clause);
            if (get_param_integer('option_cns_own_pt_starter', 0) == 1) {
                $extra_join_clause .= ' AND ';
                $extra_join_clause .= 'ixxx.i_starter=1';
            }

            $extra_join_clause .= ' AND ixxx.i_for=' . strval(get_member());
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

            $db = $GLOBALS['FORUM_DB'];
            $index_table = 'f_pposts_fulltext_index';
            $key_transfer_map = array('id' => 'i_post_id');
            $rows = $engine->get_search_rows($db, $index_table, $db->get_table_prefix() . $table, $key_transfer_map, $where_clause, $extra_join_clause, $content, $boolean_search, $only_search_meta, $only_titles, $max, $start, $remapped_orderer, $direction);
        } else {
            // Calculate our where clause (search)
            $where_clause .= ' AND ';
            $where_clause .= 't_forum_id IS NULL AND (t_pt_from=' . strval(get_member()) . ' OR t_pt_to=' . strval(get_member()) . ')';
            $where_clause .= ' AND ';
            $where_clause .= '(r.p_intended_solely_for IS NULL';
            if (!is_guest()) {
                $where_clause .= ' OR r.p_intended_solely_for=' . strval(get_member()) . ' OR r.p_poster=' . strval(get_member());
            }
            $where_clause .= ')';
            $sq = build_search_submitter_clauses('p_poster', $author_id, $author);
            if (is_null($sq)) {
                return array();
            } else {
                $where_clause .= $sq;
            }
            $this->_handle_date_check($cutoff, 'p_time', $where_clause);
            if (get_param_integer('option_cns_own_pt_starter', 0) == 1) {
                $where_clause .= ' AND ';
                $where_clause .= 's.t_cache_first_post_id=r.id';
            }

            if ((!has_privilege(get_member(), 'see_unvalidated')) && (addon_installed('unvalidated'))) {
                $where_clause .= ' AND ';
                $where_clause .= 'p_validated=1';
            }

            $rows = get_search_rows(null, null, $content, $boolean_search, $boolean_operator, $only_search_meta, $direction, $max, $start, $only_titles, 'f_posts r JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_topics s ON r.p_topic_id=s.id', array('!' => '!', 'r.p_post' => 'LONG_TRANS__COMCODE'), $where_clause, $content_where, $remapped_orderer, 'r.*', array('r.p_title'));
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
        require_code('cns_posts2');
        return render_post_box($row);
    }
}

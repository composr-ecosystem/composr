<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

/**
 * Hook class.
 */
class Hook_search_tutorials_external extends FieldsSearchHook
{
    /**
     * Find details for this search hook.
     *
     * @param  boolean $check_permissions Whether to check permissions
     * @param  ?MEMBER $member_id The member ID to check with (null: current member)
     * @return ~?array Map of search hook details (null: hook is disabled) (false: access denied)
     */
    public function info(bool $check_permissions = true, ?int $member_id = null)
    {
        if (!addon_installed('composr_tutorials')) {
            return null;
        }

        if (!addon_installed('composr_homesite')) {
            return null;
        }
        if (!addon_installed('composr_homesite_support_credits')) {
            return null;
        }
        if (!addon_installed('composr_release_build')) {
            return null;
        }

        if ($member_id === null) {
            $member_id = get_member();
        }

        require_lang('tutorials');

        $info = [];
        $info['lang'] = do_lang_tempcode('TUTORIALS_EXTERNAL');
        $info['default'] = true;
        $info['integer_category'] = true;

        $info['permissions'] = [];

        return $info;
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
     * @param  mixed $cutoff Cutoff date (TIME or a pair representing the range or null)
     * @return array List of maps (template, orderer)
     */
    public function run(string $search_query, string $content_where, string $where_clause, string $search_under, bool $only_search_meta, bool $only_titles, int $max, int $start, string $sort, string $direction, string $author, ?int $author_id, $cutoff) : array
    {
        require_code('tutorials');

        $remapped_orderer = '';
        switch ($sort) {
            case 'title':
                $remapped_orderer = 't_title';
                break;

            case 'add_date':
                $remapped_orderer = 't_add_date';
                break;
        }

        $sq = build_search_submitter_clauses('t_submitter', $author_id, $author, 't_author');
        if ($sq === null) {
            return [];
        } else {
            $where_clause .= $sq;
        }

        $table = 'tutorials_external r';
        $trans_fields = [];
        $nontrans_fields = ['r.t_title', 'r.t_summary'];
        $this->_get_search_parameterisation_advanced_for_content_type('_comcode_page', $table, $where_clause, $trans_fields, $nontrans_fields);

        // Calculate and perform query
        $rows = get_search_rows(null, 'id', $search_query, $content_where, $where_clause, $only_search_meta, $only_titles, $max, $start, $remapped_orderer, $direction, $table, 'r.*,' . tutorial_sql_rating(db_cast('r.id', 'CHAR')) . ',' . tutorial_sql_rating_recent(db_cast('r.id', 'CHAR')) . ',' . tutorial_sql_likes(db_cast('r.id', 'CHAR')) . ',' . tutorial_sql_likes_recent(db_cast('r.id', 'CHAR')), $trans_fields, $nontrans_fields);

        $out = [];
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
    public function render(array $row) : object
    {
        $tags = collapse_1d_complexity('t_tag', $GLOBALS['SITE_DB']->query_select('tutorials_external_tags', ['t_tag'], ['t_id' => $row['id']]));
        $metadata = get_tutorial_metadata(strval($row['id']), $row, $tags);
        return do_template('TUTORIAL_BOX', templatify_tutorial($metadata));
    }
}

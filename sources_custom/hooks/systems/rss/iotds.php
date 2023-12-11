<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Hook class.
 */
class Hook_rss_iotds
{
    /**
     * Run function for RSS hooks.
     *
     * @param  string $_filters A list of categories we accept from
     * @param  TIME $cutoff Cutoff time, before which we do not show results from
     * @param  string $prefix Prefix that represents the template set we use
     * @set    RSS_ ATOM_
     * @param  string $date_string The standard format of date to use for the syndication type represented in the prefix
     * @param  integer $max The maximum number of entries to return, ordering by date
     * @return ?array A pair: The main syndication section, and a title (null: error)
     */
    public function run($_filters, $cutoff, $prefix, $date_string, $max)
    {
        if (!addon_installed('iotds')) {
            return null;
        }

        if (!$GLOBALS['SITE_DB']->table_exists('iotd')) {
            return null;
        }

        if (!has_actual_page_access(get_member(), 'iotds')) {
            return null;
        }

        $content = new Tempcode();
        $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'iotd WHERE add_date>' . strval($cutoff) . ' AND (used=1 OR is_current=1) ORDER BY add_date DESC', $max);
        foreach ($rows as $row) {
            $id = strval($row['id']);
            $author = $GLOBALS['FORUM_DRIVER']->get_username($row['submitter']);
            if (is_null($author)) {
                $author = '';
            }

            $news_date = date($date_string, $row['add_date']);
            $edit_date = is_null($row['edit_date']) ? '' : date($date_string, $row['edit_date']);

            $_news_title = get_translated_tempcode('iotd', $row, 'i_title');
            $news_title = xmlentities($_news_title->evaluate());
            $_summary = get_translated_tempcode('iotd', $row, 'caption');
            $summary = xmlentities($_summary->evaluate());
            $news = '';

            $category = '';
            $category_raw = '';

            $view_url = build_url(array('page' => 'iotds', 'type' => 'view', 'id' => $row['id']), get_module_zone('iotds'), null, false, false, true);

            if (($prefix == 'RSS_') && (get_option('is_on_comments') == '1') && ($row['allow_comments'] >= 1)) {
                $if_comments = do_template('RSS_ENTRY_COMMENTS', array('_GUID' => 'a8ccf291cb27c8ffb34f023416b85664', 'COMMENT_URL' => $view_url, 'ID' => $id), null, false, null, '.xml', 'xml');
            } else {
                $if_comments = new Tempcode();
            }

            require_code('images');
            $enclosure_url = ensure_thumbnail($row['url'], $row['thumb_url'], 'iotds', 'iotd', $row['id']);
            list($enclosure_length, $enclosure_type) = get_enclosure_details($row['url'], $enclosure_url);

            $content->attach(do_template($prefix . 'ENTRY', array(
                'ENCLOSURE_URL' => $enclosure_url,
                'ENCLOSURE_LENGTH' => $enclosure_length,
                'ENCLOSURE_TYPE' => $enclosure_type,
                'VIEW_URL' => $view_url,
                'SUMMARY' => $summary,
                'EDIT_DATE' => $edit_date,
                'IF_COMMENTS' => $if_comments,
                'TITLE' => $news_title,
                'CATEGORY_RAW' => $category_raw,
                'CATEGORY' => $category,
                'AUTHOR' => $author,
                'ID' => $id,
                'NEWS' => $news,
                'DATE' => $news_date,
            ), null, false, null, '.xml', 'xml'));
        }

        require_lang('iotds');
        return array($content, do_lang('IOTDS'));
    }
}

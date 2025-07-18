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

/**
 * Hook class.
 */
class Hook_rss_comcode_pages
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
        $filters = explode(',', $_filters);

        $content = new Tempcode();
        $_rows = $GLOBALS['SITE_DB']->query_select('cached_comcode_pages', array('the_page', 'the_zone', 'cc_page_title'));
        $rows = array();
        foreach ($_rows as $row) {
            $rows[$row['the_zone'] . ':' . $row['the_page']] = $row;
        }
        $_rows2 = $GLOBALS['SITE_DB']->query_select('seo_meta', array('*'), array('meta_for_type' => 'comcode_page'));
        $rows2 = array();
        foreach ($_rows2 as $row) {
            $rows2[$row['meta_for_id']] = $row;
        }
        $_rows3 = $GLOBALS['SITE_DB']->query_select('comcode_pages');
        $rows3 = array();
        foreach ($_rows3 as $row) {
            $rows3[$row['the_zone'] . ':' . $row['the_page']] = $row;
        }
        $zones = find_all_zones(false, true);
        foreach ($zones as $zone => $zone_details) {
            if (!has_zone_access(get_member(), $zone)) {
                continue;
            }

            if ($filters != array('*')) {
                $ok = false;
                foreach ($filters as $filter) {
                    if ($zone == $filter) {
                        $ok = true;
                    }
                }
                if (!$ok) {
                    continue;
                }
            }

            $pages = find_all_pages($zone, 'comcode_custom/' . get_site_default_lang(), 'txt', false, $cutoff);
            foreach (array_keys($pages) as $i => $page) {
                if ($i == $max) {
                    break;
                }

                if (substr($page, 0, 6) == 'panel_') {
                    continue;
                }
                if (!has_page_access(get_member(), $page, $zone)) {
                    continue;
                }

                $id = $zone . ':' . $page;

                $page_request = _request_page($page, $zone);
                if (strpos($page_request[0], 'COMCODE') === false) {
                    continue;
                }
                $_zone = $page_request[count($page_request) - 1];
                $path = get_custom_file_base() . (($_zone == '') ? '' : '/') . $_zone;
                if (!file_exists($path)) {
                    continue;
                }

                $news_date = date($date_string, filectime($path));
                $edit_date = date($date_string, filemtime($path));
                if ($news_date == $edit_date) {
                    $edit_date = '';
                }

                $summary = '';
                $news = '';
                $author = '';
                $news_title = xmlentities($page);
                if (array_key_exists($id, $rows)) {
                    $_news_title = get_translated_text($rows[$id]['cc_page_title'], null, null, true);
                    if (is_null($_news_title)) {
                        $_news_title = '';
                    }
                    $news_title = xmlentities($_news_title);
                }
                if (array_key_exists($id, $rows2)) {
                    $summary = xmlentities(get_translated_text($rows2[$id]['meta_description']));
                }
                if (array_key_exists($id, $rows3)) {
                    if ((!has_privilege(get_member(), 'see_unvalidated')) && ($rows3[$id]['p_validated'] == 0) && (addon_installed('unvalidated'))) {
                        continue;
                    }

                    $author = $GLOBALS['FORUM_DRIVER']->get_username($rows3[$id]['p_submitter']);
                    if ($author === null) {
                        $author = '';
                    }
                    $news_date = date($date_string, $rows3[$id]['p_add_date']);
                    $edit_date = ($rows3[$id]['p_edit_date'] === null) ? '' : date($date_string, $rows3[$id]['p_edit_date']);
                    if ($news_date == $edit_date) {
                        $edit_date = '';
                    }
                }
                if (is_null($author)) {
                    $author = '';
                }

                $category = $zone_details[1];
                $category_raw = $zone;

                $view_url = build_url(array('page' => $page), $zone, null, false, false, true);

                $if_comments = new Tempcode();

                $content->attach(do_template($prefix . 'ENTRY', array('VIEW_URL' => $view_url, 'SUMMARY' => $summary, 'EDIT_DATE' => $edit_date, 'IF_COMMENTS' => $if_comments, 'TITLE' => $news_title, 'CATEGORY_RAW' => $category_raw, 'CATEGORY' => $category, 'AUTHOR' => $author, 'ID' => $id, 'NEWS' => $news, 'DATE' => $news_date), null, false, null, '.xml', 'xml'));
            }
        }

        require_lang('zones');
        return array($content, do_lang('COMCODE_PAGES'));
    }
}

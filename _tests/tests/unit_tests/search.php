<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class search_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        $this->establish_admin_session();

        require_code('xml');
    }

    public function testNoSearchHooksCrash()
    {
        require_code('database_search');

        $boolean_operator = 'AND';
        $content = 'qwertyuiop';
        list($content_where) = build_content_where($content, false, $boolean_operator);
        $only_search_meta = false;
        $direction = 'DESC';
        $max = 10;
        $start = 0;
        $only_titles = false;
        $author = '';
        $author_id = null;
        $cutoff = null;
        $sort = 'relevance';
        $where_clause = '';
        $search_under = '!';
        $boolean_search = false;

        $_hooks = find_all_hooks('modules', 'search'); // TODO: find_all_hook_obs in v11
        foreach (array_keys($_hooks) as $hook) {
            require_code('hooks/modules/search/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_search_' . filter_naughty_harsh($hook), true);
            if ($ob === null) {
                continue;
            }
            $info = $ob->info();
            if (($info === null) || ($info === false)) {
                continue;
            }

            if (php_function_allowed('set_time_limit')) {
                @set_time_limit(10); // Prevent errant search hooks (easily written!) taking down a server. Each call given 10 seconds (calling set_time_limit resets the timer).
            }
            $hook_results = $ob->run($content, $only_search_meta, $direction, $max, $start, $only_titles, $content_where, $author, $author_id, $cutoff, $sort, $max, $boolean_operator, $where_clause, $search_under, $boolean_search ? 1 : 0);
            $this->assertTrue(is_array($hook_results));
        }
    }

    public function testHttpGuestFullSearch()
    {
        $this->establish_admin_session();

        require_lang('search');

        $url_parts = array('page' => 'search', 'type' => 'results', 'content' => 'qwertyuiopxxx');

        $_hooks = find_all_hooks('modules', 'search');
        foreach (array_keys($_hooks) as $hook) {
            $url_parts['search_' . $hook] = 1;
        }

        foreach (array(0, 1) as $safe_mode) {
            foreach (array($this->get_canonical_username('admin'), $this->get_canonical_username('test')) as $username) {
                $url = build_url($url_parts + array('keep_su' => $username, 'keep_safe_mode' => $safe_mode), get_module_zone('search'));
                $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
                $this->assertTrue(strpos($data, do_lang('NO_RESULTS_SEARCH')) !== false); // We expect no results, but also no crash!
            }
        }
    }

    public function testOpenSearch()
    {
        $url = find_script('opensearch');
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $parsed = new CMS_simple_xml_reader($data);
        global $HTTP_DOWNLOAD_MIME_TYPE;
        $this->assertTrue(strpos($HTTP_DOWNLOAD_MIME_TYPE, 'text/xml') !== false);

        $url = find_script('opensearch') . '?type=suggest&request=abc';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(is_array(json_decode($data)));
        global $HTTP_DOWNLOAD_MIME_TYPE;
        $this->assertTrue(strpos($HTTP_DOWNLOAD_MIME_TYPE, 'application/x-suggestions+json') !== false);
    }

    public function testKeywordSummary()
    {
        require_code('search');
        $results = perform_keyword_search(array('downloads', 'news'));
        $this->assertTrue(is_array($results));
    }
}

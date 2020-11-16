<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

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

        require_code('xml');
    }

    public function testNoSearchHooksCrash()
    {
        require_code('database_search');

        $content = 'qwertyuiop';
        list($content_where) = build_content_where($content, false);
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

        $_hooks = find_all_hook_obs('modules', 'search', 'Hook_search_');
        foreach ($_hooks as $ob) {
            $info = $ob->info();
            if (($info === null) || ($info === false)) {
                continue;
            }

            if (php_function_allowed('set_time_limit')) {
                @set_time_limit(10); // Prevent errant search hooks (easily written!) taking down a server. Each call given 10 seconds (calling set_time_limit resets the timer).
            }
            $hook_results = $ob->run($content, $content_where, $where_clause, $search_under, $only_search_meta, $only_titles, $max, $start, $sort, $direction, $author, $author_id, $cutoff);
            $this->assertTrue(is_array($hook_results));
        }
    }

    public function testHttpGuestFullSearch()
    {
        $this->establish_admin_session();

        require_lang('search');

        $url_parts = ['page' => 'search', 'type' => 'results', 'content' => 'qwertyuiopxxx'];

        $_hooks = find_all_hooks('modules', 'search');
        foreach (array_keys($_hooks) as $hook) {
            $url_parts['search_' . $hook] = 1;
        }

        foreach ([0, 1] as $safe_mode) {
            foreach (['admin', 'test'] as $username) {
                $url = build_url($url_parts + ['keep_su' => $username, 'keep_safe_mode' => $safe_mode], get_module_zone('search'));
                $data = http_get_contents($url->evaluate(), ['cookies' => [get_session_cookie() => get_session_id()]]);
                $this->assertTrue(strpos($data, do_lang('NO_RESULTS_SEARCH')) !== false); // We expect no results, but also no crash!
            }
        }
    }

    public function testOpenSearch()
    {
        $session_id = $this->establish_admin_callback_session();

        $url = find_script('opensearch');
        $data = cms_http_request($url, ['cookies' => [get_session_cookie() => $session_id]]);
        $parsed = new CMS_simple_xml_reader($data->data);
        $this->assertTrue(strpos($data->download_mime_type, 'text/xml') !== false);

        $url = find_script('opensearch') . '?type=suggest&request=abc';
        $data = cms_http_request($url, ['convert_to_internal_encoding' => true, 'cookies' => [get_session_cookie() => $session_id]]);
        $this->assertTrue(is_array(json_decode($data->data)));
        $this->assertTrue(strpos($data->download_mime_type, 'application/x-suggestions+json') !== false);
    }

    public function testKeywordSummary()
    {
        require_code('search');
        $results = perform_keyword_search(['downloads', 'news']);
        $this->assertTrue(is_array($results));
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class ___search_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();

        require_code('xml');
    }

    public function testLoremSearch()
    {
        $this->establish_admin_session();

        require_code('lorem');
        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('news', 'id', ['title' => $GLOBALS['SITE_DB']->translate_field_ref(lorem_phrase())]);
        if ($test === null) {
            require_code('setupwizard');
            install_test_content();
        }

        require_code('database_search');

        // Set up our test cases; each array item is a test case with a map of run parameters to use
        // _name is a label for the test, and _no_results is an array of search hooks we expect no results (null: no results from all hooks)
        $subtests = [
            'no_filters' => [
                '_no_results' => [],
                'only_search_meta' => false,
                'only_titles' => false,
                'author' => '',
                'author_id' => null,
                'cutoff' => null,
                'search_under' => '!',
            ],
            'blank_under_root' => [
                '_no_results' => [],
                'only_search_meta' => false,
                'only_titles' => false,
                'author' => '',
                'author_id' => null,
                'cutoff' => null,
                'search_under' => '',
            ],
            'titles_only' => [
                '_no_results' => [],
                'only_search_meta' => false,
                'only_titles' => true,
                'author' => '',
                'author_id' => null,
                'cutoff' => null,
                'search_under' => '!',
            ],
            'meta_only' => [
                '_no_results' => false, // Whether we get results really depends what keywords were picked out by our algorithm, we just don't want it to crash
                'only_search_meta' => true,
                'only_titles' => false,
                'author' => '',
                'author_id' => null,
                'cutoff' => null,
                'search_under' => '!',
            ],
            'author_test_username' => [
                '_no_results' => null, // Expect no results from all
                'only_search_meta' => false,
                'only_titles' => false,
                'author' => $this->get_canonical_username('test'),
                'author_id' => null,
                'cutoff' => null,
                'search_under' => '!',
            ],
            'author_admin_id' => [
                '_no_results' => [],
                'only_search_meta' => false,
                'only_titles' => false,
                'author' => '',
                'author_id' => $this->get_canonical_member_id('admin'),
                'cutoff' => null,
                'search_under' => '!',
            ],
            'cutoff_now' => [
                '_no_results' => null, // Expect no results from all
                'only_search_meta' => false,
                'only_titles' => false,
                'author' => '',
                'author_id' => null,
                'cutoff' => time(),
                'search_under' => '!',
            ],
        ];

        // TODO: Test template search of catalogue_entries
        // TODO: Test template search of cns_members
        // TODO: Test template search of a custom field on downloads
        // TODO: Test with both non-admin and admin access
        // TODO: Test with both full text search and Composr fast custom index search
        // TODO: cns_clubs is not searching due to lack of test content, add some lorem content - or perhaps actually make cns_clubs into cns_groups
        // TODO: Create test code and searchability for filedump

        // These parameters are used in all test cases
        $direction = 'DESC';
        $max = 1;
        $start = 0;
        $sort = 'relevance';
        $where_clause = '';

        $_hooks = find_all_hook_obs('modules', 'search', 'Hook_search_');

        foreach ($subtests as $test_name => $test_details) {
            foreach ($_hooks as $hook => $ob) {
                if (($this->only !== null) && ($this->only != 'testLoremSearch') && ($this->only != $test_name) && ($this->only != $hook) && ($this->only != $test_name . '|' . $hook)) {
                    continue;
                }

                $content = '"' . lorem_word() . '"'; // Implies the site was installed with test content, and searches in boolean mode
                $author_id = $test_details['author_id'];
                $search_under = $test_details['search_under'];
                $no_results = $test_details['_no_results'];

                switch ($hook) {
                    case 'cns_own_pt':
                        if ($test_name == 'author_admin_id') {
                            $author_id = $this->get_canonical_member_id('test');
                        }
                        break;

                    case 'cns_members':
                        $content = '"' . $this->get_canonical_username('admin') . '"';
                        break;

                    case 'filedump':
                        $no_results = false; // Lack of test data
                        break;

                    case 'wiki_posts':
                        if ($test_name == 'titles_only') {
                            $no_results = false; // There are no titles
                        }
                        break;

                    case 'tutorials_external':
                        $no_results = false; // Lack of test data
                        break;
                }

                // Needed to force some to run that are disabled to run
                $_GET['search_' . $hook] = '1';
                $_GET['search_under'] = $search_under;

                $info = $ob->info();
                if (($info === null) || ($info === false)) {
                    continue;
                }

                if (php_function_allowed('set_time_limit')) {
                    @set_time_limit(10); // Prevent errant search hooks (easily written!) taking down a server. Each call given 10 seconds (calling set_time_limit resets the timer).
                }

                list($content_where) = build_content_where($content, false);

                $hook_results = $ob->run($content, $content_where, $where_clause, $search_under, $test_details['only_search_meta'], $test_details['only_titles'], $max, $start, $sort, $direction, $test_details['author'], $author_id, $test_details['cutoff']);

                // Test that the hook did not crash (bail from the rest of this test if it did)
                $is_array = is_array($hook_results);
                $this->assertTrue($is_array, 'Test ' . $test_name . ', hook ' . $hook . ': it crashed!');
                if (!$is_array) {
                    continue;
                }

                // Result set count check
                $results_count = count($hook_results);
                if ($no_results !== false) {
                    if (($no_results !== null) && !in_array($hook, $no_results)) {
                        $this->assertTrue(($results_count > 0), 'Test ' . $test_name . ', hook ' . $hook . ': did not return any results when it was expected.');
                    } else {
                        $this->assertTrue(($results_count == 0), 'Test ' . $test_name . ', hook ' . $hook . ': returned results when it was not expected.');
                    }
                }
                if ($results_count == 0) {
                    continue;
                }

                // Test that the first result has a data property (bail from the rest of this test otherwise)
                $has_data = array_key_exists('data', $hook_results[0]);
                $this->assertTrue($has_data, 'Test ' . $test_name . ', hook ' . $hook . ': The first result from run() had no data key.');
                if (!$has_data) {
                    continue;
                }

                // Test that the data is an object/Tempcode (bail if not)
                $is_array = is_array($hook_results[0]['data']);
                $this->assertTrue($is_array, 'Test ' . $test_name . ', hook ' . $hook . ': The data key on the first result from run() is not an array.');
                if (!$is_array) {
                    continue;
                }

                // Test that the first result will render
                $hook_render = $ob->render($hook_results[0]['data']);
                $this->assertTrue(is_object($hook_render), 'Test ' . $test_name . ', hook ' . $hook . ': render() for the first result did not return an object / Tempcode.');
            }
        }
    }

    public function testHttpGuestFullSearch()
    {
        if (($this->only !== null) && ($this->only != 'testHttpGuestFullSearch')) {
            return;
        }

        $this->establish_admin_session();

        require_lang('search');

        $url_parts = ['page' => 'search', 'type' => 'results', 'content' => 'csd+icsblcbAKBXnhui3fh3o4'];

        $_hooks = find_all_hooks('modules', 'search');
        foreach (array_keys($_hooks) as $hook) {
            $url_parts['search_' . $hook] = 1;
        }

        foreach ([0, 1] as $safe_mode) {
            foreach ([$this->get_canonical_username('admin'), $this->get_canonical_username('test')] as $username) {
                $url = build_url($url_parts + ['keep_su' => $username, 'keep_safe_mode' => $safe_mode], get_module_zone('search'));
                $data = http_get_contents($url->evaluate(), ['cookies' => [get_session_cookie() => get_session_id()]]);
                $this->assertTrue(strpos($data, do_lang('NO_RESULTS_SEARCH')) !== false, 'Got unexpected results for ' . $username . ($safe_mode ? ' in safe mode' : '')); // We expect no results, but also no crash!
            }
        }
    }

    public function testOpenSearch()
    {
        if (($this->only !== null) && ($this->only != 'testOpenSearch')) {
            return;
        }

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
        if (($this->only !== null) && ($this->only != 'testKeywordSummary')) {
            return;
        }

        require_code('search');
        $results = perform_keyword_search(['downloads', 'news']);
        $this->assertTrue(is_array($results));
    }
}

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
class search_test_set extends cms_test_case
{
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

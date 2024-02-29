<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

// This test will likely fail if using Cloudflare or a proxy

/**
 * Composr test case class (unit testing).
 */
class rest_test_set extends cms_test_case
{
    protected $path = null;
    protected $session_id = null;

    public function setUp()
    {
        parent::setUp();

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        $this->session_id = $this->establish_admin_callback_session();

        // This is needed for the default news categories to be discovered in the alternative_ids table
        require_code('commandr_fs');
        $fs = new Commandr_fs();
        $fs->listing(['var', 'news']);

        if ($this->path === null) {
            $this->path = '/var/news/general/hello' . substr(md5(uniqid('', true)), 0, 10) . '.cms';
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL);
    }

    public function testCreate()
    {
        if (in_safe_mode()) {
            return;
        }

        $url = get_base_url() . '/data/endpoint.php/content/commandr_fs' . $this->path;
        $post_params = json_encode(['summary' => 'test']);
        $cookies = [get_session_cookie() => $this->session_id];
        $http_verb = 'POST';
        $raw_content_type = 'application/json';
        $result = http_get_contents($url, ['timeout' => 10.0, 'convert_to_internal_encoding' => true, 'ignore_http_status' => $this->debug, 'post_params' => $post_params, 'cookies' => $cookies, 'http_verb' => $http_verb, 'raw_content_type' => $raw_content_type]);
        $_result = @json_decode($result, true);
        $this->assertTrue(is_array($_result));
        if (is_array($_result)) {
            if ($this->debug) {
                // NB: Could fail if the 'General' news category is renamed/deleted
                @var_dump($_result);
                if (!$_result['success']) {
                    @exit('Failed on creating ' . $url);
                }
            }

            $this->assertTrue($_result['success']);
        } else {
            if ($this->debug) {
                @exit($result);
            }
        }
    }

    public function testUpdate()
    {
        if (in_safe_mode()) {
            return;
        }

        $url = get_base_url() . '/data/endpoint.php/content/commandr_fs' . $this->path;
        $post_params = json_encode(['summary' => 'test']);
        $cookies = [get_session_cookie() => $this->session_id];
        $http_verb = 'PUT';
        $raw_content_type = 'application/json';
        $result = http_get_contents($url, ['timeout' => 10.0, 'convert_to_internal_encoding' => true, 'ignore_http_status' => $this->debug, 'post_params' => $post_params, 'cookies' => $cookies, 'http_verb' => $http_verb, 'raw_content_type' => $raw_content_type]);
        $_result = @json_decode($result, true);
        $this->assertTrue(is_array($_result));
        if (is_array($_result)) {
            if ($this->debug) {
                @var_dump($_result);
            }

            $this->assertTrue($_result['success']);
        } else {
            if ($this->debug) {
                @exit($result);
            }
        }
    }

    public function testDelete()
    {
        if (in_safe_mode()) {
            return;
        }

        $url = get_base_url() . '/data/endpoint.php/content/commandr_fs' . $this->path;
        $post_params = json_encode(['summary' => 'test']);
        $cookies = [get_session_cookie() => $this->session_id];
        $http_verb = 'DELETE';
        $raw_content_type = 'application/json';
        $result = http_get_contents($url, ['timeout' => 10.0, 'convert_to_internal_encoding' => true, 'ignore_http_status' => $this->debug, 'post_params' => $post_params, 'cookies' => $cookies, 'http_verb' => $http_verb, 'raw_content_type' => $raw_content_type]);
        $_result = @json_decode($result, true);
        $this->assertTrue(is_array($_result));
        if (is_array($_result)) {
            if ($this->debug) {
                @var_dump($_result);
            }

            $this->assertTrue($_result['success']);
        } else {
            if ($this->debug) {
                @exit($result);
            }
        }
    }
}

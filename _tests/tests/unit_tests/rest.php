<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class rest_test_set extends cms_test_case
{
    protected $path = null;

    public function setUp()
    {
        parent::setUp();

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        $this->establish_admin_session();

        // This is needed for the default news categories to be discovered in the alternative_ids table
        require_code('commandr_fs');
        $fs = new Commandr_fs();
        $fs->listing(array('var', 'news'));

        if ($this->path === null) {
            $this->path = '/var/news/general/hello' . substr(md5(uniqid('', true)), 0, 10) . '.cms';
        }
    }

    public function testCreate()
    {
        if (in_safe_mode()) {
            return;
        }

        $url = get_base_url() . '/data/endpoint.php/content/commandr_fs' . $this->path;
        $post_params = array(json_encode(array('summary' => 'test')));
        $cookies = array(get_session_cookie() => get_session_id());
        $raw_post = true;
        $http_verb = 'POST';
        $raw_content_type = 'application/json';
        $debug = (get_param_integer('debug', 0) == 1);
        $result = http_get_contents($url, array('ignore_http_status' => $debug, 'post_params' => $post_params, 'cookies' => $cookies, 'raw_post' => $raw_post, 'http_verb' => $http_verb, 'raw_content_type' => $raw_content_type));
        $_result = @json_decode($result, true);
        $this->assertTrue(is_array($_result));
        if (is_array($_result)) {
            if ($debug) {
                @var_dump($_result);
                if (!$_result['success']) {
                    exit();
                }
            }

            $this->assertTrue($_result['success']);
        } else {
            if ($debug) {
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
        $post_params = array(json_encode(array('summary' => 'test')));
        $cookies = array(get_session_cookie() => get_session_id());
        $raw_post = true;
        $http_verb = 'PUT';
        $raw_content_type = 'application/json';
        $debug = (get_param_integer('debug', 0) == 1);
        $result = http_get_contents($url, array('ignore_http_status' => $debug, 'post_params' => $post_params, 'cookies' => $cookies, 'raw_post' => $raw_post, 'http_verb' => $http_verb, 'raw_content_type' => $raw_content_type));
        $_result = @json_decode($result, true);
        $this->assertTrue(is_array($_result));
        if (is_array($_result)) {
            if ($debug) {
                @var_dump($_result);
            }

            $this->assertTrue($_result['success']);
        } else {
            if ($debug) {
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
        $post_params = array(json_encode(array('summary' => 'test')));
        $cookies = array(get_session_cookie() => get_session_id());
        $raw_post = true;
        $http_verb = 'DELETE';
        $raw_content_type = 'application/json';
        $debug = (get_param_integer('debug', 0) == 1);
        $result = http_get_contents($url, array('ignore_http_status' => $debug, 'post_params' => $post_params, 'cookies' => $cookies, 'raw_post' => $raw_post, 'http_verb' => $http_verb, 'raw_content_type' => $raw_content_type));
        $_result = @json_decode($result, true);
        $this->assertTrue(is_array($_result));
        if (is_array($_result)) {
            if ($debug) {
                @var_dump($_result);
            }

            $this->assertTrue($_result['success']);
        } else {
            if ($debug) {
                @exit($result);
            }
        }
    }
}

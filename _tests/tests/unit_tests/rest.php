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
class rest_test_set extends cms_test_case
{
    protected $path;

    public function setUp()
    {
        $this->establish_admin_session();

        $this->path = 'var/news/general/Hello.cms';

        require_code('commandr_fs');
        $fs = new Commandr_fs();
        $fs->listing(array('var', 'news'));

        parent::setUp();
    }

    public function testCreate()
    {
        $url = get_base_url() . '/data/endpoint.php/content/commandr_fs/' . $this->path;
        $post_params = array(json_encode(array('summary' => 'test')));
        $cookies = array(get_session_cookie() => get_session_id());
        $raw_post = true;
        $http_verb = 'POST';
        $raw_content_type = 'application/json';
        $result = http_download_file($url, null, true, false, 'Composr', $post_params, $cookies, null, null, null, null, null, null, 6.0, $raw_post, null, null, $http_verb, $raw_content_type);
        $_result = json_decode($result, true);
        $this->assertTrue($_result['success']);
    }

    public function testUpdate()
    {
        $url = get_base_url() . '/data/endpoint.php/content/commandr_fs/' . $this->path;
        $post_params = array(json_encode(array('summary' => 'test')));
        $cookies = array(get_session_cookie() => get_session_id());
        $raw_post = true;
        $http_verb = 'PUT';
        $raw_content_type = 'application/json';
        $result = http_download_file($url, null, true, false, 'Composr', $post_params, $cookies, null, null, null, null, null, null, 6.0, $raw_post, null, null, $http_verb, $raw_content_type);
        $_result = json_decode($result, true);
        $this->assertTrue($_result['success']);
    }

    public function testDelete()
    {
        $url = get_base_url() . '/data/endpoint.php/content/commandr_fs/' . $this->path;
        $post_params = array(json_encode(array('summary' => 'test')));
        $cookies = array(get_session_cookie() => get_session_id());
        $raw_post = true;
        $http_verb = 'DELETE';
        $raw_content_type = 'application/json';
        $result = http_download_file($url, null, true, false, 'Composr', $post_params, $cookies, null, null, null, null, null, null, 6.0, $raw_post, null, null, $http_verb, $raw_content_type);
        $_result = json_decode($result, true);
        $this->assertTrue($_result['success']);
    }
}

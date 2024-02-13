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

/**
 * Composr test case class (unit testing).
 */
class downloads_http_cycle_test_set extends cms_test_case
{
    protected $session_id = null;

    public function setUp()
    {
        parent::setUp();

        $this->session_id = $this->establish_admin_callback_session();
    }

    public function testUpload()
    {
        require_code('uploads');
        require_code('csrf_filter');

        $url = build_url(['page' => 'cms_downloads', 'type' => '_add', 'keep_fatalistic' => 1], 'cms');
        $post_params = [
            'download_name' => 'Test' . uniqid('', true),
            'csrf_token' => generate_csrf_token(),
            'category_id' => strval(db_get_first_id()),
            'author' => 'Test',
            'description' => '',
            'additional_details' => '',
            'url_redirect' => '',
            'validated' => '1',
        ];
        $files = [
            'file__upload' => get_file_base() . '/data/images/donate.png',
        ];
        $data = http_get_contents($url->evaluate(), ['ignore_http_status' => $this->debug, 'trigger_error' => false, 'post_params' => $post_params, 'cookies' => [get_session_cookie() => $this->session_id], 'files' => $files, 'timeout' => 100.0]);
        if ($this->debug) {
            @var_dump($data);
            exit();
        }
        $this->assertTrue(is_string($data), 'Got non-string result for upload');
    }

    public function testDownload()
    {
        set_option('immediate_downloads', '0');

        $max_download_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'MAX(id)');
        if ($max_download_id === null) {
            return;
        }
        $url = find_script('dload') . '?id=' . strval($max_download_id);
        $result = cms_http_request($url, ['cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue($result->data == cms_file_get_contents_safe(get_file_base() . '/data/images/donate.png', FILE_READ_LOCK));
        $this->assertTrue($result->download_mime_type == 'application/octet-stream', 'Wrong mime type, ' . $result->download_mime_type);
        $this->assertTrue($result->filename == 'donate.png', 'Wrong filename, ' . $result->filename);
    }
}

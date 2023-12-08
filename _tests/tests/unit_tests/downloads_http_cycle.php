<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class downloads_http_cycle_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        $this->establish_admin_session();
    }

    public function testUpload()
    {
        require_code('uploads');

        $url = build_url(array('page' => 'cms_downloads', 'type' => '_add', 'keep_fatalistic' => 1), 'cms');
        $post_params = array(
            'name' => 'Test' . uniqid('', true),
            'csrf_token' => get_session_id(),
            'category_id' => strval(db_get_first_id()),
            'author' => 'Test',
            'description' => '',
            'additional_details' => '',
            'url_redirect' => '',
            'validated' => '1',
        );
        $files = array(
            'file__upload' => get_file_base() . '/data/images/donate.png',
        );
        http_download_file($url->evaluate(), null, true, false, 'Composr', $post_params, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 6.0, false, $files);
    }

    public function testDownload()
    {
        set_option('immediate_downloads', '0');

        $max_download_id = $GLOBALS['SITE_DB']->query_select_value('download_downloads', 'MAX(id)');
        $url = find_script('dload') . '?id=' . strval($max_download_id);
        $result = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue($result == file_get_contents(get_file_base() . '/data/images/donate.png'));
        global $HTTP_DOWNLOAD_MIME_TYPE, $HTTP_FILENAME;
        $this->assertTrue($HTTP_DOWNLOAD_MIME_TYPE == 'application/octet-stream');
        $this->assertTrue($HTTP_FILENAME == 'donate.png');
    }
}

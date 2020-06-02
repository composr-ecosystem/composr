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
}

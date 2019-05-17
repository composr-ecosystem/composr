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
class http_test_set extends cms_test_case
{
    public function testSimple()
    {
        $result = cms_http_request('http://example.com/');
        $this->assertTrue(strpos($result->data, 'Example Domain') !== false);
    }

    public function testSimpleHttps()
    {
        $result = cms_http_request('https://example.com/');
        $this->assertTrue(strpos($result->data, 'Example Domain') !== false);
    }

    public function testHead()
    {
        $result = cms_http_request('http://example.com/', array('byte_limit' => 0));
        $this->assertTrue($result->data !== null);
    }

    public function testHeadHttps()
    {
        $result = cms_http_request('https://example.com/', array('byte_limit' => 0));
        $this->assertTrue($result->data !== null);
    }

    public function testFail()
    {
        $result = cms_http_request('http://fdsdsfdsjfdsfdgfdgdf.com/', array('trigger_error' => false));
        $this->assertTrue($result->data === null, 'Invalid domain producing a result; maybe your ISPs DNS mucks about and you need to disable that in their preferences somehow');
    }

    public function testFailHttps()
    {
        $result = cms_http_request('https://fdsdsfdsjfdsfdgfdgdf.com/', array('trigger_error' => false));
        $this->assertTrue($result->data === null);
    }

    public function testRedirect()
    {
        $result = cms_http_request('http://jigsaw.w3.org/HTTP/300/301.html');
        $this->assertTrue(strpos($result->data, 'Redirect test page') !== false);
    }

    public function testRedirectHttps()
    {
        $result = cms_http_request('https://jigsaw.w3.org/HTTP/300/301.html');
        $this->assertTrue(strpos($result->data, 'Redirect test page') !== false);
    }

    public function testRedirectDisabled()
    {
        $result = cms_http_request('https://jigsaw.w3.org/HTTP/300/301.html', array('no_redirect' => true));
        $this->assertTrue(strpos($result->data, 'Redirect test page') === false);
    }

    public function testHttpAuth()
    {
        $result = cms_http_request('https://jigsaw.w3.org/HTTP/Basic/', array('auth' => array('guest', 'guest')));
        $this->assertTrue(strpos($result->data, 'Your browser made it!') !== false);
    }

    public function testWriteToFile()
    {
        $write_path = cms_tempnam();
        $write = fopen($write_path, 'wb');
        $result = cms_http_request('http://example.com/', array('write_to_file' => $write));
        $this->assertTrue(strpos(file_get_contents($write_path), 'Example Domain') !== false);
        fclose($write);
        unlink($write_path);
    }

    public function testWriteToFileHttps()
    {
        $write_path = cms_tempnam();
        $write = fopen($write_path, 'wb');
        $result = cms_http_request('https://example.com/', array('write_to_file' => $write));
        $this->assertTrue(strpos(file_get_contents($write_path), 'Example Domain') !== false);
        fclose($write);
        unlink($write_path);
    }
}

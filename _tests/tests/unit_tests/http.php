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
class http_test_set extends cms_test_case
{
    public function testSimple()
    {
        $result = http_download_file('http://example.com/');
        $this->assertTrue($result !== null && strpos($result, 'Example Domain') !== false);
    }

    public function testSimpleHttps()
    {
        $result = http_download_file('https://example.com/');
        $this->assertTrue($result !== null && strpos($result, 'Example Domain') !== false);
    }

    public function testHead()
    {
        $this->assertTrue(http_download_file('http://example.com/', 0) !== null);
    }

    public function testHeadHttps()
    {
        $this->assertTrue(http_download_file('https://example.com/', 0) !== null);
    }

    public function testFail()
    {
        $this->assertTrue(http_download_file('http://fdsdsfdsjfdsfdgfdgdf.com/', null, false) === null);
    }

    public function testFailHttps()
    {
        $this->assertTrue(http_download_file('https://fdsdsfdsjfdsfdgfdgdf.com/', null, false) === null);
    }

    public function testRedirect()
    {
        $result = http_download_file('http://jigsaw.w3.org/HTTP/300/301.html', null, false);
        $this->assertTrue($result !== null && strpos($result, 'Redirect test page') !== false);
    }

    public function testRedirectHttps()
    {
        $result = http_download_file('https://jigsaw.w3.org/HTTP/300/301.html', null, false);
        $this->assertTrue($result !== null && strpos($result, 'Redirect test page') !== false);
    }

    public function testRedirectDisabled()
    {
        $result = http_download_file('https://jigsaw.w3.org/HTTP/300/301.html', null, false, true);
        $this->assertTrue($result === null);
    }

    public function testHttpAuth()
    {
        $result = http_download_file('https://jigsaw.w3.org/HTTP/Basic/', null, false, true, 'Composr', null, null, null, null, null, null, null, array('guest', 'guest'));
        $this->assertTrue($result !== null && strpos($result, 'Your browser made it!') !== false);
    }

    public function testWriteToFile()
    {
        $write_path = cms_tempnam();
        $write = fopen($write_path, 'wb');
        $result = http_download_file('http://example.com/', null, false, true, 'Composr', null, null, null, null, null, $write);
        $this->assertTrue($result !== null && strpos(file_get_contents($write_path), 'Example Domain') !== false);
        fclose($write);
        unlink($write_path);
    }

    public function testWriteToFileHttps()
    {
        $write_path = cms_tempnam();
        $write = fopen($write_path, 'wb');
        $result = http_download_file('https://example.com/', null, false, true, 'Composr', null, null, null, null, null, $write);
        $this->assertTrue($result !== null && strpos(file_get_contents($write_path), 'Example Domain') !== false);
        fclose($write);
        unlink($write_path);
    }
}

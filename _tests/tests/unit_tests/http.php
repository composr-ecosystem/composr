<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class http_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);
    }

    public function testProxyServer()
    {
        // This test ONLY runs if called explicitly because it requires a proxy to be set up on localhost
        // Configure roughly as follows:
        //  sudo a2enmod proxy proxy_http
        //  Edit /etc/apache2/mods-enabled/proxy.conf
        //   https://www.techrepublic.com/article/save-money-and-provide-security-with-apache-as-a-proxy-server/
        //   https://stackoverflow.com/questions/5011102/apache-reverse-proxy-with-basic-authentication
        //   https://www.web2generators.com/apache-tools/htpasswd-generator
        if (($this->only === null) || ($this->only != 'proxy')) {
            return;
        }

        set_option('proxy', '127.0.0.1');
        set_option('proxy_port', '8080');
        set_option('proxy_user', 'test');
        set_option('proxy_password', 'test');

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $options = [];
            $options['trigger_error'] = false;
            $options['force_' . $implementation] = true;
            $result = cms_http_request('http://example.com', $options);
            $this->assertTrue($result->data !== null && strpos($result->data, 'Example Domain') !== false, 'Failed on ' . $implementation);
        }

        set_option('proxy', '');
    }

    public function testSimpleLocalForceImplementations()
    {
        if (($this->only !== null) && ($this->only != 'local')) {
            return;
        }

        $url = get_base_url() . '/data/index.html';

        $handlers = ['curl', 'filesystem'];

        if (strpos($url, 'https://') === false) {
            // May not be reliable for HTTPS
            $handlers = array_merge($handlers, ['sockets', 'file_wrapper']);
        }

        foreach ($handlers as $implementation) {
            $options = [];
            $options['trigger_error'] = false;
            $options['force_' . $implementation] = true;
            $result = cms_http_request($url, $options);
            $this->assertTrue(is_string($result->data) && $result->data == '', 'Failed on ' . $implementation);
        }
    }

    public function testSimple()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        $result = cms_http_request('http://example.com/', ['trigger_error' => false]);
        $this->assertTrue($result->data !== null && strpos($result->data, 'Example Domain') !== false);
    }

    public function testSimpleHttps()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        $result = cms_http_request('https://example.com/', ['trigger_error' => false]);
        $this->assertTrue($result->data !== null && strpos($result->data, 'Example Domain') !== false);
    }

    public function testHead()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        $result = cms_http_request('http://example.com/', ['byte_limit' => 0, 'trigger_error' => false]);
        $this->assertTrue($result->data !== null);
    }

    public function testHeadHttps()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        $result = cms_http_request('https://example.com/', ['byte_limit' => 0, 'trigger_error' => false]);
        $this->assertTrue($result->data !== null);
    }

    public function testFail()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        $result = cms_http_request('http://fdsdsfdsjfdsfdgfdgdf.com/', ['trigger_error' => false]);
        $this->assertTrue($result->data === null, 'Invalid domain producing a result; maybe your ISPs DNS mucks about and you need to disable that in their preferences somehow');
    }

    public function testFailHttps()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        $result = cms_http_request('https://fdsdsfdsjfdsfdgfdgdf.com/', ['trigger_error' => false]);
        $this->assertTrue($result->data === null);
    }

    public function testRedirect()
    {
        if (($this->only !== null) && ($this->only != 'redirects')) {
            return;
        }

        $result = cms_http_request('http://jigsaw.w3.org/HTTP/300/301.html', ['convert_to_internal_encoding' => true, 'trigger_error' => false]);
        $this->assertTrue($result->data !== null && strpos($result->data, 'Redirect test page') !== false);
    }

    public function testRedirectHttps()
    {
        if (($this->only !== null) && ($this->only != 'redirects')) {
            return;
        }

        $result = cms_http_request('https://jigsaw.w3.org/HTTP/300/301.html', ['convert_to_internal_encoding' => true, 'trigger_error' => false]);
        $this->assertTrue($result->data !== null && strpos($result->data, 'Redirect test page') !== false);
    }

    public function testRedirectDisabled()
    {
        if (($this->only !== null) && ($this->only != 'redirects')) {
            return;
        }

        $result = cms_http_request('https://jigsaw.w3.org/HTTP/300/301.html', ['convert_to_internal_encoding' => true, 'no_redirect' => true, 'trigger_error' => false]);
        $this->assertTrue($result->data === null);
    }

    public function testHttpAuth()
    {
        if (($this->only !== null) && ($this->only != 'httpauth')) {
            return;
        }

        $result = cms_http_request('https://jigsaw.w3.org/HTTP/Basic/', ['convert_to_internal_encoding' => true, 'auth' => ['guest', 'guest'], 'trigger_error' => false]);
        $this->assertTrue($result->data !== null && strpos($result->data, 'Your browser made it!') !== false);
    }

    public function testWriteToFile()
    {
        if (($this->only !== null) && ($this->only != 'files')) {
            return;
        }

        $write_path = cms_tempnam();
        $write = fopen($write_path, 'wb');
        $result = cms_http_request('http://example.com/', ['convert_to_internal_encoding' => true, 'write_to_file' => $write, 'trigger_error' => false]);
        $this->assertTrue(strpos(cms_file_get_contents_safe($write_path, FILE_READ_LOCK), 'Example Domain') !== false);
        fclose($write);
        unlink($write_path);
    }

    public function testWriteToFileHttps()
    {
        if (($this->only !== null) && ($this->only != 'files')) {
            return;
        }

        $write_path = cms_tempnam();
        $write = fopen($write_path, 'wb');
        $result = cms_http_request('https://example.com/', ['convert_to_internal_encoding' => true, 'write_to_file' => $write, 'trigger_error' => false]);
        $this->assertTrue(strpos(cms_file_get_contents_safe($write_path, FILE_READ_LOCK), 'Example Domain') !== false);
        fclose($write);
        unlink($write_path);
    }

    /*
    public function testProxy() {
        $old_settings = [
            get_option('proxy'),
            get_option('proxy_port'),
            get_option('proxy_user'),
            get_option('proxy_password')
        ];

        // Proxies come and go all the time; this may need to be updated.
        set_option('proxy', '127.0.0.1');
        set_option('proxy_port', '80');
        set_option('proxy_user', '');
        set_option('proxy_password', '');

        $result = http_get_contents('http://example.com/', ['convert_to_internal_encoding' => true, 'trigger_error' => true]);
        $this->assertTrue(($result !== null) && (strpos($result, 'Example Domain') !== false), 'Proxy test failed. You may need to use a different proxy.' . "\n\n" . ($result !== null ? $result : 'NULL'));

        set_option('proxy', $old_settings[0]);
        set_option('proxy_port', $old_settings[1]);
        set_option('proxy_user', $old_settings[2]);
        set_option('proxy_password', $old_settings[3]);
    }
    */
}

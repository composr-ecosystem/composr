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
class http_base_test_set extends cms_test_case
{
    protected $http_faux_loopback = null;
    public function setUp()
    {
        parent::setUp();

        $this->http_faux_loopback = get_value('http_faux_loopback');
        set_value('http_faux_loopback', '^' . preg_quote(get_base_url(), '#') . '.*\.(html)\??');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);
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
            $message = 'Proxy test needs to be tested manually / explicitly when a proxy is set up. See the test for documentation.';
            $this->dump($message, 'INFO:');
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
            $this->assertTrue($result->data !== null && strpos($result->data, 'Example Domain') !== false, 'Proxy failed on ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'ProxyServer HTTP object for implementation ' . $implementation);
            }
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
        } else {
            $message = 'sockets and file_wrapper skipped due to not being reliable on HTTPS.';
            $this->dump($message, 'INFO:');
        }

        foreach ($handlers as $implementation) {
            $options = [];
            $options['trigger_error'] = false;
            $options['force_' . $implementation] = true;
            $result = cms_http_request($url, $options);
            $this->assertTrue(is_string($result->data) && $result->data == '', 'Failed on ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'SimpleLocalForceImplementations HTTP object for implementation ' . $implementation);
            }
        }
    }

    public function testSimple()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('http://example.com/', ['trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data !== null && strpos($result->data, 'Example Domain') !== false, 'Did not get Example Domain content on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'Simple HTTP object');
            }
        }
    }

    public function testSimpleHttps()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('https://example.com/', ['trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data !== null && strpos($result->data, 'Example Domain') !== false, 'Did not get Example Domain content on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'SimpleHttps HTTP object');
            }
        }
    }

    public function testHead()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('http://example.com/', ['byte_limit' => 0, 'trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data !== null, 'Data returned null when it should not, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'Head HTTP object');
            }
        }
    }

    public function testHeadHttps()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('https://example.com/', ['byte_limit' => 0, 'trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data !== null, 'Data returned null when it should not, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'HeadHttps HTTP object');
            }
        }
    }

    public function testFail()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('http://fdsdsfdsjfdsfdgfdgdf.com/', ['trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data === null, 'Invalid domain producing a result; maybe your ISPs DNS mucks about and you need to disable that in their preferences somehow, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'Fail HTTP object');
            }
        }
    }

    public function testFailHttps()
    {
        if (($this->only !== null) && ($this->only != 'broad')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('https://fdsdsfdsjfdsfdgfdgdf.com/', ['trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data === null, 'Expected null data but instead got some, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'FailHttps HTTP object');
            }
        }
    }

    public function testRedirect()
    {
        if (($this->only !== null) && ($this->only != 'redirects')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('http://jigsaw.w3.org/HTTP/300/301.html', ['convert_to_internal_encoding' => true, 'trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data !== null && strpos($result->data, 'Redirect test page') !== false, 'Expected Redirect test page but did not get it, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'Redirect HTTP object');
            }
        }
    }

    public function testRedirectHttps()
    {
        if (($this->only !== null) && ($this->only != 'redirects')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('https://jigsaw.w3.org/HTTP/300/301.html', ['convert_to_internal_encoding' => true, 'trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data !== null && strpos($result->data, 'Redirect test page') !== false, 'Expected Redirect test page but did not get it, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'RedirectHttps HTTP object');
            }
        }
    }

    public function testRedirectDisabled()
    {
        if (($this->only !== null) && ($this->only != 'redirects')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('https://jigsaw.w3.org/HTTP/300/301.html', ['convert_to_internal_encoding' => true, 'no_redirect' => true, 'trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data === null, 'Expected null data but got some, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'RedirectDisabled HTTP object');
            }
        }
    }

    public function testHttpAuth()
    {
        if (($this->only !== null) && ($this->only != 'httpauth')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $result = cms_http_request('https://jigsaw.w3.org/HTTP/Basic/', ['convert_to_internal_encoding' => true, 'auth' => ['guest', 'guest'], 'trigger_error' => false, ('force_' . $implementation) => true]);
            $this->assertTrue($result->data !== null && strpos($result->data, 'Your browser made it!') !== false, 'Expected Http Auth to pass but it did not, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'HttpAuth HTTP object');
            }
        }
    }

    public function testWriteToFile()
    {
        if (($this->only !== null) && ($this->only != 'files')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $write_path = cms_tempnam();
            $write = fopen($write_path, 'wb');
            $result = cms_http_request('http://example.com/', ['convert_to_internal_encoding' => true, 'write_to_file' => $write, 'trigger_error' => false, ('force_' . $implementation) => true]);
            fclose($write);
            $data = cms_file_get_contents_safe($write_path, FILE_READ_LOCK);
            $this->assertTrue(strpos($data, 'Example Domain') !== false, 'Expected file data to contain Example Domain but it did not, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'WriteToFile HTTP object');
                $this->dump($data, 'WriteToFile File contents');
            }
            unlink($write_path);
        }
    }

    public function testWriteToFileHttps()
    {
        if (($this->only !== null) && ($this->only != 'files')) {
            return;
        }

        foreach (['curl', 'sockets', 'file_wrapper'] as $implementation) {
            $write_path = cms_tempnam();
            $write = fopen($write_path, 'wb');
            $result = cms_http_request('https://example.com/', ['convert_to_internal_encoding' => true, 'write_to_file' => $write, 'trigger_error' => false, ('force_' . $implementation) => true]);
            fclose($write);
            $data = cms_file_get_contents_safe($write_path, FILE_READ_LOCK);
            $this->assertTrue(strpos($data, 'Example Domain') !== false, 'Expected file data to contain Example Domain but it did not, on implementation ' . $implementation);
            if ($this->debug) {
                $this->dump($result, 'WriteToFileHttps HTTP object');
                $this->dump($data, 'WriteToFileHttps File contents');
            }
            unlink($write_path);
        }
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

    public function tearDown()
    {
        set_value('http_faux_loopback', $this->http_faux_loopback);

        parent::tearDown();
    }
}

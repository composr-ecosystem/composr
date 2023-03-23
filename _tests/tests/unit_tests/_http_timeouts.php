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

// This test makes sure our assumptions about PHP's timeout facilities are correct.

/*EXTRA FUNCTIONS: curl_.*|fsockopen|socket_set_timeout|stream_select*/

/**
 * Composr test case class (unit testing).
 */
class _http_timeouts_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);
    }

    public function testTimeouts()
    {
        if (!function_exists('curl_init')) {
            $this->assertTrue(false, 'Test only works if cURL is available');
            return;
        }

        $timeout = 3.0;

        // Test timeout not being hit for large file
        $url = 'http://compo.sr/docs/php-5.2.4-ocproducts.zip'; // Must be HTTP; some PHP clients do not have the https wrapper
        $expected_size = 28941943;
        if (($this->only === null) || ($this->only == 'big_curl')) {
            $r1 = $this->_testCurl($url, $timeout);
            $this->assertTrue($r1[0]);
            $this->assertTrue($r1[1] == $expected_size, 'Wrong download size @ ' . strval($r1[1]));
        }
        if (($this->only === null) || ($this->only == 'big_wrapper')) {
            $r2 = $this->_testURLWrappers($url, $timeout);
            $this->assertTrue($r2[0]);
            $this->assertTrue($r2[1] == $expected_size, 'Wrong download size @ ' . strval($r2[1]));
        }
        if ((($this->only === null) || ($this->only == 'big_socket')) && (strpos($url, 'https://') === false)) {
            $r3 = $this->_testFSockOpen($url, $timeout);
            $this->assertTrue($r3[0]);
            $this->assertTrue($r3[1] >= $expected_size, 'Wrong download size @ ' . strval($r3[1]));
        }

        // Test timeout being hit for something that really is timing out
        $url = get_base_url() . '/_tests/sleep.php?timeout=' . float_to_raw_string($timeout + 2);
        if (($this->only === null) || ($this->only == 'timeout_curl')) {
            $r1 = $this->_testCurl($url, $timeout);
            $this->assertTrue(!$r1[0]);
            $this->assertTrue($r1[1] == 0, 'Wrong download size @ ' . strval($r1[1]));
        }
        if (($this->only === null) || ($this->only == 'timeout_wrapper')) {
            $r2 = $this->_testURLWrappers($url, $timeout);
            $this->assertTrue(!$r2[0]);
            $this->assertTrue($r2[1] == 0, 'Wrong download size @ ' . strval($r2[1]));
        }
        if ((($this->only === null) || ($this->only == 'timeout_socket')) && (strpos($url, 'https://') === false)) {
            $r3 = $this->_testFSockOpen($url, $timeout);
            $this->assertTrue(!$r3[0]);
            $this->assertTrue($r3[1] == 0, 'Wrong download size @ ' . strval($r3[1]));
        }
    }

    protected function _testCurl($url, $timeout)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, intval(ceil($timeout)));
        curl_setopt($ch, CURLOPT_LOW_SPEED_LIMIT, 1);
        curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, intval(ceil($timeout)));
        $result = curl_exec($ch);
        curl_close($ch);

        return [is_string($result), is_string($result) ? strlen($result) : 0];
    }

    protected function _testURLWrappers($url, $timeout)
    {
        ini_set('allow_url_fopen', '1');
        ini_set('default_socket_timeout', strval(intval(ceil($timeout))));
        $result = @file_get_contents($url);
        return [is_string($result), is_string($result) ? strlen($result) : 0];
    }

    protected function _testFSockOpen($url, $timeout)
    {
        $errno = 0;
        $errstr = '';
        $result = mixed();
        $result = false;
        $parsed = cms_parse_url_safe($url);
        $fh = fsockopen($parsed['host'], isset($parsed['port']) ? $parsed['port'] : 80, $errno, $errstr, $timeout);
        if ($fh !== false) {
            socket_set_timeout($fh, intval($timeout), fmod($timeout, 1.0) / 1000000.0);
            $out = "GET " . $url . " HTTP/1.1\r\n";
            $out .= "Host: " . $parsed['host'] . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            fwrite($fh, $out);
            $_frh = [$fh];
            $_fwh = null;
            while (!feof($fh)) {
                if (!stream_select($_frh, $_fwh, $_fwh, intval($timeout), fmod($timeout, 1.0) / 1000000.0)) {
                    $result = false;
                    break;
                }

                if ($result === false) {
                    $result = '';
                }
                $_data = fread($fh, 1024);
                if ($_data !== false) {
                    $result .= $_data;
                }
            }
            fclose($fh);
        }

        if ($this->debug) {
            var_dump(gettype($result));
            if (is_string($result)) {
                var_dump(substr($result, 0, 1000));
            }
        }

        return [is_string($result), is_string($result) ? strlen($result) : 0];
    }
}

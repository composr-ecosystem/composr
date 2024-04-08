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

        $timeout = 5.0;

        // Test timeout not being hit for large file
        $url = 'https://composr.app/docs/php-5.2.4-ocproducts.zip' /*TODO: change name of file*/;
        $expected_size = 28941943;
        if (($this->only === null) || ($this->only == 'big_curl')) {
            $r1 = $this->_testCurl($url, $timeout);
            $this->assertTrue($r1[0], 'Got no results for CURL for file download');
            $this->assertTrue($r1[1] == $expected_size, 'CURL wrong file download size @ ' . strval($r1[1]));
        }
        if (($this->only === null) || ($this->only == 'big_wrapper')) {
            $r2 = $this->_testURLWrappers($url, $timeout);
            $this->assertTrue($r2[0], 'Got no results for URL wrappers for file download');
            $this->assertTrue($r2[1] == $expected_size, 'URL wrappers wrong file download size @ ' . strval($r2[1]));
        }
        if ((($this->only === null) || ($this->only == 'big_socket'))) {
            $r3 = $this->_testFSockOpen($url, $timeout);
            $this->assertTrue($r3[0], 'Got no results for FSockOpen for file download');
            $this->assertTrue($r3[1] >= $expected_size, 'FSockOpen wrong file download size @ ' . strval($r3[1]));
        }

        // Test timeout being hit for something that really is timing out
        $url = get_base_url() . '/_tests/sleep.php?timeout=' . float_to_raw_string($timeout + 2);
        if (($this->only === null) || ($this->only == 'timeout_curl')) {
            $r1 = $this->_testCurl($url, $timeout);
            $this->assertTrue(!$r1[0], 'CURL should have timed out but did not');
            $this->assertTrue($r1[1] == 0, 'CURL wrong timeout download size @ ' . strval($r1[1]));
        }
        if (($this->only === null) || ($this->only == 'timeout_wrapper')) {
            $r2 = $this->_testURLWrappers($url, $timeout);
            $this->assertTrue(!$r2[0], 'URl wrappers should have timed out but did not');
            $this->assertTrue($r2[1] == 0, 'URL wrappers wrong timeout download size @ ' . strval($r2[1]));
        }
        if ((($this->only === null) || ($this->only == 'timeout_socket'))) {
            $r3 = $this->_testFSockOpen($url, $timeout);
            $this->assertTrue(!$r3[0], 'FSockOpen should have timed out but did not');
            $this->assertTrue($r3[1] == 0, 'FSockOpen wrong timeout download size @ ' . strval($r3[1]));
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

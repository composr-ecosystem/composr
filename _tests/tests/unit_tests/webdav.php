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

// This test will likely fail if using Cloudflare or a proxy

/**
 * Composr test case class (unit testing).
 */
class webdav_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);
    }

    public function testWebdav()
    {
        $this->assertTrue(empty($GLOBALS['SITE_INFO']['backdoor_ip']), 'Backdoor to IP address present, may break other tests');

        if (!addon_installed('webdav')) {
            $this->assertTrue(false, 'The webdav addon must be installed for this test to run');
            return;
        }

        if (!function_exists('mb_strtoupper')) {
            $this->assertTrue(false, 'mbstring needed');
            return;
        }

        $ht = get_file_base() . '/.htaccess';
        if ((!is_file($ht)) || (strpos(file_get_contents($ht), 'WebDAV implementation') === false)) {
            $this->assertTrue(false, 'root .htaccess file required for WebDAV tests');
            return;
        }

        $session_id = $this->establish_admin_callback_session();
        $guest_cookies = [get_session_cookie() => uniqid('', true)];
        $cookies = [get_session_cookie() => $session_id];

        require_code('http');

        $webdav_filedump_base_url = get_base_url() . '/webdav/filedump';

        // Test access control
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?' . '>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = cms_http_request($webdav_filedump_base_url, [
            'http_verb' => 'PROPFIND',
            'post_params' => $xml,
            'trigger_error' => false,
            'cookies' => $guest_cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue($result->data === null);

        // Test upload
        $file_data = file_get_contents(get_file_base() . '/_tests/assets/media/early_cinema.mp4');
        $result = cms_http_request($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'PUT',
            'post_params' => $file_data,
            'trigger_error' => false,
            'cookies' => $cookies,
            //'ignore_http_status' => true,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue(is_string($result->data));

        // Test folder listing
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?' . '>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
                <D:resourcetype />
            </D:prop>
        </D:propfind>
        ';
        $result = cms_http_request($webdav_filedump_base_url . '/', [
            'http_verb' => 'PROPFIND',
            'post_params' => $xml,
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue($result->data !== null && strpos($result->data, 'early_cinema.mp4') !== false);

        // Test file properties
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?' . '>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = cms_http_request($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'PROPFIND',
            'post_params' => $xml,
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue($result->data !== null && strpos($result->data, '191805') !== false || strpos($result->data, '259941') !== false); // May have higher file-size than actual file due to JSON encoding

        // Test download
        $result = cms_http_request($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'GET',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $_result = json_decode($result->data, true);
        $__result = is_array($_result) ? base64_decode($_result['data']) : $result->data;
        $this->assertTrue($__result === $file_data);

        // Test edit
        $result = cms_http_request($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'PUT',
            'post_params' => str_repeat('x', 12343),
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue(is_string($result->data));

        // Test file properties
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?' . '>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = cms_http_request($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'PROPFIND',
            'post_params' => $xml,
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue($result->data !== null && strpos($result->data, '12343') !== false || strpos($result->data, '16605') !== false); // May have higher file-size than actual file due to JSON encoding

        // Test delete
        $result = cms_http_request($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'DELETE',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue(is_string($result->data));

        // Test folder listing
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?' . '>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = cms_http_request($webdav_filedump_base_url . '/', [
            'http_verb' => 'PROPFIND',
            'post_params' => $xml,
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue($result->data !== null && strpos($result->data, 'early_cinema.mp4') === false);

        deldir_contents(get_custom_file_base() . '/uploads/filedump/xxx123', false, true);

        // Test create folder
        $result = cms_http_request($webdav_filedump_base_url . '/xxx123/', [
            'http_verb' => 'MKCOL',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue(is_string($result->data));

        // Test folder listing
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?' . '>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = cms_http_request($webdav_filedump_base_url . '/', [
            'http_verb' => 'PROPFIND',
            'post_params' => $xml,
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue($result->data !== null && strpos($result->data, 'xxx123') !== false);

        // Test delete folder
        $result = cms_http_request($webdav_filedump_base_url . '/xxx123/', [
            'http_verb' => 'DELETE',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue(is_string($result->data));

        // Test folder listing
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?' . '>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = cms_http_request($webdav_filedump_base_url . '/', [
            'http_verb' => 'PROPFIND',
            'post_params' => $xml,
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        if ($this->debug) {
            var_dump($result->message);
            var_dump($result->data);
        }
        $this->assertTrue($result->data !== null && strpos($result->data, 'xxx123') === false);
    }
}

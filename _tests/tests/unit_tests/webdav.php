<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class webdav_test_set extends cms_test_case
{
    public function testWebdav()
    {
        if (!addon_installed('webdav')) {
            $this->assertTrue(false, 'The webdav addon must be installed for this test to run');
            return;
        }

        $session_id = $this->establish_admin_callback_session();
        $guest_cookies = [get_session_cookie() => uniqid('', true)];
        $cookies = [get_session_cookie() => $session_id];

        require_code('http');

        $webdav_filedump_base_url = get_base_url() . '/webdav/filedump';

        // Test access control
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = http_get_contents($webdav_filedump_base_url, [
            'http_verb' => 'PROPFIND',
            'raw_post' => true,
            'post_params' => [$xml],
            'trigger_error' => false,
            'cookies' => $guest_cookies,
        ]);
        $this->assertTrue($result === null);

        // Test upload
        $file_data = file_get_contents(get_file_base() . '/_tests/assets/media/early_cinema.mp4');
        $result = http_get_contents($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'PUT',
            'raw_post' => true,
            'post_params' => [$file_data],
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(is_string($result));

        // Test folder listing
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = http_get_contents($webdav_filedump_base_url, [
            'http_verb' => 'PROPFIND',
            'raw_post' => true,
            'post_params' => [$xml],
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(strpos($result, 'early_cinema.mp4') !== false);

        // Test file properties
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = http_get_contents($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'PROPFIND',
            'raw_post' => true,
            'post_params' => [$xml],
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(strpos($result, '191805') !== false);

        // Test download
        $result = http_get_contents($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'GET',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue($result == $file_data);

        // Test lock gives no error
        $result = http_get_contents($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'LOCK',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue($result == $file_data);

        // Test unlock gives no error
        $result = http_get_contents($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'UNLOCK',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue($result == $file_data);

        // Test edit
        $result = http_get_contents($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'PUT',
            'raw_post' => true,
            'post_params' => [str_repeat('x', 12343)],
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(is_string($result));

        // Test file properties
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = http_get_contents($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'PROPFIND',
            'raw_post' => true,
            'post_params' => [$xml],
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(strpos($result, '12343') !== false);

        // Test delete
        $result = http_get_contents($webdav_filedump_base_url . '/early_cinema.mp4', [
            'http_verb' => 'DELETE',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(is_string($result));

        // Test folder listing
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = http_get_contents($webdav_filedump_base_url, [
            'http_verb' => 'PROPFIND',
            'raw_post' => true,
            'post_params' => [$xml],
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(strpos($result, 'early_cinema.mp4') === false);

        // Test create folder
        $result = http_get_contents($webdav_filedump_base_url . '/xxx123', [
            'http_verb' => 'MKCOL',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(is_string($result));

        // Test folder listing
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = http_get_contents($webdav_filedump_base_url, [
            'http_verb' => 'PROPFIND',
            'raw_post' => true,
            'post_params' => [$xml],
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(strpos($result, 'xxx123') !== false);

        // Test delete folder
        $result = http_get_contents($webdav_filedump_base_url . '/xxx123', [
            'http_verb' => 'DELETE',
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(is_string($result));

        // Test folder listing
        $xml = '<' . '?xml version="1.0" encoding="utf-8" ?>
        <D:propfind xmlns:D="DAV:">
            <D:prop>
            </D:prop>
        </D:propfind>
        ';
        $result = http_get_contents($webdav_filedump_base_url, [
            'http_verb' => 'PROPFIND',
            'raw_post' => true,
            'post_params' => [$xml],
            'trigger_error' => false,
            'cookies' => $cookies,
        ]);
        $this->assertTrue(strpos($result, 'xxx123') === false);
    }
}

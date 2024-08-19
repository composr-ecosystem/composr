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
class telemetry_test_set extends cms_test_case
{

    public function testErrorTelemetry()
    {
        require_code('version');
        if (!file_exists(get_file_base() . '/data_custom/keys/telemetry-' . float_to_raw_string(cms_version_number(), 2, true) . '.key')) {
            $this->assertTrue(false, 'Missing private key for this version of the software.');
            return;
        }
        if (!file_exists(get_file_base() . '/data_custom/keys/telemetry-' . float_to_raw_string(cms_version_number(), 2, true) . '.pub')) {
            $this->assertTrue(false, 'Missing public key for this version of the software.');
            return;
        }

        require_code('encryption');
        require_code('version');

        $__payload = [
            'website_url' => get_base_url(),
            'error_message' => 'TEST',
            'version' => cms_version_pretty(), // Encrypted and contains full version
        ];
        $_payload = encrypt_data_telemetry(serialize($__payload));
        $_payload['version'] = cms_version_number(); // Decrypted major/minor for use in determining which key pair to use
        $payload = json_encode($_payload);

        $url = get_brand_base_url() . '/data/endpoint.php/cms_homesite/telemetry';
        $error_code = null;
        $error_message = '';
        $response = cms_fsock_request($payload, $url, $error_code, $error_message);

        if ($this->debug) {
            $this->dump($response, 'Homesite response');
        }

        if (($response === null) || ($error_message != '')) {
            $this->assertTrue(false, 'Temeletry failed: ' . $error_message);
            return;
        }

        $this->assertTrue((strpos($response, '{"relayed_error_id":0}') !== false), 'Expected Telemetry to return an error ID of 0 (dummy) but did not.');
    }

    public function testAdminZoneTelemetry()
    {
        $timeout_before = ini_get('default_socket_timeout');
        cms_ini_set('default_socket_timeout', '3');

        require_code('version2');
        $num_members = $GLOBALS['FORUM_DRIVER']->get_num_members();
        $num_hits_per_day = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . get_table_prefix() . 'stats WHERE date_and_time>' . strval(time() - 60 * 60 * 24));
        $url = get_brand_base_url() . '/data/endpoint.php/cms_homesite/user_stats/?url=' . urlencode(get_base_url()) . '&name=' . urlencode(get_site_name()) . '&version=' . urlencode(get_version_dotted()) . '&num_members=' . urlencode(strval($num_members)) . '&num_hits_per_day=' . urlencode(strval($num_hits_per_day));

        require_code('http');
        $response = cms_http_request($url, ['trigger_error' => false]);
        if ($this->debug) {
            $this->dump($response, 'HTTP response');
        }

        $this->assertTrue((($response->data !== null) && (strpos($response->data, '"success":true') !== false)), 'Error when submitting user stats to homesite. Set debug=1 to see output.');

        set_value('last_call_home', strval(time()));
        cms_ini_set('default_socket_timeout', $timeout_before);
    }
}

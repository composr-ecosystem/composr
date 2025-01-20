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
    public function setUp()
    {
        parent::setUp();

        require_code('encryption');
        $status = register_site_telemetry();
        $this->assertTrue($status, 'Telemetry is unavailable (either disabled, not available for this server, or there was a problem registering). Therefore, these tests may fail.');
    }
    public function testErrorTelemetry()
    {
        require_code('version');

        if (!is_encryption_enabled_telemetry()) {
            $this->assertTrue(false, 'Skipped test; telemetry is not enabled or available for this site.');
            return;
        }

        $__payload = [
            'error_message' => 'TEST',
            'version' => cms_version_pretty(), // Encrypted and contains full version
            'website_url' => get_base_url(),
        ];
        $_payload = encrypt_data_site_telemetry(serialize($__payload));
        $payload = json_encode($_payload);

        $url = get_brand_base_url() . '/data/endpoint.php/cms_homesite/telemetry/';
        $error_code = null;
        $error_message = '';
        $response = cms_fsock_request($payload, $url, $error_code, $error_message);

        if ($this->debug) {
            $this->dump($response, 'Homesite response');
        }

        if (($response === null) || ($error_message != '')) {
            $this->assertTrue(false, 'Telemetry failed: ' . $error_message);
            return;
        }

        $this->assertTrue((strpos($response, '"success":true') !== false), 'Expected Telemetry to return success but it did not.');
    }

    public function testAdminZoneTelemetry()
    {
        require_code('version2');

        $count_members = $GLOBALS['FORUM_DRIVER']->get_num_members();
        $count_daily_hits = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . get_table_prefix() . 'stats WHERE date_and_time>' . strval(time() - 60 * 60 * 24));
        $url = get_brand_base_url() . '/data/endpoint.php/cms_homesite/user_stats/';
        $__payload = [
            'version' => cms_version_pretty(),
            'count_members' => $count_members,
            'count_daily_hits' => $count_daily_hits,
        ];
        $_payload = encrypt_data_site_telemetry(serialize($__payload));
        $payload = json_encode($_payload);

        if ($payload === false) {
            $this->assertTrue(false, 'Failed to convert payload to JSON.');
            return;
        }

        $error_code = null;
        $error_message = '';
        $response = cms_fsock_request($payload, $url, $error_code, $error_message, 3.0);
        if (($response === null) || ($error_message != '')) {
            $this->assertTrue(false, 'Error sending statistics. Set debug to see output.');
            if ($this->debug) {
                $this->dump($response, 'RESPONSE');
                $this->dump($error_message, 'ERROR MESSAGE');
            }
        }
    }
}

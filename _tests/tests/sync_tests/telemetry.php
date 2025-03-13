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
    private $brand_base_url = null;
    private $telemetry = '0';
    public function setUp()
    {
        parent::setUp();

        $info = 'There is a bug with this test at this time. It may say you have a few failures, but if no fail messages appear, you can assume this test passed.';
        $this->dump($info, 'INFO');

        if (!addon_installed('cms_homesite')) {
            $this->assertTrue(false, 'This test requires the cms_homesite addon to be installed.');
            return;
        }

        require_code('telemetry');

        if (!is_encryption_available_telemetry()) {
            $this->assertTrue(false, 'Cannot run telemetry tests on this server; missing public software key, or PHP libsodium not available.');
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);

        // Set the brand base URL to our URL; we do this to test the telemetry service locally
        $this->brand_base_url = get_value('rebrand_base_url');
        set_value('rebrand_base_url', get_base_url());

        // Turn telemetry on
        $this->telemetry = get_option('telemetry');
        set_option('telemetry', '2');

        // Try registering this site with telemetry
        $status = register_site_telemetry();
        if (!$status) {
            $this->assertTrue(false, 'Failed to register the site with the local telemetry service.');
            $this->tearDown();
            return;
        }
    }

    public function testEncryptionTelemetry()
    {
        if (!is_encryption_enabled_telemetry()) {
            $this->assertTrue(false, 'Skipped test; telemetry not enabled.');
            return;
        }

        require_code('version');
        require_code('global3');
        require_code('crypt');

        $version = float_to_raw_string(cms_version_number(), 2, true);

        $relative_path = 'data_custom/keys/telemetry-' . $version . '.json';
        $path = get_file_base() . '/' . $relative_path;

        if (!is_file($path)) {
            $this->assertTrue(false, 'Missing key file for this version: ' . $relative_path);
            return;
        }

        $_contents = cms_file_get_contents_safe($path);
        $contents = @json_decode($_contents, true);
        if (!$contents) {
            $this->assertTrue(false, 'Failed to parse ' . $relative_path);
            return;
        }

        $this->assertTrue(isset($contents['private']), 'Missing private key for this version of the software in ' . $relative_path);
        $this->assertTrue(isset($contents['public']), 'Missing public key for this version of the software in ' . $relative_path);

        $test_string = get_secure_random_string(32, CRYPT_BASE64);

        require_code('encryption');
        $payload = encrypt_data_telemetry($test_string);
        $result = decrypt_data_telemetry($payload['nonce'], $payload['encrypted_data'], $payload['encrypted_session_key'], cms_version_number());

        $this->assertTrue($result == $test_string, 'Expected ' . $test_string . ' but got ' . $result);
    }

    public function testErrorTelemetry()
    {
        if (!is_encryption_enabled_telemetry()) {
            $this->assertTrue(false, 'Skipped test; telemetry not enabled.');
            return;
        }

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
        if (!is_encryption_enabled_telemetry()) {
            $this->assertTrue(false, 'Skipped test; telemetry not enabled.');
            return;
        }

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
        if (($response === null) || ($error_message != '')) { // We do not check if Composr actually responded with a success message because there is flood control on it
            $this->assertTrue(false, 'Error sending statistics. Set debug to see output.');
            if ($this->debug) {
                $this->dump($response, 'RESPONSE');
                $this->dump($error_message, 'ERROR MESSAGE');
            }
        }
    }

    public function tearDown()
    {
        // Reset brand base URL
        if ($this->brand_base_url !== null) {
            set_value('rebrand_base_url', $this->brand_base_url);
        } else {
            delete_value('rebrand_base_url');
        }

        // Reset telemetry
        set_option('telemetry', $this->telemetry);

        parent::tearDown();
    }
}

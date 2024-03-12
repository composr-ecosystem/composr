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
class encryption_test_set extends cms_test_case
{
    public function testEncryptionOpenSSL()
    {
        require_code('encryption');

        if (!is_encryption_available()) {
            $this->assertTrue(false, 'openssl needed');
            return;
        }

        set_option('encryption_key', get_file_base() . '/_tests/assets/encryption/public.pem');
        set_option('decryption_key', get_file_base() . '/_tests/assets/encryption/private.pem');

        $in = 'test';
        $passphrase = 'test';

        $out = encrypt_data($in);
        $this->assertTrue($out != $in);

        $this->assertTrue(is_data_encrypted($out));

        $cycled = decrypt_data($out, $passphrase);
        $this->assertTrue($cycled == $in);
    }

    public function testEncryptionTelemetry()
    {
        require_code('version');
        if (!file_exists(get_file_base() . '/data_custom/keys/telemetry-' . float_to_raw_string(cms_version_number()) . '.key')) {
            $this->assertTrue(false, 'Missing private key for this version of the software.');
            return;
        }
        if (!file_exists(get_file_base() . '/data_custom/keys/telemetry-' . float_to_raw_string(cms_version_number()) . '.pub')) {
            $this->assertTrue(false, 'Missing public key for this version of the software.');
            return;
        }

        $test_string = 'Hello world';

        require_code('encryption');
        $payload = encrypt_data_telemetry($test_string);
        $result = decrypt_data_telemetry($payload['nonce'], $payload['encrypted_data'], $payload['encrypted_session_key'], cms_version_number());

        $this->assertTrue($result == $test_string, 'Expected ' . $test_string . ' but got ' . $result);
    }
}

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
class encryption_test_set extends cms_test_case
{
    public function testEncryption()
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
}

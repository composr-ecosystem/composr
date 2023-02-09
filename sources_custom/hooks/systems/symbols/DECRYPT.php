<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    password_censor
 */

/**
 * Hook class.
 */
class Hook_symbol_DECRYPT
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run(array $param) : string
    {
        if (!addon_installed('password_censor')) {
            return '';
        }

        $value = '';

        if (!@cms_empty_safe($param[1])) {
            require_code('encryption');
            if ((is_encryption_enabled()) && (is_data_encrypted($param[0]))) {
                $value = decrypt_data($param[0], $param[1]);
            } else {
                $value = $param[0];
            }
        }

        return $value;
    }
}

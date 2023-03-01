<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_cns_cpf_filter_customers
{
    /**
     * Find which special CPFs to enable.
     *
     * @return array A list of CPFs to enable
     */
    public function to_enable() : array
    {
        if (!addon_installed('composr_homesite_support_credits')) {
            return [];
        }

        require_lang('customers');

        $cpf = [];
        $cpf['ftp_host'] = true;
        $cpf['ftp_path'] = true;
        $cpf['ftp_username'] = true;
        $cpf['ftp_password'] = true;
        $cpf['profession'] = true;
        $cpf['support_credits'] = true;
        $cpf['currency'] = true;
        return $cpf;
    }
}

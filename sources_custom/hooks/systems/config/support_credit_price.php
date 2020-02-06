<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_config_support_credit_price
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return [
            'human_name' => 'SUPPORT_CREDIT_PRICE',
            'type' => 'float',
            'category' => 'FEATURE',
            'group' => 'CUSTOMERS',
            'explanation' => 'CONFIG_OPTION_support_credit_price',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 1,
            'required' => true,
            'public' => false,

            'addon' => 'composr_homesite_support_credits',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        if (!addon_installed('composr_homesite_support_credits')) {
            return null;
        }

        return '5.5';
    }
}

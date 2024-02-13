<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_config_support_credit_tax_code
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'SUPPORT_CREDIT_TAX_CODE',
            'type' => 'tax_code',
            'category' => 'ECOMMERCE_PRODUCTS',
            'group' => 'CREDITS',
            'explanation' => 'CONFIG_OPTION_support_credit_tax_code',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 2,
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
    public function get_default() : ?string
    {
        if (!addon_installed('composr_homesite_support_credits')) {
            return null;
        }

        return '0%';
    }
}

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    bankr
 */

/**
 * Hook class.
 */
class Hook_config_bank_dividend
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'BANK_DIVIDEND',
            'type' => 'integer',
            'category' => 'ECOMMERCE_PRODUCTS',
            'group' => 'BANKING',
            'explanation' => 'CONFIG_OPTION_bank_dividend',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,
            'public' => false,
            'addon' => 'bankr',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('bankr')) {
            return null;
        }

        return '4';
    }
}

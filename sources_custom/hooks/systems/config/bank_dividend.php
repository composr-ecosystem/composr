<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    public function get_details()
    {
        return array(
            'human_name' => 'BANK_DIVIDEND',
            'type' => 'integer',
            'category' => 'POINTSTORE',
            'group' => 'BANKING',
            'explanation' => 'CONFIG_OPTION_bank_dividend',
            'shared_hosting_restricted' => '0',
            'list_options' => '',

            'addon' => 'bankr',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return '4';
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    booking
 */

/**
 * Hook class.
 */
class Hook_config_bookings_show_warnings_for_months
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'BOOKINGS_SHOW_WARNINGS_FOR_MONTHS',
            'type' => 'integer',
            'category' => 'FEATURE',
            'group' => 'BOOKINGS',
            'explanation' => 'CONFIG_OPTION_bookings_show_warnings_for_months',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,

            'addon' => 'booking',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return '6';
    }
}

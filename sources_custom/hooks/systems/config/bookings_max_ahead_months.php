<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class Hook_config_bookings_max_ahead_months
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'BOOKINGS_MAX_AHEAD_MONTHS',
            'type' => 'integer',
            'category' => 'FEATURE',
            'group' => 'BOOKINGS',
            'explanation' => 'CONFIG_OPTION_bookings_max_ahead_months',
            'shared_hosting_restricted' => '0',
            'list_options' => '',

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
        return '36';
    }
}

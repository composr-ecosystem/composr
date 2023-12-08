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
class Hook_config_member_booking_only
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'MEMBER_BOOKING_ONLY',
            'type' => 'tick',
            'category' => 'FEATURE',
            'group' => 'BOOKINGS',
            'explanation' => 'CONFIG_OPTION_member_booking_only',
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
        return '0';
    }
}

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
     * @return array The details
     */
    public function get_details() : array
    {
        return [
            'human_name' => 'MEMBER_BOOKING_ONLY',
            'type' => 'tick',
            'category' => 'FEATURE',
            'group' => 'BOOKINGS',
            'explanation' => 'CONFIG_OPTION_member_booking_only',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,
            'public' => false,
            'addon' => 'booking',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('booking')) {
            return null;
        }

        return '0';
    }
}

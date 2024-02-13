<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    mentorr
 */

/**
 * Hook class.
 */
class Hook_config_mentor_usergroup
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'MENTOR_USERGROUP',
            'type' => 'usergroup_not_guest',
            'category' => 'USERS',
            'group' => 'JOINING',
            'explanation' => 'CONFIG_OPTION_mentor_usergroup',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 100,
            'required' => false,
            'public' => false,
            'addon' => 'mentorr',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('mentorr')) {
            return null;
        }

        return do_lang('SUPER_MODERATORS');
    }
}

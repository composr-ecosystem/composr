<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    stealr
 */

/**
 * Hook class.
 */
class Hook_config_stealr_group
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'STEALR_GROUP',
            'type' => 'usergroup_not_guest',
            'category' => 'FEATURE',
            'group' => 'STEALR_TITLE',
            'explanation' => 'CONFIG_OPTION_stealr_group',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => false,
            'public' => false,
            'addon' => 'stealr',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('stealr')) {
            return null;
        }

        return do_lang('MEMBER');
    }
}

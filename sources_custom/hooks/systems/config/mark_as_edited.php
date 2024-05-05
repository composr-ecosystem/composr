<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cns_tapatalk
 */

/**
 * Hook class.
 */
class Hook_config_mark_as_edited
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'TAPATALK_MARK_AS_EDITED',
            'type' => 'tick',
            'category' => 'CMS_APIS',
            'group' => 'TAPATALK',
            'explanation' => 'CONFIG_OPTION_mark_as_edited',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,
            'public' => false,
            'addon' => 'cns_tapatalk',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('cns_tapatalk')) {
            return null;
        }

        return '1';
    }
}

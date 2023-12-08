<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    jestr
 */

/**
 * Hook class.
 */
class Hook_config_jestr_leet_shown_for
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'JESTER_LEET_SHOWN_FOR',
            'type' => 'line',
            'category' => 'FEATURE',
            'group' => 'JESTER_TITLE',
            'explanation' => 'CONFIG_OPTION_jestr_leet_shown_for',
            'shared_hosting_restricted' => '0',
            'list_options' => '',

            'addon' => 'jestr',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return '';
    }
}

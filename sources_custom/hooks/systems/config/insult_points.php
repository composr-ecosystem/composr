<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    bantr
 */

/**
 * Hook class.
 */
class Hook_config_insult_points
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'INSULT_POINTS',
            'type' => 'integer',
            'category' => 'POINTS',
            'group' => 'INSULT_TITLE',
            'explanation' => 'CONFIG_OPTION_insult_points',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,

            'addon' => 'bantr',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return '10';
    }
}

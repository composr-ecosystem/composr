<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    tester
 */

/**
 * Hook class.
 */
class Hook_config_tester_forum_name
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'TESTER_FORUM_NAME',
            'type' => 'forum',
            'category' => 'FEATURE',
            'group' => 'TESTER',
            'explanation' => 'CONFIG_OPTION_tester_forum_name',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 2,
            'required' => true,

            'addon' => 'tester',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return do_lang('tester:DEFAULT_TESTER_FORUM', '', '', '', get_site_default_lang());
    }
}

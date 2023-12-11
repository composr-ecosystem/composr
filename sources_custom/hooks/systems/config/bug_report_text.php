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
class Hook_config_bug_report_text
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'BUG_REPORT_TEXT',
            'type' => 'text',
            'category' => 'FEATURE',
            'group' => 'TESTER',
            'explanation' => 'CONFIG_OPTION_bug_report_text',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 1,

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
        return do_lang('tester:DEFAULT_BUG_REPORT_TEMPLATE');
    }
}

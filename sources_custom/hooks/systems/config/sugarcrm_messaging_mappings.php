<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    sugarcrm
 */

/**
 * Hook class.
 */
class Hook_config_sugarcrm_messaging_mappings
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'SUGARCRM_MESSAGING_MAPPINGS',
            'type' => 'text',
            'category' => 'COMPOSR_APIS',
            'group' => 'SUGARCRM_MESSAGING_SYNC',
            'explanation' => 'CONFIG_OPTION_sugarcrm_messaging_mappings',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 2,
            'required' => false,
            'public' => false,
            'addon' => 'sugarcrm',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('sugarcrm')) {
            return null;
        }

        return "post=description
phone=phone_work
website=website
country=primary_address_country
(Web Site)=lead_source
[_lead_source_description]=lead_source_description
[keep_referer]=refered_by
";
    }
}

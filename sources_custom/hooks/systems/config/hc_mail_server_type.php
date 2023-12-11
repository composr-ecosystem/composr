<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    health_check
 */

/**
 * Hook class.
 */
class Hook_config_hc_mail_server_type
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'HC_MAIL_SERVER_TYPE',
            'type' => 'list',
            'category' => 'HEALTH_CHECK',
            'group' => '_EMAIL',
            'explanation' => 'CONFIG_OPTION_hc_mail_server_type',
            'shared_hosting_restricted' => '0',
            'list_options' => 'pop3|pop3s|imap|imaps',
            'required' => true,
            'order_in_category_group' => 6,

            'addon' => 'health_check',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        if (!function_exists('imap_open')) {
            return null;
        }

        return 'imap';
    }
}

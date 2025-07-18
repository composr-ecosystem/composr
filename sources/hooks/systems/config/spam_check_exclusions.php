<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_configuration
 */

/**
 * Hook class.
 */
class Hook_config_spam_check_exclusions
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'SPAM_CHECK_EXCLUSIONS',
            'type' => 'line',
            'category' => 'SECURITY',
            'group' => 'SPAMMER_DETECTION',
            'explanation' => 'CONFIG_OPTION_spam_check_exclusions',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 11,

            'addon' => 'core_configuration',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        // Excluded ipv6 because almost every spam blacklist does not support it
        return '127.0.0.1,' . cms_srv('SERVER_ADDR') . '';
    }
}

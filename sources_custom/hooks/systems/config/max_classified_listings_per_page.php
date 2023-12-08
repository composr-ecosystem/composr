<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    classified_ads
 */

/**
 * Hook class.
 */
class Hook_config_max_classified_listings_per_page
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'MAX_CLASSIFIED_LISTINGS_PER_PAGE',
            'type' => 'integer',
            'category' => 'FEATURE',
            'group' => 'CLASSIFIEDS',
            'explanation' => 'CONFIG_OPTION_max_classified_listings_per_page',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,

            'addon' => 'classified_ads',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return '30';
    }
}

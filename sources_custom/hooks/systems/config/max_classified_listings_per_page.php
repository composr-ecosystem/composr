<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
     * @return array The details
     */
    public function get_details() : array
    {
        return [
            'human_name' => 'MAX_CLASSIFIED_LISTINGS_PER_PAGE',
            'type' => 'integer',
            'category' => 'ECOMMERCE_PRODUCTS',
            'group' => 'CLASSIFIEDS',
            'explanation' => 'CONFIG_OPTION_max_classified_listings_per_page',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => true,
            'public' => false,
            'addon' => 'classified_ads',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('classified_ads')) {
            return null;
        }

        return '30';
    }
}

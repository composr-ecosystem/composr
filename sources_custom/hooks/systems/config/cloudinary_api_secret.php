<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cloudinary
 */

/**
 * Hook class.
 */
class Hook_config_cloudinary_api_secret
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details() : ?array
    {
        return [
            'human_name' => 'CLOUDINARY_API_SECRET',
            'type' => 'line',
            'category' => 'CONTENT_EDITING',
            'group' => 'UPLOADED_FILES',
            'explanation' => 'CONFIG_OPTION_cloudinary_api_secret',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'required' => false,
            'public' => false,
            'addon' => 'cloudinary',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('cloudinary')) {
            return null;
        }

        return '';
    }
}

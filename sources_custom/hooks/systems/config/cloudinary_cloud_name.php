<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cloudinary
 */

/**
 * Hook class.
 */
class Hook_config_cloudinary_cloud_name
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'CLOUDINARY_CLOUD_NAME',
            'type' => 'line',
            'category' => 'FEATURE',
            'group' => 'UPLOADED_FILES',
            'explanation' => 'CONFIG_OPTION_cloudinary_cloud_name',
            'shared_hosting_restricted' => '0',
            'list_options' => '',

            'addon' => 'cloudinary',
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

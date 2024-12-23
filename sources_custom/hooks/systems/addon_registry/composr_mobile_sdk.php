<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_addon_registry_composr_mobile_sdk
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11'; // addon_version_auto_update 3f2352cb2ca3c9d25c9d617fb91457c5
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return 11.9;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Development';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Amit Nigam';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'ApnsPHP developers',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as Composr / New BSD License (ApnsPHP)';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Server support for Composr Mobile SDK, including Composr mobile APIs and push notification support for iOS and Android.

The documentation for this addon is covered in a [url="' . get_brand_base_url() . '/docs/tut_mobile_sdk.htm"]dedicated tutorial[/url].';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return ['tut_mobile_sdk'];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [
                'Conversr',
            ],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/tool.svg';
    }
    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('device_token_details');
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install(?int $upgrade_from = null)
    {
        if ($upgrade_from === null) {
            // Table for holding the IDs of devices signed up for notifications
            $GLOBALS['SITE_DB']->create_table('device_token_details', [
                'id' => '*AUTO',
                'token_type' => 'ID_TEXT', // ios|android
                'device_token' => 'SHORT_TEXT',
                'member_id' => 'MEMBER',
            ]);
            $GLOBALS['SITE_DB']->create_index('device_token_details', 'member_id', ['member_id']);
        }
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'data_custom/composr_mobile_sdk_build.php',
            'data_custom/modules/composr_mobile_sdk/android/index.html',
            'data_custom/modules/composr_mobile_sdk/index.html',
            'data_custom/modules/composr_mobile_sdk/ios/entrust_root_certification_authority.pem',
            'data_custom/modules/composr_mobile_sdk/ios/index.html',
            'exports/composr_mobile_sdk/.htaccess',
            'exports/composr_mobile_sdk/image_assets/index.html',
            'exports/composr_mobile_sdk/index.html',
            'lang_custom/EN/composr_mobile_sdk.ini',
            'sources_custom/composr_mobile_sdk/.htaccess',
            'sources_custom/composr_mobile_sdk/android/index.html',
            'sources_custom/composr_mobile_sdk/android/notifications.php',
            'sources_custom/composr_mobile_sdk/index.html',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Abstract.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Autoload.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Exception.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Feedback.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Log/Embedded.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Log/Error.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Log/Interface.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Log/Silent.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Log/index.html',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Message.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Message/Custom.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Message/Exception.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Message/Safari.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Message/index.html',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push/Exception.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push/Server.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push/Server/Exception.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push/Server/index.html',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push/index.html',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/index.html',
            'sources_custom/composr_mobile_sdk/ios/index.html',
            'sources_custom/composr_mobile_sdk/ios/notifications.php',
            'sources_custom/hooks/endpoints/account/join.php',
            'sources_custom/hooks/endpoints/account/login.php',
            'sources_custom/hooks/endpoints/account/lost_password.php',
            'sources_custom/hooks/endpoints/account/setup_push_notifications.php',
            'sources_custom/hooks/endpoints/content/commandr_fs.php',
            'sources_custom/hooks/endpoints/misc/contact_us.php',
            'sources_custom/hooks/systems/addon_registry/composr_mobile_sdk.php',
            'sources_custom/hooks/systems/config/android_icon_name.php',
            'sources_custom/hooks/systems/config/enable_notifications_instant_android.php',
            'sources_custom/hooks/systems/config/enable_notifications_instant_ios.php',
            'sources_custom/hooks/systems/config/ios_cert_passphrase.php',
            'sources_custom/hooks/systems/config/notification_codes_for_mobile.php',
            'sources_custom/hooks/systems/notification_types_extended/composr_mobile_sdk.php',
            'sources_custom/hooks/systems/privacy/composr_mobile_sdk.php',
            'sources_custom/hooks/systems/tasks/android_notification.php',
            'sources_custom/hooks/systems/tasks/ios_notification.php',
        ];
    }
}

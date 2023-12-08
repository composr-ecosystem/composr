<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_mobile_sdk
 */

/**
 * Hook class.
 */
class Hook_addon_registry_composr_mobile_sdk
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Development';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Amit Nigam';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'ApnsPHP developers',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr / New BSD License (ApnsPHP)';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Server support for Composr Mobile SDK, including Composr mobile APIs and push notification support for iOS and Android.

The documentation for this addon is covered in a [url="' . get_brand_base_url() . '/docs/tut_mobile_sdk.htm"]dedicated tutorial[/url].';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array('tut_mobile_sdk');
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'PHP5.3',
                'Conversr',
            ),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/_generic_admin/tool.png';
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
    public function install($upgrade_from = null)
    {
        if (is_null($upgrade_from)) {
            // Table for holding the IDs of devices signed up for notifications
            $GLOBALS['SITE_DB']->create_table('device_token_details', array(
                'id' => '*AUTO',
                'token_type' => 'ID_TEXT', // ios|android
                'device_token' => 'SHORT_TEXT',
                'member_id' => 'MEMBER',
            ));
            $GLOBALS['SITE_DB']->create_index('device_token_details', 'member_id', array('member_id'));
        }
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/composr_mobile_sdk.php',

            'data_custom/modules/composr_mobile_sdk/index.html',
            'data_custom/modules/composr_mobile_sdk/android/index.html',
            'data_custom/modules/composr_mobile_sdk/ios/index.html',
            'sources_custom/composr_mobile_sdk/index.html',
            'sources_custom/composr_mobile_sdk/.htaccess',
            'sources_custom/composr_mobile_sdk/android/index.html',
            'sources_custom/composr_mobile_sdk/ios/index.html',
            'lang_custom/EN/composr_mobile_sdk.ini',

            // Toolkit
            'data_custom/composr_mobile_sdk_build.php',
            'exports/composr_mobile_sdk/.htaccess',
            'exports/composr_mobile_sdk/index.html',
            'exports/composr_mobile_sdk/image_assets/index.html',

            // External endpoints (API)
            'sources_custom/hooks/endpoints/account/join.php',
            'sources_custom/hooks/endpoints/account/login.php',
            'sources_custom/hooks/endpoints/account/lost_password.php',
            'sources_custom/hooks/endpoints/account/setup_push_notifications.php',
            'sources_custom/hooks/endpoints/content/commandr_fs.php',
            'sources_custom/hooks/endpoints/misc/contact_us.php',
            // Also see cns_tapatalk addon for more useful endpoints, but via Tapatalk API. At some point we likely will merge this into a single framework

            // Notifications
            'data_custom/modules/composr_mobile_sdk/ios/entrust_root_certification_authority.pem',
            'sources_custom/hooks/systems/notification_types_extended/composr_mobile_sdk.php',
            'sources_custom/composr_mobile_sdk/ios/notifications.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Abstract.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Autoload.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Exception.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Feedback.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Log/Embedded.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Log/Interface.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Message.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Message/Custom.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Message/Exception.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push/Exception.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push/Server.php',
            'sources_custom/composr_mobile_sdk/ios/ApnsPHP/Push/Server/Exception.php',
            'sources_custom/hooks/systems/config/enable_notifications_instant_ios.php',
            'sources_custom/hooks/systems/config/ios_cert_passphrase.php',
            'sources_custom/hooks/systems/tasks/ios_notification.php',
            'sources_custom/composr_mobile_sdk/android/notifications.php',
            'sources_custom/hooks/systems/config/enable_notifications_instant_android.php',
            'sources_custom/hooks/systems/config/android_icon_name.php',
            'sources_custom/hooks/systems/tasks/android_notification.php',
            'sources_custom/hooks/systems/config/notification_codes_for_mobile.php',
        );
    }
}

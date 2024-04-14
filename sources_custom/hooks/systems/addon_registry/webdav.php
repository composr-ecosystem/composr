<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    webdav
 */

/**
 * Hook class.
 */
class Hook_addon_registry_webdav
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
     * Get the version of Composr this addon is for (used in generating the TAR filename).
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
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
        return null;
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
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'SabreDAV developers',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'BSD-like licence';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Access your website repository as a folder on your computer. See the [url="' . get_brand_base_url() . '/docs/tut_repository.htm"]Repository Tutorial[/url] for more information. This addon is only tested with Apache, custom redirect configuration will be needed for IIS. This addon will NOT work with PHP 8 or higher.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
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
                'commandr',
                'PHP 7.1',
                'PHP mbstring extension',
            ],
            'recommends' => [],
            'conflicts_with' => [
                'PHP 8'
            ],
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
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'data_custom/modules/webdav/index.html',
            'data_custom/modules/webdav/locks/index.html',
            'data_custom/modules/webdav/locks/locks.dat',
            'data_custom/modules/webdav/tmp/index.html',
            'data_custom/webdav.php',
            'sources_custom/hooks/systems/addon_registry/webdav.php',
            'sources_custom/sabredav/.htaccess',
            'sources_custom/sabredav/composer.json',
            'sources_custom/sabredav/composer.lock',
            'sources_custom/sabredav/index.html',
            'sources_custom/sabredav/vendor/.htaccess',
            'sources_custom/sabredav/vendor/autoload.php',
            'sources_custom/sabredav/vendor/composer/.htaccess',
            'sources_custom/sabredav/vendor/composer/ClassLoader.php',
            'sources_custom/sabredav/vendor/composer/LICENSE',
            'sources_custom/sabredav/vendor/composer/autoload_classmap.php',
            'sources_custom/sabredav/vendor/composer/autoload_files.php',
            'sources_custom/sabredav/vendor/composer/autoload_namespaces.php',
            'sources_custom/sabredav/vendor/composer/autoload_psr4.php',
            'sources_custom/sabredav/vendor/composer/autoload_real.php',
            'sources_custom/sabredav/vendor/composer/autoload_static.php',
            'sources_custom/sabredav/vendor/composer/index.html',
            'sources_custom/sabredav/vendor/composer/installed.json',
            'sources_custom/sabredav/vendor/index.html',
            'sources_custom/sabredav/vendor/psr/log/.htaccess',
            'sources_custom/sabredav/vendor/psr/log/LICENSE',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/.htaccess',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/AbstractLogger.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/InvalidArgumentException.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/LogLevel.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/LoggerAwareInterface.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/LoggerAwareTrait.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/LoggerInterface.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/LoggerTrait.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/NullLogger.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/Test/.htaccess',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/Test/DummyTest.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/Test/LoggerInterfaceTest.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/Test/TestLogger.php',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/Test/index.html',
            'sources_custom/sabredav/vendor/psr/log/Psr/Log/index.html',
            'sources_custom/sabredav/vendor/psr/log/README.md',
            'sources_custom/sabredav/vendor/psr/log/composer.json',
            'sources_custom/sabredav/vendor/psr/log/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/.gitignore',
            'sources_custom/sabredav/vendor/sabre/dav/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/.php_cs.dist',
            'sources_custom/sabredav/vendor/sabre/dav/.travis.yml',
            'sources_custom/sabredav/vendor/sabre/dav/CHANGELOG.md',
            'sources_custom/sabredav/vendor/sabre/dav/CONTRIBUTING.md',
            'sources_custom/sabredav/vendor/sabre/dav/LICENSE',
            'sources_custom/sabredav/vendor/sabre/dav/README.md',
            'sources_custom/sabredav/vendor/sabre/dav/composer.json',
            'sources_custom/sabredav/vendor/sabre/dav/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/AbstractBackend.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/BackendInterface.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/NotificationSupport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/PDO.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/SchedulingSupport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/SharingSupport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/SimplePDO.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/SubscriptionSupport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/SyncSupport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Backend/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Calendar.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/CalendarHome.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/CalendarObject.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/CalendarQueryValidator.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/CalendarRoot.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Exception/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Exception/InvalidComponentType.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Exception/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/ICSExportPlugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/ICalendar.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/ICalendarObject.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/ICalendarObjectContainer.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/ISharedCalendar.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Notifications/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Notifications/Collection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Notifications/ICollection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Notifications/INode.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Notifications/Node.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Notifications/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Notifications/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Principal/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Principal/Collection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Principal/IProxyRead.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Principal/IProxyWrite.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Principal/ProxyRead.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Principal/ProxyWrite.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Principal/User.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Principal/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/IInbox.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/IMipPlugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/IOutbox.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/ISchedulingObject.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/Inbox.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/Outbox.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/SchedulingObject.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Schedule/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/SharedCalendar.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/SharingPlugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Subscriptions/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Subscriptions/ISubscription.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Subscriptions/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Subscriptions/Subscription.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Subscriptions/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Filter/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Filter/CalendarData.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Filter/CompFilter.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Filter/ParamFilter.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Filter/PropFilter.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Filter/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Notification/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Notification/Invite.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Notification/InviteReply.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Notification/NotificationInterface.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Notification/SystemStatus.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Notification/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Property/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Property/AllowedSharingModes.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Property/EmailAddressSet.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Property/Invite.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Property/ScheduleCalendarTransp.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Property/SupportedCalendarComponentSet.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Property/SupportedCalendarData.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Property/SupportedCollationSet.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Property/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Request/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Request/CalendarMultiGetReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Request/CalendarQueryReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Request/FreeBusyQueryReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Request/InviteReply.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Request/MkCalendar.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Request/Share.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/Xml/Request/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CalDAV/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/AddressBook.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/AddressBookHome.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/AddressBookRoot.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Backend/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Backend/AbstractBackend.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Backend/BackendInterface.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Backend/PDO.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Backend/SyncSupport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Backend/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Card.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/IAddressBook.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/ICard.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/IDirectory.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/VCFExportPlugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Filter/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Filter/AddressData.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Filter/ParamFilter.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Filter/PropFilter.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Filter/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Property/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Property/SupportedAddressData.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Property/SupportedCollationSet.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Property/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Request/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Request/AddressBookMultiGetReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Request/AddressBookQueryReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/Xml/Request/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/CardDAV/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/AbstractBasic.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/AbstractBearer.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/AbstractDigest.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/Apache.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/BackendInterface.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/BasicCallBack.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/File.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/IMAP.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/PDO.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Backend/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Auth/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/GuessContentType.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/HtmlOutput.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/HtmlOutputHelper.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/MapGetToPropFind.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/PropFindAll.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/favicon.ico',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/openiconic/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/openiconic/ICON-LICENSE',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/openiconic/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/openiconic/open-iconic.css',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/openiconic/open-iconic.eot',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/openiconic/open-iconic.otf',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/openiconic/open-iconic.svg',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/openiconic/open-iconic.ttf',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/openiconic/open-iconic.woff',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/sabredav.css',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/assets/sabredav.png',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Browser/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Client.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Collection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/CorePlugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/BadRequest.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/Conflict.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/ConflictingLock.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/Forbidden.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/InsufficientStorage.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/InvalidResourceType.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/InvalidSyncToken.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/LengthRequired.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/LockTokenMatchesRequestUri.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/Locked.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/MethodNotAllowed.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/NotAuthenticated.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/NotFound.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/NotImplemented.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/PaymentRequired.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/PreconditionFailed.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/ReportNotSupported.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/RequestedRangeNotSatisfiable.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/ServiceUnavailable.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/TooManyMatches.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/UnsupportedMediaType.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Exception/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/FS/Directory.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/FS/File.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/FS/Node.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/FSExt/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/FSExt/Directory.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/FSExt/File.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/FSExt/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/File.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/ICollection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/ICopyTarget.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/IExtendedCollection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/IFile.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/IMoveTarget.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/IMultiGet.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/INode.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/IProperties.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/IQuota.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/Backend/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/Backend/AbstractBackend.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/Backend/BackendInterface.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/Backend/File.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/Backend/PDO.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/Backend/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/LockInfo.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Locks/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/MkCol.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Mount/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Mount/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Mount/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Node.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PartialUpdate/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PartialUpdate/IPatchSupport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PartialUpdate/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PartialUpdate/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PropFind.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PropPatch.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PropertyStorage/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PropertyStorage/Backend/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PropertyStorage/Backend/BackendInterface.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PropertyStorage/Backend/PDO.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PropertyStorage/Backend/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PropertyStorage/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/PropertyStorage/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Server.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/ServerPlugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Sharing/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Sharing/ISharedNode.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Sharing/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Sharing/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/SimpleCollection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/SimpleFile.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/StringUtil.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Sync/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Sync/ISyncCollection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Sync/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Sync/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/TemporaryFileFilterPlugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Tree.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/UUIDUtil.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Version.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Element/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Element/Prop.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Element/Response.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Element/Sharee.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Element/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/Complex.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/GetLastModified.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/Href.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/Invite.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/LocalHref.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/LockDiscovery.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/ResourceType.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/ShareAccess.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/SupportedLock.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/SupportedMethodSet.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/SupportedReportSet.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Property/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Request/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Request/Lock.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Request/MkCol.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Request/PropFind.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Request/PropPatch.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Request/ShareResource.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Request/SyncCollectionReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Request/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Response/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Response/MultiStatus.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Response/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/Service.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/Xml/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAV/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/ACLTrait.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/AbstractPrincipalCollection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Exception/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Exception/AceConflict.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Exception/NeedPrivileges.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Exception/NoAbstract.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Exception/NotRecognizedPrincipal.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Exception/NotSupportedPrivilege.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Exception/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/FS/Collection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/FS/File.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/FS/HomeCollection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/IACL.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/IPrincipal.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/IPrincipalCollection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Principal.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/PrincipalBackend/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/PrincipalBackend/AbstractBackend.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/PrincipalBackend/BackendInterface.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/PrincipalBackend/CreatePrincipalSupport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/PrincipalBackend/PDO.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/PrincipalBackend/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/PrincipalCollection.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Property/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Property/Acl.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Property/AclRestrictions.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Property/CurrentUserPrivilegeSet.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Property/Principal.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Property/SupportedPrivilegeSet.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Property/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Request/.htaccess',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Request/AclPrincipalPropSetReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Request/ExpandPropertyReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Request/PrincipalMatchReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Request/PrincipalPropertySearchReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Request/PrincipalSearchPropertySetReport.php',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/Xml/Request/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/lib/DAVACL/index.html',
            'sources_custom/sabredav/vendor/sabre/dav/phpstan.neon',
            'sources_custom/sabredav/vendor/sabre/event/.htaccess',
            'sources_custom/sabredav/vendor/sabre/event/.php_cs.dist',
            'sources_custom/sabredav/vendor/sabre/event/LICENSE',
            'sources_custom/sabredav/vendor/sabre/event/composer.json',
            'sources_custom/sabredav/vendor/sabre/event/index.html',
            'sources_custom/sabredav/vendor/sabre/event/lib/.htaccess',
            'sources_custom/sabredav/vendor/sabre/event/lib/Emitter.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/EmitterInterface.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/EmitterTrait.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/EventEmitter.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/Loop/.htaccess',
            'sources_custom/sabredav/vendor/sabre/event/lib/Loop/Loop.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/Loop/functions.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/Loop/index.html',
            'sources_custom/sabredav/vendor/sabre/event/lib/Promise.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/Promise/.htaccess',
            'sources_custom/sabredav/vendor/sabre/event/lib/Promise/functions.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/Promise/index.html',
            'sources_custom/sabredav/vendor/sabre/event/lib/PromiseAlreadyResolvedException.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/Version.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/WildcardEmitter.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/WildcardEmitterTrait.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/coroutine.php',
            'sources_custom/sabredav/vendor/sabre/event/lib/index.html',
            'sources_custom/sabredav/vendor/sabre/event/phpstan.neon',
            'sources_custom/sabredav/vendor/sabre/http/.gitignore',
            'sources_custom/sabredav/vendor/sabre/http/.htaccess',
            'sources_custom/sabredav/vendor/sabre/http/.php_cs.dist',
            'sources_custom/sabredav/vendor/sabre/http/.travis.yml',
            'sources_custom/sabredav/vendor/sabre/http/CHANGELOG.md',
            'sources_custom/sabredav/vendor/sabre/http/LICENSE',
            'sources_custom/sabredav/vendor/sabre/http/README.md',
            'sources_custom/sabredav/vendor/sabre/http/composer.json',
            'sources_custom/sabredav/vendor/sabre/http/index.html',
            'sources_custom/sabredav/vendor/sabre/http/lib/.htaccess',
            'sources_custom/sabredav/vendor/sabre/http/lib/Auth/.htaccess',
            'sources_custom/sabredav/vendor/sabre/http/lib/Auth/AWS.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Auth/AbstractAuth.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Auth/Basic.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Auth/Bearer.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Auth/Digest.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Auth/index.html',
            'sources_custom/sabredav/vendor/sabre/http/lib/Client.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/ClientException.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/ClientHttpException.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/HttpException.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Message.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/MessageDecoratorTrait.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/MessageInterface.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Request.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/RequestDecorator.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/RequestInterface.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Response.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/ResponseDecorator.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/ResponseInterface.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Sapi.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/Version.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/functions.php',
            'sources_custom/sabredav/vendor/sabre/http/lib/index.html',
            'sources_custom/sabredav/vendor/sabre/http/phpstan.neon',
            'sources_custom/sabredav/vendor/sabre/uri/.gitattributes',
            'sources_custom/sabredav/vendor/sabre/uri/.gitignore',
            'sources_custom/sabredav/vendor/sabre/uri/.htaccess',
            'sources_custom/sabredav/vendor/sabre/uri/.php_cs.dist',
            'sources_custom/sabredav/vendor/sabre/uri/LICENSE',
            'sources_custom/sabredav/vendor/sabre/uri/composer.json',
            'sources_custom/sabredav/vendor/sabre/uri/index.html',
            'sources_custom/sabredav/vendor/sabre/uri/lib/.htaccess',
            'sources_custom/sabredav/vendor/sabre/uri/lib/InvalidUriException.php',
            'sources_custom/sabredav/vendor/sabre/uri/lib/Version.php',
            'sources_custom/sabredav/vendor/sabre/uri/lib/functions.php',
            'sources_custom/sabredav/vendor/sabre/uri/lib/index.html',
            'sources_custom/sabredav/vendor/sabre/uri/phpstan.neon',
            'sources_custom/sabredav/vendor/sabre/vobject/.gitignore',
            'sources_custom/sabredav/vendor/sabre/vobject/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/.php_cs.dist',
            'sources_custom/sabredav/vendor/sabre/vobject/.travis.yml',
            'sources_custom/sabredav/vendor/sabre/vobject/CHANGELOG.md',
            'sources_custom/sabredav/vendor/sabre/vobject/LICENSE',
            'sources_custom/sabredav/vendor/sabre/vobject/README.md',
            'sources_custom/sabredav/vendor/sabre/vobject/composer.json',
            'sources_custom/sabredav/vendor/sabre/vobject/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/BirthdayCalendarGenerator.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Cli.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/Available.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/VAlarm.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/VAvailability.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/VCalendar.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/VCard.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/VEvent.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/VFreeBusy.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/VJournal.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/VTimeZone.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/VTodo.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Component/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/DateTimeParser.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Document.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/ElementList.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/EofException.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/FreeBusyData.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/FreeBusyGenerator.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/ITip/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/ITip/Broker.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/ITip/ITipException.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/ITip/Message.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/ITip/SameOrganizerForAllComponentsException.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/ITip/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/InvalidDataException.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Node.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/PHPUnitAssertions.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parameter.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/ParseException.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parser/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parser/Json.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parser/MimeDir.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parser/Parser.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parser/XML.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parser/XML/Element/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parser/XML/Element/KeyValue.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parser/XML/Element/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Parser/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/Binary.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/Boolean.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/FlatText.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/FloatValue.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/ICalendar/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/ICalendar/CalAddress.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/ICalendar/Date.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/ICalendar/DateTime.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/ICalendar/Duration.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/ICalendar/Period.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/ICalendar/Recur.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/ICalendar/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/IntegerValue.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/Text.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/Time.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/Unknown.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/Uri.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/UtcOffset.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/VCard/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/VCard/Date.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/VCard/DateAndOrTime.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/VCard/DateTime.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/VCard/LanguageTag.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/VCard/PhoneNumber.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/VCard/TimeStamp.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/VCard/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Property/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Reader.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Recur/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Recur/EventIterator.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Recur/MaxInstancesExceededException.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Recur/NoInstancesException.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Recur/RDateIterator.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Recur/RRuleIterator.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Recur/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Settings.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Splitter/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Splitter/ICalendar.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Splitter/SplitterInterface.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Splitter/VCard.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Splitter/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/StringUtil.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/TimeZoneUtil.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/UUIDUtil.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/VCardConverter.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Version.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Writer.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/timezonedata/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/timezonedata/exchangezones.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/timezonedata/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/timezonedata/lotuszones.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/timezonedata/php-bc.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/timezonedata/php-workaround.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/timezonedata/windowszones.php',
            'sources_custom/sabredav/vendor/sabre/vobject/phpstan.neon',
            'sources_custom/sabredav/vendor/sabre/vobject/resources/schema/.htaccess',
            'sources_custom/sabredav/vendor/sabre/vobject/resources/schema/index.html',
            'sources_custom/sabredav/vendor/sabre/vobject/resources/schema/xcal.rng',
            'sources_custom/sabredav/vendor/sabre/vobject/resources/schema/xcard.rng',
            'sources_custom/sabredav/vendor/sabre/xml/.gitignore',
            'sources_custom/sabredav/vendor/sabre/xml/.htaccess',
            'sources_custom/sabredav/vendor/sabre/xml/.php_cs.dist',
            'sources_custom/sabredav/vendor/sabre/xml/.travis.yml',
            'sources_custom/sabredav/vendor/sabre/xml/CHANGELOG.md',
            'sources_custom/sabredav/vendor/sabre/xml/LICENSE',
            'sources_custom/sabredav/vendor/sabre/xml/README.md',
            'sources_custom/sabredav/vendor/sabre/xml/composer.json',
            'sources_custom/sabredav/vendor/sabre/xml/index.html',
            'sources_custom/sabredav/vendor/sabre/xml/lib/.htaccess',
            'sources_custom/sabredav/vendor/sabre/xml/lib/ContextStackTrait.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Deserializer/.htaccess',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Deserializer/functions.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Deserializer/index.html',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Element.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Element/.htaccess',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Element/Base.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Element/Cdata.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Element/Elements.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Element/KeyValue.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Element/Uri.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Element/XmlFragment.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Element/index.html',
            'sources_custom/sabredav/vendor/sabre/xml/lib/LibXMLException.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/ParseException.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Reader.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Serializer/.htaccess',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Serializer/functions.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Serializer/index.html',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Service.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Version.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/Writer.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/XmlDeserializable.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/XmlSerializable.php',
            'sources_custom/sabredav/vendor/sabre/xml/lib/index.html',
            'sources_custom/sabredav/vendor/sabre/xml/phpstan.neon',
            'sources_custom/webdav.php',
            'sources_custom/webdav_commandr_fs.php',
        ];
    }
}

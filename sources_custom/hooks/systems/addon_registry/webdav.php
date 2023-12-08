<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    webdav
 */

/**
 * Hook class.
 */
class Hook_addon_registry_webdav
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
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'SabreDAV developers',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'BSD-like licence';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Access your website repository as a folder on your computer. See the [url="' . get_brand_base_url() . '/docs/tut_repository.htm"]Repository Tutorial[/url] for more information. This addon is only tested with Apache, custom redirect configuration will be needed for IIS.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
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
                'commandr',
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
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/webdav.php',
            'sources_custom/sabredav/lib/Sabre/autoload.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Auth/Backend/AbstractBasic.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Auth/Backend/AbstractDigest.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Auth/Backend/Apache.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Auth/Backend/BackendInterface.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Auth/Backend/File.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Auth/Backend/PDO.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Auth/Plugin.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Component/VTodo.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/assets/favicon.ico',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/assets/icons/addressbook.png',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/assets/icons/calendar.png',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/assets/icons/card.png',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/assets/icons/collection.png',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/assets/icons/file.png',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/assets/icons/parent.png',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/assets/icons/principal.png',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/GuessContentType.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/MapGetToPropFind.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Browser/Plugin.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Client.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Collection.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/BadRequest.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/Conflict.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/ConflictingLock.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/FileNotFound.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/Forbidden.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/InsufficientStorage.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/InvalidResourceType.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/Locked.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/LockTokenMatchesRequestUri.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/MethodNotAllowed.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/NotAuthenticated.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/NotFound.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/NotImplemented.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/PaymentRequired.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/PreconditionFailed.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/ReportNotSupported.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/RequestedRangeNotSatisfiable.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/ServiceUnavailable.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception/UnsupportedMediaType.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Exception.php',
            'sources_custom/sabredav/lib/Sabre/DAV/File.php',
            'sources_custom/sabredav/lib/Sabre/DAV/FS/Directory.php',
            'sources_custom/sabredav/lib/Sabre/DAV/FS/File.php',
            'sources_custom/sabredav/lib/Sabre/DAV/FS/Node.php',
            'sources_custom/sabredav/lib/Sabre/DAV/FSExt/Directory.php',
            'sources_custom/sabredav/lib/Sabre/DAV/FSExt/File.php',
            'sources_custom/sabredav/lib/Sabre/DAV/FSExt/Node.php',
            'sources_custom/sabredav/lib/Sabre/DAV/ICollection.php',
            'sources_custom/sabredav/lib/Sabre/DAV/IExtendedCollection.php',
            'sources_custom/sabredav/lib/Sabre/DAV/IFile.php',
            'sources_custom/sabredav/lib/Sabre/DAV/INode.php',
            'sources_custom/sabredav/lib/Sabre/DAV/IProperties.php',
            'sources_custom/sabredav/lib/Sabre/DAV/IQuota.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Locks/Backend/AbstractBackend.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Locks/Backend/BackendInterface.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Locks/Backend/File.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Locks/Backend/FS.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Locks/Backend/PDO.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Locks/LockInfo.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Locks/Plugin.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Mount/Plugin.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Node.php',
            'sources_custom/sabredav/lib/Sabre/DAV/ObjectTree.php',
            'sources_custom/sabredav/lib/Sabre/DAV/PartialUpdate/IFile.php',
            'sources_custom/sabredav/lib/Sabre/DAV/PartialUpdate/Plugin.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/GetLastModified.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/Href.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/HrefList.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/IHref.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/LockDiscovery.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/ResourceType.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/Response.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/ResponseList.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/SupportedLock.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property/SupportedReportSet.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Property.php',
            'sources_custom/sabredav/lib/Sabre/DAV/PropertyInterface.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Server.php',
            'sources_custom/sabredav/lib/Sabre/DAV/ServerPlugin.php',
            'sources_custom/sabredav/lib/Sabre/DAV/SimpleCollection.php',
            'sources_custom/sabredav/lib/Sabre/DAV/SimpleFile.php',
            'sources_custom/sabredav/lib/Sabre/DAV/StringUtil.php',
            'sources_custom/sabredav/lib/Sabre/DAV/TemporaryFileFilterPlugin.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Tree/Filesystem.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Tree.php',
            'sources_custom/sabredav/lib/Sabre/DAV/URLUtil.php',
            'sources_custom/sabredav/lib/Sabre/DAV/UUIDUtil.php',
            'sources_custom/sabredav/lib/Sabre/DAV/Version.php',
            'sources_custom/sabredav/lib/Sabre/DAV/XMLUtil.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/AbstractPrincipalCollection.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Exception/AceConflict.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Exception/NeedPrivileges.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Exception/NoAbstract.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Exception/NotRecognizedPrincipal.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Exception/NotSupportedPrivilege.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/IACL.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/IPrincipal.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/IPrincipalCollection.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Plugin.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Principal.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/PrincipalBackend/AbstractBackend.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/PrincipalBackend/BackendInterface.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/PrincipalBackend/PDO.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/PrincipalCollection.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Property/Acl.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Property/AclRestrictions.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Property/CurrentUserPrivilegeSet.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Property/Principal.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Property/SupportedPrivilegeSet.php',
            'sources_custom/sabredav/lib/Sabre/DAVACL/Version.php',
            'sources_custom/sabredav/lib/Sabre/HTTP/AbstractAuth.php',
            'sources_custom/sabredav/lib/Sabre/HTTP/AWSAuth.php',
            'sources_custom/sabredav/lib/Sabre/HTTP/BasicAuth.php',
            'sources_custom/sabredav/lib/Sabre/HTTP/DigestAuth.php',
            'sources_custom/sabredav/lib/Sabre/HTTP/Request.php',
            'sources_custom/sabredav/lib/Sabre/HTTP/Response.php',
            'sources_custom/sabredav/lib/Sabre/HTTP/Util.php',
            'sources_custom/sabredav/lib/Sabre/HTTP/Version.php',
            'sources_custom/sabredav/vendor/autoload.php',
            'sources_custom/sabredav/vendor/composer/autoload_classmap.php',
            'sources_custom/sabredav/vendor/composer/autoload_namespaces.php',
            'sources_custom/sabredav/vendor/composer/autoload_real.php',
            'sources_custom/sabredav/vendor/composer/ClassLoader.php',
            'sources_custom/sabredav/vendor/composer/installed.json',
            'sources_custom/sabredav/vendor/sabre/vobject/.travis.yml',
            'sources_custom/sabredav/vendor/sabre/vobject/bin/bench.php',
            'sources_custom/sabredav/vendor/sabre/vobject/bin/generateicalendardata.php',
            'sources_custom/sabredav/vendor/sabre/vobject/bin/vobjectvalidate.php',
            'sources_custom/sabredav/vendor/sabre/vobject/ChangeLog',
            'sources_custom/sabredav/vendor/sabre/vobject/composer.json',
            'sources_custom/sabredav/vendor/sabre/vobject/doc/DesignFor3_0.md',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Component/VAlarm.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Component/VCalendar.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Component/VCard.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Component/VEvent.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Component/VFreeBusy.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Component/VJournal.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Component.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/DateTimeParser.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Document.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/ElementList.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/FreeBusyGenerator.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/includes.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Node.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Parameter.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/ParseException.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Property/Compound.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Property/DateTime.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Property/MultiDateTime.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Property.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Reader.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/RecurrenceIterator.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Splitter/ICalendar.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Splitter/SplitterInterface.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Splitter/VCard.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/StringUtil.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/TimeZoneUtil.php',
            'sources_custom/sabredav/vendor/sabre/vobject/lib/Sabre/VObject/Version.php',
            'sources_custom/sabredav/vendor/sabre/vobject/LICENSE',
            'sources_custom/sabredav/vendor/sabre/vobject/README.md',
            'data_custom/webdav.php',
            'sources_custom/webdav.php',
            'data_custom/modules/webdav/index.html',
            'data_custom/modules/webdav/locks/index.html',
            'data_custom/modules/webdav/locks/locks.dat',
            'data_custom/modules/webdav/tmp/index.html',
            'sources_custom/webdav_commandr_fs.php',
        );
    }
}

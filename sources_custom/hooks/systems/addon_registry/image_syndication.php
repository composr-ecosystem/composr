<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    image_syndication
 */

/**
 * Hook class.
 */
class Hook_addon_registry_image_syndication
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
        return 'Third Party Integration';
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
            'Photobucket developers',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'BSD';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Syndicate attachments and gallery images to various photo services (at the time of writing, Photobucket). You may also use the service(s) for primary storage.';
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
                'PHP cURL extension',
                'PHP5.2',
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
            'sources_custom/hooks/systems/addon_registry/image_syndication.php',
            'sources_custom/photobucket/OAuth/Consumer.php',
            'sources_custom/photobucket/OAuth/Request.php',
            'sources_custom/photobucket/OAuth/Signature/hmac_sha1.php',
            'sources_custom/photobucket/OAuth/Signature/Interface.php',
            'sources_custom/photobucket/OAuth/Signature/plaintext.php',
            'sources_custom/photobucket/OAuth/Signature.php',
            'sources_custom/photobucket/OAuth/Token.php',
            'sources_custom/photobucket/OAuth/Utils.php',
            'sources_custom/photobucket/PBAPI/data/api-defs.yml',
            'sources_custom/photobucket/PBAPI/data/methods.dat',
            'sources_custom/photobucket/PBAPI/data/yml2phpserialize.php',
            'sources_custom/photobucket/PBAPI/Exception/Response.php',
            'sources_custom/photobucket/PBAPI/Exception.php',
            'sources_custom/photobucket/PBAPI/Methods/album.php',
            'sources_custom/photobucket/PBAPI/Methods/base.php',
            'sources_custom/photobucket/PBAPI/Methods/featured.php',
            'sources_custom/photobucket/PBAPI/Methods/findstuff.php',
            'sources_custom/photobucket/PBAPI/Methods/group.php',
            'sources_custom/photobucket/PBAPI/Methods/login.php',
            'sources_custom/photobucket/PBAPI/Methods/media.php',
            'sources_custom/photobucket/PBAPI/Methods/search.php',
            'sources_custom/photobucket/PBAPI/Methods/user.php',
            'sources_custom/photobucket/PBAPI/Methods.php',
            'sources_custom/photobucket/PBAPI/Request/curl.php',
            'sources_custom/photobucket/PBAPI/Request/fopenurl.php',
            'sources_custom/photobucket/PBAPI/Request/fsockopen.php',
            'sources_custom/photobucket/PBAPI/Request.php',
            'sources_custom/photobucket/PBAPI/Response/json.php',
            'sources_custom/photobucket/PBAPI/Response/phpserialize.php',
            'sources_custom/photobucket/PBAPI/Response/simplexml.php',
            'sources_custom/photobucket/PBAPI/Response/simplexmlarray.php',
            'sources_custom/photobucket/PBAPI/Response/xmlserializer.php',
            'sources_custom/photobucket/PBAPI/Response.php',
            'sources_custom/photobucket/PBAPI.php',
            'sources_custom/hooks/systems/upload_syndication/photobucket.php',
            'sources_custom/hooks/systems/config/photobucket_client_id.php',
            'sources_custom/hooks/systems/config/photobucket_client_secret.php',
            'lang_custom/EN/video_syndication_photobucket.ini',
        );
    }
}

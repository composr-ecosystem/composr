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
class Hook_addon_registry_cloudinary
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
        return 'ocProducts';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'Contains code from Cloudinary Inc',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Automatically syndicate some uploads to Cloudinary, rather than storing them locally. Not supported for general use, should be managed by a programmer capable of patching any side-issues that may occur.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [
            'tut_performance',
        ];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [],
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
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/cloudinary.php',
            'sources_custom/hooks/systems/config/cloudinary_transfer_directories.php',
            'sources_custom/hooks/systems/config/cloudinary_cloud_name.php',
            'sources_custom/hooks/systems/config/cloudinary_api_key.php',
            'sources_custom/hooks/systems/config/cloudinary_api_secret.php',
            'sources_custom/hooks/systems/config/cloudinary_test_mode.php',
            'lang_custom/EN/cloundinary.ini',
            'sources_custom/hooks/systems/cdn_transfer/cloudinary.php',
            'sources_custom/hooks/systems/health_checks/cloudinary.php',

            'sources_custom/Cloudinary/index.html',
            'sources_custom/Cloudinary/.htaccess',
            'sources_custom/Cloudinary/autoload.php',
            'sources_custom/Cloudinary/src/.htaccess',
            'sources_custom/Cloudinary/src/index.html',
            'sources_custom/Cloudinary/src/Api/AlreadyExists.php',
            'sources_custom/Cloudinary/src/Api/AuthorizationRequired.php',
            'sources_custom/Cloudinary/src/Api/BadRequest.php',
            'sources_custom/Cloudinary/src/Api/Error.php',
            'sources_custom/Cloudinary/src/Api/GeneralError.php',
            'sources_custom/Cloudinary/src/Api/.htaccess',
            'sources_custom/Cloudinary/src/Api/index.html',
            'sources_custom/Cloudinary/src/Api/NotAllowed.php',
            'sources_custom/Cloudinary/src/Api/NotFound.php',
            'sources_custom/Cloudinary/src/Api.php',
            'sources_custom/Cloudinary/src/Api/RateLimited.php',
            'sources_custom/Cloudinary/src/Api/Response.php',
            'sources_custom/Cloudinary/src/AuthToken.php',
            'sources_custom/Cloudinary/src/cacert.pem',
            'sources_custom/Cloudinary/src/Cache/Adapter/CacheAdapter.php',
            'sources_custom/Cloudinary/src/Cache/Adapter/.htaccess',
            'sources_custom/Cloudinary/src/Cache/Adapter/index.html',
            'sources_custom/Cloudinary/src/Cache/Adapter/KeyValueCacheAdapter.php',
            'sources_custom/Cloudinary/src/Cache/.htaccess',
            'sources_custom/Cloudinary/src/Cache/index.html',
            'sources_custom/Cloudinary/src/Cache/ResponsiveBreakpointsCache.php',
            'sources_custom/Cloudinary/src/Cache/Storage/FileSystemKeyValueStorage.php',
            'sources_custom/Cloudinary/src/Cache/Storage/.htaccess',
            'sources_custom/Cloudinary/src/Cache/Storage/index.html',
            'sources_custom/Cloudinary/src/Cache/Storage/KeyValueStorage.php',
            'sources_custom/Cloudinary/src/CloudinaryField.php',
            'sources_custom/Cloudinary/src/Cloudinary.php',
            'sources_custom/Cloudinary/src/Error.php',
            'sources_custom/Cloudinary/src/Helpers.php',
            'sources_custom/Cloudinary/src/HttpClient.php',
            'sources_custom/Cloudinary/src/PreloadedFile.php',
            'sources_custom/Cloudinary/src/Search.php',
            'sources_custom/Cloudinary/src/SignatureVerifier.php',
            'sources_custom/Cloudinary/src/Uploader.php',
            'sources_custom/Cloudinary/src/Utils/.htaccess',
            'sources_custom/Cloudinary/src/Utils/index.html',
            'sources_custom/Cloudinary/src/Utils/Singleton.php',
        ];
    }
}

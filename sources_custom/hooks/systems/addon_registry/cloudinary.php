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
class Hook_addon_registry_cloudinary
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
        return 'ocProducts';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'Contains code from Cloudinary Inc',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Automatically syndicate some uploads to Cloudinary, rather than storing them locally. Not supported for general use, should be managed by a programmer capable of patching any side-issues that may occur.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array(
            'tut_optimisation',
        );
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
            ),
            'recommends' => array(),
            'conflicts_with' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/cloudinary.php',
            'sources_custom/hooks/systems/config/cloudinary_transfer_directories.php',
            'sources_custom/hooks/systems/config/cloudinary_cloud_name.php',
            'sources_custom/hooks/systems/config/cloudinary_api_key.php',
            'sources_custom/hooks/systems/config/cloudinary_api_secret.php',
            'sources_custom/hooks/systems/config/cloudinary_test_mode.php',
            'lang_custom/EN/cloundinary.ini',
            'sources_custom/Cloudinary/Cloudinary.php',
            'sources_custom/Cloudinary/Helpers.php',
            'sources_custom/Cloudinary/Uploader.php',
            'sources_custom/Cloudinary/cacert.pem',
            'sources_custom/Cloudinary/index.html',
            'sources_custom/hooks/systems/cdn_transfer/cloudinary.php',
            'sources_custom/Cloudinary/.htaccess',
        );
    }
}

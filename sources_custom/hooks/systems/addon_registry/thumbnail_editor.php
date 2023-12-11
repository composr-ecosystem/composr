<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    thumbnail_editor
 */

/**
 * Hook class.
 */
class Hook_addon_registry_thumbnail_editor
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
        return 'Admin Utilities';
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
            'webmotionuk',
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
        return 'Allow inline attachments to get a custom-created thumbnail, via an integrated editing tool. After creating the attachment an automatic thumbnail will be generated, and then anyone with Admin Zone access gets the chance to customise it by choosing the size, cropping, and scaling.';
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
                'GD',
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
        return 'themes/default/images/icons/48x48/buttons/thumbnail.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/thumbnail_editor.php',
            'themes/default/templates_custom/MEDIA_IMAGE_WEBSAFE.tpl',
            'sources_custom/hooks/systems/cleanup/image_thumbs.php',
            'data_custom/upload-crop/upload_crop_v1.2.php',
            'data_custom/upload-crop/js/jquery.imgareaselect.min.js',
            'data_custom/upload-crop/js/jquery-pack.js',
            'data_custom/upload-crop/index.html',
            'data_custom/upload-crop/js/index.html',
            'data_custom/upload-crop/upload_pic/index.html',
        );
    }
}

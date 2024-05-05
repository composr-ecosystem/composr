<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    transliteration
 */

/**
 * Hook class.
 */
class Hook_addon_registry_transliteration
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
     * Get the version of the software this addon is for (used in generating the TAR filename).
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
        return 'Architecture';
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
        return ['Konsta Vesterinen', 'Jonathan H. Wage', 'Other unknown developers'];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Artistic License (https://github.com/Behat/Transliterator/blob/master/LICENSE)';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'This provides transliteration support to those without the PHP [tt]intl[/tt] extension. This is used for URL moniker generation.';
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
            'sources_custom/Transliterator/.htaccess',
            'sources_custom/Transliterator/Transliterator.php',
            'sources_custom/Transliterator/data/index.html',
            'sources_custom/Transliterator/data/x00.php',
            'sources_custom/Transliterator/data/x01.php',
            'sources_custom/Transliterator/data/x02.php',
            'sources_custom/Transliterator/data/x03.php',
            'sources_custom/Transliterator/data/x04.php',
            'sources_custom/Transliterator/data/x05.php',
            'sources_custom/Transliterator/data/x06.php',
            'sources_custom/Transliterator/data/x07.php',
            'sources_custom/Transliterator/data/x09.php',
            'sources_custom/Transliterator/data/x0a.php',
            'sources_custom/Transliterator/data/x0b.php',
            'sources_custom/Transliterator/data/x0c.php',
            'sources_custom/Transliterator/data/x0d.php',
            'sources_custom/Transliterator/data/x0e.php',
            'sources_custom/Transliterator/data/x0f.php',
            'sources_custom/Transliterator/data/x10.php',
            'sources_custom/Transliterator/data/x11.php',
            'sources_custom/Transliterator/data/x12.php',
            'sources_custom/Transliterator/data/x13.php',
            'sources_custom/Transliterator/data/x14.php',
            'sources_custom/Transliterator/data/x15.php',
            'sources_custom/Transliterator/data/x16.php',
            'sources_custom/Transliterator/data/x17.php',
            'sources_custom/Transliterator/data/x18.php',
            'sources_custom/Transliterator/data/x1e.php',
            'sources_custom/Transliterator/data/x1f.php',
            'sources_custom/Transliterator/data/x20.php',
            'sources_custom/Transliterator/data/x21.php',
            'sources_custom/Transliterator/data/x24.php',
            'sources_custom/Transliterator/data/x25.php',
            'sources_custom/Transliterator/data/x26.php',
            'sources_custom/Transliterator/data/x27.php',
            'sources_custom/Transliterator/data/x28.php',
            'sources_custom/Transliterator/data/x30.php',
            'sources_custom/Transliterator/data/x31.php',
            'sources_custom/Transliterator/data/x32.php',
            'sources_custom/Transliterator/data/x33.php',
            'sources_custom/Transliterator/data/x4e.php',
            'sources_custom/Transliterator/data/x4f.php',
            'sources_custom/Transliterator/data/x50.php',
            'sources_custom/Transliterator/data/x51.php',
            'sources_custom/Transliterator/data/x52.php',
            'sources_custom/Transliterator/data/x53.php',
            'sources_custom/Transliterator/data/x54.php',
            'sources_custom/Transliterator/data/x55.php',
            'sources_custom/Transliterator/data/x56.php',
            'sources_custom/Transliterator/data/x57.php',
            'sources_custom/Transliterator/data/x58.php',
            'sources_custom/Transliterator/data/x59.php',
            'sources_custom/Transliterator/data/x5a.php',
            'sources_custom/Transliterator/data/x5b.php',
            'sources_custom/Transliterator/data/x5c.php',
            'sources_custom/Transliterator/data/x5d.php',
            'sources_custom/Transliterator/data/x5e.php',
            'sources_custom/Transliterator/data/x5f.php',
            'sources_custom/Transliterator/data/x60.php',
            'sources_custom/Transliterator/data/x61.php',
            'sources_custom/Transliterator/data/x62.php',
            'sources_custom/Transliterator/data/x63.php',
            'sources_custom/Transliterator/data/x64.php',
            'sources_custom/Transliterator/data/x65.php',
            'sources_custom/Transliterator/data/x66.php',
            'sources_custom/Transliterator/data/x67.php',
            'sources_custom/Transliterator/data/x68.php',
            'sources_custom/Transliterator/data/x69.php',
            'sources_custom/Transliterator/data/x6a.php',
            'sources_custom/Transliterator/data/x6b.php',
            'sources_custom/Transliterator/data/x6c.php',
            'sources_custom/Transliterator/data/x6d.php',
            'sources_custom/Transliterator/data/x6e.php',
            'sources_custom/Transliterator/data/x6f.php',
            'sources_custom/Transliterator/data/x70.php',
            'sources_custom/Transliterator/data/x71.php',
            'sources_custom/Transliterator/data/x72.php',
            'sources_custom/Transliterator/data/x73.php',
            'sources_custom/Transliterator/data/x74.php',
            'sources_custom/Transliterator/data/x75.php',
            'sources_custom/Transliterator/data/x76.php',
            'sources_custom/Transliterator/data/x77.php',
            'sources_custom/Transliterator/data/x78.php',
            'sources_custom/Transliterator/data/x79.php',
            'sources_custom/Transliterator/data/x7a.php',
            'sources_custom/Transliterator/data/x7b.php',
            'sources_custom/Transliterator/data/x7c.php',
            'sources_custom/Transliterator/data/x7d.php',
            'sources_custom/Transliterator/data/x7e.php',
            'sources_custom/Transliterator/data/x7f.php',
            'sources_custom/Transliterator/data/x80.php',
            'sources_custom/Transliterator/data/x81.php',
            'sources_custom/Transliterator/data/x82.php',
            'sources_custom/Transliterator/data/x83.php',
            'sources_custom/Transliterator/data/x84.php',
            'sources_custom/Transliterator/data/x85.php',
            'sources_custom/Transliterator/data/x86.php',
            'sources_custom/Transliterator/data/x87.php',
            'sources_custom/Transliterator/data/x88.php',
            'sources_custom/Transliterator/data/x89.php',
            'sources_custom/Transliterator/data/x8a.php',
            'sources_custom/Transliterator/data/x8b.php',
            'sources_custom/Transliterator/data/x8c.php',
            'sources_custom/Transliterator/data/x8d.php',
            'sources_custom/Transliterator/data/x8e.php',
            'sources_custom/Transliterator/data/x8f.php',
            'sources_custom/Transliterator/data/x90.php',
            'sources_custom/Transliterator/data/x91.php',
            'sources_custom/Transliterator/data/x92.php',
            'sources_custom/Transliterator/data/x93.php',
            'sources_custom/Transliterator/data/x94.php',
            'sources_custom/Transliterator/data/x95.php',
            'sources_custom/Transliterator/data/x96.php',
            'sources_custom/Transliterator/data/x97.php',
            'sources_custom/Transliterator/data/x98.php',
            'sources_custom/Transliterator/data/x99.php',
            'sources_custom/Transliterator/data/x9a.php',
            'sources_custom/Transliterator/data/x9b.php',
            'sources_custom/Transliterator/data/x9c.php',
            'sources_custom/Transliterator/data/x9d.php',
            'sources_custom/Transliterator/data/x9e.php',
            'sources_custom/Transliterator/data/x9f.php',
            'sources_custom/Transliterator/data/xa0.php',
            'sources_custom/Transliterator/data/xa1.php',
            'sources_custom/Transliterator/data/xa2.php',
            'sources_custom/Transliterator/data/xa3.php',
            'sources_custom/Transliterator/data/xa4.php',
            'sources_custom/Transliterator/data/xac.php',
            'sources_custom/Transliterator/data/xad.php',
            'sources_custom/Transliterator/data/xae.php',
            'sources_custom/Transliterator/data/xaf.php',
            'sources_custom/Transliterator/data/xb0.php',
            'sources_custom/Transliterator/data/xb1.php',
            'sources_custom/Transliterator/data/xb2.php',
            'sources_custom/Transliterator/data/xb3.php',
            'sources_custom/Transliterator/data/xb4.php',
            'sources_custom/Transliterator/data/xb5.php',
            'sources_custom/Transliterator/data/xb6.php',
            'sources_custom/Transliterator/data/xb7.php',
            'sources_custom/Transliterator/data/xb8.php',
            'sources_custom/Transliterator/data/xb9.php',
            'sources_custom/Transliterator/data/xba.php',
            'sources_custom/Transliterator/data/xbb.php',
            'sources_custom/Transliterator/data/xbc.php',
            'sources_custom/Transliterator/data/xbd.php',
            'sources_custom/Transliterator/data/xbe.php',
            'sources_custom/Transliterator/data/xbf.php',
            'sources_custom/Transliterator/data/xc0.php',
            'sources_custom/Transliterator/data/xc1.php',
            'sources_custom/Transliterator/data/xc2.php',
            'sources_custom/Transliterator/data/xc3.php',
            'sources_custom/Transliterator/data/xc4.php',
            'sources_custom/Transliterator/data/xc5.php',
            'sources_custom/Transliterator/data/xc6.php',
            'sources_custom/Transliterator/data/xc7.php',
            'sources_custom/Transliterator/data/xc8.php',
            'sources_custom/Transliterator/data/xc9.php',
            'sources_custom/Transliterator/data/xca.php',
            'sources_custom/Transliterator/data/xcb.php',
            'sources_custom/Transliterator/data/xcc.php',
            'sources_custom/Transliterator/data/xcd.php',
            'sources_custom/Transliterator/data/xce.php',
            'sources_custom/Transliterator/data/xcf.php',
            'sources_custom/Transliterator/data/xd0.php',
            'sources_custom/Transliterator/data/xd1.php',
            'sources_custom/Transliterator/data/xd2.php',
            'sources_custom/Transliterator/data/xd3.php',
            'sources_custom/Transliterator/data/xd4.php',
            'sources_custom/Transliterator/data/xd5.php',
            'sources_custom/Transliterator/data/xd6.php',
            'sources_custom/Transliterator/data/xd7.php',
            'sources_custom/Transliterator/data/xf9.php',
            'sources_custom/Transliterator/data/xfa.php',
            'sources_custom/Transliterator/data/xfb.php',
            'sources_custom/Transliterator/data/xfc.php',
            'sources_custom/Transliterator/data/xfd.php',
            'sources_custom/Transliterator/data/xfe.php',
            'sources_custom/Transliterator/data/xff.php',
            'sources_custom/Transliterator/index.html',
            'sources_custom/hooks/systems/addon_registry/transliteration.php',
            'sources_custom/hooks/systems/startup/transliteration.php',
            'sources_custom/transliteration.php',
        ];
    }
}

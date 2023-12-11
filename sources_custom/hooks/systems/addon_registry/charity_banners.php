<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    charity_banners
 */

/**
 * Hook class.
 */
class Hook_addon_registry_charity_banners
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
        return 'Information Display';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Kamen Blaginov';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
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
        return 'Automatically creates a [tt]button[/tt] banner type and banners for it and set them in a main (or side) block. Administrator could enable/disable banners, and also add custom banners.

The auto-created bundled banners are for \'causes\' and are: Composr, Firefox, W3C XHTML, W3C CSS, W3C WCAG, CancerResearch, RSPCA, PETA, Unicef, WWF, Greenpeace, HelpTheAged, NSPCC, Oxfam, BringDownIE6, CND, Amnesty International, British Heart Foundation, GNU.

To Use the Block go to where you would like the block to be placed (likely either a side panel or the front page) and use the add block button.

You have 3 block parameters to fill in:
 - [tt]param[/tt] will be "buttons" as standard but you could create a different banner type and use that if you want.
 - You only need to put something in [tt]extra[/tt] if its a side panel in which case you put "side" in there.
 - [tt]max[/tt] is the maximum number of banners you want to display.

An example:
[code="Comcode"][block="buttons" extra="side" max="5"]main_buttons[/block][/code]
If you want to delete some of the banners:
1) Go to the Banners section under Content Management
2) Click Edit Banner
3) Choose a banner you want to delete
4) Select delete at the bottom and Save
5) Repeat till only the banners you want are showing

You can add more banners through this section, just make sure they are 120px &times; 60px.';
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
                'Conversr',
                'banners',
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
            'sources_custom/hooks/systems/addon_registry/charity_banners.php',
            'data_custom/images/causes/index.html',
            'sources_custom/blocks/main_buttons.php',
            'data_custom/images/causes/amnestyinternational.gif',
            'data_custom/images/causes/bhf.gif',
            'data_custom/images/causes/cancerresearch.gif',
            'data_custom/images/causes/cnd.gif',
            'data_custom/images/causes/firefox.gif',
            'data_custom/images/causes/GNU.gif',
            'data_custom/images/causes/greenpeace.gif',
            'data_custom/images/causes/helptheaged.gif',
            'data_custom/images/causes/nspcc.gif',
            'data_custom/images/causes/composr.gif',
            'data_custom/images/causes/oxfam.gif',
            'data_custom/images/causes/peta.gif',
            'data_custom/images/causes/rspca.gif',
            'data_custom/images/causes/unicef.gif',
            'data_custom/images/causes/w3c-css.gif',
            'data_custom/images/causes/w3c-xhtml.gif',
            'data_custom/images/causes/wwf.gif',
            'themes/default/templates_custom/BLOCK_MAIN_BANNER_WAVE_BWRAP_CUSTOM.tpl',
            'themes/default/templates_custom/BLOCK_MAIN_BUTTONS.tpl',
            'sources_custom/banners3.php',
            'lang_custom/EN/buttons.ini',
        );
    }
}

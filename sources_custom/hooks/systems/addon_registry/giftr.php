<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    giftr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_giftr
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array(
            'uploads/giftr_addon',
        );
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
        return 'Fun and Games';
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
        return 'Provides the ability for members to purchase a wide variety of configurable gifts and to send them to other members or even to send them themselves.

The gifts are configurable by the admin section:
 - gift title (name)
 - gift image
 - gift price (in points)

When a gift is sent to a member it creates a Private Topic that describes the gift. Also, it places the gift in the list of gifts received by the member in the profile section. Gifts also could be sent anonymously to members.

Creating new Gifts:
When creating new gifts please only use images which are free to use, we suggest [url="http://www.openclipart.org/"]Open Clipart[/url] which has a good selection of free to use images. Go to the set up section and click "Manage Gifts". Click Add Gift. Upload the image and give it a title, choose the cost to send and click save. You can edit the standard gifts or ones you have created in the same section.';
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
                'pointstore',
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
        return 'themes/default/images_custom/icons/48x48/menu/giftr.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images_custom/icons/24x24/menu/giftr.png',
            'themes/default/images_custom/icons/48x48/menu/giftr.png',
            'sources_custom/hooks/systems/addon_registry/giftr.php',
            'sources_custom/hooks/systems/notifications/gift.php',
            'adminzone/pages/modules_custom/admin_giftr.php',
            'lang_custom/EN/giftr.ini',
            'sources_custom/hooks/modules/members/gifts.php',
            'sources_custom/hooks/modules/pointstore/giftr.php',
            'sources_custom/hooks/systems/page_groupings/giftr.php',
            'themes/default/templates_custom/POINTSTORE_GIFTR.tpl',
            'themes/default/templates_custom/POINTSTORE_GIFTR_GIFTS.tpl',
            'themes/default/templates_custom/CNS_MEMBER_SCREEN_GIFTS_WRAP.tpl',
            'themes/default/css_custom/gifts.css',
            'uploads/giftr_addon/index.html',
            'data_custom/images/giftr/2a kiss.jpg',
            'data_custom/images/giftr/2Football_(soccer).gif',
            'data_custom/images/giftr/2Muga_Glass_of_red_wine.png',
            'data_custom/images/giftr/3Football_(soccer).gif',
            'data_custom/images/giftr/a kiss.jpg',
            'data_custom/images/giftr/Birthday_cake.gif',
            'data_custom/images/giftr/bouquet_of_flowers.gif',
            'data_custom/images/giftr/Champagne.jpg',
            'data_custom/images/giftr/Drum_Kit_3.jpg',
            'data_custom/images/giftr/electric_guitar.jpg',
            'data_custom/images/giftr/Football_(soccer).gif',
            'data_custom/images/giftr/ghirlande_festa.gif',
            'data_custom/images/giftr/glass_of_beer.gif',
            'data_custom/images/giftr/hrum_cocktail.gif',
            'data_custom/images/giftr/jean_victor_balin_balloons.gif',
            'data_custom/images/giftr/liftarn_Four_leaf_clover.gif',
            'data_custom/images/giftr/liftarn_Green_hat.gif',
            'data_custom/images/giftr/love note.jpg',
            'data_custom/images/giftr/love_heart.gif',
            'data_custom/images/giftr/Money_Bag_Icon.gif',
            'data_custom/images/giftr/Muga_Glass_of_red_wine.png',
            'data_custom/images/giftr/piano.jpg',
            'data_custom/images/giftr/red-rose.jpg',
            'data_custom/images/giftr/reporter_Happy_Valentine.gif',
            'data_custom/images/giftr/Santa_Hat.jpg',
            'data_custom/images/giftr/index.html',
            'themes/default/templates_custom/BLOCK_MAIN_BOTTOM_BAR.tpl',
            'themes/default/templates_custom/CNS_USER_MEMBER.tpl',
        );
    }
}

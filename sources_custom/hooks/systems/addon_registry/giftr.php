<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    giftr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_giftr
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        $ret = [
            'uploads/giftr_addon',
        ];
        if ($runtime) {
            $ret = array_merge($ret, [
                'uploads/giftr_addon/*',
            ]);
        }
        return $ret;
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11'; // addon_version_auto_update b60d471cc43f43ab4e77f6e5a613ab41
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
        return 11.9;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Fun and Games';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Kamen Blaginov';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Provides the ability for members to purchase a wide variety of configurable gifts and to send them to other members or even to send them themselves.

The gifts are configurable by the admin section:
 - gift title (name)
 - gift image
 - gift price (in points)

When a gift is sent to a member it creates a Private Topic that describes the gift. Also, it places the gift in the list of gifts received by the member in the profile section. Gifts also could be sent anonymously to members.

Creating new Gifts:
When creating new gifts please only use images which are free to use, we suggest [url="https://openclipart.org/"]Open Clipart[/url] which has a good selection of free to use images. Go to the set up section and click "Manage Gifts". Click Add Gift. Upload the image and give it a title, choose the price and click save. You can edit the standard gifts or ones you have created in the same section.';
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
                'Conversr',
                'points',
                'ecommerce',
                //'core_all_icons',
            ],
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
        return 'themes/default/images/icons/spare/gifts.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'adminzone/pages/modules_custom/admin_giftr.php',
            'data_custom/images/giftr/birthday_cake.gif',
            'data_custom/images/giftr/bouquet_of_flowers.gif',
            'data_custom/images/giftr/champagne.jpg',
            'data_custom/images/giftr/drum_kit.jpg',
            'data_custom/images/giftr/electric_guitar.jpg',
            'data_custom/images/giftr/football.gif',
            'data_custom/images/giftr/ghirlande_festa.gif',
            'data_custom/images/giftr/glass_of_beer.gif',
            'data_custom/images/giftr/hrum_cocktail.gif',
            'data_custom/images/giftr/index.html',
            'data_custom/images/giftr/jean_victor_balin_balloons.gif',
            'data_custom/images/giftr/kiss.jpg',
            'data_custom/images/giftr/liftarn_four_leaf_clover.gif',
            'data_custom/images/giftr/liftarn_green_hat.gif',
            'data_custom/images/giftr/love_heart.gif',
            'data_custom/images/giftr/love_note.jpg',
            'data_custom/images/giftr/money_bag.gif',
            'data_custom/images/giftr/muga_glass_of_red_wine.png',
            'data_custom/images/giftr/piano.jpg',
            'data_custom/images/giftr/red_rose.jpg',
            'data_custom/images/giftr/reporter_happy_valentine.gif',
            'data_custom/images/giftr/santa_hat.jpg',
            'lang_custom/EN/giftr.ini',
            'sources_custom/hooks/modules/members/gifts.php',
            'sources_custom/hooks/systems/actionlog/giftr.php',
            'sources_custom/hooks/systems/addon_registry/giftr.php',
            'sources_custom/hooks/systems/contentious_overrides/giftr.php',
            'sources_custom/hooks/systems/ecommerce/giftr.php',
            'sources_custom/hooks/systems/notifications/gift.php',
            'sources_custom/hooks/systems/page_groupings/giftr.php',
            'sources_custom/hooks/systems/privacy/giftr.php',
            'themes/default/css_custom/gifts.css',
            'themes/default/templates_custom/CNS_MEMBER_SCREEN_GIFTS_WRAP.tpl',
            'themes/default/templates_custom/CNS_USER_MEMBER.tpl',
            'uploads/giftr_addon/index.html',
        ];
    }
}

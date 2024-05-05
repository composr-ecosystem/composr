<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    booking
 */

/**
 * Hook class.
 */
class Hook_addon_registry_booking
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
        return 'Sophisticated booking system. Not yet fully finished for public use, but fully cohesive and suitable for some use cases.

You may wish to deny access to the usergroup and member directories when using this addon, to prevent leakage of customer lists.';
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
                'calendar',
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
        return 'themes/default/images/icons/booking/book.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'cms/pages/modules_custom/cms_booking.php',
            'data_custom/bookables_ical.php',
            'data_custom/booking_price_ajax.php',
            'data_custom/bookings_ical.php',
            'lang_custom/EN/booking.ini',
            'site/pages/modules_custom/booking.php',
            'sources_custom/blocks/main_choose_to_book.php',
            'sources_custom/blocks/side_book_date_range.php',
            'sources_custom/blocks/side_choose_showing.php',
            'sources_custom/booking.php',
            'sources_custom/booking2.php',
            'sources_custom/bookings_ical.php',
            'sources_custom/hooks/modules/members/booking.php',
            'sources_custom/hooks/systems/actionlog/booking.php',
            'sources_custom/hooks/systems/addon_registry/booking.php',
            'sources_custom/hooks/systems/config/bookings_max_ahead_months.php',
            'sources_custom/hooks/systems/config/bookings_show_warnings_for_months.php',
            'sources_custom/hooks/systems/config/member_booking_only.php',
            'sources_custom/hooks/systems/notifications/booking_customer.php',
            'sources_custom/hooks/systems/notifications/booking_inform_staff.php',
            'sources_custom/hooks/systems/page_groupings/booking.php',
            'sources_custom/hooks/systems/privacy/booking.php',
            'themes/default/images/icons/booking/blacked.svg',
            'themes/default/images/icons/booking/book.svg',
            'themes/default/images/icons/booking/bookable.svg',
            'themes/default/images/icons/booking/booking.svg',
            'themes/default/images/icons/booking/index.html',
            'themes/default/images/icons/booking/supplement.svg',
            'themes/default/images/icons_monochrome/booking/blacked.svg',
            'themes/default/images/icons_monochrome/booking/book.svg',
            'themes/default/images/icons_monochrome/booking/bookable.svg',
            'themes/default/images/icons_monochrome/booking/booking.svg',
            'themes/default/images/icons_monochrome/booking/index.html',
            'themes/default/images/icons_monochrome/booking/supplement.svg',
            'themes/default/images_custom/icons/calendar/booking.svg',
            'themes/default/images_custom/icons/calendar/index.html',
            'themes/default/images_custom/icons_monochrome/calendar/booking.svg',
            'themes/default/images_custom/icons_monochrome/calendar/index.html',
            'themes/default/javascript_custom/booking.js',
            'themes/default/templates_custom/BLOCK_MAIN_CHOOSE_TO_BOOK.tpl',
            'themes/default/templates_custom/BLOCK_SIDE_BOOK_DATE_RANGE.tpl',
            'themes/default/templates_custom/BOOKABLE_NOTES.tpl',
            'themes/default/templates_custom/BOOKING_CONFIRM_FCOMCODE.tpl',
            'themes/default/templates_custom/BOOKING_DISPLAY.tpl',
            'themes/default/templates_custom/BOOKING_FLESH_OUT_SCREEN.tpl',
            'themes/default/templates_custom/BOOKING_JOIN_OR_LOGIN_SCREEN.tpl',
            'themes/default/templates_custom/BOOKING_NOTICE_FCOMCODE.tpl',
            'themes/default/templates_custom/BOOKING_START_SCREEN.tpl',
            'themes/default/templates_custom/BOOK_DATE_CHOOSE.tpl',
        ];
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    booking
 */

/**
 * Hook class.
 */
class Hook_members_booking
{
    /**
     * Find member-related links to inject to details section of the about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting links for
     * @return array List of pairs: title to value
     */
    public function run(int $member_id) : array
    {
        if (!addon_installed('booking')) {
            return [];
        }

        if (!has_actual_page_access(get_member(), 'cms_booking')) {
            return [];
        }

        require_lang('booking');
        require_code('booking');
        require_code('booking2');

        $zone = get_module_zone('cms_booking');

        $request = get_member_booking_request($member_id);

        $links = [];

        foreach ($request as $i => $r) {
            $from = get_timezoned_date(mktime(0, 0, 0, $r['start_month'], $r['start_day'], $r['start_year']));
            $to = get_timezoned_date(mktime(0, 0, 0, $r['end_month'], $r['end_day'], $r['end_year']));

            $bookable = $GLOBALS['SITE_DB']->query_select('bookable', ['*'], ['id' => $r['bookable_id']], '', 1);
            if (!array_key_exists(0, $bookable)) {
                continue;
            }

            $links[] = [
                'content',
                do_lang_tempcode('BOOKING_EDIT', escape_html($from), escape_html($to), get_translated_tempcode('bookable', $bookable[0], 'title')),
                build_url(['page' => 'cms_booking', 'type' => '_edit_booking', 'id' => strval($member_id) . '_' . strval($i)], $zone),
                'booking/booking',
            ];
        }

        return $links;
    }
}

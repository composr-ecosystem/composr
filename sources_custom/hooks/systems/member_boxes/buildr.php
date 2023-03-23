<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    buildr
 */

/**
 * Hook class.
 */
class Hook_member_boxes_buildr
{
    /**
     * Find member box details.
     *
     * @param  MEMBER $member_id The ID of the member we are getting extra details for
     * @return array Map of extra box details
     */
    public function run(int $member_id) : array
    {
        if (!addon_installed('buildr')) {
            return null;
        }

        if (!addon_installed('points')) {
            return null;
        }
        if (!addon_installed('ecommerce')) {
            return null;
        }
        if (!addon_installed('chat')) {
            return null;
        }

        $zone = get_page_zone('buildr', false);
        if ($zone === null) {
            return [];
        }
        if (!has_zone_access(get_member(), $zone)) {
            return [];
        }

        $rows = $GLOBALS['SITE_DB']->query_select('w_members m JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_realms r ON m.location_realm=r.id JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_rooms rm ON m.location_x=rm.location_x AND m.location_y=rm.location_y AND m.location_realm=rm.location_realm', ['m.*', 'r.*', 'rm.name AS room_name'], ['m.id' => $member_id], '', 1);
        if (!array_key_exists(0, $rows)) {
            return [];
        }

        $row = $rows[0];

        require_lang('buildr');

        return [
            do_lang('_W_ROOM') => do_lang('W_ROOM_COORD', escape_html($row['room_name']), strval($row['location_realm']), [strval($row['location_x']), strval($row['location_y'])]),
            do_lang('_W_REALM') => $row['name'],
        ];
    }
}

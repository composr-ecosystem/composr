<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    buildr
 */

/**
 * Hook class.
 */
class Hook_topicview_buildr
{
    /**
     * Execute the module.
     *
     * @param  MEMBER $member_id The ID of the member we are getting detail hooks for
     * @return ?Tempcode Results (null: no action)
     */
    public function run($member_id)
    {
        if (!$GLOBALS['SITE_DB']->table_exists('w_realms')) {
            return null;
        }

        global $BUILDR_MEMBER_CACHE;
        if (!isset($BUILDR_MEMBER_CACHE)) {
            $BUILDR_MEMBER_CACHE = array();
        }
        if (array_key_exists($member_id, $BUILDR_MEMBER_CACHE)) {
            return $BUILDR_MEMBER_CACHE[$member_id];
        }

        $zone = get_page_zone('buildr', false);
        if (is_null($zone)) {
            return null;
        }
        if (!has_zone_access(get_member(), $zone)) {
            return null;
        }

        $rows = $GLOBALS['SITE_DB']->query_select('w_members m JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_realms r ON m.location_realm=r.id JOIN ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'w_rooms rm ON m.location_x=rm.location_x AND m.location_y=rm.location_y AND m.location_realm=rm.location_realm', array('m.*', 'r.*', 'rm.name AS room_name'), array('m.id' => $member_id), '', 1);
        if (array_key_exists(0, $rows)) {
            $row = $rows[0];

            require_lang('buildr');
            $a = do_template('CNS_MEMBER_BOX_CUSTOM_FIELD', array('_GUID' => '3d36d5ae8bcb66d59a0676200571fb1a', 'NAME' => do_lang_tempcode('_W_ROOM'), 'VALUE' => do_lang_tempcode('W_ROOM_COORD', escape_html($row['room_name']), strval($row['location_realm']), array(strval($row['location_x']), strval($row['location_y'])))));
            $b = do_template('CNS_MEMBER_BOX_CUSTOM_FIELD', array('_GUID' => '72c62771f7796d69d1f1a616c2591206', 'NAME' => do_lang_tempcode('_W_REALM'), 'VALUE' => $row['name']));
            $a->attach($b);

            $BUILDR_MEMBER_CACHE[$member_id] = $a;

            return $a;
        }
        return null;
    }
}

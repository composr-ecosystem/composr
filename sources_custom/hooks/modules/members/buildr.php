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
class Hook_members_buildr
{
    /**
     * Find member-related links to inject to details section of the about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting links for
     * @return array List of pairs: title to value.
     */
    public function run($member_id)
    {
        if (!addon_installed('buildr')) {
            return array();
        }

        if (!$GLOBALS['SITE_DB']->table_exists('w_members')) {
            return array();
        }

        $zone = get_page_zone('buildr', false);
        if (is_null($zone)) {
            return array();
        }
        if (!has_zone_access(get_member(), $zone)) {
            return array();
        }

        $id = $GLOBALS['SITE_DB']->query_select_value_if_there('w_members', 'id', array('id' => $member_id), '', true);
        if (!is_null($id)) {
            require_lang('buildr');
            return array(array('audit', do_lang_tempcode('BUILDR'), build_url(array('page' => 'buildr', 'type' => 'inventory', 'member' => $member_id), get_page_zone('buildr')), 'menu/buildr'));
        }
        return array();
    }
}

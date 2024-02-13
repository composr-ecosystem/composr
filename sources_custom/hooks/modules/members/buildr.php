<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
     * @return array List of pairs: title to value
     */
    public function run(int $member_id) : array
    {
        if (!addon_installed('buildr')) {
            return [];
        }

        $zone = get_page_zone('buildr', false);
        if ($zone === null) {
            return [];
        }
        if (!has_zone_access(get_member(), $zone)) {
            return [];
        }

        $id = $GLOBALS['SITE_DB']->query_select_value_if_there('w_members', 'id', ['id' => $member_id]);
        if ($id !== null) {
            require_lang('buildr');
            return [['audit', do_lang_tempcode('BUILDR'), build_url(['page' => 'buildr', 'type' => 'inventory', 'member' => $member_id], get_module_zone('buildr')), 'spare/world']];
        }
        return [];
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    member_filedumps
 */

/**
 * Hook class.
 */
class Hook_members_filedump
{
    /**
     * Find member-related links to inject to details section of the about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting links for
     * @return array List of pairs: title to value
     */
    public function run(int $member_id) : array
    {
        if (!addon_installed('member_filedumps')) {
            return [];
        }

        if (!addon_installed('filedump')) {
            return [];
        }

        $zone = get_page_zone('filedump', false);
        if ($zone === null) {
            return [];
        }
        if (!has_zone_access(get_member(), $zone)) {
            return [];
        }

        require_lang('filedump');

        $path = $GLOBALS['FORUM_DRIVER']->get_username($member_id);

        return [['content', do_lang_tempcode('FILEDUMP'), build_url(['page' => 'filedump', 'type' => 'browse', 'subpath' => '/' . $path . '/'], $zone), 'menu/cms/filedump']];
    }
}

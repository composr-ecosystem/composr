<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    member_filedumps
 */

/**
 * Hook class.
 */
class Hook_profiles_tabs_filedump
{
    /**
     * Find whether this hook is active.
     *
     * @param  MEMBER $member_id_of The ID of the member who is being viewed
     * @param  MEMBER $member_id_viewing The ID of the member who is doing the viewing
     * @return boolean Whether this hook is active
     */
    public function is_active($member_id_of, $member_id_viewing)
    {
        return (($member_id_of == $member_id_viewing) || (has_privilege($member_id_viewing, 'assume_any_member')));
    }

    /**
     * Render function for profile tab hooks.
     *
     * @param  MEMBER $member_id_of The ID of the member who is being viewed
     * @param  MEMBER $member_id_viewing The ID of the member who is doing the viewing
     * @param  boolean $leave_to_ajax_if_possible Whether to leave the tab contents null, if tis hook supports it, so that AJAX can load it later
     * @return array A tuple: The tab title, the tab contents, the suggested tab order, the icon
     */
    public function render_tab($member_id_of, $member_id_viewing, $leave_to_ajax_if_possible = false)
    {
        require_lang('filedump');

        $title = do_lang_tempcode('_FILEDUMP');

        $order = 70;

        if ($leave_to_ajax_if_possible) {
            return array($title, null, $order, 'menu/cms/filedump');
        }

        $content = do_template('CNS_MEMBER_PROFILE_FILEDUMP', array('_GUID' => '87c683590a6e2d435d877cec1c97baba', 'MEMBER_ID' => strval($member_id_of)));

        return array($title, $content, $order, 'menu/cms/filedump');
    }
}

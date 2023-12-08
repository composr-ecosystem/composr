<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    related_members
 */

/**
 * Hook class.
 */
class Hook_profiles_tabs_related
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
        require_lang('related');

        return (get_cms_cpf(do_lang('RELATED_CPF'), $member_id_of) != '');
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
        require_lang('related');

        $title = do_lang_tempcode('RELATED_MEMBERS');

        $order = 150;

        if ($leave_to_ajax_if_possible) {
            return array($title, null, $order, '');
        }

        require_css('cns_member_directory');

        $cpf_value = get_cms_cpf(do_lang('RELATED_CPF'), $member_id_of);
        $filter = do_lang('RELATED_CPF') . '=' . $cpf_value . ',id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ',id<>' . strval($member_id_of);
        $content = do_block('main_multi_content', array('param' => 'member', 'filter' => $filter, 'no_links' => '1', 'guid' => 'module'));

        return array($title, $content, $order, '');
    }
}

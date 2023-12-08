<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * Hook class.
 */
class Hook_profiles_tabs_activities
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
        return true;
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
        // Need to declare these here as the Tempcode engine can't look as deep, into a loop (I think), as it would need to, to find the block declaring the dependency
        require_lang('activities');
        require_css('activities');
        require_javascript('activities_state');
        require_javascript('activities');
        require_javascript('jquery');
        require_javascript('base64');

        require_code('site');
        set_feed_url('?mode=activities&select=' . strval($member_id_of));

        require_lang('activities');

        $title = do_lang_tempcode('ACTIVITY');

        $order = 70;

        // Allow user to link up things for syndication
        $syndications = array();
        if ($member_id_of == $member_id_viewing) {
            $dests = find_all_hooks('systems', 'syndication');
            foreach (array_keys($dests) as $hook) {
                require_code('hooks/systems/syndication/' . $hook);
                $ob = object_factory('Hook_syndication_' . $hook);
                if ($ob->is_available($member_id_of)) {
                    if (either_param_string('syndicate_stop__' . $hook, null) !== null) {
                        $ob->auth_unset($member_id_of);
                    } elseif (either_param_string('syndicate_start__' . $hook, null) !== null) {
                        $url_map = array('page' => '_SELF', 'type' => 'view', 'id' => ($member_id_of == get_member()) ? null : $member_id_of, 'oauth_in_progress' => 1);
                        foreach (array_keys($_POST) as $key) {
                            $url_map[$key] = post_param_string($key, '');
                        }
                        $url_map['syndicate_start__' . $hook] = 1;
                        $oauth_url = build_url($url_map, '_SELF', null, false, false, false, 'tab__activities');
                        $ob->auth_set($member_id_of, $oauth_url);
                    } elseif ((running_script('index')) && (!$leave_to_ajax_if_possible) && ($ob->auth_is_set($member_id_of)) && (either_param_string('oauth_in_progress', null) === null) && (!$GLOBALS['IS_ACTUALLY_ADMIN'])) {
                        /* running_script('index') won't work currently due to execution contexts, and it is never non-AJAX, and it's probably not needed anyway
                        // Do a refresh to make sure the token is updated
                        $url_map = array('page' => '_SELF', 'type' => 'view', 'id' => ($member_id_of == get_member()) ? null : $member_id_of, 'oauth_in_progress' => 1);
                        $url_map['syndicate_start__' . $hook] = 1;
                        $oauth_url = build_url($url_map, '_SELF', null, false, false, false, 'tab__activities');
                        $ob->auth_set($member_id_of, $oauth_url);
                        */
                    }

                    $syndications[$hook] = array(
                        'SYNDICATION_IS_SET' => $ob->auth_is_set($member_id_of),
                        'SYNDICATION_SERVICE_NAME' => $ob->get_service_name(),
                        'SYNDICATION_JAVASCRIPT' => method_exists($ob, 'syndication_javascript') ? $ob->syndication_javascript() : ''
                    );
                }
            }
        }

        if ($leave_to_ajax_if_possible) {
            return array($title, null, $order, 'tabs/member_account/activity');
        }

        $content = do_template('CNS_MEMBER_PROFILE_ACTIVITIES', array('_GUID' => '9fe3b8bb9a4975fa19631c43472b4539', 'MEMBER_ID' => strval($member_id_of), 'SYNDICATIONS' => $syndications));

        return array($title, $content, $order, 'tabs/member_account/activity');
    }
}

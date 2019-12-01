<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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
        if (!addon_installed('activity_feed')) {
            return false;
        }

        return true;
    }

    /**
     * Render function for profile tab hooks.
     *
     * @param  MEMBER $member_id_of The ID of the member who is being viewed
     * @param  MEMBER $member_id_viewing The ID of the member who is doing the viewing
     * @param  boolean $leave_to_ajax_if_possible Whether to leave the tab contents null, if this hook supports it, so that AJAX can load it later
     * @return array A tuple: The tab title, the tab contents, the suggested tab order, the icon
     */
    public function render_tab($member_id_of, $member_id_viewing, $leave_to_ajax_if_possible = false)
    {
        // Need to declare these here as the Tempcode engine can't look as deep, into a loop (I think), as it would need to, to find the block declaring the dependency
        require_lang('activities');
        require_css('activities');
        require_javascript('activity_feed');
        require_javascript('jquery');
        require_javascript('base64');

        require_code('site');
        set_feed_url('?mode=activities&select=' . strval($member_id_of));

        require_lang('activities');

        $title = do_lang_tempcode('ACTIVITY');

        $order = 70;

        // Allow user to link up things for syndication
        $syndications = [];
        if ($member_id_of == $member_id_viewing) {
            $dests = find_all_hook_obs('systems', 'syndication', 'Hook_syndication_');
            foreach ($dests as $hook => $ob) {
                if ($ob->is_available()) {
                    if (either_param_string('syndicate_stop__' . $hook, null) !== null) {
                        $ob->auth_unset($member_id_of);
                    } elseif (either_param_string('syndicate_start__' . $hook, null) !== null) {
                        $url_map = ['page' => '_SELF', 'type' => 'view', 'id' => $member_id_of, 'oauth_in_progress' => 1];
                        foreach (array_keys($_POST) as $key) {
                            $url_map[$key] = post_param_string($key, '');
                        }
                        $url_map['syndicate_start__' . $hook] = 1;
                        $oauth_url = build_url($url_map, '_SELF', [], false, false, false, 'tab--activities');
                        $ob->auth_set($member_id_of, $oauth_url);
                    } elseif ((running_script('index')) && (!$leave_to_ajax_if_possible) && ($ob->auth_is_set($member_id_of)) && (either_param_string('oauth_in_progress', null) === null) && (!$GLOBALS['IS_ACTUALLY_ADMIN'])) {
                        /* running_script('index') won't work currently due to execution contexts, and it is never non-AJAX, and it's probably not needed anyway
                        // Do a refresh to make sure the token is updated
                        $url_map = array('page' => '_SELF', 'type' => 'view', 'id' => $member_id_of, 'oauth_in_progress' => 1);
                        $url_map['syndicate_start__' . $hook] = 1;
                        $oauth_url = build_url($url_map, '_SELF', array(), false, false, false, 'tab--activities');
                        $ob->auth_set($member_id_of, $oauth_url);
                        */
                    }

                    $syndications[$hook] = [
                        'SYNDICATION_IS_SET' => $ob->auth_is_set($member_id_of),
                        'SYNDICATION_SERVICE_NAME' => $ob->get_service_name(),
                        'SYNDICATION_JAVASCRIPT_FUNCTION_CALLS' => method_exists($ob, 'syndication_javascript_function_calls') ? $ob->syndication_javascript_function_calls() : '',
                    ];
                }
            }
        }

        if ($leave_to_ajax_if_possible) {
            return [$title, null, $order, 'spare/activity'];
        }

        $content = do_template('CNS_MEMBER_PROFILE_ACTIVITIES', ['_GUID' => '9fe3b8bb9a4975fa19631c43472b4539', 'MEMBER_ID' => strval($member_id_of), 'SYNDICATIONS' => $syndications]);

        return [$title, $content, $order, 'spare/activity'];
    }
}

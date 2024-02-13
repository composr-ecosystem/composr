<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    activity_feed
 */

/**
 * Syndicate human-intended descriptions of activities performed to the internal feed, and external listeners.
 *
 * @param  string $language_string_code Language string codename
 * @param  string $label_1 Label 1 (given as a parameter to the language string codename)
 * @param  string $label_2 Label 2 (given as a parameter to the language string codename)
 * @param  string $label_3 Label 3 (given as a parameter to the language string codename)
 * @param  string $page_link_1 Page-link 1
 * @param  string $page_link_2 Page-link 2
 * @param  string $page_link_3 Page-link 3
 * @param  string $addon Addon that caused the event
 * @param  BINARY $is_public Whether this post should be public or friends-only
 * @param  ?MEMBER $member_id Member being written for (null: current member)
 * @param  boolean $sitewide_too Whether to push this out as a site event if user requested
 * @param  ?MEMBER $also_involving Member also 'intimately' involved, such as a content submitter who is a friend (null: none)
 * @return ?AUTO_LINK The activity ID
 */
function activity_feed_syndicate_described_activity(string $language_string_code = '', string $label_1 = '', string $label_2 = '', string $label_3 = '', string $page_link_1 = '', string $page_link_2 = '', string $page_link_3 = '', string $addon = '', int $is_public = 1, ?int $member_id = null, bool $sitewide_too = false, ?int $also_involving = null) : ?int
{
    require_code('activity_feed');
    require_lang('activity_feed');

    if ($member_id === null) {
        $member_id = get_member();
    }
    if (is_guest($member_id)) {
        return null;
    }

    $go = [
        'a_language_string_code' => $language_string_code,
        'a_label_1' => $label_1,
        'a_label_2' => $label_2,
        'a_label_3' => $label_3,
        'a_is_public' => $is_public,
    ];

    $stored_id = null;

    // Check if this has been posted previously (within the last 10 minutes) to stop spamming but allow generalised repeat status messages.
    $test = $GLOBALS['SITE_DB']->query('SELECT a_language_string_code,a_label_1,a_label_2,a_label_3,a_is_public FROM ' . get_table_prefix() . 'activities WHERE a_member_id=' . strval($member_id) . ' AND a_time>' . strval(time() - 600) . ' AND a_time<=' . strval(time()), 1);
    if ((!array_key_exists(0, $test)) || ($test[0] != $go) || (running_script('execute_temp')) || ($GLOBALS['SEMI_DEV_MODE'])) {
        // Log the activity
        $row = $go + [
            'a_member_id' => $member_id,
            'a_also_involving' => $also_involving,
            'a_page_link_1' => $page_link_1,
            'a_page_link_2' => $page_link_2,
            'a_page_link_3' => $page_link_3,
            'a_time' => time(),
            'a_addon' => $addon,
        ];
        if ((get_db_type() != 'xml') || (get_param_integer('keep_testing_logging', 0) == 1)) {
            $stored_id = $GLOBALS['SITE_DB']->query_insert('activities', $row, true);

            // Update the latest activity file
            log_newest_activity($stored_id, 1000, true/*We do want to force it, IDs can get out of sync on dev sites*/);
        }

        // External places, which is built on top of the hybridauth addon
        if (($is_public == 1) && ($sitewide_too) && (($language_string_code == 'RAW_DUMP') || (!$GLOBALS['IS_ACTUALLY_ADMIN']/*SU means oauth'd user is not intended user*/))) {
            if (addon_installed('hybridauth')) {
                require_code('hybridauth_admin');
                require_lang('hybridauth');

                list($message) = render_activity($row, false);

                $atom = new \Hybridauth\Atom\Atom();
                $atom->author = $GLOBALS['FORUM_DRIVER']->get_username($member_id, true);
                $atom->published = new \DateTime('@' . strval(time()));
                $atom->updated = new \DateTime('@' . strval(time()));
                $atom->summary = $message->evaluate();
                if ($page_link_1 != '') {
                    $atom->url = page_link_to_url($page_link_1, true);
                }

                $before_type_strictness = ini_get('ocproducts.type_strictness');
                cms_ini_set('ocproducts.type_strictness', '0');
                $before_xss_detect = ini_get('ocproducts.xss_detect');
                cms_ini_set('ocproducts.xss_detect', '0');

                list($hybridauth, $admin_storage) = initiate_hybridauth_admin();

                $providers = find_all_hybridauth_admin_providers_matching(HYBRIDAUTH__ADVANCEDAPI_INSERT_ATOMS);
                foreach ($providers as $provider => $info) {
                    $syndicate_from = explode(',', $info['syndicate_from']);
                    if (!in_array('activity_feed', $syndicate_from)) {
                        continue;
                    }

                    try {
                        $adapter = $hybridauth->getAdapter($provider);
                        if (!$adapter->isConnected()) {
                            continue;
                        }

                        $adapter->saveAtom($atom);
                    } catch (Exception $e) {
                        require_code('failure');
                        cms_error_log($e->getMessage(), 'error_occurred_api');
                    }
                }

                cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
                cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
            }
        }

        list($message) = render_activity($row, false);
        require_code('notifications');
        $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
        $displayname = $GLOBALS['FORUM_DRIVER']->get_username($member_id, true);
        $subject = do_lang('ACTIVITY_NOTIFICATION_MAIL_SUBJECT', get_site_name(), $username, strip_html($message->evaluate()));
        $mail = do_notification_lang('ACTIVITY_NOTIFICATION_MAIL', comcode_escape(get_site_name()), comcode_escape($username), ['[semihtml]' . $message->evaluate() . '[/semihtml]', $displayname]);
        dispatch_notification('activity_feed', strval($member_id), $subject, $mail);
    }

    return $stored_id;
}

/**
 * AJAX script for submitting posts.
 */
function activity_feed_handler_script()
{
    if (!addon_installed('activity_feed')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('activity_feed')));
    }

    prepare_backend_response();

    $response = '<' . '?xml version="1.0" encoding="' . escape_html(get_charset()) . '" ?' . '>';
    $response .= '<response><content>';

    $map = [];

    $guest_id = intval($GLOBALS['FORUM_DRIVER']->get_guest_id());

    if (!is_guest(get_member())) {
        $map['STATUS'] = post_param_string('status', '');

        if ((post_param_string('zone', '') != '') && ($map['STATUS'] != '') && ($map['STATUS'] != do_lang('activity_feed:TYPE_HERE'))) {
            comcode_to_tempcode($map['STATUS'], $guest_id, false);

            $map['PRIVACY'] = post_param_string('privacy', 'private');

            if (strlen(strip_tags($map['STATUS'])) < strlen($map['STATUS'])) {
                $help_zone = get_comcode_zone('userguide_comcode', false);
                if ($help_zone === null) {
                    $response .= '<success>0</success><feedback><![CDATA[No HTML allowed. Use Comcode.]]></feedback>';
                } else {
                    $cc_guide = build_url(['page' => 'userguide_comcode'], $help_zone);
                    $response .= '<success>0</success><feedback><![CDATA[No HTML allowed. See <a href="' . escape_html($cc_guide->evaluate()) . '">Comcode Help</a> for info on the alternative.]]></feedback>';
                }
            } else {
                if (strlen($map['STATUS']) > 255) {
                    $response .= '<success>0</success><feedback>Message is ' . strval(strlen($map['STATUS']) - 255) . ' characters too long</feedback>';
                } else {
                    $stored_id = activity_feed_syndicate_described_activity(
                        'RAW_DUMP',
                        $map['STATUS'],
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ($map['PRIVACY'] == 'public') ? 1 : 0
                    );

                    if ($stored_id !== null) {
                        $response .= '<success>1</success><feedback>Message received.</feedback>';
                    } else {
                        $response .= '<success>0</success><feedback>Message already received.</feedback>';
                    }
                }
            }
        }
    } else {
        $response .= '<success>0</success><feedback>' . do_lang('LOGIN_EXPIRED_POST') . '</feedback>';
    }

    $response .= '</content></response>';

    echo $response;

    cms_safe_exit_flow();
}

/**
 * AJAX script for removing posts.
 */
function activity_feed_removal_script()
{
    if (!addon_installed('activity_feed')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('activity_feed')));
    }

    $is_guest = false; // Can't be doing with over-complicated SQL breakages. Weed it out.
    $guest_id = intval($GLOBALS['FORUM_DRIVER']->get_guest_id());
    $viewer_id = intval(get_member()); //We'll need this later anyway.
    if ($guest_id == $viewer_id) {
        $is_guest = true;
    }

    $can_remove_others = (has_zone_access($viewer_id, 'adminzone'));

    prepare_backend_response();

    $response = '<' . '?xml version="1.0" encoding="' . escape_html(get_charset()) . '" ?' . '>';
    $response .= '<response>';

    $stat_id = post_param_integer('removal_id');
    $stat_owner = $GLOBALS['SITE_DB']->query_select_value_if_there('activities', 'a_member_id', ['id' => $stat_id]);

    if (($is_guest !== true) && ($stat_owner !== null)) {
        if (($stat_owner != $viewer_id) && ($can_remove_others !== true)) {
            $response .= '<success>0</success><err>perms</err>';
            $response .= '<feedback>You do not have permission to remove this status message.</feedback><status_id>' . strval($stat_id) . '</status_id>';
        } else { // I suppose we can proceed now.
            $GLOBALS['SITE_DB']->query_delete('activities', ['id' => $stat_id], '', 1);

            $response .= '<success>1</success><feedback>Message deleted.</feedback><status_id>' . strval($stat_id) . '</status_id>';
        }
    } elseif ($stat_owner === null) {
        $response .= '<success>0</success><err>missing</err><feedback>Missing ID for status removal or ID does not exist.</feedback>';
    } else {
        $response .= '<success>0</success><feedback>Login expired, you must log in again to post</feedback>';
    }

    $response .= '</response>';

    echo $response;

    cms_safe_exit_flow();
}

/**
 * Maintains a text file in data_custom. This contains the latest activity's ID.
 * Since the JavaScript polls for updates, it can check against this before running any PHP.
 *
 * @param  integer $id The ID we are going to write to the file
 * @param  integer $timeout Our timeout in milliseconds (how long we should keep trying). Default: 1000
 * @param  boolean $force Whether to force this ID to be the newest, even if it's less than the current value
 */
function log_newest_activity(int $id, int $timeout = 1000, bool $force = false)
{
    $file_path = get_custom_file_base() . '/data_custom/latest_activity.bin';

    $old_id = @cms_file_get_contents_safe($file_path, FILE_READ_LOCK);
    if (($force) || ($old_id === false) || (intval($old_id) < $id)) {
        require_code('files');
        cms_file_put_contents_safe($file_path, strval($id), FILE_WRITE_FAILURE_SOFT | FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    }
}

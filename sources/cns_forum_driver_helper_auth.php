<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_cns
 */

/**
 * Find if the given member ID and password is valid. If username is null, then the member ID is used instead.
 * All authorisation, cookies, and form-logins, are passed through this function.
 * Some forums do cookie logins differently, so a Boolean is passed in to indicate whether it is a cookie login.
 *
 * @param  object $this_ref Link to the real forum driver
 * @param  ?SHORT_TEXT $username The member username (null: don't use this in the authentication - but look it up using the ID if needed)
 * @param  ?MEMBER $userid The member ID (null: use member name)
 * @param  SHORT_TEXT $password_hashed The md5-hashed password
 * @param  string $password_raw The raw password
 * @param  boolean $cookie_login Whether this is a cookie login, determines how the hashed password is treated for the value passed in
 * @return array A map of 'id' and 'error'. If 'id' is null, an error occurred and 'error' is set
 *
 * @ignore
 */
function _forum_authorise_login($this_ref, $username, $userid, $password_hashed, $password_raw, $cookie_login = false)
{
    require_code('cns_forum_driver_helper_auth');

    $out = array();
    $out['id'] = null;

    require_code('cns_members');
    require_code('cns_groups');
    if (!function_exists('require_lang')) {
        require_code('lang');
    }
    if (!function_exists('do_lang_tempcode')) {
        require_code('tempcode');
    }
    if (!function_exists('require_lang')) {
        return $out; // Bootstrap got really mixed up, so we need to bomb out
    }
    require_lang('cns');
    require_code('mail');

    if (php_function_allowed('usleep')) {
        usleep(500000); // Wait for half a second, to reduce brute force potential
    }

    $skip_auth = false;

    if ($userid === null) {
        if (get_option('one_per_email_address') == '2') {
            $rows = array();
        } else {
            $rows = $this_ref->connection->query_select('f_members', array('*'), array('m_username' => $username), '', 1);
        }
        if ((!array_key_exists(0, $rows)) && (get_option('one_per_email_address') != '0')) {
            $rows = $this_ref->connection->query_select('f_members', array('*'), array('m_email_address' => $username), 'ORDER BY id ASC', 1);
        }
        if (array_key_exists(0, $rows)) {
            $this_ref->MEMBER_ROWS_CACHED[$rows[0]['id']] = $rows[0];
            $userid = $rows[0]['id'];
        }
    } else {
        $rows[0] = $this_ref->get_member_row($userid);
    }

    // LDAP to the rescue if we couldn't get a row
    global $LDAP_CONNECTION;
    if ((!array_key_exists(0, $rows)) && ($LDAP_CONNECTION !== null) && ($userid === null)) {
        // See if LDAP has it -- if so, we can add
        $test = cns_is_on_ldap($username);
        if (!$test) {
            $out['error'] = is_null($username) ? do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : 'MEMBER_NO_EXISTS') : do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($username));
            return $out;
        }

        $test_auth = cns_ldap_authorise_login($username, $password_raw);
        if ($test_auth['m_pass_hash_salted'] == '!!!') {
            $out['error'] = do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : 'MEMBER_BAD_PASSWORD');
            return $out;
        }

        if ($test) {
            require_code('cns_members_action');
            require_code('cns_members_action2');
            $completion_form_submitted = (trim(post_param_string('email_address', '')) != '');
            if ((!$completion_form_submitted) && (get_option('finish_profile') == '1')) { // UI
                require_code('failure');
                if (throwing_errors()) {
                    throw new CMSException(do_lang('ENTER_PROFILE_DETAILS_FINISH'));
                }

                cms_ob_end_clean(); // Emergency output, potentially, so kill off any active buffer
                $middle = cns_member_external_linker_ask($username, 'ldap', cns_ldap_guess_email($username));
                $tpl = globalise($middle, null, '', true);
                $tpl->evaluate_echo();
                exit();
            } else {
                $userid = cns_member_external_linker($username, uniqid('', true), 'ldap');
                $row = $this_ref->get_member_row($userid);
            }
        }
    }

    if ((!array_key_exists(0, $rows)) || ($rows[0] === null)) { // All hands to lifeboats
        // Run hooks for other interactive login possibilities, if any exist
        $hooks = find_all_hooks('systems', 'login_providers_direct_auth');
        foreach ($hooks as $hook => $hook_dir) {
            require_code('hooks/systems/login_providers_direct_auth/' . filter_naughty_harsh($hook), false, $hook_dir == 'sources_custom');
            $ob = object_factory('Hook_login_providers_direct_auth_' . filter_naughty_harsh($hook), true);
            if (is_null($ob)) {
                continue;
            }
            $try_login = $ob->try_login($username, $userid, $password_hashed, $password_raw, $cookie_login);
            if (!is_null($try_login)) {
                return $try_login;
            }
        }

        $out['error'] = is_null($username) ? do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : 'MEMBER_NO_EXIST') : do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : '_MEMBER_NO_EXIST', escape_html($username));
        return $out;
    }
    $row = $rows[0];

    // Now LDAP can kick in and get the correct hash
    if (cns_is_ldap_member($userid)) {
        //$rows[0]['m_pass_hash_salted'] = cns_get_ldap_hash($userid);

        // Doesn't exist any more? This is a special case - the 'LDAP member' exists in our DB, but not LDAP. It has been deleted from LDAP or LDAP server has jumped
        /*if (is_null($rows[0]['m_pass_hash_salted']))
        {
            $out['error'] = do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : '_MEMBER_NO_EXIST', $username);
            return $out;
        } No longer appropriate with new authentication mode - instead we just have to give an invalid password message */

        $row = array_merge($row, cns_ldap_authorise_login($username, $password_hashed));
    }

    // Check valid user
    if (addon_installed('unvalidated')) {
        if ($row['m_validated'] == 0) {
            $out['error'] = do_lang_tempcode('MEMBER_NOT_VALIDATED_STAFF');
            return $out;
        }
    }
    if ($row['m_validated_email_confirm_code'] != '') {
        $out['error'] = do_lang_tempcode('MEMBER_NOT_VALIDATED_EMAIL');
        return $out;
    }
    if ($this_ref->is_banned($row['id'])) { // All hands to the guns
        $out['error'] = do_lang_tempcode('YOU_ARE_BANNED');
        return $out;
    }

    // Check password
    if (!$skip_auth) {
        // Choose a compatibility screen.
        // Note that almost all cookie logins are the same. This is because the cookie logins use Conversr cookies, regardless of compatibility scheme.
        $password_compatibility_scheme = $row['m_password_compat_scheme'];
        switch ($password_compatibility_scheme) {
            case '': // Composr style salted MD5 algorithm
            case 'temporary': // as above, but forced temporary password
                if ($cookie_login) {
                    if ($password_hashed !== $row['m_pass_hash_salted']) {
                        require_code('tempcode'); // This can be incidental even in fast AJAX scripts, if an old invalid cookie is present, so we need Tempcode for do_lang_tempcode
                        $out['error'] = do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : 'MEMBER_BAD_PASSWORD');
                        return $out;
                    }
                } else {
                    require_code('crypt');
                    if (!ratchet_hash_verify($password_raw, $row['m_pass_salt'], $row['m_pass_hash_salted'])) {
                        $out['error'] = do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : 'MEMBER_BAD_PASSWORD');
                        return $out;
                    }
                }
                break;

            case 'plain':
                if ($password_hashed !== md5($row['m_pass_hash_salted'])) {
                    $out['error'] = do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : 'MEMBER_BAD_PASSWORD');
                    return $out;
                }
                break;

            case 'md5': // Old style plain md5     (also works if both are unhashed: used for LDAP)
                if (($password_hashed !== $row['m_pass_hash_salted']) && ($password_hashed !== '!!!')) { // The !!! bit would never be in a hash, but for plain text checks using this same code, we sometimes use '!!!' to mean 'Error'.
                    $out['error'] = do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : 'MEMBER_BAD_PASSWORD');
                    return $out;
                }
                break;

            /*
            case 'httpauth':
                // This is handled in get_member()
                break;
            */

            case 'ldap':
                if ($password_hashed !== $row['m_pass_hash_salted']) {
                    $out['error'] = do_lang_tempcode((get_option('login_error_secrecy') == '1') ? 'MEMBER_INVALID_LOGIN' : 'MEMBER_BAD_PASSWORD');
                    return $out;
                }
                break;

            default:
                $path = get_file_base() . '/sources_custom/hooks/systems/cns_auth/' . $password_compatibility_scheme . '.php';
                if (!file_exists($path)) {
                    $path = get_file_base() . '/sources/hooks/systems/cns_auth/' . $password_compatibility_scheme . '.php';
                }
                if (!file_exists($path)) {
                    if (function_exists('build_url')) {
                        $reset_url = build_url(array('page' => 'lost_password'), get_module_zone('lost_password'));
                        $out['error'] = do_lang_tempcode('UNKNOWN_AUTH_SCHEME_IN_DB', escape_html($reset_url->evaluate()));
                    } else {
                        $out['error'] = do_lang_tempcode('UNKNOWN_AUTH_SCHEME_IN_DB', escape_html(get_base_url() . '/index.php?page=lost_password'));
                    }
                    return $out;
                }
                require_code('hooks/systems/cns_auth/' . $password_compatibility_scheme);
                $ob = object_factory('Hook_cns_auth_' . $password_compatibility_scheme);
                $error = $ob->auth($username, $userid, $password_hashed, $password_raw, $cookie_login, $row);
                if (!is_null($error)) {
                    $out['error'] = $error;
                    return $out;
                }
                break;
        }
    }

    // Ok, authorised basically, but we need to see if this is a valid login IP
    if ((cns_get_best_group_property($this_ref->get_members_groups($row['id']), 'enquire_on_new_ips') == 1)) { // High security usergroup membership
        global $SENT_OUT_VALIDATE_NOTICE, $IN_SELF_ROUTING_SCRIPT;
        $ip = get_ip_address(3);
        $test2 = $this_ref->connection->query_select_value_if_there('f_member_known_login_ips', 'i_val_code', array('i_member_id' => $row['id'], 'i_ip' => $ip));
        if (((is_null($test2)) || ($test2 != '')) && (!compare_ip_address($ip, $row['m_ip_address']))) {
            if (!$SENT_OUT_VALIDATE_NOTICE) {
                if (!is_null($test2)) { // Tidy up
                    $this_ref->connection->query_delete('f_member_known_login_ips', array('i_member_id' => $row['id'], 'i_ip' => $ip), '', 1);
                }

                $code = !is_null($test2) ? $test2 : uniqid('', true);
                $this_ref->connection->query_insert('f_member_known_login_ips', array('i_val_code' => $code, 'i_member_id' => $row['id'], 'i_ip' => $ip), false, true);
                $url = find_script('approve_ip') . '?code=' . urlencode($code);
                $url_simple = find_script('approve_ip');
                require_code('comcode');
                $mail = do_lang('IP_VERIFY_MAIL', comcode_escape($url), comcode_escape(get_ip_address()), array($url_simple, $code), get_lang($row['id']));
                $email_address = $row['m_email_address'];
                if ($email_address == '') {
                    $email_address = get_option('staff_address');
                }
                if ($IN_SELF_ROUTING_SCRIPT) {
                    mail_wrap(do_lang('IP_VERIFY_MAIL_SUBJECT', null, null, null, get_lang($row['id'])), $mail, array($email_address), $row['m_username'], '', '', 1, null, false, null, false, false, false, 'MAIL', false, null, null, $row['m_join_time']);
                }

                $SENT_OUT_VALIDATE_NOTICE = true;
            }

            $out['error'] = do_lang_tempcode('REQUIRES_IP_VALIDATION');
            return $out;
        }
    }

    $this_ref->cns_flood_control($row['id']);

    $out['id'] = $row['id'];
    return $out;
}

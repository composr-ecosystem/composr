<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: TapatalkPush*/

/**
 * Composr API helper class.
 */
class CMSMemberACL
{
    /**
     * Login.
     *
     * @param  string $username Username
     * @param  string $password Password
     * @param  boolean $invisible Log in as invisible
     * @return ?MEMBER Member ID (null: login failed)
     */
    public function authenticate_credentials_and_set_auth($username, $password, $invisible = false)
    {
        $feedback = $GLOBALS['FORUM_DRIVER']->forum_authorise_login($username, null, md5($password), $password);

        $id = $feedback['id'];
        if (!is_null($id)) {
            $this->set_auth($id, $invisible);

            return $id;
        }
        return null;
    }

    /**
     * Login with no password check.
     *
     * @param  MEMBER $id Member ID
     * @param  boolean $invisible Log in as invisible
     */
    public function set_auth($id, $invisible = false)
    {
        require_code('users_active_actions');

        cms_setcookie(get_member_cookie(), strval($id));

        $password_compat_scheme = $GLOBALS['FORUM_DRIVER']->get_member_row_field($id, 'm_password_compat_scheme');
        $password_hashed_salted = $GLOBALS['FORUM_DRIVER']->get_member_row_field($id, 'm_pass_hash_salted');
        if ($password_compat_scheme == 'plain') {
            cms_setcookie(get_pass_cookie(), md5($password_hashed_salted), false, true);
        } else {
            cms_setcookie(get_pass_cookie(), $password_hashed_salted, false, true);
        }

        if ($invisible) {
            set_invisibility();
        }

        $push = new TapatalkPush();
        $push->set_is_tapatalk_member($id);

        header('Mobiquo_is_login: true');
    }

    /**
     * Logout.
     */
    public function logout_user()
    {
        require_code('users_active_actions');
        handle_active_logout();
    }
}

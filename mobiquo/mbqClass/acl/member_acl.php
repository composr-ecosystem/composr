<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
    public function authenticate_credentials_and_set_auth(string $username, string $password, bool $invisible = false) : ?int
    {
        $feedback = $GLOBALS['FORUM_DRIVER']->authorise_login($username, null, $password);

        $id = $feedback['id'];
        if ($id !== null) {
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
    public function set_auth(int $id, bool $invisible = false)
    {
        require_code('cns_forum_driver_helper_auth');
        cns_create_login_cookie($id);

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

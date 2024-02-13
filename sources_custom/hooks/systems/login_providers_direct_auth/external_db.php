<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    external_db_login
 */

/**
 * Hook class.
 */
class Hook_login_providers_direct_auth_external_db
{
    /**
     * Find if the given member ID and password is valid. If username is null, then the member ID is used instead.
     * All authorisation, cookies, and form-logins, are passed through this function.
     * Some forums do cookie logins differently, so a Boolean is passed in to indicate whether it is a cookie login.
     *
     * @param  ?SHORT_TEXT $username The member username (null: use $member_id)
     * @param  ?MEMBER $user_id The member ID (null: use username)
     * @param  string $password_raw The raw password
     * @param  boolean $cookie_login Whether this is a cookie login, determines how the hashed password is treated for the value passed in
     * @return ?array A map of 'id' and 'error'. If 'id' is null, an error occurred and 'error' is set (null: no action by this hook)
     */
    public function try_login(?string $username, ?int $user_id, string $password_raw, bool $cookie_login = false) : ?array
    {
        if (!addon_installed('external_db_login')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        require_code('external_db');

        if ($cookie_login || $password_raw == '') {
            return null;
        }

        $db = external_db();
        if ($db === null) {
            return null;
        }

        $table = get_value('external_db_login__table', null, true);
        $username_field = get_value('external_db_login__username_field', null, true);
        $password_field = get_value('external_db_login__password_field', null, true);
        $email_address_field = get_value('external_db_login__email_address_field', null, true);

        // Handle active login
        $query = 'SELECT * FROM ' . $table . ' WHERE ';
        switch (get_option('one_per_email_address')) {
            case '1':
                $query .= '(';
                $query .= db_string_equal_to($username_field, $username);
                $query .= ' OR ';
                $query .= db_string_equal_to($email_address_field, $username);
                $query .= ')';
                break;

            case '2':
                $query .= db_string_equal_to($email_address_field, $username);
                break;

            case '0':
            default:
                $query .= db_string_equal_to($username_field, $username);
                break;
        }
        $query .= ' AND ' . db_string_equal_to($password_field, $password_raw);
        $records = $db->query($query);
        if (isset($records[0])) {
            // Create new member
            return ['id' => external_db_user_add($records[0])];
        }

        return null;
    }
}

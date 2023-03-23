<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    external_db_login
 */

/**
 * Hook class.
 */
class Hook_upon_login_external_db
{
    /**
     * Standard upon login hook.
     *
     * @param  boolean $new_attempt Whether it is a new login attempt
     * @param  string $username Username
     * @param  ?MEMBER $member_id Member ID of already-confirmed login
     */
    public function run(bool $new_attempt, string $username, ?int $member_id)
    {
        if (!addon_installed('external_db_login')) {
            return;
        }

        if (get_forum_type() != 'cns') {
            return;
        }

        if (!$new_attempt) {
            return; // We don't try and bind to a third-party login if we're dealing with re-establishing an existing Composr session
        }
        if (($member_id === null) || is_guest($member_id)) {
            return; // No login to speak of
        }

        // If we get a remote binding, we need to re-sync it...

        require_code('external_db');

        $db = external_db();
        if ($db === null) {
            return;
        }

        $table = get_value('external_db_login__table', null, true);
        $username_field = get_value('external_db_login__username_field', null, true);
        $password_field = get_value('external_db_login__password_field', null, true);
        $email_address_field = get_value('external_db_login__email_address_field', null, true);

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
        $records = $db->query($query);
        if (isset($records[0])) {
            external_db_user_sync($member_id, $records[0]);
        }
    }
}

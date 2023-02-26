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
class Hook_login_provider_external_db
{
    /**
     * Standard login provider hook.
     *
     * @param  ?MEMBER $member_id Member ID already detected as logged in (null: none). May be a guest ID.
     * @return ?MEMBER Member ID now detected as logged in (null: none). May be a guest ID.
     */
    public function try_login(?int $member_id) : ?int
    {
        if (!addon_installed('external_db_login')) {
            return $member_id;
        }

        if (get_forum_type() != 'cns') {
            return $member_id;
        }

        if (($member_id === null) || (is_guest($member_id))) {
            require_code('external_db');

            $record = external_db_user_from_session();

            if ($record === null) {
                return $member_id;
            }

            // Existing Composr user?
            $username_field = get_value('external_db_login__username_field', null, true);
            $email_address_field = get_value('external_db_login__email_address_field', null, true);
            $member_id = null;
            if (get_option('one_per_email_address') != '0') {
                $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($record[$email_address_field]);
            }
            if (($member_id === null) && (get_option('one_per_email_address') != '2')) {
                $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($record[$username_field]);
            }
            if ($member_id !== null) {
                external_db_user_sync($member_id, $record);

                // Return existing user
                return $member_id;
            }

            // Create new user
            return external_db_user_add($record);
        }

        return $member_id;
    }
}

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    hybridauth
 */

/* This file is designed to be overwritten by addons that implement external user sync schemes. */

/**
 * Find is a field is editable.
 * Called for fields that have a fair chance of being set to auto-sync, and hence be locked to local edits.
 *
 * @param  ID_TEXT $field_name Field name
 * @param  ID_TEXT $special_type The special type of the user (built-in types are: <blank>, ldap, httpauth, <name of import source>)
 * @return boolean Whether the field is editable
 */
function cns_field_editable(string $field_name, string $special_type) : bool
{
    if ((addon_installed('hybridauth')) && ($special_type != '')) {
        require_code('hybridauth');
        $is_hybridauth_account = is_hybridauth_special_type($special_type);

        if ($is_hybridauth_account) {
            switch ($field_name) {
                case 'username':
                    if (get_option('hybridauth_sync_username') == '1') {
                        return false;
                    }
                    break;

                // Actually, we want to allow changing password to disassociate the account from Hybridauth
                /*
                case 'password':
                    return false;
                    break;
                */

                case 'email':
                    if (get_option('hybridauth_sync_email') == '1') {
                        return false;
                    }
                    break;
            }
        }
    }

    return non_overridden__cns_field_editable($field_name, $special_type);
}

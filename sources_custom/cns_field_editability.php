<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
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
function cns_field_editable($field_name, $special_type)
{
    switch ($field_name) {
        case 'username':
            switch ($special_type) {
                case 'facebook':
                    if (get_option('facebook_sync_username') == '1') {
                        return false;
                    }
                    break;

                case 'ldap':
                    return false;
            }
            break;

        case 'password':
            switch ($special_type) {
                case 'facebook':
                    return false;

                case 'ldap':
                case 'httpauth':
                    return false;
            }
            break;

        case 'primary_group':
            switch ($special_type) {
                case 'ldap':
                    return false;
            }
            break;

        case 'secondary_groups':
            switch ($special_type) {
                case 'ldap':
                    return false;
            }
            break;

        case 'dob':
            switch ($special_type) {
                case 'facebook':
                    if (get_option('facebook_sync_dob') == '1') {
                        return false;
                    }
                    break;
            }
            break;

        case 'email':
            switch ($special_type) {
                case 'facebook':
                    if (get_option('facebook_sync_email') == '1') {
                        return false;
                    }
                    break;
            }
            break;
    }

    return true;
}

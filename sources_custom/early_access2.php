<?php /*

Composr
Copyright (c) Christopher Graham, 2004-2024

See docs/LICENSE.md for full licensing information.

*/

/**
* @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
* @copyright  Christopher Graham
* @package    early_access
*/

function init__early_access2()
{
    require_lang('early_access');
}

/**
 * Create an early access code.
 *
 * @param  ID_TEXT $access_code The access code which should not yet exist
 * @param  SHORT_TEXT $label The contextual label for this code
 * @param  SHORT_TEXT $trigger_access A $HAS_TRIGGER_ACCESS parameter on which to match (blank: none)
 * @param  ?TIME $date_from The time at which this code becomes active (null: immediately)
 * @param  ?TIME $date_to The time at which this code expires (null: never)
 * @param  ?integer $num_views_allowed The maximum number of views allowed for this code (null: unlimited)
 * @param  array $content_items Array of content type and content ID pairs associated with this access code
 */
function add_early_access_code(string $access_code, string $label, string $trigger_access, ?int $date_from, ?int $date_to, ?int $num_views_allowed, array $content_items)
{
    if (in_array($access_code, ['login', 'session', ''])) {
        warn_exit(do_lang_tempcode('ACCESS_CODE_NOT_ALLOWED', escape_html($access_code)));
    }

    $already_exists = $GLOBALS['SITE_DB']->query_select_value_if_there('early_access_codes', 'c_access_code', ['c_access_code' => $access_code]);
    if ($already_exists !== null) {
        warn_exit(do_lang_tempcode('ACCESS_CODE_IN_USE', escape_html($access_code)));
    }

    $GLOBALS['SITE_DB']->query_insert('early_access_codes', [
        'c_access_code' => $access_code,
        'c_label' => $label,
        'c_trigger_access' => $trigger_access,
        'c_date_from' => $date_from,
        'c_date_to' => $date_to,
        'c_num_views' => 0,
        'c_num_views_allowed' => $num_views_allowed,
        'c_created_by' => get_member(),
        'c_creation_time' => time(),
        'c_edit_time' => time(),
    ]);

    foreach ($content_items as $content_item) {
        list($content_type, $content_id) = $content_item;

        $GLOBALS['SITE_DB']->query_insert('early_access_code_content', [
            'a_access_code' => $access_code,
            'a_content_type' => $content_type,
            'a_content_id' => $content_id,
        ]);
    }

    log_it('ADD_EARLY_ACCESS_CODE', $access_code, $label);
}

/**
 * Create an early access code.
 *
 * @param  ID_TEXT $access_code The access code we are editing
 * @param  SHORT_TEXT $label The contextual label for this code
 * @param  SHORT_TEXT $trigger_access A $HAS_TRIGGER_ACCESS parameter on which to match (blank: none)
 * @param  ?TIME $date_from The time at which this code becomes active (null: immediately)
 * @param  ?TIME $date_to The time at which this code expires (null: never)
 * @param  ?integer $num_views_allowed The maximum number of views allowed for this code (null: unlimited)
 * @param  array $content_items Array of content type and content ID pairs associated with this access code
 */
function edit_early_access_code(string $access_code, string $label, string $trigger_access, ?int $date_from, ?int $date_to, ?int $num_views_allowed, array $content_items)
{
    $old_trigger_access = $GLOBALS['SITE_DB']->query_select_value_if_there('early_access_codes', 'c_trigger_access', ['c_access_code' => $access_code]);
    if ($old_trigger_access === null) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'early_access'));
    } elseif (($old_trigger_access != '') && ($old_trigger_access != $trigger_access)) {
        // Clean up low-level values set for this access code's trigger access tag, but only if we're changing it and the old tag is not used in another access code.
        // Actually we don't want to do this because it could cause content to no-longer be accessible once all their access codes were removed / changed.
        /*
            $exists_elsewhere = $GLOBALS['SITE_DB']->query_select_value_if_there('early_access_codes', 'c_trigger_access', ['c_trigger_access' => $old_trigger_access], ' AND ' . db_string_not_equal_to('c_access_code', $access_code));
            if ($exists_elsewhere === null) {
                delete_value('trigger_access_tag__' . $old_trigger_access);
            }
        */
    }

    $GLOBALS['SITE_DB']->query_update('early_access_codes', [
        'c_label' => $label,
        'c_trigger_access' => $trigger_access,
        'c_date_from' => $date_from,
        'c_date_to' => $date_to,
        'c_num_views_allowed' => $num_views_allowed,
        'c_edit_time' => time(),
    ], ['c_access_code' => $access_code]);

    $GLOBALS['SITE_DB']->query_delete('early_access_code_content', ['a_access_code' => $access_code]);

    foreach ($content_items as $content_item) {
        list($content_type, $content_id) = $content_item;

        $GLOBALS['SITE_DB']->query_insert('early_access_code_content', [
            'a_access_code' => $access_code,
            'a_content_type' => $content_type,
            'a_content_id' => $content_id,
        ]);
    }

    log_it('EDIT_EARLY_ACCESS_CODE', $access_code, $label);
}

/**
 * Delete an early access code.
 *
 * @param  ID_TEXT $access_code The access code to delete
 */
function delete_early_access_code(string $access_code)
{
    $old_trigger_access = $GLOBALS['SITE_DB']->query_select_value_if_there('early_access_codes', 'c_trigger_access', ['c_access_code' => $access_code]);
    if ($old_trigger_access === null) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'early_access'));
    } elseif ($old_trigger_access != '') {
        // Clean up low-level values set for this access code's trigger access tag, but only if the tag is not used in another access code.
        // Actually we don't want to do this because it could cause content to no-longer be accessible once all their access codes were removed / changed.
        /*
        $exists_elsewhere = $GLOBALS['SITE_DB']->query_select_value_if_there('early_access_codes', 'c_trigger_access', ['c_trigger_access' => $old_trigger_access], ' AND ' . db_string_not_equal_to('c_access_code', $access_code));
        if ($exists_elsewhere === null) {
            delete_value('trigger_access_tag__' . $old_trigger_access);
        }
        */
    }

    $GLOBALS['SITE_DB']->query_delete('early_access_codes', ['c_access_code' => $access_code]);
    $GLOBALS['SITE_DB']->query_delete('early_access_code_content', ['a_access_code' => $access_code]);

    log_it('DELETE_EARLY_ACCESS_CODE', $access_code);
}

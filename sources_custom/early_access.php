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

/**
 * Standard initialisation function for early_access.
 */
function init__early_access()
{
    require_lang('early_access');

    global $EARLY_ACCESS_CODE_VIEW_COUNTED; // Whether we already counted a view to early access on this page hit
    if (!isset($EARLY_ACCESS_CODE_VIEW_COUNTED)) {
        $EARLY_ACCESS_CODE_VIEW_COUNTED = false;
    }
}

/**
 * Check if the current visitor has the privilege to view the given content through early access.
 * This will also attach relevant messages regarding access code use.
 *
 * @param  MEMBER $member_id The member accessing the content
 * @param  ID_TEXT $content_type The content type being accessed
 * @param  ID_TEXT $content_id The content ID being accessed
 * @return boolean Whether we have access
 */
function check_has_special_page_access_for_unvalidated_content(int $member_id, string $content_type, string $content_id) : bool
{
    $special_pass = false;

    if (!$special_pass) {
        $access_code = get_param_string('keep_access_code', '');
        if (($access_code != '') && ($access_code != 'login') && ($access_code != 'session')) {
            check_access_code__content($special_pass, $content_type, $content_id, $access_code);
        }
    }

    // No fast cache for early access content
    if ($special_pass) {
        require_code('static_cache');
        global $INVALIDATED_FAST_SPIDER_CACHE;
        $INVALIDATED_FAST_SPIDER_CACHE = true;
    }

    // NB: Generally, this is already covered by the validation addon; cannot use check_jump_to_not_validated as that will trigger an infinite loop
    if ((addon_installed('validation')) && (has_privilege($member_id, 'jump_to_not_validated'))) {
        if (!$special_pass) {
            $special_pass = true;
            $message = do_lang_tempcode((get_param_integer('redirected', 0) == 1) ? 'UNVALIDATED_TEXT_NON_DIRECT' : 'UNVALIDATED_TEXT', $content_type);
            attach_message($message, 'inform');
        }
    }

    if (!$special_pass) {
        return false;
    }

    return true;
}

/**
 * Check if the current visitor has the privilege to view any of the given trigger tags through early access.
 * This will also attach relevant messages regarding access code use.
 *
 * @param  array $trigger_tags An array of trigger tags which any can be matched to consider access granted
 * @return boolean Whether we have access
 */
function check_has_special_page_access_for_triggers(array $trigger_tags) : bool
{
    $special_pass = false;

    // First, check for global value overrides
    foreach ($trigger_tags as $tag) {
        if (get_value('trigger_access_tag__' . $tag) === '1') { // Global enable
            if ($special_pass === false) {
                $special_pass = true;
            }
            continue;
        }

        if (get_value('trigger_access_tag__' . $tag) === '-1') { // Global disable
            // Overrides everything else
            $special_pass = false;
            return false;
        }
    }

    if (!$special_pass) {
        $access_code = get_param_string('keep_access_code', '');
        if (!in_array($access_code, ['', 'login', 'session'])) {
            check_access_code__trigger($special_pass, $trigger_tags, $access_code);
        }
    }

    // No fast cache for early access content
    if ($special_pass) {
        require_code('static_cache');
        global $INVALIDATED_FAST_SPIDER_CACHE;
        $INVALIDATED_FAST_SPIDER_CACHE = true;
    }

    if (!$special_pass) {
        return false;
    }

    return true;
}

/**
 * Check if a given access code is valid, and get its row as well.
 *
 * @param ID_TEXT $access_code The access code to check
 * @param ?array $row The database row for the given access code, passed by reference (null: not found)
 * @return ?Tempcode An error message that should be attached with warn (null: no error; the access code is valid)
 * @ignore
 */
function _check_access_code(string $access_code, ?array &$row) : ?object
{
    global $EARLY_ACCESS_CODE_VIEW_COUNTED;

    // Invalid codes
    if (in_array($access_code, ['', 'login', 'session'])) {
        $row = null;
        return do_lang_tempcode('INVALID_EARLY_ACCESS_CODE');
    }

    // Check access code exists
    $early_access_code_rows = $GLOBALS['SITE_DB']->query_select('early_access_codes', ['*'], ['c_access_code' => $access_code], '', 1);
    if (!array_key_exists(0, $early_access_code_rows)) {
        $row = null;
        return do_lang_tempcode('INVALID_EARLY_ACCESS_CODE');
    }

    $row = $early_access_code_rows[0];

    $num_views_assigned = $row['c_num_views_allowed'];
    $num_views_used = $row['c_num_views'];

    // Check if all views were used
    if ((!$EARLY_ACCESS_CODE_VIEW_COUNTED) && ($num_views_assigned !== null) && ($num_views_used >= $num_views_assigned)) {
        return do_lang_tempcode('EARLY_ACCESS_CODE_EXPIRED__VIEWS', escape_html(integer_format($num_views_assigned)));
    }

    // Check if expired or not yet valid
    require_code('temporal');

    $date_from = $row['c_date_from'];
    $date_to = $row['c_date_to'];
    if (($date_from !== null) && ($date_from > time())) {
        return do_lang_tempcode('EARLY_ACCESS_CODE_EXPIRED__TOO_EARLY', escape_html(get_timezoned_date($date_from, true, false)));
    }
    if (($date_to !== null) && ($date_to < time())) {
        return do_lang_tempcode('EARLY_ACCESS_CODE_EXPIRED__TOO_LATE', escape_html(get_timezoned_date($date_to, true, false)));
    }

    // At this point, the code is valid
    return null;
}

/**
 * Check if the given access code grants early access to the given content.
 *
 * @param  boolean $special_pass Whether we have early access, passed by reference
 * @param  ID_TEXT $content_type The content type being accessed
 * @param  ID_TEXT $content_id The content ID being accessed
 * @param  string $access_code The access code to check
 */
function check_access_code__content(bool &$special_pass, string $content_type, string $content_id, string $access_code)
{
    // Check the access code
    $row = [];
    $error = _check_access_code($access_code, $row);
    if ($error !== null) {
        attach_message($error, 'warn');
        return;
    }

    // Check for content-specific access
    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('early_access_code_content', 'a_access_code', ['a_content_type' => $content_type, 'a_content_id' => $content_id, 'a_access_code' => $access_code]);
    if ($test === null) {
        return;
    }

    // We have access, so finalise it
    $special_pass = true;
    _finalise_early_access($row);
}

/**
 * Check if the given access code grants early access to any of the given trigger tags.
 *
 * @param  boolean $special_pass Whether we have early access, passed by reference
 * @param  array $trigger_tags An array of trigger tags we want to check against where any one of them grants access
 * @param  string $access_code The access code to check (blank: we only want to check global overrides)
 */
function check_access_code__trigger(bool &$special_pass, array $trigger_tags, string $access_code)
{
    // Check the access code
    $row = [];
    $error = _check_access_code($access_code, $row);
    if ($error !== null) {
        $special_pass = false;
        attach_message($error, 'warn');
        return;
    }

    // Does the access code trigger exist in our allowed triggers?
    $trigger_tag = $row['c_trigger_access'];
    if (($trigger_tag != '') && in_array($trigger_tag, $trigger_tags)) {
        $special_pass = true;
    }

    // We have access as part of an access code, so finalise it
    if ($special_pass) {
        _finalise_early_access($row);
    }
}

/**
 * Finalise granted access to early content.
 *
 * @param array $row The access code database row
 * @ignore
 */
function _finalise_early_access(array $row)
{
    $access_code = $row['c_access_code'];
    $num_views_used = $row['c_num_views'];
    $num_views_allowed = $row['c_num_views_allowed'];
    $date_to = $row['c_date_to'];

    // Count a view if we have not already done so on this page load
    global $EARLY_ACCESS_CODE_VIEW_COUNTED;
    if (!$EARLY_ACCESS_CODE_VIEW_COUNTED) {
        $num_views_used++;
        $GLOBALS['SITE_DB']->query_update('early_access_codes', ['c_num_views' => $num_views_used], ['c_access_code' => $access_code]);
        $EARLY_ACCESS_CODE_VIEW_COUNTED = true;
    }

    // Generate a message to attach
    $ret = new Tempcode();
    $ret->attach(paragraph(do_lang_tempcode('EARLY_ACCESS_CODE_IN_USE')));
    if ($num_views_allowed !== null) {
        $ret->attach(paragraph(do_lang_tempcode('EARLY_ACCESS_CODE_VIEWS_LEFT', escape_html(integer_format($num_views_used)), escape_html(integer_format($num_views_allowed)))));
    }
    if ($date_to !== null) {
        $ret->attach(paragraph(do_lang_tempcode('EARLY_ACCESS_CODE_EXPIRES', escape_html(get_timezoned_date($date_to, true, false)))));
    }
    attach_message($ret, 'inform');
}

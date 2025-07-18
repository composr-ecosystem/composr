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
 * @package    core
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__wordfilter()
{
    global $WORDFILTERING_ALREADY;
    $WORDFILTERING_ALREADY = false;
}

/**
 * Check the specified text ($a) for banned words.
 * If any are found, and the member cannot bypass the word filter, an error message is displayed.
 *
 * @param  string $a The sentence to check
 * @param  ?ID_TEXT $name The name of the parameter this is coming from. Certain parameters are not checked, for reasons of efficiency (avoiding loading whole word check list if not needed) (null: don't know param, do not check to avoid)
 * @param  boolean $no_die Whether to avoid dying on fully blocked words (useful if importing, for instance)
 * @param  boolean $try_patterns Whether to try pattern matching (this takes more resources)
 * @param  boolean $perm_check Whether to allow permission-based skipping, and length-based skipping
 * @return string "Fixed" version
 */
function check_wordfilter($a, $name = null, $no_die = false, $try_patterns = false, $perm_check = true)
{
    global $WORDFILTERING_ALREADY;
    if ($WORDFILTERING_ALREADY) {
        return $a;
    }

    if ($perm_check) {
        if (strlen($a) < 3) {
            return $a;
        }
        if ((function_exists('has_privilege')) && (!$GLOBALS['MICRO_AJAX_BOOTUP']) && (has_privilege(get_member(), 'bypass_wordfilter'))) {
            return $a;
        }
    }

    // Load filter
    global $WORDS_TO_FILTER_CACHE;
    if (is_null($WORDS_TO_FILTER_CACHE)) {
        $WORDS_TO_FILTER_CACHE = array();
        $rows = $GLOBALS['SITE_DB']->query_select('wordfilter', array('*'), null, '', null, null, true);
        if (!is_null($rows)) {
            foreach ($rows as $i => $r) {
                if (($i == 0) && (!array_key_exists('w_replacement', $r))) {
                    return $a; // Safe upgrading
                }
                $WORDS_TO_FILTER_CACHE[strtolower($r['word'])] = $r;
            }
        }
    }

    // Find words
    $words = str_word_count($a, 2);
    if (is_null($words)) {
        $words = array(); // HPHP issue #113
    }

    // Apply filter for complete blocked words
    $changes = array();
    foreach ($words as $pos => $word) {
        if ((array_key_exists(strtolower($word), $WORDS_TO_FILTER_CACHE)) && ($WORDS_TO_FILTER_CACHE[strtolower($word)]['w_substr'] == 0)) {
            $w = $WORDS_TO_FILTER_CACHE[strtolower($word)];
            if (($w['w_replacement'] == '') && (!$no_die)) {
                warn_exit_wordfilter($name, do_lang_tempcode('WORDFILTER_YOU', escape_html($word))); // In soviet Russia, words filter you
            } else {
                $changes[] = array($pos, $word, $w['w_replacement']);
            }
        }

        if ($try_patterns) {
            // Now try patterns
            foreach ($WORDS_TO_FILTER_CACHE as $word2 => $w) {
                if (($w['w_substr'] == 0) && (simulated_wildcard_match($word, $word2, true))) {
                    if (($w['w_replacement'] == '') && (!$no_die)) {
                        warn_exit_wordfilter($name, do_lang_tempcode('WORDFILTER_YOU', escape_html($word))); // In soviet Russia, words filter you
                    } else {
                        $changes[] = array($pos, $word, $w['w_replacement']);
                    }
                }
            }
        }
    }

    // Make changes
    $changes = array_reverse($changes);
    foreach ($changes as $change) {
        $before = substr($a, 0, $change[0]);
        $after = substr($a, $change[0] + strlen($change[1]));
        $a = $before . $change[2] . $after;
    }

    // Apply filter for disallowed substrings
    foreach ($WORDS_TO_FILTER_CACHE as $word => $w) {
        if (is_integer($word)) {
            $word = strval($word);
        }

        if (($w['w_substr'] == 1) && (strpos($a, $word) !== false)) {
            if (($w['w_replacement'] == '') && (!$no_die)) {
                warn_exit_wordfilter($name, do_lang_tempcode('WORDFILTER_YOU', escape_html($word)));
            } else {
                $a = preg_replace('#' . preg_quote($word) . '#i', $w['w_replacement'], $a);
            }
        }
    }

    return $a;
}

/**
 * Exit with a message about word-filtering.
 *
 * @param  ?ID_TEXT $name The name of the parameter this is coming from. Certain parameters are not checked, for reasons of efficiency (avoiding loading whole word check list if not needed) (null: don't know param, do not check to avoid)
 * @param  Tempcode $message Error message
 * @return mixed Never returns (i.e. exits)
 */
function warn_exit_wordfilter($name, $message)
{
    global $WORDFILTERING_ALREADY;
    $WORDFILTERING_ALREADY = true;

    if (is_null($name)) {
        warn_exit($message);
    }

    require_code('failure');
    if (throwing_errors()) {
        throw new CMSException($message);
    }

    require_code('global3');
    set_http_status_code('400');

    // Output our error / correction form
    cms_ob_end_clean(); // Emergency output, potentially, so kill off any active buffer
    $hidden = build_keep_post_fields(array($name));
    require_code('form_templates');
    $value = post_param_string($name);
    if (strpos($value, "\n") === false) {
        $fields = form_input_line(do_lang_tempcode('CHANGE'), '', $name, $value, true);
    } else {
        $fields = form_input_text(do_lang_tempcode('CHANGE'), '', $name, $value, true);
    }
    $post_url = get_self_url();
    $output = do_template('FORM_SCREEN', array('_GUID' => 'e644c444027b244ebc382eae66ae23fc', 'TITLE' => get_screen_title('ERROR_OCCURRED'), 'TEXT' => $message, 'URL' => $post_url, 'HIDDEN' => $hidden, 'FIELDS' => $fields, 'SUBMIT_ICON' => 'buttons__proceed', 'SUBMIT_NAME' => do_lang_tempcode('PROCEED')));
    $echo = globalise($output, null, '', true);
    $echo->handle_symbol_preprocessing();
    $echo->evaluate_echo();
    exit();
}

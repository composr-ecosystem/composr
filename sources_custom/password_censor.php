<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    password_censor
 */

function init__password_censor()
{
    define('PASSWORD_CENSOR__PRE_SCAN', 0);
    define('PASSWORD_CENSOR__INTERACTIVE_SCAN', 1);
    define('PASSWORD_CENSOR__TIMEOUT_SCAN', 2);
}

function password_censor($auto = false, $display = true, $days_ago = 30)
{
    if (!addon_installed('tickets')) {
        exit('tickets addon not installed');
    }

    @header('X-Robots-Tag: noindex');

    if ($display) {
        if (!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) {
            exit('Permission denied');
        }
    }

    $_forum = get_option('ticket_forum_name');
    if (is_numeric($_forum)) {
        $forum_id = intval($_forum);
    } else {
        $forum_id = $GLOBALS['FORUM_DRIVER']->forum_id_from_name($_forum);
    }

    $sql = 'SELECT p.id,p_post FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts p';
    $sql .= ' WHERE (' . $GLOBALS['FORUM_DB']->translate_field_ref('p_post') . ' LIKE \'%password%\' OR ' . $GLOBALS['FORUM_DB']->translate_field_ref('p_post') . ' LIKE \'%Password%\')';
    $sql .= ' AND (p_cache_forum_id=' . strval($forum_id) . ' OR p_cache_forum_id IS NULL OR p_whisper_to_member IS NOT NULL)';
    $sql .= ' AND p_time<=' . strval(time() - 60 * 60 * 24 * $days_ago);
    $rows = $GLOBALS['FORUM_DB']->query($sql, null, 0, false, false, ['p_post' => 'LONG_TRANS__COMCODE']);
    if ($display) {
        header('Content-Type: text/plain; charset=' . get_charset());
    }

    foreach ($rows as $row) {
        $text_start = get_translated_text($row['p_post'], $GLOBALS['FORUM_DB']);
        $text_after = _password_censor($text_start, PASSWORD_CENSOR__TIMEOUT_SCAN);
        if ($text_after != $text_start) {
            if (multi_lang_content()) {
                $update_query = 'UPDATE ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'translate SET text_original=\'' . db_escape_string($text_after) . '\',text_parsed=\'\' WHERE id=' . strval($row['p_post']);
            } else {
                $update_query = 'UPDATE ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts SET p_post=\'' . db_escape_string($text_after) . '\',p_post__text_parsed=\'\' WHERE id=' . strval($row['id']);
            }

            if ($auto) {
                $GLOBALS['FORUM_DB']->query($update_query, null, 0, false, true);
            }

            if ($display) {
                echo $text_start . "\n\n-------->\n\n" . $text_after . "\n\n-------------\n\n" . $update_query . "\n\n<-----------\n\n\n\n\n";
            }
        }
    }
}

function _password_censor($text, $scan_type = 1, $explicit_only = false)
{
    $original_text = $text;

    if (($explicit_only) || (strpos($text, '[self_destruct') !== false) || (strpos($text, '[encrypt') !== false)) { // Explicit control, Comcode writer knows what they're doing
        if ($scan_type != PASSWORD_CENSOR__PRE_SCAN) {
            $matches = [];
            $num_matches = preg_match_all('#\[self_destruct[^\]]*\](.*)\[/self_destruct\]#Us', $text, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $text = str_replace($matches[0][$i], ($scan_type == PASSWORD_CENSOR__INTERACTIVE_SCAN) ? '(auto-censored)' : '(self-destructed)', $text);
            }
        }

        // Check for text to encrypt
        $matches = [];
        $num_matches = preg_match_all('#\[encrypt[^\]]*\](.*)\[/encrypt\]#Us', $text, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            if ($scan_type != PASSWORD_CENSOR__PRE_SCAN) {
                $text = str_replace($matches[0][$i], '(encrypted)', $text);
            } else {
                require_code('encryption');
                if (is_encryption_enabled()) {
                    $text = str_replace($matches[0][$i], '[encrypt]' . encrypt_data($matches[1][$i]) . '[/encrypt]', $text);
                } else {
                    $text = str_replace($matches[0][$i], '(encryption not available, cannot save)', $text);
                }
            }
        }
    } else { // Try and detect things to censor
        if ($scan_type != PASSWORD_CENSOR__PRE_SCAN) {
            $matches = [];
            $num_matches = preg_match_all('#(^|[^\w])([^\s"\'=:]{5,255})#', $text, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $m = $matches[2][$i];

                // Strip tags, so these aren't considered for passwords
                $m = preg_replace('#\[[^\]]+\]#', '', $m);
                $m = preg_replace('#<[^>]+>#', '', $m);

                // Strip brackets
                $m = ltrim($m, '<[{(');
                $m = rtrim($m, '>]})');

                // Skip blanks
                if ($m == '') {
                    continue;
                }

                // Skip explicit labels
                if (cms_strtolower_ascii(trim($m, ':')) == 'password') {
                    continue;
                }
                if (cms_strtolower_ascii(trim($m, ':')) == 'username') {
                    continue;
                }
                if (cms_strtolower_ascii($m) == 'reminder') {
                    continue;
                }

                if ($GLOBALS['FORUM_DRIVER']->get_member_from_username($m) !== null) {
                    continue; // Skip; is a username
                }
                if (preg_match('#://[^ ]*' . preg_quote($m, '#') . '#', $text) != 0) {
                    continue; // Skip; is a URL
                }

                // TODO: skip emoticons

                $probably_plural_word = true;

                // Add a point for each category of characters found
                $c = 0;
                if (preg_match('#\d#', $m) != 0) { // Digits
                    $c++;
                    $probably_plural_word = false;
                }
                if (preg_match('#,+[A-Z]#', $m) != 0) { // Uppercase letters
                    $c++;
                }
                if (preg_match('#[a-z]#', $m) != 0) { // Lowercase letters
                    $c++;
                }
                if (preg_match('#[^\w]#', $m) != 0) { // Symbols
                    $c++;
                    if (preg_match('#[^\']#', $m) != 0) {
                        $probably_plural_word = false;
                    }
                }
                if ((is_numeric($m)) && (strlen($m) >= 4)) { // Numerical strings greater than or equal to 4 characters long with no delimiters; possibly a sensitive PIN or ID number
                    $c++;
                }
                if ((is_numeric($m)) && (strlen($m) >= 8)) { // Numerical strings greater than or equal to 8 characters long with no delimiters; almost certainly a PIN number of some sort
                    $c++;
                }

                // Add points if a label exists indicating this is probably a password
                if (preg_match('#(password|pass|pword|pw|p/w|pwd|pin)\s*:?=?\s*\n' . preg_quote($m, '#') . '#i', $text) != 0) {
                    $c++; // Potential passwords are on a new line; less likely to be an actual password than if they were on the same line but we should still add 1 point
                    $probably_plural_word = false;
                } elseif (preg_match('#(code|secret|key)\s*:?=?\s*\n?' . preg_quote($m, '#') . '#i', $text) != 0) {
                    $c++; // The words "code", "secret", and "key" are less likely to indicate a password, but they could, so add 1 point
                    $probably_plural_word = false;
                } elseif (preg_match('#(password|pass|pword|pw|p/w|pwd|pin)\s*:?=?\s*' . preg_quote($m, '#') . '#i', $text) != 0) {
                    $c += 2; // Password labels with a colon or equal sign on the same line are almost certainly passwords. Add 2 points.
                    $probably_plural_word = false;
                }

                // If the score is 3 points or more, and this probably isn't just a pluralised word, censor it.
                if (($c >= 3) && (!$probably_plural_word)) {
                    $text = str_replace($m, '(auto-censored)', $text);
                }
            }
        }
    }

    return $text;
}

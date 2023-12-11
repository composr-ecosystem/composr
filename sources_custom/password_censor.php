<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    $sql .= ' WHERE (' . $GLOBALS['SITE_DB']->translate_field_ref('p_post') . ' LIKE \'%password%\' OR ' . $GLOBALS['SITE_DB']->translate_field_ref('p_post') . ' LIKE \'%Password%\')';
    $sql .= ' AND (p_cache_forum_id=' . strval($forum_id) . ' OR p_cache_forum_id IS NULL OR p_intended_solely_for IS NOT NULL)';
    $sql .= ' AND p_time<=' . strval(time() - 60 * 60 * 24 * $days_ago);

    $rows = $GLOBALS['FORUM_DB']->query($sql, null, null, false, false, array('p_post' => 'LONG_TRANS__COMCODE'));
    if ($display) {
        header('Content-type: text/plain; charset=' . get_charset());
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
                $GLOBALS['FORUM_DB']->query($update_query, null, null, false, true);
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
            $matches = array();
            $num_matches = preg_match_all('#\[self_destruct[^\]]*\](.*)\[/self_destruct\]#Us', $text, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $text = str_replace($matches[0][$i], ($scan_type == PASSWORD_CENSOR__INTERACTIVE_SCAN) ? '(auto-censored)' : '(self-destructed)', $text);
            }
        }

        $matches = array();
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
            $matches = array();
            $num_matches = preg_match_all('#(^|[^\w])([^\s"\'=]{5,30})#', $text, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $m = $matches[2][$i];

                // Strip tags, so these aren't considered for passwords
                $m = preg_replace('#\[[^\]]+\]#', '', $m);
                $m = preg_replace('#\<[^\>]+\>#', '', $m);

                // Strip brackets
                $m = ltrim($m, '<[{(');
                $m = rtrim($m, '>]})');

                if ($m == '') {
                    continue;
                }
                if (strtolower(trim($m, ':')) == 'password') {
                    continue;
                }
                if (strtolower(trim($m, ':')) == 'username') {
                    continue;
                }
                if (strtolower($m) == 'reminder') {
                    continue;
                }
                if (!is_null($GLOBALS['FORUM_DRIVER']->get_member_from_username($m))) {
                    continue; // A username
                }
                if (preg_match('#://[^ ]*' . preg_quote($m, '#') . '#', $text) != 0) {
                    continue; // Part of a URL
                }

                $c = 0;
                if (preg_match('#\d#', $m) != 0) {
                    $c++;
                }
                if (preg_match('#,+[A-Z]#', $m) != 0) {
                    $c++;
                }
                if (preg_match('#[a-z]#', $m) != 0) {
                    $c++;
                }
                if (preg_match('#[^\w]#', $m) != 0) {
                    $c++;
                }
                if ((is_numeric($m)) && (strlen($m) > 6)) {
                    $c++;
                }
                if (preg_match('#(password|pass|pword|pw|p/w)\s*:?=?\s+' . preg_quote($m, '#') . '#i', $text) != 0) {
                    $c += 2;
                }
                if ($c >= 3) {
                    $text = str_replace($m, '(auto-censored)', $text);
                }
            }
        }
    }

    return $text;
}

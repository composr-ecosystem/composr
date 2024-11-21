<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    top_posters
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('top_posters')) {
    return do_template('RED_ALERT', ['_GUID' => '2000b6d0271353429d4a10c4040c8335', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('top_posters'))]);
}

if (get_forum_type() != 'cns') {
    return do_template('RED_ALERT', ['_GUID' => '57db11e5a68d54ff812bc99f3964fbd5', 'TEXT' => do_lang_tempcode('NO_CNS')]);
}

?>

<table class="columned-table results-table wide-table">
    <thead>
    <tr>
        <th>Avatar</th>
        <th>Member</th>
        <th>Average post length</th>
        <th>Number of posts</th>
    </tr>
    </thead>
    <tbody>
    <?php

    $max = array_key_exists('max', $map) ? intval($map['max']) : 10;

    $sql = 'SELECT m.id,AVG(' . db_function('LENGTH', [$GLOBALS['FORUM_DB']->translate_field_ref('p_post')]) . ') AS avg,COUNT(*) AS cnt FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members m LEFT JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts p ON p.p_posting_member=m.id WHERE m.id<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' GROUP BY m.id ORDER BY avg DESC';
    $members = $GLOBALS['FORUM_DB']->query($sql, $max, 0, false, false, ['p_post' => 'LONG_TRANS__COMCODE']);

    foreach ($members as $_member) {
        $member_id = $_member['id'];
        $av_post_length = @intval(round($_member['avg']));

        $_avatar_url = escape_html($GLOBALS['FORUM_DRIVER']->get_member_avatar_url($member_id));
        $url = $GLOBALS['FORUM_DRIVER']->member_profile_url($member_id, true);
        if (is_object($url)) {
            $url = $url->evaluate();
        }
        $_url = escape_html($url);
        $_avatar = ($_avatar_url != '') ? ('<img alt="Avatar" src="' . $_avatar_url . '" />') : '';
        $_username = escape_html($GLOBALS['FORUM_DRIVER']->get_username($member_id, true));
        $_av_post_length = escape_html(integer_format($av_post_length));
        $_num_posts = escape_html(integer_format($_member['cnt'], 0));

        echo <<<END
        <tr>
                <td>{$_avatar}</td>
                <td><a href="{$_url}">{$_username}</a></td>
                <td>{$_av_post_length} letters</td>
                <td>{$_num_posts} posts</td>
        </tr>
END;
    }
    ?>
    </tbody>
</table>

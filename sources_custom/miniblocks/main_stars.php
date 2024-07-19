<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    idolisr
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('idolisr')) {
    return do_template('RED_ALERT', ['_GUID' => '22e04956ec18c41717e3b34dba4c00c3', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('idolisr'))]);
}

if (!addon_installed('points')) {
    return do_template('RED_ALERT', ['_GUID' => 'zm4ccp3vsxc8wj32w3e4ejtrkn35705x', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('points'))]);
}

require_code('cns_groups');
require_code('cns_members');
require_lang('cns');

$block_id = get_block_id($map);

$stars = [];

if (@cms_empty_safe($map['param'])) {
    return do_template('RED_ALERT', ['_GUID' => 'f4feed787ebcf1d007ba53625c8accce', 'TEXT' => do_lang_tempcode('NO_PARAMETER_SENT', 'param')]);
}

$sql = 'SELECT receiving_member,SUM(amount_gift_points+amount_points) as cnt FROM ' . get_table_prefix() . 'points_ledger g WHERE ';
$sql .= $GLOBALS['SITE_DB']->translate_field_ref('reason') . ' LIKE \'' . db_encode_like($map['param'] . ': %') . '\' AND sending_member<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());
$sql .= ' GROUP BY receiving_member ORDER BY cnt DESC';
$rows = $GLOBALS['SITE_DB']->query($sql, 10, 0, false, false, ['reason' => 'SHORT_TRANS']);

if (empty($rows) && $GLOBALS['DEV_MODE']) {
    $rows[] = ['receiving_member' => 2, 'cnt' => 123];
    $rows[] = ['receiving_member' => 3, 'cnt' => 7334];
}

$count = 0;
foreach ($rows as $row) {
    $member_id = $row['receiving_member'];
    $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id, true, USERNAME_DEFAULT_NULL);
    if ($username !== null) {
        $url = $GLOBALS['FORUM_DRIVER']->member_profile_url($member_id, true);
        $avatar_url = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($member_id);
        $just_member_row = db_map_restrict($GLOBALS['FORUM_DRIVER']->get_member_row($member_id), ['id', 'm_signature']);
        $signature = get_translated_tempcode('f_members', $just_member_row, 'm_signature', $GLOBALS['FORUM_DB']);
        $points = $row['cnt'];
        $rank = get_translated_text(cns_get_group_property(cns_get_member_primary_group($member_id), 'name'), $GLOBALS['FORUM_DB']);

        $stars[] = [
            'MEMBER_ID' => strval($member_id),
            'USERNAME' => $username,
            'URL' => $url,
            'AVATAR_URL' => $avatar_url,
            '_POINTS' => strval($points),
            'POINTS' => integer_format($points, 0),
            'RANK' => $rank,
            'SIGNATURE' => $signature,
        ];

        $count++;
    }
}

return do_template('BLOCK_MAIN_STARS', [
    '_GUID' => '298e81f1062087de02e30d77ff61305d',
    'BLOCK_ID' => $block_id,
    'STARS' => $stars,
]);

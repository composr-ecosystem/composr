<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    idolisr
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_code('cns_groups');
require_code('cns_members');
require_lang('cns');

$stars = array();

$sql = 'SELECT gift_to,SUM(amount) as cnt FROM ' . get_table_prefix() . 'gifts g WHERE ';
$sql .= $GLOBALS['SITE_DB']->translate_field_ref('reason') . ' LIKE \'' . db_encode_like($map['param'] . ': %') . '\' AND gift_from<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id());
$sql .= ' GROUP BY gift_to ORDER BY cnt DESC';
$gifts = $GLOBALS['SITE_DB']->query($sql, 10, null, false, false, array('reason' => 'SHORT_TRANS'));

if (count($gifts) == 0 && $GLOBALS['DEV_MODE']) {
    $gifts[] = array('gift_to' => 2, 'cnt' => 123);
    $gifts[] = array('gift_to' => 3, 'cnt' => 7334);
}

$count = 0;
foreach ($gifts as $gift) {
    $member_id = $gift['gift_to'];
    $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id, true);
    if (!is_null($username)) {
        $url = $GLOBALS['FORUM_DRIVER']->member_profile_url($member_id);
        $avatar_url = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($member_id);
        $just_member_row = db_map_restrict($GLOBALS['FORUM_DRIVER']->get_member_row($member_id), array('id', 'm_signature'));
        $signature = get_translated_tempcode('f_members', $just_member_row, 'm_signature', $GLOBALS['FORUM_DB']);
        $points = $gift['cnt'];
        $rank = get_translated_text(cns_get_group_property(cns_get_member_primary_group($member_id), 'name'), $GLOBALS['FORUM_DB']);

        $stars[] = array(
            'MEMBER_ID' => strval($member_id),
            'USERNAME' => $username,
            'URL' => $url,
            'AVATAR_URL' => $avatar_url,
            'POINTS' => integer_format($points),
            'RANK' => $rank,
            'SIGNATURE' => $signature,
        );

        $count++;
    }
}

return do_template('BLOCK_MAIN_STARS', array('_GUID' => '298e81f1062087de02e30d77ff61305d', 'STARS' => $stars));

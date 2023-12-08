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

$max = array_key_exists('max', $map) ? intval($map['max']) : 10;

$sql = 'SELECT * FROM ' . get_table_prefix() . 'gifts g WHERE gift_from<>' . strval($GLOBALS['FORUM_DRIVER']->get_guest_id()) . ' ORDER BY g.id DESC';
$gifts = $GLOBALS['SITE_DB']->query($sql, $max, null, false, false, array('reason' => 'SHORT_TRANS__COMCODE'));

$_gifts = array();

require_code('templates_interfaces');

foreach ($gifts as $gift) {
    $amount = $gift['amount'];
    if ($amount <= 0) {
        continue;
    }

    $from_name = $GLOBALS['FORUM_DRIVER']->get_username($gift['gift_from'], true);
    if (is_null($from_name)) {
        continue;
    }
    $from_url = build_url(array('page' => 'points', 'type' => 'member', 'id' => $gift['gift_from']), get_module_zone('points'));
    $from_link = hyperlink($from_url, $from_name, false, true);

    $to_name = $GLOBALS['FORUM_DRIVER']->get_username($gift['gift_to'], true);
    if (is_null($to_name)) {
        continue;
    }
    $to_url = build_url(array('page' => 'points', 'type' => 'member', 'id' => $gift['gift_to']), get_module_zone('points'));
    $to_link = do_template('MEMBER_TOOLTIP', array('_GUID' => '0cdd0adf612cf0f50a732daa79718d09', 'SUBMITTER' => strval($gift['gift_to'])));//hyperlink($to_url, $to_name, false, true);

    $reason = get_translated_text($gift['reason']);

    $_gifts[] = array(
        'AMOUNT' => integer_format($amount),
        '_AMOUNT' => strval($amount),

        'FROM_NAME' => $from_name,
        'FROM_ID' => strval($gift['gift_from']),
        'FROM_URL' => $from_url,
        'FROM_LINK' => $from_link,

        'TO_NAME' => $to_name,
        'TO_ID' => strval($gift['gift_to']),
        'TO_URL' => $to_url,
        'TO_LINK' => $to_link,

        'REASON' => $reason,

        'ANONYMOUS' => ($gift['anonymous'] == 1),
    );
}

$tpl = do_template('BLOCK_SIDE_RECENT_POINTS', array('_GUID' => 'ee241c0bd5356f1d6e28a9de3cdfa387', 'GIFTS' => $_gifts));
$tpl->evaluate_echo();

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    ad_success
 */

/*
Simple script to track advertising purchase successes.
Requires super_logging enabled.
Probably better to configure tracking codes in Google Analytics TBH.

Assumes '_t' GET parameter used to track what campaign hits came from.

May be very slow to run.
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('ad_success', $error_msg)) {
    return $error_msg;
}

$title = get_screen_title('Simple referral tracker', false);
$title->evaluate_echo();

$success = [];
$joining = [];
$failure = [];
$query = 'SELECT member_id,page_link,ip,date_and_time FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'stats WHERE date_and_time>' . strval(time() - 60 * 60 * 24 * get_param_integer('days', 1)) . ' AND s_get LIKE \'' . db_encode_like('%<param>_t=%') . '\'';
if ($GLOBALS['DB_STATIC_OBJECT']->can_arbitrary_groupby()) {
    $query .= ' GROUP BY member_id';
}
$advertiser_sessions = $GLOBALS['SITE_DB']->query($query);
$advertiser_sessions = remove_duplicate_rows($advertiser_sessions, 'member_id');
foreach ($advertiser_sessions as $session) {
    list(, $attributes) = page_link_decode($session['page_link']);

    if (!isset($attributes['_t'])) {
        continue;
    }

    $_t = $attributes['_t'];
    $member_id = $session['member_id'];

    if (!array_key_exists($_t, $success)) {
        $success[$_t] = 0;
        $failure[$_t] = 0;
        $joining[$_t] = 0;
    }

    if (get_param_integer('track', 0) == 1) {
        echo '<strong>Tracking information for <em>' . $_t . '</em> visitor</strong> (' . $session['ip'] . ')&hellip;<br />';
        $places = $GLOBALS['SITE_DB']->query('SELECT page_link,date_and_time,referer FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'stats WHERE member_id=' . strval($member_id) . ' AND date_and_time>=' . strval($session['date_and_time']) . ' ORDER BY date_and_time');
        foreach ($places as $place) {
            echo '<p>' . escape_html($place['page_link']) . ' at ' . get_timezoned_date_time($place['date_and_time'], false) . ' (from ' . escape_html(substr($place['referer'], 0, 200)) . ')</p>';
        }
    }

    $ip = $GLOBALS['SITE_DB']->query_select_value_if_there('stats', 'ip', ['page_link' => ':join', 'member_id' => $member_id]);
    $member_id = ($ip === null) ? null : $GLOBALS['SITE_DB']->query_select_value_if_there('stats', 'member_id', ['ip' => $ip]);
    if ($member_id !== null) {
        $joining[$_t]++;
    }
    $test = ($member_id === null) ? null : $GLOBALS['SITE_DB']->query_select_value_if_there('stats', 'id', ['page_link' => get_page_zone('purchase') . ':purchase', 'member_id' => $member_id]);
    if ($test !== null) {
        $success[$_t]++;
    } else {
        $failure[$_t]++;
    }
}

echo '<p><b>Summary</b>&hellip;</p>';
echo 'Successes&hellip;';
var_dump($success);
echo '<br />';
echo 'Joinings&hellip;';
var_dump($joining);
echo '<br />';
echo 'Failures&hellip;';
var_dump($failure);

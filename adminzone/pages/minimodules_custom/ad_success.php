<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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

Assumes 'from' GET parameter used to track what campaign hits came from.

May be very slow to run.
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$title = get_screen_title('Simple referral tracker', false);
$title->evaluate_echo();

$success = array();
$joining = array();
$failure = array();
$query = 'SELECT member_id,s_get,ip,date_and_time FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'stats WHERE date_and_time>' . strval(time() - 60 * 60 * 24 * get_param_integer('days', 1)) . ' AND s_get LIKE \'' . db_encode_like('%<param>from=%') . '\'';
if (can_arbitrary_groupby()) {
    $query .= ' GROUP BY member_id';
}
$advertiser_sessions = $GLOBALS['SITE_DB']->query($query);
$advertiser_sessions = remove_duplicate_rows($advertiser_sessions, 'member_id');
foreach ($advertiser_sessions as $session) {
    $matches = array();
    if (!preg_match('#<param>from=([' . URL_CONTENT_REGEXP . ']+)</param>#', $session['s_get'], $matches)) {
        continue;
    }
    $from = $matches[1];
    $member_id = $session['member_id'];

    if (!array_key_exists($from, $success)) {
        $success[$from] = 0;
        $failure[$from] = 0;
        $joining[$from] = 0;
    }

    if (get_param_integer('track', 0) == 1) {
        echo '<strong>Tracking information for <em>' . $from . '</em> visitor</strong> (' . $session['ip'] . ')&hellip;<br />';
        $places = $GLOBALS['SITE_DB']->query('SELECT the_page,date_and_time,referer FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'stats WHERE member_id=' . strval($member_id) . ' AND date_and_time>=' . strval($session['date_and_time']) . ' ORDER BY date_and_time');
        foreach ($places as $place) {
            echo '<p>' . escape_html($place['the_page']) . ' at ' . get_timezoned_date($place['date_and_time'], false, false, false, true) . ' (from ' . escape_html(substr($place['referer'], 0, 200)) . ')</p>';
        }
    }

    $ip = $GLOBALS['SITE_DB']->query_select_value_if_there('stats', 'ip', array('the_page' => 'site/pages/modules/join.php', 'member_id' => $member_id));
    $member_id = is_null($ip) ? null : $GLOBALS['SITE_DB']->query_select_value_if_there('stats', 'member_id', array('ip' => $ip));
    if (!is_null($member_id)) {
        $joining[$from]++;
    }
    $test = is_null($member_id) ? null : $GLOBALS['SITE_DB']->query_select_value_if_there('stats', 'id', array('the_page' => 'site/pages/modules_custom/purchase.php', 'member_id' => $member_id));
    if (!is_null($test)) {
        $success[$from]++;
    } else {
        $failure[$from]++;
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

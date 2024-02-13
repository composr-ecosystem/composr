<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite_support_credits
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('composr_homesite_support_credits')) {
    return do_template('RED_ALERT', ['_GUID' => '4y9qesedhhw6peuc11wfmnu4eq5zioa1', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite_support_credits'))]);
}

if (!addon_installed('tickets')) {
    return do_template('RED_ALERT', ['_GUID' => 'xxuw4d79s9qmq2jgaris712w3nj95ce0', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('tickets'))]);
}
if (!addon_installed('ecommerce')) {
    return do_template('RED_ALERT', ['_GUID' => 'z9wzu51gk1qa89j52xdc7ccwpq6l3soz', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce'))]);
}
if (!addon_installed('points')) {
    return do_template('RED_ALERT', ['_GUID' => 'l56duwk68ywo099433j2st5472a25mqd', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('points'))]);
}

if (get_forum_type() != 'cns') {
    return do_template('RED_ALERT', ['_GUID' => '5v7eclbf681s79gdkminmik8ykk7abgy', 'TEXT' => do_lang_tempcode('NO_CNS')]);
}

if (strpos(get_db_type(), 'mysql') === false) {
    return do_template('RED_ALERT', ['_GUID' => 'uyjdizierysgbisjrrmbipait8lxfyen', 'TEXT' => 'This works with MySQL only']);
}

require_css('tracker');
require_lang('customers');

$block_id = get_block_id($map);

// Defer to inner frame
if (!running_script('tracker') && get_param_integer('keep_frames', null) !== 0) {
    $params = '?' . http_build_query($map);
    $params .= static_evaluate_tempcode(symbol_tempcode('KEEP'));

    $frame_name = 'frame_' . uniqid('', true);

    $tpl = do_template('BLOCK_MAIN_MANTIS_TRACKER', [
        '_GUID' => '52af5edf59440ba86c54e0324518561a',
        'BLOCK_ID' => $block_id,
        'FRAME_NAME' => $frame_name,
        'PARAMS' => $params,
    ]);
    $tpl->evaluate_echo();

    return;
}

// Some fundamental settings...

$sql = 'SELECT id FROM mantis_custom_field_table WHERE ' . db_string_equal_to('name', 'Time estimation (hours)');
$cms_hours_field = $GLOBALS['FORUM_DB']->query_value_if_there($sql);

$s_currency = get_option('currency', true);
if (empty($s_currency)) {
    $s_currency = 'USD';
}

$s_credit_value = floatval(get_option('support_credit_price'));

$minutes_per_credit = intval(get_option('support_priority_backburner_minutes'));
$credits_per_hour = intval(60 / $minutes_per_credit);
if ($credits_per_hour == 0) {
    $credits_per_hour = 1;
}

// Patreons...

require_code('patreon');
$patreon_patrons = get_patreon_patrons_on_minimum_level(3);
if (!empty($patreon_patrons)) {
    $patreon_bonuses_a = '(';
    $patreon_bonuses_a .= 'SELECT COUNT(*)*3 FROM mantis_bug_monitor_table yy WHERE yy.bug_id=a.id AND yy.user_id IN ('; // 4-1=3
    foreach ($patreon_patrons as $i => $patron) {
        if ($i != 0) {
            $patreon_bonuses_a .= ',';
        }
        $patreon_bonuses_a .= '(SELECT uu.id FROM mantis_user_table uu WHERE ' . db_string_equal_to('uu.username', $patron['username']) . ')';
    }
    $patreon_bonuses_a .= ')';
    $patreon_bonuses_a .= ')';
} else {
    $patreon_bonuses_a = '0';
}
$patreon_patrons = get_patreon_patrons_on_minimum_level(10);
if (!empty($patreon_patrons)) {
    $patreon_bonuses_b = '(';
    $patreon_bonuses_b .= 'SELECT COUNT(*)*11 FROM mantis_bug_monitor_table yy WHERE yy.bug_id=a.id AND yy.user_id IN ('; // 15-4-1=11
    foreach ($patreon_patrons as $i => $patron) {
        if ($i != 0) {
            $patreon_bonuses_b .= ',';
        }
        $patreon_bonuses_b .= '(SELECT uu.id FROM mantis_user_table uu WHERE ' . db_string_equal_to('uu.username', $patron['username']) . ')';
    }
    $patreon_bonuses_b .= ')';
    $patreon_bonuses_b .= ')';
} else {
    $patreon_bonuses_b = '0';
}

// Build up SQL...

$select = 'a.*,b.description,d.name AS category';
$select .= ',(SELECT COUNT(*) FROM mantis_bugnote_table x WHERE x.bug_id=a.id) AS num_comments';
$select .= ',(SELECT COUNT(*) FROM mantis_bug_monitor_table y WHERE y.bug_id=a.id)+' . $patreon_bonuses_a . '+' . $patreon_bonuses_b . ' AS num_votes';
$select .= ',(SELECT SUM(amount) FROM mantis_sponsorship_table z WHERE z.bug_id=a.id) AS money_raised';
$select .= ',CAST(c.value AS DECIMAL) as hours';
$select .= ',CAST(c.value AS DECIMAL)*' . strval($credits_per_hour) . '*' . float_to_raw_string($s_credit_value) . ' AS currency_needed';

$table = 'mantis_bug_table a JOIN mantis_bug_text_table b ON b.id=a.bug_text_id JOIN mantis_custom_field_string_table c ON c.bug_id=a.id AND field_id=' . $cms_hours_field . ' JOIN mantis_category_table d ON d.id=a.category_id';

$where = 'duplicate_id=0';
$where .= ' AND view_state=10';
$where .= ' AND severity=10';
if (isset($map['completed'])) {
    $where .= ' AND ' . (($map['completed'] == '0') ? 'a.status<=50' : 'a.status=80');
}
if (isset($map['voted'])) {
    $where .= ' AND (' . (($map['voted'] == '1') ? /*disabled as messy if someone's reported lots 'a.reporter_id='.strval(get_member()).' OR '.*/'EXISTS' : 'NOT EXISTS') . ' (SELECT * FROM mantis_bug_monitor_table p WHERE user_id=' . strval(get_member()) . ' AND p.bug_id=a.id))';
}
if (isset($map['project'])) {
    $where .= ' AND a.project_id=' . strval(intval($map['project']));
}

$order = 'id';
if (isset($map['sort'])) {
    list($sort, $direction) = explode(' ', $map['sort'], 2);
    if (($direction != 'ASC') && ($direction != 'DESC')) {
        $direction = 'DESC';
    }
    switch ($sort) {
        case 'popular':
            $order = 'num_votes ' . $direction;
            break;
        case 'added':
            $order = 'date_submitted ' . $direction;
            break;
        case 'hours':
            $order = 'hours ' . $direction;
            $where .= ' AND ' . db_string_not_equal_to('c.value', '');
            break;
        case 'sponsorship_progress':
            $where .= ' AND (SELECT SUM(amount) FROM mantis_sponsorship_table z WHERE z.bug_id=a.id)<>0';
            $order = '(SELECT SUM(amount) FROM mantis_sponsorship_table z WHERE z.bug_id=a.id)/CAST(c.value AS DECIMAL)*' . strval($credits_per_hour) . '*' . float_to_raw_string($s_credit_value) . ' ' . $direction;
            break;
    }
}

$max = get_param_integer('mantis_max', 10);
$start = get_param_integer('mantis_start', 0);

$query = 'SELECT ' . $select . ' FROM ' . $table . ' WHERE ' . $where . ' ORDER BY ' . $order;
$_issues = $GLOBALS['SITE_DB']->query($query, $max, $start);

$query_count = 'SELECT COUNT(*) FROM ' . $table . ' WHERE ' . $where;
$max_rows = $GLOBALS['SITE_DB']->query_value_if_there($query_count);

$issues = [];
foreach ($_issues as $issue) {
    $cost = ($issue['hours'] == 0 || ($issue['hours'] === null)) ? null : ($issue['hours'] * $s_credit_value * $credits_per_hour);
    $_cost = ($cost === null) ? do_lang('FEATURES_UNKNOWN_lc') : (static_evaluate_tempcode(comcode_to_tempcode('[currency="' . $s_currency . '"]' . float_to_raw_string($cost) . '[/currency]')));
    $money_raised = ($issue['money_raised'] !== null) ? $issue['money_raised'] : 0.0;
    $_money_raised = static_evaluate_tempcode(comcode_to_tempcode('[currency="' . $s_currency . '"]' . float_to_raw_string($money_raised) . '[/currency]'));
    $_percentage = ($cost === null) ? do_lang('FEATURES_UNKNOWN_lc') : (escape_html(float_format(100.0 * $money_raised / $cost, 0)) . '%');
    $_hours = ($cost === null) ? do_lang('FEATURES_UNKNOWN_lc') : do_lang('FEATURES_HOURS_lc', escape_html(integer_format($issue['hours'])));
    $_credits = ($cost === null) ? do_lang('FEATURES_UNKNOWN_lc') : do_lang('FEATURES_CREDITS_lc', escape_html(integer_format($issue['hours'] * $credits_per_hour)));

    $voted = ($GLOBALS['SITE_DB']->query_value_if_there('SELECT user_id FROM mantis_bug_monitor_table WHERE user_id=' . strval(get_member()) . ' AND bug_id=' . strval($issue['id'])) !== null);

    $issues[] = [
        'CATEGORY' => $issue['category'],
        'SUMMARY' => $issue['summary'],
        'DESCRIPTION' => nl2br(escape_html($issue['description'])),

        'COST' => $_cost,
        'MONEY_RAISED' => $_money_raised,
        'PERCENTAGE' => $_percentage,
        'HOURS' => $_hours,
        'CREDITS' => $_credits,

        '_NUM_COMMENTS' => strval($issue['num_comments']),
        'NUM_COMMENTS' => integer_format($issue['num_comments'], 0),
        'DATE' => get_timezoned_date($issue['date_submitted']),
        'MEMBER_LINK' => $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($issue['reporter_id']),

        'VOTED' => $voted,
        '_VOTES' => strval(intval($issue['num_votes'])),
        'VOTES' => integer_format(intval($issue['num_votes']), 0),
        'VOTE_URL' => get_base_url() . '/tracker/bug_monitor_add.php?bug_id=' . strval($issue['id']),
        'UNVOTE_URL' => get_base_url() . '/tracker/bug_monitor_delete.php?bug_id=' . strval($issue['id']),

        'FULL_URL' => get_base_url() . '/tracker/view.php?id=' . strval($issue['id']),
    ];
}

// Pagination...

require_code('templates_pagination');
$pagination = pagination(make_string_tempcode('Issues'), $start, 'mantis_start', $max, 'mantis_max', $max_rows);

// Templating...

$tpl = do_template('MANTIS_TRACKER', [
    '_GUID' => '619919c2bf1e5207a4bf25111638f719',
    'BLOCK_ID' => $block_id,
    'ISSUES' => $issues,
    'PAGINATION' => $pagination,
]);
$tpl->evaluate_echo();

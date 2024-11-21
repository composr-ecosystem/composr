<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_tracker
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('cms_homesite_tracker')) {
    return do_template('RED_ALERT', ['_GUID' => '4d671f3291e8548fac9d6c9f3e632634', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite_tracker'))]);
}

if (!addon_installed('tickets')) {
    return do_template('RED_ALERT', ['_GUID' => '55b8a2367d975704a26365e9c6d15ba9', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('tickets'))]);
}
if (!addon_installed('ecommerce')) {
    return do_template('RED_ALERT', ['_GUID' => '21e96f65bd2d5040961b00274375015a', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce'))]);
}
if (!addon_installed('points')) {
    return do_template('RED_ALERT', ['_GUID' => '0af62402501f5980bf6f653eaf9e3fde', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('points'))]);
}

if (get_forum_type() != 'cns') {
    return do_template('RED_ALERT', ['_GUID' => '22c4cd11c9f25513af23afdb09bd2366', 'TEXT' => do_lang_tempcode('NO_CNS')]);
}

if (strpos(get_db_type(), 'mysql') === false) {
    return do_template('RED_ALERT', ['_GUID' => 'fe367ec7147456f1acee6be4dbdbdd7d', 'TEXT' => 'This works with MySQL only']);
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

// FUDGE: We are making the assumption off of $50/hour and 100 points = $1.
$s_currency = 'POINTS';
$s_points_per_hour = 2000;

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
$select .= ',(SELECT SUM(amount) FROM ' . get_table_prefix() . 'escrow z WHERE z.content_type=\'tracker_issue\' AND z.content_id=a.id AND status=2) AS points_raised';
$select .= ',CAST(c.value AS FLOAT) as hours';
if ($s_points_per_hour !== null) {
    $select .= ',CAST(c.value AS DECIMAL)*' . strval($s_points_per_hour) . ' AS currency_needed';
}

$table = 'mantis_bug_table a JOIN mantis_bug_text_table b ON b.id=a.bug_text_id JOIN mantis_custom_field_string_table c ON c.bug_id=a.id AND field_id=' . $cms_hours_field . ' JOIN mantis_category_table d ON d.id=a.category_id';

$where = 'duplicate_id=0';
$where .= ' AND view_state=10';
$where .= ' AND severity=10';
$where .= ' AND ' . ((isset($map['completed']) && ($map['completed'] == '1')) ? 'a.status=80' : 'a.status<=50');

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
            $where .= ' AND (SELECT SUM(amount) FROM ' . get_table_prefix() . 'escrow z WHERE z.content_type=\'tracker_issue\' AND z.content_id=a.id AND status=2)<>0';
            if ($s_points_per_hour !== null) {
                $order = '(SELECT SUM(amount) FROM ' . get_table_prefix() . 'escrow z WHERE z.content_type=\'tracker_issue\' AND z.content_id=a.id AND status=2)/CAST(c.value AS DECIMAL)*' . strval($s_points_per_hour) . ' ' . $direction;
            }
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
    if ($s_points_per_hour !== null) {
        $cost = ($issue['hours'] == 0 || ($issue['hours'] === null)) ? null : ($issue['hours'] * $s_points_per_hour);
    } else {
        $cost = null;
    }
    $_cost = ($cost === null) ? '' : integer_format($cost);
    $points_raised = ($issue['points_raised'] !== null) ? $issue['points_raised'] : 0.0;
    $_points_raised = integer_format($points_raised);
    $_percentage = ($cost === null) ? do_lang('FEATURES_UNKNOWN_lc') : (escape_html(float_format(100.0 * $points_raised / $cost, 0)) . '%');
    $_hours = ($cost === null) ? do_lang('FEATURES_UNKNOWN_lc') : do_lang('FEATURES_HOURS_lc', escape_html(integer_format($issue['hours'])));

    $voted = ($GLOBALS['SITE_DB']->query_value_if_there('SELECT user_id FROM mantis_bug_monitor_table WHERE user_id=' . strval(get_member()) . ' AND bug_id=' . strval($issue['id'])) !== null);

    $issues[] = [
        'CATEGORY' => $issue['category'],
        'SUMMARY' => $issue['summary'],
        'DESCRIPTION' => nl2br(escape_html($issue['description'])),

        'COST' => $_cost,
        'POINTS_RAISED' => $_points_raised,
        'PERCENTAGE' => $_percentage,
        'HOURS' => $_hours,

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

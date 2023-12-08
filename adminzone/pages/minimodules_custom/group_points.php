<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    group_points
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$title = get_screen_title('Usergroup point assignments', false);

require_code('points');
require_code('form_templates');

$groups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(false, true, true);
$done_something = false;
foreach (array_keys($groups) as $group_id) {
    $points_one_off = post_param_integer('points_one_off_' . strval($group_id), null);
    $points_per_month = post_param_integer('points_per_month_' . strval($group_id), null);
    if ($points_one_off !== null) {
        $GLOBALS['SITE_DB']->query_delete('group_points', array(
            'p_group_id' => $group_id,
        ), '', 1);

        $GLOBALS['SITE_DB']->query_insert('group_points', array(
            'p_group_id' => $group_id,
            'p_points_one_off' => $points_one_off,
            'p_points_per_month' => $points_per_month,
        ));

        $done_something = true;
    }
}

if ($done_something) {
    attach_message('New point bonuses saved.', 'inform');
}

$group_points = get_group_points();

$fields = new Tempcode();

foreach ($groups as $group_id => $group_name) {
    if ($group_id == db_get_first_id()) {
        continue;
    }

    if (isset($group_points[$group_id])) {
        $points = $group_points[$group_id];
    } else {
        $points = array('p_points_one_off' => 0, 'p_points_per_month' => 0);
    }

    $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => '096274e977e4cd99ef20eb4b1c2174e3', 'TITLE' => $group_name)));
    $fields->attach(form_input_integer('One-off bonus', '', 'points_one_off_' . strval($group_id), $points['p_points_one_off'], true));
    $fields->attach(form_input_integer('Points per month', '', 'points_per_month_' . strval($group_id), $points['p_points_per_month'], true));
}

$form = do_template('FORM_SCREEN', array(
    '_GUID' => '8677d9f9c1c4ad969188ddff850a1b2c',
    'TITLE' => $title,
    'HIDDEN' => '',
    'TEXT' => paragraph('Enter how many points being in each usergroup is worth. For the one-off bonuses, Composr will look at all the usergroups each member is in, and count these numbers within the point totals. The monthly points are assigned to members as system gifts, on the 1st of each month (the CRON scheduler must be enabled).'),
    'FIELDS' => $fields,
    'SUBMIT_ICON' => 'buttons__save',
    'SUBMIT_NAME' => 'Set points',
    'URL' => get_self_url(),
));

$form->evaluate_echo();

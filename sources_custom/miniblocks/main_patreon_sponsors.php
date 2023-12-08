<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

require_code('patreons');
$level = isset($map['level']) ? intval($map['level']) : 30;
$patreons = get_patreons_on_minimum_level($level);
$_patreons = array();
foreach ($patreons as $patron) {
    $_patreons[] = array(
        'NAME' => $patron['name'],
        'USERNAME' => $patron['username'],
        'MONTHLY' => strval($patron['monthly']),
    );
}

$tpl = do_template('BLOCK_MAIN_PATREON_SPONSORS', array('_GUID' => '8b7ed8319aa6ec0e6bc0e8b5e1fede4d', 'PATREONS' => $_patreons));
$tpl->evaluate_echo();

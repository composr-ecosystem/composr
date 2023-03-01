<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    group_points
 */

function get_group_points()
{
    if (!addon_installed('group_points')) {
        return [];
    }

    $group_points = $GLOBALS['SITE_DB']->query_select('group_points', ['*']);
    return list_to_map('p_group_id', $group_points);
}

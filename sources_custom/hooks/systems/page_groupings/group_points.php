<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    group_points
 */

/**
 * Hook class.
 */
class Hook_page_groupings_group_points
{
    /**
     * Run function for do_next_menu hooks. They find links to put on standard navigation menus of the system.
     *
     * @param  ?MEMBER $member_id Member ID to run as (null: current member)
     * @param  boolean $extensive_docs Whether to use extensive documentation tooltips, rather than short summaries
     * @return array List of tuple of links (page grouping, icon, do-next-style linking data), label, help (optional) and/or nulls
     */
    public function run(?int $member_id = null, bool $extensive_docs = false) : array
    {
        if (!addon_installed('group_points')) {
            return [];
        }

        return [
            ['setup', 'menu/rich_content/ecommerce/purchase', ['group_points', ['type' => 'browse'], get_page_zone('group_points', false, 'adminzone', 'minimodules')], make_string_tempcode('Usergroup point assignments')],
        ];
    }
}

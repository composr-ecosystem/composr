<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    comcode_html_safelist
 */

/**
 * Hook class.
 */
class Hook_page_groupings_comcode_safelist
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
        if (!addon_installed('comcode_html_safelist')) {
            return [];
        }

        return [
            ['security', 'menu/adminzone/setup/custom_comcode', ['comcode_safelist', [], get_comcode_zone('comcode_safelist', false, 'adminzone')], make_string_tempcode('Edit Comcode safelist')],
        ];
    }
}

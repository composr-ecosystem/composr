<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    static_export
 */

/**
 * Hook class.
 */
class Hook_page_groupings_static_export
{
    /**
     * Run function for do_next_menu hooks. They find links to put on standard navigation menus of the system.
     *
     * @param  ?MEMBER $member_id Member ID to run as (null: current member)
     * @param  boolean $extensive_docs Whether to use extensive documentation tooltips, rather than short summaries
     * @return array List of tuple of links (page grouping, icon, do-next-style linking data), label, help (optional) and/or nulls
     */
    public function run($member_id = null, $extensive_docs = false)
    {
        return array(
            array('tools', 'menu/_generic_admin/tool', array('static_export', array('utheme' => $GLOBALS['FORUM_DRIVER']->get_theme('')), get_page_zone('static_export', false, 'adminzone', 'minimodules')), make_string_tempcode('Export static site (tar)')),
            array('tools', 'menu/_generic_admin/tool', array('static_export', array('dir' => '1', 'utheme' => $GLOBALS['FORUM_DRIVER']->get_theme('')), get_page_zone('static_export', false, 'adminzone', 'minimodules')), make_string_tempcode('Export static site (exports/static, with mtimes)')),
        );
    }
}

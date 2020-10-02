<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    theme_debug
 */

/**
 * Hook class.
 */
class Hook_page_groupings_theme_debug
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
            array('site_meta', 'menu/_generic_admin/tool', array('theme_debug', array(), get_page_zone('theme_debug', false, 'adminzone', 'minimodules')), make_string_tempcode('Theme testing / fixup tools')),
            array('style', 'menu/_generic_admin/tool', array('fix_partial_themewizard_css', array(), get_page_zone('fix_partial_themewizard_css', false, 'adminzone', 'minimodules')), make_string_tempcode('Fixup themewizard themes')),
            array('style', 'menu/_generic_admin/tool', array('css_check', array(), get_page_zone('css_check', false, 'adminzone', 'minimodules')), make_string_tempcode('Look for unused CSS')),
        );
    }
}

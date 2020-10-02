<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_release_build
 */

/**
 * Hook class.
 */
class Hook_page_groupings_make_release
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
            array('tools', 'menu/_generic_admin/tool', array('plug_guid', array(), get_page_zone('plug_guid', false, 'adminzone', 'minimodules')), make_string_tempcode('Release tools: Plug in missing GUIDs')),
            array('tools', 'menu/_generic_admin/tool', array('make_release', array(), get_page_zone('make_release', false, 'adminzone', 'minimodules')), make_string_tempcode('Release tools: Make a Composr release')),
            array('tools', 'menu/_generic_admin/tool', array('push_bugfix', array(), get_page_zone('push_bugfix', false, 'adminzone', 'minimodules')), make_string_tempcode('Release tools: Push a Composr bugfix')),
            array('tools', 'menu/_generic_admin/tool', array('doc_index_build', array(), get_page_zone('doc_index_build', false, 'adminzone', 'minimodules')), make_string_tempcode('Doc tools: Make addon tutorial index')),
        );
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    addon_publish
 */

/**
 * Hook class.
 */
class Hook_page_groupings_addon_publish
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
            array('tools', 'menu/_generic_admin/tool', array('admin_generate_adhoc_upgrade', array(), get_page_zone('admin_generate_adhoc_upgrade', false, 'adminzone', 'minimodules')), make_string_tempcode('Release tools: Create ad hoc-upgrade-TAR/guidance')),
            array('tools', 'menu/_generic_admin/tool', array('build_addons', array(), get_page_zone('build_addons', false, 'adminzone', 'minimodules')), make_string_tempcode('Release tools: Build non-bundled addon TARs')),
            array('tools', 'menu/_generic_admin/tool', array('publish_addons_as_downloads', array(), get_page_zone('publish_addons_as_downloads', false, 'adminzone', 'minimodules')), make_string_tempcode('compo.sr: Publish non-bundled addons')),
        );
    }
}

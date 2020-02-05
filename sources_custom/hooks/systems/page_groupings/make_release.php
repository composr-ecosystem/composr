<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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
        if (!addon_installed('composr_release_build')) {
            return [];
        }

        return [
            ['tools', 'admin/tool', ['plug_guid', [], get_page_zone('plug_guid', false, 'adminzone', 'minimodules')], make_string_tempcode('Release tools: Plug in missing GUIDs')],
            ['tools', 'admin/tool', ['make_release', [], get_page_zone('make_release', false, 'adminzone', 'minimodules')], make_string_tempcode('Release tools: Make a Composr release')],
            ['tools', 'admin/tool', ['push_bugfix', [], get_page_zone('push_bugfix', false, 'adminzone', 'minimodules')], make_string_tempcode('Release tools: Push a Composr bugfix')],
            ['tools', 'admin/tool', ['doc_index_build', [], get_page_zone('doc_index_build', false, 'adminzone', 'minimodules')], make_string_tempcode('Doc tools: Make addon tutorial index')],
        ];
    }
}

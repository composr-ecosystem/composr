<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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
    public function run(?int $member_id = null, bool $extensive_docs = false) : array
    {
        if (!addon_installed('composr_release_build')) {
            return [];
        }

        require_lang('composr_release_build');

        return [
            ['tools', 'admin/tool', ['plug_guid', [], get_page_zone('plug_guid', false, 'adminzone', 'minimodules')], do_lang_tempcode('RELEASE_TOOLS_FIX_GUIDS')],
            ['tools', 'admin/tool', ['admin_make_release', [], get_page_zone('admin_make_release', false, 'adminzone', 'modules')], do_lang_tempcode('RELEASE_TOOLS_MAKE_RELEASE')],
            ['tools', 'admin/tool', ['push_bugfix', [], get_page_zone('push_bugfix', false, 'adminzone', 'minimodules')], do_lang_tempcode('RELEASE_TOOLS_PUSH_BUGFIX')],
            ['tools', 'admin/tool', ['doc_index_build', [], get_page_zone('doc_index_build', false, 'adminzone', 'minimodules')], do_lang_tempcode('DOC_TOOLS_ADDON_TUTORIAL_INDEX')],
        ];
    }
}

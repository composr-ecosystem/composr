<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
    public function run(?int $member_id = null, bool $extensive_docs = false) : array
    {
        if (!addon_installed('addon_publish')) {
            return [];
        }

        return [
            ['tools', 'admin/tool', ['admin_generate_adhoc_upgrade', [], get_page_zone('admin_generate_adhoc_upgrade', false, 'adminzone', 'minimodules')], make_string_tempcode('Release tools: Create ad hoc-upgrade-TAR/guidance')],
            ['tools', 'admin/tool', ['build_addons', [], get_page_zone('build_addons', false, 'adminzone', 'minimodules')], make_string_tempcode('Release tools: Build non-bundled addon TARs')],
            ['tools', 'admin/tool', ['publish_addons_as_downloads', [], get_page_zone('publish_addons_as_downloads', false, 'adminzone', 'minimodules')], make_string_tempcode('compo.sr: Publish non-bundled addons')],
        ];
    }
}

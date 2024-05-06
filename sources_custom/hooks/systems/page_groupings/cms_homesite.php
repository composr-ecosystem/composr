<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_page_groupings_cms_homesite
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
        if (!addon_installed('cms_homesite')) {
            return [];
        }

        return [
            ['tools', 'admin/tool', ['admin_cmsusers', [], get_module_zone('admin_cmsusers')], make_string_tempcode('composr.app: Table of Composr users')],
        ];
    }
}

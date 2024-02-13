<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    buildr
 */

/**
 * Hook class.
 */
class Hook_page_groupings_buildr
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
        if (!addon_installed('buildr')) {
            return [];
        }

        $zone = get_module_zone('buildr');
        if ($zone === null) {
            return []; // Zone not installed yet
        }

        return [
            ['rich_content', 'spare/world', ['buildr', [], $zone], do_lang_tempcode('buildr:BUILDR')],
        ];
    }
}

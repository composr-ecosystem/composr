<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    meta_toolkit
 */

/**
 * Hook class.
 */
class Hook_page_groupings_meta_toolkit
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
        if (!addon_installed('meta_toolkit')) {
            return [];
        }

        return [
            ['tools', 'admin/tool', ['sql_schema_generate', [], get_page_zone('sql_schema_generate', false, 'adminzone', 'minimodules')], make_string_tempcode('Doc build: Generate database schema')],
            ['tools', 'admin/tool', ['sql_schema_generate_by_addon', [], get_page_zone('sql_schema_generate_by_addon', false, 'adminzone', 'minimodules')], make_string_tempcode('Doc build: Generate database schema, by addon')],
            ['tools', 'admin/tool', ['sql_show_tables_by_addon', [], get_page_zone('sql_show_tables_by_addon', false, 'adminzone', 'minimodules')], make_string_tempcode('Doc build: Show database tables, by addon')],
            ['tools', 'admin/tool', ['sql_dump', [], get_page_zone('sql_dump', false, 'adminzone', 'minimodules')], make_string_tempcode('Backup tools: Create SQL dump (MySQL syntax)')],
            ['tools', 'admin/tool', ['tar_dump', [], get_page_zone('tar_dump', false, 'adminzone', 'minimodules')], make_string_tempcode('Backup tools: Create files dump (TAR file)')],
            ['tools', 'admin/tool', ['string_scan', [], get_page_zone('string_scan', false, 'adminzone', 'minimodules')], make_string_tempcode('Analyse admin/user language strings')],
        ];
    }
}

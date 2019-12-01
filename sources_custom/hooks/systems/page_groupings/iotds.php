<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Hook class.
 */
class Hook_page_groupings_iotds
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
        if (!addon_installed('iotds')) {
            return [];
        }

        return [
            ['cms', 'menu/rich_content/iotds', ['cms_iotds', ['type' => 'browse'], get_module_zone('cms_iotds')], do_lang_tempcode('ITEMS_HERE', do_lang_tempcode('iotds:IOTDS'), make_string_tempcode(escape_html(integer_format(intval($GLOBALS['SITE_DB']->query_select_value('iotd', 'COUNT(*)')))))), 'iotds:DOC_IOTDS'],
            ['rich_content', 'menu/rich_content/iotds', ['iotds', [], get_module_zone('iotds')], do_lang_tempcode('iotds:IOTDS')],
        ];
    }
}

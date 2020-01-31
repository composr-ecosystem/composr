<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_page_groupings_support_credits
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
            array('audit', 'menu/_generic_admin/tool', array('outstanding_credits', array(), get_page_zone('outstanding_credits', false, 'adminzone', 'minimodules')), do_lang_tempcode('customers:UNSPENT_SUPPORT_CREDITS')),
            array('tools', 'menu/_generic_admin/tool', array('admin_customers', array(), get_module_zone('admin_customers')), do_lang_tempcode('customers:CHARGE_CUSTOMER')),
        );
    }
}

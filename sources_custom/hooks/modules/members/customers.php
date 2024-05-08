<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_members_customers
{
    /**
     * Find member-related links to inject to details section of the about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting links for
     * @return array List of pairs: title to value
     */
    public function run(int $member_id) : array
    {
        if (!addon_installed('cms_homesite_support_credits')) {
            return [];
        }

        if (!has_actual_page_access(get_member(), 'admin_ecommerce', get_module_zone('admin_ecommerce'))) {
            return [];
        }

        require_lang('customers');
        return [
            ['views', do_lang_tempcode('GIVE_CREDITS'), build_url(['page' => 'admin_ecommerce_reports', 'type' => 'trigger', 'member_id' => $member_id], get_module_zone('admin_ecommerce_reports')), 'menu/rich_content/ecommerce/purchase'],
            ['views', do_lang_tempcode('CHARGE_CUSTOMER'), build_url(['page' => 'admin_customers', 'type' => 'charge', 'username' => $GLOBALS['FORUM_DRIVER']->get_username($member_id)], get_module_zone('admin_customers')), 'menu/adminzone/audit/ecommerce/transactions_log'],
        ];
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_notification_ecom_product_request_community_billboard extends Hook_notification__Staff
{
    /**
     * Get a list of all the notification codes this hook can handle.
     * (Addons can define hooks that handle whole sets of codes, so hooks are written so they can take wide authority).
     *
     * @return array List of codes (mapping between code names, and a pair: section and labelling for those codes)
     */
    public function list_handled_codes()
    {
        if (!addon_installed('community_billboard')) {
            return [];
        }

        $list = [];
        $list['ecom_request_community_billboard'] = [do_lang('ecommerce:ECOMMERCE'), do_lang('community_billboard:NOTIFICATION_TYPE_ecom_request_community_billboard')];
        return $list;
    }
}

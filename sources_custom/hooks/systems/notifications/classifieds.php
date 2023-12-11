<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    classified_ads
 */

/**
 * Hook class.
 */
class Hook_notification_classifieds extends Hook_Notification
{
    /**
     * Get a list of all the notification codes this hook can handle.
     * (Addons can define hooks that handle whole sets of codes, so hooks are written so they can take wide authority)
     *
     * @return array List of codes (mapping between code names, and a pair: section and labelling for those codes)
     */
    public function list_handled_codes()
    {
        if (!$GLOBALS['SITE_DB']->table_exists('classifieds_prices')) {
            return array();
        }

        $list = array();
        $catalogues = $GLOBALS['SITE_DB']->query_select('classifieds_prices', array('DISTINCT c_catalogue_name'), null, '', null, null, true);
        if (is_null($catalogues)) {
            return array();
        }
        foreach ($catalogues as $catalogue) {
            $list['classifieds__' . $catalogue['c_catalogue_name']] = array(do_lang('GENERAL'), do_lang('classifieds:NOTIFICATION_TYPE_classifieds'));
        }
        return $list;
    }

    /**
     * Get a list of members who have enabled this notification (i.e. have permission to AND have chosen to or are defaulted to).
     *
     * @param  ID_TEXT $notification_code Notification code
     * @param  ?SHORT_TEXT $category The category within the notification code (null: none)
     * @param  ?array $to_member_ids List of member IDs we are restricting to (null: no restriction). This effectively works as a intersection set operator against those who have enabled.
     * @param  integer $start Start position (for pagination)
     * @param  integer $max Maximum (for pagination)
     * @return array A pair: Map of members to their notification setting, and whether there may be more
     */
    public function list_members_who_have_enabled($notification_code, $category = null, $to_member_ids = null, $start = 0, $max = 300)
    {
        $members = $this->_all_members_who_have_enabled($notification_code, $category, $to_member_ids, $start, $max);
        $members = $this->_all_members_who_have_enabled_with_page_access($members, 'cms_catalogues', $notification_code, $category, $to_member_ids, $start, $max);

        return $members;
    }
}

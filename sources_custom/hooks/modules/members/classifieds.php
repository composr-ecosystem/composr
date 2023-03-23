<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    classified_ads
 */

/**
 * Hook class.
 */
class Hook_members_classifieds
{
    /**
     * Find member-related links to inject to details section of the about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting links for
     * @return array List of pairs: title to value
     */
    public function run(int $member_id) : array
    {
        if (!addon_installed('classified_ads')) {
            return [];
        }

        if (!has_actual_page_access(get_member(), 'classifieds', get_module_zone('classifieds'))) {
            return [];
        }

        require_lang('classifieds');

        $result = [];

        if (($member_id == get_member()) || (has_privilege(get_member(), 'assume_any_member'))) {
            $result[] = ['content', do_lang('CLASSIFIED_ADVERTS'), build_url(['page' => 'classifieds', 'type' => 'browse', 'member_id' => $member_id], get_module_zone('classifieds')), 'spare/classifieds'];
        }

        return $result;
    }
}

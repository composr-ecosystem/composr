<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    referrals
 */

/**
 * Hook class.
 */
class Hook_members_referrals
{
    /**
     * Find member-related links to inject to details section of the about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting links for
     * @return array List of pairs: title to value.
     */
    public function run($member_id)
    {
        if ((!has_zone_access(get_member(), 'adminzone')) && ($member_id !== get_member())) {
            return array();
        }

        if (!$GLOBALS['SITE_DB']->table_exists('referees_qualified_for')) {
            return array(); // Not installed somehow
        }

        require_code('referrals');
        require_lang('referrals');

        $keep = symbol_tempcode('KEEP');

        $ret = array();

        $ini_file = parse_ini_file(get_custom_file_base() . '/text_custom/referrals.txt', true);

        foreach ($ini_file as $ini_file_section_name => $ini_file_section) {
            if ($ini_file_section_name != 'global') {
                $scheme_name = $ini_file_section_name;
                $scheme = $ini_file_section;
                $scheme['name'] = $scheme_name;

                $scheme_title = isset($scheme['title']) ? $scheme['title'] : $ini_file_section_name;

                if (has_zone_access(get_member(), 'adminzone')) {
                    $ret[] = array(
                        'views',
                        do_lang_tempcode('MANUALLY_ADJUST_SCHEME_SETTINGS', escape_html($scheme_title)),
                        build_url(array('page' => 'admin_referrals', 'type' => 'adjust', 'scheme' => $scheme_name, 'member_id' => $member_id), get_module_zone('admin_referrals')),
                        'menu/referrals'
                    );
                }

                if (!referrer_is_qualified($scheme, $member_id)) {
                    continue;
                }

                $ret[] = array(
                    'audit',
                    make_string_tempcode(escape_html($scheme_title)),
                    find_script('referrer_report') . '?scheme=' . urlencode($scheme_name) . '&member_id=' . strval($member_id) . $keep->evaluate(),
                    'menu/referrals'
                );
            }
        }

        return $ret;
    }

    /**
     * Find member-related links to inject to tracking section of the about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting links for
     * @return array List of pairs: title to value.
     */
    public function get_tracking_details($member_id)
    {
        if ((!has_zone_access(get_member(), 'adminzone')) && ($member_id !== get_member())) {
            return array();
        }

        if (!$GLOBALS['SITE_DB']->table_exists('referees_qualified_for')) {
            return array(); // Not installed somehow
        }

        require_code('referrals');
        require_lang('referrals');

        $keep = symbol_tempcode('KEEP');

        $ret = array();

        $ini_file = parse_ini_file(get_custom_file_base() . '/text_custom/referrals.txt', true);

        foreach ($ini_file as $ini_file_section_name => $ini_file_section) {
            if ($ini_file_section_name != 'global') {
                $scheme_name = $ini_file_section_name;
                $scheme = $ini_file_section;
                $scheme['name'] = $scheme_name;

                $scheme_title = isset($scheme['title']) ? $scheme['title'] : $ini_file_section_name;

                $qualified = referrer_is_qualified($scheme, $member_id);
                if ($qualified) {
                    list($num_total_qualified_by_referrer, $num_total_by_referrer) = get_referral_scheme_stats_for($member_id, $scheme_name);
                    $scheme_text = do_lang_tempcode('MEMBER_SCHEME_SUMMARY_LINE', escape_html(integer_format($num_total_by_referrer)), escape_html(integer_format($num_total_qualified_by_referrer)));
                } else {
                    $scheme_text = do_lang_tempcode('MEMBER_SCHEME_SUMMARY_LINE_UNQUALIFIED');
                }

                $ret[do_lang('MEMBER_SCHEME_SUMMARY_LINE_HEADER', $scheme_title)] = $scheme_text;
            }
        }

        if (has_privilege(get_member(), 'member_maintenance')) {
            $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id);
            $referrer = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_invites', 'i_inviter', array('i_email_address' => $GLOBALS['FORUM_DRIVER']->get_member_email_address($member_id)));
            if (!is_null($referrer)) {
                $referrer_username = $GLOBALS['FORUM_DRIVER']->get_username($referrer);
                if (is_null($referrer_username)) {
                    $referrer_username = do_lang('DELETED');
                }
                $referrer_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($referrer);
                $test = $GLOBALS['SITE_DB']->query_select_value_if_there('referees_qualified_for', 'id', array('q_referee' => $member_id));
                $link = do_lang_tempcode(is_null($test) ? 'MEMBER_REFERRED_BY_NONQUALIFIED' : 'MEMBER_REFERRED_BY_QUALIFIED', escape_html($username), escape_html($referrer_username), escape_html($referrer_url));
                $ret[do_lang('TYPE_REFERRER')] = $link;
            } else {
                $ret[do_lang('TYPE_REFERRER')] = do_lang_tempcode('MEMBER_NOT_REFERRED', escape_html($username));
            }
        }

        return $ret;
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */
// activity_feed addon's implementation of activity syndication. Which in turn implements syndication hooks for further syndication (i.e. it's a syndication API built on top of a syndication API, as we don't want to provide lots of implementation code in Composr itself when no default use case is shipped).

/**
 * Hook class.
 */
class Hook_activities_activities
{
    /**
     * Syndicate human-intended descriptions of activities performed to the internal wall, and external listeners.
     *
     * @param  string $a_language_string_code Language string ID
     * @param  string $a_label_1 Label 1 (given as a parameter to the language string ID)
     * @param  string $a_label_2 Label 2 (given as a parameter to the language string ID)
     * @param  string $a_label_3 Label 3 (given as a parameter to the language string ID)
     * @param  string $a_page_link_1 Page-link 1
     * @param  string $a_page_link_2 Page-link 2
     * @param  string $a_page_link_3 Page-link 3
     * @param  string $a_addon Addon that caused the event
     * @param  BINARY $a_is_public Whether this post should be public or friends-only
     * @param  ?MEMBER $a_member_id Member being written for (null: current member)
     * @param  boolean $sitewide_too Whether to push this out as a site event if user requested
     * @param  ?MEMBER $also_involving Member also 'intimately' involved, such as a content submitter who is a friend (null: none)
     * @return ?AUTO_LINK ID of the row in the activities table (null: N/A)
     */
    public function syndicate_described_activity($a_language_string_code = '', $a_label_1 = '', $a_label_2 = '', $a_label_3 = '', $a_page_link_1 = '', $a_page_link_2 = '', $a_page_link_3 = '', $a_addon = '', $a_is_public = 1, $a_member_id = null, $sitewide_too = false, $also_involving = null)
    {
        require_code('activities_submission');

        cms_profile_start_for('syndicate_described_activity');
        $ret = activities_addon_syndicate_described_activity($a_language_string_code, $a_label_1, $a_label_2, $a_label_3, $a_page_link_1, $a_page_link_2, $a_page_link_3, $a_addon, $a_is_public, $a_member_id, $sitewide_too, $also_involving);
        cms_profile_end_for('syndicate_described_activity', is_null($ret) ? '' : ('#' . strval($ret)));
        return $ret;
    }

    /**
     * Detect whether we have external site-wide syndication support somewhere.
     *
     * @return boolean Whether we do
     */
    public function has_external_site_wide_syndication()
    {
        $dests = find_all_hooks('systems', 'syndication');
        foreach (array_keys($dests) as $hook) {
            require_code('hooks/systems/syndication/' . $hook);
            $ob = object_factory('Hook_syndication_' . $hook);
            if (($ob->is_available()) && ($ob->auth_is_set_site())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get syndication field UI.
     *
     * @param  string $content_type The content type this is for
     * @return Tempcode Syndication fields (or empty)
     */
    public function get_syndication_option_fields($content_type)
    {
        $fields = new Tempcode();
        require_code('activities');
        if ((has_privilege(get_member(), 'syndicate_site_activity')) && (has_external_site_wide_syndication())) {
            require_lang('activities');

            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => 'ded75eeb85f5bb8a6c1b6da597555750', 'TITLE' => do_lang_tempcode('SYNDICATION'))));
            $default = in_array($content_type, array_map('trim', explode(',', get_option('syndicate_site_activity_default'))));
            $fields->attach(form_input_tick(do_lang_tempcode('SYNDICATE_THIS'), do_lang_tempcode('DESCRIPTION_SYNDICATE_THIS'), 'syndicate_this', $default));
        }
        return $fields;
    }
}

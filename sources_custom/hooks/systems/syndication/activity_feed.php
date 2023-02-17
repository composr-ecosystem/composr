<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * Hook class.
 */
class Hook_syndication_activity_feed
{
    /**
     * Syndicate human-intended descriptions of activities performed to the internal feed, and external listeners.
     *
     * @param  string $language_string_code Language string codename
     * @param  string $label_1 Label 1 (given as a parameter to the language string codename)
     * @param  string $label_2 Label 2 (given as a parameter to the language string codename)
     * @param  string $label_3 Label 3 (given as a parameter to the language string codename)
     * @param  string $page_link_1 Page-link 1
     * @param  string $page_link_2 Page-link 2
     * @param  string $page_link_3 Page-link 3
     * @param  string $addon Addon that caused the event
     * @param  BINARY $is_public Whether this post should be public or friends-only
     * @param  ?MEMBER $member_id Member being written for (null: current member)
     * @param  boolean $sitewide_too Whether to push this out as a site event if user requested
     * @param  ?MEMBER $also_involving Member also 'intimately' involved, such as a content submitter who is a friend (null: none)
     * @param  array $syndication_context A serialisable representation of data set via get_syndication_option_fields
     * @return ?AUTO_LINK ID of the row in the activities table (null: N/A)
     */
    public function syndicate_described_activity(string $language_string_code, string $label_1, string $label_2, string $label_3, string $page_link_1, string $page_link_2, string $page_link_3, string $addon, int $is_public, ?int $member_id, bool $sitewide_too, ?int $also_involving, array $syndication_context) : ?int
    {
        if (!addon_installed('activity_feed')) {
            return null;
        }

        if ($member_id === null) {
            $member_id = get_member();
        }

        if (($syndication_context['syndicate_site_activity']) || (!$syndication_context['syndicate_activity'])) {
            $sitewide_too = false;
        }

        require_code('activity_feed_submission');

        cms_profile_start_for('syndicate_described_activity');
        $ret = activity_feed_syndicate_described_activity($language_string_code, $label_1, $label_2, $label_3, $page_link_1, $page_link_2, $page_link_3, $addon, $is_public, $member_id, $sitewide_too, $also_involving);
        cms_profile_end_for('syndicate_described_activity', ($ret === null) ? '' : ('#' . strval($ret)));
        return $ret;
    }

    /**
     * Detect whether we have external site-wide syndication support somewhere.
     *
     * @param  ?string $content_type The content type this is for (null: look for 'activity_feed' only)
     * @param  boolean $is_edit If these options are for an edit
     * @return boolean Whether we do
     */
    protected function has_external_site_wide_syndication(?string $content_type, bool $is_edit) : bool
    {
        if (!addon_installed('activity_feed')) {
            return false;
        }

        if (addon_installed('hybridauth')) {
            if ($content_type !== null) {
                require_code('content');
                $addon_name = convert_composr_type_codes('content_type', $content_type, 'addon_name');
            }

            require_code('hybridauth_admin');
            $providers = find_all_hybridauth_admin_providers_matching($is_edit ? HYBRIDAUTH__ADVANCEDAPI_UPDATE_ATOMS : HYBRIDAUTH__ADVANCEDAPI_INSERT_ATOMS);
            foreach ($providers as $provider => $info) {
                $syndicate_from = explode(',', $info['syndicate_from']);
                if ($content_type !== null) {
                    if ((in_array($addon_name, $syndicate_from)) || (in_array($content_type, $syndicate_from))) {
                        return true;
                    }
                } else {
                    if (in_array('activity_feed', $syndicate_from)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Get syndication field UI.
     *
     * @param  ?string $content_type The content type this is for (null: none)
     * @param  boolean $is_edit If these options are for an edit
     * @return Tempcode Syndication fields (or empty)
     */
    public function get_syndication_option_fields(?string $content_type, bool $is_edit) : object
    {
        if (!addon_installed('activity_feed')) {
            return new Tempcode();
        }

        $fields = new Tempcode();
        if ((has_privilege(get_member(), 'syndicate_site_activity')) && ($this->has_external_site_wide_syndication(null, $is_edit))) {
            require_lang('activity_feed');

            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => 'ded75eeb85f5bb8a6c1b6da597555750', 'TITLE' => do_lang_tempcode('SYNDICATION')]));
            $by_default = in_array($content_type, array_map('trim', explode(',', get_option('syndicate_site_activity_default'))));

            if ((addon_installed('hybridauth')) && ($by_default) && ($content_type !== null)) {
                // We will cooperate with the hybridauth addon, do not want duplication

                if ($this->has_external_site_wide_syndication($content_type, $is_edit)) {
                    $by_default = false;
                }
            }

            $fields->attach(form_input_tick(do_lang_tempcode('SYNDICATE_TO_ACTIVITY_FEED'), do_lang_tempcode('DESCRIPTION_SYNDICATE_TO_ACTIVITY_FEED'), 'syndicate_activity', $by_default));
        }
        return $fields;
    }

    /**
     * Get syndication field settings, and other context we may need to serialise.
     *
     * @param  ?string $content_type The content type this is for (null: none)
     * @return array Syndication field context
     */
    public function read_get_syndication_option_fields(?string $content_type) : array
    {
        if (!addon_installed('hybridauth')) {
            return [];
        }

        return [
            'syndicate_activity' => post_param_integer('syndicate_activity', 0) == 1,
            'syndicate_site_activity' => has_privilege(get_member(), 'syndicate_site_activity'),
        ];
    }
}

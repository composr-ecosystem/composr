<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    giftr
 */

/**
 * Hook class.
 */
class Hook_members_gifts
{
    /**
     * Find member-related links to inject to details section of the about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting links for
     * @return array List of pairs: title to value
     */
    public function run(int $member_id) : array
    {
        if (!addon_installed('giftr')) {
            return [];
        }

        require_lang('giftr');

        if (is_guest()) {
            return [];
        }
        if (!has_actual_page_access(get_member(), 'purchase', get_module_zone('purchase'))) {
            return [];
        }
        if ($member_id == get_member()) {
            return [];
        }

        return [['contact', do_lang_tempcode('GIFT_GIFT'), build_url(['page' => 'purchase', 'type' => 'browse', 'category' => 'giftr', 'username' => $GLOBALS['FORUM_DRIVER']->get_username($member_id)], get_module_zone('purchase')), 'spare/gifts']];
    }

    /**
     * Get sections to inject to about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting sections for
     * @return array List of sections. Each tuple is Tempcode.
     */
    public function get_sections(int $member_id) : array
    {
        if (!addon_installed('giftr')) {
            return [];
        }

        require_lang('giftr');
        $rows = $GLOBALS['SITE_DB']->query_select('members_gifts', ['*'], ['to_member_id' => $member_id]);
        if ($rows === null) {
            return [];
        }

        $gifts = [];
        foreach ($rows as $gift) {
            $gift_rows = $GLOBALS['SITE_DB']->query_select('giftr', ['*'], ['id' => $gift['gift_id']], '', 1);

            if (array_key_exists(0, $gift_rows)) {
                $gift_row = $gift_rows[0];

                if ($gift['is_anonymous'] == 0) {
                    $sender_displayname = $GLOBALS['FORUM_DRIVER']->get_username($gift['from_member_id'], true);
                    $sender_username = $GLOBALS['FORUM_DRIVER']->get_username($gift['from_member_id']);
                    $sender_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($gift['from_member_id'], true);
                    $gift_explanation = do_lang_tempcode('GIFT_EXPLANATION', escape_html($sender_displayname), escape_html($gift_row['name']), [escape_html(is_object($sender_url) ? $sender_url->evaluate() : $sender_url), escape_html($sender_username)]);
                } else {
                    $gift_explanation = do_lang_tempcode('GIFT_EXPLANATION_ANONYMOUS', escape_html($gift_row['name']));
                }

                $image_url = '';
                if (is_file(get_custom_file_base() . '/' . urldecode($gift_row['image']))) {
                    $image_url = get_custom_base_url() . '/' . $gift_row['image'];
                }

                $gifts[] = [
                    'GIFT_EXPLANATION' => $gift_explanation,
                    'IMAGE_URL' => $image_url,
                ];
            }
        }

        $gifts_block = do_template('CNS_MEMBER_SCREEN_GIFTS_WRAP', ['_GUID' => 'fd4b5344b3b16cdf129e49bae903cbb2', 'GIFTS' => $gifts]);
        $gifts_block->handle_symbol_preprocessing();
        return [$gifts_block];
    }
}

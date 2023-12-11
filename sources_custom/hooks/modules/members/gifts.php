<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
     * @return array List of pairs: title to value.
     */
    public function run($member_id)
    {
        require_lang('giftr');

        if (is_guest()) {
            return array();
        }
        if (!has_actual_page_access(get_member(), 'pointstore', get_module_zone('pointstore'))) {
            return array();
        }
        if ($member_id == get_member()) {
            return array();
        }

        return array(array('contact', do_lang_tempcode('GIFT_GIFT'), build_url(array('page' => 'pointstore', 'type' => 'action', 'id' => 'giftr', 'username' => $GLOBALS['FORUM_DRIVER']->get_username($member_id)), get_module_zone('pointstore')), 'menu/giftr'));
    }

    /**
     * Get sections to inject to about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting sections for
     * @return array List of sections. Each tuple is Tempcode.
     */
    public function get_sections($member_id)
    {
        require_lang('giftr');
        $rows = $GLOBALS['SITE_DB']->query_select('members_gifts', array('*'), array('to_member_id' => $member_id), '', null, 0, true);
        if (is_null($rows)) {
            return array();
        }

        $gifts = array();
        foreach ($rows as $gift) {
            $gift_rows = $GLOBALS['SITE_DB']->query_select('giftr', array('*'), array('id' => $gift['gift_id']), '', 1);

            if (array_key_exists(0, $gift_rows)) {
                $gift_row = $gift_rows[0];

                if ($gift['is_anonymous'] == 0) {
                    $sender_displayname = $GLOBALS['FORUM_DRIVER']->get_username($gift['from_member_id'], true);
                    $sender_username = $GLOBALS['FORUM_DRIVER']->get_username($gift['from_member_id']);
                    $sender_url = $GLOBALS['FORUM_DRIVER']->member_profile_url($gift['from_member_id'], true, true);
                    $gift_explanation = do_lang_tempcode('GIFT_EXPLANATION', escape_html($sender_displayname), escape_html($gift_row['name']), array(escape_html(is_object($sender_url) ? $sender_url->evaluate() : $sender_url), escape_html($sender_username)));
                } else {
                    $gift_explanation = do_lang_tempcode('GIFT_EXPLANATION_ANONYMOUS', escape_html($gift_row['name']));
                }

                $image_url = '';
                if (is_file(get_custom_file_base() . '/' . urldecode($gift_row['image']))) {
                    $image_url = get_custom_base_url() . '/' . $gift_row['image'];
                }

                $gifts[] = array(
                    'GIFT_EXPLANATION' => $gift_explanation,
                    'IMAGE_URL' => $image_url,
                );
            }
        }

        $gifts_block = do_template('CNS_MEMBER_SCREEN_GIFTS_WRAP', array('_GUID' => 'fd4b5344b3b16cdf129e49bae903cbb2', 'GIFTS' => $gifts));
        $gifts_block->handle_symbol_preprocessing();
        return array($gifts_block);
    }
}

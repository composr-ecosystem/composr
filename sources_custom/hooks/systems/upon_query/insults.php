<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    bantr
 */

/**
 * Hook class.
 */
class Hook_upon_query_insults
{
    public function run_post($ob, $query, $max, $start, $fail_ok, $get_insert_id, $ret)
    {
        if ($query[0] == 'S') {
            return;
        }

        if (get_forum_type() != 'cns') {
            return;
        }

        if (!isset($GLOBALS['FORUM_DB'])) {
            return;
        }
        if ($GLOBALS['IN_MINIKERNEL_VERSION']) {
            return;
        }
        if ($GLOBALS['BOOTSTRAPPING']) {
            return;
        }

        if (get_mass_import_mode()) {
            return;
        }

        //if ((strpos($query, $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members') !== false) && (strpos($query, 'BY RAND') == false)) // to test without registration

        if (strpos($query, 'INTO ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_posts ') !== false) {
            if (!addon_installed('bantr')) {
                return;
            }

            if (!addon_installed('points')) {
                return;
            }

            require_code('permissions');

            load_user_stuff();
            $GLOBALS['FORUM_DRIVER']->forum_layer_initialise();

            global $FORCE_INVISIBLE_GUEST, $MEMBER_CACHED;
            $FORCE_INVISIBLE_GUEST = false;
            $MEMBER_CACHED = null;

            $poster_id = get_member();
            $post = post_param_string('post', '');

            $posted_data = $GLOBALS['FORUM_DB']->query_select('f_posts', ['*'], ['id' => $ret], '', 1);

            $topic_id = (isset($posted_data[0]['p_topic_id']) && $posted_data[0]['p_topic_id'] > 0) ? $posted_data[0]['p_topic_id'] : 0;

            $first_post_data = $GLOBALS['FORUM_DB']->query_select('f_posts', ['*'], ['p_topic_id' => $topic_id], 'ORDER BY p_time,id', 1);

            $_first_post = $first_post_data[0]['p_post'];
            if ($_first_post === 0 || $_first_post === '') { // Still being created
                return;
            }

            $first_post = get_translated_text($_first_post, $GLOBALS['FORUM_DB']);

            $_insult = explode('[b]', $first_post);
            $insult = (isset($_insult[1]) && strlen($_insult[1]) > 0) ? $_insult[1] : '';
            $_insult = explode('[/b]', $insult);
            $insult = (isset($_insult[0]) && strlen($_insult[0]) > 0) ? $_insult[0] : '';

            if ($insult != '') {
                $get_reply = '';
                if (is_file(get_file_base() . '/text_custom/' . user_lang() . '/insults.txt')) {
                    $insults = cms_file_safe(get_file_base() . '/text_custom/' . user_lang() . '/insults.txt');
                    $insults_array = [];
                    foreach ($insults as $insult_item) {
                        $x = explode('=', $insult_item);
                        if (isset($x[0]) && strlen($x[0]) > 0 && isset($x[1]) && strlen($x[1]) > 0) {
                            $insults_array[trim($x[0])] = trim($x[1]);
                        }
                    }

                    $get_reply = isset($insults_array[$insult]) ? $insults_array[$insult] : '';
                }

                if ($get_reply != '') {
                    // Get PT
                    $pt = $GLOBALS['FORUM_DB']->query_select('f_topics', ['*'], ['id' => $topic_id], '', 1);

                    $to_member = (isset($pt[0]['t_pt_to']) && $pt[0]['t_pt_to'] > 0) ? $pt[0]['t_pt_to'] : 0;

                    if ($to_member == $poster_id) {
                        // Start comparing insult reply and the post
                        if (levenshtein(trim(cms_mb_strtolower($post)), trim(cms_mb_strtolower($get_reply))) < intval(0.1 * strlen($get_reply))) {
                            $_insult_points = get_option('insult_points', true);
                            $insult_points = (isset($_insult_points) && intval($_insult_points) > 0) ? intval($_insult_points) : 10;

                            // Credit points
                            require_code('points2');
                            require_lang('insults');

                            $rows = $GLOBALS['SITE_DB']->query('SELECT g.id FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'points_ledger g WHERE ' . $GLOBALS['SITE_DB']->translate_field_ref('reason') . ' LIKE \'' . db_encode_like('%' . $insult . '%') . '\' AND g.recipient_id=' . strval($poster_id), 1, 0, false, false, ['reason' => 'SHORT_TRANS']);

                            // If the member hasn't got their reward yet, give it to them
                            if (!isset($rows[0]['id']) && (intval($insult_points) > 0)) {
                                points_credit_member($poster_id, do_lang('SUCCESSFULLY_SUGGESTED_COMEBACK') . ' (' . $insult . ')', intval($insult_points), 0, null);

                                require_code('cns_posts_action');
                                $congratulations_post = do_lang('CONGRATULATIONS_WON'); // Congratulations that is the correct response

                                cns_make_post($topic_id, '', $congratulations_post, 0, true, 1, 0, do_lang('SYSTEM'), null, null, $GLOBALS['FORUM_DRIVER']->get_guest_id(), $poster_id, null, null, false, true, null, true, '', null, false, true, true);
                            }
                        }
                    }
                }
            }
        }
    }
}

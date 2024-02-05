<?php /*

Composr
Copyright (c) ocProducts, 2004-2023

See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    karma
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_karma
{
    public function compile_template(&$data, $template_name, $theme, $lang, $suffix, $directory)
    {
        if (!addon_installed('karma') || (get_forum_type() != 'cns')) {
            return;
        }

        if (strpos($directory, 'templates_custom') !== false) {
            return;
        }

        if ($suffix != '.tpl') {
            return;
        }

        switch ($template_name) {
            case 'CNS_MEMBER_PROFILE_ABOUT':
                $data = override_str_replace_exactly(
                    "\t\t" . '{+START,IF_NON_EMPTY,{AVATAR_URL}}' . "\n\t\t\t" . '<div class="cns-member-profile-avatar">' . "\n\t\t\t\t" . '<img src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" alt="{!AVATAR}" />' . "\n\t\t\t" . '</div>' . "\n\t\t" . '{+END}',
                    "<ditto>{\$BLOCK,block=main_karma_graph,param={MEMBER_ID}}",
                    $data
                );
                break;

            case 'CNS_TOPIC_POST':
                $data = override_str_replace_exactly(
                    '{POST_AVATAR}',
                    "<ditto>{\$BLOCK,block=main_karma_graph,param={POSTER_ID}}",
                    $data
                );
                break;
        }
    }

    public function call_included_code($path, $codename, &$code)
    {
        if (!addon_installed('karma') || (get_forum_type() != 'cns')) {
            return;
        }

        require_code('override_api');

        // Top Content / Awards karma (we do not support removing karma for this)
        if ($codename == 'awards' && (strpos($path, 'sources_custom/') === false)) {
            if ($code === null) {
                $code = clean_php_file_for_eval(file_get_contents($path));
            }

            insert_code_before__by_command(
                $code,
                "give_award",
                "\$GLOBALS['SITE_DB']->query_insert('award_archive',",
                "if (addon_installed('karma')) {
                    require_code('karma2');
                    add_karma('good', null, \$member_id, intval(get_option('karma_awards')), 'award', strval(\$award_id), \$content_id);
                }
                "
            );
        }

        // Feedback karma
        if (($codename == 'feedback') && (strpos($path, 'sources_custom/') === false)) {
            if ($code === null) {
                $code = clean_php_file_for_eval(file_get_contents($path));
            }

            insert_code_before__by_command(
                $code,
                "actualise_specific_rating",
                "// Top rating / liked",
                "// Karma
                if (addon_installed('karma')) {
                    require_code('karma');
                    require_code('karma2');

                    \$karma_likes = floatval(get_option('karma_likes'));
                    \$karma_dislikes = floatval(get_option('karma_dislikes'));
                    \$influence = get_karmic_influence(\$member_id);

                    // Undo previous rating
                    if (\$already_rated) {
                        reverse_karma(null, \$member_id, null, \$content_type . ':' . \$type, \$content_id);
                    }

                    if ((\$rating !== null) && (\$submitter !== null) && (!is_guest(\$submitter))) {
                        if (\$rating <= 2) {
                            add_karma('bad', \$member_id, \$submitter, intval(\$karma_dislikes * \$influence), 'feedback ' . \$type, \$content_type . ':' . \$type, \$content_id);
                        } else if (\$rating <= 4) {
                            add_karma('bad', \$member_id, \$submitter, intval((\$karma_dislikes * \$influence) / 2.0), 'feedback ' . \$type, \$content_type . ':' . \$type, \$content_id);
                        } else if (\$rating >= 9) {
                            add_karma('good', \$member_id, \$submitter, intval(\$karma_likes * \$influence), 'feedback ' . \$type, \$content_type . ':' . \$type, \$content_id);
                        } else if (\$rating >= 7) {
                            add_karma('good', \$member_id, \$submitter, intval((\$karma_likes * \$influence) / 2.0), 'feedback ' . \$type, \$content_type . ':' . \$type, \$content_id);
                        }
                    }
                }
                "
            );
        }

        // Topic poll voting karma
        if (($codename == 'cns_polls_action2') && (strpos($path, 'sources_custom/') === false)) {
            if ($code === null) {
                $code = clean_php_file_for_eval(file_get_contents($path));
            }

            // When erasing all votes on a poll, also reverse all karma.
            insert_code_after__by_command(
                $code,
                "cns_edit_poll",
                "if (\$erase_votes) {",
                "// Reverse all associated karma
                if (addon_installed('karma')) {
                    require_code('karma2');
                    reverse_karma(null, null, null, 'topic_poll', strval(\$poll_id));
                }
                "
            );

            // Reverse karma when a poll is deleted.
            insert_code_after__by_command(
                $code,
                "cns_delete_poll",
                "\$GLOBALS['FORUM_DB']->query_delete('f_poll_votes', ['pv_poll_id' => \$poll_id]);",
                "// Reverse all associated karma
                if (addon_installed('karma')) {
                    require_code('karma2');
                    reverse_karma(null, null, null, 'topic_poll', strval(\$poll_id));
                }
                "
            );

            // Add karma when voting
            insert_code_before__by_command(
                $code,
                "cns_vote_in_poll",
                "// Award points",
                "// Award karma
                if (addon_installed('karma')) {
                    require_code('karma2');
                    \$karma_to_award = intval(get_option('karma_voting'));
                    if (\$karma_to_award > 0) {
                        add_karma('good', null, \$member_id, \$karma_to_award, 'Voted on topic poll', 'topic_poll', strval(\$poll_id));
                    }
                }
                "
            );

            // Reverse karma when a member revokes their vote
            insert_code_before__by_command(
                $code,
                "cns_revoke_vote_in_poll",
                "// Reverse points",
                "// Reverse karma
                if (addon_installed('karma')) {
                    require_code('karma2');
                    reverse_karma(null, null, \$member_id, 'topic_poll', strval(\$poll['id']));
                }
                "
            );
        }

        // Website poll voting karma (web polls do not support vote reversals, and deleting polls do not delete the votes)
        if (($codename == 'polls') && (strpos($path, 'sources_custom/') === false)) {
            if ($code === null) {
                $code = clean_php_file_for_eval(file_get_contents($path));
            }

            // Add karma when voting
            insert_code_after__by_command(
                $code,
                "vote_in_poll",
                "if (may_vote_in_poll(\$poll_id, \$member_id, \$ip)) {",
                "// Award karma
                if (addon_installed('karma')) {
                    require_code('karma2');
                    \$karma_to_award = intval(get_option('karma_voting'));
                    if (\$karma_to_award > 0) {
                        add_karma('good', null, \$member_id, \$karma_to_award, 'Voted on website poll', 'poll', strval(\$poll_id));
                    }
                }
                "
            );
        }

        // giftr karma
        if ($codename == 'hooks/systems/ecommerce/giftr') {
            if ($code === null) {
                $code = clean_php_file_for_eval(file_get_contents($path));
            }

            // Add karma when receiving a gift
            insert_code_before__by_command(
                $code,
                "actualiser",
                "// Send notification to recipient",
                "// Award karma
                if (addon_installed('karma')) {
                    require_code('karma');
                    require_code('karma2');
                    \$karma_multiplier = intval(get_option('karma_giftr'));
                    if (\$karma_multiplier > 0) {
                        \$influence = get_karmic_influence(\$from_member_id);
                        add_karma('good', null, \$to_member_id, intval(\$karma_multiplier * \$influence), 'Received a gift', 'giftr', strval(\$gift_id));
                    }
                }
                "
            );
        }
    }
}

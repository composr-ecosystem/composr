<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Find what forums a member may access, as a list of IDs.
 *
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @return array List of forum IDs
 */
function get_allowed_forum_ids(?int $member_id = null) : array
{
    if ($member_id === null) {
        $member_id = get_member();
    }

    static $cache = [];
    if (isset($cache[$member_id])) {
        return $cache[$member_id];
    }

    $forums = $GLOBALS['FORUM_DB']->query_select('f_forums', ['id']);
    $ids = [];
    foreach ($forums as $forum) {
        if (has_category_access($member_id, 'forums', strval($forum['id']))) {
            $ids[] = $forum['id'];
        }
    }
    $cache[$member_id] = $ids;
    return $ids;
}

/**
 * Find what forums a member may access, as SQL.
 *
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @return string SQL, for use within an IN clause
 */
function get_allowed_forum_sql(?int $member_id = null) : string
{
    return implode(',', array_map('strval', get_allowed_forum_ids($member_id)));
}

/**
 * Find how we can do actions in a forum.
 *
 * @param  array $forum_details Forum details
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @return array Details of capabilities
 */
function action_assessment_forum(array $forum_details, ?int $member_id = null) : array
{
    if ($member_id === null) {
        $member_id = get_member();
    }

    $can_post = (cns_may_post_topic($forum_details['forum_id'], $member_id)) && ($forum_details['f_redirection'] == '');
    $can_upload = (cns_get_member_best_group_property($member_id, 'max_attachments_per_post') > 0);

    $ret = [
        'can_post' => $can_post,
        'can_upload' => $can_upload,
    ];
    return $ret;
}

/**
 * Find how we can moderate a topic.
 *
 * @param  array $topic_details Topic details
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  integer $behaviour_modifiers A bitmask of RENDER_TOPIC_* settings
 * @return array Details of capabilities
 */
function moderation_assessment_topic(array $topic_details, ?int $member_id = null, int $behaviour_modifiers = 0) : array
{
    if ($member_id === null) {
        $member_id = get_member();
    }

    $ret = [
        'can_ban' => can_ban_member($member_id),
        'is_ban' => $GLOBALS['FORUM_DRIVER']->is_banned($topic_details['t_cache_first_member_id']),
        'can_delete' => can_delete('topic', $topic_details['topic_id'], $member_id, $topic_details),
        'is_deleted' => false,
        'can_approve' => can_approve('topic', $topic_details['topic_id'], $member_id, $topic_details),
        'is_approved' => is_approved('topic', $topic_details['topic_id'], $topic_details),
        'can_stick' => can_stick_topic($topic_details['topic_id'], $member_id, $topic_details),
        'is_sticky' => is_sticky_topic($topic_details['topic_id'], $topic_details),
        'can_close' => can_close_topic($topic_details['topic_id'], $member_id, $topic_details),
        'can_rename' => can_rename_topic($topic_details['topic_id'], $member_id, $topic_details),
        'can_move' => can_move('topic', $topic_details['topic_id'], $member_id, $topic_details),
    ];

    if (($behaviour_modifiers & RENDER_TOPIC_DEEP_PERMISSIONS) != 0) {
        $ret += [
            'can_merge' => can_merge_topics($topic_details['topic_id'], null, $member_id, $topic_details),
            'can_merge_post' => can_merge_posts($topic_details['topic_id'], $member_id),
        ];
    }

    return $ret;
}

/**
 * Find how we can moderate a post.
 *
 * @param  array $post_details Post details
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  integer $behaviour_modifiers A bitmask of RENDER_POST_* settings
 * @return array Details of capabilities
 */
function moderation_assessment_post(array $post_details, ?int $member_id = null, int $behaviour_modifiers = 0) : array
{
    if ($member_id === null) {
        $member_id = get_member();
    }

    return [
        'can_ban' => can_ban_member($member_id),
        'is_ban' => $GLOBALS['FORUM_DRIVER']->is_banned($post_details['p_poster']),
        'can_edit' => can_edit('post', $post_details['post_id'], $member_id, $post_details),
        'can_delete' => can_delete('post', $post_details['post_id'], $member_id, $post_details),
        'is_deleted' => false,
        'can_approve' => can_approve('post', $post_details['post_id'], $member_id, $post_details),
        'is_approved' => is_approved('post', $post_details['post_id'], $post_details),
        'can_move' => can_move('post', $post_details['post_id'], $member_id, $post_details),
    ];
}

/**
 * Whether a member can reply to a topic.
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $topic_details Topic row (null: lookup)
 * @return boolean Whether they can
 */
function can_reply_to_topic(int $topic_id, ?int $member_id = null, ?array $topic_details = null) : bool
{
    if ($member_id === null) {
        $member_id = get_member();
    }

    if ($topic_details === null) {
        $_topic_details = $GLOBALS['FORUM_DB']->query_select('f_topics t', ['*', 't.id AS topic_id'], ['t.id' => $topic_id], '', 1);
        if (!isset($_topic_details[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'topic'));
        }
        $topic_details = $_topic_details[0];
    }

    return cns_may_post_in_topic($topic_details['t_forum_id'], $topic_id, $topic_details['t_cache_last_member_id'], $topic_details['t_is_open'] == 0, $member_id);
}

/**
 * Whether editing is allowed.
 *
 * @param  string $type Resource type
 * @set topic post
 * @param  AUTO_LINK $id Resource ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $details Resource row (null: lookup)
 * @return boolean Whether they can
 */
function can_edit(string $type, int $id, ?int $member_id = null, ?array $details = null) : bool
{
    switch ($type) {
        case 'topic':
            if ($details === null) {
                $_topic_details = $GLOBALS['FORUM_DB']->query_select('f_topics t', ['*', 't.id AS topic_id'], ['t.id' => $id], '', 1);
                if (!isset($_topic_details[0])) {
                    warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'topic'));
                }
                $topic_details = $_topic_details[0];
            } else {
                $topic_details = $details;
            }
            return cns_may_edit_topics_by($topic_details['t_forum_id'], $member_id, $topic_details['t_cache_first_member_id']);

        case 'post':
            if ($details === null) {
                $_post_details = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_topics t ON t.id=p.p_topic_id', ['*', 'p.id AS post_id'], ['p.id' => $id], '', 1);
                if (!isset($_post_details[0])) {
                    warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
                }
                $post_details = $_post_details[0];
            } else {
                $post_details = $details;
            }
            return cns_may_edit_post_by($id, $post_details['p_time'], $post_details['p_poster'], $post_details['p_cache_forum_id'], $member_id, $post_details['t_is_open'] == 0);
    }

    return false;
}

/**
 * Whether deleting is allowed.
 *
 * @param  string $type Resource type
 * @set topic post
 * @param  AUTO_LINK $id Resource ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $details Resource row (null: lookup)
 * @return boolean Whether they can
 */
function can_delete(string $type, int $id, ?int $member_id = null, ?array $details = null) : bool
{
    switch ($type) {
        case 'topic':
            if ($details === null) {
                $_topic_details = $GLOBALS['FORUM_DB']->query_select('f_topics t', ['t.*', 't.id AS topic_id'], ['t.id' => $id], '', 1);
                if (!isset($_topic_details[0])) {
                    warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'topic'));
                }
                $topic_details = $_topic_details[0];
            } else {
                $topic_details = $details;
            }
            return cns_may_delete_topics_by($topic_details['t_forum_id'], $member_id, $topic_details['t_cache_first_member_id']);

        case 'post':
            if ($details === null) {
                $_post_details = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_topics t ON t.id=p.p_topic_id', ['*', 'p.id AS post_id'], ['p.id' => $id], '', 1);
                if (!isset($_post_details[0])) {
                    warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
                }
                $post_details = $_post_details[0];
            } else {
                $post_details = $details;
            }
            return cns_may_delete_post_by($id, $post_details['p_time'], $post_details['p_poster'], $post_details['p_cache_forum_id'], $member_id, $post_details['t_is_open'] == 0);
    }

    return false;
}

/**
 * Whether moving is allowed.
 *
 * @param  string $type Resource type
 * @set topic post
 * @param  AUTO_LINK $id Resource ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $details Resource row (null: lookup)
 * @return boolean Whether they can
 */
function can_move(string $type, int $id, ?int $member_id = null, ?array $details = null) : bool
{
    switch ($type) {
        case 'topic':
            return can_moderate_topic($id, $member_id, $details);

        case 'post':
            return can_moderate_post($id, $member_id, $details);
    }

    return false;
}

/**
 * Whether approving is allowed.
 *
 * @param  string $type Resource type
 * @set topic post
 * @param  AUTO_LINK $id Resource ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $details Resource row (null: lookup)
 * @return boolean Whether they can
 */
function can_approve(string $type, int $id, ?int $member_id = null, ?array $details = null) : bool
{
    if ($member_id === null) {
        $member_id = get_member();
    }

    switch ($type) {
        case 'topic':
            if ($details === null) {
                $_topic_details = $GLOBALS['FORUM_DB']->query_select('f_topics t', ['*', 't.id AS topic_id'], ['t.id' => $id], '', 1);
                if (!isset($_topic_details[0])) {
                    warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'topic'));
                }
                $topic_details = $_topic_details[0];
            } else {
                $topic_details = $details;
            }
            return !is_guest($member_id) && $topic_details['t_cache_first_member_id'] == $member_id && has_privilege($member_id, 'bypass_validation_midrange_content', 'topics') || can_moderate_topic($topic_details['topic_id'], $member_id, $topic_details);

        case 'post':
            if ($details === null) {
                $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
                $_post_details = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id', ['*', 'p.id AS post_id', 't.id AS topic_id'], ['p.id' => $id], '', 1);
                if (!isset($_post_details[0])) {
                    warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
                }
                $post_details = $_post_details[0];
            } else {
                $post_details = $details;
            }
            return !is_guest($member_id) && $post_details['p_poster'] == $member_id && has_privilege($member_id, 'bypass_validation_lowrange_content', 'topics') || can_moderate_topic($post_details['topic_id'], $member_id, $post_details);
    }

    return false;
}

/**
 * Whether pinning a topic is allowed.
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $topic_details Topic row (null: lookup)
 * @return boolean Whether they can
 */
function can_stick_topic(int $topic_id, ?int $member_id = null, ?array $topic_details = null) : bool
{
    return can_moderate_topic($topic_id, $member_id, $topic_details);
}

/**
 * Whether closing a topic is allowed.
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $topic_details Topic row (null: lookup)
 * @return boolean Whether they can
 */
function can_close_topic(int $topic_id, ?int $member_id = null, ?array $topic_details = null) : bool
{
    return can_moderate_topic($topic_id, $member_id, $topic_details);
}

/**
 * Whether moderating a topic is allowed (i.e. the forum it is in).
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $topic_details Topic row (null: lookup)
 * @return boolean Whether they can
 */
function can_moderate_topic(int $topic_id, ?int $member_id = null, ?array $topic_details = null) : bool
{
    if ($topic_details === null) {
        $_topic_details = $GLOBALS['FORUM_DB']->query_select('f_topics t', ['*', 't.id AS topic_id'], ['t.id' => $topic_id], '', 1);
        if (!isset($_topic_details[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'topic'));
        }
        $topic_details = $_topic_details[0];
    }
    return cns_may_moderate_forum($topic_details['t_forum_id'], $member_id);
}

/**
 * Whether moderating a post is allowed (i.e. the forum it is in).
 *
 * @param  AUTO_LINK $post_id Post ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $post_details Post row (null: lookup)
 * @return boolean Whether they can
 */
function can_moderate_post(int $post_id, ?int $member_id = null, ?array $post_details = null) : bool
{
    if ($post_details === null) {
        $_post_details = $GLOBALS['FORUM_DB']->query_select('f_posts p', ['*', 'p.id AS post_id'], ['p.id' => $post_id], '', 1);
        if (!isset($_post_details[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'post'));
        }
        $post_details = $_post_details[0];
    }
    return cns_may_moderate_forum($post_details['p_cache_forum_id'], $member_id);
}

/**
 * Whether renaming a topic is allowed.
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $topic_details Topic row (null: lookup)
 * @return boolean Whether they can
 */
function can_rename_topic(int $topic_id, ?int $member_id = null, ?array $topic_details = null) : bool
{
    if ($topic_details === null) {
        $_topic_details = $GLOBALS['FORUM_DB']->query_select('f_topics t', ['*', 't.id AS topic_id'], ['t.id' => $topic_id], '', 1);
        if (!isset($_topic_details[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'topic'));
        }
        $topic_details = $_topic_details[0];
    }
    return cns_may_edit_topics_by($topic_details['t_forum_id'], $member_id, $topic_details['t_cache_first_member_id']);
}

/**
 * Whether merging a member's posts within a topic is allowed.
 *
 * @param  AUTO_LINK $topic_id Topic ID
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @return boolean Whether they can
 */
function can_merge_posts(int $topic_id, ?int $member_id = null) : bool
{
    $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
    $posts = $GLOBALS['FORUM_DB']->query_select('f_posts p JOIN ' . $table_prefix . 'f_topics t ON t.id=p.p_topic_id', ['*', 'p.id AS post_id'], ['p_topic_id' => $topic_id, 'p_poster' => $member_id], '', 2);
    return (count($posts) >= 2) && (can_edit('post', $posts[0]['post_id'], $member_id, $posts[0]));
}

/**
 * Whether merging a topic is allowed.
 *
 * @param  AUTO_LINK $from_topic_id From topic ID (to get forum)
 * @param  ?AUTO_LINK $to_topic_id To topic ID (to get forum) (null: assume same forum as $from_forum_id)
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @param  ?array $topic_details Topic row for from topic (null: lookup)
 * @return boolean Whether they can
 */
function can_merge_topics(int $from_topic_id, ?int $to_topic_id, ?int $member_id = null, ?array $topic_details = null) : bool
{
    return can_moderate_topic($from_topic_id, $member_id, $topic_details) && (($to_topic_id === null) || can_moderate_topic($to_topic_id, $member_id));
}

/**
 * Whether banning a member is allowed.
 *
 * @param  ?MEMBER $member_id Member involved (null: current member)
 * @return boolean Whether they can
 */
function can_ban_member(?int $member_id = null) : bool
{
    if ($member_id === null) {
        $member_id = get_member();
    }

    return has_privilege($member_id, 'warn_members');
}

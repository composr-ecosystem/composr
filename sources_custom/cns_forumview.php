<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/**
 * Render a topic row (i.e. a row in a forum or results view), from given details (from cns_get_topic_array).
 *
 * @param  array $topic The details (array containing: last_post_id, id, modifiers, emoticon, first_member_id, first_username, first_post, num_posts, num_views)
 * @param  boolean $has_topic_marking Whether the viewing member has the facility to mark off topics (send as false if there are no actions for them to perform)
 * @param  boolean $pt Whether the topic is a Private Topic
 * @param  ?Tempcode $show_forum The forum name (null: do not show the forum name)
 * @return Tempcode The topic row
 */
function cns_render_topic(array $topic, bool $has_topic_marking, bool $pt = false, ?object $show_forum = null) : object
{
    $ret = non_overridden__cns_render_topic($topic, $has_topic_marking, $pt, $show_forum);

    if (!addon_installed('composr_homesite')) {
        return $ret;
    }

    if (!addon_installed('tickets')) {
        return $ret;
    }

    if ((!isset($topic['forum_id'])) || (!is_numeric($topic['forum_id']))) {
        return $ret;
    }

    $forum_id = $topic['forum_id'];

    $is_ticket = false;
    if (addon_installed('tickets')) {
        require_code('tickets');
        if (is_ticket_forum($forum_id)) {
            $is_ticket = true;
        }
    }
    if ($is_ticket) {
        require_lang('tickets');
        require_code('feedback');
        $ticket_id = extract_topic_identifier($topic['description']);
        $ticket_type_id = $GLOBALS['SITE_DB']->query_select_value_if_there('tickets', 'ticket_type', ['ticket_id' => $ticket_id]);
        $ticket_type_name = null;
        if ($ticket_type_id !== null) {
            $_ticket_type_name = $GLOBALS['SITE_DB']->query_select_value_if_there('ticket_types', 'ticket_type_name', ['id' => $ticket_type_id]);

            $d = new Tempcode();
            $d->attach(div(make_string_tempcode(escape_html($topic['description']))));
            $ticket_type_name = get_translated_text($_ticket_type_name);
            $d->attach(div(make_string_tempcode(escape_html($ticket_type_name))));

            $d->attach(get_composr_support_timings_wrap(!in_array('closed', $topic['modifiers']), $topic['id'], $ticket_type_name));

            $ret->singular_bind('DESCRIPTION', protect_from_escaping($d));
        }
    }

    return $ret;
}

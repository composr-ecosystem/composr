<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite_support_credits
 */

function get_composr_support_timings_wrap($open, $topic_id, $ticket_type_name, $say_more = false)
{
    $posts = $GLOBALS['FORUM_DB']->query_select('f_posts', array('p_poster', 'p_time'), array('p_topic_id' => $topic_id), 'ORDER BY p_time DESC,id DESC');
    $most_recent_post = null;
    foreach ($posts as $post) {
        if (!has_privilege($post['p_poster'], 'support_operator')) {
            $most_recent_post = $post;
            // Don't break, their first unanswered query may be further back
        } else {
            if (is_null($most_recent_post)) {
                $most_recent_post = $post; // Ok, most recent was staff
            }
            break; // Okay, the furthest back unanswered/answer post was the one in $most_recent_post
        }
    }

    $member_id = $most_recent_post['p_poster'];
    $last_time = $most_recent_post['p_time'];
    return get_composr_support_timings($open, $member_id, $ticket_type_name, $last_time, $say_more);
}

function get_composr_support_timings($open, $member_id, $ticket_type_name, $last_time, $say_more = false)
{
    $d = new Tempcode();

    if (!$open) {
        return $d;
    }

    if (has_privilege($member_id, 'support_operator')) {
        $d->attach(div(make_string_tempcode('The last reply was by staff.')));
    } else {
        $timestamp_to_answer_by = mixed();
        switch ($ticket_type_name) {
            // Very rough. Ignores weekends (okay, as means over-delivering) and in-day times
            case 'Back-burner priority':
                $timestamp_to_answer_by = within_business_hours(14 * 8, $last_time);
                break;
            case 'Regular priority':
                $timestamp_to_answer_by = within_business_hours(3 * 8, $last_time);
                break;
        }
        if (!is_null($timestamp_to_answer_by)) {
            $text = 'Next reply due by: ' . date('D jS M Y', $timestamp_to_answer_by);
            if ($say_more) {
                $text .= ' (Response times are determined by the requested ticket priority. Any requested programming tasks are started at the time of response. Response times apply between replies, as well as initially. Tickets may not be read until the next response time, so higher priority requests should be made in a new ticket. Sometimes we will beat response times but this should not be considered a precedent.)';
            }
            $d->attach(div(make_string_tempcode($text)));
        }
    }

    return $d;
}

function within_business_hours($hours, $time)
{
    while ($hours > 0) {
        // Go forward an hour
        $time += 60 * 60;
        $_time = tz_time($time, get_site_timezone()); // Convert to site time-zone

        // Skip outside business hours from counting
        $dow = date('D', $_time);
        if ($dow == 'Sat' || $dow == 'Sun') {
            continue;
        }
        $hour = intval(date('H', $_time));
        if ($hour < 10 || $hour >= 18) { // Support hours are 10am-6pm (as 9am-10am is setting up time)
            continue;
        }

        // Okay, a business hour, so deprecate our countdown
        $hours--;
    }

    return $time;
}


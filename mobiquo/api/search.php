<?php /*

 Composr
 Copyright (c) Christopher Graham/Tapatalk, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: classTTConnection|CMS.**/

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function search_topic_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/search_read.php');

    $search_key = (isset($params[0])) ? $params[0] : '';

    $search_id = (isset($params[3])) ? $params[3] : '';

    list($start, $max) = get_pagination_positions($params, 1, 2, 9);

    $search_object = new CMSSearchRead();
    list($total_topic_num, $_topics) = $search_object->search_topics($search_key, $start, $max);

    $topics = [];
    foreach ($_topics as $topic) {
        $topics[] = render_topic_to_tapatalk($topic['topic_id'], false, $start, $max, $topic, RENDER_TOPIC_POST_KEY_NAME | RENDER_TOPIC_SEARCH);
    }

    $response = mobiquo_val([
        'total_topic_num' => mobiquo_val($total_topic_num, 'int'),
        //'search_id' => mobiquo_val($search_id, 'string'), We don't save search result sets
        'topics' => mobiquo_val($topics, 'array'),
    ], 'struct');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function search_post_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/search_read.php');

    $search_key = (isset($params[0])) ? $params[0] : '';

    $search_id = (isset($params[3])) ? $params[3] : '';

    list($start, $max) = get_pagination_positions($params, 1, 2, 9);

    $search_object = new CMSSearchRead();
    list($total_post_num, $_posts) = $search_object->search_posts($search_key, $start, $max);

    $posts = [];
    foreach ($_posts as $post) {
        $posts[] = render_post_to_tapatalk($post['post_id'], false, $post, RENDER_POST_SHORT_CONTENT | RENDER_POST_FORUM_DETAILS | RENDER_POST_TOPIC_DETAILS | RENDER_POST_SEARCH);
    }

    $response = mobiquo_val([
        'total_post_num' => mobiquo_val($total_post_num, 'int'),
        //'search_id' => mobiquo_val($search_id, 'string'), We don't save search result sets
        'posts' => mobiquo_val($posts, 'array'),
    ], 'struct');
    return mobiquo_response($response);
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function search_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_READ . '/search_read.php');

    $filter = $params[0];

    $search_object = new CMSSearchRead();

    $search_id = (isset($params['search_id'])) ? $params['search_id'] : '';

    $page = (isset($params['page'])) ? $params['page'] : 1;
    $max = (isset($params['perpage'])) ? $params['perpage'] : 20;
    $start = ($page - 1) * $max;

    $keywords = !empty($filter['keywords']) ? $filter['keywords'] : '';
    $userid = !@cms_empty_safe($filter['userid']) ? intval($filter['userid']) : null;
    $searchuser = !@cms_empty_safe($filter['searchuser']) ? $filter['searchuser'] : null;
    $forumid = !@cms_empty_safe($filter['forumid']) ? intval($filter['forumid']) : null;
    $threadid = !@cms_empty_safe($filter['threadid']) ? intval($filter['threadid']) : null;
    $titleonly = !isset($filter['titleonly']) || ($filter['titleonly'] == 1);
    $showposts = (isset($filter['showposts']) && $filter['showposts'] == 0) ? 0 : 1;
    $searchtime = !empty($filter['searchtime']) ? @intval($filter['searchtime']) : null;
    $only_in = !@cms_empty_safe($filter['only_in']) ? $filter['only_in'] : null;
    $not_in = !@cms_empty_safe($filter['not_in']) ? $filter['not_in'] : null;

    if ($showposts == 0) {
        list($total_topic_num, $_topics) = $search_object->search_topics($keywords, $start, $max, $userid, $searchuser, $forumid, $titleonly, $searchtime, $only_in, $not_in);

        $topics = [];
        foreach ($_topics as $topic) {
            $topics[] = render_topic_to_tapatalk($topic['topic_id'], false, $start, $max, $topic, RENDER_TOPIC_POST_KEY_NAME | RENDER_TOPIC_SEARCH);
        }

        $response = mobiquo_val([
            'total_topic_num' => mobiquo_val($total_topic_num, 'int'),
            //'search_id' => mobiquo_val($search_id, 'string'), We don't save search result sets
            'topics' => mobiquo_val($topics, 'array'),
        ], 'struct');
    } else {
        list($total_post_num, $_posts) = $search_object->search_posts($keywords, $start, $max, $userid, $searchuser, $forumid, $threadid, $searchtime, $only_in, $not_in);

        $posts = [];
        foreach ($_posts as $post) {
            $posts[] = render_post_to_tapatalk($post['post_id'], false, $post, RENDER_POST_SHORT_CONTENT | RENDER_POST_FORUM_DETAILS | RENDER_POST_TOPIC_DETAILS | RENDER_POST_SEARCH);
        }

        $response = mobiquo_val([
            'total_post_num' => mobiquo_val($total_post_num, 'int'),
            //'search_id' => mobiquo_val($search_id, 'string'), We don't save search result sets
            'posts' => mobiquo_val($posts, 'array'),
        ], 'struct');
    }

    return mobiquo_response($response);
}

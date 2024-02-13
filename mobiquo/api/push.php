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

/*EXTRA FUNCTIONS: classTTConnection|CMS.*|TapatalkPush*/

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function reset_push_slug_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    if (mobiquo_input_protocol() != 'post') {
        warn_exit('Only supported for POST method');
    }

    if (!isset($params['code'])) {
        warn_exit('Missing parameter');
    }

    $code = $params['code'];

    $connection = new classTTConnection();
    $test = $connection->actionVerification($code, 'reset_push_slug');
    if ($test === false) {
        warn_exit('Could not verify connection');
    }

    $push = new TapatalkPush();
    $push->set_push_slug('');

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function push_content_check_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    if (mobiquo_input_protocol() != 'post') {
        warn_exit('Only supported for POST method');
    }

    if (!isset($params['code']) || !isset($params['data'])) {
        warn_exit('Missing parameter');
    }

    $code = $params['code'];
    $data = unserialize($params['data']);

    $connection = new classTTConnection();
    $test = $connection->actionVerification($code, 'push_content_check');
    if ($test === false) {
        warn_exit('Could not verify connection');
    }

    $result = false;
    switch ($data['type']) {
        case 'newtopic':
        case 'sub': // =reply
        case 'quote':
        case 'tag': //=mention
            $posts = $GLOBALS['FORUM_DB']->query_select('f_posts', ['*'], ['id' => intval($data['subid'])], '', 1);
            if ((isset($posts[0])) && ($posts[0]['p_topic_id'] == intval($data['id'])) && ($posts[0]['p_poster'] == $data['authorid']) && ((is_guest($posts[0]['p_poster'])) || ($GLOBALS['FORUM_DRIVER']->get_username($posts[0]['p_poster'], false, USERNAME_DEFAULT_BLANK) == $data['author']))) {
                $result = true;
            }
            break;
        case 'conv': // =PT new topic or PT reply
        case 'pm':
            $posts = $GLOBALS['FORUM_DB']->query_select('f_posts', ['*'], ['id' => intval($data['mid'])], '', 1);
            if ((isset($posts[0])) && ($posts[0]['p_poster'] == intval($data['authorid'])) && ($GLOBALS['FORUM_DRIVER']->get_username($posts[0]['p_poster'], false, USERNAME_DEFAULT_BLANK) == $data['author'])) {
                $result = true;
            }
            break;
    }

    if (!$result) {
        warn_exit('fail');
    }

    return mobiquo_response_true();
}

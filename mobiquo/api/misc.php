<?php /*

 Composr
 Copyright (c) ocProducts/Tapatalk, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
function get_real_url_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    $mode = $params[0];
    $id = intval($params[1]);

    switch ($mode) {
        case 'forum':
            $url = static_evaluate_tempcode(build_url(['page' => 'forumview', 'type' => 'browse', 'id' => $id], get_module_zone('forumview'), [], false, false, true));
            break;
        case 'topic':
            $url = static_evaluate_tempcode(build_url(['page' => 'topicview', 'type' => 'browse', 'id' => $id], get_module_zone('topicview'), [], false, false, true));
            break;
        case 'post':
            $url = static_evaluate_tempcode(build_url(['page' => 'topicview', 'type' => 'findpost', 'id' => $id], get_module_zone('topicview'), [], false, false, true, 'post_' . strval($id)));
            break;
        default:
            warn_exit('Unknown content type');
    }

    $response = mobiquo_val([
        'result' => true,
        'url' => $url,
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
function set_api_key_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    if (mobiquo_input_protocol() != 'post') {
        warn_exit('Only supported for POST method');
    }

    if (!isset($params['code']) || !isset($params['key'])) {
        warn_exit('Missing parameter');
    }

    $code = $params['code'];
    $key = $params['key'];

    $connection = new classTTConnection();
    if (!$connection->actionVerification($code, 'set_api_key')) {
        warn_exit('Could not verify request');
    }

    require_code('config2');
    set_option('tapatalk_api_key', $key);

    return mobiquo_response_true();
}

/**
 * Standard Tapatalk endpoint implementation.
 * Sits in front of a more Composr-appropriate equivalent API method.
 *
 * @param  mixed $raw_params Mobiquo parameters
 * @return mixed Mobiquo result
 */
function verify_connection_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    if (mobiquo_input_protocol() != 'post') {
        warn_exit('Only supported for POST method');
    }

    if (!isset($params['type']) || !isset($params['code'])) {
        warn_exit('Missing parameter');
    }

    $type = $params['type'];
    $code = $params['code'];

    $connection = new classTTConnection();
    $test = $connection->verify_connection($type, $code);
    if ($test === false) {
        warn_exit('Could not verify connection');
    }

    $response = mobiquo_val($test, 'struct');
    return mobiquo_response($response);
}

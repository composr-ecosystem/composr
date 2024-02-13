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
function upload_attach_func($raw_params)
{
    require_once(COMMON_CLASS_PATH_WRITE . '/attachment_write.php');

    $account_object = new CMSAttachmentWrite();
    $results = $account_object->handle_upload_attach();

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'attachment_id' => mobiquo_val(strval($results['attachment_id']), 'string'),
        'filters_size' => mobiquo_val($results['filters_size'], 'int'),
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
function upload_avatar_func($raw_params)
{
    require_once(COMMON_CLASS_PATH_WRITE . '/attachment_write.php');

    $account_object = new CMSAttachmentWrite();
    $results = $account_object->handle_upload_avatar();

    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'filters_size' => mobiquo_val($results['filters_size'], 'int'),
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
function remove_attachment_func($raw_params)
{
    $params = mobiquo_params_decode($raw_params);

    require_once(COMMON_CLASS_PATH_WRITE . '/attachment_write.php');

    $attachment_id = intval($params[0]);
    $forum_id = ($params[1] == '0') ? null : intval($params[1]);
    $message_id = isset($params[3]) ? intval($params[3]) : null;

    $account_object = new CMSAttachmentWrite();
    $account_object->remove_attachment($attachment_id, $forum_id, $message_id);

    return mobiquo_response_true();
}

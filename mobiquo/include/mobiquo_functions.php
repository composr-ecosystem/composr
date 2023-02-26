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

/**
 * Bootstrap some Mobiquo framework stuff.
 */
function initialise_mobiquo()
{
    static $already_initialised = false;
    if ($already_initialised) {
        return;
    }

    define('TAPATALK_LOG', dirname(__DIR__, 2) . '/data_custom/tapatalk.log'); // Make this file (writeable) if you want automatic logging
    define('TAPATALK_REQUEST_ID', md5(uniqid('', true)));

    $already_initialised = true;
}

/**
 * Find the input protocol for the API call.
 *
 * @return string The input protocol
 */
function mobiquo_input_protocol() : string
{
    global $MOBIQUO_SERVER;
    switch (cms_strtolower_ascii(get_class($MOBIQUO_SERVER))) {
        case 'mobiquoserverjson':
            return 'json';
        case 'mobiquoserverxmlrpc':
            return 'xmlrpc';
        case 'mobiquoserverpost':
            return 'post';
    }
    return 'xmlrpc';
}

/**
 * Decode parameters we were called with.
 *
 * @param  mixed $raw_params Raw params
 * @return array Params as an array
 */
function mobiquo_params_decode($raw_params) : array
{
    global $MOBIQUO_SERVER;
    return $MOBIQUO_SERVER->params_decode($raw_params);
}

/**
 * Wrap a value for the particular MobiquoServer server implementation.
 *
 * @param  mixed $data Data
 * @param  ?string $type Type (null: autodetect)
 * @set string boolean base64 int dateTime.iso8601
 * @return mixed Mobiquo result
 */
function mobiquo_val($data, ?string $type = null)
{
    if (is_array($data)) {
        foreach ($data as $i => $val) {
            if (!is_object($val)) {
                $data[$i] = mobiquo_val($val);
            }
        }
    }

    global $MOBIQUO_SERVER;
    return $MOBIQUO_SERVER->val($data, $type);
}

/**
 * Wrap a result for the particular MobiquoServer server implementation.
 *
 * @param  mixed $data Data
 * @return mixed Mobiquo response
 */
function mobiquo_response($data)
{
    global $MOBIQUO_SERVER;
    return $MOBIQUO_SERVER->response($data);
}

/**
 * Generate simple "success" Mobiquo response. For endpoints that don't require additional response details.
 *
 * @return mixed Mobiquo result
 */
function mobiquo_response_true()
{
    $response = mobiquo_val([
        'result' => mobiquo_val(true, 'boolean'),
        'result_text' => mobiquo_val('', 'base64'),
    ], 'struct');
    return mobiquo_response($response);
}

/**
 * Generate Mobiquo failure response.
 *
 * @param  ?string $error_message Error message (null: Standard internal error message)
 * @return mixed Mobiquo result
 */
function mobiquo_response_false(?string $error_message = null)
{
    if ($error_message === null) {
        $error_message = do_lang('INTERNAL_ERROR');
    }

    $response = mobiquo_val([
        'result' => mobiquo_val(false, 'boolean'),
        'result_text' => mobiquo_val($error_message, 'base64'),
    ], 'struct');
    return mobiquo_response($response);
}

/**
 * Find which file a method is implemented in.
 *
 * @param  string $request_method Mobiquo method name
 * @return string File
 */
function request_helper_get_file(string $request_method) : string
{
    if (substr($request_method, 0, 7) == 'upload_') {
        return 'attachment';
    }

    $data_file = __DIR__ . '/request_helper.bin';
    if (!is_file($data_file)) {
        $func_file_mapping = [];

        $_d = dirname(__DIR__) . '/api';
        $dh = opendir($_d);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.php') {
                $_f = $_d . '/' . $f;
                $funcs_before = get_defined_functions();
                require_once($_f);
                $funcs_after = get_defined_functions();
                $funcs_diff = array_diff($funcs_after['user'], $funcs_before['user']);

                foreach ($funcs_diff as $func) {
                    if (substr($func, -5) == '_func') {
                        $func_file_mapping[substr($func, 0, strlen($func) - 5)] = basename($f, '.php');
                    }
                }
            }
        }
        closedir($dh);

        // Verify
        global $SERVER_DEFINE;
        foreach ($SERVER_DEFINE as $key => $sp) {
            if ($sp['function'] != $key . '_func') {
                exit('Expected function pattern naming not followed for ' . $key);
            }
            if (!isset($func_file_mapping[$key])) {
                exit('Function implementation missing for ' . $key);
            }
        }
        foreach (array_keys($func_file_mapping) as $key) {
            if (!isset($SERVER_DEFINE[$key])) {
                exit('Function signature missing in server_define.php for ' . $key);
            }
        }

        // Save
        require_code('files');
        cms_file_put_contents_safe($data_file, serialize($func_file_mapping), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    } else {
        $func_file_mapping = unserialize(cms_file_get_contents_safe($data_file, FILE_READ_LOCK));
    }

    if (!isset($func_file_mapping[$request_method])) {
        return preg_replace('#_\d+$#', '', $request_method); // Maybe in a file with same name. Unlikely, but worth a shot.
    }
    return $func_file_mapping[$request_method];
}

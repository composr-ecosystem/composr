<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cns_tapatalk
 */

/**
 * Mobiquo server implementation.
 */
class MobiquoServerJSON extends MobiquoServer
{
    /**
     * Decode parameters we were called with.
     *
     * @param  mixed $raw_params Raw params
     * @return array Params as an array
     */
    public function params_decode($raw_params) : array
    {
        return $raw_params; // We decoded in dispatch_request(), as data is merged from $_GET and JSON
    }

    /**
     * Find Mobiquo method name (endpoint name).
     *
     * @return string Method name
     */
    public function get_method_name() : string
    {
        if (isset($_GET['method_name'])) {
            return $_GET['method_name'];
        }
        if (isset($_POST['method_name'])) {
            return $_POST['method_name'];
        }

        header('Content-Type: text/plain; charset=utf-8');
        exit('No method is provided');
    }

    /**
     * Dispatch a server request.
     */
    public function dispatch_request()
    {
        header('Content-Type: application/json; charset=' . get_charset());

        $data = @file_get_contents('php://input');

        if (isset($_SERVER['HTTP_CONTENT_ENCODING'])) {
            $content_encoding = str_replace('x-', '', $_SERVER['HTTP_CONTENT_ENCODING']);
        } else {
            $content_encoding = '';
        }
        if ($content_encoding != '' && strlen($data) > 0) {
            if ($content_encoding == 'deflate' || $content_encoding == 'gzip') {
                // If decoding works, use it else assume data wasn't gzencoded
                if (function_exists('gzinflate')) {
                    if ($content_encoding == 'deflate' && ($degzdata = @gzuncompress($data)) !== false) {
                        $data = $degzdata;
                    } elseif (($degzdata = @gzinflate(substr($data, 10))) !== false) {
                        $data = $degzdata;
                    }
                } else {
                    warn_exit('Received from client compressed HTTP request and cannot decompress');
                }
            }
        }

        require_code('character_sets');
        $data = convert_to_internal_encoding($data, 'utf-8');

        $get = $_GET;

        unset($get['method_name']);

        $params = @array_merge(array_values(json_decode($data, true)), $get);

        cms_ini_set('ocproducts.xss_detect', '0');

        global $SERVER_DEFINE;
        if (!isset($SERVER_DEFINE[$this->get_method_name()])) {
            warn_exit('Unknown endpoint');
        }
        $function = $SERVER_DEFINE[$this->get_method_name()]['function'];
        try {
            $response = call_user_func($function, $params);
        } catch (Exception $e) {
            @header('HTTP/1.0 200'); // We always give 200 responses, so make sure we undo any other ones given

            $msg = $e->getMessage();
            if ($GLOBALS['DEV_MODE'] && get_param_integer('keep_fatalistic', 0) != 0) {
                $msg .= ' ' . var_export($e->getTrace(), true);
            }
            $response = mobiquo_response_false($msg);

            if ((is_file(TAPATALK_LOG)) && (cms_is_writable(TAPATALK_LOG))) {
                // Request
                $log_file = fopen(TAPATALK_LOG, 'ab');
                flock($log_file, LOCK_EX);
                fseek($log_file, 0, SEEK_END);
                fwrite($log_file, TAPATALK_REQUEST_ID . ' -- ' . loggable_date() . " *TRACE*:\n");
                fwrite($log_file, var_export($e->getTrace(), true));
                fwrite($log_file, "\n\n");
                flock($log_file, LOCK_UN);
                fclose($log_file);
            }
        }
        $this->output_response($response);
    }

    /**
     * Wrap a value for the particular MobiquoServer server implementation.
     *
     * @param  mixed $data Data
     * @param  ?string $type Type (null: autodetect)
     * @set string boolean base64 int dateTime.iso8601 array struct
     * @return mixed Mobiquo result
     */
    public function val($data, ?string $type)
    {
        if ($type === 'dateTime.iso8601') {
            $data = $this->date_encode($data);
        }

        if (is_string($data)) {
            $data = convert_to_internal_encoding($data, get_charset(), 'utf-8');
            $data = html_entity_decode($data, ENT_QUOTES, 'utf-8');
        }

        return $data; // No further internal value encoding needed for JSON
    }

    /**
     * Generate a standard Mobiquo date.
     *
     * @param  integer $timet Timestamp
     * @param  integer $timezone Timezone hour offset
     * @return string iso8601 date
     */
    private function date_encode(int $timet, int $timezone = 0) : string
    {
        return gmdate('Y-m-d\TH:i:s', $timet + $timezone * 3600) . '+00:00';
    }

    /**
     * Wrap a result for the particular MobiquoServer server implementation.
     *
     * @param  mixed $data Data
     * @return mixed Mobiquo response
     */
    public function response($data)
    {
        return json_encode($data);
    }
}

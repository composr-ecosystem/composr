<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Mobiquo server implementation.
 */
class MobiquoServerPOST extends MobiquoServer
{
    private $output_server;

    /**
     * Decode parameters we were called with.
     *
     * @param  mixed $raw_params Raw params
     * @return array Params as an array
     */
    public function params_decode($raw_params) : array
    {
        return $raw_params; // No decoding needed
    }

    /**
     * Find Mobiquo method name (endpoint name).
     *
     * @return string Method name
     */
    public function get_method_name() : string
    {
        if (isset($_POST['method_name'])) {
            return $_POST['method_name'];
        }
        if (isset($_GET['method_name'])) {
            return $_GET['method_name'];
        }

        header('Content-Type: text/plain; charset=utf-8');
        exit('No method is provided');
    }

    /**
     * Dispatch a server request.
     */
    public function dispatch_request()
    {
        $this->output_server = $this->get_output_server();

        if ($this->output_server === null) {
            header('Content-Type: text/plain; charset=utf-8');
        }

        convert_data_encodings(true);

        $get = $_GET;
        unset($get['method_name']);

        $post = $_POST;
        unset($post['method_name']);

        $params = array_merge($post, $get);

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
     * Get output server object for this request.
     *
     * @return ?object The output Moquiquo server (null: none)
     */
    private function get_output_server() : ?object
    {
        if (isset($_POST['format'])) {
            $format = trim($_POST['format']);
        } else {
            $format = 'xmlrpc';
        }

        switch ($format) {
            case 'json':
                require_once(__DIR__ . '/mobiquo_json.php');
                return new MobiquoServerJSON();
            case 'xmlrpc':
                require_once(__DIR__ . '/mobiquo_xmlrpc.php');
                return new MobiquoServerXMLRPC();
            case 'serialize':
                return null;
        }

        warn_exit($format . ' is not valid format');
        return null;
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
        if ($this->output_server === null) {
            if (is_string($data)) {
                $data = convert_to_internal_encoding($data, get_charset(), 'utf-8');
                $data = html_entity_decode($data, ENT_QUOTES, 'utf-8');
            }

            if ($type === 'dateTime.iso8601') {
                $data = $this->date_encode($data);
            }

            return $data;
        }

        return $this->output_server->val($data, $type);
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
        if ($this->output_server === null) {
            return serialize($data);
        }

        return $this->output_server->response($data);
    }

    /**
     * Output a response.
     *
     * @param  mixed $response Response
     */
    public function output_response($response)
    {
        if ($this->output_server === null) {
            parent::output_response($response);
            return;
        }

        $this->output_server->output_response($response);
    }
}

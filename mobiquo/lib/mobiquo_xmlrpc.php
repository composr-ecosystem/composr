<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/*EXTRA FUNCTIONS: .*xmlrpc.*|var_export*/

/**
 * Mobiquo server implementation.
 */
class MobiquoServerXMLRPC extends MobiquoServer
{
    private $rpc_server;

    /**
     * Construct object.
     */
    public function __construct()
    {
        require_once(dirname(__FILE__) . '/xmlrpc.php');
        require_once(dirname(__FILE__) . '/xmlrpcs.php');

        global $SERVER_DEFINE;
        $this->rpc_server = new xmlrpc_server($SERVER_DEFINE, false);
        $this->rpc_server->setDebug(1);
        $this->rpc_server->compress_response = 'true';
        $this->rpc_server->response_charset_encoding = 'UTF-8';
    }

    /**
     * Decode parameters we were called with.
     *
     * @param  mixed $raw_params Raw params
     * @return array Params as an array
     */
    public function params_decode($raw_params)
    {
        return php_xmlrpc_decode($raw_params);
    }

    /**
     * Find Mobiquo method name (endpoint name).
     *
     * @return string Method name
     */
    public function get_method_name()
    {
        if (isset($_POST['method_name'])) {
            return $_POST['method_name'];
        }

        $data = @file_get_contents('php://input');

        if ($data == '') {
            header('Content-type: text/plain');
            exit('No method is provided');
        }
        $parsed = php_xmlrpc_decode_xml($data);
        return trim($parsed->methodname);
    }

    /**
     * Dispatch a server request.
     */
    public function dispatch_request()
    {
        ini_set('ocproducts.type_strictness', '0'); // Much Tapatalk client code will not be compatible with this

        ini_set('ocproducts.xss_detect', '0');

        if (!empty($_POST['method_name'])) // HTTP post message, no further payload data
        {
            $xml = new xmlrpcmsg($_POST['method_name']);
            $request = $xml->serialize();
        } else // Native XML-RPC message, it will parse out the method name from the POST environment
        {
            $request = null; // Let it auto-calculate
        }
        try {
            $this->rpc_server->service($request);
        } catch (Exception $e) {
            @header('HTTP/1.0 200'); // We always give 200 responses, so make sure we undo any other ones given

            $msg = $e->getMessage();
            if ($GLOBALS['DEV_MODE'] && get_param_integer('keep_fatalistic', 0) == 1) {
                $msg .= ' ' . var_export($e->getTrace(), true);
            }
            $response = mobiquo_response_false($msg);
            $this->output_response($response);

            if ((is_file(TAPATALK_LOG)) && (is_writable_wrap(TAPATALK_LOG))) {
                // Request
                $log_file = fopen(TAPATALK_LOG, 'at');
                flock($log_file, LOCK_EX);
                fseek($log_file, 0, SEEK_END);
                fwrite($log_file, TAPATALK_REQUEST_ID . ' -- ' . date('Y-m-d H:i:s') . " *TRACE*:\n");
                fwrite($log_file, var_export($e->getTrace(), true));
                fwrite($log_file, "\n\n");
                flock($log_file, LOCK_UN);
                fclose($log_file);
            }
        }
    }

    /**
     * Wrap a value for the particular MobiquoServer server implementation.
     *
     * @param  mixed $data Data
     * @param  ?string $type Type (null: autodetect)
     * @set string boolean base64 int dateTime.iso8601 array struct
     * @return mixed Mobiquo result
     */
    public function val($data, $type)
    {
        ini_set('ocproducts.type_strictness', '0'); // Much Tapatalk client code will not be compatible with this

        if ($type === 'dateTime.iso8601') {
            $data = $this->iso8601_encode($data);
        }

        if (is_string($data)) {
            $data = convert_to_internal_encoding($data, get_charset(), 'UTF-8');
            $data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
        }

        if (is_null($type)) {
            return php_xmlrpc_encode($data);
        }

        return new xmlrpcval($data, $type);
    }

    /**
     * Generate a standard Mobiquo date.
     *
     * @param  integer $timet Timestamp
     * @param  integer $timezone Timezone hour offset
     * @return string iso8601 date
     */
    private function iso8601_encode($timet, $timezone = 0)
    {
        return gmdate('Ymd\TH:i:s', $timet + $timezone * 3600) . '+00:00';
    }

    /**
     * Wrap a result for the particular MobiquoServer server implementation.
     *
     * @param  mixed $data Data
     * @return mixed Mobiquo response
     */
    public function response($data)
    {
        ini_set('ocproducts.type_strictness', '0'); // Much Tapatalk client code will not be compatible with this

        return new xmlrpcresp($data);
    }

    /**
     * Output a response.
     *
     * @param  mixed $response Response
     */
    public function output_response($response)
    {
        ini_set('ocproducts.type_strictness', '0'); // Much Tapatalk client code will not be compatible with this

        echo $this->rpc_server->xml_header('UTF-8');
        echo $response->serialize();
    }
}

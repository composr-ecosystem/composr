<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    confluence
 */

function init__confluence2()
{
    require_code('confluence');
}

function confluence_proxy_script()
{
    if (!addon_installed('confluence')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('confluence')));
    }

    global $CONFLUENCE_SUBDOMAIN;

    $url = qualify_url($_SERVER['QUERY_STRING'], get_confluence_base_url());

    list($output, $mime_type, , , $http_message, $http_message_b) = confluence_call_url($url, false);

    if ($output == '') {
        if ($http_message == '404') {
            warn_exit('URL not found on Confluence, ' . $url, false, false, 404);
        }
        warn_exit($http_message_b);
    }

    cms_ini_set('ocproducts.xss_detect', '0');

    if ((preg_match('#^image/#i', $mime_type) != 0) || (stripos($mime_type, 'svg') !== false)) {
        $mime_type = 'application/octet-stream';
        header('Location: ' . $url); // assign_refresh not used, as no UI here
    }

    header('Content-Type: ' . $mime_type);

    echo $output;
}

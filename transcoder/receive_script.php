<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    transcoder
 */

// Copies stuff into the transcoder queue from Composr CMS (the 'transcoding_server' option links to this script)

ini_set('display_errors', '1');
error_reporting(E_ALL);
if (function_exists('set_time_limit')) {
    @set_time_limit(0);
}

$get_url = empty($_GET['file']) ? '' : str_replace(' ', '%20', $_GET['file']);
if (substr($get_url, 0, 4) != 'http') {
    exit('Security issue');
}

if ($get_url != '') {
    $filename = basename(rawurldecode(preg_replace('#\?.*$#s', '', $get_url)));
    $filename_stub = preg_replace('#\..*$#s', '', $filename);
    $filename_ext = strtolower(preg_replace('#^[^\.]*\.#s', '', $filename));

    if (!in_array($filename_ext, array('avi', 'mp4', 'm4v', 'mkv', 'webm', 'flv', 'vob', 'ogv', 'drc', 'mov', 'qt', 'wmv', 'rm', 'asf', 'mpg', 'mp2', 'mpeg', 'mpe', 'mpv', 'm2v', '3gp', '3g2'))) {
        exit('Unknown file extension');
    }

    $save_to_file = __DIR__ . '/queue/' . $filename;

    ini_set('allow_url_fopen', '1');
    file_put_contents($save_to_file, file_get_contents($get_url));
}

echo 'Copied';

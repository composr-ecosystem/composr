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

define('IN_MOBIQUO', true);
define('FORUM_ROOT', dirname(__FILE__));

define('COMMON_CLASS_PATH_INCLUDE', dirname(__FILE__) . '/include');

include(COMMON_CLASS_PATH_INCLUDE . '/common_functions.php');

initialise_composr();

if (isset($_GET['user_id'])) {
    $member_id = intval($_GET['user_id']);
} elseif (isset($_GET['username'])) {
    $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($_GET['username']);
} else {
    $member_id = get_member();
}

safe_ini_set('ocproducts.xss_detect', '0');

$url = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($member_id);
if ($url == '') {
    // Transparent 1x1 PNG
    header('Content-Type: image/png');
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
    return;
}

$stem = get_base_url() . '/';
if (substr($url, 0, strlen($stem)) == $stem) {
    $url = get_file_base() . '/' . rawurldecode(substr($url, strlen($stem)));
} else {
    $stem = get_custom_base_url() . '/';
    if (substr($url, 0, strlen($stem)) == $stem) {
        $url = get_custom_file_base() . '/' . rawurldecode(substr($url, strlen($stem)));
    }
}

require_code('mime_types');
require_code('files');
header('Content-Type: ' . get_mime_type(get_file_extension($url), false));
ini_set('allow_url_fopen', '1');

cms_ob_end_clean();
@readfile($url);

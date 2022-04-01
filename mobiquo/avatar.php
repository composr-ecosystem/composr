<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

define('IN_MOBIQUO', true);
define('FORUM_ROOT', __DIR__);

define('COMMON_CLASS_PATH_INCLUDE', __DIR__ . '/include');

include(COMMON_CLASS_PATH_INCLUDE . '/common_functions.php');

initialise_composr();

if (isset($_GET['user_id'])) {
    $member_id = intval($_GET['user_id']);
} elseif (isset($_GET['username'])) {
    $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($_GET['username']);
} else {
    $member_id = get_member();
}

cms_ini_set('ocproducts.xss_detect', '0');

$url_or_path = $GLOBALS['FORUM_DRIVER']->get_member_avatar_url($member_id);
if ($url_or_path == '') {
    // Transparent 1x1 PNG
    header('Content-Type: image/png');
    echo base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=');
    return;
}

$relative_part = '';
$custom_dir = null;
if (url_is_local($url_or_path, $relative_part, $custom_dir)) {
    $url_or_path = get_file_base($custom_dir) . '/' . rawurldecode($relative_part);
}

require_code('mime_types');
require_code('files');
header('Content-Type: ' . get_mime_type(get_file_extension($url), false));
ini_set('allow_url_fopen', '1');

cms_ob_end_clean();
@readfile($url);

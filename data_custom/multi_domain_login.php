<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    multi_domain_login
 */

/*EXTRA FUNCTIONS: setcookie*/

global $SITE_INFO;

require dirname(__DIR__) . '/_config.php';

$session_expiry_time = floatval($_GET['session_expiry_time']);
$session_id = $_GET['session_id'];
$guest_session = ($_GET['guest_session'] == '1');

$timeout = $guest_session ? (time() + intval(60.0 * 60.0 * max(0.017, $session_expiry_time))) : null;

$test = setcookie(get_session_cookie(), $session_id, $timeout, get_cookie_path());

header('X-Robots-Tag: noindex');
@header_remove('x-powered-by'); // Security

header('Content-Type: image/png');
$img = imagecreatetruecolor(1, 1);
imagesavealpha($img, true);
$color = imagecolorallocatealpha($img, 0, 0, 0, 127);
imagefill($img, 0, 0, $color);
imagepng($img);
imagedestroy($img);

/**
 * Get the session cookie's name.
 *
 * @return string The session ID cookie's name
 */
function get_session_cookie() : string
{
    global $SITE_INFO;
    if (empty($SITE_INFO['session_cookie'])) {
        $SITE_INFO['session_cookie'] = '__Host-cms_session';
    }

    validate_special_cookie_prefix($SITE_INFO['session_cookie']);

    return $SITE_INFO['session_cookie'];
}

/**
 * Get the Composr cookie path.
 *
 * @return ?string The Composr cookie path (null: no special path, global)
 */
function get_cookie_path() : ?string
{
    global $SITE_INFO;
    $ret = array_key_exists('cookie_path', $SITE_INFO) ? $SITE_INFO['cookie_path'] : '/';
    return ($ret == '') ? null : $ret;
}

/**
 * Ensure that if we are using a special cookie name prefix that we can actually do so, otherwise strip it.
 *
 * @param ID_TEXT $cookie_name The name of the cookie (passed by reference; prefix will be stripped if it cannot be used)
 */
function validate_special_cookie_prefix(string &$cookie_name)
{
    global $SITE_INFO;

    // If __Host- prefixed, determine if we can use it
    if (strpos($cookie_name, '__Host-') === 0) {
        if (isset($SITE_INFO['cookie_domain']) && !empty($SITE_INFO['cookie_domain'])) { // Cannot use __Host- if a domain is set
            $cookie_name = substr($cookie_name, 7);
            return;
        }

        if (strpos($SITE_INFO['base_url'], 'https://') !== 0) { // Cannot use __Host- if not running securely
            $cookie_name = substr($cookie_name, 7);
            return;
        }

        $path = get_cookie_path();
        if ($path !== '/') { // Cannot use __Host- if path is not /
            $cookie_name = substr($cookie_name, 7);
            return;
        }
    }

    // If __Secure- prefixed, determine if we can use it
    if (strpos($cookie_name, '__Secure-') === 0) {
        if (strpos($SITE_INFO['base_url'], 'https://') !== 0) { // Cannot use __Secure- if not running securely
            $cookie_name = substr($cookie_name, 9);
            return;
        }
    }
}

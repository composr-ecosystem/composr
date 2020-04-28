<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

header('X-Robots-Tag: noindex');

$cache_expire = 60 * 60 * 24 * 365;
header_remove('Last-Modified');
header('Cache-Control: public, max-age=' . strval($cache_expire));
header_remove('Pragma');
header_remove('Expires');

echo '<script src="https://connect.facebook.net/en_US/all.js"></script>';

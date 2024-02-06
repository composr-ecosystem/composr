<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

$d = opendir('.');
$one = false;
while (($file = readdir($d)) !== false) {
    if (substr($file, -4) == '.png') {
        if ($one) {
            echo ',';
        }
        $one = true;
        echo $file;
    }
}
closedir($d);

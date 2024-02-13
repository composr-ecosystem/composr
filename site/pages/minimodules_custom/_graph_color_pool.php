<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    visualisation
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('visualisation', $error_msg)) {
    return $error_msg;
}

require_code('graphs');

$color_pool = [];
_generate_graph_color_pool($color_pool);

echo '<div class="float-surrounder">';
foreach ($color_pool as $color) {
    echo '<div style="text-align: center; box-sizing: border-box; padding-top: 35px; float: left; width: 100px; height: 100px; color: white; font-weight: bold; background-color: ' . escape_html($color) . '">' . escape_html($color) . '</div>';
}
echo '</div>';

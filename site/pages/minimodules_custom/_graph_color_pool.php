<?php

require_code('graphs');

$color_pool = array();
_generate_graph_color_pool($color_pool);

echo '<div class="float-surrounder">';
foreach ($color_pool as $color) {
    echo '<div style="text-align: center; box-sizing: border-box; padding-top: 35px; float: left; width: 100px; height: 100px; color: white; font-weight: bold; background-color: ' . escape_html($color) . '">' . escape_html($color) . '</div>';
}
echo '</div>';

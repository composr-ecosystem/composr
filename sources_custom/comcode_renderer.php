<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    comcode_html_whitelist
 */

if (!function_exists('init__comcode_renderer')) {
    function init__comcode_renderer($in = null)
    {
        $in = override_str_replace_exactly(
            "if ((isset(\$DANGEROUS_TAGS[\$tag])) && (!\$comcode_dangerous))",
            "if ((isset(\$DANGEROUS_TAGS[\$tag])) && (!\$comcode_dangerous) && (!comcode_white_listed(\$tag, \$marker, \$comcode)))",
            $in
        );

        return $in;
    }
}

function comcode_white_listed($tag, $marker, $comcode)
{
    $start_pos = strrpos(substr($comcode, 0, $marker), '[' . $tag);
    $end_pos = $marker - $start_pos;
    $comcode_portion_at_and_after = substr($comcode, $start_pos);
    $comcode_portion = substr($comcode_portion_at_and_after, 0, $end_pos);

    require_code('textfiles');
    static $whitelists = null;
    if ($whitelists === null) {
        $whitelists = explode("\n", read_text_file('comcode_whitelist'));
    }

    if (in_array($comcode_portion, $whitelists)) {
        return true;
    }
    foreach ($whitelists as $whitelist) {
        if ((substr($whitelist, 0, 1) == '/') && (substr($whitelist, -1) == '/') && (preg_match($whitelist, $comcode_portion) != 0)) {
            return true;
        }
    }

    return false;
}

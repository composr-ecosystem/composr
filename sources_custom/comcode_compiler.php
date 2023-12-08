<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    comcode_html_whitelist
 */

if (!function_exists('init__comcode_compiler')) {
    function init__comcode_compiler($in = null)
    {
        $in = override_str_replace_exactly(
            "if (\$as_admin)",
            "
            require_code('textfiles');
            static \$whitelists = null;
            if (\$whitelists === null) {
                \$whitelists = explode(\"\n\", read_text_file('comcode_whitelist', null, true));
            }
            foreach (\$whitelists as \$w) {
                if (trim(\$w) != '') {
                    if (\$w[0] != '/') {
                        \$w = preg_quote(\$w, '#');
                    } else {
                        \$w = substr(\$w, 1, strlen(\$w) - 2);
                    }
                    \$allowed_html_seqs[] = \$w;
                }
            }
            <ditto>
            ",
            $in
        );

        $in = override_str_replace_exactly(
            "hard_filter_input_data__html(\$ahead);",
            "
            global \$OBSCURE_REPLACEMENTS;
            \$OBSCURE_REPLACEMENTS = array();
            require_code('textfiles');
            static \$whitelists = null;
            if (\$whitelists === null) {
                \$whitelists = explode(\"\n\", read_text_file('comcode_whitelist', null, true));
            }
            foreach (\$whitelists as \$i => \$w) {
                if (trim(\$w) != '') {
                    if (\$w[0] != '/') {
                        \$w = preg_quote(\$w, '#');
                    } else {
                        \$w = substr(\$w, 1, strlen(\$w) - 2);
                    }
                    \$ahead = preg_replace_callback('#' . \$w . '#', 'obscure_html_callback', \$ahead);
                }
            }

            <ditto>
            ",
            $in
        );

        $in = override_str_replace_exactly(
            "// Tidy up",
            "
            require_code('input_filter');
            foreach (\$OBSCURE_REPLACEMENTS as \$rep => \$from) {
                \$ahead = str_replace(\$rep, \$from, \$ahead);
            }

            <ditto>
            ",
            $in
        );

        return $in;
    }
}

function obscure_html_callback($matches)
{
    global $OBSCURE_REPLACEMENTS;
    $rep = uniqid('', true);
    $OBSCURE_REPLACEMENTS[$rep] = $matches[0];
    return $rep;
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/*
For other language packs you can copy this file to the obvious new name. This is optional, providing code-based improvements to a pack.
*/

/**
 * Do filtering for the bundled English language pack.
 *
 * @package        language_TA
 */
class LangFilter_TA extends LangFilter_EN
{
    /**
     * Do a compile-time filter of the CSS code.
     *
     * @param  string $name The name of the CSS file
     * @param  string $code The CSS code
     * @return string The filtered CSS code
     */
    public function filter_css($name, $code)
    {
        // Tamil text is larger. We could try and force it down, but Chrome won't even let us due to its minimum size. We need things to be legible too.
        //$code = preg_replace_callback('#font-size: ([\d\.]+)px#', function($matches) { return 'font-size: ' . float_to_raw_string(floatval($matches[1]) - 2) . 'px'; }, $code);

        // Force consistent known font, as Tamil choice is less, and problems more, so we at least need consistency
        $code = str_replace('font-family: \'Open Sans\', \'Segoe UI\', Tahoma, Verdana, Arial, Helvetica, sans-serif;', 'font-family: Latha;', $code);
        $code = str_replace('font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;', 'font-family: Latha;', $code);
        $code = str_replace('font-family: Arial;', 'font-family: Arial;', $code);

        return $code;
    }
}

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    columns
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_columns
{
    public function compile_template(&$data, $template_name, $theme, $lang, $suffix, $directory)
    {
        if (($template_name != 'global') || (($suffix != '.js') && ($suffix != '.css'))) {
            return;
        }

        if (!addon_installed('columns')) {
            return;
        }

        switch ($suffix) {
            case '.css':
                $c = 'columns';
                $found = find_template_place($c, $theme, '.css', 'css');
                if ($found !== null) {
                    $full_path = get_custom_file_base() . '/themes/' . $found[0] . $found[1] . $c . $found[2];
                    $data .= cms_file_get_contents_safe($full_path);
                }
                break;

            case '.js':
                $j = 'columns';
                $found = find_template_place($j, $theme, '.js', 'javascript');
                if ($found !== null) {
                    $full_path = get_custom_file_base() . '/themes/' . $found[0] . $found[1] . $j . $found[2];
                    $data .= cms_file_get_contents_safe($full_path);
                }
                break;
        }
    }
}

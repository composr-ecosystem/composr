<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
            case '.js':
                $path = get_file_base() . '/themes/default/css_custom/columns.css';
                if (is_file($path)) {
                    $data .= cms_file_get_contents_safe($path);
                }
                break;

            case '.css':
                $path = get_file_base() . '/themes/default/javascript_custom/columns.js';
                if (is_file($path)) {
                    $data .= cms_file_get_contents_safe($path);
                }
                break;
        }
    }
}

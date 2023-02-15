<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_hybridauth
{
    public function compile_template(&$data, $template_name, $theme, $lang, $suffix, $directory)
    {
        if (($template_name != 'global') || ($suffix != '.css')) {
            return;
        }

        if (!addon_installed('hybridauth')) {
            return;
        }

        $c = 'hybridauth';
        $found = find_template_place($c, $theme, '.css', 'css');
        if ($found !== null) {
            $full_path = get_custom_file_base() . '/themes/' . $found[0] . $found[1] . $c . $found[2];
            $data .= cms_file_get_contents_safe($full_path);
        }
    }
}

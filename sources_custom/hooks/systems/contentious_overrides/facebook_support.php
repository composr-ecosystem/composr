<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_facebook_support
{
    public function compile_template(&$data, $template_name, $theme, $lang, $suffix, $directory)
    {
        if (($template_name != 'global') || ($suffix != '.js')) {
            return;
        }

        if (!addon_installed('facebook_support')) {
            return;
        }

        $j = 'facebook_support';
        $found = find_template_place($j, '', $theme, '.js', 'javascript');
        if ($found !== null) {
            $full_path = get_custom_file_base() . '/themes/' . $found[0] . $found[1] . $j . $found[2];
            $data .= cms_file_get_contents_safe($full_path);
        }
    }
}

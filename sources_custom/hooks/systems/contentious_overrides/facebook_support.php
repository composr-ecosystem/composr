<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_facebook_support
{
    public function compile_template(&$data, $template_name, $theme, $lang, $suffix, $directory)
    {
        if (addon_installed('facebook_support')) {
            if ((($template_name == 'global') && ($suffix == '.js'))) {
                $j = 'facebook_support';
                $found = find_template_place($j, $theme, '.js', 'javascript');
                if ($found !== null) {
                    $full_path = get_custom_file_base() . '/themes/' . $found[0] . $found[1] . $j . $found[2];
                    $data .= cms_file_get_contents_safe($full_path);
                }
            }

            if ((($template_name == 'GLOBAL_HTML_WRAP') && ($theme != 'admin') && ($suffix == '.tpl'))) {
                $data = override_str_replace_exactly(
                    '{$,extra_footer_right_goes_here}',
                    "<ditto>
                    {+START,INCLUDE,FACEBOOK_FOOTER}{+END}",
                    $data,
                    1,
                    true
                );
            }

            if ((($template_name == 'STANDALONE_HTML_WRAP') && ($suffix == '.tpl'))) {
                $data = override_str_replace_exactly(
                    '{$,extra_footer_standalone_goes_here}',
                    "<ditto>
                    {+START,INCLUDE,FACEBOOK_FOOTER}{+END}",
                    $data,
                    1,
                    true
                );
            }
        }
    }
}

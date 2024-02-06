<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_community_billboard
{
    public function compile_template(&$data, $template_name, $theme, $lang, $suffix, $directory)
    {
        if (($template_name != 'GLOBAL_HTML_WRAP') || ($theme == 'admin') || ($suffix != '.tpl')) {
            return;
        }

        if (!addon_installed('community_billboard')) {
            return;
        }

        $data = override_str_replace_exactly(
            '{$,extra_footer_right_goes_here}',
            "<ditto>
            {+START,INCLUDE,COMMUNITY_BILLBOARD_FOOTER}{+END}",
            $data
        );
    }
}

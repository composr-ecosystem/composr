<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class themeini_images_test_set extends cms_test_case
{
    public function testThemeImageThere()
    {
        global $THEMEWIZARD_IMAGES;

        require_code('themes2');
        require_code('themewizard');

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            load_themewizard_params_from_theme($theme);

            foreach ($THEMEWIZARD_IMAGES as $theme_image) {
                if (strpos($theme_image, '*') === false) {
                    $this->assertTrue(find_theme_image($theme_image, true) != '', 'Missing but referenced in theme.ini: ' . $theme_image);
                } else { // This code branch is assumptive (that the '*' goes on the end), but it works with the current theme.ini...
                    $x = str_replace('/*', '', $theme_image);
                    $there = is_dir(get_file_base() . '/themes/default/images/' . $x) || is_dir(get_file_base() . '/themes/default/images/EN/' . $x) || is_dir(get_file_base() . '/themes/' . $theme . '/images/' . $x) || is_dir(get_file_base() . '/themes/' . $theme . '/images/EN/' . $x);
                    $this->assertTrue($there, 'Possible error on this theme.ini image wildcard: ' . $theme_image);
                }
            }
        }
    }
}

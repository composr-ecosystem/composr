<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class js_strict_mode_test_set extends cms_test_case
{
    public function testInStrictMode()
    {
        $templates = [];
        $path = get_file_base() . '/themes/default/javascript';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (cms_strtolower_ascii(substr($file, -3)) == '.js') {
                if (in_array($file, [
                    '_attachment_ui_defaults.js',
                    'button_realtime_rain.js',
                    'skitter.js',
                    'jquery.js',
                    'webfontloader.js',
                    'jquery_autocomplete.js',
                    'jquery_ui.js',
                    'modernizr.js',
                    'widget_date.js',
                    '_wysiwyg_settings.js',
                    'xsl_mopup.js',
                    '_polyfill_web_animations.js',
                    'toastify.js',
                    'password_checks.js', // Not a standalone file
                ])) {
                    continue;
                }

                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_BOM);

                $this->assertTrue(strpos($c, 'use strict') !== false, 'Strict mode not enabled for ' . $file);
            }
        }
        closedir($dh);
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

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
            if (strtolower(substr($file, -3)) == '.js') {
                if (in_array($file, [
                    'ATTACHMENT_UI_DEFAULTS.js',
                    'button_realtime_rain.js',
                    'jquery.js',
                    'jquery_autocomplete.js',
                    'jquery_ui.js',
                    'modernizr.js',
                    'widget_date.js',
                    'WYSIWYG_SETTINGS.js',
                    'xsl_mopup.js',
                    'POLYFILL_WEB_ANIMATIONS.js',
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

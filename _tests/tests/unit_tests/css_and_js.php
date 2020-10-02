<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// E.g. http://localhost/composr/_tests/?id=unit_tests%2Fcss_and_js&close_if_passed=1&debug=1&keep_no_minify=1&only=checking.js

/**
 * Composr test case class (unit testing).
 */
class css_and_js_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        disable_php_memory_limit();

        $_GET['keep_no_minify'] = '1';

        require_code('webstandards');
        require_code('webstandards2');
        require_lang('webstandards');
        require_code('themes2');

        global $WEBSTANDARDS_JAVASCRIPT, $WEBSTANDARDS_CSS, $WEBSTANDARDS_WCAG, $WEBSTANDARDS_COMPAT, $WEBSTANDARDS_EXT_FILES, $WEBSTANDARDS_MANUAL, $MAIL_MODE;
        $WEBSTANDARDS_JAVASCRIPT = true;
        $WEBSTANDARDS_CSS = true;
        $WEBSTANDARDS_WCAG = true;
        $WEBSTANDARDS_COMPAT = false;
        $WEBSTANDARDS_EXT_FILES = true;
        $WEBSTANDARDS_MANUAL = false;
        $MAIL_MODE = false;
    }

    public function testJavaScript()
    {
        require_code('webstandards_js_lint');

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            foreach (array('javascript', 'javascript_custom') as $dir) {
                $this->javaScriptTestForTheme($theme, $dir);
            }
        }
    }

    protected function javaScriptTestForTheme($theme, $dir)
    {
        $exceptions = array(
            // Won't parse
            'jwplayer.js',
            'jquery_ui.js',
            'modernizr.js',
            'plupload.js',

            // Third-party code not confirming to Composr standards
            'widget_color.js',
            'widget_date.js',
            'jquery.js',
            'sortable_tables.js',
            'openid.js',
            'unslider.js',
            'tag_cloud.js',
            'mediaelement-and-player.js',
            'sound.js',
            'base64.js',

            // Partial code that will give errors
            'ATTACHMENT_UI_DEFAULTS.js',
        );

        $only = get_param_string('only', null);
        if (($only !== null) && (in_array($only, $exceptions))) {
            unset($exceptions[array_search($only, $exceptions)]);
        }

        $dh = opendir(get_file_base() . '/themes/' . $theme . '/' . $dir);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -3) == '.js') {
                if (in_array($f, $exceptions)) {
                    continue;
                }

                if ($only !== null) {
                    if ($f != $only) {
                        continue;
                    }
                }

                $path = javascript_enforce(basename($f, '.js'), $theme);
                if ($path == '') { // Compiled to empty
                    continue;
                }
                $contents = file_get_contents($path);
                $errors = check_js($contents);
                if (!is_null($errors)) {
                    foreach ($errors['errors'] as $i => $e) {
                        $e['line'] += 3;
                        $errors['errors'][$i] = $e;
                    }
                }
                if ((!is_null($errors)) && ($errors['errors'] == array())) {
                    $errors = null; // Normalise
                }
                $this->assertTrue(is_null($errors), 'Bad JS in ' . $f);
                if (!is_null($errors)) {
                    if (get_param_integer('debug', 0) == 1) {
                        unset($errors['tag_ranges']);
                        unset($errors['value_ranges']);
                        unset($errors['level_ranges']);
                        echo '<pre>';
                        var_dump($errors['errors']);
                        echo '</pre>';
                    }
                }
            }
        }
    }

    public function testCSS()
    {
        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            foreach (array('css', 'css_custom') as $dir) {
                $this->cssTestForTheme($theme, $dir);
            }
        }
    }

    protected function cssTestForTheme($theme, $dir)
    {
        $exceptions = array(
            // Third-party code not confirming to Composr standards
            'widget_color.css',
            'widget_select2.css',
            'unslider.css',
            'skitter.css',
            'mediaelementplayer.css',
            'jquery_ui.css',
        );

        $only = get_param_string('only', null);
        if (($only !== null) && (in_array($only, $exceptions))) {
            unset($exceptions[array_search($only, $exceptions)]);
        }

        $dh = opendir(get_file_base() . '/themes/' . $theme . '/' . $dir);
        while (($f = readdir($dh)) !== false) {
            if ((substr($f, -4) == '.css') && ($f != 'svg.css'/*SVG-CSS*/) && ($f != 'no_cache.css')) {
                if (in_array($f, $exceptions)) {
                    continue;
                }

                $path = css_enforce(basename($f, '.css'), $theme);
                if ($path == '') {
                    continue; // Nothing in file after minimisation
                }

                if ($only !== null) {
                    if ($f != $only) {
                        continue;
                    }
                }

                $contents = file_get_contents($path);
                $errors = check_css($contents);
                if ((!is_null($errors)) && ($errors['errors'] == array())) {
                    $errors = null; // Normalise
                }
                $this->assertTrue(is_null($errors), 'Bad CSS in ' . $f);
                if (!is_null($errors)) {
                    if (get_param_integer('debug', 0) == 1) {
                        echo '<pre>';
                        var_dump($errors['errors']);
                        echo '</pre>';
                    }
                }
            }
        }
    }
}

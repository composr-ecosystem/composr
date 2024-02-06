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

// E.g. http://localhost/composr/_tests/?id=unit_tests%2Fweb_resources&close_if_passed=1&debug=1&keep_minify=0&only=checking.js

/**
 * Composr test case class (unit testing).
 */
class _web_resources_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL);

        $_GET['keep_minify'] = '0'; // Doesn't seem to actually work due to internal caching

        disable_php_memory_limit();

        require_code('webstandards');
        require_code('webstandards2');
        require_lang('webstandards');
        require_code('themes2');
        require_code('files2');

        global $WEBSTANDARDS_JAVASCRIPT, $WEBSTANDARDS_CSS, $WEBSTANDARDS_WCAG, $WEBSTANDARDS_COMPAT, $WEBSTANDARDS_EXT_FILES, $WEBSTANDARDS_MANUAL;
        $WEBSTANDARDS_JAVASCRIPT = true;
        $WEBSTANDARDS_CSS = true;
        $WEBSTANDARDS_WCAG = true;
        $WEBSTANDARDS_COMPAT = false;
        $WEBSTANDARDS_EXT_FILES = true;
        $WEBSTANDARDS_MANUAL = false;
    }

    public function testJavaScript()
    {
        require_code('webstandards_js_lint');

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            foreach (['javascript', 'javascript_custom'] as $dir) {
                $this->javascript_test_for_theme($theme, $dir);
            }
        }
    }

    protected function javascript_test_for_theme($theme, $dir)
    {
        $exceptions = [
            // Won't parse
            'jquery_ui.js',
            'modernizr.js',
            'plupload.js',
            'confluence.js',
            'confluence2.js',
            'masonry.js',
            'glide.js',

            // Third-party code not confirming to Composr standards
            'widget_color.js',
            'widget_date.js',
            'select2.js',
            'skitter.js',
            'cookie_consent.js',
            'columns.js',
            'jquery.js',
            'webfontloader.js',
            'sortable_tables.js',
            'unslider.js',
            'charts.js',
            'tag_cloud.js',
            'mediaelement-and-player.js',
            'sound.js',
            'global.js', // Due to including polyfills (included files will be checked separately though)
            '_json5.js',
            '_polyfill_fetch.js',
            '_polyfill_web_animations.js',
            'toastify.js',

            // Partial code that will give errors
            '_attachment_ui_defaults.js',
        ];

        if (($this->only !== null) && (in_array($this->only, $exceptions))) {
            unset($exceptions[array_search($this->only, $exceptions)]);
        }

        $files = get_directory_contents(get_file_base() . '/themes/' . $theme . '/' . $dir, get_file_base() . '/themes/' . $theme . '/' . $dir, null, false, true, ['js']);
        foreach ($files as $path) {
            if (in_array(basename($path), $exceptions)) {
                continue;
            }

            if ($this->only !== null) {
                if (basename($path) != $this->only) {
                    continue;
                }
            }

            $path_compiled = javascript_enforce(basename($path, '.js'), $theme);
            if ($path_compiled == '') {
                continue; // Empty file, so skipped
            }

            $c = cms_file_get_contents_safe($path_compiled, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
            $errors = check_js($c);
            if ($errors !== null) {
                foreach ($errors['errors'] as $i => $e) {
                    $e['line'] += 3;
                    $errors['errors'][$i] = $e;
                }
            }
            if (($errors !== null) && (empty($errors['errors']))) {
                $errors = null; // Normalise
            }
            $this->assertTrue(($errors === null), 'Bad JS in ' . $path . (($this->only === null) ? (' (run with &only=' . basename($path) . '&debug=1&keep_minify=0 to see errors)') : ''));
            if ($errors !== null) {
                if ($this->debug) {
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

    public function testCSS()
    {
        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            foreach (['css', 'css_custom'] as $dir) {
                $this->css_test_for_theme($theme, $dir);
            }
        }
    }

    protected function css_test_for_theme($theme, $dir)
    {
        $exceptions = [
            'no_cache.css',
            'svg.css', // SVG-CSS

            // Third-party code not confirming to Composr standards
            'widget_color.css',
            'widget_date.css',
            'widget_select2.css',
            'unslider.css',
            'skitter.css',
            'mediaelementplayer.css',
            'jquery_ui.css',
            'confluence.css',
            'widget_glide.css',
            'toastify.css',
        ];

        if (($this->only !== null) && (in_array($this->only, $exceptions))) {
            unset($exceptions[array_search($this->only, $exceptions)]);
        }

        $files = get_directory_contents(get_file_base() . '/themes/' . $theme . '/' . $dir, get_file_base() . '/themes/' . $theme . '/' . $dir, null, false, true, ['css']);
        foreach ($files as $path) {
            if (in_array(basename($path), $exceptions)) {
                continue;
            }

            $filename = basename($path, '.css');
            if ($filename[0] == '_') {
                continue;
            }

            $path = css_enforce($filename, $theme);
            if ($path == '') {
                continue; // Nothing in file after minimisation
            }

            if ($this->only !== null) {
                if (basename($path) != $this->only) {
                    continue;
                }
            }

            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
            $errors = check_css($c);
            if (($errors !== null) && (empty($errors['errors']))) {
                $errors = null; // Normalise
            }
            $this->assertTrue(($errors === null), 'Bad CSS in ' . $path . (($this->only === null) ? (' (run with &only=' . basename($path) . '&debug=1 to see errors)') : ''));
            if ($errors !== null) {
                if ($this->debug) {
                    echo '<pre>';
                    var_dump($errors['errors']);
                    echo '</pre>';
                }
            }
        }
    }
}

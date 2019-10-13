<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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
class web_resources_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND_crawl);

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
            if ($theme == '_unnamed_') {
                continue;
            }

            foreach (array('javascript', 'javascript_custom') as $dir) {
                $this->javascript_test_for_theme($theme, $dir);
            }
        }
    }

    protected function javascript_test_for_theme($theme, $dir)
    {
        $exceptions = array(
            // Won't parse
            'jquery_ui.js',
            'modernizr.js',
            'plupload.js',
            'base64.js',
            'confluence.js',
            'confluence2.js',

            // Third-party code not confirming to Composr standards
            'widget_color.js',
            'widget_date.js',
            'jquery.js',
            'sortable_tables.js',
            'unslider.js',
            'charts.js',
            'tag_cloud.js',
            'mediaelement-and-player.js',
            'sound.js',
            'base64.js',
            'global.js', // Due to including polyfills (included files will be checked separately though)
            'JSON5.js',
            'POLYFILL_FETCH.js',
            'POLYFILL_WEB_ANIMATIONS.js',

            // Partial code that will give errors
            'ATTACHMENT_UI_DEFAULTS.js',
        );

        if (($this->only !== null) && (in_array($this->only, $exceptions))) {
            unset($exceptions[array_search($this->only, $exceptions)]);
        }

        $files = get_directory_contents(get_file_base() . '/themes/' . $theme . '/' . $dir, get_file_base() . '/themes/' . $theme . '/' . $dir, null, false, true, array('js'));
        foreach ($files as $path) {
            if (in_array(basename($path), $exceptions)) {
                continue;
            }

            if ($this->only !== null) {
                if (basename($path) != $this->only) {
                    continue;
                }
            }

            $path = javascript_enforce(basename($path, '.js'), $theme);
            if ($path == '') {
                continue; // Empty file, so skipped
            }

            $c = cms_file_get_contents_safe($path); // TODO #3467
            $errors = check_js($c);
            if ($errors !== null) {
                foreach ($errors['errors'] as $i => $e) {
                    $e['line'] += 3;
                    $errors['errors'][$i] = $e;
                }
            }
            if (($errors !== null) && ($errors['errors'] == array())) {
                $errors = null; // Normalise
            }
            $this->assertTrue(($errors === null), 'Bad JS in ' . $path);
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
            if ($theme == '_unnamed_') {
                continue;
            }

            foreach (array('css', 'css_custom') as $dir) {
                $this->css_test_for_theme($theme, $dir);
            }
        }
    }

    protected function css_test_for_theme($theme, $dir)
    {
        $exceptions = array(
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
        );

        if (($this->only !== null) && (in_array($this->only, $exceptions))) {
            unset($exceptions[array_search($this->only, $exceptions)]);
        }

        $files = get_directory_contents(get_file_base() . '/themes/' . $theme . '/' . $dir, get_file_base() . '/themes/' . $theme . '/' . $dir, null, false, true, array('css'));
        foreach ($files as $path) {
            if (in_array(basename($path), $exceptions)) {
                continue;
            }

            $path = css_enforce(basename($path, '.css'), $theme);
            if ($path == '') {
                continue; // Nothing in file after minimisation
            }

            if ($this->only !== null) {
                if (basename($path) != $this->only) {
                    continue;
                }
            }

            $c = cms_file_get_contents_safe($path); // TODO #3467
            $errors = check_css($c);
            if (($errors !== null) && ($errors['errors'] == array())) {
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

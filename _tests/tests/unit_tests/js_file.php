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
class js_file_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('files2');
    }

    public function testTemplateReferences()
    {
        $templates_used_js = $this->find_templates_used_in_js();
        $templates_defined_html = $this->find_templates_used_in_html();

        $_templates_used_js = array_flip($templates_used_js);
        $_templates_defined_html = array_flip($templates_defined_html);

        foreach ($templates_used_js as $template) {
            // Exceptions
            if (in_array($template, [
                'miniblockMainCalculator',
            ])) {
                continue;
            }

            $this->assertTrue(isset($_templates_defined_html[$template]), 'Template used in JavaScript but not present in HTML: ' . $template);
        }

        foreach ($templates_defined_html as $template) {
            // Exceptions
            if (in_array($template, [])) {
                continue;
            }

            $this->assertTrue(isset($_templates_used_js[$template]), 'Template used in HTML but not present in JavaScript: ' . $template);
        }
    }

    protected function find_templates_used_in_html()
    {
        static $cache = null;
        if (isset($cache)) {
            return $cache;
        }

        $templates = [];

        $directories = [
             get_file_base() . '/themes/default/templates_custom',
             get_file_base() . '/themes/default/templates',
        ];

        foreach ($directories as $dir) {
            $d = @opendir($dir);
            if ($d !== false) {
                while (($e = readdir($d)) !== false) {
                    if (substr($e, -4) == '.tpl') {
                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
                        $matches = [];
                        $found = preg_match_all('#data-tpl="([^"]*)"#', $c, $matches);
                        for ($i = 0; $i < $found; $i++) {
                            $templates[] = $matches[1][$i];
                        }
                    }
                }
                closedir($d);
            }
        }

        $cache = array_unique($templates);

        return $cache;
    }

    protected function find_templates_used_in_js()
    {
        static $cache = null;
        if (isset($cache)) {
            return $cache;
        }

        // Find selectors used in JavaScript...

        $directories = [
             get_file_base() . '/themes/default/javascript_custom',
             get_file_base() . '/themes/default/javascript',
        ];

        $templates = [];

        foreach ($directories as $dir) {
            $d = @opendir($dir);
            if ($d !== false) {
                while (($e = readdir($d)) !== false) {
                    if (substr($e, -3) == '.js') {
                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
                        $matches = [];
                        $found = preg_match_all('#\$cms.templates.(\w+)\s*=#', $c, $matches);
                        for ($i = 0; $i < $found; $i++) {
                            $templates[] = $matches[1][$i];
                        }
                    }
                }
                closedir($d);
            }
        }

        $cache = array_unique($templates);

        return $cache;
    }

    public function testViewReferences()
    {
        $views_used_js = $this->find_views_used_in_js();
        $views_defined_html = $this->find_views_used_in_html();

        $_views_used_js = array_flip($views_used_js);
        $_views_defined_html = array_flip($views_defined_html);

        foreach ($views_used_js as $view) {
            // Exceptions
            if (in_array($view, [
                'TreeList',
                'ToggleableTray',
                'ModalWindow',
            ])) {
                continue;
            }

            $this->assertTrue(isset($_views_defined_html[$view]), 'View used in JavaScript but not present in HTML: ' . $view);
        }

        foreach ($views_defined_html as $view) {
            // Exceptions
            if (in_array($view, [])) {
                continue;
            }

            $this->assertTrue(isset($_views_used_js[$view]), 'View used in HTML but not present in JavaScript: ' . $view);
        }
    }

    protected function find_views_used_in_html()
    {
        static $cache = null;
        if (isset($cache)) {
            return $cache;
        }

        $views = [];

        $directories = [
             get_file_base() . '/themes/default/templates_custom',
             get_file_base() . '/themes/default/templates',
        ];

        foreach ($directories as $dir) {
            $d = @opendir($dir);
            if ($d !== false) {
                while (($e = readdir($d)) !== false) {
                    if (substr($e, -4) == '.tpl') {
                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
                        $matches = [];
                        $found = preg_match_all('#data-view="([^"]*)"#', $c, $matches);
                        for ($i = 0; $i < $found; $i++) {
                            $views[] = $matches[1][$i];
                        }
                    }
                }
                closedir($d);
            }
        }

        $cache = array_unique($views);

        return $cache;
    }

    protected function find_views_used_in_js()
    {
        static $cache = null;
        if (isset($cache)) {
            return $cache;
        }

        // Find selectors used in JavaScript...

        $directories = [
             get_file_base() . '/themes/default/javascript_custom',
             get_file_base() . '/themes/default/javascript',
        ];

        $views = [];

        foreach ($directories as $dir) {
            $d = @opendir($dir);
            if ($d !== false) {
                while (($e = readdir($d)) !== false) {
                    if (substr($e, -3) == '.js') {
                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
                        $matches = [];
                        $found = preg_match_all('#\$cms.views.(\w+)\s*=#', $c, $matches);
                        for ($i = 0; $i < $found; $i++) {
                            $views[] = $matches[1][$i];
                        }
                    }
                }
                closedir($d);
            }
        }

        $cache = array_unique($views);

        return $cache;
    }

    public function testClassReferences()
    {
        $classes_used_js = $this->find_classes_used_in_js();
        $classes_defined_html = $this->find_classes_used_in_html();
        $classes_defined_css = $this->find_classes_used_in_css();

        $_classes_used_js = array_flip($classes_used_js);
        $_classes_defined_html = array_flip($classes_defined_html);
        $_classes_defined_css = array_flip($classes_defined_css);

        foreach ($classes_used_js as $class) {
            // Exceptions
            if (in_array($class, [
                'tooltip',
                'js-img-review-bar',
                'js-no-theme-img-click',
                'js-keyup-sortable-table-filter-input',
                'js-btn-click-calculate-sum',
                'js-icon-checklist-status',
                'is-active',
                'is-popup-open',
                'is-expanded',
                'js-onclick-do-option-yes',
                'js-onclick-do-option-no',
                'js-onclick-do-option-cancel',
                'js-onclick-do-option-finished',
                'js-onclick-do-option-left',
                'js-onclick-do-option-right',
                'gsc-search-box',
                'file-changed',
                'progress-container',
                'progress-name',
                'commandr-img',
                'key-to-delete',
                'pagination-load-more',
                'ajax-tree-expand-icon',
                'js-click-open-images-into-lightbox',
                'ajax-loading-block',
                'js-key-to-delete',
                'js-change-update-chooser',
                'js-mouseover-show-permission-setting',
            ])) {
                continue;
            }

            $this->assertTrue(isset($_classes_defined_html[$class]), 'Class used in JavaScript but not present in HTML: ' . $class);
        }

        foreach ($classes_used_js as $class) {
            // Exceptions
            if (in_array($class, [
                'menu-editor-page-inner',
                'is-active',
                'is-popup-open',
                'is-expanded',
                'is-current',
                'gsc-search-box',
                'commandr-img',
                'is-sticky',
            ])) {
                continue;
            }

            if (substr($class, 0, 3) != 'js-') {
                $this->assertTrue(isset($_classes_defined_css[$class]), 'Class used in JavaScript without js- prefix but not used in CSS, breaking conventions: ' . $class);
            }
        }

        foreach ($classes_defined_html as $class) {
            // Exceptions
            if (in_array($class, [
                'js-comcode-button-',
                'js-click-confirm-warning',
                'js-delete-photo',
            ])) {
                continue;
            }

            if (substr($class, 0, 3) == 'js-') {
                $this->assertTrue(isset($_classes_used_js[$class]), 'js- prefixed class used in HTML but not present in JavaScript: ' . $class);
            }
        }

        foreach ($_classes_defined_css as $class) {
            // Exceptions
            if (in_array($class, [])) {
                continue;
            }

            if (substr($class, 0, 3) == 'js-') {
                $this->assertTrue(false, 'js- prefixed class used in CSS: ' . $class);
            }
        }
    }

    protected function find_classes_used_in_css()
    {
        static $cache = null;
        if (isset($cache)) {
            return $cache;
        }

        $classes = [];

        $directories = [
             get_file_base() . '/themes/default/css_custom',
             get_file_base() . '/themes/default/css',
        ];

        foreach ($directories as $dir) {
            $d = @opendir($dir);
            if ($d !== false) {
                while (($e = readdir($d)) !== false) {
                    if (substr($e, -4) == '.css') {
                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                        $matches_selector_lines = [];
                        $num_selector_lines = preg_match_all('#.*(\{|,\s*)#', $c, $matches_selector_lines);
                        for ($i = 0; $i < $num_selector_lines; $i++) {
                            $matches = [];
                            $found = preg_match_all('#\.([a-z][\w\-]*)[ ,:.]#i', $matches_selector_lines[0][$i], $matches);
                            for ($j = 0; $j < $found; $j++) {
                                $classes[] = $matches[1][$j];
                            }
                        }
                    }
                }
                closedir($d);
            }
        }

        $cache = array_unique($classes);

        return $cache;
    }

    protected function find_classes_used_in_html()
    {
        static $cache = null;
        if (isset($cache)) {
            return $cache;
        }

        $classes = [];

        $directories = [
             get_file_base() . '/themes/default/templates_custom',
             get_file_base() . '/themes/default/templates',
        ];

        foreach ($directories as $dir) {
            $d = @opendir($dir);
            if ($d !== false) {
                while (($e = readdir($d)) !== false) {
                    if (substr($e, -4) == '.tpl') {
                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
                        $matches = [];
                        $found = preg_match_all('#class="([^"]*)"#', $c, $matches);
                        for ($i = 0; $i < $found; $i++) {
                            $matches2 = [];
                            $class_property = preg_replace('#\{.*\}#U', '', $matches[1][$i]);
                            $num_matches2 = preg_match_all('#[\w\-]+#', $class_property, $matches2);
                            for ($j = 0; $j < $num_matches2; $j++) {
                                $classes[] = $matches2[0][$j];
                            }
                        }
                    }
                }
                closedir($d);
            }
        }

        $cache = array_unique($classes);

        return $cache;
    }

    protected function find_classes_used_in_js()
    {
        static $cache = null;
        if (isset($cache)) {
            return $cache;
        }

        // Find selectors used in JavaScript...

        $directories = [
             get_file_base() . '/themes/default/javascript_custom',
             get_file_base() . '/themes/default/javascript',
        ];

        $classes = [];

        foreach ($directories as $dir) {
            $d = @opendir($dir);
            if ($d !== false) {
                while (($e = readdir($d)) !== false) {
                    if (substr($e, -3) == '.js') {
                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                        $patterns = [
                            '\$dom\.on\(\w+, \'[\w ]+\', \'([^\']+)\'',
                            '\$dom\.\$\(\'([^\']+)\'',
                            '\$dom\.\$\$\(\'([^\']+)\'',
                            '\$dom\.\$\$\$\(\'([^\']+)\'',
                            'this\.\$\(\'([^\']+)\'',
                            'this\.\$\$\(\'([^\']+)\'',
                            'this\.\$\$\$\(\'([^\']+)\'',
                            '\$dom\.\$\(\w+,\s*\'([^\']+)\'',
                            '\$dom\.\$\$\(\w+,\s*\'([^\']+)\'',
                            '\$dom\.\$\$\$\(\w+,\s*\'([^\']+)\'',
                            'this\.\$\(\w+,\s*\'([^\']+)\'',
                            'this\.\$\$\(\w+,\s*\'([^\']+)\'',
                            'this\.\$\$\$\(\w+,\s*\'([^\']+)\'',
                            '\.querySelector\(\'([^\']+)\'',
                        ];
                        foreach ($patterns as $pattern) {
                            $matches = [];
                            $num_matches = preg_match_all('#' . $pattern . '#', $c, $matches);
                            for ($i = 0; $i < $num_matches; $i++) {
                                $selector = $matches[1][$i];
                                $new_classes = $this->convert_selector_to_classes($selector);
                                $classes = array_merge($classes, $new_classes);
                            }
                        }

                        $events_pattern = 'events: function\s*(\w+\s*)?\(\)\s*\{\s*return \{((.|\n)*?)\};\s*\}';
                        $matches = [];
                        $num_matches = preg_match_all('#' . $events_pattern . '#', $c, $matches);
                        for ($i = 0; $i < $num_matches; $i++) {
                            $inner = $matches[2][$i];

                            $events_pattern_pattern = '\'\w+ ([^\']+)\': \'\w+\'';
                            $matches2 = [];
                            $num_matches2 = preg_match_all('#' . $events_pattern_pattern . '#', $inner, $matches2);
                            for ($j = 0; $j < $num_matches2; $j++) {
                                $selector = $matches2[1][$j];
                                $new_classes = $this->convert_selector_to_classes($selector);
                                $classes = array_merge($classes, $new_classes);
                            }
                        }
                    }
                }
                closedir($d);
            }
        }

        $cache = array_unique($classes);

        return $cache;
    }

    protected function convert_selector_to_classes($selector)
    {
        $classes = [];

        $matches = [];
        $num_matches = preg_match_all('#\.([a-z][\w\-]*)#i', $selector, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $classes[] = $matches[1][$i];
        }

        return $classes;
    }
}

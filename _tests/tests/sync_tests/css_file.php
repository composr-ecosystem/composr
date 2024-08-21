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

// Pass &debug=1 for extra checks that would not be expected to ever consistently pass

/**
 * Composr test case class (unit testing).
 */
class css_file_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('themes2');
        require_code('images');
        require_code('files2');
    }

    public function testBasicCSSIntegrity()
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

            if (($this->only !== null) && ($this->only != $theme)) {
                continue;
            }

            $dir = get_file_base() . '/themes/' . $theme . '/css_custom';
            $d = @opendir($dir);
            if ($d !== false) {
                while (($f = readdir($d)) !== false) {
                    if (substr($f, -4) == '.css') {
                        // Exceptions
                        $exceptions = [
                        ];
                        if (in_array($f, $exceptions)) {
                            continue;
                        }

                        $c = cms_file_get_contents_safe($dir . '/' . $f, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                        // Test comment/brace balancing
                        if (substr_count($c, '{') != substr_count($c, '}')) {
                            echo '<br />Mismatched braces in ' . escape_html($f);
                        }
                        if (substr_count($c, '/*') != substr_count($c, '*/')) {
                            echo '<br />Mismatched comments in ' . escape_html($f);
                        }
                    }
                }
                closedir($d);
            }
        }
    }

    public function testClassUsage()
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

            if (($this->only !== null) && ($this->only != $theme)) {
                continue;
            }

            $_classes_defined = array_merge($this->find_classes_defined('default'), $this->find_classes_defined($theme));
            sort($_classes_defined);
            $classes_defined = array_flip($_classes_defined);

            $_classes_used = array_merge($this->find_classes_used('default'), $this->find_classes_used($theme));
            sort($_classes_used);
            $classes_used = array_flip($_classes_used);

            if ($this->debug) {
                foreach (array_keys($classes_used) as $class) {
                    // Exceptions
                    if (strpos($class, 'box___') !== false) {
                        continue;
                    }

                    $this->assertTrue(isset($classes_defined[$class]), 'CSS class used but not defined: ' . $class . ' (for theme: ' . $theme . ')');
                }
            } else {
                $message = 'Pass in debug for more aggressive testing which may include false-positives.';
                $this->dump($message, 'INFO:');
            }

            foreach (array_keys($classes_defined) as $class) {
                if ($this->is_class_defined_and_intentionally_not_explicitly_used($class)) {
                    continue;
                }

                $this->assertTrue(isset($classes_used[$class]), 'CSS class defined but not used: ' . $class . ' (for theme: ' . $theme . ')');
            }
        }
    }

    protected function find_classes_defined($theme)
    {
        static $cache = [];
        if (isset($cache[$theme])) {
            return $cache[$theme];
        }

        $out = [];

        $directories = [
             get_file_base() . '/themes/' . $theme . '/css_custom',
             get_file_base() . '/themes/' . $theme . '/css',
        ];

        foreach ($directories as $dir) {
            $d = @opendir($dir);
            if ($d !== false) {
                while (($e = readdir($d)) !== false) {
                    if (substr($e, -4) == '.css') {
                        // Exceptions
                        $exceptions = [
                            'svg.css',
                            'confluence.css',
                            'mediaelementplayer.css',
                        ];
                        if (in_array($e, $exceptions)) {
                            continue;
                        }

                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                        $matches_selector_lines = [];
                        $num_selector_lines = preg_match_all('#.*(\{|,\s*)#', $c, $matches_selector_lines);
                        for ($i = 0; $i < $num_selector_lines; $i++) {
                            $matches = [];
                            $classes = [];
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

        $cache[$theme] = array_unique($out);
        return $cache[$theme];
    }

    protected function find_classes_used($theme)
    {
        static $cache = [];
        if (isset($cache[$theme])) {
            return $cache[$theme];
        }
        $out = [];

        $directories = [
             get_file_base() . '/themes/' . $theme . '/templates_custom',
             get_file_base() . '/themes/' . $theme . '/templates',
             get_file_base() . '/themes/' . $theme . '/javascript_custom',
             get_file_base() . '/themes/' . $theme . '/javascript',
        ];

        foreach ($directories as $dir) {
            $d = @opendir($dir);
            if ($d !== false) {
                while (($e = readdir($d)) !== false) {
                    if (substr($e, -4) == '.tpl' || substr($e, -3) == '.js') {
                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
                        $matches = [];
                        $found = preg_match_all('#class="([\w\- ]+)"#', $c, $matches);
                        for ($i = 0; $i < $found; $i++) {
                            $out = array_merge($out, explode(' ', $matches[1][$i]));
                        }
                    }
                }
                closedir($d);
            }
        }

        $cache[$theme] = array_unique($out);
        return $cache[$theme];
    }

    public function testSelectorUsage()
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

            if (($this->only !== null) && ($this->only != $theme)) {
                continue;
            }

            $directories = [
                 get_file_base() . '/themes/default/css_custom' => ($theme == 'default'),
                 get_file_base() . '/themes/default/css' => ($theme == 'default'),
                 get_file_base() . '/themes/default/templates_custom' => ($theme == 'default'),
                 get_file_base() . '/themes/default/templates' => ($theme == 'default'),
                 get_file_base() . '/themes/default/javascript_custom' => ($theme == 'default'),
                 get_file_base() . '/themes/default/javascript' => ($theme == 'default'),
            ];
            if ($theme != 'default') {
                $directories = array_merge($directories, [
                    get_file_base() . '/themes/' . $theme . '/css_custom' => true,
                    get_file_base() . '/themes/' . $theme . '/css' => true,
                    get_file_base() . '/themes/' . $theme . '/templates_custom' => true,
                    get_file_base() . '/themes/' . $theme . '/templates' => true,
                    get_file_base() . '/themes/' . $theme . '/javascript_custom' => true,
                    get_file_base() . '/themes/' . $theme . '/javascript' => true,
                ]);
            }

            $non_css_contents = '';
            $selector_files = [];

            foreach ($directories as $dir => $to_use) {
                $dh = @opendir($dir);
                if ($dh !== false) {
                    while (($file = readdir($dh)) !== false) {
                        // Exceptions
                        $exceptions = [
                            '.',
                            '..',
                            'columns.css',
                            'google_search.css',
                            'mediaelementplayer.css',
                            'skitter.css',
                            'svg.css',
                            'widget_color.css',
                            'widget_select2.css',
                            'widget_glide.css',
                            'confluence.css',
                        ];
                        if (in_array($file, $exceptions)) {
                            continue;
                        }

                        $c = cms_file_get_contents_safe($dir . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                        $is_css_file = (substr($file, -4) == '.css');

                        if ($is_css_file) {
                            if (!$to_use) {
                                continue;
                            }

                            // Let's do a few simple CSS checks, less than a proper validator would do
                            if (($is_css_file) && (strpos($c, '{$,parser hint: pure}') === false)) {
                                // Test comment/brace balancing
                                $a = substr_count($c, '{');
                                $b = substr_count($c, '}');
                                $this->assertTrue($a == $b, 'Mismatched braces in ' . $file . ' in ' . $theme . ', ' . integer_format($a) . ' vs ' . integer_format($b));
                                $a = substr_count($c, '/*');
                                $b = substr_count($c, '*/');
                                $this->assertTrue($a == $b, 'Mismatched comments in ' . $file . ' in ' . $theme . ', ' . integer_format($a) . ' vs ' . integer_format($b));

                                // Strip comments
                                $c = preg_replace('#/\*.*\*/#s', '', $c);

                                // Test selectors
                                $matches = [];
                                $num_matches = preg_match_all('#^\s*[^@\s].*[^%\s]\s*\{$#m', $c, $matches); // Finds selectors. However NB: @ is media rules, % is keyframe rules, neither are selectors.
                                for ($i = 0; $i < $num_matches; $i++) {
                                    $matches2 = [];
                                    $current = $matches[0][$i];
                                    $current = /*strip CSS syntax*/preg_replace('#[:@][\w\-]+#', '', $current);
                                    $current = /*strip quotes*/preg_replace('#"[^"]*"#', '', $current);
                                    $current = /*strip bracketed section*/preg_replace('#\([^\(\)]*\)#', '', $current);
                                    $num_matches2 = /*find class/ID words*/preg_match_all('#[\w\-]+#', $current, $matches2);
                                    for ($j = 0; $j < $num_matches2; $j++) {
                                        if (!isset($selector_files[$file])) {
                                            $selector_files[$file] = [];
                                        }
                                        $selector_files[$file][$matches2[0][$j]] = true;
                                    }
                                }
                            }
                        } else {
                            $non_css_contents .= $c;
                        }
                    }
                    closedir($dh);
                }
            }

            foreach ($selector_files as $file => $selectors) {
                ksort($selectors);
                foreach (array_keys($selectors) as $selector) {
                    // Exceptions
                    if ($this->is_class_defined_and_intentionally_not_explicitly_used($selector)) {
                        continue;
                    }
                    if ($this->is_id_defined_and_intentionally_not_explicitly_used($selector)) {
                        continue;
                    }

                    $this->assertTrue(strpos($non_css_contents, $selector) !== false, 'Possibly unused CSS selector for theme ' . $theme . ', ' . $file . ': ' . $selector);
                }
            }
        }
    }

    protected function is_class_defined_and_intentionally_not_explicitly_used($class)
    {
        $prefix_exceptions = [
            'btn-\w+-login',
            'zone-running-',
            'page-running-',
            'has-header-', // Dynamically set on <html> in GLOBAL_HTML_WRAP.tpl
            'menu-',
            'buttons--',
            'admin--',
            'box---block-no-entries-',
            'cns-gcol-',
            'calendar-priority-',
            'cke_',
            'bubble-',
            'attitude-',
            'input-',
            'align',
            'display-type-',
            'icon-',
            'select2-',
            'cms-modal',
            'faded',
            'notification-button',
            'btn-comcode-(block|box|code|comcode|hide|html|list|page|quote|thumb|url|email)',
            'toast',
        ];
        if (preg_match('#^' . implode('|', $prefix_exceptions) . '#', $class) != 0) {
            return true;
        }
        $suffix_exceptions = [
            '-link',
        ];
        if (preg_match('#' . implode('|', $suffix_exceptions) . '$#', $class) != 0) {
            return true;
        }

        $exceptions = [
            'big-tab-first',
            'download-box-description',
            'modern-tab-body',
            'cart-table',
            'date-calendar-dialog',
            'trad-box-model',
            'box---points-leader-board-page',
            'first',
            'cms-modal-container',
            'comcode-fake-table',
            'fake-table',
            'fake-td',
            'flex-wrapper',
            'float-left',
            'float-right',
            'floats-wrap',
            'inline-block-box',
            'has-no-img',
            'theme-image--header-classic-image',
            'active-repeating',
            'md',
            'error-output',
            'responsive-video',
            'content-wrap-auto-center',
            'table-header-sorted',
            'comcode-button',
            'back-to-top-by-buttons',
            'mobile-only',
            'desktop-only',
            'help',
            'forceful',
            'contains-preview-button',
            'near-preview-button',
            'active-item',
            'thick-border',
            'breadcrumbs-img',
            'access-restricted-in-list',
            'activated-quote-button',
            'activities-content--remove-failure',
            'activities-content--remove-success',
            'adminzone-search',
            'ajax-loading-block',
            'ajax-tree-magic-button',
            'alert',
            'alt-field',
            'fake-table-cell',
            'cns-unread-topic-title',
            'docked',
            'non-docked',
            'menu-editor-page',
            'with-both-panels',
            'with-images',
            'tree-list-node',
            'cns-topic-control-functions',
            'helper-panel-visible',
            'inlineblock-desktop',
            'associated-breadcrumbs',
            'associated-details-smaller',
            'screen-title',
            'attachment-left',
            'attachment-right',
            'author-defined',
            'author-undefined',
            'being-dragged',
            'big-tab-active',
            'big-tab-inactive',
            'block-main-members--avatars',
            'block-main-members--media',
            'block-main-members--photos',
            'blue',
            'siblings-1',
            'digg',
            'clearfix',
            'box---block-main-content',
            'box---block-menu-embossed',
            'box---standardbox-accordion',
            'box_scroll_thumbs',
            'bubble',
            'buildr-self-member',
            'calendar-active',
            'calendar-current',
            'calendar-day',
            'calendar-free-time',
            'calendar-free-time-hourly',
            'calendar-month-day',
            'calendar-multiple',
            'calendar-year-month-day',
            'chat-lobby-convos-current-tab',
            'chat-lobby-convos-tab-first',
            'chat-lobby-convos-tab-new-messages',
            'chat-lobby-convos-tab-uptodate',
            'chat-message',
            'chat-message-old',
            'chat-operator-staff',
            'chat-options',
            'checked',
            'cms-keep',
            'cms-keep-block',
            'cns-edit-forum-forum',
            'cns-forum-topic-indent',
            'cns-forum-topic-wrapper-column-column6',
            'cns-forum-topic-wrapper-column-column6-shorter',
            'cns-guest-column-b',
            'cns-member-box-avatar-touching',
            'cns-on',
            'cns-post-emphasis',
            'cns-post-map-item-unread',
            'cns-post-personal',
            'column',
            'column-wrapper',
            'column-wrapper-2',
            'com',
            'command-output',
            'comments-sorting-box',
            'confirm',
            'count-0',
            'cse',
            'css',
            'csstransitions',
            'current',
            'decryption-overlay',
            'delete-cross-button',
            'dh',
            'divider',
            'e',
            'has-children',
            'faded-tooltip-img',
            'feature-background-image',
            'feature-image',
            'feature-video',
            'fieldset',
            'filledin',
            'footer-button-loading',
            'footer-links',
            'form-table-description-above-cell',
            'form-table-description-under-cell',
            'form-table-huge-field-description-is-under',
            'fp-col-block',
            'fp-col-blocks-wrap',
            'fractional-edit',
            'frame',
            'friend-active',
            'friend-inactive',
            'ghost',
            'global-community-message',
            'global-helper-panel-text-over',
            'green',
            'h',
            'has-img',
            'has-item-width',
            'has-preview',
            'have-links',
            'helper-panel',
            'helper-panel-hidden',
            'hero-button',
            'hero-section',
            'table-nofilter',
            'highlighted-post',
            'nlevel-link',
            'hover',
            'hover__dark',
            'im-event',
            'im-popup-avatar',
            'im-popup-close-button',
            'image_number',
            'img-thumb-opaque',
            'image_number_select',
            'info_slide_dots',
            'info_slide_thumb',
            'js',
            'js-widget',
            'label',
            'leave-native-tooltip',
            'legend',
            'light-table',
            'lightbox-image',
            'linear',
            'link',
            'link__dark',
            'link-exempt2',
            'loading-overlay',
            'magic-image-edit-link',
            'media-set',
            'menu-editor-selected-field',
            'menu-type--top',
            'message',
            'mobile',
            'moono',
            'mousemove',
            'must-show-together',
            'native-ui-foreground',
            'native-ui-selected',
            'non-current',
            'notes-about',
            'notification',
            'notification-code',
            'notification-has-read',
            'opens-below',
            'overlay',
            'overlay-close-button',
            'p',
            'pagination-load-more',
            'paused',
            'people-list',
            'pic',
            'play_button',
            'plupload',
            'popup-spacer',
            'post',
            'preview-box',
            'previous_button',
            'proceed-button-left-2',
            'radio-list-picture',
            'radio-list-picture-na',
            'rating-box',
            'rating-likers',
            'rating-star',
            'rating-star-highlight',
            'rating-stars',
            'red',
            'related-field',
            'required',
            'responsive',
            'rss-copyright',
            'rss-main',
            'rss-main-inner',
            'scroll_thumbs',
            'selected',
            'site-special-message-alt-inner',
            'sitewide-im-popup-body',
            'skip-step-button-wrap-with-req-note',
            'sku',
            'sortable-table',
            'standard-field-name',
            'status-green',
            'status-orange',
            'status-red',
            'theme-image--background-theme-image',
            'theme-image--logo-theme-image',
            'theme-image-preview',
            'theme-image-preview-wide',
            'tooltip',
            'tooltip-img',
            'tooltip-nolayout',
            'tooltip-ownlayout',
            'tooltip-with-img',
            'topic-list-title',
            'topic-list-topic',
            'toplevel',
            'toplevel-link',
            'is-touch-enabled',
            'tpl',
            'tpl-dropdown-row-a',
            'tpl-dropdown-row-b',
            'tree-list-highlighted',
            'tree-list-nonhighlighted',
            'unclosed-ticket',
            'unslider',
            'up-alert',
            'v',
            'validated-checkbox',
            'version',
            'version-button',
            'version-details',
            'version-help-icon',
            'version-news-link',
            'version-number',
            'visited',
            'vr',
            'width',
            'wysiwyg-toolbar-color-finder',
            'ze-panel-expanded',
            'ze-textarea',
            'ze-textarea-middle',
            'zebra-0',
            'zebra-1',
            'hide-if-in-panel',
            'hide-if-not-in-panel',
            'cellDebug',
            'cellDebugA',
            'cellDebugB',
            'cellEmpty',
            'cellEmptyA',
            'cellEmptyB',
            'cellLetter',
            'cellLetterA',
            'cellLetterB',
            'cellNumber',
            'cellNumberA',
            'cellNumberB',
            'crossTable',
            'crossTableA',
            'crossTableB',
            'progress-bar-complete',
            'progress-bar-error',
            'progress-bar-in-progress',
            'progress-bar-status',
            'progress-cancel',
            'progress-container',
            'progress-name',
            'progress-wrapper',
            'questionTable',
            'less-compact',
            'ace_layer',
            'ch-timespan',
            'code',
            'coding-standards-tables',
            'date-closed',
            'date-datepicker-button',
            'date-open',
            'file-changed',
            'file-unchanged',
            'glowing-node',
            'gsc-branding',
            'gsc-input',
            'gsc-search-button',
            'hidden-save-frame',
            'local-payment-merchant-details-simple',
            'main-website-faux',
            'me-cannotplay',
            'me-plugin',
            'mejs-background',
            'mejs-backlight-off',
            'mejs-backlight-on',
            'mejs-captions-translations',
            'mejs-chapter-block',
            'mejs-chapter-block-last',
            'mejs-container',
            'mejs-container-fullscreen',
            'mejs-embed',
            'mejs-fullscreen',
            'mejs-jump-forward-button',
            'mejs-long-video',
            'mejs-loop-off',
            'mejs-loop-on',
            'mejs-pause',
            'mejs-picturecontrols-button',
            'mejs-sourcechooser-button',
            'mejs-sourcechooser-selector',
            'mejs-unfullscreen',
            'mejs-unmute',
            'modern-subtab-bodies',
            'modern-subtab-headers',
            'modern-subtabs',
            'no-svg',
            'popup-blocker-warning',
            'preview-box-inner',
            'radio-list',
            'radio-list-pictures',
            'skitter-clean',
            'skitter-minimalist',
            'skitter-round',
            'skitter-square',
            'sp-1',
            'sp-2',
            'sp-3',
            'sp-4',
            'sp-5',
            'sp-6',
            'sp-active',
            'sp-alpha',
            'sp-alpha-enabled',
            'sp-alpha-handle',
            'sp-alpha-inner',
            'sp-button-container',
            'sp-buttons-disabled',
            'sp-cancel',
            'sp-cf',
            'sp-clear',
            'sp-clear-display',
            'sp-clear-enabled',
            'sp-color',
            'sp-container',
            'sp-dd',
            'sp-disabled',
            'sp-dragger',
            'sp-dragging',
            'sp-fill',
            'sp-flat',
            'sp-hidden',
            'sp-hue',
            'sp-initial',
            'sp-initial-disabled',
            'sp-input',
            'sp-input-container',
            'sp-input-disabled',
            'sp-palette',
            'sp-palette-button-container',
            'sp-palette-buttons-disabled',
            'sp-palette-container',
            'sp-palette-disabled',
            'sp-palette-only',
            'sp-picker-container',
            'sp-preview',
            'sp-preview-inner',
            'sp-replacer',
            'sp-sat',
            'sp-slider',
            'sp-thumb-active',
            'sp-thumb-el',
            'sp-top',
            'sp-top-inner',
            'sp-val',
            'sp-validation-error',
            'ui-checkboxradio-checked',
            'ui-checkboxradio-disabled',
            'ui-checkboxradio-label',
            'ui-checkboxradio-radio-label',
            'ui-controlgroup',
            'ui-controlgroup-horizontal',
            'ui-controlgroup-item',
            'ui-controlgroup-label',
            'ui-controlgroup-vertical',
            'ui-state-checked',
            'ui-visual-focus',
            'with-dots',
            'with-thumbs',
            'suggested',
            'table-autofilter',
            'table-filtered',
            'table-sortable',
            'table-sorted-asc',
            'table-sorted-desc',
            'themewizard-use-colour',
            'time-spin-btn',
            'time-spin-btn-container',
            'time-spin-btn-down',
            'time-spin-btn-up',
            'checklist-delete',
            'ui-accordion',
            'ui-accordion-content',
            'ui-accordion-header',
            'ui-accordion-header-icon',
            'ui-accordion-icons',
            'ui-autocomplete',
            'ui-button',
            'ui-button-icon-only',
            'ui-button-icon-primary',
            'ui-button-icon-secondary',
            'ui-button-icons-only',
            'ui-button-text',
            'ui-button-text-icon-primary',
            'ui-button-text-icon-secondary',
            'ui-button-text-icons',
            'ui-button-text-only',
            'ui-buttonset',
            'ui-corner-all',
            'ui-corner-bl',
            'ui-corner-bottom',
            'ui-corner-br',
            'ui-corner-left',
            'ui-corner-right',
            'ui-corner-tl',
            'ui-corner-top',
            'ui-corner-tr',
            'ui-datepicker',
            'ui-datepicker-buttonpane',
            'ui-datepicker-calendar',
            'ui-datepicker-current',
            'ui-datepicker-group',
            'ui-datepicker-group-last',
            'ui-datepicker-group-middle',
            'ui-datepicker-header',
            'ui-datepicker-month',
            'ui-datepicker-multi',
            'ui-datepicker-multi-2',
            'ui-datepicker-multi-3',
            'ui-datepicker-multi-4',
            'ui-datepicker-next',
            'ui-datepicker-next-hover',
            'ui-datepicker-prev',
            'ui-datepicker-prev-hover',
            'ui-datepicker-row-break',
            'ui-datepicker-rtl',
            'ui-datepicker-title',
            'ui-datepicker-year',
            'ui-dialog',
            'ui-dialog-buttonpane',
            'ui-dialog-buttonset',
            'ui-dialog-content',
            'ui-dialog-title',
            'ui-dialog-titlebar',
            'ui-dialog-titlebar-close',
            'ui-draggable',
            'ui-draggable-handle',
            'ui-front',
            'ui-helper-clearfix',
            'ui-helper-hidden',
            'ui-helper-hidden-accessible',
            'ui-helper-reset',
            'ui-helper-zfix',
            'ui-icon',
            'ui-icon-alert',
            'ui-icon-arrow-1-e',
            'ui-icon-arrow-1-n',
            'ui-icon-arrow-1-ne',
            'ui-icon-arrow-1-nw',
            'ui-icon-arrow-1-s',
            'ui-icon-arrow-1-se',
            'ui-icon-arrow-1-sw',
            'ui-icon-arrow-1-w',
            'ui-icon-arrow-2-e-w',
            'ui-icon-arrow-2-n-s',
            'ui-icon-arrow-2-ne-sw',
            'ui-icon-arrow-2-se-nw',
            'ui-icon-arrow-4',
            'ui-icon-arrow-4-diag',
            'ui-icon-arrowrefresh-1-e',
            'ui-icon-arrowrefresh-1-n',
            'ui-icon-arrowrefresh-1-s',
            'ui-icon-arrowrefresh-1-w',
            'ui-icon-arrowreturn-1-e',
            'ui-icon-arrowreturn-1-n',
            'ui-icon-arrowreturn-1-s',
            'ui-icon-arrowreturn-1-w',
            'ui-icon-arrowreturnthick-1-e',
            'ui-icon-arrowreturnthick-1-n',
            'ui-icon-arrowreturnthick-1-s',
            'ui-icon-arrowreturnthick-1-w',
            'ui-icon-arrowstop-1-e',
            'ui-icon-arrowstop-1-n',
            'ui-icon-arrowstop-1-s',
            'ui-icon-arrowstop-1-w',
            'ui-icon-arrowthick-1-e',
            'ui-icon-arrowthick-1-n',
            'ui-icon-arrowthick-1-ne',
            'ui-icon-arrowthick-1-nw',
            'ui-icon-arrowthick-1-s',
            'ui-icon-arrowthick-1-se',
            'ui-icon-arrowthick-1-sw',
            'ui-icon-arrowthick-1-w',
            'ui-icon-arrowthick-2-e-w',
            'ui-icon-arrowthick-2-n-s',
            'ui-icon-arrowthick-2-ne-sw',
            'ui-icon-arrowthick-2-se-nw',
            'ui-icon-arrowthickstop-1-e',
            'ui-icon-arrowthickstop-1-n',
            'ui-icon-arrowthickstop-1-s',
            'ui-icon-arrowthickstop-1-w',
            'ui-icon-battery-0',
            'ui-icon-battery-1',
            'ui-icon-battery-2',
            'ui-icon-battery-3',
            'ui-icon-blank',
            'ui-icon-bookmark',
            'ui-icon-bullet',
            'ui-icon-calculator',
            'ui-icon-calendar',
            'ui-icon-cancel',
            'ui-icon-carat-1-e',
            'ui-icon-carat-1-n',
            'ui-icon-carat-1-ne',
            'ui-icon-carat-1-nw',
            'ui-icon-carat-1-s',
            'ui-icon-carat-1-se',
            'ui-icon-carat-1-sw',
            'ui-icon-carat-1-w',
            'ui-icon-carat-2-e-w',
            'ui-icon-carat-2-n-s',
            'ui-icon-cart',
            'ui-icon-check',
            'ui-icon-circle-arrow-e',
            'ui-icon-circle-arrow-n',
            'ui-icon-circle-arrow-s',
            'ui-icon-circle-arrow-w',
            'ui-icon-circle-check',
            'ui-icon-circle-close',
            'ui-icon-circle-minus',
            'ui-icon-circle-plus',
            'ui-icon-circle-triangle-e',
            'ui-icon-circle-triangle-n',
            'ui-icon-circle-triangle-s',
            'ui-icon-circle-triangle-w',
            'ui-icon-circle-zoomin',
            'ui-icon-circle-zoomout',
            'ui-icon-circlesmall-close',
            'ui-icon-circlesmall-minus',
            'ui-icon-circlesmall-plus',
            'ui-icon-clipboard',
            'ui-icon-clock',
            'ui-icon-close',
            'ui-icon-closethick',
            'ui-icon-comment',
            'ui-icon-contact',
            'ui-icon-copy',
            'ui-icon-disk',
            'ui-icon-document',
            'ui-icon-document-b',
            'ui-icon-eject',
            'ui-icon-extlink',
            'ui-icon-flag',
            'ui-icon-folder-collapsed',
            'ui-icon-folder-open',
            'ui-icon-gear',
            'ui-icon-grip-diagonal-se',
            'ui-icon-grip-dotted-horizontal',
            'ui-icon-grip-dotted-vertical',
            'ui-icon-grip-solid-horizontal',
            'ui-icon-grip-solid-vertical',
            'ui-icon-gripsmall-diagonal-se',
            'ui-icon-heart',
            'ui-icon-help',
            'ui-icon-home',
            'ui-icon-image',
            'ui-icon-info',
            'ui-icon-key',
            'ui-icon-lightbulb',
            'ui-icon-link',
            'ui-icon-locked',
            'ui-icon-mail-closed',
            'ui-icon-mail-open',
            'ui-icon-minus',
            'ui-icon-minusthick',
            'ui-icon-newwin',
            'ui-icon-note',
            'ui-icon-notice',
            'ui-icon-pause',
            'ui-icon-pencil',
            'ui-icon-person',
            'ui-icon-pin-s',
            'ui-icon-pin-w',
            'ui-icon-play',
            'ui-icon-plus',
            'ui-icon-plusthick',
            'ui-icon-power',
            'ui-icon-print',
            'ui-icon-radio-off',
            'ui-icon-radio-on',
            'ui-icon-refresh',
            'ui-icon-scissors',
            'ui-icon-script',
            'ui-icon-search',
            'ui-icon-seek-end',
            'ui-icon-seek-first',
            'ui-icon-seek-next',
            'ui-icon-seek-prev',
            'ui-icon-seek-start',
            'ui-icon-shuffle',
            'ui-icon-signal',
            'ui-icon-signal-diag',
            'ui-icon-squaresmall-close',
            'ui-icon-squaresmall-minus',
            'ui-icon-squaresmall-plus',
            'ui-icon-star',
            'ui-icon-stop',
            'ui-icon-suitcase',
            'ui-icon-tag',
            'ui-icon-transfer-e-w',
            'ui-icon-transferthick-e-w',
            'ui-icon-trash',
            'ui-icon-triangle-1-e',
            'ui-icon-triangle-1-n',
            'ui-icon-triangle-1-ne',
            'ui-icon-triangle-1-nw',
            'ui-icon-triangle-1-s',
            'ui-icon-triangle-1-se',
            'ui-icon-triangle-1-sw',
            'ui-icon-triangle-1-w',
            'ui-icon-triangle-2-e-w',
            'ui-icon-triangle-2-n-s',
            'ui-icon-unlocked',
            'ui-icon-video',
            'ui-icon-volume-off',
            'ui-icon-volume-on',
            'ui-icon-wrench',
            'ui-icon-zoomin',
            'ui-icon-zoomout',
            'ui-menu',
            'ui-menu-divider',
            'ui-menu-icon',
            'ui-menu-icons',
            'ui-menu-item',
            'ui-priority-primary',
            'ui-priority-secondary',
            'ui-progressbar',
            'ui-progressbar-indeterminate',
            'ui-progressbar-overlay',
            'ui-progressbar-value',
            'ui-resizable',
            'ui-resizable-autohide',
            'ui-resizable-disabled',
            'ui-resizable-e',
            'ui-resizable-handle',
            'ui-resizable-n',
            'ui-resizable-ne',
            'ui-resizable-nw',
            'ui-resizable-s',
            'ui-resizable-se',
            'ui-resizable-sw',
            'ui-resizable-w',
            'ui-selectable',
            'ui-selectable-helper',
            'ui-selectmenu-button',
            'ui-selectmenu-menu',
            'ui-selectmenu-open',
            'ui-selectmenu-optgroup',
            'ui-selectmenu-text',
            'ui-slider',
            'ui-slider-handle',
            'ui-slider-horizontal',
            'ui-slider-range',
            'ui-slider-range-max',
            'ui-slider-range-min',
            'ui-slider-vertical',
            'ui-sortable-handle',
            'ui-spinner',
            'ui-spinner-button',
            'ui-spinner-down',
            'ui-spinner-input',
            'ui-spinner-up',
            'ui-state-active',
            'ui-state-default',
            'ui-state-disabled',
            'ui-state-error',
            'ui-state-error-text',
            'ui-state-focus',
            'ui-state-highlight',
            'ui-state-hover',
            'ui-tabs',
            'ui-tabs-active',
            'ui-tabs-anchor',
            'ui-tabs-collapsible',
            'ui-tabs-loading',
            'ui-tabs-nav',
            'ui-tabs-panel',
            'ui-tooltip',
            'ui-widget',
            'ui-widget-content',
            'ui-widget-header',
            'ui-widget-overlay',
            'ui-widget-shadow',
            'wp-caption',
            // Glide.js:
            'glide--dragging',
            'glide--rtl',
            'glide--swipeable',
            'glide__arrow',
            'glide__arrow--disabled',
            'glide__arrow--left',
            'glide__arrow--right',
            'glide__bullet',
            'glide__bullet--active',
            'glide__bullets',
            'glide__slides--dragging',
            //
            'notes_about', // Can come from a https://composr.app call
            'lightbox',
            'horiz-field-sep-rightward',
            'media-audio-websafe',
            'media-video-websafe',
            'contrast-box', // Currently used in site/pages/modules/galleries.php for the homepage hero slider

            // Classes set with Tempcode
            'header',
            'header-modern',
            'header-classic',
            'header-side',
            'header-side',
            'header-side',
            'global-message-icon',
            'global-message-warn',
            'red-alert-icon',
            'box---message-warn',
            'proceed-button-left',
            'rating-display-star',
            'proceed-button-left',
            'slider-homepage-hero', // .block-main-hero-slider#slider-<ID>

            // Classes not detected because of Tempcode in the [class] attribute
            'box---message',
            'with-seed-navbar',
            'with-white-navbar',
            'news-grid-item',
            'slideshow-media-box-item',

            // Classes set in JS
            'modern-tab-body-edit',

            // Boolean classes
            'is-active',
            'is-bg-color-dark',
            'is-bg-color-light',
            'is-current',
            'is-dom-animate-toggling-width',
            'is-enabled-show-details',
            'is-expanded',
            'is-gallery-slideshow',
            'is-hover-interface',
            'is-invalid',
            'is-items-main-scrolled-inside',
            'is-loading',
            'is-mass-selected',
            'is-mouse-only',
            'is-popup-open',
            'is-scrolled',
            'is-see-through',
            'is-sticky',
            'is-touch-interface',
            'is-touch-only',
            'is-valid',
            'has-1-news-item',
            'has-2-news-items',
            'has-3-news-items',
            'has-image',
            'has-loading-indicator',
            'has-multiple-views',
            'has-single-item',
            'did-fade-in',
            'mouse-only',
            'touch-only',

            // Potentially useful Bootstrap-inspired classes:
            'h1',
            'h3',
            'h4',
            'h5',
            'h6',
            'mark',
            'small',
            'flex',
            'inline-flex',
            'sr',
            'sr-only-focusable',
            'chevron-bottom',
            'collapse',
            'collapsing',
            'container-fluid',
            'custom-select',
            'disabled',
            'fade',
            'form-check',
            'form-check-input',
            'form-control-file',
            'form-control-lg',
            'form-control-plaintext',
            'form-control-range',
            'btn-block',
            'btn-dark',
            'btn-group',
            'btn-group-lg',
            'btn-group-sm',
            'btn-group-toggle',
            'btn-group-vertical',
            'btn-lg',
            'btn-light',
            'btn-outline-danger',
            'btn-outline-dark',
            'btn-outline-light',
            'btn-outline-secondary',
            'btn-outline-success',
            'btn-row-centered',
            'btn-success',
            'btn-toolbar',

            // userguide_comcode.txt:
            'responsive-table-separate-lines',
        ];
        if (in_array($class, $exceptions)) {
            return true;
        }

        return false;
    }

    protected function is_id_defined_and_intentionally_not_explicitly_used($id)
    {
        $prefix_exceptions = [
            't_1_',
            'jump_to_',
            'sort-filedump-',
            'type_filter_',
            'edit_panel_',
            'info_panel_',
            'settings_panel_',
            'view_panel_',
        ];
        if (preg_match('#^' . implode('|', $prefix_exceptions) . '#', $id) != 0) {
            return true;
        }

        $exceptions = [
            'chosen_categories',
            'xslt-introduction',
        ];
        if (in_array($id, $exceptions)) {
            return true;
        }

        return false;
    }
}

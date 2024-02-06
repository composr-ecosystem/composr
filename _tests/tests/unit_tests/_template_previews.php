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

/*EXTRA FUNCTIONS: diff_simple_2*/

// Expect ~3014 files to appear in _tests/screens_tested

/**
 * Composr test case class (unit testing).
 */
class _template_previews_test_set extends cms_test_case
{
    protected $template_id;

    public function setUp()
    {
        parent::setUp();

        $log_path = get_custom_file_base() . '/data_custom/template_previews.log';
        global $PREVIEWS_LOG;
        if (is_file($log_path)) {
            $PREVIEWS_LOG = fopen($log_path, 'at');
        } else {
            $PREVIEWS_LOG = null;
        }

        $a = scandir(get_file_base() . '/_tests/screens_tested');
        if (function_exists('sleep')) {
            sleep(3);
        }
        $b = scandir(get_file_base() . '/_tests/screens_tested');
        if (count($b) > count($a)) {
            fatal_exit('Test is already running');
        }

        disable_php_memory_limit();

        $_GET['keep_has_js'] = '0';
        push_query_limiting(false);
        $_GET['keep_query_limit'] = '0';
        $_GET['wide'] = '1';
        $_GET['keep_devtest'] = '1';
        $_GET['keep_has_js'] = '0';
        $_GET['keep_minify'] = '0'; // Disables resource merging, which messes with results
        //$_GET['keep_fatalistic'] = '1';

        require_code('lorem');
        require_code('files2');
    }

    public function testScreenPreview()
    {
        require_code('webstandards');
        require_lang('webstandards');
        require_code('themes2');

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            $this->screen_preview_test_for_theme('testScreenPreview:' . $theme, $theme);
        }
    }

    protected function screen_preview_test_for_theme($called_for, $theme)
    {
        global $THEME_BEING_TESTED;
        $THEME_BEING_TESTED = $theme;

        global $LOADED_TPL_CACHE, $BLOCKS_CACHE, $PANELS_CACHE;

        global $RECORD_TEMPLATES_USED, $RECORDED_TEMPLATES_USED;
        $RECORD_TEMPLATES_USED = true;

        $skip = empty($_GET['skip']) ? [] : explode(',', $_GET['skip']);

        $old_limit = null;

        $previews__by_template = find_all_previews__by_template();
        $previews__by_screen = find_all_previews__by_screen();
        $this->shuffle_assoc($previews__by_template); // So parallelism can work
        foreach ($previews__by_template as $template => $list) {
            $old_limit = cms_set_time_limit(30); // Time to generate and validate, with potential for spiky CPU availability over long-running task

            $hook = $list[0];
            $function = $list[1];

            // Exceptions...

            if (($this->only !== null) && ($this->only != $function)) {
                continue;
            }
            if (in_array($function, $skip)) {
                continue;
            }

            if ((!is_file(get_file_base() . '/themes/' . $theme . '/' . $template)) && (!is_file(get_file_base() . '/themes/' . $theme . '/' . str_replace('/', '_custom/', $template)))) {
                continue; // Does not exist in theme
            }

            if ($template == 'templates/tempcode_test.tpl') {
                continue; // Only a test template
            }
            if ($template == 'templates/ADMIN_ZONE_SEARCH.tpl') {
                continue; // Only in admin theme, causes problem
            }

            if (is_raw_code_template($template)) {
                continue;
            }

            $cache_path = get_file_base() . '/_tests/screens_tested/' . $theme . '__' . $function . '.tmp';
            if ((is_file($cache_path)) && (filemtime($cache_path) > time() - 60 * 60)) {
                continue; // To make easier to debug through
            }

            // Render...

            init__lorem();
            push_output_state();
            $LOADED_TPL_CACHE = [];
            $BLOCKS_CACHE = [];
            $PANELS_CACHE = [];

            $RECORDED_TEMPLATES_USED = [];

            $out = $this->render_screen_preview($called_for, $hook, $function, $template);

            restore_output_state();

            if (!is_object($out)) {
                $this->assertTrue(false, 'Claimed screen for ' . $template . ' is not defined');
                continue;
            }

            $_out = $out->evaluate();

            // Test all templates in this screen really were used...

            $flag = false;
            foreach ($previews__by_screen[$function][1] as $template_2) {
                // Exceptions
                if (in_array($template_2, [
                    // Designed for indirect inclusion
                    'templates/NEWSLETTER_PREVIEW.tpl',
                    'templates/GLOBAL_HELPER_PANEL.tpl',
                    'templates/HTML_HEAD.tpl',
                    'templates/HTML_HEAD_POLYFILLS.tpl',
                    'templates/MEMBER_TOOLTIP.tpl',
                    'templates/FORM_STANDARD_START.tpl',
                    'templates/FORM_STANDARD_END.tpl',
                    'templates/MEMBER_BAR_SEARCH.tpl',
                    'templates/MENU_LINK_PROPERTIES.tpl',
                    'templates/CNS_MEMBER_DIRECTORY_SCREEN_FILTER.tpl',
                    'templates/CNS_MEMBER_DIRECTORY_SCREEN_FILTERS.tpl',
                    'templates/ADMIN_ZONE_SEARCH.tpl',
                    'templates/FILEDUMP_FOOTER.tpl',
                    'templates/FILEDUMP_SEARCH.tpl',
                    'templates/FONT_SIZER.tpl',
                    'templates/FORM_SCREEN_ARE_REQUIRED.tpl',
                    'templates/FORM_SCREEN_FIELD_DESCRIPTION.tpl',
                    'templates/FORM_STANDARD_START.tpl',
                    'templates/FORM_STANDARD_END.tpl',
                    'templates/MEDIA__DOWNLOAD_LINK.tpl',
                    'templates/MEMBER_BAR_SEARCH.tpl',
                    'templates/NOTIFICATION_BUTTONS.tpl',
                    'templates/NOTIFICATION_TYPES.tpl',
                    'templates/CNS_MEMBER_PROFILE_FIELDS.tpl',
                    'templates/CNS_MEMBER_PROFILE_FIELD.tpl',
                    'templates/ICON.tpl',
                    'templates/RED_ALERT.tpl',
                    'templates/BLOCK_MAIN_MULTI_CONTENT__HEADER.tpl',
                    'templates/BLOCK_MAIN_MULTI_CONTENT__FOOTER.tpl',
                    'templates/PERMISSIONS_CONTENT_ACCESS_LIST.tpl',
                    'templates/PERMISSIONS_CONTENT_ACCESS_TICK.tpl',
                    'templates/THEME_TEMPLATE_EDITOR_TAB_REVISIONS.tpl',
                    'templates/FORM_SCREEN_FIELD_DESCRIPTION.tpl',
                    'templates/FORM_SCREEN_ARE_REQUIRED.tpl',
                    'templates/GALLERY_POPULAR.tpl',
                    'templates/FILTER_BOX.tpl',
                    'templates/MASS_SELECT_DELETE_FORM.tpl',
                    'templates/MASS_SELECT_MARKER.tpl',
                    'templates/AJAX_PAGINATION.tpl',

                    // Used in the <head> by default
                    'templates/CSS_NEED.tpl',
                    'templates/JAVASCRIPT_NEED.tpl',
                    'templates/RSS_HEADER.tpl',

                    // Won't run outside of CNS, so just stop it complaining if it's not available
                    'templates/CNS_MEMBER_DIRECTORY_SCREEN.tpl',
                ])) {
                    continue;
                }

                $this->assertTrue(array_key_exists($template_2, $RECORDED_TEMPLATES_USED), $template_2 . ' not used in preview as claimed in ' . $hook . '/' . $function);
                if (!array_key_exists($template_2, $RECORDED_TEMPLATES_USED)) {
                    $flag = true;
                }
            }

            // Validate...

            if ((stripos($_out, '<html') !== false) && (strpos($_out, '<xsl') === false)) {
                $result = check_xhtml($_out, false, false, false, true, true, true, false, false, true);
                if (($result !== null) && (empty($result['errors']))) {
                    $result = null;
                }
            } else {
                $result = null;
            }
            $this->assertTrue(($result === null), $hook . ':' . $function);
            if ($result !== null) {
                require_code('view_modes');
                //display_webstandards_results($_out, $result, false, false);
            } else {
                // Mark done
                if (!$flag) {
                    cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/' . $theme . '__' . $function . '.tmp', '1', FILE_WRITE_FIX_PERMISSIONS);
                }
            }
        }

        if ($old_limit !== null) {
            cms_set_time_limit($old_limit);
        }

        $THEME_BEING_TESTED = null;
    }

    public function testRepeatConsistency()
    {
        global $STATIC_TEMPLATE_TEST_MODE, $LOADED_TPL_CACHE, $BLOCKS_CACHE, $PANELS_CACHE;
        $STATIC_TEMPLATE_TEST_MODE = true;

        global $HAS_KEEP_IN_URL_CACHE;
        $_GET['wide'] = '1';
        $HAS_KEEP_IN_URL_CACHE = null;

        $skip = empty($_GET['skip']) ? [] : explode(',', $_GET['skip']);

        $previews__by_screen = find_all_previews__by_screen();
        $this->shuffle_assoc($previews__by_screen); // So parallelism can work
        foreach ($previews__by_screen as $function => $details) {
            if (empty($details[1])) {
                continue; // No templates in this preview
            }

            $old_limit = cms_set_time_limit(10);

            $template = $details[1][0];
            $hook = null;

            if (($this->only !== null) && ($this->only != $function)) {
                continue;
            }
            if (in_array($function, $skip)) {
                continue;
            }

            if ($template == 'ADMIN_ZONE_SEARCH.tpl') {
                continue; // Only in admin theme, causes problem
            }

            $cache_path = get_file_base() . '/_tests/screens_tested/consistency__' . $function . '.tmp';
            if ((is_file($cache_path)) && (filemtime($cache_path) > time() - 60 * 60)) {
                continue; // To make easier to debug through
            }

            init__lorem();
            push_output_state();
            $LOADED_TPL_CACHE = [];
            $BLOCKS_CACHE = [];
            $PANELS_CACHE = [];
            $out1 = $this->render_screen_preview('testRepeatConsistency-1', $hook, $function, $template);
            $_out1 = $this->cleanup_varying_code($out1->evaluate());
            restore_output_state();

            init__lorem();
            push_output_state();
            $LOADED_TPL_CACHE = [];
            $BLOCKS_CACHE = [];
            $PANELS_CACHE = [];
            $out2 = $this->render_screen_preview('testRepeatConsistency-2', $hook, $function, $template);
            $_out2 = $this->cleanup_varying_code($out2->evaluate());
            restore_output_state();

            $different = ($_out1 != $_out2);

            $this->assertFalse($different, 'Screen preview not same each time, ' . $function);

            if (!$different) {
                cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/consistency__' . $function . '.tmp', '1', FILE_WRITE_FIX_PERMISSIONS);
            } else {
                cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/consistency__' . $function . '_v1.tmp', $_out1, FILE_WRITE_FIX_PERMISSIONS);
                cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/consistency__' . $function . '_v2.tmp', $_out2, FILE_WRITE_FIX_PERMISSIONS);

                $this->assertTrue(false, 'Error! Do a diff between consistency__' . $function . '_v1.tmp and consistency__' . $function . '_v2.tmp');
            }

            unset($out1);
            unset($out2);

            cms_set_time_limit($old_limit);
        }
    }

    protected function cleanup_varying_code($_out)
    {
        $_out = preg_replace('#\s*<script[^<>]*>.*</script>\s*#Us', '', $_out); // We need to replace CSS/JS as load order/merging is not guaranteed consistent
        $_out = preg_replace('#\s*<style[^<>]*>.*</style>\s*#Us', '', $_out);
        $_out = preg_replace('#\s*<link[^<>]*>\s*#', '', $_out);
        $_out = preg_replace('#\s*<meta[^<>]*>\s*#', '', $_out);

        // Maybe got into content, or somehow otherwise double encoded
        $_out = preg_replace('#\s*&lt;script.*&gt;.*&lt;/script&gt;\s*#Us', '', $_out); // We need to replace CSS/JS as load order/merging is not guaranteed consistent
        $_out = preg_replace('#\s*&lt;style.*&gt;.*&lt;/style&gt;\s*#Us', '', $_out);
        $_out = preg_replace('#\s*&lt;link.*&gt;\s*#', '', $_out);

        $_out = preg_replace('#\s#', '', $_out);

        return $_out;
    }

    public function testNoMissingParams()
    {
        global $ATTACHED_MESSAGES, $ATTACHED_MESSAGES_RAW;

        $skip = empty($_GET['skip']) ? [] : explode(',', $_GET['skip']);

        $previews__by_screen = find_all_previews__by_screen();
        $this->shuffle_assoc($previews__by_screen); // So parallelism can work
        foreach ($previews__by_screen as $function => $details) {
            if (empty($details[1])) {
                continue; // No templates in this preview
            }

            $old_limit = cms_set_time_limit(10);

            $template = $details[1][0];
            $hook = null;

            if (($this->only !== null) && ($this->only != $function)) {
                continue;
            }
            if (in_array($function, $skip)) {
                continue;
            }

            if ($template === 'ADMIN_ZONE_SEARCH.tpl') {
                continue; // Only in admin theme, causes problem
            }

            $cache_path = get_file_base() . '/_tests/screens_tested/nonemissing__' . $function . '.tmp';
            if ((is_file($cache_path)) && (filemtime($cache_path) > time() - 60 * 60)) {
               continue; // To make easier to debug through
            }

            $ATTACHED_MESSAGES = new Tempcode();
            $ATTACHED_MESSAGES_RAW = [];
            push_output_state();
            $out1 = $this->render_screen_preview('testNoMissingParams', $hook, $function, $template);
            restore_output_state();

            if ($ATTACHED_MESSAGES === null) {
                $ATTACHED_MESSAGES = new Tempcode();
            }
            $put_out = (!$ATTACHED_MESSAGES->is_empty()) || (!empty($ATTACHED_MESSAGES_RAW));
            $this->assertFalse($put_out, 'Messages put out by ' . $function . '  (' . strip_html($ATTACHED_MESSAGES->evaluate()) . ')');

            if (!$put_out) {
                cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/nonemissing__' . $function . '.tmp', '1', FILE_WRITE_FIX_PERMISSIONS);
            }

            unset($out1);

            cms_set_time_limit($old_limit);
        }
    }

    protected function render_screen_preview($called_form, ?string $hook, string $function, ?string $template = null) : object
    {
        // Useful for debugging crashes
        global $PREVIEWS_LOG;
        if ($PREVIEWS_LOG !== null) {
            fwrite($PREVIEWS_LOG, date('Y-m-d H:i:s') . ' - RAM@' . clean_file_size(memory_get_usage()) . '/' . clean_file_size(php_return_bytes(ini_get('memory_limit'))) . ' - BEFORE:' . $called_form . ':' . $function . "\n");
        }

        $ret = render_screen_preview($hook, $function, $template);

        // Useful for debugging crashes
        if ($PREVIEWS_LOG !== null) {
            fwrite($PREVIEWS_LOG, date('Y-m-d H:i:s') . ' - RAM@' . clean_file_size(memory_get_usage()) . '/' . clean_file_size(php_return_bytes(ini_get('memory_limit'))) . ' - AFTER:' . $called_form . ':' . $function . "\n");
        }
        return $ret;
    }

    protected function shuffle_assoc(&$array)
    {
        $keys = array_keys($array);
        shuffle($keys);

        $new = [];
        foreach ($keys as $key) {
            $new[$key] = $array[$key];
        }
        $array = $new;
    }

    public function tearDown()
    {
        global $PREVIEWS_LOG;
        if ($PREVIEWS_LOG !== null) {
            fclose($PREVIEWS_LOG);
            $PREVIEWS_LOG = null;
        }

        parent::tearDown();
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

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

        $a = scandir(get_file_base() . '/_tests/screens_tested');
        if (function_exists('sleep')) {
            sleep(3);
        }
        $b = scandir(get_file_base() . '/_tests/screens_tested');
        if (count($b) > count($a)) {
            exit('Already running');
        }

        cms_disable_time_limit();
        disable_php_memory_limit();

        $_GET['keep_has_js'] = '0';
        push_query_limiting(false);
        $_GET['keep_query_limit'] = '0';
        cms_ini_set('memory_limit', '-1');
        $_GET['wide'] = '1';
        $_GET['keep_devtest'] = '1';
        $_GET['keep_has_js'] = '0';
        $_GET['keep_minify'] = '0'; // Disables resource merging, which messes with results
        //$_GET['keep_fatalistic'] = '1';

        require_code('lorem');
        require_code('files2');
    }

    public function testNoMissingPreviews()
    {
        $templates = [];

        $files = get_directory_contents(get_file_base() . '/themes/default/templates', get_file_base() . '/themes/default/templates', null, false, true, ['tpl']);
        foreach ($files as $path) {
            $templates[] = 'templates/' . basename($path);
        }

        $all_previews = find_all_previews__by_template();

        foreach ($templates as $t) {
            $this->assertFalse((!array_key_exists($t, $all_previews)), 'Missing preview for: ' . $t);
        }

        cms_ini_set('ocproducts.type_strictness', '0');
        cms_ini_set('ocproducts.xss_detect', '0');
    }

    public function testScreenPreview()
    {
        require_code('webstandards');
        require_lang('webstandards');
        require_code('themes2');

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            if (($this->only !== null) && ($this->only != $theme)) {
                continue;
            }

            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            )) {
                continue;
            }

            $this->screen_preview_test_for_theme($theme);
        }

        cms_ini_set('ocproducts.type_strictness', '0');
        cms_ini_set('ocproducts.xss_detect', '0');
    }

    protected function screen_preview_test_for_theme($theme)
    {
        global $THEME_BEING_TESTED;
        $THEME_BEING_TESTED = $theme;

        global $LOADED_TPL_CACHE, $BLOCKS_CACHE, $PANELS_CACHE;

        global $RECORD_TEMPLATES_USED, $RECORDED_TEMPLATES_USED;
        $RECORD_TEMPLATES_USED = true;

        $only_do_these = []; // If you want to test specific templates temporarily put the template names (without .tpl) in this array. But remove again before you commit!

        $lists = find_all_previews__by_template();
        $this->shuffle_assoc($lists); // So parallelism can work
        foreach ($lists as $template => $list) {
            if (!empty($only_do_these)) {
                if (!in_array($template, $only_do_these)) {
                    continue;
                }
            }

            if ((!is_file(get_file_base() . '/themes/' . $theme . '/' . $template)) && (!is_file(get_file_base() . '/themes/' . $theme . '/' . str_replace('/', '_custom/', $template)))) {
                continue;
            }

            if ($template == 'templates/tempcode_test.tpl') {
                continue;
            }
            if ($template == 'templates/ADMIN_ZONE_SEARCH.tpl') {
                continue; // Only in admin theme, causes problem
            }

            if (is_plain_text_template($template)) {
                continue;
            }

            $hook = $list[0];
            $function = $list[1];

            if (is_file(get_file_base() . '/_tests/screens_tested/' . $theme . '__' . $function . '.tmp')) {
                continue; // To make easier to debug through
            }

            cms_disable_time_limit();

            init__lorem();
            push_output_state();
            $LOADED_TPL_CACHE = [];
            $BLOCKS_CACHE = [];
            $PANELS_CACHE = [];

            $RECORDED_TEMPLATES_USED = [];

            $out = render_screen_preview($hook, $function, $template);

            restore_output_state();

            $flag = false;
            foreach ($lists as $template_2 => $list_2) {
                if (!empty($only_do_these)) {
                    if (!in_array($template_2, $only_do_these)) {
                        continue;
                    }
                }

                if ($template_2 == 'templates/tempcode_test.tpl') {
                    continue;
                }

                if (is_plain_text_template($template_2)) {
                    continue;
                }
                if ($list_2[1] == $function) {
                    // Ignore templates designed for indirect inclusion
                    if (in_array($template_2, [
                        'templates/NEWSLETTER_PREVIEW.tpl',
                        'templates/GLOBAL_HELPER_PANEL.tpl',
                        'templates/HTML_HEAD.tpl',
                        'templates/HTML_HEAD_POLYFILLS.tpl',
                        'templates/MEMBER_TOOLTIP.tpl',
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
                    ])) {
                        continue;
                    }

                    $this->assertTrue(array_key_exists($template_2, $RECORDED_TEMPLATES_USED), $template_2 . ' not used in preview as claimed in ' . $hook . '/' . $function);
                    if (!array_key_exists($template_2, $RECORDED_TEMPLATES_USED)) {
                        $flag = true;
                    }
                }
            }

            if (!is_object($out)) {
                fatal_exit('Claimed screen for ' . $template . ' is not defined');
            }
            $_out = $out->evaluate();

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
                display_webstandards_results($_out, $result, false, false);
            } else {
                if (!$flag) {
                    cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/' . $theme . '__' . $function . '.tmp', '1', FILE_WRITE_FIX_PERMISSIONS);
                }
            }
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

        $lists = find_all_previews__by_screen();
        $this->shuffle_assoc($lists); // So parallelism can work
        foreach ($lists as $function => $details) {
            $tpls = $details[1];
            $template = $tpls[0];
            $hook = null;

            if ($template == 'ADMIN_ZONE_SEARCH.tpl') {
                continue; // Only in admin theme, causes problem
            }

            if (is_file(get_file_base() . '/_tests/screens_tested/consistency__' . $function . '.tmp')) {
                continue; // To make easier to debug through
            }

            cms_disable_time_limit();

            init__lorem();
            push_output_state();
            $LOADED_TPL_CACHE = [];
            $BLOCKS_CACHE = [];
            $PANELS_CACHE = [];
            $out1 = render_screen_preview($hook, $function, $template);
            $_out1 = $this->cleanup_varying_code($out1->evaluate());
            restore_output_state();

            init__lorem();
            push_output_state();
            $LOADED_TPL_CACHE = [];
            $BLOCKS_CACHE = [];
            $PANELS_CACHE = [];
            $out2 = render_screen_preview($hook, $function, $template);
            $_out2 = $this->cleanup_varying_code($out2->evaluate());
            restore_output_state();

            $different = ($_out1 != $_out2);

            $this->assertFalse($different, 'Screen preview not same each time, ' . $function);

            if (!$different) {
                cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/consistency__' . $function . '.tmp', '1', FILE_WRITE_FIX_PERMISSIONS);
            } else {
                cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/v1__' . '.tmp', $_out1, FILE_WRITE_FIX_PERMISSIONS);
                cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/v2__' . '.tmp', $_out2, FILE_WRITE_FIX_PERMISSIONS);

                exit('Error! Do a diff between v1__.tmp and v2__.tmp');
            }

            unset($out1);
            unset($out2);
        }

        cms_ini_set('ocproducts.type_strictness', '0');
        cms_ini_set('ocproducts.xss_detect', '0');
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

        $lists = find_all_previews__by_screen();
        $this->shuffle_assoc($lists); // So parallelism can work
        foreach ($lists as $function => $details) {
            $tpls = $details[1];
            $template = $tpls[0];
            $hook = null;

            if ($template === 'ADMIN_ZONE_SEARCH.tpl') {
                continue; // Only in admin theme, causes problem
            }

            if (is_file(get_file_base() . '/_tests/screens_tested/nonemissing__' . $function . '.tmp')) {
                continue; // To make easier to debug through
            }

            cms_disable_time_limit();

            $ATTACHED_MESSAGES = new Tempcode();
            $ATTACHED_MESSAGES_RAW = [];
            $out1 = render_screen_preview($hook, $function, $template);

            if ($ATTACHED_MESSAGES === null) {
                $ATTACHED_MESSAGES = new Tempcode();
            }
            $put_out = (!$ATTACHED_MESSAGES->is_empty()) || (!empty($ATTACHED_MESSAGES_RAW));
            $this->assertFalse($put_out, 'Messages put out by ' . $function . '  (' . strip_html($ATTACHED_MESSAGES->evaluate()) . ')');

            if (!$put_out) {
                cms_file_put_contents_safe(get_file_base() . '/_tests/screens_tested/nonemissing__' . $function . '.tmp', '1', FILE_WRITE_FIX_PERMISSIONS);
            }

            unset($out1);
        }

        cms_ini_set('ocproducts.type_strictness', '0');
        cms_ini_set('ocproducts.xss_detect', '0');
    }

    public function testNoRedundantFunctions()
    {
        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach ($hooks as $hook => $place) {
            require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($hook));

            $ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($hook));
            if (!method_exists($ob, 'tpl_previews')) {
                continue;
            }
            $used = array_unique($ob->tpl_previews());

            $code = cms_file_get_contents_safe(get_file_base() . '/' . $place . '/hooks/systems/addon_registry/' . $hook . '.php');

            $matches = [];
            $num_matches = preg_match_all('#function tpl_preview__(.*)\(#U', $code, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $this->assertTrue(in_array($matches[1][$i], $used), 'Non-used screen function ' . $matches[1][$i]);
            }
        }

        cms_ini_set('ocproducts.type_strictness', '0');
        cms_ini_set('ocproducts.xss_detect', '0');
    }

    public function testNoDoublePreviews()
    {
        $all_used = [];

        $hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($hooks as $ob) {
            if (!method_exists($ob, 'tpl_previews')) {
                continue;
            }
            $used = array_unique($ob->tpl_previews());
            foreach (array_keys($used) as $u) {
                $this->assertFalse(array_key_exists($u, $all_used), 'Double defined ' . $u);
            }
            $all_used += $used;
        }

        cms_ini_set('ocproducts.type_strictness', '0');
        cms_ini_set('ocproducts.xss_detect', '0');
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
}

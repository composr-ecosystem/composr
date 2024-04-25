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

/*EXTRA FUNCTIONS: error_log*/

/**
 * Composr test case class (unit testing).
 */
class __extra_logging_test_set extends cms_test_case
{
    protected $session_id = null;

    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);

        $config_path = get_file_base() . '/_config.php';
        $c = cms_file_get_contents_safe($config_path, FILE_READ_LOCK);
        $c = str_replace("\n\$SITE_INFO['static_caching_hours'] = '1';", '', $c);
        $c = str_replace("\n\$SITE_INFO['any_guest_cached_too'] = '1';", '', $c);
        require_code('files');
        cms_file_put_contents_safe($config_path, $c);

        $this->session_id = $this->establish_admin_callback_session();

        set_option('grow_template_meta_tree', '0');
    }

    public function testProfiler()
    {
        if (($this->only !== null) && ($this->only != 'testProfiler')) {
            return;
        }

        $glob_cmd = get_custom_file_base() . '/data_custom/profiling--*.log';

        clearstatcache();
        $before = glob($glob_cmd);

        set_value('enable_profiler', '1');
        $url = build_url(['page' => ''], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        set_value('enable_profiler', '0');

        clearstatcache();
        $after = glob($glob_cmd);

        $this->assertTrue(count($after) > count($before), 'Profiling files after = ' . integer_format(count($after)));

        foreach ($after as $path) {
            if (strpos($path, 'in-progress') === false) {
                unlink($path);
            }
        }
    }

    public function testMemoryMonitorSlowURLs()
    {
        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        if (($this->only !== null) && ($this->only != 'testMemoryMonitorSlowURLs')) {
            return;
        }

        set_value('monitor_slow_urls', '0.1');

        $log_path = get_custom_file_base() . '/data_custom/errorlog.php';
        cms_file_put_contents_safe($log_path, '', FILE_WRITE_BOM);
        $url = build_url(['page' => 'faq', 'cache' => 0], 'docs');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos(cms_file_get_contents_safe($log_path, FILE_READ_LOCK | FILE_READ_BOM), 'Over time limit @'));

        set_value('monitor_slow_urls', '0');
    }

    public function testMemoryTracking()
    {
        if (($this->only !== null) && ($this->only != 'testMemoryTracking')) {
            return;
        }

        set_value('memory_tracking', '1');

        $log_path = get_custom_file_base() . '/data_custom/errorlog.php';
        cms_file_put_contents_safe($log_path, '', FILE_WRITE_BOM);
        $url = build_url(['page' => ''], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos(cms_file_get_contents_safe($log_path, FILE_READ_LOCK | FILE_READ_BOM), 'Memory usage above memory_tracking'));

        set_value('memory_tracking', '0');
    }

    public function testSpecialPageTypeMemory()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeMemory')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'memory'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Memory usage:') !== false);
    }

    public function testSpecialPageTypeIDELinkage()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeIDELinkage')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'ide_linkage'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'txmt://') !== false);
    }

    public function testSpecialPageTypeQuery()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeQuery')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'query'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'View queries') !== false);
    }

    public function testSpecialPageTypeTranslateContent()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeTranslateContent')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'lang_EN'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Translate/rephrase Composr into English') !== false || strpos($data, 'Translate/rephrase the software into English') !== false);
    }

    public function testSpecialPageTypeValidate()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeValidate')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'code'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Standards checker notices') !== false);
    }

    public function testSpecialPageTypeThemeImages()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeThemeImages')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'theme_images'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Theme image editing') !== false);
    }

    public function testSpecialPageTypeTemplates()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeTemplates')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'templates'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Edit templates') !== false);
    }

    public function testSpecialPageTypeTree()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeTree')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'tree'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Template tree') !== false);
    }

    public function testSpecialPageTypeShowMarkers()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeShowMarkers')) {
            return;
        }

        $url = build_url(['page' => '', 'keep_markers' => 1], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '<!-- START-TEMPLATE=CSS_NEED') !== false);
    }

    public function testSpecialPageTypeShowEditLinks()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeShowEditLinks')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'show_edit_links'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'admin-themes') !== false);
    }

    public function testErrorLog()
    {
        if (($this->only !== null) && ($this->only != 'testErrorLog')) {
            return;
        }

        $path = get_custom_file_base() . '/data_custom/errorlog.php';

        clearstatcache();
        $size_before = filesize($path);
        error_log('Composr: DEBUG This is a test log');
        clearstatcache();
        $size_after = filesize($path);
        $this->assertTrue($size_after > $size_before);
    }

    public function testPermissionChecksLog()
    {
        if (($this->only !== null) && ($this->only != 'testPermissionChecksLog')) {
            return;
        }

        $path = get_custom_file_base() . '/data_custom/permission_checks.log';
        cms_file_put_contents_safe($path, '', FILE_WRITE_BOM);

        clearstatcache();
        $size_before = filesize($path);
        $url = build_url(['page' => '', 'keep_su' => 'Guest'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        clearstatcache();
        $size_after = filesize($path);
        $this->assertTrue($size_after > $size_before);

        unlink($path);
    }

    public function testQueryLog()
    {
        if (($this->only !== null) && ($this->only != 'testQueryLog')) {
            return;
        }

        $path = get_custom_file_base() . '/data_custom/queries.log';
        cms_file_put_contents_safe($path, '', FILE_WRITE_BOM);

        clearstatcache();
        $size_before = filesize($path);
        $url = build_url(['page' => ''], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        clearstatcache();
        $size_after = filesize($path);
        $this->assertTrue($size_after > $size_before);

        unlink($path);
    }
}

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
class extra_logging_test_set extends cms_test_case
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

        $this->assertTrue(count($after) > count($before), 'Failed to generate profiler files');

        if ($this->debug) {
            $this->dump($data, 'HTTP Request');
        }

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
        $url = build_url(['page' => 'home', 'cache' => 0], 'adminzone');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        if ($this->debug) {
            $this->dump($data, 'adminzone page with no cache');
        }
        $this->assertTrue(strpos(cms_file_get_contents_safe($log_path, FILE_READ_LOCK | FILE_READ_BOM), 'request time above monitor_slow_urls @'), 'Expected a request time above monitor_slow_urls log but did not get one.');

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
        $this->assertTrue(strpos(cms_file_get_contents_safe($log_path, FILE_READ_LOCK | FILE_READ_BOM), 'Memory usage above memory_tracking'), 'Expected Memory usage above memory_tracking but did not get one');

        set_value('memory_tracking', '0');
    }

    public function testSpecialPageTypeMemory()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeMemory')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'memory'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Memory usage:') !== false, 'Expected Memory usage tracking but did not get any');
    }

    public function testSpecialPageTypeIDELinkage()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeIDELinkage')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'ide_linkage'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'txmt://') !== false, 'Expected txmt:// link but did not get one');
    }

    public function testSpecialPageTypeQuery()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeQuery')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'query'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'View queries') !== false, 'Expected queries page but did not get it');
    }

    public function testSpecialPageTypeTranslateContent()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeTranslateContent')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'lang_EN'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Translate/rephrase Composr into English') !== false || strpos($data, 'Translate/rephrase the software into English') !== false, 'Expected translation to English page but did not get it');
    }

    public function testSpecialPageTypeValidate()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeValidate')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'code'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Standards checker notices') !== false, 'Expected standards checker notices but did not get them');
    }

    public function testSpecialPageTypeThemeImages()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeThemeImages')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'theme_images'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Theme image editing') !== false, 'Expected theme image editor but did not get it');
    }

    public function testSpecialPageTypeTemplates()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeTemplates')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'templates'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Edit templates') !== false, 'Expected template editor but did not get it');
    }

    public function testSpecialPageTypeTree()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeTree')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'tree'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'Template tree') !== false, 'Expected template tree but did not get it');
    }

    public function testSpecialPageTypeShowMarkers()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeShowMarkers')) {
            return;
        }

        $url = build_url(['page' => '', 'keep_markers' => 1], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '<!-- START-TEMPLATE=CSS_NEED') !== false, 'Expected HTML comment markers but did not get them');
    }

    public function testSpecialPageTypeShowEditLinks()
    {
        if (($this->only !== null) && ($this->only != 'testSpecialPageTypeShowEditLinks')) {
            return;
        }

        $url = build_url(['page' => '', 'special_page_type' => 'show_edit_links'], '');
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 100.0, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'admin-themes') !== false, 'Expected edit links but did not get them');
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
        $this->assertTrue($size_after > $size_before, 'Expected a test log but did not get it');
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
        $this->assertTrue($size_after > $size_before, 'Expected a permissions check log on Guest but did not get one');

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
        $this->assertTrue($size_after > $size_before, 'Expected queries to get logged but that did not happen');

        unlink($path);
    }
}

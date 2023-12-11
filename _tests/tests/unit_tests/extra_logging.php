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

/**
 * Composr test case class (unit testing).
 */
class extra_logging_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        $config_path = get_file_base() . '/_config.php';
        $c = cms_file_get_contents_safe($config_path);
        $c = str_replace("\n\$SITE_INFO['fast_spider_cache'] = '1';", '', $c);
        $c = str_replace("\n\$SITE_INFO['any_guest_cached_too'] = '1';", '', $c);
        require_code('files');
        cms_file_put_contents_safe($config_path, $c);

        $this->establish_admin_session();
    }

    public function testProfiler()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testProfiler')) {
            return;
        }

        $glob_cmd = get_file_base() . '/data_custom/profiling--*.log';

        clearstatcache();
        $before = glob($glob_cmd);

        set_value('enable_profiler', '1');
        $url = build_url(array('page' => ''), 'forum');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
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
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testMemoryMonitorSlowURLs')) {
            return;
        }

        set_value('monitor_slow_urls', '0.1');

        $log_path = get_file_base() . '/data_custom/errorlog.php';
        cms_file_put_contents_safe($log_path, '');
        $url = build_url(array('page' => 'faq', 'cache' => 0), 'docs');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos(cms_file_get_contents_safe($log_path), 'Over time limit @'));

        set_value('monitor_slow_urls', '0');
    }

    public function testMemoryTracking()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testMemoryTracking')) {
            return;
        }

        set_value('memory_tracking', '1');

        $log_path = get_file_base() . '/data_custom/errorlog.php';
        cms_file_put_contents_safe($log_path, '');
        $url = build_url(array('page' => ''), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos(cms_file_get_contents_safe($log_path), 'Memory usage above memory_tracking'));

        set_value('memory_tracking', '0');
    }

    public function testSpecialPageTypeMemory()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeMemory')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'memory'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, 'Memory usage:') !== false);
    }

    public function testSpecialPageTypeIDELinkage()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeIDELinkage')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'ide_linkage'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, 'txmt://') !== false);
    }

    public function testSpecialPageTypeQuery()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeQuery')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'query'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, 'View queries') !== false);
    }

    public function testSpecialPageTypeTranslateContent()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeTranslateContent')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'lang_EN'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, 'Translate/rephrase Composr into English') !== false || strpos($data, 'Translate/rephrase the software into English') !== false);
    }

    public function testSpecialPageTypeValidate()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeValidate')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'code'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, 'Standards checker notices') !== false);
    }

    public function testSpecialPageTypeThemeImages()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeThemeImages')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'theme_images'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, 'Theme image editing') !== false);
    }

    public function testSpecialPageTypeTemplates()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeTemplates')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'templates'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, 'Templates') !== false);
    }

    public function testSpecialPageTypeTree()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeTree')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'tree'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, 'Template tree') !== false);
    }

    public function testSpecialPageTypeShowMarkers()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeShowMarkers')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'show_markers'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, '<!-- START-TEMPLATE=HTML_HEAD -->') !== false);
    }

    public function testSpecialPageTypeShowEditLinks()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testSpecialPageTypeShowEditLinks')) {
            return;
        }

        $url = build_url(array('page' => '', 'special_page_type' => 'show_edit_links'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        $this->assertTrue(strpos($data, 'admin-themes') !== false);
    }

    public function testErrorLog()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testErrorLog')) {
            return;
        }

        $path = get_file_base() . '/data_custom/errorlog.php';
        clearstatcache();
        $size_before = filesize($path);
        error_log('Testing');
        clearstatcache();
        $size_after = filesize($path);
        $this->assertTrue($size_after > $size_before);
    }

    public function testPermissionChecksLog()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testPermissionChecksLog')) {
            return;
        }

        $path = get_file_base() . '/data_custom/permissioncheckslog.php';
        cms_file_put_contents_safe($path, '');

        clearstatcache();
        $size_before = filesize($path);
        $url = build_url(array('page' => '', 'keep_su' => 'Guest'), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        clearstatcache();
        $size_after = filesize($path);
        $this->assertTrue($size_after > $size_before);

        unlink($path);
    }

    public function testQueryLog()
    {
        $limit_to = get_param_String('limit_to', null);
        if (($limit_to !== null) && ($limit_to != 'testQueryLog')) {
            return;
        }

        $path = get_file_base() . '/data_custom/queries.log';
        cms_file_put_contents_safe($path, '');

        clearstatcache();
        $size_before = filesize($path);
        $url = build_url(array('page' => ''), '');
        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 20.0);
        clearstatcache();
        $size_after = filesize($path);
        $this->assertTrue($size_after > $size_before);

        unlink($path);
    }
}

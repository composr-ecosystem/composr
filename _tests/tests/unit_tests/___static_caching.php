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
class ___static_caching_test_set extends cms_test_case
{
    public function testStaticCacheWorks()
    {
        $panel_text = @strval(file_get_contents(get_custom_file_base() . '/pages/comcode_custom/' . get_site_default_lang() . '/panel_left.txt') . file_get_contents(get_custom_file_base() . '/pages/comcode_custom/' . get_site_default_lang() . '/panel_right.txt'));
        if ((strpos($panel_text, 'main_newsletter_signup') !== false) || (strpos($panel_text, 'side_newsletter') !== false) || (strpos($panel_text, 'side_shoutbox') !== false)) {
            $this->assertTrue(false, 'Cannot have a POSTing block in a side panel for this test');
        }

        $config_file_path = get_file_base() . '/_config.php';
        $config_file = cms_file_get_contents_safe($config_file_path, FILE_READ_LOCK);
        file_put_contents($config_file_path, $config_file . "\n\n\$SITE_INFO['static_caching_hours'] = '1';\n\$SITE_INFO['any_guest_cached_too'] = '1';\n\$SITE_INFO['static_caching_inclusion_list']='.*';");
        fix_permissions($config_file_path);
        if (php_function_allowed('usleep')) {
            usleep(500000);
        }

        $url = build_url(['page' => '', 'debug_static_cache' => 1], '');

        $result = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0]); // Prime cache

        if (get_param_integer('early_debug', 0) == 1) {
            require_code('files2');
            var_dump(get_directory_contents(get_custom_file_base() . '/caches/static'));

            var_dump($result);
        }

        $time_before = microtime(true);
        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0]);

        $time_after = microtime(true);

        $time = $time_after - $time_before;

        $this->assertTrue($time < 0.2/*HTTPS negotiation takes a little time at least*/, 'Took too long, ' . float_format($time) . ' seconds');

        $this->assertTrue(preg_match('#global\w*\.css#', $data) != 0, 'Expected global.css to be loaded but was not.');
        $this->assertTrue(strpos($data, '</html>') !== false, 'Expected closing HTML tag but did not get one.');
        if ($this->debug) {
            var_dump($data);
        }

        file_put_contents($config_file_path, $config_file);
    }

    public function testFailover()
    {
        $url = build_url(['page' => '', 'keep_devtest' => null], '');
        $url2 = build_url(['page' => 'xxx-does-not-exist' . uniqid('', true), 'keep_devtest' => null], '');

        $test_url = get_base_url() . '/does-not-exist.bin';

        $config_file_path = get_file_base() . '/_config.php';
        $config_file = cms_file_get_contents_safe($config_file_path, FILE_READ_LOCK);
        file_put_contents($config_file_path, $config_file . "\n\n\$SITE_INFO['static_caching_hours'] = '1';\n\$SITE_INFO['any_guest_cached_too'] = '1';\n\$SITE_INFO['static_caching_inclusion_list']='.*';\n\$SITE_INFO['failover_mode'] = 'auto_off';\n\$SITE_INFO['failover_check_urls'] = '" . $test_url . "';\n\$SITE_INFO['failover_cache_miss_message'] = 'FAILOVER_CACHE_MISS';\n\$SITE_INFO['failover_email_contact'] = '';\$SITE_INFO['base_url'] = '" . addslashes(get_base_url()) . "';\n");
        fix_permissions($config_file_path);

        // This will empty the static cache, meaning when it is re-primed it actually will do so for fail-over (now that's enabled) priming rather than just outputting from the cache made in testStaticCacheWorks
        global $ALLOW_DOUBLE_DECACHE;
        $ALLOW_DOUBLE_DECACHE = true;
        erase_persistent_cache();

        $result = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'timeout' => 20.0]); // Prime cache
        $this->assertTrue($result !== null, 'Failed to prime cache');

        $detect_url = find_script('failover_script');
        $result_ob = cms_http_request($detect_url, ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'timeout' => 20.0, 'ignore_http_status' => true]); // Should trigger failover, due to our $test_url being a broken URL
        $this->assertTrue($result_ob->message === '200', 'Failed to call failover script');

        clearstatcache();
        $ccc = cms_file_get_contents_safe(get_file_base() . '/_config.php', FILE_READ_LOCK);
        $this->assertTrue(strpos($ccc, "\$SITE_INFO['failover_mode'] = 'auto_on';") !== false, 'Failover should have activated but did not...');
        if ($this->debug) {
            var_dump($ccc);
        }

        $result = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'ignore_http_status' => true, 'trigger_error' => false, 'timeout' => 20.0]); // Should be failed over, but cached
        $this->assertTrue($result !== null && strpos($result, '</body>') !== false, 'Failover should have been able to use static cache but did not');
        if ($this->debug) {
            var_dump($result);
        }

        $result = http_get_contents($url2->evaluate(), ['convert_to_internal_encoding' => true, 'ignore_http_status' => true, 'trigger_error' => false, 'timeout' => 20.0]); // Should be failed over, but cached
        $this->assertTrue($result !== null && strpos($result, 'FAILOVER_CACHE_MISS') !== false, 'Failover cache miss message not found');
        if ($this->debug) {
            var_dump($result);
        }

        file_put_contents($config_file_path, $config_file);
    }
}

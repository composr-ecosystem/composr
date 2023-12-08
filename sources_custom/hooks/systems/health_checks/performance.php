<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    health_check
 */

/*EXTRA FUNCTIONS: stream_context_set_default*/

/**
 * Hook class.
 */
class Hook_health_check_performance extends Hook_Health_Check
{
    protected $category_label = 'Performance';

    /**
     * Standard hook run function to run this category of health checks.
     *
     * @param  ?array $sections_to_run Which check sections to run (null: all)
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @return array A pair: category label, list of results
     */
    public function run($sections_to_run, $check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        $this->process_checks_section('testManualPerformance', 'Manual performance checks', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('test404Pages', '404 pages', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testCookies', 'Cookies', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testHTTPOptimisation', 'HTTP optimisation', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testPageSpeed', 'Page speed (slow)', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testSetup', 'Setup', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

        return array($this->category_label, $this->results);
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testManualPerformance($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!$manual_checks) {
            return;
        }

        $this->state_check_manual('Check for [url="speed issues"]https://pagespeed.web.dev/[/url] (take warnings with a pinch of salt, not every suggestion is appropriate)');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function test404Pages($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        /*  Currently can't work
        if ($manual_checks) {
            $url = get_base_url() . '/testing-for-404.png';
            $data = http_download_file($url, null, false); // In v11 set the parameter to return output even for 404
            $this->assert_true(($data === null) || (strpos($data, '<nav class="menu_type__sitemap">') === false), '[tt]404[/tt] status code page is too complex looking for broken images');
        }
        */
    }

    /**
     * Make a URL firewall-safe.
     *
     * @param  URLPATH $url URL
     * @return URLPATH URL that is firewall-safe
     */
    protected function firewallify_url($url)
    {
        $config_ip_forwarding = get_option('ip_forwarding');

        switch ($config_ip_forwarding) {
            case '':
                return $url;

            case '1':
                $connect_to = cms_srv('SERVER_ADDR');
                if ($connect_to == '') {
                    $connect_to = '127.0.0.1'; // "localhost" can fail due to IP6
                }

                $url = preg_replace('#^(.*://)(.*)(/|:|$)#U', '$1' . $connect_to . '$3', $url);

                break;

            default:
                $protocol_end_pos = strpos($config_ip_forwarding, '://');
                if ($protocol_end_pos !== false) {
                    // Full with protocol
                    $url = preg_replace('#^(.*://)(.*)(/|$)#U', $config_ip_forwarding . '$3', $url);
                } else {
                    // IP address
                    $url = preg_replace('#^(.*://)(.*)(/|:|$)#U', '$1' . $config_ip_forwarding . '$3', $url);
                }

                break;
        }

        $opts = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'SNI_enabled' => true,
                'ciphers' => 'TLSv1',
            ),
        );
        stream_context_set_default($opts);

        return $url;
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testCookies($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $url = $this->firewallify_url($this->get_page_url());

        require_code('files');

        $headers = @get_headers($url, 1);
        if ($headers === false) {
            $this->state_check_skipped('Could not find headers for URL [url="' . $url . '"]' . $url . '[/url]');
            return;
        }

        $found_has_cookies_cookie = false;
        foreach ($headers as $key => $vals) {
            if (strtolower($key) == strtolower('Set-Cookie')) {
                if (is_string($vals)) {
                    $vals = array($vals);
                }

                foreach ($vals as $val) {
                    if (preg_match('#^has_cookies=1;#', $val) != 0) {
                        $found_has_cookies_cookie = true;
                    }

                    // Large cookies set
                    $_val = preg_replace('#^.*=#U', '', preg_replace('#; .*$#s', '', $val));
                    $this->assert_true(strlen($_val) < 100, 'Large cookie @ ' . clean_file_size(strlen($_val)));
                }

                // Too many cookies set
                $this->assert_true(count($vals) < 8, 'Many cookies are being set which is bad for performance @ ' . integer_format(count($vals)) . ' cookies');
            }
        }

        // Composr cookies not set
        $this->assert_true($found_has_cookies_cookie, 'Cookies not being properly set');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testHTTPOptimisation($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        //set_option('gzip_output', '1');   To test

        if (!php_function_allowed('stream_context_set_default')) {
            $this->state_check_skipped('PHP stream_context_set_default function not available');
            return;
        }

        $css_basename = basename(css_enforce('global', 'default'));
        $javascript_basename = basename(javascript_enforce('global', 'default'));
        if (php_function_allowed('usleep')) {
            usleep(1000000);
        } else {
            $start_time = time();
            while (time() == $start_time) {
            }
        }

        $urls = array(
            'page' => $this->get_page_url(),
            'css' => get_base_url() . '/themes/default/templates_cached/EN/' . $css_basename,
            'js' => get_base_url() . '/themes/default/templates_cached/EN/' . $javascript_basename,
            'png' => get_base_url() . '/themes/default/images/no_image.png',
        );

        foreach ($urls as $type => $url) {
            $url = $this->firewallify_url($url);

            stream_context_set_default(array('http' => array('header' => 'Accept-Encoding: gzip')));
            $headers = @get_headers($url, 1);
            if ($headers === false) {
                $this->state_check_skipped('Could not find headers for URL [url="' . $url . '"]' . $url . '[/url]');
                continue;
            }

            $is_gzip = false;
            $is_cached = null;
            foreach ($headers as $key => $vals) {
                if (is_string($vals)) {
                    $vals = array($vals);
                }

                switch (strtolower($key)) {
                    case 'content-encoding':
                        foreach ($vals as $val) {
                            if ($val == 'gzip') {
                                $is_gzip = true;
                            }
                        }

                        break;

                    case 'expires':
                        $is_cached = (strtotime($vals[0]) > time());
                        break;

                    case 'last-modified':
                        if ($is_cached === null) {
                            $is_cached = (strtotime($vals[0]) < time());
                        }
                        break;
                }
            }
            if ($is_cached === null) {
                $is_cached = false;
            }

            switch ($type) {
                case 'page':
                    $this->assert_true(!$is_cached, 'Caching should not be given for pages (except for bots, which the software will automatically do if the static cache is enabled). Full headers: ' . serialize($headers));
                    $this->assert_true($is_gzip, 'Gzip compression is not enabled/working for pages, significantly wasting bandwidth for page loads. Full headers: ' . serialize($headers));
                    break;

                case 'css':
                case 'js':
                    $this->assert_true($is_cached, 'Caching should be given for [tt].' . $type . '[/tt] files (the software will automatically make sure edited versions cache under different URLs via automatic timestamp parameters). Full headers: ' . serialize($headers));
                    $this->assert_true($is_gzip, 'Gzip compression is not enabled/working for [tt].' . $type . '[/tt] files, significantly wasting bandwidth for page loads. Full headers: ' . serialize($headers));
                    break;

                case 'png':
                    $this->assert_true($is_cached, 'Caching should be given for [tt].' . $type . '[/tt] files. Full headers: ' . serialize($headers));
                    $this->assert_true(!$is_gzip, 'Gzip compression should not be given for [tt].' . $type . '[/tt] files, they are already compressed so it is a waste of CPU power. Full headers: ' . serialize($headers));
                    break;
            }
        }
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testPageSpeed($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $threshold = floatval(get_option('hc_page_speed_threshold'));

        $page_links = $this->process_urls_into_page_links();

        foreach ($page_links as $page_link) {
            $url = page_link_to_url($page_link);

            $time_before = microtime(true);
            $data = http_download_file($url, null, false);
            $time_after = microtime(true);

            $time = ($time_after - $time_before);

            $this->assert_true($time < $threshold, 'Slow page generation speed for "' . $page_link . '" page @ ' . float_format($time) . ' seconds)');
        }

        if (time() < mktime(0, 0, 0, 10, 1, 2017)) {
            return; // LEGACY. Data was wrong in older versions, so we'll only activate this check after a while
        }

        if (addon_installed('stats')) {
            $results = $GLOBALS['SITE_DB']->query_select('stats', array('the_page', 'AVG(milliseconds) AS milliseconds'), null, 'GROUP BY the_page');
            foreach ($results as $result) {
                $time = floatval($result['milliseconds']) / 1000.0;
                $this->assert_true($time < $threshold, 'Slow page generation speed for [tt]' . $result['the_page'] . '[/tt] page @ ' . float_format($time) . ' seconds)');
            }
        }
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testSetup($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $options = array(
            'is_on_template_cache',
            'is_on_lang_cache',
            'is_on_comcode_page_cache',
            'is_on_block_cache',
        );
        foreach ($options as $option) {
            $this->assert_true(get_option($option) == '1', 'Cache option should be enabled, ' . $option);
        }

        if ($manual_checks) {
            global $SITE_INFO;
            if (!isset($SITE_INFO['self_learning_cache']) || $SITE_INFO['self_learning_cache'] == '0') {
                $this->state_check_manual('Consider enabling self-learning cache');
            }

            if ($GLOBALS['PERSISTENT_CACHE'] === null) {
                $this->state_check_manual('Consider persistent caching');
            }

            if (support_smart_decaching()) {
                $this->state_check_manual('Consider disabling smart decaching');
            }
        }
    }
}

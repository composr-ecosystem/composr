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
class _rate_limiting_test_set extends cms_test_case
{
    public function testRateLimitingWorks()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
        $config_file_path = get_file_base() . '/_config.php';
        $config_file = cms_file_get_contents_safe($config_file_path, FILE_READ_LOCK);
        file_put_contents($config_file_path, $config_file . "\n\n\$SITE_INFO['rate_limiting'] = '1';\n\$SITE_INFO['rate_limit_time_window'] = '60';\n\$SITE_INFO['rate_limit_hits_per_window'] = '3';");
        fix_permissions($config_file_path);

        $rate_limiter_path = get_custom_file_base() . '/data_custom/rate_limiter.php';
        file_put_contents($rate_limiter_path, '');

        $url = build_url(['page' => ''], '');
        for ($i = 0; $i < 4; $i++) {
            $result = cms_http_request($url->evaluate(), ['trigger_error' => false, 'timeout' => 8.0]);
            if ($i < 3) {
                $this->assertTrue($result->data !== null, 'Iteration ' . strval($i) . ' expected data but did not get any.');
                $this->assertTrue($result->message === '200', 'Iteration ' . strval($i) . ' expected status code 200 but got ' . $result->message . '.');
            } else {
                $this->assertTrue($result->data === null, 'Iteration ' . strval($i) . ' expected NO data (rate limit) but got some.');
                $this->assertTrue($result->message === '429', 'Iteration ' . strval($i) . ' expected status code 429 (rate limit) but got ' . $result->message . '.');
            }

            if ($this->debug) {
                var_dump($i, $result->data);
            }
        }

        // Output contents of rate limit file if debugging
        if ($this->debug) {
            $test = cms_file_get_contents_safe($rate_limiter_path, FILE_READ_LOCK);
            var_dump($test);
        }

        file_put_contents($config_file_path, $config_file);
        unlink($rate_limiter_path);
    }
}

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
class _rate_limiting_test_set extends cms_test_case
{
    public function testRateLimitingWorks()
    {
        $config_file_path = get_file_base() . '/_config.php';
        $config_file = file_get_contents($config_file_path);
        file_put_contents($config_file_path, $config_file . "\n\n\$SITE_INFO['rate_limiting'] = '1';\n\$SITE_INFO['rate_limit_time_window'] = '60';\n\$SITE_INFO['rate_limit_hits_per_window'] = '3';\n\$_SERVER['SERVER_ADDR'] = '';\n\$_SERVER['LOCAL_ADDR'] = '';");
        fix_permissions($config_file_path);

        $rate_limiter_path = get_file_base() . '/data_custom/rate_limiter.php';
        file_put_contents($rate_limiter_path, '');

        global $HTTP_MESSAGE;
        $url = build_url(array('page' => ''), '');
        for ($i = 0; $i < 4; $i++) {
            $data = http_download_file($url->evaluate(), null, false);
            if ($i < 3) {
                $this->assertTrue($data !== null);
                $this->assertTrue($HTTP_MESSAGE === '200');
            } else {
                $this->assertTrue($data === null);
                $this->assertTrue($HTTP_MESSAGE === '429');
            }
        }

        file_put_contents($config_file_path, $config_file);
        unlink($rate_limiter_path);
    }
}

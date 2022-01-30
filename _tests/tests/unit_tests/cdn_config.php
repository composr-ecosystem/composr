<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class cdn_config_test_set extends cms_test_case
{
    public function testCDNConfigAlternateDomains()
    {
        // Tests setting CDN to both IP and hostname, and also a prefix, and checking we see theme images distributed from all...

        $hostname = get_base_url_hostname();
        $server_ips = get_server_ips();
        $ip_address = $server_ips[0];

        if ($hostname == $ip_address) {
            $this->assertTrue(false, 'Test can only run from a hostname.');
        }

        require_code('images');

        $_cdn_config = $hostname . ';' . $ip_address . ';' . 'https://res.cloudinary.com/demo/image/fetch/,jpg,jpe,jpeg,gif,png';
        set_option('cdn', $_cdn_config);

        if ($this->debug) {
            var_dump($_cdn_config);
        }

        $a = false;
        $b = false;

        $path = get_file_base() . '/themes/default/images';
        $dh = opendir($path);
        if ($dh !== false) {
            while (($file = readdir($dh)) !== false) {
                if ($file == 'no_image.png') {
                    continue;
                }

                if (is_image($file, IMAGE_CRITERIA_WEBSAFE)) {
                    $ext = get_file_extension($file);
                    $url = find_theme_image(basename($file, '.' . $ext), false, false, 'default');
                    if (strpos($url, $hostname) !== false) {
                        $a = true;
                    }
                    if (strpos($url, $ip_address) !== false) {
                        $b = true;
                    }
                }
            }
            closedir($dh);
        }

        $this->assertTrue($a, 'Could not find usage of hostname-CDN');
        $this->assertTrue($b, 'Could not find usage of IP-CDN');

        $got = find_theme_image('no_image', false, false, 'default');
        $expected = 'https://res.cloudinary.com/demo/image/fetch/' . get_base_url() . '/themes/default/images/no_image.png';
        $this->assertTrue($got == $expected, 'Got: ' . $got);

        set_option('cdn', '');
    }
}

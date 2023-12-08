<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class cdn_config_test_set extends cms_test_case
{
    public function testCDNConfig()
    {
        require_code('images');

        set_option('cdn', 'localhost,127.0.0.1');

        $a = false;
        $b = false;

        $path = get_file_base() . '/themes/default/images';
        $dh = opendir($path);
        if ($dh !== false) {
            while (($f = readdir($dh)) !== false) {
                if (is_image($f)) {
                    $ext = get_file_extension($f);
                    $url = find_theme_image(basename($f, '.' . $ext));
                    if (strpos($url, 'localhost') !== false) {
                        $a = true;
                    }
                    if (strpos($url, '127.0.0.1') !== false) {
                        $b = true;
                    }
                }
            }
            closedir($dh);
        }

        $this->assertTrue($a && $b);

        set_option('cdn', '');
    }
}

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
class zip_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('m_zip');
    }

    public function testMZip()
    {
        // Test m-ZIP is working
        if (@file_exists('/usr/bin/unzip')) {
            $this->doPrefixedFunctionTest('m_');
        }
    }

    public function testZip()
    {
        // Test PHP's internal ZIP extension, or m-ZIP again if that's not available (but via standard PHP function names)
        $this->doPrefixedFunctionTest('');
    }

    protected function doPrefixedFunctionTest($prefix)
    {
        if (!addon_installed('cms_homesite')) {
            $this->assertTrue(false, 'The cms_homesite addon must be installed for this test to run'); // That's where our test ZIP file is from
            return;
        }

        $expected = [
            'ad-banner/banner-468x60.psd' => 419653,
            'ad-banner/banner-728x90.psd' => 4178180,
            'ad-banner/composr-banner-468x60.gif' => 89272,
            'ad-banner/composr-banner-728x90.gif' => 643225,
            'mini/a.png' => 3570,
            'mini/b.png' => 3293,
        ];

        $path = get_file_base() . '/uploads/website_specific/cms_homesite/banners.zip';
        $zip_file = call_user_func($prefix . 'zip_open', $path);
        $files = [];
        if (!is_integer($zip_file)) {
            while (($f = call_user_func($prefix . 'zip_read', $zip_file)) !== false) {
                $filename = call_user_func($prefix . 'zip_entry_name', $f);
                if (substr($filename, -1) != '/') {
                    $files[$filename] = call_user_func($prefix . 'zip_entry_filesize', $f);
                }
            }
            call_user_func($prefix . 'zip_close', $zip_file);
        }
        ksort($files);
        $this->assertTrue($files == $expected);
    }
}

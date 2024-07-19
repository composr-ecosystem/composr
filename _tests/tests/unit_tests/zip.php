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
    public function testZip()
    {
        if (!addon_installed('cms_homesite')) {
            $this->assertTrue(false, 'The cms_homesite addon must be installed for this test to run'); // That's where our test ZIP file is from
            return;
        }

        if (!class_exists('ZipArchive', false)) {
            $this->assertTrue(false, 'The PHP ZipArchive class is not available on this server');
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

        $zip_archive = new ZipArchive();

        $in_file = $zip_archive->open($path);
        if ($in_file !== true) {
            $this->assertTrue(false, zip_error($path, $in_file)->evaluate());
            return;
        }

        $files = [];
        for ($i = 0; $i < $zip_archive->numFiles; $i++) {
            $filename = $zip_archive->getNameIndex($i);
            if ($filename === false) {
                $this->assertTrue(false, 'Failed to get entry filename');
                return;
            }
            if (substr($filename, -1) != '/') {
                $zip_stats = $zip_archive->statIndex($i);
                if ($zip_stats === false) {
                    $this->assertTrue(false, 'Failed to get the stats of ' . $filename);
                    return;
                }
                $files[$filename] = $zip_stats['size'];
            }
        }

        $zip_archive->close();
        unset($zip_archive);

        // Test expected filesize

        ksort($files);
        $this->assertTrue($files == $expected, 'ZIP files do not match what was expected');
        if ($files != $expected) {
            $this->dump($files, 'EXPECTED');
            $this->dump($files, 'GOT');
        }

        // Test ZIP to TAR

        require_code('tar2');

        $files = [];
        $out_path = null;
        convert_zip_to_tar($path, $out_path);
        $out_file = tar_open($out_path, 'rb');
        $directory = tar_get_directory($out_file, true);
        if ($directory === false) {
            $this->assertTrue(false, 'Failed to read the TAR directories');
            return;
        }
        $directories = $out_file['directory'];
        foreach ($directories as $offset => $stuff) {
            $files[$stuff['path']] = $stuff['size'];
        }
        tar_close($out_file);
        ksort($files);
        $this->assertTrue($files == $expected, 'TAR files in ' . $out_path . ' do not match what was expected');
        if ($files != $expected) {
            $this->dump($files, 'EXPECTED');
            $this->dump($files, 'GOT');
        }
    }
}

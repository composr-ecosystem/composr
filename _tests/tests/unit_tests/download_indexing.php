<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class download_indexing_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('downloads2');

        set_option('dload_search_index', '1');
    }

    public function testTarIndexing()
    {
        require_code('tar');
        $temp_name = cms_tempnam();
        $tar = tar_open($temp_name, 'wb');
        tar_add_file($tar, 'test.txt', 'foobar blah', 0666, time());
        tar_close($tar);

        $data_mash = create_data_mash('foo.tar', file_get_contents($temp_name));

        $this->assertTrue(strpos($data_mash, 'foobar') !== false);

        if (function_exists('gzencode')) {
            $data_mash = create_data_mash('foo.tar.gz', gzencode(file_get_contents($temp_name)));

            $this->assertTrue(strpos($data_mash, 'foobar') !== false);
        } else {
            $this->assertTrue(false, 'PHP Gzip extension is not enabled on server, cannot run test');
        }

        unlink($temp_name);
    }

    public function testZipIndexing()
    {
        if ((!function_exists('zip_open')) && (get_option('unzip_cmd') == '')) {
            $this->assertTrue(false, 'Zip is not enabled on server, cannot run test');
            return;
        }

        $file_array = [
            [
                'name' => 'test.txt',
                'data' => 'foobar blah',
                'time' => time(),
            ],
        ];

        require_code('zip');
        $tmp_path2 = cms_tempnam();
        create_zip_file($tmp_path2, $file_array);

        $data_mash = create_data_mash('foo.zip', file_get_contents($tmp_path2));

        unlink($tmp_path2);

        $this->assertTrue(strpos($data_mash, 'foobar') !== false);
    }

    public function testPdfIndexing()
    {
        if (!addon_installed('composr_tutorials')) {
            $this->assertTrue(false, 'Composr tutorials addon needed');
            return;
        }
        if (_find_pdftohtml() == 'pdftohtml'/*could not explicitly find*/) {
            $this->assertTrue(false, 'pdftohtml is not available on server');
            return;
        }

        $data_mash = create_data_mash('pdf_sample.pdf', file_get_contents(get_file_base() . '/_tests/assets/pdf_sample.pdf'));

        $this->assertTrue(strpos($data_mash, 'incompatibilities') !== false);
    }
}

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
class download_indexing_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('downloads2');
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

        unlink($temp_name);
    }

    public function testZipIndexing()
    {
        $file_array = array(
            array(
                'name' => 'test.txt',
                'data' => 'foobar blah',
                'time' => time(),
            ),
        );
        require_code('zip');
        $data = create_zip_file($file_array, false, false, null);

        $data_mash = create_data_mash('foo.zip', $data);

        $this->assertTrue(strpos($data_mash, 'foobar') !== false);
    }
}

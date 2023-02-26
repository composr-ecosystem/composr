<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class tar_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('tar');
    }

    public function testTar()
    {
        $file1 = 'a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/example.txt';
        $to_write1 = 'test' . uniqid('', true);

        $path = cms_tempnam();

        $myfile = tar_open($path, 'wb');
        tar_add_file($myfile, $file1, $to_write1);
        tar_close($myfile);

        $myfile = tar_open($path, 'rb');

        $dir = array_values(tar_get_directory($myfile));
        $this->assertTrue(count($dir) == 1);
        $this->assertTrue($dir[0]['path'] == $file1);
        $this->assertTrue($dir[0]['size'] == strlen($to_write1));

        $c = tar_get_file($myfile, $file1);
        $this->assertTrue($dir);
        $this->assertTrue($c['data'] == $to_write1);
        $this->assertTrue($c['size'] == strlen($to_write1));

        tar_close($myfile);

        $file2 = 'sample.txt';
        $to_write2 = 'test' . uniqid('', true);

        $myfile = tar_open($path, 'c+b');
        tar_add_file($myfile, $file2, $to_write2);
        tar_close($myfile);

        $myfile = tar_open($path, 'rb');

        $dir = array_values(tar_get_directory($myfile));
        $this->assertTrue(count($dir) == 2);
        $this->assertTrue($dir[1]['path'] == $file2);
        $this->assertTrue($dir[1]['size'] == strlen($to_write2));

        $c = tar_get_file($myfile, $file2);
        $this->assertTrue($dir);
        $this->assertTrue($c['data'] == $to_write2);
        $this->assertTrue($c['size'] == strlen($to_write2));

        tar_close($myfile);

        @unlink($path);
    }
}

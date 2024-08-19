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
class tar_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('tar');
    }

    public function testBasicTar()
    {
        // Test very long directory
        $file1 = 'a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/a/example.txt';
        $to_write1 = 'test' . uniqid('', true);

        $path = cms_tempnam();

        $myfile = tar_open($path, 'wb');
        tar_add_file($myfile, $file1, $to_write1);
        tar_close($myfile);

        $myfile = tar_open($path, 'rb');

        $_dir = tar_get_directory($myfile);
        $this->assertTrue($_dir !== null, 'Expected root directory of TAR but got nothing');
        if ($_dir !== null) {
            $dir = array_values($_dir);
            $this->assertTrue(count($dir) == 1, 'Expected exactly 1 directory but got ' . strval(count($dir)));
            if (array_key_exists(0, $dir)) {
                $this->assertTrue($dir[0]['path'] == $file1, 'Expected path to match test but it did not.');
                $this->assertTrue($dir[0]['size'] == strlen($to_write1), 'Expected directory size to be ' . strval(strlen($to_write1)) . ' but it was ' . strval($dir[0]['size']));
            }
        }

        $c = tar_get_file($myfile, $file1);
        $this->assertTrue($c['data'] == $to_write1, 'Expected file data to match test but it did not.');
        $this->assertTrue($c['size'] == strlen($to_write1), 'Expected file size to be ' . strval(strlen($to_write1)) . ' but it was ' . strval($c['size']));

        tar_close($myfile);

        // Add another file, this time a basic one, into the same TAR

        $file2 = 'sample.txt';
        $to_write2 = 'test' . uniqid('', true);

        $myfile = tar_open($path, 'c+b');
        tar_add_file($myfile, $file2, $to_write2);
        tar_close($myfile);

        $myfile = tar_open($path, 'rb');

        $_dir = tar_get_directory($myfile);
        $this->assertTrue($_dir !== null, 'Expected root directory of TAR but got nothing');
        if ($_dir !== null) {
            $dir = array_values($_dir);
            $this->assertTrue(count($dir) == 2, 'Expected exactly 2 directories but got ' . strval(count($dir)));
            if (array_key_exists(1, $dir)) {
                $this->assertTrue($dir[1]['path'] == $file2, 'Expected path to match sample.txt but it did not.');
                $this->assertTrue($dir[1]['size'] == strlen($to_write2), 'Expected directory size to be ' . strval(strlen($to_write2)) . ' but it was ' . strval($dir[0]['size']));
            }
        }

        $c = tar_get_file($myfile, $file2);
        $this->assertTrue($c['data'] == $to_write2, 'Expected file data to match test but it did not.');
        $this->assertTrue($c['size'] == strlen($to_write2), 'Expected file size to be ' . strval(strlen($to_write2)) . ' but it was ' . strval($c['size']));

        tar_close($myfile);

        // Add yet another file but this one zero bytes

        $file3 = 'zero/bytes.txt';
        $to_write3 = '';

        $myfile = tar_open($path, 'c+b');
        tar_add_file($myfile, $file3, $to_write3);
        tar_close($myfile);

        $myfile = tar_open($path, 'rb');

        $_dir = tar_get_directory($myfile);
        $this->assertTrue($_dir !== null, 'Expected root directory of TAR but got nothing');
        if ($_dir !== null) {
            $dir = array_values($_dir);
            $this->assertTrue(count($dir) == 3, 'Expected exactly 3 directories but got ' . strval(count($dir)));
            if (array_key_exists(2, $dir)) {
                $this->assertTrue($dir[2]['path'] == $file3, 'Expected path to match zero/bytes.txt but it did not.');
                $this->assertTrue($dir[2]['size'] == strlen($to_write3), 'Expected directory size to be ' . strval(strlen($to_write3)) . ' but it was ' . strval($dir[0]['size']));
            }
        }

        $c = tar_get_file($myfile, $file3);
        $this->assertTrue($c['data'] == $to_write3, 'Expected file data to match test but it did not.');
        $this->assertTrue($c['size'] == strlen($to_write3), 'Expected file size to be ' . strval(strlen($to_write3)) . ' but it was ' . strval($c['size']));

        tar_close($myfile);

        @unlink($path);
    }

    public function testEmptyTar()
    {
        $path = cms_tempnam();

        $myfile = tar_open($path, 'wb');
        tar_close($myfile);

        $myfile = tar_open($path, 'rb');
        $_dir = tar_get_directory($myfile);
        $this->assertTrue($_dir !== null, 'Expected root directory of TAR but got nothing');
        if ($_dir !== null) {
            $dir = array_values($_dir);
            $this->assertTrue(count($dir) == 0, 'Expected exactly 0 directories but got ' . strval(count($dir)));
        }

        tar_close($myfile);

        @unlink($path);
    }

    public function testSuperLongFilenameTar()
    {
        $file1 = str_repeat(uniqid(), 10) . '.txt'; // Should be 133 characters
        $to_write1 = 'test' . uniqid('', true);

        $path = cms_tempnam();

        $myfile = tar_open($path, 'wb');
        tar_add_file($myfile, $file1, $to_write1);
        tar_close($myfile);

        $myfile = tar_open($path, 'rb');

        $_dir = tar_get_directory($myfile);
        $this->assertTrue($_dir !== null, 'Expected root directory of TAR but got nothing');
        if ($_dir !== null) {
            $dir = array_values($_dir);
            $this->assertTrue(count($dir) == 1, 'Expected exactly 1 directory but got ' . strval(count($dir)));
            if (array_key_exists(0, $dir)) {
                $this->assertTrue($dir[0]['path'] == $file1, 'Expected path to match test but it did not.');
                $this->assertTrue($dir[0]['size'] == strlen($to_write1), 'Expected directory size to be ' . strval(strlen($to_write1)) . ' but it was ' . strval($dir[0]['size']));
            }
        }

        $c = tar_get_file($myfile, $file1);
        $this->assertTrue($c['data'] == $to_write1, 'Expected file data to match test but it did not.');
        $this->assertTrue($c['size'] == strlen($to_write1), 'Expected file size to be ' . strval(strlen($to_write1)) . ' but it was ' . strval($c['size']));

        tar_close($myfile);

        @unlink($path);
    }

    public function testBigFileTar()
    {
        $file1 = 'bigfile.txt';
        $to_write1 = str_repeat(uniqid(), 1024); // Should be about 13kb

        $path = cms_tempnam();

        $myfile = tar_open($path, 'wb');
        tar_add_file($myfile, $file1, $to_write1);
        tar_close($myfile);

        $myfile = tar_open($path, 'rb');

        $_dir = tar_get_directory($myfile);
        $this->assertTrue($_dir !== null, 'Expected root directory of TAR but got nothing');
        if ($_dir !== null) {
            $dir = array_values($_dir);
            $this->assertTrue(count($dir) == 1, 'Expected exactly 1 directory but got ' . strval(count($dir)));
            if (array_key_exists(0, $dir)) {
                $this->assertTrue($dir[0]['path'] == $file1, 'Expected path to match test but it did not.');
                $this->assertTrue($dir[0]['size'] == strlen($to_write1), 'Expected directory size to be ' . strval(strlen($to_write1)) . ' but it was ' . strval($dir[0]['size']));
            }
        }

        $c = tar_get_file($myfile, $file1);
        $this->assertTrue($c['data'] == $to_write1, 'Expected file data to match test but it did not.');
        $this->assertTrue($c['size'] == strlen($to_write1), 'Expected file size to be ' . strval(strlen($to_write1)) . ' but it was ' . strval($c['size']));

        tar_close($myfile);

        @unlink($path);
    }
}

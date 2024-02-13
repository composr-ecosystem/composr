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
class character_sets_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('character_sets');
    }

    public function testConvertNormal()
    {
        // ISO-8859-1 --> utf-8...

        // Upper case character set names
        $input = hex2bin('a3'); // GBP symbol
        $output = convert_to_internal_encoding($input, 'ISO-8859-1', 'UTF-8');
        $this->assertTrue($output == "\u{00A3}");

        // Lower case character set names
        $input = hex2bin('a3'); // GBP symbol
        $output = convert_to_internal_encoding($input, 'iso-8859-1', 'utf-8');
        $this->assertTrue($output == "\u{00A3}");

        // utf-8 --> ISO-8859-1...

        // Upper case character set names
        $input = "\u{00A3}"; // GBP symbol
        $output = convert_to_internal_encoding($input, 'UTF-8', 'ISO-8859-1');
        $this->assertTrue($output == hex2bin('a3'));

        // Lower case character set names
        $input = "\u{00A3}"; // GBP symbol
        $output = convert_to_internal_encoding($input, 'utf-8', 'iso-8859-1');
        $this->assertTrue($output == hex2bin('a3'));
    }

    public function testConvertEntities()
    {
        $input = "\u{00A3}"; // GBP symbol
        $output = convert_to_html_encoding($input);
        $this->assertTrue($output == '&#163;');
    }

    public function testBOMs()
    {
        require_code('files');

        $path_stub = get_file_base() . '/_tests/assets/text';
        $path_a = $path_stub . '/utf-16.txt'; // Trickiest case
        $path_b = $path_stub . '/utf-8.txt';
        $path_c = $path_stub . '/iso-8859-1.txt';
        $path_d = $path_stub . '/utf-16be.txt';

        $url_stub = get_base_url() . '/_tests/assets/text';
        $url_a = $url_stub . '/utf-16.txt'; // Trickiest case
        $url_b = $url_stub . '/utf-8.txt';
        $url_c = $url_stub . '/iso-8859-1.txt';
        $url_d = $url_stub . '/utf-16be.txt';

        // Test easy reading
        $a = cms_file_get_contents_safe($path_a, FILE_READ_BOM);
        $b = cms_file_get_contents_safe($path_b, FILE_READ_BOM);
        $c = cms_file_get_contents_safe($path_c, FILE_READ_BOM, 'ISO-8859-1');
        $d = cms_file_get_contents_safe($path_d, FILE_READ_BOM);
        $this->assertTrue($a == $b);
        $this->assertTrue($a == $c);
        $this->assertTrue($a == $d);

        // Test line array reading
        $this->assertTrue(cms_file_safe($path_a) == [$a]);

        // Test byte-by-byte reading
        $charset = null;
        $myfile = cms_fopen_text_read($path_a, $charset);
        $this->assertTrue(cms_fgets($myfile, $charset) == $a);
        fclose($myfile);

        // Test easy writing
        $tmp_path = cms_tempnam();
        cms_file_put_contents_safe($tmp_path, $b, FILE_WRITE_BOM);
        $this->assertTrue(file_get_contents($tmp_path) == file_get_contents($path_b));
        unlink($tmp_path);

        // Test byte-by-byte writing
        $tmp_path = cms_tempnam();
        $myfile = cms_fopen_text_write($tmp_path);
        fwrite($myfile, $b);
        fclose($myfile);
        $this->assertTrue(file_get_contents($tmp_path) == file_get_contents($path_b));
        unlink($tmp_path);

        // Test HTTP downloader
        $_a = http_get_contents($url_a, ['convert_to_internal_encoding' => true]);
        if ($this->debug) {
            @var_dump($a);
            @var_dump($_a);
        }
        $this->assertTrue($a == $_a);
    }
}

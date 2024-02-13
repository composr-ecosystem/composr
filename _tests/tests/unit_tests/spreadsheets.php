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
class spreadsheets_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('files_spreadsheets_read');
        require_code('files_spreadsheets_write');

        $this->files = [
            'test.csv',
            'test-scsv.txt',
            'test-tsv.txt',
        ];
        if (addon_installed('enhanced_spreadsheets')) {
            $this->files = array_merge($this->files, [
                'test.ods',
                'test.xlsx',
            ]);
        }

        $this->expected = [];

        $this->expected[CMS_Spreadsheet_Reader::ALGORITHM_RAW] = [
            ['A', 'B', 'C'],
            ['A1', 'B1', 'C,"1'],
            ['A2', 'B2', 'C"2'],
            ['', '', "C3\nC3"],
            ['A4', '', ''],
        ];

        $this->expected[CMS_Spreadsheet_Reader::ALGORITHM_UNNAMED_FIELDS] = [
            ['A1', 'B1', 'C,"1'],
            ['A2', 'B2', 'C"2'],
            ['', '', "C3\nC3"],
            ['A4', '', ''],
        ];

        $this->expected[CMS_Spreadsheet_Reader::ALGORITHM_NAMED_FIELDS] = [
            ['A' => 'A1', 'B' => 'B1', 'C' => 'C,"1'],
            ['A' => 'A2', 'B' => 'B2', 'C' => 'C"2'],
            ['A' => '', 'B' => '', 'C' => "C3\nC3"],
            ['A' => 'A4', 'B' => '', 'C' => ''],
        ];
    }

    public function testRead()
    {
        if (!function_exists('zip_open') || !function_exists('xml_parser_create')) {
            $this->assertTrue(false, 'Zip and XML extensions needed for test');
            return;
        }

        $exts = [];
        foreach ($this->files as $file) {
            foreach ($this->expected as $algorithm => $expected) {
                $this->assertTrue(is_spreadsheet_readable($file));

                $sheet_reader = spreadsheet_open_read(get_file_base() . '/_tests/assets/spreadsheets/' . $file, $file, $algorithm);
                $rows = [];
                while (($row = $sheet_reader->read_row()) !== false) {
                    $rows[] = $row;
                }
                $sheet_reader->close();

                $this->assertTrue($rows == $expected, 'Failed on ' . $file . ' (algorithm '  . strval($algorithm) . ')');
                if ($this->debug) {
                    if ($rows != $expected) {
                        @var_dump($expected);
                        @var_dump($rows);
                        exit();
                    }
                }

                $exts[get_file_extension($file)] = true;
            }
        }

        $this->assertTrue(!is_spreadsheet_readable('foo.bar'));

        $_exts = explode(',', spreadsheet_read_file_types());
        sort($_exts);
        ksort($exts);
        $this->assertTrue($_exts == array_keys($exts), 'Not all file extensions covered');
    }

    public function testWrite()
    {
        if (!function_exists('zip_open') || !function_exists('xml_parser_create')) {
            $this->assertTrue(false, 'Zip and XML extensions needed for test');
            return;
        }

        foreach ($this->expected as $algorithm => $expected) {
            if ($algorithm == CMS_Spreadsheet_Reader::ALGORITHM_UNNAMED_FIELDS) {
                continue; // Not supported for write
            }

            foreach ($this->files as $file) {
                $this->assertTrue(is_spreadsheet_writable($file));

                // Write out
                $path = null; // Will be written by reference
                $sheet_writer = spreadsheet_open_write($path, $file, $algorithm);
                foreach ($expected as $row) {
                    $sheet_writer->write_row($row);
                }
                $sheet_writer->close();

                // Read back in and compare
                $sheet_reader = spreadsheet_open_read($path, $file, $algorithm);
                $rows = [];
                while (($row = $sheet_reader->read_row()) !== false) {
                    $rows[] = $row;
                }
                $sheet_reader->close();
                $this->assertTrue($rows == $expected, 'Failed on ' . $file . ' (algorithm '  . strval($algorithm) . ')');
                if ($this->debug) {
                    if ($rows != $expected) {
                        @var_dump($expected);
                        @var_dump($rows);
                        exit();
                    }
                }
            }
        }

        $this->assertTrue(!is_spreadsheet_writable('foo.bar'));

        if (addon_installed('enhanced_spreadsheets')) {
            $this->assertTrue(spreadsheet_write_default() == 'ods');
        } else {
            $this->assertTrue(spreadsheet_write_default() == 'csv');
        }
    }
}

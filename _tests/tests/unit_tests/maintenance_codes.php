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
class maintenance_codes_test_set extends cms_test_case
{
    public function testMaintenanceSheetStructure()
    {
        require_code('files_spreadsheets_read');
        $sheet_reader = spreadsheet_open_read(get_file_base() . '/data/maintenance_status.csv', null, CMS_Spreadsheet_Reader::ALGORITHM_RAW);

        $line = 1;
        while (($row = $sheet_reader->read_row()) !== false) {
            $this->assertTrue(count($row) == 7, 'Wrong number of columns on line ' . integer_format($line) . ', got ' . integer_format(count($row)) . ' expected 7');

            if ($line != 1) {
                $this->assertTrue(preg_match('#^\w+$#', $row[0]) != 0, 'Invalid codename ' . $row[0]);
                $this->assertTrue(preg_match('#^(Yes|No)$#', $row[5]) != 0, 'Invalid "Non-bundled addon" column, ' . $row[5] . ' for ' . $row[0]);
            }

            $line++;
        }

        $sheet_reader->close();
    }

    public function testMaintenanceCodeReferences()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $codenames = [];
        require_code('files_spreadsheets_read');
        $sheet_reader = spreadsheet_open_read(get_file_base() . '/data/maintenance_status.csv');
        while (($row = $sheet_reader->read_row()) !== false) {
            $codename = $row['Codename'];
            $codenames[$codename] = true;
        }
        $sheet_reader->close();

        // Test PHP code
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $_c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = [];
            $num_matches = preg_match_all('#is_maintained\(\'([^\']*)\'\)#', $_c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $codename = $matches[1][$i];
                $this->assertTrue(isset($codenames[$codename]), 'Broken maintenance code referenced in PHP code, ' . $codename);
            }
        }

        // Test config options
        $config_hooks = find_all_hook_obs('systems', 'config', 'Hook_config_');
        foreach ($config_hooks as $ob) {
            $details = $ob->get_details();
            if (isset($details['maintenance_code'])) {
                $codename = $details['maintenance_code'];
                $this->assertTrue(isset($codenames[$codename]), 'Broken maintenance code referenced in config option, ' . $codename);
            }
        }

        // Test tutorials
        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file[0] == '.') {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                $matches = [];
                $num_matches = preg_match_all('#\{\$IS_MAINTAINED,(\w+),#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $codename = $matches[1][$i];
                    $this->assertTrue(isset($codenames[$codename]), 'Broken maintenance code reference in tutorial, ' . $codename);
                }
            }
        }
        closedir($dh);

        // third_party_code test also tests some references
    }

    public function testHealthCheckReferences()
    {
        if (addon_installed('health_check')) {
            require_code('health_check');
            $sections = [];
            foreach (find_health_check_categories_and_sections(true) as $_sections) {
                $sections = array_merge($sections, $_sections);
            }

            require_code('files_spreadsheets_read');
            $sheet_reader = spreadsheet_open_read(get_file_base() . '/data/maintenance_status.csv');
            while (($row = $sheet_reader->read_row()) !== false) {
                $matches = [];
                if (preg_match('#(\w+) Health Check([^s]|$)#', $row['Testing automation'], $matches) != 0) {
                    $health_check = $matches[1];
                    if (($health_check != 'N/A') && (strpos($health_check, 'TODO') === false)) {
                        $this->assertTrue(array_key_exists($health_check, $sections), 'Missing Health Check: ' . $health_check . ' for ' . $row['Codename']);
                    }
                }
            }
            $sheet_reader->close();
        }
    }

    public function testTestReferences()
    {
        // Test maintenance sheet...

        require_code('files_spreadsheets_read');
        $sheet_reader = spreadsheet_open_read(get_file_base() . '/data/maintenance_status.csv');
        while (($row = $sheet_reader->read_row()) !== false) {
            $matches = [];
            if (preg_match('#(\w+) automated test#', $row['Testing automation'], $matches) != 0) {
                $test = $matches[1];
                $this->assertTrue(is_file(get_file_base() . '/_tests/tests/unit_tests/' . $test . '.php'), 'Could not find referenced test, ' . $test);
            }
        }
        $sheet_reader->close();

        // Test coding standards tutorial...

        $c = cms_file_get_contents_safe(get_file_base() . '/docs/pages/comcode_custom/EN/codebook_standards.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

        $matches = [];
        $num_matches = preg_match_all('#Automated test \(\[tt\](\w+)\[/tt\]\)#i', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $test = $matches[1][$i];
            $this->assertTrue(is_file(get_file_base() . '/_tests/tests/unit_tests/' . $test . '.php'), 'Could not find referenced test, ' . $test);
        }

        // third_party_code test also tests some references
    }
}

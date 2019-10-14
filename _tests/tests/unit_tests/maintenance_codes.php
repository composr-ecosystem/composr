<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class maintenance_codes_test_set extends cms_test_case
{
    public function testMaintenanceSheetStructure()
    {
        $myfile = fopen(get_file_base() . '/data/maintenance_status.csv', 'rb');
        // TODO: #3467
        // TODO: #3032 (must default charset to utf-8 if no BOM though)

        $line = 1;
        while (($row = fgetcsv($myfile)) !== false) {
            $this->assertTrue(count($row) == 7, 'Wrong number of columns on line ' . integer_format($line) . ', got ' . integer_format(count($row)) . ' expected 7');

            if ($line != 1) {
                $this->assertTrue(preg_match('#^\w+$#', $row[0]) != 0, 'Invalid codename ' . $row[0]);
                $this->assertTrue(preg_match('#^(Yes|No)$#', $row[5]) != 0, 'Invalid "Non-bundled addon" column, ' . $row[5]);
            }

            $line++;
        }

        fclose($myfile);
    }

    public function testMaintenanceCodeReferences()
    {
        $myfile = fopen(get_file_base() . '/data/maintenance_status.csv', 'rb');
        // TODO: #3467
        // TODO: #3032 (must default charset to utf-8 if no BOM though)

        $header_row = fgetcsv($myfile); // Header row
        unset($header_row[0]);

        $codenames = array();
        while (($row = fgetcsv($myfile)) !== false) {
            $codename = $row[0];
            $codenames[$codename] = true;
        }

        fclose($myfile);

        // Test PHP code
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, array('php'));
        $files[] = 'install.php';
        foreach ($files as $path) {
            $_c = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $matches = array();
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
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                $matches = array();
                $num_matches = preg_match_all('#\{\$IS_MAINTAINED,(\w+),#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $codename = $matches[1][$i];
                    $this->assertTrue(isset($codenames[$codename]), 'Broken maintenance code reference in tutorial, ' . $codename);
                }
            }
        }
        closedir($dh);
    }

    public function testTestReferences()
    {
        // Test maintenance sheet...

        $myfile = fopen(get_file_base() . '/data/maintenance_status.csv', 'rb');
        // TODO: #3467
        // TODO: #3032 (must default charset to utf-8 if no BOM though)

        $header_row = fgetcsv($myfile); // Header row
        unset($header_row[0]);

        while (($row = fgetcsv($myfile)) !== false) {
            $testing = isset($row[6]) ? $row[6] : '';

            $matches = array();
            if (preg_match('#(\w+) automated test#', $testing, $matches) != 0) {
                $test = $matches[1];
                $this->assertTrue(is_file(get_file_base() . '/_tests/tests/unit_tests/' . $test . '.php'), 'Could not find referenced test, ' . $test);
            }
        }

        fclose($myfile);

        // Test coding standards tutorial...

        $c = cms_file_get_contents_safe(get_file_base() . '/docs/pages/comcode_custom/EN/codebook_standards.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

        $matches = array();
        $num_matches = preg_match_all('#Automated test \(\[tt\](\w+)\[/tt\]\)#i', $c, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $test = $matches[1][$i];
            $this->assertTrue(is_file(get_file_base() . '/_tests/tests/unit_tests/' . $test . '.php'), 'Could not find referenced test, ' . $test);
        }
    }
}

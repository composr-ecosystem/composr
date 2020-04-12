<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class third_party_code_test_set extends cms_test_case
{
    protected $third_party_code = [];
    protected $third_party_apis = [];

    public function setUp()
    {
        parent::setUp();

        require_code('third_party_code');

        require_code('files_spreadsheets_read');

        $this->third_party_code = [];
        $sheet_reader = spreadsheet_open_read(get_file_base() . '/data_custom/third_party_code.csv');
        while (($row = $sheet_reader->read_row()) !== false) {
            $this->third_party_code[] = $row;
        }
        $sheet_reader->close();

        $this->third_party_apis = [];
        $sheet_reader = spreadsheet_open_read(get_file_base() . '/data_custom/third_party_apis.csv');
        while (($row = $sheet_reader->read_row()) !== false) {
            $this->third_party_apis[] = $row;
        }
        $sheet_reader->close();
    }

    public function testCodeReferencesExist()
    {
        $dirs = list_untouchable_third_party_directories();
        foreach ($dirs as $dir) {
            if (in_array($dir, [
                'data_custom/ckeditor',
                'docs/api',
                '_old',
                'themes/_unnamed_/templates_cached/EN',
                'vendor',
            ])) {
                continue;
            }

            $this->assertTrue(is_dir(get_file_base() . '/' . $dir), 'Missing: ' . $dir);
        }

        $files = list_untouchable_third_party_files();
        foreach ($files as $file) {
            $this->assertTrue(is_file(get_file_base() . '/' . $file), 'Missing: ' . $file);
        }
    }

    public function testBundledLicencing()
    {
        $licence = file_get_contents(get_file_base() . '/text/EN/licence.txt');

        foreach ($this->third_party_code as $row) {
            if (substr($row['Project'], 0, 1) == '(') {
                continue;
            }

            if ($row['Bundled?'] == 'Yes') {
                $this->assertTrue(strpos($licence, $row['Project']) !== false, 'Project not apparently referenced in licencing, ' . $row['Project']);
            }
        }
    }

    public function testSyncDates()
    {
        foreach ($this->third_party_code as $row) {
            if (($row['Intention'] != 'No action') && ($row['Last sync/review date'] != 'N/A') && ($row['Last sync/review date'] != 'TODO')) {
                $last_date = strtotime($row['Last sync/review date']);
                $this->assertTrue($last_date > time() - 60 * 60 * 24 * 365, 'Need to reconsider integration of ' . $row['Project'] . 'SDK');
            }

            $ok = (strpos($row['Last sync/review date'], 'TODO') === false) && (strpos($row['Unit test?'], 'TODO') === false) && (strpos($row['Health check?'], 'TODO') === false);
            $this->assertTrue($ok, 'TODO for ' . $row['Project']);
        }

        foreach ($this->third_party_apis as $row) {
            if (($row['Intention'] != 'No action') && ($row['Last sync/review date'] != 'N/A') && ($row['Last sync/review date'] != 'TODO')) {
                $last_date = strtotime($row['Last sync/review date']);
                $this->assertTrue($last_date > time() - 60 * 60 * 24 * 365, 'Need to reconsider integration of ' . $row['API'] . ' API');
            }

            $ok = (strpos($row['Last sync/review date'], 'TODO') === false) && (strpos($row['Unit test?'], 'TODO') === false) && (strpos($row['Health check?'], 'TODO') === false);
            $this->assertTrue($ok, 'TODO for ' . $row['API']);
        }
    }

    public function testMaintenanceCodeReferences()
    {
        $codenames = [];
        require_code('files_spreadsheets_read');
        $sheet_reader = spreadsheet_open_read(get_file_base() . '/data/maintenance_status.csv');
        while (($row = $sheet_reader->read_row()) !== false) {
            $codename = $row['Codename'];
            $codenames[$codename] = true;
        }
        $sheet_reader->close();

        foreach ($this->third_party_code as $row) {
            if ($row['Maintenance codename'] != '') {
                $this->assertTrue(isset($codenames[$row['Maintenance codename']]), 'Missing maintenance code: ' . $row['Maintenance codename']);
            }
        }

        foreach ($this->third_party_apis as $row) {
            if ($row['Maintenance codename'] != '') {
                $this->assertTrue(isset($codenames[$row['Maintenance codename']]), 'Missing maintenance code: ' . $row['Maintenance codename']);
            }
        }
    }

    public function testHealthCheckReferences()
    {
        if (addon_installed('health_check')) {
            require_code('health_check');
            $sections = [];
            foreach (find_health_check_categories_and_sections() as $_sections) {
                $sections = array_merge($sections, $_sections);
            }

            foreach ($this->third_party_code as $row) {
                if (($row['Health check?'] != 'N/A') && (strpos($row['Health check?'], 'TODO') === false)) {
                    $this->assertTrue(array_key_exists($row['Health check?'], $sections), 'Missing Health Check: ' . $row['Health check?']);
                }
            }

            foreach ($this->third_party_apis as $row) {
                if (($row['Health check?'] != 'N/A') && (strpos($row['Health check?'], 'TODO') === false)) {
                    $this->assertTrue(array_key_exists($row['Health check?'], $sections), 'Missing Health Check: ' . $row['Health check?']);
                }
            }
        }
    }

    public function testTestReferences()
    {
        foreach ($this->third_party_code as $row) {
            if (($row['Unit test?'] != 'N/A') && (strpos($row['Unit test?'], 'TODO') === false)) {
                $this->assertTrue(is_file(get_file_base() . '/_tests/tests/unit_tests/' . $row['Unit test?'] . '.php'), 'Could not find referenced test, ' . $row['Unit test?']);
            }
        }

        foreach ($this->third_party_apis as $row) {
            if (($row['Unit test?'] != 'N/A') && (strpos($row['Unit test?'], 'TODO') === false)) {
                $this->assertTrue(is_file(get_file_base() . '/_tests/tests/unit_tests/' . $row['Unit test?'] . '.php'), 'Could not find referenced test, ' . $row['Unit test?']);
            }
        }
    }
}

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
class __third_party_code_test_set extends cms_test_case
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
        if (($this->only !== null) && ($this->only != 'testCodeReferencesExist')) {
            return;
        }

        $dirs = list_untouchable_third_party_directories();
        foreach ($dirs as $dir) {
            // Exceptions, stuff that does not exist in Git
            if (in_array($dir, [
                'data_custom/ckeditor',
                'docs/api',
                '_old',
                'themes/_unnamed_/templates_cached/EN',
                'vendor',
                'uploads/website_specific/test',
                'nbproject',
            ])) {
                continue;
            }

            $this->assertTrue(is_dir(get_file_base() . '/' . $dir), 'Missing: ' . $dir);
        }

        $files = list_untouchable_third_party_files();
        foreach ($files as $file) {
            // Exceptions
            if (in_array($file, [
                '_tests/codechecker/codechecker.ini',
                'data_custom/execute_temp.php',
            ])) {
                continue;
            }

            $this->assertTrue(is_file(get_file_base() . '/' . $file), 'Missing: ' . $file);
        }
    }

    public function testStrewnThirdPartyCodeMarked()
    {
        if (($this->only !== null) && ($this->only != 'testStrewnThirdPartyCodeMarked')) {
            return;
        }

        // So CQC tests and PHP-doc parser do not need to be smart about what they are skipping
        $files = list_untouchable_third_party_files();
        foreach ($files as $file) {
            if ((strpos($file, '_custom') === false) && (substr($file, -4) == '.php') && ($file != '_config.php')) {
                $c = file_get_contents(get_file_base() . '/' . $file);

                $this->assertTrue(strpos($c, '/*CQC: No API check*/'), 'No API check missing from: ' . $file);
                $this->assertTrue(strpos($c, '/*CQC: No check*/'), 'No check missing from: ' . $file);
            }
        }
    }

    public function testBundledLicencing()
    {
        if (($this->only !== null) && ($this->only != 'testBundledLicencing')) {
            return;
        }

        $licence = file_get_contents(get_file_base() . '/docs/THANKS.md');

        foreach ($this->third_party_code as $row) {
            if (substr($row['Project'], 0, 1) == '(') {
                continue;
            }

            if ($row['Bundled?'] == 'Yes') {
                $this->assertTrue(strpos($licence, $row['Project']) !== false, 'Project not apparently referenced in THANKS.md, ' . $row['Project']);
            }
        }
    }

    public function testSyncDates()
    {
        if (($this->only !== null) && ($this->only != 'testSyncDates')) {
            return;
        }

        $years_between_reviews = 3;

        foreach ($this->third_party_code as $row) {
            if (($row['Intention'] != 'No action') && ($row['Last sync/review date'] != 'N/A') && ($row['Last sync/review date'] != 'TODO')) {
                $last_date = strtotime($row['Last sync/review date']);
                $this->assertTrue($last_date > time() - 60 * 60 * 24 * 365 * $years_between_reviews, 'Need to review integration of ' . $row['Project'] . ' SDK');
            }

            $this->assertTrue(strpos($row['Last sync/review date'], 'TODO') === false, 'Review-TODO for ' . $row['Project']);
            $this->assertTrue(strpos($row['Unit test?'], 'TODO') === false, 'Unit-test-TODO for ' . $row['Project']);
            $this->assertTrue(strpos($row['Health check?'], 'TODO') === false, 'Health-check-TODO for ' . $row['Project']);
        }

        foreach ($this->third_party_apis as $row) {
            if (($row['Intention'] != 'No action') && ($row['Last sync/review date'] != 'N/A') && ($row['Last sync/review date'] != 'TODO')) {
                $last_date = strtotime($row['Last sync/review date']);
                $this->assertTrue($last_date > time() - 60 * 60 * 24 * 365 * $years_between_reviews, 'Need to review integration of ' . $row['API'] . ' API');
            }

            $this->assertTrue(strpos($row['Last sync/review date'], 'TODO') === false, 'Review-TODO for ' . $row['API']);
            $this->assertTrue(strpos($row['Unit test?'], 'TODO') === false, 'Unit-test-TODO for ' . $row['API']);
            $this->assertTrue(strpos($row['Health check?'], 'TODO') === false, 'Health-check-TODO for ' . $row['API']);
        }
    }

    public function testMaintenanceCodeReferences()
    {
        if (($this->only !== null) && ($this->only != 'testMaintenanceCodeReferences')) {
            return;
        }

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
        if (($this->only !== null) && ($this->only != 'testHealthCheckReferences')) {
            return;
        }

        if (addon_installed('health_check')) {
            require_code('health_check');
            $sections = [];
            foreach (find_health_check_categories_and_sections(true) as $_sections) {
                $sections = array_merge($sections, $_sections);
            }

            foreach ($this->third_party_code as $row) {
                if (($row['Health check?'] != 'N/A') && (strpos($row['Health check?'], 'TODO') === false)) {
                    $this->assertTrue(array_key_exists($row['Health check?'], $sections), 'Missing Health Check for ' . $row['Project'] . ': ' . $row['Health check?']);
                }
            }

            foreach ($this->third_party_apis as $row) {
                if (($row['Health check?'] != 'N/A') && (strpos($row['Health check?'], 'TODO') === false)) {
                    $this->assertTrue(array_key_exists($row['Health check?'], $sections), 'Missing Health Check for ' . $row['API'] . ': ' . $row['Health check?']);
                }
            }
        }
    }

    public function testTestReferences()
    {
        if (($this->only !== null) && ($this->only != 'testTestReferences')) {
            return;
        }

        foreach ($this->third_party_code as $row) {
            if (($row['Unit test?'] != 'N/A') && (strpos($row['Unit test?'], 'TODO') === false)) {
                $this->assertTrue(is_file(get_file_base() . '/_tests/tests/unit_tests/' . $row['Unit test?'] . '.php'), 'Could not find referenced test for ' . $row['Project'] . ', ' . $row['Unit test?']);
            }
        }

        foreach ($this->third_party_apis as $row) {
            if (($row['Unit test?'] != 'N/A') && (strpos($row['Unit test?'], 'TODO') === false)) {
                $this->assertTrue(is_file(get_file_base() . '/_tests/tests/unit_tests/' . $row['Unit test?'] . '.php'), 'Could not find referenced test for ' . $row['API'] . ', ' . $row['Unit test?']);
            }
        }
    }

    public function testThirdPartySoftwareConfigFiles()
    {
        $matches = [];

        $c = file_get_contents(get_file_base() . '/.phpcs.xml');
        $num_matches = preg_match_all('#<exclude-pattern>(.*)</exclude-pattern>#', $c, $matches);
        $phpcs = [];
        for ($i = 0; $i < $num_matches; $i++) {
            $phpcs[$matches[1][$i]] = true;
        }

        $eslintignore = array_flip(array_map('trim', file(get_file_base() . '/.eslintignore')));

        $c = file_get_contents(get_file_base() . '/phpdoc.dist.xml');
        $num_matches = preg_match_all('#<ignore>(.*)</ignore>#', $c, $matches);
        $phpdoc = [];
        for ($i = 0; $i < $num_matches; $i++) {
            $phpdoc[$matches[1][$i]] = true;
        }

        $dirs = list_untouchable_third_party_directories();
        foreach ($dirs as $dir) {
            if (($this->only === null) || ($this->only == 'phpcs')) {
                /* We can not expect all directories skipped for this
                $this->assertTrue(isset($phpcs[$dir]), 'Missing reference for phpcs: ' . $dir);
                */
                unset($phpcs[$dir]);
            }

            if (($this->only === null) || ($this->only == 'eslintignore')) {
                $_dir = '/' . $dir . '/*';
                /* We can not expect all directories skipped for this
                $this->assertTrue(isset($eslintignore[$_dir]), 'Missing reference for .eslintignore: ' . $_dir);
                */
                unset($eslintignore[$_dir]);
            }

            if (($this->only === null) || ($this->only == 'phpdoc')) {
                $_dir = $dir . '/';
                if (substr($_dir, 0, 8) == 'sources/') {
                    $this->assertTrue(isset($phpdoc[$_dir]), 'Missing reference for phpdoc: ' . $_dir);
                }
                unset($phpdoc[$_dir]);
            }
        }
        $files = list_untouchable_third_party_files();
        foreach ($files as $file) {
            if (($this->only === null) || ($this->only == 'phpcs')) {
                if (substr($file, -4) == '.php') {
                    $this->assertTrue(isset($phpcs[$file]), 'Missing reference for phpcs: ' . $file);
                }
                unset($phpcs[$file]);
            }

            if (($this->only === null) || ($this->only == 'eslintignore')) {
                $_file = '/' . $file;
                if (substr($file, -3) == '.js') {
                    $this->assertTrue(isset($eslintignore[$_file]), 'Missing reference for .eslintignore: ' . $_file);
                }
                unset($eslintignore[$_file]);
            }

            if (($this->only === null) || ($this->only == 'phpdoc')) {
                if ((substr($file, -4) == '.php') && (substr($file, 0, 8) == 'sources/')) {
                    $this->assertTrue(isset($phpdoc[$file]), 'Missing reference for phpdoc: ' . $file);
                }
                unset($phpdoc[$file]);
            }
        }

        if (($this->only === null) || ($this->only == 'phpcs')) {
            // Exceptions that .eslintignore includes for non-third-party-code reasons
            unset($phpcs['*.js']);
            unset($phpcs['*.css']);

            foreach (array_keys($phpcs) as $path) {
                if (in_array($path, [
                    'sources/jsmin.php',
                    'sources/lang_stemmer_EN.php',
                    'sources/diff',
                ])) {
                    continue;
                }

                $this->assertTrue(false, 'Unexpected reference for phpcs: ' . $path);
            }
        }

        if (($this->only === null) || ($this->only == 'eslintignore')) {
            // Exceptions that .eslintignore includes for non-third-party-code reasons
            unset($eslintignore['/themes/default/javascript/_attachment_ui_defaults.js']);
            unset($eslintignore['/themes/default/javascript/password_checks.js']);
            unset($eslintignore['/themes/default/javascript/_wysiwyg_settings.js']);
            unset($eslintignore['/themes/*/templates_cached/*']);

            foreach (array_keys($eslintignore) as $path) {
                $this->assertTrue(false, 'Unexpected reference for eslintignore: ' . $path);
            }
        }

        if (($this->only === null) || ($this->only == 'phpdoc')) {
            foreach (array_keys($phpdoc) as $path) {
                // Exceptions
                if (preg_match('#^sources/(hooks|blocks)/$#', $path) != 0) {
                    continue;
                }
                if (preg_match('#^sources/(forum|database)/#', $path) != 0) {
                    continue;
                }
                if (preg_match('#^sources/diff/#', $path) != 0) {
                    continue;
                }
                if (in_array($path, [
                    'sources/minikernel.php',
                    'sources/jsmin.php',
                    'sources/lang_stemmer_EN.php',
                ])) {
                    continue;
                }

                $this->assertTrue(false, 'Unexpected reference for phpdoc: ' . $path);
            }
        }
    }
}

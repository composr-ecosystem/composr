<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/*EXTRA FUNCTIONS: shell_exec*/

/*
Use &test_mode=1 for using non-live test data.
*/

function init__cms_homesite()
{
    define('LEAD_DEVELOPER_MEMBER_ID', 2);
}

// IDENTIFYING RELEASES
// --------------------

function get_latest_version_dotted()
{
    static $version = null; // null means unset (uncached)
    if ($version === null) {
        $_version = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'name', [$GLOBALS['SITE_DB']->translate_field_ref('additional_details') => 'This is the latest version.']);
        if ($_version === null) {
            $version = '0.0'; // unknown
        } else {
            require_code('version2');
            $__version = preg_replace('# \(.*#', '', $_version);
            $version = get_version_dotted__from_anything($__version);
        }
        $fetched_version = true;
    }
    return ($version == '0.0') ? null : $version;
}

function get_latest_version_pretty()
{
    $version_dotted = get_latest_version_dotted();
    if ($version_dotted === null) {
        return null;
    }
    return get_version_pretty__from_dotted($version_dotted);
}

function get_latest_version_basis_number()
{
    require_code('version2');
    $latest_pretty = get_latest_version_pretty();
    if (($latest_pretty === null) && ($GLOBALS['DEV_MODE'])) { // Not uploaded any releases to dev site?
        $latest_pretty = float_to_raw_string(cms_version_number(), 2, true);
    }
    if ($latest_pretty === null) {
        return null;
    }

    $latest_dotted = get_version_dotted__from_anything($latest_pretty);
    list($_latest_number) = get_version_components__from_dotted($latest_dotted);
    return floatval($_latest_number);
}

function get_release_tree($type = 'manual')
{
    require_code('version2');

    $versions = [];

    global $DOWNLOAD_ROWS;
    load_version_download_rows();

    foreach ($DOWNLOAD_ROWS as $download_row) {
        $matches = [];
        if (preg_match('#^Composr Version (.*) \((.*)\)$#', $download_row['nice_title'], $matches) != 0) {
            if (strpos($matches[2], $type) === false) {
                continue;
            }
            $version_dotted = get_version_dotted__from_anything($matches[1]);
            list(, $qualifier, $qualifier_number, $long_dotted_number, , $long_dotted_number_with_qualifier) = get_version_components__from_dotted($version_dotted);
            $versions[$long_dotted_number_with_qualifier] = $download_row;
        }
    }

    uksort($versions, 'version_compare');

    $_versions = [];
    foreach ($versions as $long_dotted_number_with_qualifier => $download_row) {
        $_versions[preg_replace('#\.0$#', '', $long_dotted_number_with_qualifier)] = $download_row;
    }

    return $_versions;
}

function is_release_discontinued($version)
{
    // LEGACY: update as required
    $discontinued = ['1', '2', '2.1', '2.5', '2.6', '3', '3.1', '3.2', '4', '5', '6', '7', '8', '9', '10.1'];
    return (preg_match('#^(' . implode('|', array_map('preg_quote', $discontinued)) . ')($|\.)#', $version) != 0);
}

function find_version_download_fast($version_pretty, $type_wanted = 'manual', $version_must_be_newer_than = null)
{
    if ($GLOBALS['DEV_MODE']) {
        $t = 'Composr version 1337';

        $myrow = [
            'd_id' => 123,
            'num_downloads' => 321,
            'name' => $t . '(' . $type_wanted . ')',
            'file_size' => 12345,
        ];
    } else {
        $sql = 'SELECT d.num_downloads,d.name,d.file_size,d.id AS d_id,d.add_date FROM ' . get_table_prefix() . 'download_downloads d' . $GLOBALS['SITE_DB']->prefer_index('download_downloads', 'downloadauthor');
        $sql .= ' WHERE ' . db_string_equal_to('author', 'Core Development Team') . ' AND validated=1';
        $like = 'Composr Version ';
        $like .= (($version_pretty === null) ? '%' : $version_pretty);
        if ($type_wanted != '') {
            $like .= ' (' . $type_wanted . ')';
        }
        $sql .= ' AND ' . $GLOBALS['SITE_DB']->translate_field_ref('name') . ' LIKE \'' . db_encode_like('%' . $like) . '\'';
        $sql .= ' ORDER BY add_date DESC';
        $rows = $GLOBALS['SITE_DB']->query($sql, 1, 0, false, false, ['name' => 'SHORT_TRANS']);
        if (!array_key_exists(0, $rows)) {
            return null; // Shouldn't happen, but let's avoid transitional errors
        }

        $myrow = $rows[0];
    }
    return $myrow;
}

function find_version_download($version_pretty, $type_wanted = 'manual')
{
    global $DOWNLOAD_ROWS;
    load_version_download_rows();

    $download_row = null;
    foreach ($DOWNLOAD_ROWS as $download_row) {
        $nice_title_stripped = preg_replace('# \(.*\)$#', '', $download_row['nice_title']);
        if ($nice_title_stripped == 'Composr Version ' . $version_pretty) {
            $is_manual = (strpos($download_row['nice_title'], 'manual') !== false);
            if (($is_manual && $type_wanted == 'manual') || (!$is_manual && $type_wanted == 'quick')) {
                return $download_row;
            }
        }
    }

    return null;
}

function load_version_download_rows()
{
    global $DOWNLOAD_ROWS;
    if (!isset($DOWNLOAD_ROWS)) {
        if (get_param_integer('test_mode', 0) == 1) {
            // Test data
            $DOWNLOAD_ROWS = [
                ['id' => 20, 'edit_date' => time() - 60 * 60 * 8, 'nice_title' => 'Composr Version 13 (manual)', 'add_date' => time() - 60 * 60 * 8, 'nice_description' => 'Manual installer (as opposed to the regular quick installer). Please note this isn\'t documentation.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/a.zip'],
                ['id' => 30, 'edit_date' => time() - 60 * 60 * 5, 'nice_title' => 'Composr Version 13.1 (manual)', 'add_date' => time() - 60 * 60 * 5, 'nice_description' => '', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/b.zip'],
                ['id' => 35, 'edit_date' => time() - 60 * 60 * 4, 'nice_title' => 'Composr Version 13.1.1 (manual)', 'add_date' => time() - 60 * 60 * 5, 'nice_description' => 'Manual installer (as opposed to the regular quick installer). Please note this isn\'t documentation.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/c.zip'],
                ['id' => 40, 'edit_date' => time() - 60 * 60 * 4, 'nice_title' => 'Composr Version 13.2 beta1 (manual)', 'add_date' => time() - 60 * 60 * 4, 'nice_description' => 'Manual installer (as opposed to the regular quick installer). Please note this isn\'t documentation.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/d.zip'],
                ['id' => 50, 'edit_date' => time() - 60 * 60 * 3, 'nice_title' => 'Composr Version 13.2 (manual)', 'add_date' => time() - 60 * 60 * 3, 'nice_description' => 'Manual installer (as opposed to the regular quick installer). Please note this isn\'t documentation.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/e.zip'],
                ['id' => 60, 'edit_date' => time() - 60 * 60 * 1, 'nice_title' => 'Composr Version 14 (manual)', 'add_date' => time() - 60 * 60 * 1, 'nice_description' => 'Manual installer (as opposed to the regular quick installer). Please note this isn\'t documentation.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/f.zip'],

                ['id' => 20, 'edit_date' => time() - 60 * 60 * 8, 'nice_title' => 'Composr Version 13 (quick)', 'add_date' => time() - 60 * 60 * 8, 'nice_description' => '[Test message] This is 3. Yo peeps. 3.1 is the biz.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/a.zip'],
                ['id' => 30, 'edit_date' => time() - 60 * 60 * 5, 'nice_title' => 'Composr Version 13.1 (quick)', 'add_date' => time() - 60 * 60 * 5, 'nice_description' => '[Test message] This is 3.1.1. 3.1.1 is out dudes.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/b.zip'],
                ['id' => 35, 'edit_date' => time() - 60 * 60 * 4, 'nice_title' => 'Composr Version 13.1.1 (quick)', 'add_date' => time() - 60 * 60 * 5, 'nice_description' => '[Test message] This is 3.1.1. 3.2 is out dudes.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/c.zip'],
                ['id' => 40, 'edit_date' => time() - 60 * 60 * 4, 'nice_title' => 'Composr Version 13.2 beta1 (quick)', 'add_date' => time() - 60 * 60 * 4, 'nice_description' => '[Test message] This is 3.2 beta1. 3.2 beta2 is out.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/d.zip'],
                ['id' => 50, 'edit_date' => time() - 60 * 60 * 3, 'nice_title' => 'Composr Version 13.2 (quick)', 'add_date' => time() - 60 * 60 * 3, 'nice_description' => '[Test message] This is 3.2. 4 is out.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/e.zip'],
                ['id' => 60, 'edit_date' => time() - 60 * 60 * 1, 'nice_title' => 'Composr Version 14 (quick)', 'add_date' => time() - 60 * 60 * 1, 'nice_description' => '[Test message] This is the 4 and you can find bug reports somewhere.', 'url' => 'uploads/website_specific/cms_homesite/upgrades/sample_data/f.zip'],
            ];
        } else {
            // Live data
            $sql = 'SELECT d.* FROM ' . get_table_prefix() . 'download_downloads d' . $GLOBALS['SITE_DB']->prefer_index('download_downloads', 'recent_downloads', false);
            $sql .= ' WHERE validated=1 AND ' . $GLOBALS['SITE_DB']->translate_field_ref('name') . ' LIKE \'' . db_encode_like('Composr Version %') . '\' ORDER BY add_date';
            $DOWNLOAD_ROWS = $GLOBALS['SITE_DB']->query($sql, null, 0, false, false, ['name' => 'SHORT_TRANS', 'the_description' => 'LONG_TRANS__COMCODE']);
            foreach ($DOWNLOAD_ROWS as $i => $row) {
                $DOWNLOAD_ROWS[$i] = $row;
                $DOWNLOAD_ROWS[$i]['nice_title'] = get_translated_text($row['name']);
                $DOWNLOAD_ROWS[$i]['nice_description'] = get_translated_text($row['the_description']);
            }
        }
    }
}

function find_version_news($version_pretty)
{
    global $NEWS_ROWS;
    load_version_news_rows();

    foreach ($NEWS_ROWS as $news_row) {
        if ($news_row['nice_title'] == 'Composr ' . $version_pretty . ' released') {
            return $news_row;
        }
        if ($news_row['nice_title'] == 'Composr ' . $version_pretty . ' released!') { // Major releases have exclamation marks
            return $news_row;
        }
    }

    return null;
}

function load_version_news_rows()
{
    global $NEWS_ROWS;
    if (!isset($NEWS_ROWS)) {
        if (get_param_integer('test_mode', 0) == 1) {
            // Test data
            $NEWS_ROWS = [
                ['id' => 2, 'nice_title' => 'Composr 13 released', 'add_date' => time() - 60 * 60 * 8],
                ['id' => 3, 'nice_title' => '13.1 released', 'add_date' => time() - 60 * 60 * 5],
                ['id' => 4, 'nice_title' => '13.1.1 released', 'add_date' => time() - 60 * 60 * 5],
                ['id' => 5, 'nice_title' => 'Composr 13.2 beta1 released', 'add_date' => time() - 60 * 60 * 4],
                ['id' => 6, 'nice_title' => 'Composr 13.2 released', 'add_date' => time() - 60 * 60 * 3],
                ['id' => 7, 'nice_title' => 'Composr 14 released', 'add_date' => time() - 60 * 60 * 1],
            ];
        } else {
            // Live data
            $db = $GLOBALS['SITE_DB'];

            $start = 0;
            $max = 100;
            $NEWS_ROWS = [];
            do {
                $rows = $db->query_select('news', ['*', 'date_and_time AS add_date'], ['validated' => 1], ' AND ' . $db->translate_field_ref('title') . ' LIKE \'' . db_encode_like('%released%') . '\' ORDER BY add_date', $max, $start);
                foreach ($rows as $i => $row) {
                    $NEWS_ROWS[$i] = $row;
                    $NEWS_ROWS[$i]['nice_title'] = get_translated_text($row['title']);
                }

                $start += $max;
            } while (!empty($rows));
        }
    }
}

function get_composr_branches()
{
    require_code('version2');

    $_branches = shell_exec('git branch');
    $branches = [];
    foreach (explode("\n", is_string($_branches) ? $_branches : '') as $_branch) {
        $matches = [];
        if (preg_match('#^\s*\*?\s*(master|main|v[\d\.]+)$#', $_branch, $matches) != 0) {
            $git_branch = $matches[1];

            $version_file = shell_exec('git show ' . $git_branch . ':sources/version.php');

            $tempnam = cms_tempnam();
            file_put_contents($tempnam, $version_file . "\n\ninit__version();\n\necho serialize([cms_version_number(), function_exists('cms_version_branch_status') ? cms_version_branch_status() : 'Unknown']);");
            $test = shell_exec('php ' . $tempnam . ' 2>&1');
            $results = @unserialize($test);
            unlink($tempnam);
            if ($results === false) {
                attach_message($test, 'warn');
                continue;
            }
            if ((is_array($results)) && (count($results) == 2)) {
                list($version_number, $status) = $results;

                $branches[str_pad(float_to_raw_string($version_number), 10, '0', STR_PAD_LEFT)] = [
                    'git_branch' => $git_branch,
                    'branch' => get_version_branch($version_number),
                    'status' => $status,
                ];
            }
        }
    }

    ksort($branches);

    return $branches;
}

// PROBING RELEASES
// ----------------

function recursive_unzip($zip_path, $unzip_path)
{
    if (!class_exists('ZipArchive', false)) {
        warn_exit(do_lang_tempcode('ZIP_NOT_ENABLED'));
    }

    $zip_archive = new ZipArchive();

    $in_file = $zip_archive->open($zip_path);
    if ($in_file !== true) {
        require_code('failure');
        warn_exit(zip_error($zip_path, $in_file), false, true);
    }

    for ($i = 0; $i < $zip_archive->numFiles; $i++) {
        $entry_name = $zip_archive->getNameIndex($i);
        if (substr($entry_name, -1) != '/') {
            @mkdir(dirname($unzip_path . '/' . $entry_name), 0777, true);
            $out_file = fopen($unzip_path . '/' . $entry_name, 'wb');
            flock($out_file, LOCK_EX);
            $it = $zip_archive->getFromIndex($i);
            if (($it === false) || ($it == '')) {
                continue;
            }
            fwrite($out_file, $it);
            flock($out_file, LOCK_UN);
            fclose($out_file);
        }
    }

    $zip_archive->close();
    unset($zip_archive);
}

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

function init__cms_homesite()
{
    define('LEAD_DEVELOPER_MEMBER_ID', 2);
}

// IDENTIFYING RELEASES
// --------------------

/**
 * Get the latest available version of the software in the downloads system (in dotted form).
 *
 * @param  ?float $version_dotted Limit to the download category for this version (null: do not limit)
 * @param  boolean $bleeding_edge Whether to include bleeding-edge releases
 * @return ?string The latest version (null: none found)
 */
function get_latest_version_dotted(?float $version_dotted = null, bool $bleeding_edge = false) : ?string
{
    static $version = null; // null means unset (uncached)

    if ($version === null) {
        $download_rows = load_version_download_rows();

        $latest_category_version = 0.0;
        $latest_time = 0;
        foreach ($download_rows as $cat_version => $types) {
            // Skip categories whose version does not pertain to the one we specified, if applicable
            if (($version_dotted !== null) && ($cat_version != $version_dotted)) {
                continue;
            }

            // Skip categories of older versions if we already picked up on a newer version download
            if (($cat_version < $latest_category_version) && ($version !== null)) {
                continue;
            }
            $latest_category_version = $cat_version;

            foreach ($types as $type => $rows) {
                foreach ($rows as $download) {
                    $name = get_translated_text($download['name']);

                    // If we do not want bleeding-edge, skip bleeding-edge releases
                    if ((!$bleeding_edge) && strpos($name, 'bleeding-edge') !== false) {
                        continue;
                    }

                    // This download is older than our newest one, so it is not the newest version
                    if ($download['add_date'] < $latest_time) {
                        continue;
                    }

                    // At this point, this download / version is now considered the latest one
                    $latest_time = $download['add_date'];
                    $version = preg_replace('# \(.*#', '', $name);
                }
            }
        }
    }

    return $version;
}

/**
 * Get a pretty version of the latest version of the software available in the downloads system.
 *
 * @param  ?float $version_dotted Limit to this version branch (null: do not limit)
 * @param  boolean $bleeding_edge Whether to include bleeding-edge releases
 * @return ?string The latest version (null: none found)
 */
function get_latest_version_pretty(?float $version_dotted = null, bool $bleeding_edge = false) : ?string
{
    $version_dotted = get_latest_version_dotted($version_dotted, $bleeding_edge);
    if ($version_dotted === null) {
        return null;
    }
    return get_version_pretty__from_dotted($version_dotted);
}

/**
 * Get the major.minor version of the latest available software version in the downloads.
 *
 * @param  ?float $version_dotted Limit to this major/minor category version (null: do not limit)
 * @param  boolean $bleeding_edge Whether to include bleeding-edge releases
 * @return ?float The major.minor version (null: none found)
 */
function get_latest_version_basis_number(?float $version_dotted = null, bool $bleeding_edge = false) : ?float
{
    require_code('version2');
    $latest_pretty = get_latest_version_pretty($version_dotted, $bleeding_edge);
    if ($latest_pretty === null) {
        return null;
    }

    $latest_dotted = get_version_dotted__from_anything($latest_pretty);
    list($_latest_number) = get_version_components__from_dotted($latest_dotted);
    return floatval($_latest_number);
}

/**
 * Return an array of downloads in sequence for upgrading.
 *
 * @param  ID_TEXT $type The type of installer we want
 * @set manual quick
 * @param  boolean $bleeding_edge Whether to include bleeding-edge releases in the tree
 * @return array Maps of version number to its download row, in order according to upgrade path
 */
function get_release_tree(string $type = 'manual', bool $bleeding_edge = false) : array
{
    static $versions;
    if (isset($versions)) {
        return $versions;
    }

    require_code('version2');

    $download_rows = load_version_download_rows();

    $versions = [];

    foreach ($download_rows as $cat_version => $download_types) {
        foreach ($download_types as $download_type => $download_rows) {
            if ($type != $download_type) {
                continue;
            }

            foreach ($download_rows as $download_row) {
                $name = get_translated_text($download_row['name']);

                // If we do not want bleeding-edge, skip bleeding-edge releases
                if ((!$bleeding_edge) && strpos($name, 'bleeding-edge') !== false) {
                    continue;
                }

                $matches = [];
                if (preg_match('#^' . preg_quote(brand_name(), '#') . ' Version (.*)$#', $name, $matches) != 0) {
                    $version_dotted = get_version_dotted__from_anything($matches[1]);
                    list(, $qualifier, $qualifier_number, $long_dotted_number, , $long_dotted_number_with_qualifier) = get_version_components__from_dotted($version_dotted);
                    $versions[$long_dotted_number_with_qualifier] = $download_row;
                }
            }
        }
    }

    uksort($versions, 'version_compare');

    $_versions = [];
    foreach ($versions as $long_dotted_number_with_qualifier => $download_row) {
        $_versions[preg_replace('#\.0$#', '', $long_dotted_number_with_qualifier)] = $download_row;
    }

    return $_versions;
}

/**
 * Find out if a given major/minor version has been discontinued.
 *
 * @param  string $version The major/minor dotted version
 * @return boolean Whether it was discontinued
 */
function is_release_discontinued(string $version)
{
    // LEGACY: update as required
    $discontinued = ['1', '2', '2.1', '2.5', '2.6', '3', '3.1', '3.2', '4', '5', '6', '7', '8', '9', '10.1'];
    return (preg_match('#^(' . implode('|', array_map('preg_quote', $discontinued)) . ')($|\.)#', $version) != 0);
}

/**
 * Find a download row for the given version.
 *
 * @param  ?ID_TEXT $version_pretty The pretty version we want (null: get the latest, ignoring bleeding-edge)
 * @param  ID_TEXT $type_wanted The type of installer we want
 * @set manual quick
 * @return ?array The download row (null: not found)
 */
function find_version_download(string $version_pretty, string $type_wanted = 'manual') : ?array
{
    if ($version_pretty === null) {
        $version_pretty = get_latest_version_pretty();
    }
    if ($version_pretty === null) {
        return null;
    }

    $download_rows = load_version_download_rows();

    foreach ($download_rows as $cat_version => $types) {
        foreach ($types as $type => $rows) {
            if ($type_wanted != $type) {
                continue;
            }

            foreach ($rows as $download_row) {
                $nice_title_stripped = preg_replace('# \(.*\)$#', '', get_translated_text($download_row['name']));

                // Search both stable and bleeding-edge because there should never be the same version with both types
                if (($nice_title_stripped == brand_name() . ' Version ' . $version_pretty) || ($nice_title_stripped == brand_name() . ' Version ' . $version_pretty . ' (bleeding-edge)')) {
                    return $download_row;
                }
            }
        }
    }

    return null;
}

/**
 * Return a structured array of version download rows.
 *
 * @return array Array of download rows
 */
function load_version_download_rows() : array
{
    // Cache
    static $download_rows;
    if (isset($download_rows)) {
        return $download_rows;
    }

    // Get the main releases category
    $download_category = brand_name() . ' Releases';
    $releases_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => db_get_first_id(), $GLOBALS['SITE_DB']->translate_field_ref('category') => $download_category]);
    if ($releases_category_id === null) {
        $download_rows = [];
        return $download_rows;
    }

    // Get all of the version categories within the releases
    $where = ['parent_id' => $releases_category_id];
    $release_categories = $GLOBALS['SITE_DB']->query_select('download_categories', ['id', 'category'], $where);

    // Process each category
    foreach ($release_categories as $category) {
        $category_version = str_replace('Version ', '', get_translated_text($category['category']));
        $version_branch = floatval($category_version);
        if (!isset($download_rows[$version_branch])) {
            $download_rows[$version_branch] = ['manual' => [], 'quick' => []];
        }

        // Get quick installers
        $quick_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $category['id'], $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Quick Installer']);
        if ($quick_category_id !== null) {
            $quick_downloads = $GLOBALS['SITE_DB']->query_select('download_downloads', ['*'], ['category_id' => $quick_category_id], ' ORDER BY add_date ASC');
            $download_rows[$version_branch]['quick'] = $quick_downloads;
        }

        // Get manual installers
        $manual_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $category['id'], $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Manual Installer']);
        if ($manual_category_id !== null) {
            $manual_downloads = $GLOBALS['SITE_DB']->query_select('download_downloads', ['*'], ['category_id' => $manual_category_id], ' ORDER BY add_date ASC');
            $download_rows[$version_branch]['manual'] = $manual_downloads;
        }
    }

    return $download_rows;
}

function find_version_news($version_pretty)
{
    global $NEWS_ROWS;
    load_version_news_rows();

    foreach ($NEWS_ROWS as $news_row) {
        if ($news_row['nice_title'] == brand_name() . ' ' . $version_pretty . ' released') {
            return $news_row;
        }
        if ($news_row['nice_title'] == brand_name() . ' ' . $version_pretty . ' released!') { // Major releases have exclamation marks
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

/**
 * Get details of software branches available in git or GitLab.
 *
 * @return array Map of available branches and information
 */
function get_composr_branches() : array
{
    require_code('version2');

    $branches = [];
    $_branches = shell_exec('git branch');

    if (is_string($_branches)) { // Local git repository present
        foreach (explode("\n", $_branches) as $_branch) {
            $matches = [];
            if (preg_match('#^\s*\*?\s*(master|main|v[\S]+)$#', $_branch, $matches) != 0) { // We only want main/master and 'v' branches
                $git_branch = $matches[1];

                $version_file = shell_exec('git show ' . $git_branch . ':sources/version.php');
                $uid = uniqid('', false);
                $version_file = clean_php_file_for_eval($version_file, get_file_base() . '/sources/version.php');
                $version_file = preg_replace('/function\s+(\w+)\s*\(/', 'function ${1}_' . $uid . '(', $version_file);

                cms_eval($version_file, 'git branch ' . $git_branch . ' sources/version.php');
                $version_minor = call_user_func_array('cms_version_minor_' . $uid, []);
                $version_major = call_user_func_array('cms_version_number_' . $uid, []);
                $version_time = call_user_func_array('cms_version_time_' . $uid, []);
                $branch_status = call_user_func_array('cms_version_branch_status_' . $uid, []);

                $version = get_version_dotted(intval($version_major), $version_minor);

                $branches[$git_branch] = [
                    'git_branch' => $git_branch,
                    'branch' => get_version_branch($version_major),
                    'status' => $branch_status,
                    'version' => $version,
                    'version_time' => $version_time
                ];
            }
        }
    } elseif (file_exists(get_file_base() . '/data_custom/keys/gitlab.ini')) { // Local git repository not present; try GitLab if a key is present
        require_code('files');
        require_code('http');
        require_code('zones');
        require_code('version2');

        $gitlab_info = cms_parse_ini_file_fast(get_file_base() . '/data_custom/keys/gitlab.ini');
        list($gitlab_response) = cache_and_carry('cms_http_request', [
            'https://gitlab.com/api/v4/projects/' . $gitlab_info['project_id'] . '/repository/branches',
            [
                'convert_to_internal_encoding' => true,
                'timeout' => 5.0,
                'trigger_error' => $GLOBALS['DEV_MODE'],
                'extra_headers' => [
                    'Authorization' => 'Bearer ' . $gitlab_info['api_token']
                ]
            ]
        ], 60);
        $_branches = @json_decode($gitlab_response, true);
        if (!is_array($_branches)) {
            return [];
        }
        $_branches = collapse_1d_complexity('name', $_branches);

        foreach ($_branches as $branch) {
            if (!in_array($branch, ['main', 'master']) && (strpos($branch, 'v') !== 0)) { // We only want main/master and 'v' branches
                continue;
            }

            list($gitlab_response) = cache_and_carry('cms_http_request', [
                'https://gitlab.com/api/v4/projects/' . $gitlab_info['project_id'] . '/repository/files/' . urlencode('sources/version.php') . '?ref=' . $branch,
                [
                    'convert_to_internal_encoding' => true,
                    'timeout' => 5.0,
                    'trigger_error' => $GLOBALS['DEV_MODE'],
                    'extra_headers' => [
                        'Authorization' => 'Bearer ' . $gitlab_info['api_token']
                    ]
                ]
            ], 60);

            $_version_file = @json_decode($gitlab_response, true);
            if (!is_array($_version_file)) {
                return [];
            }
            $version_file = base64_decode($_version_file['content']);
            $uid = uniqid('', false);
            $version_file = clean_php_file_for_eval($version_file, get_file_base() . '/sources/version.php');
            $version_file = preg_replace('/function\s+(\w+)\s*\(/', 'function ${1}_' . $uid . '(', $version_file);

            cms_eval($version_file, 'GitLab branch ' . $branch . ' sources/version.php');
            $version_minor = call_user_func_array('cms_version_minor_' . $uid, []);
            $version_major = call_user_func_array('cms_version_number_' . $uid, []);
            $version_time = call_user_func_array('cms_version_time_' . $uid, []);
            $branch_status = call_user_func_array('cms_version_branch_status_' . $uid, []);
            $version = get_version_dotted(intval($version_major), $version_minor);

            $branches[$branch] = [
                'git_branch' => $branch,
                'branch' => get_version_branch($version_major),
                'status' => $branch_status,
                'version' => $version,
                'version_time' => $version_time
            ];
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

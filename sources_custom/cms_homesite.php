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
    define('DEMONSTRATR_DEMO_LAST_DAYS', 30);
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
    $zip_handle = zip_open($zip_path);
    while (($entry = (zip_read($zip_handle))) !== false) {
        $entry_name = zip_entry_name($entry);
        if (substr($entry_name, -1) != '/') {
            $_entry = zip_entry_open($zip_handle, $entry);
            if ($_entry !== false) {
                @mkdir(dirname($unzip_path . '/' . $entry_name), 0777, true);
                $out_file = fopen($unzip_path . '/' . $entry_name, 'wb');
                flock($out_file, LOCK_EX);
                while (true) {
                    $it = zip_entry_read($entry, 1024);
                    if (($it === false) || ($it == '')) {
                        break;
                    }
                    fwrite($out_file, $it);
                }
                zip_entry_close($entry);
                flock($out_file, LOCK_UN);
                fclose($out_file);
            }
        }
    }
    zip_close($zip_handle);
}

// DEMONSTRATR
// -----------

function server__public__demo_reset()
{
    require_lang('sites');

    set_value('last_demo_set_time', strval(time()));

    require_lang('cms_homesite');

    $servers = find_all_servers();
    $server = array_shift($servers);
    $codename = 'shareddemo';
    $password = 'demo123';
    $email_address = '';
    demonstratr_add_site_raw($server, $codename, $email_address, $password);
}

function demonstratr_add_site($codename, $name, $email_address, $password, $description, $category, $show_in_directory)
{
    if (cms_mb_strlen($name) > 200) {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    // Check named site valid
    if ((strlen($codename) < 3) || (cms_mb_strlen($codename) > 20) || (preg_match('#^[\w\-]*$#', $codename) == 0)) {
        warn_exit(do_lang_tempcode('CMS_BAD_NAME'));
    }

    // Check named site available
    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('sites', 's_server', ['s_codename' => $codename]);
    if ($test !== null) {
        // Did it fail adding before? It's useful to not have to fiddle around manually cleaning up when debugging
        $definitely_failed = false;//(strpos(cms_file_get_contents_safe(special_demonstratr_dir() . '/rcpthosts'), "\n" . $codename . '.composr.info' . "\n") === false);
        $probably_failed = !file_exists(special_demonstratr_dir() . '/alias/.qmail-demonstratr_' . $codename . '_staff');
        if (($definitely_failed) || ((($probably_failed) || (get_param_integer('keep_force', 0) == 1)) && ($GLOBALS['FORUM_DRIVER']->is_staff(get_member())))) {
            demonstratr_delete_site($test, $codename);
            $test = null;
        }
    }
    if (($test !== null) || (in_array($codename, ['ssh', 'ftp', 'ns1', 'ns2', 'ns3', 'ns4', 'private', 'staff', 'webmail', 'imap', 'smtp', 'mail', 'ns', 'com', 'net', 'www', 'sites', 'chris', 'test', 'example', 'ocproducts', 'composr', 'cms'])) || (strpos($codename, 'demonstratr') !== false)) {
        warn_exit(do_lang_tempcode('CMS_NOT_AVAILABLE'));
    }

    $server = choose_available_server();

    $GLOBALS['SITE_DB']->query_insert('sites', [
        's_codename' => $codename,
        's_name' => $name,
        's_description' => $description,
        's_category' => $category,
        's_domain_name' => '',
        's_server' => $server,
        's_member_id' => get_member(),
        's_add_time' => time(),
        's_last_backup_time' => null,
        's_subscribed' => 0,
        's_show_in_directory' => $show_in_directory,
        's_sponsored_in_category' => 0,
        's_sent_expire_message' => 0,
    ]);

    demonstratr_add_site_raw($server, $codename, $email_address, $password);

    // Aliases
    $GLOBALS['SITE_DB']->query_insert('sites_email', [
        's_codename' => $codename,
        's_email_from' => 'staff',
        's_email_to' => $email_address,
    ], false, true);
    reset_aliases();

    // _config.php
    reset_base_config_file($server);

    // Welcome e-mail
    require_lang('sites');
    require_code('mail');
    $subject = do_lang('CMS_EMAIL_SUBJECT');
    $message = do_lang('CMS_EMAIL_BODY', comcode_escape($codename)/*e-mail is not secure,comcode_escape($password)*/);
    dispatch_mail($subject, $message, [$email_address]);
}

function demonstratr_add_site_raw($server, $codename, $email_address, $password)
{
    global $SITE_INFO;

    if (!isset($SITE_INFO['mysql_root_password']) || !isset($SITE_INFO['mysql_demonstratr_password'])) {
        fatal_exit(do_lang_tempcode('cms_homesite:DEMONSTRATR_NOT_CONFIGURED'));
    }

    if (cms_strtoupper_ascii(substr(PHP_OS, 0, 3)) == 'WIN') {
        fatal_exit('Windows is not supported for this operation.');
    }

    // Create database
    $central_conn = new DatabaseConnector(get_db_site(), 'localhost'/*$server*/, 'root', $SITE_INFO['mysql_root_password'], 'cms_');
    $central_conn->query('DROP DATABASE IF EXISTS `demonstratr_site_' . $codename . '`');
    $central_conn->query('CREATE DATABASE `demonstratr_site_' . $codename . '`');
    $user = substr(md5('demonstratr_site_' . $codename), 0, 16);
    $central_conn->query('DROP USER \'' . $user . '\'@\'%\'', null, 0, true); // tcp/ip
    $central_conn->query('CREATE USER \'' . $user . '\'@\'%\' IDENTIFIED WITH mysql_native_password BY \'' . db_escape_string($SITE_INFO['mysql_demonstratr_password']) . '\'');
    $central_conn->query('GRANT ALL PRIVILEGES ON `demonstratr_site_' . $codename . '`.* TO \'' . $user . '\'@\'%\'');
    $central_conn->query('DROP USER \'' . $user . '\'@\'localhost\'', null, 0, true); // local socket
    $central_conn->query('CREATE USER \'' . $user . '\'@\'localhost\' IDENTIFIED WITH mysql_native_password BY \'' . db_escape_string($SITE_INFO['mysql_demonstratr_password']) . '\'');
    $central_conn->query('GRANT ALL PRIVILEGES ON `demonstratr_site_' . $codename . '`.* TO \'' . $user . '\'@\'localhost\'');

    // Import database contents
    $cmd = '/usr/local/bin/mysql';
    if (!is_file($cmd)) {
        $cmd = '/usr/bin/mysql';
    }
    $cmd .= ' -h' . /*$server*/'localhost';
    $cmd .= ' -Ddemonstratr_site_' . $codename;
    $cmd .= ' -u' . $user;
    if ($SITE_INFO['mysql_demonstratr_password'] != '') {
        $cmd .= ' -p' . $SITE_INFO['mysql_demonstratr_password'];
    }
    $cmd .= ' < ' . special_demonstratr_dir() . '/template.sql';
    $cmd .= ' 2>&1'; // We want to gather error messages
    if ($GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) {
        attach_message('Running import command... ' . $cmd, 'inform');
    }
    $output = [];
    $return_var = 0;
    $last_line = exec($cmd, $output, $return_var);
    if ($return_var != 0) {
        fatal_exit('Failed to create database, ' . implode("\n", $output) . "\n" . $last_line);
    }

    // Set some default config
    $db_conn = new DatabaseConnector('demonstratr_site_' . $codename, 'localhost'/*$server*/, $user, $SITE_INFO['mysql_demonstratr_password'], 'cms_');
    $db_conn->query_update('config', ['c_value' => $email_address], ['c_name' => 'staff_address'], '', 1);
    require_code('crypt');
    $salt = get_secure_random_string();
    $password_salted = ratchet_hash($password, $salt);
    push_db_scope_check(false);
    $db_conn->query_update('f_members', ['m_email_address' => $email_address, 'm_pass_hash_salted' => $password_salted, 'm_pass_salt' => $salt, 'm_password_compat_scheme' => '', 'm_login_key' => ''], ['m_username' => 'admin'], '', 1);
    pop_db_scope_check();

    // Create default file structure
    $path = special_demonstratr_dir() . '/servers/' . filter_naughty($server) . '/sites/' . filter_naughty($codename);
    if (file_exists($path)) {
        //require_code('files'); @deldir_contents($path);
        exec('rm -rf ' . $path); // More efficient
    }
    @mkdir(dirname($path), 0777);
    @mkdir($path, 0777);
    @chmod($path, 0777);
    require_code('tar');
    $tar = tar_open(special_demonstratr_dir() . '/template.tar', 'rb');
    $path_short = substr($path, strlen(get_custom_file_base() . '/'));
    tar_extract_to_folder($tar, $path_short, false, null, false, false);
    tar_close($tar);
    require_code('files2');
    $contents = get_directory_contents($path, $path, null, true, true);
    foreach ($contents as $c) {
        if (is_file($c)) {
            @chmod($c, 0666);
        }
    }
    $contents = get_directory_contents($path, $path, null, true, false);
    foreach ($contents as $c) {
        if (is_dir($c)) {
            @chmod($c, 0777);
        }
    }
}

/**
 * Get the relative path to the special directory that holds NFS links to servers, etc.
 *
 * @return string Server path
 */
function special_demonstratr_dir() : string
{
    return get_file_base() . '/uploads/website_specific/cms_homesite/demonstratr';
}

/**
 * Get a list of categories that sites may be in.
 *
 * @return Tempcode The result of execution
 */
function get_site_categories() : object
{
    $cats = ['Entertainment', 'Computers', 'Sport', 'Art', 'Music', 'Television/Movies', 'Businesses', 'Other', 'Informative/Factual', 'Political', 'Humour', 'Geographical/Regional', 'Games', 'Personal/Family', 'Hobbies', 'Culture/Community', 'Religious', 'Health'];
    cms_mb_sort($cats, SORT_NATURAL | SORT_FLAG_CASE);
    return $cats;
}

/**
 * Get a form field list of site categories.
 *
 * @param  string $cat The default selected item
 * @return Tempcode List
 */
function create_selection_list_site_categories(string $cat) : object
{
    $cat_list = new Tempcode();
    $categories = get_site_categories();
    foreach ($categories as $_cat) {
        $cat_list->attach(form_input_list_entry($_cat, $_cat == $cat));
    }
    return $cat_list;
}

/**
 * Get a form field list of servers.
 *
 * @param  string $server The default selected item
 * @return Tempcode List
 */
function create_selection_list_servers(string $server) : object
{
    $server_list = new Tempcode();
    $servers = find_all_servers();
    foreach ($servers as $_server) {
        $server_list->attach(form_input_list_entry($_server, $_server == $server));
    }
    return $server_list;
}

/**
 * Find all the servers for our shared hosting.
 *
 * @return array A list of servers
 */
function find_all_servers() : array
{
    if (!file_exists(special_demonstratr_dir() . '/servers')) {
        return [''];
    }

    $d = opendir(special_demonstratr_dir() . '/servers');
    $servers = [];
    while (($e = readdir($d)) !== false) {
        if ($e[0] != '.') { //if (substr_count($e,'.')==4)
            $servers[] = $e;
        }
    }
    closedir($d);
    return $servers;
}

/**
 * Cause the _config.php file to be rebuilt.
 *
 * @param  ID_TEXT $server The server
 */
function reset_base_config_file(string $server)
{
    global $SITE_INFO;

    $path = special_demonstratr_dir() . '/servers/' . filter_naughty($server) . '/_config.php';
    $contents = "<" . "?php
global \$SITE_INFO;


if (!function_exists('git_repos')) {
    /**
     * Find the Git branch name. This is useful for making this config file context-adaptive (i.e. dev settings vs production settings).
     *
     * @return ?ID_TEXT Branch name (null: not in Git)
     */
    function git_repos() : ?string
    {
        \$path = __DIR__ . '/.git/HEAD';
        if (!is_file(\$path)) return '';
        \$lines = file(\$path);
        \$parts = explode('/', \$lines[0]);
        return trim(end(\$parts));
    }
}

\$SITE_INFO['multi_lang_content'] = '0';
\$SITE_INFO['default_lang'] = 'EN';
\$SITE_INFO['forum_type'] = 'cns';
\$SITE_INFO['db_type'] = 'mysql';
\$SITE_INFO['db_site_host'] = '127.0.0.1';
\$SITE_INFO['user_cookie'] = 'cms_member_id';
\$SITE_INFO['pass_cookie'] = 'cms_member_hash';
\$SITE_INFO['cookie_domain'] = '';
\$SITE_INFO['cookie_path'] = '/';
\$SITE_INFO['cookie_days'] = '1825';
\$SITE_INFO['session_cookie'] = 'cms_session__567206a440a52943735248';
\$SITE_INFO['self_learning_cache'] = '1';

\$SITE_INFO['db_site_user'] = 'demonstratr_site';
\$SITE_INFO['db_site_password'] = '" . $SITE_INFO['mysql_demonstratr_password'] . "';
\$SITE_INFO['db_site'] = 'demonstratr_site';
\$SITE_INFO['table_prefix'] = 'cms_';

\$SITE_INFO['dev_mode'] = '0';

\$SITE_INFO['throttle_space_complementary'] = 100;
\$SITE_INFO['throttle_space_views_per_meg'] = 10;
\$SITE_INFO['throttle_bandwidth_complementary'] = 500;
\$SITE_INFO['throttle_bandwidth_views_per_meg'] = 1;

\$SITE_INFO['domain'] = \$_SERVER['HTTP_HOST'];
\$SITE_INFO['base_url'] = 'http://'.\$_SERVER['HTTP_HOST'];

\$SITE_INFO['custom_base_url_stub'] = 'http://'.\$_SERVER['HTTP_HOST'].'/sites';
\$SITE_INFO['custom_file_base_stub'] = __DIR__ . '/sites';
\$SITE_INFO['custom_share_domain'] = 'composr.info';
\$SITE_INFO['custom_share_path'] = 'sites';

if (\$_SERVER['HTTP_HOST'] == 'composr.info') {
        exit('Must run an individual demo site');
}
";
    $rows = $GLOBALS['SITE_DB']->query_select('sites', ['s_codename', 's_domain_name'], ['s_server' => $server]);
    foreach ($rows as $row) {
        if ($row['s_domain_name'] != '') {
            $contents .= "
\$SITE_INFO['custom_domain_" . db_escape_string($row['s_domain_name']) . "']='" . db_escape_string($row['s_codename']) . "';
";
        }
        $contents .= "
\$SITE_INFO['custom_user_" . db_escape_string($row['s_codename']) . "'] = true;
";
    }
    require_code('files');
    cms_file_put_contents_safe($path, $contents, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
}

/**
 * Cause the E-mail server to reload its database.
 */
function reset_aliases()
{
    return; // Needs customising for each deployment; Demonstratr personal demos currently not supporting e-mail hosting

    /*
    require_code('files');

    // Rebuild virtualdomains
    $vds = cms_file_safe(special_demonstratr_dir() . '/virtualdomains');
    $text = '';
    foreach ($vds as $vd) {
        if ((strpos($vd, ':alias-demonstratr_') === false) && (trim($vd) != '')) {
            $text .= $vd . "\n";
        }
    }
    $sites = $GLOBALS['SITE_DB']->query_select('sites', ['s_codename', 's_domain_name']);
    foreach ($sites as $site) {
        $text .= $site['s_codename'] . '.composr.info:' . 'alias-demonstratr_' . $site['s_codename'] . "\n";
        if ($site['s_domain_name'] != '') {
            $text .= $site['s_domain_name'] . ':' . 'alias-demonstratr_' . $site['s_codename'] . "\n";
        }
    }
    $path = special_demonstratr_dir() . '/virtualdomains';
    cms_file_put_contents_safe($path, $text, FILE_WRITE_FIX_PERMISSIONS);

    // Rebuild rcpthosts
    $vds = cms_file_safe(special_demonstratr_dir() . '/rcpthosts');
    $hosts = [];
    foreach ($vds as $vd) {
        if (trim($vd) != '') {
            $hosts[$vd] = true;
        }
    }
    $sites = $GLOBALS['SITE_DB']->query_select('sites', ['s_codename', 's_domain_name']);
    foreach ($sites as $site) {
        $hosts[$site['s_codename'] . '.composr.info'] = true;
        if ($site['s_domain_name'] != '') {
            $hosts[$site['s_domain_name']] = true;
        }
    }
    $path = special_demonstratr_dir() . '/rcpthosts';
    cms_file_put_contents_safe($path, implode("\n", array_keys($hosts)) . "\n", FILE_WRITE_FIX_PERMISSIONS);

    // Go through aliases directory and remove Demonstratr aliases
    $a_path = special_demonstratr_dir() . '/alias';
    $d = opendir($a_path . '/');
    while (($e = readdir($d)) !== false) {
        if (substr($e, 0, 13) == '.qmail-demonstratr_') {
            unlink($a_path . '/' . $e);
        }
    }
    closedir($d);

    // Rebuild alias files
    $emails = $GLOBALS['SITE_DB']->query_select('sites_email', ['*']);
    foreach ($emails as $email) {
        $path = $a_path . '/.qmail-demonstratr_' . filter_naughty($email['s_codename']) . '_' . filter_naughty(str_replace('.', ':', $email['s_email_from']));
        cms_file_put_contents_safe($path, '&' . $email['s_email_to'], FILE_WRITE_FIX_PERMISSIONS);
    }

    shell_exec(special_demonstratr_dir() . '/reset_aliases');
    */
}

/**
 * Find the load of a server.
 *
 * @param  ID_TEXT $server The server to check load for
 * @return ?float The load (null: out of action)
 */
function find_server_load(string $server) : ?float
{
    return 1; // Not currently supported, needs customising per-server

    /*
    //$stats = http_get_contents('https://' . $server . '/data_custom/stats.php?html=1', ['convert_to_internal_encoding' => true]);
    $stats = shell_exec('php /home/demonstratr/public_html/data_custom/stats.php 1');
    $matches = [];
    preg_match('#Memory%: (.*)<br />Swap%: (.*)<br />15-min-load: load average: (.*)<br />5-min-load: (.*)<br />1-min-load: (.*)<br />CPU-user%: (.*)<br />CPU-idle%: (.*)<br />Free-space: (.*)#', $stats, $matches);
    list(, $mempercent, $swappercent, $load_15, $load_5, $load_1, $cpu_usage, $cpu_idle, $freespace) = $matches;
    if (intval($freespace) < 1024 * 1024 * 1024) {
        return null;
    }
    $av_load = (floatval($load_15) + floatval($load_5) + floatval($load_1)) / 3.0;
    return $av_load;
    */
}

/**
 * Find the best server.
 *
 * @return ID_TEXT The best server
 */
function choose_available_server() : string
{
    $servers = find_all_servers();
    $lowest_load = null;
    $lowest_for = null;
    foreach ($servers as $server) {
        $server_load = find_server_load($server);
        if ($server_load === null) {
            continue;
        }
        if (($lowest_load === null) || ($server_load < $lowest_load)) {
            $lowest_load = $server_load;
            $lowest_for = $server;
        }
    }
    return $lowest_for;
}

/**
 * Do a backup.
 */
function do_backup_script()
{
    require_lang('cms_homesite');

    $id = get_param_string('id');
    $sites = $GLOBALS['SITE_DB']->query_select('sites', ['s_member_id', 's_server'], ['s_codename' => $id], '', 1);
    if (!array_key_exists(0, $sites)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    $member_id = $sites[0]['s_member_id'];
    if ($member_id != get_member()) {
        access_denied('I_ERROR');
    }
    $server = $sites[0]['s_server'];

    global $SITE_INFO;

    // Create data
    require_code('zip');
    $file_array = zip_scan_folder(special_demonstratr_dir() . '/servers/' . filter_naughty($server) . '/sites/' . filter_naughty($id));
    $tmp_path = cms_tempnam();
    $user = substr(md5('demonstratr_site_' . $id), 0, 16);
    shell_exec('mysqldump -h' . /*$server*/'localhost' . ' -u' . $user . ' -p' . $SITE_INFO['mysql_demonstratr_password'] . ' demonstratr_site_' . $id . ' --skip-opt > ' . $tmp_path);
    $file_array[] = ['full_path' => $tmp_path, 'name' => 'database.sql', 'time' => time()];
    $tmp_path2 = cms_tempnam();
    create_zip_file($tmp_path2, $file_array);
    unlink($tmp_path);

    // Send header
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="backup-' . date('Y-m-d') . '.zip"');

    // Default to no resume
    header('Content-Length: ' . strval(filesize($tmp_path2)));

    cms_disable_time_limit();
    error_reporting(0);
    cms_ob_end_clean();

    readfile($tmp_path2);

    unlink($tmp_path2);
}

/**
 * Find which sites have expired.
 *
 * @return array List of expired sites
 */
function find_expired_sites() : array
{
    return $GLOBALS['SITE_DB']->query('SELECT s_codename,s_server FROM ' . get_table_prefix() . 'sites WHERE s_add_time<' . strval(time() - 60 * 60 * 24 * DEMONSTRATR_DEMO_LAST_DAYS) . ' AND ' . db_string_not_equal_to('s_codename', 'shareddemo'));
}

/**
 * Delete demo sites over DEMONSTRATR_DEMO_LAST_DAYS days old.
 */
function demonstratr_delete_old_sites()
{
    // Expire sites
    $sites = find_expired_sites();
    foreach ($sites as $site) {
        demonstratr_delete_site($site['s_server'], $site['s_codename'], true);
    }
    if (!empty($sites)) {
        reset_aliases();
    }

    // Warning e-mails
    require_code('mail');
    $sites = $GLOBALS['SITE_DB']->query('SELECT s_codename FROM ' . get_table_prefix() . 'sites WHERE s_add_time<' . strval(time() - 60 * 60 * 24 * 20) . ' AND ' . db_string_not_equal_to('s_codename', 'shareddemo') . ' AND s_sent_expire_message=0');
    foreach ($sites as $site) {
        $subject = do_lang('CMS_EMAIL_EXPIRE_SUBJECT', $site['s_codename']);
        $message = do_lang('CMS_EMAIL_EXPIRE_BODY', comcode_escape($site['s_codename']), get_brand_page_url(['page' => 'free_tickets'], 'site'));
        $email_address = $GLOBALS['SITE_DB']->query_select_value_if_there('sites_email', 's_email_to', ['s_codename' => $site['s_codename'], 's_email_from' => 'staff']);
        if ($email_address !== null) {
            dispatch_mail($subject, $message, [$email_address]);
        }

        $GLOBALS['SITE_DB']->query_update('sites', ['s_sent_expire_message' => 1], ['s_codename' => $site['s_codename']], '', 1);
    }
}

/**
 * Delete a site from Demonstratr.
 *
 * @param  ID_TEXT $server The server to delete from
 * @param  ID_TEXT $codename The site
 * @param  boolean $bulk Whether this is a bulk delete (in which case we don't want to do a config file reset each time)
 */
function demonstratr_delete_site(string $server, string $codename, bool $bulk = false)
{
    global $SITE_INFO;

    // Database
    $central_conn = new DatabaseConnector(get_db_site(), 'localhost'/*$server*/, 'root', $SITE_INFO['mysql_root_password'], 'cms_');
    $central_conn->query('DROP DATABASE IF EXISTS `demonstratr_site_' . $codename . '`');
    $user = substr(md5('demonstratr_site_' . $codename), 0, 16);
    $central_conn->query('REVOKE ALL ON `demonstratr_site_' . $codename . '`.* FROM \'' . $user . '\'', null, 0, true); // Suppress errors in case access denied
    //$central_conn->query('DROP USER \'demonstratr_site_' . $codename . '\'');

    $GLOBALS['SITE_DB']->query_delete('sites_deletion_codes', ['s_codename' => $codename], '', 1);
    $GLOBALS['SITE_DB']->query_update('sites_email', ['s_codename' => $codename . '__expired_' . strval(mt_rand(0, mt_getrandmax()))], ['s_codename' => $codename], '', 1);

    // Directory entry
    $GLOBALS['SITE_DB']->query_delete('sites', ['s_codename' => $codename], '', 1);

    // Files
    if ($codename != '') {
        $path = special_demonstratr_dir() . '/servers/' . filter_naughty($server) . '/sites/' . filter_naughty($codename);
        if (file_exists($path)) {
            //require_code('files'); deldir_contents($path); @rmdir($path);
            exec('rm -rf ' . $path); // More efficient
        }
    }

    if (!$bulk) {
        reset_aliases();
    }
    reset_base_config_file($server);

    // Special
    //$GLOBALS['SITE_DB']->query_delete('sites_email', ['s_codename' => $codename]);
}

<?php

/*EXTRA FUNCTIONS: _?m_\w+|shell_exec*/
/*CQC: No API check*/
/*CQC: !FLAG__SOMEWHAT_PEDANTIC*/
/*CQC: !FLAG__ESLINT*/

// M-ZIP Library: unzipping files using command line tools
//
// Feel free to use, modify and distribute this but please keep this note intact
// (you may add at the bottom)
// Please if you make modifications, fix bugs, etc... let me know!
//
//
// Original author: Francesco M. Munafo'
//
// EMail: phpDev (AT) eSurfers (DOT) com
// Download latest at http://eSurfers.com/m-lib/
//
// Modified for Composr. This file remains copyright to Francesco.

/**
 * @copyright  Francesco M. Munafo'
 * @package core
 */

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__m_zip()
{
    if (!defined('UNZIP_DIR')) {
        $_ud = get_option('unzip_dir');
        if ($_ud == '') {
            // M-zip not needed, but we still need unit testing to be able to test it, so assign default value for it
            require_code('files2');
            $ret = cms_get_temp_dir();
            $_ud = $ret[0];
        }
        $ud = rtrim($_ud, '/\\');
        define('UNZIP_DIR', $ud);

        $_uc = get_option('unzip_cmd');
        if ($_uc == '') {
            // M-zip not needed, but we still need unit testing to be able to test it, so assign default value for it
            $_uc = 'unzip -o @_SRC_@ -x -d @_DST_@';
        }
        define('UNZIP_CMD', $_uc);
    }

    if (!function_exists('zip_open')) {
        @eval("
            class ZIPARCHIVE {
                const ER_OK=0;  /* N No error */
                const ER_MULTIDISK=1;  /* N Multi-disk ZIP archives not supported */
                const ER_RENAME=2;  /* S Renaming temporary file failed */
                const ER_CLOSE=3;  /* S Closing ZIP archive failed */
                const ER_SEEK=4;  /* S Seek error */
                const ER_READ=5;  /* S Read error */
                const ER_WRITE=6;  /* S Write error */
                const ER_CRC=7;  /* N CRC error */
                const ER_ZIPCLOSED=8;  /* N Containing ZIP archive was closed */
                const ER_NOENT=9;  /* N No such file */
                const ER_EXISTS=10;  /* N File already exists */
                const ER_OPEN=11;  /* S Can't open file */
                const ER_TMPOPEN=12;  /* S Failure to create temporary file */
                const ER_ZLIB=13;  /* Z Zlib error */
                const ER_MEMORY=14;  /* N Malloc failure */
                const ER_CHANGED=15;  /* N Entry has been changed */
                const ER_COMPNOTSUPP=16;  /* N Compression method not supported */
                const ER_EOF=17;  /* N Premature EOF */
                const ER_INVAL=18;  /* N Invalid argument */
                const ER_NOZIP=19;  /* N Not a ZIP archive */
                const ER_INTERNAL=20;  /* N Internal error */
                const ER_INCONS=21;  /* N Zip archive inconsistent */
                const ER_REMOVE=22;  /* S Can't remove file */
                const ER_DELETED=23;  /* N Entry has been deleted */
            }
        ");

        /**
         * Open a ZIP file for reading.
         *
         * @param PATH $zip_file The ZIP file path
         * @return mixed The ZIP file resource (number if error)
         */
        function zip_open(string $zip_file)
        {
            return m_zip_open($zip_file);
        }

        /**
         * Close a ZIP file.
         *
         * @param array $open_zip_file The ZIP file resource
         * @return boolean Whether the file closed correctly
         */
        function zip_close(array $open_zip_file): bool
        {
            return m_zip_close($open_zip_file);
        }

        /**
         * Reads the next entry in a ZIP file archive.
         *
         * @param array $open_zip_file The ZIP file resource
         * @return ~array A directory entry resource for later use with the m_zip_entry_...() functions (false: if there's no more entries to read).
         */
        function zip_read(array $open_zip_file)
        {
            return m_zip_read($open_zip_file);
        }

        /**
         * Opens a directory entry in a ZIP file for reading.
         *
         * @param array $zip The ZIP file resource
         * @param array $zip_entry Directory entry resource returned by m_zip_read()
         * @param string $mode The file access mode
         * @set rb
         * @return boolean Whether the operation was successful
         */
        function zip_entry_open(array $zip, array $zip_entry, string $mode = 'rb'): bool
        {
            return m_zip_entry_open($zip, $zip_entry, $mode);
        }

        /**
         * Closes a directory entry previously opened for reading.
         *
         * @param array $zip_entry Directory entry resource returned by m_zip_read()
         * @return boolean Whether the operation was successful
         */
        function zip_entry_close(array $zip_entry): bool
        {
            return m_zip_entry_close($zip_entry);
        }

        /**
         * Returns the name of the directory entry specified in the given entry.
         *
         * @param array $zip_entry Directory entry resource returned by m_zip_read()
         * @return string The entry name
         */
        function zip_entry_name(array $zip_entry): string
        {
            return m_zip_entry_name($zip_entry);
        }

        /**
         * Returns the filesize of the directory entry specified in the given entry.
         *
         * @param array $zip_entry Directory entry resource returned by m_zip_read()
         * @return integer The file size
         */
        function zip_entry_filesize(array $zip_entry): int
        {
            return m_zip_entry_filesize($zip_entry);
        }

        /**
         * Returns the file data of the directory entry specified in the given entry.
         *
         * @param array $zip_entry Directory entry resource returned by m_zip_read()
         * @param integer $zip_entry_file_size The maximum returned data size
         * @return ~string The data (false: failure)
         */
        function zip_entry_read(array $zip_entry, int $zip_entry_file_size = 1024)
        {
            return m_zip_entry_read($zip_entry, $zip_entry_file_size);
        }
    }
}

/**
 * Compatibility implementation. Open a ZIP file for reading.
 *
 * @param PATH $zip_file The ZIP file path
 * @return mixed The ZIP file resource (number if error)
 */
function m_zip_open(string $zip_file)
{
    global $M_ZIP_DIR_HANDLES, $M_ZIP_DIR_OPEN_PATHS;

    $old_limit = cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

    list($usec, $sec) = explode(' ', microtime(false));
    $id = strval(intval($sec) - 1007700000) . str_pad(strval(intval($usec) * 1000000), 6, '0', STR_PAD_LEFT) . str_pad(strval(mt_rand(0, 999)), 3, '0', STR_PAD_LEFT);

    $_m_zip_open_file = explode('/', str_replace("\\", '/', $zip_file));
    $m_zip_open_file = 'Z' . $id . $_m_zip_open_file[count($_m_zip_open_file) - 1];

    $zip_dir = UNZIP_DIR . '/' . $m_zip_open_file . '/';

    mkdir($zip_dir, 0777);

    $unzip_cmd = UNZIP_CMD;
    $unzip_cmd = str_replace('@_SRC_@', cms_escapeshellarg($zip_file), $unzip_cmd);
    $unzip_cmd = str_replace('@_DST_@', cms_escapeshellarg($zip_dir), $unzip_cmd);

    $bits = explode(' ', UNZIP_CMD);
    $help_result = shell_exec($bits[0] . ' -h');
    if (@strpos($help_result, 'UnZip') === false) { // Check that InfoZip is where it is configured to be
        $config_url = build_url(['page' => 'admin_config', 'type' => 'category', 'id' => 'SITE'], get_module_zone('admin_config'), [], false, false, false, 'group-ARCHIVES');
        attach_message(do_lang_tempcode('NO_SHELL_ZIP_POSSIBLE2', escape_html_tempcode($config_url)), 'warn', false, true);

        return constant('ZIPARCHIVE::ER_INTERNAL');
    }

    $res = -1; // any nonzero value
    $unused_array_result = [];
    if (!php_function_allowed('shell_exec')) {
        attach_message(do_lang_tempcode('NO_SHELL_ZIP_POSSIBLE'), 'warn', false, true);

        return constant('ZIPARCHIVE::ER_INTERNAL');
    }
    $res = shell_exec($unzip_cmd);

    // IT IS IMPORTANT THAT YOUR COMMANDLINE ZUNZIP TOOL CORRECTLY SETS RESULT CODE
    // result code 0 == NO ERROR as in:
    if ($res === null) {
        m_deldir($zip_dir);
        return constant('ZIPARCHIVE::ER_INTERNAL');
    }

    // OTHERWISE, you still have the option of parsing $unused_array_result to find clues of errors
    // (lines starting with or "inflating" mean no error)

    $m_zip_open_dirs = [opendir($zip_dir)];
    $m_zip_dir_paths = [$zip_dir];

    $M_ZIP_DIR_HANDLES[$zip_file] = false;
    unset($M_ZIP_DIR_HANDLES[$zip_file]);
    $M_ZIP_DIR_OPEN_PATHS[$zip_file] = false;
    unset($M_ZIP_DIR_OPEN_PATHS[$zip_file]);

    cms_set_time_limit($old_limit);

    return [$zip_file, $m_zip_open_file, $m_zip_open_dirs, $m_zip_dir_paths];
}

/**
 * Compatibility implementation. Close a ZIP file.
 *
 * @param array $open_zip_file The ZIP file resource
 * @return boolean Whether the file closed correctly
 */
function m_zip_close(array $open_zip_file): bool
{
    global $M_ZIP_DIR_HANDLES, $M_ZIP_DIR_OPEN_PATHS;

    $m_zip_original_file = $open_zip_file[0];
    $m_zip_open_file = $open_zip_file[1];
    $m_zip_open_dirs = $open_zip_file[2];

    $zip_dir = UNZIP_DIR . '/' . $m_zip_open_file . '/';

    $M_ZIP_DIR_HANDLES[$m_zip_original_file] = false;
    unset($M_ZIP_DIR_HANDLES[$m_zip_original_file]);
    $M_ZIP_DIR_OPEN_PATHS[$m_zip_original_file] = false;
    unset($M_ZIP_DIR_OPEN_PATHS[$m_zip_original_file]);

    if (is_dir($zip_dir)) {
        foreach ($m_zip_open_dirs as $opendir) {
            closedir($opendir);
        }
        m_deldir($zip_dir);
    }
    $m_zip_open_file = '';
    return true;
}

/**
 * Compatibility implementation. Reads the next entry in a ZIP file archive.
 *
 * @param array $open_zip_file The ZIP file resource
 * @return ~array A directory entry resource for later use with the m_zip_entry_...() functions (false: if there's no more entries to read).
 */
function m_zip_read(array $open_zip_file)
{
    global $M_ZIP_DIR_HANDLES, $M_ZIP_DIR_OPEN_PATHS;

    $m_zip_original_file = $open_zip_file[0];
    $m_zip_open_file = $open_zip_file[1];

    // LOAD FILEHANDLES AND PATHS ARRAYS IF AVAILABLE

    if (array_key_exists($m_zip_original_file, $M_ZIP_DIR_HANDLES)) {
        $m_zip_open_dirs = $M_ZIP_DIR_HANDLES[$m_zip_original_file];
    } else {
        $m_zip_open_dirs = $open_zip_file[2];
    }

    if (array_key_exists($m_zip_original_file, $M_ZIP_DIR_OPEN_PATHS)) {
        $m_zip_dir_paths = $M_ZIP_DIR_OPEN_PATHS[$m_zip_original_file];
    } else {
        $m_zip_dir_paths = $open_zip_file[3];
    }

    $zip_dir = UNZIP_DIR . '/' . $m_zip_open_file . '/';

    $m_zip_last_open_dir = $m_zip_open_dirs[count($m_zip_open_dirs) - 1];
    $m_zip_last_dir_path = $m_zip_dir_paths[count($m_zip_dir_paths) - 1];

    $exit = false;
    do {
        $entryname = readdir($m_zip_last_open_dir);
        $exit = true;

        if ($entryname !== false) {
            $entrypath = $m_zip_last_dir_path . $entryname;
            if ($entryname == '.' || $entryname == '..') {
                $exit = false;
            } elseif (($entryname !== false) && is_dir($entrypath)) {
                $exit = false;
                $new_dir_path = $entrypath . '/';
                $new_open_dir = opendir($new_dir_path);

                if ($new_open_dir) {
                    array_push($m_zip_dir_paths, $new_dir_path);
                    array_push($m_zip_open_dirs, $new_open_dir);
                    $m_zip_last_open_dir = $m_zip_open_dirs[count($m_zip_open_dirs) - 1];
                    $m_zip_last_dir_path = $m_zip_dir_paths[count($m_zip_dir_paths) - 1];
                }
            }
        } elseif (($entryname === false) && (count($m_zip_open_dirs) > 1)) {
            $exit = false;
            closedir($m_zip_last_open_dir);
            array_pop($m_zip_open_dirs);
            array_pop($m_zip_dir_paths);
            $m_zip_last_open_dir = $m_zip_open_dirs[count($m_zip_open_dirs) - 1];
            $m_zip_last_dir_path = $m_zip_dir_paths[count($m_zip_dir_paths) - 1];
        }
    } while (!$exit);

    if ($entryname === false) {
        // NO MORE FILES CLEAR FILEHANDLES AND PATHS ARRAYS

        $M_ZIP_DIR_HANDLES[$m_zip_original_file] = false;
        unset($M_ZIP_DIR_HANDLES[$m_zip_original_file]);
        $M_ZIP_DIR_OPEN_PATHS[$m_zip_original_file] = false;
        unset($M_ZIP_DIR_OPEN_PATHS[$m_zip_original_file]);

        return false;
    }

    // SAVE FILEHANDLES AND PATHS ARRAYS FOR NEXT RUN

    $M_ZIP_DIR_HANDLES[$m_zip_original_file] = $m_zip_open_dirs;
    $M_ZIP_DIR_OPEN_PATHS[$m_zip_original_file] = $m_zip_dir_paths;

    return [$entryname, _m_zip_RelPath($zip_dir, $m_zip_last_dir_path), $m_zip_last_dir_path];
}

/**
 * Compatibility implementation. Make a path relative.
 *
 * @param PATH $base_path The base path (path to make relative to)
 * @param PATH $path The path to make relative
 * @return PATH The relative path
 * @ignore
 */
function _m_zip_RelPath(string $base_path, string $path): string
{
    //echo("BasePath:$base_path, Path;$path");
    if ($path == $base_path) {
        return '';
    }

    if (strpos($path, $base_path) === 0) {
        return substr($path, strlen($base_path));
    }

    return $path;
}

/**
 * Compatibility implementation. Opens a directory entry in a ZIP file for reading.
 *
 * @param array $zip The ZIP file resource
 * @param array $zip_entry Directory entry resource returned by m_zip_read()
 * @param string $mode The file access mode
 * @set rb
 * @return boolean Whether the operation was successful
 */
function m_zip_entry_open(array $zip, array $zip_entry, string $mode = 'rb'): bool
{
    global $M_ZIP_FILE_HANDLES;

    $m_zip_file_handle = fopen($zip_entry[2] . $zip_entry[0], 'rb');
    if ($m_zip_file_handle !== false) {
        $M_ZIP_FILE_HANDLES[$zip_entry[0]] = $m_zip_file_handle;
        return true;
    }
    return false;
}

/**
 * Compatibility implementation. Closes a directory entry previously opened for reading.
 *
 * @param array $zip_entry Directory entry resource returned by m_zip_read()
 * @return boolean Whether the operation was successful
 */
function m_zip_entry_close(array $zip_entry): bool
{
    global $M_ZIP_FILE_HANDLES;

    if ($M_ZIP_FILE_HANDLES[$zip_entry[0]]) {
        fclose($M_ZIP_FILE_HANDLES[$zip_entry[0]]);
        $M_ZIP_FILE_HANDLES[$zip_entry[0]] = false;
        unset($M_ZIP_FILE_HANDLES[$zip_entry[0]]);
    }

    return true;
}

/**
 * Compatibility implementation. Returns the name of the directory entry specified in the given entry.
 *
 * @param array $zip_entry Directory entry resource returned by m_zip_read()
 * @return string The entry name
 */
function m_zip_entry_name(array $zip_entry): string
{
    return $zip_entry[1] . $zip_entry[0];
}

/**
 * Compatibility implementation. Returns the filesize of the directory entry specified in the given entry.
 *
 * @param array $zip_entry Directory entry resource returned by m_zip_read()
 * @return integer The file size
 */
function m_zip_entry_filesize(array $zip_entry): int
{
    clearstatcache();
    return filesize($zip_entry[2] . $zip_entry[0]);
}

/**
 * Compatibility implementation. Returns the file data of the directory entry specified in the given entry.
 *
 * @param array $zip_entry Directory entry resource returned by m_zip_read()
 * @param integer $zip_entry_file_size The maximum returned data size
 * @return ~string The data (false: failure)
 */
function m_zip_entry_read(array $zip_entry, int $zip_entry_file_size = 1024)
{
    // Chris: Changed $zip_entry_file_size to default to 1024 as the proper zip_entry_read does -- no parameter does not mean read the whole file

    global $M_ZIP_FILE_HANDLES;

    // UNCOMMENT THIS TO SPEEDUP. WILL REQUIRE MORE RAM AND FAIL FOR BIG-BIG FILES

    //if (/*$zip_entry_file_size == 0 ||*/ $zip_entry_file_size == filesize($TheFile)) return implode('', cms_file_safe($TheFile));

    $FH = $M_ZIP_FILE_HANDLES[$zip_entry[0]];
    if ($FH) {
        $res = '';
        while ((strlen($res) < $zip_entry_file_size) && (!feof($FH))) {
            $read_amount = min(4096, $zip_entry_file_size - strlen($res));
            $res .= fread($FH, $read_amount);
        }

        if ($res == '') {
            return false;
        } else {
            return $res;
        }
    }
    return false; // Boh? (that in italian means who-knows-if-it-is-right-or-wrong-,-anyway-I-have-serious-perplexities-about-it-all)
}

/**
 * Delete a directory of files.
 * From "User Contributed Notes" at http://php.net/manual/en/function.rmdir.php. Thanks flexer at cutephp dot com.
 *
 * @param PATH $a_dir The path to the directory
 */
function m_deldir(string $a_dir)
{
    // Support for trailing slash
    if ((substr($a_dir, -1) == '/') || (substr($a_dir, -1) == '\\')) {
        $a_dir = substr($a_dir, 0, -1);
    }

    if (is_dir($a_dir)) {
        $current_dir = opendir($a_dir);
        while (false !== ($entryname = readdir($current_dir))) {
            if (is_dir($a_dir . '/' . $entryname) && ($entryname != '.' && $entryname != '..')) {
                m_deldir($a_dir . '/' . $entryname);
            } elseif ($entryname != '.' && $entryname != '..') {
                unlink($a_dir . '/' . $entryname);
            }
        }
        closedir($current_dir);
        rmdir($a_dir);
    } else {
        if (php_function_allowed('error_log')) {
            error_log(brand_name() . ': WARNING m_deldir() Not a directory: ' . $a_dir);
        }
    }
}

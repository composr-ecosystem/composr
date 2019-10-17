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

/*
Not doing (from Zend's Code Analyzer):
 - if-else-else check (Reason: doesn't confuse us)
 - unnecessary reference check (Reason: lies!)
 - error for breaking with a variable (Reason: would only get used for a good reason, so let programmers do it if they have one)
 - Values overwritten before use (Reason: we may be initialising them for code clarity; PHPStorm's inspector can show these)
Not doing (from HPHP's warnings):
 - Anything specific to compilation (Reason: we must manually ensure that works, often code branching structures will be involved but warnings still happen)
 - Wrong number of parameters, on method of subclass (Reason: very hard to do)
 - Undeclared constant (Reason: we don't track these)
Not doing (from CodeLobster Errors):
 - IF and ELSEIF have same conditions (theoretically the prior IF might have changed the state)
Not doing (from PhpStorm Code Inspector):
 - Many (lots of false positives in here)
 - "Missing return statement" (this is useful, but if we have like a warn_exit at the end of a function, this would trigger the error)
*/

ini_set('memory_limit', '-1');

ini_set('display_errors', '1');

ini_set('pcre.jit', '0'); // Compatibility issue in PHP 7.3, "JIT compilation failed: no more memory"

$extra = array();
if (isset($_SERVER['argv'])) {
    foreach ($_SERVER['argv'] as $index => $argv) {
        $argv = str_replace('\\\\', '\\', $argv);
        $_SERVER['argv'][$index] = $argv;
        $explode = explode('=', $argv, 2);
        if (count($explode) == 2) {
            $extra[$explode[0]] = $explode[1];
            unset($_SERVER['argv'][$index]);
        }
    }
    $_SERVER['argv'] = array_merge($_SERVER['argv'], $extra);
    if (array_key_exists('api', $_SERVER['argv'])) {
        $GLOBALS['API'] = true;
    }
    if (array_key_exists('mixed', $_SERVER['argv'])) {
        $GLOBALS['MIXED'] = true;
    }
    if (array_key_exists('pedantic', $_SERVER['argv'])) {
        $GLOBALS['PEDANTIC'] = true;
    }
    if (array_key_exists('todo', $_SERVER['argv'])) {
        $GLOBALS['TODO'] = true;
    }
    if (array_key_exists('security', $_SERVER['argv'])) {
        $GLOBALS['SECURITY'] = true;
    }
    if (array_key_exists('checks', $_SERVER['argv'])) {
        $GLOBALS['CHECKS'] = true;
    }
    if (array_key_exists('spelling', $_SERVER['argv'])) {
        $GLOBALS['SPELLING'] = true;
    }
}
if (array_key_exists('api', $_GET)) {
    $GLOBALS['API'] = true;
}
if (array_key_exists('mixed', $_GET)) {
    $GLOBALS['MIXED'] = true;
}
if (array_key_exists('pedantic', $_GET)) {
    $GLOBALS['PEDANTIC'] = true;
}
/*if (array_key_exists('todo', $_GET)) { */
$GLOBALS['TODO'] = true; // NB: Unit test skips these ones anyway
/* } */
if (array_key_exists('security', $_GET)) {
    $GLOBALS['SECURITY'] = true;
}
if (array_key_exists('checks', $_GET)) {
    $GLOBALS['CHECKS'] = true;
}
if (array_key_exists('spelling', $_GET)) {
    $GLOBALS['SPELLING'] = true;
}

require_once('tests.php');
require_once('lex.php');
require_once('parse.php');

require_once('lib.php');

if (isset($_SERVER['argv'])) {
    if (array_key_exists('path', $_SERVER['argv'])) {
        $GLOBALS['COMPOSR_PATH'] = $_SERVER['argv']['path'];
    }
}
global $COMPOSR_PATH, $START_TIME, $MADE_CALL;
$START_TIME = time();
$MADE_CALL = null;

// Load up table info
global $TABLE_FIELDS;
if (file_exists($COMPOSR_PATH . '/data/db_meta.bin') && (filemtime($COMPOSR_PATH . '/index.php') < filemtime($COMPOSR_PATH . '/data/db_meta.bin'))) {
    $_table_fields = unserialize(file_get_contents($COMPOSR_PATH . '/data/db_meta.bin'));
    $TABLE_FIELDS = $_table_fields['tables'];
} else {
    $TABLE_FIELDS = array();
}
$TABLE_FIELDS['db_meta'] = array(
    'addon' => 'core',
    'fields' => array(
        'm_table' => '*ID_TEXT',
        'm_name' => '*ID_TEXT',
        'm_type' => 'ID_TEXT',
    )
);
$TABLE_FIELDS['db_meta_indices'] = array(
    'addon' => 'core',
    'fields' => array(
        'i_table' => '*ID_TEXT',
        'i_name' => '*ID_TEXT',
        'i_fields' => '*ID_TEXT',
    )
);

global $KNOWN_EXTRA_FUNCTIONS;
$KNOWN_EXTRA_FUNCTIONS = array(
    'critical_error' => true,
    'file_array_exists' => true,
    'file_array_get' => true,
    'file_array_count' => true,
    'file_array_get_at' => true,
    'master__sync_file' => true,
    'master__sync_file_move' => true,
    '__construct' => true,
);

global $KNOWN_EXTRA_CLASSES;
$KNOWN_EXTRA_CLASSES = array(
    'stdClass' => true,

    'Exception' => true,
    'ErrorException' => true,
    //PHP7+ 'Error' => true,
    //PHP7+ 'ParseError' => true,
    //PHP7+ 'TypeError' => true,
    //PHP7+ 'ArithmeticError' => true,
    //PHP7+ 'DivisionByZeroError' => true,
    'LogicException' => true,
    'BadFunctionCallException' => true,
    'BadMethodCallException' => true,
    'DomainException' => true,
    'InvalidArgumentException' => true,
    'LengthException' => true,
    'OutOfRangeException' => true,
    'RuntimeException' => true,
    'OutOfBoundsException' => true,
    'OverflowException' => true,
    'RangeException' => true,
    'UnderflowException' => true,
    'UnexpectedValueException' => true,
    //PHP7+ 'AssertionError' => true,

    'Closure' => true,
    'Generator' => true,
    'ClosedGeneratorException' => true,

    'DateTime' => true,
    'DateTimeImmutable' => true,
    'DateTimeZone' => true,
    'DateInterval' => true,
    'DatePeriod' => true,

    'RecursiveIteratorIterator' => true,
    'IteratorIterator' => true,
    'FilterIterator' => true,
    'RecursiveFilterIterator' => true,
    'CallbackFilterIterator' => true,
    'RecursiveCallbackFilterIterator' => true,
    'ParentIterator' => true,
    'LimitIterator' => true,
    'CachingIterator' => true,
    'RecursiveCachingIterator' => true,
    'NoRewindIterator' => true,
    'AppendIterator' => true,
    'InfiniteIterator' => true,
    'RegexIterator' => true,
    'RecursiveRegexIterator' => true,
    'EmptyIterator' => true,
    'RecursiveTreeIterator' => true,
    'ArrayIterator' => true,
    'RecursiveArrayIterator' => true,
    'DirectoryIterator' => true,
    'FilesystemIterator' => true,
    'RecursiveDirectoryIterator' => true,
    'GlobIterator' => true,
    'MultipleIterator' => true,
    'ArrayObject' => true,

    'SplFileInfo' => true,
    'SplFileObject' => true,
    'SplTempFileObject' => true,
    'SplDoublyLinkedList' => true,
    'SplQueue' => true,
    'SplStack' => true,
    'SplHeap' => true,
    'SplMinHeap' => true,
    'SplMaxHeap' => true,
    'SplPriorityQueue' => true,
    'SplFixedArray' => true,
    'SplObjectStorage' => true,

    'php_user_filter' => true,

    'Directory' => true,
);

// Special funcs (these may have been defined with stubs, but this says to mark them as requiring guards anyway)...
global $EXT_FUNCS;
$EXT_FUNCS = array(
    'gzclose' => true,
    'gzopen' => true,
    'gzread' => true,
    'gzwrite' => true,
    'gzcompress' => true,
    'gzdeflate' => true,
    'gzencode' => true,
    'gzfile' => true,
    'gzinflate' => true,
    'gzuncompress' => true,
    /*'gd_info' => true,
    'getimagesize' => true,
    'getimagesizefromstring' => true,
    'image_type_to_extension' => true,
    'image_type_to_mime_type' => true,
    'imagealphablending' => true,
    'imagechar' => true,
    'imagecharup' => true,
    'imagecolorallocate' => true,
    'imagecolorallocatealpha' => true,
    'imagecolorat' => true,
    'imagecolorclosest' => true,
    'imagecolorclosestalpha' => true,
    'imagecolorclosesthwb' => true,
    'imagecolordeallocate' => true,
    'imagecolorexact' => true,
    'imagecolorexactalpha' => true,
    'imagecolormatch' => true,
    'imagecolorresolve' => true,
    'imagecolorresolvealpha' => true,
    'imagecolorset' => true,
    'imagecolorsforindex' => true,
    'imagecolorstotal' => true,
    'imagecolortransparent' => true,
    'imagecopy' => true,
    'imagecopymerge' => true,
    'imagecopymergegray' => true,
    'imagecopyresampled' => true,
    'imagecopyresized' => true,
    'imagecreate' => true,
    'imagecreatefromgif' => true,
    'imagecreatefromjpeg' => true,
    'imagecreatefrompng' => true,
    'imagecreatefrombmp' => true,
    'imagecreatefromwebp' => true,
    'imagecreatefromstring' => true,
    'imagecreatetruecolor' => true,
    'imagedestroy' => true,
    'imagefill' => true,
    'imagefontheight' => true,
    'imagefontwidth' => true,
    'imageftbbox' => true,
    'imagefttext' => true,
    'imagegif' => true,
    'imageistruecolor' => true,
    'imagejpeg' => true,
    'imagebmp' => true,
    'imagewebp' => true,
    'imageloadfont' => true,
    'imagepalettecopy' => true,
    'imagepng' => true,
    'imagesavealpha' => true,
    'imagestring' => true,
    'imagestringup' => true,
    'imagesx' => true,
    'imagesy' => true,
    'imagetruecolortopalette' => true,
    'imagepalettetotruecolor' => true,
    'imagettfbbox' => true,
    'imagettftext' => true,
    'imagetypes' => true,
    'iptcembed' => true,
    'iptcparse' => true,*/
);

// Error funcs...
global $FALSE_ERROR_FUNCS;
$FALSE_ERROR_FUNCS = array(
    'base64_decode' => true,
    'fsockopen' => true,
    'ftell' => true,
    'ftp_connect' => true,
    'ftp_fput' => true,
    'ftp_nlist' => true,
    'ftp_size' => true,
    'ftp_cdup' => true,
    'ftp_pasv' => true,
    'ftp_rawlist' => true,
    'ftp_chdir' => true,
    'ftp_pwd' => true,
    'ftp_login' => true,
    'ftp_mkdir' => true,
    'ftp_rmdir' => true,
    'ftp_get' => true,
    'ftp_fget' => true,
    'ftp_put' => true,
    'ftp_rename' => true,
    'ftp_delete' => true,
    'ftp_site' => true,
    'gzopen' => true,
    'imagecreatefromstring' => true,
    'imagecreatefrompng' => true,
    'imagecreatefrombmp' => true,
    'imagecreatefromwebp' => true,
    'imagecreatefromjpeg' => true,
    'imagettfbbox' => true,
    'imagettftext' => true,
    'imageloadfont' => true,
    'strtok' => true,
    'include' => true,
    'include_once' => true,
    'ldap_bind' => true,
    'ldap_connect' => true,
    'ldap_list' => true,
    'ldap_search' => true,
    'ldap_add' => true,
    'ldap_compare' => true,
    'ldap_delete' => true,
    'ldap_mod_add' => true,
    'ldap_mod_del' => true,
    'ldap_mod_replace' => true,
    'ldap_modify' => true,
    'ldap_next_attribute' => true,
    'ldap_next_entry' => true,
    'ldap_read' => true,
    'ldap_rename' => true,
    'mail' => true,
    'move_uploaded_file' => true,
    'mysql_connect' => true,
    'mysql_data_seek' => true,
    'mysql_fetch_assoc' => true,
    'mysql_fetch_row' => true,
    'mysql_field_name' => true,
    'mysql_field_len' => true,
    'mysql_field_flags' => true,
    'mysql_field_type' => true,
    'mysql_field_seek' => true,
    'mysql_field_table' => true,
    'mysql_fetch_field' => true,
    'mysql_fetch_object' => true,
    'mysql_list_dbs' => true,
    'mysql_result' => true,
    'mysql_unbuffered_query' => true,
    'mysql_pconnect' => true,
    'mysql_query' => true,
    'ob_get_contents' => true,
    'ob_end_flush' => true,
    //'ob_end_clean' => true,
    'parse_url' => true,
    'posix_getgrgid' => true,
    'posix_getpwuid' => true,
    'readdir' => true,
    'setcookie' => true,
    //'header' => true,
    'setlocale' => true,
    'shell_exec' => true,
    'unserialize' => true,
    'xml_parse' => true,
    'xml_parser_create_ns' => true,
    'unpack' => true,
    'pspell_new_config' => true,
    'pspell_save_wordlist' => true,
    'xml_parser_create' => true,
    'xml_parse_into_struct' => true,
    'system' => true,
    'fgetc' => true,
    'fread' => true,
    'fgets' => true,
    'fgetcsv' => true,
    'fgetss' => true,
    'ftruncate' => true,
    'pfsockopen' => true,
    'ob_get_length' => true,
    'openlog' => true,
    'popen' => true,
    'gethostbynamel' => true,
    'getimagesize' => true,
    'get_cfg_var' => true,
    'gzinflate' => true,
    'gzuncompress' => true,
    'session_decode' => true,
    'error_log' => true,
    'session_cache_limiter' => true,
    'session_start' => true,
    'imagepng' => true,
    'imagejpeg' => true,
    'imagebmp' => true,
    'imagewebp' => true,
    'gethostbyname' => true,
    'imagecreatetruecolor' => true,
    'imagetruecolortopalette' => true,
    'imagesetthickness' => true,
    'imageellipse' => true,
    'imagefilledellipse' => true,
    'imagefilledarc' => true,
    'imagealphablending' => true,
    'imagecolorresolvealpha' => true,
    'imagecolorexactalpha' => true,
    'imagecopyresampled' => true,
    'imagesettile' => true,
    'imagesetbrush' => true,
    'putenv' => true,
    'rmdir' => true,
    'opendir' => true,
    'copy' => true,
    'file' => true,
    'fopen' => true,
    'file_get_contents' => true,
    'file_put_contents' => true,
    //'chmod' => true,
    //'chgrp' => true,
    //'unlink' => true,
    'mkdir' => true,
    'rename' => true,
    //'chdir' => true,
    'filectime' => true,
    'filegroup' => true,
    'filemtime' => true,
    'fileowner' => true,
    'fileperms' => true,
    'filesize' => true,
    'pathinfo' => true,
    'fileatime' => true,
    'md5_file' => true,
    'readfile' => true,
    'readgzfile' => true,
    'filetype' => true,
    'parse_ini_file' => true,
    'is_executable' => true,
    'disk_free_space' => true,
    'disk_total_space' => true,
    'get_meta_tags' => true,
    'gzfile' => true,
    'tempnam' => true,
    'tmpfile' => true,
    'flock' => true,
    'touch' => true,
    'strpos' => true,
    'strrpos' => true,
    'strstr' => true,
    'stristr' => true,
);
global $NULL_ERROR_FUNCS;
$NULL_ERROR_FUNCS = array(
    'array_pop' => true,
    'array_shift' => true,
);
global $VAR_ERROR_FUNCS;
$VAR_ERROR_FUNCS = array(
    'strpos' => true,
    'strrpos' => true,
    'strstr' => true,
    'stristr' => true,
    'substr_count' => true,
);
global $ERROR_FUNCS;
$ERROR_FUNCS = array(
    'ftp_fput' => true,
    'ftp_nlist' => true,
    'ftp_size' => true,
    'ftp_pasv' => true,
    'ftp_rawlist' => true,
    'ftp_cdup' => true,
    'ftp_chdir' => true,
    'ftp_pwd' => true,
    'ftp_login' => true,
    'ftp_mkdir' => true,
    'ftp_rmdir' => true,
    'ftp_get' => true,
    'ftp_fget' => true,
    'ftp_put' => true,
    'ftp_rename' => true,
    'ftp_delete' => true,
    'ftp_site' => true,
    'gzopen' => true,
    'imagecreatefromstring' => true,
    'imagecreatefrompng' => true,
    'imagecreatefrombmp' => true,
    'imagecreatefromwebp' => true,
    'imagecreatefromjpeg' => true,
    'ldap_bind' => true,
    'ldap_connect' => true,
    'ldap_list' => true,
    'ldap_search' => true,
    'ldap_add' => true,
    'ldap_compare' => true,
    'ldap_delete' => true,
    'ldap_mod_add' => true,
    'ldap_mod_del' => true,
    'ldap_mod_replace' => true,
    'ldap_modify' => true,
    'ldap_read' => true,
    'ldap_rename' => true,
    'mail' => true,
    'move_uploaded_file' => true,
    'mysql_data_seek' => true,
    'mysql_field_name' => true,
    'mysql_field_len' => true,
    'mysql_field_flags' => true,
    'mysql_field_type' => true,
    'mysql_field_seek' => true,
    'mysql_field_table' => true,
    'ob_end_flush' => true,
    'ob_end_clean' => true,
    'parse_url' => true,
    'shell_exec' => true,
    'unserialize' => true,
    'unpack' => true,
    'system' => true,
    'popen' => true,
    'getimagesize' => true,
    'error_log' => true,
    'session_cache_limiter' => true,
    'session_start' => true,
    'imagepng' => true,
    'imagejpeg' => true,
    'imagebmp' => true,
    'imagewebp' => true,
    'imagettfbbox' => true,
    'imagettftext' => true,
    'gethostbyname' => true,
    'imagecreatetruecolor' => true,
    'imagetruecolortopalette' => true,
    'imagesetthickness' => true,
    'imageellipse' => true,
    'imagefilledellipse' => true,
    'imagefilledarc' => true,
    'imagealphablending' => true,
    'imagecolorresolvealpha' => true,
    'imagecolorexactalpha' => true,
    'imagecopyresampled' => true,
    'imagesettile' => true,
    'imagesetbrush' => true,
    'putenv' => true,
    'rmdir' => true,
    'opendir' => true,
    'copy' => true,
    'file' => true,
    'fopen' => true,
    'chmod' => true,
    'chgrp' => true,
    'unlink' => true,
    'mkdir' => true,
    'rename' => true,
    'chdir' => true,
    'filectime' => true,
    'filegroup' => true,
    'filemtime' => true,
    'fileowner' => true,
    'fileperms' => true,
    'filesize' => true,
    'pathinfo' => true,
    'fileatime' => true,
    'md5_file' => true,
    'readfile' => true,
    'readgzfile' => true,
    'filetype' => true,
    'parse_ini_file' => true,
    'is_executable' => true,
    'disk_free_space' => true,
    'disk_total_space' => true,
    'get_meta_tags' => true,
    'gzfile' => true,
    'tempnam' => true,
    'tmpfile' => true,
    'flock' => true,
    'touch' => true,
    'highlight_file' => true,
    'set_time_limit' => true,
    'exec' => true,
    'passthru' => true,
);

if (function_exists('set_time_limit')) {
    @set_time_limit(10000);
}

global $COMPOSR_PATH;
global $FUNCTION_SIGNATURES;
$FUNCTION_SIGNATURES = array();

if (isset($GLOBALS['API'])) {
    load_function_signatures();
}

// To get it started
if (isset($_GET['test'])) {
    // Checking an internal test

    $GLOBALS['API'] = 1;
    load_function_signatures();

    $GLOBALS['CHECKS'] = 1;

    $tests = get_tests();
    $parsed = parse(lex('<' . '?php' . "\n" . $tests[$_GET['test']] . "\n"));
    check($parsed);
} elseif ((!isset($_GET['to_use'])) && (!isset($_SERVER['argv'][1]))) {
    // Search for stuff to check

    $GLOBALS['API'] = 1;
    load_function_signatures();

    $avoid = array();
    if (isset($_GET['avoid'])) {
        $avoid = explode(',', $_GET['avoid']);
    }
    $filter = array();
    if (isset($_GET['filter'])) {
        $filter = explode(',', $_GET['filter']);
    }
    $filter_avoid = array();
    if (isset($_GET['filter_avoid'])) {
        $filter_avoid = explode(',', $_GET['filter_avoid']);
    }
    $enable_custom = isset($_GET['enable_custom']) && ($_GET['enable_custom'] == '1');
    $files = do_dir($COMPOSR_PATH . (isset($_GET['subdir']) ? ('/' . $_GET['subdir']) : ''), $enable_custom, true, $avoid, $filter, $filter_avoid);
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    foreach ($files as $i => $to_use) {
        if ($i < $start) {
            continue; // Set to largest number we know so far work
        }

        if (strpos(file_get_contents($to_use), '/*CQC:' . ' No check*/') !== false) {
            //echo 'SKIP: ' . $to_use;
            continue;
        }

        try {
            check(parse_file($to_use, false, false, $i, count($files)));
        } catch (Exception $e) {
            echo $e->getMessage() . cnl();
        }
    }
} else {
    // Given list of things to check

    $_to_use = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : $_GET['to_use'];
    if (isset($_SERVER['argv']) && array_key_exists('single', $_SERVER['argv'])) {
        $to_use_multi = array($_to_use);
    } else {
        if ((isset($_SERVER['argv'])) && ((array_key_exists(2, $_SERVER['argv'])) || (array_key_exists('spacedpaths', $_SERVER['argv'])))) {
            $to_use_multi = array();
            foreach ($_SERVER['argv'] as $key => $val) {
                if ((is_integer($key)) && ($key != 0)) {
                    $to_use_multi[] = $val;
                }
            }
        } else {
            $to_use_multi = explode(':', $_to_use);
        }
    }

    foreach ($to_use_multi as $to_use) {
        $full_path = (($COMPOSR_PATH == '') ? '' : ($COMPOSR_PATH . ((substr($COMPOSR_PATH, -1) == DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR))) . $to_use;

        if (strpos(file_get_contents($full_path), '/*CQC:' . ' No check*/') !== false) {
            echo 'SKIP: ' . $to_use . cnl();
            continue;
        }

        try {
            check(parse_file($full_path));
        } catch (Exception $e) {
            echo $e->getMessage() . cnl();
        }
    }
}
echo 'FINAL Done!';

function load_function_signatures()
{
    // Load up function info
    global $FUNCTION_SIGNATURES;
    global $COMPOSR_PATH;
    $functions_file_path = file_exists($COMPOSR_PATH . '/data_custom/functions.bin') ? ($COMPOSR_PATH . '/data_custom/functions.bin') : 'functions.bin';
    $functions_file = file_get_contents($functions_file_path);
    $FUNCTION_SIGNATURES = unserialize($functions_file);
}

// Do the actual code quality check
function check($structure)
{
    global $GLOBAL_VARIABLES, $CURRENT_CLASS, $OK_EXTRA_FUNCTIONS, $STRUCTURE;
    $GLOBAL_VARIABLES = array();
    $OK_EXTRA_FUNCTIONS = $structure['ok_extra_functions'];
    $STRUCTURE = $structure;

    $CURRENT_CLASS = '__global';
    global $LOCAL_VARIABLES;
    $LOCAL_VARIABLES = reinitialise_local_variables();
    if ($GLOBALS['OK_EXTRA_FUNCTIONS'] == '') { // Useful for tests to be able to define functions natively
        foreach ($structure['functions'] as $function) {
            if ($GLOBALS['OK_EXTRA_FUNCTIONS'] != '') {
                $GLOBALS['OK_EXTRA_FUNCTIONS'] .= '|';
            }
            $GLOBALS['OK_EXTRA_FUNCTIONS'] .= $function['name'];
        }
        foreach ($structure['classes'] as $class) {
            if ($GLOBALS['OK_EXTRA_FUNCTIONS'] != '') {
                $GLOBALS['OK_EXTRA_FUNCTIONS'] .= '|';
            }
            $GLOBALS['OK_EXTRA_FUNCTIONS'] .= $class['name'];
        }
    }
    check_command($structure['main'], 0);
    $local_variables = $LOCAL_VARIABLES;
    foreach ($structure['functions'] as $function) {
        check_function($function);
    }
    foreach ($structure['classes'] as $class) {
        if (isset($GLOBALS['API'])) {
            if (substr($class['name'], 0, 1) == strtolower(substr($class['name'], 0, 1))) {
                log_warning('Class names should start with an upper case letter, \'' . $class['name'] . '\'');
            }
        }

        /*if (substr($class['name'], 1) != strtolower(substr($class['name'], 1))) {     Too Composr-specific
            log_warning('Class names should be lower case apart from the first letter, \'' . $class['name'] . '\'');
        }*/

        $CURRENT_CLASS = $class['name'];
        foreach ($class['functions'] as $function) {
            if (strtolower($function['name']) == strtolower($class['name'])) {
                log_warning('Use __construct for construct name, not \'' . $function['name'] . '\'', $function['offset']);
            }

            check_function($function, false, true);
        }

        foreach ($class['vars'] as $var) {
            check_expression($var[1]);
        }

        foreach ($class['constants'] as $constant) {
            check_expression($constant[1]);
        }
    }
    check_variable_list($local_variables, 0);

    // Check for type conflicts in the global variables
    check_variable_list($GLOBAL_VARIABLES);
}

function check_function($function, $is_closure = false, $inside_class = false)
{
    global $GLOBAL_VARIABLES, $LOCAL_VARIABLES, $CURRENT_CLASS;
    if (!$is_closure) {
        $LOCAL_VARIABLES = reinitialise_local_variables($inside_class); // Map (by name) of maps : is_global, types. Note there is boolean-false and null types: boolean_false is when we KNOW a boolean is false, so it might map to ~
    }

    //if (isset($GLOBALS['PEDANTIC'])) if (strlen(serialize($function)) > 30000) log_warning('Function ' . $function['name'] . ' is too big', $function['offset']);

    global $FUNCTION_SIGNATURES;
    $class = $CURRENT_CLASS;
    if (isset($FUNCTION_SIGNATURES[$class]['functions'][$function['name']])) {
        $func = $FUNCTION_SIGNATURES[$class]['functions'][$function['name']];
    } else {
        $func = null;
    }

    // Initialise any local variables that come from parameters
    foreach ($function['parameters'] as $p) {
        add_variable_reference($p[1], $function['offset'], false);
        if (isset($func)) {
            foreach ($func['parameters'] as $x) {
                if ((isset($x['name'])) && ($x['name'] == $p[1])) {
                    set_composr_type($p[1], $x['type']);
                    break;
                }
            }
        } else {
            set_composr_type($p[1], 'mixed');
        }
    }

    // Check commands
    check_command($function['code'], 0);

    // Check for type conflicts in the variables
    check_variable_list($LOCAL_VARIABLES, $function['offset']);

    // Update global variables
    foreach ($LOCAL_VARIABLES as $name => $v) {
        if ($v['is_global']) {
            if (isset($GLOBAL_VARIABLES[$name])) {
                $GLOBAL_VARIABLES[$name]['types'] = array_merge($GLOBAL_VARIABLES[$name]['types'], $v['types']);
            } else {
                $GLOBAL_VARIABLES[$name] = $v;
            }
        }
    }

    // Return stuff
    if (isset($func)) {
        $ret = (isset($func['return']));

        // Check a return is given if the function returns and the opposite
        if (($ret) && ($func['return']['type'] != 'mixed') && (!in_array('abstract', $function['modifiers'])) && (!isset($LOCAL_VARIABLES['__return']))) {
            log_warning('Function \'' . $function['name'] . '\' is missing a return statement.', $function['offset']);
        }
        if ((!$ret) && (isset($LOCAL_VARIABLES['__return'])) && (array_unique($LOCAL_VARIABLES['__return']['types']) != array('null'))) {
            log_warning('Function \'' . $function['name'] . '\' has a return with a value, and the function doesn\'t return a value', $LOCAL_VARIABLES['__return']['first_mention']);
        }

        // Check return types
        if (($ret) && (isset($LOCAL_VARIABLES['__return']['types']))) {
            foreach ($LOCAL_VARIABLES['__return']['types'] as $i => $ret_type) {
                ensure_type(array($func['return']['type']), $ret_type, $LOCAL_VARIABLES['__return']['mentions'][$i], 'Bad return type (should be ' . $func['return']['type'] . ' not ' . $ret_type . ')');
            }
        }
    }
}

function check_variable_list($LOCAL_VARIABLES, $offset = -1)
{
    foreach ($LOCAL_VARIABLES as $name => $v) {
        // Check for type conflicts
        $observed_types = array();
        if (isset($GLOBALS['PEDANTIC'])) {
            foreach ($v['conditioner'] as $conditioner) {
                if (((!$v['conditioned_null']) && (isset($GLOBALS['NULL_ERROR_FUNCS'][$conditioner]))) || ((!$v['conditioned_false']) && (isset($GLOBALS['FALSE_ERROR_FUNCS'][$conditioner])))) {
                    log_warning('Error value was not handled', $v['first_mention']);
                    break;
                } elseif (($conditioner == '_divide_') && (!$v['conditioned_zero'])) {
                    log_warning('Divide by zero possibility was not handled', $v['first_mention']);
                    break;
                }
            }
        }
        foreach ($v['types'] as $t) {
            if (is_array($t)) {
                $t = $t[0];
            }

            $t = ltrim($t, '?~');
            if (substr($t, 0, 6) == 'object') {
                $t = 'object';
            }
            if ($t == 'REAL') {
                $t = 'float';
            }
            if (in_array($t, array('MEMBER', 'SHORT_INTEGER', 'UINTEGER', 'AUTO_LINK', 'BINARY', 'GROUP', 'TIME'))) {
                $t = 'integer';
            }
            if (in_array($t, array('LONG_TEXT', 'SHORT_TEXT', 'MINIID_TEXT', 'ID_TEXT', 'LANGUAGE_NAME', 'URLPATH', 'PATH', 'IP', 'EMAIL'))) {
                $t = 'string';
            }
            if (in_array($t, array('Tempcode'))) {
                $t = 'object';
            }
            if (in_array($t, array('list', 'map'))) {
                $t = 'array';
            }
            if ($t != 'mixed') {
                $observed_types[$t] = 1;
            }
        }
        if (array_keys($observed_types) != array('array', 'resource')) {
            if (
                (count($observed_types) > 3) ||
                ((count($observed_types) > 1) && (!isset($observed_types['boolean-false'])) && (!isset($observed_types['null']))) ||
                ((count($observed_types) > 2) && ((!isset($observed_types['boolean-false'])) || (!isset($observed_types['null'])))
                )
            ) {
                if (($name != '_') && ($name != '__return') && (!$v['mixed_tag'])) {
                    log_warning('Type conflict for variable: ' . $name . ' (' . implode(',', array_keys($observed_types)) . ')', $v['first_mention']);
                }
            }
        }

        // Check for solely mixed
        if (isset($GLOBALS['MIXED'])) {
            $non_mixed = false;
            foreach ($v['types'] as $t) {
                if ($t != 'mixed') {
                    $non_mixed = true;
                }
            }
            if ((!$non_mixed) && (count($v['types']) != 0)) {
                log_warning('Solely mixed variable: ' . $name, $v['first_mention']);
            }
        }

        // Check for non-used variables
        if (($GLOBALS['FILENAME'] != 'sources\phpstub.php') && ($v['references'] == 0) && ($name != '__return') && ($name != '_') && (!$v['is_global']) && (!in_array($name, array('db', 'file_base', 'table_prefix', 'old_base_dir', 'upgrade_from_hack', 'upgrade_from', 'this', 'GLOBALS', 'http_response_header',/*'_GET','_POST','_REQUEST','_COOKIE','_SERVER','_ENV', These are intentionally removed as they should only be used at one point in the code*/'_SESSION', '_FILES')))) {
            if (!$v['unused_value']) {
                log_warning('Non-used ' . ($v['unused_value'] ? 'value' : 'variable') . ' (\'' . $name . '\')', $v['first_mention']);
            }
        }
    }
}

function check_command($command, $depth, $function_guard = '', $nogo_parameters = array(), $jump_structures = array())
{
    global $LOCAL_VARIABLES, $CURRENT_CLASS, $FUNCTION_SIGNATURES;
    foreach ($command as $i => $c) {
        if ($c == array()) {
            continue;
        }

        if (is_integer($c[count($c) - 1])) {
            $c_pos = $c[count($c) - 1];
            $or = false;
        } else {
            $c_pos = $c[count($c) - 2];
            $or = true;
        }

        switch ($c[0]) {
            case 'CONST':
                foreach ($c[1] as $const) {
                    check_expression($const);
                }
                break;
            case 'CALL_METHOD':
                check_method($c, $c_pos, $function_guard);
                break;
            case 'CALL_INDIRECT':
                add_variable_reference($c[1][1], $c_pos);
                break;
            case 'VARIABLE':
                check_variable($c, false, $function_guard);
                break;
            case 'CALL_DIRECT':
                if (isset($GLOBALS['PEDANTIC'])) {
                    if ((isset($GLOBALS['NULL_ERROR_FUNCS'][$c[1]])) || (isset($GLOBALS['FALSE_ERROR_FUNCS'][$c[1]]))) {
                        log_warning('Crucial return value was not handled', $c_pos);
                    }
                }
                check_call($c, $c_pos, null, $function_guard);
                break;
            case 'GLOBAL':
                foreach ($c[1] as $v) {
                    if ((isset($LOCAL_VARIABLES[$v[1]])) && (!$LOCAL_VARIABLES[$v[1]]['is_global'])) {
                        log_warning($v[1] . ' was referenced before this globalisation.', $c_pos);
                    }
                    add_variable_reference($v[1], $c_pos, false);
                    $LOCAL_VARIABLES[$v[1]]['is_global'] = true;
                    $LOCAL_VARIABLES[$v[1]]['unused_value'] = true;
                }
                break;
            case 'YIELD_0':
                break;
            case 'YIELD_1':
                check_expression($c[1], false, false, $function_guard);
                break;
            case 'YIELD_2':
                check_expression($c[1], false, false, $function_guard);
                check_expression($c[2], false, false, $function_guard);
                break;
            case 'RETURN':
                $ret_type = check_expression($c[1], false, false, $function_guard);
                add_variable_reference('__return', $c_pos);
                set_composr_type('__return', $ret_type);
                if (!isset($LOCAL_VARIABLES['__return']['mentions'])) {
                    $LOCAL_VARIABLES['__return']['mentions'] = array();
                }
                $LOCAL_VARIABLES['__return']['mentions'][] = $c_pos;
                if (count($command) - 1 > $i) {
                    log_warning('There is unreachable code', $c_pos);
                }
                break;
            case 'SWITCH':
                $switch_type = check_expression($c[1], false, false, $function_guard);
                foreach ($c[2] as $case) {
                    if ($case[0] !== null) {
                        $passes = ensure_type(array($switch_type), check_expression($case[0], false, false, $function_guard), $c_pos, 'Switch type inconsistency');
                        if ($passes) {
                            infer_expression_type_to_variable_type($switch_type, $case[0]);
                        }
                    }
                    check_command($case[1], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, array('SWITCH')));
                }
                break;
            case 'STATIC_ASSIGNMENT':
                foreach ($c[1] as $_c) {
                    check_assignment($_c, $c_pos, $function_guard);
                }
                break;
            case 'ASSIGNMENT':
                check_assignment($c, $c_pos, $function_guard);
                break;
            case 'IF':
            case 'IF_ELSE':
                $t = check_expression($c[1], false, false, $function_guard);
                if ($c[0] == 'IF_ELSE') {
                    $passes = ensure_type(array('boolean'), $t, $c_pos, 'Conditionals must be boolean (if-else) [is ' . $t . ']', true);
                } else {
                    $passes = ensure_type(array('boolean'), $t, $c_pos, 'Conditionals must be boolean (if) [is ' . $t . ']', true);
                }
                if ($passes) {
                    infer_expression_type_to_variable_type('boolean', $c[1]);
                }
                $temp_function_guard = $function_guard;
                foreach (array(0, 1) as $function_parameter_pos) {
                    if (($c[1][0] == 'BOOLEAN_NOT') && ($c[1][1][0] == 'CALL_DIRECT') && ($c[1][1][1] == 'php_function_allowed' || strpos($c[1][1][1], '_exists') !== false) && (isset($c[1][1][2][$function_parameter_pos])) && ($c[1][1][2][$function_parameter_pos][0][0][0] == 'LITERAL') && ($c[1][1][2][$function_parameter_pos][1][0][0] == 'STRING') && (($c[2][0][0] == 'BREAK') || ($c[2][0][0] == 'CONTINUE') || ($c[2][0][0] == 'RETURN') || (($c[2][0][0] == 'CALL_DIRECT') && ($c[2][0][1] == 'critical_error')))) {
                        $temp_function_guard .= ',' . $c[1][1][2][$function_parameter_pos][0][1][1] . ',';
                    }
                    if (($c[1][0] == 'CALL_DIRECT') && ($c[1][1] == 'php_function_allowed' || strpos($c[1][1], '_exists') !== false) && (isset($c[1][2][$function_parameter_pos])) && ($c[1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($c[1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                        $temp_function_guard .= ',' . $c[1][2][$function_parameter_pos][0][1][1] . ',';
                    }

                    foreach (array(0, 1) as $and_position) { // NB: Can't check 3rd AND position because this is actually nested AND's, so we'd need to write recursive code or more hard-coded checking
                        if (($c[1][0] == 'BOOLEAN_AND') && ($c[1][$and_position + 1][0] == 'CALL_DIRECT') && ($c[1][$and_position + 1][1] == 'php_function_allowed' || strpos($c[1][$and_position + 1][1], '_exists') !== false) && (isset($c[1][$and_position + 1][2][$function_parameter_pos])) && ($c[1][$and_position + 1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($c[1][$and_position + 1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                            $temp_function_guard .= ',' . $c[1][$and_position + 1][2][$function_parameter_pos][0][1][1] . ',';
                        }
                        if (($c[1][0] == 'BOOLEAN_AND') && ($c[1][$and_position + 1][0] == 'PARENTHESISED') && ($c[1][$and_position + 1][1][0] == 'CALL_DIRECT') && ($c[1][$and_position + 1][1][1] == 'php_function_allowed' || strpos($c[1][$and_position + 1][1][1], '_exists') !== false) && (isset($c[1][$and_position + 1][1][2][$function_parameter_pos])) && ($c[1][$and_position + 1][1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($c[1][$and_position + 1][1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                            $temp_function_guard .= ',' . $c[1][$and_position + 1][1][2][$function_parameter_pos][0][1][1] . ',';
                        }
                    }
                }
                check_command($c[2], $depth, $temp_function_guard, $nogo_parameters, $jump_structures);
                if ($c[0] == 'IF_ELSE') {
                    check_command($c[3], $depth, $function_guard, $nogo_parameters, $jump_structures);
                }
                break;
            case 'INNER_FUNCTION':
                $temp = $LOCAL_VARIABLES;
                check_function($c[1]);
                $LOCAL_VARIABLES = $temp;
                break;
            case 'INNER_CLASS':
                $class = $c[1];
                foreach ($class['functions'] as $function) {
                    $temp = $LOCAL_VARIABLES;
                    $LOCAL_VARIABLES['this'] = array('is_global' => false, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('object'), 'references' => 0, 'object_type' => $CURRENT_CLASS, 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false);
                    check_function($function, false, true);
                    $LOCAL_VARIABLES = $temp;
                }
                break;
            case 'TRY':
                check_command($c[1], $depth + 1, $function_guard, $nogo_parameters, $jump_structures); // Goes first so that we get local variables defined inside loop for use in our loop conditional
                foreach ($c[2] as $catch) {
                    add_variable_reference($catch[1][0][1], $c_pos, false);
                    check_command($catch[2], $depth + 1, $function_guard, $nogo_parameters, $jump_structures); // Goes first so that we get local variables defined inside loop for use in our loop conditional
                }
                if ($c[3] !== null) {
                    check_command($c[3], $depth + 1, $function_guard, $nogo_parameters, $jump_structures); // Finally
                }
                break;
            case 'FOREACH_map':
                $passes = ensure_type(array('array'), check_expression($c[1], false, false, $function_guard), $c_pos, 'Foreach must take array');
                if ($passes) {
                    infer_expression_type_to_variable_type('array', $c[1]);
                }
                add_variable_reference($c[2][1], $c_pos, false);
                if ($c[3][0] == 'LIST') {
                    foreach ($c[3][1] as $var) {
                        if (count($var[2]) == 0) {
                            add_variable_reference($var[1], $c_pos, false);
                        }
                    }
                } else {
                    add_variable_reference($c[3][1], $c_pos, false);
                }

                if (in_array($c[2][1], $nogo_parameters)) {
                    log_warning('Re-using a loop variable, ' . $c[2][1], $c_pos);
                }
                if (in_array($c[3][1], $nogo_parameters)) {
                    log_warning('Re-using a loop variable, ' . $c[3][1], $c_pos);
                }

                check_command($c[4], $depth + 1, $function_guard, array_merge($nogo_parameters, array($c[2][1], $c[3][1])), array_merge($jump_structures, array('FOREACH_map')));
                break;
            case 'FOREACH_list':
                $passes = ensure_type(array('array'), check_expression($c[1], false, false, $function_guard), $c_pos, 'Foreach must take array');
                if ($passes) {
                    infer_expression_type_to_variable_type('array', $c[1]);
                }
                if ($c[2][0] == 'LIST') {
                    foreach ($c[2][1] as $var) {
                        if (count($var[2]) == 0) {
                            add_variable_reference($var[1], $c_pos, false);
                        }
                    }
                } else {
                    add_variable_reference($c[2][1], $c_pos, false);
                }

                if (in_array($c[2][1], $nogo_parameters)) {
                    log_warning('Re-using a loop variable, ' . $c[2][1], $c_pos);
                }

                check_command($c[3], $depth + 1, $function_guard, array_merge($nogo_parameters, array($c[2][1])), array_merge($jump_structures, array('FOREACH_list')));
                break;
            case 'FOR':
                if ($c[1] !== null) {
                    check_command(array($c[1]), $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, array('FOR')));
                }
                check_command(array($c[3]), $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, array('FOR')));
                $passes = ensure_type(array('boolean'), check_expression($c[2], false, false, $function_guard), $c_pos, 'Conditionals must be boolean (for)', true);
                if ($passes) {
                    infer_expression_type_to_variable_type('boolean', $c[2]);
                }
                check_command($c[4], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, array('FOR')));
                break;
            case 'DO':
                check_command($c[2], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, array('DO'))); // Goes first so that we get local variables defined inside loop for use in our loop conditional
                $passes = ensure_type(array('boolean'), check_expression($c[1], false, false, $function_guard), $c_pos, 'Conditionals must be boolean (do)', true);
                if ($passes) {
                    infer_expression_type_to_variable_type('boolean', $c[1]);
                }
                break;
            case 'WHILE':
                $passes = ensure_type(array('boolean'), check_expression($c[1], false, false, $function_guard), $c_pos, 'Conditionals must be boolean (while)', true);
                if ($passes) {
                    infer_expression_type_to_variable_type('boolean', $c[1]);
                }
                check_command($c[2], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, array('WHILE')));
                break;
            case 'CONTINUE':
                if (($c[1][0] == 'SOLO') && ($c[1][1][0] == 'LITERAL') && ($c[1][1][1][0] == 'INTEGER')) {
                    $continue_level = $c[1][1][1][1];
                } elseif (($c[1][0] == 'LITERAL') && ($c[1][1][0] == 'INTEGER')) {
                    $continue_level = $c[1][1][1];
                } else {
                    $continue_level = 1;
                }

                if ($continue_level > count($jump_structures)) {
                    if (count($jump_structures) == 0) {
                        log_warning('Nothing to continue out of', $c_pos);
                    } else {
                        log_warning('Continue level greater than loop/switch depth', $c_pos);
                        log_warning('Continue level greater than loop/switch depth', $c_pos);
                    }
                } else {
                    if ($jump_structures[count($jump_structures) - $continue_level] == 'SWITCH') {
                        log_warning('Cannot continue by level ' . $continue_level . ', as it is a switch statement', $c_pos);
                    }
                }

                $passes = ensure_type(array('integer'), check_expression($c[1], false, false, $function_guard), $c_pos, 'Loop/switch control must use integers (continue)');
                if ($passes) {
                    infer_expression_type_to_variable_type('integer', $c[1]);
                }
                break;
            case 'BREAK':
                $passes = ensure_type(array('integer'), check_expression($c[1], false, false, $function_guard), $c_pos, 'Loop/switch control must use integers (break)');
                if ($passes) {
                    infer_expression_type_to_variable_type('integer', $c[1]);
                }

                if (count($jump_structures) == 0) {
                    log_warning('Nothing to break out of', $c_pos);
                }
                break;
            case 'PRE_DEC':
                ensure_type(array('integer', 'float'), check_variable($c[1], false, $function_guard), $c_pos, 'Can only decrement numbers');
                break;
            case 'PRE_INC':
                ensure_type(array('integer', 'float'), check_variable($c[1], false, $function_guard), $c_pos, 'Can only increment numbers');
                break;
            case 'DEC':
                ensure_type(array('integer', 'float'), check_variable($c[1], false, $function_guard), $c_pos, 'Can only decrement numbers');
                break;
            case 'INC':
                ensure_type(array('integer', 'float'), check_variable($c[1], false, $function_guard), $c_pos, 'Can only increment numbers');
                break;
            case 'ECHO':
                foreach ($c[1] as $e) {
                    $passes = ensure_type(array('string'), check_expression($e, false, false, $function_guard), $c_pos, 'Can only echo strings');
                    if ($passes) {
                        infer_expression_type_to_variable_type('string', $e);
                    }
                }
                break;
        }

        if ($or) {
            check_command(array($c[count($c) - 1]), $depth, $function_guard, $nogo_parameters, $jump_structures);
        }
    }
}

function check_method($c, $c_pos, $function_guard = '')
{
    global $LOCAL_VARIABLES, $FUNCTION_SIGNATURES;

    $params = $c[2];

    if ($c[1] !== null) {
        check_variable($c[1], false, $function_guard);

        // Special rule for 'this->db'
        if (($c[1][1] == 'this') && ($c[1][2][1][1] == 'db') && ((!isset($c[1][2][2][0])) || ($c[1][2][2][0] != 'DEREFERENCE'))) {
            $method = $c[1][2][2][1][1];
            $class = 'DatabaseConnector';
            return actual_check_method($class, $method, $params, $c_pos, $function_guard);
        }

        // Special rule for $GLOBALS['?_DB']
        if (($c[1][1] == 'GLOBALS') && (substr($c[1][2][1][1][0], -3) == 'LITERAL') && (substr($c[1][2][1][1][1], -3) == '_DB') && ((!isset($c[1][2][2][2][0])) || ($c[1][2][2][2][0] != 'DEREFERENCE'))) {
            $method = $c[1][2][2][1][1];
            $class = 'DatabaseConnector';
            return actual_check_method($class, $method, $params, $c_pos, $function_guard);
        }

        // Special rule for $GLOBALS['FORUM_DRIVER']
        if (($c[1][1] == 'GLOBALS') && (substr($c[1][2][1][1][0], -3) == 'LITERAL') && ($c[1][2][1][1][1] == 'FORUM_DRIVER')) {
            $method = $c[1][2][2][1][1];
            $class = 'Forum_driver_base';
            return actual_check_method($class, $method, $params, $c_pos, $function_guard);
        }

        if ((isset($c[1][2][0])) && ($c[1][2][0] == 'DEREFERENCE') && (count($c[1][2][2]) == 0)) {
            $object = $c[1][1];
            $method = $c[1][2][1][1];
            add_variable_reference($object, $c_pos);

            if (((count($LOCAL_VARIABLES[$object]['types']) == 1) && ($LOCAL_VARIABLES[$object]['types'][0] == 'Tempcode')) || (isset($FUNCTION_SIGNATURES[$LOCAL_VARIABLES[$object]['object_type']]))) { // Construction
                $class = substr($LOCAL_VARIABLES[$object]['types'][0], 6);
                return actual_check_method($class, $method, $params, $c_pos, $function_guard);
            } else {
                // Parameters
                foreach ($params as $e) {
                    check_expression($e[0], false, false, $function_guard);
                }

                return 'mixed';
            }
        }

        scan_extractive_expressions($c[1][2]);
    }
    // Parameters
    foreach ($params as $e) {
        check_expression($e[0], false, false, $function_guard);
    }

    return 'mixed';
}

function actual_check_method($class, $method, $params, $c_pos, $function_guard = '')
{
    // Parameters
    foreach ($params as $e) {
        check_expression($e[0], false, false, $function_guard);
    }

    return check_call(array('CALL_DIRECT', $method, $params), $c_pos, $class, $function_guard);
}

function check_call($c, $c_pos, $class = null, $function_guard = '')
{
    global $CURRENT_CLASS;
    if ($class === null) {
        $class = '__global';
    }
    $class = preg_replace('#^(\?|~|object-)*#', '', $class);

    $params = $c[2];

    if ((isset($GLOBALS['PEDANTIC'])) && (array_key_exists(3, $c)) && (!$c[3])) {
        if (((isset($GLOBALS['VAR_ERROR_FUNCS'][$c[1]])) && (@$params[1][0][0] == 'VARIABLE')) || (isset($GLOBALS['ERROR_FUNCS'][$c[1]]))) {
            log_warning('Check this call is error-caught', $c_pos);
        }
    }

    $function = $c[1];
    if (isset($GLOBALS['CHECKS'])) {
        global $EXT_FUNCS;
        if (isset($EXT_FUNCS[$function])) {
            log_warning('Check for function_exists usage around ' . $function, $c_pos);
        }
    }
    $_function = ($class == '__global') ? $function : ($class . '.' . $function);
    if (isset($GLOBALS['SECURITY'])) {
        if (in_array($_function, get_insecure_functions())) {
            log_warning('Call to insecure function (' . $_function . ')', $c_pos);
        }
    }
    global $FUNCTION_SIGNATURES, $STRUCTURE;
    $ret = null;
    $found = false;

    if (isset($FUNCTION_SIGNATURES[$class]['functions'][$function])) {
        $potential = $FUNCTION_SIGNATURES[$class]['functions'][$function];
    } else {
        $potential = null;
    }
    if (($potential === null) && ($class == 'Forum_driver_base')) {
        $class = 'Forum_driver_cns';
        if (isset($FUNCTION_SIGNATURES[$class]['functions'][$function])) {
            $potential = $FUNCTION_SIGNATURES[$class]['functions'][$function];
        } else {
            $potential = null;
        }
    }

    if ($class == 'DatabaseConnector') {
        if ((count($params) >= 2) && ($params[0][0][0] == 'LITERAL')) {
            $table = $params[0][0][1][1];
            if ($function == 'query_insert') {
                $map = check_db_map($table, $params[1][0], $c_pos, true);
            }
            if ($function == 'query_update') {
                check_db_map($table, $params[1][0], $c_pos);
                if (isset($params[2][0])) {
                    check_db_map($table, $params[2][0], $c_pos);
                }
            }
            if ($function == 'query_select') {
                check_db_fields($table, $params[1][0], $c_pos);
                if (isset($params[2][0])) {
                    check_db_map($table, $params[2][0], $c_pos);
                }
            }
            if (($function == 'query_select_value') || ($function == 'query_select_value_if_there')) {
                check_db_field($table, $params[1][0], $c_pos);
                if (isset($params[2][0])) {
                    check_db_map($table, $params[2][0], $c_pos);
                }
            }
        }
    }

    foreach ($params as $param) {
        if ($param[0][0] == 'VARIABLE_REFERENCE') {
            log_warning('Call by reference to function \'' . $function . '\'', $c_pos);
        }
    }

    if ($potential !== null) {
        $found = true;
        if (isset($potential['return'])) {
            $ret = $potential['return'];
        }
        foreach ($potential['parameters'] as $i => $param) {
            if ((!isset($params[$i])) && ((count($params) == 0) || (!$params[count($params) - 1][1]/*not a vadiadic call*/)) && (!$param['is_variadic']/*not variadic parameter spot*/) && (!array_key_exists('default', $param))) {
                log_warning('Insufficient parameters to function \'' . $function . '\'', $c_pos);
                break;
            }
            if (isset($params[$i])) {
                $temp = $params[$i][0];

                // Can't pass references
                if (($temp[0] == 'SOLO') && (is_array($temp[1])) && ($temp[1][0] == 'VARIABLE_REFERENCE')) {
                    log_warning('Reference parameter passed to function \'' . $function . '\'', $c_pos);
                    break;
                }

                // If it is a referenced parameter then we must pass a variable expression not a general expression
                if ((@$param['ref']) && ($temp[0] != 'VARIABLE')) {
                    log_warning('A referenced parameter for \'' . $function . '\' was given a non-variable expression', $c_pos);
                    break;
                }

                $t = check_expression($temp, false, false, $function_guard);
                $passes = ensure_type(array($param['type']), $t, $c_pos, 'Parameter type error for ' . $function . '/' . ($i + 1) . ' (should be ' . $param['type'] . ' not ' . $t . ')');
                if (($t === 'float') && ($function === 'strval')) {
                    log_warning('Floats should not be used with strval, use float_to_raw_string or float_format', $c_pos);
                }
                if (isset($GLOBALS['CHECKS'])) {
                    if (($t === 'mixed') && ($function === 'count') && (strpos($c[2][0][1], 'rows') === false)) {
                        log_warning('Make sure that count parameter is definitely counting an array', $c_pos);
                    }
                }
                if ($passes) {
                    infer_expression_type_to_variable_type($param['type'], $temp);
                }
            } else {
                break;
            }
        }
        if ((count($potential['parameters']) < count($params)) && (count($potential['parameters']) != 0) && (!$potential['parameters'][count($potential['parameters']) - 1]['is_variadic']/*not a variadic function*/)) {
            log_warning('Too many parameters to function \'' . $function . '\'', $c_pos);
        }

        // Look for file-creators, and give notice that chmoding might be required to allow it to be deleted via FTP
        if (in_array('creates-file', $potential['flags'])) {
            if (isset($GLOBALS['CHECKS'])) {
                if (($function == 'fopen') && (in_array(@$params[1][0][1][1][0], array('w', 'a')))) {
                    log_warning('Call to \'' . $function . '\' that may create a file/folder. Check that the code chmods it so that FTP can delete it.', $c_pos);
                }
            }

            if (($function == 'fopen') && (in_array(@$params[1][0][1][1][0], array('t')))) {
                log_warning('Call to \'' . $function . '\' uses a text flag. This creates platform-dependent text files, which we do not want.', $c_pos);
            }
        }
    }

    if (($function == 'isset') && (@$params[0][0][0] != 'VARIABLE')) {
        log_warning('Can only pass variables to ' . $function, $c_pos);
    }

    if (($function == 'tempnam') && (@$params[0][0][0] == 'LITERAL') && (substr(@$params[0][0][1][1], 0, 4) == '/tmp')) {
        log_warning('Don\'t assume you can write to the shared temp directory', $c_pos);
    }
    if (($function == 'strpos') && (@$params[0][0][0] == 'LITERAL') && (@$params[1][0][0] != 'LITERAL')) {
        log_warning('Looks like strpos parameters are the wrong way around; you fell for a common API anomaly: unlike most functions like in_array, strpos is haystack followed by needle', $c_pos);
    }
    if ((($function == 'sprintf') || ($function == 'printf')) && (@$params[0][0][0] == 'LITERAL')) {
        $matches = array();
        $num_matches = preg_match_all('#\%[+-]?.?-?\d*(\.\d+)?(\$[bcdefuFodsxX])?#', $params[0][0][1][1], $matches);
        if ($num_matches + 1 != count($params)) {
            log_warning('Looks like the wrong number of parameters were sent to this [s]printf function, got ' . integer_format(count($params)) . ', expected, ' . integer_format($num_matches + 1), $c_pos);
        }
    }
    if ((isset($GLOBALS['CHECKS'])) && ($function == 'tempname')) {
        log_warning('Make sure temporary files are deleted', $c_pos);
    }
    //if ((isset($GLOBALS['CHECKS'])) && ($function == 'fopen')) log_warning('Make sure opened files are closed', $c_pos);  Not going to actually cause problems, as PHP'll close it when the script finishes
    if ((isset($GLOBALS['CHECKS'])) && ($function == 'get_username') && (@$params[0][0][1] != 'get_member')) {
        log_warning('Make sure guests/deleted-members are handled properly', $c_pos);
    }
    if ((isset($GLOBALS['CHECKS'])) && ($function == 'get_url')) {
        log_warning('Make sure that deleting the entry for this file/URL deletes the disk file', $c_pos);
    }
    if ((isset($GLOBALS['CHECKS'])) && (in_array($function, array('query_insert', 'insert_lang', 'insert_lang_comcode')))) {
        log_warning('Make sure that deleting the entry (or uninstalling) for this row deletes the row (if applicable)', $c_pos);
    }
    if ((isset($GLOBALS['CHECKS'])) && ($function == 'query_delete') && (!array_key_exists(2, $params))) {
        log_warning('Check that non-singular modification is wanted for this query', $c_pos);
    }
    if ((isset($GLOBALS['CHECKS'])) && ($function == 'query_update') && (!array_key_exists(3, $params))) {
        log_warning('Check that non-singular modification is wanted for this query', $c_pos);
    }
    if (($function == 'implode' || $function == 'explode')) {
        if ($params[0][0][0] != 'LITERAL' && $params[1][0][0] == 'LITERAL') {
            log_warning('You have almost certainly got the ' . $function . ' parameters the wrong way around', $c_pos);
        }
    }
    if ((isset($GLOBALS['CHECKS'])) && ($function == 'unlink')) {
        log_warning('Be very careful that shared URLs cannot be deleted (check upload dir, and staff access)', $c_pos);
    }
    if ((isset($GLOBALS['CHECKS'])) && (isset($GLOBALS['PEDANTIC'])) && (in_array($function, array('query_update', 'query_delete')))) {
        log_warning('Check log_it/cat-entry-handling/delete_lang', $c_pos);
    }
    //if ((isset($GLOBALS['CHECKS'])) && (isset($GLOBALS['PEDANTIC'])) && ($function == 'query_select')) log_warning('Check that non-singular select is wanted for this query', $c_pos);  This is REALLY pedantic ;) I'm sure MySQL is clever enough to see that only one row can match against a key

    if ($ret !== null) {
        return $ret['type'];
    }
    if (!$found) {
        if ((isset($FUNCTION_SIGNATURES['__global'])) && (count($FUNCTION_SIGNATURES['__global']['functions']) == 0)) {
            static $warned_missing_api_once = false;
            if (!$warned_missing_api_once) {
                log_warning('No API function metabase available', $c_pos);
            }
            $warned_missing_api_once = true;
        } elseif (isset($GLOBALS['API'])) {
            if (
                (
                    ($GLOBALS['OK_EXTRA_FUNCTIONS'] === null) ||
                    (
                        (preg_match('#^(' . $GLOBALS['OK_EXTRA_FUNCTIONS'] . ')#', $function) == 0) &&
                        (($class === null) || (preg_match('#^(' . $GLOBALS['OK_EXTRA_FUNCTIONS'] . ')#', $class) == 0))
                    )
                )
                &&
                (strpos($function_guard, ',' . $function . ',') === false) &&
                (!isset($GLOBALS['KNOWN_EXTRA_FUNCTIONS'][$function]))
                &&
                (
                    ($class === null)
                    ||
                    (
                        (strpos($function_guard, ',' . $class . ',') === false) &&
                        (!in_array($class, array('mixed', '?mixed', 'object', '?object', ''/*Dynamic*/))) &&
                        (!isset($GLOBALS['KNOWN_EXTRA_CLASSES'][$class]))
                    )
                )
            ) {
                if (($class === null) || ($class == '__global')) {
                    if ($function != '' && $function != 'ocp_mark_as_escaped' && $function != 'ocp_is_escaped'/*These aren't checked with function_exists, checked with a global, for performance reasons*/) {
                        log_warning('Could not find function \'' . $function . '\'', $c_pos);
                    }
                } else {
                    if (!isset($FUNCTION_SIGNATURES[$class])) {
                        if (in_array($class, array('integer', 'float', 'string', 'boolean', 'boolean-false', 'null'))) {
                            log_warning('Mixing variable type', $c_pos);
                        } else {
                            log_warning('Could not find class \'' . $class . '\'', $c_pos);
                        }
                    } elseif (empty($STRUCTURE['classes'][$class]['superclass'])) {
                        //@var_dump($FUNCTION_SIGNATURES[$class]['functions']);exit(); Useful for debugging
                        log_warning('Could not find method \'' . $class . '->' . $function . '\'', $c_pos);
                    }
                }
            }
        }
        foreach ($params as $param) {
            check_expression($param, false, false, $function_guard);
        }
        return 'mixed';
    }
    return $ret;
}

function check_db_map($table, $expr_map, $c_pos, $must_be_complete = false)
{
    $map = array();
    $arr_count = 0;
    if ($expr_map[0] == 'CREATE_ARRAY') {
        foreach ($expr_map[1] as $passing) {
            if (count($passing) == 1) {
                log_warning('Map required, list given', $c_pos);
            } else {
                if ($passing[0][0] == 'LITERAL') {
                    $type = check_expression($passing[1]);
                    if ($type == 'array') {
                        $arr_count++;
                    }
                    $map[$passing[0][1][1]] = $type;
                }
            }
        }
    }
    if ($arr_count == count($map)) {
        if ((!isset($GLOBALS['TABLE_FIELDS'][$table])) && ($GLOBALS['TABLE_FIELDS'] !== null)) {
            if ((strpos($table, ' ') === false) && (isset($GLOBALS['CHECKS']))) {
                log_warning('Unknown table referenced (' . $table . ')', $c_pos);
            }
        }
    } else {
        foreach ($map as $field => $type) {
            _check_db_field($table, $field, $c_pos, $type);
        }
    }
    if (($must_be_complete) && (isset($GLOBALS['TABLE_FIELDS'][$table])) && ($GLOBALS['TABLE_FIELDS'] !== null)) {
        if ((isset($GLOBALS['TABLE_FIELDS'][$table]['fields']['id'])) && (strpos($GLOBALS['TABLE_FIELDS'][$table]['fields']['id'], 'AUTO') !== false)) {
            $map['id'] = 'integer'; // Auto
        }
        $missing = implode(', ', array_diff(array_keys($GLOBALS['TABLE_FIELDS'][$table]['fields']), array_keys($map)));
        if (($missing != '') && (isset($GLOBALS['CHECKS']))) {
            log_warning('Field map for ' . $table . ' table may be incomplete (unsure, but can\'t see: ' . $missing . ' )', $c_pos);
        }
    }
    return $map;
}

function check_db_fields($table, $expr_map, $c_pos)
{
    if ($expr_map[0] == 'CREATE_ARRAY') {
        foreach ($expr_map[1] as $passing) {
            if (count($passing) == 2) {
                log_warning('The selection array must be a list, not a map', $c_pos);
            } else {
                check_db_field($table, $passing[0], $c_pos);
            }
        }
    }
}

function check_db_field($table, $expr_map, $c_pos)
{
    if (($expr_map[0] == 'LITERAL') && ($GLOBALS['TABLE_FIELDS'] !== null)) {
        _check_db_field($table, $expr_map[1][1], $c_pos);
    }
}

function _check_db_field($table, $field, $c_pos, $type = null)
{
    if ($GLOBALS['TABLE_FIELDS'] === null) {
        return;
    }

    if (!isset($GLOBALS['TABLE_FIELDS'][$table])) {
        if ((strpos($table, ' ') === false) && (isset($GLOBALS['CHECKS']))) {
            log_warning('Unknown table referenced (' . $table . ')', $c_pos);
        }
        return;
    }

    $field = str_replace('DISTINCT ', '', $field);
    $field = preg_replace('# AS .*#', '', $field);
    $field = preg_replace('#MAX\((.*)\)#', '${1}', $field);
    $field = preg_replace('#MIN\((.*)\)#', '${1}', $field);
    $field = preg_replace('#SUM\((.*)\)#', '${1}', $field);
    if (strpos($field, '*') !== false) {
        return;
    }

    if ((!isset($GLOBALS['TABLE_FIELDS'][$table]['fields'][$field])) && (strpos($field, '(') === false) && (isset($GLOBALS['CHECKS']))) {
        log_warning('Unknown field (' . $field . ') referenced', $c_pos);
        return;
    }

    if ($type !== null) {
        if (isset($GLOBALS['TABLE_FIELDS'][$table]['fields'][$field])) {
            $expected_type = str_replace('*', '', $GLOBALS['TABLE_FIELDS'][$table]['fields'][$field]);
            if (isset($GLOBALS['CHECKS'])) {
                ensure_type(array($expected_type), $type, $c_pos, 'DB field ' . $field . ' should be ' . $expected_type . ', not ' . $type);
            }
        }
    }
}

function get_insecure_functions()
{
    return array(
        'eval',
         'ldap_search', 'ldap_list',
         'register_shutdown_function', 'register_tick_function', 'call_user_method_array',
         'call_user_func_array', 'call_user_method', 'call_user_func',
         'fsockopen', 'chroot', 'chdir', 'chgrp', 'chmod', 'copy', 'delete', 'fopen', 'file', 'rmdir', 'unlink',
         'file_get_contents', 'fpassthru', 'mkdir', 'move_uploaded_file', 'popen', 'readfile', 'rename',
         'imagepng', 'imagejpeg', 'imagegif', 'imagebmp', 'imagewebp',
         'mail', 'header',
         'cms_parse_ini_file_fast', 'deldir_contents',
         'include', 'include_once', 'require', 'require_once',
         'escapeshellarg', 'escapeshellcmd', 'exec', 'passthru', 'proc_open', 'shell_exec', 'system',
         'DatabaseConnector.query', 'DatabaseConnector._query', 'DatabaseConnector.query_value_if_there',
     );
}

/*
Demonstration of all the assignment checks we could make:

$foo += 'a';

$bar = 1;
$bar[] = 'a';

list($a) = 1;

$b = 1;
$b[3] = 'a';
*/
function check_assignment($c, $c_pos, $function_guard = '')
{
    global $LOCAL_VARIABLES;
    $GLOBALS['MADE_CALL'] = null;
    $e_type = check_expression($c[3], true, false, $function_guard);
    $made_call = $GLOBALS['MADE_CALL'];
    $GLOBALS['MADE_CALL'] = null;
    $op = $c[1];
    $target = $c[2];

    // Special assignment operational checks
    if (in_array($op, array('CONCAT_EQUAL'))) {
        $passes = ensure_type(array('string'), $e_type, $c_pos, 'Can only concatenate onto and with strings (not ' . $e_type . ')');
        if ($passes) {
            infer_expression_type_to_variable_type('string', $c[3]);
        }
        if ($c[3][0] == 'VARIABLE_REFERENCE') {
            log_warning('Cannot append a reference', $c_pos);
        }
        if ($target[0] == 'VARIABLE') {
            $v_type = get_variable_type($target);
            ensure_type(array('string'), $v_type, $c_pos, 'Can only concatenate onto and with strings (not ' . $v_type . ')');
        }
    }
    if (in_array($op, array('PLUS_EQUAL'))) {
        ensure_type(array('array', 'integer', 'float'), $e_type, $c_pos, 'Can only perform addition to arrays or numbers (not ' . $e_type . ')');
        if ($target[0] == 'VARIABLE') {
            $v_type = get_variable_type($target);
            ensure_type(array('array', 'integer', 'float'), $v_type, $c_pos, 'Can only perform addition to arrays or numbers (not ' . $v_type . ')');
        }
    }
    if (in_array($op, array('DIV_EQUAL', 'MUL_EQUAL', 'MINUS_EQUAL'))) {
        ensure_type(array('integer', 'float'), $e_type, $c_pos, 'Can only perform relative arithmetic with numbers (not ' . $e_type . ')');
        if ($target[0] == 'VARIABLE') {
            $v_type = get_variable_type($target);
            ensure_type(array('integer', 'float'), $v_type, $c_pos, 'Can only perform relative arithmetic with numbers (not ' . $v_type . ')');
        }
    }

    // Special assignment target checks
    if ($target[0] == 'LIST') {
        $passes = ensure_type(array('array'), $e_type, $c_pos, 'Can only list from an array (not ' . $e_type . ')');
        if ($passes) {
            infer_expression_type_to_variable_type('array', $c[3]);
        }
        foreach ($target[1] as $var) {
            if (count($var[2]) == 0) {
                add_variable_reference($var[1], $c_pos, false);
            }
        }
        return 'array';
    }
    if ($target[0] == 'ARRAY_APPEND') {
        if (empty($target[2][2])) { // Simple variable, meaning we can test to see if it's an array
            $v_type = check_variable($target[1], true, $function_guard);
            $passes = ensure_type(array('array'), $v_type, $c_pos, 'Can only append to an array (not ' . $v_type . ')');
            if ($passes) {
                infer_expression_type_to_variable_type('array', $c[3]);
            }
        }
        return 'array';
    }

    // check_variable will do the internalised checks. Type conflict checks will be done at the end of the function, based on all the types the variable has been set with. Variable type usage checks are done inside expressions.
    if ($target[0] == 'VARIABLE') {
        if (($op == 'EQUAL') && (count($target[2]) == 0)) {
            add_variable_reference($target[1], $c_pos, false);
            $v = $LOCAL_VARIABLES[$target[1]];
            if (($made_call !== null) && (((!$v['conditioned_null']) && (isset($GLOBALS['NULL_ERROR_FUNCS'][$made_call]))) || ((!$v['conditioned_false']) && (isset($GLOBALS['FALSE_ERROR_FUNCS'][$made_call]))))) {
                $LOCAL_VARIABLES[$target[1]]['conditioner'][] = $made_call;
            }
            if ($e_type == '*MIXED*') {
                global $LOCAL_VARIABLES;
                $LOCAL_VARIABLES[$c[2][1]]['mixed_tag'] = true;
                $e_type = '?mixed';
            }
            /*elseif (($e_type == 'boolean-false') && ($c[3][0] == 'LITERAL')) { No, it'll give a mixed type error
                global $LOCAL_VARIABLES;
                $LOCAL_VARIABLES[$c[2][1]]['types'][] = 'boolean';
            }*/
            set_composr_type($target[1], $e_type);
        } else {
            if (($made_call !== null) && (((isset($GLOBALS['NULL_ERROR_FUNCS'][$made_call]))) || ((isset($GLOBALS['FALSE_ERROR_FUNCS'][$made_call]))))) {
                if (isset($GLOBALS['PEDANTIC'])) {
                    log_warning('Result probably wasn\'t error checked', $c_pos);
                }
            }
        }
        $type = check_variable($target, false, $function_guard);
        return $type;
    }

    // Should never get here
    return 'mixed';
}

function check_expression($e, $assignment = false, $equate_false = false, $function_guard = '')
{
    $c_pos = $e[count($e) - 1];
    if ($e[0] == 'VARIABLE_REFERENCE') {
        $e = $e[1];
    }
    if ($e[0] == 'SOLO') {
        $type = check_expression($e[1], false, false, $function_guard);
        return $type;
    }
    if ($e[0] == 'EXPRESSION_CHAINING') {
        return check_variable($e, false, $function_guard);
    }
    if ((in_array($e[0], array('DIVIDE', 'REMAINDER', 'DIV_EQUAL'))) && ($e[2][0] != 'LITERAL')) {
        if (($assignment) && (@count($e[2][1][2]) == 0)) {
            $GLOBALS['LOCAL_VARIABLES'][$e[2][1][1]]['conditioner'][] = '_divide_';
        } elseif (isset($GLOBALS['PEDANTIC'])) {
            log_warning('Divide by zero un-handled', $c_pos);
        }
    }
    if ($e[0] == 'TERNARY_IF') {
        if (($e[1][0] == 'CALL_DIRECT') && ($e[1][1] == 'php_function_allowed' || strpos($e[1][1], '_exists') !== false/*function_exists or method_exists or class_exists*/) && ($e[1][2][0][0][0] == 'LITERAL') && ($e[1][2][0][0][1][0] == 'STRING')) {
            $function_guard .= ',' . $e[1][2][0][0][1][1] . ',';
        }
        $passes = ensure_type(array('boolean'), check_expression($e[1], false, false, $function_guard), $c_pos, 'Conditionals must be boolean (ternary)');
        if ($passes) {
            infer_expression_type_to_variable_type('boolean', $e[1]);
        }
        $type_a = check_expression($e[2][0], false, false, $function_guard);
        $type_b = check_expression($e[2][1], false, false, $function_guard);
        if (($type_a != 'null') && ($type_b != 'null')) {
            $passes = ensure_type(array($type_a, 'mixed'/*imperfect, but useful for performance*/), $type_b, $c_pos, 'Type symettry error in ternary operator');
            if ($passes) {
                infer_expression_type_to_variable_type($type_a, $e[2][1]);
            }
        }

        // Common most egregious cases of horribly ugly code
        if (in_array($e[1][0], array('TERNARY_IF', 'IS_EQUAL', 'IS_NOT_EQUAL', 'IS_IDENTICAL', 'IS_NOT_IDENTICAL', 'IS_SMALLER', 'IS_SMALLER_OR_EQUAL', 'IS_GREATER', 'IS_GREATER_OR_EQUAL', 'INSTANCEOF', 'BOOLEAN_XOR', 'BOOLEAN_OR', 'BOOLEAN_AND'))) {
            log_warning('Raw complex expression in ternary syntax boolean controller should be parenthesised, for clarity', $c_pos);
        }
        $bad_ops = array('TERNARY_IF', 'BOOLEAN_XOR', 'BOOLEAN_OR', 'BOOLEAN_AND', 'REFERENCE', 'BW_OR', 'BW_XOR', 'BW_AND', 'IS_EQUAL', 'IS_NOT_EQUAL', 'IS_IDENTICAL', 'INSTANCEOF', 'IS_NOT_IDENTICAL', 'IS_SMALLER', 'IS_SMALLER_OR_EQUAL', 'IS_GREATER', 'IS_GREATER_OR_EQUAL', 'SL', 'SR', 'ADD', 'SUBTRACT', 'CONC', 'MULTIPLY', 'DIVIDE', 'REMAINDER', 'EXPONENTIATION');
        if ((in_array($e[2][0][0], $bad_ops)) || (in_array($e[2][1][0], $bad_ops))) {
            log_warning('Raw complex expression in ternary syntax should be parenthesised, for clarity', $c_pos);
        }

        return $type_a;
    }
    if (in_array($e[0], array('BOOLEAN_AND', 'BOOLEAN_OR', 'BOOLEAN_XOR'))) {
        foreach (array(0, 1) as $function_parameter_pos) {
            foreach (array(0, 1) as $and_position) {
                if (($e[0] == 'BOOLEAN_AND') && ($e[1][0] == 'PARENTHESISED') && ($e[1][$and_position + 1][0] == 'CALL_DIRECT') && ($e[1][$and_position + 1][1] == 'php_function_allowed' || strpos($e[1][$and_position + 1][1], '_exists') !== false) && (isset($e[1][$and_position + 1][2][$function_parameter_pos])) && ($e[1][$and_position + 1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($e[1][$and_position + 1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                    $function_guard .= ',' . $e[1][1][2][$function_parameter_pos][0][1][1] . ',';
                }
                if (($e[0] == 'BOOLEAN_AND') && ($e[2][0] == 'BOOLEAN_AND') && ($e[2][1][0] == 'PARENTHESISED') && ($e[2][1][$and_position + 1][0] == 'CALL_DIRECT') && ($e[2][1][$and_position + 1][1] == 'php_function_allowed' || strpos($e[2][1][$and_position + 1][1], '_exists') !== false) && (isset($e[2][1][$and_position + 1][2][$function_parameter_pos][0])) && ($e[2][1][$and_position + 1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($e[2][1][$and_position + 1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                    $function_guard .= ',' . $e[2][1][1][2][$function_parameter_pos][0][1][1] . ',';
                }
            }
        }
        $passes = ensure_type(array('boolean'), check_expression($e[1], false, false, $function_guard), $c_pos - 1, 'Can only use boolean combinators with booleans');
        if ($passes) {
            infer_expression_type_to_variable_type('boolean', $e[1]);
        }
        $passes = ensure_type(array('boolean'), check_expression($e[2], false, false, $function_guard), $c_pos, 'Can only use boolean combinators with booleans');
        if ($passes) {
            infer_expression_type_to_variable_type('boolean', $e[2]);
        }
        return 'boolean';
    }
    if (in_array($e[0], array('SL', 'SR', 'REMAINDER'))) {
        $passes = ensure_type(array('integer'), check_expression($e[1], false, false, $function_guard), $c_pos - 1, 'Can only use integer combinators with integers');
        if ($passes) {
            infer_expression_type_to_variable_type('integer', $e[1]);
        }
        $passes = ensure_type(array('integer'), check_expression($e[2], false, false, $function_guard), $c_pos, 'Can only use integer combinators with integers');
        if ($passes) {
            infer_expression_type_to_variable_type('integer', $e[2]);
        }
        return 'integer';
    }
    if (in_array($e[0], array('CONC'))) {
        $type_a = check_expression($e[1], false, false, $function_guard);
        $type_b = check_expression($e[2], false, false, $function_guard);
        $passes = ensure_type(array('string'), $type_a, $c_pos - 1, 'Can only use string combinators with strings (1) (not ' . $type_a . ')');
        if ($passes) {
            infer_expression_type_to_variable_type('string', $e[1]);
        }
        $passes = ensure_type(array('string'), $type_b, $c_pos, 'Can only use string combinators with strings (2) (not ' . $type_b . ')');
        if ($passes) {
            infer_expression_type_to_variable_type('string', $e[2]);
        }
        return 'string';
    }
    if (in_array($e[0], array('SUBTRACT', 'MULTIPLY', 'DIVIDE', 'EXPONENTIATION'))) {
        $type_a = check_expression($e[1], false, false, $function_guard);
        $t = check_expression($e[2], false, false, $function_guard);
        ensure_type(array('integer', 'float'), $type_a, $c_pos - 1, 'Can only use arithmetical combinators with numbers (1) (not ' . $type_a . ')');
        ensure_type(array('integer', 'float'), $t, $c_pos, 'Can only use arithmetical combinators with numbers (2) (not ' . $t . ')');
        return ($e[0] == 'DIVIDE') ? 'float' : $type_a;
    }
    if (in_array($e[0], array('ADD'))) {
        $type_a = check_expression($e[1], false, false, $function_guard);
        $t = check_expression($e[2], false, false, $function_guard);
        ensure_type(array('integer', 'float', 'array'), $type_a, $c_pos - 1, 'Can only use + combinator with numbers/arrays (1) (not ' . $type_a . ')');
        ensure_type(array('integer', 'float', 'array'), $t, $c_pos, 'Can only use + combinator with numbers/arrays (2) (not ' . $t . ')');
        return $type_a;
    }
    if (in_array($e[0], array('IS_GREATER_OR_EQUAL', 'IS_SMALLER_OR_EQUAL', 'IS_GREATER', 'IS_SMALLER'))) {
        $type_a = check_expression($e[1], false, false, $function_guard);
        $type_b = check_expression($e[2], false, false, $function_guard);
        ensure_type(array('integer', 'float', 'string'), $type_a, $c_pos - 1, 'Can only use arithmetical comparators with numbers or strings');
        ensure_type(array('integer', 'float', 'string'), $type_b, $c_pos, 'Can only use arithmetical comparators with numbers or strings');
        ensure_type(array($type_a), $type_b, $c_pos, 'Comparators must have type symmetric operands (' . $type_a . ' vs ' . $type_b . ')');
        return 'boolean';
    }
    if (in_array($e[0], array('IS_EQUAL', 'IS_IDENTICAL', 'IS_NOT_IDENTICAL', 'IS_NOT_EQUAL'))) {
        $type_a = check_expression($e[1], false, (in_array($e[0], array('IS_IDENTICAL', 'IS_NOT_IDENTICAL'))) && ($e[2][0] == 'LITERAL') && ($e[2][1][0] == 'BOOLEAN') && (!$e[2][1][1]), $function_guard);
        $type_b = check_expression($e[2], false, false, $function_guard);
        $x = $e;
        if ($x[1][0] == 'EMBEDDED_ASSIGNMENT') {
            $x = $e[1];
        }
        if (($x[1][0] == 'VARIABLE') && (@count($x[1][1][2]) == 0) && ($e[2][0] == 'LITERAL')) {
            if (in_array($e[0], array('IS_IDENTICAL', 'IS_NOT_IDENTICAL'))) {
                if (($e[2][1][0] == 'BOOLEAN') && (!$e[2][1][1])) {
                    $GLOBALS['LOCAL_VARIABLES'][$x[1][1][1]]['conditioned_false'] = true;
                } elseif ($e[2][1][0] == 'null') {
                    $GLOBALS['LOCAL_VARIABLES'][$x[1][1][1]]['conditioned_null'] = true;
                }
            }
            if (($e[2][1][0] == 'INTEGER') && ($e[2][1][1] == 0)) {
                $GLOBALS['LOCAL_VARIABLES'][$x[1][1][1]]['conditioned_zero'] = true;
            }
        }
        if (($e[0] == 'IS_EQUAL') && ($e[2][0] == 'LITERAL') && ($e[2][1][0] == 'BOOLEAN')) {
            log_warning('It\'s redundant to equate to truths', $c_pos);
        }
        if (strpos($e[0], 'IDENTICAL') === false) {
            if ($type_b == 'null') {
                log_warning('Comparing to null is considered bad', $c_pos);
            }
            $passes = ensure_type(array($type_a), $type_b, $c_pos, 'Comparators must have type symmetric operands (' . $type_a . ' vs ' . $type_b . ')');
            if ($passes) {
                infer_expression_type_to_variable_type($type_a, $e[2]);
            }
        }
        return 'boolean';
    }

    $inner = $e;
    switch ($inner[0]) {
        case 'CLOSURE':
            global $LOCAL_VARIABLES;
            $temp = $LOCAL_VARIABLES;
            $ret = check_function($inner[1] + array('name' => '(closure)'), true);
            $LOCAL_VARIABLES = $temp;
            return $ret;
        case 'EMBEDDED_ASSIGNMENT':
            $ret = check_assignment($inner, $c_pos, $function_guard);
            return $ret;
        case 'CALL_METHOD':
            $ret = check_method($inner, $c_pos, $function_guard);
            if ($ret === null) {
                log_warning('Method that returns no value used in an expression', $c_pos);
                return 'mixed';
            }
            return $ret;
        case 'CALL_INDIRECT':
            add_variable_reference($inner[1][1], $c_pos);
            return 'mixed';
        case 'CALL_DIRECT':
            $ret = check_call($inner, $c_pos, null, $function_guard);
            if ($ret === null) {
                log_warning('Function (\'' . $inner[1] . '\') that returns no value used in an expression', $c_pos);
                return 'mixed';
            }
            if ($inner[1] == 'mixed') {
                return '*MIXED*';
            }
            if ($assignment) {
                $GLOBALS['MADE_CALL'] = $inner[1];
                if ((@$e[2][0][0] == 'VARIABLE') && (@count($e[2][0][1][2]) == 0) && ($e[1] == 'is_null')) {
                    $GLOBALS['LOCAL_VARIABLES'][$e[2][0][1][1]]['conditioned_null'] = true;
                }
            } else {
                if (isset($GLOBALS['PEDANTIC'])) {
                    if (isset($GLOBALS['NULL_ERROR_FUNCS'][$inner[1]])) {
                        log_warning('Crucial error value un-handled', $c_pos);
                    }
                    if ((isset($GLOBALS['FALSE_ERROR_FUNCS'][$inner[1]])) && (!$equate_false)) {
                        log_warning('Crucial error value un-handled', $c_pos);
                    }
                }
            }
            return $ret;
        case 'CASTED':
            check_expression($inner[2], false, false, $function_guard);
            return strtolower($inner[1]);
        case 'PARENTHESISED':
            return check_expression($inner[1], false, false, $function_guard);
        case 'BOOLEAN_NOT':
            $passes = ensure_type(array('boolean'), check_expression($inner[1], false, false, $function_guard), $c_pos, 'Can only \'NOT\' a boolean', true);
            if ($passes) {
                infer_expression_type_to_variable_type('boolean', $inner[1]);
            }
            return 'boolean';
        case 'BW_NOT':
            $passes = ensure_type(array('integer'), check_expression($inner[1], false, false, $function_guard), $c_pos, 'Can only \'BITWISE-NOT\' an integer', true);
            if ($passes) {
                infer_expression_type_to_variable_type('integer', $inner[1]);
            }
            return 'integer';
        case 'BW_OR':
        case 'BW_XOR':
        case 'BW_AND':
            $exp_1 = check_expression($inner[1], false, false, $function_guard);
            $exp_2 = check_expression($inner[2], false, false, $function_guard);

            $string_mode = ensure_type(array('string'), $exp_1, $c_pos, '', true, true) || ensure_type(array('string'), $exp_2, $c_pos, '', true, true);
            $integer_mode = ensure_type(array('integer'), $exp_1, $c_pos, '', true, true) || ensure_type(array('integer'), $exp_2, $c_pos, '', true, true);
            $arg_type = ($string_mode ? 'string' : 'integer');

            $passes = ensure_type(array($arg_type), $exp_1, $c_pos, 'Can only bitwise 2 integers or 2 1-byte strings (got ' . $exp_1 . ')', true);

            if (($passes) && ($string_mode || $integer_mode)) {
                infer_expression_type_to_variable_type($arg_type, $inner[1]);
                infer_expression_type_to_variable_type($arg_type, $inner[2]);
            }

            return $arg_type;
        case 'NEGATE':
            $type = check_expression($inner[1], false, false, $function_guard);
            ensure_type(array('integer', 'float'), $type, $c_pos, 'Can only negate a number');
            return $type;
        case 'LITERAL':
            $type = check_literal($inner[1]);
            return $type;
        case 'NEW_OBJECT':
            $class = preg_replace('#^.*\\\\#', '', $inner[1]); // We strip out namespaces from the name, and just look at the actual class name
            global $FUNCTION_SIGNATURES;
            if ((!isset($FUNCTION_SIGNATURES[$class])) && ($FUNCTION_SIGNATURES != array()) && (strpos($function_guard, ',' . $class . ',') === false)) {
                if ((($GLOBALS['OK_EXTRA_FUNCTIONS'] === null) || (preg_match('#^' . $GLOBALS['OK_EXTRA_FUNCTIONS'] . '#', $class) == 0))) {
                    if (!isset($GLOBALS['KNOWN_EXTRA_CLASSES'][$class]) && $class != '') {
                        if ($class !== null) {
                            log_warning('Could not find class, ' . $class, $c_pos);
                        }
                    }
                }
            }
            foreach ($inner[2] as $param) {
                check_expression($param, false, false, $function_guard);
            }
            if (count($inner[2]) != 0) {
                check_call(array('CALL_METHOD', '__construct', $inner[2]), $c_pos, $inner[1], $function_guard);
            }
            return 'object-' . $inner[1];
        case 'CLONE_OBJECT':
            // $a = clone $b will make a shallow copy of the object $, so we just
            // return $b's type
            return check_expression($inner[1], false, false, '');
        case 'CREATE_ARRAY':
            foreach ($inner[1] as $param) {
                check_expression($param[0], false, false, $function_guard);
                if (isset($param[1])) {
                    check_expression($param[1], false, false, $function_guard);
                }
            }
            return 'array';
        case 'ASSIGNMENT':
            check_assignment($inner, $c_pos, $function_guard);
            break;
        case 'PRE_DEC':
            ensure_type(array('integer', 'float'), check_variable($inner[1], false, $function_guard), $c_pos, 'Can only decrement numbers');
            break;
        case 'PRE_INC':
            ensure_type(array('integer', 'float'), check_variable($inner[1], false, $function_guard), $c_pos, 'Can only increment numbers');
            break;
        case 'DEC':
            ensure_type(array('integer', 'float'), check_variable($inner[1], false, $function_guard), $c_pos, 'Can only decrement numbers');
            break;
        case 'INC':
            ensure_type(array('integer', 'float'), check_variable($inner[1], false, $function_guard), $c_pos, 'Can only increment numbers');
            break;
        case 'VARIABLE':
            return check_variable($inner, true, $function_guard);
    }
    return 'mixed';
}

function check_variable($variable, $reference = false, $function_guard = '')
{
    $identifier = $variable[1];

    if ($identifier === null) {
        return null;
    }

    if (!is_array($identifier)) {
        global $LOCAL_VARIABLES;
        if ((!isset($LOCAL_VARIABLES[$identifier])) && !((is_array($identifier) && (in_array($identifier[0], array('CALL_METHOD')))))) {
            // We skip this check if the "variable" is coming from a function/method
            // (in which case we have a function/method call rather than a variable)
            log_warning('Variable \'' . $identifier . '\' referenced before initialised', $variable[3]);
        }

        // Add to reference count if: this specifically is a reference, or it's complex therefore the base is explicitly a reference, or we are forced to add it because it is yet unseen
        if (($reference) || (count($variable[2]) != 0) || (!isset($LOCAL_VARIABLES[$identifier]))) {
            add_variable_reference($identifier, $variable[count($variable) - 1], ($reference) || (count($variable[2]) != 0));
        }

        $variable_stem = $variable;
        $variable_stem[2] = array();
        $type = get_variable_type($variable_stem);
    } else {
        $type = check_expression($variable[1]);
    }

    $next = $variable[2];
    while ($next != array()) { // Complex: we must perform checks to make sure the base is of the correct type for the complexity to be valid. We must also note any deep variable references used in array index / string extract expressions
        /*if ($next[0] == 'CHAR_OF_STRING') {    Deprecated syntax
            check_expression($next[1]);
            $passes = ensure_type(array('string'), check_variable(array('VARIABLE', $identifier, array())), $variable[3], 'Variable \'' . $identifier . '\' must be a string due to dereferencing');
            if ($passes) {
                infer_expression_type_to_variable_type('string', $next[1]);
            }
            return 'string';
        }*/

        if ($next[0] == 'ARRAY_AT') {
            if (($identifier == 'GLOBALS') && ($next[1][0] == 'SOLO') && ($next[1][1][0] == 'LITERAL')) {
                $gid = $next[1][1][1][1];
                add_variable_reference($gid, $variable[count($variable) - 1]);
                $LOCAL_VARIABLES[$gid]['is_global'] = true;
            }
            check_expression($next[1]);
            $passes = ensure_type(array('array', 'string'), $type, $variable[3], 'Variable must be an array/string due to dereferencing');
            if (ensure_type(array('string'), $type, $variable[3], '', true, true) == 'string') {
                $type_2 = check_expression($variable[2][1]);
                $passes = ensure_type(array('integer'), $type_2, $variable[3], 'String character extraction must use an integer');
            }
            //if ($passes) infer_expression_type_to_variable_type('array', $next[1]);
            $type = 'mixed'; // We don't know the array data types

            $next = $next[2];
        } elseif ($next[0] == 'DEREFERENCE') {
            // Special rule for 'this->db'
            if (($variable[1] == 'this') && ($variable[2][1][1] == 'db') && ((!isset($variable[2][2][0])) || ($variable[2][2][0] != 'DEREFERENCE'))) {
                $type = 'DatabaseConnector';
            }

            // Special rule for $GLOBALS['?_DB']
            if (($variable[1] == 'GLOBALS') && ($variable[2][1][1][0] == 'STRING') && (substr($variable[2][1][1][1], -3) == '_DB') && ((!isset($variable[2][2][2][0])) || ($variable[2][2][2][0] != 'DEREFERENCE'))) {
                $type = 'DatabaseConnector';
            }

            // Special rule for $GLOBALS['FORUM_DRIVER']
            if (($variable[1] == 'GLOBALS') && ($variable[2][1][1][0] == 'STRING') && ($variable[2][1][1][1] == 'FORUM_DRIVER')) {
                $type = 'Forum_driver_base';
            }

            ensure_type(array('object', 'resource'), $type, $variable[3], 'Variable must be an object due to dereferencing');
            if (($next[2] != array()) && ($next[2][0] == 'CALL_METHOD')) {
                $type = actual_check_method($type/*class*/, $next[1][1]/*method*/, $next[2][2]/*params*/, $next[3]/*line number*/, $function_guard);
                $next = $next[2][5];
            } else {
                $type = 'mixed';
                $next = $next[2];
            }
        } else {
            $next = array();
        }
    }

    return $type;
}

function scan_extractive_expressions($variable)
{
    if (!is_array($variable)) {
        return;
    }
    if ($variable == array()) {
        return;
    }

    if (($variable[0] == 'ARRAY_AT') || ($variable[0] == 'CHAR_OF_STRING')) {
        check_expression($variable[1]);
    }

    if ((($variable[0] == 'ARRAY_AT') || ($variable[0] == 'DEREFERENCE')) && (count($variable[2]) != 0)) {
        scan_extractive_expressions($variable[2]);
    }
}

function get_variable_type($variable)
{
    global $LOCAL_VARIABLES;

    $identifier = $variable[1];

    if (count($variable[2]) != 0) {
        return 'mixed'; // Too complex
    }

    if (!isset($LOCAL_VARIABLES[$identifier])) {
        return 'mixed';
    }

    if (count($LOCAL_VARIABLES[$identifier]['types']) == 0) {
        return 'mixed'; // There is a problem, but it will be identified elsewhere.
    }

    $temp = array_unique(array_values(array_diff($LOCAL_VARIABLES[$identifier]['types'], array('null'))));
    if ($temp == array('boolean-false', 'boolean')) {
        return 'boolean';
    }
    if (count($temp) != 0) {
        return is_array($temp[0]) ? $temp[0][0] : $temp[0]; // We'll assume the first set type is the actual type
    }
    return 'mixed';
}

function check_literal($literal)
{
    if ($literal[0] == 'NEGATE') {
        $type = check_literal($literal[1]);
        ensure_type(array('integer', 'float'), $type, $literal[count($literal) - 1], 'Can only negate a number');
        return $type;
    }
    if ($literal[0] == 'INTEGER') {
        return 'integer';
    }
    if ($literal[0] == 'FLOAT') {
        return 'float';
    }
    if ($literal[0] == 'STRING') {
        return 'string';
    }
    if ($literal[0] == 'BOOLEAN') {
        if (!$literal[1]) {
            return 'boolean-false';
        }
        return 'boolean';
    }
    if ($literal[0] == 'null') {
        return 'null';
    }
    return 'mixed';
}

function set_composr_type($identifier, $type)
{
    if (is_array($type)) {
        $type = $type[0];
    }

    global $LOCAL_VARIABLES;
    $LOCAL_VARIABLES[$identifier]['types'][] = $type;
    if (substr($type, 0, 7) == 'object-') {
        $LOCAL_VARIABLES[$identifier]['object_type'] = substr($type, 7);
    }
    if (($type == 'mixed') || ($type == '?mixed')) {
        $LOCAL_VARIABLES[$identifier]['mixed_tag'] = true;
    }

    return true;
}

function add_variable_reference($identifier, $first_mention, $reference = true)
{
    $unused_value = !$reference; // May have some problems with loops - as we may use the value in a prior command

    global $LOCAL_VARIABLES;
    if (!isset($LOCAL_VARIABLES[$identifier])) {
        $LOCAL_VARIABLES[$identifier] = array('is_global' => false, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array(), 'references' => $reference ? 1 : 0, 'object_type' => '', 'unused_value' => $unused_value, 'first_mention' => $first_mention, 'mixed_tag' => false);
    } else {
        if ($reference) {
            $LOCAL_VARIABLES[$identifier]['references']++;
        }
        $LOCAL_VARIABLES[$identifier]['unused_value'] = $unused_value;
    }
}

function reinitialise_local_variables($inside_class = false)
{
    $ret = array(
        'http_response_header' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
        '_GET' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
        '_POST' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
        '_REQUEST' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
        '_COOKIE' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
        '_SERVER' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
        '_ENV' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
        '_SESSION' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
        '_FILES' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
        'GLOBALS' => array('is_global' => true, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('array'), 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false),
    );
    if ($inside_class) {
        global $CURRENT_CLASS;
        $ret['this'] = array('is_global' => false, 'conditioner' => array(), 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => array('object'), 'references' => 0, 'object_type' => $CURRENT_CLASS, 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false);
    }
    return $ret;
}

// If the given expression is a direct variable expression, this function will infer the type as the given type. This therefore allows type infering on usage as well as on assignment
function infer_expression_type_to_variable_type($type, $expression)
{
    /*if (($expression[0] == 'VARIABLE') && (count($expression[1][2]) == 0)) {      Not reliable
        $identifier = $expression[1][1];
        set_composr_type($identifier, $type);
    }*/
}

function ensure_type($_allowed_types, $actual_type, $pos, $alt_error = null, $extra_strict = false, $mixed_as_fail = false)
{
    if (is_array($actual_type)) {
        $actual_type = $actual_type[0];
    }

    if ((ltrim($actual_type, '~?') == 'mixed') && (!$mixed_as_fail)) {
        return true; // We can't check it
    }

    // Tidy up our allow list to be a nice map
    if ((!$extra_strict) && ((in_array('boolean', $_allowed_types)) || (in_array('?boolean', $_allowed_types)))) {
        $_allowed_types[] = 'boolean-false';
    }
    if (($extra_strict) && ($_allowed_types == array('boolean'))) {
        $_allowed_types[] = 'boolean-false';
    }
    $allowed_types = array();
    foreach ($_allowed_types as $type) {
        if (is_array($type)) {
            $type = $type[0];
        }

        if ((ltrim($type, '~?') == 'mixed') || (ltrim($type, '~?') == 'resource')) {
            return true; // Anything works!
        }
        if (strpos($type, '?') !== false) {
            $type = str_replace('?', '', $type);
            $allowed_types['null'] = true;
        }
        if (strpos($type, '~') !== false) {
            $type = str_replace('~', '', $type);
            $allowed_types['boolean-false'] = true;
        }
        if (substr($type, 0, 6) == 'object') {
            $type = 'object';
        }
        if ($type == 'REAL') {
            $allowed_types['float'] = true;
        }
        if (in_array($type, array('AUTO', 'INTEGER', 'UINTEGER', 'SHORT_TRANS', 'LONG_TRANS', 'SHORT_TRANS__COMCODE', 'LONG_TRANS__COMCODE', 'MEMBER', 'MEMBER', 'SHORT_INTEGER', 'AUTO_LINK', 'BINARY', 'GROUP', 'TIME'))) {
            $allowed_types['integer'] = true;
        }
        if (in_array($type, array('LONG_TEXT', 'SHORT_TEXT', 'MINIID_TEXT', 'ID_TEXT', 'LANGUAGE_NAME', 'URLPATH', 'PATH', 'IP', 'EMAIL'))) {
            $allowed_types['string'] = true;
        }
        if (in_array($type, array('Tempcode'))) {
            $allowed_types['object'] = true;
        }
        if (in_array($type, array('list', 'map'))) {
            $allowed_types['array'] = true;
        }
        $allowed_types[$type] = true;
    }

    // Special cases for our actual type
    if (strpos($actual_type, '?') !== false) {
        //if (isset($allowed_types['null'])) return true;    We can afford not to give this liberty due to is_null
        $actual_type = str_replace('?', '', $actual_type);
    }
    if (strpos($actual_type, '~') !== false) {
        if (isset($allowed_types['boolean-false'])) {
            return true;
        }
        $actual_type = str_replace('~', '', $actual_type);
    }
    if (substr($actual_type, 0, 6) == 'object') {
        $actual_type = 'object';
    }

    // The check
    if (isset($allowed_types[$actual_type])) {
        return true;
    }
    if ($actual_type == 'REAL') {
        if (isset($allowed_types['float'])) {
            return true;
        }
    }
    if (in_array($actual_type, array('AUTO', 'INTEGER', 'UINTEGER', 'SHORT_TRANS', 'LONG_TRANS', 'SHORT_TRANS__COMCODE', 'LONG_TRANS__COMCODE', 'MEMBER', 'MEMBER', 'SHORT_INTEGER', 'AUTO_LINK', 'BINARY', 'GROUP', 'TIME'))) {
        if (isset($allowed_types['integer'])) {
            return true;
        }
    }
    if (in_array($actual_type, array('LONG_TEXT', 'SHORT_TEXT', 'MINIID_TEXT', 'ID_TEXT', 'LANGUAGE_NAME', 'URLPATH', 'PATH', 'IP', 'EMAIL'))) {
        if (isset($allowed_types['string'])) {
            return true;
        }
    }
    if (in_array($actual_type, array('Tempcode'))) {
        if (isset($allowed_types['object'])) {
            return true;
        }
    }
    if (in_array($actual_type, array('list', 'map'))) {
        if (isset($allowed_types['array'])) {
            return true;
        }
    }

    if ($alt_error != '') {
        log_warning(($alt_error === null) ? 'Type mismatch' : $alt_error, $pos);
    }

    return false;
}

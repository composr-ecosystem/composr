<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

function load_table_fields()
{
    global $TABLE_FIELDS, $COMPOSR_PATH;
    if (file_exists($COMPOSR_PATH . '/data/db_meta.bin') && (filemtime($COMPOSR_PATH . '/index.php') < filemtime($COMPOSR_PATH . '/data/db_meta.bin'))) {
        $_table_fields = unserialize(file_get_contents($COMPOSR_PATH . '/data/db_meta.bin'));
        $TABLE_FIELDS = $_table_fields['tables'];
    } else {
        $TABLE_FIELDS = [];
    }
    $TABLE_FIELDS['db_meta'] = [
        'addon' => 'core',
        'fields' => [
            'm_table' => '*ID_TEXT',
            'm_name' => '*ID_TEXT',
            'm_type' => 'ID_TEXT',
        ]
    ];
    $TABLE_FIELDS['db_meta_indices'] = [
        'addon' => 'core',
        'fields' => [
            'i_table' => '*ID_TEXT',
            'i_name' => '*ID_TEXT',
            'i_fields' => '*ID_TEXT',
        ]
    ];
}

function load_function_signatures()
{
    global $FUNCTION_SIGNATURES, $COMPOSR_PATH;
    $FUNCTION_SIGNATURES = [];

    if (!empty($GLOBALS['FLAG__API'])) {
        // Load up function info
        $functions_file_path = $COMPOSR_PATH . '/data_custom/functions.bin';
        if (!is_file($functions_file_path)) {
            $functions_file_path = '../../data_custom/functions.bin';
        }
        if (!is_file($functions_file_path)) {
            $functions_file_path = 'functions.bin';
        }
        $functions_file = file_get_contents($functions_file_path);
        $FUNCTION_SIGNATURES = unserialize($functions_file);
    }
}

function load_php_metadetails()
{
    global $KNOWN_EXTRA_FUNCTIONS;
    $KNOWN_EXTRA_FUNCTIONS = [
        'critical_error' => true,
        'file_array_exists' => true,
        'file_array_get' => true,
        'file_array_count' => true,
        'file_array_get_at' => true,
        '__construct' => true,
    ];

    global $KNOWN_EXTRA_INTERFACES;
    $KNOWN_EXTRA_INTERFACES = [
        'Traversable' => true,
        'Iterator' => true,
        'IteratorAggregate' => true,
        'Throwable' => true,
        'ArrayAccess' => true,
        'Serializable' => true,
        'Countable' => true,
        'OuterIterator' => true,
        'RecursiveIterator' => true,
        'SeekableIterator' => true,
        'SplObserver' => true,
        'SplSubject' => true,
    ];

    global $KNOWN_EXTRA_CLASSES;
    $KNOWN_EXTRA_CLASSES = [
        'stdClass' => true,

        'Exception' => true,
        'ErrorException' => true,
        'Error' => true,
        'ParseError' => true,
        'TypeError' => true,
        'ArithmeticError' => true,
        'DivisionByZeroError' => true,
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
        'AssertionError' => true,

        'Closure' => true,
        'Generator' => true,
        //PHP7.4+ 'WeakReference' => true,
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
    ];

    // Special funcs (these may have been defined with stubs, but this says to mark them as requiring guards anyway)...
    global $EXT_FUNCS;
    $EXT_FUNCS = [
    ];

    // Error funcs...
    global $FALSE_ERROR_FUNCS;
    $FALSE_ERROR_FUNCS = [
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
    ];
    global $NULL_ERROR_FUNCS;
    $NULL_ERROR_FUNCS = [
        'array_pop' => true,
        'array_shift' => true,
    ];
    global $VAR_ERROR_FUNCS;
    $VAR_ERROR_FUNCS = [
        'strpos' => true,
        'strrpos' => true,
        'strstr' => true,
        'stristr' => true,
        'substr_count' => true,
    ];
    global $ERROR_FUNCS;
    $ERROR_FUNCS = [
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
    ];

    global $INSECURE_FUNCTIONS;
    $INSECURE_FUNCTIONS = [
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
     ];
}

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
This file contains compatibility code to be able to load parts of Composr, as well as some custom functions needed by the CQC.
*/

ini_set('memory_limit', '-1');
error_reporting(E_ALL);
if (function_exists('set_time_limit')) {
    @set_time_limit(1000);
}
global $COMPOSR_PATH;
$COMPOSR_PATH = dirname(dirname(__DIR__));

function parse_file($to_use, $verbose = false, $very_verbose = false, $i = null, $count = null)
{
    global $TOKENS, $TEXT, $FILENAME, $COMPOSR_PATH;
    $FILENAME = $to_use;

    if (($COMPOSR_PATH != '') && (substr($FILENAME, 0, strlen($COMPOSR_PATH)) == $COMPOSR_PATH)) {
        $FILENAME = substr($FILENAME, strlen($COMPOSR_PATH));
        if (substr($FILENAME, 0, 1) == DIRECTORY_SEPARATOR) {
            $FILENAME = substr($FILENAME, 1);
        }
        if (substr($FILENAME, 0, 1) == DIRECTORY_SEPARATOR) {
            $FILENAME = substr($FILENAME, 1);
        }
    }
    $TEXT = str_replace("\r", '', file_get_contents($to_use));

    if (substr($TEXT, 0, 4) == hex2bin('efbbbf')) { // Strip a utf-8 BOM
        $TEXT = substr($TEXT, 4);
    }

    if ($verbose) {
        echo '<hr /><p>DOING ' . $to_use . '</p>';
    }
    if ($verbose) {
        echo '<pre>';
    }
    if ($very_verbose) {
        echo '<b>Our code...</b>' . "\n";
    }
    if ($very_verbose) {
        echo htmlentities($TEXT);
    }
    if ($verbose) {
        echo "\n\n" . '<b>Starting lexing...</b>' . "\n";
    }
    $TOKENS = lex();
    if ($very_verbose) {
        print_r($TOKENS);
    }
    if ($very_verbose) {
        echo strval(count($TOKENS)) . ' tokens';
    }
    if ($verbose) {
        echo "\n\n" . '<b>Starting parsing...</b>' . "\n";
    }
    $structure = parse();
    if ($very_verbose) {
        print_r($structure);
    }
    if ($verbose) {
        echo '</pre>';
    }
    /*echo 'DONE ' . $FILENAME;
    if ($i !== null) {
        echo ' - ' . $i . ' of ' . $count;
    }
    echo cnl();*/

    return $structure;
}

function cnl()
{
    $cli = (php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR']));
    return $cli ? "\n" : '<br />';
}

function get_custom_file_base()
{
    global $COMPOSR_PATH;
    return $COMPOSR_PATH;
}

function get_file_base()
{
    global $COMPOSR_PATH;
    return $COMPOSR_PATH;
}

function unixify_line_format($in)
{
    $in = str_replace("\r\n", "\n", $in);
    return str_replace("\r", "\n", $in);
}

function object_factory($class)
{
    return new $class;
}

function find_all_hooks($type, $entry)
{
    $out = [];

    if (strpos($type, '..') !== false) {
        $type = filter_naughty($type);
    }
    if (strpos($entry, '..') !== false) {
        $entry = filter_naughty($entry);
    }
    $dir = get_file_base() . '/sources/hooks/' . $type . '/' . $entry;
    $dh = @scandir($dir);
    if ($dh !== false) {
        foreach ($dh as $file) {
            $basename = basename($file, '.php');
            if (($file[0] != '.') && ($file == $basename . '.php')/* && (preg_match('#^[\w\-]*$#', $basename) !=0 ) Let's trust - performance*/) {
                $out[$basename] = 'sources';
            }
        }
    }

    $dir = get_file_base() . '/sources_custom/hooks/' . $type . '/' . $entry;
    $dh = @scandir($dir);
    if ($dh !== false) {
        foreach ($dh as $file) {
            $basename = basename($file, '.php');
            if (($file[0] != '.') && ($file == $basename . '.php')/* && (preg_match('#^[\w\-]*$#', $basename) != 0) Let's trust - performance*/) {
                $out[$basename] = 'sources_custom';
            }
        }
    }

    return $out;
}

function get_charset()
{
    return 'utf-8';
}

function do_dir($dir, $enable_custom = true, $orig_priority = false, $avoid = [], $filter = [], $filter_avoid = [])
{
    global $COMPOSR_PATH;
    require_once($COMPOSR_PATH . '/sources/files.php');
    init__files();

    $out = [];
    $_dir = ($dir == '') ? '.' : $dir;
    $dh = opendir($_dir);
    if ($dh) {
        while (($file = readdir($dh)) !== false) {
            if (in_array($file, $avoid)) {
                continue;
            }

            foreach ($filter as $_filter) {
                if (preg_match('#^' . $_filter . '$#', $file) == 0) {
                    continue 2;
                }
            }
            foreach ($filter_avoid as $_filter_avoid) {
                if (preg_match('#^' . $_filter_avoid . '$#', $file) != 0) {
                    continue 2;
                }
            }

            $stripped_path = preg_replace('#^' . preg_quote($COMPOSR_PATH . '/', '#') . '#', '', $dir . '/') . $file;

            $bitmask = IGNORE_ACCESS_CONTROLLERS | IGNORE_HIDDEN_FILES | IGNORE_EDITFROM_FILES | IGNORE_REVISION_FILES | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_UPLOADS;
            if (!$enable_custom) {
                $bitmask = $bitmask | IGNORE_CUSTOM_ZONES | IGNORE_CUSTOM_DIRS | IGNORE_NONBUNDLED;
            }
            if (should_ignore_file($stripped_path, $bitmask)) {
                continue;
            }

            if ($file[0] != '.') {
                if (is_file($_dir . '/' . $file)) {
                    if (substr($file, -4, 4) == '.php') {
                        $path = $dir . (($dir != '') ? '/' : '') . $file;
                        if ($orig_priority) {
                            $alt = str_replace('_custom', '', $path);
                        } else {
                            $alt = str_replace('modules/', 'modules_custom/', str_replace('sources/', 'sources_custom/', $path));
                        }
                        if (($alt == $path) || (!file_exists($alt))) {
                            $out[] = $path;
                        }
                    }
                } elseif (is_dir($_dir . '/' . $file)) {
                    $out = array_merge($out, do_dir($dir . (($dir != '') ? '/' : '') . $file, $enable_custom, $orig_priority, $avoid, $filter, $filter_avoid));
                }
            }
        }
    }
    closedir($dh);

    return $out;
}

function check_parameters()
{
    return true;
}

function die_error($system, $pos, $line, $message)
{
    global $FILENAME;
    throw new Exception('ERROR "' . $FILENAME . '" ' . $line . ' ' . $pos . ' ' . 'PHP: ' . $message);
}

function warn_error($system, $pos, $line, $message)
{
    global $FILENAME;
    @touch(get_file_base() . '/' . $FILENAME); // So CQC can sort by mtime and find it easily
    echo 'WARNING "' . $FILENAME . '" ' . $line . ' ' . $pos . ' ' . 'PHP: ' . $message . cnl();
}

function get_file_extension($name)
{
    $dot_pos = strrpos($name, '.');
    if ($dot_pos === false) {
        return '';
    }
    return strtolower(substr($name, $dot_pos + 1));
}

function die_html_trace($message)
{
    echo $message . '<br /><br />';

    while (ob_get_level() > 0) { // Emergency output, potentially, so kill off any active buffer
        ob_end_clean();
    }
    $_trace = debug_backtrace();
    $trace = '';
    foreach ($_trace as $stage) {
        $traces = '';
        foreach ($stage as $key => $value) {
            if ($key == 'file') {
                continue;
            }
            $_value = var_export($value, true);
            $traces .= ucfirst($key) . ' -> ' . htmlentities($_value) . '<br />';
        }
        $trace .= '<p>' . $traces . '</p>';
    }

    exit('<span style="color: blue">' . $trace . '</span>');
}

function pos_to_line_details($i, $absolute = false)
{
    global $TEXT, $TOKENS;
    if ((!$absolute) && (!isset($TOKENS[$i]))) {
        $i = -1;
    }
    if ($i == -1) {
        return [0, 0, ''];
    }
    $j = $absolute ? $i : $TOKENS[$i][count($TOKENS[$i]) - 1];
    $line = substr_count(substr($TEXT, 0, $j), "\n") + 1;
    $pos = $j - strrpos(substr($TEXT, 0, $j), "\n");
    $l_s = strrpos(substr($TEXT, 0, $j + 1), "\n") + 1;
    if ($l_s == 1) {
        $l_s = 0;
    }
    $full_line = @strval(htmlentities(substr($TEXT, $l_s, strpos($TEXT, "\n", $j) - 1 - $l_s)));

    return [$pos, $line, $full_line];
}

function log_warning($warning, $i = -1, $absolute = false)
{
    global $TEXT, $FILENAME, $START_TIME, $MYFILE_WARNINGS;

    if (($i == -1) && (isset($GLOBALS['I']))) {
        $i = $GLOBALS['I'];
    }
    list($pos, $line, $full_line) = pos_to_line_details($i, $absolute);

    echo 'WARNING "' . $FILENAME . '" ' . $line . ' ' . $pos . ' ' . 'PHP: ' . $warning . cnl();
}

function log_special($type, $value)
{
    global $START_TIME;

    if (!isset($GLOBALS[$type])) {
        $GLOBALS[$type] = fopen('special_' . $START_TIME . '_' . $type . '.log', 'ab');
    }
    fwrite($GLOBALS[$type], $value . "\n");
    //fclose($GLOBALS[$$type]);
}

function require_code($codename)
{
    global $COMPOSR_PATH;
    if (file_exists($COMPOSR_PATH . '/sources_custom/' . $codename . '.php')) {
        require_once($COMPOSR_PATH . '/sources_custom/' . $codename . '.php');
    } else {
        require_once($COMPOSR_PATH . '/sources/' . $codename . '.php');
    }
}

function filter_naughty($in)
{
    return $in;
}

function filter_naughty_harsh($in)
{
    return $in;
}

function do_lang($x, $a = null, $b = null, $c = null)
{
    return do_lang_tempcode($x, $a, $b, $c);
}

function do_lang_tempcode($x, $a = null, $b = null, $c = null)
{
    global $PARSED;
    if (!isset($PARSED)) {
        $temp = file_get_contents(__DIR__ . '/../../lang_custom/EN/phpdoc.ini') . file_get_contents(__DIR__ . '/../../lang/EN/webstandards.ini') . file_get_contents(__DIR__ . '/../../lang/EN/global.ini');
        $temp_2 = explode("\n", $temp);
        $PARSED = [];
        foreach ($temp_2 as $p) {
            $pos = strpos($p, '=');
            if ($pos !== false) {
                $PARSED[substr($p, 0, $pos)] = substr($p, $pos + 1);
            }
        }
    }
    $out = strip_tags(str_replace('{1}', $a, str_replace('{2}', $b, $PARSED[$x])));
    if (is_string($c)) {
        $out = str_replace('{3}', $c, $out);
    } else {
        $out = @str_replace('{3}', $c[0], $out);
        $out = @str_replace('{4}', $c[1], $out);
        $out = @str_replace('{5}', $c[2], $out);
        $out = @str_replace('{6}', $c[3], $out);
    }
    return rtrim($out);
}

function escape_html($in)
{
    return $in;
}

function php_function_allowed($function)
{
    return true;
}

function integer_format($num)
{
    return number_format($num);
}

function attach_message($message, $message_type)
{
    global $TO_USE, $LINE, $COMPOSR_PATH;
    echo('ISSUE "' . substr($TO_USE, strlen($COMPOSR_PATH) + 1) . '" ' . strval($LINE) . ' 0 ' . $message . cnl());

    return '';
}

if (!function_exists('is_alphanumeric')) {
    /**
     * Find whether the specified string is alphanumeric or not.
     *
     * @param  string $string The string to test
     * @param  boolean $strict Whether to check stricter identifier-validity
     * @return boolean Whether the string is alphanumeric or not
     */
    function is_alphanumeric($string, $strict = false)
    {
        if ($strict) {
            return preg_match('#^[\w\-]*$#', $string) != 0;
        }

        $test = @preg_match('#^[\pL\w\-\.]*$#u', $string) != 0; // unicode version, may fail on some servers
        if ($test !== false) {
            return $test;
        }
        return preg_match('#^[\w\-\.]*$#', $string) != 0;
    }
}

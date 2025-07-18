<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/**
 * Find the MD5 hash of the space-stripped copy of function within the given code.
 *
 * @param  string $code The code.
 * @param  string $function Name of the function.
 * @return SHORT_TEXT The MD5 hash (blank: no such function).
 */
function get_function_hash($code, $function)
{
    $matches = array();
    if (preg_match('#^(function ' . $function . '\(.*\n\{.*\n\})#msU', $code, $matches) != 0) {
        return md5(preg_replace('#\s#', '', $matches[1]));
    }

    return '';
}

/**
 * Insert some code into a function in the given code snippet, by line number (before line number).
 *
 * @param  string $code The code.
 * @param  string $function Name of the function.
 * @param  integer $linenum Line number relative to start of function.
 * @param  string $newcode Code to insert.
 * @param  boolean $fail_ok Whether a failure should trigger an error
 * @return boolean Success status.
 */
function insert_code_before__by_linenum(&$code, $function, $linenum, $newcode, $fail_ok = false)
{
    $pos = strpos($code, 'function ' . $function . '(');
    if ($pos === false) {
        if ($fail_ok) {
            return false;
        }
        $lines = debug_backtrace();
        critical_error('CORRUPT_OVERRIDE', preg_replace('#^' . preg_quote(get_file_base() . '/') . '#', '', $lines[0]['file']) . ':' . strval($lines[0]['line']));
    }

    $pos = strpos($code, "\n", $pos) + 1;
    for ($i = 0; $i < $linenum; $i++) {
        $next = strpos($code, "\n", $pos);
        if ($next === false) {
            if ($fail_ok) {
                return false;
            }
            $lines = debug_backtrace();
            critical_error('CORRUPT_OVERRIDE', preg_replace('#^' . preg_quote(get_file_base() . '/') . '#', '', $lines[0]['file']) . ':' . strval($lines[0]['line']));
        }
        $pos = $next + 1;
    }
    $code = substr($code, 0, $pos) . "\t" . $newcode . "\n" . substr($code, $pos);

    return true;
}

/**
 * Insert some code into a function in the given code snippet, by line number (after line number).
 *
 * @param  string $code The code.
 * @param  string $function Name of the function.
 * @param  integer $linenum Line number relative to start of function.
 * @param  string $newcode Code to insert.
 * @param  boolean $fail_ok Whether a failure should trigger an error
 * @return boolean Success status.
 */
function insert_code_after__by_linenum(&$code, $function, $linenum, $newcode, $fail_ok = false)
{
    return insert_code_before__by_linenum($code, $function, $linenum + 1, $newcode, $fail_ok);
}

/**
 * Insert some code into a function in the given code snippet, by command (before command).
 *
 * @param  string $code The code.
 * @param  string $function Name of the function.
 * @param  string $command The command we're searching to insert by.
 * @param  string $newcode Code to insert.
 * @param  integer $instance_of_command We are inserting at this instance of the line (i.e. takes into account a literal line of code may exist in other places in a function).
 * @param  boolean $fail_ok Whether a failure should trigger an error
 * @return boolean Success status.
 */
function insert_code_before__by_command(&$code, $function, $command, $newcode, $instance_of_command = 1, $fail_ok = false)
{
    $pos = strpos($code, 'function ' . $function . '(');
    if ($pos === false) {
        if ($fail_ok) {
            return false;
        }
        $lines = debug_backtrace();
        critical_error('CORRUPT_OVERRIDE', preg_replace('#^' . preg_quote(get_file_base() . '/') . '#', '', $lines[0]['file']) . ':' . strval($lines[0]['line']));
    }

    for ($i = 0; $i < $instance_of_command; $i++) {
        $next = strpos($code, $command, $pos);
        if ($next === false) {
            if ($fail_ok) {
                return false;
            }
            $lines = debug_backtrace();
            critical_error('CORRUPT_OVERRIDE', preg_replace('#^' . preg_quote(get_file_base() . '/') . '#', '', $lines[0]['file']) . ':' . strval($lines[0]['line']));
        }
        $pos = $next + 1;
    }
    $pos = strrpos(substr($code, 0, $pos), "\n");
    $code = substr($code, 0, $pos) . "\n\t" . $newcode . substr($code, $pos);

    return true;
}

/**
 * Insert some code into a function in the given code snippet, by command (after command).
 *
 * @param  string $code The code.
 * @param  string $function Name of the function.
 * @param  string $command The command we're searching to insert by.
 * @param  string $newcode Code to insert.
 * @param  integer $instance_of_command We are inserting at this instance of the line (i.e. takes into account a literal line of code may exist in other places in a function).
 * @param  boolean $fail_ok Whether a failure should trigger an error
 * @return boolean Success status.
 */
function insert_code_after__by_command(&$code, $function, $command, $newcode, $instance_of_command = 1, $fail_ok = false)
{
    $pos = strpos($code, 'function ' . $function . '(');
    if ($pos === false) {
        if ($fail_ok) {
            return false;
        }
        $lines = debug_backtrace();
        critical_error('CORRUPT_OVERRIDE', preg_replace('#^' . preg_quote(get_file_base() . '/') . '#', '', $lines[0]['file']) . ':' . strval($lines[0]['line']));
    }

    for ($i = 0; $i < $instance_of_command; $i++) {
        $next = strpos($code, $command, $pos);
        if ($next === false) {
            if ($fail_ok) {
                return false;
            }
            $lines = debug_backtrace();
            critical_error('CORRUPT_OVERRIDE', preg_replace('#^' . preg_quote(get_file_base() . '/') . '#', '', $lines[0]['file']) . ':' . strval($lines[0]['line']));
        }
        $pos = $next + 1;
    }
    $pos = strpos($code, "\n", $pos);
    $code = substr($code, 0, $pos) . "\n\t" . $newcode . substr($code, $pos);

    return true;
}

/**
 * Remove some code from a function in the given code snippet.
 *
 * @param  string $code The code.
 * @param  string $function Name of the function.
 * @param  string $command The command we're searching to insert by.
 * @param  integer $instance_of_command We remove the nth instance of this command.
 * @param  boolean $fail_ok Whether a failure should trigger an error
 * @return boolean Success status.
 */
function remove_code(&$code, $function, $command, $instance_of_command = 1, $fail_ok = false)
{
    $pos = strpos($code, 'function ' . $function . '(');
    if ($pos === false) {
        if ($fail_ok) {
            return false;
        }
        $lines = debug_backtrace();
        critical_error('CORRUPT_OVERRIDE', preg_replace('#^' . preg_quote(get_file_base() . '/') . '#', '', $lines[0]['file']) . ':' . strval($lines[0]['line']));
    }

    for ($i = 0; $i < $instance_of_command; $i++) {
        $next = strpos($code, $command, $pos);
        if ($next === false) {
            if ($fail_ok) {
                return false;
            }
            $lines = debug_backtrace();
            critical_error('CORRUPT_OVERRIDE', preg_replace('#^' . preg_quote(get_file_base() . '/') . '#', '', $lines[0]['file']) . ':' . strval($lines[0]['line']));
        }
        $pos = $next + 1;
    }
    $old_pos = $pos;
    $pos = strpos($code, "\n", $pos);
    $code = substr($code, 0, $pos) . "\n" . substr($code, $old_pos + 1);

    return true;
}

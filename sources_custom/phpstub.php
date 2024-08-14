<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/*

This file should never be included.
It serves to *specify* the subset of PHP that Composr is written to. We define a limited subset so we can verify we write to multiple versions of PHP.
The code quality checker automatically parses and uses this file, to build up the checker API.

*/

/**
 * Absolute value.
 *
 * @param  mixed $number The number to get the absolute value of.
 * @return mixed The absolute value of number.
 */
function abs($number)
{
    return 0;
}

/**
 * Returns the floating point remainder (modulo) of the division of the arguments.
 *
 * @param  float $x The dividend
 * @param  float $y The divisor
 * @return mixed The remainder
 */
function fmod($x, $y)
{
    return 0.0;
}

/**
 * Counts all the values of an array.
 *
 * @param  array $input Input array.
 * @return array An array using the values of the input array as keys and their frequency in input as values.
 */
function array_count_values($input)
{
    return array();
}

/**
 * Calculate the difference between arrays.
 *
 * @param  array $array1 First array.
 * @param  array $array2 Second array.
 * @param  ?array $array3 Third array (null: only 2).
 * @return array The difference.
 */
function array_diff($array1, $array2, $array3 = null)
{
    return array();
}

/**
 * Computes the intersection of arrays with additional index check.
 *
 * @param  array $array1 First array.
 * @param  array $array2 Second array.
 * @param  ?array $array3 Third array (null: only 2).
 * @return array The difference.
 */
function array_diff_assoc($array1, $array2, $array3 = null)
{
    return array();
}

/**
 * Exchanges all keys with their associated values in an array.
 *
 * @param  array $trans Array to flip.
 * @return array An array in flip order.
 */
function array_flip($trans)
{
    return array();
}

/**
 * Checks if the given key or index exists in the array.
 *
 * @param  mixed $key Key.
 * @param  array $search Search array.
 * @return boolean Whether the key is set in the search array.
 */
function array_key_exists($key, $search)
{
    return false;
}

/**
 * Return all the keys of an array.
 *
 * @param  array $input Input array.
 * @param  ?mixed $search_value Only find keys with this value (null: no such filter).
 * @return array The keys of the array.
 */
function array_keys($input, $search_value = null)
{
    return array();
}

/**
 * Calculate the intersection between arrays.
 *
 * @param  array $array1 First array.
 * @param  array $array2 Second array.
 * @param  ?array $array3 Third array (null: only 2).
 * @return array The intersection.
 */
function array_intersect($array1, $array2, $array3 = null)
{
    return array();
}

/**
 * Computes the intersection of arrays using keys for comparison.
 *
 * @param  array $array1 First array.
 * @param  array $array2 Second array.
 * @param  ?array $array3 Third array (null: only 2).
 * @return array The intersection.
 */
function array_intersect_key($array1, $array2, $array3 = null)
{
    return array();
}

/**
 * Calculate the intersection of arrays with additional index check.
 *
 * @param  array $array1 First array.
 * @param  array $array2 Second array.
 * @param  ?array $array3 Third array (null: only 2).
 * @return array The intersection.
 */
function array_intersect_assoc($array1, $array2, $array3 = null)
{
    return array();
}

/**
 * Merge two or more arrays.
 *
 * @param  array $array1 First array.
 * @param  array $array2 Second array.
 * @param  ?array $array3 Third array (null: only 2).
 * @param  ?array $array4 Fourth array to merge (null: not this one).
 * @param  ?array $array5 Fifth array to merge (null: not this one).
 * @return array Merged array.
 */
function array_merge($array1, $array2, $array3 = null, $array4 = null, $array5 = null)
{
    return array();
}

/**
 * Pop the element off the end of array.
 *
 * @param  array $array The array.
 * @return ?mixed The value (null: no value).
 */
function array_pop(&$array)
{
    return 0;
}

/**
 * Push one or more elements onto the end of array.
 *
 * @param  array $array The array.
 * @param  mixed $var1 Element to append.
 * @param  ?mixed $var2 Element to append (null: no more - actually pass nothing for this).
 * @param  ?mixed $var3 Element to append (null: no more - actually pass nothing for this).
 * @param  ?mixed $var4 Element to append (null: no more - actually pass nothing for this).
 * @return integer The new number of elements in the array.
 */
function array_push(&$array, $var1, $var2 = null, $var3 = null, $var4 = null)
{
    return 0;
}

/**
 * Return an array with elements in reverse order.
 *
 * @param  array $array The array to reverse.
 * @param  boolean $preserve_keys Whether to preserve keys.
 * @return array The reversed array.
 */
function array_reverse($array, $preserve_keys = false)
{
    return array();
}

/**
 * Searches the array for a given value and returns the corresponding key if successful.
 *
 * @param  mixed $needle Needle.
 * @param  array $haystack Haystack.
 * @return ~mixed The key (false: not found).
 */
function array_search($needle, $haystack)
{
    return 0;
}

/**
 * Shift an element off the beginning of array.
 *
 * @param  array $array The array.
 * @return ?mixed Shifted element (null: empty array given).
 */
function array_shift(&$array)
{
    return '';
}

/**
 * Extract a slice of the array.
 *
 * @param  array $array The array.
 * @param  integer $offset The offset.
 * @param  ?integer $length The length (null: up to the end of the array).
 * @return array The slice.
 */
function array_slice($array, $offset, $length = null)
{
    return array();
}

/**
 * Remove a portion of the array and replace it with something else.
 *
 * @param  array $input The array.
 * @param  integer $offset The offset.
 * @param  ?integer $length The length (null: up to the end of the array).
 * @param  ?array $replacement The replacement (null: nothing put in, just bit taken out).
 * @return array The spliced result.
 */
function array_splice(&$input, $offset, $length = null, $replacement = null)
{
    return array();
}

/**
 * Removes duplicate values from an array. Equivalence determined by string comparison.
 *
 * @param  array $array Input array.
 * @return array Output array.
 */
function array_unique($array)
{
    return array();
}

/**
 * Return all the values of an array.
 *
 * @param  array $array Input array.
 * @return array Output array.
 */
function array_values($array)
{
    return array();
}

/**
 * Sort an array in reverse order and maintain index association.
 *
 * @param  array $array Array.
 * @param  integer $sort_flags Sort flags.
 */
function arsort(&$array, $sort_flags = 0)
{
}

/**
 * Sort an array and maintain index association.
 *
 * @param  array $array Array.
 * @param  integer $sort_flags Sort flags.
 */
function asort(&$array, $sort_flags = 0)
{
}

/**
 * Decodes data encoded with MIME base64.
 *
 * @param  string $encoded_data Encoded data.
 * @return ~string Decoded data (false: error).
 */
function base64_decode($encoded_data)
{
    return '';
}

/**
 * Encodes data with MIME base64.
 *
 * @param  string $data Data.
 * @return string Encoded data.
 */
function base64_encode($data)
{
    return '';
}

/**
 * Call a user function given by the first parameter.
 *
 * @param  mixed $function Function callback.
 * @param  ?mixed $param_a Optional parameter (null: none).
 * @param  ?mixed $param_b Optional parameter (null: none).
 * @param  ?mixed $param_c Optional parameter (null: none).
 * @param  ?mixed $param_d Optional parameter (null: none).
 * @return mixed Whatever the function returns.
 */
function call_user_func($function, $param_a = null, $param_b = null, $param_c = null, $param_d = null)
{
    return 0;
}

/**
 * Round fractions up.
 *
 * @param  float $function Value to round up.
 * @return float Rounded value.
 */
function ceil($function)
{
    return 0.0;
}

/**
 * Change directory.
 *
 * @param  PATH $directory Path to change to.
 * @return boolean Success status.
 */
function chdir($directory)
{
    return false;
}

/**
 * Validate a gregorian date.
 *
 * @param  integer $month The month.
 * @param  integer $day The day.
 * @param  integer $year The year.
 * @return boolean Whether the date is valid.
 */
function checkdate($month, $day, $year)
{
    return false;
}

/**
 * Changes file mode.
 *
 * @param  PATH $filename The file to change the mode of.
 * @param  integer $mode The mode (e.g. 0777).
 * @return boolean Success status.
 */
function chmod($filename, $mode)
{
    return false;
}

/**
 * Return a specific character.
 *
 * @param  integer $ascii The ASCII code for the character required.
 * @return string A string of length 1, where the first character is as requested.
 */
function chr($ascii)
{
    return '';
}

/**
 * Split a string into smaller chunks. Can be used to split a string into smaller chunks which is useful for e.g. converting base64_encode output to match RFC 2045 semantics. It inserts end (defaults to "\r\n") every chunklen characters.
 *
 * @param  string $body The input string.
 * @param  integer $chunklen The maximum chunking length.
 * @param  string $splitter Split character.
 * @return string The chunked version of the input string.
 */
function chunk_split($body, $chunklen = 76, $splitter = "\r\n")
{
    return '';
}

/**
 * Checks if the class has been defined.
 *
 * @param  string $class_name The class identifier.
 * @return boolean Whether the class has been defined.
 */
function class_exists($class_name)
{
    return false;
}

/**
 * Clears file status cache.
 *
 * @param  boolean $clear_realcache_path Whether to clear the realpath cache or not.
 * @param  ?PATH $filename Clear the realpath and the stat cache for a specific filename only; only used if clear_realpath_cache is true (null: clear for all).
 */
function clearstatcache($clear_realcache_path = false, $filename = null)
{
}

/**
 * Close directory handle.
 *
 * @param  resource $handle The directory handle to close.
 */
function closedir($handle)
{
}

/**
 * Returns the value of a constant.
 *
 * @param  string $name The name of the constant.
 * @return mixed The value of the constant.
 */
function constant($name)
{
    return '';
}

/**
 * Copies a file. {{creates-file}}
 *
 * @param  PATH $source The source path.
 * @param  PATH $dest The destination path.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @return boolean Success status.
 */
function copy($source, $dest, $context = null)
{
    return false;
}

/**
 * Calculate the cosine of an angle.
 *
 * @param  float $angle The angle in radians.
 * @return float The cosine.
 */
function cos($angle)
{
    return 0.0;
}

/**
 * Count elements in a variable.
 *
 * @param  array $var Variable to count elements of.
 * @param  integer $mode The count mode (COUNT_NORMAL or COUNT_RECURSIVE).
 * @return integer The count.
 */
function count($var, $mode = 0)
{
    return 0;
}

/**
 * One-way string hashing (not encryption, as not reversible).
 *
 * @param  string $string The string to hash.
 * @param  ?string $salt The salt (null: generate a random salt).
 * @return string The hash. The start of the hash determines parameters (encoding, salt).
 */
function crypt($string, $salt = null)
{
    return '';
}

/**
 * Return the current element in an array.
 *
 * @param  array $array The array.
 * @return mixed The current element.
 */
function current($array)
{
    return 0;
}

/**
 * Format a local time/date.
 *
 * @param  string $format The format string.
 * @param  ?TIME $timestamp The timestamp (null: current time).
 * @return string The string representation of the local time/date.
 */
function date($format, $timestamp = null)
{
    return '';
}

/**
 * Integer to string representation of hexadecimal.
 *
 * @param  integer $number The integer ('decimal' form, although truly stored in binary).
 * @return string The string representation.
 */
function dechex($number)
{
    return '';
}

/**
 * Integer to string representation of octal.
 *
 * @param  integer $number The integer ('decimal' form, although truly stored in binary).
 * @return string The string representation.
 */
function decoct($number)
{
    return '';
}

/**
 * Defines a named constant.
 *
 * @param  string $name Identifier.
 * @param  mixed $value Value.
 * @return boolean Success status.
 */
function define($name, $value)
{
    return false;
}

/**
 * Checks whether a given named constant exists.
 *
 * @param  string $name The identifier of a constant.
 * @return boolean Whether the constant exists.
 */
function defined($name)
{
    return false;
}

/**
 * Returns directory name component of path.
 *
 * @param  PATH $name The path.
 * @return PATH The directory name component.
 */
function dirname($name)
{
    return '';
}

/**
 * Converts the number in degrees to the radian equivalent.
 *
 * @param  float $number Angle in degrees.
 * @return float Angle in radians.
 */
function deg2rad($number)
{
    return 0.0;
}

/**
 * Sets which PHP errors are reported.
 *
 * @param  ?integer $level OR'd combination of error type constants. (E_ERROR, E_WARNING,  E_PARSE, E_NOTICE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_ALL) (null: find current level).
 * @return integer Current error reporting level.
 */
function error_reporting($level = null)
{
    return 0;
}

/**
 * Output a message and terminate the current script.
 *
 * @param  mixed $message The message (string), or status code (integer).
 * @return mixed Never returns (i.e. exits)
 */
function exit($message = '')
{
}

/**
 * Split a string by string.
 *
 * @param  string $separator The separator.
 * @param  string $string The string to split.
 * @param  ?integer $limit The maximum number of splits (the last element containing the remainder) (null: no limit).
 * @return array The split list.
 */
function explode($separator, $string, $limit = null)
{
    return array();
}

/**
 * Closes an open file pointer.
 *
 * @param  resource $handle The file pointer.
 * @return boolean Success status.
 */
function fclose($handle)
{
    return false;
}

/**
 * Tests for end-of-file on a file pointer.
 *
 * @param  resource $handle The file pointer.
 * @return boolean Whether the end of the file has been reached.
 */
function feof($handle)
{
    return false;
}

/**
 * Gets line from file pointer.
 *
 * @param  resource $handle The file pointer.
 * @param  ?integer $length The maximum length of the line (null: no limit).
 * @return string The string read.
 */
function fgets($handle, $length = null)
{
    return '';
}

/**
 * Reads entire file into an array.
 *
 * @param  PATH $filename The file name.
 * @param  integer $flags Flags.
 * @return ~array The array (each line being an entry in the array, and newlines still attached) (false: error).
 */
function file($filename, $flags = 0)
{
    return array();
}

/**
 * Checks whether a file or directory exists.
 *
 * @param  PATH $filename The path.
 * @return boolean Whether it exists.
 */
function file_exists($filename)
{
    return false;
}

/**
 * Gets inode change time of file.
 *
 * @param  PATH $filename The filename.
 * @return ~TIME Timestamp of creation (negativity is blasphemy) (false: error).
 */
function filectime($filename)
{
    return 0;
}

/**
 * Gets file group.
 *
 * @param  PATH $filename The filename.
 * @return ~integer The posix group ID (false: error).
 */
function filegroup($filename)
{
    return 0;
}

/**
 * Gets file modification time.
 *
 * @param  PATH $filename The filename.
 * @return ~TIME Timestamp of modification (false: error).
 */
function filemtime($filename)
{
    return 0;
}

/**
 * Gets file owner.
 *
 * @param  PATH $filename The filename.
 * @return ~integer The posix user ID (false: error).
 */
function fileowner($filename)
{
    return 0;
}

/**
 * Gets file permissions.
 *
 * @param  PATH $filename The filename.
 * @return ~integer The permissions (e.g. 0777) (false: error).
 */
function fileperms($filename)
{
    return 0;
}

/**
 * Gets file size.
 *
 * @param  PATH $filename The filename.
 * @return ~integer The file size (false: error).
 */
function filesize($filename)
{
    return 0;
}

/**
 * Get float value of a variable.
 *
 * @param  mixed $var The input.
 * @return float The float value.
 */
function floatval($var)
{
    return 0.0;
}

/**
 * Round fractions down.
 *
 * @param  float $value The input.
 * @return float The rounded value.
 */
function floor($value)
{
    return 0.0;
}

/**
 * Get an array of all defined variables.
 *
 * @return array All defined variables.
 */
function get_defined_vars()
{
    return array();
}

/**
 * Get an array of all declared classes.
 *
 * @return array All declared classes.
 */
function get_declared_classes()
{
    return array();
}

/**
 * Get an array of all defined functions.
 *
 * @return array All defined functions.
 */
function get_defined_functions()
{
    return array();
}

/**
 * Opens file or URL. {{creates-file}}
 *
 * @param  PATH $filename Filename.
 * @param  string $mode Mode (e.g. at).
 * @param  boolean $use_include_path Whether to search within the include path.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @return ~resource The file handle (false: could not be opened).
 */
function fopen($filename, $mode, $use_include_path = false, $context = null)
{
    return array();
}

/**
 * Output all remaining data on a file pointer.
 * Call cms_ob_end_clean() first, else too much memory will be used.
 *
 * @param  resource $handle The file handle.
 * @return ~integer The number of characters that got read (false: error).
 */
function fpassthru($handle)
{
    return 0;
}

/**
 * Binary-safe file read.
 *
 * @param  resource $handle The file handle.
 * @param  integer $length Maximum length to read.
 * @return string The read data.
 */
function fread($handle, $length)
{
    return '';
}

/**
 * Seeks on a file pointer.
 *
 * @param  resource $handle The file handle.
 * @param  integer $offset The offset (meaning depends on whence).
 * @param  integer $whence SEEK_SET, SEEK_CUR or SEEK_END.
 * @return integer Success status (-1 means error).
 */
function fseek($handle, $offset, $whence = SEEK_SET)
{
    return 0;
}

/**
 * Gets file pointer read/write position.
 *
 * @param  resource $handle The file handle.
 * @return ~integer The offset (false: error).
 */
function ftell($handle)
{
    return 0;
}

/**
 * Find whether the function of the given function name has been defined.
 *
 * @param  string $function_name The name of the function.
 * @return boolean Whether it is defined.
 */
function function_exists($function_name)
{
    return false;
}

/**
 * Binary-safe file write.
 *
 * @param  resource $handle The file handle.
 * @param  string $string The string to write to the file.
 * @param  ?integer $length The length of data to write (null: all of $string).
 * @return ~integer The number of bytes written (false: error).
 */
function fwrite($handle, $string, $length = null)
{
    return 0;
}

/**
 * Retrieve information about the currently installed GD library.
 *
 * @return array Array of information.
 */
function gd_info()
{
    return array();
}

/**
 * Returns the name of the class of an object.
 *
 * @param  object $obj The object.
 * @return string The class name.
 */
function get_class($obj)
{
    return '';
}

/**
 * Gets the class methods' names.
 *
 * @param  mixed $class_name The class name or an object instance.
 * @return ?array An array of method names defined (null: error).
 */
function get_class_methods($class_name)
{
    return array();
}

/**
 * Get the default properties of the class.
 *
 * @param  string $class_name The class name.
 * @return ~array An associative array of declared properties visible from the current scope, with their default value (false: error).
 */
function get_class_vars($class_name)
{
    return array();
}

/**
 * Gets the properties of the given object.
 *
 * @param  object $object An object instance.
 * @return array An associative array of defined object accessible non-static properties.
 */
function get_object_vars($object)
{
    return array();
}

/**
 * Returns the translation table used by htmlspecialchars and htmlentities.
 *
 * @param  integer $table The table to select (HTML_ENTITIES or HTML_SPECIALCHARS).
 * @param  integer $quote_style The quote style (ENT_QUOTES or ENT_NOQUOTES or ENT_COMPAT).
 * @param  string $charset The character set to use
 * @return array The translation table.
 */
function get_html_translation_table($table, $quote_style = ENT_COMPAT, $charset = 'utf-8')
{
    return array();
}

/**
 * Gets the current working directory.
 *
 * @return PATH The cwd.
 */
function getcwd()
{
    return '';
}

/**
 * Get date/time information.
 *
 * @param  ?TIME $timestamp Timestamp to get information for (null: now).
 * @return array The information.
 */
function getdate($timestamp = null)
{
    return array();
}

/**
 * Gets the value of an environment variable.
 *
 * @param  string $string The environment name to get (e.g. PATH).
 * @return ~string The value (false: error).
 */
function getenv($string)
{
    return '';
}

/**
 * Format a GMT/UTC date/time (uses different format to 'date' function).
 *
 * @param  string $format The 'gm' format string.
 * @param  ?TIME $timestamp Timestamp to use (null: now).
 * @return string The formatted string.
 */
function gmdate($format, $timestamp = null)
{
    return '';
}

/**
 * Close an open gz-file pointer.
 *
 * @param  resource $handle The handle.
 * @return boolean Success status.
 */
function gzclose($handle)
{
    return false;
}

/**
 * Open gz-file. {{creates-file}}
 *
 * @param  PATH $filename The filename.
 * @param  string $mode The mode (e.g. b).
 * @return ~resource The handle (false: error).
 */
function gzopen($filename, $mode)
{
    return array();
}

/**
 * Binary-safe gz-file write.
 *
 * @param  resource $handle The file handle.
 * @param  string $string The string to write to the file.
 * @param  ?integer $length The length of data to write (null: full length of input string).
 * @return ~integer The number of bytes written (false: error).
 */
function gzwrite($handle, $string, $length = null)
{
    return 0;
}

/**
 * Send a raw HTTP header.
 *
 * @sets_output_state
 *
 * @param  string $string The header to send.
 * @param  boolean $replace_last Whether to replace a previous call to set the same header (if you choose to not replace, it will send two different values for the same header).
 */
function header($string, $replace_last = true)
{
}

/**
 * Checks if or where headers have been sent.
 *
 * @return boolean The answer.
 */
function headers_sent()
{
    return false;
}

/**
 * String representation of hexadecimal to decimal.
 *
 * @param  string $hex_string The string representation.
 * @return integer The integer ('decimal' form, although truly stored in binary).
 */
function hexdec($hex_string)
{
    return 0;
}

/**
 * Convert all applicable characters to HTML entities.
 *
 * @param  string $string The string to encode.
 * @param  integer $quote_style The quote style (ENT_COMPAT, ENT_QUOTES, ENT_NOQUOTES).
 * @param  string $charset The character set to use.
 * @return string The encoded string.
 */
function htmlentities($string, $quote_style = ENT_COMPAT, $charset = '')
{
    return '';
}

/**
 * Convert all basic HTML encoding characters to HTML entities.
 *
 * @param  string $string The string to encode.
 * @param  integer $quote_style The quote style (ENT_COMPAT, ENT_QUOTES, ENT_NOQUOTES).
 * @param  string $charset The character set to use.
 * @return string The encoded string.
 */
function htmlspecialchars($string, $quote_style = ENT_COMPAT, $charset = '')
{
    return '';
}

/**
 * Set the blending mode for an image.
 *
 * @param  resource $image The image handle.
 * @param  boolean $blendmode Whether to alpha blend.
 * @return boolean Success status.
 */
function imagealphablending($image, $blendmode)
{
    return true;
}

/**
 * Allocate a color for an image.
 *
 * @param  resource $image The image handle.
 * @param  integer $red Red component (0-255).
 * @param  integer $green Green component (0-255).
 * @param  integer $blue Blue component (0-255).
 * @return ~integer Combined colour identifier (false: could not allocate).
 */
function imagecolorallocate($image, $red, $green, $blue)
{
    return 0;
}

/**
 * Allocate a color for an image, with an alpha-component.
 *
 * @param  resource $image The image handle.
 * @param  integer $red Red component (0-255).
 * @param  integer $green Green component (0-255).
 * @param  integer $blue Blue component (0-255).
 * @param  integer $alpha Alpha component (0-127).
 * @return integer Combined colour identifier.
 */
function imagecolorallocatealpha($image, $red, $green, $blue, $alpha)
{
    return 0;
}

/**
 * Define a color as transparent.
 *
 * @param  resource $image The image handle.
 * @param  ?integer $color Transparency colour identifier (null: get it, don't set it).
 * @return integer Transparency colour identifier.
 */
function imagecolortransparent($image, $color = null)
{
    return 0;
}

/**
 * Copy part of an image.
 *
 * @param  resource $dst_im Destination image handle.
 * @param  resource $src_im Source image handle.
 * @param  integer $dst_x Destination X-ordinate.
 * @param  integer $dst_y Destination Y-ordinate.
 * @param  integer $src_x Source X-ordinate.
 * @param  integer $src_y Source Y-ordinate.
 * @param  integer $src_w Width to copy.
 * @param  integer $src_h Height to copy.
 */
function imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
{
}

/**
 * Copy and resize part of an image with resampling.
 *
 * @param  resource $dst_im Destination image handle.
 * @param  resource $src_im Source image handle.
 * @param  integer $dst_x Destination X-ordinate.
 * @param  integer $dst_y Destination Y-ordinate.
 * @param  integer $src_x Source X-ordinate.
 * @param  integer $src_y Source Y-ordinate.
 * @param  integer $dst_w Destination width.
 * @param  integer $dst_h Destination height.
 * @param  integer $src_w Source width.
 * @param  integer $src_h Source height.
 * @return boolean Success status.
 */
function imagecopyresampled($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
{
    return true;
}

/**
 * Copy and resize part of an image.
 *
 * @param  resource $dst_im Destination image handle.
 * @param  resource $src_im Source image handle.
 * @param  integer $dst_x Destination X-ordinate.
 * @param  integer $dst_y Destination Y-ordinate.
 * @param  integer $src_x Source X-ordinate.
 * @param  integer $src_y Source Y-ordinate.
 * @param  integer $dst_w Destination width.
 * @param  integer $dst_h Destination height.
 * @param  integer $src_w Source width.
 * @param  integer $src_h Source height.
 */
function imagecopyresized($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h)
{
}

/**
 * Create a new palette based image.
 *
 * @param  integer $width Width.
 * @param  integer $height Height.
 * @return resource The image handle.
 */
function imagecreate($width, $height)
{
    return array();
}

/**
 * Create a new image from the image stream in the string.
 *
 * @param  string $image The image.
 * @return ~resource The image handle (false: error).
 */
function imagecreatefromstring($image)
{
    return array();
}

/**
 * Create a new image from a PNG file on disk.
 *
 * @param  PATH $path The PNG file.
 * @return ~resource The image handle (false: error).
 */
function imagecreatefrompng($path)
{
    return array();
}

/**
 * Create a new image from a JPEG file on disk.
 *
 * @param  PATH $path The JPEG file.
 * @return ~resource The image handle (false: error).
 */
function imagecreatefromjpeg($path)
{
    return array();
}

/**
 * Finds whether an image is a truecolor image.
 *
 * @param  resource $image The image handle.
 * @return boolean Whether the image is truecolor.
 */
function imageistruecolor($image)
{
    return true;
}

/**
 * Create a new truecolor image.
 *
 * @param  integer $x Width.
 * @param  integer $y Height.
 * @return resource The image handle.
 */
function imagecreatetruecolor($x, $y)
{
    return array();
}

/**
 * Get the index of the color of a pixel.
 *
 * @param  resource $image The image handle.
 * @param  integer $x X ordinate.
 * @param  integer $y Y ordinate.
 * @return integer The colour.
 */
function imagecolorat($image, $x, $y)
{
    return 0;
}

/**
 * Get the colors for an index.
 *
 * @param  resource $image The image handle.
 * @param  integer $color The colour.
 * @return array Map of components.
 */
function imagecolorsforindex($image, $color)
{
    return array();
}

/**
 * Destroy an image resource.
 *
 * @param  resource $image The image handle.
 */
function imagedestroy($image)
{
}

/**
 * Flood fill.
 *
 * @param  resource $image The image handle.
 * @param  integer $x Start from X.
 * @param  integer $y Start from Y.
 * @param  integer $colour The colour code to use.
 */
function imagefill($image, $x, $y, $colour)
{
}

/**
 * Get font height.
 *
 * @param  integer $font Font code.
 * @return integer Height.
 */
function imagefontheight($font)
{
    return 0;
}

/**
 * Get font width.
 *
 * @param  integer $font Font code.
 * @return integer Width.
 */
function imagefontwidth($font)
{
    return 0;
}

/**
 * Output image to browser or file as JPEG.
 *
 * @param  resource $image The image handle.
 * @param  ?string $filename The filename (null: output to browser).
 * @param  ?integer $quality Quality level (null: default).
 * @return boolean Success status.
 */
function imagejpeg($image, $filename = null, $quality = null)
{
    return true;
}

/**
 * Output image to browser or file as PNG.
 *
 * @param  resource $image The image handle.
 * @param  ?string $filename The filename (null: output to browser).
 * @param  integer $quality Compression level (0-9, 9 being highest compression).
 * @return boolean Success status.
 */
function imagepng($image, $filename = null, $quality = 0)
{
    return true;
}

/**
 * Set the flag to save full alpha channel information (as opposed to single-color transparency) when saving PNG images.
 *
 * @param  resource $image The image handle.
 * @param  boolean $saveflag Whether to save alpha channel information.
 */
function imagesavealpha($image, $saveflag)
{
}

/**
 * Set a single pixel.
 *
 * @param  resource $image The image handle.
 * @param  integer $x X-ordinate.
 * @param  integer $y Y-ordinate.
 * @param  integer $color Colour code.
 */
function imagesetpixel($image, $x, $y, $color)
{
}

/**
 * Draw a string horizontally.
 *
 * @param  resource $image The image handle.
 * @param  integer $font Font code.
 * @param  integer $x X-ordinate.
 * @param  integer $y Y-ordinate.
 * @param  string $s Text to draw.
 * @param  integer $col Colour code.
 */
function imagestring($image, $font, $x, $y, $s, $col)
{
}

/**
 * Get image width.
 *
 * @param  resource $image The image handle.
 * @return integer The image width.
 */
function imagesx($image)
{
    return 0;
}

/**
 * Get image height.
 *
 * @param  resource $image The image handle.
 * @return integer The image height.
 */
function imagesy($image)
{
    return 0;
}

/**
 * Give the bounding box of a text using TrueType fonts.
 *
 * @param  resource $image The image handle.
 * @param  integer $font The loaded font.
 * @param  integer $x X-ordinate.
 * @param  integer $y Y-ordinate.
 * @param  string $s Text to draw.
 * @param  integer $col Colour code.
 */
function imagestringup($image, $font, $x, $y, $s, $col)
{
}

/**
 * Give the bounding box of a text using TrueType fonts.
 *
 * @param  float $size The font size in pixels.
 * @param  float $angle Angle in degrees in which text will be measured.
 * @param  string $fontfile The name of the TrueType font file.
 * @param  string $text The string to be measured.
 * @return ~array Tuple: lower-left-X, lower-left-Y, lower-right-X, lower-right-Y, upper-right-X, upper-right-Y, upper-left-X, upper-left-Y (false: error).
 */
function imagettfbbox($size, $angle, $fontfile, $text)
{
    return array();
}

/**
 * Give the bounding box of a text using TrueType fonts.
 *
 * @param  resource $handle The image handle.
 * @param  float $size The font size in pixels.
 * @param  float $angle Angle in degrees in which text will be measured.
 * @param  integer $x X-ordinate.
 * @param  integer $y Y-ordinate.
 * @param  integer $colour Colour code.
 * @param  string $fontfile The name of the TrueType font file.
 * @param  string $text Text to draw.
 * @return ~array Tuple: lower-left-X, lower-left-Y, lower-right-X, lower-right-Y, upper-right-X, upper-right-Y, upper-left-X, upper-left-Y (false: error).
 */
function imagettftext($handle, $size, $angle, $x, $y, $colour, $fontfile, $text)
{
    return array();
}

/**
 * Return the image types supported by this execution environment.
 *
 * @return integer Bit field of constants: IMG_GIF | IMG_JPG | IMG_PNG.
 */
function imagetypes()
{
    return 0;
}

/**
 * Draw a partial ellipse.
 *
 * @param  resource $image The image involved.
 * @param  integer $cx X@top-left.
 * @param  integer $cy Y@top-left.
 * @param  integer $w width.
 * @param  integer $h height.
 * @param  integer $s start degrees (0 degrees=3 o clock).
 * @param  integer $e end degrees (0 degrees=3 o clock).
 * @param  integer $color Colour code.
 * @return boolean Success status.
 */
function imagearc($image, $cx, $cy, $w, $h, $s, $e, $color)
{
    return true;
}

/**
 * Draw a partial ellipse and fill it.
 *
 * @param  resource $image The image involved.
 * @param  integer $cx X@top-left.
 * @param  integer $cy Y@top-left.
 * @param  integer $w width.
 * @param  integer $h height.
 * @param  integer $s start degrees (0 degrees=3 o clock).
 * @param  integer $e end degrees (0 degrees=3 o clock).
 * @param  integer $color Style, bitwise of IMG_ARC_PIE, IMG_ARC_CHORD, IMG_ARC_NOFILL, IMG_ARC_EDGED.
 * @param  integer $style Colour code.
 * @return boolean Success status.
 */
function imagefilledarc($image, $cx, $cy, $w, $h, $s, $e, $color, $style)
{
    return true;
}

/**
 * Copy and merge part of an image with gray scale.
 *
 * @param  resource $dst_im Destination image handle.
 * @param  resource $src_im Source image handle.
 * @param  integer $dst_x Destination X-ordinate.
 * @param  integer $dst_y Destination Y-ordinate.
 * @param  integer $src_x Source X-ordinate.
 * @param  integer $src_y Source Y-ordinate.
 * @param  integer $src_w Width to copy.
 * @param  integer $src_h Height to copy.
 * @param  integer $pct Opacity value.
 */
function imagecopymergegray($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
}

/**
 * Draw a line.
 *
 * @param  resource $image The image involved.
 * @param  integer $x1 Start-X.
 * @param  integer $y1 Start-Y.
 * @param  integer $x2 End-X.
 * @param  integer $y2 End-Y.
 * @param  integer $color The colour.
 */
function imageline($image, $x1, $y1, $x2, $y2, $color)
{
}

/**
 * Draw an ellipse.
 *
 * @param  resource $image The image involved.
 * @param  integer $cx Centre-X.
 * @param  integer $cy Centre-Y.
 * @param  integer $w Width.
 * @param  integer $h Height.
 * @param  integer $color Colour.
 * @return boolean Success status.
 */
function imageellipse($image, $cx, $cy, $w, $h, $color)
{
    return true;
}

/**
 * Draw a filled ellipse.
 *
 * @param  resource $image The image involved.
 * @param  integer $cx Centre-X.
 * @param  integer $cy Centre-Y.
 * @param  integer $w Width.
 * @param  integer $h Height.
 * @param  integer $color Colour.
 * @return boolean Success status.
 */
function imagefilledellipse($image, $cx, $cy, $w, $h, $color)
{
    return true;
}

/**
 * Draw a character horizontally.
 *
 * @param  resource $image The image involved.
 * @param  integer $font Font number.
 * @param  integer $x X.
 * @param  integer $y Y.
 * @param  string $c What to write.
 * @param  integer $color Colour number.
 */
function imagechar($image, $font, $x, $y, $c, $color)
{
}

/**
 * Draw a filled polygon.
 *
 * @param  resource $image The image involved.
 * @param  array $points Array of pairs.
 * @param  integer $num_points Number of points in array.
 * @param  integer $colour Colour number.
 */
function imagefilledpolygon($image, $points, $num_points, $colour)
{
}

/**
 * Draw a polygon.
 *
 * @param  resource $image The image involved.
 * @param  array $points Array of pairs.
 * @param  integer $num_points Number of points in array.
 * @param  integer $colour Colour number.
 */
function imagepolygon($image, $points, $num_points, $colour)
{
}

/**
 * Draw a filled rectangle.
 *
 * @param  resource $image The image involved.
 * @param  integer $x1 First-X.
 * @param  integer $y1 First-Y.
 * @param  integer $x2 Second-X.
 * @param  integer $y2 Second-Y.
 * @param  integer $col Colour number.
 */
function imagefilledrectangle($image, $x1, $y1, $x2, $y2, $col)
{
}

/**
 * Draw a rectangle.
 *
 * @param  resource $image The image involved.
 * @param  integer $x1 First-X.
 * @param  integer $y1 First-Y.
 * @param  integer $x2 Second-X.
 * @param  integer $y2 Second-Y.
 * @param  integer $col Colour number.
 */
function imagerectangle($image, $x1, $y1, $x2, $y2, $col)
{
}

/**
 * Flood fill to specific color.
 *
 * @param  resource $image The image involved.
 * @param  integer $x Origin X.
 * @param  integer $y Origin Y.
 * @param  integer $border Border colour number.
 * @param  integer $color Fill colour number.
 */
function imagefilltoborder($image, $x, $y, $border, $color)
{
}

/**
 * Apply a gamma correction to a GD image.
 *
 * @param  resource $image The image involved.
 * @param  float $in Input gamma.
 * @param  float $out Output gamma.
 */
function imagegammacorrect($image, $in, $out)
{
}

/**
 * Enable or disable interlace / progressive-save.
 *
 * @param  resource $image The image involved.
 * @param  BINARY $interlace On/Off.
 * @return boolean Whether interlace is set.
 */
function imageinterlace($image, $interlace)
{
    return true;
}

/**
 * Load a new font.
 *
 * @param  PATH $file File.
 * @return ~integer Font code (false: error).
 */
function imageloadfont($file)
{
    return 0;
}

/**
 * Copy the palette from one image to another.
 *
 * @param  resource $destination The image the palette is from.
 * @param  resource $source The image the palette is to.
 */
function imagepalettecopy($destination, $source)
{
}

/**
 * Set the brush image for line drawing.
 *
 * @param  resource $image The image involved.
 * @param  resource $brush The brush image.
 * @return boolean Success status.
 */
function imagesetbrush($image, $brush)
{
    return true;
}

/**
 * Set the style for line drawing.
 *
 * @param  resource $image The image involved.
 * @param  integer $style Style number (IMG_COLOR_STYLED or IMG_COLOR_STYLEDBRUSHED).
 */
function imagesetstyle($image, $style)
{
}

/**
 * Set the thickness for line drawing.
 *
 * @param  resource $image The image involved.
 * @param  integer $thickness Thickness in pixels.
 * @return boolean Success status.
 */
function imagesetthickness($image, $thickness)
{
    return true;
}

/**
 * Set the tile image for filling.
 *
 * @param  resource $image The image involved.
 * @param  resource $tile The tile image.
 * @return boolean Success status.
 */
function imagesettile($image, $tile)
{
    return true;
}

/**
 * Convert a truecolor image to a palette image.
 *
 * @param  resource $image The image involved.
 * @param  boolean $dither Whether to use dithering.
 * @param  integer $ncolors The maximum number of colors that should be retained in the palette.
 */
function imagetruecolortopalette($image, $dither, $ncolors)
{
}

/**
 * Draw a character vertically.
 *
 * @param  resource $image The image involved.
 * @param  integer $font Font number.
 * @param  integer $x X.
 * @param  integer $y Y.
 * @param  string $c What to write.
 * @param  integer $color Colour number.
 */
function imagecharup($image, $font, $x, $y, $c, $color)
{
}

/**
 * Get the index of the closest color to the specified color.
 *
 * @param  resource $image The image involved.
 * @param  integer $red Red.
 * @param  integer $green Green.
 * @param  integer $blue Blue.
 * @return integer Colour number.
 */
function imagecolorclosest($image, $red, $green, $blue)
{
    return 0;
}

/**
 * Get the index of the closest color to the specified color + alpha.
 *
 * @param  resource $image The image involved.
 * @param  integer $red Red.
 * @param  integer $green Green.
 * @param  integer $blue Blue.
 * @param  integer $alpha Alpha.
 * @return integer Colour number.
 */
function imagecolorclosestalpha($image, $red, $green, $blue, $alpha)
{
    return 0;
}

/**
 * Get the index of the color which has the hue, white and blackness nearest to the given color .
 *
 * @param  resource $image The image involved.
 * @param  integer $red Red.
 * @param  integer $green Green.
 * @param  integer $blue Blue.
 * @return integer Colour number.
 */
function imagecolorclosesthwb($image, $red, $green, $blue)
{
    return 0;
}

/**
 * De-allocate a color for an image.
 *
 * @param  resource $image The image involved.
 * @param  integer $colour Colour number.
 * @return boolean Success status.
 */
function imagecolordeallocate($image, $colour)
{
    return true;
}

/**
 * Get the index of the specified color.
 *
 * @param  resource $image The image involved.
 * @param  integer $red Red.
 * @param  integer $green Green.
 * @param  integer $blue Blue.
 * @return integer Colour number.
 */
function imagecolorexact($image, $red, $green, $blue)
{
    return 0;
}

/**
 * Get the index of the specified color + alpha.
 *
 * @param  resource $image The image involved.
 * @param  integer $red Red.
 * @param  integer $green Green.
 * @param  integer $blue Blue.
 * @param  integer $alpha Alpha.
 * @return ~integer Colour number (false: error).
 */
function imagecolorexactalpha($image, $red, $green, $blue, $alpha)
{
    return 0;
}

/**
 * Get the index of the specified color or its closest possible alternative.
 *
 * @param  resource $image The image involved.
 * @param  integer $red Red.
 * @param  integer $green Green.
 * @param  integer $blue Blue.
 * @return integer Colour number.
 */
function imagecolorresolve($image, $red, $green, $blue)
{
    return 0;
}

/**
 * Get the index of the specified color + alpha or its closest possible alternative.
 *
 * @param  resource $image The image involved.
 * @param  integer $red Red.
 * @param  integer $green Green.
 * @param  integer $blue Blue.
 * @param  integer $alpha Alpha.
 * @return ~integer Colour number (false: error).
 */
function imagecolorresolvealpha($image, $red, $green, $blue, $alpha)
{
    return 0;
}

/**
 * Set the color for the specified palette index.
 *
 * @param  resource $image The image involved.
 * @param  integer $red Red.
 * @param  integer $green Green.
 * @param  integer $blue Blue.
 */
function imagecolorset($image, $red, $green, $blue)
{
}

/**
 * Find out the number of colors in an image's palette.
 *
 * @param  resource $image The image involved.
 * @return integer Total number of colours.
 */
function imagecolorstotal($image)
{
    return 0;
}

/**
 * Copy and merge part of an image.
 *
 * @param  resource $dst_im Destination image handle.
 * @param  resource $src_im Source image handle.
 * @param  integer $dst_x Destination X-ordinate.
 * @param  integer $dst_y Destination Y-ordinate.
 * @param  integer $src_x Source X-ordinate.
 * @param  integer $src_y Source Y-ordinate.
 * @param  integer $src_w Width to copy.
 * @param  integer $src_h Height to copy.
 * @param  integer $pct Opacity value.
 */
function imagecopymerge($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
}

/**
 * Join array elements with a string.
 *
 * @param  string $glue The glue component (becomes a deliminator).
 * @param  array $pieces The pieces to join.
 * @return string The joined string.
 */
function implode($glue, $pieces)
{
    return '';
}

/**
 * Checks if a value exists in an array.
 *
 * @param  mixed $needle Needle.
 * @param  array $haystack Haystack.
 * @param  boolean $strict Use strict type checking.
 * @return boolean Whether the value exists in the array.
 */
function in_array($needle, $haystack, $strict = false)
{
    return false;
}

/**
 * Include and evaluate the specified file.
 *
 * @param  PATH $filename The filename of the file to include.
 * @return mixed Success status or returned value.
 */
function include($filename)
{
    return false;
}

/**
 * Include and evaluate the specified file, but only if it has not already been included.
 *
 * @param  PATH $filename The filename of the file to include.
 * @return mixed Success status or returned value.
 */
function include_once($filename)
{
    return false;
}

/**
 * Gets the value of a configuration option. Note: On Phalanger any unknown config options will produce a warning if fetched.
 *
 * @param  string $varname Config option.
 * @return ~mixed Value of option (empty: no such config option, or an empty value) (false: ditto).
 */
function ini_get($varname)
{
    return '';
}

/**
 * Sets the value of a configuration option.
 *
 * @param  string $var Config option.
 * @param  string $value New value of option.
 * @return ~string Old value of option (false: error).
 */
function ini_set($var, $value)
{
    return '';
}

/**
 * Get integer value of a variable.
 *
 * @param  mixed $var Integer, but in some other form (usually string).
 * @param  integer $base The base.
 * @return integer The integer, extracted.
 */
function intval($var, $base = 10)
{
    return 0;
}

/**
 * Whether the object is of this class or has this class as one of its parents.
 *
 * @param  object $object The object.
 * @param  string $class_name The class name.
 * @return boolean Whether it is.
 */
function is_a($object, $class_name)
{
    return false;
}

/**
 * Finds whether a variable is an array.
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it is.
 */
function is_array($var)
{
    return false;
}

/**
 * Finds whether a variable is a boolean.
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it is.
 */
function is_bool($var)
{
    return false;
}

/**
 * Finds whether a path is to a directory.
 *
 * @param  PATH $path The path to check.
 * @return boolean Whether it is.
 */
function is_dir($path)
{
    return false;
}

/**
 * Finds whether a path is to a file.
 *
 * @param  PATH $path The path to check.
 * @return boolean Whether it is.
 */
function is_file($path)
{
    return false;
}

/**
 * Finds whether a path is to a symbolic link.
 *
 * @param  PATH $path The path to check.
 * @return boolean Whether it is.
 */
function is_link($path)
{
    return false;
}

/**
 * Finds whether a variable is a float.
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it is.
 */
function is_float($var)
{
    return false;
}

/**
 * Finds whether a variable is an integer.
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it is.
 */
function is_integer($var)
{
    return false;
}

/**
 * Finds whether a variable holds a callable function reference.
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it does.
 */
function is_callable($var)
{
    return false;
}

/**
 * Finds whether a variable is null.
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it is.
 */
function is_null($var)
{
    return false;
}

/**
 * Finds whether a variable is numeric (e.g. a numeric string, or a pure integer).
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it is.
 */
function is_numeric($var)
{
    return false;
}

/**
 * Finds whether a variable is an object.
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it is.
 */
function is_object($var)
{
    return false;
}

/**
 * Finds whether a path is to an actual readable file.
 *
 * @param  PATH $path The path to check.
 * @return boolean Whether it is.
 */
function is_readable($path)
{
    return false;
}

/**
 * Finds whether a variable is a resource.
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it is.
 */
function is_resource($var)
{
    return false;
}

/**
 * Finds whether a variable is a string.
 *
 * @param  mixed $var What to check.
 * @return boolean Whether it is.
 */
function is_string($var)
{
    return false;
}

/**
 * Finds whether a path is to an actual uploaded file.
 *
 * @param  PATH $path The path to check.
 * @return boolean Whether it is.
 */
function is_uploaded_file($path)
{
    return false;
}

/**
 * Finds whether a path is to an actual writeable file.
 *
 * @param  PATH $path The path to check.
 * @return boolean Whether it is.
 */
function is_writable($path)
{
    return false;
}

/**
 * Finds whether a variable exists / is not null / is an actually derefereable array element. Do not use this for the null case, and otherwise ONLY when for efficiency reasons.
 *
 * @param  mixed $path The variable.
 * @return boolean Whether it is set.
 */
function isset(&$path)
{
    return false;
}

/**
 * Sort an array by key in reverse order.
 *
 * @param  array $array The array to sort.
 */
function krsort(&$array)
{
}

/**
 * Sort an array by key.
 *
 * @param  array $array The array to sort.
 * @param  integer $sort_flags Flags.
 */
function ksort(&$array, $sort_flags = 0)
{
}

/**
 * Get numeric formatting information.
 *
 * @return array Array of formatting information.
 */
function localeconv()
{
    return array();
}

/**
 * Strip whitespace from the beginning of a string.
 *
 * @param  string $string The string to trim from.
 * @param  string $characters Characters to trim.
 * @return string The trimmed string.
 */
function ltrim($string, $characters = " \t\n\r\0\x0B")
{
    return '';
}

/**
 * Send an e-mail.
 *
 * @param  string $to The TO address.
 * @param  string $subject The subject.
 * @param  string $message The message.
 * @param  string $additional_headers Additional headers.
 * @param  string $additional_flags Additional stuff to send to sendmail executable (if appropriate, only works when safe mode is off).
 * @return boolean Success status.
 */
function mail($to, $subject, $message, $additional_headers = '', $additional_flags = '')
{
    return false;
}

/**
 * Find highest value between arguments.
 *
 * @param  mixed $arg1 First argument (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg2 Second argument (null: no second argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg3 Third argument (null: no third argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg4 Fourth argument (null: no fourth argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg5 Fifth argument (null: no fith argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg6 Sixth argument (null: no sixth argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg7 Seventh argument (null: no seventh argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg8 Eighth argument (null: no eighth argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg9 Ninth argument (null: no ninth argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg10 Tenth argument (null: no tenth argument) (if array, then each treated as a separate parameter).
 * @return mixed The highest valued argument.
 */
function max($arg1, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null, $arg6 = null, $arg7 = null, $arg8 = null, $arg9 = null, $arg10 = null)
{
    return 0;
}

/**
 * Calculate the md5 hash of a string.
 *
 * @param  string $str String to hash.
 * @return string Hashed result.
 */
function md5($str)
{
    return '';
}

/**
 * Checks if the class method exists.
 *
 * @param  object $object Object of the class we want to check.
 * @param  string $method_name The method name.
 * @return boolean Whether the class method exists.
 */
function method_exists($object, $method_name)
{
    return false;
}

/**
 * Return current UNIX timestamp with microseconds.
 *
 * @param  boolean $as_float Whether to return a float result. ALWAYS PASS THIS IN AS *FALSE* - FOR COMPATIBILITY WITH OLD VERSIONS OF PHP THAT DO NOT HAVE IT, WHILST PHP 6 DEFAULTS IT TO TRUE.
 * @return mixed Micro-time.
 */
function microtime($as_float)
{
    return '';
}

/**
 * Find lowest value between arguments.
 *
 * @param  mixed $arg1 First argument (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg2 Second argument (null: no second argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg3 Third argument (null: no third argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg4 Fourth argument (null: no fourth argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg5 Fifth argument (null: no fith argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg6 Sixth argument (null: no sixth argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg7 Seventh argument (null: no seventh argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg8 Eighth argument (null: no eighth argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg9 Ninth argument (null: no ninth argument) (if array, then each treated as a separate parameter).
 * @param  ?mixed $arg10 Tenth argument (null: no tenth argument) (if array, then each treated as a separate parameter).
 * @return mixed The lowest valued argument.
 */
function min($arg1, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null, $arg6 = null, $arg7 = null, $arg8 = null, $arg9 = null, $arg10 = null)
{
    return 0;
}

/**
 * Makes a directory. {{creates-file}}
 *
 * @param  PATH $path The path to the directory to make.
 * @param  integer $mode The mode (e.g. 0777).
 * @param  boolean $recursive Whether to do recursively.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @return boolean Success status.
 */
function mkdir($path, $mode, $recursive = false, $context = null)
{
    return false;
}

/**
 * Get UNIX timestamp for a componentialised date.
 *
 * @param  integer $hour The hour.
 * @param  ?integer $minute The minute (null: now).
 * @param  ?integer $second The second (null: now).
 * @param  ?integer $month The month (null: now).
 * @param  ?integer $day The day (null: now).
 * @param  ?integer $year The year (null: now).
 * @return TIME The timestamp.
 */
function mktime($hour, $minute = null, $second = null, $month = null, $day = null, $year = null)
{
    return 0;
}

/**
 * Moves an uploaded file to a new location. {{creates-file}}
 *
 * @param  PATH $filename Filename to move (taken from tmpname element of $_FILES list entry).
 * @param  PATH $destination Path to move file to (preferably containing filename component).
 * @return boolean The success status.
 */
function move_uploaded_file($filename, $destination)
{
    return false;
}

/**
 * Get largest possible random value.
 *
 * @return integer The value.
 */
function mt_getrandmax()
{
    return 0;
}

/**
 * Generate a better random value.
 *
 * @param  integer $min Minimum value.
 * @param  integer $max Maximum value.
 * @return integer Random value.
 */
function mt_rand($min, $max)
{
    return 0;
}

/**
 * Seed the better random number generator.
 *
 * @param  integer $seed The seed.
 */
function mt_srand($seed)
{
}

/**
 * Format a number with grouped thousands.
 *
 * @param  mixed $number The number to format [integer or float] (technically always float because it could be larger than an integer, but that's ugly).
 * @param  integer $decimals The number of decimal fraction digits to show.
 * @param  string $dec_point The string to use as a decimal point.
 * @param  string $thousands_sep The string to separate groups of 1000's with.
 * @return string The string formatted number.
 */
function number_format($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',')
{
    return '';
}

/**
 * Turn on output buffering.
 *
 * @return boolean Success status.
 */
function ob_start()
{
    return false;
}

/**
 * Clean (erase) the output buffer and turn off output buffering.
 *
 * @return boolean Success status (could fail if there is no buffer).
 */
function ob_end_clean()
{
    return false;
}

/**
 * Flush (output and erase) the output buffer and turn off output buffering.
 *
 * @return boolean Success status (could fail if there is no buffer).
 */
function ob_end_flush()
{
    return false;
}

/**
 * Return the contents of the output buffer .
 *
 * @return ~string The buffer contents (false: no buffer).
 */
function ob_get_contents()
{
    return '';
}

/**
 * Flush (send) the output buffer.
 */
function ob_flush()
{
}

/**
 * Get current buffer contents and delete current output buffer.
 *
 * @return ~string Contents of the buffer (false: no buffer was open).
 */
function ob_get_clean()
{
    return '';
}

/**
 * Empty the output buffer.
 */
function ob_clean()
{
}

/**
 * ob_start callback function to gzip output buffer.
 *
 * @param  string $buffer Input string.
 * @param  integer $mode Irrelevant (we don't use this function directly anyway).
 * @return string Filtered version.
 */
function ob_gzhandler($buffer, $mode)
{
    return '';
}

/**
 * Return the length of the output buffer.
 *
 * @return ~integer Output buffer length (false: error).
 */
function ob_get_length()
{
    return 0;
}

/**
 * Return the nesting level of the output buffering mechanism.
 *
 * @return integer Nesting level.
 */
function ob_get_level()
{
    return 0;
}

/**
 * Turn implicit flush on/off .
 *
 * @param  integer $flag Flag (1 for on, 0 for off).
 */
function ob_implicit_flush($flag)
{
}

/**
 * Output something.
 *
 * @param  string $octal_string The string to output.
 * @return integer The number '1', always.
 */
function print($octal_string)
{
    return 1;
}

/**
 * String representation of octal to decimal.
 *
 * @param  string $octal_string The string representation.
 * @return integer The integer ('decimal' form, although truly stored in binary).
 */
function octdec($octal_string)
{
    return 0;
}

/**
 * Open a directory for analysis.
 *
 * @param  PATH $path The path to the directory to open.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @return ~resource The directory handle (false: error).
 */
function opendir($path, $context = null)
{
    return array();
}

/**
 * Return ASCII value of character.
 *
 * @param  string $string String of length 1, containing character to find ASCII value of.
 * @return integer The ASCII value.
 */
function ord($string)
{
    return 0;
}

/**
 * Pack data into binary string.
 *
 * @param  string $format The formatting string.
 * @param  ?mixed $arg1 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg2 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg3 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg4 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg5 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg6 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg7 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg8 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg9 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg10 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg11 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg12 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg13 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg14 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg15 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg16 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg17 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg18 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg19 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg20 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg21 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg22 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg23 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg24 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg25 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg26 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg27 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg28 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg29 Argument that binds to the formatting string (null: none).
 * @param  ?mixed $arg30 Argument that binds to the formatting string (null: none).
 * @return string The binary string.
 */
function pack($format, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null, $arg6 = null, $arg7 = null, $arg8 = null, $arg9 = null, $arg10 = null, $arg11 = null, $arg12 = null, $arg13 = null, $arg14 = null, $arg15 = null, $arg16 = null, $arg17 = null, $arg18 = null, $arg19 = null, $arg20 = null, $arg21 = null, $arg22 = null, $arg23 = null, $arg24 = null, $arg25 = null, $arg26 = null, $arg27 = null, $arg28 = null, $arg29 = null, $arg30 = null)
{
    return '';
}

/**
 * Parse a URL and return its components.
 *
 * @param  string $url The URL to parse.
 * @param  integer $component The component to get (-1 get all in an array).
 * @return ?~mixed A map of details about the URL (null: $component was provided but was not found) (false: URL cannot be parsed)
 */
function parse_url($url, $component = -1)
{
    return array();
}

/**
 * Returns information about a file path.
 *
 * @param  PATH $path The path to parse.
 * @return ~array A map of details about the path (false: error).
 */
function pathinfo($path)
{
    return array();
}

/**
 * Perform a regular expression match.
 *
 * @param  string $pattern The pattern.
 * @param  string $subject The subject string.
 * @param  ?array $matches Where matches will be put (note that it is a list of maps, except the arrays are turned inside out) (null: do not store matches). Note that this is actually passed by reference, but is also optional. (null: don't gather)
 * @param  integer $flags Either 0, or PREG_OFFSET_CAPTURE.
 * @param  integer $offset Offset to start from. Usually use with 'A' modifier to anchor it (using '^' in the pattern will not work)
 * @return ~integer The number of matches (false: error).
 */
function preg_match($pattern, $subject, &$matches = null, $flags = 0, $offset = 0)
{
    return 0;
}

/**
 * Array entries that match the pattern.
 *
 * @param  string $pattern The pattern.
 * @param  array $subject The subject strings.
 * @param  integer $flags Either 0, or PREG_GREP_INVERT.
 * @return array Matches.
 */
function preg_grep($pattern, $subject, $flags = 0)
{
    return array();
}

/**
 * Perform a global regular expression match.
 *
 * @param  string $pattern The pattern.
 * @param  string $subject The subject string.
 * @param  ?array $matches Where matches will be put (note that it is a list of maps, except the arrays are turned inside out). Note that this is actually passed by reference, but is also optional. (null: don't gather)
 * @param  integer $flags Either 0, or PREG_OFFSET_CAPTURE.
 * @return ~integer The number of matches (false: error).
 */
function preg_match_all($pattern, $subject, &$matches, $flags = 0)
{
    return 0;
}

/**
 * Perform a regular expression search and replace.
 *
 * @param  mixed $pattern The pattern (string or array).
 * @param  mixed $replacement The replacement string (string or array).
 * @param  string $subject The subject string.
 * @param  integer $limit The limit of replacements (-1: no limit).
 * @return ~string The string with replacements made (false: error).
 */
function preg_replace($pattern, $replacement, $subject, $limit = -1)
{
    return '';
}

/**
 * Perform a regular expression search and replace using a callback.
 *
 * @param  string $pattern The pattern.
 * @param  mixed $callback The callback.
 * @param  string $subject The subject string.
 * @param  integer $limit The limit of replacements (-1: no limit).
 * @return ~string The string with replacements made (false: error).
 */
function preg_replace_callback($pattern, $callback, $subject, $limit = -1)
{
    return '';
}

/**
 * Split string by a regular expression.
 *
 * @param  string $pattern The pattern.
 * @param  string $subject The subject.
 * @param  integer $max_splits The maximum number of splits to make (-1: no limit).
 * @param  ?integer $mode The special mode (null: none).
 * @return array The array due to splitting.
 */
function preg_split($pattern, $subject, $max_splits = -1, $mode = null)
{
    return array();
}

/**
 * Prints human-readable information about a variable.
 *
 * @param  mixed $data The variable.
 */
function print_r($data)
{
}

/**
 * Decode URL-encoded strings.
 *
 * @param  string $str The string to decode.
 * @return string Decoded string.
 */
function rawurldecode($str)
{
    return '';
}

/**
 * Encode URL-encoded strings. Used for everything *except* GET-parameter encoding.
 *
 * @param  string $str The string to encode.
 * @return string Encoded string.
 */
function rawurlencode($str)
{
    return '';
}

/**
 * Read entry from directory handle.
 *
 * @param  resource $dir_handle Handle.
 * @return ~string Next filename (false: reached end already).
 */
function readdir($dir_handle)
{
    return '';
}

/**
 * Returns canonicalized absolute pathname.
 *
 * @param  PATH $path (Possibly) perceived path.
 * @return PATH Actual path.
 */
function realpath($path)
{
    return '';
}

/**
 * Register a function for execution on shutdown.
 *
 * @param  mixed $callback Callback.
 * @param  ?mixed $parama Parameter (null: not used).
 * @param  ?mixed $paramb Parameter (null: not used).
 * @param  ?mixed $paramc Parameter (null: not used).
 * @param  ?mixed $paramd Parameter (null: not used).
 * @param  ?mixed $parame Parameter (null: not used).
 * @param  ?mixed $paramf Parameter (null: not used).
 * @param  ?mixed $paramg Parameter (null: not used).
 * @param  ?mixed $paramh Parameter (null: not used).
 * @param  ?mixed $parami Parameter (null: not used).
 * @param  ?mixed $paramj Parameter (null: not used).
 * @param  ?mixed $paramk Parameter (null: not used).
 * @param  ?mixed $paraml Parameter (null: not used).
 * @param  ?mixed $paramm Parameter (null: not used).
 * @param  ?mixed $paramn Parameter (null: not used).
 * @param  ?mixed $paramo Parameter (null: not used).
 * @param  ?mixed $paramp Parameter (null: not used).
 * @param  ?mixed $paramq Parameter (null: not used).
 */
function register_shutdown_function($callback, $parama = null, $paramb = null, $paramc = null, $paramd = null, $parame = null, $paramf = null, $paramg = null, $paramh = null, $parami = null, $paramj = null, $paramk = null, $paraml = null, $paramm = null, $paramn = null, $paramo = null, $paramp = null, $paramq = null)
{
}

/**
 * Renames a file.
 *
 * @param  PATH $oldname Old name.
 * @param  PATH $newname New name.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @return boolean Success status.
 */
function rename($oldname, $newname, $context = null)
{
    return false;
}

/**
 * Require and evaluate the specified file (dies with error if it can not).
 *
 * @param  PATH $filename The filename of the file to require.
 * @return mixed Success status or returned value.
 */
function require($filename)
{
    return false;
}

/**
 * Require and evaluate the specified file (dies with error if it can not), but only if it has not been loaded already.
 *
 * @param  PATH $filename The filename of the file to require.
 * @return mixed Success status or returned value.
 */
function require_once($filename)
{
    return false;
}

/**
 * Set the internal pointer of an array to its first element.
 *
 * @param  array $array The array.
 * @return mixed The value of the first element.
 */
function reset($array)
{
    return 0;
}

/**
 * Removes directory.
 *
 * @param  PATH $dirname Directory path.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @return boolean Success status.
 */
function rmdir($dirname, $context = null)
{
    return false;
}

/**
 * Rounds a float.
 *
 * @param  float $val Value to round.
 * @param  integer $precision Number of decimal points of precision required (-ve allowed).
 * @return float Rounded value.
 */
function round($val, $precision = 0)
{
    return 0.0;
}

/**
 * Sort an array in reverse order.
 *
 * @param  array $array The array to sort.
 */
function rsort(&$array)
{
}

/**
 * Strip whitespace from the end of a string.
 *
 * @param  string $str String to trim from.
 * @param  string $characters Characters to trim.
 * @return string Trimmed string.
 */
function rtrim($str, $characters = " \t\n\r\0\x0B")
{
    return '';
}

/**
 * Generates a storable representation of a value.
 *
 * @param  mixed $value Whatever is to be serialised .
 * @return string The serialisation.
 */
function serialize($value)
{
    return '';
}

/**
 * Sets a user-defined error handler function.
 *
 * @param  mixed $error_handler The call back.
 * @return mixed The previously defined error handler.
 */
function set_error_handler($error_handler)
{
    return '';
}

/**
 * Sets a user-defined exception handler function.
 *
 * @param  mixed $exception_handler The call back.
 * @return mixed The previously defined error handler.
 */
function set_exception_handler($exception_handler)
{
    return '';
}

/**
 * Send a cookie.
 *
 * @sets_output_state
 *
 * @param  string $name The name.
 * @param  ?string $value The value (null: unset existing cookie).
 * @param  ?integer $expire Expiration timestamp (null: session cookie).
 * @param  ?string $path Path (null: current URL path).
 * @param  ?string $domain Domain (null: current URL domain).
 * @param  boolean $secure Whether the cookie is only for HTTPS.
 * @param  boolean $httponly Whether the cookie can only be accessed through the HTTP protocol.
 * @return ?boolean Success status (fails if output already started) (null: failed also).
 */
function setcookie($name, $value = null, $expire = null, $path = null, $domain = null, $secure = false, $httponly = false)
{
    return false;
}

/**
 * Set locale information.
 *
 * @param  integer $category The locale category (LC_ALL, LC_COLLATE, LC_CTYPE, LC_MONETARY, LC_NUMERIC, LC_TIME).
 * @param  mixed $locale The locale (Some PHP versions require an array, and some a string with multiple calls).
 * @return ~string The set locale (false: error).
 */
function setlocale($category, $locale)
{
    return '';
}

/**
 * Calculate the sha1 hash of a string.
 *
 * @param  string $str The string to hash.
 * @return string The hash of the string.
 */
function sha1($str)
{
    return '';
}

/**
 * Calculate the sine of an angle.
 *
 * @param  float $arg The angle.
 * @return float The sine of the angle.
 */
function sin($arg)
{
    return 0.0;
}

/**
 * Sort an array.
 *
 * @param  array $array The array.
 * @param  integer $sort_flags Flags.
 */
function sort(&$array, $sort_flags = 0)
{
}

/**
 * Return a formatted string.
 *
 * @param  string $format Formatting string.
 * @param  ?mixed $arg1 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg2 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg3 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg4 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg5 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg6 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg7 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg8 Argument for the formatting string (null: none required).
 * @return string Formatted string.
 */
function sprintf($format, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null, $arg6 = null, $arg7 = null, $arg8 = null)
{
    return '';
}

/**
 * Print a formatted string into a file.
 *
 * @param  resource $handle File to write to.
 * @param  string $format Formatting string.
 * @param  ?mixed $arg1 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg2 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg3 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg4 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg5 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg6 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg7 Argument for the formatting string (null: none required).
 * @param  ?mixed $arg8 Argument for the formatting string (null: none required).
 * @return string Formatted string.
 */
function fprintf($handle, $format, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null, $arg6 = null, $arg7 = null, $arg8 = null)
{
    return '';
}

/**
 * Seed the random number generator.
 *
 * @param  integer $seed The seed.
 */
function srand($seed)
{
}

/**
 * Pad a string to a certain length with another string.
 *
 * @param  string $input The subject.
 * @param  integer $pad_length The length to pad up to.
 * @param  string $pad_string What we are padding with.
 * @param  integer $pad_type The padding type (STR_PAD_RIGHT, STR_PAD_LEFT, STR_PAD_BOTH).
 * @return string The result.
 */
function str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT)
{
    return '';
}

/**
 * Repeat a string.
 *
 * @param  string $input The string to repeat.
 * @param  integer $multiplier How many times to repeat the string.
 * @return string The result.
 */
function str_repeat($input, $multiplier)
{
    return '';
}

/**
 * Replace all occurrences of the search string with the replacement string.
 *
 * @param  mixed $search What's being replaced (string or array).
 * @param  mixed $replace What's being replaced with (string or array).
 * @param  mixed $subject Subject (string or array).
 * @return mixed Result (string or array).
 */
function str_replace($search, $replace, $subject)
{
    return '';
}

/**
 * Replace all occurrences of the search string with the replacement string (case insensitive).
 *
 * @param  mixed $search What's being replaced (string or array).
 * @param  mixed $replace What's being replaced with (string or array).
 * @param  mixed $subject Subject (string or array).
 * @return mixed Result (string or array).
 */
function str_ireplace($search, $replace, $subject)
{
    return '';
}

/**
 * Binary safe string comparison.
 *
 * @param  string $str1 The first string.
 * @param  string $str2 The second string.
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2.
 */
function strcmp($str1, $str2)
{
    return 0;
}

/**
 * Format a local time/date according to locale settings (uses alternative formatting to 'date' function).
 *
 * @param  string $format The formatting string.
 * @param  ?TIME $timestamp The timestamp (null: now).
 * @return string The formatted string.
 */
function strftime($format, $timestamp = null)
{
    return '';
}

/**
 * Strip HTML and PHP tags from a string.
 *
 * @param  string $str Subject.
 * @param  string $allowable_tags Comma-separated list of allowable tags.
 * @return string Result.
 */
function strip_tags($str, $allowable_tags = '')
{
    return '';
}

/**
 * Quote string for encapsulation in a written string data type.
 *
 * @param  string $str Unslashed string.
 * @return string Slashed string.
 */
function addslashes($str)
{
    return '';
}

/**
 * Un-quote string slashed with addslashes.
 *
 * @param  string $str Slashed string.
 * @return string Unslashed string.
 */
function stripslashes($str)
{
    return '';
}

/**
 * Get string length.
 *
 * @param  string $str The string to get the length of.
 * @return integer The string length.
 */
function strlen($str)
{
    return 0;
}

// FUDGE: strpos can give "Offset not contained in string" error. We'd ideally have it in our catch errors list (code_quality.php) but it's unrealistic to catch all strpos errors.

/**
 * Find position of first occurrence of a string.
 *
 * @param  string $haystack Haystack.
 * @param  string $needle Needle.
 * @param  integer $offset Offset to search from.
 * @return ~integer The offset it is found at (false: not found).
 */
function strpos($haystack, $needle, $offset = 0)
{
    return 0;
}

/**
 * Find position of first occurrence of a string (case-insensitive).
 *
 * @param  string $haystack Haystack.
 * @param  string $needle Needle.
 * @param  integer $offset Offset to search from.
 * @return ~integer The offset it is found at (false: not found).
 */
function stripos($haystack, $needle, $offset = 0)
{
    return 0;
}

/**
 * Find position of last occurrence of a char in a string.
 *
 * @param  string $haystack Haystack.
 * @param  string $needle Needle.
 * @return ~integer The offset it is found at (false: not found).
 */
function strrpos($haystack, $needle)
{
    return 0;
}

/**
 * Find first occurrence of a string.
 *
 * @param  string $haystack Haystack.
 * @param  string $needle Needle.
 * @return ~string The answer (false: does not occur).
 */
function strstr($haystack, $needle)
{
    return '';
}

/**
 * Tokenize string.
 *
 * @param  string $subject String to tokenise. EXCEPT if $deliminators=null, then this has actual deliminators.
 * @param  ?string $deliminators Deliminators (null: continue with previous tokenisation).
 * @return ~string Next token (false: could not return a token, no more tokens to return).
 */
function strtok($subject, $deliminators = null)
{
    return '';
}

/**
 * Make a string lowercase.
 *
 * @param  string $str Subject.
 * @return string Result.
 */
function strtolower($str)
{
    return '';
}

/**
 * Parse about any English textual datetime description into a UNIX timestamp.
 *
 * @param  string $time The subject.
 * @param  ?TIME $now The timestamp to find times relative to (null: now).
 * @return TIME The timetamp (-1: failed).
 */
function strtotime($time, $now = null)
{
    return 0;
}

/**
 * Make a string uppercase.
 *
 * @param  string $str Subject.
 * @return string Result.
 */
function strtoupper($str)
{
    return '';
}

/**
 * Translate certain characters.
 *
 * @param  string $string Subject.
 * @param  mixed $replace_pairs Map of translations to do OR from string.
 * @param  ?mixed $to To string (null: previous parameter was a map).
 * @return string Result.
 */
function strtr($string, $replace_pairs, $to = null)
{
    return '';
}

/**
 * Get string value of a variable.
 *
 * @param  mixed $var The variable.
 * @return string String value of the variable.
 */
function strval($var)
{
    return '';
}

/**
 * Return part of a string.
 *
 * @param  string $string The subject.
 * @param  integer $start The start position.
 * @param  ?integer $length The length to extract (null: all remaining).
 * @return ~string String part (false: $start was over the end of the string).
 */
function substr($string, $start, $length = null)
{
    return '';
}

/**
 * Count the number of substring occurrences.
 *
 * @param  string $haystack The subject.
 * @param  string $needle The substring to search for in the subject.
 * @param  integer $offset Offset.
 * @param  ?integer $maxlen Maximum length (null: no limit).
 * @return integer The number of times substring occurs in the subject.
 */
function substr_count($haystack, $needle, $offset = 0, $maxlen = null)
{
    return 0;
}

/**
 * Return current UNIX timestamp.
 *
 * @return TIME The timestamp.
 */
function time()
{
    return 0;
}

/**
 * Strip whitespace from both ends of a string.
 *
 * @param  string $str String to trim from.
 * @param  string $characters Characters to trim.
 * @return string Trimmed string.
 */
function trim($str, $characters = " \t\n\r\0\x0B")
{
    return '';
}

/**
 * Generates a user-level error/warning/notice message.
 *
 * @param  string $error_msg The error message.
 * @param  integer $error_type The PHP error type constant.
 */
function trigger_error($error_msg, $error_type)
{
}

/**
 * Sort an array with a user-defined comparison function and maintain index association.
 *
 * @param  array $array The array.
 * @param  mixed $cmp_function Comparison function.
 */
function uasort(&$array, $cmp_function)
{
}

/**
 * Make a string's first character uppercase.
 *
 * @param  string $str Subject.
 * @return string Result.
 */
function ucfirst($str)
{
    return '';
}

/**
 * Uppercase the first character of each word in a string.
 *
 * @param  string $str Subject.
 * @return string Result.
 */
function ucwords($str)
{
    return '';
}

/**
 * Sort an array by keys using a user-defined comparison function.
 *
 * @param  array $array The array.
 * @param  mixed $cmp_function Comparison function.
 */
function uksort(&$array, $cmp_function)
{
}

/**
 * Generate a unique ID.
 *
 * @param  string $prefix Prefix for unique ID.
 * @param  boolean $lcg Whether to add additional "combined LCG" entropy at the end of the return value. Always pass as true, because on some IIS systems the timer resolution will be in seconds.
 * @return string Unique ID.
 */
function uniqid($prefix, $lcg)
{
    return '';
}

/**
 * Deletes a file.
 *
 * @param  PATH $filename The filename.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @return boolean Success status.
 */
function unlink($filename, $context = null)
{
    return false;
}

/**
 * Creates a PHP value from a stored representation.
 *
 * @param  string $str Serialized string.
 * @param  ?array $options Extra options (null: none).
 * @return ~mixed What was originally serialised (false: bad data given, or actually false was serialized).
 */
function unserialize($str, $options = null)
{
    return 0;
}

/**
 * Unset a given variable.
 *
 * @param  mixed $var Unset this.
 */
function unset(&$var)
{
}

/**
 * Decodes URL-encoded string.
 *
 * @param  string $str URL encoded string.
 * @return string Pure string.
 */
function urldecode($str)
{
    return '';
}

/**
 * URL-encodes string. Used for GET-parameter encoding ONLY.
 *
 * @param  string $str The pure string to URL encode.
 * @return string URL encoded string.
 */
function urlencode($str)
{
    return '';
}

/**
 * Sort an array by values using a user-defined comparison function.
 *
 * @param  array $array The array.
 * @param  mixed $cmp_function Comparison function.
 */
function usort(&$array, $cmp_function)
{
}

/**
 * Wraps a string to a given number of characters using a string break character.
 *
 * @param  string $string Subject.
 * @param  integer $width The word wrap position.
 * @param  string $break The string to put at wrap points.
 * @param  boolean $cut Whether to cut up words.
 * @return string Word-wrapped string.
 */
function wordwrap($string, $width = 75, $break = "\n", $cut = false)
{
    return '';
}

/**
 * Arc cosine.
 *
 * @param  float $arg Argument.
 * @return float Angle.
 */
function acos($arg)
{
    return 0.0;
}

/**
 * Pick one or more random entries out of an array.
 *
 * @param  array $input Array to choose from.
 * @param  integer $num_req Number of entries required.
 * @return mixed Random entry, or array of random entries if $num_req!=1.
 */
function array_rand($input, $num_req = 1)
{
    return 0;
}

/**
 * Prepend one or more elements to the beginning of array.
 *
 * @param  array $array Array to prepend to.
 * @param  mixed $var1 Element to prepend.
 * @param  ?mixed $var2 Element to prepend (null: no more - actually pass nothing for this).
 * @param  ?mixed $var3 Element to prepend (null: no more - actually pass nothing for this).
 * @param  ?mixed $var4 Element to prepend (null: no more - actually pass nothing for this).
 * @return integer The new number of elements in the array.
 */
function array_unshift(&$array, $var1, $var2 = null, $var3 = null, $var4 = null)
{
    return 0;
}

/**
 * Arc sine.
 *
 * @param  float $arg Argument.
 * @return float Angle.
 */
function asin($arg)
{
    return 0.0;
}

/**
 * Checks if assertion is FALSE.
 *
 * @param  string $assertion The expression to assert on.
 */
function assert($assertion)
{
}

/**
 * Set/get the various assert flags (and sometimes, options for them).
 *
 * @param  integer $option The option (ASSERT_ACTIVE, ASSERT_WARNING, ASSERT_BAIL, ASSERT_QUIET_EVAL, ASSERT_CALLBACK).
 * @param  ?mixed $value The value for flag (null: N/A).
 * @return ~mixed Old value (false: error).
 */
function assert_options($option, $value = null)
{
    return 0;
}

/**
 * Arc tan.
 *
 * @param  float $num Argument.
 * @return float Angle.
 */
function atan($num)
{
    return 0.0;
}

/**
 * Convert a number between arbitrary bases (string representations).
 *
 * @param  string $number The string representation number to convert.
 * @param  integer $frombase From base.
 * @param  integer $tobase To base.
 * @return string New base representation.
 */
function base_convert($number, $frombase, $tobase)
{
    return '';
}

/**
 * Returns filename component of path.
 *
 * @param  PATH $path Path.
 * @param  string $ext File extension to cut off (blank: none).
 * @return string File name component.
 */
function basename($path, $ext = '')
{
    return '';
}

/**
 * Convert binary data (in string form) into hexadecimal representation.
 *
 * @param  string $str Binary string.
 * @return string Hex string.
 */
function bin2hex($str)
{
    return '';
}

/**
 * Binary (string representation) to decimal (integer).
 *
 * @param  string $binary_string Binary in string form.
 * @return integer Number.
 */
function bindec($binary_string)
{
    return 0;
}

/**
 * Call a user function given with an array of parameters.
 *
 * @param  mixed $callback Callback.
 * @param  array $parameters Parameters.
 * @return mixed Whatever the function returned.
 */
function call_user_func_array($callback, $parameters)
{
    return 0;
}

/**
 * Whether the client has disconnected.
 *
 * @return boolean Whether the client has disconnected.
 */
function connection_aborted()
{
    return false;
}

/**
 * Returns connection status bitfield.
 *
 * @return integer Connection status bitfield.
 */
function connection_status()
{
    return 0;
}

/**
 * Calculates the crc32 polynomial of a string.
 *
 * @param  string $str The string to get the CRC32 of.
 * @return integer The CRC32.
 */
function crc32($str)
{
    return 0;
}

/**
 * Decimal (integer) to binary (string representation).
 *
 * @param  integer $number Decimal.
 * @return string String representation of binary number.
 */
function decbin($number)
{
    return '';
}

/**
 * Determine whether a variable is empty (empty being defined differently for different types).
 *
 * @param  mixed $var Input.
 * @return boolean Whether it is CONSIDERED empty.
 */
function empty($var)
{
    return false;
}

/**
 * Set the internal pointer of an array to its last element.
 *
 * @param  array $array The array.
 * @return mixed Value of the last element.
 */
function end($array)
{
    return 0;
}

/**
 * Flushes the output to a file.
 *
 * @param  resource $handle The file handle to flush.
 * @return boolean Success status.
 */
function fflush($handle)
{
    return false;
}

/**
 * Gets last access time of file.
 *
 * @param  PATH $filename The filename.
 * @return ~TIME Timestamp of last access (false: error).
 */
function fileatime($filename)
{
    return 0;
}

/**
 * Gets last access time of file.
 *
 * @param  PATH $filename The filename.
 * @return ~integer Inode number of the file (false: error).
 */
function fileinode($filename)
{
    return 0;
}

/**
 * Portable advisory file locking.
 *
 * @param  resource $handle File handle.
 * @param  integer $operation Operation (LOCK_SH, LOCK_EX, LOCK_UN).
 * @return boolean Success status.
 */
function flock($handle, $operation)
{
    return false;
}

/**
 * Flush the output buffer.
 */
function flush()
{
}

/**
 * Get the Internet host name corresponding to a given IP address.
 *
 * @param  string $ip_address IP address.
 * @return string Host name OR IP address if failed to look up.
 */
function gethostbyaddr($ip_address)
{
    return '';
}

/**
 * Get the IP address corresponding to a given Internet host name.
 *
 * @param  string $hostname Host name.
 * @return string IP address OR host name if failed to look up.
 */
function gethostbyname($hostname)
{
    return '';
}

/**
 * Get largest possible random value.
 *
 * @return integer Largest possible random value.
 */
function getrandmax()
{
    return 0;
}

/**
 * Get UNIX timestamp for a GMT date.
 *
 * @param  integer $hour The hour.
 * @param  integer $minute The minute.
 * @param  integer $second The second.
 * @param  integer $month The month.
 * @param  integer $day The day.
 * @param  integer $year The year.
 * @return integer The timestamp.
 */
function gmmktime($hour, $minute, $second, $month, $day, $year)
{
    return 0;
}

/**
 * Format a GMT/UTC time/date according to locale settings.
 *
 * @param  string $format The formatting string.
 * @param  ?TIME $timestamp The timestamp (null: now).
 * @return string The formatted string.
 */
function gmstrftime($format, $timestamp = null)
{
    return '';
}

/**
 * Converts a string containing an (IPv4) Internet Protocol dotted address into a proper address.
 *
 * @param  string $ip_address The IP address.
 * @return ~integer The long form (false: cannot perform conversion).
 */
function ip2long($ip_address)
{
    return 0;
}

/**
 * Fetch a key from an associative array.
 *
 * @param  array $array The array.
 * @return mixed The index element of the current array position.
 */
function key($array)
{
    return 0;
}

/**  --> Use similar_text
 * Calculate Levenshtein distance between two strings.
 *
 * @param  string $str1 First string.
 * @param  string $str2 Second string.
 * @return integer Distance.
 */
function levenshtein($str1, $str2)
{
    return 0;
}

/**
 * Natural logarithm.
 *
 * @param  float $arg Number to find log of.
 * @return float Log of given number.
 */
function log($arg)
{
    return 0.0;
}

/**
 * Base-10 logarithm.
 *
 * @param  float $arg Number to find log of.
 * @return float Log of given number.
 */
function log10($arg)
{
    return 0.0;
}

/**
 * Converts an (IPv4) Internet network address into a string in Internet standard dotted format.
 *
 * @param  integer $proper_address The IP address.
 * @return integer The long form.
 */
function long2ip($proper_address)
{
    return 0;
}

/**
 * Calculates the md5 hash of the file identified by the given filename.
 *
 * @param  PATH $filename File name.
 * @return ~string The hash of the file (false: error).
 */
function md5_file($filename)
{
    return '';
}

/**
 * Advance the internal array pointer of an array.
 *
 * @param  array $array The array.
 * @return mixed The array value we're now pointing at.
 */
function next($array)
{
    return 0;
}

/**
 * Get value of PI.
 *
 * @return float PI.
 */
function pi()
{
    return 0.0;
}

/**
 * Exponential expression.
 *
 * @param  mixed $base Base (integer or float).
 * @param  mixed $exp Exponent (integer or float).
 * @return mixed Result (integer or float).
 */
function pow($base, $exp)
{
    return 0.0;
}

/**
 * Quote regular expression characters.
 *
 * @param  string $str The string to escape.
 * @param  string $surround_char Extra character to escape, was used in regular expression to surround it.
 * @return string The escape string.
 */
function preg_quote($str, $surround_char = '/')
{
    return '';
}

/**
 * Rewind the internal array pointer.
 *
 * @param  array $array The array.
 * @return mixed The array value we're now pointing at.
 */
function prev($array)
{
    return 0;
}

/**
 * Converts the radian number to the equivalent number in degrees.
 *
 * @param  float $number The angle in radians.
 * @return float The angle in degrees.
 */
function rad2deg($number)
{
    return 0.0;
}

/**
 * Create a sequence in an array.
 *
 * @param  mixed $from From (integer or character string).
 * @param  mixed $to To (integer or character string).
 * @param  integer $step Step.
 * @return array The sequence.
 */
function range($from, $to, $step = 1)
{
    return array();
}

/**
 * Outputs a file.
 * Call cms_ob_end_clean() first, else too much memory will be used.
 *
 * @param  PATH $filename The filename.
 * @param  boolean $use_include_path Whether to search within the include path.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @return ~integer The number of bytes read (false: error).
 */
function readfile($filename, $use_include_path = false, $context = null)
{
    return 0;
}

/**
 * Shuffle an array.
 *
 * @param  array $array The array to shuffle.
 */
function shuffle($array)
{
}

/**
 * Calculate the similarity between two strings.
 *
 * @param  string $first First string.
 * @param  string $second Second string.
 * @param  ?float $percent Returns the percentage of similarity (null: do not get).
 * @return integer The number of matching characters.
 */
function similar_text($first, $second, &$percent = null)
{
    return 0;
}

/**
 * Square root.
 *
 * @param  float $arg Number.
 * @return float return 0.0.
 */
function sqrt($arg)
{
    return 0.0;
}

/**
 * Binary safe case-insensitive string comparison.
 *
 * @param  string $str1 The first string.
 * @param  string $str2 The second string.
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2.
 */
function strcasecmp($str1, $str2)
{
    return 0;
}

/**
 * Locale based string comparison.
 *
 * @param  string $str1 The first string.
 * @param  string $str2 The second string.
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2.
 */
function strcoll($str1, $str2)
{
    return 0;
}

/**
 * Find length of initial segment not matching mask.
 *
 * @param  string $str1 The subject string.
 * @param  string $str2 The string of stop characters.
 * @return integer The length.
 */
function strcspn($str1, $str2)
{
    return 0;
}

/**
 * Case-insensitive strstr.
 *
 * @param  string $haystack Haystack.
 * @param  string $needle Needle.
 * @return string All of haystack from the first occurrence of needle to the end.
 */
function stristr($haystack, $needle)
{
    return '';
}

/**
 * Case insensitive string comparisons using a "natural order" algorithm.
 *
 * @param  string $str1 The first string.
 * @param  string $str2 The second string.
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2.
 */
function strnatcasecmp($str1, $str2)
{
    return 0;
}

/**
 * String comparisons using a "natural order" algorithm.
 *
 * @param  string $str1 The first string.
 * @param  string $str2 The second string.
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2.
 */
function strnatcmp($str1, $str2)
{
    return 0;
}

/**
 * Binary safe case-insensitive string comparison of the first n characters.
 *
 * @param  string $str1 The first string.
 * @param  string $str2 The second string.
 * @param  integer $len Up to this length (n).
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2.
 */
function strncasecmp($str1, $str2, $len)
{
    return 0;
}

/**
 * Binary safe string comparison of the first n characters.
 *
 * @param  string $str1 The first string.
 * @param  string $str2 The second string.
 * @param  integer $len Up to this length (n).
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2.
 */
function strncmp($str1, $str2, $len)
{
    return 0;
}

/**
 * Find the last occurrence of a character in a string.
 *
 * @param  string $haystack Haystack.
 * @param  string $needle Needle (string of length 1).
 * @length 1
 * @return string The portion of haystack which starts at the last occurrence of needle and goes until the end of haystack.
 */
function strrchr($haystack, $needle)
{
    return '';
}

/**
 * Reverse a string.
 *
 * @param  string $string String to reverse.
 * @return string Reversed string.
 */
function strrev($string)
{
    return '';
}

/**
 * Find length of initial segment matching mask.
 *
 * @param  string $string String to work upon.
 * @param  string $mask String consisting of alternative characters to require along our run.
 * @return string The length of the initial segment of string which consists entirely of characters in mask.
 */
function strspn($string, $mask)
{
    return '';
}

/**
 * Replace text within a portion of a string.
 *
 * @param  string $string The subject string.
 * @param  string $replacement The replacement string.
 * @param  integer $start The start position of what's being replaced.
 * @param  ?integer $length The run-length of what is being replaced (null: go to end of string).
 * @return string A copy of string delimited by the start and (optionally) length parameters with the string given in replacement.
 */
function substr_replace($string, $replacement, $start, $length = null)
{
    return '';
}

/**
 * Calculate the tangent of an angle.
 *
 * @param  float $arg The angle in radians.
 * @return float The tangent.
 */
function tan($arg)
{
    return 0.0;
}

/**
 * Unpack data from binary string.
 *
 * @param  string $format The formatting string for unpacking.
 * @param  string $data The data to unpack.
 * @return ~array The unpacked data (false: error).
 */
function unpack($format, $data)
{
    return array();
}

/**
 * Compares two "PHP-standardized" version number strings.
 *
 * @param  string $version1 First version number.
 * @param  string $version2 Second version number.
 * @param  ?string $compare_symbol The operator to compare with (null: unified).
 * @return mixed For unified: -1 if v1<v2, 0 if v1=v2, 1 if v1>v2. Else BINARY or boolean.
 */
function version_compare($version1, $version2, $compare_symbol = null)
{
    return 0;
}

/**
 * Get the type of a variable.
 *
 * @param  mixed $var The variable.
 * @return string The type.
 */
function gettype($var)
{
    return '';
}

/**
 * Dumps information about a variable.
 *
 * @param  mixed $expression Data.
 */
function var_dump($expression)
{
}

/**
 * Output a formatted string.
 *
 * @param  string $format Formatting string.
 * @param  array $args Arguments.
 * @return integer Length of outputed string.
 */
function vprintf($format, $args)
{
    return 0;
}

/**
 * Return a formatted string.
 *
 * @param  string $format Formatting string.
 * @param  array $args Arguments.
 * @return string Fixed string.
 */
function vsprintf($format, $args)
{
    return '';
}

/**
 * Sets access and modification time of file.
 *
 * @param  PATH $filename File to touch.
 * @param  ?TIME $time New modification time (null: do not change).
 * @param  ?TIME $atime New access time (null: do not change).
 * @return boolean Success status.
 */
function touch($filename, $time = null, $atime = null)
{
    return true;
}

/**
 * Hyperbolic tangent.
 *
 * @param  float $in In.
 * @return float Out.
 */
function tanh($in)
{
    return 0.0;
}

/**
 * Hyperbolic sine.
 *
 * @param  float $in In.
 * @return float Out.
 */
function sinh($in)
{
    return 0.0;
}

/**
 * Calculate the soundex key of a string.
 *
 * @param  string $input Input.
 * @return string Soundex.
 */
function soundex($input)
{
    return '';
}

/**
 * Un-quote string quoted with addcslashes.
 *
 * @param  string $in In.
 * @return string Out.
 */
function stripcslashes($in)
{
    return '';
}

/**
 * Output a gz-file.
 *
 * @param  PATH $filename Path to read from.
 * @return ~integer Number of uncompressed bytes handled (false: error).
 */
function readgzfile($filename)
{
    return 0;
}

/**
 * Restores the previous error handler function.
 */
function restore_error_handler()
{
}

/**
 * Rewind the position of a file pointer.
 *
 * @param  resource $handle File handle.
 * @return boolean Success status.
 */
function rewind($handle)
{
    return true;
}

/**
 * Rewind directory handle.
 *
 * @param  resource $handle Directory handle.
 */
function rewinddir($handle)
{
}

/**
 * Convert a quoted-printable string to an 8 bit string.
 *
 * @param  string $in In.
 * @return string Out.
 */
function quoted_printable_decode($in)
{
    return '';
}

/**
 * Quote meta characters. Returns a version of str with a backslash character (\) before every character that is among these: . \ + * ? [ ^ ] ( $ ).
 *
 * @param  string $in In.
 * @return string Out.
 */
function quotemeta($in)
{
    return '';
}

/**
 * Calculates the exponent of e.
 *
 * @param  float $arg Arg.
 * @return float Result.
 */
function exp($arg)
{
    return 0.0;
}

/**
 * Combined linear congruential generator.
 *
 * @return float Random number.
 */
function lcg_value()
{
    return 0.0;
}

/**
 * Get the local time.
 *
 * @param  ?TIME $timestamp Timestamp (null: now).
 * @param  boolean $associative If set to FALSE or not supplied than the array is returned as a regular, numerically indexed array. If the argument is set to TRUE then localtime() is an associative array containing all the different elements of the structure returned by the C function call to localtime.
 * @return array Components.
 */
function localtime($timestamp, $associative = false)
{
    return array();
}

/**
 * Quote string with slashes in a C style.
 *
 * @param  string $str Input string.
 * @param  string $charlist Chars to convert.
 * @return string Result.
 */
function addcslashes($str, $charlist)
{
    return '';
}

/**
 * Filters elements of an array using a callback function.
 *
 * @param  array $input In.
 * @param  ?mixed $callback The filter function callback (null: filter out false's).
 * @return array Out.
 */
function array_filter($input, $callback = null)
{
    return array();
}

/**
 * Applies the callback to the elements of the given array.
 *
 * @param  mixed $callback Callback map function.
 * @param  array $array In.
 * @return array Out.
 */
function array_map($callback, $array)
{
    return array();
}

/**
 * Add all the elements of an array.
 *
 * @param  array $array In.
 * @return mixed The sum (float or integer).
 */
function array_sum($array)
{
    return array();
}

/**
 * Merges the elements of one or more arrays together so that the values of one are appended to the end of the previous one. It returns the resulting array.
 * If the input arrays have the same string keys, then the values for these keys are merged together into an array, and this is done recursively, so that if one of the values is an array itself, the function will merge it with a corresponding entry in another array too. If, however, the arrays have the same numeric key, the later value will not overwrite the original value, but will be appended.
 *
 * @param  array $array1 First array to merge.
 * @param  array $array2 Second array to merge.
 * @param  ?array $array3 Third array to merge (null: not this one).
 * @param  ?array $array4 Fourth array to merge (null: not this one).
 * @param  ?array $array5 Fifth array to merge (null: not this one).
 * @return array Result.
 */
function array_merge_recursive($array1, $array2, $array3 = null, $array4 = null, $array5 = null)
{
    return array();
}

/**
 * Sort multiple or multi-dimensional array.
 *
 * @param  array $array Array to sort.
 * @param  ?integer $args Argument code (null: none given).
 * @return array Result.
 */
function array_multisort($array, $args = null)
{
    return array();
}

/**
 * Pad array to the specified length with a value.
 *
 * @param  array $input Input.
 * @param  integer $pad_size Pad size.
 * @param  mixed $pad_value Pad value.
 * @return array Output.
 */
function array_pad($input, $pad_size, $pad_value)
{
    return array();
}

/**
 * Iteratively reduce the array to a single value using a callback function.
 *
 * @param  array $input Input.
 * @param  mixed $callback Process function.
 * @param  ?integer $initial Initial value (null: no initial).
 * @return ?integer Result (null: no initial given, and empty array given).
 */
function array_reduce($input, $callback, $initial = null)
{
    return 0;
}

/**
 * Apply a user function to every member of an array .
 *
 * @param  array $array Data.
 * @return boolean Success status.
 */
function array_walk(&$array)
{
    return true;
}

/**
 * Arc tangent of two variables.
 *
 * @param  float $x First.
 * @param  float $y Second.
 * @return float Result.
 */
function atan2($x, $y)
{
    return 0.0;
}

/**
 * Gets character from file pointer.
 *
 * @param  resource $handle Handle.
 * @return ~string Character (false: error).
 */
function fgetc($handle)
{
    return '';
}

/**
 * Gets line from file pointer and parse for CSV fields.
 *
 * @param  resource $handle File handle.
 * @param  ?integer $length The maximum length of the line (null: no limit).
 * @param  string $delimiter Delimiter.
 * @return ~array Line (false: error).
 */
function fgetcsv($handle, $length = null, $delimiter = ',')
{
    return array();
}

/**
 * Gets file type.
 *
 * @param  PATH $file Filename.
 * @return ~string Result (fifo, char, dir, block, link, file, and unknown) (false: error).
 */
function filetype($file)
{
    return '';
}

/**
 * Parses input from a file according to a format.
 *
 * @param  resource $handle File handle.
 * @param  string $format Formatting string.
 * @return ~array Data (false: error).
 */
function fscanf($handle, $format)
{
    return array();
}

/**
 * Gets information about a file.
 *
 * @param  PATH $path File.
 * @return ~array Map of status information (false: error).
 */
function stat($path)
{
    return array();
}

/**
 * Gets information about a file using an open file pointer.
 *
 * @param  resource $handle File handle.
 * @return ~array Map of status information (false: error).
 */
function fstat($handle)
{
    return array();
}

/**
 * Truncates a file to a given length.
 *
 * @param  resource $file File handle.
 * @param  integer $size Cut off size.
 * @return boolean Success status.
 */
function ftruncate($file, $size)
{
    return true;
}

/**
 * Return an item from the argument list.
 *
 * @param  integer $arg_num Argument number.
 * @return mixed Argument.
 */
function func_get_arg($arg_num)
{
    return '';
}

/**
 * Returns an array comprising a function's argument list.
 *
 * @return array List of arguments.
 */
function func_get_args()
{
    return array();
}

/**
 * Returns the number of arguments passed to the function.
 *
 * @return integer Number of arguments.
 */
function func_num_args()
{
    return 0;
}

/**
 * Parse a configuration file.
 *
 * @param  PATH $filename The file path.
 * @param  boolean $process_sections Whether to process sections.
 * @return ~array Map of Ini file data (2d if processed sections) (false: error).
 */
function parse_ini_file($filename, $process_sections = false)
{
    return array();
}

/**
 * Parses the string into variables.
 *
 * @param  string $str Query string to parse.
 * @param  array $arr Target for variable mappings.
 */
function parse_str($str, &$arr)
{
}

/**
 * Tells whether the filename is executable.
 *
 * @param  PATH $filename Filename.
 * @return boolean Whether it is.
 */
function is_executable($filename)
{
    return true;
}

/**
 * Finds whether a variable is a scalar (integer, float, string or boolean).
 *
 * @param  mixed $var Variable.
 * @return boolean Whether it is.
 */
function is_scalar($var)
{
    return true;
}

/**
 * Find whether the object has this class as one of its parents.
 *
 * @param  mixed $object Object to check whether is an instance.
 * @param  string $class_name Class name to check against.
 * @return boolean Whether it is.
 */
function is_subclass_of($object, $class_name)
{
    return true;
}

/**
 * Calculate the metaphone key of a string.
 *
 * @param  string $string String to do.
 * @param  integer $value Phones value.
 * @return string Metaphone key.
 */
function metaphone($string, $value)
{
    return '';
}

/**
 * Sort an array using a case insensitive "natural order" algorithm .
 *
 * @param  array $array Array to sort.
 * @return boolean Success status.
 */
function natcasesort(&$array)
{
    return true;
}

/**
 * Sort an array using a "natural order" algorithm.
 *
 * @param  array $array Array to sort.
 * @return boolean Success status.
 */
function natsort(&$array)
{
    return true;
}

/**
 * Inserts HTML line breaks before all newlines in a string.
 *
 * @param  string $in In.
 * @return string Out.
 */
function nl2br($in)
{
    return '';
}

/**
 * Output a formatted string.
 *
 * @param  string $format Formatting string.
 * @param  ?mixed $arg1 Argument (null: not given).
 * @param  ?mixed $arg2 Argument (null: not given).
 * @param  ?mixed $arg3 Argument (null: not given).
 * @param  ?mixed $arg4 Argument (null: not given).
 * @param  ?mixed $arg5 Argument (null: not given).
 * @return string Assembled string.
 */
function printf($format, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null)
{
    return '';
}

/**
 * Hyperbolic cosine.
 *
 * @param  float $arg Argument.
 * @return float Result.
 */
function cosh($arg)
{
    return 0.0;
}

/**
 * Return information about characters used in a string.
 *
 * @param  string $string The string which to work within.
 * @param  integer $mode Operation mode.
 * @set    0 1 2 3 4
 * @return mixed Result, depending on mode used.
 */
function count_chars($string, $mode = 0)
{
    return '';
}

/**
 * Run some code. Do not use unless absolutely needed.
 *
 * @param  string $code Code to run.
 * @return mixed Result.
 */
function eval($code)
{
    return 0;
}

/**
 * Get a list of IP addresses corresponding to a given Internet host name.
 *
 * @param  string $hostname Hostname.
 * @return ~array List of IP addresses (false: could not resolve).
 */
function gethostbynamel($hostname)
{
    return array();
}

/**
 * Get the size of an image.
 *
 * @param  PATH $filename Filename.
 * @param  ?array $image_info Extra details will be put here (null: return-only). Note that this is actually passed by reference, but is also optional.
 * @return ~array List of details: $width, $height, $type, $attr (false: error).
 */
function getimagesize($filename, $image_info = null)
{
    return array();
}

/**
 * Gets time of last page modification.
 *
 * @return TIME Last modification time.
 */
function getlastmod()
{
    return 0;
}

/**
 * Get current time.
 *
 * @param boolean $return_float Return as float.
 * @return array Map of time details.
 */
function gettimeofday($return_float = false)
{
    return array();
}

/**
 * Gets the value of a PHP configuration option.
 *
 * @param  string $varname Value name to get.
 * @return ~string Value (false: error).
 */
function get_cfg_var($varname)
{
    return '';
}

/**
 * Extracts all meta tag content attributes from a file and returns an array.
 *
 * @param  PATH $filename Filename.
 * @return ~array Map of meta information (false: error).
 */
function get_meta_tags($filename)
{
    return array();
}

/**
 * Retrieves the parent class name for object or class.
 *
 * @param  object $object Object to check.
 * @return string Classname.
 */
function get_parent_class($object)
{
    return '';
}

/**
 * Returns an array with the names of included or required files.
 *
 * @return array Included files.
 */
function get_included_files()
{
    return array();
}

/**
 * Returns the resource type.
 *
 * @param  resource $handle Resource to check.
 * @return string The resource type.
 */
function get_resource_type($handle)
{
    return '';
}

/**
 * Compress a string.
 *
 * @param  string $data Data to compress.
 * @param  integer $level Compression level.
 * @return string Compressed data.
 */
function gzcompress($data, $level)
{
    return '';
}

/**
 * Deflate a string.
 *
 * @param  string $data Compressed data.
 * @param  integer $level Compression level.
 * @return ~string Uncompressed data (false: error).
 */
function gzdeflate($data, $level)
{
    return '';
}

/**
 * Create a gzip compressed string.
 *
 * @param  string $data In.
 * @param  integer $level How much compression.
 * @range  1 9
 * @return string Out.
 */
function gzencode($data, $level)
{
    return '';
}

/**
 * Read entire gz-file into an array.
 *
 * @param  PATH $filename The filename.
 * @return ~array An array containing the file, one line per cell (false: error).
 */
function gzfile($filename)
{
    return array();
}

/**
 * Inflate a deflated string.
 *
 * @param  string $data The data compressed by gzdeflate().
 * @param  integer $length Maximum length to read in.
 * @return string Inflated (uncompressed) data.
 */
function gzinflate($data, $length = 0)
{
    return '';
}

/**
 * Uncompress a compressed string.
 *
 * @param  string $data The data compressed by gzcompress().
 * @param  integer $length Maximum length to read in.
 * @return string Uncompressed data.
 */
function gzuncompress($data, $length = 0)
{
    return '';
}

/**
 * Convert logical Hebrew text to visual text.
 *
 * @param  string $hebrew_text In.
 * @param  ?integer $max_chars_per_line Maximum number of characters per line (null: no limit).
 * @return string Out.
 */
function hebrev($hebrew_text, $max_chars_per_line = null)
{
    return '';
}

/**
 * Calculate the length of the hypotenuse of a right-angle triangle.
 *
 * @param  float $x X.
 * @param  float $y Y.
 * @return float Result.
 */
function hypot($x, $y)
{
    return 0.0;
}

/**
 * Set whether a client disconnect should abort script execution.
 *
 * @param  boolean $setting Setting.
 * @return boolean Previous setting.
 */
function ignore_user_abort($setting)
{
    return true;
}

/**
 * Get the contents of a file.
 *
 * @param  SHORT_TEXT $filename The file name.
 * @param  boolean $use_include_path Whether to search within the include path.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @param  integer $offset Offset.
 * @param  ?integer $maxlen Maximum length (null: no limit).
 * @return ~LONG_TEXT The file contents (false: error).
 */
function file_get_contents($filename, $use_include_path = false, $context = null, $offset = 0, $maxlen = null)
{
    return '';
}

/**
 * Isolate the words in the input string.
 *
 * @param  string $input String to count words in.
 * @param  integer $format The format.
 * @set    0 1 2
 * @param  string $chars A list of additional characters which will be considered as 'word'.
 * @return mixed Typically a list - the words of the input string.
 */
function str_word_count($input, $format = 0, $chars = '')
{
    return array();
}

/**
 * Decode the HTML entitity encoded input string.
 *
 * @param  string $input The text to decode.
 * @param  integer $quote_style The quote style code.
 * @param  ?string $charset Character set to decode to (null: default).
 * @return string The decoded text.
 */
function html_entity_decode($input, $quote_style, $charset = null)
{
    return '';
}

/**
 * Creates an array by using one array for keys and another for its values.
 *
 * @param  array $keys Keys.
 * @param  array $values Values.
 * @return array Combined.
 */
function array_combine($keys, $values)
{
    return array();
}

/**
 * Computes the difference of arrays with additional index check which is performed by a user supplied callback function.
 *
 * @param  array $a Array 1.
 * @param  array $b Array 2.
 * @return array Result.
 */
function array_diff_uassoc($a, $b)
{
    return array();
}

/**
 * Computes the difference of arrays by using a callback function for data comparison.
 *
 * @param  array $a Array 1.
 * @param  array $b Array 2.
 * @return array Result.
 */
function array_udiff($a, $b)
{
    return array();
}

/**
 * Computes the difference of arrays with additional index check. The data is compared by using a callback function.
 *
 * @param  array $a Array 1.
 * @param  array $b Array 2.
 * @return array Result.
 */
function array_udiff_assoc($a, $b)
{
    return array();
}

/**
 * Computes the difference of arrays with additional index check. The data is compared by using a callback function. The index check is done by a callback function also.
 *
 * @param  array $a Array 1.
 * @param  array $b Array 2.
 * @return array Result.
 */
function array_udiff_uassoc($a, $b)
{
    return array();
}

/**
 * Apply a user function recursively to every member of an array.
 *
 * @param  array $input The input array.
 * @param  mixed $funcname Callback.
 * @param  ?mixed $userdata If the optional userdata parameter is supplied, it will be passed as the third parameter to the callback funcname (null: no user data).
 * @return boolean Result.
 */
function array_walk_recursive($input, $funcname, $userdata = null)
{
    return true;
}

/**
 * Computes the intersection of arrays with additional index check. The data is compared by using a callback function.
 *
 * @param  array $a Array 1.
 * @param  array $b Array 2.
 * @return array Result.
 */
function array_uintersect_assoc($a, $b)
{
    return array();
}

/**
 * Computes the intersection of arrays with additional index check. Both the data and the indexes are compared by using separate callback functions.
 *
 * @param  array $a Array 1.
 * @param  array $b Array 2.
 * @return array Result.
 */
function array_uintersect_uassoc($a, $b)
{
    return array();
}

/**
 * Computes the intersection of arrays. The data is compared by using a callback function.
 *
 * @param  array $a Array 1.
 * @param  array $b Array 2.
 * @return array Result.
 */
function array_uintersect($a, $b)
{
    return array();
}

/**
 * Convert a string to an array.
 *
 * @param  string $str The input string.
 * @param  integer $split_length Maximum length of the chunk.
 * @return array Result.
 */
function str_split($str, $split_length = 1)
{
    return array();
}

/**
 * Search a string for any of a set of characters.
 *
 * @param  string $haystack The string where char_list is looked for.
 * @param  string $char_list The character list.
 * @return ~string String starting from the character found, or FALSE if it is not found (false: not found).
 */
function strpbrk($haystack, $char_list)
{
    return '';
}

/**
 * Binary safe optionally case insensitive comparison of two strings from an offset, up to length characters.
 *
 * @param  string $main_str The main string being compared.
 * @param  string $str The secondary string being compared.
 * @param  integer $offset The start position for the comparison. If negative, it starts counting from the end of the string.
 * @param  ?integer $length The length of the comparison (null: the largest of the length of the str compared to the length of main_str less the offset).
 * @param  boolean $case_insensitivity Whether to compare as case insensitive.
 * @return ~integer Returns < 0 if main_str from position offset is less than str, > 0 if it is greater than str, and 0 if they are equal (false: out of bounds).
 */
function substr_compare($main_str, $str, $offset, $length = null, $case_insensitivity = false)
{
    return 0;
}

/**
 * Write a string to a file.
 *
 * @param  PATH $filename Path to the file where to write the data.
 * @param  string $data The data to write.
 * @param  integer $flags Supported flags.
 * @param  ?resource $context A stream context to attach to (null: no special context).
 * @return ~integer Bytes written (false: error).
 */
function file_put_contents($filename, $data, $flags = 0, $context = null)
{
    return 0;
}

/**
 * Fetches all the headers sent by the server in response to a HTTP request.
 *
 * @param  URLPATH $url The target URL.
 * @param  BINARY $parse Whether to parse into a map.
 * @return array Result.
 */
function get_headers($url, $parse = 0)
{
    return array();
}

/**
 * Returns a list of response headers sent (or ready to send).
 *
 * @return array List of headers.
 */
function headers_list()
{
    return array();
}

/**
 * Generate URL-encoded query string.
 *
 * @param  array $query_data URL parameters.
 * @return string URL.
 */
function http_build_query($query_data)
{
    return '';
}

/**
 * Get file extension for image-type returned by .
 *
 * @param  integer $imagetype One of the IMAGETYPE_XXX constants.
 * @param  boolean $include_dot Whether to prepend a dot to the extension or not.
 * @return string A string with the extension corresponding to the given image type.
 */
function image_type_to_extension($imagetype, $include_dot = true)
{
    return '';
}

/**
 * Applies a filter to an image using custom arguments.
 *
 * @param  resource $image Image.
 * @param  integer $filtertype A constant indicating the filter type.
 * @param  ?mixed $arg1 Parameter (null: don't read).
 * @param  ?mixed $arg2 Parameter (null: don't read).
 * @param  ?mixed $arg3 Parameter (null: don't read).
 * @param  ?mixed $arg4 Parameter (null: don't read).
 * @return boolean Success status.
 */
function imagefilter($image, $filtertype, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null)
{
    return true;
}

/**
 * List files and directories inside the specified path.
 *
 * @param  PATH $directory Directory.
 * @return ~array Files (false: error).
 */
function scandir($directory)
{
    return array();
}

/**
 * Randomly shuffles a string.
 *
 * @param  string $in In.
 * @return string Out.
 */
function str_shuffle($in)
{
    return '';
}

/**
 * Get Mime-Type for image-type returned by getimagesize, exif_read_data, exif_thumbnail, exif_imagetype.
 *
 * @param  integer $image_type Image type.
 * @return string Mime type.
 */
function image_type_to_mime_type($image_type)
{
    return '';
}

/**
 * Find pathnames matching a pattern.
 *
 * @param  string $pattern Pattern according to the rules used by the libc glob.
 * @param  integer $flags Flags.
 * @return ~array Files found (false: error).
 */
function glob($pattern, $flags = 0)
{
    return array();
}

/**
 * Generates a backtrace.
 *
 * @return array Backtrace.
 */
function debug_backtrace()
{
    return array();
}

/**
 * Generates a backtrace to the output.
 */
function debug_print_backtrace()
{
}

/**
 * Sets the default timezone used by all date/time functions in a script.
 *
 * @param  string $timezone_identifier Timezone identifier.
 * @return boolean Success status.
 */
function date_default_timezone_set($timezone_identifier)
{
    return true;
}

/**
 * Gets the default timezone used by all date/time functions in a script.
 *
 * @return string The timezone identifier.
 */
function date_default_timezone_get()
{
    return '';
}

/**
 * Computes the difference of arrays using keys for comparison.
 *
 * @param  array $array1 Array 1.
 * @param  array $array2 Array 2.
 * @return array Result.
 */
function array_diff_key($array1, $array2)
{
    return array();
}

/**
 * Converts a human readable IP address to its packed in_addr representation.
 *
 * @param  string $address A human readable IPv4 or IPv6 address.
 * @return ~string The in_addr representation of the given address (false: error).
 */
function inet_pton($address)
{
    return '';
}

/**
 * Calculate the product of values in an array.
 *
 * @param  array $array Input.
 * @return float Result.
 */
function array_product($array)
{
    return 0.0;
}

/**
 * Computes the difference of arrays using a callback function on the keys for comparison.
 *
 * @param  array $array1 Array 1.
 * @param  array $array2 Array 2.
 * @param  mixed $callback Callback.
 * @return array Result.
 */
function array_diff_ukey($array1, $array2, $callback)
{
    return array();
}

/**
 * Computes the intersection of arrays using a callback function on the keys for comparison.
 *
 * @param  array $array1 Array 1.
 * @param  array $array2 Array 2.
 * @param  mixed $callback Callback.
 * @return array Result.
 */
function array_intersect_ukey($array1, $array2, $callback)
{
    return array();
}

/**
 * Converts a packed internet address to a human readable representation.
 *
 * @param  string $in_addr Converts a packed internet address to a human readable representation.
 * @return ~string A string representation of the address (false: error).
 */
function inet_ntop($in_addr)
{
    return '';
}

/**
 * Format line as CSV and write to file pointer.
 *
 * @param  resource $handle File pointer.
 * @param  array $fields An array of values.
 * @param  string $delimiter The optional delimiter parameter sets the field delimiter (one character only).
 * @param  string $enclosure The optional enclosure parameter sets the field enclosure (one character only).
 * @return ~integer The length of the written string (false: error).
 */
function fputcsv($handle, $fields, $delimiter = ',', $enclosure = '"')
{
    return 0;
}

/**
 * Finds whether a value is not a number.
 *
 * @param  float $val The value to check.
 * @return boolean Answer.
 */
function is_nan($val)
{
    return true;
}

/**
 * Finds whether a value is a legal finite number.
 *
 * @param  float $val The value to check.
 * @return boolean Answer.
 */
function is_finite($val)
{
    return true;
}

/**
 * Finds whether a value is infinite.
 *
 * @param  float $val The value to check.
 * @return boolean Answer.
 */
function is_infinite($val)
{
    return true;
}

/**
 * Split an array into chunks.
 *
 * @param  array $input The array to work on.
 * @param  integer $size The size of each chunk.
 * @param  boolean $preserve_keys When set to TRUE keys will be preserved. Default is FALSE which will reindex the chunk numerically.
 * @return array A multidimensional numerically indexed array, starting with zero, with each dimension containing size elements.
 */
function array_chunk($input, $size, $preserve_keys = false)
{
    return array();
}

/**
 * Fill an array with values.
 *
 * @param  integer $start_index The first index of the returned array. If start_index is negative, the first index of the returned array will be start_index and the following indices will start from zero.
 * @param  integer $num Number of elements to insert. Must be greater than zero.
 * @param  mixed $value Value to use for filling.
 * @return array The filled array.
 */
function array_fill($start_index, $num, $value)
{
    return array();
}

/**
 * Changes all keys in an array.
 *
 * @param  array $input The array to work on.
 * @param  integer $case Either CASE_UPPER or CASE_LOWER.
 * @return array An array with its keys lower or uppercased.
 */
function array_change_key_case($input, $case)
{
    return array();
}

/**
 * Outputs or returns a parsable string representation of a variable.
 *
 * @param  mixed $expression The variable you want to export.
 * @param  boolean $return If used and set to TRUE, var_export() will return the variable representation instead of outputting it.
 * @return ?string Variable representation (null: asked to not return a value).
 */
function var_export($expression, $return = false)
{
    return '';
}

/**
 * Creates a stream context.
 *
 * @param  ?array $options Options (null: none).
 * @param  ?array $params Parameters (null: none). Usually options is used, parameters not needed and refers to standard parameters for all context types.
 * @return resource Stream context.
 */
function stream_context_create($options = null, $params = null)
{
    return array();
}

/**
 * Reads remainder of a stream into a string.
 *
 * @param  resource $handle A stream resource.
 * @param  integer $maxlength The maximum bytes to read (-1: no limit).
 * @param  integer $offset Seek to the specified offset before reading. If this number is negative, no seeking will occur and reading will start from the current position.
 * @return string Contents.
 */
function stream_get_contents($handle, $maxlength = -1, $offset = -1)
{
    return '';
}

/**
 * Returns the amount of memory allocated to PHP.
 *
 * @return integer The amount of memory, in bytes, that's currently being allocated to your PHP script.
 */
function memory_get_usage()
{
    return 0;
}

/*

Various things are disabled for various reasons. You may use them, if you use php_function_allowed

Disabled due to Google App Engine...

gc_collect_cycles
gc_enable
gc_disable
phpversion
php_sapi_name

Disabled due to too much general weirdness or just generally a bad idea to use...

sscanf
zend_logo_guid
zend_version
phpcredits
php_logo_guid
php_real_logo_guid
php_egg_logo_guid
register_tick_function
unregister_tick_function
get_loaded_extensions
extension_loaded
get_extension_funcs
php_ini_scanned_files
php_ini_loaded_file
dl
rand
convert_uuencode
convert_uudecode
import_request_variables
debug_zval_dump
php_strip_whitespace
ini_get_all
get_include_path
set_include_path
setrawcookie
umask
get_browser
chown
chgrp
extract
compact
str_rot13
output_add_rewrite_var
output_reset_rewrite_vars

Disabled due to multi-OS compatibility...

getservbyname
getservbyport
getprotobyname
getprotobynumber
virtual
apache_*
getallheaders
posix_uname
posix_kill
posix_mkfifo
posix_setpgid
posix_setsid
posix_setuid
posix_setuid
posix_getpwuid
posix_getuid
syslog
openlog
closelog
symlink
link
readlink
linkinfo
lchown
lchgrp
lstat
sys_getloadavg
getmypid
getmyuid
getrusage
getmyinode
getmygid
get_current_user
fnmatch

Disabled various legacy synonyms (aliases), such as...

show_source
doubleval
ini_alter
fputs
get_required_files
user_error
chop
diskfreespace
is_double
is_int
is_long
is_real
is_writeable
join
key_exists
magic_quotes_runtime
strchr
pos
sizeof
die

Disabled due to very commonly being disabled on hosts...

popen
pclose
proc_close
proc_get_status
proc_nice
proc_open
proc_terminate
passthru
pfsockopen
escapeshellcmd
escapeshellarg
define_syslog_variables
exec
system
shell_exec
ftp_exec
set_time_limit
fsockopen
phpinfo
highlight_string
highlight_file
disk_free_space
disk_total_space
error_log
php_uname
ini_restore
putenv
sleep
usleep
time_nanosleep
time_sleep_until

Disabled due to often being ill-configured or disabled on hosts...

tmpfile
tempnam

Disabled due to being removed/deprecated from PHP...

set_magic_quotes_runtime
call_user_method
call_user_method_array
split
spliti
ereg
ereg_replace
eregi
eregi_replace
sql_regcase
strptime
date_sunrise
date_sunset
date_sun_info
hebrevc
convert_cyr_string
money_format
ezmlm_hash
restore_include_path
get_magic_quotes_gpc
get_magic_quotes_runtime
fgetss
utf8_encode
utf8_decode

Disabled simply as we don't feel a need to use them (can enable if we find a use)...

property_exists
interface_exists
restore_exception_handler
get_declared_interfaces
get_defined_constants
htmlspecialchars_decode
sha1_file
strripos
nl_langinfo
vfprintf
asinh
acosh
atanh
expm1
log1p
getopt
settype
dir
ob_get_flush
ob_get_status
ob_list_handlers
array_intersect_uassoc

// ---

Not yet in our compatibility list (<=PHP5.1), but would be disabled if they were...
gethostname (Google AppEngine disallows)

In newer PHP so we will add at some point...
memory_get_peak_usage
error_get_last
array_fill_keys
sys_get_temp_dir
preg_last_error

NOT disabled...
GD functions - we TRY to be conditional with them, but they are in our minimum specs and we don't want dirty code
GZIP functions - we TRY to be conditional with them, but they are in our minimum specs and we don't want dirty code

// ---

NB about paths:
 Obviously do not rely on PHP_SELF/SCRIPT_NAME/REQUEST_URI if you're not sure it is a web-request; use get_self_url_easy()
 PATH_INFO is very specific and should not be relied on; Composr may do something with it if it is there
 PATH_TRANSLATED may be wrong or missing, never use it
 DOCUMENT_ROOT is never really knowable, don't rely on it
 PHP_SELF is always set but you almost always want SCRIPT_NAME instead (or REQUEST_URI for URLs)
 Chris's notes in the PHP manual (http://php.net/manual/en/reserved.variables.server.php) explain how everything works; we emulate stuff as discussed in the notes

NB about $_SERVER:
 We should always check both $_SERVER and $_ENV for stuff (usually via cms_srv) apart from for...
  argv
  PHP_AUTH_USER
  HTTP_CACHE_CONTROL
  When we know the architecture involved implicitly (e.g. Demonstratr, Rackspace Cloud, CloudFlare with Apache)
*/

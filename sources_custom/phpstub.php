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

/*

This file should never be included.
It serves to *specify* the subset of PHP that Composr is written to. We define a limited subset so we can verify we write to multiple versions of PHP.
The code quality checker automatically parses and uses this file, to build up the checker API.

*/

/**
 * Verify that the contents of a variable is an iterable value.
 *
 * @param  mixed $var Variable to check
 * @return boolean Whether it is
 */
function is_iterable($var) : bool
{
    return false;
}

/**
 * Return the values from a single column in the input array.
 *
 * @param  array $input A multi-dimensional array or an array of objects from which to pull a column of values from
 * @param  mixed $column_key The column of values to return
 * @param  ?mixed $index_key The column to use as the index/keys for the returned array (null: numeric sequential indices)
 * @return array Collapsed values
 */
function array_column(array $input, $column_key, $index_key = null) : array
{
    return [];
}

/**
 * Timing attack safe string comparison.
 *
 * @param  string $known_string The string of known length to compare against
 * @param  string $user_string The user-supplied string
 * @return boolean If the strings are equal
 */
function hash_equals(string $known_string, string $user_string) : bool
{
    return false;
}

/**
 * Hash the password using the specified algorithm.
 *
 * @param  string $password The password to hash
 * @param  integer $algo The algorithm to use (Defined by PASSWORD_* constants)
 * @param  array $options The options for the algorithm to use
 * @return ~string The hashed password (false: error)
 */
function password_hash(string $password, int $algo, array $options)
{
    return false;
}

/**
 * Verify a password against a hash using a timing attack resistant approach.
 *
 * @param  string $password The password to verify
 * @param  string $hash The hash to verify against
 * @return boolean If the password matches the hash
 */
function password_verify(string $password, string $hash) : bool
{
    return false;
}

/**
 * Checks if the given hash matches the given options.
 *
 * @param  string $hash The password hash
 * @param  integer $algo The algorithm wanted (Defined by PASSWORD_* constants)
 * @param  ?array $options The options for the algorithm wanted (null: no options)
 * @return boolean Whether rehash is needed
 */
function password_needs_rehash(string $hash, int $algo, ?array $options = null) : bool
{
    return false;
}

/**
 * Returns information about the given hash.
 *
 * @param  string $hash The password hash
 * @return array A map of info, include algo, algoName, options
 */
function password_get_info(string $hash) : array
{
    return [];
}

/**
 * Absolute value.
 *
 * @param  mixed $number The number to get the absolute value of
 * @return mixed The absolute value of number
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
function fmod(float $x, float $y)
{
    return 0.0;
}

/**
 * Counts all the values of an array.
 *
 * @param  array $input Input array
 * @return array An array using the values of the input array as keys and their frequency in input as values
 */
function array_count_values(array $input) : array
{
    return [];
}

/**
 * Calculate the difference between arrays.
 *
 * @param  array $array1 First array
 * @param  array ...$arrays Other arrays
 * @return array The difference
 */
function array_diff(array $array1, array ...$arrays) : array
{
    return [];
}

/**
 * Computes the intersection of arrays with additional index check.
 *
 * @param  array $array1 First array
 * @param  array ...$arrays Other arrays
 * @return array The difference
 */
function array_diff_assoc(array $array1, array ...$arrays) : array
{
    return [];
}

/**
 * Exchanges all keys with their associated values in an array.
 *
 * @param  array $trans Array to flip
 * @return array An array in flip order
 */
function array_flip(array $trans) : array
{
    return [];
}

/**
 * Checks if the given key or index exists in the array.
 *
 * @param  mixed $key Key
 * @param  array $search Search array
 * @return boolean Whether the key is set in the search array
 */
function array_key_exists($key, array $search) : bool
{
    return false;
}

/**
 * Return all the keys of an array.
 *
 * @param  array $input Input array
 * @param  ?mixed $search_value Only find keys with this value (null: no such filter)
 * @return array The keys of the array
 */
function array_keys(array $input, $search_value = null) : array
{
    return [];
}

/**
 * Calculate the intersection between arrays.
 *
 * @param  array $array1 First array
 * @param  array ...$arrays Other arrays
 * @return array The intersection
 */
function array_intersect(array $array1, array ...$arrays) : array
{
    return [];
}

/**
 * Computes the intersection of arrays using keys for comparison.
 *
 * @param  array $array1 First array
 * @param  array ...$arrays Other arrays
 * @return array The intersection
 */
function array_intersect_key(array $array1, array ...$arrays) : array
{
    return [];
}

/**
 * Calculate the intersection of arrays with additional index check.
 *
 * @param  array $array1 First array
 * @param  array ...$arrays Other arrays
 * @return array The intersection
 */
function array_intersect_assoc(array $array1, array ...$arrays) : array
{
    return [];
}

/**
 * Merge arrays together.
 *
 * @param  array ...$arrays Arrays to merge
 * @return array Merged array
 */
function array_merge(array ...$arrays) : array
{
    return [];
}

/**
 * Pop the element off the end of array.
 *
 * @param  array $array The array
 * @return ?mixed The value (null: no value)
 */
function array_pop(array &$array)
{
    return 0;
}

/**
 * Push one or more elements onto the end of array.
 *
 * @param  array $array The array
 * @param  mixed ...$vars Elements to append
 * @return integer The new number of elements in the array
 */
function array_push(array &$array, ...$vars) : int
{
    return 0;
}

/**
 * Return an array with elements in reverse order.
 *
 * @param  array $array The array to reverse
 * @param  boolean $preserve_keys Whether to preserve keys
 * @return array The reversed array
 */
function array_reverse(array $array, bool $preserve_keys = false) : array
{
    return [];
}

/**
 * Searches the array for a given value and returns the corresponding key if successful.
 *
 * @param  mixed $needle Needle
 * @param  array $haystack Haystack
 * @return ~mixed The key (false: not found)
 */
function array_search($needle, array $haystack)
{
    return 0;
}

/**
 * Shift an element off the beginning of array.
 *
 * @param  array $array The array
 * @return ?mixed Shifted element (null: empty array given)
 */
function array_shift(array &$array)
{
    return '';
}

/**
 * Extract a slice of the array.
 *
 * @param  array $array The array
 * @param  integer $offset The offset
 * @param  ?integer $length The length (null: up to the end of the array)
 * @param  boolean $preserve_keys When set to TRUE keys will be preserved. Default is FALSE which will reindex the slice numerically.
 * @return array The slice
 */
function array_slice(array $array, int $offset, ?int $length = null, bool $preserve_keys = false) : array
{
    return [];
}

/**
 * Remove a portion of the array and replace it with something else.
 *
 * @param  array $input The array
 * @param  integer $offset The offset
 * @param  ?integer $length The length (null: up to the end of the array)
 * @param  ?array $replacement The replacement (null: nothing put in, just bit taken out)
 * @return array The spliced result
 */
function array_splice(array &$input, int $offset, ?int $length = null, ?array $replacement = null) : array
{
    return [];
}

/**
 * Removes duplicate values from an array. Equivalence determined by string comparison.
 *
 * @param  array $array Input array
 * @param  integer $compare_flags An integer flag defining how to compare values
 * @return array Output array
 */
function array_unique(array $array, int $compare_flags = 0) : array
{
    return [];
}

/**
 * Return all the values of an array.
 *
 * @param  array $array Input array
 * @return array Output array
 */
function array_values(array $array) : array
{
    return [];
}

/**
 * Decodes data encoded with MIME base64.
 *
 * @param  string $encoded_data Encoded data
 * @param  boolean $strict Return FALSE if input contains character from outside the base64 alphabet
 * @return ~string Decoded data (false: error)
 */
function base64_decode(string $encoded_data, bool $strict = false)
{
    return '';
}

/**
 * Encodes data with MIME base64.
 *
 * @param  string $data Data
 * @return string Encoded data
 */
function base64_encode(string $data) : string
{
    return '';
}

/**
 * Call a user function given by the first parameter.
 *
 * @param  mixed $function Function callback
 * @param  mixed ...$params Parameters
 * @return mixed Whatever the function returns
 */
function call_user_func($function, ...$params)
{
    return 0;
}

/**
 * Round fractions up.
 *
 * @param  float $function Value to round up
 * @return float Rounded value
 */
function ceil(float $function) : float
{
    return 0.0;
}

/**
 * Change directory.
 *
 * @param  PATH $directory Path to change to
 * @return boolean Success status
 */
function chdir(string $directory) : bool
{
    return false;
}

/**
 * Validate a gregorian date.
 *
 * @param  integer $month The month
 * @param  integer $day The day
 * @param  integer $year The year
 * @return boolean Whether the date is valid
 */
function checkdate(int $month, int $day, int $year) : bool
{
    return false;
}

/**
 * Changes file mode.
 * Only can set the 'read only' flag on Windows.
 *
 * @param  PATH $filename The file to change the mode of
 * @param  integer $mode The mode (e.g. 0777).
 * @return boolean Success status
 */
function chmod(string $filename, int $mode) : bool
{
    return false;
}

/**
 * Return a specific character.
 *
 * @param  integer $ascii The ASCII code for the character required
 * @return string A string of length 1, where the first character is as requested
 */
function chr(int $ascii) : string
{
    return '';
}

/**
 * Split a string into smaller chunks. Can be used to split a string into smaller chunks which is useful for e.g. converting base64_encode output to match RFC 2045 semantics. It inserts end (defaults to "\r\n") every chunklen characters.
 *
 * @param  string $body The input string
 * @param  integer $chunklen The maximum chunking length
 * @param  string $splitter Split character
 * @return string The chunked version of the input string
 */
function chunk_split(string $body, int $chunklen = 76, string $splitter = "\r\n") : string
{
    return '';
}

/**
 * Checks if the class has been defined.
 *
 * @param  string $class_name The class identifier
 * @param  boolean $autoload Whether to cosnider autoloading
 * @return boolean Whether the class has been defined
 */
function class_exists(string $class_name, bool $autoload = true) : bool
{
    return false;
}

/**
 * Clears file status cache.
 *
 * @param  boolean $clear_realpath_cache Whether to clear the realpath cache or not
 * @param  ?PATH $filename Clear the realpath and the stat cache for a specific filename only; only used if clear_realpath_cache is true. (null: no filter)
 */
function clearstatcache(bool $clear_realpath_cache = false, ?string $filename = null)
{
}

/**
 * Close directory handle.
 *
 * @param  resource $handle The directory handle to close
 */
function closedir($handle)
{
}

/**
 * Returns the value of a constant.
 *
 * @param  string $name The name of the constant
 * @return mixed The value of the constant
 */
function constant(string $name)
{
    return '';
}

/**
 * Copies a file. {{creates-file}}
 *
 * @param  PATH $source The source path
 * @param  PATH $dest The destination path
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return boolean Success status
 */
function copy(string $source, string $dest, $context = null) : bool
{
    return false;
}

/**
 * Calculate the cosine of an angle.
 *
 * @param  float $angle The angle in radians
 * @return float The cosine
 */
function cos(float $angle) : float
{
    return 0.0;
}

/**
 * Count elements in a variable.
 *
 * @param  array $var Variable to count elements of
 * @param  integer $mode The count mode (COUNT_NORMAL or COUNT_RECURSIVE)
 * @return integer The count
 */
function count(array $var, int $mode = 0) : int
{
    return 0;
}

/**
 * Return the current element in an array.
 *
 * @param  array $array The array
 * @return mixed The current element
 */
function current(array $array)
{
    return 0;
}

/**
 * Format a local time/date.
 *
 * @param  string $format The format string
 * @param  ?TIME $timestamp The timestamp (null: current time)
 * @return string The string representation of the local time/date
 */
function date(string $format, ?int $timestamp = null) : string
{
    return '';
}

/**
 * Integer to string representation of hexadecimal.
 *
 * @param  integer $number The integer ('decimal' form, although truly stored in binary)
 * @return string The string representation
 */
function dechex(int $number) : string
{
    return '';
}

/**
 * Integer to string representation of octal.
 *
 * @param  integer $number The integer ('decimal' form, although truly stored in binary)
 * @return string The string representation
 */
function decoct(int $number) : string
{
    return '';
}

/**
 * Defines a named constant.
 *
 * @param  string $name Identifier
 * @param  mixed $value Value
 * @return boolean Success status
 */
function define(string $name, $value) : bool
{
    return false;
}

/**
 * Checks whether a given named constant exists.
 *
 * @param  string $name The identifier of a constant
 * @return boolean Whether the constant exists
 */
function defined(string $name) : bool
{
    return false;
}

/**
 * Returns directory name component of path.
 *
 * @param  PATH $name The path
 * @param  integer $levels Levels up from filename
 * @return PATH The directory name component
 */
function dirname(string $name, int $levels = 1) : string
{
    return '';
}

/**
 * Converts the number in degrees to the radian equivalent.
 *
 * @param  float $number Angle in degrees
 * @return float Angle in radians
 */
function deg2rad(float $number) : float
{
    return 0.0;
}

/**
 * Sets which PHP errors are reported.
 *
 * @param  ?integer $level OR'd combination of error type constants. (E_ERROR, E_WARNING, E_PARSE, E_NOTICE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_ALL) (null: find current level).
 * @return integer Current error reporting level
 */
function error_reporting(?int $level = null) : int
{
    return 0;
}

/**
 * Output a message and terminate the current script.
 *
 * @param  mixed $message The message (string), or status code (integer)
 * @exits
 */
function exit($message = '')
{
}

/**
 * Split a string by string.
 *
 * @param  string $separator The separator
 * @param  string $string The string to split
 * @param  ?integer $limit The maximum number of splits (the last element containing the remainder) (null: no limit)
 * @return array The split list
 */
function explode(string $separator, string $string, ?int $limit = null) : array
{
    return [];
}

/**
 * Reads remainder of a stream into a string.
 *
 * @param  resource $handle A stream resource
 * @param  integer $maxlength The maximum bytes to read (-1: no limit)
 * @param  integer $offset Seek to the specified offset before reading. If this number is negative, no seeking will occur and reading will start from the current position
 * @return string Contents
 */
function stream_get_contents($handle, int $maxlength = -1, int $offset = -1) : string
{
    return '';
}

/**
 * Closes an open file pointer.
 *
 * @param  resource $handle The file pointer
 * @return boolean Success status
 */
function fclose($handle) : bool
{
    return false;
}

/**
 * Tests for end-of-file on a file pointer.
 *
 * @param  resource $handle The file pointer
 * @return boolean Whether the end of the file has been reached
 */
function feof($handle) : bool
{
    return false;
}

/**
 * Gets line from file pointer.
 *
 * @param  resource $handle The file pointer
 * @param  ?integer $length The maximum length of the line (null: no limit)
 * @return ~string The string read (false: error)
 */
function fgets($handle, ?int $length = null)
{
    return '';
}

/**
 * Reads entire file into an array.
 *
 * @param  PATH $filename The file name
 * @param  integer $flags Flags
 * @return ~array The array (each line being an entry in the array, and newlines still attached) (false: error)
 */
function file(string $filename, int $flags = 0)
{
    return [];
}

/**
 * Checks whether a file or directory exists.
 *
 * @param  PATH $filename The path
 * @return boolean Whether it exists
 */
function file_exists(string $filename) : bool
{
    return false;
}

/**
 * Gets inode change time of file.
 *
 * @param  PATH $filename The filename
 * @return ~TIME Timestamp of creation (negativity is blasphemy) (false: error)
 */
function filectime(string $filename)
{
    return 0;
}

/**
 * Gets file modification time.
 *
 * @param  PATH $filename The filename
 * @return ~TIME Timestamp of modification (false: error)
 */
function filemtime(string $filename)
{
    return 0;
}

/**
 * Gets file permissions.
 * Not very useful on Windows.
 *
 * @param  PATH $filename The filename
 * @return ~integer The permissions (e.g. 0777) (false: error).
 */
function fileperms(string $filename)
{
    return 0;
}

/**
 * Gets file size.
 *
 * @param  PATH $filename The filename
 * @return ~integer The file size (false: error)
 */
function filesize(string $filename)
{
    return 0;
}

/**
 * Get float value of a variable.
 *
 * @param  mixed $var The input
 * @return float The float value
 */
function floatval($var) : float
{
    return 0.0;
}

/**
 * Round fractions down.
 *
 * @param  float $value The input
 * @return float The rounded value
 */
function floor(float $value) : float
{
    return 0.0;
}

/**
 * Get an array of all defined variables.
 *
 * @return array All defined variables
 */
function get_defined_vars() : array
{
    return [];
}

/**
 * Get an array of all declared classes.
 *
 * @return array All declared classes
 */
function get_declared_classes() : array
{
    return [];
}

/**
 * Get an array of all defined functions.
 *
 * @return array All defined functions
 */
function get_defined_functions() : array
{
    return [];
}

/**
 * Opens file or URL. {{creates-file}}
 *
 * @param  PATH $filename Filename
 * @param  string $mode Mode (e.g. at).
 * @param  boolean $use_include_path Whether to search within the include path
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return ~resource The file handle (false: could not be opened)
 */
function fopen(string $filename, string $mode, bool $use_include_path = false, $context = null)
{
    return [];
}

/**
 * Output all remaining data on a file pointer.
 * Call cms_ob_end_clean() first, else too much memory will be used.
 *
 * @param  resource $handle The file handle
 * @return ~integer The number of characters that got read (false: error)
 */
function fpassthru($handle)
{
    return 0;
}

/**
 * Binary-safe file read.
 *
 * @param  resource $handle The file handle
 * @param  integer $length Maximum length to read
 * @return ~string The read data (false: error)
 */
function fread($handle, int $length)
{
    return '';
}

/**
 * Seeks on a file pointer.
 *
 * @param  resource $handle The file handle
 * @param  integer $offset The offset (meaning depends on whence)
 * @param  integer $whence SEEK_SET, SEEK_CUR or SEEK_END
 * @return integer Success status (-1 means error)
 */
function fseek($handle, int $offset, int $whence = SEEK_SET) : int
{
    return 0;
}

/**
 * Gets file pointer read/write position.
 *
 * @param  resource $handle The file handle
 * @return ~integer The offset (false: error)
 */
function ftell($handle)
{
    return 0;
}

/**
 * Find whether the function of the given function name has been defined.
 *
 * @param  string $function_name The name of the function
 * @return boolean Whether it is defined
 */
function function_exists(string $function_name) : bool
{
    return false;
}

/**
 * Binary-safe file write.
 *
 * @param  resource $handle The file handle
 * @param  string $string The string to write to the file
 * @param  ?integer $length The length of data to write (null: all of $string)
 * @return ~integer The number of bytes written (false: error)
 */
function fwrite($handle, string $string, ?int $length = null)
{
    return 0;
}

/**
 * Retrieve information about the currently installed GD library.
 *
 * @return array Array of information
 */
function gd_info() : array
{
    return [];
}

/**
 * Returns the name of the class of an object.
 *
 * @param  object $obj The object
 * @return string The class name
 */
function get_class(object $obj) : string
{
    return '';
}

/**
 * Returns the translation table used by htmlspecialchars and htmlentities.
 *
 * @param  integer $table The table to select (HTML_ENTITIES or HTML_SPECIALCHARS)
 * @param  integer $quote_style The quote style (ENT_QUOTES or ENT_NOQUOTES or ENT_COMPAT)
 * @param  string $charset The character set to use
 * @return array The translation table
 */
function get_html_translation_table(int $table, int $quote_style = ENT_COMPAT, string $charset = 'utf-8') : array
{
    return [];
}

/**
 * Gets the current working directory.
 *
 * @return PATH The cwd
 */
function getcwd() : string
{
    return '';
}

/**
 * Get date/time information.
 *
 * @param  ?TIME $timestamp Timestamp to get information for (null: now)
 * @return array The information
 */
function getdate(?int $timestamp = null) : array
{
    return [];
}

/**
 * Gets the value of an environment variable.
 *
 * @param  string $string The environment name to get (e.g. PATH).
 * @return ~string The value (false: error)
 */
function getenv(string $string)
{
    return '';
}

/**
 * Format a GMT/UTC date/time (uses different format to 'date' function).
 *
 * @param  string $format The 'gm' format string
 * @param  ?TIME $timestamp Timestamp to use (null: now)
 * @return string The formatted string
 */
function gmdate(string $format, ?int $timestamp = null) : string
{
    return '';
}

/**
 * Send a raw HTTP header.
 *
 * @sets_output_state
 *
 * @param  string $string The header to send
 * @param  boolean $replace_last Whether to replace a previous call to set the same header (if you choose to not replace, it will send two different values for the same header)
 */
function header(string $string, bool $replace_last = true)
{
}

/**
 * Remove a PHP header. This only works if headers have not yet been sent.
 *
 * @sets_output_state
 *
 * @param  ?string $name The header to send (null: all)
 */
function header_remove(?string $name = null)
{
}

/**
 * Checks if or where headers have been sent.
 *
 * @return boolean The answer
 */
function headers_sent() : bool
{
    return false;
}

/**
 * String representation of hexadecimal to decimal.
 *
 * @param  string $hex_string The string representation
 * @return integer The integer ('decimal' form, although truly stored in binary)
 */
function hexdec(string $hex_string) : int
{
    return 0;
}

/**
 * Convert all applicable characters to HTML entities.
 *
 * @param  string $string The string to encode
 * @param  integer $quote_style The quote style (ENT_COMPAT, ENT_QUOTES, ENT_NOQUOTES)
 * @param  string $charset The character set to use
 * @return string The encoded string
 */
function htmlentities(string $string, int $quote_style = ENT_COMPAT, string $charset = '') : string
{
    return '';
}

/**
 * Convert all basic HTML encoding characters to HTML entities.
 *
 * @param  string $string The string to encode
 * @param  integer $quote_style The quote style (ENT_COMPAT, ENT_QUOTES, ENT_NOQUOTES)
 * @param  string $charset The character set to use
 * @return string The encoded string
 */
function htmlspecialchars(string $string, int $quote_style = ENT_COMPAT, string $charset = '') : string
{
    return '';
}

/**
 * Gets the host name.
 *
 * @return ~string Hostname (false: error)
 */
function gethostname()
{
    return '';
}

/**
 * Set the blending mode for an image.
 *
 * @param  resource $image The image handle
 * @param  boolean $blendmode Whether to alpha blend
 * @return boolean Success status
 */
function imagealphablending($image, bool $blendmode) : bool
{
    return true;
}

/**
 * Allocate a color for an image.
 *
 * @param  resource $image The image handle
 * @param  integer $red Red component (0-255)
 * @param  integer $green Green component (0-255)
 * @param  integer $blue Blue component (0-255)
 * @return ~integer Combined colour identifier (false: could not allocate)
 */
function imagecolorallocate($image, int $red, int $green, int $blue)
{
    return 0;
}

/**
 * Allocate a color for an image, with an alpha-component.
 *
 * @param  resource $image The image handle
 * @param  integer $red Red component (0-255)
 * @param  integer $green Green component (0-255)
 * @param  integer $blue Blue component (0-255)
 * @param  integer $alpha Alpha component (0-127)
 * @return integer Combined colour identifier
 */
function imagecolorallocatealpha($image, int $red, int $green, int $blue, int $alpha) : int
{
    return 0;
}

/**
 * Define a color as transparent.
 *
 * @param  resource $image The image handle
 * @param  ?integer $color Transparency colour identifier (null: get it, don't set it)
 * @return integer Transparency colour identifier
 */
function imagecolortransparent($image, ?int $color = null) : int
{
    return 0;
}

/**
 * Copy part of an image.
 *
 * @param  resource $dst_im Destination image handle
 * @param  resource $src_im Source image handle
 * @param  integer $dst_x Destination X-ordinate
 * @param  integer $dst_y Destination Y-ordinate
 * @param  integer $src_x Source X-ordinate
 * @param  integer $src_y Source Y-ordinate
 * @param  integer $src_w Width to copy
 * @param  integer $src_h Height to copy
 */
function imagecopy($dst_im, $src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_w, int $src_h)
{
}

/**
 * Copy and resize part of an image with resampling.
 *
 * @param  resource $dst_im Destination image handle
 * @param  resource $src_im Source image handle
 * @param  integer $dst_x Destination X-ordinate
 * @param  integer $dst_y Destination Y-ordinate
 * @param  integer $src_x Source X-ordinate
 * @param  integer $src_y Source Y-ordinate
 * @param  integer $dst_w Destination width
 * @param  integer $dst_h Destination height
 * @param  integer $src_w Source width
 * @param  integer $src_h Source height
 * @return boolean Success status
 */
function imagecopyresampled($dst_im, $src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $dst_w, int $dst_h, int $src_w, int $src_h) : bool
{
    return true;
}

/**
 * Copy and resize part of an image.
 *
 * @param  resource $dst_im Destination image handle
 * @param  resource $src_im Source image handle
 * @param  integer $dst_x Destination X-ordinate
 * @param  integer $dst_y Destination Y-ordinate
 * @param  integer $src_x Source X-ordinate
 * @param  integer $src_y Source Y-ordinate
 * @param  integer $dst_w Destination width
 * @param  integer $dst_h Destination height
 * @param  integer $src_w Source width
 * @param  integer $src_h Source height
 */
function imagecopyresized($dst_im, $src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $dst_w, int $dst_h, int $src_w, int $src_h)
{
}

/**
 * Create a new palette based image.
 *
 * @param  integer $width Width
 * @param  integer $height Height
 * @return resource The image handle
 */
function imagecreate(int $width, int $height)
{
    return [];
}

/**
 * Create a new image from the image stream in the string.
 *
 * @param  string $image The image
 * @return ~resource The image handle (false: error)
 */
function imagecreatefromstring(string $image)
{
    return [];
}

/**
 * Create a new image from a PNG file on disk.
 *
 * @param  PATH $path The PNG file
 * @return ~resource The image handle (false: error)
 */
function imagecreatefrompng(string $path)
{
    return [];
}

/**
 * Create a new image from a GIF file on disk.
 *
 * @param  PATH $path The GIF file
 * @return ~resource The image handle (false: error)
 */
function imagecreatefromgif(string $path)
{
    return [];
}

/**
 * Create a new image from a JPEG file on disk.
 *
 * @param  PATH $path The JPEG file
 * @return ~resource The image handle (false: error)
 */
function imagecreatefromjpeg(string $path)
{
    return [];
}

/**
 * Finds whether an image is a truecolor image.
 *
 * @param  resource $image The image handle
 * @return boolean Whether the image is truecolor
 */
function imageistruecolor($image) : bool
{
    return true;
}

/**
 * Make an image truecolor.
 *
 * @param  resource $image The image handle
 * @return boolean False on some kind of error, e.g. $image is invalid resource. Will return true if image is already truecolor.
 */
function imagepalettetotruecolor(&$image) : bool
{
    return true;
}

/**
 * Create a new truecolor image.
 *
 * @param  integer $x Width
 * @param  integer $y Height
 * @return resource The image handle
 */
function imagecreatetruecolor(int $x, int $y)
{
    return [];
}

/**
 * Get the index of the color of a pixel.
 *
 * @param  resource $image The image handle
 * @param  integer $x X ordinate
 * @param  integer $y Y ordinate
 * @return integer The colour
 */
function imagecolorat($image, int $x, int $y) : int
{
    return 0;
}

/**
 * Get the colors for an index.
 *
 * @param  resource $image The image handle
 * @param  integer $color The colour
 * @return array Map of components
 */
function imagecolorsforindex($image, int $color) : array
{
    return [];
}

/**
 * Destroy an image resource.
 *
 * @param  resource $image The image handle
 */
function imagedestroy($image)
{
}

/**
 * Flood fill.
 *
 * @param  resource $image The image handle
 * @param  integer $x Start from X
 * @param  integer $y Start from Y
 * @param  integer $colour The colour code to use
 */
function imagefill($image, int $x, int $y, int $colour)
{
}

/**
 * Get font height.
 *
 * @param  integer $font Font code
 * @return integer Height
 */
function imagefontheight(int $font) : int
{
    return 0;
}

/**
 * Get font width.
 *
 * @param  integer $font Font code
 * @return integer Width
 */
function imagefontwidth(int $font) : int
{
    return 0;
}

/**
 * Output image to browser or file as JPEG.
 *
 * @param  resource $image The image handle
 * @param  ?string $filename The filename (null: output to browser)
 * @param  ?integer $quality Quality level (null: default)
 * @return boolean Success status
 */
function imagejpeg($image, ?string $filename = null, ?int $quality = null) : bool
{
    return true;
}

/**
 * Output image to browser or file as PNG.
 *
 * @param  resource $image The image handle
 * @param  ?string $filename The filename (null: output to browser)
 * @param  integer $quality Compression level (0-9, 9 being highest compression)
 * @return boolean Success status
 */
function imagepng($image, ?string $filename = null, int $quality = 0) : bool
{
    return true;
}

/**
 * Output image to browser or file as GIF.
 *
 * @param  resource $image The image handle
 * @param  ?string $filename The filename (null: output to browser)
 * @return boolean Success status
 */
function imagegif($image, ?string $filename = null) : bool
{
    return true;
}

/**
 * Set the flag to save full alpha channel information (as opposed to single-color transparency) when saving PNG images.
 *
 * @param  resource $image The image handle
 * @param  boolean $saveflag Whether to save alpha channel information
 */
function imagesavealpha($image, bool $saveflag)
{
}

/**
 * Set a single pixel.
 *
 * @param  resource $image The image handle
 * @param  integer $x X-ordinate
 * @param  integer $y Y-ordinate
 * @param  integer $color Colour code
 */
function imagesetpixel($image, int $x, int $y, int $color)
{
}

/**
 * Draw a string horizontally.
 *
 * @param  resource $image The image handle
 * @param  integer $font Font code
 * @param  integer $x X-ordinate
 * @param  integer $y Y-ordinate
 * @param  string $s Text to draw
 * @param  integer $col Colour code
 */
function imagestring($image, int $font, int $x, int $y, string $s, int $col)
{
}

/**
 * Get image width.
 *
 * @param  resource $image The image handle
 * @return integer The image width
 */
function imagesx($image) : int
{
    return 0;
}

/**
 * Get image height.
 *
 * @param  resource $image The image handle
 * @return integer The image height
 */
function imagesy($image) : int
{
    return 0;
}

/**
 * Draw a vertical string.
 *
 * @param  resource $image The image handle
 * @param  integer $font The loaded font
 * @param  integer $x X-ordinate
 * @param  integer $y Y-ordinate
 * @param  string $s Text to draw
 * @param  integer $col Colour code
 */
function imagestringup($image, int $font, int $x, int $y, string $s, int $col)
{
}

/**
 * Give the bounding box of a text using TrueType fonts.
 *
 * @param  float $size The font size in pixels
 * @param  float $angle Angle in degrees in which text will be measured
 * @param  string $fontfile The name of the TrueType font file
 * @param  string $text The string to be measured
 * @return ~array Tuple: lower-left-X, lower-left-Y, lower-right-X, lower-right-Y, upper-right-X, upper-right-Y, upper-left-X, upper-left-Y (false: error)
 */
function imagettfbbox(float $size, float $angle, string $fontfile, string $text)
{
    return [];
}

/**
 * Draw a string.
 *
 * @param  resource $handle The image handle
 * @param  float $size The font size in pixels
 * @param  float $angle Angle in degrees in which text will be measured
 * @param  integer $x X-ordinate
 * @param  integer $y Y-ordinate
 * @param  integer $colour Colour code
 * @param  string $fontfile The name of the TrueType font file
 * @param  string $text Text to draw
 * @return ~array Tuple: lower-left-X, lower-left-Y, lower-right-X, lower-right-Y, upper-right-X, upper-right-Y, upper-left-X, upper-left-Y (false: error)
 */
function imagettftext($handle, float $size, float $angle, int $x, int $y, int $colour, string $fontfile, string $text)
{
    return [];
}

/**
 * Return the image types supported by this execution environment.
 *
 * @return integer Bit field of constants: IMG_GIF | IMG_JPG | IMG_PNG
 */
function imagetypes() : int
{
    return 0;
}

/**
 * Load a new font.
 *
 * @param  PATH $file File
 * @return ~integer Font code (false: error)
 */
function imageloadfont(string $file)
{
    return 0;
}

/**
 * Convert a truecolor image to a palette image.
 *
 * @param  resource $image The image involved
 * @param  boolean $dither Whether to use dithering
 * @param  integer $ncolors The maximum number of colors that should be retained in the palette
 */
function imagetruecolortopalette($image, bool $dither, int $ncolors)
{
}

/**
 * Get the index of the closest color to the specified color.
 *
 * @param  resource $image The image involved
 * @param  integer $red Red
 * @param  integer $green Green
 * @param  integer $blue Blue
 * @return integer Colour number
 */
function imagecolorclosest($image, int $red, int $green, int $blue) : int
{
    return 0;
}

/**
 * Get the index of the closest color to the specified color + alpha.
 *
 * @param  resource $image The image involved
 * @param  integer $red Red
 * @param  integer $green Green
 * @param  integer $blue Blue
 * @param  integer $alpha Alpha
 * @return integer Colour number
 */
function imagecolorclosestalpha($image, int $red, int $green, int $blue, int $alpha) : int
{
    return 0;
}

/**
 * De-allocate a color for an image.
 *
 * @param  resource $image The image involved
 * @param  integer $colour Colour number
 * @return boolean Success status
 */
function imagecolordeallocate($image, int $colour) : bool
{
    return true;
}

/**
 * Get the index of the specified color.
 *
 * @param  resource $image The image involved
 * @param  integer $red Red
 * @param  integer $green Green
 * @param  integer $blue Blue
 * @return integer Colour number
 */
function imagecolorexact($image, int $red, int $green, int $blue) : int
{
    return 0;
}

/**
 * Get the index of the specified color + alpha.
 *
 * @param  resource $image The image involved
 * @param  integer $red Red
 * @param  integer $green Green
 * @param  integer $blue Blue
 * @param  integer $alpha Alpha
 * @return ~integer Colour number (false: error)
 */
function imagecolorexactalpha($image, int $red, int $green, int $blue, int $alpha)
{
    return 0;
}

/**
 * Get the index of the specified color or its closest possible alternative.
 *
 * @param  resource $image The image involved
 * @param  integer $red Red
 * @param  integer $green Green
 * @param  integer $blue Blue
 * @return integer Colour number
 */
function imagecolorresolve($image, int $red, int $green, int $blue) : int
{
    return 0;
}

/**
 * Get the index of the specified color + alpha or its closest possible alternative.
 *
 * @param  resource $image The image involved
 * @param  integer $red Red
 * @param  integer $green Green
 * @param  integer $blue Blue
 * @param  integer $alpha Alpha
 * @return ~integer Colour number (false: error)
 */
function imagecolorresolvealpha($image, int $red, int $green, int $blue, int $alpha)
{
    return 0;
}

/**
 * Set the color for the specified palette index.
 *
 * @param  resource $image The image involved
 * @param  integer $red Red
 * @param  integer $green Green
 * @param  integer $blue Blue
 */
function imagecolorset($image, int $red, int $green, int $blue)
{
}

/**
 * Find out the number of colors in an image's palette.
 *
 * @param  resource $image The image involved
 * @return integer Total number of colours
 */
function imagecolorstotal($image) : int
{
    return 0;
}

/**
 * Copy and merge part of an image.
 *
 * @param  resource $dst_im Destination image handle
 * @param  resource $src_im Source image handle
 * @param  integer $dst_x Destination X-ordinate
 * @param  integer $dst_y Destination Y-ordinate
 * @param  integer $src_x Source X-ordinate
 * @param  integer $src_y Source Y-ordinate
 * @param  integer $src_w Width to copy
 * @param  integer $src_h Height to copy
 * @param  integer $pct Opacity value
 */
function imagecopymerge($dst_im, $src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_w, int $src_h, int $pct)
{
}

/**
 * Join array elements with a string.
 *
 * @param  string $glue The glue component (becomes a deliminator)
 * @param  array $pieces The pieces to join
 * @return string The joined string
 */
function implode(string $glue, array $pieces) : string
{
    return '';
}

/**
 * Checks if a value exists in an array.
 *
 * @param  mixed $needle Needle
 * @param  array $haystack Haystack
 * @param  boolean $strict Use strict type checking
 * @return boolean Whether the value exists in the array
 */
function in_array($needle, array $haystack, bool $strict = false) : bool
{
    return false;
}

/**
 * Include and evaluate the specified file.
 *
 * @param  PATH $filename The filename of the file to include
 * @return mixed Success status or returned value
 */
function include(string $filename)
{
    return false;
}

/**
 * Include and evaluate the specified file, but only if it has not already been included.
 *
 * @param  PATH $filename The filename of the file to include
 * @return mixed Success status or returned value
 */
function include_once(string $filename)
{
    return false;
}

/**
 * Gets the value of a configuration option. Note: On Phalanger any unknown config options will produce a warning if fetched.
 *
 * @param  string $varname Config option
 * @return ~mixed Value of option (empty: no such config option, or an empty value) (false: ditto)
 */
function ini_get(string $varname)
{
    return '';
}

/**
 * Sets the value of a configuration option.
 * Usually call cms_ini_set for Composr code.
 *
 * @param  string $var Config option
 * @param  string $value New value of option
 * @return ~string Old value of option (false: error)
 */
function ini_set(string $var, string $value)
{
    return '';
}

/**
 * Get integer value of a variable.
 *
 * @param  mixed $var Integer, but in some other form (usually string)
 * @param  integer $base The base
 * @return integer The integer, extracted
 */
function intval($var, int $base = 10) : int
{
    return 0;
}

/**
 * Whether the object is of this class or has this class as one of its parents.
 *
 * @param  object $object The object
 * @param  string $class_name The class name
 * @return boolean Whether it is
 */
function is_a(object $object, string $class_name) : bool
{
    return false;
}

/**
 * Finds whether a variable is an array.
 *
 * @param  mixed $var What to check
 * @return boolean Whether it is
 */
function is_array($var) : bool
{
    return false;
}

/**
 * Finds whether a variable is a boolean.
 *
 * @param  mixed $var What to check
 * @return boolean Whether it is
 */
function is_bool($var) : bool
{
    return false;
}

/**
 * Finds whether a path is to a directory.
 *
 * @param  PATH $path The path to check
 * @return boolean Whether it is
 */
function is_dir(string $path) : bool
{
    return false;
}

/**
 * Finds whether a path is to a file.
 *
 * @param  PATH $path The path to check
 * @return boolean Whether it is
 */
function is_file(string $path) : bool
{
    return false;
}

/**
 * Finds whether a path is to a symbolic link.
 *
 * @param  PATH $path The path to check
 * @return boolean Whether it is
 */
function is_link(string $path) : bool
{
    return false;
}

/**
 * Finds whether a variable is a float.
 *
 * @param  mixed $var What to check
 * @return boolean Whether it is
 */
function is_float($var) : bool
{
    return false;
}

/**
 * Finds whether a variable is an integer.
 *
 * @param  mixed $var What to check
 * @return boolean Whether it is
 */
function is_integer($var) : bool
{
    return false;
}

/**
 * Finds whether a variable holds a callable function reference.
 *
 * @param  mixed $var What to check
 * @return boolean Whether it does
 */
function is_callable($var) : bool
{
    return false;
}

/**
 * Finds whether a variable is null. Avoid this, use "=== null" instead for performance reasons.
 *
 * @param  mixed $var What to check
 * @return boolean Whether it is
 */
function is_null($var) : bool
{
    return false;
}

/**
 * Finds whether a variable is numeric (e.g. a numeric string, or a pure integer).
 *
 * @param  mixed $var What to check
 * @return boolean Whether it is
 */
function is_numeric($var) : bool
{
    return false;
}

/**
 * Finds whether a variable is an object.
 *
 * @param  mixed $var What to check
 * @return boolean Whether it is
 */
function is_object($var) : bool
{
    return false;
}

/**
 * Finds whether a path is to an actual readable file.
 *
 * @param  PATH $path The path to check
 * @return boolean Whether it is
 */
function is_readable(string $path) : bool
{
    return false;
}

/**
 * Finds whether a variable is a resource.
 *
 * @param  mixed $var What to check
 * @return boolean Whether it is
 */
function is_resource($var) : bool
{
    return false;
}

/**
 * Finds whether a variable is a string.
 *
 * @param  mixed $var What to check
 * @return boolean Whether it is
 */
function is_string($var) : bool
{
    return false;
}

/**
 * Finds whether a path is to an actual uploaded file.
 *
 * @param  PATH $path The path to check
 * @return boolean Whether it is
 */
function is_uploaded_file(string $path) : bool
{
    return false;
}

/**
 * Finds whether a path is to an actual writeable file.
 *
 * @param  PATH $path The path to check
 * @return boolean Whether it is
 */
function is_writable(string $path) : bool
{
    return false;
}

/**
 * Finds whether a variable exists / is not null / is an actually dereferencable array element. Do not use this for the null case, and otherwise ONLY when for efficiency reasons.
 *
 * @param  mixed $path_a The variable
 * @param  mixed $path_b The variable
 * @param  mixed $path_c The variable
 * @param  mixed $path_d The variable
 * @return boolean Whether it is set
 */
function isset(&$path_a, &$path_b = true, &$path_c = true, &$path_d = true) : bool
{
    return false;
}

/**
 * Strip whitespace from the beginning of a string.
 *
 * @param  string $string The string to trim from
 * @param  string $characters Characters to trim
 * @return string The trimmed string
 */
function ltrim(string $string, string $characters = " \t\n\r\0\x0B") : string
{
    return '';
}

/**
 * Send an e-mail.
 *
 * @param  string $to The TO address
 * @param  string $subject The subject
 * @param  string $message The message
 * @param  string $additional_headers Additional headers
 * @param  mixed $additional_flags Additional stuff to send to sendmail executable (array or string)
 * @return boolean Success status
 */
function mail(string $to, string $subject, string $message, string $additional_headers = '', $additional_flags = '') : bool
{
    return false;
}

/**
 * Find highest value between arguments.
 *
 * @param  mixed ...$args Arguments (if array, then each treated as a separate parameter)
 * @return mixed The highest valued argument
 */
function max(...$args)
{
    return 0;
}

/**
 * Calculate the md5 hash of a string.
 *
 * @param  string $str String to hash
 * @return string Hashed result
 */
function md5(string $str) : string
{
    return '';
}

/**
 * Checks if the class method exists.
 *
 * @param  object $object Object of the class we want to check
 * @param  string $method_name The method name
 * @return boolean Whether the class method exists
 */
function method_exists(object $object, string $method_name) : bool
{
    return false;
}

/**
 * Checks if the class property exists.
 *
 * @param  object $object Object of the class we want to check
 * @param  string $property_name The property name
 * @return boolean Whether the class property exists
 */
function property_exists(object $object, string $property_name) : bool
{
    return false;
}

/**
 * Return current UNIX timestamp with microseconds.
 *
 * @param  boolean $as_float Whether to return a float result
 * @return mixed Micro-time
 */
function microtime(bool $as_float = false)
{
    return '';
}

/**
 * Find lowest value between arguments.
 *
 * @param  mixed ...$args Arguments (if array, then each treated as a separate parameter)
 * @return mixed The lowest valued argument
 */
function min(...$args)
{
    return 0;
}

/**
 * Makes a directory. {{creates-file}}
 *
 * @param  PATH $path The path to the directory to make
 * @param  integer $mode The mode (e.g. 0777).
 * @param  boolean $recursive Whether to do recursively
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return boolean Success status
 */
function mkdir(string $path, int $mode, bool $recursive = false, $context = null) : bool
{
    return false;
}

/**
 * Moves an uploaded file to a new location. {{creates-file}}
 *
 * @param  PATH $filename Filename to move (taken from tmpname element of $_FILES list entry)
 * @param  PATH $destination Path to move file to (preferably containing filename component)
 * @return boolean The success status
 */
function move_uploaded_file(string $filename, string $destination) : bool
{
    return false;
}

/**
 * Get largest possible random value (better generator).
 *
 * @return integer The value
 */
function mt_getrandmax() : int
{
    return 0;
}

/**
 * Generate a better random value.
 * NOT CRYPTOGRAPHICALLY SECURE: USE get_secure_random_number() instead.
 *
 * @param  integer $min Minimum value
 * @param  integer $max Maximum value
 * @return integer Random value
 */
function mt_rand(int $min, int $max) : int
{
    return 0;
}

/**
 * Seed the better random number generator.
 *
 * @param  ?integer $seed The seed (null: random seed)
 */
function mt_srand(?int $seed = null)
{
}

/**
 * Format a number with grouped thousands.
 *
 * @param  mixed $number The number to format [integer or float] (technically always float because it could be larger than an integer, but that's ugly)
 * @param  integer $decimals The number of decimal fraction digits to show
 * @param  string $dec_point The string to use as a decimal point
 * @param  string $thousands_sep The string to separate groups of 1000's with
 * @return string The string formatted number
 */
function number_format($number, int $decimals = 0, string $dec_point = '.', string $thousands_sep = ',') : string
{
    return '';
}

/**
 * Turn on output buffering.
 *
 * @param  ?mixed $output_callback Callback after output is going to flush, works as a filter (null: none)
 * @param  integer $chunk_size Buffer will be auto-flushed after this amount (0: no limit)
 * @param  integer $flags A PHP_OUTPUT_HANDLER_* constant
 * @return boolean Success status
 */
function ob_start($output_callback = null, int $chunk_size = 0, int $flags = PHP_OUTPUT_HANDLER_STDFLAGS) : bool
{
    return false;
}

/**
 * Clean (erase) the output buffer and turn off output buffering.
 *
 * @return boolean Success status (could fail if there is no buffer)
 */
function ob_end_clean() : bool
{
    return false;
}

/**
 * Flush (output and erase) the output buffer and turn off output buffering.
 *
 * @return boolean Success status (could fail if there is no buffer)
 */
function ob_end_flush() : bool
{
    return false;
}

/**
 * Return the contents of the output buffer .
 *
 * @return ~string The buffer contents (false: no buffer)
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
 * @return ~string Contents of the buffer (false: no buffer was open)
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
 * Return the length of the output buffer.
 *
 * @return ~integer Output buffer length (false: error)
 */
function ob_get_length()
{
    return 0;
}

/**
 * Return the nesting level of the output buffering mechanism.
 *
 * @return integer Nesting level
 */
function ob_get_level() : int
{
    return 0;
}

/**
 * Turn implicit flush on/off .
 *
 * @param  integer $flag Flag (1 for on, 0 for off)
 */
function ob_implicit_flush(int $flag)
{
}

/**
 * Output something.
 *
 * @param  string $octal_string The string to output
 * @return integer The number '1', always
 */
function print(string $octal_string) : int
{
    return 1;
}

/**
 * String representation of octal to decimal.
 *
 * @param  string $octal_string The string representation
 * @return integer The integer ('decimal' form, although truly stored in binary)
 */
function octdec(string $octal_string) : int
{
    return 0;
}

/**
 * Open a directory for analysis.
 *
 * @param  PATH $path The path to the directory to open
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return ~resource The directory handle (false: error)
 */
function opendir(string $path, $context = null)
{
    return [];
}

/**
 * Return ASCII value of character.
 *
 * @param  string $string String of length 1, containing character to find ASCII value of
 * @return integer The ASCII value
 */
function ord(string $string) : int
{
    return 0;
}

/**
 * Pack data into binary string.
 *
 * @param  string $format The formatting string
 * @param  mixed ...$args Argument that binds to the formatting string
 * @return string The binary string
 */
function pack(string $format, ...$args) : string
{
    return '';
}

/**
 * Parse a URL and return its components.
 *
 * @param  string $url The URL to parse
 * @param  integer $component The component to get (-1 get all in an array)
 * @return ?~mixed A map of details about the URL (false: URL cannot be parsed) (null: missing component)
 */
function parse_url(string $url, int $component = -1)
{
    return [];
}

/**
 * Returns information about a file path.
 *
 * @param  PATH $path The path to parse
 * @param  integer $flags The PATHINFO_* constant defining what to return (default: all)
 * @return mixed A map of details about the path or a specific element if flag is not PATHINFO_ALL
 */
function pathinfo(string $path, int $flags = PATHINFO_ALL)
{
    return [];
}

/**
 * Perform a regular expression match.
 *
 * @param  string $pattern The pattern
 * @param  string $subject The subject string
 * @param  ?array $matches Where matches will be put (note that it is a list of maps, except the arrays are turned inside out) (null: do not store matches). Note that this is actually passed by reference, but is also optional. (null: don't gather)
 * @param  integer $flags Either 0, or PREG_OFFSET_CAPTURE
 * @param  integer $offset Offset to start from. Usually use with 'A' modifier to anchor it (using '^' in the pattern will not work)
 * @return ~integer The number of matches (false: error)
 */
function preg_match(string $pattern, string $subject, ?array &$matches = null, int $flags = 0, int $offset = 0)
{
    return 0;
}

/**
 * Perform a global regular expression match.
 *
 * @param  string $pattern The pattern
 * @param  string $subject The subject string
 * @param  ?array $matches Where matches will be put (note that it is a list of maps, except the arrays are turned inside out). Note that this is actually passed by reference, but is also optional. (null: don't gather)
 * @param  integer $flags Either 0, or PREG_OFFSET_CAPTURE
 * @return ~integer The number of matches (false: error)
 */
function preg_match_all(string $pattern, string $subject, ?array &$matches = null, int $flags = 0)
{
    return 0;
}

/**
 * Array entries that match the pattern.
 *
 * @param  string $pattern The pattern
 * @param  array $subject The subject strings
 * @param  integer $flags Either 0, or PREG_GREP_INVERT
 * @return array Matches
 */
function preg_grep(string $pattern, array $subject, int $flags = 0) : array
{
    return [];
}

/**
 * Perform a regular expression search and replace.
 *
 * @param  mixed $pattern The pattern (string or array)
 * @param  mixed $replacement The replacement string (string or array)
 * @param  mixed $subject The subject string (string or array)
 * @param  integer $limit The limit of replacements (-1: no limit)
 * @param  integer $count Number of replacements made
 * @return ?mixed The string with replacements made (null: error)
 */
function preg_replace($pattern, $replacement, $subject, int $limit = -1, int &$count = 0)
{
    return '';
}

/**
 * Perform a regular expression search and replace using a callback.
 *
 * @param  string $pattern The pattern
 * @param  mixed $callback The callback
 * @param  string $subject The subject string
 * @param  integer $limit The limit of replacements (-1: no limit)
 * @param  integer $count Number of replacements made
 * @return ?string The string with replacements made (null: error)
 */
function preg_replace_callback(string $pattern, $callback, string $subject, int $limit = -1, int &$count = 0) : ?string
{
    return '';
}

/**
 * Perform a regular expression search and replace using callbacks.
 *
 * @param  array $patterns_and_callbacks An associative array mapping patterns (keys) to callables (values)
 * @param  string $subject The subject string
 * @param  integer $limit The limit of replacements (-1: no limit)
 * @param  integer $count Number of replacements made
 * @return ?string The string with replacements made (null: error)
 */
function preg_replace_callback_array(array $patterns_and_callbacks, string $subject, int $limit = -1, int &$count = 0) : ?string
{
    return '';
}

/**
 * Split string by a regular expression.
 *
 * @param  string $pattern The pattern
 * @param  string $subject The subject
 * @param  integer $max_splits The maximum number of splits to make (-1: no limit)
 * @param  ?integer $mode The special mode (null: none)
 * @return array The array due to splitting
 */
function preg_split(string $pattern, string $subject, int $max_splits = -1, ?int $mode = null) : array
{
    return [];
}

/**
 * Returns the error code of the last PCRE regex execution.
 *
 * @return integer The error code of the last PCRE regex execution
 */
function preg_last_error() : int
{
    return 0;
}

/**
 * Prints human-readable information about a variable.
 *
 * @param  mixed $data The variable
 */
function print_r($data)
{
}

/**
 * Decode URL-encoded strings.
 *
 * @param  string $str The string to decode
 * @return string Decoded string
 */
function rawurldecode(string $str) : string
{
    return '';
}

/**
 * Encode URL-encoded strings. Used for everything *except* GET-parameter encoding.
 *
 * @param  string $str The string to encode
 * @return string Encoded string
 */
function rawurlencode(string $str) : string
{
    return '';
}

/**
 * Read entry from directory handle.
 *
 * @param  resource $dir_handle Handle
 * @return ~string Next filename (false: reached end already)
 */
function readdir($dir_handle)
{
    return '';
}

/**
 * Rewind directory handle.
 *
 * @param  resource $dir_handle Handle
 */
function rewinddir($dir_handle)
{
}

/**
 * Returns canonicalised absolute pathname.
 *
 * @param  PATH $path (Possibly) perceived path
 * @return PATH Actual path
 */
function realpath(string $path) : string
{
    return '';
}

/**
 * Renames a file.
 *
 * @param  PATH $oldname Old name
 * @param  PATH $newname New name
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return boolean Success status
 */
function rename(string $oldname, string $newname, $context = null) : bool
{
    return false;
}

/**
 * Require and evaluate the specified file (dies with error if it can not).
 *
 * @param  PATH $filename The filename of the file to require
 * @return mixed Success status or returned value
 */
function require(string $filename)
{
    return false;
}

/**
 * Require and evaluate the specified file (dies with error if it can not), but only if it has not been loaded already.
 *
 * @param  PATH $filename The filename of the file to require
 * @return mixed Success status or returned value
 */
function require_once(string $filename)
{
    return false;
}

/**
 * Set the internal pointer of an array to its first element.
 *
 * @param  array $array The array
 * @return mixed The value of the first element
 */
function reset(array $array)
{
    return 0;
}

/**
 * Removes directory.
 *
 * @param  PATH $dirname Directory path
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return boolean Success status
 */
function rmdir(string $dirname, $context = null) : bool
{
    return false;
}

/**
 * Rounds a float.
 *
 * @param  float $val Value to round
 * @param  integer $precision Number of decimal points of precision required (-ve allowed)
 * @param  integer $mode Rounding mode, a PHP_ROUND_* constant
 * @return float Rounded value
 */
function round(float $val, int $precision = 0, int $mode = PHP_ROUND_HALF_UP) : float
{
    return 0.0;
}

/**
 * Strip whitespace from the end of a string.
 *
 * @param  string $str String to trim from
 * @param  string $characters Characters to trim
 * @return string Trimmed string
 */
function rtrim(string $str, string $characters = " \t\n\r\0\x0B") : string
{
    return '';
}

/**
 * Generates a storable representation of a value.
 *
 * @param  mixed $value Whatever is to be serialised
 * @return string The serialisation
 */
function serialize($value) : string
{
    return '';
}

/**
 * Sets a user-defined error handler function.
 *
 * @param  ?mixed $error_handler The call back (null: reset to default)
 * @return mixed The previously defined error handler
 */
function set_error_handler($error_handler)
{
    return '';
}

/**
 * Sets a user-defined exception handler function.
 *
 * @param  ?mixed $exception_handler The call back (null: reset to default)
 * @return mixed The previously defined error handler
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
 * @param  string $name The name
 * @param  ?string $value The value (null: unset existing cookie)
 * @param  integer $expire Expiration timestamp (0: session cookie)
 * @param  ?string $path Path (null: current URL path)
 * @param  ?string $domain Domain (null: current URL domain)
 * @param  boolean $secure Whether the cookie is only for HTTPS
 * @param  boolean $httponly Whether the cookie will not be available to JavaScript
 * @return ?boolean Success status (fails if output already started) (null: failed also)
 */
function setcookie(string $name, ?string $value = null, int $expire = 0, ?string $path = null, ?string $domain = null, bool $secure = false, bool $httponly = false) : ?bool
{
    return false;
}

/**
 * Set locale information.
 *
 * @param  integer $category The locale category (LC_ALL, LC_COLLATE, LC_CTYPE, LC_MONETARY, LC_NUMERIC, LC_TIME)
 * @param  mixed $locale The locale (Some PHP versions require an array, and some a string with multiple calls)
 * @return ~string The set locale (false: error)
 */
function setlocale(int $category, $locale)
{
    return '';
}

/**
 * Calculate the sha1 hash of a string.
 *
 * @param  string $str The string to hash
 * @return string The hash of the string
 */
function sha1(string $str) : string
{
    return '';
}

/**
 * Calculate the sine of an angle.
 *
 * @param  float $arg The angle
 * @return float The sine of the angle
 */
function sin(float $arg) : float
{
    return 0.0;
}

/**
 * Return a formatted string.
 *
 * @param  string $format Formatting string
 * @param  mixed ...$args Arguments for the formatting string
 * @return string Formatted string
 */
function sprintf(string $format, ...$args) : string
{
    return '';
}

/**
 * Print a formatted string into a file.
 *
 * @param  resource $handle File to write to
 * @param  string $format Formatting string
 * @param  mixed ...$args Arguments for the formatting string
 * @return string Formatted string
 */
function fprintf($handle, string $format, ...$args) : string
{
    return '';
}

/**
 * Pad a string to a certain length with another string.
 *
 * @param  string $input The subject
 * @param  integer $pad_length The length to pad up to
 * @param  string $pad_string What we are padding with
 * @param  integer $pad_type The padding type (STR_PAD_RIGHT, STR_PAD_LEFT, STR_PAD_BOTH)
 * @return string The result
 */
function str_pad(string $input, int $pad_length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT) : string
{
    return '';
}

/**
 * Repeat a string.
 *
 * @param  string $input The string to repeat
 * @param  integer $multiplier How many times to repeat the string
 * @return string The result
 */
function str_repeat(string $input, int $multiplier) : string
{
    return '';
}

/**
 * Replace all occurrences of the search string with the replacement string.
 *
 * @param  mixed $search What's being replaced (string or array)
 * @param  mixed $replace What's being replaced with (string or array)
 * @param  mixed $subject Subject (string or array)
 * @return mixed Result (string or array)
 */
function str_replace($search, $replace, $subject)
{
    return '';
}

/**
 * Replace all occurrences of the search string with the replacement string (case-insensitive).
 *
 * @param  mixed $search What's being replaced (string or array)
 * @param  mixed $replace What's being replaced with (string or array)
 * @param  mixed $subject Subject (string or array)
 * @return mixed Result (string or array)
 */
function str_ireplace($search, $replace, $subject)
{
    return '';
}

/**
 * Binary safe string comparison.
 *
 * @param  string $str1 The first string
 * @param  string $str2 The second string
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2
 */
function strcmp(string $str1, string $str2) : int
{
    return 0;
}

/**
 * Strip HTML and PHP tags from a string.
 *
 * @param  string $str Subject
 * @param  string $allowed_tags Tags that should not be stripped (blank: strip all tags)
 * @return string Result
 */
function strip_tags(string $str, string $allowed_tags = '') : string
{
    return '';
}

/**
 * Quote string for encapsulation in a written string data type.
 *
 * @param  string $str Unslashed string
 * @return string Slashed string
 */
function addslashes(string $str) : string
{
    return '';
}

/**
 * Un-quote string slashed with addslashes.
 *
 * @param  string $str Slashed string
 * @return string Unslashed string
 */
function stripslashes(string $str) : string
{
    return '';
}

/**
 * Get string length.
 *
 * @param  string $str The string to get the length of
 * @return integer The string length
 */
function strlen(string $str) : int
{
    return 0;
}

// FUDGE: strpos can give "Offset not contained in string" error. We'd ideally have it in our catch errors list (codechecker.php) but it's unrealistic to catch all strpos errors.

/**
 * Find position of first occurrence of a string.
 *
 * @param  string $haystack Haystack
 * @param  string $needle Needle
 * @param  integer $offset Offset to search from
 * @return ~integer The offset it is found at (false: not found)
 */
function strpos(string $haystack, string $needle, int $offset = 0)
{
    return 0;
}

/**
 * Find position of first occurrence of a string (case-insensitive).
 *
 * @param  string $haystack Haystack
 * @param  string $needle Needle
 * @param  integer $offset Offset to search from
 * @return ~integer The offset it is found at (false: not found)
 */
function stripos(string $haystack, string $needle, int $offset = 0)
{
    return 0;
}

/**
 * Find position of last occurrence of a char in a string.
 *
 * @param  string $haystack Haystack
 * @param  string $needle Needle
 * @return ~integer The offset it is found at (false: not found)
 */
function strrpos(string $haystack, string $needle)
{
    return 0;
}

/**
 * Find position of last occurrence of a char in a string (case-insensitive).
 *
 * @param  string $haystack Haystack
 * @param  string $needle Needle
 * @return ~integer The offset it is found at (false: not found)
 */
function strripos(string $haystack, string $needle)
{
    return 0;
}

/**
 * Find first occurrence of a string.
 *
 * @param  string $haystack Haystack
 * @param  string $needle Needle
 * @param  boolean $before_needle The part of the haystack before the first occurrence of the needle (excluding the needle)
 * @return ~string The answer (false: does not occur)
 */
function strstr(string $haystack, string $needle, bool $before_needle = false)
{
    return '';
}

/**
 * Case-insensitive strstr.
 *
 * @param  string $haystack Haystack
 * @param  string $needle Needle
 * @param  boolean $before_needle The part of the haystack before the first occurrence of the needle (excluding the needle)
 * @return string All of haystack from the first occurrence of needle to the end
 */
function stristr(string $haystack, string $needle, bool $before_needle = false) : string
{
    return '';
}

/**
 * Tokenise string.
 *
 * @param  string $subject String to tokenise. EXCEPT if $delimiters=null, then this has actual delimiters.
 * @param  ?string $delimiters Delimiters (null: continue with previous tokenisation)
 * @return ~string Next token (false: could not return a token, no more tokens to return)
 */
function strtok(string $subject, ?string $delimiters = null)
{
    return '';
}

/**
 * Parse about any English textual datetime description into a UNIX timestamp.
 *
 * @param  string $time The subject
 * @param  ?TIME $now The timestamp to find times relative to (null: now)
 * @return TIME The timestamp (-1: failed)
 */
function strtotime(string $time, ?int $now = null) : int
{
    return 0;
}

/**
 * Translate certain characters.
 *
 * @param  string $string Subject
 * @param  mixed $replace_pairs Map of translations to do OR from string
 * @param  ?mixed $to To string (null: previous parameter was a map)
 * @return string Result
 */
function strtr(string $string, $replace_pairs, $to = null) : string
{
    return '';
}

/**
 * Get string value of a variable.
 *
 * @param  mixed $var The variable
 * @return string String value of the variable
 */
function strval($var) : string
{
    return '';
}

/**
 * Return part of a string.
 *
 * @param  string $string The subject
 * @param  integer $start The start position
 * @param  ?integer $length The length to extract (null: all remaining)
 * @return ~string String part (false: $start was over the end of the string)
 */
function substr(string $string, int $start, ?int $length = null)
{
    return '';
}

/**
 * Count the number of substring occurrences.
 *
 * @param  string $haystack The subject
 * @param  string $needle The substring to search for in the subject
 * @param  integer $offset Offset
 * @param  ?integer $maxlen Maximum length (null: no limit)
 * @return integer The number of times substring occurs in the subject
 */
function substr_count(string $haystack, string $needle, int $offset = 0, ?int $maxlen = null) : int
{
    return 0;
}

/**
 * Return current UNIX timestamp.
 *
 * @return TIME The timestamp
 */
function time() : int
{
    return 0;
}

/**
 * Strip whitespace from both ends of a string.
 *
 * @param  string $str String to trim from
 * @param  string $characters Characters to trim
 * @return string Trimmed string
 */
function trim(string $str, string $characters = " \t\n\r\0\x0B") : string
{
    return '';
}

/**
 * Generates a user-level error/warning/notice message.
 *
 * @param  string $error_msg The error message
 * @param  integer $error_type The PHP error type constant
 */
function trigger_error(string $error_msg, int $error_type)
{
}

/**
 * Generate a unique ID.
 * NOT CRYPTOGRAPHICALLY SECURE: USE get_secure_random_string() instead.
 *
 * @param  string $prefix Prefix for unique ID
 * @param  boolean $lcg Whether to add additional "combined LCG" entropy at the end of the return value. Always pass as true, because on some IIS systems the timer resolution will be in seconds.
 * @return string Unique ID
 */
function uniqid(string $prefix, bool $lcg) : string
{
    return '';
}

/**
 * Deletes a file.
 *
 * @param  PATH $filename The filename
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return boolean Success status
 */
function unlink(string $filename, $context = null) : bool
{
    return false;
}

/**
 * Creates a PHP value from a stored representation.
 *
 * @param  string $str Serialized string
 * @param  ?array $options Extra options (null: none)
 * @return ~mixed What was originally serialised (false: bad data given, or actually false was serialized)
 */
function unserialize(string $str, ?array $options = null)
{
    return 0;
}

/**
 * Unset a given variable.
 *
 * @param  mixed $var Unset this
 */
function unset(&$var)
{
}

/**
 * Decodes URL-encoded string.
 *
 * @param  string $str URL encoded string
 * @return string Pure string
 */
function urldecode(string $str) : string
{
    return '';
}

/**
 * URL-encodes string. Used for GET-parameter encoding ONLY.
 *
 * @param  string $str The pure string to URL encode
 * @return string URL encoded string
 */
function urlencode(string $str) : string
{
    return '';
}

/**
 * Wraps a string to a given number of characters using a string break character.
 *
 * @param  string $string Subject
 * @param  integer $width The word wrap position
 * @param  string $break The string to put at wrap points
 * @param  boolean $cut Whether to cut up words
 * @return string Word-wrapped string
 */
function wordwrap(string $string, int $width = 75, string $break = "\n", bool $cut = false) : string
{
    return '';
}

/**
 * Arc cosine.
 *
 * @param  float $arg Argument
 * @return float Angle
 */
function acos(float $arg) : float
{
    return 0.0;
}

/**
 * Pick one or more random entries out of an array.
 *
 * @param  array $input Array to choose from
 * @param  integer $num_req Number of entries required
 * @return mixed Random entry, or array of random entries if $num_req!=1
 */
function array_rand(array $input, int $num_req = 1)
{
    return 0;
}

/**
 * Prepend one or more elements to the beginning of array.
 *
 * @param  array $array Array to prepend to
 * @param  mixed ...$vars Elements to prepend
 * @return integer The new number of elements in the array
 */
function array_unshift(array &$array, ...$vars) : int
{
    return 0;
}

/**
 * Arc sine.
 *
 * @param  float $arg Argument
 * @return float Angle
 */
function asin(float $arg) : float
{
    return 0.0;
}

/**
 * Checks if assertion is FALSE.
 *
 * @param  string $assertion The expression to assert on
 * @param  mixed $description message / exception
 */
function assert(string $assertion, $description = '')
{
}

/**
 * Set/get the various assert flags (and sometimes, options for them).
 *
 * @param  integer $option The option (ASSERT_ACTIVE, ASSERT_WARNING, ASSERT_BAIL, ASSERT_QUIET_EVAL, ASSERT_CALLBACK)
 * @param  ?mixed $value The value for flag (null: N/A)
 * @return ~mixed Old value (false: error)
 */
function assert_options(int $option, $value = null)
{
    return 0;
}

/**
 * Arc tan.
 *
 * @param  float $num Argument
 * @return float Angle
 */
function atan(float $num) : float
{
    return 0.0;
}

/**
 * Convert a number between arbitrary bases (string representations).
 *
 * @param  string $number The string representation number to convert
 * @param  integer $frombase From base
 * @param  integer $tobase To base
 * @return string New base representation
 */
function base_convert(string $number, int $frombase, int $tobase) : string
{
    return '';
}

/**
 * Returns filename component of path.
 *
 * @param  PATH $path Path
 * @param  string $ext File extension to cut off (blank: none)
 * @return string File name component
 */
function basename(string $path, string $ext = '') : string
{
    return '';
}

/**
 * Convert binary data (in string form) into hexadecimal representation (in string form).
 *
 * @param  string $str Binary string
 * @return string Hex string
 */
function bin2hex(string $str) : string
{
    return '';
}

/**
 * Convert a hexadecimal representation of data (in string form) into binary data (in string form).
 *
 * @param  string $str Hex string
 * @return string Binary string
 */
function hex2bin(string $str) : string
{
    return '';
}

/**
 * Binary (string representation) to decimal (integer).
 *
 * @param  string $binary_string Binary in string form
 * @return integer Number
 */
function bindec(string $binary_string) : int
{
    return 0;
}

/**
 * Call a user function given with an array of parameters.
 *
 * @param  mixed $callback Callback
 * @param  array $parameters Parameters
 * @return mixed Whatever the function returned
 */
function call_user_func_array($callback, array $parameters)
{
    return 0;
}

/**
 * Whether the client has disconnected.
 *
 * @return boolean Whether the client has disconnected
 */
function connection_aborted() : bool
{
    return false;
}

/**
 * Returns connection status bitfield.
 *
 * @return integer Connection status bitfield
 */
function connection_status() : int
{
    return 0;
}

/**
 * Calculates the crc32 polynomial of a string.
 *
 * @param  string $str The string to get the CRC32 of
 * @return integer The CRC32
 */
function crc32(string $str) : int
{
    return 0;
}

/**
 * Decimal (integer) to binary (string representation).
 *
 * @param  integer $number Decimal
 * @return string String representation of binary number
 */
function decbin(int $number) : string
{
    return '';
}

/**
 * Determine whether a variable is empty (empty being defined differently for different types).
 * Note that the string '0' is considered empty. If you don't want that, use cms_empty_safe (maybe with @), or a combination of isset and is_numeric.
 * Generally this function is used if you don't want to check for array index presence, non-nullness, non-blank-stringness, and non-zeroness via individual ANDd clauses.
 *
 * @param  mixed $var Input
 * @return boolean Whether it is CONSIDERED empty
 */
function empty($var) : bool
{
    return false;
}

/**
 * Set the internal pointer of an array to its last element.
 *
 * @param  array $array The array
 * @return mixed Value of the last element
 */
function end(array $array)
{
    return 0;
}

/**
 * Flushes the output to a file.
 *
 * @param  resource $handle The file handle to flush
 * @return boolean Success status
 */
function fflush($handle) : bool
{
    return false;
}

/**
 * Gets last access time of file.
 *
 * @param  PATH $filename The filename
 * @return ~TIME Timestamp of last access (false: error)
 */
function fileatime(string $filename)
{
    return 0;
}

/**
 * Portable advisory file locking.
 *
 * @param  resource $handle File handle
 * @param  integer $operation Operation (LOCK_SH, LOCK_EX, LOCK_UN)
 * @return boolean Success status
 */
function flock($handle, int $operation) : bool
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
 * @param  string $ip_address IP address
 * @return string Host name OR IP address if failed to look up
 */
function gethostbyaddr(string $ip_address) : string
{
    return '';
}

/**
 * Get the IP address corresponding to a given Internet host name.
 *
 * @param  string $hostname Host name
 * @return string IP address OR host name if failed to look up
 */
function gethostbyname(string $hostname) : string
{
    return '';
}

/**
 * Get largest possible random value.
 *
 * @return integer Largest possible random value
 */
function getrandmax() : int
{
    return 0;
}

/**
 * Converts a string containing an (IPv4) Internet Protocol dotted address into a proper address.
 *
 * @param  string $ip_address The IP address
 * @return ~integer The long form (false: cannot perform conversion)
 */
function ip2long(string $ip_address)
{
    return 0;
}

/**
 * Fetch a key from an associative array.
 *
 * @param  array $array The array
 * @return mixed The index element of the current array position
 */
function key(array $array)
{
    return 0;
}

/**  --> Use similar_text
 * Calculate Levenshtein distance between two strings.
 *
 * @param  string $str1 First string
 * @param  string $str2 Second string
 * @return integer Distance
 */
function levenshtein(string $str1, string $str2) : int
{
    return 0;
}

/**
 * Logarithm.
 *
 * @param  float $arg Number to find log of
 * @param  float $base Base
 * @return float Log of given number
 */
function log(float $arg, float $base = 2.718281828459) : float
{
    return 0.0;
}

/**
 * Base-10 logarithm.
 *
 * @param  float $arg Number to find log of
 * @return float Log of given number
 */
function log10(float $arg) : float
{
    return 0.0;
}

/**
 * Converts an (IPv4) Internet network address into a string in Internet standard dotted format.
 *
 * @param  integer $proper_address The IP address
 * @return string  The long form
 */
function long2ip(int $proper_address) : string
{
    return '';
}

/**
 * Calculates the md5 hash of the file identified by the given filename.
 *
 * @param  PATH $filename File name
 * @return ~string The hash of the file (false: error)
 */
function md5_file(string $filename)
{
    return '';
}

/**
 * Advance the internal array pointer of an array.
 *
 * @param  array $array The array
 * @return mixed The array value we're now pointing at
 */
function next(array $array)
{
    return 0;
}

/**
 * Returns the JSON representation of a value.
 *
 * @param  mixed $value The value being encoded. Can be any type except a resource.
 * @param  integer $options Bitmask of options
 * @return string Encoded data
 */
function json_encode($value, int $options = 0) : string
{
    return '';
}

/**
 * Decodes a JSON string.
 *
 * @param  string $json The JSON string being decoded
 * @param  boolean $assoc Whether returned objects will be converted into associative arrays
 * @param  integer $depth User specified recursion depth
 * @return ~mixed Decoded data (false: error)
 */
function json_decode(string $json, bool $assoc = false, int $depth = 512)
{
    return [];
}

/**
 * Returns the last error occurred.
 *
 * @return integer Last error, a JSON_* constant
 */
function json_last_error() : int
{
    return 0;
}

/**
 * Returns the last JSON error occurred.
 *
 * @return string Last error message
 */
function json_last_error_msg() : string
{
    return '';
}

/**
 * Get value of PI.
 *
 * @return float PI
 */
function pi() : float
{
    return 0.0;
}

/**
 * Exponential expression.
 *
 * @param  mixed $base Base (integer or float)
 * @param  mixed $exp Exponent (integer or float)
 * @return mixed Result (integer or float)
 */
function pow($base, $exp)
{
    return 0.0;
}

/**
 * Quote regular expression characters.
 *
 * @param  string $str The string to escape
 * @param  string $surround_char Extra character to escape, was used in regular expression to surround it
 * @return string The escape string
 */
function preg_quote(string $str, string $surround_char = '/') : string
{
    return '';
}

/**
 * Rewind the internal array pointer.
 *
 * @param  array $array The array
 * @return mixed The array value we're now pointing at
 */
function prev(array $array)
{
    return 0;
}

/**
 * Converts the radian number to the equivalent number in degrees.
 *
 * @param  float $number The angle in radians
 * @return float The angle in degrees
 */
function rad2deg(float $number) : float
{
    return 0.0;
}

/**
 * Create a sequence in an array.
 *
 * @param  mixed $from From (integer or character string)
 * @param  mixed $to To (integer or character string)
 * @param  integer $step Step
 * @return array The sequence
 */
function range($from, $to, int $step = 1) : array
{
    return [];
}

/**
 * Outputs a file.
 * Call cms_ob_end_clean() first, else too much memory will be used.
 *
 * @param  PATH $filename The filename
 * @param  boolean $use_include_path Whether to search within the include path
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return ~integer The number of bytes read (false: error)
 */
function readfile(string $filename, bool $use_include_path = false, $context = null)
{
    return 0;
}

/**
 * Shuffle an array.
 *
 * @param  array $array The array to shuffle
 */
function shuffle(array $array)
{
}

/**
 * Calculate the similarity between two strings.
 *
 * @param  string $first First string
 * @param  string $second Second string
 * @param  ?float $percent Returns the percentage of similarity (null: do not get)
 * @return integer The number of matching characters
 */
function similar_text(string $first, string $second, ?float &$percent = null) : int
{
    return 0;
}

/**
 * Square root.
 *
 * @param  float $arg Number
 * @return float return 0.0.
 */
function sqrt(float $arg) : float
{
    return 0.0;
}

/**
 * Binary safe case-insensitive string comparison.
 *
 * @param  string $str1 The first string
 * @param  string $str2 The second string
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2
 */
function strcasecmp(string $str1, string $str2) : int
{
    return 0;
}

/**
 * Find length of initial segment not matching mask.
 *
 * @param  string $str1 The subject string
 * @param  string $str2 The string of stop characters
 * @return integer The length
 */
function strcspn(string $str1, string $str2) : int
{
    return 0;
}

/**
 * Case-insensitive string comparisons using a "natural order" algorithm.
 *
 * @param  string $str1 The first string
 * @param  string $str2 The second string
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2
 */
function strnatcasecmp(string $str1, string $str2) : int
{
    return 0;
}

/**
 * String comparisons using a "natural order" algorithm.
 *
 * @param  string $str1 The first string
 * @param  string $str2 The second string
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2
 */
function strnatcmp(string $str1, string $str2) : int
{
    return 0;
}

/**
 * Binary safe case-insensitive string comparison of the first n characters.
 *
 * @param  string $str1 The first string
 * @param  string $str2 The second string
 * @param  integer $len Up to this length (n)
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2
 */
function strncasecmp(string $str1, string $str2, int $len) : int
{
    return 0;
}

/**
 * Binary safe string comparison of the first n characters.
 *
 * @param  string $str1 The first string
 * @param  string $str2 The second string
 * @param  integer $len Up to this length (n)
 * @return integer <0 if s1<s2, 0 if s1=s2, >1 if s1>s2
 */
function strncmp(string $str1, string $str2, int $len) : int
{
    return 0;
}

/**
 * Find the last occurrence of a character in a string.
 *
 * @param  string $haystack Haystack
 * @param  string $needle Needle (string of length 1)
 * @length 1
 * @return string The portion of haystack which starts at the last occurrence of needle and goes until the end of haystack
 */
function strrchr(string $haystack, string $needle) : string
{
    return '';
}

/**
 * Reverse a string.
 *
 * @param  string $string String to reverse
 * @return string Reversed string
 */
function strrev(string $string) : string
{
    return '';
}

/**
 * Find length of initial segment matching mask.
 *
 * @param  string $string String to work upon
 * @param  string $mask String consisting of alternative characters to require along our run
 * @return string The length of the initial segment of string which consists entirely of characters in mask
 */
function strspn(string $string, string $mask) : string
{
    return '';
}

/**
 * Replace text within a portion of a string.
 *
 * @param  string $string The subject string
 * @param  string $replacement The replacement string
 * @param  integer $start The start position of what's being replaced
 * @param  ?integer $length The run-length of what is being replaced (null: go to end of string)
 * @return string A copy of string delimited by the start and (optionally) length parameters with the string given in replacement
 */
function substr_replace(string $string, string $replacement, int $start, ?int $length = null) : string
{
    return '';
}

/**
 * Calculate the tangent of an angle.
 *
 * @param  float $arg The angle in radians
 * @return float The tangent
 */
function tan(float $arg) : float
{
    return 0.0;
}

/**
 * Unpack data from binary string.
 *
 * @param  string $format The formatting string for unpacking
 * @param  string $data The data to unpack
 * @param  integer $offset The offset to begin unpacking from
 * @return ~array The unpacked data (false: error)
 */
function unpack(string $format, string $data, int $offset = 0)
{
    return [];
}

/**
 * Compares two "PHP-standardsedd" version number strings.
 *
 * @param  string $version1 First version number
 * @param  string $version2 Second version number
 * @param  ?string $compare_symbol The operator to compare with (null: unified)
 * @return mixed For unified: -1 if v1<v2, 0 if v1=v2, 1 if v1>v2. Else BINARY or boolean.
 */
function version_compare(string $version1, string $version2, ?string $compare_symbol = null)
{
    return 0;
}

/**
 * Get the type of a variable.
 *
 * @param  mixed $var The variable
 * @return string The type
 */
function gettype($var) : string
{
    return '';
}

/**
 * Dumps information about a variable.
 *
 * @param  mixed $expression Data
 */
function var_dump($expression)
{
}

/**
 * Output a formatted string (similar to printf, but takes an array of arguments).
 *
 * @param  string $format Formatting string
 * @param  array $args Arguments
 * @return integer Length of outputted string
 */
function vprintf(string $format, array $args) : int
{
    return 0;
}

/**
 * Return a formatted string (similar to sprintf, but takes an array of arguments).
 *
 * @param  string $format Formatting string
 * @param  array $args Arguments
 * @return string Fixed string
 */
function vsprintf(string $format, array $args) : string
{
    return '';
}

/**
 * Sets access and modification time of file.
 *
 * @param  PATH $filename File to touch
 * @param  ?TIME $time New modification time (null: do not change)
 * @param  ?TIME $atime New access time (null: do not change)
 * @return boolean Success status
 */
function touch(string $filename, ?int $time = null, ?int $atime = null) : bool
{
    return true;
}

/**
 * Hyperbolic tangent.
 *
 * @param  float $in In
 * @return float Out
 */
function tanh(float $in) : float
{
    return 0.0;
}

/**
 * Hyperbolic sine.
 *
 * @param  float $in In
 * @return float Out
 */
function sinh(float $in) : float
{
    return 0.0;
}

/**
 * Un-quote string quoted with addcslashes.
 *
 * @param  string $in In
 * @return string Out
 */
function stripcslashes(string $in) : string
{
    return '';
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
 * @param  resource $handle File handle
 * @return boolean Success status
 */
function rewind($handle) : bool
{
    return true;
}

/**
 * Calculates the exponent of e.
 *
 * @param  float $arg Arg
 * @return float Result
 */
function exp(float $arg) : float
{
    return 0.0;
}

/**
 * Combined linear congruential generator.
 *
 * @return float Random number
 */
function lcg_value() : float
{
    return 0.0;
}

/**
 * Get the local time.
 *
 * @param  ?TIME $timestamp Timestamp (null: now)
 * @param  boolean $associative If set to FALSE or not supplied than the array is returned as a regular, numerically indexed array. If the argument is set to TRUE then localtime() is an associative array containing all the different elements of the structure returned by the C function call to localtime.
 * @return array Components
 */
function localtime(?int $timestamp, bool $associative = false) : array
{
    return [];
}

/**
 * Quote string with slashes in a C style.
 *
 * @param  string $str Input string
 * @param  string $charlist Chars to convert
 * @return string Result
 */
function addcslashes(string $str, string $charlist) : string
{
    return '';
}

/**
 * Filters elements of an array using a callback function.
 *
 * @param  array $input In
 * @param  ?mixed $callback The filter function callback (null: filter out false's)
 * @return array Out
 */
function array_filter(array $input, $callback = null) : array
{
    return [];
}

/**
 * Applies the callback to the elements of the given array.
 *
 * @param  mixed $callback Callback map function
 * @param  array $array In
 * @return array Out
 */
function array_map($callback, array $array) : array
{
    return [];
}

/**
 * Add all the elements of an array.
 *
 * @param  array $array In
 * @return mixed The sum (float or integer)
 */
function array_sum(array $array)
{
    return [];
}

/**
 * Merges the elements of one or more arrays together so that the values of one are appended to the end of the previous one. It returns the resulting array.
 * If the input arrays have the same string keys, then the values for these keys are merged together into an array, and this is done recursively, so that if one of the values is an array itself, the function will merge it with a corresponding entry in another array too. If, however, the arrays have the same numeric key, the later value will not overwrite the original value, but will be appended.
 *
 * @param  array ...$arrays Arrays to merge
 * @return array Result
 */
function array_merge_recursive(array ...$arrays) : array
{
    return [];
}

/**
 * Sort multiple or multi-dimensional array.
 *
 * @param  array $array Array to sort
 * @param  mixed ...$args Other arguments
 * @return array Result
 */
function array_multisort(array $array, ...$args) : array
{
    return [];
}

/**
 * Sort an array in reverse order and maintain index association.
 *
 * @param  array $array Array
 * @param  integer $sort_flags Sort flags
 */
function arsort(array &$array, int $sort_flags = 0)
{
}

/**
 * Sort an array and maintain index association.
 *
 * @param  array $array Array
 * @param  integer $sort_flags Sort flags
 */
function asort(array &$array, int $sort_flags = 0)
{
}

/**
 * Sort an array by values using a user-defined comparison function.
 *
 * @param  array $array The array
 * @param  mixed $cmp_function Comparison function
 */
function usort(array &$array, $cmp_function)
{
}

/**
 * Sort an array by keys using a user-defined comparison function.
 *
 * @param  array $array The array
 * @param  mixed $cmp_function Comparison function
 */
function uksort(array &$array, $cmp_function)
{
}

/**
 * Sort an array with a user-defined comparison function and maintain index association.
 *
 * @param  array $array The array
 * @param  mixed $cmp_function Comparison function
 */
function uasort(array &$array, $cmp_function)
{
}

/**
 * Sort an array.
 *
 * @param  array $array The array
 * @param  integer $sort_flags Sort flags
 */
function sort(array &$array, int $sort_flags = 0)
{
}

/**
 * Sort an array in reverse order.
 *
 * @param  array $array The array to sort
 * @param  integer $sort_flags Sort flags
 */
function rsort(array &$array, int $sort_flags = 0)
{
}

/**
 * Sort an array by key in reverse order.
 *
 * @param  array $array The array to sort
 * @param  integer $sort_flags Sort flags
 */
function krsort(array &$array, int $sort_flags = 0)
{
}

/**
 * Sort an array by key.
 *
 * @param  array $array The array to sort
 * @param  integer $sort_flags Sort flags
 */
function ksort(array &$array, int $sort_flags = 0)
{
}

/**
 * Pad array to the specified length with a value.
 *
 * @param  array $input Input
 * @param  integer $pad_size Pad size
 * @param  mixed $pad_value Pad value
 * @return array Output
 */
function array_pad(array $input, int $pad_size, $pad_value) : array
{
    return [];
}

/**
 * Iteratively reduce the array to a single value using a callback function.
 *
 * @param  array $input Input
 * @param  mixed $callback Process function
 * @param  ?integer $initial Initial value (null: no initial)
 * @return ?integer Result (null: no initial given, and empty array given)
 */
function array_reduce(array $input, $callback, ?int $initial = null) : ?int
{
    return 0;
}

/**
 * Apply a user function to every member of an array .
 *
 * @param  array $array Data
 * @param  mixed $callback Process function
 * @return boolean Success status
 */
function array_walk(array &$array, $callback) : bool
{
    return true;
}

/**
 * Arc tangent of two variables.
 *
 * @param  float $x First
 * @param  float $y Second
 * @return float Result
 */
function atan2(float $x, float $y) : float
{
    return 0.0;
}

/**
 * Gets character from file pointer.
 *
 * @param  resource $handle Handle
 * @return ~string Character (false: error)
 */
function fgetc($handle)
{
    return '';
}

/**
 * Parses input from a file according to a format.
 *
 * @param  resource $handle File handle
 * @param  string $format Formatting string
 * @return ~array Data (false: error)
 */
function fscanf($handle, string $format)
{
    return [];
}

/**
 * Gets information about a file.
 *
 * @param  PATH $path File
 * @return ~array Map of status information (false: error)
 */
function stat(string $path)
{
    return [];
}

/**
 * Gets information about a file using an open file pointer.
 *
 * @param  resource $handle File handle
 * @return ~array Map of status information (false: error)
 */
function fstat($handle)
{
    return [];
}

/**
 * Truncates a file to a given length.
 *
 * @param  resource $file File handle
 * @param  integer $size Cut off size
 * @return boolean Success status
 */
function ftruncate($file, int $size) : bool
{
    return true;
}

/**
 * Return an item from the argument list.
 *
 * @param  integer $arg_num Argument number
 * @return mixed Argument
 */
function func_get_arg(int $arg_num)
{
    return '';
}

/**
 * Returns an array comprising a function's argument list.
 *
 * @return array List of arguments
 */
function func_get_args() : array
{
    return [];
}

/**
 * Returns the number of arguments passed to the function.
 *
 * @return integer Number of arguments
 */
function func_num_args() : int
{
    return 0;
}

/**
 * Parse a configuration file.
 *
 * @param  PATH $filename The file path
 * @param  boolean $process_sections Whether to process sections
 * @param  integer $scanner_mode Any INI_SCANNER_* constant
 * @return ~array Map of Ini file data (2d if processed sections) (false: error)
 */
function parse_ini_file(string $filename, bool $process_sections = false, int $scanner_mode = INI_SCANNER_NORMAL)
{
    return [];
}

/**
 * Parse a configuration string.
 *
 * @param  PATH $string The configuration string
 * @param  boolean $process_sections Whether to process sections
 * @param  integer $scanner_mode Any INI_SCANNER_* constant
 * @return ~array Map of Ini file data (2d if processed sections) (false: error)
 */
function parse_ini_string(string $string, bool $process_sections = false, int $scanner_mode = INI_SCANNER_NORMAL)
{
    return [];
}

/**
 * Parses the string into variables.
 *
 * @param  string $str Query string to parse
 * @param  array $arr Target for variable mappings
 */
function parse_str(string $str, array &$arr)
{
}

/**
 * Finds whether a variable is a scalar (integer, float, string or boolean).
 *
 * @param  mixed $var Variable
 * @return boolean Whether it is
 */
function is_scalar($var) : bool
{
    return true;
}

/**
 * Inserts HTML line breaks before all newlines in a string.
 *
 * @param  string $in In
 * @param  boolean $is_xhtml Whether to use XHTML compatible line breaks or not
 * @return string Out
 */
function nl2br(string $in, bool $is_xhtml = true) : string
{
    return '';
}

/**
 * Output a formatted string.
 *
 * @param  string $format Formatting string
 * @param  mixed ...$args Arguments for the formatting string
 * @return string Assembled string
 */
function printf(string $format, ...$args) : string
{
    return '';
}

/**
 * Hyperbolic cosine.
 *
 * @param  float $arg Argument
 * @return float Result
 */
function cosh(float $arg) : float
{
    return 0.0;
}

/**
 * Run some code. Do not use unless absolutely needed.
 *
 * @param  string $code Code to run
 * @return mixed Result
 */
function eval(string $code)
{
    return 0;
}

/**
 * Get or Set the HTTP response code.
 *
 * @param  ?integer $response_code Response code to set (null: don't set)
 * @return mixed The previous response code if $response_code was set / current response mode if $response_code is not set / false if CLI / true if CLI but $response_code was set
 */
function http_response_code(?int $response_code = null)
{
    return false;
}

/**
 * Get the size of an image.
 *
 * @param  PATH $filename Filename
 * @param  ?array $image_info Extra details will be put here (null: return-only). Note that this is actually passed by reference, but is also optional.
 * @return ~array List of details: $width, $height, $type, $attr (false: error)
 */
function getimagesize(string $filename, ?array $image_info = null)
{
    return [];
}

/**
 * Get the size of an image from a string.
 *
 * @param  string $imagedata The image data
 * @param  ?array $image_info Extra details will be put here (null: return-only). Note that this is actually passed by reference, but is also optional.
 * @return ~array List of details: $width, $height, $type, $attr (false: error)
 */
function getimagesizefromstring(string $imagedata, ?array $image_info = null)
{
    return [];
}

/**
 * Gets time of last page modification.
 *
 * @return TIME Last modification time
 */
function getlastmod() : int
{
    return 0;
}

/**
 * Get current time.
 *
 * @param  boolean $return_float Return as float
 * @return array Map of time details
 */
function gettimeofday(bool $return_float = false) : array
{
    return [];
}

/**
 * Gets the value of a PHP configuration option.
 *
 * @param  string $varname Value name to get
 * @return ~string Value (false: error)
 */
function get_cfg_var(string $varname)
{
    return '';
}

/**
 * Calculate the length of the hypotenuse of a right-angle triangle.
 *
 * @param  float $x X
 * @param  float $y Y
 * @return float Result
 */
function hypot(float $x, float $y) : float
{
    return 0.0;
}

/**
 * Set whether a client disconnect should abort script execution.
 *
 * @param  boolean $setting Setting
 * @return boolean Previous setting
 */
function ignore_user_abort(bool $setting) : bool
{
    return true;
}

/**
 * Get the contents of a file.
 *
 * @param  SHORT_TEXT $filename The file name
 * @param  boolean $use_include_path Whether to search within the include path
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @param  integer $offset Offset
 * @param  ?integer $maxlen Maximum length (null: no limit)
 * @return ~LONG_TEXT The file contents (false: error)
 */
function file_get_contents(string $filename, bool $use_include_path = false, $context = null, int $offset = 0, ?int $maxlen = null)
{
    return '';
}

/**
 * Decode the HTML entity encoded input string.
 *
 * @param  string $input The text to decode
 * @param  integer $quote_style The quote style code
 * @param  ?string $charset Character set to decode to (null: default)
 * @return string The decoded text
 */
function html_entity_decode(string $input, int $quote_style, ?string $charset = null) : string
{
    return '';
}

/**
 * Creates an array by using one array for keys and another for its values.
 *
 * @param  array $keys Keys
 * @param  array $values Values
 * @return array Combined
 */
function array_combine(array $keys, array $values) : array
{
    return [];
}

/**
 * Apply a user function recursively to every member of an array.
 *
 * @param  array $input The input array
 * @param  mixed $funcname Callback
 * @param  ?mixed $userdata If the optional userdata parameter is supplied, it will be passed as the third parameter to the callback funcname (null: no user data)
 * @return boolean Result
 */
function array_walk_recursive(array $input, $funcname, $userdata = null) : bool
{
    return true;
}

/**
 * Convert a string to an array.
 *
 * @param  string $str The input string
 * @param  integer $split_length Maximum length of the chunk
 * @return array Result
 */
function str_split(string $str, int $split_length = 1) : array
{
    return [];
}

/**
 * Search a string for any of a set of characters.
 *
 * @param  string $haystack The string where char_list is looked for
 * @param  string $char_list The character list
 * @return ~string String starting from the character found, or FALSE if it is not found (false: not found)
 */
function strpbrk(string $haystack, string $char_list)
{
    return '';
}

/**
 * Binary safe optionally case-insensitive comparison of two strings from an offset, up to length characters.
 *
 * @param  string $main_str The main string being compared
 * @param  string $str The secondary string being compared
 * @param  integer $offset The start position for the comparison. If negative, it starts counting from the end of the string.
 * @param  ?integer $length The length of the comparison (null: the largest of the length of the str compared to the length of main_str less the offset)
 * @param  boolean $case_insensitivity Whether to compare as case-insensitive
 * @return ~integer Returns < 0 if main_str from position offset is less than str, > 0 if it is greater than str, and 0 if they are equal (false: out of bounds)
 */
function substr_compare(string $main_str, string $str, int $offset, ?int $length = null, bool $case_insensitivity = false)
{
    return 0;
}

/**
 * Write a string to a file.
 *
 * @param  PATH $filename Path to the file where to write the data
 * @param  string $data The data to write
 * @param  integer $flags Supported flags
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return ~integer Bytes written (false: error)
 */
function file_put_contents(string $filename, string $data, int $flags = 0, $context = null)
{
    return 0;
}

/**
 * Fetches all the headers sent by the server in response to a HTTP request.
 *
 * @param  URLPATH $url The target URL
 * @param  BINARY $parse Whether to parse into a map
 * @param  ?resource $context A stream context to attach to (null: no special context)
 * @return array Result
 */
function get_headers(string $url, int $parse = 0, $context = null) : array
{
    return [];
}

/**
 * Returns a list of response headers sent (or ready to send).
 *
 * @return array List of headers
 */
function headers_list() : array
{
    return [];
}

/**
 * Generate URL-encoded query string.
 *
 * @param  array $query_data URL parameters
 * @return string URL
 */
function http_build_query(array $query_data) : string
{
    return '';
}

/**
 * List files and directories inside the specified path.
 *
 * @param  PATH $directory Directory
 * @return ~array Files (false: error)
 */
function scandir(string $directory)
{
    return [];
}

/**
 * Randomly shuffles a string.
 *
 * @param  string $in In
 * @return string Out
 */
function str_shuffle(string $in) : string
{
    return '';
}

/**
 * Finds the path of the directory PHP stores temporary files in by default.
 *
 * @return string The path of the temporary directory
 */
function sys_get_temp_dir() : string
{
    return '';
}

/**
 * Get the last occurred error.
 *
 * @return ?array Details of the last occurred error (map with 'type', 'message', 'file', 'line') (null: none)
 */
function error_get_last() : ?array
{
    return [];
}

/**
 * Clear the most recent error.
 */
function error_clear_last()
{
}

/**
 * Find pathnames matching a pattern.
 *
 * @param  string $pattern Pattern according to the rules used by the libc glob
 * @param  integer $flags Flags
 * @return ~array Files found (false: error)
 */
function glob(string $pattern, int $flags = 0)
{
    return [];
}

/**
 * Generates a backtrace.
 *
 * @param  boolean $provide_object Provide object part in results
 * @param  integer $limit Maximum stack frames to return (0: no limit)
 * @return array Backtrace
 */
function debug_backtrace(bool $provide_object = true, int $limit = 0) : array
{
    return [];
}

/**
 * Generates a backtrace to the output.
 *
 * @param  boolean $provide_object Provide object part in results
 * @param  integer $limit Maximum stack frames to return (0: no limit)
 */
function debug_print_backtrace(bool $provide_object = true, int $limit = 0)
{
}

/**
 * Sets the default timezone used by all date/time functions in a script.
 *
 * @param  string $timezone_identifier Timezone identifier
 * @return boolean Success status
 */
function date_default_timezone_set(string $timezone_identifier) : bool
{
    return true;
}

/**
 * Gets the default timezone used by all date/time functions in a script.
 *
 * @return string The timezone identifier
 */
function date_default_timezone_get() : string
{
    return '';
}

/**
 * Computes the difference of arrays using keys for comparison.
 *
 * @param  array $array1 Array 1
 * @param  array $array2 Array 2
 * @return array Result
 */
function array_diff_key(array $array1, array $array2) : array
{
    return [];
}

/**
 * Converts a human readable IP address to its packed in_addr representation.
 *
 * @param  string $address A human readable IPv4 or IPv6 address
 * @return ~string The in_addr representation of the given address (false: error)
 */
function inet_pton(string $address)
{
    return '';
}

/**
 * Converts a packed Internet address to a human readable representation.
 *
 * @param  string $in_addr Converts a packed Internet address to a human readable representation
 * @return ~string A string representation of the address (false: error)
 */
function inet_ntop(string $in_addr)
{
    return '';
}

/**
 * Format line as CSV and write to file pointer.
 *
 * @param  resource $handle File pointer
 * @param  array $fields An array of values
 * @param  string $delimiter The optional delimiter parameter sets the field delimiter (one character only)
 * @param  string $enclosure The optional enclosure parameter sets the field enclosure (one character only)
 * @param  string $escape Set the escape character (one character only)
 * @return ~integer The length of the written string (false: error)
 */
function fputcsv($handle, array $fields, string $delimiter = ',', string $enclosure = '"', string $escape = '\\')
{
    return 0;
}

/**
 * Match filename against a pattern.
 *
 * @param  string $pattern The shell wildcard pattern
 * @param  string $string The tested string
 * @param  integer $flags FNM_* flags
 * @return boolean Answer
 */
function fnmatch(string $pattern, string $string, int $flags = 0) : bool
{
    return false;
}

/**
 * Finds whether a value is not a number.
 *
 * @param  float $val The value to check
 * @return boolean Answer
 */
function is_nan(float $val) : bool
{
    return true;
}

/**
 * Finds whether a value is a legal finite number.
 *
 * @param  float $val The value to check
 * @return boolean Answer
 */
function is_finite(float $val) : bool
{
    return true;
}

/**
 * Finds whether a value is infinite.
 *
 * @param  float $val The value to check
 * @return boolean Answer
 */
function is_infinite(float $val) : bool
{
    return true;
}

/**
 * Split an array into chunks.
 *
 * @param  array $input The array to work on
 * @param  integer $size The size of each chunk
 * @param  boolean $preserve_keys When set to TRUE keys will be preserved. Default is FALSE which will reindex the chunk numerically.
 * @return array A multidimensional numerically indexed array, starting with zero, with each dimension containing size elements
 */
function array_chunk(array $input, int $size, bool $preserve_keys = false) : array
{
    return [];
}

/**
 * Fill an array with values.
 *
 * @param  integer $start_index The first index of the returned array. If start_index is negative, the first index of the returned array will be start_index and the following indices will start from zero.
 * @param  integer $num Number of elements to insert. Must be greater than zero.
 * @param  mixed $value Value to use for filling
 * @return array The filled array
 */
function array_fill(int $start_index, int $num, $value) : array
{
    return [];
}

/**
 * Fill an array with values, specifying keys.
 *
 * @param  array $keys Keys
 * @param  mixed $value Value to fill with
 * @return array Filled array
 */
function array_fill_keys(array $keys, $value) : array
{
    return [];
}

/**
 * Changes all keys in an array.
 *
 * @param  array $input The array to work on
 * @param  integer $case Either CASE_UPPER or CASE_LOWER
 * @return array An array with its keys lower or uppercased
 */
function array_change_key_case(array $input, int $case) : array
{
    return [];
}

/**
 * Outputs or returns a parsable string representation of a variable.
 *
 * @param  mixed $expression The variable you want to export
 * @param  boolean $return If used and set to TRUE, var_export() will return the variable representation instead of outputting it
 * @return ?string Variable representation (null: asked to not return a value)
 */
function var_export($expression, bool $return = false) : ?string
{
    return '';
}

/**
 * Creates a stream context.
 *
 * @param  array $options Options
 * @param  array $params Parameters. Usually options is used, parameters not needed and refers to standard parameters for all context types.
 * @return resource Stream context
 */
function stream_context_create(array $options = [], array $params = [])
{
    return [];
}

/**
 * Returns the amount of memory allocated to PHP.
 *
 * @param  boolean $real_usage Get total memory allocated, including unused pages
 * @return integer The amount of memory, in bytes, that's currently being allocated to your PHP script
 */
function memory_get_usage(bool $real_usage = false) : int
{
    return 0;
}

/**
 * Returns the amount of memory allocated to PHP at peak.
 *
 * @param  boolean $real_usage Get total memory allocated, including unused pages
 * @return integer The amount of memory, in bytes, at peak
 */
function memory_get_peak_usage(bool $real_usage = false) : int
{
    return 0;
}

/**
 * Parse a binary IPTC block into single tags.
 *
 * @param  string $iptcblock A binary IPTC block
 * @return array Returns an array using the tagmarker as an index and the value as the value. It returns FALSE on error or if no IPTC data was found.
 */
function iptcparse(string $iptcblock) : array
{
    return [];
}

/**
 * Embed an IPTC block in a JPEG file.
 *
 * @param  string $iptcdata The data to be written
 * @param  PATH $jpeg_file_name Path to the JPEG image
 * @param  integer $spool Spool flag. If the spool flag is less than 2 then the JPEG will be returned as a string. Otherwise the JPEG will be printed to STDOUT.
 * @return mixed If spool is less than 2, the JPEG will be returned, or FALSE on failure. Otherwise returns TRUE on success or FALSE on failure.
 */
function iptcembed(string $iptcdata, string $jpeg_file_name, int $spool = 0)
{
    return null;
}

/**
 * Register given function as __autoload() implementation.
 *
 * @param  mixed $autoload_function The autoload function being registered
 * @param  boolean $throw Throw error if cannot register
 * @param  boolean $prepend Prepend to queue rather than append
 * @return boolean Success status
 */
function spl_autoload_register($autoload_function, bool $throw = true, bool $prepend = false) : bool
{
    return true;
}

/**
 * Gets options from the command line argument list.
 *
 * @param  string $options Each character in this string will be used as option characters and matched against options passed to the script starting with a single hyphen (-)
 * @param  array $longopts Each element in this array will be used as option strings and matched against options passed to the script starting with two hyphens (--)
 * @param  integer $optind The index where argument parsing stopped
 * @return array Map of options
 */
function getopt(string $options, array $longopts = [], int &$optind = 0) : array
{
    return [];
}

/**
 * Gets the current resource usages.
 *
 * @param  integer $who If who is 1, getrusage will be called with RUSAGE_CHILDREN
 * @return array Map of usage data
 */
function getrusage(int $who) : array
{
    return [];
}

/**
 * Generates cryptographically secure pseudo-random bytes.
 *
 * @param  integer $length The length of the random string that should be returned in bytes
 * @return string A string containing the requested number of cryptographically secure random bytes
 */
function random_bytes(int $length) : string
{
    return '';
}

/**
 * Generates cryptographically secure pseudo-random integers.
 *
 * @param  integer $min The lowest value to be returned, which must be PHP_INT_MIN or higher
 * @param  integer $max The highest value to be returned, which must be less than or equal to PHP_INT_MAX
 * @return integer A cryptographically secure random integer in the range min to max, inclusive
 */
function random_int(int $min, int $max) : int
{
    return 0;
}

/**
 * Integer division.
 *
 * @param  integer $dividend Number to be divided
 * @param  integer $divisor Number which divides the dividend
 * @return integer The integer quotient of the division
 */
function intdiv(int $dividend, int $divisor) : int
{
    return 0;
}

/**
 * Gets the class methods' names.
 *
 * @param  string $class_name The class name or an object instance
 * @return ?array An array of method names defined for the class specified by $class_name (null: error)
 */
function get_class_methods(string $class_name) : ?array
{
    return [];
}

/**
 * Get the default properties of the class.
 *
 * @param  string $class_name The class name
 * @return ~array an associative array of declared properties visible from the current scope, with their default value (false: error)
 */
function get_class_vars(string $class_name)
{
    return [];
}

/**
 * Gets the properties of the given object.
 *
 * @param  object $object An object instance
 * @return ~array An associative array of defined object accessible non-static properties for the specified object in scope (false: error)
 */
function get_object_vars(object $object)
{
    return [];
}

/**
 * Check whether a given string has been marked as escaped.
 * This function only exists in the dev version of PHP provided by ocProducts in use for XSS detection.
 *
 * @param  string $in The string to check for escaping
 * @return boolean Whether the string has been escaped by OCP's PHP-dev
 */
function ocp_is_escaped(string $in) : bool
{
    return false;
}

/**
 * Mark a string as having been escaped (for use with ocp_is_escaped).
 * This function only exists in the dev version of PHP provided by ocProducts in use for XSS detection.
 *
 * @param  string $in The string to mark as escaped
 */
function ocp_mark_as_escaped(string $in)
{
    return;
}

/**
 * Encrypt a message with a symmetric (shared) key.
 *
 * @param  string $message The plaintext message to encrypt
 * @param  string $nonce A random 24-byte string to use only for this encryption
 * @param  string $key The 256-bit encryption key to use
 * @return string The encrypted text
 */
function sodium_crypto_secretbox(string $message, string $nonce, string $key) : string
{
    return '';
}

/**
 * Encrypt a message such that only the recipient can decrypt it.
 *
 * @param  string $message The message to encrypt
 * @param  string $public_key The public key that corresponds to the only key that can decrypt the message
 * @return string A ciphertext string in the format of (one-time public key, encrypted message, authentication tag)
 */
function sodium_crypto_box_seal(string $message, string $public_key) : string
{
    return '';
}

/**
 * Create a keypair from a private and public key.
 *
 * @param  string $secret_key The private key
 * @param  string $public_key The public key
 * @return string The keypair
 */
function sodium_crypto_box_keypair_from_secretkey_and_publickey(string $secret_key, string $public_key) : string
{
    return '';
}

/**
 * Decrypt a message that was encrypted with sodium_crypto_box_seal().
 *
 * @param  string $ciphertext The encrypted message
 * @param  string $key_pair The keypair of the recipient
 * @return ~string The decrypted message (false: error)
 */
function sodium_crypto_box_seal_open(string $ciphertext, string $key_pair)
{
    return '';
}

/**
 * Decrypt an encrypted message with a symmetric (shared) key.
 * This should be used to decrypt text from sodium_crypto_secretbox().
 *
 * @param  string $ciphertext The encrypted text
 * @param  string $nonce The nonce which was used
 * @param  string $key The 256-bit encryption key used
 * @return ~string The decrypted message (false: error)
 */
function sodium_crypto_secretbox_open(string $ciphertext, string $nonce, string $key)
{
    return '';
}

/**
 * Generates a private key and a public key as one string.
 * To get the secret key out of this unified keypair string, see sodium_crypto_box_secretkey(). To get the public key out of this unified keypair string, see sodium_crypto_box_publickey().
 *
 * @return string The keypair
 */
function sodium_crypto_box_keypair() : string
{
    return '';
}

/**
 * Given a keypair, fetch only the public key.
 *
 * @param  string $key_pair The keypair
 * @return string The public key
 */
function sodium_crypto_box_publickey(string $key_pair) : string
{
    return '';
}

/**
 * Given a keypair, fetch only the private key.
 *
 * @param  string $key_pair The keypair
 * @return string The private key
 */
function sodium_crypto_box_secretkey(string $key_pair) : string
{
    return '';
}

/*

Various things are disabled for various reasons. You may use them, if you use php_function_allowed

Disabled due to Google App Engine...

gc_collect_cycles
gc_enable
gc_disable
phpversion
php_sapi_name
gethostname (Google AppEngine disallows)

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
srand
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
crypt
mktime
gmmktime

Disabled due to multi-OS compatibility...

getservbyname
getservbyport
getprotobyname
getprotobynumber
virtual
apache_*
getallheaders
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
getmyuid
getmyinode
getmygid
get_current_user
ftok
mime_content_type
posix_*
fileowner
filegroup
getmypid
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
dns_check_record
dns_get_mx
set_file_buffer
socket_set_timeout
socket_get_status
gzputs
set_socket_blocking
socket_setopt
socket_getopt
stream_register_wrapper
socket_set_blocking

Disabled due to being effectively aliases...

natcasesort
natsort

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

Disabled due to being locale-dependent, which is not thread-safe:
fgetcsv
str_getcsv
str_word_count
strcoll
strtoupper
strtolower
lcfirst
ucfirst
ucwords
localeconv

Disabled due to often being ill-configured or disabled on hosts...

tmpfile
tempnam

Disabled simply as we don't feel a need to use them (can enable if we find a use)...

idate
get_called_class
interface_exists
trait_exists
class_alias
restore_exception_handler
get_declared_traits
get_declared_interfaces
get_defined_constants
htmlspecialchars_decode
sha1_file
nl_langinfo
preg_last_error
vfprintf
asinh
acosh
atanh
expm1
log1p
settype
dir
ob_get_flush
ob_get_status
ob_list_handlers
array_intersect_uassoc
forward_static_call
forward_static_call_array
gc_enabled
date_create
date_create_immutable
date_create_from_format
date_create_immutable_from_format
date_parse
date_parse_from_format
date_get_last_errors
date_format
date_modify
date_add
date_sub
date_timezone_get
date_timezone_set
date_offset_get
date_diff
date_time_set
date_date_set
date_isodate_set
date_timestamp_set
date_timestamp_get
date_interval_create_from_date_string
date_interval_format
date_sunrise
date_sunset
date_sun_info
timezone_open
timezone_name_get
timezone_name_from_abbr
timezone_offset_get
timezone_transitions_get
timezone_location_get
timezone_identifiers_list
timezone_abbreviations_list
timezone_version_get
preg_filter
imagecreatefromwbmp
imagecreatefromxbm
imagecreatefromgd
imagecreatefromgd2
imagecreatefromgd2part
imagewbmp
imagegd
imagegd2
imagedashedline
jpeg2wbmp
png2wbmp
image2wbmp
imagearc
imagefilledarc
imagecopymergegray
imageline
imageellipse
imagefilledellipse
imagechar
imagefilledpolygon
imagepolygon
imagefilledrectangle
imagerectangle
imagefilltoborder
imagegammacorrect
imageinterlace
imagepalettecopy
imagesetbrush
imagesetstyle
imagesetthickness
imagesettile
imagecharup
imagecolorclosesthwb
image_type_to_mime_type
imagefilter
image_type_to_extension
imagerotate
imageflip
imageantialias
imagecrop
imagecropauto
imagescale
imageaffine
imageaffinematrixconcat
imageaffinematrixget
imagesetinterpolation
imagelayereffect
imagexbm
imagecolormatch
imageconvolution
imagesetclip
imagegetclip
imageopenpolygon
imageresolution
fileinode
soundex
quotemeta
filetype
is_executable
is_subclass_of
metaphone
count_chars
get_meta_tags
get_parent_class
get_included_files
get_resource_type
hebrev
array_diff_uassoc
array_udiff
array_udiff_assoc
array_udiff_uassoc
array_uintersect_assoc
array_uintersect_uassoc
array_uintersect
array_intersect_ukey
array_diff_ukey
array_product
class_parents
class_implements
class_uses
spl_object_hash
spl_classes
spl_autoload
spl_autoload_functions
spl_autoload_extensions
spl_autoload_call
spl_autoload_unregister
iterator_to_array
iterator_count
iterator_apply
header_register_callback
parse_ini_string
checkdnsrr
getmxrr
gethostbynamel
dns_get_record
boolval
realpath_cache_size
realpath_cache_get
array_replace
array_replace_recursive
cli_set_process_title
cli_get_process_title
quoted_printable_decode
quoted_printable_encode
gzrewind
gzeof
gzgetc
gzgets
gzgetss
gzread
gzpassthru
gzseek
gztell
stream_bucket_append
stream_bucket_make_writeable
stream_bucket_new
stream_bucket_prepend
stream_context_get_default
stream_context_get_options
stream_context_get_params
stream_context_set_default
stream_context_set_option
stream_context_set_params
stream_copy_to_stream
stream_encoding
stream_filter_append
stream_filter_prepend
stream_filter_register
stream_filter_remove
stream_get_filters
stream_get_line
stream_get_meta_data
stream_get_transports
stream_get_wrappers
stream_is_local
stream_notification_callback
stream_register_wrapper
stream_resolve_include_path
stream_select
stream_set_blocking
stream_set_chunk_size
stream_set_read_buffer
stream_set_timeout
stream_set_write_buffer
stream_socket_accept
stream_socket_client
stream_socket_enable_crypto
stream_socket_get_name
stream_socket_pair
stream_socket_recvfrom
stream_socket_sendto
stream_socket_server
stream_socket_shutdown
stream_supports_lock
stream_wrapper_register
stream_wrapper_restore
stream_wrapper_unregister
get_resources
gc_mem_caches
deflate_init
deflate_add
inflate_init
inflate_add
socket_addrinfo_lookup
socket_addrinfo_connect
socket_addrinfo_bind
socket_addrinfo_explain
stream_isatty
imagecreatefrombmp
imagebmp

GD stuff that's not on by default...

imagecreatefromwebp
imagewebp


// ---

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
*/

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    enhanced_spreadsheets
 */

/**
 * Find whether a file is a writable spreadsheet.
 *
 * @param  string $filename Filename
 * @return boolean Whether it is
 */
function is_spreadsheet_writable(string $filename) : bool
{
    if (!addon_installed('enhanced_spreadsheets') || !function_exists('zip_open') || !function_exists('xml_parser_create')) {
        return non_overridden__is_spreadsheet_writable($filename);
    }

    $ext = get_file_extension($filename);
    return in_array($ext, ['csv', 'txt', 'ods', 'xlsx']);
}

/**
 * Find the default spreadsheet file format.
 *
 * @return string Default format
 */
function spreadsheet_write_default() : string
{
    if (!addon_installed('enhanced_spreadsheets') || !function_exists('zip_open') || !function_exists('xml_parser_create')) {
        return non_overridden__spreadsheet_write_default();
    }

    $file_type = either_param_string('file_type', null);
    if (($file_type !== null) && (is_spreadsheet_writable('example.' . $file_type))) {
        return $file_type;
    }

    return 'ods';
}

/**
 * Open spreadsheet for writing.
 *
 * @param  ?PATH $path File to write into (null: create a temporary file and return by reference)
 * @param  ?string $filename Filename (null: derive from $path)
 * @param  integer $algorithm An ALGORITHM_* constant
 * @param  ?string $charset The character set to write with
 * @return object A subclass of CMS_Spreadsheet_Writer
 */
function spreadsheet_open_write(?string &$path, ?string $filename = null, int $algorithm = 3, ?string $charset = '') : object
{
    if (!addon_installed('enhanced_spreadsheets') || !function_exists('zip_open') || !function_exists('xml_parser_create')) {
        return non_overridden__spreadsheet_open_write($path, $filename, $algorithm, $charset);
    }

    if ($filename === null) {
        $filename = basename($path);
        if (!is_spreadsheet_writable($filename)) {
            $filename = 'data.csv';
        }
    }

    $ext = get_file_extension($filename);
    switch ($ext) {
        case 'csv':
        case 'txt':
            return new CMS_CSV_Writer($path, $filename, $algorithm, $charset);

        case 'xlsx':
        case 'ods':
            require_code('files_spreadsheets_write__spout');
            return new CMS_CSV_Writer_Spout($path, $filename, $algorithm, $charset);
    }

    warn_exit(do_lang_tempcode('UNKNOWN_FORMAT', escape_html($ext)));
}

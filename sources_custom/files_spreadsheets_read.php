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
 * Find supported spreadsheet file types for reading.
 *
 * @return string A comma-separated list of supported file types
 */
function spreadsheet_read_file_types() : string
{
    if (!addon_installed('enhanced_spreadsheets') || !function_exists('zip_open') || !function_exists('xml_parser_create')) {
        return non_overridden__spreadsheet_read_file_types();
    }

    return 'csv,txt,ods,xlsx';
}

/**
 * Open spreadsheet for reading.
 *
 * @param  PATH $path File path
 * @param  ?string $filename Filename (null: derive from $path)
 * @param  integer $algorithm An ALGORITHM_* constant
 * @param  boolean $trim Whether to trim each cell
 * @param  ?string $default_charset The default character set to assume if none is specified in the file (null: website character set) (blank: smart detection)
 * @return object A subclass of CMS_Spreadsheet_Reader
 */
function spreadsheet_open_read(string $path, ?string $filename = null, int $algorithm = 3, bool $trim = true, ?string $default_charset = '') : object
{
    if (!addon_installed('enhanced_spreadsheets') || !function_exists('zip_open') || !function_exists('xml_parser_create')) {
        return non_overridden__spreadsheet_open_read($path, $filename, $algorithm, $trim, $default_charset);
    }

    if ($filename === null) {
        $filename = basename($path);
    }

    $ext = get_file_extension($filename);
    switch ($ext) {
        case 'csv':
        case 'txt':
            return new CMS_CSV_Reader($path, $filename, $algorithm, $trim, $default_charset);

        case 'xlsx':
        case 'ods':
            require_code('files_spreadsheets_read__spout');
            return new CMS_Spout_Reader($path, $filename, $algorithm, $trim, $default_charset);
    }

    warn_exit(do_lang_tempcode('UNKNOWN_FORMAT', escape_html($ext)));
}

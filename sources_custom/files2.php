<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    excel_support
 */

/**
 * Output data to a CSV file.
 *
 * @param  array $data List of maps, each map representing a row
 * @param  ID_TEXT $filename Filename to output
 * @param  boolean $headers Whether to output CSV headers
 * @param  boolean $output_and_exit Whether to output/exit when we're done instead of return
 * @param  ?PATH $outfile_path File to spool into (null: none)
 * @param  ?mixed $callback Callback for dynamic row insertion (null: none). Only implemented for the excel_support addon. Is passed: row just done, next row (or null), returns rows to insert
 * @param  ?array $metadata List of maps, each map representing metadata of a row; supports 'url' (null: none)
 * @return string CSV data (we might not return though, depending on $exit; if $outfile_path is not null, this will be blank)
 */
function make_csv($data, $filename = 'data.csv', $headers = true, $output_and_exit = true, $outfile_path = null, $callback = null, $metadata = null)
{
    if (version_compare(PHP_VERSION, '5.3.0') < 0) {
        return non_overridden__make_csv($data, $filename, $headers, $output_and_exit, $outfile_path, $callback, $metadata);
    }

    require_code('files_spout');
    require_code('spout/src/Spout/Common/Exception/SpoutException');
    require_code('spout/src/Spout/Reader/Exception/ReaderException');
    require_code('spout/src/Spout/Reader/Exception/EndOfFileReachedException');
    require_code('spout/src/Spout/Reader/Exception/EndOfWorksheetsReachedException');
    require_code('spout/src/Spout/Reader/Exception/NoWorksheetsFoundException');
    require_code('spout/src/Spout/Reader/Exception/ReaderNotOpenedException');
    require_code('spout/src/Spout/Reader/Exception/SharedStringNotFoundException');
    require_code('spout/src/Spout/Writer/Exception/WriterException');
    require_code('spout/src/Spout/Writer/Exception/SheetNotFoundException');
    require_code('spout/src/Spout/Writer/Exception/WriterNotOpenedException');
    require_code('spout/src/Spout/Common/Escaper/EscaperInterface');
    require_code('spout/src/Spout/Common/Escaper/CSV');
    require_code('spout/src/Spout/Common/Escaper/XLSX');
    require_code('spout/src/Spout/Common/Exception/BadUsageException');
    require_code('spout/src/Spout/Common/Exception/InvalidArgumentException');
    require_code('spout/src/Spout/Common/Exception/IOException');
    require_code('spout/src/Spout/Common/Exception/UnsupportedTypeException');
    require_code('spout/src/Spout/Reader/ReaderInterface');
    require_code('spout/src/Spout/Writer/WriterInterface');
    require_code('spout/src/Spout/Common/Helper/FileSystemHelper');
    require_code('spout/src/Spout/Common/Helper/GlobalFunctionsHelper');
    require_code('spout/src/Spout/Common/Type');
    require_code('spout/src/Spout/Reader/AbstractReader');
    require_code('spout/src/Spout/Reader/CSV');
    require_code('spout/src/Spout/Reader/Helper/XLSX/CellHelper');
    require_code('spout/src/Spout/Reader/Helper/XLSX/SharedStringsHelper');
    require_code('spout/src/Spout/Reader/Helper/XLSX/WorksheetHelper');
    require_code('spout/src/Spout/Reader/Internal/XLSX/Worksheet');
    require_code('spout/src/Spout/Reader/ReaderFactory');
    require_code('spout/src/Spout/Reader/XLSX');
    require_code('spout/src/Spout/Writer/AbstractWriter');
    require_code('spout/src/Spout/Writer/CSV');
    require_code('spout/src/Spout/Writer/Helper/XLSX/CellHelper');
    require_code('spout/src/Spout/Writer/Helper/XLSX/FileSystemHelper');
    require_code('spout/src/Spout/Writer/Helper/XLSX/SharedStringsHelper');
    require_code('spout/src/Spout/Writer/Helper/XLSX/ZipHelper');
    require_code('spout/src/Spout/Writer/HTM');
    require_code('spout/src/Spout/Writer/Internal/XLSX/Workbook');
    require_code('spout/src/Spout/Writer/Internal/XLSX/Worksheet');
    require_code('spout/src/Spout/Writer/Sheet');
    require_code('spout/src/Spout/Writer/WriterFactory');
    require_code('spout/src/Spout/Writer/XLS');
    require_code('spout/src/Spout/Writer/XLSX');

    require_code('character_sets');

    $ext = get_file_extension($filename);

    if ($headers) {
        require_code('mime_types');
        if ($ext != 'html' && $ext != 'htm') {
            header('Content-type: ' . get_mime_type($ext, true));
        } else {
            header('Content-type: text/html; charset=utf-8');
        }
        if (($ext != 'html') && ($ext != 'htm')) {
            header('Content-Disposition: attachment; filename="' . escape_header($filename, true) . '"');
        } else {
            header('Content-Disposition: inline');
        }
    }

    return spreadsheet_export__spout($ext, $data, $filename, $headers, $output_and_exit, $outfile_path, $callback, $metadata);
}

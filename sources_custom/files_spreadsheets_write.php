<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    enhanced_spreadsheets
 */

/**
 * Find whether a file is a writable spreadsheet.
 *
 * @param  string $filename Filename
 * @return boolean Whether it is
 */
function is_spreadsheet_writable($filename)
{
    if (!addon_installed('enhanced_spreadsheets') || !function_exists('zip_open') || !function_exists('xml_parser_create')) {
        return non_overridden__is_spreadsheet_readable($filename);
    }

    $ext = get_file_extension($filename);
    return in_array($ext, array('csv', 'txt', 'ods', 'xlsx'));
}

/**
 * Find the default spreadsheet file format.
 *
 * @return string Default format
 */
function spreadsheet_write_default()
{
    if (!addon_installed('enhanced_spreadsheets') || !function_exists('zip_open') || !function_exists('xml_parser_create')) {
        return non_overridden__spreadsheet_write_default();
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
 */
function spreadsheet_open_write(&$path, $filename = null, $algorithm = 3, $charset = '')
{
    if (!addon_installed('enhanced_spreadsheets') || !function_exists('zip_open') || !function_exists('xml_parser_create')) {
        return non_overridden__spreadsheet_open_write($path, $filename, $algorithm, $charset);
    }

    if ($filename === null) {
        $filename = basename($path);
    }

    $ext = get_file_extension($filename);
    switch ($ext) {
        case 'csv':
        case 'txt':
            return new CMS_CSV_Writer($path, $algorithm, $charset);

        case 'xlsx':
        case 'ods':
            return new CMS_CSV_Writer_Spout($path, $algorithm, $charset);
    }

    warn_exit(do_lang_tempcode('UNKNOWN_FORMAT', escape_html($ext)));
}

/**
 * Spout spreadsheet reader.
 *
 * @package    core
 */
class CMS_CSV_Writer_Spout extends CMS_Spreadsheet_Writer
{
    protected $writer = null;

    /**
     * Open spreadsheet for writing.
     *
     * @param  ?PATH $path File to write into (null: create a temporary file and return by reference)
     * @param  integer $algorithm An ALGORITHM_* constant
     * @param  ?string $charset The character set to write with (if supported) (null: website character set)
     */
    public function __construct(&$path, $algorithm = 3, $charset = null)
    {
        require_code('files');

        if ($path === null) {
            $path = cms_tempnam();
        }

        $this->path = $path;
        $this->algorithm = $algorithm;

        require_code('spout/Autoloader/autoload');

        $this->reader = Box\Spout\Writer\Common\Creator\WriterEntityFactory::createWriterFromFile($path);

        if (method_exists($this->writer, 'setShouldUseInlineStrings')) {
            $this->writer->setShouldUseInlineStrings(false); // Inline strings are buggy in Excel, line-breaks don't initially show, until a new input within Excel shifts it to shared strings mode
        }

        $this->writer->openToFile($path);
    }

    /**
     * Write spreadsheet row.
     *
     * @param  array $row Row
     * @param  ?array $metadata Map representing metadata of a row; supports 'url'; will only be used by file formats that support it (null: none)
     */
    protected function _write_row($row, $metadata = null)
    {
        if ($this->handle === null) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        require_code('character_sets');

        $_row = array();
        foreach ($row as $column => $val) {
            $_row[] = convert_to_internal_encoding(@strval($val), get_charset(), 'utf-8');
        }

        $this->writer->addRow(WriterEntityFactory::createRowFromArray($_row));
    }

    /**
     * Standard destructor.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Close down the spreadsheet file handle, for when we're done.
     */
    public function close()
    {
        if ($this->writer !== null) {
            $this->writer->close();
            $this->writer = null;
        }
    }

    /**
     * Get the mime-type for the spreadsheet.
     *
     * @return string Mime-type
     */
    public function get_mime_type()
    {
        $ext = get_file_extension(basename($this->path));
        require_code('mime_types');
        return get_mime_type($ext, true);
    }
}

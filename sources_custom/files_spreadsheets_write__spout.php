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
 * OpenSpout spreadsheet writer.
 *
 * @package core
 */
class CMS_CSV_Writer_OpenSpout extends CMS_Spreadsheet_Writer
{
    protected $writer = null;

    /**
     * Open spreadsheet for writing.
     *
     * @param  ?PATH $path File to write into (null: create a temporary file and return by reference)
     * @param  string $filename Filename
     * @param  integer $algorithm An ALGORITHM_* constant
     * @param  ?string $charset The character set to write with (if supported) (null: website character set)
     */
    public function __construct(?string &$path, string $filename, int $algorithm = 3, ?string $charset = null)
    {
        require_code('files');
        require_code('openspout/vendor/autoload');

        if ($path === null) {
            $path = cms_tempnam();
        }

        $this->path = $path;
        $this->algorithm = $algorithm;

        $before = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');

        $ext = get_file_extension($filename);
        switch ($ext) {
            case 'ods':
                $this->writer = new \OpenSpout\Writer\ODS\Writer();
                break;

            case 'xlsx':
                $this->writer = new \OpenSpout\Writer\XLSX\Writer();
                break;

            default:
                fatal_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('bc44dac5ec94559c99316e85edea3e9a')));
        }

        $this->writer->openToFile($path);

        cms_ini_set('ocproducts.type_strictness', $before);
    }

    /**
     * Write spreadsheet row.
     *
     * @param  array $row Row
     * @param  ?array $metadata Map representing metadata of a row; supports 'url'; will only be used by file formats that support it (null: none)
     */
    protected function _write_row(array $row, ?array $metadata = null)
    {
        if ($this->writer === null) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('e10ed0fbbb6a53b7a947e48e46248689')));
        }

        require_code('character_sets');
        $_row = [];
        foreach ($row as $val) {
            if (is_string($val)) {
                $_val = convert_to_internal_encoding($val, get_charset(), 'utf-8');
            } else {
                $_val = $val;
            }
            $_row[] = $_val;
        }

        $before = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');

        // Convert data to proper cells
        $cells = [];
        foreach ($_row as $val) {
            $cells[] = \OpenSpout\Common\Entity\Cell::fromValue($val);
        }

        // Save row
        $actual_row = new \OpenSpout\Common\Entity\Row($cells);
        $this->writer->addRow($actual_row);

        cms_ini_set('ocproducts.type_strictness', $before);
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
            $before = ini_get('ocproducts.type_strictness');
            cms_ini_set('ocproducts.type_strictness', '0');
            $this->writer->close();
            cms_ini_set('ocproducts.type_strictness', $before);
            $this->writer = null;
        }
    }

    /**
     * Get the mime-type for the spreadsheet.
     *
     * @return string Mime-type
     */
    public function get_mime_type() : string
    {
        $ext = get_file_extension(basename($this->path));
        require_code('mime_types');
        return get_mime_type($ext, true);
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    enhanced_spreadsheets
 */

/**
 * Spout spreadsheet writer.
 *
 * @package core
 */
class CMS_CSV_Writer_Spout extends CMS_Spreadsheet_Writer
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
        require_code('spout/Autoloader/autoload');

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
                $this->writer = Box\Spout\Writer\Common\Creator\WriterEntityFactory::createODSWriter();
                break;

            case 'xlsx':
                $this->writer = Box\Spout\Writer\Common\Creator\WriterEntityFactory::createXLSXWriter();
                $this->writer->setShouldUseInlineStrings(false); // Inline strings are buggy in Excel, line-breaks don't initially show, until a new input within Excel shifts it to shared strings mode
                break;

            default:
                fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
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
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
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

        $__row = Box\Spout\Writer\Common\Creator\WriterEntityFactory::createRowFromArray($_row);
        $this->writer->addRow($__row);

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

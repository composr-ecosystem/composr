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
 * Find supported spreadsheet file types for reading.
 *
 * @return string A comma-separated list of supported file types
 */
function spreadsheet_read_file_types()
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
function spreadsheet_open_read($path, $filename = null, $algorithm = 3, $trim = true, $default_charset = '')
{
    if (!addon_installed('enhanced_spreadsheets') || !function_exists('zip_open') || !function_exists('xml_parser_create')) {
        return non_overridden__spreadsheet_open_read($path, $filename, $algorithm, $trim, $default_charset);
    }

    $ext = get_file_extension($filename);
    switch ($ext) {
        case 'csv':
        case 'txt':
            return new CMS_CSV_Reader($path, $algorithm, $trim, $default_charset);

        case 'xlsx':
        case 'ods':
            return new CMS_Spout_Reader($path, $algorithm, $trim, $default_charset);
    }

    warn_exit(do_lang_tempcode('UNKNOWN_FORMAT', escape_html($ext)));
}

/**
 * Spout spreadsheet reader.
 *
 * @package    core
 */
class CMS_Spout_Reader extends CMS_Spreadsheet_Reader
{
    protected $reader = null;
    protected $row_iterator = null;

    /**
     * Constructor. Opens spreadsheet for reading.
     *
     * @param  PATH $path File path
     * @param  integer $algorithm An ALGORITHM_* constant
     * @param  boolean $trim Whether to trim each cell
     * @param  ?string $default_charset The default character set to assume if none is specified in the file (null: website character set) (blank: smart detection)
     */
    public function __construct($path, $algorithm = 3, $trim = true, $default_charset = '')
    {
        require_code('spout/Autoloader/autoload');

        $this->reader = Box\Spout\Reader\Common\Creator\ReaderEntityFactory\ReaderEntityFactory::createReaderFromFile($path);

        $this->reader->open($path);

        if ($algorithm == self::ALGORITHM_RAW) {
            $this->fields = null;
        } else {
            $row = $this->read_row();
            if ($row === false) {
                $row = array();
            }
            $this->fields = $row;
        }

        $sheet_iterator = $this->reader->getSheetIterator(); // We will only look at the first sheet
        $this->row_iterator = $sheet_iterator->getRowIterator();
    }

    /**
     * Rewind to return first record again.
     */
    public function rewind()
    {
        $this->row_iterator->rewind();
    }

    /**
     * Read spreadsheet row.
     *
     * @return ~array Row (false: error)
     */
    protected function _read_row()
    {
        if ($this->handle === null) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (!$this->row_iterator->valid()) {
            return false;
        }

        $row = $this->row_iterator->current();
        $row->next();
        if (RowManager::isEmpty($row)) {
            return $this->read_row();
        }
        return $row->getCells();
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
        if ($this->reader !== null) {
            $this->reader->close();
            $this->reader = null;
        }
    }
}

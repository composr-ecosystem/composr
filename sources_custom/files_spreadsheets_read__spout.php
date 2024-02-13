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
 * Spout spreadsheet reader.
 *
 * @package core
 */
class CMS_Spout_Reader extends CMS_Spreadsheet_Reader
{
    protected $reader = null;
    protected $row_iterator = null;

    /**
     * Constructor. Opens spreadsheet for reading.
     *
     * @param  PATH $path File path
     * @param  string $filename Filename
     * @param  integer $algorithm An ALGORITHM_* constant
     * @param  boolean $trim Whether to trim each cell
     * @param  ?string $default_charset The default character set to assume if none is specified in the file (null: website character set) (blank: smart detection)
     */
    public function __construct(string $path, string $filename, int $algorithm = 3, bool $trim = true, ?string $default_charset = '')
    {
        require_code('spout/Autoloader/autoload');

        $before = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');

        $ext = get_file_extension($filename);
        switch ($ext) {
            case 'ods':
                $this->reader = Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createODSReader();
                break;

            case 'xlsx':
                $this->reader = Box\Spout\Reader\Common\Creator\ReaderEntityFactory::createXLSXReader();
                break;

            default:
                fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        if (function_exists('libxml_disable_entity_loader')) {
            @libxml_disable_entity_loader(false);
        }

        $this->reader->open($path);

        $sheet_iterator = $this->reader->getSheetIterator(); // We will only look at the first sheet
        $sheet_iterator->rewind();
        $this->row_iterator = $sheet_iterator->current()->getRowIterator();
        $this->row_iterator->rewind();

        cms_ini_set('ocproducts.type_strictness', $before);

        parent::__construct($path, $filename, $algorithm, $trim, $default_charset);
    }

    /**
     * Rewind to return first record again.
     */
    public function rewind()
    {
        $before = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $this->row_iterator->rewind();
        cms_ini_set('ocproducts.type_strictness', $before);
    }

    /**
     * Read spreadsheet row.
     *
     * @return ~array Row (false: error)
     */
    protected function _read_row()
    {
        if ($this->reader === null) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }

        $before = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');

        if (!$this->row_iterator->valid()) {
            cms_ini_set('ocproducts.type_strictness', $before);
            return false;
        }

        $row = $this->row_iterator->current();
        $this->row_iterator->next();
        $cells = @array_map('strval', $row->getCells());

        cms_ini_set('ocproducts.type_strictness', $before);

        return $cells;
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
            $before = ini_get('ocproducts.type_strictness');
            cms_ini_set('ocproducts.type_strictness', '0');
            $this->reader->close();
            cms_ini_set('ocproducts.type_strictness', $before);
            $this->reader = null;
        }
    }
}

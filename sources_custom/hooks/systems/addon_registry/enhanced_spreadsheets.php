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
 * Hook class.
 */
class Hook_addon_registry_enhanced_spreadsheets
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Information Display';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'ocProducts';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'Contains code from the Spout project',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'BSD license';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Allows the software to read/write OpenOffice ([tt].ods[/tt]) and Excel ([tt].xlsx[/tt]) spreadsheet files, in addition to the built-in [tt].csv[/tt] support.

Note the old-style Excel format ([tt].xls[/tt]) is intentionally not supported because it can not handle international characters properly, and due to its age.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [
                'PHP 7.1',
                'PHP zip extension',
                'PHP xml extension',
            ],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/enhanced_spreadsheets.php',
            'sources_custom/files_spreadsheets_write.php',
            'sources_custom/files_spreadsheets_read.php',
            'sources_custom/files_spreadsheets_write__spout.php',
            'sources_custom/files_spreadsheets_read__spout.php',

            'sources_custom/spout/index.html',
            'sources_custom/spout/Common/index.html',
            'sources_custom/spout/Common/Exception/index.html',
            'sources_custom/spout/Common/Exception/EncodingConversionException.php',
            'sources_custom/spout/Common/Exception/UnsupportedTypeException.php',
            'sources_custom/spout/Common/Exception/InvalidArgumentException.php',
            'sources_custom/spout/Common/Exception/SpoutException.php',
            'sources_custom/spout/Common/Exception/IOException.php',
            'sources_custom/spout/Common/Exception/InvalidColorException.php',
            'sources_custom/spout/Common/Exception/.htaccess',
            'sources_custom/spout/Common/Manager/index.html',
            'sources_custom/spout/Common/Manager/OptionsManagerAbstract.php',
            'sources_custom/spout/Common/Manager/OptionsManagerInterface.php',
            'sources_custom/spout/Common/Manager/.htaccess',
            'sources_custom/spout/Common/Entity/index.html',
            'sources_custom/spout/Common/Entity/Row.php',
            'sources_custom/spout/Common/Entity/Cell.php',
            'sources_custom/spout/Common/Entity/Style/BorderPart.php',
            'sources_custom/spout/Common/Entity/Style/Style.php',
            'sources_custom/spout/Common/Entity/Style/index.html',
            'sources_custom/spout/Common/Entity/Style/CellAlignment.php',
            'sources_custom/spout/Common/Entity/Style/Color.php',
            'sources_custom/spout/Common/Entity/Style/Border.php',
            'sources_custom/spout/Common/Entity/Style/.htaccess',
            'sources_custom/spout/Common/Entity/.htaccess',
            'sources_custom/spout/Common/Helper/Escaper/index.html',
            'sources_custom/spout/Common/Helper/Escaper/CSV.php',
            'sources_custom/spout/Common/Helper/Escaper/ODS.php',
            'sources_custom/spout/Common/Helper/Escaper/XLSX.php',
            'sources_custom/spout/Common/Helper/Escaper/EscaperInterface.php',
            'sources_custom/spout/Common/Helper/Escaper/.htaccess',
            'sources_custom/spout/Common/Helper/FileSystemHelperInterface.php',
            'sources_custom/spout/Common/Helper/index.html',
            'sources_custom/spout/Common/Helper/CellTypeHelper.php',
            'sources_custom/spout/Common/Helper/StringHelper.php',
            'sources_custom/spout/Common/Helper/EncodingHelper.php',
            'sources_custom/spout/Common/Helper/GlobalFunctionsHelper.php',
            'sources_custom/spout/Common/Helper/.htaccess',
            'sources_custom/spout/Common/Helper/FileSystemHelper.php',
            'sources_custom/spout/Common/.htaccess',
            'sources_custom/spout/Common/Type.php',
            'sources_custom/spout/Common/Creator/index.html',
            'sources_custom/spout/Common/Creator/HelperFactory.php',
            'sources_custom/spout/Common/Creator/.htaccess',
            'sources_custom/spout/.editorconfig',
            'sources_custom/spout/.travis.yml',
            'sources_custom/spout/Writer/WriterInterface.php',
            'sources_custom/spout/Writer/index.html',
            'sources_custom/spout/Writer/Common/Manager/index.html',
            'sources_custom/spout/Writer/Common/Manager/WorkbookManagerAbstract.php',
            'sources_custom/spout/Writer/Common/Manager/SheetManager.php',
            'sources_custom/spout/Writer/Common/Manager/WorksheetManagerInterface.php',
            'sources_custom/spout/Writer/Common/Manager/Style/StyleRegistry.php',
            'sources_custom/spout/Writer/Common/Manager/Style/index.html',
            'sources_custom/spout/Writer/Common/Manager/Style/StyleMerger.php',
            'sources_custom/spout/Writer/Common/Manager/Style/StyleManagerInterface.php',
            'sources_custom/spout/Writer/Common/Manager/Style/.htaccess',
            'sources_custom/spout/Writer/Common/Manager/Style/StyleManager.php',
            'sources_custom/spout/Writer/Common/Manager/RowManager.php',
            'sources_custom/spout/Writer/Common/Manager/.htaccess',
            'sources_custom/spout/Writer/Common/Manager/CellManager.php',
            'sources_custom/spout/Writer/Common/Manager/WorkbookManagerInterface.php',
            'sources_custom/spout/Writer/Common/Entity/index.html',
            'sources_custom/spout/Writer/Common/Entity/Options.php',
            'sources_custom/spout/Writer/Common/Entity/Workbook.php',
            'sources_custom/spout/Writer/Common/Entity/Worksheet.php',
            'sources_custom/spout/Writer/Common/Entity/.htaccess',
            'sources_custom/spout/Writer/Common/Entity/Sheet.php',
            'sources_custom/spout/Writer/Common/Helper/ZipHelper.php',
            'sources_custom/spout/Writer/Common/Helper/index.html',
            'sources_custom/spout/Writer/Common/Helper/FileSystemWithRootFolderHelperInterface.php',
            'sources_custom/spout/Writer/Common/Helper/CellHelper.php',
            'sources_custom/spout/Writer/Common/Helper/.htaccess',
            'sources_custom/spout/Writer/Common/Creator/WriterFactory.php',
            'sources_custom/spout/Writer/Common/Creator/ManagerFactoryInterface.php',
            'sources_custom/spout/Writer/Common/Creator/index.html',
            'sources_custom/spout/Writer/Common/Creator/WriterEntityFactory.php',
            'sources_custom/spout/Writer/Common/Creator/Style/BorderBuilder.php',
            'sources_custom/spout/Writer/Common/Creator/Style/index.html',
            'sources_custom/spout/Writer/Common/Creator/Style/StyleBuilder.php',
            'sources_custom/spout/Writer/Common/Creator/Style/.htaccess',
            'sources_custom/spout/Writer/Common/Creator/InternalEntityFactory.php',
            'sources_custom/spout/Writer/Common/Creator/.htaccess',
            'sources_custom/spout/Writer/WriterMultiSheetsAbstract.php',
            'sources_custom/spout/Writer/Exception/WriterException.php',
            'sources_custom/spout/Writer/Exception/index.html',
            'sources_custom/spout/Writer/Exception/WriterAlreadyOpenedException.php',
            'sources_custom/spout/Writer/Exception/SheetNotFoundException.php',
            'sources_custom/spout/Writer/Exception/Border/InvalidWidthException.php',
            'sources_custom/spout/Writer/Exception/Border/InvalidStyleException.php',
            'sources_custom/spout/Writer/Exception/Border/index.html',
            'sources_custom/spout/Writer/Exception/Border/InvalidNameException.php',
            'sources_custom/spout/Writer/Exception/Border/.htaccess',
            'sources_custom/spout/Writer/Exception/.htaccess',
            'sources_custom/spout/Writer/Exception/WriterNotOpenedException.php',
            'sources_custom/spout/Writer/Exception/InvalidSheetNameException.php',
            'sources_custom/spout/Writer/ODS/index.html',
            'sources_custom/spout/Writer/ODS/Manager/index.html',
            'sources_custom/spout/Writer/ODS/Manager/WorksheetManager.php',
            'sources_custom/spout/Writer/ODS/Manager/Style/StyleRegistry.php',
            'sources_custom/spout/Writer/ODS/Manager/Style/index.html',
            'sources_custom/spout/Writer/ODS/Manager/Style/.htaccess',
            'sources_custom/spout/Writer/ODS/Manager/Style/StyleManager.php',
            'sources_custom/spout/Writer/ODS/Manager/OptionsManager.php',
            'sources_custom/spout/Writer/ODS/Manager/.htaccess',
            'sources_custom/spout/Writer/ODS/Manager/WorkbookManager.php',
            'sources_custom/spout/Writer/ODS/Writer.php',
            'sources_custom/spout/Writer/ODS/Helper/index.html',
            'sources_custom/spout/Writer/ODS/Helper/.htaccess',
            'sources_custom/spout/Writer/ODS/Helper/FileSystemHelper.php',
            'sources_custom/spout/Writer/ODS/Helper/BorderHelper.php',
            'sources_custom/spout/Writer/ODS/.htaccess',
            'sources_custom/spout/Writer/ODS/Creator/index.html',
            'sources_custom/spout/Writer/ODS/Creator/ManagerFactory.php',
            'sources_custom/spout/Writer/ODS/Creator/HelperFactory.php',
            'sources_custom/spout/Writer/ODS/Creator/.htaccess',
            'sources_custom/spout/Writer/.htaccess',
            'sources_custom/spout/Writer/CSV/index.html',
            'sources_custom/spout/Writer/CSV/Manager/index.html',
            'sources_custom/spout/Writer/CSV/Manager/OptionsManager.php',
            'sources_custom/spout/Writer/CSV/Manager/.htaccess',
            'sources_custom/spout/Writer/CSV/Writer.php',
            'sources_custom/spout/Writer/CSV/.htaccess',
            'sources_custom/spout/Writer/WriterAbstract.php',
            'sources_custom/spout/Writer/XLSX/index.html',
            'sources_custom/spout/Writer/XLSX/Manager/index.html',
            'sources_custom/spout/Writer/XLSX/Manager/WorksheetManager.php',
            'sources_custom/spout/Writer/XLSX/Manager/Style/StyleRegistry.php',
            'sources_custom/spout/Writer/XLSX/Manager/Style/index.html',
            'sources_custom/spout/Writer/XLSX/Manager/Style/.htaccess',
            'sources_custom/spout/Writer/XLSX/Manager/Style/StyleManager.php',
            'sources_custom/spout/Writer/XLSX/Manager/OptionsManager.php',
            'sources_custom/spout/Writer/XLSX/Manager/.htaccess',
            'sources_custom/spout/Writer/XLSX/Manager/WorkbookManager.php',
            'sources_custom/spout/Writer/XLSX/Manager/SharedStringsManager.php',
            'sources_custom/spout/Writer/XLSX/Writer.php',
            'sources_custom/spout/Writer/XLSX/Helper/index.html',
            'sources_custom/spout/Writer/XLSX/Helper/.htaccess',
            'sources_custom/spout/Writer/XLSX/Helper/FileSystemHelper.php',
            'sources_custom/spout/Writer/XLSX/Helper/BorderHelper.php',
            'sources_custom/spout/Writer/XLSX/.htaccess',
            'sources_custom/spout/Writer/XLSX/Creator/index.html',
            'sources_custom/spout/Writer/XLSX/Creator/ManagerFactory.php',
            'sources_custom/spout/Writer/XLSX/Creator/HelperFactory.php',
            'sources_custom/spout/Writer/XLSX/Creator/.htaccess',
            'sources_custom/spout/Reader/index.html',
            'sources_custom/spout/Reader/Common/index.html',
            'sources_custom/spout/Reader/Common/Manager/index.html',
            'sources_custom/spout/Reader/Common/Manager/RowManager.php',
            'sources_custom/spout/Reader/Common/Manager/.htaccess',
            'sources_custom/spout/Reader/Common/Entity/index.html',
            'sources_custom/spout/Reader/Common/Entity/Options.php',
            'sources_custom/spout/Reader/Common/Entity/.htaccess',
            'sources_custom/spout/Reader/Common/XMLProcessor.php',
            'sources_custom/spout/Reader/Common/.htaccess',
            'sources_custom/spout/Reader/Common/Creator/ReaderEntityFactory.php',
            'sources_custom/spout/Reader/Common/Creator/index.html',
            'sources_custom/spout/Reader/Common/Creator/ReaderFactory.php',
            'sources_custom/spout/Reader/Common/Creator/InternalEntityFactoryInterface.php',
            'sources_custom/spout/Reader/Common/Creator/.htaccess',
            'sources_custom/spout/Reader/Exception/SharedStringNotFoundException.php',
            'sources_custom/spout/Reader/Exception/index.html',
            'sources_custom/spout/Reader/Exception/InvalidValueException.php',
            'sources_custom/spout/Reader/Exception/ReaderNotOpenedException.php',
            'sources_custom/spout/Reader/Exception/ReaderException.php',
            'sources_custom/spout/Reader/Exception/IteratorNotRewindableException.php',
            'sources_custom/spout/Reader/Exception/.htaccess',
            'sources_custom/spout/Reader/Exception/XMLProcessingException.php',
            'sources_custom/spout/Reader/Exception/NoSheetsFoundException.php',
            'sources_custom/spout/Reader/ReaderAbstract.php',
            'sources_custom/spout/Reader/ReaderInterface.php',
            'sources_custom/spout/Reader/Wrapper/index.html',
            'sources_custom/spout/Reader/Wrapper/XMLInternalErrorsHelper.php',
            'sources_custom/spout/Reader/Wrapper/XMLReader.php',
            'sources_custom/spout/Reader/Wrapper/.htaccess',
            'sources_custom/spout/Reader/ODS/index.html',
            'sources_custom/spout/Reader/ODS/Manager/index.html',
            'sources_custom/spout/Reader/ODS/Manager/OptionsManager.php',
            'sources_custom/spout/Reader/ODS/Manager/.htaccess',
            'sources_custom/spout/Reader/ODS/Helper/index.html',
            'sources_custom/spout/Reader/ODS/Helper/SettingsHelper.php',
            'sources_custom/spout/Reader/ODS/Helper/CellValueFormatter.php',
            'sources_custom/spout/Reader/ODS/Helper/.htaccess',
            'sources_custom/spout/Reader/ODS/RowIterator.php',
            'sources_custom/spout/Reader/ODS/.htaccess',
            'sources_custom/spout/Reader/ODS/Sheet.php',
            'sources_custom/spout/Reader/ODS/Creator/index.html',
            'sources_custom/spout/Reader/ODS/Creator/ManagerFactory.php',
            'sources_custom/spout/Reader/ODS/Creator/HelperFactory.php',
            'sources_custom/spout/Reader/ODS/Creator/InternalEntityFactory.php',
            'sources_custom/spout/Reader/ODS/Creator/.htaccess',
            'sources_custom/spout/Reader/ODS/Reader.php',
            'sources_custom/spout/Reader/ODS/SheetIterator.php',
            'sources_custom/spout/Reader/.htaccess',
            'sources_custom/spout/Reader/CSV/index.html',
            'sources_custom/spout/Reader/CSV/Manager/index.html',
            'sources_custom/spout/Reader/CSV/Manager/OptionsManager.php',
            'sources_custom/spout/Reader/CSV/Manager/.htaccess',
            'sources_custom/spout/Reader/CSV/RowIterator.php',
            'sources_custom/spout/Reader/CSV/.htaccess',
            'sources_custom/spout/Reader/CSV/Sheet.php',
            'sources_custom/spout/Reader/CSV/Creator/index.html',
            'sources_custom/spout/Reader/CSV/Creator/InternalEntityFactory.php',
            'sources_custom/spout/Reader/CSV/Creator/.htaccess',
            'sources_custom/spout/Reader/CSV/Reader.php',
            'sources_custom/spout/Reader/CSV/SheetIterator.php',
            'sources_custom/spout/Reader/SheetInterface.php',
            'sources_custom/spout/Reader/IteratorInterface.php',
            'sources_custom/spout/Reader/XLSX/index.html',
            'sources_custom/spout/Reader/XLSX/Manager/index.html',
            'sources_custom/spout/Reader/XLSX/Manager/WorkbookRelationshipsManager.php',
            'sources_custom/spout/Reader/XLSX/Manager/SheetManager.php',
            'sources_custom/spout/Reader/XLSX/Manager/SharedStringsCaching/index.html',
            'sources_custom/spout/Reader/XLSX/Manager/SharedStringsCaching/CachingStrategyFactory.php',
            'sources_custom/spout/Reader/XLSX/Manager/SharedStringsCaching/CachingStrategyInterface.php',
            'sources_custom/spout/Reader/XLSX/Manager/SharedStringsCaching/FileBasedStrategy.php',
            'sources_custom/spout/Reader/XLSX/Manager/SharedStringsCaching/.htaccess',
            'sources_custom/spout/Reader/XLSX/Manager/SharedStringsCaching/InMemoryStrategy.php',
            'sources_custom/spout/Reader/XLSX/Manager/OptionsManager.php',
            'sources_custom/spout/Reader/XLSX/Manager/.htaccess',
            'sources_custom/spout/Reader/XLSX/Manager/StyleManager.php',
            'sources_custom/spout/Reader/XLSX/Manager/SharedStringsManager.php',
            'sources_custom/spout/Reader/XLSX/Helper/index.html',
            'sources_custom/spout/Reader/XLSX/Helper/DateFormatHelper.php',
            'sources_custom/spout/Reader/XLSX/Helper/CellHelper.php',
            'sources_custom/spout/Reader/XLSX/Helper/CellValueFormatter.php',
            'sources_custom/spout/Reader/XLSX/Helper/.htaccess',
            'sources_custom/spout/Reader/XLSX/RowIterator.php',
            'sources_custom/spout/Reader/XLSX/.htaccess',
            'sources_custom/spout/Reader/XLSX/Sheet.php',
            'sources_custom/spout/Reader/XLSX/Creator/index.html',
            'sources_custom/spout/Reader/XLSX/Creator/ManagerFactory.php',
            'sources_custom/spout/Reader/XLSX/Creator/HelperFactory.php',
            'sources_custom/spout/Reader/XLSX/Creator/InternalEntityFactory.php',
            'sources_custom/spout/Reader/XLSX/Creator/.htaccess',
            'sources_custom/spout/Reader/XLSX/Reader.php',
            'sources_custom/spout/Reader/XLSX/SheetIterator.php',
            'sources_custom/spout/.gitignore',
            'sources_custom/spout/.htaccess',
            'sources_custom/spout/Autoloader/index.html',
            'sources_custom/spout/Autoloader/Psr4Autoloader.php',
            'sources_custom/spout/Autoloader/.htaccess',
            'sources_custom/spout/Autoloader/autoload.php',
        ];
    }
}

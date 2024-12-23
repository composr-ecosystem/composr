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
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.1'; // addon_version_auto_update ef531d5ce7deefd854b1549b612c8812
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return 11.9;
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
        return 'Core Development Team';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'Contains code from the OpenSpout project',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Apache 2.0; MIT';
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
                'PHP 8.2',
                'PHP zip extension',
                'PHP fileinfo extension',
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
            'sources_custom/files_spreadsheets_read.php',
            'sources_custom/files_spreadsheets_read__spout.php',
            'sources_custom/files_spreadsheets_write.php',
            'sources_custom/files_spreadsheets_write__spout.php',
            'sources_custom/hooks/systems/addon_registry/enhanced_spreadsheets.php',
            'sources_custom/openspout/.htaccess',
            'sources_custom/openspout/LICENSE',
            'sources_custom/openspout/LICENSE-for-cc42c1d',
            'sources_custom/openspout/README.md',
            'sources_custom/openspout/UPGRADE.md',
            'sources_custom/openspout/composer.json',
            'sources_custom/openspout/composer.lock',
            'sources_custom/openspout/index.html',
            'sources_custom/openspout/renovate.json',
            'sources_custom/openspout/src/Common/.htaccess',
            'sources_custom/openspout/src/Common/Entity/.htaccess',
            'sources_custom/openspout/src/Common/Entity/Cell.php',
            'sources_custom/openspout/src/Common/Entity/Cell/.htaccess',
            'sources_custom/openspout/src/Common/Entity/Cell/BooleanCell.php',
            'sources_custom/openspout/src/Common/Entity/Cell/DateIntervalCell.php',
            'sources_custom/openspout/src/Common/Entity/Cell/DateTimeCell.php',
            'sources_custom/openspout/src/Common/Entity/Cell/EmptyCell.php',
            'sources_custom/openspout/src/Common/Entity/Cell/ErrorCell.php',
            'sources_custom/openspout/src/Common/Entity/Cell/FormulaCell.php',
            'sources_custom/openspout/src/Common/Entity/Cell/NumericCell.php',
            'sources_custom/openspout/src/Common/Entity/Cell/StringCell.php',
            'sources_custom/openspout/src/Common/Entity/Cell/index.html',
            'sources_custom/openspout/src/Common/Entity/Comment/.htaccess',
            'sources_custom/openspout/src/Common/Entity/Comment/Comment.php',
            'sources_custom/openspout/src/Common/Entity/Comment/TextRun.php',
            'sources_custom/openspout/src/Common/Entity/Comment/index.html',
            'sources_custom/openspout/src/Common/Entity/Row.php',
            'sources_custom/openspout/src/Common/Entity/Style/.htaccess',
            'sources_custom/openspout/src/Common/Entity/Style/Border.php',
            'sources_custom/openspout/src/Common/Entity/Style/BorderPart.php',
            'sources_custom/openspout/src/Common/Entity/Style/CellAlignment.php',
            'sources_custom/openspout/src/Common/Entity/Style/CellVerticalAlignment.php',
            'sources_custom/openspout/src/Common/Entity/Style/Color.php',
            'sources_custom/openspout/src/Common/Entity/Style/Style.php',
            'sources_custom/openspout/src/Common/Entity/Style/index.html',
            'sources_custom/openspout/src/Common/Entity/index.html',
            'sources_custom/openspout/src/Common/Exception/.htaccess',
            'sources_custom/openspout/src/Common/Exception/EncodingConversionException.php',
            'sources_custom/openspout/src/Common/Exception/IOException.php',
            'sources_custom/openspout/src/Common/Exception/InvalidArgumentException.php',
            'sources_custom/openspout/src/Common/Exception/InvalidColorException.php',
            'sources_custom/openspout/src/Common/Exception/OpenSpoutException.php',
            'sources_custom/openspout/src/Common/Exception/UnsupportedTypeException.php',
            'sources_custom/openspout/src/Common/Exception/index.html',
            'sources_custom/openspout/src/Common/Helper/.htaccess',
            'sources_custom/openspout/src/Common/Helper/EncodingHelper.php',
            'sources_custom/openspout/src/Common/Helper/Escaper/.htaccess',
            'sources_custom/openspout/src/Common/Helper/Escaper/EscaperInterface.php',
            'sources_custom/openspout/src/Common/Helper/Escaper/ODS.php',
            'sources_custom/openspout/src/Common/Helper/Escaper/XLSX.php',
            'sources_custom/openspout/src/Common/Helper/Escaper/index.html',
            'sources_custom/openspout/src/Common/Helper/FileSystemHelper.php',
            'sources_custom/openspout/src/Common/Helper/FileSystemHelperInterface.php',
            'sources_custom/openspout/src/Common/Helper/StringHelper.php',
            'sources_custom/openspout/src/Common/Helper/index.html',
            'sources_custom/openspout/src/Common/TempFolderOptionTrait.php',
            'sources_custom/openspout/src/Common/index.html',
            'sources_custom/openspout/src/Reader/.htaccess',
            'sources_custom/openspout/src/Reader/AbstractReader.php',
            'sources_custom/openspout/src/Reader/CSV/.htaccess',
            'sources_custom/openspout/src/Reader/CSV/Options.php',
            'sources_custom/openspout/src/Reader/CSV/Reader.php',
            'sources_custom/openspout/src/Reader/CSV/RowIterator.php',
            'sources_custom/openspout/src/Reader/CSV/Sheet.php',
            'sources_custom/openspout/src/Reader/CSV/SheetIterator.php',
            'sources_custom/openspout/src/Reader/CSV/index.html',
            'sources_custom/openspout/src/Reader/Common/.htaccess',
            'sources_custom/openspout/src/Reader/Common/ColumnWidth.php',
            'sources_custom/openspout/src/Reader/Common/Creator/.htaccess',
            'sources_custom/openspout/src/Reader/Common/Creator/ReaderFactory.php',
            'sources_custom/openspout/src/Reader/Common/Creator/index.html',
            'sources_custom/openspout/src/Reader/Common/Manager/.htaccess',
            'sources_custom/openspout/src/Reader/Common/Manager/RowManager.php',
            'sources_custom/openspout/src/Reader/Common/Manager/index.html',
            'sources_custom/openspout/src/Reader/Common/XMLProcessor.php',
            'sources_custom/openspout/src/Reader/Common/index.html',
            'sources_custom/openspout/src/Reader/Exception/.htaccess',
            'sources_custom/openspout/src/Reader/Exception/InvalidValueException.php',
            'sources_custom/openspout/src/Reader/Exception/IteratorNotRewindableException.php',
            'sources_custom/openspout/src/Reader/Exception/NoSheetsFoundException.php',
            'sources_custom/openspout/src/Reader/Exception/ReaderException.php',
            'sources_custom/openspout/src/Reader/Exception/ReaderNotOpenedException.php',
            'sources_custom/openspout/src/Reader/Exception/SharedStringNotFoundException.php',
            'sources_custom/openspout/src/Reader/Exception/XMLProcessingException.php',
            'sources_custom/openspout/src/Reader/Exception/index.html',
            'sources_custom/openspout/src/Reader/ODS/.htaccess',
            'sources_custom/openspout/src/Reader/ODS/Helper/.htaccess',
            'sources_custom/openspout/src/Reader/ODS/Helper/CellValueFormatter.php',
            'sources_custom/openspout/src/Reader/ODS/Helper/SettingsHelper.php',
            'sources_custom/openspout/src/Reader/ODS/Helper/index.html',
            'sources_custom/openspout/src/Reader/ODS/Options.php',
            'sources_custom/openspout/src/Reader/ODS/Reader.php',
            'sources_custom/openspout/src/Reader/ODS/RowIterator.php',
            'sources_custom/openspout/src/Reader/ODS/Sheet.php',
            'sources_custom/openspout/src/Reader/ODS/SheetIterator.php',
            'sources_custom/openspout/src/Reader/ODS/index.html',
            'sources_custom/openspout/src/Reader/ReaderInterface.php',
            'sources_custom/openspout/src/Reader/RowIteratorInterface.php',
            'sources_custom/openspout/src/Reader/SheetInterface.php',
            'sources_custom/openspout/src/Reader/SheetIteratorInterface.php',
            'sources_custom/openspout/src/Reader/SheetWithMergeCellsInterface.php',
            'sources_custom/openspout/src/Reader/SheetWithVisibilityInterface.php',
            'sources_custom/openspout/src/Reader/Wrapper/.htaccess',
            'sources_custom/openspout/src/Reader/Wrapper/XMLInternalErrorsHelper.php',
            'sources_custom/openspout/src/Reader/Wrapper/XMLReader.php',
            'sources_custom/openspout/src/Reader/Wrapper/index.html',
            'sources_custom/openspout/src/Reader/XLSX/.htaccess',
            'sources_custom/openspout/src/Reader/XLSX/Helper/.htaccess',
            'sources_custom/openspout/src/Reader/XLSX/Helper/CellHelper.php',
            'sources_custom/openspout/src/Reader/XLSX/Helper/CellValueFormatter.php',
            'sources_custom/openspout/src/Reader/XLSX/Helper/DateFormatHelper.php',
            'sources_custom/openspout/src/Reader/XLSX/Helper/DateIntervalFormatHelper.php',
            'sources_custom/openspout/src/Reader/XLSX/Helper/index.html',
            'sources_custom/openspout/src/Reader/XLSX/Manager/.htaccess',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SharedStringsCaching/.htaccess',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SharedStringsCaching/CachingStrategyFactory.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SharedStringsCaching/CachingStrategyFactoryInterface.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SharedStringsCaching/CachingStrategyInterface.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SharedStringsCaching/FileBasedStrategy.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SharedStringsCaching/InMemoryStrategy.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SharedStringsCaching/MemoryLimit.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SharedStringsCaching/index.html',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SharedStringsManager.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/SheetManager.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/StyleManager.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/StyleManagerInterface.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/WorkbookRelationshipsManager.php',
            'sources_custom/openspout/src/Reader/XLSX/Manager/index.html',
            'sources_custom/openspout/src/Reader/XLSX/Options.php',
            'sources_custom/openspout/src/Reader/XLSX/Reader.php',
            'sources_custom/openspout/src/Reader/XLSX/RowIterator.php',
            'sources_custom/openspout/src/Reader/XLSX/Sheet.php',
            'sources_custom/openspout/src/Reader/XLSX/SheetHeaderReader.php',
            'sources_custom/openspout/src/Reader/XLSX/SheetIterator.php',
            'sources_custom/openspout/src/Reader/XLSX/SheetMergeCellsReader.php',
            'sources_custom/openspout/src/Reader/XLSX/index.html',
            'sources_custom/openspout/src/Reader/index.html',
            'sources_custom/openspout/src/Writer/.htaccess',
            'sources_custom/openspout/src/Writer/AbstractWriter.php',
            'sources_custom/openspout/src/Writer/AbstractWriterMultiSheets.php',
            'sources_custom/openspout/src/Writer/AutoFilter.php',
            'sources_custom/openspout/src/Writer/CSV/.htaccess',
            'sources_custom/openspout/src/Writer/CSV/Options.php',
            'sources_custom/openspout/src/Writer/CSV/Writer.php',
            'sources_custom/openspout/src/Writer/CSV/index.html',
            'sources_custom/openspout/src/Writer/Common/.htaccess',
            'sources_custom/openspout/src/Writer/Common/AbstractOptions.php',
            'sources_custom/openspout/src/Writer/Common/ColumnWidth.php',
            'sources_custom/openspout/src/Writer/Common/Creator/.htaccess',
            'sources_custom/openspout/src/Writer/Common/Creator/WriterFactory.php',
            'sources_custom/openspout/src/Writer/Common/Creator/index.html',
            'sources_custom/openspout/src/Writer/Common/Entity/.htaccess',
            'sources_custom/openspout/src/Writer/Common/Entity/Sheet.php',
            'sources_custom/openspout/src/Writer/Common/Entity/Workbook.php',
            'sources_custom/openspout/src/Writer/Common/Entity/Worksheet.php',
            'sources_custom/openspout/src/Writer/Common/Entity/index.html',
            'sources_custom/openspout/src/Writer/Common/Helper/.htaccess',
            'sources_custom/openspout/src/Writer/Common/Helper/CellHelper.php',
            'sources_custom/openspout/src/Writer/Common/Helper/FileSystemWithRootFolderHelperInterface.php',
            'sources_custom/openspout/src/Writer/Common/Helper/ZipHelper.php',
            'sources_custom/openspout/src/Writer/Common/Helper/index.html',
            'sources_custom/openspout/src/Writer/Common/Manager/.htaccess',
            'sources_custom/openspout/src/Writer/Common/Manager/AbstractWorkbookManager.php',
            'sources_custom/openspout/src/Writer/Common/Manager/RegisteredStyle.php',
            'sources_custom/openspout/src/Writer/Common/Manager/SheetManager.php',
            'sources_custom/openspout/src/Writer/Common/Manager/Style/.htaccess',
            'sources_custom/openspout/src/Writer/Common/Manager/Style/AbstractStyleManager.php',
            'sources_custom/openspout/src/Writer/Common/Manager/Style/AbstractStyleRegistry.php',
            'sources_custom/openspout/src/Writer/Common/Manager/Style/PossiblyUpdatedStyle.php',
            'sources_custom/openspout/src/Writer/Common/Manager/Style/StyleManagerInterface.php',
            'sources_custom/openspout/src/Writer/Common/Manager/Style/StyleMerger.php',
            'sources_custom/openspout/src/Writer/Common/Manager/Style/index.html',
            'sources_custom/openspout/src/Writer/Common/Manager/WorkbookManagerInterface.php',
            'sources_custom/openspout/src/Writer/Common/Manager/WorksheetManagerInterface.php',
            'sources_custom/openspout/src/Writer/Common/Manager/index.html',
            'sources_custom/openspout/src/Writer/Common/index.html',
            'sources_custom/openspout/src/Writer/Exception/.htaccess',
            'sources_custom/openspout/src/Writer/Exception/Border/.htaccess',
            'sources_custom/openspout/src/Writer/Exception/Border/InvalidNameException.php',
            'sources_custom/openspout/src/Writer/Exception/Border/InvalidStyleException.php',
            'sources_custom/openspout/src/Writer/Exception/Border/InvalidWidthException.php',
            'sources_custom/openspout/src/Writer/Exception/Border/index.html',
            'sources_custom/openspout/src/Writer/Exception/InvalidSheetNameException.php',
            'sources_custom/openspout/src/Writer/Exception/SheetNotFoundException.php',
            'sources_custom/openspout/src/Writer/Exception/WriterAlreadyOpenedException.php',
            'sources_custom/openspout/src/Writer/Exception/WriterException.php',
            'sources_custom/openspout/src/Writer/Exception/WriterNotOpenedException.php',
            'sources_custom/openspout/src/Writer/Exception/index.html',
            'sources_custom/openspout/src/Writer/ODS/.htaccess',
            'sources_custom/openspout/src/Writer/ODS/Helper/.htaccess',
            'sources_custom/openspout/src/Writer/ODS/Helper/BorderHelper.php',
            'sources_custom/openspout/src/Writer/ODS/Helper/FileSystemHelper.php',
            'sources_custom/openspout/src/Writer/ODS/Helper/index.html',
            'sources_custom/openspout/src/Writer/ODS/Manager/.htaccess',
            'sources_custom/openspout/src/Writer/ODS/Manager/Style/.htaccess',
            'sources_custom/openspout/src/Writer/ODS/Manager/Style/StyleManager.php',
            'sources_custom/openspout/src/Writer/ODS/Manager/Style/StyleRegistry.php',
            'sources_custom/openspout/src/Writer/ODS/Manager/Style/index.html',
            'sources_custom/openspout/src/Writer/ODS/Manager/WorkbookManager.php',
            'sources_custom/openspout/src/Writer/ODS/Manager/WorksheetManager.php',
            'sources_custom/openspout/src/Writer/ODS/Manager/index.html',
            'sources_custom/openspout/src/Writer/ODS/Options.php',
            'sources_custom/openspout/src/Writer/ODS/Writer.php',
            'sources_custom/openspout/src/Writer/ODS/index.html',
            'sources_custom/openspout/src/Writer/WriterInterface.php',
            'sources_custom/openspout/src/Writer/XLSX/.htaccess',
            'sources_custom/openspout/src/Writer/XLSX/Entity/.htaccess',
            'sources_custom/openspout/src/Writer/XLSX/Entity/SheetView.php',
            'sources_custom/openspout/src/Writer/XLSX/Entity/index.html',
            'sources_custom/openspout/src/Writer/XLSX/Helper/.htaccess',
            'sources_custom/openspout/src/Writer/XLSX/Helper/BorderHelper.php',
            'sources_custom/openspout/src/Writer/XLSX/Helper/DateHelper.php',
            'sources_custom/openspout/src/Writer/XLSX/Helper/DateIntervalHelper.php',
            'sources_custom/openspout/src/Writer/XLSX/Helper/FileSystemHelper.php',
            'sources_custom/openspout/src/Writer/XLSX/Helper/index.html',
            'sources_custom/openspout/src/Writer/XLSX/Manager/.htaccess',
            'sources_custom/openspout/src/Writer/XLSX/Manager/CommentsManager.php',
            'sources_custom/openspout/src/Writer/XLSX/Manager/SharedStringsManager.php',
            'sources_custom/openspout/src/Writer/XLSX/Manager/Style/.htaccess',
            'sources_custom/openspout/src/Writer/XLSX/Manager/Style/StyleManager.php',
            'sources_custom/openspout/src/Writer/XLSX/Manager/Style/StyleRegistry.php',
            'sources_custom/openspout/src/Writer/XLSX/Manager/Style/index.html',
            'sources_custom/openspout/src/Writer/XLSX/Manager/WorkbookManager.php',
            'sources_custom/openspout/src/Writer/XLSX/Manager/WorksheetManager.php',
            'sources_custom/openspout/src/Writer/XLSX/Manager/index.html',
            'sources_custom/openspout/src/Writer/XLSX/MergeCell.php',
            'sources_custom/openspout/src/Writer/XLSX/Options.php',
            'sources_custom/openspout/src/Writer/XLSX/Options/.htaccess',
            'sources_custom/openspout/src/Writer/XLSX/Options/HeaderFooter.php',
            'sources_custom/openspout/src/Writer/XLSX/Options/PageMargin.php',
            'sources_custom/openspout/src/Writer/XLSX/Options/PageOrientation.php',
            'sources_custom/openspout/src/Writer/XLSX/Options/PageSetup.php',
            'sources_custom/openspout/src/Writer/XLSX/Options/PaperSize.php',
            'sources_custom/openspout/src/Writer/XLSX/Options/index.html',
            'sources_custom/openspout/src/Writer/XLSX/Writer.php',
            'sources_custom/openspout/src/Writer/XLSX/index.html',
            'sources_custom/openspout/src/Writer/index.html',
            'sources_custom/openspout/vendor/.htaccess',
            'sources_custom/openspout/vendor/autoload.php',
            'sources_custom/openspout/vendor/composer/.htaccess',
            'sources_custom/openspout/vendor/composer/ClassLoader.php',
            'sources_custom/openspout/vendor/composer/InstalledVersions.php',
            'sources_custom/openspout/vendor/composer/LICENSE',
            'sources_custom/openspout/vendor/composer/autoload_classmap.php',
            'sources_custom/openspout/vendor/composer/autoload_namespaces.php',
            'sources_custom/openspout/vendor/composer/autoload_psr4.php',
            'sources_custom/openspout/vendor/composer/autoload_real.php',
            'sources_custom/openspout/vendor/composer/autoload_static.php',
            'sources_custom/openspout/vendor/composer/index.html',
            'sources_custom/openspout/vendor/composer/installed.json',
            'sources_custom/openspout/vendor/composer/installed.php',
            'sources_custom/openspout/vendor/composer/platform_check.php',
            'sources_custom/openspout/vendor/index.html',
        ];
    }
}

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
 * Hook class.
 */
class Hook_addon_registry_excel_support
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Development';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'ocProducts';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'Contains code from the Spout project',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'BSD license';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'API support for writing Excel files as well as CSV files. Does require a much higher PHP memory limit.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'GD',
                'Zip',
                'XML',
                'PHP5.3',
            ),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/excel_support.php',
            'sources_custom/spout/.editorconfig',
            'sources_custom/spout/.gitignore',
            'sources_custom/spout/.travis.yml',
            'sources_custom/spout/composer.json',
            'sources_custom/spout/composer.phar',
            'sources_custom/spout/CONTRIBUTING.md',
            'sources_custom/spout/LICENSE',
            'sources_custom/spout/logo.png',
            'sources_custom/spout/phpunit.xml',
            'sources_custom/spout/README.md',
            'sources_custom/spout/src/Spout/Common/Escaper/CSV.php',
            'sources_custom/spout/src/Spout/Common/Escaper/EscaperInterface.php',
            'sources_custom/spout/src/Spout/Common/Escaper/XLSX.php',
            'sources_custom/spout/src/Spout/Common/Exception/BadUsageException.php',
            'sources_custom/spout/src/Spout/Common/Exception/InvalidArgumentException.php',
            'sources_custom/spout/src/Spout/Common/Exception/IOException.php',
            'sources_custom/spout/src/Spout/Common/Exception/SpoutException.php',
            'sources_custom/spout/src/Spout/Common/Exception/UnsupportedTypeException.php',
            'sources_custom/spout/src/Spout/Common/Helper/FileSystemHelper.php',
            'sources_custom/spout/src/Spout/Common/Helper/GlobalFunctionsHelper.php',
            'sources_custom/spout/src/Spout/Common/Type.php',
            'sources_custom/spout/src/Spout/Reader/AbstractReader.php',
            'sources_custom/spout/src/Spout/Reader/CSV.php',
            'sources_custom/spout/src/Spout/Reader/Exception/EndOfFileReachedException.php',
            'sources_custom/spout/src/Spout/Reader/Exception/EndOfWorksheetsReachedException.php',
            'sources_custom/spout/src/Spout/Reader/Exception/NoWorksheetsFoundException.php',
            'sources_custom/spout/src/Spout/Reader/Exception/ReaderException.php',
            'sources_custom/spout/src/Spout/Reader/Exception/ReaderNotOpenedException.php',
            'sources_custom/spout/src/Spout/Reader/Exception/SharedStringNotFoundException.php',
            'sources_custom/spout/src/Spout/Reader/Helper/XLSX/CellHelper.php',
            'sources_custom/spout/src/Spout/Reader/Helper/XLSX/SharedStringsHelper.php',
            'sources_custom/spout/src/Spout/Reader/Helper/XLSX/WorksheetHelper.php',
            'sources_custom/spout/src/Spout/Reader/Internal/XLSX/Worksheet.php',
            'sources_custom/spout/src/Spout/Reader/ReaderFactory.php',
            'sources_custom/spout/src/Spout/Reader/ReaderInterface.php',
            'sources_custom/spout/src/Spout/Reader/XLSX.php',
            'sources_custom/spout/src/Spout/Writer/AbstractWriter.php',
            'sources_custom/spout/src/Spout/Writer/CSV.php',
            'sources_custom/spout/src/Spout/Writer/Exception/SheetNotFoundException.php',
            'sources_custom/spout/src/Spout/Writer/Exception/WriterException.php',
            'sources_custom/spout/src/Spout/Writer/Exception/WriterNotOpenedException.php',
            'sources_custom/spout/src/Spout/Writer/Helper/XLSX/CellHelper.php',
            'sources_custom/spout/src/Spout/Writer/Helper/XLSX/FileSystemHelper.php',
            'sources_custom/spout/src/Spout/Writer/Helper/XLSX/SharedStringsHelper.php',
            'sources_custom/spout/src/Spout/Writer/Helper/XLSX/ZipHelper.php',
            'sources_custom/spout/src/Spout/Writer/HTM.php',
            'sources_custom/spout/src/Spout/Writer/Internal/XLSX/Workbook.php',
            'sources_custom/spout/src/Spout/Writer/Internal/XLSX/Worksheet.php',
            'sources_custom/spout/src/Spout/Writer/Sheet.php',
            'sources_custom/spout/src/Spout/Writer/WriterFactory.php',
            'sources_custom/spout/src/Spout/Writer/WriterInterface.php',
            'sources_custom/spout/src/Spout/Writer/XLS.php',
            'sources_custom/spout/src/Spout/Writer/XLSX.php',
            'sources_custom/spout/tests/bootstrap.php',
            'sources_custom/spout/tests/resources/csv/csv_delimited_with_pipes.csv',
            'sources_custom/spout/tests/resources/csv/csv_standard.csv',
            'sources_custom/spout/tests/resources/csv/csv_text_enclosed_with_pound.csv',
            'sources_custom/spout/tests/resources/csv/csv_with_comma_enclosed.csv',
            'sources_custom/spout/tests/resources/csv/csv_with_different_cells_number.csv',
            'sources_custom/spout/tests/resources/csv/csv_with_empty_cells.csv',
            'sources_custom/spout/tests/resources/csv/csv_with_empty_line.csv',
            'sources_custom/spout/tests/resources/csv/csv_with_utf8_bom.csv',
            'sources_custom/spout/tests/resources/xlsx/billion_laughs_test_file.xlsx',
            'sources_custom/spout/tests/resources/xlsx/file_corrupted.xlsx',
            'sources_custom/spout/tests/resources/xlsx/file_with_no_sheets_in_content_types.xlsx',
            'sources_custom/spout/tests/resources/xlsx/one_sheet_with_inline_strings.xlsx',
            'sources_custom/spout/tests/resources/xlsx/one_sheet_with_shared_strings.xlsx',
            'sources_custom/spout/tests/resources/xlsx/sheet_with_dimensions_and_empty_cells.xlsx',
            'sources_custom/spout/tests/resources/xlsx/sheet_with_empty_rows.xlsx',
            'sources_custom/spout/tests/resources/xlsx/sheet_with_no_cells.xlsx',
            'sources_custom/spout/tests/resources/xlsx/sheet_with_pronunciation.xlsx',
            'sources_custom/spout/tests/resources/xlsx/sheet_without_dimensions_and_empty_cells.xlsx',
            'sources_custom/spout/tests/resources/xlsx/sheet_without_dimensions_but_spans_and_empty_cells.xlsx',
            'sources_custom/spout/tests/resources/xlsx/two_sheets_with_inline_strings.xlsx',
            'sources_custom/spout/tests/resources/xlsx/two_sheets_with_shared_strings.xlsx',
            'sources_custom/spout/tests/Spout/Common/Escaper/XLSXTest.php',
            'sources_custom/spout/tests/Spout/Common/Helper/FileSystemHelperTest.php',
            'sources_custom/spout/tests/Spout/Reader/CSVTest.php',
            'sources_custom/spout/tests/Spout/Reader/Helper/XLSX/SharedStringsHelperTest.php',
            'sources_custom/spout/tests/Spout/Reader/XLSXTest.php',
            'sources_custom/spout/tests/Spout/ReflectionHelper.php',
            'sources_custom/spout/tests/Spout/TestUsingResource.php',
            'sources_custom/spout/tests/Spout/Writer/CSVTest.php',
            'sources_custom/spout/tests/Spout/Writer/Helper/XLSX/CellHelperTest.php',
            'sources_custom/spout/tests/Spout/Writer/SheetTest.php',
            'sources_custom/spout/tests/Spout/Writer/XLSXTest.php',
            'sources_custom/files2.php',
            'sources_custom/files_spout.php',
        );
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    pdf_viewer
 */

/**
 * Hook class.
 */
class Hook_addon_registry_pdf_viewer
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
        return 'New Features';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Apache Licence';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'PDF media (including the Comcode media tag, Comcode attachments, and in galleries) will be displayed inline using a PDF viewer than prevents easy download or printing.';
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
            'requires' => array(),
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
            'sources_custom/hooks/systems/addon_registry/pdf_viewer.php',
            'data_custom/pdf_viewer/LICENSE',
            'data_custom/pdf_viewer/build/pdf.js',
            'data_custom/pdf_viewer/build/pdf.js.map',
            'data_custom/pdf_viewer/build/pdf.worker.js',
            'data_custom/pdf_viewer/build/pdf.worker.js.map',
            'data_custom/pdf_viewer/web/cmaps/78-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/78-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/78-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/78-RKSJ-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/78-RKSJ-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/78-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/78ms-RKSJ-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/78ms-RKSJ-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/83pv-RKSJ-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/90ms-RKSJ-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/90ms-RKSJ-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/90msp-RKSJ-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/90msp-RKSJ-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/90pv-RKSJ-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/90pv-RKSJ-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Add-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Add-RKSJ-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Add-RKSJ-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Add-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-CNS1-0.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-CNS1-1.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-CNS1-2.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-CNS1-3.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-CNS1-4.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-CNS1-5.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-CNS1-6.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-CNS1-UCS2.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-GB1-0.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-GB1-1.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-GB1-2.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-GB1-3.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-GB1-4.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-GB1-5.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-GB1-UCS2.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Japan1-0.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Japan1-1.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Japan1-2.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Japan1-3.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Japan1-4.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Japan1-5.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Japan1-6.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Japan1-UCS2.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Korea1-0.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Korea1-1.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Korea1-2.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Adobe-Korea1-UCS2.bcmap',
            'data_custom/pdf_viewer/web/cmaps/B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/B5pc-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/B5pc-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/CNS-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/CNS-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/CNS1-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/CNS1-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/CNS2-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/CNS2-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/ETHK-B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/ETHK-B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/ETen-B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/ETen-B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/ETenms-B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/ETenms-B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Ext-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Ext-RKSJ-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Ext-RKSJ-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Ext-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GB-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GB-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GB-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GB-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBK-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBK-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBK2K-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBK2K-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBKp-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBKp-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBT-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBT-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBT-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBT-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBTpc-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBTpc-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBpc-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/GBpc-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKdla-B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKdla-B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKdlb-B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKdlb-B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKgccs-B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKgccs-B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKm314-B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKm314-B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKm471-B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKm471-B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKscs-B5-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/HKscs-B5-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Hankaku.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Hiragana.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSC-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSC-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSC-Johab-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSC-Johab-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSCms-UHC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSCms-UHC-HW-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSCms-UHC-HW-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSCms-UHC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSCpc-EUC-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/KSCpc-EUC-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Katakana.bcmap',
            'data_custom/pdf_viewer/web/cmaps/LICENSE',
            'data_custom/pdf_viewer/web/cmaps/NWP-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/NWP-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/RKSJ-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/RKSJ-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/Roman.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniCNS-UCS2-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniCNS-UCS2-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniCNS-UTF16-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniCNS-UTF16-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniCNS-UTF32-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniCNS-UTF32-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniCNS-UTF8-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniCNS-UTF8-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniGB-UCS2-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniGB-UCS2-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniGB-UTF16-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniGB-UTF16-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniGB-UTF32-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniGB-UTF32-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniGB-UTF8-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniGB-UTF8-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UCS2-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UCS2-HW-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UCS2-HW-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UCS2-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UTF16-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UTF16-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UTF32-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UTF32-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UTF8-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS-UTF8-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS2004-UTF16-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS2004-UTF16-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS2004-UTF32-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS2004-UTF32-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS2004-UTF8-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJIS2004-UTF8-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJISPro-UCS2-HW-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJISPro-UCS2-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJISPro-UTF8-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJISX0213-UTF32-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJISX0213-UTF32-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJISX02132004-UTF32-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniJISX02132004-UTF32-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniKS-UCS2-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniKS-UCS2-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniKS-UTF16-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniKS-UTF16-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniKS-UTF32-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniKS-UTF32-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniKS-UTF8-H.bcmap',
            'data_custom/pdf_viewer/web/cmaps/UniKS-UTF8-V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/V.bcmap',
            'data_custom/pdf_viewer/web/cmaps/WP-Symbol.bcmap',
            'data_custom/pdf_viewer/web/debugger.js',
            'data_custom/pdf_viewer/web/images/annotation-check.svg',
            'data_custom/pdf_viewer/web/images/annotation-comment.svg',
            'data_custom/pdf_viewer/web/images/annotation-help.svg',
            'data_custom/pdf_viewer/web/images/annotation-insert.svg',
            'data_custom/pdf_viewer/web/images/annotation-key.svg',
            'data_custom/pdf_viewer/web/images/annotation-newparagraph.svg',
            'data_custom/pdf_viewer/web/images/annotation-noicon.svg',
            'data_custom/pdf_viewer/web/images/annotation-note.svg',
            'data_custom/pdf_viewer/web/images/annotation-paragraph.svg',
            'data_custom/pdf_viewer/web/images/findbarButton-next-rtl.png',
            'data_custom/pdf_viewer/web/images/findbarButton-next-rtl@2x.png',
            'data_custom/pdf_viewer/web/images/findbarButton-next.png',
            'data_custom/pdf_viewer/web/images/findbarButton-next@2x.png',
            'data_custom/pdf_viewer/web/images/findbarButton-previous-rtl.png',
            'data_custom/pdf_viewer/web/images/findbarButton-previous-rtl@2x.png',
            'data_custom/pdf_viewer/web/images/findbarButton-previous.png',
            'data_custom/pdf_viewer/web/images/findbarButton-previous@2x.png',
            'data_custom/pdf_viewer/web/images/grab.cur',
            'data_custom/pdf_viewer/web/images/grabbing.cur',
            'data_custom/pdf_viewer/web/images/loading-icon.gif',
            'data_custom/pdf_viewer/web/images/loading-small.png',
            'data_custom/pdf_viewer/web/images/loading-small@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-documentProperties.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-documentProperties@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-firstPage.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-firstPage@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-handTool.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-handTool@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-lastPage.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-lastPage@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-rotateCcw.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-rotateCcw@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-rotateCw.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-rotateCw@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollHorizontal.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollHorizontal@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollVertical.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollVertical@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollWrapped.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollWrapped@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-selectTool.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-selectTool@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-spreadEven.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-spreadEven@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-spreadNone.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-spreadNone@2x.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-spreadOdd.png',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-spreadOdd@2x.png',
            'data_custom/pdf_viewer/web/images/shadow.png',
            'data_custom/pdf_viewer/web/images/texture.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-bookmark.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-bookmark@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-download.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-download@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-menuArrows.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-menuArrows@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-openFile.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-openFile@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageDown-rtl.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageDown-rtl@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageDown.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageDown@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageUp-rtl.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageUp-rtl@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageUp.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageUp@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-presentationMode.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-presentationMode@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-print.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-print@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-search.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-search@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-secondaryToolbarToggle-rtl.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-secondaryToolbarToggle-rtl@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-secondaryToolbarToggle.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-secondaryToolbarToggle@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-sidebarToggle-rtl.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-sidebarToggle-rtl@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-sidebarToggle.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-sidebarToggle@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewAttachments.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewAttachments@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewOutline-rtl.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewOutline-rtl@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewOutline.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewOutline@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewThumbnail.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewThumbnail@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-zoomIn.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-zoomIn@2x.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-zoomOut.png',
            'data_custom/pdf_viewer/web/images/toolbarButton-zoomOut@2x.png',
            'data_custom/pdf_viewer/web/images/treeitem-collapsed-rtl.png',
            'data_custom/pdf_viewer/web/images/treeitem-collapsed-rtl@2x.png',
            'data_custom/pdf_viewer/web/images/treeitem-collapsed.png',
            'data_custom/pdf_viewer/web/images/treeitem-collapsed@2x.png',
            'data_custom/pdf_viewer/web/images/treeitem-expanded.png',
            'data_custom/pdf_viewer/web/images/treeitem-expanded@2x.png',
            'data_custom/pdf_viewer/web/locale/ach/viewer.properties',
            'data_custom/pdf_viewer/web/locale/af/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ak/viewer.properties',
            'data_custom/pdf_viewer/web/locale/an/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ar/viewer.properties',
            'data_custom/pdf_viewer/web/locale/as/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ast/viewer.properties',
            'data_custom/pdf_viewer/web/locale/az/viewer.properties',
            'data_custom/pdf_viewer/web/locale/be/viewer.properties',
            'data_custom/pdf_viewer/web/locale/bg/viewer.properties',
            'data_custom/pdf_viewer/web/locale/bn-BD/viewer.properties',
            'data_custom/pdf_viewer/web/locale/bn-IN/viewer.properties',
            'data_custom/pdf_viewer/web/locale/br/viewer.properties',
            'data_custom/pdf_viewer/web/locale/brx/viewer.properties',
            'data_custom/pdf_viewer/web/locale/bs/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ca/viewer.properties',
            'data_custom/pdf_viewer/web/locale/cak/viewer.properties',
            'data_custom/pdf_viewer/web/locale/crh/viewer.properties',
            'data_custom/pdf_viewer/web/locale/cs/viewer.properties',
            'data_custom/pdf_viewer/web/locale/csb/viewer.properties',
            'data_custom/pdf_viewer/web/locale/cy/viewer.properties',
            'data_custom/pdf_viewer/web/locale/da/viewer.properties',
            'data_custom/pdf_viewer/web/locale/de/viewer.properties',
            'data_custom/pdf_viewer/web/locale/el/viewer.properties',
            'data_custom/pdf_viewer/web/locale/en-CA/viewer.properties',
            'data_custom/pdf_viewer/web/locale/en-GB/viewer.properties',
            'data_custom/pdf_viewer/web/locale/en-US/viewer.properties',
            'data_custom/pdf_viewer/web/locale/en-ZA/viewer.properties',
            'data_custom/pdf_viewer/web/locale/eo/viewer.properties',
            'data_custom/pdf_viewer/web/locale/es-AR/viewer.properties',
            'data_custom/pdf_viewer/web/locale/es-CL/viewer.properties',
            'data_custom/pdf_viewer/web/locale/es-ES/viewer.properties',
            'data_custom/pdf_viewer/web/locale/es-MX/viewer.properties',
            'data_custom/pdf_viewer/web/locale/et/viewer.properties',
            'data_custom/pdf_viewer/web/locale/eu/viewer.properties',
            'data_custom/pdf_viewer/web/locale/fa/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ff/viewer.properties',
            'data_custom/pdf_viewer/web/locale/fi/viewer.properties',
            'data_custom/pdf_viewer/web/locale/fr/viewer.properties',
            'data_custom/pdf_viewer/web/locale/fy-NL/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ga-IE/viewer.properties',
            'data_custom/pdf_viewer/web/locale/gd/viewer.properties',
            'data_custom/pdf_viewer/web/locale/gl/viewer.properties',
            'data_custom/pdf_viewer/web/locale/gn/viewer.properties',
            'data_custom/pdf_viewer/web/locale/gu-IN/viewer.properties',
            'data_custom/pdf_viewer/web/locale/he/viewer.properties',
            'data_custom/pdf_viewer/web/locale/hi-IN/viewer.properties',
            'data_custom/pdf_viewer/web/locale/hr/viewer.properties',
            'data_custom/pdf_viewer/web/locale/hsb/viewer.properties',
            'data_custom/pdf_viewer/web/locale/hto/viewer.properties',
            'data_custom/pdf_viewer/web/locale/hu/viewer.properties',
            'data_custom/pdf_viewer/web/locale/hy-AM/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ia/viewer.properties',
            'data_custom/pdf_viewer/web/locale/id/viewer.properties',
            'data_custom/pdf_viewer/web/locale/is/viewer.properties',
            'data_custom/pdf_viewer/web/locale/it/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ja/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ka/viewer.properties',
            'data_custom/pdf_viewer/web/locale/kab/viewer.properties',
            'data_custom/pdf_viewer/web/locale/kk/viewer.properties',
            'data_custom/pdf_viewer/web/locale/km/viewer.properties',
            'data_custom/pdf_viewer/web/locale/kn/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ko/viewer.properties',
            'data_custom/pdf_viewer/web/locale/kok/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ks/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ku/viewer.properties',
            'data_custom/pdf_viewer/web/locale/lg/viewer.properties',
            'data_custom/pdf_viewer/web/locale/lij/viewer.properties',
            'data_custom/pdf_viewer/web/locale/lo/viewer.properties',
            'data_custom/pdf_viewer/web/locale/locale.properties',
            'data_custom/pdf_viewer/web/locale/lt/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ltg/viewer.properties',
            'data_custom/pdf_viewer/web/locale/lv/viewer.properties',
            'data_custom/pdf_viewer/web/locale/mai/viewer.properties',
            'data_custom/pdf_viewer/web/locale/meh/viewer.properties',
            'data_custom/pdf_viewer/web/locale/mk/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ml/viewer.properties',
            'data_custom/pdf_viewer/web/locale/mn/viewer.properties',
            'data_custom/pdf_viewer/web/locale/mr/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ms/viewer.properties',
            'data_custom/pdf_viewer/web/locale/my/viewer.properties',
            'data_custom/pdf_viewer/web/locale/nb-NO/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ne-NP/viewer.properties',
            'data_custom/pdf_viewer/web/locale/nl/viewer.properties',
            'data_custom/pdf_viewer/web/locale/nn-NO/viewer.properties',
            'data_custom/pdf_viewer/web/locale/nso/viewer.properties',
            'data_custom/pdf_viewer/web/locale/oc/viewer.properties',
            'data_custom/pdf_viewer/web/locale/or/viewer.properties',
            'data_custom/pdf_viewer/web/locale/pa-IN/viewer.properties',
            'data_custom/pdf_viewer/web/locale/pl/viewer.properties',
            'data_custom/pdf_viewer/web/locale/pt-BR/viewer.properties',
            'data_custom/pdf_viewer/web/locale/pt-PT/viewer.properties',
            'data_custom/pdf_viewer/web/locale/rm/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ro/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ru/viewer.properties',
            'data_custom/pdf_viewer/web/locale/rw/viewer.properties',
            'data_custom/pdf_viewer/web/locale/sah/viewer.properties',
            'data_custom/pdf_viewer/web/locale/sat/viewer.properties',
            'data_custom/pdf_viewer/web/locale/si/viewer.properties',
            'data_custom/pdf_viewer/web/locale/sk/viewer.properties',
            'data_custom/pdf_viewer/web/locale/sl/viewer.properties',
            'data_custom/pdf_viewer/web/locale/son/viewer.properties',
            'data_custom/pdf_viewer/web/locale/sq/viewer.properties',
            'data_custom/pdf_viewer/web/locale/sr/viewer.properties',
            'data_custom/pdf_viewer/web/locale/sv-SE/viewer.properties',
            'data_custom/pdf_viewer/web/locale/sw/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ta-LK/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ta/viewer.properties',
            'data_custom/pdf_viewer/web/locale/te/viewer.properties',
            'data_custom/pdf_viewer/web/locale/th/viewer.properties',
            'data_custom/pdf_viewer/web/locale/tl/viewer.properties',
            'data_custom/pdf_viewer/web/locale/tn/viewer.properties',
            'data_custom/pdf_viewer/web/locale/tr/viewer.properties',
            'data_custom/pdf_viewer/web/locale/tsz/viewer.properties',
            'data_custom/pdf_viewer/web/locale/uk/viewer.properties',
            'data_custom/pdf_viewer/web/locale/ur/viewer.properties',
            'data_custom/pdf_viewer/web/locale/uz/viewer.properties',
            'data_custom/pdf_viewer/web/locale/vi/viewer.properties',
            'data_custom/pdf_viewer/web/locale/wo/viewer.properties',
            'data_custom/pdf_viewer/web/locale/xh/viewer.properties',
            'data_custom/pdf_viewer/web/locale/zam/viewer.properties',
            'data_custom/pdf_viewer/web/locale/zh-CN/viewer.properties',
            'data_custom/pdf_viewer/web/locale/zh-TW/viewer.properties',
            'data_custom/pdf_viewer/web/locale/zu/viewer.properties',
            'data_custom/pdf_viewer/web/viewer.css',
            'data_custom/pdf_viewer/web/viewer.html',
            'data_custom/pdf_viewer/web/viewer.js',
            'data_custom/pdf_viewer/web/viewer.js.map',
            'themes/default/templates_custom/MEDIA_PDF.tpl',
        );
    }
}

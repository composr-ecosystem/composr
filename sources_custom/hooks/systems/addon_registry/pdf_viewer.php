<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
        return '11'; // addon_version_auto_update 5584af7da0398b60c74c80d0c10f5282
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
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Information Display';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Apache Licence';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'PDF media (including the Comcode media tag, Comcode attachments, and in galleries) will be displayed inline using a PDF viewer than prevents easy download or printing.' . "\n\n" . 'Note that in order for this addon to work properly, your server must be able to specify the application/javascript MIME type for .mjs files. This is handled for Apache automatically if you use recommended.htaccess from the software.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [],
            'recommends' => [],
            'conflicts_with' => []
        ];
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'data_custom/pdf_viewer/LICENSE',
            'data_custom/pdf_viewer/build/index.html',
            'data_custom/pdf_viewer/build/pdf.mjs',
            'data_custom/pdf_viewer/build/pdf.mjs.map',
            'data_custom/pdf_viewer/build/pdf.sandbox.mjs',
            'data_custom/pdf_viewer/build/pdf.sandbox.mjs.map',
            'data_custom/pdf_viewer/build/pdf.worker.mjs',
            'data_custom/pdf_viewer/build/pdf.worker.mjs.map',
            'data_custom/pdf_viewer/index.html',
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
            'data_custom/pdf_viewer/web/cmaps/index.html',
            'data_custom/pdf_viewer/web/debugger.css',
            'data_custom/pdf_viewer/web/debugger.mjs',
            'data_custom/pdf_viewer/web/images/altText_add.svg',
            'data_custom/pdf_viewer/web/images/altText_done.svg',
            'data_custom/pdf_viewer/web/images/annotation-check.svg',
            'data_custom/pdf_viewer/web/images/annotation-comment.svg',
            'data_custom/pdf_viewer/web/images/annotation-help.svg',
            'data_custom/pdf_viewer/web/images/annotation-insert.svg',
            'data_custom/pdf_viewer/web/images/annotation-key.svg',
            'data_custom/pdf_viewer/web/images/annotation-newparagraph.svg',
            'data_custom/pdf_viewer/web/images/annotation-noicon.svg',
            'data_custom/pdf_viewer/web/images/annotation-note.svg',
            'data_custom/pdf_viewer/web/images/annotation-paperclip.svg',
            'data_custom/pdf_viewer/web/images/annotation-paragraph.svg',
            'data_custom/pdf_viewer/web/images/annotation-pushpin.svg',
            'data_custom/pdf_viewer/web/images/cursor-editorFreeText.svg',
            'data_custom/pdf_viewer/web/images/cursor-editorInk.svg',
            'data_custom/pdf_viewer/web/images/editor-toolbar-delete.svg',
            'data_custom/pdf_viewer/web/images/findbarButton-next.svg',
            'data_custom/pdf_viewer/web/images/findbarButton-previous.svg',
            'data_custom/pdf_viewer/web/images/gv-toolbarButton-download.svg',
            'data_custom/pdf_viewer/web/images/gv-toolbarButton-openinapp.svg',
            'data_custom/pdf_viewer/web/images/index.html',
            'data_custom/pdf_viewer/web/images/loading-icon.gif',
            'data_custom/pdf_viewer/web/images/loading.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-documentProperties.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-firstPage.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-handTool.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-lastPage.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-rotateCcw.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-rotateCw.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollHorizontal.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollPage.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollVertical.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-scrollWrapped.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-selectTool.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-spreadEven.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-spreadNone.svg',
            'data_custom/pdf_viewer/web/images/secondaryToolbarButton-spreadOdd.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-bookmark.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-currentOutlineItem.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-download.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-editorFreeText.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-editorHighlight.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-editorInk.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-editorStamp.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-menuArrow.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-openFile.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageDown.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-pageUp.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-presentationMode.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-print.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-search.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-secondaryToolbarToggle.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-sidebarToggle.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewAttachments.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewLayers.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewOutline.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-viewThumbnail.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-zoomIn.svg',
            'data_custom/pdf_viewer/web/images/toolbarButton-zoomOut.svg',
            'data_custom/pdf_viewer/web/images/treeitem-collapsed.svg',
            'data_custom/pdf_viewer/web/images/treeitem-expanded.svg',
            'data_custom/pdf_viewer/web/index.html',
            'data_custom/pdf_viewer/web/locale/ach/index.html',
            'data_custom/pdf_viewer/web/locale/ach/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/af/index.html',
            'data_custom/pdf_viewer/web/locale/af/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/an/index.html',
            'data_custom/pdf_viewer/web/locale/an/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ar/index.html',
            'data_custom/pdf_viewer/web/locale/ar/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ast/index.html',
            'data_custom/pdf_viewer/web/locale/ast/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/az/index.html',
            'data_custom/pdf_viewer/web/locale/az/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/be/index.html',
            'data_custom/pdf_viewer/web/locale/be/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/bg/index.html',
            'data_custom/pdf_viewer/web/locale/bg/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/bn/index.html',
            'data_custom/pdf_viewer/web/locale/bn/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/bo/index.html',
            'data_custom/pdf_viewer/web/locale/bo/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/br/index.html',
            'data_custom/pdf_viewer/web/locale/br/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/brx/index.html',
            'data_custom/pdf_viewer/web/locale/brx/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/bs/index.html',
            'data_custom/pdf_viewer/web/locale/bs/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ca/index.html',
            'data_custom/pdf_viewer/web/locale/ca/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/cak/index.html',
            'data_custom/pdf_viewer/web/locale/cak/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ckb/index.html',
            'data_custom/pdf_viewer/web/locale/ckb/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/cs/index.html',
            'data_custom/pdf_viewer/web/locale/cs/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/cy/index.html',
            'data_custom/pdf_viewer/web/locale/cy/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/da/index.html',
            'data_custom/pdf_viewer/web/locale/da/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/de/index.html',
            'data_custom/pdf_viewer/web/locale/de/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/dsb/index.html',
            'data_custom/pdf_viewer/web/locale/dsb/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/el/index.html',
            'data_custom/pdf_viewer/web/locale/el/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/en-CA/index.html',
            'data_custom/pdf_viewer/web/locale/en-CA/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/en-GB/index.html',
            'data_custom/pdf_viewer/web/locale/en-GB/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/en-US/index.html',
            'data_custom/pdf_viewer/web/locale/en-US/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/eo/index.html',
            'data_custom/pdf_viewer/web/locale/eo/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/es-AR/index.html',
            'data_custom/pdf_viewer/web/locale/es-AR/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/es-CL/index.html',
            'data_custom/pdf_viewer/web/locale/es-CL/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/es-ES/index.html',
            'data_custom/pdf_viewer/web/locale/es-ES/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/es-MX/index.html',
            'data_custom/pdf_viewer/web/locale/es-MX/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/et/index.html',
            'data_custom/pdf_viewer/web/locale/et/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/eu/index.html',
            'data_custom/pdf_viewer/web/locale/eu/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/fa/index.html',
            'data_custom/pdf_viewer/web/locale/fa/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ff/index.html',
            'data_custom/pdf_viewer/web/locale/ff/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/fi/index.html',
            'data_custom/pdf_viewer/web/locale/fi/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/fr/index.html',
            'data_custom/pdf_viewer/web/locale/fr/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/fur/index.html',
            'data_custom/pdf_viewer/web/locale/fur/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/fy-NL/index.html',
            'data_custom/pdf_viewer/web/locale/fy-NL/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ga-IE/index.html',
            'data_custom/pdf_viewer/web/locale/ga-IE/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/gd/index.html',
            'data_custom/pdf_viewer/web/locale/gd/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/gl/index.html',
            'data_custom/pdf_viewer/web/locale/gl/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/gn/index.html',
            'data_custom/pdf_viewer/web/locale/gn/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/gu-IN/index.html',
            'data_custom/pdf_viewer/web/locale/gu-IN/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/he/index.html',
            'data_custom/pdf_viewer/web/locale/he/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/hi-IN/index.html',
            'data_custom/pdf_viewer/web/locale/hi-IN/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/hr/index.html',
            'data_custom/pdf_viewer/web/locale/hr/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/hsb/index.html',
            'data_custom/pdf_viewer/web/locale/hsb/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/hu/index.html',
            'data_custom/pdf_viewer/web/locale/hu/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/hy-AM/index.html',
            'data_custom/pdf_viewer/web/locale/hy-AM/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/hye/index.html',
            'data_custom/pdf_viewer/web/locale/hye/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ia/index.html',
            'data_custom/pdf_viewer/web/locale/ia/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/id/index.html',
            'data_custom/pdf_viewer/web/locale/id/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/index.html',
            'data_custom/pdf_viewer/web/locale/is/index.html',
            'data_custom/pdf_viewer/web/locale/is/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/it/index.html',
            'data_custom/pdf_viewer/web/locale/it/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ja/index.html',
            'data_custom/pdf_viewer/web/locale/ja/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ka/index.html',
            'data_custom/pdf_viewer/web/locale/ka/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/kab/index.html',
            'data_custom/pdf_viewer/web/locale/kab/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/kk/index.html',
            'data_custom/pdf_viewer/web/locale/kk/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/km/index.html',
            'data_custom/pdf_viewer/web/locale/km/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/kn/index.html',
            'data_custom/pdf_viewer/web/locale/kn/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ko/index.html',
            'data_custom/pdf_viewer/web/locale/ko/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/lij/index.html',
            'data_custom/pdf_viewer/web/locale/lij/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/lo/index.html',
            'data_custom/pdf_viewer/web/locale/lo/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/locale.json',
            'data_custom/pdf_viewer/web/locale/lt/index.html',
            'data_custom/pdf_viewer/web/locale/lt/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ltg/index.html',
            'data_custom/pdf_viewer/web/locale/ltg/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/lv/index.html',
            'data_custom/pdf_viewer/web/locale/lv/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/meh/index.html',
            'data_custom/pdf_viewer/web/locale/meh/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/mk/index.html',
            'data_custom/pdf_viewer/web/locale/mk/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/mr/index.html',
            'data_custom/pdf_viewer/web/locale/mr/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ms/index.html',
            'data_custom/pdf_viewer/web/locale/ms/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/my/index.html',
            'data_custom/pdf_viewer/web/locale/my/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/nb-NO/index.html',
            'data_custom/pdf_viewer/web/locale/nb-NO/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ne-NP/index.html',
            'data_custom/pdf_viewer/web/locale/ne-NP/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/nl/index.html',
            'data_custom/pdf_viewer/web/locale/nl/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/nn-NO/index.html',
            'data_custom/pdf_viewer/web/locale/nn-NO/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/oc/index.html',
            'data_custom/pdf_viewer/web/locale/oc/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/pa-IN/index.html',
            'data_custom/pdf_viewer/web/locale/pa-IN/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/pl/index.html',
            'data_custom/pdf_viewer/web/locale/pl/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/pt-BR/index.html',
            'data_custom/pdf_viewer/web/locale/pt-BR/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/pt-PT/index.html',
            'data_custom/pdf_viewer/web/locale/pt-PT/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/rm/index.html',
            'data_custom/pdf_viewer/web/locale/rm/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ro/index.html',
            'data_custom/pdf_viewer/web/locale/ro/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ru/index.html',
            'data_custom/pdf_viewer/web/locale/ru/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/sat/index.html',
            'data_custom/pdf_viewer/web/locale/sat/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/sc/index.html',
            'data_custom/pdf_viewer/web/locale/sc/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/scn/index.html',
            'data_custom/pdf_viewer/web/locale/scn/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/sco/index.html',
            'data_custom/pdf_viewer/web/locale/sco/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/si/index.html',
            'data_custom/pdf_viewer/web/locale/si/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/sk/index.html',
            'data_custom/pdf_viewer/web/locale/sk/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/skr/index.html',
            'data_custom/pdf_viewer/web/locale/skr/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/sl/index.html',
            'data_custom/pdf_viewer/web/locale/sl/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/son/index.html',
            'data_custom/pdf_viewer/web/locale/son/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/sq/index.html',
            'data_custom/pdf_viewer/web/locale/sq/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/sr/index.html',
            'data_custom/pdf_viewer/web/locale/sr/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/sv-SE/index.html',
            'data_custom/pdf_viewer/web/locale/sv-SE/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/szl/index.html',
            'data_custom/pdf_viewer/web/locale/szl/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ta/index.html',
            'data_custom/pdf_viewer/web/locale/ta/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/te/index.html',
            'data_custom/pdf_viewer/web/locale/te/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/tg/index.html',
            'data_custom/pdf_viewer/web/locale/tg/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/th/index.html',
            'data_custom/pdf_viewer/web/locale/th/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/tl/index.html',
            'data_custom/pdf_viewer/web/locale/tl/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/tr/index.html',
            'data_custom/pdf_viewer/web/locale/tr/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/trs/index.html',
            'data_custom/pdf_viewer/web/locale/trs/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/uk/index.html',
            'data_custom/pdf_viewer/web/locale/uk/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/ur/index.html',
            'data_custom/pdf_viewer/web/locale/ur/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/uz/index.html',
            'data_custom/pdf_viewer/web/locale/uz/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/vi/index.html',
            'data_custom/pdf_viewer/web/locale/vi/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/wo/index.html',
            'data_custom/pdf_viewer/web/locale/wo/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/xh/index.html',
            'data_custom/pdf_viewer/web/locale/xh/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/zh-CN/index.html',
            'data_custom/pdf_viewer/web/locale/zh-CN/viewer.ftl',
            'data_custom/pdf_viewer/web/locale/zh-TW/index.html',
            'data_custom/pdf_viewer/web/locale/zh-TW/viewer.ftl',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitDingbats.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitFixed.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitFixedBold.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitFixedBoldItalic.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitFixedItalic.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitSerif.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitSerifBold.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitSerifBoldItalic.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitSerifItalic.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/FoxitSymbol.pfb',
            'data_custom/pdf_viewer/web/standard_fonts/LICENSE_FOXIT',
            'data_custom/pdf_viewer/web/standard_fonts/LICENSE_LIBERATION',
            'data_custom/pdf_viewer/web/standard_fonts/LiberationSans-Bold.ttf',
            'data_custom/pdf_viewer/web/standard_fonts/LiberationSans-BoldItalic.ttf',
            'data_custom/pdf_viewer/web/standard_fonts/LiberationSans-Italic.ttf',
            'data_custom/pdf_viewer/web/standard_fonts/LiberationSans-Regular.ttf',
            'data_custom/pdf_viewer/web/standard_fonts/index.html',
            'data_custom/pdf_viewer/web/viewer.css',
            'data_custom/pdf_viewer/web/viewer.html',
            'data_custom/pdf_viewer/web/viewer.mjs',
            'data_custom/pdf_viewer/web/viewer.mjs.map',
            'sources_custom/hooks/systems/addon_registry/pdf_viewer.php',
            'themes/default/templates_custom/MEDIA_PDF.tpl',
        ];
    }
}

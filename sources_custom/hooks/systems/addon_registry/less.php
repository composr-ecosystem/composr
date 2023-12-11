<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    less
 */

/**
 * Hook class.
 */
class Hook_addon_registry_less
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
        return 'Admin Utilities';
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
        return array(
            // iLess
            'Michal Moravec',
            'Martin Hason',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'MIT';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Native support for .less files as well as .css files. Note that this support doesn\'t include the files showing up in the CSS editor; it is assumed you are an advanced web designer and comfortable editing via a text editor.';
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
            'conflicts_with' => array(),
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
            'sources_custom/hooks/systems/addon_registry/less.php',

            // iLess
            'sources_custom/ILess/ANSIColor.php',
            'sources_custom/ILess/Autoloader.php',
            'sources_custom/ILess/CLI.php',
            'sources_custom/ILess/Cache.php',
            'sources_custom/ILess/Cache/FileSystem.php',
            'sources_custom/ILess/Cache/None.php',
            'sources_custom/ILess/CacheInterface.php',
            'sources_custom/ILess/Color.php',
            'sources_custom/ILess/Configurable.php',
            'sources_custom/ILess/DebugInfo.php',
            'sources_custom/ILess/Environment.php',
            'sources_custom/ILess/Exception.php',
            'sources_custom/ILess/Exception/Cache.php',
            'sources_custom/ILess/Exception/Compiler.php',
            'sources_custom/ILess/Exception/Function.php',
            'sources_custom/ILess/Exception/IO.php',
            'sources_custom/ILess/Exception/Import.php',
            'sources_custom/ILess/Exception/Parser.php',
            'sources_custom/ILess/FileInfo.php',
            'sources_custom/ILess/FunctionRegistry.php',
            'sources_custom/ILess/ImportedFile.php',
            'sources_custom/ILess/Importer.php',
            'sources_custom/ILess/Importer/Array.php',
            'sources_custom/ILess/Importer/Callback.php',
            'sources_custom/ILess/Importer/Database.php',
            'sources_custom/ILess/Importer/FileSystem.php',
            'sources_custom/ILess/ImporterInterface.php',
            'sources_custom/ILess/Math.php',
            'sources_custom/ILess/Mime.php',
            'sources_custom/ILess/Node.php',
            'sources_custom/ILess/Node/Alpha.php',
            'sources_custom/ILess/Node/Anonymous.php',
            'sources_custom/ILess/Node/Assignment.php',
            'sources_custom/ILess/Node/Attribute.php',
            'sources_custom/ILess/Node/Call.php',
            'sources_custom/ILess/Node/Color.php',
            'sources_custom/ILess/Node/Combinator.php',
            'sources_custom/ILess/Node/Comment.php',
            'sources_custom/ILess/Node/ComparableInterface.php',
            'sources_custom/ILess/Node/Condition.php',
            'sources_custom/ILess/Node/Dimension.php',
            'sources_custom/ILess/Node/DimensionUnit.php',
            'sources_custom/ILess/Node/Directive.php',
            'sources_custom/ILess/Node/Element.php',
            'sources_custom/ILess/Node/Expression.php',
            'sources_custom/ILess/Node/Extend.php',
            'sources_custom/ILess/Node/Import.php',
            'sources_custom/ILess/Node/Javascript.php',
            'sources_custom/ILess/Node/Keyword.php',
            'sources_custom/ILess/Node/MakeableImportantInterface.php',
            'sources_custom/ILess/Node/MarkableAsReferencedInterface.php',
            'sources_custom/ILess/Node/Media.php',
            'sources_custom/ILess/Node/MixinCall.php',
            'sources_custom/ILess/Node/MixinDefinition.php',
            'sources_custom/ILess/Node/Negative.php',
            'sources_custom/ILess/Node/Operation.php',
            'sources_custom/ILess/Node/Paren.php',
            'sources_custom/ILess/Node/Quoted.php',
            'sources_custom/ILess/Node/Rule.php',
            'sources_custom/ILess/Node/Ruleset.php',
            'sources_custom/ILess/Node/Selector.php',
            'sources_custom/ILess/Node/UnicodeDescriptor.php',
            'sources_custom/ILess/Node/Url.php',
            'sources_custom/ILess/Node/Value.php',
            'sources_custom/ILess/Node/Variable.php',
            'sources_custom/ILess/Node/VisitableInterface.php',
            'sources_custom/ILess/Output.php',
            'sources_custom/ILess/Output/Mapped.php',
            'sources_custom/ILess/OutputFilter.php',
            'sources_custom/ILess/OutputFilter/GzCompress.php',
            'sources_custom/ILess/OutputFilterInterface.php',
            'sources_custom/ILess/Parser.php',
            'sources_custom/ILess/Parser/Core.php',
            'sources_custom/ILess/SourceMap/Base64VLQ.php',
            'sources_custom/ILess/SourceMap/Generator.php',
            'sources_custom/ILess/StringExcerpt.php',
            'sources_custom/ILess/UnitConversion.php',
            'sources_custom/ILess/Util.php',
            'sources_custom/ILess/Variable.php',
            'sources_custom/ILess/Visitor.php',
            'sources_custom/ILess/Visitor/Arguments.php',
            'sources_custom/ILess/Visitor/ExtendFinder.php',
            'sources_custom/ILess/Visitor/Import.php',
            'sources_custom/ILess/Visitor/JoinSelector.php',
            'sources_custom/ILess/Visitor/ProcessExtend.php',
            'sources_custom/ILess/Visitor/ToCSS.php',
        );
    }
}


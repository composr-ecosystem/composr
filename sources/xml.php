<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/*EXTRA FUNCTIONS: xml_.**/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/*
XML or JSON?

We generally prefer JSON when connecting to 3rd party backend services, where no human touches the data transferred.
However in some cases particular standards do require XML, we do use XML for our own AJAX, and we generally tend to prefer XML for user-edited file formats.

To use JSON in Composr, use standard PHP functions and do require_code('json'); in advance so we can plug in missing functions for older PHP versions.
*/

/**
 * Get XML definition of common entities we may use.
 *
 * @return string XML
 */
function get_xml_entities()
{
    return '
        <!DOCTYPE xc:content [
        <!ENTITY euro "&#8364;">
        <!ENTITY ldquo "&#8220;">
        <!ENTITY rdquo "&#8221;">
        <!ENTITY lsquo "&#8216;">
        <!ENTITY rsquo "&#8217;">
        <!ENTITY dagger "&#8224;">
        <!ENTITY Dagger "&#8225;">
        <!ENTITY permil "&#8240;">
        <!ENTITY Scaron "&#352;">
        <!ENTITY scaron "&#353;">
        <!ENTITY Yuml "&#376;">
        <!ENTITY ndash "&#8211;">
        <!ENTITY mdash "&#8212;">
        <!ENTITY hellip "&#8230;">
        <!ENTITY copy "&#169;">
        <!ENTITY nbsp " ">
        <!ENTITY fnof "&#402;">
        <!ENTITY reg "&#174;">
        <!ENTITY trade "&#8482;">
        <!ENTITY raquo "&#187;">
        <!ENTITY frac14 "&#188;">
        <!ENTITY frac12 "&#189;">
        <!ENTITY frac34 "&#190;">
        <!ENTITY rarr "&#8594;">
        ]>
    ';
}

/**
 * XML escape the input string.
 *
 * @param  string $string Input string
 * @param  ?string $charset Charset (null: current)
 * @return string Escaped version of input string
 */
function xmlentities($string, $charset = null)
{
    if (is_null($charset)) {
        $charset = get_charset();
    }
    if (ENT_SUBSTITUTE == 0 && $charset == 'utf-8') {
        $string = fix_bad_unicode($string);
    }
    $ret = htmlspecialchars($string, ENT_COMPAT | ENT_SUBSTITUTE, $charset); // htmlspecialchars is appropriate, htmlentities uses entities XML does not have
    if (function_exists('ocp_mark_as_escaped')) {
        ocp_mark_as_escaped($ret);
    }
    return $ret;
}

/**
 * Escape an XML CDATA section.
 *
 * @param  string $string Input string
 * @return string Escaped version of input string
 */
function escape_cdata($string)
{
    return str_replace(']]>', ']]]]><![CDATA[>', $string);
}

/**
 * Convert HTML entities to plain characters for XML conformance.
 *
 * @param  string $data HTML to convert entities from
 * @param  string $charset The character set we are using for $data (both in and out)
 * @return string Valid XHTML
 */
function convert_bad_entities($data, $charset = 'ISO-8859-1')
{
    if (defined('ENT_HTML401')) { // PHP5.4+, we must explicitly give the charset, but when we do it helps us
        if ((strtoupper($charset) != 'ISO-8859-1') && (strtolower($charset) != 'utf-8')) {
            $charset = 'ISO-8859-1';
        }
        $table = array_flip(get_html_translation_table(HTML_ENTITIES, ENT_COMPAT | ENT_HTML401, $charset));
    } else {
        $table = array_flip(get_html_translation_table(HTML_ENTITIES));

        if (strtolower($charset) == 'utf-8') {
            require_code('character_sets');
            foreach ($table as $x => $y) {
                $table[$x] = convert_to_internal_encoding($y, 'ISO-8859-1', 'utf-8');
            }
        }
    }

    unset($table['&amp;']);
    unset($table['&gt;']);
    unset($table['&lt;']);
    unset($table['&quot;']);

    return strtr($data, $table);
}

/**
 * Simple XML reader.
 *
 * @package    core
 */
class CMS_simple_xml_reader
{
    // Used during parsing
    public $tag_stack, $attribute_stack, $children_stack, $text_stack;

    public $gleamed, $error;

    /**
     * Constructs the XML reader: parses the given data. Check $gleamed and $error after constructing.
     *
     * @param  string $xml_data The XML data
     */
    public function __construct($xml_data)
    {
        $this->gleamed = array();
        $this->error = null;

        $this->tag_stack = array();
        $this->attribute_stack = array();
        $this->children_stack = array();
        $this->text_stack = array();

        if (!function_exists('xml_parser_create')) {
            $this->error = do_lang_tempcode('XML_NEEDED');
            return;
        }

        // Our internal charset
        $parser_charset = get_charset();
        if (!in_array(strtoupper($parser_charset), array('ISO-8859-1', 'US-ASCII', 'utf-8'))) {
            $parser_charset = 'utf-8';
        }

        // Create and setup our parser
        if (function_exists('libxml_disable_entity_loader')) {
            @libxml_disable_entity_loader(); // LEGACY
        }
        $xml_parser = @xml_parser_create_ns($parser_charset);
        if ($xml_parser === false) {
            $this->error = do_lang_tempcode('XML_PARSING_NOT_SUPPORTED');
            return; // PHP5 default build on windows comes with this function disabled, so we need to be able to escape on error
        }
        xml_set_object($xml_parser, $this);
        @xml_parser_set_option($xml_parser, XML_OPTION_TARGET_ENCODING, $parser_charset);
        xml_set_element_handler($xml_parser, 'startElement', 'endElement');
        xml_set_character_data_handler($xml_parser, 'startText');

        if (strpos($xml_data, '<' . '?xml') === false) {
            $xml_data = '<' . '?xml version="1.0" encoding="' . xmlentities($parser_charset) . '"?' . '>' . $xml_data;
        }
        $xml_data = unixify_line_format($xml_data, $parser_charset); // Fixes Windows characters

        if (xml_parse($xml_parser, $xml_data, true) == 0) {
            $err_code = xml_get_error_code($xml_parser);
            $err_msg = xml_error_string($err_code) . ' [#' . strval($err_code) . ' @ ' . strval(xml_get_current_line_number($xml_parser)) . ']';
            warn_exit($err_msg);
        }

        @xml_parser_free($xml_parser);
    }

    /**
     * Standard PHP XML parser function.
     *
     * @param  object $parser The parser object (same as 'this')
     * @param  string $name The name of the element found
     * @param  array $attributes Array of attributes of the element
     */
    public function startElement($parser, $name, $attributes)
    {
        array_push($this->tag_stack, strtolower($name));
        if ($attributes != array()) {
            $attributes_lowered = array();
            foreach ($attributes as $key => $val) {
                $attributes_lowered[strtolower($key)] = $val;
            }
            $attributes = $attributes_lowered;
        }
        array_push($this->attribute_stack, $attributes);
        array_push($this->children_stack, array());
        array_push($this->text_stack, '');
    }

    /**
     * Standard PHP XML parser function.
     *
     * @param  object $parser The parser object (same as 'this')
     */
    public function endElement($parser)
    {
        $this_tag = array_pop($this->tag_stack);
        $this_attributes = array_pop($this->attribute_stack);
        $this_children = array_pop($this->children_stack);
        $this_text = array_pop($this->text_stack);

        if (count($this->tag_stack) == 0) {
            $this->gleamed = array($this_tag, $this_attributes, $this_text, $this_children);
        } else {
            $next_top_tags_children = array_pop($this->children_stack);
            $next_top_tags_children[] = array($this_tag, $this_attributes, $this_text, $this_children);
            array_push($this->children_stack, $next_top_tags_children);
        }
    }

    /**
     * Standard PHP XML parser function.
     *
     * @param  object $parser The parser object (same as 'this')
     * @param  string $data The text
     */
    public function startText($parser, $data)
    {
        $next_top_tags_text = array_pop($this->text_stack);
        $next_top_tags_text .= $data;
        array_push($this->text_stack, $next_top_tags_text);

        $next_top_tags_children = array_pop($this->children_stack);
        $next_top_tags_children[] = $data;
        array_push($this->children_stack, $next_top_tags_children);
    }

    /**
     * Pull a portion of an XML tree structure back into textual XML.
     *
     * @param  array $children Level of XML tree
     * @return string The combined XML
     */
    public function pull_together($children)
    {
        $data = '';
        foreach ($children as $_) {
            if (is_array($_)) {
                list($tag, $attributes, , $children) = $_;
                $drawn = '';
                foreach ($attributes as $key => $val) {
                    $drawn .= $key . '=' . xmlentities($val);
                }
                $data .= '<' . $tag . $drawn . '>' . $this->pull_together($children) . '</' . $tag . '>';
            } else {
                $data .= xmlentities($_);
            }
        }
        return $data;
    }
}

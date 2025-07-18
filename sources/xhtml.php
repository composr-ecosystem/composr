<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core
 */

/**
 * Function that tucks-under-the-carpet lumps of XHTML that knowingly won't conform to XHTML5.
 *
 * @param  Tempcode $html What to hide away
 * @return Tempcode Hidden away version
 */
function hide_the_evidence($html)
{
    return do_template('POOR_XHTML_WRAPPER', array('_GUID' => '4304e51390cd4c4dd5761b6af9682205', 'CONTENT' => $html));
}

/**
 * Function that 'fixes' HTML (or bad XHTML) enough for it to pass most basic structural webstandards checking.
 *
 * @param  string $html The XHTML string to convert to XHTML
 * @param  boolean $definitely_want Whether to force a repair even if we aren't enforcing XHTML strictness
 * @param  boolean $snippet Whether this is a partial snippet of HTML, embeddable in some unknown context
 * @return string The converted string
 */
function xhtmlise_html($html, $definitely_want = false, $snippet = false)
{
    // Tests...
    // echo xhtmlise_html('test<a></a><br /><po></p><p></po>'); // expect: test<a></a><br /><po><p></p></po>

    if (!$definitely_want) {
        return $html; // One day, this will get removed and we'll ensure all our output is always XHTML. But so far there's no point as IE doesn't support true XHTML
    }

    $is_escaped = (($GLOBALS['XSS_DETECT']) && (ocp_is_escaped($html)));

    $html = preg_replace('#<\!--.*($|-->)#Us', '', $html); // Strip comments

    require_code('obfuscate');
    require_code('webstandards');

    global $XML_CONSTRAIN, $LAST_TAG_ATTRIBUTES, $POS, $OUT, $TAG_STACK, $INBETWEEN_TEXT, $LEN, $WELL_FORMED_ONLY, $MUST_SELFCLOSE_TAGS, $NEVER_SELFCLOSE_TAGS, $LINENO, $LINESTART;
    $POS = 0;
    $OUT = $html;
    $LEN = strlen($html);
    $TAG_STACK = array();
    $WELL_FORMED_ONLY = true;
    $LINENO = 0;
    $LINESTART = 0;
    $XML_CONSTRAIN = true;

    $new = '';

    $token = _get_next_tag();

    // If we actually have a partial tag right at the start (ie. we're breaking into some HTML at a bad point)
    $ang_pos = strpos($INBETWEEN_TEXT, '>');
    if ($ang_pos !== false) {
        $INBETWEEN_TEXT = substr($INBETWEEN_TEXT, $ang_pos + 1);
    }

    $new .= fix_entities($INBETWEEN_TEXT);
    while ($token !== null) {
        while (is_array($token)) { // Some kind of error in our token
            if ($token[0] !== null) {
                $token = $token[0]; // We can at least discern something
            } else {
                $token = _get_next_tag(); // No, we need to just move on
            }
        }

        if ($token === null) {
            break;
        }

        $basis_token = _get_tag_basis($token);

        if ($basis_token != '') {
            // Open, close, or monitonic?
            $term = strpos($token, '/');

            if ($term !== 1) {
                if ((($term === false) || (isset($NEVER_SELFCLOSE_TAGS[$basis_token]))) && (!isset($MUST_SELFCLOSE_TAGS[$basis_token]))) { // Opening a tag
                    // Fix nesting
                    if (!$snippet) {
                        if (($basis_token == 'li') && (!in_array('ul', $TAG_STACK)) && (!in_array('ol', $TAG_STACK)) && (!in_array('dl', $TAG_STACK)) && (!in_array('dd', $TAG_STACK)) && (!in_array('dt', $TAG_STACK)) && (!in_array('dir', $TAG_STACK)) && (!in_array('menu', $TAG_STACK))) {
                            array_push($TAG_STACK, 'ul');
                            $new .= '<ul>';
                        }
                        if ((($basis_token == 'tr') || ($basis_token == 'colgroup') || ($basis_token == 'col') || ($basis_token == 'tbody') || ($basis_token == 'tfoot') || ($basis_token == 'thead') || ($basis_token == 'caption')) && (!in_array('table', $TAG_STACK))) {
                            array_push($TAG_STACK, 'table');
                            $new .= '<table>';
                        }
                        if ((($basis_token == 'td') || ($basis_token == 'th')) && (!in_array('table', $TAG_STACK))) {
                            array_push($TAG_STACK, 'table');
                            $new .= '<table>';
                            array_push($TAG_STACK, 'tr');
                            $new .= '<tr>';
                        }
                        if (($basis_token == 'param') && (!in_array('object', $TAG_STACK))) {
                            array_push($TAG_STACK, 'object');
                            $new .= '<object>';
                        }
                        if (($basis_token == 'option') && (!in_array('select', $TAG_STACK))) {
                            array_push($TAG_STACK, 'select');
                            $new .= '<select>';
                        }
                        if (($basis_token == 'noembed') && (!in_array('map', $TAG_STACK))) {
                            array_push($TAG_STACK, 'map');
                            $new .= '<map>';
                        }
                    }

                    array_push($TAG_STACK, $basis_token);
                    $new .= '<' . $basis_token;
                    foreach ($LAST_TAG_ATTRIBUTES as $key => $val) {
                        if (strpos($val, '&') !== false) {
                            $val = fix_entities($val);
                        }
                        $new .= ' ' . $key . '="' . $val . '"';
                    }
                    $new .= '>';
                } else { // Self-closing tag
                    $new .= '<' . $basis_token;
                    foreach ($LAST_TAG_ATTRIBUTES as $key => $val) {
                        if (strpos($val, '&') !== false) {
                            $val = fix_entities($val);
                        }
                        $new .= ' ' . $key . '="' . $val . '"';
                    }
                    $new .= ' />';
                }
            } else { // Closing a tag
                // For case 3
                if (!in_array($basis_token, $TAG_STACK)) {
                    // Do nothing, we can't handle it because we're closing something that was never opened
                } else {
                    $previous = '';
                    do {
                        $previous = array_pop($TAG_STACK);
                        if ($basis_token != $previous) {
                            $new .= '</' . $previous . '>'; // We'll have to assume it should be implicitly closed
                        }
                    } while ($basis_token != $previous);
                    $new .= '</' . $basis_token . '>'; // Ok so we finally got an opener match and managed to put out our closer
                }
            }
        }

        if ((in_array('style', $TAG_STACK)) || (in_array('script', $TAG_STACK))) { // Fix that script/style tags MUST be quoted explicitly as CDATA to be both XML and HTML compatible. HTML assumes CDATA, XML assumes not-CDATA unless specified
            $treat_as_cdata = in_array('style', $TAG_STACK) ? 'style' : 'script';
            $temp = '';
            while (true) {
                $token = _get_next_tag();
                $temp .= $INBETWEEN_TEXT;
                if (is_null($token)) {
                    break;
                }
                if ($token[0] != '</' . $treat_as_cdata . '>') {
                    if (!is_null($token[0])) {
                        $temp .= $token[0];
                    }
                } else {
                    break;
                }
            }
            if ($temp != '') {
                if (strpos($temp, '<![CDATA[') === false) {
                    $new .= '// <![CDATA[' . "\n";
                }
                $temp = str_replace('</', '<\/', $temp);
                if (strpos($temp, '<![CDATA[') === false) {
                    $temp = str_replace(']]>', ']]\'+\'>', $temp);
                }
                $new .= $temp;
                if (strpos($temp, '<![CDATA[') === false) {
                    $new .= '//]]>';
                }
            }
        } else {
            $token = _get_next_tag();
            if ($token === null) {
                // If we actually have a partial tag right at the end (ie. we're breaking out of some HTML at a bad point)
                $ang_pos = strpos($INBETWEEN_TEXT, '<');
                if ($ang_pos !== false) {
                    $INBETWEEN_TEXT = substr($INBETWEEN_TEXT, 0, $ang_pos);
                }
            }
            $new .= fix_entities($INBETWEEN_TEXT);
        }
    }

    // Check we have everything closed
    while (isset($TAG_STACK[0])) {
        $previous = array_pop($TAG_STACK);
        $new .= '</' . $previous . '>';
    }

    // Remove some empty tags that shouldn't be empty (e.g. table)
    $may_not_be_empty = array(
        /*'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'blockquote', 'pre',*/
        'br', 'hr',/*'fieldset', 'address', 'noscript',*/
        'table', 'tbody',
        'tfoot', 'thead', 'tr', 'dd', 'dt', 'dl', 'li', 'ol', 'ul', 'rbc', 'rtc', 'rb', 'rt', 'rp',/*'span',*/
        'abbr',
        'acronym', 'cite',/* 'code',*/
        'dfn',/* 'em', 'strong', 'kbd', 'q', 'samp', 'var', 'sub', 'sup', 'tt', 'del',*/
        'ruby', 'bdo',
        'img',/*'ins',*/
        'param', 'input', 'select', 'object', 'caption', 'label',/* 'b', 'i', 'small', 'big',*/
        'base', 'body', 'col', 'colgroup', 'map',
        'optgroup', 'legend', 'area', 'form',
    );
    foreach ($may_not_be_empty as $t) {
        if (stripos($new, '<' . $t) !== false) {
            $new = preg_replace('#<' . $t . '(\s[^>]*)?' . '>\s*</' . $t . '>#', '', $new);
        }
    }

    unset($OUT);
    unset($TAG_STACK);

    if ($is_escaped) {
        ocp_mark_as_escaped($new);
    }

    return $new;
}

/**
 * Turn a full HTML document into an HTML fragment.
 *
 * @param  string $html The document
 * @return string The fragment
 */
function extract_html_body($html)
{
    $matches = array();
    if (preg_match('#<body[^>]*>(.*)</body>#', $html, $matches) != 0) {
        return $matches[1];
    }
    return $html;
}

/**
 * XHTML-aware substring extraction. Note that it will strip images, linebreaks, rules, etc - it's intended really for inline XHTML.
 *
 * @param  string $html The text to perform on.
 * @param  integer $from The start position of the cut (HTML tags will be taken from before this though).
 * @param  ?integer $length The length you want the concatenated text to be (null: go to end).
 * @param  boolean $literal_pos Whether to do a substr based on literal position in XHTML, rather than based on number of visible characters.
 * @param  boolean $ellipses Whether to add ellipses.
 * @param  float $grammar_completeness_tolerance A fraction of the length we may exceed by in order to maintain grammar tolerance (finish sentences, finish paragraphs, finish the whole thing).
 * @return string The result.
 */
function xhtml_substr($html, $from, $length = null, $literal_pos = false, $ellipses = false, $grammar_completeness_tolerance = 0.0)
{
    $html = preg_replace('#<\!--.*($|-->)#Us', '', $html); // Strip comments
    $html = preg_replace('#[ \t]+#', ' ', $html); // Remove double spaces

    // Sort out the negative offset possibility
    if ($from < 0) {
        $from = strlen(strip_tags($html)) + $from; // Not perfectly accurate using strip_tags as it's a different algorithm - but close enough, considering all our XHTML is well formed anyway
    }

    $tag_stack = array(); // A stack of simple tags (opening only, just the names), that we can search
    $unbreakable_tag_stack = array(); // Booleans specifying whether the current tag must finished
    $current_tag = '';
    $in_tag = false;
    $in_entity = false;
    $in_tag_type = '';
    $real_from = 0;
    $_html_buildup = '';
    $html_buildup = array(); // A stack of HTML tags we need from before we start our portion, to move us into the right tag context. None tags are thrown out.

    $has_xhtml_substr_no_break_somewhere = (strpos($html, 'xhtml_substr_no_break') !== false); // Optimisation so as to run faster in most cases (only run extended check when needed)

    // Reset the character counter and pass through (part of) the entire text
    $c = 0; // The virtual length so far in the scan
    $total_length = strlen($html);
    $total_length_minus_one = $total_length - 1;
    $end_pos = is_null($length) ? $total_length : ($from + $length);
    for ($i = 0; $i < $total_length; $i++) {
        // Load the current character and the next one if the string has not arrived at the last character
        $current_char = $html[$i];
        $next_char = ($i < $total_length - 1) ? $html[$i + 1] : '';
        if ($in_entity) {
            if ($current_char == ';') {
                $in_entity = false;
            } else {
                if (preg_match('#[\#\w]#', $current_char) == 0) { // Broken entity
                    $in_entity = false;
                    $_html_buildup = preg_replace('#(.*)&#s', '${1}&amp;', $_html_buildup);
                    $i--;
                    continue;
                }
            }

            if (($literal_pos ? $i : $c) < $from || (($real_from == 0) && ($from != 0))) {
                $_html_buildup .= $current_char;
            }
        } elseif ($in_tag) {
            if (($current_char == '/') && (($next_char == '>') || ($current_tag == ''))) {
                if ($current_tag == '') {
                    $in_tag_type = 'CLOSE';
                } else {
                    $in_tag_type = 'SELF_CLOSE';
                }
            } elseif ($current_char == '>') {
                $in_tag = false;
                if (($in_tag_type == 'OPEN') || ($in_tag_type == '')) {
                    if ($current_tag != '!--') { // If not a comment (comment closing isn't even seen as a tag (except in html_buildup) -- for this we need to make sure we're sure to strip sections that may be contained within later. We need to do it like this as --> is not tag-like so wouldn't otherwise be recognised to be tag balanced. i.e. we copy it in always without intelligent removal then just wholesale strip it.
                        if (($grammar_completeness_tolerance != 0.0) && (_smart_grammar_says_futile($end_pos, $grammar_completeness_tolerance, $i + 1, $html, $length))) {
                            // Force termination
                            $length = 0;
                            $end_pos = 0;
                        }
                        if (($current_tag != 'br') && ($current_tag != 'img') && ($current_tag != 'hr')) { // A little sanity checking, for HTML used as XHTML
                            $tag_stack[] = $current_tag;
                            $matches = array();
                            $unbreakable_tag_stack[] = (($current_tag == 'figure') || ($current_tag == 'div') && ($has_xhtml_substr_no_break_somewhere) && (preg_match('#\sclass="[^"<>]*xhtml_substr_no_break[^"<>]*"[^<>]*$#', substr($html, 0, $i)) != 0));
                        }
                    }
                } elseif ($in_tag_type == 'CLOSE') {
                    if (@$tag_stack[count($tag_stack) - 1] == $current_tag) {
                        array_pop($tag_stack);
                        array_pop($unbreakable_tag_stack);
                    } else { // Invalid XHTML, but we need to clean up neatly
                        $reverse_tag_stack = array_reverse($tag_stack);
                        $reverse_unbreakable_tag_stack = array_reverse($unbreakable_tag_stack);
                        foreach ($reverse_tag_stack as $rti => $rtt) {
                            if ($rtt == $current_tag) {
                                unset($reverse_tag_stack[$rti]);
                                unset($reverse_unbreakable_tag_stack[$rti]);
                                $tag_stack = array_reverse($reverse_tag_stack);
                                $unbreakable_tag_stack = array_reverse($reverse_unbreakable_tag_stack);
                                break;
                            }
                        }
                    }
                } elseif ($in_tag_type == 'SELF_CLOSE') {
                    if (($grammar_completeness_tolerance != 0.0) && (_smart_grammar_says_futile($end_pos, $grammar_completeness_tolerance, $i + 1, $html, $length))) {
                        // Force termination
                        $length = 0;
                        $end_pos = 0;
                    }
                }
            } elseif (ltrim($current_char) == '') {
                $in_tag_type = 'OPEN';
            } elseif (($in_tag_type == '') || ($in_tag_type == 'CLOSE')) {
                $current_tag .= $current_char;
            }

            if (($literal_pos ? $i : $c) < $from || (($real_from == 0) && ($from != 0))) {
                $_html_buildup .= $current_char;
                if (!$in_tag) {
                    if (($in_tag_type == 'CLOSE') && (@$html_buildup[count($html_buildup) - 1][0] == $current_tag)) {
                        array_pop($html_buildup);
                    } elseif ($in_tag_type != 'SELF_CLOSE') {
                        $html_buildup[] = array($current_tag, $_html_buildup);
                    }
                }
            }
        } else {
            if ($current_char == '&') { // Entity starting
                $in_entity = true;
                $_html_buildup = '';
                if (($literal_pos ? $i : $c) < $from || (($real_from == 0) && ($from != 0))) {
                    $_html_buildup .= $current_char;
                }

                $c++;
            } elseif (($current_char == '<') && ($next_char == '!') && (substr($html, $i, 9) == '<![CDATA[')) { // Skip over CDATA
                $close = strpos($html, '//]]>', $i);
                if ($close !== false) {
                    $i = $close + 4;
                } else {
                    $i = $total_length;
                }
            } elseif ($current_char == '<') { // Tag starting
                $in_tag = true;

                // The regexp just checks for img tag match and grabs the src into $matches[1]
                $matches = array();
                if (isset($html[$i + 1]) && strtolower($html[$i + 1]) == 'i'/*Optimisation before we bother looking harder*/ && preg_match('#<img[^<>]+src="([^"]+)"#iA', $html, $matches, 0, $i) != 0) {
                    require_code('images');
                    list($width, $height) = _symbol_image_dims(array(html_entity_decode($matches[1], ENT_QUOTES, get_charset()), '0', '1')); // Safe way to grab image dimensions
                    if ($width == '') {
                        $width = get_option('thumb_width');
                        $height = get_option('thumb_width');
                    }
                    $pixels = intval($width) * intval($height);
                    $pixels_per_character = 15 * 15;
                    $img_characters = intval((float)$pixels / (float)$pixels_per_character);
                    $c += $img_characters;
                }

                $in_tag_type = '';
                $current_tag = '';
                $_html_buildup = '';
                if (($literal_pos ? $i : $c) < $from || (($real_from == 0) && ($from != 0))) {
                    $_html_buildup .= $current_char;
                }
            } else { // Plain text / plain text continues
                if ($real_from == 0) {
                    if ($literal_pos) {
                        // Optimisation: jump forwards as far as non-interesting
                        $jump_max_a = strpos($html, '<', $i);
                        if ($jump_max_a === false) {
                            $jump_max_a = $from;
                        } else {
                            $jump_max_a--;
                        }
                        $jump_max_b = strpos($html, '&', $i);
                        if ($jump_max_b === false) {
                            $jump_max_b = $from;
                        } else {
                            $jump_max_b--;
                        }
                        $jump_max_c = $from;
                        $min = min($jump_max_a, $jump_max_b, $jump_max_c);
                        $dif = $min - $i;
                        if ($dif > 0) {
                            $c += $dif;
                            $i = $min;
                        }
                    }

                    // We've not reached our length yet but we have JUST reached a usable from. Set real_from  (by doing it here we make sure we don't crop from inside an entity or tag)
                    if ((($literal_pos ? $i : $c) >= $from) && ($from != 0)) {
                        $real_from = $i;
                    }
                }

                $c++;
            }
        }

        // Check if the counter has reached the minimum length yet, then wait for the tag_counter to become 0, and chop the string there
        $ord = ord($current_char);
        if (
            (!$in_tag) &&
            (!$in_entity) &&
            (!(($ord >= 192) && ($ord <= 223))) &&
            (($literal_pos ? $i : $c) >= $end_pos) &&
            (
                ($grammar_completeness_tolerance == 0.0) ||
                (!_smart_grammar_says_continue($end_pos, $grammar_completeness_tolerance, $i, $html, $length))
            ) &&
            (!in_array(true, $unbreakable_tag_stack)) // We want to allow certain tags to finish
            ||
            ($i == $total_length_minus_one)
        ) {
            $entity = '&hellip;';
            if (function_exists('ocp_mark_as_escaped')) {
                ocp_mark_as_escaped($entity);
            }

            if (($real_from == 0) && ($from != 0)) { // We never found text start even after exceeding length so we'll have to rewind and just start from the XHTML start
                $html_buildup = array();
            }

            $new_html = '';

            if ($ellipses) {
                $start_ellipses = (($from > 0) ? $entity : '');
            } else {
                $start_ellipses = '';
            }
            if ($ellipses) {
                $end_ellipses = ((!is_null($length)) && (($end_pos < $total_length - 1)) ? $entity : '');
            } else {
                $end_ellipses = '';
            }
            $no_text_inside = array('tr', 'thead', 'colgroup', 'table', 'ul', 'ol', 'dl', 'dir', 'menu', 'applet', 'param', 'embed', 'object', 'legend', 'select', 'tfoot', 'ruby',);

            if (in_array(array_peek($tag_stack), $no_text_inside)) {
                $new_html = rtrim($new_html) . $start_ellipses;
                $start_ellipses = '';
            }

            foreach ($html_buildup as $details) {
                $new_html .= $details[1];
            }

            $new_html = rtrim($new_html) . $start_ellipses; // NB: $_html_buildup is not used, as that's for HTML stack analysis, not for final output

            $main_portion = substr($html, $real_from, $i - $real_from + 1); // +1 because offsets are one less than lengths
            $new_html .= $main_portion;

            if (!in_array(array_peek($tag_stack), $no_text_inside)) {
                $new_html = rtrim($new_html) . $end_ellipses;
                $end_ellipses = '';
            }

            foreach (array_reverse($tag_stack) as $tag) {
                $new_html .= '</' . $tag . '>';
            }

            // Shall we back-track a bit to stop cutting up a word?
            $backtrack = strrpos($new_html, ' ');
            $backtrack_safety = strrpos($new_html, '>');
            if (($current_char != ' ') && ($grammar_completeness_tolerance != 0.0) && ($backtrack !== false) && (floatval(strlen($new_html) - $backtrack) < $grammar_completeness_tolerance * strlen($new_html)) && (($backtrack_safety === false) || ($backtrack > $backtrack_safety))) {
                $new_html = substr($new_html, 0, $backtrack);
            }

            $new_html = rtrim($new_html) . $end_ellipses;

            $new_html = preg_replace('#<!--.*(-->|$)#Us', '', $new_html); // Our algorithm doesn't handle comments so we need to be slightly clever about it
            $new_html = preg_replace('#<script.*</script>#Us', '', $new_html); // ... and also script/CDATA stuff, due to problems in XHTML/HTML incompatibility bypass techniques that use character data (which we skip)

            // Remove some empty tags that shouldn't be empty (e.g. td)
            $may_not_be_empty = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'blockquote', 'pre', 'br', 'hr', 'fieldset', 'address', 'noscript', 'table', 'tbody',
                                      'tfoot', 'thead', 'tr', 'dd', 'dt', 'dl', 'li', 'ol', 'ul', 'rbc', 'rtc', 'rb', 'rt', 'rp', 'span', 'abbr',
                                      'acronym', 'cite', 'code', 'dfn', 'em', 'strong', 'kbd', 'q', 'samp', 'var', 'sub', 'sup', 'tt', 'del', 'ruby', 'bdo',
                                      'img', 'ins', 'param', 'input', 'select', 'object', 'caption', 'label', 'b', 'i', 'small', 'big', 'base', 'body', 'col', 'colgroup', 'map',
                                      'optgroup', 'legend', 'area', 'form',
            );
            foreach ($may_not_be_empty as $t) {
                $new_html = preg_replace('#<' . $t . '>\s*</' . $t . '>#', '', $new_html);
            }

            if ($ellipses) {
                $new_html = str_replace(array('</p>' . $entity, '</div>' . $entity), array($entity . '</p>', $entity . '</div>'), $new_html);
            }

            return $new_html;
        }
    }
    return $html; // Should never get here
}

/**
 * XHTML-aware helper function to try and get to a nice grammatical point to call our substring termius.
 *
 * @param  integer $nieve_end_pos The nieve position where we'd end on.
 * @param  float $grammar_completeness_tolerance A fraction of the length we may exceed by in order to maintain grammar tolerance (finish sentences, finish paragraphs, finish the whole thing).
 * @param  integer $real_offset Current offset in the text.
 * @param  string $html The text to perform on.
 * @param  integer $desired_length Desired (nieve) substring length.
 * @return boolean Whether to keep continuing.
 *
 * @ignore
 */
function _smart_grammar_says_futile($nieve_end_pos, $grammar_completeness_tolerance, $real_offset, $html, $desired_length)
{
    // See if we're starting a paragraph
    $look_out = array(
        'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'blockquote', 'pre', 'br', 'hr', 'fieldset', 'iframe', 'table',
        'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'dd', 'dt', 'dl', 'li', 'ol', 'ul',
    );
    $starting = false;
    foreach ($look_out as $l) {
        if (substr($html, $real_offset, strlen($l) + 1) == '<' . $l) {
            $starting = true;
            break;
        }
    }
    if (!$starting) {
        return false;
    }

    // Find length of paragraph
    $best_pos = mixed();
    foreach ($look_out as $l) {
        $pos = strpos($html, '<' . $l, $real_offset + 1);
        if ($pos !== false) {
            if ((is_null($best_pos)) || ($best_pos > $pos)) {
                $best_pos = $pos;
            }
        }
        $pos = strpos($html, '</' . $l, $real_offset + 1);
        if ($pos !== false) {
            if ((is_null($best_pos)) || ($best_pos > $pos)) {
                $best_pos = $pos;
            }
        }
    }
    $length_of_paragraph = (is_null($best_pos) ? strlen($html) : $best_pos) - $real_offset;

    // Work it out, based on looking-ahead into this paragraph. If we can't get through 0.3 (hard-coded) then it's considered a futile paragraph.
    $jump_ahead = intval(round(0.3 * floatval($length_of_paragraph)));
    $check = _smart_grammar_says_continue($nieve_end_pos, $grammar_completeness_tolerance, $real_offset + $jump_ahead, $html, $desired_length, true);
    return !$check;
}

/**
 * XHTML-aware helper function to try and get to a nice grammatical point to call our substring termius.
 *
 * @param  integer $nieve_end_pos The nieve position where we'd end on.
 * @param  float $grammar_completeness_tolerance A fraction of the length we may exceed by in order to maintain grammar tolerance (finish sentences, finish paragraphs, finish the whole thing).
 * @param  integer $real_offset Current offset in the text.
 * @param  string $html The text to perform on.
 * @param  integer $desired_length Desired (nieve) substring length.
 * @param  boolean $testing_ahead Whether this is a cursory look-ahead rather than a byte-by-byte callback (therefore skip fine-grained checks which would interfere with a cursory result).
 * @return boolean Whether to keep continuing.
 *
 * @ignore
 */
function _smart_grammar_says_continue($nieve_end_pos, $grammar_completeness_tolerance, $real_offset, $html, $desired_length, $testing_ahead = false)
{
    // NOTE: This algorithm isn't perfect. Grammar is exceptionally complex and it does not do a parse as such.

    // Work out "paragraph" end (paragraph end determined by next block/table tag, our block/table tag ending, or the end of $html)
    $look_out = array( // We will assume there are no HTML tags that start with these, like dtango. There aren't, and if there are, it's very unlikely to cause a problem. We will also assume it's valid XHTML- no upper case tags.
                       'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'blockquote', 'pre', 'br', 'hr', 'fieldset', 'iframe', 'table',
                       'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'dd', 'dt', 'dl', 'li', 'ol', 'ul',
    );
    $best_pos = mixed();
    foreach ($look_out as $l) {
        $pos = strpos($html, '<' . $l, $real_offset);
        if ($pos !== false) {
            if ((is_null($best_pos)) || ($best_pos > $pos)) {
                $best_pos = $pos;
            }
        }
        $pos = strpos($html, '</' . $l, $real_offset);
        if ($pos !== false) {
            if ((is_null($best_pos)) || ($best_pos > $pos)) {
                $best_pos = $pos;
            }
        }
    }
    $para_end_pos = is_null($best_pos) ? strlen($html) : $best_pos;
    if ($para_end_pos == $real_offset + 1) {
        return false; // Just finished paragraph
    }
    // Decide, is it worth maintaining the paragraph?
    if ($para_end_pos - $nieve_end_pos <= intval(round($grammar_completeness_tolerance * $desired_length))) {
        return true;
    }

    // See if we just finished our sentence (sentence end determined by next full stop followed by tab/nl/cr, or full stop followed by space followed by capital letter or another space, or question mark, or exclamation mark, or the end of $html)
    $look_out = array('.  ', '. A', '. B', '. C', '. D', '. E', '. F', '. G', '. H', '. I', '. J', '. K', '. L', '. M', '. N', '. O', '. P', '. Q', '. R', '. S', '. T', '. U', '. V', '. W', '. X', '. Y', '. Z', '.' . "\n", '.' . "\r", ".\t", '?', '!');
    foreach ($look_out as $l) {
        if (substr($html, $real_offset - strlen($l), strlen($l)) == $l) {
            return false;
        }
    }
    // Work out the "sentence" end
    $best_pos = null;
    foreach ($look_out as $l) {
        $pos = strpos($html, $l, $real_offset);
        if ($pos !== false) {
            if ((is_null($best_pos)) || ($best_pos > $pos)) {
                $best_pos = $pos;
            }
        }
    }
    $sentence_end_pos = is_null($best_pos) ? strlen($html) : ($best_pos + 1);
    if ($sentence_end_pos == $real_offset) {
        return false; // Just finished sentence
    }
    // Decide, is it worth maintaining the sentence?
    if ($sentence_end_pos - $nieve_end_pos <= intval(round($grammar_completeness_tolerance * $desired_length))) {
        return true;
    }

    if (!$testing_ahead) {
        // Try at least to finish word (imperfect as e.g. "1.2" is a word but could be broken within)
        if (!@in_array($html[$real_offset + 1], array(false, null, '.', ' ', "\t", "\n", "\r"))) {
            return true;
        }
    }

    return false;
}

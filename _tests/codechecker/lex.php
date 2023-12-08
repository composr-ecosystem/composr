<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    code_quality
 */

// These are standalone lexer tokens: finding them doesn't affect the lexing state
// ===============================================================================

global $PTOKENS;

// Logical combinators
$PTOKENS['BOOLEAN_AND'] = '&&';
$PTOKENS['BOOLEAN_OR'] = '||';
$PTOKENS['BOOLEAN_XOR'] = 'xor';
$PTOKENS['BOOLEAN_OR_2'] = 'or'; // Used by convention for error handling
// Logical comparators
$PTOKENS['IS_EQUAL'] = '==';
$PTOKENS['IS_GREATER'] = '>';
$PTOKENS['IS_SMALLER'] = '<';
$PTOKENS['IS_GREATER_OR_EQUAL'] = '>=';
$PTOKENS['IS_SMALLER_OR_EQUAL'] = '<=';
$PTOKENS['IS_IDENTICAL'] = '===';
$PTOKENS['IS_NOT_EQUAL'] = '!=';
$PTOKENS['IS_NOT_IDENTICAL'] = '!==';
// Unary logical operators
$PTOKENS['BOOLEAN_NOT'] = '!';
// Logical commands
$PTOKENS['IF'] = 'if';
$PTOKENS['ELSE'] = 'else';
$PTOKENS['ELSEIF'] = 'elseif';
$PTOKENS['SWITCH'] = 'switch';
$PTOKENS['CASE'] = 'case';
$PTOKENS['DEFAULT'] = 'default';
// Assignment
$PTOKENS['CONCAT_EQUAL'] = '.=';
$PTOKENS['DIV_EQUAL'] = '/=';
$PTOKENS['MINUS_EQUAL'] = '-=';
$PTOKENS['MUL_EQUAL'] = '*=';
$PTOKENS['PLUS_EQUAL'] = '+=';
$PTOKENS['EQUAL'] = '=';
$PTOKENS['BOR_EQUAL'] = '|=';
// General structural
$PTOKENS['SUPPRESS_ERROR'] = '@';
$PTOKENS['COLON'] = ':';
$PTOKENS['QUESTION'] = '?';
$PTOKENS['COMMA'] = ',';
$PTOKENS['CURLY_CLOSE'] = '}';
$PTOKENS['CURLY_OPEN'] = '{';
$PTOKENS['BRACKET_OPEN'] = '(';
$PTOKENS['BRACKET_CLOSE'] = ')';
$PTOKENS['COMMAND_TERMINATE'] = ';';
$PTOKENS['EXTRACT_OPEN'] = '[';
$PTOKENS['EXTRACT_CLOSE'] = ']';
// Loops
$PTOKENS['FOREACH'] = 'foreach';
$PTOKENS['AS'] = 'as';
$PTOKENS['BREAK'] = 'break';
$PTOKENS['CONTINUE'] = 'continue';
$PTOKENS['FOR'] = 'for';
$PTOKENS['WHILE'] = 'while';
$PTOKENS['DO'] = 'do';
// Casts
$PTOKENS['INTEGER'] = 'integer';
$PTOKENS['BOOLEAN'] = 'boolean';
$PTOKENS['INT'] = 'int';
$PTOKENS['BOOL'] = 'bool';
$PTOKENS['FLOAT'] = 'float';
$PTOKENS['ARRAY'] = 'array';
$PTOKENS['OBJECT'] = 'object';
$PTOKENS['STRING'] = 'string';
// Unary operators
$PTOKENS['DEC'] = '--';
$PTOKENS['INC'] = '++';
$PTOKENS['REFERENCE'] = '&';
// Binary operators
$PTOKENS['BW_XOR'] = '^';
$PTOKENS['BW_OR'] = '|';
$PTOKENS['BW_NOT'] = '~';
$PTOKENS['SL'] = '<<';
$PTOKENS['SR'] = '>>';
$PTOKENS['CONC'] = '.';
$PTOKENS['ADD'] = '+';
$PTOKENS['SUBTRACT'] = '-';
$PTOKENS['MULTIPLY'] = '*';
$PTOKENS['DIVIDE'] = '/';
$PTOKENS['REMAINDER'] = '%';
// Classes/objects
$PTOKENS['SCOPE'] = '::';
$PTOKENS['CLASS'] = 'class';
$PTOKENS['VAR'] = 'var';
$PTOKENS['CONST'] = 'const';
$PTOKENS['EXTENDS'] = 'extends';
$PTOKENS['OBJECT_OPERATOR'] = '->';
$PTOKENS['NEW'] = 'new';
$PTOKENS['CLONE'] = 'clone';
$PTOKENS['PUBLIC'] = 'public';
$PTOKENS['PRIVATE'] = 'private';
$PTOKENS['PROTECTED'] = 'protected';
$PTOKENS['ABSTRACT'] = 'abstract';
$PTOKENS['INTERFACE'] = 'interface';
$PTOKENS['IMPLEMENTS'] = 'implements';
// Functions
$PTOKENS['FUNCTION'] = 'function';
$PTOKENS['RETURN'] = 'return';
// Arrays
$PTOKENS['DOUBLE_ARROW'] = '=>';
$PTOKENS['LIST'] = 'list';
$PTOKENS['ARRAY'] = 'array';
// Other
$PTOKENS['ECHO'] = 'echo';
$PTOKENS['GLOBAL'] = 'global';
$PTOKENS['STATIC'] = 'static';
$PTOKENS['TRY'] = 'try';
$PTOKENS['CATCH'] = 'catch';
$PTOKENS['THROW'] = 'throw';
// Simple types
$PTOKENS['true'] = 'true';
$PTOKENS['false'] = 'false';
$PTOKENS['null'] = 'null';
// None matches go to be 'IDENTIFIER'
// Also detected are: integer_literal, float_literal, string_literal, variable, comment

// Loaded lexer tokens that change the lexing state
// ================================================
$PTOKENS['DOLLAR_OPEN_CURLY_BRACES'] = '${';
$PTOKENS['START_HEREDOC_NOWDOC'] = '<<<'; // Ending it with "END;" is implicit in the PLEXER_HEREDOC state
$PTOKENS['START_ML_COMMENT'] = '/*'; // Ending it with "* /" is implicit in the PLEXER_ML_COMMENT state
$PTOKENS['COMMENT'] = '//'; // Ending it with a new-line is implicit in the PLEXER_COMMENT state
$PTOKENS['VARIABLE'] = '$'; // Ending it with a non-variable-character is implicit in the PLEXER_VARIABLE state
$PTOKENS['DOUBLE_QUOTE'] = '"'; // Ending it with non-escaped " is implicit in PLEXER_DOUBLE_QUOTE_STRING_LITERAL state (as well as extended escaping)
$PTOKENS['SINGLE_QUOTE'] = '\''; // Ending it with non-escaped ' is implicit in PLEXER_SINGLE_QUOTE_STRING_LITERAL state

// Lexer states
define('PLEXER_FREE', 1); // (grabs implicitly)
define('PLEXER_VARIABLE', 2); // grab variable
define('PLEXER_HEREDOC', 3); // grab string_literal
define('PLEXER_NOWDOC', 4); // grab string_literal
define('PLEXER_ML_COMMENT', 5); // grab comment
define('PLEXER_COMMENT', 6); // grab comment
define('PLEXER_DOUBLE_QUOTE_STRING_LITERAL', 7); // grab string_literal
define('PLEXER_SINGLE_QUOTE_STRING_LITERAL', 8); // grab string_literal
define('PLEXER_NUMERIC_LITERAL', 9); // grab float_literal/integer_literal (supports decimal, octal, hexadecimal)
define('PLEXER_EMBEDDED_VARIABLE', 10); // grab variable (and return to previous state)

// These are characters that can be used to continue an identifier lexer token (any other character starts a new token).
global $PCONTINUATIONS;
$PCONTINUATIONS = array(
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
    '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '_');
global $PCONTINUATIONS_SIMPLE;
$PCONTINUATIONS_SIMPLE = array(
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
    '_');
// For non-identifier tokens, tokenisation is driven purely upon "best match".

function lex($text = null)
{
    global $PCONTINUATIONS, $PCONTINUATIONS_SIMPLE, $PTOKENS, $TEXT;

    ini_set('pcre.backtrack_limit', '10000000');

    if (!is_null($text)) {
        $TEXT = $text;
    }

    $TEXT = preg_replace('#declare\(\w+=\w+\);#', '', $TEXT); // HACKHACK: We do not support parsing declare directives

    // Composr doesn't make use of this, so no need to understand it
    $matches = array();
    if (strpos($TEXT, '<' . '?php') === false) {
        if (strpos($TEXT, '<' . '?') !== false) {
            if (strpos($TEXT, '?' . '>') !== false) {
                log_warning('It is best to only have one PHP code block and not to terminate it. This stops problems with white-space at the end of files.');
            } else {
                $TEXT .= '?' . '>' . ((substr($TEXT, -1) == "\n") ? "\n" : '');
            }

            log_warning('Use "<' . '?php" tagging for compatibility.');
            $num_matches = preg_match_all('#<\\?(.*)\\?' . '>#sU', $TEXT, $matches, PREG_OFFSET_CAPTURE);
        } elseif (strpos($TEXT, '<' . '%') !== false) {
            if (strpos($TEXT, '%' . '>') !== false) {
                log_warning('It is best to only have one PHP code block and not to terminate it. This stops problems with white-space at the end of files.');
            } else {
                $TEXT .= '%' . '>' . ((substr($TEXT, -1) == "\n") ? "\n" : '');
            }

            log_warning('Use "<' . '?php" tagging for compatibility.');
            $num_matches = preg_match_all('#<%(.*)\\%>#sU', $TEXT, $matches, PREG_OFFSET_CAPTURE);
        } else {
            $num_matches = 0;
        }
    } else {
        if (strpos($TEXT, '?' . '>') !== false) {
            log_warning('It is best to only have one PHP code block and not to terminate it. This stops problems with white-space at the end of files.');
        } else {
            $TEXT .= '?' . '>' . ((substr($TEXT, -1) == "\n") ? "\n" : '');
        }

        $num_matches = preg_match_all('#<\\?php(.*)\\?' . '>#sU', $TEXT, $matches, PREG_OFFSET_CAPTURE);
    }
    $new_text = '';
    global $BETWEEN_ALL;
    $BETWEEN_ALL = '';
    $extra_skipped = 0;
    $last_m = null;
    for ($i = 0; $i < $num_matches; $i++) {
        $m = $matches[1][$i];
        if (is_string($m)) {
            continue;
        } else {
            $between = substr($TEXT, strlen($new_text) + $extra_skipped, $m[1] - 5 - strlen($new_text) - $extra_skipped);
            $extra_skipped += 7;
            $BETWEEN_ALL .= $between;
            $new_text .= preg_replace('#[^\n]#s', ' ', $between);
            $new_text .= $m[0];
            $last_m = $m;
        }
    }
    if (!is_null($last_m)) {
        $between = substr($TEXT, $last_m[1] + strlen($last_m[0]) + 2);
        $BETWEEN_ALL .= $between;
        $new_text .= preg_replace('#[^\n]#', ' ', $between);
    }
    if ($num_matches == 0) {
        $BETWEEN_ALL = $TEXT;
    }
    $TEXT = $new_text;
    if ((trim($BETWEEN_ALL) != '') && (isset($GLOBALS['FILENAME']))) {
        global $WITHIN_PHP;
        $WITHIN_PHP = true;
        require('xhtml.php');
    }

    // So that we don't have to consider end-of-file states as much.
    if (substr($TEXT, -1) != "\n") {
        log_warning('Files are supposed to end with a blank line according to PSR-2', $i, true);
        $TEXT .= "\n";
    }

    $tokens = array(); // We will be lexing into this list of tokens

    $special_token_value = ''; // This will be used during special lexing modes to build up the special token value being lexed
    $special_token_value_2 = '';
    $previous_state = null;

    $lex_state = PLEXER_FREE;
    $escape_flag = false; // Used for string_literal escaping
    $heredoc_simple = false;
    $heredoc_buildup = array();
    $heredoc_nowdoc_symbol = '';

    $tokens_since_comment = 0;

    $indentation = 0;
    $new_line = false;
    $brace_stack = array();

    // Lex the code. Hard coded state changes occur. Understanding of tokenisation implicit. Trying to match tokens to $PTOKENS, otherwise an identifier.
    $char = '';
    $i = 0;
    $len = strlen($TEXT);
    while (true) {
        switch ($lex_state) {
            case PLEXER_FREE:
                // Jump over any white space in our way
                do {
                    $previous_char = $char;
                    list($reached_end, $i, $char) = plex__get_next_char($i);
                    if ($reached_end) {
                        break 3;
                    }

                    $has_tab = false;
                    if ($new_line) {
                        if ($char == ' ') {
                            $indentation++;
                        } elseif ($char == "\t") {
                            $has_tab = true;
                            $indentation += 4;
                        }
                    }
                    if ($char == "\n") {
                        $indentation = 0;
                        $new_line = true;
                    }
                } while (trim($char) == '');
                if ($has_tab) {
                    log_warning('PSR-2 says to use soft tabs, not hard tabs', $i, true);
                }
                if ((trim($previous_char) == '')) {
                    if ($char == '{') {
                        $line = substr($TEXT, 0, $i);
                        if ($new_line) {
                            $t_pos = strrpos($line, "\n");
                            $t_pos = strrpos(substr($line, 0, $t_pos), "\n");
                            $line = substr($line, $t_pos);
                        } else {
                            $line = substr($line, strrpos($line, "\n"));
                        }
                        if (preg_replace('#\s#', '', $line) == '){') {
                            $should_not_be_on_same_line = (preg_match('#^\s*(function|class|public|private|protected|static|abstract|interface) #', $line) != 0);
                            if ($should_not_be_on_same_line != $new_line) {
                                log_warning('Bracing error (opening brace on wrong line) ', $i, true);
                            }
                        }
                        if ($indentation % 4 == 0 || strpos($line, '=>') === false) {
                            array_push($brace_stack, $indentation);
                        } else {
                            array_push($brace_stack, end($brace_stack) + 4); // Has array structure indenting, messing with brace offsets, so calculate via other method
                        }
                    } elseif ($char == '}') {
                        if (!$new_line) {
                            log_warning('Bracing error (closing brace not on new line) ', $i, true);
                        }
                        $past_indentation = array_pop($brace_stack);
                        if ($past_indentation != $indentation) {
                            log_warning('Bracing error (' . $past_indentation . ' vs ' . strval($indentation) . ')', $i, true);
                        }
                    }
                }
                $new_line = false;

                // We need to know where our token is starting
                $i--;
                $i_current = $i;

                // Try and work out what token we're looking at next
                $maybe_applicable_tokens = $PTOKENS;
                $applicable_tokens = array();
                $token_so_far = '';
                while (count($maybe_applicable_tokens) != 0) {
                    list($reached_end, $i, $char) = plex__get_next_char($i);
                    if ($reached_end) {
                        break 3;
                    }

                    $token_so_far .= $char;

                    // Filter out any tokens that no longer match
                    foreach ($maybe_applicable_tokens as $token_name => $token_value) {
                        // Hasn't matched (or otherwise, may still match)
                        if (substr($token_value, 0, strlen($token_so_far)) != $token_so_far) {
                            unset($maybe_applicable_tokens[$token_name]);
                        } else {
                            // Is it a perfect match?
                            if ((strlen($token_so_far) == strlen($token_value)) && ((!in_array($token_so_far[0], $PCONTINUATIONS)) || (!in_array($TEXT[$i], $PCONTINUATIONS)))) {
                                $applicable_tokens[] = $token_name;
                                unset($maybe_applicable_tokens[$token_name]);
                            }
                        }
                    }
                }

                // Special case, don't allow tokens in object dereferencing chains
                $_last_token = end($tokens);
                if ($_last_token !== false) {
                    if ($_last_token[0] == 'OBJECT_OPERATOR') {
                        $applicable_tokens = array();
                    }
                }

                // If we have any applicable tokens, find the longest and move $i so it's as we just read it
                $i = $i_current;
                if (count($applicable_tokens) != 0) {
                    usort($applicable_tokens, 'plex__strlen_sort');
                    $token_found = $applicable_tokens[count($applicable_tokens) - 1];

                    $i += strlen($PTOKENS[$token_found]);

                    // Is it a special state jumping token?
                    if ($token_found == 'VARIABLE') {
                        $lex_state = PLEXER_VARIABLE;
                        break;
                    } elseif ($token_found == 'START_HEREDOC_NOWDOC') {
                        $matches = array();
                        if (preg_match('#\'([A-Za-z0-9\_]+)\'#A', $TEXT, $matches, 0, $i) != 0) {
                            $lex_state = PLEXER_NOWDOC;
                            $heredoc_nowdoc_symbol = $matches[1];
                        } else {
                            preg_match('#([A-Za-z0-9\_]*)#A', $TEXT, $matches, 0, $i);
                            $lex_state = PLEXER_HEREDOC;
                            $heredoc_nowdoc_symbol = $matches[1];
                        }
                        $i += strlen($heredoc_nowdoc_symbol);
                        break;
                    } elseif ($token_found == 'START_ML_COMMENT') {
                        $lex_state = PLEXER_ML_COMMENT;
                        break;
                    } elseif ($token_found == 'COMMENT') {
                        $lex_state = PLEXER_COMMENT;
                        break;
                    } elseif ($token_found == 'DOUBLE_QUOTE') {
                        $lex_state = PLEXER_DOUBLE_QUOTE_STRING_LITERAL;
                        break;
                    } elseif ($token_found == 'SINGLE_QUOTE') {
                        $lex_state = PLEXER_SINGLE_QUOTE_STRING_LITERAL;
                        break;
                    } else {
                        if (!in_array($token_found, array('COMMA', 'DOUBLE_ARROW'))) { // We don't count array definitions, etc
                            $tokens_since_comment++;
                            if ((isset($GLOBALS['pedantic'])) && ($tokens_since_comment > 200)) {
                                log_warning('Bad comment density', $i, true);
                                $tokens_since_comment = 0;
                            }
                        }
                    }

                    if (($token_found == 'IF') && (@$tokens[count($tokens) - 1][0] == 'ELSE')) {
                        log_warning('Use \'elseif\' not \'else if\'', $i, true);
                    }

                    if (($token_found == 'CURLY_OPEN') && (isset($tokens[0]))) {
                        if ($tokens[count($tokens) - 1][0] == 'OBJECT_OPERATOR') {
                            list($reached_end, $i, $char) = plex__get_next_char($i);

                            if ($char == '\'') {
                                $token_found = '';
                                do {
                                    list($reached_end, $i, $char) = plex__get_next_char($i);
                                    if ($char != '\'') {
                                        $token_found .= $char;
                                    }
                                } while (($char != '\'') && (!$reached_end));

                                list($reached_end, $i, $char) = plex__get_next_char($i);
                                if ($char != '}') {
                                    log_warning('Bad token found', $i, true);
                                    break 2;
                                }

                                $tokens[] = array('IDENTIFIER', $token_found, $i);
                                break;
                            } else {
                                $i--;
                            }
                        }
                    }

                    if (($i_current > 0) && (isset($TEXT[$i_current - 2])) && ($TEXT[$i_current - 1] == ' ') && ($TEXT[$i_current - 2] != ' ') && (in_array($token_found, array('OBJECT_OPERATOR')))) {
                        log_warning('Superfluous spacing (for ' . $token_found . ') against coding standards', $i, true);
                    }
                    if (($i_current > 0) && (($TEXT[$i] != ' ') && ($TEXT[$i] != "\n") && ($TEXT[$i] != ')') && ($TEXT[$i] != "/") && ($TEXT[$i] != "\r")) && (in_array($token_found, array('COMMA', 'COMMAND_TERMINATE')))) {
                        log_warning('Missing surrounding spacing (for ' . $token_found . ') against coding standards', $i, true);
                    }
                    if (($i_current > 0) && (($TEXT[$i_current - 1] != ' ') || (($TEXT[$i] != ' ') && ($TEXT[$i] != "\n") && ($TEXT[$i] != "\r"))) && (in_array($token_found, array('IS_EQUAL', 'IS_GREATER', 'IS_SMALLER', 'IS_GREATER_OR_EQUAL', 'IS_SMALLER_OR_EQUAL', 'IS_IDENTICAL', 'IS_NOT_EQUAL', 'IS_NOT_IDENTICAL', 'CONCAT_EQUAL', 'DIV_EQUAL', 'MINUS_EQUAL', 'MUL_EQUAL', 'PLUS_EQUAL', 'BOR_EQUAL', 'EQUAL', 'BW_XOR', 'BW_OR', 'SL', 'SR', 'CONC', 'ADD', 'SUBTRACT', 'MULTIPLY', 'DIVIDE', 'REMAINDER')))) {
                        if ($token_found != 'SUBTRACT' || is_alphanumeric($TEXT[$i_current - 1]) /* As could be minus sign */) {
                            log_warning('Missing surrounding spacing (for ' . $token_found . ') against coding standards', $i, true);
                        }
                    }
                    if (in_array($token_found, array('IF', 'ELSE', 'ELSEIF', 'FOREACH', 'FOR', 'FOREACH', 'WHILE', 'DO', 'TRY', 'CATCH', 'SWITCH', 'INTERFACE', 'CLASS', 'FUNCTION'))) {
                        $line_end = strpos($TEXT, "\n", $i);
                        if ($line_end !== false) {
                            $remaining_line = str_replace("\r", '', substr($TEXT, $i, $line_end - $i + 1));

                            $next_line_end = strpos($TEXT, "\n", $line_end + 1);
                            $next_line = ($next_line_end === false) ? '' : substr($TEXT, $line_end + 1, $next_line_end - $line_end - 1 + 1);

                            if ((strpos($remaining_line, ' {') === false) && (strpos($remaining_line, '/*') === false) && (($token_found != 'WHILE') || (substr($remaining_line, -2) != ";\n")) && (strpos($next_line, '{') !== false/*brace should move to own line for multi-line boolean checks*/) && (in_array($token_found, array('IF', 'ELSE', 'ELSEIF', 'FOREACH', 'FOR', 'FOREACH', 'WHILE', 'DO', 'TRY', 'CATCH', 'SWITCH')))) {
                                log_warning('Incorrect bracing spacing (for ' . $token_found . ') against coding standards', $i, true);
                            }
                            if ((strpos($remaining_line, ' {') !== false) && (strpos($next_line, '{') === false/*To weed out edge cases like when a parameter default contains ' {'*/) && (in_array($token_found, array('INTERFACE', 'CLASS', 'FUNCTION')))) {
                                log_warning('Incorrect bracing spacing (for ' . $token_found . ') against coding standards', $i, true);
                            }
                        }
                    }
                    if (($i_current > 0) && (($TEXT[$i_current - 1] != ' ') || (($TEXT[$i] != ' ') && ($TEXT[$i] != "\n") && ($TEXT[$i] != "\r"))) && (in_array($token_found, array('BOOLEAN_AND', 'BOOLEAN_XOR', 'BOOLEAN_OR', 'BOOLEAN_OR_2')))) {
                        log_warning('Missing surrounding spacing (for ' . $token_found . ') against coding standards', $i, true);
                    }

                    $tokens[] = array($token_found, $i);
                } else {
                    // Otherwise, we've found an identifier or numerical literal token, so extract it
                    $token_found = '';
                    $numeric = null;
                    do {
                        list($reached_end, $i, $char) = plex__get_next_char($i);
                        if ($reached_end) {
                            break 3;
                        }
                        if (is_null($numeric)) {
                            $numeric = in_array($char, array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9'));
                        }

                        if ((!in_array($char, $PCONTINUATIONS)) && (($numeric === false) || ($char != '.') || (!is_numeric($TEXT[$i])))) {
                            break;
                        }

                        $token_found .= $char;
                    } while (true);
                    $i--;

                    if ($numeric) {
                        if (strpos($token_found, '.') !== false) {
                            $tokens[] = array('float_literal', floatval($token_found), $i);
                        } elseif (strpos($token_found, 'x') !== false) {
                            $tokens[] = array('integer_literal', intval($token_found, 16), $i);
                        } elseif ($token_found[0] == '0') {
                            $tokens[] = array('integer_literal', intval($token_found, 8), $i);
                        } else {
                            $tokens[] = array('integer_literal', intval($token_found), $i);
                        }
                    } else {
                        if ($token_found == 'NULL' || $token_found == 'TRUE' || $token_found == 'FALSE') {
                            log_warning('Use lower-case for null/false/true', $i, true);
                        }

                        if ($token_found == '') {
                            log_warning('Bad token found', $i, true);
                            break 2;
                        }

                        $tokens[] = array('IDENTIFIER', $token_found, $i);
                    }
                }

                //print_r($tokens[count($tokens)-1]);
                //echo '<br />';
                //flush();

                break;

            case PLEXER_VARIABLE:
                list($reached_end, $i, $char) = plex__get_next_char($i);
                if ($reached_end) {
                    break 2;
                }

                // Exit case
                if (!in_array($char, $PCONTINUATIONS)) {
                    $lex_state = PLEXER_FREE;
                    $tokens[] = array('variable', $special_token_value, $i);
                    $special_token_value = '';
                    $i--;
                    break;
                }

                // Normal case
                $special_token_value .= $char;

                break;

            case PLEXER_NOWDOC:
            case PLEXER_HEREDOC:
                list($reached_end, $i, $char) = plex__get_next_chars($i, strlen($heredoc_nowdoc_symbol) + 2);

                // Exit case
                if ($char == "\n" . $heredoc_nowdoc_symbol . ';') {
                    $lex_state = PLEXER_FREE;
                    if ((isset($GLOBALS['CHECKS'])) && (preg_match('#\<[^\<\>]*\>#', $special_token_value) != 0)) {
                        log_warning('It looks like HTML used outside of templates', $i, true);
                    }
                    $tokens[] = array('string_literal', $special_token_value, $i);
                    $tokens[] = array('COMMAND_TERMINATE', $i);
                    if ((isset($GLOBALS['CHECKS'])) && (isset($GLOBALS['PEDANTIC'])) && (strpos($special_token_value, '<') !== false) && (strpos($special_token_value, '<') != strlen($special_token_value) - 1)) {
                        log_warning('Should\'t this be templated?', $i, true);
                    }
                    $special_token_value = '';
                    break;
                }
                $i -= strlen($heredoc_nowdoc_symbol) + 1;
                if (!isset($char[0])) {
                    break 2;
                }
                $char = $char[0];

                if ($lex_state == PLEXER_HEREDOC) {
                    // Escape flag based filtering
                    $actual_char = $char;
                    if ($escape_flag) {
                        if ($char == '$') {
                            $actual_char = '$';
                        } elseif ($char == '{') {
                            $actual_char = '{';
                        } elseif ($char == '}') {
                            $actual_char = '}';
                        } else {
                            $actual_char = '\\' . $char;
                        }
                    } else {
                        $heredoc_simple = !((($char == '{') && ($TEXT[$i] == '$')) || (($char == '$') && ($TEXT[$i] == '{')));
                        if (($char == '$') || (!$heredoc_simple)) {
                            if (!$heredoc_simple) {
                                $i++;
                            }
                            $tokens[] = array('string_literal', $special_token_value, $i);
                            $tokens[] = array('CONC', $i);
                            $special_token_value = '';
                            $lex_state = PLEXER_EMBEDDED_VARIABLE;
                            $previous_state = PLEXER_HEREDOC;
                            $heredoc_buildup = array();
                            break;
                        } elseif (($char == '\\') || ($char == '{')) {
                            $actual_char = '';// Technically we should only allow "$whatever" if whatever exists, but this future proofs checked code
                        }
                    }

                    $escape_flag = ((!$escape_flag) && ($char == '\\'));
                }

                $special_token_value .= $actual_char;

                break;

            case PLEXER_EMBEDDED_VARIABLE:
                list($reached_end, $i, $char) = plex__get_next_char($i);
                if ($reached_end) {
                    break 2;
                }

                if (!in_array($char, $heredoc_simple ? $PCONTINUATIONS_SIMPLE : $PCONTINUATIONS)) {
                    $exit = false;

                    if (!$heredoc_simple) {
                        // Complex
                        if ($char == '}') {
                            $exit = true;
                        } else {
                            $matches = array();
                            if (($char == '[') && ($TEXT[$i] == '\'') && (preg_match('#\[\'([^\']*)\'\]#A', $TEXT, $matches, 0, $i - 1) != 0)) { // NOTE: Have disallowed escaping within the quotes
                                $heredoc_buildup[] = array((count($heredoc_buildup) == 0) ? 'variable' : 'IDENTIFIER', $special_token_value_2, $i);
                                $special_token_value_2 = '';
                                $heredoc_buildup[] = array('EXTRACT_OPEN', $i);
                                $heredoc_buildup[] = array('string_literal', $matches[1], $i);
                                $heredoc_buildup[] = array('EXTRACT_CLOSE', $i);
                                $i += strlen($matches[1]) + 3;
                            } elseif (($char == '[') && (preg_match('#\[([A-Za-z0-9\_]+)\]#A', $TEXT, $matches, 0, $i - 1) != 0)) {
                                $heredoc_buildup[] = array((count($heredoc_buildup) == 0) ? 'variable' : 'IDENTIFIER', $special_token_value_2, $i);
                                $special_token_value_2 = '';
                                $heredoc_buildup[] = array('EXTRACT_OPEN', $i);
                                $heredoc_buildup[] = array('IDENTIFIER', $matches[1], $i);
                                $heredoc_buildup[] = array('EXTRACT_CLOSE', $i);
                                $i += strlen($matches[1]) + 1;
                            } elseif (($char == '-') && ($TEXT[$i] == '>')) {
                                $heredoc_buildup[] = array((count($heredoc_buildup) == 0) ? 'variable' : 'IDENTIFIER', $special_token_value_2, $i);
                                $special_token_value_2 = '';
                                $heredoc_buildup[] = array('OBJECT_OPERATOR', $i);
                                $i++;
                            } else {
                                log_warning('Bad token found', $i, true);
                                break 2;
                            }
                        }
                    } else {
                        // Simple
                        $matches = array();
                        if (($char == '-') && ($TEXT[$i] == '>')) {
                            $heredoc_buildup[] = array((count($heredoc_buildup) == 0) ? 'variable' : 'IDENTIFIER', $special_token_value_2, $i);
                            $special_token_value_2 = '';
                            $heredoc_buildup[] = array('OBJECT_OPERATOR', $i);
                            $i++;
                        } elseif (($char == '[') && (preg_match('#\[([\'A-Za-z0-9\_]+)\]#A', $TEXT, $matches, 0, $i - 1) != 0)) {
                            if (strpos($matches[1], "'") !== false) {
                                log_warning('Do not use quotes with the simple variable embedding syntax', $i, true);
                                break 2;
                            }
                            $heredoc_buildup[] = array((count($heredoc_buildup) == 0) ? 'variable' : 'IDENTIFIER', $special_token_value_2, $i);
                            $special_token_value_2 = '';
                            $heredoc_buildup[] = array('EXTRACT_OPEN', $i);
                            $heredoc_buildup[] = array('string_literal', $matches[1], $i);
                            $heredoc_buildup[] = array('EXTRACT_CLOSE', $i);
                            $i += strlen($matches[1]) + 1;
                        } else {
                            $exit = true;
                        }
                    }

                    if ($exit) {
                        $lex_state = $previous_state;
                        if ($special_token_value_2 != '') {
                            $heredoc_buildup[] = array((count($heredoc_buildup) == 0) ? 'variable' : 'IDENTIFIER', $special_token_value_2, $i);
                        }
                        if (count($heredoc_buildup) > 0) {
                            $tokens[] = array('IDENTIFIER', 'strval', $i);
                            $tokens[] = array('BRACKET_OPEN', $i);
                            $tokens = array_merge($tokens, $heredoc_buildup);
                            $tokens[] = array('BRACKET_CLOSE', $i);
                            $tokens[] = array('CONC', $i);
                        }
                        $special_token_value_2 = '';

                        if ($heredoc_simple) {
                            $i--;
                        }
                        break;
                    }
                } else {
                    // Normal case
                    $special_token_value_2 .= $char;
                }

                break;

            case PLEXER_COMMENT:
                $tokens_since_comment = 0;

                list($reached_end, $i, $char) = plex__get_next_char($i);
                if ($reached_end) {
                    break 2;
                }

                // Exit case
                if ($char == "\n") {
                    $lex_state = PLEXER_FREE;
                    $tokens[] = array('comment', $special_token_value, $i);
                    $special_token_value = '';
                    $i--;
                    break;
                }

                // Normal case
                $special_token_value .= $char;

                break;

            case PLEXER_ML_COMMENT:
                $tokens_since_comment = 0;

                list($reached_end, $i, $char) = plex__get_next_chars($i, 2);

                // Exit case
                if ($char == '*/') {
                    $lex_state = PLEXER_FREE;
                    $tokens[] = array('comment', $special_token_value, $i);
                    $special_token_value = '';
                    break;
                }

                $i -= 1;
                if (!isset($char[0])) {
                    break 2;
                }
                $char = $char[0];

                // Normal case
                $special_token_value .= $char;

                break;

            case PLEXER_DOUBLE_QUOTE_STRING_LITERAL:
                list($reached_end, $i, $char) = plex__get_next_char($i);
                if ($reached_end) {
                    break 2;
                }

                // Exit case
                if (($char == '"') && (!$escape_flag)) {
                    $lex_state = PLEXER_FREE;
                    $tokens[] = array('string_literal', $special_token_value, $i);
                    if ((isset($GLOBALS['CHECKS'])) && (isset($GLOBALS['PEDANTIC'])) && (strpos($special_token_value, '<') !== false) && (strpos($special_token_value, '<') != strlen($special_token_value) - 1)) {
                        log_warning('Should\'t this be templated?', $i, true);
                    }
                    $special_token_value = '';

                    break;
                }

                // Escape flag based filtering
                $actual_char = $char;
                if ($escape_flag) {
                    if ($char == 'n') {
                        $actual_char = "\n";
                    } elseif ($char == 'r') {
                        $actual_char = "\r";
                    } elseif ($char == 't') {
                        $actual_char = "\t";
                    }
                } else {
                    $heredoc_simple = !((($char == '{') && ($TEXT[$i] == '$')) || (($char == '$') && ($TEXT[$i] == '{')));
                    if (($char == '$') || (!$heredoc_simple)) {
                        if (!$heredoc_simple) {
                            $i++;
                        }
                        $tokens[] = array('string_literal', $special_token_value, $i);
                        $tokens[] = array('CONC', $i);
                        $special_token_value = '';
                        $lex_state = PLEXER_EMBEDDED_VARIABLE;
                        $previous_state = PLEXER_DOUBLE_QUOTE_STRING_LITERAL;
                        $heredoc_buildup = array();
                        break;
                    }
                    if ($char == '\\') {
                        $actual_char = '';
                    }
                }

                // Normal case
                $special_token_value .= $actual_char;

                $escape_flag = ((!$escape_flag) && ($char == '\\'));

                break;

            case PLEXER_SINGLE_QUOTE_STRING_LITERAL:
                list($reached_end, $i, $char) = plex__get_next_char($i);
                if ($reached_end) {
                    break 2;
                }

                // Exit case
                if (($char == "'") && (!$escape_flag)) {
                    $lex_state = PLEXER_FREE;
                    $tokens[] = array('string_literal', $special_token_value, $i);
                    if ((isset($GLOBALS['CHECKS'])) && (isset($GLOBALS['PEDANTIC'])) && (strpos($special_token_value, '<') !== false) && (strpos($special_token_value, '<') != strlen($special_token_value) - 1)) {
                        log_warning('Shouldn\'t this be templated?', $i, true);
                    }
                    $special_token_value = '';
                    break;
                }

                // Escape flag based filtering
                $actual_char = $char;
                if ($escape_flag) {
                    if ($char == "'") {
                        $actual_char = "'";
                    } elseif ($char == '\\') {
                        $actual_char = '\\';
                    } else {
                        $actual_char = '\\' . $char;
                    }
                } elseif ($char == '\\') {
                    $actual_char = '';
                }

                // Normal case
                $special_token_value .= $actual_char;

                $escape_flag = ((!$escape_flag) && ($char == '\\'));

                break;
        }
    }

    return $tokens;
}

/**
 * Helper function for usort to sort a list by string length.
 *
 * @param  string $a The first string to compare
 * @param  string $b The second string to compare
 * @return boolean The comparison result
 */
function plex__strlen_sort($a, $b)
{
    global $PTOKENS;
    $a = $PTOKENS[$a];
    $b = $PTOKENS[$b];
    if ($a == $b) {
        return 0;
    }
    return (strlen($a) < strlen($b)) ? -1 : 1;
}

function plex__get_next_char($i)
{
    global $TEXT;
    if ($i >= strlen($TEXT)) {
        return array(true, $i + 1, '');
    }
    $char = $TEXT[$i];
    return array(false, $i + 1, $char);
}

function plex__get_next_chars($i, $num)
{
    global $TEXT;
    $str = substr($TEXT, $i, $num);
    return array(strlen($str) < $num, $i + $num, $str);
}

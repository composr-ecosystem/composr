<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/*
Here is the code used to extract PHPDoc style function comments  from PHP code.
*/

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__php()
{
    global $LANG_TD_MAP;
    $LANG_TD_MAP = null;
}

/**
 * Get a complex API information structure from a PHP file. It assumes the file has reasonably properly layed out class and function whitespace.
 * The return structure is...
 *  list of classes
 * each entry is a map containing 'functions' (list of functions) and 'name'
 *  each functions entry is a map containing 'parameters' and 'name' and 'return'
 *   each parameters entry is a map containing...
 *    name
 *    description
 *    type
 *    default
 *    default_raw
 *    set
 *    range
 *    ref
 *
 * @param  PATH $filename The PHP code module to get API information for
 * @param  boolean $include_code Whether to include function source code
 * @param  boolean $pedantic_warnings Whether to give warnings for non-consistent alignment of spacing
 * @return array The complex structure of API information
 */
function get_php_file_api($filename, $include_code = true, $pedantic_warnings = false)
{
    require_code('type_sanitisation');

    $classes = array();
    $class_has_comments = array();

    $make_alterations = false;
    if ((isset($_GET['allow_write'])) && ($_GET['allow_write'] == '1')) {
        $make_alterations = true;
    }

    // Open up PHP file
    if ($filename == 'phpstub.php') {
        $full_path = $filename;
    } else {
        $full_path = ((get_file_base() != '') ? (get_file_base() . '/') : '') . filter_naughty($filename);
    }
    $lines = file($full_path);
    if (!$make_alterations) {
        foreach ($lines as $i => $line) {
            $lines[$i] = str_replace("\t", ' ', $line);
        }
    }

    // Go through all lines, keeping record of what current class we are looking at
    $current_class = '__global';
    $current_class_level = 0;
    $functions = array();
    global $LINE;
    for ($i = 0; array_key_exists($i, $lines); $i++) {
        $line = $lines[$i];
        $LINE = $i + 1;

        if (strpos($line, '/' . '*CQC: No API check*/') !== false) {
            return array();
        }

        // Sense class boundaries (hackerish: assumes whitespace laid out correctly)
        $ltrim = ltrim($line);
        if (substr($ltrim, 0, 6) == 'class ' || substr($ltrim, 0, 15) == 'abstract class ') {
            if (!empty($functions)) {
                $classes[$current_class] = array('functions' => $functions, 'name' => $current_class);
            }

            $ltrim2 = preg_replace('#^abstract #', '', $ltrim);

            $space_pos = strpos($ltrim2, ' ');
            $space_pos_2 = strpos($ltrim2, ' ', $space_pos + 1);
            if ($space_pos_2 === false) {
                $space_pos_2 = strpos($ltrim2, "\r", $space_pos + 1);
            }
            if ($space_pos_2 === false) {
                $space_pos_2 = strpos($ltrim2, "\n", $space_pos + 1);
            }
            $current_class = substr($ltrim2, $space_pos + 1, $space_pos_2 - $space_pos - 1);
            $current_class_level = strlen($line) - strlen($ltrim);
            $functions = array();
        } elseif (($current_class != '__global') && (substr($line, 0, $current_class_level + 1) == str_repeat(' ', $current_class_level) . '}')) {
            if (!empty($functions)) {
                $classes[$current_class] = array('functions' => $functions, 'name' => $current_class);
            }

            $current_class = '__global';
            $functions = array_key_exists('__global', $classes) ? $classes['__global']['functions'] : array();
        }

        // Detect an API class or function
        if (substr($ltrim, 0, 3) == '/**') {
            $depth = strlen($line) - strlen($ltrim);

            // Find class or function line
            for ($j = $i + 1; array_key_exists($j, $lines); $j++) {
                $line2 = $lines[$j];
                $_depth = str_repeat(' ', $depth);

                // Class
                if (substr($line2, 0, $depth + 6) == $_depth . 'class ') {
                    $class_with_comments = preg_replace('#\s.*$#s', '', substr($line2, $depth + 6));
                    $class_has_comments[$class_with_comments] = true;
                    continue 2;
                }
                if (substr($line2, 0, $depth + 15) == $_depth . 'abstract class ') {
                    $class_with_comments = preg_replace('#\s.*$#s', '', substr($line2, $depth + 15));
                    $class_has_comments[$class_with_comments] = true;
                    continue 2;
                }

                // Function
                if (substr($line2, 0, $depth + 9) == $_depth . 'function ') {
                    // Parse function line
                    $_line = substr($line2, $depth + 9);
                    list($function_name, $parameters) = _read_php_function_line($_line);
                    break;
                }

                // Method
                $matches = array();
                if (preg_match('#^' . $_depth . '((public|private|protected|static|abstract) )*function (.*)#', $line2, $matches) != 0) {
                    // Parse function line
                    $_line = $matches[3];
                    list($function_name, $parameters) = _read_php_function_line($_line);

                    if ($current_class == '__global') {
                        attach_message($function_name . ' seems to be a method outside of a class', 'warn');
                    }

                    break;
                }

                // Irrelevant line, don't let it confuse us
                if ((substr(trim($line2), 0, 3) == '/**') || ((strpos($line2, '*/') !== false) && (array_key_exists($j + 1, $lines)) && (strpos($lines[$j + 1], 'function ') === false) && (strpos($lines[$j + 1], 'class ') === false))) { // Probably just skipped past a top header
                    $i = $j - 1;
                    continue 2;
                }
            }
            if (!array_key_exists($j, $lines)) {
                continue; // No function: probably we commented it out
            }

            // Parse comment block bits
            $description = '';
            $flags = array();
            $arg_counter = -1;
            $in_return = false;
            $return = null;
            for ($i++; $i < $j - 1; $i++) {
                $ltrim = ltrim($lines[$i]);
                $ltrim = ltrim(substr($ltrim, 1)); // Remove '*'
                $ltrim = rtrim($ltrim); // Remove additional whitespace
                if ($ltrim == '') {
                    continue;
                }

                if ($ltrim[0] == '@') { // Some kind of code
                    if (substr($ltrim, 0, 6) == '@param') {
                        if ($return !== null) {
                            attach_message('Parameters should not be defined after a return value', 'inform');
                        }

                        $arg_counter++;
                        if (!array_key_exists($arg_counter, $parameters)) {
                            attach_message(do_lang_tempcode('PARAMETER_MISMATCH', escape_html($function_name)), 'warn');
                            continue 2;
                        }

                        if ($pedantic_warnings) {
                            if (preg_match('#^@param  [^\s]+ \$\w+ #s', $ltrim) == 0) {
                                attach_message('The spacing alignment for a PHPDoc parameter definition on ' . $function_name . ' was not as expected; maybe too few or too many spaces. This is a pedantic error, but we like consistent code layout.', 'inform');
                            }

                            if ((substr($ltrim, -1) == '.') && (substr_count($ltrim, '.') == 1)) {
                                attach_message('Do not need trailing full stop for parameter definitions', 'inform');
                            }
                        }

                        $parts = _cleanup_array(preg_split('/\s/', substr($ltrim, 6)));
                        if ((strpos($parts[0], '?') === false) && (array_key_exists('default', $parameters[$arg_counter])) && ($parameters[$arg_counter]['default'] === null)) {
                            attach_message(do_lang_tempcode('UNALLOWED_NULL', escape_html($parameters[$arg_counter]['name']), escape_html($function_name), array(escape_html('null'))), 'warn');
                            continue 2;
                        }
                        if ((array_key_exists('default', $parameters[$arg_counter])) && ($parameters[$arg_counter]['default'] === false) && (!in_array(preg_replace('#[^\w]#', '', $parts[0]), array('mixed', 'boolean')))) {
                            attach_message(do_lang_tempcode('UNALLOWED_NULL', escape_html($parameters[$arg_counter]['name']), escape_html($function_name), array(escape_html('false'))), 'warn');
                            continue 2;
                        }

                        $parameters[$arg_counter]['type'] = $parts[0];
                        unset($parts[0]);
                        $_description = trim(implode(' ', $parts));
                        if (substr($_description, 0, 1) != '$') {
                            if ($make_alterations) {
                                $found = false;
                                for ($k = $i + 1; $k < count($lines); $k++) {
                                    $matches = array();
                                    if (preg_match('#^\s*((public|protected|private|static|abstract) )*function \w+\((.*)\)#', $lines[$k], $matches) != 0) {
                                        $params = explode(',', $matches[3]);
                                        if (isset($params[$arg_counter])) {
                                            $_description = /*str_pad(*/preg_replace('#^\s*&?(\$\w+).*$#', '$1', $params[$arg_counter])/*, 20, ' ')*/ . ' ' . $_description;
                                            $found = true;
                                        }
                                        break;
                                    }
                                }
                                if ($found) {
                                    $lines[$i] = str_replace(trim(implode(' ', $parts)), $_description, $lines[$i]);
                                    require_code('files');
                                    cms_file_put_contents_safe($full_path, implode('', $lines), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
                                }
                            }
                        }
                        $parameters[$arg_counter]['description'] = preg_replace('#^\$\w+ #', '', $_description);
                        $parameters[$arg_counter]['phpdoc_name'] = ltrim(preg_replace('#^(\$\w+) .*#', '$1', $_description), '$');
                    } elseif (substr($ltrim, 0, 7) == '@return') {
                        if ($return !== null) {
                            attach_message('Multiple return values defined', 'inform');
                        }

                        $return = array();

                        if ($pedantic_warnings) {
                            if (preg_match('#^@return [^ ]#s', $ltrim) == 0) {
                                attach_message('The spacing alignment for a PHPDoc return definition on ' . $function_name . ' was not as expected; maybe too few or too many spaces. This is a pedantic error, but we like consistent code layout.', 'inform');
                            }

                            if ((substr($ltrim, -1) == '.') && (substr_count($ltrim, '.') == 1)) {
                                attach_message('Do not need trailing full stop for return definitions', 'inform');
                            }
                        }

                        $parts = _cleanup_array(preg_split('/\s/', substr($ltrim, 7)));
                        $return['type'] = $parts[0];
                        unset($parts[0]);
                        $return['description'] = implode(' ', $parts);

                        $in_return = true;
                    } elseif ((substr($ltrim, 0, 4) == '@set') && (substr($ltrim, 0, 5) != '@sets')) {
                        $set = ltrim(substr($ltrim, 5));
                        if ($in_return) {
                            $return['set'] = $set;
                        } else {
                            $parameters[$arg_counter]['set'] = $set;
                        }
                    } elseif (substr($ltrim, 0, 6) == '@range') {
                        $range = ltrim(substr($ltrim, 6));
                        if ($in_return) {
                            $return['range'] = $range;
                        } else {
                            $parameters[$arg_counter]['range'] = $range;
                        }
                    }
                } else { // Part of the description
                    if ($pedantic_warnings) {
                        if ((!in_array(substr($ltrim, -1), array('.', ':', '!', '?', '}', ';', ','))) && (substr($ltrim, 0, 2) != '- ') && (preg_match('#^\d+\) #', $ltrim) == 0)) {
                            attach_message('Expects trailing full stop for function description', 'inform');
                        }
                    }

                    $description .= function_exists('unixify_line_format') ? unixify_line_format($ltrim) : $ltrim;
                }
            }
            $f_a = strpos($description, '{{');
            if ($f_a !== false) {
                $f_b = strpos($description, '}}', $f_a);
                if ($f_b !== false) {
                    $_flags = substr($description, $f_a + 2, $f_b - $f_a - 2);
                    $flags = explode(' ', $_flags);
                    $description = substr($description, $f_a) . substr($description, $f_b);
                }
            }

            if (array_key_exists($arg_counter + 1, $parameters)) {
                attach_message(do_lang_tempcode('PARAMETER_MISMATCH', escape_html($function_name)), 'warn');
                continue;
            }

            // Do some checks
            foreach ($parameters as $parameter) {
                // Type check
                if (array_key_exists('default', $parameter)) {
                    $default = $parameter['default'];
                    if ($default === 'boolean-true') {
                        $default = true;
                    }
                    if ($default === 'boolean-false') {
                        $default = false;
                    }
                } else {
                    $default = null;
                }

                if ($parameters[$arg_counter]['phpdoc_name'] == '') {
                    attach_message(do_lang_tempcode('MISSING_PARAMETER_NAME', escape_html($function_name)), 'warn');
                } else {
                    if ($parameter['name'] != $parameter['phpdoc_name']) {
                        attach_message(do_lang_tempcode('NAME_MISMATCH', escape_html($parameter['name']), escape_html($parameter['phpdoc_name']), array(escape_html($function_name))), 'warn');
                    }
                }

                check_function_type($parameter['type'], $function_name, $parameter['name'], $default, array_key_exists('range', $parameter) ? $parameter['range'] : null, array_key_exists('set', $parameter) ? $parameter['set'] : null);

                // Check that null is fully specified
                if (strpos($parameter['type'], '?') !== false) {
                    if (strpos($parameter['description'], '(null: ') === false) {
                        attach_message(do_lang_tempcode('NULL_MEANING_NOT_SPECIFIED', escape_html($parameter['name']), escape_html($function_name), array(escape_html('null'))), 'warn');
                    }
                } else {
                    if (strpos($parameter['description'], '(null: ') !== false) {
                        attach_message(do_lang_tempcode('NULL_MEANING_SHOULDNT_BE_SPECIFIED', escape_html($parameter['name']), escape_html($function_name), array(escape_html('null'))), 'warn');
                    }
                }
                if (strpos($parameter['type'], '~') !== false) {
                    if (strpos($parameter['description'], '(false: ') === false) {
                        attach_message(do_lang_tempcode('NULL_MEANING_NOT_SPECIFIED', escape_html($parameter['name']), escape_html($function_name), array(escape_html('false'))), 'warn');
                    }
                } else {
                    if (strpos($parameter['description'], '(false: ') !== false) {
                        attach_message(do_lang_tempcode('NULL_MEANING_SHOULDNT_BE_SPECIFIED', escape_html($parameter['name']), escape_html($function_name), array(escape_html('false'))), 'warn');
                    }
                }
            }
            if ($return !== null) {
                $fret = $return;
                check_function_type($return['type'], $function_name, '(return)', null, array_key_exists('range', $return) ? $return['range'] : null, array_key_exists('set', $return) ? $return['set'] : null);

                // Check that null is fully specified
                if (strpos($return['type'], '?') !== false) {
                    if (strpos($return['description'], '(null: ') === false) {
                        attach_message(do_lang_tempcode('NULL_MEANING_NOT_SPECIFIED', escape_html('(return)'), escape_html($function_name), array(escape_html('null'))), 'warn');
                    }
                } else {
                    if (strpos($return['description'], '(null: ') !== false) {
                        attach_message(do_lang_tempcode('NULL_MEANING_SHOULDNT_BE_SPECIFIED', escape_html('(return)'), escape_html($function_name), array(escape_html('null'))), 'warn');
                    }
                }
                if (strpos($return['type'], '~') !== false) {
                    if (strpos($return['description'], '(false: ') === false) {
                        attach_message(do_lang_tempcode('NULL_MEANING_NOT_SPECIFIED', escape_html('(return)'), escape_html($function_name), array(escape_html('false'))), 'warn');
                    }
                } else {
                    if (strpos($return['description'], '(false: ') !== false) {
                        attach_message(do_lang_tempcode('NULL_MEANING_SHOULDNT_BE_SPECIFIED', escape_html('(return)'), escape_html($function_name), array(escape_html('false'))), 'warn');
                    }
                }
            } else {
                $fret = null;
            }

            // Now get source code
            $code = '';
            for ($k = $j; array_key_exists($k, $lines); $k++) {
                $line2 = $lines[$k];
                $code .= $line2;
                if (substr($line2, 0, $depth + 1) == str_repeat(' ', $depth) . '}') {
                    break;
                }
            }

            $function = array(
                'filename' => $filename,
                'parameters' => $parameters,
                'name' => $function_name,
                'description' => $description,
                'flags' => $flags,
            );
            if ($include_code) {
                $function['code'] = $code;
            }
            if ($fret !== null) {
                $function['return'] = $fret;
            }
            $functions[$function_name] = $function;

            $i++;
        }
    }

    // See if there are any functions with blank lines above them
    for ($i = 0; array_key_exists($i, $lines); $i++) {
        $line = ltrim($lines[$i]);
        if ((preg_match('#^((public|private|protected|static|abstract) )*function (.*)#', $line) != 0) && ((trim($lines[$i - 1]) == '') || (trim($lines[$i - 1]) == '{'))) {
            // Infer some parameters from the function line, given we have no PHPDoc
            if (substr($lines[$i], 0, 9) == 'function ') { // Only if not class level (i.e. global)
                $function_name = preg_replace('#function\s+(\w+)\s*\(.*#s', '${1}', $line);
                $parameters = array();
                $num_parameters = substr_count($line, '$');
                $num_parameters_defaulted = substr_count($line, '=');
                for ($arg_counter = 0; $arg_counter < $num_parameters; $arg_counter++) {
                    $parameters[$arg_counter]['type'] = 'mixed';
                    $parameters[$arg_counter]['description'] = '';
                    if ($arg_counter >= $num_parameters - $num_parameters_defaulted) {
                        $parameters[$arg_counter]['default'] = 'boolean-true';
                    }
                }
                $function = array('filename' => $filename, 'parameters' => $parameters, 'name' => $function_name, 'description' => '', 'flags' => array());
                if ($include_code) {
                    $function['code'] = '';
                }
                $function['return'] = array('type' => 'mixed');
                $functions[$function_name] = $function;
            }

            if (!function_exists('do_lang_tempcode')) {
                exit('Missing function comment for: ' . $line);
            }
            attach_message(do_lang_tempcode('MISSING_FUNCTION_COMMENT', rtrim($line)), 'warn');
        }
    }

    // See if there are classes with no comments
    foreach (array_keys($classes) as $class) {
        if ($class == '__global') {
            continue;
        }

        if (empty($class_has_comments[$class])) {
            if (!function_exists('do_lang_tempcode')) {
                exit('Missing class comment for: ' . $line);
            }
            attach_message(do_lang_tempcode('MISSING_CLASS_COMMENT', rtrim($class)), 'warn');

            $classes[$class]['comment'] = '';
        } else {
            $classes[$class]['comment'] = $class_has_comments[$class];
        }
    }

    if (!empty($functions)) {
        $classes[$current_class/*will be global*/] = array('functions' => $functions, 'name' => $current_class);
    }

    return $classes;
}

/**
 * Read a PHP function line and return parsed details.
 *
 * @param  string $_line The line
 * @return array A pair: (function name, parameters), where parameters is a list of maps detailing each parameter
 */
function _read_php_function_line($_line)
{
    $parse = 'function_name';
    $function_name = '';
    $parameters = array();
    $arg_default = '';
    $arg_name = '';
    $ref = false;
    $is_variadic = false;
    $in_string = null;
    $escaping = false;

    for ($k = 0; $k < strlen($_line); $k++) {
        $char = $_line[$k];

        switch ($parse) {
            case 'in_comment':
                if (($char == '*') && ($_line[$k + 1] == '/')) {
                    $parse = 'in_args';
                    $ref = false;
                    $k++;
                }
                break;

            case 'in_comment_default':
                if (($char == '*') && ($_line[$k + 1] == '/')) {
                    $parse = 'in_default';
                    $k++;
                }
                break;

            case 'in_default':
                if (($char == '/') && ($_line[$k + 1] == '*') && ($in_string === null) && (!$escaping)) {
                    $parse = 'in_comment_default';
                } elseif (($char == ',') && ($in_string === null) && (!$escaping)) {
                    $default_raw = $arg_default;
                    if ($arg_default === 'true') {
                        $default = 'boolean-true'; // hack, to stop booleans coming out of arrays as integers
                    } elseif ($arg_default === 'false') {
                        $default = 'boolean-false';
                    } else {
                        $default = @eval('return ' . $arg_default . ';'); // Could be unprocessable by php.php in standalone mode
                    }
                    $parameters[] = array('name' => $arg_name, 'default' => $default, 'default_raw' => $default_raw, 'ref' => $ref, 'is_variadic' => $is_variadic);
                    $arg_name = '';
                    $arg_default = '';
                    $parse = 'in_args';
                    $ref = false;
                } elseif (($char == ')') && ($in_string === null) && (!$escaping) && (preg_match('#^\s*array\([^\)]*$#', $arg_default) == 0)) {
                    $default_raw = $arg_default;
                    if ($arg_default === 'true') {
                        $default = 'boolean-true'; // hack, to stop booleans coming out of arrays as integers
                    } elseif ($arg_default === 'false') {
                        $default = 'boolean-false';
                    } else {
                        $default = @eval('return ' . $arg_default . ';'); // Could be unprocessable by php.php in standalone mode
                    }
                    $parameters[] = array('name' => $arg_name, 'default' => $default, 'default_raw' => $default_raw, 'ref' => $ref, 'is_variadic' => $is_variadic);
                    $parse = 'done';
                } elseif ($in_string !== null) {
                    $arg_default .= $char;
                    if ($escaping) {
                        $escaping = false;
                    } elseif ($in_string == $char) {
                        $in_string = null;
                    } elseif ($char == '\\') {
                        $escaping = true;
                    }
                } else {
                    $arg_default .= $char;
                    if (($char == '"') || ($char == "'")) {
                        $in_string = $char;
                    }
                }
                break;

            case 'in_args':
                if (($char == '.') && ($_line[$k + 1] == '.') && ($_line[$k + 2] == '.')) {
                    $k += 2;
                    $is_variadic = true;
                } elseif (($char == '/') && ($_line[$k + 1] == '*')) {
                    $parse = 'in_comment';
                } elseif (is_alphanumeric($char)) {
                    $arg_name .= $char;
                } elseif ($char == '&') {
                    $ref = true;
                } elseif ($char == ',') {
                    $parameters[] = array('name' => $arg_name, 'ref' => $ref, 'is_variadic' => $is_variadic);
                    $ref = false;
                    $arg_name = '';
                } elseif ($char == '=') {
                    $parse = 'in_default';
                    $arg_default = '';
                } elseif ($char == ')') {
                    if ($arg_name != '') {
                        $parameters[] = array('name' => $arg_name, 'ref' => $ref, 'is_variadic' => $is_variadic);
                    }
                    $parse = 'done';
                }
                break;

            case 'function_name':
                if (is_alphanumeric($char)) {
                    $function_name .= $char;
                } elseif ($char == '(') {
                    $parse = 'in_args';
                    $ref = false;
                    $arg_name = '';
                } else {
                    $parse = 'between_name_and_args';
                }
                break;

            case 'between_name_and_args':
                if ($char == '(') {
                    $parse = 'in_args';
                    $ref = false;
                    $arg_name = '';
                }
                break;

            case 'done':
                if ($char == '{') {
                    attach_message(do_lang_tempcode('UNEXPECTED_TEXT_AFTER_FUNCTION', rtrim($_line)), 'warn');
                }
                break;
        }
    }

    return array($function_name, $parameters);
}

/**
 * Remove and blank strings from the given array.
 *
 * @param  array $in List of strings
 * @return array List of strings, with blank strings removed
 */
function _cleanup_array($in)
{
    $out = array();
    foreach ($in as $bit) {
        if ($bit != '') {
            $out[] = $bit;
        }
    }
    return $out;
}

/**
 * Type-check the specified parameter (giving an error if the type checking fails) [all checks].
 *
 * @param  ID_TEXT $type The parameter type
 * @param  string $function_name The functions name (used in error message)
 * @param  string $name The parameter name (used in error message)
 * @param  ?mixed $value The parameters value (null: value actually is null)
 * @param  ?string $range The string of value range of the parameter (null: no range constraint)
 * @param  ?string $set The string of value set limitation for the parameter (null: no set constraint)
 * @param  boolean $echo Whether we just echo errors instead of exiting
 */
function check_function_type($type, $function_name, $name, $value, $range, $set, $echo = false)
{
    $valid_types = array(
        'AUTO_LINK',
        'SHORT_INTEGER',
        'UINTEGER',
        'REAL',
        'BINARY',
        'MEMBER',
        'GROUP',
        'TIME',
        'LONG_TEXT',
        'SHORT_TEXT',
        'ID_TEXT',
        'MINIID_TEXT',
        'IP',
        'LANGUAGE_NAME',
        'URLPATH',
        'PATH',
        'EMAIL',
        'string',
        'integer',
        'array',
        'list',
        'map',
        'boolean',
        'float',
        'Tempcode',
        'object',
        'resource',
        'mixed',
    );

    $_type = ltrim($type, '?~');

    if (!in_array($_type, $valid_types)) {
        attach_message(do_lang_tempcode('INVALID_PARAMETER_TYPE', escape_html($type), escape_html($function_name)), 'warn');
    }

    if ($value !== null) {
        test_fail_php_type_check($type, $function_name, $name, $value, $echo);
    }

    // Check range
    if (($range !== null) && ($value !== null)) {
        $allowed = array(
            'UINTEGER',
            'SHORT_INTEGER',
            'REAL',
            'integer',
            'float',
        );
        $allowed_string = array(
            'LONG_TEXT',
            'SHORT_TEXT',
            'ID_TEXT',
            'MINIID_TEXT',
            'string',
        );
        if ((!in_array($_type, $allowed)) && (!in_array($_type, $allowed_string)) && ($type != 'array') && ($type != 'list') && ($type != 'map')) {
            attach_message(do_lang_tempcode('BAD_RANGE_SPECIFICATION', escape_html($_type), escape_html($function_name)), 'warn');
        }

        list($min, $max) = explode(' ', $range);

        if (in_array($_type, $allowed)) {
            if ($value != '') {
                if ((($min != 'min') && ($value < intval($min))) || (($max != 'max') && ($value > intval($max)))) {
                    attach_message(do_lang_tempcode('OUT_OF_RANGE_VALUE', escape_html($name), escape_html($function_name), array(escape_html($value))), 'warn');
                }
            }
        } elseif (in_array($_type, $allowed_string)) {
            if ($value != '') {
                if ((($min != 'min') && (strlen($value) < intval($min))) || (($max != 'max') && (strlen($value) > intval($max)))) {
                    attach_message(do_lang_tempcode('OUT_OF_RANGE_VALUE', escape_html($name), escape_html($function_name), array(escape_html($value))), 'warn');
                }
            }
        } else {
            if ($value != '') {
                if ((($min != 'min') && (count($value) < intval($min))) || (($max != 'max') && (count($value) > intval($max)))) {
                    attach_message(do_lang_tempcode('OUT_OF_RANGE_VALUE', escape_html($name), escape_html($function_name), array(escape_html($value))), 'warn');
                }
            }
        }
    }

    // Check set
    if (($set !== null) && ($value !== null)) {
        $_set = array();
        $len = strlen($set);
        $in_quotes = false;
        $current = '';
        for ($i = 0; $i < $len; $i++) {
            $char = $set[$i];
            if ($in_quotes) {
                if ($char == '"') {
                    $in_quotes = false;
                } else {
                    $current .= $char;
                }
            } else {
                if ($char == '"') {
                    $in_quotes = true;
                } elseif ($char == ' ') {
                    $_set[] = $current;
                    $current = '';
                } else {
                    $current .= $char;
                }
            }
        }
        $_set[] = $current;

        if (!in_array(is_string($value) ? $value : strval($value), $_set)) {
            if ($value != '') {
                attach_message(do_lang_tempcode('OUT_OF_RANGE_VALUE', escape_html($name), escape_html($function_name), array(escape_html($value))), 'warn');
            }
        }
    }
}

/**
 * Type-check the specified parameter (giving an error if the type checking fails) [just value against type].
 *
 * @param  ID_TEXT $type The parameter type
 * @param  string $function_name The functions name (used in error message)
 * @param  string $name The parameter name (used in error message)
 * @param  mixed $value The parameters value (cannot be null)
 * @param  boolean $echo Whether we just echo errors instead of exiting
 */
function test_fail_php_type_check($type, $function_name, $name, $value, $echo = false)
{
    $null_allowed = (strpos($type, '?') !== false);
    $false_allowed = (strpos($type, '~') !== false);
    $_type = preg_replace('#[^\w]#', '', $type);

    if (($value === null) && (!$null_allowed)) {
        attach_message(do_lang_tempcode('UNALLOWED_NULL', escape_html($name), escape_html($function_name), array('null')), 'warn');
    }

    if (($value === false) && (!$false_allowed) && (!in_array($_type, array('mixed', 'boolean')))) {
        attach_message(do_lang_tempcode('UNALLOWED_NULL', escape_html($name), escape_html($function_name), array('false')), 'warn');
    }

    if ($_type == 'mixed') {
        return;
    }

    if ((is_string($value)) && (preg_match('#^[A-Z_]+$#', $value) != 0)) {
        return;
    }

    switch ($_type) {
        case 'integer':
            if ((!is_integer($value)) && ((!is_float($value)) || (strval(intval(round($value))) != strval($value)))) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'UINTEGER':
            if ((!is_integer($value)) && ((!is_float($value)) || (strval(intval(round($value))) != strval($value))) || ($value < 0)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'resource':
            if (!is_resource($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'object':
            if (!is_object($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'Tempcode':
            if ((!is_object($value)) || (!is_a($value, 'Tempcode'))) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'REAL':
        case 'float':
            if (!is_float($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'boolean':
            if (!is_bool($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'list':
            if (!is_array($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'map':
            if (!is_array($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'array':
            if (!is_array($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'string':
            if (!is_string($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'PATH':
            if (!is_string($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'EMAIL':
            if ((!is_string($value)) || ((!is_email_address($value)) && ($value != ''))) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'URLPATH':
            if ((!is_string($value)) || (strlen($value) > 127)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'LONG_TEXT':
            if (!is_string($value)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'MINIID_TEXT':
            if ((!is_string($value)) || (strlen($value) > 40)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'ID_TEXT':
            if ((!is_string($value)) || (strlen($value) > 80)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'LANGUAGE_NAME':
            global $LANG_TD_MAP;
            require_code('files');
            if ($LANG_TD_MAP === null) {
                $LANG_TD_MAP = cms_parse_ini_file_fast(get_file_base() . '/lang/langs.ini');
            }
            if ((!is_string($value)) || (!array_key_exists($value, $LANG_TD_MAP))) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'IP':
            if ((!is_string($value)) || (strlen($value) > 40) || ((strlen($value) < 7) && ($value != '')) || ((count(explode('.', $value)) != 4) && ($value != '') && (count(explode(':', $value)) < 3))) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'SHORT_TEXT':
            if ((!is_string($value)) || (strlen($value) > 255)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'SHORT_INTEGER':
            if ((!is_integer($value)) || ($value > 255) || ($value < 0)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'AUTO_LINK':
            if ((!is_integer($value)) || ($value < -1)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo); // -1 means something different to null
            }
            break;
        case 'BINARY':
            if ((!is_integer($value)) || (($value != 0) && ($value != 1))) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'MEMBER':
            if ((!is_integer($value)) || ($value < $GLOBALS['FORUM_DRIVER']->get_guest_id())) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
        case 'TIME':
            if ((!is_integer($value)) || ($value > time() + 500000000) || ($value < 1000)) {
                _fail_php_type_check($type, $function_name, $name, $value, $echo);
            }
            break;
    }
}

/**
 * Throw out a type checker error message.
 *
 * @param  string $type The type involved
 * @param  string $function_name The function involved
 * @param  string $name The parameter name involved
 * @param  string $value The value involved
 * @param  boolean $echo Whether we just echo errors instead of exiting
 */
function _fail_php_type_check($type, $function_name, $name, $value, $echo = false)
{
    if ($echo) {
        echo 'TYPE_MISMATCH in \'' . $function_name . '\' (' . $name . ' is ' . (is_string($value) ? $value : strval($value)) . ' which is not a ' . $type . ')<br />';
    } else {
        attach_message(do_lang_tempcode('TYPE_MISMATCH', escape_html($function_name), escape_html($name), is_string($value) ? $value : strval($value)/*, $type*/), 'warn');
    }
}

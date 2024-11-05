<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
function init__phpdoc()
{
    global $LANG_TD_MAP;
    $LANG_TD_MAP = null;
}

/**
 * Get a complex API information structure from a PHP file. It assumes the file has reasonably properly layed out class and function whitespace.
 * The return structure is...
 *  list of classes/interfaces/traits
 * each entry is a map containing 'functions' (list of functions) and 'name' and 'extends' and 'implements' and 'type'
 *  each functions entry is a map containing 'parameters' and 'name' and 'return' and 'flags' and 'is_static' and 'is_abstract' and 'is_final' and 'visibility'
 *   each parameters entry is a map containing...
 *    name
 *    description
 *    type
 *    default
 *    default_raw
 *    set
 *    range
 *    ref
 *    is_variadic
 *
 * @param  PATH $filename The PHP code module to get API information for
 * @param  boolean $include_code Whether to include function source code
 * @param  boolean $pedantic_warnings Whether to give warnings for non-consistent alignment of spacing
 * @param  boolean $writeback Whether to write in PHP type hinting from the phpdoc, if it is missing
 * @param  boolean $force_return Whether to ignore CQC No API Check comments
 * @return array The complex structure of API information
 */
function get_php_file_api(string $filename, bool $include_code = false, bool $pedantic_warnings = false, bool $writeback = false, bool $force_return = false) : array
{
    require_code('type_sanitisation');

    $classes = [];
    $class_has_comments = [];

    $meta_keywords_available = ['final', 'public', 'private', 'protected', 'static', 'abstract'];

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
    if ($lines === false) {
        attach_message('Could not read file ' . $filename, 'warn');
    }

    if (!$make_alterations) {
        foreach ($lines as $i => $line) {
            $lines[$i] = str_replace("\t", ' ', $line);
        }
    }

    // Go through all lines, keeping record of what current class we are looking at
    $current_class = '__global';
    $current_class_level = 0;
    $functions = [];
    $class_is_abstract = false;
    $implements = [];
    $traits = [];
    $extends = null;
    $type = null;
    $package = null;

    // Define __global class right-away so we can note the package if there was one
    $classes['__global'] = ['functions' => $functions, 'name' => $current_class, 'is_abstract' => $class_is_abstract, 'implements' => $implements, 'traits' => $traits, 'extends' => $extends, 'type' => $type, 'package' => $package];

    global $LINE;
    for ($i = 0; array_key_exists($i, $lines); $i++) {
        $line = $lines[$i];
        $LINE = $i + 1;

        if ((!$force_return) && (strpos($line, '/' . '*CQC: No API check*/') !== false)) {
            return [];
        }

        // Sense class boundaries (hackerish: assumes whitespace laid out correctly)
        $ltrim = ltrim($line);
        $matches = [];
        if (preg_match('#^(abstract\s+)?(interface|class|trait)\s+(\w+)#', $ltrim, $matches) != 0) {
            if (!empty($functions)) {
                $classes[$current_class] = ['functions' => $functions, 'name' => $current_class, 'is_abstract' => $class_is_abstract, 'implements' => $implements, 'traits' => $traits, 'extends' => $extends, 'type' => $type];
                if ($current_class == '__global') {
                    $classes[$current_class]['package'] = $package;
                }
            }

            $current_class = $matches[3];
            $current_class_level = strlen($line) - strlen($ltrim);
            $functions = [];
            $class_is_abstract = (strpos($matches[1], 'abstract') !== false);
            $type = $matches[2];

            $matches = [];
            $num_matches = preg_match_all('#\s(extends)\s([\w\\\\]+)#', $ltrim, $matches);
            for ($j = 0; $j < $num_matches; $j++) {
                $extends = $matches[2][$j];
            }
            if ($num_matches > 1) {
                attach_message($current_class . ' is trying to extend multiple classes', 'warn');
            }
            $implements = [];
            $num_matches = preg_match_all('#\s(implements)\s([\w\\\\]+)#', $ltrim, $matches);
            for ($j = 0; $j < $num_matches; $j++) {
                $implements[] = $matches[2][$j];
            }
            $traits = [];
        } elseif (($current_class != '__global') && (substr($line, 0, $current_class_level + 1) == str_repeat(' ', $current_class_level) . '}')) {
            if (!empty($functions)) {
                $classes[$current_class] = ['functions' => $functions, 'name' => $current_class, 'is_abstract' => $class_is_abstract, 'implements' => $implements, 'traits' => $traits, 'extends' => $extends, 'type' => $type];
            }

            $current_class = '__global';
            $functions = array_key_exists('__global', $classes) ? $classes['__global']['functions'] : [];
            $class_is_abstract = false;
            $type = null;

            $implements = [];
            $traits = [];
            $extends = null;
        }

        // Detect an API class or function
        if (substr($ltrim, 0, 3) == '/**') {
            $depth = strlen($line) - strlen($ltrim);

            // Find class or function line
            for ($j = $i + 1; array_key_exists($j, $lines); $j++) {
                $line2 = $lines[$j];
                $_depth = str_repeat(' ', $depth);

                // Class/Interface/Trait
                $matches = [];
                if (preg_match('#^' . str_repeat(' ', $depth) . '(abstract\s+)?(interface|class|trait)\s+([\w\\\\]+)#', $line2, $matches) != 0) {
                    $class_has_comments[$matches[3]] = true;
                    continue 2;
                }

                // Function
                if (substr($line2, 0, $depth + 9) == $_depth . 'function ') {
                    // Parse function line
                    $_line = substr($line2, $depth + 9);
                    list($function_name, $parameters, $php_return_type, $php_return_type_nullable) = _read_php_function_line($_line);
                    $is_static = null;
                    $is_abstract = null;
                    $is_final = null;
                    $visibility = null;
                    break;
                }

                // Method
                $matches = [];
                if (preg_match('#^' . $_depth . '(((' . implode('|', $meta_keywords_available) . ') )*)function &?(.*)#', $line2, $matches) != 0) {
                    // Parse function line
                    $_line = $matches[4];
                    list($function_name, $parameters, $php_return_type, $php_return_type_nullable) = _read_php_function_line($_line);

                    // Check meta properties for sanity
                    foreach ($meta_keywords_available as $meta_keyword) {
                        if (substr_count($matches[1], $meta_keyword) > 1) {
                            attach_message($function_name . ' has repeated meta keywords', 'warn');
                        }
                    }
                    if (substr_count($matches[1], 'public') + substr_count($matches[1], 'protected') + substr_count($matches[1], 'private') > 1) {
                        attach_message($function_name . ' has multiple visibilities set', 'warn');
                    }

                    // Detect meta properties
                    $is_static = (strpos($matches[1], 'static') !== false);
                    $is_abstract = (strpos($matches[1], 'abstract') !== false);
                    $is_final = (strpos($matches[1], 'final') !== false);
                    $visibility = 'public';
                    if (strpos($matches[1], 'private') !== false) {
                        $visibility = 'private';
                    } elseif (strpos($matches[1], 'protected') !== false) {
                        $visibility = 'protected';
                    }

                    if ($current_class == '__global') {
                        attach_message($function_name . ' seems to be a method outside of a class', 'warn');
                    }

                    break;
                }

                // Irrelevant lines, don't let it confuse us
                if ((substr(trim($line2), 0, 3) == '/**') || ((strpos($line2, '*/') !== false) && (array_key_exists($j + 1, $lines)) && (preg_match('#(^|\s)(function|class|interface|trait)\s+#', $lines[$j + 1]) == 0))) { // Probably just skipped past a top header
                    // At least check for a package name if we do not already have one
                    if (($current_class == '__global') && ($classes['__global']['package'] === null)) {
                        for ($k = $i; $k <= ($j - 1); $k++) {
                            $matches = [];
                            if (preg_match('#@package\s+(\w+)#', $lines[$k], $matches) === 1) {
                                $package = $matches[1];
                                $classes['__global']['package'] = $matches[1];
                            }
                        }
                    }

                    $i = $j - 1;
                    continue 2;
                }
            }
            if (!array_key_exists($j, $lines)) {
                continue; // No function: probably we commented it out
            }

            $funcdef_line = $line2;
            $funcdef_line_new = $line2;
            $funcdef_line_index = $j;

            // Parse comment block bits
            $description = '';
            $flags = [];
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
                            attach_message('There is an API parameter mismatch in function ' . $function_name, 'warn');
                            continue 2;
                        }

                        if ($pedantic_warnings) {
                            if (preg_match('#^@param  [^\s]+ (\.\.\.)?\$\w+ #s', $ltrim) == 0) {
                                attach_message('The spacing alignment for a PHPDoc parameter definition on ' . $function_name . ' was not as expected; maybe too few or too many spaces. This is a pedantic error, but we like consistent code layout.', 'inform');
                            }

                            if ((substr($ltrim, -1) == '.') && (substr_count($ltrim, '.') == 1)) {
                                attach_message('Do not need trailing full stop for parameter definitions', 'inform');
                            }
                        }

                        $parts = _cleanup_array(preg_split('/\s/', substr($ltrim, 6)));
                        if ((strpos($parts[0], '?') === false) && (array_key_exists('default', $parameters[$arg_counter])) && ($parameters[$arg_counter]['default'] === null)) {
                            attach_message(do_lang__phpdoc('UNALLOWED_NULL', escape_html($parameters[$arg_counter]['name']), escape_html($function_name), escape_html('null')), 'warn');
                            continue 2;
                        }
                        if ((array_key_exists('default', $parameters[$arg_counter])) && ($parameters[$arg_counter]['default'] === false) && (!in_array(preg_replace('#[^\w]#', '', $parts[0]), ['mixed', 'boolean']))) {
                            attach_message(do_lang__phpdoc('UNALLOWED_NULL', escape_html($parameters[$arg_counter]['name']), escape_html($function_name), escape_html('false')), 'warn');
                            continue 2;
                        }

                        $parameters[$arg_counter]['type'] = $parts[0];
                        unset($parts[0]);
                        $_description = trim(implode(' ', $parts));
                        if (substr($_description, 0, 1) != '$') {
                            if ($make_alterations) {
                                $found = false;
                                for ($k = $i + 1; $k < count($lines); $k++) {
                                    $matches = [];
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
                        $parameters[$arg_counter]['phpdoc_name'] = ltrim(preg_replace('#^(\.\.\.)?(\$\w+) .*#', '$2', $_description), '$');
                    } elseif (substr($ltrim, 0, 7) == '@return') {
                        if ($return !== null) {
                            attach_message('Multiple return values defined', 'inform');
                        }

                        $return = [];

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
                        if ((!in_array(substr($ltrim, -1), ['.', ':', '!', '?', '}', ';', ','])) && (substr($ltrim, 0, 2) != '- ') && (preg_match('#^\d+\) #', $ltrim) == 0)) {
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
                attach_message('There is an API parameter mismatch in function ' . $function_name, 'warn');
                continue;
            }

            // Do some checks
            $found_a_default = false;
            foreach ($parameters as $parameter_i => $parameter) {
                // Type check
                if (array_key_exists('default', $parameter)) {
                    $found_a_default = true;
                    $default = $parameter['default'];
                    if ($default === 'boolean-true') {
                        $default = true;
                    }
                    if ($default === 'boolean-false') {
                        $default = false;
                    }
                } else {
                    $default = null;
                    if ($found_a_default) {
                        attach_message('You defined a defaulted parameter before a parameter that has no default, for ' . $function_name, 'warn');
                        $found_a_default = false;
                    }
                }

                if ($parameters[$arg_counter]['phpdoc_name'] == '') {
                    attach_message('You did not give the name of a parameter in the phpdoc for ' . $function_name, 'warn');
                } else {
                    if ($parameter['name'] != $parameter['phpdoc_name']) {
                        attach_message('Parameter naming mismatch, ' . $parameter['name'] . ' vs ' . $parameter['phpdoc_name'] . ' for a parameter in ' . $function_name, 'warn');
                    }
                }

                foreach ($parameters as $parameter_j => $_parameter) {
                    if (($parameter_j > $parameter_i) && ($parameter['description'] == $_parameter['description'])) {
                        if (preg_replace('#_.+$#', '', $parameter['name']) != preg_replace('#_.+$#', '', $_parameter['name'])) { // Check not sharing a prefix
                            attach_message('Duplicated parameter description in ' . $function_name . ' (' . $parameter['name'] . ' vs ' . $_parameter['name'] . ')', 'warn');
                            $found_a_duplication = true;
                            break;
                        }
                    }
                }

                $funcdef_line_new = check_function_parameter_typing($parameter['type'], $parameter['php_type'], $parameter['php_type_nullable'], $function_name, $parameter['name'], $default, array_key_exists('range', $parameter) ? $parameter['range'] : null, array_key_exists('set', $parameter) ? $parameter['set'] : null, $writeback ? $funcdef_line_new : null);

                // Check that null is fully specified
                if (strpos($parameter['type'], '?') !== false) {
                    if (strpos($parameter['description'], '(null: ') === false) {
                        attach_message(do_lang__phpdoc('NULL_MEANING_NOT_SPECIFIED', escape_html($parameter['name']), escape_html($function_name), escape_html('null')), 'warn');
                    }
                } else {
                    if (strpos($parameter['description'], '(null: ') !== false) {
                        attach_message(do_lang__phpdoc('NULL_MEANING_SHOULDNT_BE_SPECIFIED', escape_html($parameter['name']), escape_html($function_name), escape_html('null')), 'warn');
                    }
                }
                if (strpos($parameter['type'], '~') !== false) {
                    if (strpos($parameter['description'], '(false: ') === false) {
                        attach_message(do_lang__phpdoc('NULL_MEANING_NOT_SPECIFIED', escape_html($parameter['name']), escape_html($function_name), escape_html('false')), 'warn');
                    }
                } elseif (strpos($parameter['type'], 'boolean') === false) {
                    if (strpos($parameter['description'], '(false: ') !== false) {
                        attach_message(do_lang__phpdoc('NULL_MEANING_SHOULDNT_BE_SPECIFIED', escape_html($parameter['name']), escape_html($function_name), escape_html('false')), 'warn');
                    }
                }
                if (strpos($parameter['type'], 'boolean') === false) {
                    if (strpos($parameter['description'], '(true: ') !== false) {
                        attach_message(do_lang__phpdoc('NULL_MEANING_SHOULDNT_BE_SPECIFIED', escape_html($parameter['name']), escape_html($function_name), escape_html('true')), 'warn');
                    }
                }
            }
            if ($return !== null) {
                $fret = $return;
                $funcdef_line_new = check_function_parameter_typing($return['type'], $php_return_type, $php_return_type_nullable, $function_name, '(return)', null, array_key_exists('range', $return) ? $return['range'] : null, array_key_exists('set', $return) ? $return['set'] : null, $writeback ? $funcdef_line_new : null);

                // Check that null is fully specified
                if (strpos($return['type'], '?') !== false) {
                    if (strpos($return['description'], '(null: ') === false) {
                        attach_message(do_lang__phpdoc('NULL_MEANING_NOT_SPECIFIED', escape_html('(return)'), escape_html($function_name), escape_html('null')), 'warn');
                    }
                } else {
                    if (strpos($return['description'], '(null: ') !== false) {
                        attach_message(do_lang__phpdoc('NULL_MEANING_SHOULDNT_BE_SPECIFIED', escape_html('(return)'), escape_html($function_name), escape_html('null')), 'warn');
                    }
                }
                if (strpos($return['type'], '~') !== false) {
                    if (strpos($return['description'], '(false: ') === false) {
                        attach_message(do_lang__phpdoc('NULL_MEANING_NOT_SPECIFIED', escape_html('(return)'), escape_html($function_name), escape_html('false')), 'warn');
                    }
                } else {
                    if (strpos($return['description'], '(false: ') !== false) {
                        attach_message(do_lang__phpdoc('NULL_MEANING_SHOULDNT_BE_SPECIFIED', escape_html('(return)'), escape_html($function_name), escape_html('false')), 'warn');
                    }
                }
            } else {
                $fret = null;
            }

            // Write-back
            if ($writeback) {
                $lines[$funcdef_line_index] = str_replace($funcdef_line, $funcdef_line_new, $lines[$funcdef_line_index]);
            }

            // Now get source code
            $code = '';
            for ($k = $funcdef_line_index; array_key_exists($k, $lines); $k++) {
                $line2 = $lines[$k];
                $code .= $line2;
                if (substr($line2, 0, $depth + 1) == str_repeat(' ', $depth) . '}') {
                    break;
                }
            }

            if (trim($description) == '') {
                attach_message('There is an empty function description for \'' .  rtrim($line) . '\'', 'warn');
            }

            $function = [
                'filename' => $filename,
                'parameters' => $parameters,
                'php_return_type' => $php_return_type,
                'php_return_type_nullable' => $php_return_type_nullable,
                'name' => $function_name,
                'description' => $description,
                'flags' => $flags,
                'is_static' => $is_static,
                'is_abstract' => $is_abstract,
                'is_final' => $is_final,
                'visibility' => $visibility,
                'return' => $fret,
            ];
            if ($include_code) {
                $function['code'] = $code;
            }
            $functions[$function_name] = $function;

            $i++;
        }

        $matches = [];
        if (preg_match('#^use ([\w\\\\]+);#', $ltrim, $matches) != 0) {
            $traits[] = $matches[1];
        }
    }

    // See if there are any functions with blank lines above them
    for ($i = 0; array_key_exists($i, $lines); $i++) {
        $line = ltrim($lines[$i]);
        if ((preg_match('#^((' . implode('|', $meta_keywords_available) . ') )*function (.*)#', $line) != 0) && ((trim($lines[$i - 1]) == '') || (trim($lines[$i - 1]) == '{'))) {
            // Infer some parameters from the function line, given we have no PHPDoc
            if (substr($lines[$i], 0, 9) == 'function ') { // Only if not class level (i.e. global)
                $function_name = preg_replace('#function\s+(\w+)\s*\(.*#s', '${1}', $line);
                $parameters = [];
                $num_parameters = substr_count($line, '$');
                $num_parameters_defaulted = substr_count($line, '=');
                for ($arg_counter = 0; $arg_counter < $num_parameters; $arg_counter++) {
                    $parameters[$arg_counter]['type'] = 'mixed';
                    $parameters[$arg_counter]['description'] = '';
                    if ($arg_counter >= $num_parameters - $num_parameters_defaulted) {
                        $parameters[$arg_counter]['default'] = 'boolean-true';
                    }
                }
                $function = ['filename' => $filename, 'parameters' => $parameters, 'name' => $function_name, 'description' => '', 'flags' => []];
                if ($include_code) {
                    $function['code'] = '';
                }
                $function['return'] = ['type' => 'mixed'];
                $functions[$function_name] = $function;
            }

            if (!function_exists('do_lang__phpdoc')) {
                exit('Missing function comment for: ' . $line);
            }
            attach_message('There is a missing function comment for \'' .  rtrim($line) . '\'', 'warn');
        }
    }

    // See if there are classes with no comments
    foreach (array_keys($classes) as $class) {
        if ($class == '__global') {
            continue;
        }

        if (empty($class_has_comments[$class])) {
            attach_message('There is a missing class comment for ' . rtrim($class), 'warn');

            $classes[$class]['comment'] = '';
        } else {
            $classes[$class]['comment'] = $class_has_comments[$class];
        }
    }

    if (!empty($functions)) {
        $classes[$current_class/*will be global*/] = ['functions' => $functions, 'name' => $current_class, 'is_abstract' => $class_is_abstract, 'implements' => $implements, 'traits' => $traits, 'extends' => $extends, 'type' => $type, 'package' => $package];
    }

    // Write-back
    if ($writeback) {
        $code_new = implode('', $lines);
        file_put_contents($full_path, $code_new);
    }

    return $classes;
}

/**
 * Read a PHP function line and return parsed details.
 *
 * @param  string $_line The line
 * @return array A pair: (function name, parameters), where parameters is a list of maps detailing each parameter
 */
function _read_php_function_line(string $_line) : array
{
    $function_name = '';
    $parameters = [];
    $return_type = null;
    $return_type_nullable = false;

    // State
    $parse = 'function_name';
    $post_comment_state = null; // Where to go back to after a comment is ended

    // String default argument parsing
    $in_string = null; // " or '
    $escaping = false;

    // Properties of particular arguments
    $arg_default = '';
    $arg_type = null;
    $arg_type_nullable = false;
    $arg_name = '';
    $ref = false;
    $is_variadic = false;

    for ($k = 0; $k < strlen($_line); $k++) {
        $char = $_line[$k];

        switch ($parse) {
            case 'function_name':
                if (is_alphanumeric($char, true)) {
                    $function_name .= $char;
                } elseif ($char == '(') {
                    $parse = 'in_args';

                    $arg_default = '';
                    $arg_type = null;
                    $arg_type_nullable = false;
                    $arg_name = '';
                    $ref = false;
                    $is_variadic = false;
                } elseif (trim($char) == '') {
                    $parse = 'between_name_and_args';
                } else {
                    attach_message('Unexpected character, ' . $char . ', parsing function arguments [' . $parse . '], for ' .  rtrim($_line), 'warn');
                }
                break;

            case 'between_name_and_args':
                if ($char == '(') {
                    $parse = 'in_args';

                    $arg_default = '';
                    $arg_type = null;
                    $arg_type_nullable = false;
                    $arg_name = '';
                    $ref = false;
                    $is_variadic = false;
                } elseif (trim($char) != '') {
                    attach_message('Unexpected character, ' . $char . ', parsing function arguments [' . $parse . '], for ' .  rtrim($_line), 'warn');
                }
                break;

            case 'in_args':
                if ($char == '$') {
                    $parse = 'in_arg_variable';
                } elseif ($char == '?') {
                    $parse = 'in_arg_type';
                    $arg_type = '';
                    $arg_type_nullable = true;
                } elseif (is_alphanumeric($char, true)) {
                    $parse = 'in_arg_type';
                    $arg_type = $char;
                    $arg_type_nullable = false;
                } elseif (trim($char) == '') {
                    // Nothing
                } elseif ($char == '&') {
                    $ref = true;
                } elseif (($char == '.') && ($_line[$k + 1] == '.') && ($_line[$k + 2] == '.')) {
                    $k += 2;
                    $is_variadic = true;
                } elseif (($char == '/') && ($_line[$k + 1] == '*')) {
                    $post_comment_state = $parse;
                    $parse = 'in_comment';
                } elseif ($char == ')') {
                    $parse = 'after_args';
                } else {
                    attach_message('Unexpected character, ' . $char . ', parsing function arguments [' . $parse . '], for ' .  rtrim($_line), 'warn');
                }
                break;

            case 'in_arg_type':
                if (is_alphanumeric($char, true)) {
                    $arg_type .= $char;
                } elseif (trim($char) == '') {
                    $parse = 'in_args';
                } elseif (($char == '/') && ($_line[$k + 1] == '*')) {
                    $parse = 'in_comment';
                    $post_comment_state = 'in_args';
                } else {
                    attach_message('Unexpected character, ' . $char . ', parsing function arguments [' . $parse . '], for ' .  rtrim($_line), 'warn');
                }
                break;

            case 'in_arg_variable':
                if (is_alphanumeric($char, true)) {
                    $arg_name .= $char;
                } elseif ($char == ',') {
                    $parameters[] = ['name' => $arg_name, 'php_type' => $arg_type, 'php_type_nullable' => $arg_type_nullable, 'ref' => $ref, 'is_variadic' => $is_variadic];
                    $parse = 'in_args';

                    $arg_default = '';
                    $arg_type = null;
                    $arg_type_nullable = false;
                    $arg_name = '';
                    $ref = false;
                    $is_variadic = false;
                } elseif ($char == '=') {
                    $parse = 'in_arg_default';
                    $arg_default = '';
                } elseif ($char == ')') {
                    $parameters[] = ['name' => $arg_name, 'php_type' => $arg_type, 'php_type_nullable' => $arg_type_nullable, 'ref' => $ref, 'is_variadic' => $is_variadic];
                    $parse = 'after_args';
                } elseif (($char == '/') && ($_line[$k + 1] == '*')) {
                    $parse = 'in_comment';
                    $post_comment_state = 'in_args';
                } elseif (trim($char) == '') {
                    $parse = 'in_arg_after_variable';
                } else {
                    attach_message('Unexpected character, ' . $char . ', parsing function arguments [' . $parse . '], for ' .  rtrim($_line), 'warn');
                }
                break;

            case 'in_arg_after_variable':
                if ($char == ',') {
                    $parameters[] = ['name' => $arg_name, 'php_type' => $arg_type, 'php_type_nullable' => $arg_type_nullable, 'ref' => $ref, 'is_variadic' => $is_variadic];
                    $parse = 'in_args';

                    $arg_default = '';
                    $arg_type = null;
                    $arg_type_nullable = false;
                    $arg_name = '';
                    $ref = false;
                    $is_variadic = false;
                } elseif ($char == '=') {
                    $parse = 'in_arg_default';
                    $arg_default = '';
                } elseif ($char == ')') {
                    $parameters[] = ['name' => $arg_name, 'php_type' => $arg_type, 'php_type_nullable' => $arg_type_nullable, 'ref' => $ref, 'is_variadic' => $is_variadic];
                    $parse = 'after_args';
                } elseif (($char == '/') && ($_line[$k + 1] == '*')) {
                    $parse = 'in_comment';
                    $post_comment_state = 'in_args';
                } elseif (trim($char) != '') {
                    attach_message('Unexpected character, ' . $char . ', parsing function arguments [' . $parse . '], for ' .  rtrim($_line), 'warn');
                }
                break;

            case 'in_arg_default':
                $in_array = (preg_match('#^\s*(\[[^\]]*|array\([^)]*)$#', $arg_default) != 0);
                $closed_array = ($in_array) && (preg_match('#^\s*(\[[^\]]*\]|array\([^)]*\))$#', $arg_default) != 0);
                if (($char == '/') && ($_line[$k + 1] == '*') && ($in_string === null) && ((!$in_array) || ($closed_array)) && (!$escaping)) {
                    $post_comment_state = $parse;
                    $parse = 'in_comment';
                } elseif ((($char == ',') || ($char == ')')) && ($in_string === null) && ((!$in_array) || ($closed_array)) && (!$escaping)) {
                    $new_parameter = ['name' => $arg_name, 'php_type' => $arg_type, 'php_type_nullable' => $arg_type_nullable, 'ref' => $ref, 'is_variadic' => $is_variadic];

                    $default_raw = $arg_default;
                    if ((preg_match('#^\s*[A-Z_]+\s*$#', $arg_default) != 0) && (!defined(trim($arg_default)))) {
                        $default = 0; // Cannot look it up, not currently defined
                        $new_parameter += ['default' => $default, 'default_raw' => $default_raw];
                    } else {
                        try {
                            $default = @eval('return ' . $arg_default . ';'); // Could be unprocessable by php.php in standalone mode
                            $new_parameter += ['default' => $default, 'default_raw' => $default_raw];
                        } catch (Throwable $e) {
                        }
                    }

                    $parameters[] = $new_parameter;

                    if ($char == ',') {
                        $parse = 'in_args';

                        $arg_default = '';
                        $arg_type = null;
                        $arg_type_nullable = false;
                        $arg_name = '';
                        $ref = false;
                        $is_variadic = false;
                    } else {
                        $parse = 'after_args';
                    }
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
                    if ((($char == '"') || ($char == "'")) && ((!$in_array) || ($closed_array))) {
                        $in_string = $char;
                    }
                }
                break;

            case 'after_args':
                if ($char == ':') {
                    $parse = 'before_return_type';
                    if ($_line[$k - 1] != ' ') {
                        attach_message('No space before the colon of the return type; against coding standards, for ' .  rtrim($_line), 'warn');
                    }
                } elseif (trim($char) != '') {
                    $parse = 'done';
                }
                break;

            case 'before_return_type':
                if ($char == '?') {
                    $parse = 'return_type';
                    $return_type = '';
                    $return_type_nullable = true;
                } elseif (is_alphanumeric($char, true)) {
                    $parse = 'return_type';
                    $return_type = $char;
                    $return_type_nullable = false;
                } elseif (trim($char) != '') {
                    $parse = 'done';
                }
                break;

            case 'return_type':
                if (is_alphanumeric($char, true)) {
                    $return_type .= $char;
                } else {
                    $parse = 'done';
                }
                break;

            case 'in_comment':
                if (($char == '*') && ($_line[$k + 1] == '/')) {
                    $parse = $post_comment_state;
                    $k++;
                }
                break;

            case 'done':
                if ($char == '{') {
                    attach_message('Opening braces for functions must be on next line; against coding standards, for ' .  rtrim($_line), 'warn');
                }
                break;
        }
    }

    return [$function_name, $parameters, $return_type, $return_type_nullable];
}

/**
 * Remove and blank strings from the given array.
 *
 * @param  array $in List of strings
 * @return array List of strings, with blank strings removed
 */
function _cleanup_array(array $in) : array
{
    $out = [];
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
 * @param  ID_TEXT $phpdoc_type The parameter type according to phpdoc
 * @param  ?ID_TEXT $php_type The PHP type hint (null: unknown)
 * @param  boolean $php_type_nullable Whether the PHP type is nullable
 * @param  string $function_name The functions name (used in error message)
 * @param  string $name The parameter name (used in error message)
 * @param  ?mixed $value The parameters value (null: value actually is null)
 * @param  ?string $range The string of value range of the parameter (null: no range constraint)
 * @param  ?string $set The string of value set limitation for the parameter (null: no set constraint)
 * @param  ?string $funcdef_line The line of code the function was defined on (null: don't do write-back)
 */
function check_function_parameter_typing(string $phpdoc_type, ?string $php_type, bool $php_type_nullable, string $function_name, string $name, $value, ?string $range, ?string $set, ?string $funcdef_line)
{
    $funcdef_line_new = $funcdef_line;

    $valid_types = [
        'integer' => 'int',
        'AUTO_LINK' => 'int',
        'SHORT_INTEGER' => 'int',
        'UINTEGER' => 'int',
        'BINARY' => 'int',
        'MEMBER' => 'int',
        'GROUP' => 'int',
        'TIME' => 'int',

        'float' => 'float',
        'REAL' => 'float',

        'string' => 'string',
        'LONG_TEXT' => 'string',
        'SHORT_TEXT' => 'string',
        'ID_TEXT' => 'string',
        'MINIID_TEXT' => 'string',
        'IP' => 'string',
        'LANGUAGE_NAME' => 'string',
        'URLPATH' => 'string',
        'PATH' => 'string',
        'EMAIL' => 'string',

        'array' => 'array',

        'boolean' => 'bool',

        'Tempcode' => 'object',
        'object' => 'object',

        'resource' => null, // Don't know why PHP type hints don't support resource but they don't
        'mixed' => null,
    ];

    $null_allowed = (strpos($phpdoc_type, '?') !== false);
    $false_allowed = (strpos($phpdoc_type, '~') !== false);
    $_phpdoc_type = ltrim($phpdoc_type, '?~');

    // Check PHP type is consistent with phpdoc type
    if (array_key_exists($_phpdoc_type, $valid_types)) {
        $expected_php_type = $false_allowed ? null : $valid_types[$_phpdoc_type];

        if ($php_type !== null) {
            if ($expected_php_type === null) {
                if (($php_type !== null) && ($php_type !== 'callable'/*No representation in our phpdoc*/)) {
                    attach_message('The phpdoc type ' . $phpdoc_type . ' implies no PHP type hint for ' . $name, 'warn');
                }
            } else {
                if ($expected_php_type == 'object') {
                    if (in_array($php_type, ['array', 'bool', 'callable', 'float', 'int', 'iterable', 'string', 'void'])) { // If not an object or class
                        attach_message('The phpdoc type ' . $phpdoc_type . ' is inconsistent with the ' . $php_type . ' PHP type hint for ' . $name, 'warn');
                    }
                } else {
                    if ($php_type != $expected_php_type) {
                        attach_message('The phpdoc type ' . $phpdoc_type . ' is inconsistent with the ' . $php_type . ' PHP type hint for ' . $name, 'warn');
                    }
                }
            }

            if ($php_type_nullable != $null_allowed) {
                attach_message('The phpdoc type and the PHP type hint conflict around nullability for ' . $name, 'warn');
            }
        } elseif ($expected_php_type !== null) {
            if ($funcdef_line_new !== null) {
                // Code write-back
                $_expected_php_type = ($null_allowed ? '?' : '') . $expected_php_type;
                if ($name == '(return)') {
                    $funcdef_line_new = rtrim($funcdef_line_new);
                    if (substr($funcdef_line_new, -1) == ';') {
                        $funcdef_line_new = rtrim($funcdef_line_new, ';') . ' : ' . $_expected_php_type . ';' . "\n";
                    } else {
                        $funcdef_line_new = $funcdef_line_new . ' : ' . $_expected_php_type . "\n";
                    }
                } else {
                    $funcdef_line_new = preg_replace('#(&?(\.\.\.)?\$' . preg_quote($name) . '[^\w])#', $_expected_php_type . ' $1', $funcdef_line_new);
                }
            } else {
                attach_message('Missing PHP type hint that should be possible from what the phpdoc says (' . $expected_php_type . ') for ' . $name, 'warn');
            }
        }
    }

    // Check phpdoc type
    if (!array_key_exists($_phpdoc_type, $valid_types)) {
        attach_message('The phpdoc type ' . $phpdoc_type . ' used in ' . $function_name . ' is not valid for ' . $name, 'warn');
    }

    // Check value
    if ($value !== null) {
        test_value_matches_type($phpdoc_type, $function_name, $name, $value);
    }

    // Check range
    if (($range !== null) && ($value !== null)) {
        $allowed = [
            'UINTEGER',
            'SHORT_INTEGER',
            'REAL',
            'integer',
            'float',
        ];
        $allowed_string = [
            'LONG_TEXT',
            'SHORT_TEXT',
            'ID_TEXT',
            'MINIID_TEXT',
            'string',
        ];
        if ((!in_array($_phpdoc_type, $allowed)) && (!in_array($_phpdoc_type, $allowed_string)) && ($phpdoc_type != 'array')) {
            attach_message('A range was specified for a parameter type ' . $_phpdoc_type . ' in function name ' . $function_name . '; this parameter type cannot have a range for ' . $name, 'warn');
        }

        list($min, $max) = explode(' ', $range);

        if (in_array($_phpdoc_type, $allowed)) {
            if ($value != '') {
                if ((($min != 'min') && ($value < intval($min))) || (($max != 'max') && ($value > intval($max)))) {
                    attach_message(do_lang__phpdoc('OUT_OF_RANGE_VALUE', escape_html($name), escape_html($function_name), escape_html($value)), 'warn');
                }
            }
        } elseif (in_array($_phpdoc_type, $allowed_string)) {
            if ($value != '') {
                if ((($min != 'min') && (strlen($value) < intval($min))) || (($max != 'max') && (strlen($value) > intval($max)))) {
                    attach_message(do_lang__phpdoc('OUT_OF_RANGE_VALUE', escape_html($name), escape_html($function_name), escape_html($value)), 'warn');
                }
            }
        } else {
            if ($value != '') {
                if ((($min != 'min') && (count($value) < intval($min))) || (($max != 'max') && (count($value) > intval($max)))) {
                    attach_message(do_lang__phpdoc('OUT_OF_RANGE_VALUE', escape_html($name), escape_html($function_name), escape_html($value)), 'warn');
                }
            }
        }
    }

    // Check set
    if (($set !== null) && ($value !== null)) {
        $_set = [];
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
                attach_message(do_lang__phpdoc('OUT_OF_RANGE_VALUE', escape_html($name), escape_html($function_name), escape_html($value)), 'warn');
            }
        }
    }

    return $funcdef_line_new;
}

/**
 * Type-check the specified value (giving an error if the type checking fails) [just value against type].
 *
 * @param  ID_TEXT $phpdoc_type The phpdoc parameter type
 * @param  string $function_name The functions name (used in error message)
 * @param  string $name The parameter name (used in error message)
 * @param  mixed $value The parameters value (cannot be null)
 */
function test_value_matches_type(string $phpdoc_type, string $function_name, string $name, $value)
{
    $null_allowed = (strpos($phpdoc_type, '?') !== false);
    $false_allowed = (strpos($phpdoc_type, '~') !== false);
    $_phpdoc_type = preg_replace('#[^\w]#', '', $phpdoc_type);

    if (($value === null) && (!$null_allowed)) {
        attach_message(do_lang__phpdoc('UNALLOWED_NULL', escape_html($name), escape_html($function_name), 'null'), 'warn');
    }

    if (($value === false) && (!$false_allowed) && (!in_array($_phpdoc_type, ['mixed', 'boolean']))) {
        attach_message(do_lang__phpdoc('UNALLOWED_NULL', escape_html($name), escape_html($function_name), 'false'), 'warn');
    }

    if ($_phpdoc_type == 'mixed') {
        return;
    }

    if ((is_string($value)) && (preg_match('#^[A-Z_]+$#', $value) != 0)) {
        return;
    }

    switch ($_phpdoc_type) {
        case 'integer':
            if ((!is_integer($value)) && ((!is_float($value)) || (strval(intval(round($value))) != strval($value)))) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'UINTEGER':
            if ((!is_integer($value)) && ((!is_float($value)) || (strval(intval(round($value))) != strval($value))) || ($value < 0)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'resource':
            if (!is_resource($value)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'object':
            if (!is_object($value)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'Tempcode':
            if ((!is_object($value)) || (!is_a($value, 'Tempcode'))) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'REAL':
        case 'float':
            if (!is_float($value)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'boolean':
            if (!is_bool($value)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'array':
            if (!is_array($value)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'string':
            if (!is_string($value)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'PATH':
            if (!is_string($value)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'EMAIL':
            if ((!is_string($value)) || ((!is_valid_email_address($value)) && ($value != ''))) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'URLPATH':
            if ((!is_string($value)) || (strlen($value) > 127)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'LONG_TEXT':
            if (!is_string($value)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'MINIID_TEXT':
            if ((!is_string($value)) || (strlen($value) > 40)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'ID_TEXT':
            if ((!is_string($value)) || (strlen($value) > 80)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'LANGUAGE_NAME':
            global $LANG_TD_MAP;
            require_code('files');
            if ($LANG_TD_MAP === null) {
                $LANG_TD_MAP = cms_parse_ini_file_fast(get_file_base() . '/lang/langs.ini');
            }
            if ((!is_string($value)) || (!array_key_exists($value, $LANG_TD_MAP))) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'IP':
            if ((!is_string($value)) || (strlen($value) > 40) || ((strlen($value) < 7) && ($value != '')) || ((count(explode('.', $value)) != 4) && ($value != '') && (count(explode(':', $value)) < 3))) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'SHORT_TEXT':
            if ((!is_string($value)) || (strlen($value) > 255)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'SHORT_INTEGER':
            if ((!is_integer($value)) || ($value > 255) || ($value < 0)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'AUTO_LINK':
            if ((!is_integer($value)) || ($value < -1)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value); // -1 means something different to null
            }
            break;
        case 'BINARY':
            if ((!is_integer($value)) || (($value != 0) && ($value != 1))) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'MEMBER':
            if ((!is_integer($value)) || ($value < $GLOBALS['FORUM_DRIVER']->get_guest_id())) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
        case 'TIME':
            if ((!is_integer($value)) || ($value > time() + 500000000)) {
                _fail_test_value_matches_type($phpdoc_type, $function_name, $name, $value);
            }
            break;
    }
}

/**
 * Throw out a type checker error message.
 *
 * @param  string $phpdoc_type The phpdoc type involved
 * @param  string $function_name The function involved
 * @param  string $name The parameter name involved
 * @param  string $value The value involved
 */
function _fail_test_value_matches_type(string $phpdoc_type, string $function_name, string $name, string $value)
{
    $str = 'A type value (' . (is_string($value) ? $value : strval($value)) . ') was used with the function ' . $function_name . ' (parameter ' . $name . '), causing a type mismatch error';
    attach_message($str, 'warn');
}

/**
 * Special low-level do_lang for phpdoc / webstandards / global.
 *
 * @param  ID_TEXT $x The language codename
 * @param  string $a First parameter
 * @param  string $b Second parameter
 * @param  string $c Third parameter
 * @return string The translated string
 */
function do_lang__phpdoc(string $x, string $a = '', string $b = '', string $c = '') : string
{
    global $PHPDOC_LANG_PARSED;
    if (!isset($PHPDOC_LANG_PARSED)) {
        $temp = file_get_contents(__DIR__ . '/../lang_custom/EN/phpdoc.ini') . file_get_contents(__DIR__ . '/../lang/EN/webstandards.ini') . file_get_contents(__DIR__ . '/../lang/EN/global.ini');
        $temp_2 = explode("\n", $temp);
        $PHPDOC_LANG_PARSED = [];
        foreach ($temp_2 as $p) {
            $pos = strpos($p, '=');
            if ($pos !== false) {
                $PHPDOC_LANG_PARSED[substr($p, 0, $pos)] = substr($p, $pos + 1);
            }
        }
    }
    $out = strip_tags(str_replace('{1}', $a, str_replace('{2}', $b, $PHPDOC_LANG_PARSED[$x])));
    if (is_array($c)) {
        $out = @str_replace('{3}', $c[0], $out);
        $out = @str_replace('{4}', $c[1], $out);
        $out = @str_replace('{5}', $c[2], $out);
        $out = @str_replace('{6}', $c[3], $out);
    } else {
        $out = str_replace('{3}', $c, $out);
    }
    return rtrim($out);
}

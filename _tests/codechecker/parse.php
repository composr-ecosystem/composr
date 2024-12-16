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

function parse($_tokens = null)
{
    ini_set('xdebug.max_nesting_level', '2000');

    global $TOKENS, $I, $OK_EXTRA_FUNCTIONS;
    $OK_EXTRA_FUNCTIONS = null;
    if ($_tokens !== null) {
        $TOKENS = $_tokens;
    }
    $I = 0;
    $structure = _parse_php($TOKENS);
    $structure['ok_extra_functions'] = $OK_EXTRA_FUNCTIONS;
    global $FILENAME;
    if ((!empty($structure['main'])) && (substr($FILENAME, 0, 7) == 'sources') && ($FILENAME != 'sources/bootstrap.php') && ($FILENAME != 'sources/global.php') && ($FILENAME != 'data/static_cache.php') && ($FILENAME != 'sources/critical_errors.php') && ((count($structure['main']) > 1) || (($structure['main'][0][0] != 'RETURN') && (($structure['main'][0][0] != 'CALL_DIRECT') || ($structure['main'][0][1] != 'require_code'))))) {
        if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
            log_warning('Sources files should not contain loose code');
        }
    }

    return $structure;
}

global $FUNCTIONS;
$FUNCTIONS = [];
global $CLASSES;
$CLASSES = [];
global $MAIN;

function _parse_php($inside_namespace = false)
{
    // Choice{"FUNCTION" "IDENTIFIER "PARENTHESIS_OPEN" comma_parameters "PARENTHESIS_CLOSE" command | "CLASS" "IDENTIFIER" ("EXTENDS" "IDENTIFIER")? "CURLY_OPEN" class_contents "CURLY_CLOSE" | command}*

    $next = pparse__parser_peek();
    $program = [];
    $program['declares'] = [];
    $program['functions'] = [];
    $program['classes'] = [];
    $program['main'] = [];
    $program['uses'] = [];
    $found_namespace = false;
    $modifiers = [];
    while ($next !== null) {
        switch ($next) {
            case 'DECLARE':
                if (count($program['functions']) + count($program['classes']) + count($program['main']) + count($program['uses']) > 0 || $found_namespace) {
                    log_warning('Declare rules must come first');
                }

                pparse__parser_next();
                pparse__parser_expect('PARENTHESIS_OPEN');
                $key = pparse__parser_expect('IDENTIFIER');
                pparse__parser_expect('EQUAL');
                if (in_array($key, ['encoding'])) {
                    $value = pparse__parser_expect('string_literal');
                } else {
                    $value = pparse__parser_expect('integer_literal');
                }
                pparse__parser_expect('PARENTHESIS_CLOSE');
                pparse__parser_expect('COMMAND_TERMINATE');
                if (isset($program['declares'][$key])) {
                    log_warning('Repeated declare for ' . $key);
                }
                if (!in_array($key, ['ticks', 'strict_types', 'encoding'])) {
                    log_warning('Unknown declare, ' . $key);
                }
                $program['declares'][$key] = $value;
                break;

            case 'NAMESPACE':
                if (count($program['functions']) + count($program['classes']) + count($program['main']) + count($program['uses']) > 0) {
                    log_warning('Namespaces must come first');
                }

                pparse__parser_next();
                if (pparse__parser_peek() == 'COMMAND_TERMINATE') {
                    // namespace;
                    $key = null;
                    pparse__parser_expect('COMMAND_TERMINATE');
                } else {
                    if (pparse__parser_peek() == 'IDENTIFIER') {
                        $key = pparse__parser_expect('IDENTIFIER');
                        if (pparse__parser_peek() == 'COMMAND_TERMINATE') {
                            // namespace my\name;
                            pparse__parser_expect('COMMAND_TERMINATE');
                        } else {
                            // namespace my\name { }
                            pparse__parser_expect('CURLY_OPEN');
                            $temp = _parse_php(true);
                            if (!empty($temp['declares'])) {
                                log_warning('Declare cannot be done within a namespace');
                            }
                            $program['functions'] = array_merge($program['functions'], $temp['functions']);
                            $program['classes'] = array_merge($program['classes'], $temp['classes']);
                            $program['main'] = array_merge($program['main'], $temp['main']);
                            $program['uses'] = array_merge($program['uses'], $temp['uses']);
                            pparse__parser_expect('CURLY_CLOSE');
                        }
                    } else {
                        // namespace {}
                        $key = null;
                        pparse__parser_expect('CURLY_OPEN');
                        $temp = _parse_php(true);
                        if (!empty($temp['declares'])) {
                            log_warning('Declare cannot be done within a namespace');
                        }
                        $program['functions'] = array_merge($program['functions'], $temp['functions']);
                        $program['classes'] = array_merge($program['classes'], $temp['classes']);
                        $program['main'] = array_merge($program['main'], $temp['main']);
                        $program['uses'] = array_merge($program['uses'], $temp['uses']);
                        pparse__parser_expect('CURLY_CLOSE');
                    }
                }

                $found_namespace = true;

                // We don't actually process/check namespaces currently, just parse them

                break;

            case 'USE':
                pparse__parser_next();
                $uses = _parse_namespace_use_list();
                $program['uses'] = $uses;
                pparse__parser_expect('COMMAND_TERMINATE');
                break;

            case 'ABSTRACT':
                if (!empty($modifiers)) {
                    if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                        log_warning('Abstract keyword must appear first: ' . implode(', ', $modifiers) . ', abstract');
                    }
                }
                pparse__parser_next();
                switch (pparse__parser_peek()) {
                    case 'CLASS':
                    case 'TRAIT':
                        $modifiers[] = 'abstract';
                        break;
                    default:
                        // This is an invalid token to appear after "abstract"
                        log_warning('Only classes/traits and their methods can be abstract, not ' . pparse__parser_peek());
                        $modifiers = [];
                        break;
                }
                break;

            case 'CLASS':
                $class = _parse_class_def($modifiers);
                foreach ($program['classes'] as $_) {
                    if ($_['name'] == $class['name']) {
                        log_warning('Duplicated class/interface/trait ' . $class['name']);
                    }
                }
                $program['classes'][$class['name']] = $class;
                $modifiers = [];
                break;

            case 'INTERFACE':
                $class = _parse_interface_def($modifiers);
                foreach ($program['classes'] as $_) {
                    if ($_['name'] == $class['name']) {
                        log_warning('Duplicated class/interface/trait ' . $class['name']);
                    }
                }
                $program['classes'][] = $class;
                $modifiers = [];
                break;

            case 'TRAIT':
                $class = _parse_trait_def($modifiers);
                foreach ($program['classes'] as $_) {
                    if ($_['name'] == $class['name']) {
                        log_warning('Duplicated class/interface/trait ' . $class['name']);
                    }
                }
                $program['classes'][] = $class;
                $modifiers = [];
                break;

            case 'FUNCTION':
                $_function = _parse_function_def();
                foreach ($program['functions'] as $_) {
                    if ($_['name'] == $_function['name']) {
                        log_warning('Duplicated function \'' . $_function['name'] . '\'');
                    }
                }
                //log_special('defined', $_function['name']);
                $program['functions'][] = $_function;
                break;

            case 'CURLY_CLOSE':
                if ($inside_namespace) {
                    return $program;
                }
                // no break

            default:
                $program['main'] = array_merge($program['main'], _parse_command());
                break;
        }

        $next = pparse__parser_peek();
    }
    return $program;
}

function _parse_namespace_use_list($is_generalised = true)
{
    $uses = [];
    do {
        $next = pparse__parser_peek();
        if (in_array($next, ['FUNCTION', 'CONST'])) {
            pparse__parser_next();
            $type = $next;
        } else {
            $type = 'namespace';
        }
        $key = pparse__parser_expect('IDENTIFIER');
        if (substr($key, -1) == '\\') {
            pparse__parser_expect('CURLY_OPEN');

            $subuses = _parse_namespace_use_list($type == 'namespace');
            foreach ($subuses as $subuse) {
                $subuse[1] = $key . $subuse[1];
                $uses[] = $subuse;
            }

            pparse__parser_expect('CURLY_CLOSE');
        } else {
            if (pparse__parser_peek() == 'AS') {
                pparse__parser_next();
                $key_new = pparse__parser_expect('IDENTIFIER');
            } else {
                $key_new = null;
            }
            $uses[] = [$type, $key, $key_new];
        }

        if (pparse__parser_peek() == 'COMMA') {
            if ((pparse__parser_peek() == 'COMMAND_TERMINATE') || (pparse__parser_peek() == 'CURLY_CLOSE')) {
                break;
            }

            pparse__parser_next();
        } else {
            break;
        }
    } while (true);
    return $uses;
}

function _parse_class_def($modifiers = [])
{
    global $FUNCTION_SIGNATURES, $KNOWN_EXTRA_INTERFACES, $KNOWN_EXTRA_CLASSES, $OK_EXTRA_FUNCTIONS;

    $class = ['type' => 'class', 'superclass' => null, 'interfaces' => []];
    if (!empty($modifiers)) {
        $class['modifiers'] = $modifiers;
    }
    pparse__parser_next();
    $class['name'] = pparse__parser_expect('IDENTIFIER');
    $next = pparse__parser_peek();
    if ($next == 'EXTENDS') {
        pparse__parser_expect('EXTENDS');
        $superclass = pparse__parser_expect('IDENTIFIER');
        $class['superclass'] = $superclass;
        $next = pparse__parser_peek();

        if (isset($FUNCTION_SIGNATURES[$superclass])) {
            if ($FUNCTION_SIGNATURES[$superclass]['type'] != 'class') {
                log_warning('Class trying to extend a non-class, ' . $superclass);
            }
        } elseif (!empty($GLOBALS['FLAG__API'])) {
            if (isset($KNOWN_EXTRA_INTERFACES[$superclass])) {
                log_warning('Class trying to extend a non-class, ' . $superclass);
            } elseif (!isset($KNOWN_EXTRA_CLASSES[$superclass])) {
                if ((($OK_EXTRA_FUNCTIONS === null) || (preg_match('#^' . $OK_EXTRA_FUNCTIONS . '#', $superclass) == 0))) {
                    log_warning('Could not find class ' . $superclass);
                }
            }
        }
    }
    if ($next == 'IMPLEMENTS') {
        pparse__parser_expect('IMPLEMENTS');
        $interface = pparse__parser_expect('IDENTIFIER');
        $class['interfaces'][] = $interface;

        if (isset($FUNCTION_SIGNATURES[$interface])) {
            if ($FUNCTION_SIGNATURES[$interface]['type'] != 'interface') {
                log_warning('Trying to implement a non-interface, ' . $interface);
            }
        } elseif (!empty($GLOBALS['FLAG__API'])) {
            if (isset($KNOWN_EXTRA_CLASSES[$interface])) {
                log_warning('Trying to implement a non-interface, ' . $interface);
            } elseif (!isset($KNOWN_EXTRA_INTERFACES[$interface])) {
                log_warning('Could not find interface ' . $interface);
            }
        }
    }
    pparse__parser_expect('CURLY_OPEN');
    $_class = _parse_class_contents($modifiers, 'class');
    $class = array_merge($class, $_class);
    pparse__parser_expect('CURLY_CLOSE');

    return $class;
}

function _parse_interface_def($modifiers = [])
{
    global $FUNCTION_SIGNATURES, $KNOWN_EXTRA_INTERFACES, $KNOWN_EXTRA_CLASSES;

    $class = ['type' => 'interface', 'superclass' => null];
    if (!empty($modifiers)) {
        $class['modifiers'] = $modifiers;
    }
    pparse__parser_next();
    $class['name'] = pparse__parser_expect('IDENTIFIER');
    $next = pparse__parser_peek();
    if ($next == 'EXTENDS') {
        pparse__parser_expect('EXTENDS');
        $superclass = _parse_comma_expressions();
        $class['superclass'] = $superclass;
        $next = pparse__parser_peek();

        if (isset($FUNCTION_SIGNATURES[$superclass])) {
            if ($FUNCTION_SIGNATURES[$superclass]['type'] != 'interface') {
                log_warning('Interface trying to extend a non-interface, ' . $superclass);
            }
        } elseif (!empty($GLOBALS['FLAG__API'])) {
            if (isset($KNOWN_EXTRA_CLASSES[$superclass])) {
                log_warning('Interface trying to extend a non-interface, ' . $superclass);
            } elseif (isset($KNOWN_EXTRA_INTERFACES[$superclass])) {
                log_warning('Could not find interface ' . $superclass);
            }
        }
    }
    if ($next == 'IMPLEMENTS') {
        log_warning('Interfaces cannot implement each other, they can only extend each other');
    }
    pparse__parser_expect('CURLY_OPEN');
    $_class = _parse_class_contents($modifiers, 'interface');
    $class = array_merge($class, $_class);
    pparse__parser_expect('CURLY_CLOSE');

    return $class;
}

function _parse_trait_def($modifiers = [])
{
    $class = ['type' => 'trait'];
    if (!empty($modifiers)) {
        $class['modifiers'] = $modifiers;
    }
    pparse__parser_next();
    $class['name'] = pparse__parser_expect('IDENTIFIER');
    $next = pparse__parser_peek();
    if ($next == 'IMPLEMENTS' || $next == 'EXTENDS') {
        log_warning('Traits cannot extend/implement anything, they can only mixin other traits using use');
    }
    pparse__parser_expect('CURLY_OPEN');
    $_class = _parse_class_contents($modifiers, 'trait');
    $class = array_merge($class, $_class);
    pparse__parser_expect('CURLY_CLOSE');

    return $class;
}

function _parse_class_contents($class_modifiers = [], $type = 'class')
{
    // Choice{"VAR" "IDENTIFIER" "EQUAL" literal "COMMAND_TERMINATE" | "VAR" "IDENTIFIER" "COMMAND_TERMINATE" | function_dec}*

    global $FUNCTION_SIGNATURES, $KNOWN_EXTRA_CLASSES;

    $next = pparse__parser_peek();
    $class = ['functions' => [], 'vars' => [], 'constants' => [], 'traits' => [], 'traits_details_insteadof' => [], 'traits_details_as' => [], 'i' => $GLOBALS['I']];
    $modifiers = [];
    while (($next == 'CONST') || ($next == 'USE') || ($next == 'VAR') || ($next == 'FUNCTION') || ($next == 'PUBLIC') || ($next == 'PRIVATE') || ($next == 'PROTECTED') || ($next == 'ABSTRACT') || ($next == 'FINAL') || ($next == 'STATIC')) {
        switch ($next) {
            case 'USE':
                if ($type == 'interface') {
                    log_warning('Interfaces cannot use traits');
                }

                do {
                    pparse__parser_next();
                    $trait = pparse__parser_expect('IDENTIFIER');

                    if (in_array($trait, $class['traits'])) {
                        log_warning('Duplicated use of trait: ' . $trait);
                    }

                    $class['traits'][] = $trait;

                    if (isset($FUNCTION_SIGNATURES[$trait])) {
                        if ($FUNCTION_SIGNATURES[$trait]['type'] != 'trait') {
                            log_warning('Trying to \'use\' a non-trait, ' . $trait);
                        }
                    } elseif (!empty($GLOBALS['FLAG__API'])) {
                        if (!isset($KNOWN_EXTRA_CLASSES[$trait])) {
                            log_warning('Could not find trait ' . $trait);
                        } else {
                            log_warning('Trying to \'use\' a non-trait, ' . $trait);
                        }
                    }
                } while (pparse__parser_peek() == 'COMMA');

                if (pparse__parser_peek() == 'CURLY_OPEN') {
                    pparse__parser_next();
                    do {
                        $identifier_in_class = pparse__parser_expect('IDENTIFIER');
                        if (!in_array($identifier_in_class, $class['traits'])) {
                            log_warning('No trait for ' . $identifier_in_class);
                        }
                        pparse__parser_expect('SCOPE');
                        $identifier_method = pparse__parser_expect('IDENTIFIER');
                        if (pparse__parser_peek() == 'INSTEADOF') {
                            pparse__parser_expect('INSTEADOF');
                            $identifier_out_class = pparse__parser_expect('IDENTIFIER');
                            if (!in_array($identifier_out_class, $class['traits'])) {
                                log_warning('No trait for ' . $identifier_out_class);
                            }
                            $class['traits_details_insteadof'][] = [$identifier_in_class, $identifier_method, $identifier_out_class];
                        } else {
                            pparse__parser_expect('AS');
                            $identifier_in_method = pparse__parser_expect('IDENTIFIER');
                            $class['traits_details_as'][] = [$identifier_in_class, $identifier_method, $identifier_in_method];
                        }

                        pparse__parser_expect('COMMAND_TERMINATE');
                    } while (pparse__parser_peek() != 'CURLY_CLOSE');
                    pparse__parser_expect('CURLY_CLOSE');
                } else {
                    pparse__parser_expect('COMMAND_TERMINATE');
                }
                break;

            case 'PRIVATE':
            case 'PROTECTED':
                if ($type == 'interface') {
                    log_warning('Interfaces cannot contain anything protected or private');
                }
                // no break
            case 'PUBLIC':
                if (in_array('public', $modifiers) || in_array('private', $modifiers) || in_array('protected', $modifiers)) {
                    log_warning('Multiple visibility levels defined: ' . implode(', ', $modifiers) . ', ' . $next);
                }
                $modifiers[] = strtolower($next);
                if ((pparse__parser_peek_dist(1) == 'FUNCTION') || (pparse__parser_peek_dist(1) == 'STATIC') || (pparse__parser_peek_dist(1) == 'ABSTRACT') || (pparse__parser_peek_dist(1) == 'FINAL')) {
                    if (pparse__parser_peek_dist(1) == 'ABSTRACT') {
                        log_warning('Abstract keyword must appear first: ' . implode(', ', $modifiers) . ', abstract');
                    }

                    // Variables fall through to VAR, functions don't
                    pparse__parser_next(); // VAR does this in its do-while loop
                    break;
                }
                // no break

            case 'VAR':
                if ($next == 'VAR') {
                    log_warning('Don\'t use the var keyword anymore, it is deprecated');
                }
                // no break

            case 'STATIC':
            case 'ABSTRACT':
            case 'FINAL':
                if (in_array($next, ['STATIC', 'ABSTRACT', 'FINAL'])) {
                    if ($next == 'ABSTRACT') {
                        if ($type == 'interface') {
                            log_warning('Everything in an interface is inherently abstract. Do not use the abstract keyword');
                        }
                        if ($type == 'trait') {
                            log_warning('Traits are inherently abstract, do not use the abstract keyword');
                        }

                        $modifiers[] = 'abstract';
                        if (!in_array('abstract', $class_modifiers)) {
                            log_warning('Abstract keyword found in a non-abstract class');
                        }
                    } elseif ($next == 'STATIC') {
                        if (empty($modifiers)) {
                            if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                                log_warning('Static keyword must not appear before visibility');
                            }
                        }
                        $modifiers[] = 'static';
                    } else {
                        $modifiers[] = 'final';
                    }
                    // Peek ahead to make sure the next token can be abstract
                    switch (pparse__parser_peek_dist(1)) {
                        case 'PUBLIC':
                        case 'PRIVATE':
                        case 'PROTECTED':
                            // If we're followed by another modifier, peek ahead further
                            switch (pparse__parser_peek_dist(2)) {
                                case 'FUNCTION':
                                    // Valid
                                    break;
                                case 'variable':
                                case 'VAR':
                                    if ($next == 'ABSTRACT') {
                                        // Invalid
                                        log_warning('Abstract keyword applied to member variable');
                                        break;
                                    }
                                    // no break
                                default:
                                    // Invalid
                                    log_warning('Visibility keywords are only valid for functions and member variables, not ' . pparse__parser_peek_dist(1));
                                    break;
                            }
                            break;
                        case 'FUNCTION':
                            // Valid
                            break;
                        case 'variable':
                        case 'VAR':
                            if ($next != 'STATIC') {
                                log_warning($next . ' keyword applied to member variable');
                            }
                            break;
                        case 'STATIC':
                            break;
                        case 'ABSTRACT':
                            break;
                        case 'FINAL':
                            break;
                        default:
                            log_warning('The ' . $next . ' keyword only applies to classes and methods, not ' . pparse__parser_peek_dist(1));
                            break;
                    }

                    if (($next == 'STATIC') && (pparse__parser_peek_dist(1) == 'variable')) {
                        // Fall through to VAR
                    } else {
                        pparse__parser_next(); // Consume the static/abstract/final keyword
                        break;
                    }
                }
                // no break

            case 'CONST': // Lots flows to here, used for parsing properties & constants
                if ($next == 'CONST') {
                    if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                        log_warning('PSR-12 says you should always specify class constant visibility');
                    }
                    $is_const = true;
                } else {
                    if (pparse__parser_peek_dist(1) == 'CONST') {
                        $is_const = true;
                        pparse__parser_next();
                    } else {
                        $is_const = false;
                    }
                }

                do {
                    pparse__parser_next();
                    if ($is_const) {
                        $identifier = pparse__parser_expect('IDENTIFIER');

                        if ((!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) && (@strtoupper($identifier) != $identifier)) {
                            log_warning('Constants should be upper case');
                        }
                    } else {
                        $identifier = pparse__parser_expect('variable');
                    }

                    foreach ($class[$is_const ? 'constants' : 'vars'] as $class_member) {
                        if ($class_member[0] == $identifier) {
                            log_warning('Duplicated class member: ' . $identifier);
                        }
                    }

                    $next_2 = pparse__parser_peek();
                    if ($next_2 == 'EQUAL') {
                        pparse__parser_next();

                        $expression =  _parse_expression();

                        $class[$is_const ? 'constants' : 'vars'][] = [$identifier, $expression];
                    } else {
                        $class[$is_const ? 'constants' : 'vars'][] = [$identifier, ['SOLO', ['LITERAL', ['null']], $GLOBALS['I']]];
                    }

                    $next_2 = pparse__parser_peek();

                    if ($next_2 == 'COMMA') {
                        if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                            log_warning('PSR-12: Don\'t define multiple class properties/constants on a single line');
                        }
                    }
                } while ($next_2 == 'COMMA');

                pparse__parser_expect('COMMAND_TERMINATE');
                $modifiers = [];
                break;

            case 'FUNCTION':
                if ((!in_array('private', $modifiers)) && (!in_array('protected', $modifiers)) && (!in_array('public', $modifiers))) {
                    log_warning('You must specify function visibility (e.g. public)');
                }

                if (($type == 'interface') && (in_array('private', $modifiers))) {
                    log_warning('All methods in an interface must be public or protected');
                }
                if (($type == 'interface') && (in_array('abstract', $modifiers))) {
                    log_warning('Everything in an interface is inherently abstract. Do not use the abstract keyword.');
                }
                if (($type == 'interface') && (in_array('final', $modifiers))) {
                    log_warning('Do not use the final keyword on an interface.');
                }
                $_function = _parse_function_def(array_merge($modifiers, ($type == 'interface') ? ['abstract'] : [])); // Interface methods are inherently abstract
                foreach ($class['functions'] as $_) {
                    if ($_['name'] == $_function['name']) {
                        log_warning('Duplicated method \'' . $_function['name'] . '\'');
                    }
                }
                $class['functions'][] = $_function;

                if ((in_array('static', $modifiers)) && (in_array('abstract', $modifiers))) {
                    log_warning('Cannot mix static and abstract');
                }
                if ((in_array('final', $modifiers)) && (in_array('abstract', $modifiers))) {
                    log_warning('Cannot mix final and abstract');
                }
                if ((in_array('final', $modifiers)) && (in_array('private', $modifiers))) {
                    log_warning('Cannot mix final and private (as it would be meaningless)');
                }

                $modifiers = [];
                break;

            default:
                parser_error('Expected <class_contents> but got ' . $next);
        }

        $next = pparse__parser_peek();
    }

    return $class;
}

function _parse_function_def($function_modifiers = [], $is_closure = false)
{
    $function = [];
    $function['offset'] = $GLOBALS['I'];

    pparse__parser_expect('FUNCTION');
    if (pparse__parser_peek() == 'REFERENCE') {
        pparse__parser_next();
    }

    if (!$is_closure) {
        $function['name'] = pparse__parser_expect('IDENTIFIER');
    }

    pparse__parser_expect('PARENTHESIS_OPEN');
    $function['parameters'] = _parse_comma_parameters();
    pparse__parser_expect('PARENTHESIS_CLOSE');

    if (pparse__parser_peek() == 'COLON') {
        pparse__parser_next();

        $next = pparse__parser_next(true);

        if ($next[0] == 'QUESTION') {
            $is_nullable = true;
            $next = pparse__parser_next(true);
        } else {
            $is_nullable = false;
        }

        switch ($next[0]) {
            // Type hints
            case 'ARRAY':
            case 'BOOL':
            case 'CALLABLE':
            case 'FLOAT':
            case 'INT':
            case 'ITERABLE':
            case 'OBJECT':
            case 'STRING':
            case 'VOID':
                $hint = $next[0];
                break;
            case 'IDENTIFIER':
                $hint = $next[1];
                break;
        }
    } else {
        $hint = null;
        $is_nullable = null;
    }

    $function['using'] = [];
    if ($is_closure) {
        if (pparse__parser_peek() == 'USE') {
            pparse__parser_next();
            pparse__parser_expect('PARENTHESIS_OPEN');
            $function['using'] = _parse_comma_variables('PARENTHESIS_CLOSE');
            pparse__parser_expect('PARENTHESIS_CLOSE');
        }
    }

    if (in_array('abstract', $function_modifiers)) {
        pparse__parser_expect('COMMAND_TERMINATE');
        $function['code'] = [];
    } else {
        $function['code'] = _parse_command();
    }

    $function['modifiers'] = $function_modifiers;

    $function['hint'] = [$hint, $is_nullable];

    return $function;
}

function _parse_command($needs_brace = false, &$is_braced = null)
{
    // Choice{"CURLY_OPEN" command* "CURLY_CLOSE" | command_actual "COMMAND_TERMINATE"*}

    $is_braced = false;

    $next = pparse__parser_peek();
    $command = [];
    switch ($next) {
        case 'CURLY_OPEN':
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            while (true) {
                switch ($next_2) {
                    case 'CURLY_CLOSE':
                        $is_braced = true;
                        pparse__parser_next();
                        break 2;

                    default:
                        $command = array_merge($command, _parse_command());
                        break;
                }
                $next_2 = pparse__parser_peek();
            }
            break;

        default:
            if ($needs_brace !== false) {
                if (($needs_brace === true) || ($next != 'IF')/*If not merely an ELSE IF*/) {
                    parser_warning('PSR-2 asks us to use braces for all control structures');
                }
            }

            $_is_braced = null;
            $new_command = _parse_command_actual(false, $_is_braced);

            // This is now a bit weird. Not all commands end with a COMMAND_TERMINATE, and those are actually for the commands to know they're finished (and the ones requiring would have complained if they were missing). Therefore we now just skip any semicolons. There can be more than one, it's valid, albeit crazy.
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'BOOLEAN_OR_2') { // Possibility of error handling
                pparse__parser_next();
                $error_command = _parse_command_actual();
                $new_command[] = $error_command;
                pparse__parser_expect('COMMAND_TERMINATE');
                $next_2 = pparse__parser_peek();
            }
            $command[] = $new_command;
            $term_count = 0;
            while ($next_2 == 'COMMAND_TERMINATE') {
                pparse__parser_next();
                $next_2 = pparse__parser_peek();
                $term_count++;
            }
            if ($term_count > ($_is_braced ? 0 : 1)) {
                parser_warning('Excess of semicolons');
            }

            break;
    }
    return $command;
}

function _test_command_end()
{
    $next = pparse__parser_peek();

    if (($next != 'BOOLEAN_OR_2') && ($next != 'COMMAND_TERMINATE')) {
        parser_error('Bad command termination');
    }

    global $TOKENS, $I, $TEXT;
    if (($next != 'BOOLEAN_OR_2') && (strpos($TEXT, '?' . '>') !== false)) {
        if ((isset($TOKENS[$I + 1])) && (!in_array($TOKENS[$I + 1][0], ['START_ML_COMMENT', 'COMMENT', 'comment']))) {
            $between_tokens = substr($TEXT, end($TOKENS[$I]), end($TOKENS[$I + 1]) - end($TOKENS[$I]));
            if (strpos($between_tokens, "\n") === false) {
                parser_warning('Multiple commands on one line, violates PSR-12, ' . $TOKENS[$I + 1][0]);
            }
        }
    }
}

function _parse_command_actual($no_term_needed = false, &$is_braced = null)
{
    // Choice{"FUNCTION" | variable "DEC" | variable "INC" | target assignment_operator expression | function "PARENTHESIS_OPEN" comma_expressions "PARENTHESIS_CLOSE" | "IF" expression command if_rest? | "SWITCH" expression "CURLY_OPEN" cases "CURLY_CLOSE" | "FOREACH" "PARENTHESIS_OPEN" expression "AS" _foreach "PARENTHESIS_CLOSE" command | "FOR" "PARENTHESIS_OPEN" command expression command "PARENTHESIS_CLOSE" command | "DO" command "WHILE" "PARENTHESIS_OPEN" expression "PARENTHESIS_CLOSE" | "WHILE" "PARENTHESIS_OPEN" expression "PARENTHESIS_CLOSE" command | "RETURN" | "CONTINUE" | "BREAK" | "BREAK" expression | "CONTINUE" expression | "RETURN" expression | "GLOBAL" comma_variables | "ECHO" expression}

    $is_braced = false;

    $is_static = false;

    $next = pparse__parser_peek(true);

    if ($next === null) {
        parser_error('Unexpected end of input');
    }

    $suppress_error = ($next[0] == 'SUPPRESS_ERROR');
    if ($suppress_error) {
        if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
            log_warning('Avoid error suppression unless you absolutely have to use it; this may hide bugs. Use try / catch instead if you can.');
        }

        pparse__parser_next();
        $next = pparse__parser_peek(true);
    }

    if ($next[0] == 'STATIC') {
        global $I;
        $I++;
        $next_2 = pparse__parser_peek();
        $I--;
        if ($next_2 == 'SCOPE') {
            $next = ['IDENTIFIER', 'static' , $GLOBALS['I']]; // Like static::FOO, a way to avoid writing in class name. But it conflicts with static variable declaration syntax so we need to adjust it.
        }
    }

    switch ($next[0]) {
        case 'CLASS':
            $command = ['INNER_CLASS', _parse_class_def(), $GLOBALS['I']];
            break;

        case 'FUNCTION':
            $command = ['INNER_FUNCTION', _parse_function_def(), $GLOBALS['I']];
            break;

        case 'DEC':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $command = ['PRE_DEC', $variable, $GLOBALS['I']];
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'INC':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $command = ['PRE_INC', $variable, $GLOBALS['I']];
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'STATIC': // Moves past
            pparse__parser_next();
            $is_static = true;
            $static_set = [];
            // no break

        case 'variable':
            do {
                $target = _parse_target();
                $next_2 = pparse__parser_peek();
                switch ($next_2) {
                    case 'DEC':
                        if ($is_static) {
                            parser_error('Cannot decrement during static initialisation');
                        }

                        if (($target[0] == 'LIST') || ($target[0] == 'ARRAY_APPEND')) {
                            parser_error('LIST is only a one way type'); // We needed to read a target (for assignment), but we really wanted a variable (subset of target) -- we ended up with something that WAS target but NOT variable (we couldn't have known till now)
                        }
                        pparse__parser_next();
                        $command = ['DEC', $target, $GLOBALS['I']];
                        break;

                    case 'INC':
                        if ($is_static) {
                            parser_error('Cannot increment during static initialisation');
                        }

                        if (($target[0] == 'LIST') || ($target[0] == 'ARRAY_APPEND')) {
                            parser_error('LIST is only a one way type'); // We needed to read a target (for assignment), but we really wanted a variable (subset of target) -- we ended up with something that WAS target but NOT variable (we couldn't have known till now)
                        }
                        pparse__parser_next();
                        $command = ['INC', $target, $GLOBALS['I']];
                        break;

                    default: // Either an assignment or an indirect function call or a method call
                        $command = $target;
                        // We should be at the end of a chain by here.
                        // We may still be an assignment, despire the $next_3 branch
                        // above. Handle this if so:
                        if (in_array(pparse__parser_peek(), ['EQUAL', 'CONCAT_EQUAL', 'DIV_EQUAL', 'MINUS_EQUAL', 'MUL_EQUAL', 'PLUS_EQUAL', 'BOR_EQUAL', 'SL_EQUAL', 'SR_EQUAL', 'BW_XOR_EQUAL', 'BW_AND_EQUAL', 'BW_OR_EQUAL', 'BW_NOT_EQUAL'], true)) {
                            $assignment = _parse_assignment_operator();
                            $expression = _parse_expression();
                            if ($is_static && $expression[0] != 'LITERAL' && $expression[0] != 'NEGATE' && $expression[0] != 'CREATE_ARRAY') {
                                parser_error('Can only use static with a literal (scalar) expression.');
                            }
                            $command = ['ASSIGNMENT', $assignment, $command, $expression, $GLOBALS['I']];
                            if ($is_static) {
                                $static_set[] = $command;
                            }
                        }
                        break;
                }

                if ($is_static) {
                    if (pparse__parser_peek() == 'COMMA') {
                        pparse__parser_next();
                        continue;
                    } else {
                        $command = ['STATIC_ASSIGNMENT', $static_set, $GLOBALS['I']];
                        break;
                    }
                }
            } while ($is_static);
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'IDENTIFIER': // Direct function call, or jump label
            pparse__parser_next();
            $identifier = $next[1];
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'COLON') {
                pparse__parser_next();
                $command = ['GOTO_LABEL', $GLOBALS['I']];
                break;
            }
            if ($next_2 == 'SCOPE') {
                pparse__parser_next();
                $command = _parse_command_actual(false);
                if ($command[0] == 'CALL_DIRECT') {
                    $command = ['CALL_METHOD', ['IDENTIFIER', /*class name*/$identifier, ['DEREFERENCE', ['VARIABLE', /*method name*/$command[1], [], $GLOBALS['I']], [], $GLOBALS['I']], $GLOBALS['I']], /*params*/$command[2], $GLOBALS['I']];
                } else {
                    $command = ['REFERENCE', $command, $GLOBALS['I']];
                }
            } else {
                pparse__parser_expect('PARENTHESIS_OPEN');
                $parameters = _parse_function_call();
                pparse__parser_expect('PARENTHESIS_CLOSE');
                $command = ['CALL_DIRECT', $identifier, $parameters, $suppress_error, $GLOBALS['I']];
            }
            while (pparse__parser_peek() == 'OBJECT_OPERATOR' || pparse__parser_peek() == 'SCOPE') {
                pparse__parser_next();
                $command = _parse_call_chain($command, $suppress_error);
            }
            //log_special('functions', $identifier . '/' . count($parameters));
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'ECHO':
            pparse__parser_next();
            $parameters = _parse_comma_expressions();
            $command = ['ECHO', $parameters, $GLOBALS['I']];
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'EXTRACT_OPEN':
        case 'LIST':
            $target = _parse_target();
            pparse__parser_expect('EQUAL');
            $expression = _parse_expression();
            $command = ['ASSIGNMENT', 'EQUAL', $target, $expression, $GLOBALS['I']];
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'IF':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('PARENTHESIS_OPEN');
            $expression = _parse_expression();
            pparse__parser_expect('PARENTHESIS_CLOSE');
            $command = _parse_command(true, $is_braced);

            $next_2 = pparse__parser_peek();
            if (($next_2 == 'ELSE') || ($next_2 == 'ELSEIF')) {
                $if_rest = _parse_if_rest($is_braced);
                $command = ['IF_ELSE', $expression, $command, $if_rest, $c_pos];
            } else {
                $command = ['IF', $expression, $command, $c_pos];
            }
            break;

        case 'SWITCH':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            $expression = _parse_expression();
            pparse__parser_expect('CURLY_OPEN');
            $cases = _parse_cases();
            pparse__parser_expect('CURLY_CLOSE');
            $is_braced = true;
            $command = ['SWITCH', $expression, $cases, $c_pos];
            break;

        case 'FOREACH':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('PARENTHESIS_OPEN');
            $expression = _parse_expression();
            pparse__parser_expect('AS');
            // Choice{"variable" "DOUBLE_ARROW" "variable" | "variable"}
            $next = pparse__parser_peek();
            if ($next == 'LIST') {
                pparse__parser_next();
                pparse__parser_expect('PARENTHESIS_OPEN');
                $variable = ['LIST', _parse_target(), $GLOBALS['I']];
                pparse__parser_expect('PARENTHESIS_CLOSE');
            } else {
                $is_reference = ($next == 'REFERENCE');
                if ($is_reference) {
                    pparse__parser_next();
                }
                $variable = _parse_variable($suppress_error);
            }
            $after_variable = pparse__parser_peek();
            if ($after_variable == 'DOUBLE_ARROW') {
                if ($variable[0] == 'LIST') {
                    parser_error('list must be on RHS of =>');
                }

                pparse__parser_next();
                $next = pparse__parser_peek();
                if ($next == 'LIST') {
                    $_foreach = [$variable, _parse_target()];
                } else {
                    $is_reference = ($next == 'REFERENCE');
                    if ($is_reference) {
                        pparse__parser_next();
                    }
                    $_foreach = [$variable, _parse_variable($suppress_error)];
                }
            } else {
                $_foreach = $variable;
            }
            pparse__parser_expect('PARENTHESIS_CLOSE');
            $loop_command = _parse_command(true, $is_braced);
            if ($after_variable == 'DOUBLE_ARROW') {
                $command = ['FOREACH_map', $expression, $_foreach[0], $_foreach[1], $loop_command, $c_pos];
            } else {
                $command = ['FOREACH_list', $expression, $_foreach, $loop_command, $c_pos];
            }
            break;

        case 'FOR':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('PARENTHESIS_OPEN');
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'COMMAND_TERMINATE') {
                $init_command = null;
            } else {
                $init_command = _parse_command_actual(true);
            }
            pparse__parser_expect('COMMAND_TERMINATE');
            $control_expression = _parse_expression();
            pparse__parser_expect('COMMAND_TERMINATE');
            if (pparse__parser_peek() == 'PARENTHESIS_CLOSE') {
                $control_command = null;
            } else {
                $control_command = _parse_command_actual(true);
            }
            pparse__parser_expect('PARENTHESIS_CLOSE');
            if (pparse__parser_peek() == 'COMMAND_TERMINATE') {
                $loop_command = null;
            } else {
                $loop_command = _parse_command(true, $is_braced);
            }
            $command = ['FOR', $init_command, $control_expression, $control_command, $loop_command, $c_pos];
            break;

        case 'DO':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            $loop_command = _parse_command(true); // No $is_braced passed because there will need to be a 'WHILE' next
            pparse__parser_expect('WHILE');
            pparse__parser_expect('PARENTHESIS_OPEN');
            $control_expression = _parse_expression();
            pparse__parser_expect('PARENTHESIS_CLOSE');
            $command = ['DO', $control_expression, $loop_command, $c_pos];
            break;

        case 'WHILE':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('PARENTHESIS_OPEN');
            $control_expression = _parse_expression();
            pparse__parser_expect('PARENTHESIS_CLOSE');
            if (pparse__parser_peek() == 'COMMAND_TERMINATE') {
                $loop_command = null;
            } else {
                $loop_command = _parse_command(true, $is_braced);
            }
            $command = ['WHILE', $control_expression, $loop_command, $c_pos];
            break;

        case 'TRY':
            pparse__parser_next(); // Consume the "try"
            $try_position = $GLOBALS['I'];
            if (pparse__parser_peek() != 'CURLY_OPEN') {
                parser_error('Expected code block after "try".');
            }
            $try = _parse_command(true, $is_braced);
            $exception = null;
            $catches = [];
            do {
                pparse__parser_expect('CATCH');
                $catch_position = $GLOBALS['I'];

                pparse__parser_expect('PARENTHESIS_OPEN');
                pparse__parser_expect('IDENTIFIER'); // E.g. 'EXCEPTION'
                $exception = _parse_parameter();
                pparse__parser_expect('PARENTHESIS_CLOSE');
                if (pparse__parser_peek() != 'CURLY_OPEN') {
                    parser_error('Expected code block after "catch".');
                }

                $catch = _parse_command(true);
                $catches[] = ['CATCH', $exception, $catch, $catch_position];
            } while (pparse__parser_peek() == 'CATCH');
            if (pparse__parser_peek() == 'FINALLY') {
                pparse__parser_expect('FINALLY');
                $_finally = _parse_command(true);
                $finally_position = $GLOBALS['I'];
                $finally = [$_finally, $finally_position];
            } else {
                $finally = null;
            }
            $command = ['TRY', $try, $catches, $finally, $try_position];
            break;

        case 'THROW':
            pparse__parser_next(); // Consume the "throw"
            $expr = _parse_expression();
            $command = ['THROW', $expr, $GLOBALS['I']];
            break;

        case 'YIELD':
            pparse__parser_next();
            $next_2 = pparse__parser_peek(true);
            if (($next_2[0] == 'IDENTIFIER') && ($next_2[1] == 'from')) {
                pparse__parser_next();
                $expr = _parse_expression();
                $command = ['YIELD_FROM', $expr, $GLOBALS['I']];
            } else {
                switch ($next_2[0]) {
                    case 'COMMAND_TERMINATE':
                        $command = ['YIELD_0', $GLOBALS['I']];
                        break;

                    default:
                        $expr = _parse_expression();
                        if (pparse__parser_peek() == 'DOUBLE_ARROW') {
                            pparse__parser_next();
                            $expr2 = _parse_expression();
                            $command = ['YIELD_2', $expr, $expr2, $GLOBALS['I']];
                        } else {
                            $command = ['YIELD_1', $expr, $GLOBALS['I']];
                        }
                }
            }
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'RETURN':
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            switch ($next_2) {
                case 'COMMAND_TERMINATE':
                    $command = ['RETURN', null, $GLOBALS['I']];
                    break;

                default:
                    $command = ['RETURN', _parse_expression(), $GLOBALS['I']];
            }
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'CONTINUE':
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            switch ($next_2) {
                case 'COMMAND_TERMINATE':
                    $command = ['CONTINUE', ['SOLO', ['LITERAL', ['INTEGER', 1]], $GLOBALS['I']], $GLOBALS['I']];
                    break;

                default:
                    $command = ['CONTINUE', _parse_expression(), $GLOBALS['I']];
            }
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'BREAK':
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            switch ($next_2) {
                case 'COMMAND_TERMINATE':
                    $command = ['BREAK', ['SOLO', ['LITERAL', ['INTEGER', 1]], $GLOBALS['I']], $GLOBALS['I']];
                    break;

                default:
                    $command = ['BREAK', _parse_expression(), $GLOBALS['I']];
            }
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'GLOBAL':
            pparse__parser_next();
            $command = ['GLOBAL', _parse_comma_variables(), $GLOBALS['I']];
            if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                foreach ($command[1] as $variable) {
                    if (strtoupper($variable[1]) != $variable[1]) {
                        log_warning('Globalised variable ' . $variable[1] . ' is in non-canonical format');
                    }
                }
            }
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'GOTO':
            pparse__parser_next();
            $label = pparse__parser_expect('IDENTIFIER');
            $command = ['GOTO', $label, $GLOBALS['I']];
            if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                log_warning('There is rarely a good reason to use goto');
            }
            break;

        default:
            parser_error('Expected <command> but got ' . $next[0]);
    }
    return $command;
}

function _parse_call_chain($command = [], $suppress_error = false)
{
    $i = pparse__parser_expect('IDENTIFIER'); // Silly PHP syntax makes $scoped_variables and scoped_functions() different, but member_variables and member_functions() the same...
    switch (pparse__parser_peek()) {
        case 'PARENTHESIS_OPEN':
            pparse__parser_next(); // Consume the "("
            $args = _parse_function_call();
            pparse__parser_expect('PARENTHESIS_CLOSE'); // Consume the ")"
            $expression = $command; // Actually the 'command' was an expression, on which we will call our object
            $command = ['CALL_METHOD', $expression, $args, $suppress_error, $GLOBALS['I']];
            break;
        default:
            // Nothing of interest. Let the next pass handle it.
            break;
    }
    return $command;
}

function _parse_target()
{
    // Choice{variable | "EXTRACT_OPEN" comma_variables "EXTRACT_CLOSE" | "LIST" "PARENTHESIS_OPEN" comma_variables "PARENTHESIS_CLOSE" | "variable" "EXTRACT_OPEN" "EXTRACT_CLOSE"}

    $next = pparse__parser_peek();
    switch ($next) {
        case 'EXTRACT_OPEN':
            pparse__parser_expect('EXTRACT_OPEN');
            $target = ['LIST', _parse_comma_variables_target('EXTRACT_CLOSE'), $GLOBALS['I']];
            pparse__parser_expect('EXTRACT_CLOSE');
            break;

        case 'LIST':
            pparse__parser_next();
            pparse__parser_expect('PARENTHESIS_OPEN');
            $target = ['LIST', _parse_comma_variables_target('PARENTHESIS_CLOSE'), $GLOBALS['I']];
            pparse__parser_expect('PARENTHESIS_CLOSE');
            break;

        default:
            $variable = _parse_variable(false, true);
            $next = pparse__parser_peek();
            if ($next == 'EXTRACT_OPEN') {
                pparse__parser_next();
                pparse__parser_expect('EXTRACT_CLOSE');
                $target = ['ARRAY_APPEND', $variable, $GLOBALS['I']];
            } else {
                $target = $variable;
            }
    }
    return $target;
}

function _parse_if_rest(&$is_braced = null)
{
    // Choice{else command | elseif expression command if_rest?}

    $is_braced = false;

    $next = pparse__parser_peek();
    switch ($next) {
        case 'ELSE':
            pparse__parser_next();
            $command = _parse_command(null, $is_braced);
            $if_rest = $command;
            break;

        case 'ELSEIF':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('PARENTHESIS_OPEN');
            $expression = _parse_expression();
            pparse__parser_expect('PARENTHESIS_CLOSE');
            $command = _parse_command(true, $is_braced);
            $next_2 = pparse__parser_peek();
            if (($next_2 == 'ELSE') || ($next_2 == 'ELSEIF')) {
                $_if_rest = _parse_if_rest($is_braced);
                $if_rest = [['IF_ELSE', $expression, $command, $_if_rest, $c_pos]];
            } else {
                $if_rest = [['IF', $expression, $command, $c_pos]];
            }
            break;

        default:
            parser_error('Expected <if_rest> but got ' . $next);
    }
    return $if_rest;
}

function _parse_cases()
{
    // Choice{"CASE" expression "COLON" command* | "DEFAULT" "COLON" command*}*

    $next = pparse__parser_peek();
    $cases = [];
    while (($next == 'CASE') || ($next == 'DEFAULT')) {
        switch ($next) {
            case 'CASE':
                pparse__parser_next();
                $expression = _parse_expression();
                pparse__parser_expect('COLON');
                $next_2 = pparse__parser_peek();
                $commands = [];
                while (($next_2 != 'CURLY_CLOSE') && ($next_2 != 'CASE') && ($next_2 != 'DEFAULT')) {
                    $commands = array_merge($commands, _parse_command());
                    $next_2 = pparse__parser_peek();
                }
                if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                    if (count($commands) > 0) {
                        $last_command = $commands[count($commands) - 1];
                        if (!in_array($last_command[0], ['BREAK', 'CONTINUE', 'CASE', 'RETURN'])) {
                            global $TOKENS, $I;
                            if ((!isset($TOKENS[$I - 1])) || ($TOKENS[$I - 1][0] != 'comment') || (strpos($TOKENS[$I - 1][1], 'no break') === false)) {
                                log_warning('PSR-12: Missing break at end of case statement, and not marked with "no break" comment (last token was ' . $TOKENS[$I - 1][0] . ')');
                            }
                        }
                    }
                }
                foreach ($cases as $c) {
                    if (($c[0] !== null) && ($c[0][0] == 'LITERAL') && ($expression[0] == 'LITERAL') && ($c[0][1][1] == $expression[1][1])) {
                        log_warning('Duplicate case expression');
                    }
                }
                $cases[] = [$expression, $commands];
                break;

            case 'DEFAULT':
                pparse__parser_next();
                pparse__parser_expect('COLON');
                $next_2 = pparse__parser_peek();
                $commands = [];
                while (($next_2 != 'CURLY_CLOSE') && ($next_2 != 'CASE')) {
                    $commands += _parse_command();
                    $next_2 = pparse__parser_peek();
                }
                $cases[] = [null, $commands];
                break;

            default:
                parser_error('Expected <cases> but got ' . $next);
        }

        $next = pparse__parser_peek();
    }

    return $cases;
}

// In precedence order. Note REFERENCE==BW_AND (it gets converted, for clarity). Ditto QUESTION==TERNARY_IF
global $OPS;
$OPS = ['SPACESHIP', 'QUESTION_COALESCE', 'QUESTION', 'TERNARY_IF', 'BOOLEAN_XOR', 'BOOLEAN_OR', 'BOOLEAN_AND', 'REFERENCE', 'BW_OR', 'BW_XOR', 'BW_AND', 'IS_EQUAL', 'IS_NOT_EQUAL', 'IS_IDENTICAL', 'INSTANCEOF', 'IS_NOT_IDENTICAL', 'IS_SMALLER', 'IS_SMALLER_OR_EQUAL', 'IS_GREATER', 'IS_GREATER_OR_EQUAL', 'SL', 'SR', 'ADD', 'SUBTRACT', 'CONC', 'MULTIPLY', 'DIVIDE', 'REMAINDER', 'EXPONENTIATION'];

function _parse_expression()
{
    // Choice{expression_inner | expression_inner binary_operation expression_inner | expression_inner QUESTION expression_inner COLON expression_inner}

    global $OPS;

    $e_pos = $GLOBALS['I'];
    $expression = _parse_expression_inner();
    $op_list = [$expression];

    $next = pparse__parser_peek();
    while (in_array($next, $OPS)) {
        pparse__parser_next();
        if ($next == 'QUESTION') {
            $next_next = pparse__parser_peek();
            if ($next_next == 'COLON') {
                $expression_2 = $expression;
            } else {
                $expression_2 = _parse_expression();
            }
            pparse__parser_expect('COLON');
            $expression_3 = _parse_expression();
            $op_list[] = 'TERNARY_IF';
            $op_list[] = [$expression_2, $expression_3];
        } else {
            $expression_2 = _parse_expression_inner();
            if ($next == 'REFERENCE') {
                $next = 'BW_AND';
            }
            $op_list[] = $next;
            $op_list[] = $expression_2;
        }
        $next = pparse__parser_peek();
    }

    $op_tree = pparse__precedence_sort($op_list, $e_pos);
    return $op_tree;
}

function pparse__precedence_sort($op_list, $e_pos) // Oh my God, this is confusing as hell
{
    if (count($op_list) == 1) {
        return $op_list[0];
    }

    if (count($op_list) == 2) {
        $_e_pos = $op_list[0][count($op_list[0]) - 1];
        $new = [$op_list[1], $op_list[0], $op_list[2], $_e_pos];
        return $new;
    }

    global $OPS;

    foreach ($OPS as $op_try) {
        foreach ($op_list as $i => $op) {
            if ($i % 2 == 0) {
                continue;
            }
            if ($op == $op_try) {
                $left = array_slice($op_list, 0, $i);
                $right = array_slice($op_list, $i + 1);
                $_e_pos = $left[count($left) - 1][count($left[count($left) - 1]) - 1];
                $_left = pparse__precedence_sort($left, $_e_pos);
                $_right = pparse__precedence_sort($right, $_e_pos);
                return [$op, $_left, $_right, $_e_pos];
            }
        }
    }

    // Should never get here
    echo '!';
    print_r($op_list);
    return null;
}

function _parse_expression_inner()
{
    // Choice{"BOOLEAN_NOT expression | SUBTRACT expression | literal | variable | variable "PARENTHESIS_OPEN" comma_parameters "PARENTHESIS_CLOSE" | "IDENTIFIER" | "IDENTIFIER" "PARENTHESIS_OPEN" comma_parameters "PARENTHESIS_CLOSE" | "NEW" "IDENTIFIER" "PARENTHESIS_OPEN" comma_expressions "PARENTHESIS_CLOSE" | "NEW" "IDENTIFIER" | "CLONE" expression | "ARRAY" "PARENTHESIS_OPEN" create_array "PARENTHESIS_CLOSE" | "PARENTHESIS_OPEN" expression "PARENTHESIS_CLOSE" | "PARENTHESIS_OPEN" assignment "PARENTHESIS_CLOSE"}

    $next = pparse__parser_peek();
    if (in_array($next, ['integer_literal', 'float_literal', 'string_literal', 'true', 'false', 'null'])) { // little trick
        $next = '*literal';
    }
    $suppress_error = ($next == 'SUPPRESS_ERROR');
    if ($suppress_error) {
        if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
            log_warning('Avoid error suppression unless you absolutely have to use it; this may hide bugs. Use try / catch instead if you can.');
        }

        pparse__parser_next();
        $next = pparse__parser_peek();
    }

    if ($next == 'STATIC') {
        global $I;
        $I++;
        $next_2 = pparse__parser_peek();
        $I--;
        if ($next_2 == 'SCOPE') {
            $next = '_STATIC'; // Like static::FOO, a way to avoid writing in class name. But it conflicts with function definition syntax so we will give it a different label
        }
    }

    switch ($next) {
        case 'STATIC':
            pparse__parser_next();
            pparse__parser_expect('FUNCTION');
            $GLOBALS['I']--;
            // no break
        case 'FUNCTION':
            $_function = _parse_function_def([], true);
            $expression = ['CLOSURE', $_function, ($next == 'STATIC'), $GLOBALS['I']];
            break;

        case 'BW_NOT':
            pparse__parser_next();
            $_expression = _parse_expression_inner();
            $expression = ['BW_NOT', $_expression, $GLOBALS['I']];
            break;

        case 'BOOLEAN_NOT':
            pparse__parser_next();
            $_expression = _parse_expression_inner();
            $expression = ['BOOLEAN_NOT', $_expression, $GLOBALS['I']];
            break;

        case 'SUBTRACT':
            pparse__parser_next();
            $_expression = _parse_expression_inner();
            $expression = ['NEGATE', $_expression, $GLOBALS['I']];
            break;

        case '*literal':
            $literal = _parse_literal();
            $expression = ['LITERAL', $literal, $GLOBALS['I']];
            break;

        case '_STATIC':
        case 'IDENTIFIER':
            if ($next == '_STATIC') {
                $next = ['IDENTIFIER', 'static', $GLOBALS['I']];
            } else {
                $next = pparse__parser_peek(true);
            }
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'SCOPE') {
                pparse__parser_next();
                $expression = _parse_expression_inner();
                if ($expression[0] == 'CALL_DIRECT') {
                    $expression[0] = 'CALL_METHOD';
                    $identifier = $next[1];
                    $expression = ['CALL_METHOD', ['IDENTIFIER', /*class name*/$identifier, ['DEREFERENCE', ['VARIABLE', /*method name*/$expression[1], [], $GLOBALS['I']], [], $GLOBALS['I']], $GLOBALS['I']], /*params*/$expression[2], $GLOBALS['I']];
                } else {
                    $expression = [['CONSTANT', $next[1], $GLOBALS['I']], ['DEREFERENCE', $expression, []], $GLOBALS['I']];
                }
            } elseif ($next_2 == 'PARENTHESIS_OPEN') { // Is it an inline direct function call
                pparse__parser_next();
                $parameters = _parse_function_call();
                pparse__parser_expect('PARENTHESIS_CLOSE');
                $expression = ['CALL_DIRECT', $next[1], $parameters, $suppress_error, $GLOBALS['I']];
                //log_special('functions', $next[1] . '/' . count($parameters));
            } else {
                if (strtolower($next[1]) == $next[1]) {
                    if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                        log_warning('Lower case constant, breaks convention. Likely a variable with a missing $');
                    }
                }
                $expression = ['CONSTANT', $next[1], $GLOBALS['I']];
            }
            break;

        case 'NEW':
            pparse__parser_next();
            $identifier = pparse__parser_next(true);
            if (($identifier[0] != 'IDENTIFIER') && ($identifier[0] != 'variable') && ($identifier[0] != 'CLASS')) {
                parser_error('Expected IDENTIFIER or variable or CLASS but got ' . $identifier[0]);
            }
            if ($identifier[0] == 'CLASS') {
                // Anonymous class
                $class = ['type' => 'class', 'superclass' => null, 'interfaces' => [], 'name' => null];
                pparse__parser_expect('CURLY_OPEN');
                $_class = _parse_class_contents([], 'class');
                $class = array_merge($class, $_class);
                pparse__parser_expect('CURLY_CLOSE');

                $expression = ['NEW_ANONYMOUS_OBJECT', $class, $GLOBALS['I']];
            } else {
                $next_2 = pparse__parser_peek();
                if ($next_2 == 'PARENTHESIS_OPEN') {
                    pparse__parser_next();
                    $expressions = _parse_function_call();
                    pparse__parser_expect('PARENTHESIS_CLOSE');
                    $expression = ['NEW_OBJECT', ($identifier[0] == 'IDENTIFIER') ? $identifier[1] : null, $expressions, $GLOBALS['I']];
                } else {
                    parser_error('PSR-12: Expects parentheses for all new object instantiations');

                    $expression = ['NEW_OBJECT', ($identifier[0] == 'IDENTIFIER') ? $identifier[1] : null, [], $GLOBALS['I']];
                }
            }
            break;

        case 'CLONE':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $expression = ['CLONE_OBJECT', $variable, $GLOBALS['I']];
            break;

        case 'EXTRACT_OPEN': // Short array syntax
            pparse__parser_next();
            $details = _parse_create_array('EXTRACT_CLOSE');
            pparse__parser_expect('EXTRACT_CLOSE');
            $expression = ['CREATE_ARRAY', $details, $GLOBALS['I']];
            break;

        case 'ARRAY': // Long array syntax
            pparse__parser_next();
            pparse__parser_expect('PARENTHESIS_OPEN');
            $details = _parse_create_array('PARENTHESIS_CLOSE');
            pparse__parser_expect('PARENTHESIS_CLOSE');
            $expression = ['CREATE_ARRAY', $details, $GLOBALS['I']];

            if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                log_warning('Short array syntax is preferred');
            }

            break;

        case 'PARENTHESIS_OPEN':
            pparse__parser_next();

            // Look ahead to see if this is an embedded assignment or a cast
            $next_2 = pparse__parser_peek_dist(0);
            $next_3 = pparse__parser_peek_dist(1);
            if ($next_3 == 'EQUAL') {
                $target = _parse_variable($suppress_error);
                pparse__parser_expect('EQUAL');
                $_expression = _parse_expression();
                $expression = ['EMBEDDED_ASSIGNMENT', 'EQUAL', $target, $_expression, $GLOBALS['I']];
                pparse__parser_expect('PARENTHESIS_CLOSE');
            } elseif ((in_array($next_2, ['INTEGER', 'INT', 'BOOLEAN', 'BOOL', 'FLOAT', 'DOUBLE', 'REAL', 'ARRAY', 'OBJECT', 'STRING'])) && ($next_3 == 'PARENTHESIS_CLOSE')) {
                if (in_array($next_2, ['INTEGER', 'BOOLEAN', 'DOUBLE', 'REAL'])) {
                    if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                        log_warning('PSR-12: Don\'t use non-canonical casts');
                    }
                }

                pparse__parser_next();
                pparse__parser_next();
                if ($next_2 == 'INT') {
                    $next_2 = 'INTEGER';
                }
                if ($next_2 == 'BOOL') {
                    $next_2 = 'BOOLEAN';
                }
                $expression = ['CASTED', $next_2, _parse_expression_inner(), $GLOBALS['I']];
            } else {
                $expression = ['PARENTHESISED', _parse_expression(), $GLOBALS['I']];
                pparse__parser_expect('PARENTHESIS_CLOSE');
            }
            break;

        case 'REFERENCE':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $expression = ['VARIABLE_REFERENCE', $variable, $GLOBALS['I']];
            break;

        case 'DEC':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $expression = ['PRE_DEC', $variable, $GLOBALS['I']];
            if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                log_warning('Decrement used within expression is messy, put parentheses around it');
            }
            break;

        case 'INC':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $expression = ['PRE_INC', $variable, $GLOBALS['I']];
            if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                log_warning('Increment used within expression is messy, put parentheses around it');
            }
            break;

        case 'variable':
            $target = _parse_target();
            $next_2 = pparse__parser_peek();
            switch ($next_2) {
                case 'DEC':
                    if (($target[0] == 'LIST') || ($target[0] == 'ARRAY_APPEND')) {
                        parser_error('LIST is only a one way type'); // We needed to read a target (for assignment), but we really wanted a variable (subset of target) -- we ended up with something that WAS target but NOT variable (we couldn't have known till now)
                    }
                    pparse__parser_next();
                    $expression = ['DEC', $target, $GLOBALS['I']];
                    if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                        log_warning('Decrement used within expression is messy, put parentheses around it');
                    }
                    break;

                case 'INC':
                    if (($target[0] == 'LIST') || ($target[0] == 'ARRAY_APPEND')) {
                        parser_error('LIST is only a one way type'); // We needed to read a target (for assignment), but we really wanted a variable (subset of target) -- we ended up with something that WAS target but NOT variable (we couldn't have known till now)
                    }
                    pparse__parser_next();
                    $expression = ['INC', $target, $GLOBALS['I']];
                    if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                        log_warning('Increment used within expression is messy, put parentheses around it');
                    }
                    break;

                default: // Either an assignment or an indirect function call or a method call
                    $expression = $target;
                    // We should be at the end of a chain by here.
                    // We may still be an assignment, despire the $next_3 branch
                    // above. Handle this if so:
                    if (in_array(pparse__parser_peek(), ['EQUAL', 'CONCAT_EQUAL', 'DIV_EQUAL', 'MINUS_EQUAL', 'MUL_EQUAL', 'PLUS_EQUAL', 'BOR_EQUAL', 'SL_EQUAL', 'SR_EQUAL', 'BW_XOR_EQUAL', 'BW_AND_EQUAL', 'BW_OR_EQUAL', 'BW_NOT_EQUAL'], true)) {
                        $assignment = _parse_assignment_operator();
                        $expression_inner = _parse_expression();
                        $expression = ['ASSIGNMENT', $assignment, $expression, $expression_inner, $GLOBALS['I']];
                        if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                            log_warning('Assignment used within expression is messy, put parentheses around it');
                        }
                    }
                    break;
            }
            break;

        default: // By elimination: Must be a variable or a call chained to a variable. Actually this branch should not run due to 'variable' above being added in.
            $expression = _parse_variable($suppress_error, true);
    }

    $variable_chain = _parse_variable_dereferencing_chain_segment(false);
    if (!empty($variable_chain)) {
        $expression = ['EXPRESSION_CHAINING', $expression, $variable_chain, $GLOBALS['I']];
    }

    if (in_array($expression[0], ['CALL_DIRECT', 'CALL_INDIRECT', 'CALL_METHOD'], true)) {
        while (pparse__parser_peek() == 'OBJECT_OPERATOR' || pparse__parser_peek() == 'SCOPE') {
            pparse__parser_next();
            $expression = _parse_call_chain($expression, $suppress_error);
        }
    }

    return $expression;
}

function _parse_variable($suppress_error, $can_be_dangling_method_call_instead = false)
{
    // Choice{"variable" | "variable" "OBJECT_OPERATOR" variable | "variable" "OBJECT_OPERATOR" "IDENTIFIER" | "variable" "EXTRACT_OPEN" expression "EXTRACT_CLOSE"}

    $variable = pparse__parser_next(true);
    $next = pparse__parser_peek(true);
    if ($next === null) {
        parser_error('Expected variable ($) but reached end of file');
    }
    $suppress_error = $suppress_error || ($next[0] == 'SUPPRESS_ERROR');
    if ($next[0] == 'SUPPRESS_ERROR') {
        if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
            log_warning('Avoid error suppression unless you absolutely have to use it; this may hide bugs. Use try / catch instead if you can.');
        }

        pparse__parser_next();
        $next = pparse__parser_peek(true);
    }
    if ($variable[0] != 'variable') {
        parser_error('Expected variable ($) but got ' . $variable[0]);
    }

    // Special case where it might be a call on the function name held in a variable
    if ($can_be_dangling_method_call_instead) {
        if ($next == 'PARENTHESIS_OPEN') { // Is it an inline indirect function call
            pparse__parser_next();
            $parameters = _parse_function_call();
            pparse__parser_expect('PARENTHESIS_CLOSE');
            if (empty($variable[2])) {
                log_warning('Indirect call');
            }
            return ['CALL_INDIRECT', $variable, $parameters, $suppress_error, $GLOBALS['I']];
        }
    }

    $variable_chain = _parse_variable_dereferencing_chain_segment($suppress_error/*, $can_be_dangling_method_call_instead*/);
    if (!empty($variable_chain)) {
        // Restructure the chain around any particular calls made
        $actual_expression = ['VARIABLE', $variable[1], $variable_chain, $GLOBALS['I']];

        // Check if it's a true variable
        if ((!$can_be_dangling_method_call_instead) && (_parse_is_non_pure_variable($actual_expression))) {
            parser_error('Expected actual variable but got an expression');
        }
    } else {
        $actual_expression = ['VARIABLE', $variable[1], [], $GLOBALS['I']];
    }

    if ((!empty($GLOBALS['FLAG__PEDANTIC'])) && (in_array($variable[1], ['_GET', '_POST', '_COOKIE', '_REQUEST', '_FILES', '_SESSION']))) {
        log_warning($variable[1] . ' variable referenced');
    }

    // Canonical check for the start of the chain
    if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
        global $FOUND_NON_CANONICAL;
        if ((strtolower($variable[1]) != $variable[1]) && (strtoupper($variable[1]) != $variable[1]) && (!isset($FOUND_NON_CANONICAL[$variable[1]]))) {
            $FOUND_NON_CANONICAL[$variable[1]] = 1;
            log_warning($variable[1] . ' is in non-canonical format');
        }
    }

    return $actual_expression;
}

function _parse_is_non_pure_variable($actual_expression)
{
    if (empty($actual_expression)) {
        return false;
    }
    if ($actual_expression[0] == 'CALL_METHOD') {
        return true;
    }
    return _parse_is_non_pure_variable($actual_expression[2]);
}

function _parse_variable_dereferencing_chain_segment($suppress_error)
{
    $next = pparse__parser_peek();
    switch ($next) {
        case 'OBJECT_OPERATOR':
        case 'SCOPE':
            pparse__parser_next();
            $next_2 = pparse__parser_peek(true);
            if (($next_2[0] != 'IDENTIFIER') && ($next_2[0] != 'variable')) {
                parser_error('Expected variable/identifier to be dereferenced from object variable but got ' . $next_2[0]);
            }
            pparse__parser_expect('IDENTIFIER');
            $calling = ['VARIABLE', $next_2[1], [], $GLOBALS['I']];
            $tunnel = [];
            $next_3 = pparse__parser_peek();
            $next_4 = pparse__parser_peek_dist(1);
            if ((($next_3 == 'EXTRACT_OPEN') && ($next_4 != 'EXTRACT_CLOSE')) || ($next_3 == 'OBJECT_OPERATOR') || ($next_3 == 'SCOPE') || ($next_3 == 'PARENTHESIS_OPEN')) {
                $tunnel = _parse_variable_dereferencing_chain_segment($suppress_error);
            }
            $variable = ['DEREFERENCE', $calling, $tunnel, $GLOBALS['I']];
            break;

        case 'EXTRACT_OPEN':
            $next_t = pparse__parser_peek_dist(1);
            if ($next_t == 'EXTRACT_CLOSE') {
                $variable = [];
                break;
            }
            pparse__parser_next();
            $next_2 = pparse__parser_peek(true);
            $expression = _parse_expression();
            pparse__parser_expect('EXTRACT_CLOSE');
            $tunnel = [];
            $next_3 = pparse__parser_peek();
            $next_4 = pparse__parser_peek_dist(1);
            if ((($next_3 == 'EXTRACT_OPEN') && ($next_4 != 'EXTRACT_CLOSE')) || ($next_3 == 'OBJECT_OPERATOR') || ($next_3 == 'SCOPE') || ($next_3 == 'PARENTHESIS_OPEN')) {
                $tunnel = _parse_variable_dereferencing_chain_segment($suppress_error);
            }
            $variable = ['ARRAY_AT', $expression, $tunnel, $GLOBALS['I']];
            break;

        case 'PARENTHESIS_OPEN':
            pparse__parser_next(); // Consume the "("
            $args = _parse_function_call();
            pparse__parser_expect('PARENTHESIS_CLOSE'); // Consume the ")"
            $tunnel = [];
            $next_3 = pparse__parser_peek();
            $next_4 = pparse__parser_peek_dist(1);
            if ((($next_3 == 'EXTRACT_OPEN') && ($next_4 != 'EXTRACT_CLOSE')) || ($next_3 == 'OBJECT_OPERATOR') || ($next_3 == 'SCOPE') || ($next_3 == 'PARENTHESIS_OPEN')) {
                $tunnel = _parse_variable_dereferencing_chain_segment($suppress_error);
            }
            $variable = ['CALL_METHOD', null/*will be subbed later for preceding part of chain*/, $args, $suppress_error, $GLOBALS['I'], $tunnel];
            break;

        /*case 'CURLY_OPEN':  Not in PHP 7
            pparse__parser_next();
            $variable = ['CHAR_OF_STRING', _parse_expression(], $GLOBALS['I']);
            pparse__parser_expect('CURLY_CLOSE');
            break;*/

        default:
            $variable = [];
            break;
    }
    return $variable;
}

function _parse_assignment_operator()
{
    // Choice{"EQUAL" | "CONCAT_EQUAL" | "DIV_EQUAL" | "MUL_EQUAL" | "MINUS_EQUAL" | "PLUS_EQUAL" | "BOR_EQUAL" | "SL_EQUAL" | "SR_EQUAL" | "BW_XOR_EQUAL" | "BW_AND_EQUAL" | "BW_OR_EQUAL" | "BW_NOT_EQUAL"}

    $next = pparse__parser_next();
    if (!in_array($next, ['EQUAL', 'CONCAT_EQUAL', 'DIV_EQUAL', 'MUL_EQUAL', 'MINUS_EQUAL', 'PLUS_EQUAL', 'BOR_EQUAL', 'SL_EQUAL', 'SR_EQUAL', 'BW_XOR_EQUAL', 'BW_AND_EQUAL', 'BW_OR_EQUAL', 'BW_NOT_EQUAL'])) {
        parser_error('Expected assignment operator but got ' . $next);
    }
    return $next;
}

function _parse_literal()
{
    // Choice{"SUBTRACT" literal | "integer_literal" | "float_literal" | "string_literal" | "true" | "false" | "null" | "IDENTIFIER"}

    $next = pparse__parser_peek();
    switch ($next) {
        case 'SUBTRACT':
            pparse__parser_next();
            $_literal = _parse_literal();
            $literal = ['NEGATE', $_literal, $GLOBALS['I']];
            break;

        case 'integer_literal':
            $_literal = pparse__parser_next(true);
            $literal = ['INTEGER', $_literal[1], $GLOBALS['I']];
            break;

        case 'float_literal':
            $_literal = pparse__parser_next(true);
            $literal = ['FLOAT', $_literal[1], $GLOBALS['I']];
            break;

        case 'string_literal':
            $_literal = pparse__parser_next(true);
            $literal = ['STRING', $_literal[1], $GLOBALS['I']];
            break;

        case 'true':
            pparse__parser_next();
            $literal = ['BOOLEAN', true, $GLOBALS['I']];
            break;

        case 'false':
            pparse__parser_next();
            $literal = ['BOOLEAN', false, $GLOBALS['I']];
            break;

        case 'null':
            pparse__parser_next();
            $literal = ['null', $GLOBALS['I']];
            break;

        case 'IDENTIFIER':
            $_literal = pparse__parser_next(true);
            if (strtolower($_literal[1]) == $_literal[1]) {
                parser_warning('Lower case constant, breaks convention. Likely a variable with a missing $');
            }
            $literal = ['CONSTANT', $_literal[1], $GLOBALS['I']];
            break;

        case 'EXTRACT_OPEN': // Short array syntax
            pparse__parser_expect('EXTRACT_OPEN');
            $details = _parse_create_array('EXTRACT_CLOSE');
            pparse__parser_expect('EXTRACT_CLOSE');
            $literal = ['CREATE_ARRAY', $details, $GLOBALS['I']];
            break;

        case 'ARRAY': // Long array syntax
            pparse__parser_next(); // Skip over the ARRAY
            pparse__parser_expect('PARENTHESIS_OPEN');
            $details = _parse_create_array('PARENTHESIS_CLOSE');
            pparse__parser_expect('PARENTHESIS_CLOSE');
            $literal = ['CREATE_ARRAY', $details, $GLOBALS['I']];

            if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
                log_warning('Short array syntax is preferred');
            }

            break;

        default:
            parser_error('Expected <literal> but got ' . $next);
    }
    return $literal;
}

function _parse_create_array($closes_with)
{
    // Choice{list | map}?

    $next = pparse__parser_peek();
    if ($next == $closes_with) {
        return [];
    }

    $expression = _parse_expression();
    $next = pparse__parser_peek();
    if ($next == 'DOUBLE_ARROW') {
        pparse__parser_next();
        $expression_2 = _parse_expression();
        $full = [[$expression, $expression_2]];
        if (($expression[0] == 'LITERAL') && (@$expression[1][0] == 'STRING')) {
            unset($expression[1][2]);
            unset($expression[2]);
        }
        $next = pparse__parser_peek();
        $seen = [serialize($expression) => 1];
        while ($next == 'COMMA') {
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            if ($next_2 == $closes_with) {
                break;
            }
            $expression = _parse_expression();
            pparse__parser_expect('DOUBLE_ARROW');
            $expression_2 = _parse_expression();
            $full[] = [$expression, $expression_2];
            if (($expression[0] == 'LITERAL') && (@$expression[1][0] == 'STRING')) {
                unset($expression[1][2]);
                unset($expression[2]);
            }
            if (isset($seen[serialize($expression)])) {
                parser_warning('Duplicated key in array creation, ' . serialize($expression));
            }
            $seen[serialize($expression)] = 1;
            $next = pparse__parser_peek();
        }
    } else {
        $full = [[$expression]];
        $next = pparse__parser_peek();
        while ($next == 'COMMA') {
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            if ($next_2 == $closes_with) {
                break;
            }
            $expression = _parse_expression();
            $full[] = [$expression];
            $next = pparse__parser_peek();
        }
    }
    return $full;
}

function _parse_comma_expressions()
{
    // Choice{expression "COMMA" comma_expressions | expression}

    $expressions = [];

    $next = pparse__parser_peek();
    if (($next == 'PARENTHESIS_CLOSE') || ($next == 'COMMAND_TERMINATE')) {
        return [];
    }

    do {
        $expression = _parse_expression();
        $expressions[] = $expression;

        $next_2 = pparse__parser_peek();
        if ($next_2 == 'COMMA') {
            pparse__parser_next();
        }
    } while ($next_2 == 'COMMA');

    return $expressions;
}

function _parse_function_call()
{
    // Choice{expression "COMMA" comma_expressions | expression}

    $parameters = [];

    $next = pparse__parser_peek();
    if (($next == 'PARENTHESIS_CLOSE') || ($next == 'COMMAND_TERMINATE')) {
        return [];
    }

    do {
        if (pparse__parser_peek() == 'VARIADIC') {
            $is_variadic = true;
            pparse__parser_next();
        } else {
            $is_variadic = false;
        }

        $expression = _parse_expression();
        $parameters[] = [$expression, $is_variadic];

        $next_2 = pparse__parser_peek();
        if ($next_2 == 'COMMA') {
            pparse__parser_next();
        }
    } while ($next_2 == 'COMMA');

    foreach ($parameters as $i => $parameter) {
        $last = !isset($parameters[$i + 1]);
        if ($parameter[1] && !$last) {
            log_warning('Only the final parameter may be variadic');
        }
    }

    return $parameters;
}

function _parse_comma_variables($closer = 'COMMAND_TERMINATE')
{
    // Choice{"variable" "COMMA" comma_variables | "variable"}?

    $variables = [];

    $next = pparse__parser_peek();
    while ($next != $closer) {
        $variable = _parse_variable(false);
        $variables[] = $variable;

        $next = pparse__parser_peek();
        if ($next != $closer) {
            pparse__parser_expect('COMMA');
        }
    };

    return $variables;
}

function _parse_comma_variables_target($closer)
{
    $variables = [];

    $next = pparse__parser_peek();
    while ($next != $closer) {
        if ($next == 'COMMA') {
            pparse__parser_next();
            $variables[] = ['VARIABLE', '_', []];

            $next = pparse__parser_peek();
            continue;
        } elseif ($next == 'variable') {
            $variable = _parse_variable(false);
            $variables[] = $variable;
        } else { // PHP 7.1+ key syntax
            $literal = _parse_literal(); // We don't actually use this in our AST as we don't need it for CQC purposes
            pparse__parser_expect('DOUBLE_ARROW');
            $variable = _parse_variable(false);
            $variables[] = $variable;
        }

        $next = pparse__parser_peek();
        if ($next != $closer) {
            pparse__parser_expect('COMMA');
            $next = pparse__parser_peek();
        }
    };

    return $variables;
}

function _parse_comma_parameters($for_function_definition = false)
{
    // Choice{parameter | parameter "COMMA" comma_parameters}?

    $parameters = [];

    $next = pparse__parser_peek();
    if (($next == 'PARENTHESIS_CLOSE') || ($next == 'COMMAND_TERMINATE')) {
        return $parameters;
    }

    $defaults_started = false;

    do {
        $parameter = _parse_parameter($for_function_definition);

        if ($parameter[2] !== null) {
            $defaults_started = true;
        } elseif ($defaults_started) {
            log_warning('Default parameter before non-default parameter');
        }

        $parameters[] = $parameter;

        $next_2 = pparse__parser_peek();
        if ($next_2 == 'COMMA') {
            pparse__parser_next();
        }
    } while ($next_2 == 'COMMA');

    if ($for_function_definition) {
        foreach ($parameters as $i => $parameter) {
            $last = !isset($parameters[$i + 1]);
            if ($parameter[4] && !$last) {
                log_warning('Only the final parameter may be variadic');
            }
        }
    }

    return $parameters;
}

function _parse_parameter($for_function_definition = false)
{
    // Choice{"REFERENCE" "variable" | "variable" | "variable" "EQUAL" literal | hint "variable" | hint "REFERENCE" "variable" | hint "variable" "EQUAL" literal}

    $hint = null;
    $is_variadic = false;
    $is_nullable = false;

    while (true) {
        $next = pparse__parser_next(true);

        if ($next[0] == 'QUESTION') {
            $is_nullable = true;
            $next = pparse__parser_next(true);
        }

        switch ($next[0]) {
            // Type hints
            case 'ARRAY':
            case 'BOOL':
            case 'CALLABLE':
            case 'FLOAT':
            case 'INT':
            case 'ITERABLE':
            case 'OBJECT':
            case 'STRING':
                $hint = $next[0];
                break;
            case 'IDENTIFIER':
                $hint = $next[1];
                break;

            // Reference parameter, which needs extra checking
            case 'REFERENCE':
                if (pparse__parser_peek() == 'VARIADIC') {
                    $is_variadic = true;
                    pparse__parser_next();
                }

                $variable = pparse__parser_expect('variable');
                // 'RECEIVE_BY_REFERENCE' and 'RECEIVE_BY_VALUE' aren't actually used for anything specifically.
                if (pparse__parser_peek() == 'EQUAL') {
                    if ($is_variadic) {
                        log_warning('Variadic parameters may not take a default value');
                    }

                    // Variable with type hint and default value
                    pparse__parser_next(); // Consume the EQUAL

                    // 'RECEIVE_BY_REFERENCE' and 'RECEIVE_BY_VALUE' aren't actually used for anything specifically.
                    $parameter = ['RECEIVE_BY_VALUE', $variable, _parse_literal(), $hint, $is_variadic, $GLOBALS['I']];
                    $parameter['HINT'] = '?' . $hint;
                } else {
                    $parameter = ['RECEIVE_BY_REFERENCE', $variable, null, $hint, $is_variadic, $GLOBALS['I']];
                }
                $next_2 = pparse__parser_peek();
                if ($next_2 == 'EQUAL') {
                    pparse__parser_next();
                    $value = _parse_literal();
                    $parameter[2] = $value;
                }
                return $parameter;

            // Normal parameters
            case 'VARIADIC':
                $is_variadic = true;
                $next = pparse__parser_expect('variable');
                // no break
            case 'variable':
                // 'RECEIVE_BY_REFERENCE' and 'RECEIVE_BY_VALUE' aren't actually used for anything specifically.
                $parameter = ['RECEIVE_BY_VALUE', $next[1], null, $hint, $is_variadic, $GLOBALS['I']];
                $next_2 = pparse__parser_peek();
                if ($next_2 == 'EQUAL') {
                    // Variable with type hint and default value

                    if ($is_variadic) {
                        log_warning('Variadic parameters may not take a default value');
                    }

                    pparse__parser_next();
                    $value = _parse_literal();
                    $parameter[2] = $value;
                }
                return $parameter;

            default:
                parser_error('Expected <parameter> but got ' . $next[0]);
        }
    }

    return null;
}

function pparse__parser_expect($token)
{
    global $TOKENS, $I;
    if (!isset($TOKENS[$I])) {
        parser_error('Ran out of input when expecting ' . $token);
    }
    $next = $TOKENS[$I];
    if ($next[0] == 'comment') {
        handle_comment($next);
        $I++;
        return pparse__parser_expect($token);
    }
    $I++;
    if ($next[0] != $token) {
        parser_error('Expected ' . $token . ' but got ' . $next[0] . ' (' . $next[1] . ')');
    }
    return $next[1];
}

function pparse__parser_peek($all = false)
{
    global $TOKENS, $I;
    if (!isset($TOKENS[$I])) {
        return null;
    }
    if ($TOKENS[$I][0] == 'comment') {
        handle_comment($TOKENS[$I]);
        $I++;
        return pparse__parser_peek($all);
    }
    if ($all) {
        return $TOKENS[$I];
    }
    return $TOKENS[$I][0];
}

function pparse__parser_peek_dist($d, $p = null)
{
    global $TOKENS, $I;
    if ($p === null) {
        $p = $I;
    }
    while ($d != 0) {
        if (!isset($TOKENS[$p])) {
            return null;
        }
        if ($TOKENS[$p][0] == 'comment') {
            handle_comment($TOKENS[$p]);
            return pparse__parser_peek_dist($d, $p + 1);
        }
        $p++;
        $d--;
    }
    if (!isset($TOKENS[$p])) {
        return null;
    }
    return $TOKENS[$p][0];
}

function pparse__parser_next($all = false)
{
    global $TOKENS, $I;
    if (!isset($TOKENS[$I])) {
        return null;
    }
    $next = $TOKENS[$I];
    $I++;
    if ($next[0] == 'comment') {
        handle_comment($next);
        return pparse__parser_next($all);
    }
    if ($all) {
        return $next;
    }
    return $next[0];
}

function parser_error($message)
{
    global $TOKENS, $I;
    /*foreach ($TOKENS as $key => $token) { Debug output
        if ($key == $I) {
            echo '<strong>';
        }
        echo ' ' . $token[0] . ' ';
        if ($key == $I) {
            echo '</strong>';
        }
    }*/
    list($pos, $line, $full_line) = pos_to_line_details($I);
    die_error('PARSER', $pos, $line, $message);
}

function parser_warning($message)
{
    global $TOKENS, $I;
    list($pos, $line, $full_line) = pos_to_line_details($I);
    warn_error('PARSER', $pos, $line, $message);
}

function handle_comment($comment)
{
    global $OK_EXTRA_FUNCTIONS;
    if (substr($comment[1], 0, 17) == 'EXTRA FUNCTIONS: ') {
        $OK_EXTRA_FUNCTIONS = substr($comment[1], 17);
    }
    if (!empty($GLOBALS['FLAG__SOMEWHAT_PEDANTIC'])) {
        if (strpos($comment[1], 'FIXME') !== false) {
            log_warning('FIXME comment found [should be a TODO] (' . str_replace("\n", ' ', trim($comment[1])) . ')', $GLOBALS['I']);
        }
        if (strpos($comment[1], 'HACKHACK') !== false) {
            log_warning('HACKHACK comment found [should be a FUDGE] (' . str_replace("\n", ' ', trim($comment[1])) . ')', $GLOBALS['I']);
        }
    }
    if (!empty($GLOBALS['FLAG__TODO'])) {
        if (strpos($comment[1], 'TODO') !== false) {
            log_warning('TODO comment found (' . str_replace("\n", ' ', trim($comment[1])) . ')', $GLOBALS['I']);
        }
        if (strpos($comment[1], 'IDEA') !== false) {
            log_warning('IDEA comment found (' . str_replace("\n", ' ', trim($comment[1])) . ')', $GLOBALS['I']);
        }
        if (strpos($comment[1], 'LEGACY') !== false) {
            log_warning('LEGACY comment found (' . str_replace("\n", ' ', trim($comment[1])) . ')', $GLOBALS['I']);
        }
        if (strpos($comment[1], 'FUDGE') !== false) {
            log_warning('FUDGE comment found (' . str_replace("\n", ' ', trim($comment[1])) . ')', $GLOBALS['I']);
        }
        if (strpos($comment[1], 'FRAGILE') !== false) {
            log_warning('FRAGILE comment found (' . str_replace("\n", ' ', trim($comment[1])) . ')', $GLOBALS['I']);
        }
        //if (strpos($comment[1], 'XHTMLXHTML') !== false) log_warning('XHTMLXHTML comment found', $GLOBALS['I']); Don't want to report these
    }
}

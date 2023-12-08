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

function parse($_tokens = null)
{
    ini_set('xdebug.max_nesting_level', '2000');

    global $TOKENS, $I, $OK_EXTRA_FUNCTIONS;
    $OK_EXTRA_FUNCTIONS = null;
    if (!is_null($_tokens)) {
        $TOKENS = $_tokens;
    }
    $I = 0;
    $structure = _parse_php($TOKENS);
    $structure['ok_extra_functions'] = $OK_EXTRA_FUNCTIONS;
    global $FILENAME;
    if ((count($structure['main']) > 0) && (substr($FILENAME, 0, 7) == 'sources') && ($FILENAME != 'sources' . DIRECTORY_SEPARATOR . 'global.php') && ($FILENAME != 'sources' . DIRECTORY_SEPARATOR . 'static_cache.php') && ($FILENAME != 'sources' . DIRECTORY_SEPARATOR . 'critical_errors.php') && ((count($structure['main']) > 1) || (($structure['main'][0][0] != 'RETURN') && (($structure['main'][0][0] != 'CALL_DIRECT') || ($structure['main'][0][1] != 'require_code'))))) {
        log_warning('Sources files should not contain loose code');
    }

    return $structure;
}

global $FUNCTIONS;
$FUNCTIONS = array();
global $CLASSES;
$CLASSES = array();
global $MAIN;

function _parse_php()
{
    // Choice{"FUNCTION" "IDENTIFIER "BRACKET_OPEN" comma_parameters "BRACKET_CLOSE" command | "CLASS" "IDENTIFIER" ("EXTENDS" "IDENTIFIER")? "CURLY_OPEN" class_contents "CURLY_CLOSE" | command}*

    $next = pparse__parser_peek();
    $program = array();
    $program['functions'] = array();
    $program['classes'] = array();
    $program['main'] = array();
    $modifiers = array();
    while (!is_null($next)) {
        switch ($next) {
            case 'FUNCTION':
                $_function = _parse_function_dec();
                foreach ($program['functions'] as $_) {
                    if ($_['name'] == $_function['name']) {
                        log_warning('Duplicated function \'' . $_function['name'] . '\'');
                    }
                }
                //log_special('defined', $_function['name']);
                $program['functions'][] = $_function;
                break;

            case 'CLASS':
                $class = _parse_class_dec($modifiers);
                foreach ($program['classes'] as $_) {
                    if ($_['name'] == $class['name']) {
                        log_warning('Duplicated class ' . $class['name']);
                    }
                }
                $program['classes'][] = $class;
                $modifiers = array();
                break;

            case 'INTERFACE':
                $class = array('is_interface' => true);
                if (count($modifiers) > 0) {
                    if (in_array('abstract', $modifiers)) {
                        log_warning('Interfaces are inherently abstract, do not use the abstract keyword');
                    }
                    if (in_array('protected', $modifiers)) {
                        log_warning('Interfaces cannot be protected. The point of an interface is that it is public');
                    }
                    if (in_array('private', $modifiers)) {
                        log_warning('Interfaces cannot be private. The point of an interface is that it is public');
                    }
                    $class['modifiers'] = $modifiers;
                }
                pparse__parser_next();
                $class['name'] = pparse__parser_expect('IDENTIFIER');
                $next = pparse__parser_peek();
                if ($next == 'IMPLEMENTS') {
                    log_warning('Interfaces cannot implement each other, they can only extend each other');
                }
                if ($next == 'EXTENDS') {
                    pparse__parser_expect('EXTENDS');
                    $class['superclass'] = _parse_comma_expressions();
                    $next = pparse__parser_peek();
                }
                if ($next == 'IMPLEMENTS') {
                    log_warning('Interfaces cannot implement each other, they can only extend each other');
                }
                pparse__parser_expect('CURLY_OPEN');
                $_class = _parse_class_contents($modifiers, $class['is_interface']);
                foreach ($program['classes'] as $_) {
                    if ($_['name'] == $class['name']) {
                        log_warning('Duplicated class ' . $class['name']);
                    }
                }
                $class = array_merge($class, $_class);
                pparse__parser_expect('CURLY_CLOSE');
                $program['classes'][] = $class;
                $modifiers = array();
                break;

            case 'ABSTRACT':
                if (count($modifiers) > 0) {
                    log_warning('Abstract keyword must appear first: ' . implode(', ', $modifiers) . ', abstract');
                }
                pparse__parser_next();
                switch (pparse__parser_peek()) {
                    case 'CLASS':
                        $modifiers[] = 'abstract';
                        break;
                    default:
                        // This is an invalid token to appear after "abstract"
                        log_warning('Only classes and their methods can be abstract, not ' . pparse__parser_peek());
                        $modifiers = array();
                        break;
                }
                break;

            default:
                $program['main'] = array_merge($program['main'], _parse_command());
                break;
        }

        $next = pparse__parser_peek();
    }
    return $program;
}

function _parse_command($needs_brace = false)
{
    // Choice{"CURLY_OPEN" command* "CURLY_CLOSE" | command_actual "COMMAND_TERMINATE"*}

    $next = pparse__parser_peek();
    $command = array();
    switch ($next) {
        case 'CURLY_OPEN':
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            while (true) {
                switch ($next_2) {
                    case 'CURLY_CLOSE':
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
            if ($needs_brace) {
                parser_warning('PSR-2 asks us to use braces for all control structures');
            }

            $new_command = _parse_command_actual();

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
            while ($next_2 == 'COMMAND_TERMINATE') {
                pparse__parser_next();
                $next_2 = pparse__parser_peek();
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
}

function _parse_command_actual($no_term_needed = false)
{
    // Choice{"FUNCTION" | variable "DEC" | variable "INC" | target assignment_operator expression | function "BRACKET_OPEN" comma_expressions "BRACKET_CLOSE" | "IF" expression command if_rest? | "SWITCH" expression "CURLY_OPEN" cases "CURLY_CLOSE" | "FOREACH" "BRACKET_OPEN" expression "AS" _foreach "BRACKET_CLOSE" command | "FOR" "BRACKET_OPEN" command expression command "BRACKET_CLOSE" command | "DO" command "WHILE" "BRACKET_OPEN" expression "BRACKET_CLOSE" | "WHILE" "BRACKET_OPEN" expression "BRACKET_CLOSE" command | "RETURN" | "CONTINUE" | "BREAK" | "BREAK" expression | "CONTINUE" expression | "RETURN" expression | "GLOBAL" comma_variables | "ECHO" expression}

    $is_static = false;

    $next = pparse__parser_peek(true);
    $suppress_error = ($next[0] == 'SUPPRESS_ERROR');
    if ($suppress_error) {
        pparse__parser_next();
        $next = pparse__parser_peek(true);
    }
    switch ($next[0]) {
        case 'CLASS':
            $command = array('INNER_CLASS', _parse_class_dec(), $GLOBALS['I']);
            break;

        case 'FUNCTION':
            $command = array('INNER_FUNCTION', _parse_function_dec(), $GLOBALS['I']);
            break;

        case 'DEC':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $command = array('PRE_DEC', $variable, $GLOBALS['I']);
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'INC':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $command = array('PRE_INC', $variable, $GLOBALS['I']);
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'STATIC': // Moves past
            pparse__parser_next();
            $is_static = true;

        case 'variable':
            $target = _parse_target();
            $next_2 = pparse__parser_peek();
            switch ($next_2) {
                case 'DEC':
                    if (($target[0] == 'LIST') || ($target[0] == 'ARRAY_APPEND')) {
                        parser_error('LIST is only a one way type'); // We needed to read a target (for assignment), but we really wanted a variable (subset of target) -- we ended up with something that WAS target but NOT variable (we couldn't have known till now)
                    }
                    pparse__parser_next();
                    $command = array('DEC', $target, $GLOBALS['I']);
                    break;

                case 'INC':
                    if (($target[0] == 'LIST') || ($target[0] == 'ARRAY_APPEND')) {
                        parser_error('LIST is only a one way type'); // We needed to read a target (for assignment), but we really wanted a variable (subset of target) -- we ended up with something that WAS target but NOT variable (we couldn't have known till now)
                    }
                    pparse__parser_next();
                    $command = array('INC', $target, $GLOBALS['I']);
                    break;

                default: // Either an assignment or an indirect function call or a method call
                    $command = $target;
                    // We should be at the end of a chain by here.
                    // We may still be an assignment, despire the $next_3 branch
                    // above. Handle this if so:
                    if (in_array(pparse__parser_peek(), array('EQUAL', 'CONCAT_EQUAL', 'DIV_EQUAL', 'MINUS_EQUAL', 'MUL_EQUAL', 'PLUS_EQUAL', 'BOR_EQUAL'), true)) {
                        $assignment = _parse_assignment_operator();
                        $expression = _parse_expression();
                        if ($is_static && $expression[0] != 'LITERAL' && $expression[0] != 'NEGATE' && $expression[0] != 'CREATE_ARRAY') {
                            parser_error('Can only use static with a literal (scalar) expression.');
                        }
                        $command = array('ASSIGNMENT', $assignment, $command, $expression, $GLOBALS['I']);
                    }
                    break;
            }
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'IDENTIFIER': // Direct function call
            pparse__parser_next();
            $identifier = $next[1];
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'SCOPE') {
                pparse__parser_next();
                $command = _parse_command_actual();
                if ($command[0] == 'CALL_DIRECT') {
                    $command[0] = 'CALL_METHOD';
                    $command[1] = array('VARIABLE', 'this', array('DEREFERENCE', array('VARIABLE', $command[1], array(), $command[4]), array(), $command[4]), $command[4]);
                } else {
                    $expression = array('REFERENCE', $command, $GLOBALS['I']);
                }
            } else {
                pparse__parser_expect('BRACKET_OPEN');
                $parameters = _parse_comma_expressions();
                pparse__parser_expect('BRACKET_CLOSE');
                $command = array('CALL_DIRECT', $identifier, $parameters, $suppress_error, $GLOBALS['I']);
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
            $command = array('ECHO', $parameters, $GLOBALS['I']);
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'LIST':
            $target = _parse_target();
            pparse__parser_expect('EQUAL');
            $expression = _parse_expression();
            $command = array('ASSIGNMENT', 'EQUAL', $target, $expression, $GLOBALS['I']);
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'IF':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('BRACKET_OPEN');
            $expression = _parse_expression();
            pparse__parser_expect('BRACKET_CLOSE');
            $command = _parse_command(true);

            $next_2 = pparse__parser_peek();
            if (($next_2 == 'ELSE') || ($next_2 == 'ELSEIF')) {
                $if_rest = _parse_if_rest();
                $command = array('IF_ELSE', $expression, $command, $if_rest, $c_pos);
            } else {
                $command = array('IF', $expression, $command, $c_pos);
            }
            break;

        case 'SWITCH':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            $expression = _parse_expression();
            pparse__parser_expect('CURLY_OPEN');
            $cases = _parse_cases();
            pparse__parser_expect('CURLY_CLOSE');
            $command = array('SWITCH', $expression, $cases, $c_pos);
            break;

        case 'FOREACH':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('BRACKET_OPEN');
            $expression = _parse_expression();
            pparse__parser_expect('AS');
            // Choice{"variable" "DOUBLE_ARROW" "variable" | "variable"}
            $next = pparse__parser_peek();
            $is_reference = ($next == 'REFERENCE');
            if ($is_reference) {
                pparse__parser_next();
            }
            $variable = _parse_variable($suppress_error);
            $after_variable = pparse__parser_peek();
            if ($after_variable == 'DOUBLE_ARROW') {
                pparse__parser_next();
                $next = pparse__parser_peek();
                $is_reference = ($next == 'REFERENCE');
                if ($is_reference) {
                    pparse__parser_next();
                }
                $_foreach = array($variable, _parse_variable($suppress_error));
            } else {
                $_foreach = $variable;
            }
            pparse__parser_expect('BRACKET_CLOSE');
            $loop_command = _parse_command(true);
            if ($after_variable == 'DOUBLE_ARROW') {
                $command = array('FOREACH_map', $expression, $_foreach[0], $_foreach[1], $loop_command, $c_pos);
            } else {
                $command = array('FOREACH_list', $expression, $_foreach, $loop_command, $c_pos);
            }
            break;

        case 'FOR':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('BRACKET_OPEN');
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'COMMAND_TERMINATE') {
                $init_command = null;
            } else {
                $init_command = _parse_command_actual(true);
            }
            pparse__parser_expect('COMMAND_TERMINATE');
            $control_expression = _parse_expression();
            pparse__parser_expect('COMMAND_TERMINATE');
            $control_command = _parse_command_actual(true);
            pparse__parser_expect('BRACKET_CLOSE');
            $loop_command = _parse_command(true);
            $command = array('FOR', $init_command, $control_expression, $control_command, $loop_command, $c_pos);
            break;

        case 'DO':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            $loop_command = _parse_command(true);
            pparse__parser_expect('WHILE');
            pparse__parser_expect('BRACKET_OPEN');
            $control_expression = _parse_expression();
            pparse__parser_expect('BRACKET_CLOSE');
            $command = array('DO', $control_expression, $loop_command, $c_pos);
            break;

        case 'WHILE':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('BRACKET_OPEN');
            $control_expression = _parse_expression();
            pparse__parser_expect('BRACKET_CLOSE');
            $loop_command = _parse_command(true);
            $command = array('WHILE', $control_expression, $loop_command, $c_pos);
            break;

        case 'TRY':
            pparse__parser_next();        // Consume the "try"
            $try_position = $GLOBALS['I'];
            if (pparse__parser_peek() != 'CURLY_OPEN') {
                parser_error('Expected code block after "try".');
            }
            $try = _parse_command(true);
            $exception = null;
            $catches = array();
            do {
                pparse__parser_expect('CATCH');
                $catch_position = $GLOBALS['I'];
                switch (pparse__parser_peek()) {
                    case 'BRACKET_OPEN':
                        // We are catching something specific. Let's treat it as a
                        // function's parameter list for the moment, as that's simplest,
                        // although it accepts a bit too much
                        pparse__parser_expect('BRACKET_OPEN');
                        pparse__parser_expect('IDENTIFIER'); // E.g. 'EXCEPTION'
                        $exception = _parse_comma_parameters();
                        pparse__parser_expect('BRACKET_CLOSE');
                        if (pparse__parser_peek() != 'CURLY_OPEN') {
                            parser_error('Expected code block after "catch".');
                        }
                    case 'CURLY_OPEN':
                        $catch = _parse_command(true);
                        $catches[] = array('CATCH', $exception, $catch, $catch_position);
                        break;
                    default:
                        parser_error('Expected BRACKET_OPEN or CURLY_OPEN after "catch".');
                        break;
                }
            } while (pparse__parser_peek() == 'CATCH');
            $command = array('TRY', $try, $catches, $try_position);
            break;

        case 'THROW':
            pparse__parser_next();        // Consume the "throw"
            $expr = _parse_expression();
            $command = array('THROW', $expr, $GLOBALS['I']);
            break;

        case 'RETURN':
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            switch ($next_2) {
                case 'COMMAND_TERMINATE':
                    $command = array('RETURN', array('SOLO', array('LITERAL', array('null')), $GLOBALS['I']), $GLOBALS['I']);
                    break;

                default:
                    $command = array('RETURN', _parse_expression(), $GLOBALS['I']);
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
                    $command = array('CONTINUE', array('SOLO', array('LITERAL', array('INTEGER', 1)), $GLOBALS['I']), $GLOBALS['I']);
                    break;

                default:
                    $command = array('CONTINUE', _parse_expression(), $GLOBALS['I']);
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
                    $command = array('BREAK', array('SOLO', array('LITERAL', array('INTEGER', 1)), $GLOBALS['I']), $GLOBALS['I']);
                    break;

                default:
                    $command = array('BREAK', _parse_expression(), $GLOBALS['I']);
            }
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        case 'GLOBAL':
            pparse__parser_next();
            $command = array('GLOBAL', _parse_comma_variables(), $GLOBALS['I']);
            foreach ($command[1] as $variable) {
                if (strtoupper($variable[1]) != $variable[1]) {
                    log_warning('Globalised variable ' . $variable[1] . ' is in non-canonical format');
                }
            }
            if (!$no_term_needed) {
                _test_command_end();
            }
            break;

        default:
            parser_error('Expected <command> but got ' . $next[0]);
    }
    return $command;
}

function _parse_call_chain($command = null, $suppress_error = false)
{
    if ($command === null) {
        $command = array();
    }

    $i = pparse__parser_expect('IDENTIFIER');        // Silly PHP syntax makes $scoped_variables and scoped_functions() different, but member_variables and member_functions() the same...
    switch (pparse__parser_peek()) {
        case 'BRACKET_OPEN':
            pparse__parser_next();        // Consume the "("
            $args = _parse_comma_expressions();
            pparse__parser_expect('BRACKET_CLOSE');        // Consume the ")"
            $expression = $command; // Actually the 'command' was an expression, on which we will call our object
            $command = array('CALL_METHOD', $expression, $args, $suppress_error, $GLOBALS['I']);
            break;
        default:
            // Nothing of interest. Let the next pass handle it.
            break;
    }
    return $command;
}

function _parse_target()
{
    // Choice{variable | "LIST" "BRACKET_OPEN" comma_variables "BRACKET_CLOSE" | "variable" "EXTRACT_OPEN" "EXTRACT_CLOSE"}

    $next = pparse__parser_peek();
    switch ($next) {
        case 'LIST':
            pparse__parser_next();
            pparse__parser_expect('BRACKET_OPEN');
            $target = array('LIST', _parse_comma_variables(true), $GLOBALS['I']);
            pparse__parser_expect('BRACKET_CLOSE');
            break;

        default:
            $variable = _parse_variable(false, true);
            $next = pparse__parser_peek();
            if ($next == 'EXTRACT_OPEN') {
                pparse__parser_next();
                pparse__parser_expect('EXTRACT_CLOSE');
                $target = array('ARRAY_APPEND', $variable, $GLOBALS['I']);
            } else {
                $target = $variable;
            }
    }
    return $target;
}

function _parse_if_rest()
{
    // Choice{else command | elseif expression command if_rest?}

    $next = pparse__parser_peek();
    switch ($next) {
        case 'ELSE':
            pparse__parser_next();
            $command = _parse_command(true);
            $if_rest = $command;
            break;

        case 'ELSEIF':
            pparse__parser_next();
            $c_pos = $GLOBALS['I'];
            pparse__parser_expect('BRACKET_OPEN');
            $expression = _parse_expression();
            pparse__parser_expect('BRACKET_CLOSE');
            $command = _parse_command(true);
            $next_2 = pparse__parser_peek();
            if (($next_2 == 'ELSE') || ($next_2 == 'ELSEIF')) {
                $_if_rest = _parse_if_rest();
                $if_rest = array(array('IF_ELSE', $expression, $command, $_if_rest, $c_pos));
            } else {
                $if_rest = array(array('IF', $expression, $command, $c_pos));
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
    $cases = array();
    while (($next == 'CASE') || ($next == 'DEFAULT')) {
        switch ($next) {
            case 'CASE':
                pparse__parser_next();
                $expression = _parse_expression();
                pparse__parser_expect('COLON');
                $next_2 = pparse__parser_peek();
                $commands = array();
                while (($next_2 != 'CURLY_CLOSE') && ($next_2 != 'CASE') && ($next_2 != 'DEFAULT')) {
                    $commands = array_merge($commands, _parse_command());
                    $next_2 = pparse__parser_peek();
                }
                foreach ($cases as $c) {
                    if (($c[0] !== null) && ($c[0][0] == 'LITERAL') && ($expression[0] == 'LITERAL') && ($c[0][1][1] == $expression[1][1])) {
                        log_warning('Duplicate case expression');
                    }
                }
                $cases[] = array($expression, $commands);
                break;

            case 'DEFAULT':
                pparse__parser_next();
                pparse__parser_expect('COLON');
                $next_2 = pparse__parser_peek();
                $commands = array();
                while (($next_2 != 'CURLY_CLOSE') && ($next_2 != 'CASE')) {
                    $commands += _parse_command();
                    $next_2 = pparse__parser_peek();
                }
                $cases[] = array(null, $commands);
                break;

            default:
                parser_error('Expected <cases> but got ' . $next);
        }

        $next = pparse__parser_peek();
    }

    return $cases;
}

function _parse_class_contents($class_modifiers = null, $is_interface = false)
{
    // Choice{"VAR" "IDENTIFIER" "EQUAL" literal "COMMAND_TERMINATE" | "VAR" "IDENTIFIER" "COMMAND_TERMINATE" | function_dec}*

    if ($class_modifiers === null) {
        $class_modifiers = array();
    }

    $next = pparse__parser_peek();
    $class = array('functions' => array(), 'vars' => array(), 'i' => $GLOBALS['I']);
    $modifiers = array();
    while (($next == 'CONST') || ($next == 'VAR') || ($next == 'FUNCTION') || ($next == 'PUBLIC') || ($next == 'PRIVATE') || ($next == 'PROTECTED') || ($next == 'ABSTRACT') || ($next == 'STATIC')) {
        switch ($next) {
            case 'PRIVATE':
            case 'PROTECTED':
                if ($is_interface) {
                    log_warning('Interfaces cannot contain anything protected or private');
                }
            case 'PUBLIC':
                if (in_array('public', $modifiers) || in_array('private', $modifiers) || in_array('protected', $modifiers)) {
                    log_warning('Multiple visibility levels defined: ' . implode(', ', $modifiers) . ', ' . $next);
                }
                $modifiers[] = strtolower($next);
                if ($is_interface && in_array('abstract', $modifiers)) {
                    log_warning('Everything in an interface is inherently abstract. Do not use the abstract keyword.');
                }
                if ((pparse__parser_peek_dist(1) == 'FUNCTION') || (pparse__parser_peek_dist(1) == 'STATIC') || (pparse__parser_peek_dist(1) == 'ABSTRACT')) {
                    if (pparse__parser_peek_dist(1) == 'ABSTRACT') {
                        log_warning('Abstract keyword must appear first: ' . implode(', ', $modifiers) . ', abstract');
                    }

                    // Variables fall through to VAR, function's don't
                    pparse__parser_next();        // VAR does this in its do-while loop
                    break;
                }

            case 'VAR':
                if ($next == 'VAR') {
                    log_warning('Don\'t use the var keyword anymore, it is deprecated.');
                }

            case 'CONST':
                do {
                    pparse__parser_next();
                    $identifier = pparse__parser_peek(true);
                    if (($identifier[0] != 'IDENTIFIER') && ($identifier[0] != 'variable')) {
                        parser_error('Expected IDENTIFIER or variable but got ' . $identifier[0]);
                    }
                    $identifier = pparse__parser_next(true);
                    $next_2 = pparse__parser_peek();
                    if ($next_2 == 'EQUAL') {
                        pparse__parser_next();
                        if (pparse__parser_peek() == 'ARRAY') {
                            pparse__parser_next();        // Skip over the ARRAY
                            pparse__parser_expect('BRACKET_OPEN');
                            $details = _parse_create_array();
                            pparse__parser_expect('BRACKET_CLOSE');
                            $literal = array('CREATE_ARRAY', $details, $GLOBALS['I']);
                        } else {
                            $literal = _parse_literal();
                        }
                        $class['vars'][] = array($identifier[1], $literal);
                    } else {
                        $class['vars'][] = array($identifier[1], array('SOLO', array('LITERAL', array('null')), $GLOBALS['I']));
                    }

                    $next_2 = pparse__parser_peek();
                } while ($next_2 == 'COMMA');

                pparse__parser_expect('COMMAND_TERMINATE');
                $modifiers = array();
                break;

            case 'FUNCTION':
                if (!in_array('private', $modifiers) && !in_array('protected', $modifiers) && !in_array('public', $modifiers)) {
                    log_warning('You must specify function visibility (e.g. public)');
                }

                if ($is_interface && in_array('private', $modifiers)) {
                    log_warning('All methods in an interface must be public or protected');
                }
                if ($is_interface && in_array('abstract', $modifiers)) {
                    log_warning('Everything in an interface is inherently abstract. Do not use the abstract keyword');
                }
                $_function = _parse_function_dec(array_merge($modifiers, $is_interface ? array('abstract') : array()));        // Interface methods are inherently abstract
                foreach ($class['functions'] as $_) {
                    if ($_['name'] == $_function['name']) {
                        log_warning('Duplicated method \'' . $_function['name'] . '\'');
                    }
                }
                $class['functions'][] = $_function;

                if ((in_array('static', $modifiers)) && (in_array('abstract', $modifiers))) {
                    log_warning('Cannot mix static and abstract');
                }

                $modifiers = array();
                break;

            case 'STATIC':
            case 'ABSTRACT':
                if ($next == 'ABSTRACT') {
                    if ($is_interface) {
                        log_warning('Everything in an interface is inherently abstract. Do not use the abstract keyword');
                    }
                    $modifiers[] = 'abstract';
                    if (!in_array('abstract', $class_modifiers)) {
                        log_warning('Abstract keyword found in a non-abstract class.');
                    }
                } else {
                    if (count($modifiers) == 0) {
                        log_warning('Static keyword must not appear before visibility');
                    }
                    $modifiers[] = 'static';
                }
                pparse__parser_next();        // Consume the abstract keyword
                // Peek ahead to make sure the next token can be abstract
                switch (pparse__parser_peek()) {
                    case 'PUBLIC':
                    case 'PRIVATE':
                    case 'PROTECTED':
                        // If we're followed by another modifier, peek ahead further
                        switch (pparse__parser_peek_dist(1)) {
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
                            default:
                                // Invalid
                                log_warning('Visibility keywords are only valid for functions and member variables, not ' . pparse__parser_peek());
                                break;
                        }
                        break;
                    case 'FUNCTION':
                        // Valid
                        break;
                    case 'variable':
                    case 'VAR':
                        log_warning('Abstract keyword applied to member variable');
                        break;
                    case 'STATIC':
                        break;
                    default:
                        log_warning('The abstract keyword only applies to classes and methods, not ' . pparse__parser_peek());
                        break;
                }
                break;

            default:
                parser_error('Expected <class_contents> but got ' . $next);
        }

        $next = pparse__parser_peek();
    }

    return $class;
}

function _parse_class_dec($modifiers = null)
{
    if ($modifiers === null) {
        $modifiers = array();
    }

    $class = array('is_interface' => false);        // Classes and interfaces aren't different enough to justify separate handlers
    if (count($modifiers) > 0) {
        $class['modifiers'] = $modifiers;
    }
    pparse__parser_next();
    $class['name'] = pparse__parser_expect('IDENTIFIER');
    $next = pparse__parser_peek();
    if ($next == 'EXTENDS') {
        pparse__parser_expect('EXTENDS');
        $class['superclass'] = pparse__parser_expect('IDENTIFIER');
        $next = pparse__parser_peek();
    }
    if ($next == 'IMPLEMENTS') {
        pparse__parser_expect('IMPLEMENTS');
        if (!isset($class['interfaces'])) {
            $class['interfaces'] = array();
        }
        $class['interfaces'][] = pparse__parser_expect('IDENTIFIER');
    }
    pparse__parser_expect('CURLY_OPEN');
    $_class = _parse_class_contents($modifiers, $class['is_interface']);
    $class = array_merge($class, $_class);
    pparse__parser_expect('CURLY_CLOSE');

    return $class;
}

function _parse_function_dec($function_modifiers = null)
{
    if ($function_modifiers === null) {
        $function_modifiers = array();
    }

    $function = array();
    $function['offset'] = $GLOBALS['I'];
    pparse__parser_expect('FUNCTION');
    if (pparse__parser_peek() == 'REFERENCE') {
        pparse__parser_next();
    }
    $function['name'] = pparse__parser_expect('IDENTIFIER');
    pparse__parser_expect('BRACKET_OPEN');
    $function['parameters'] = _parse_comma_parameters();
    pparse__parser_expect('BRACKET_CLOSE');
    if (in_array('abstract', $function_modifiers)) {
        pparse__parser_expect('COMMAND_TERMINATE');
        $function['code'] = array();
    } else {
        $function['code'] = _parse_command();
    }
    $function['modifiers'] = $function_modifiers;

    return $function;
}

// In precedence order. Note REFERENCE==BW_AND (it gets converted, for clarity). Ditto QUESTION==TERNARY_IF
global $OPS;
$OPS = array('QUESTION', 'TERNARY_IF', 'BOOLEAN_XOR', 'BOOLEAN_OR', 'BOOLEAN_AND', 'REFERENCE', 'BW_OR', 'BW_XOR', 'BW_AND', 'IS_EQUAL', 'IS_NOT_EQUAL', 'IS_IDENTICAL', 'IS_NOT_IDENTICAL', 'IS_SMALLER', 'IS_SMALLER_OR_EQUAL', 'IS_GREATER', 'IS_GREATER_OR_EQUAL', 'SL', 'SR', 'ADD', 'SUBTRACT', 'CONC', 'MULTIPLY', 'DIVIDE', 'REMAINDER');

function _parse_expression()
{
    // Choice{expression_inner | expression_inner binary_operation expression_inner | expression_inner QUESTION expression_inner COLON expression_inner}

    global $OPS;

    $e_pos = $GLOBALS['I'];
    $expression = _parse_expression_inner();
    $op_list = array($expression);

    $next = pparse__parser_peek();
    while (in_array($next, $OPS)) {
        pparse__parser_next();
        if ($next == 'QUESTION') {
            $expression_2 = _parse_expression();
            pparse__parser_expect('COLON');
            $expression_3 = _parse_expression();
            $op_list[] = 'TERNARY_IF';
            $op_list[] = array($expression_2, $expression_3);
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
        $new = array($op_list[1], $op_list[0], $op_list[2], $_e_pos);
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
                return array($op, $_left, $_right, $_e_pos);
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
    // Choice{"BOOLEAN_NOT expression | SUBTRACT expression | literal | variable | variable "BRACKET_OPEN" comma_parameters "BRACKET_CLOSE" | "IDENTIFIER" | "IDENTIFIER" "BRACKET_OPEN" comma_parameters "BRACKET_CLOSE" | "NEW" "IDENTIFIER" "BRACKET_OPEN" comma_expressions "BRACKET_CLOSE" | "NEW" "IDENTIFIER" | "CLONE" expression | "ARRAY" "BRACKET_OPEN" create_array "BRACKET_CLOSE" | "BRACKET_OPEN" expression "BRACKET_CLOSE" | "BRACKET_OPEN" assignment "BRACKET_CLOSE"}

    $next = pparse__parser_peek();
    if (in_array($next, array('integer_literal', 'float_literal', 'string_literal', 'true', 'false', 'null'))) { // little trick
        $next = '*literal';
    }
    $suppress_error = ($next == 'SUPPRESS_ERROR');
    if ($suppress_error) {
        pparse__parser_next();
        $next = pparse__parser_peek();
    }
    switch ($next) {
        case 'BW_NOT':
            pparse__parser_next();
            $_expression = _parse_expression_inner();
            $expression = array('BW_NOT', $_expression, $GLOBALS['I']);
            break;

        case 'BOOLEAN_NOT':
            pparse__parser_next();
            $_expression = _parse_expression_inner();
            $expression = array('BOOLEAN_NOT', $_expression, $GLOBALS['I']);
            break;

        case 'SUBTRACT':
            pparse__parser_next();
            $_expression = _parse_expression_inner();
            $expression = array('NEGATE', $_expression, $GLOBALS['I']);
            break;

        case '*literal':
            $literal = _parse_literal();
            $expression = array('LITERAL', $literal, $GLOBALS['I']);
            break;

        case 'IDENTIFIER':
            $next = pparse__parser_peek(true);
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'SCOPE') {
                pparse__parser_next();
                $expression = _parse_expression_inner();
                if ($expression[0] == 'CALL_DIRECT') {
                    $expression[0] = 'CALL_METHOD';
                    $expression[1] = array('VARIABLE', 'this', array('DEREFERENCE', array('VARIABLE', $expression[1], array(), $expression[4]), array(), $expression[4]), $expression[4]);
                }
            } elseif ($next_2 == 'BRACKET_OPEN') { // Is it an inline direct function call
                pparse__parser_next();
                $parameters = _parse_comma_expressions();
                pparse__parser_expect('BRACKET_CLOSE');
                $expression = array('CALL_DIRECT', $next[1], $parameters, $suppress_error, $GLOBALS['I']);
                //log_special('functions', $next[1] . '/' . count($parameters));
            } else {
                if (strtolower($next[1]) == $next[1]) {
                    log_warning('Lower case constant, breaks convention. Likely a variable with a missing $');
                }
                $expression = array('CONSTANT', $next[1], $GLOBALS['I']);
            }
            break;

        case 'NEW':
            pparse__parser_next();
            $identifier = pparse__parser_next(true);
            if (($identifier[0] != 'IDENTIFIER') && ($identifier[0] != 'variable')) {
                parser_error('Expected IDENTIFIER or variable but got ' . $identifier[0]);
            }
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'BRACKET_OPEN') {
                pparse__parser_next();
                $expressions = _parse_comma_expressions();
                pparse__parser_expect('BRACKET_CLOSE');
                $expression = array('NEW_OBJECT', ($identifier[0] == 'IDENTIFIER') ? $identifier[1] : null, $expressions, $GLOBALS['I']);
            } else {
                $expression = array('NEW_OBJECT', ($identifier[0] == 'IDENTIFIER') ? $identifier[1] : null, array(), $GLOBALS['I']);
            }
            break;

        case 'CLONE':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $expression = array('CLONE_OBJECT', $variable, $GLOBALS['I']);
            break;

        case 'ARRAY':
            pparse__parser_next();
            pparse__parser_expect('BRACKET_OPEN');
            $details = _parse_create_array();
            pparse__parser_expect('BRACKET_CLOSE');
            $expression = array('CREATE_ARRAY', $details, $GLOBALS['I']);
            break;

        case 'BRACKET_OPEN':
            pparse__parser_next();

            // Look ahead to see if this is an embedded assignment or a cast
            $next_2 = pparse__parser_peek_dist(0);
            $next_3 = pparse__parser_peek_dist(1);
            if ($next_3 == 'EQUAL') {
                $target = _parse_variable($suppress_error);
                pparse__parser_expect('EQUAL');
                $_expression = _parse_expression();
                $expression = array('EMBEDDED_ASSIGNMENT', 'EQUAL', $target, $_expression, $GLOBALS['I']);
                pparse__parser_expect('BRACKET_CLOSE');
            } elseif ((in_array($next_2, array('INTEGER', 'INT', 'BOOL', 'FLOAT', 'ARRAY', 'OBJECT', 'STRING'))) && ($next_3 == 'BRACKET_CLOSE')) {
                pparse__parser_next();
                pparse__parser_next();
                if ($next_2 == 'INT') {
                    $next_2 = 'INTEGER';
                }
                if ($next_2 == 'BOOL') {
                    $next_2 = 'BOOLEAN';
                }
                $expression = array('CASTED', $next_2, _parse_expression_inner(), $GLOBALS['I']);
            } else {
                $expression = array('BRACKETED', _parse_expression(), $GLOBALS['I']);
                pparse__parser_expect('BRACKET_CLOSE');
            }
            break;

        case 'REFERENCE':
            pparse__parser_next();
            $variable = _parse_variable($suppress_error);
            $expression = array('VARIABLE_REFERENCE', $variable, $GLOBALS['I']);
            break;

        default: // By elimination: Must be a variable or a call chained to a variable
            $expression = _parse_variable($suppress_error, true);
    }
    if (in_array($expression[0], array('CALL_DIRECT', 'CALL_INDIRECT', 'CALL_METHOD'), true)) {
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
    $suppress_error = $suppress_error || ($next[0] == 'SUPPRESS_ERROR');
    if ($next[0] == 'SUPPRESS_ERROR') {
        pparse__parser_next();
        $next = pparse__parser_peek(true);
    }
    if ($variable[0] != 'variable') {
        parser_error('Expected variable ($) but got ' . $variable[0]);
    }

    // Special case where it might be a call on the function name held in a variable
    if ($can_be_dangling_method_call_instead) {
        if ($next == 'BRACKET_OPEN') { // Is it an inline indirect function call
            pparse__parser_next();
            $parameters = _parse_comma_expressions();
            pparse__parser_expect('BRACKET_CLOSE');
            if (count($variable[2]) == 0) {
                log_warning('Indirect call');
            }
            return array('CALL_INDIRECT', $variable, $parameters, $suppress_error, $GLOBALS['I']);
        }
    }

    $variable_chain = _parse_variable_dereferencing_chain_segment($suppress_error/*, $can_be_dangling_method_call_instead*/);
    if ($variable_chain !== array()) {
        // Restructure the chain around any particular calls made
        $actual_expression = array('VARIABLE', $variable[1], $variable_chain, $GLOBALS['I']);

        // Check if it's a true variable
        if ((!$can_be_dangling_method_call_instead) && (_parse_is_non_pure_variable($actual_expression))) {
            parser_error('Expected actual variable but got an expression');
        }
    } else {
        $actual_expression = array('VARIABLE', $variable[1], array(), $GLOBALS['I']);
    }

    if ((isset($GLOBALS['pedantic'])) && (in_array($variable[1], array('_GET', '_POST', '_COOKIE', '_REQUEST', '_FILES', '_SESSION')))) {
        log_warning($variable[1] . ' variable referenced');
    }

    // Canonical check for the start of the chain
    global $FOUND_NON_CANONICAL;
    if ((strtolower($variable[1]) != $variable[1]) && (strtoupper($variable[1]) != $variable[1]) && (!isset($FOUND_NON_CANONICAL[$variable[1]]))) {
        $FOUND_NON_CANONICAL[$variable[1]] = 1;
        log_warning($variable[1] . ' is in non-canonical format');
    }

    return $actual_expression;
}

function _parse_is_non_pure_variable($actual_expression)
{
    if ($actual_expression === null) {
        return false;
    }
    if ($actual_expression == array()) {
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
            $calling = array('VARIABLE', $next_2[1], array(), $GLOBALS['I']);
            $tunnel = array();
            $next_3 = pparse__parser_peek();
            $next_4 = pparse__parser_peek_dist(1);
            if ((($next_3 == 'EXTRACT_OPEN') && ($next_4 != 'EXTRACT_CLOSE')) || ($next_3 == 'OBJECT_OPERATOR') || ($next_3 == 'SCOPE') || ($next_3 == 'BRACKET_OPEN')) {
                $tunnel = _parse_variable_dereferencing_chain_segment($suppress_error);
            }
            $variable = array('DEREFERENCE', $calling, $tunnel, $GLOBALS['I']);
            break;

        case 'EXTRACT_OPEN':
            $next_t = pparse__parser_peek_dist(1);
            if ($next_t == 'EXTRACT_CLOSE') {
                $variable = array();
                break;
            }
            pparse__parser_next();
            $next_2 = pparse__parser_peek(true);
            $expression = _parse_expression();
            pparse__parser_expect('EXTRACT_CLOSE');
            $tunnel = array();
            $next_3 = pparse__parser_peek();
            $next_4 = pparse__parser_peek_dist(1);
            if ((($next_3 == 'EXTRACT_OPEN') && ($next_4 != 'EXTRACT_CLOSE')) || ($next_3 == 'OBJECT_OPERATOR') || ($next_3 == 'SCOPE') || ($next_3 == 'BRACKET_OPEN')) {
                $tunnel = _parse_variable_dereferencing_chain_segment($suppress_error);
            }
            $variable = array('ARRAY_AT', $expression, $tunnel, $GLOBALS['I']);
            break;

        case 'BRACKET_OPEN':
            pparse__parser_next();        // Consume the "("
            $args = _parse_comma_expressions();
            pparse__parser_expect('BRACKET_CLOSE');        // Consume the ")"
            $tunnel = array();
            $next_3 = pparse__parser_peek();
            $next_4 = pparse__parser_peek_dist(1);
            if ((($next_3 == 'EXTRACT_OPEN') && ($next_4 != 'EXTRACT_CLOSE')) || ($next_3 == 'OBJECT_OPERATOR') || ($next_3 == 'SCOPE') || ($next_3 == 'BRACKET_OPEN')) {
                $tunnel = _parse_variable_dereferencing_chain_segment($suppress_error);
            }
            $variable = array('CALL_METHOD', null/*will be subbed later for preceding part of chain*/, $args, $suppress_error, $GLOBALS['I'], $tunnel);
            break;

        /*case 'CURLY_OPEN':  Not in PHP 7
            pparse__parser_next();
            $variable = array('CHAR_OF_STRING', _parse_expression(), $GLOBALS['I']);
            pparse__parser_expect('CURLY_CLOSE');
            break;*/

        default:
            $variable = array();
            break;
    }
    return $variable;
}

function _parse_assignment_operator()
{
    // Choice{"EQUAL" | "CONCAT_EQUAL" | "DIV_EQUAL" | "MUL_EQUAL" | "MINUS_EQUAL" | "PLUS_EQUAL" | "BOR_EQUAL"}

    $next = pparse__parser_next();
    if (!in_array($next, array('EQUAL', 'CONCAT_EQUAL', 'DIV_EQUAL', 'MUL_EQUAL', 'MINUS_EQUAL', 'PLUS_EQUAL', 'BOR_EQUAL'))) {
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
            $literal = array('NEGATE', $_literal, $GLOBALS['I']);
            break;

        case 'integer_literal':
            $_literal = pparse__parser_next(true);
            $literal = array('INTEGER', $_literal[1], $GLOBALS['I']);
            break;

        case 'float_literal':
            $_literal = pparse__parser_next(true);
            $literal = array('FLOAT', $_literal[1], $GLOBALS['I']);
            break;

        case 'string_literal':
            $_literal = pparse__parser_next(true);
            $literal = array('STRING', $_literal[1], $GLOBALS['I']);
            break;

        case 'true':
            pparse__parser_next();
            $literal = array('BOOLEAN', true, $GLOBALS['I']);
            break;

        case 'false':
            pparse__parser_next();
            $literal = array('BOOLEAN', false, $GLOBALS['I']);
            break;

        case 'null':
            pparse__parser_next();
            $literal = array('null', $GLOBALS['I']);
            break;

        case 'IDENTIFIER':
            $_literal = pparse__parser_next(true);
            if (strtolower($_literal[1]) == $_literal[1]) {
                parser_warning('Lower case constant, breaks convention. Likely a variable with a missing $');
            }
            $literal = array('CONSTANT', $_literal[1], $GLOBALS['I']);
            break;

        default:
            parser_error('Expected <literal> but got ' . $next);
    }
    return $literal;
}

function _parse_create_array()
{
    // Choice{list | map}?

    $next = pparse__parser_peek();
    if ($next == 'BRACKET_CLOSE') {
        return array();
    }

    $expression = _parse_expression();
    $next = pparse__parser_peek();
    if ($next == 'DOUBLE_ARROW') {
        pparse__parser_next();
        $expression_2 = _parse_expression();
        $full = array(array($expression, $expression_2));
        if (($expression[0] == 'LITERAL') && (@$expression[1][0] == 'STRING')) {
            unset($expression[1][2]);
            unset($expression[2]);
        }
        $next = pparse__parser_peek();
        $seen = array(serialize($expression) => 1);
        while ($next == 'COMMA') {
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'BRACKET_CLOSE') {
                break;
            }
            $expression = _parse_expression();
            pparse__parser_expect('DOUBLE_ARROW');
            $expression_2 = _parse_expression();
            $full[] = array($expression, $expression_2);
            if (($expression[0] == 'LITERAL') && (@$expression[1][0] == 'STRING')) {
                unset($expression[1][2]);
                unset($expression[2]);
            }
            if (isset($seen[serialize($expression)])) {
                parser_warning('Duplicated key in array creation,' . serialize($expression));
            }
            $seen[serialize($expression)] = 1;
            $next = pparse__parser_peek();
        }
    } else {
        $full = array(array($expression));
        $next = pparse__parser_peek();
        while ($next == 'COMMA') {
            pparse__parser_next();
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'BRACKET_CLOSE') {
                break;
            }
            $expression = _parse_expression();
            $full[] = array($expression);
            $next = pparse__parser_peek();
        }
    }
    return $full;
}

function _parse_comma_expressions()
{
    // Choice{expression "COMMA" comma_expressions | expression}

    $expressions = array();

    $next = pparse__parser_peek();
    if (($next == 'BRACKET_CLOSE') || ($next == 'COMMAND_TERMINATE')) {
        return array();
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

function _parse_comma_variables($allow_blanks = false)
{
    // Choice{"variable" "COMMA" comma_variables | "variable"}

    $variables = array();

    $next = pparse__parser_peek();
    if (($next == 'BRACKET_CLOSE') || ($next == 'COMMAND_TERMINATE')) {
        return $variables;
    }

    do {
        $next_2 = pparse__parser_peek();
        while (($allow_blanks) && (($next_2 == 'COMMA') || ($next_2 == 'BRACKET_CLOSE'))) {
            if ($next_2 == 'COMMA') { // ,,
                pparse__parser_next();
                $variables[] = array('VARIABLE', '_', array());
            } elseif ($next_2 == 'BRACKET_CLOSE') { // ,,
                $variables[] = array('VARIABLE', '_', array());
                return $variables;
            }

            $next_2 = pparse__parser_peek();
        }

        $variable = _parse_variable(false);
        $variables[] = $variable;

        $next_2 = pparse__parser_peek();
        if ($next_2 == 'COMMA') {
            pparse__parser_next();
        }
    } while ($next_2 == 'COMMA');

    return $variables;
}

function _parse_comma_parameters()
{
    // Choice{parameter | parameter "COMMA" comma_parameters}?

    $parameters = array();

    $next = pparse__parser_peek();
    if (($next == 'BRACKET_CLOSE') || ($next == 'COMMAND_TERMINATE')) {
        return $parameters;
    }

    do {
        $parameter = _parse_parameter();
        $parameters[] = $parameter;

        $next_2 = pparse__parser_peek();
        if ($next_2 == 'COMMA') {
            pparse__parser_next();
        }
    } while ($next_2 == 'COMMA');

    return $parameters;
}

function _parse_parameter()
{
    // Choice{"REFERENCE" "variable" | "variable" | "variable" "EQUAL" literal | hint "variable" | hint "REFERENCE" "variable" | hint "variable" "EQUAL" literal}

    $next = pparse__parser_next(true);
    $hint = null;
    switch ($next[0]) {
        case 'ARRAY':
            $hint = 'ARRAY';
        case 'IDENTIFIER':
            if (is_null($hint)) {
                $hint = $next[1];
            }

            // We have a type hint, either an array or a class
            if (pparse__parser_peek() == 'REFERENCE') {
                // Reference with type hint
                pparse__parser_expect('REFERENCE');
                $variable = pparse__parser_expect('variable');
                // 'RECEIVE_BY_REFERENCE' and 'RECEIVE_BY_VALUE' aren't actually used for anything specifically.
                $parameter = array('RECEIVE_BY_REFERENCE', $variable, null, $GLOBALS['I']);
                $parameter['HINT'] = $hint;
            } elseif (pparse__parser_peek() == 'variable') {
                // Variable with type hint
                $var = pparse__parser_expect('variable');
                if (pparse__parser_peek() == 'EQUAL') {
                    // Variable with type hint and default value. This can only be null
                    pparse__parser_next();        // Consume the EQUAL
                    if (pparse__parser_peek() == 'null') {
                        // If the default value is null, the hint is extended to allow null
                        pparse__parser_next();        // Consume the null
                        // 'RECEIVE_BY_REFERENCE' and 'RECEIVE_BY_VALUE' aren't actually used for anything specifically.
                        $parameter = array('RECEIVE_BY_VALUE', $var, null, $GLOBALS['I']);
                        $parameter['HINT'] = '?' . $hint;
                    } else {
                        parser_error('Default arguments for type-hinted parameters can only be null');
                    }
                } else {
                    // 'RECEIVE_BY_REFERENCE' and 'RECEIVE_BY_VALUE' aren't actually used for anything specifically.
                    $parameter = array('RECEIVE_BY_VALUE', $var, null, $GLOBALS['I']);
                    $parameter['HINT'] = $hint;
                }
            } else {
                parser_error('Expecting a variable name or reference, but got ' . pparse__parser_peek());
            }
            break;
        case 'REFERENCE':
            $variable = pparse__parser_expect('variable');
            // 'RECEIVE_BY_REFERENCE' and 'RECEIVE_BY_VALUE' aren't actually used for anything specifically.
            if (pparse__parser_peek() == 'EQUAL') {
                // Variable with type hint and default value. This can only be null
                pparse__parser_next();        // Consume the EQUAL
                if (pparse__parser_peek() == 'null') {
                    // If the default value is null, the hint is extended to allow null
                    pparse__parser_next();        // Consume the null
                    // 'RECEIVE_BY_REFERENCE' and 'RECEIVE_BY_VALUE' aren't actually used for anything specifically.
                    $parameter = array('RECEIVE_BY_REFERENCE', $variable, null, $GLOBALS['I']);
                    $parameter['HINT'] = '?' . $hint;
                } else {
                    parser_error('Default arguments for referenced parameters can only be null');
                }
            } else {
                $parameter = array('RECEIVE_BY_REFERENCE', $variable, null, $GLOBALS['I']);
            }
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'EQUAL') {
                pparse__parser_next();
                $value = _parse_literal();
                $parameter[2] = $value;
            }
            break;

        case 'variable':
            // 'RECEIVE_BY_REFERENCE' and 'RECEIVE_BY_VALUE' aren't actually used for anything specifically.
            $parameter = array('RECEIVE_BY_VALUE', $next[1], null, $GLOBALS['I']);
            $next_2 = pparse__parser_peek();
            if ($next_2 == 'EQUAL') {
                pparse__parser_next();
                $value = _parse_literal();
                $parameter[2] = $value;
            }
            break;

        default:
            parser_error('Expected <parameter> but got ' . $next[0]);
    }
    return $parameter;
}

function pparse__parser_expect($token)
{
    global $TOKENS, $I;
    if (!isset($TOKENS[$I])) {
        parser_error('Ran out of input when expecting ' . $token, $TOKENS);
    }
    $next = $TOKENS[$I];
    if ($next[0] == 'comment') {
        handle_comment($next);
        $I++;
        return pparse__parser_expect($token);
    }
    $I++;
    if ($next[0] != $token) {
        parser_error('Expected ' . $token . ' but got ' . $next[0] . ' (' . $next[1] . ')', $TOKENS);
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
    if (is_null($p)) {
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
    if (isset($GLOBALS['TODO'])) {
        if (strpos($comment[1], 'TODO') !== false) {
            log_warning('TODO comment found (' . str_replace("\n", ' ', trim($comment[1])) . ')', $GLOBALS['I']);
        }
        if (strpos($comment[1], 'FIXME') !== false) {
            log_warning('FIXME comment found (' . str_replace("\n", ' ', trim($comment[1])) . ')', $GLOBALS['I']);
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
        //if (strpos($comment[1], 'XHTMLXHTML') !== false) log_warning('XHTMLXHTML comment found', $GLOBALS['I']); Don't want to report these
    }
}

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

// Load up table info
global $TABLE_FIELDS, $COMPOSR_PATH;
if (file_exists($COMPOSR_PATH . '/data/db_meta.bin') && (filemtime($COMPOSR_PATH . '/index.php') < filemtime($COMPOSR_PATH . '/data/db_meta.bin'))) {
    $_table_fields = unserialize(file_get_contents($COMPOSR_PATH . '/data/db_meta.bin'));
    $TABLE_FIELDS = $_table_fields['tables'];
} else {
    $TABLE_FIELDS = [];
}
$TABLE_FIELDS['db_meta'] = [
    'addon' => 'core',
    'fields' => [
        'm_table' => '*ID_TEXT',
        'm_name' => '*ID_TEXT',
        'm_type' => 'ID_TEXT',
    ]
];
$TABLE_FIELDS['db_meta_indices'] = [
    'addon' => 'core',
    'fields' => [
        'i_table' => '*ID_TEXT',
        'i_name' => '*ID_TEXT',
        'i_fields' => '*ID_TEXT',
    ]
];

global $FUNCTION_SIGNATURES;
$FUNCTION_SIGNATURES = [];
if (!empty($GLOBALS['FLAG__API'])) {
    load_function_signatures();
}

load_php_metadetails();

function load_function_signatures()
{
    // Load up function info
    global $FUNCTION_SIGNATURES, $COMPOSR_PATH;
    $functions_file_path = file_exists($COMPOSR_PATH . '/data_custom/functions.bin') ? ($COMPOSR_PATH . '/data_custom/functions.bin') : 'functions.bin';
    $functions_file = file_get_contents($functions_file_path);
    $FUNCTION_SIGNATURES = unserialize($functions_file);
}

// Do the actual code quality check
function check($structure)
{
    global $GLOBAL_VARIABLES, $CURRENT_CLASS, $OK_EXTRA_FUNCTIONS, $STRUCTURE;
    $GLOBAL_VARIABLES = [];
    $OK_EXTRA_FUNCTIONS = $structure['ok_extra_functions'];
    $STRUCTURE = $structure;

    $CURRENT_CLASS = '__global';
    global $LOCAL_VARIABLES;
    $LOCAL_VARIABLES = reinitialise_local_variables();
    if ($GLOBALS['OK_EXTRA_FUNCTIONS'] == '') { // Useful for tests to be able to define functions natively
        foreach ($structure['functions'] as $function) {
            if ($GLOBALS['OK_EXTRA_FUNCTIONS'] != '') {
                $GLOBALS['OK_EXTRA_FUNCTIONS'] .= '|';
            }
            $GLOBALS['OK_EXTRA_FUNCTIONS'] .= $function['name'];
        }
        foreach ($structure['classes'] as $class) {
            if ($GLOBALS['OK_EXTRA_FUNCTIONS'] != '') {
                $GLOBALS['OK_EXTRA_FUNCTIONS'] .= '|';
            }
            $GLOBALS['OK_EXTRA_FUNCTIONS'] .= $class['name'];
        }
    }
    check_command($structure['main'], 0);
    $local_variables = $LOCAL_VARIABLES;
    foreach ($structure['functions'] as $function) {
        check_function($function);
    }
    foreach ($structure['classes'] as $class) {
        if (!empty($GLOBALS['FLAG__API'])) {
            if (substr($class['name'], 0, 1) == strtolower(substr($class['name'], 0, 1))) {
                log_warning('Class names should start with an upper case letter, \'' . $class['name'] . '\'');
            }
        }

        /*if (substr($class['name'], 1) != strtolower(substr($class['name'], 1))) {     Too Composr-specific
            log_warning('Class names should be lower case apart from the first letter, \'' . $class['name'] . '\'');
        }*/

        $CURRENT_CLASS = $class['name'];
        foreach ($class['functions'] as $function) {
            if (strtolower($function['name']) == strtolower($class['name'])) {
                log_warning('Use __construct for construct name, not \'' . $function['name'] . '\'', $function['offset']);
            }

            check_function($function, false, true);
        }

        foreach ($class['vars'] as $var) {
            check_expression($var[1]);
        }

        foreach ($class['constants'] as $constant) {
            check_expression($constant[1]);
        }
    }
    check_variable_list($local_variables, 0);

    // Check for type conflicts in the global variables
    check_variable_list($GLOBAL_VARIABLES);
}

function check_function($function, $is_closure = false, $inside_class = false)
{
    global $GLOBAL_VARIABLES, $LOCAL_VARIABLES, $CURRENT_CLASS;

    if ($is_closure) {
        $bak = $LOCAL_VARIABLES;
    }

    $LOCAL_VARIABLES = reinitialise_local_variables($inside_class); // Map (by name) of maps : is_global, types. Note there is boolean-false and null types: boolean_false is when we KNOW a boolean is false, so it might map to ~

    //if (!empty($GLOBALS['FLAG__PEDANTIC'])) if (strlen(serialize($function)) > 30000) log_warning('Function ' . $function['name'] . ' is too big', $function['offset']);

    global $FUNCTION_SIGNATURES;
    $class = $CURRENT_CLASS;
    if (isset($FUNCTION_SIGNATURES[$class]['functions'][$function['name']])) {
        $func = $FUNCTION_SIGNATURES[$class]['functions'][$function['name']];
    } else {
        $func = null;
    }

    // Initialise any local variables that come from parameters
    foreach ($function['parameters'] as $p) {
        add_variable_reference($p[1], $function['offset'], false);
        if (isset($func)) {
            foreach ($func['parameters'] as $x) {
                if ((isset($x['name'])) && ($x['name'] == $p[1])) {
                    set_composr_type($p[1], $x['type']);
                    break;
                }
            }
        } else {
            set_composr_type($p[1], 'mixed');
        }
    }

    // Initialise any local variables that come from the closure
    if ($is_closure) {
        foreach ($function['using'] as $p) {
            add_variable_reference($p[1], $function['offset'], false);
            if (isset($bak[$p[1]])) {
                $LOCAL_VARIABLES[$p[1]] = $bak[$p[1]];
            } else {
                log_warning('Variable \'' . $p[1] . '\' is referenced in closure but not defined.', $function['offset']);
            }
        }
    }

    // Check commands
    check_command($function['code'], 0);

    // Check for type conflicts in the variables
    check_variable_list($LOCAL_VARIABLES, $function['offset']);

    // Update global variables
    foreach ($LOCAL_VARIABLES as $name => $v) {
        if ($v['is_global']) {
            if (isset($GLOBAL_VARIABLES[$name])) {
                $GLOBAL_VARIABLES[$name]['types'] = array_merge($GLOBAL_VARIABLES[$name]['types'], $v['types']);
            } else {
                $GLOBAL_VARIABLES[$name] = $v;
            }
        }
    }

    // Return stuff
    if (isset($func)) {
        $ret = (isset($func['return']));

        // Check a return is given if the function returns and the opposite
        if (($ret) && (!in_array('abstract', $function['modifiers'])) && (!isset($LOCAL_VARIABLES['__return']))) {
            log_warning('Function \'' . $function['name'] . '\' is missing a return statement with a value', $function['offset']);
        }
        if ((!$ret) && (isset($LOCAL_VARIABLES['__return'])) && ($LOCAL_VARIABLES['__return']['types'] != [])) {
            log_warning('Function \'' . $function['name'] . '\' has a return with a value, and the function returns void', $LOCAL_VARIABLES['__return']['first_mention']);
        }

        // Check return types
        if (($ret) && (isset($LOCAL_VARIABLES['__return']['types']))) {
            foreach ($LOCAL_VARIABLES['__return']['types'] as $i => $ret_type) {
                ensure_type([$func['return']['type']], $ret_type, $LOCAL_VARIABLES['__return']['mentions'][$i], 'Bad return type (should be ' . $func['return']['type'] . ' not ' . $ret_type . ')');
            }
        }
    }

    if ($is_closure) {
        foreach ($function['using'] as $p) {
            add_variable_reference($p[1], $function['offset'], false);
            if (isset($bak[$p[1]])) {
                $bak[$p[1]] = $LOCAL_VARIABLES[$p[1]];
            }
        }
        $LOCAL_VARIABLES = $bak;
    }
}

function check_variable_list($LOCAL_VARIABLES, $offset = -1)
{
    foreach ($LOCAL_VARIABLES as $name => $v) {
        // Check for type conflicts
        $observed_types = [];
        if (!empty($GLOBALS['FLAG__PEDANTIC'])) {
            foreach ($v['conditioner'] as $conditioner) {
                if (((!$v['conditioned_null']) && (isset($GLOBALS['NULL_ERROR_FUNCS'][$conditioner]))) || ((!$v['conditioned_false']) && (isset($GLOBALS['FALSE_ERROR_FUNCS'][$conditioner])))) {
                    log_warning('Error value was not handled', $v['first_mention']);
                    break;
                } elseif (($conditioner == '_divide_') && (!$v['conditioned_zero'])) {
                    log_warning('Divide by zero possibility was not handled', $v['first_mention']);
                    break;
                }
            }
        }
        foreach ($v['types'] as $t) {
            if (is_array($t)) {
                $t = $t[0];
            }

            $t = ltrim($t, '?~');
            if (substr($t, 0, 6) == 'object') {
                $t = 'object';
            }
            if ($t == 'REAL') {
                $t = 'float';
            }
            if (in_array($t, ['MEMBER', 'SHORT_INTEGER', 'UINTEGER', 'AUTO_LINK', 'BINARY', 'GROUP', 'TIME'])) {
                $t = 'integer';
            }
            if (in_array($t, ['LONG_TEXT', 'SHORT_TEXT', 'MINIID_TEXT', 'ID_TEXT', 'LANGUAGE_NAME', 'URLPATH', 'PATH', 'IP', 'EMAIL'])) {
                $t = 'string';
            }
            if (in_array($t, ['Tempcode'])) {
                $t = 'object';
            }
            if (in_array($t, ['list', 'map'])) {
                $t = 'array';
            }
            if ($t != 'mixed') {
                $observed_types[$t] = 1;
            }
        }
        if (array_keys($observed_types) != ['array', 'resource']) {
            if (
                (count($observed_types) > 3) ||
                ((count($observed_types) > 1) && (!isset($observed_types['boolean-false'])) && (!isset($observed_types['null']))) ||
                ((count($observed_types) > 2) && ((!isset($observed_types['boolean-false'])) || (!isset($observed_types['null'])))
                )
            ) {
                if (($name != '_') && ($name != '__return') && (!$v['mixed_tag'])) {
                    log_warning('Type conflict for variable: ' . $name . ' (' . implode(',', array_keys($observed_types)) . ')', $v['first_mention']);
                }
            }
        }

        // Check for solely mixed
        if (!empty($GLOBALS['FLAG__MIXED'])) {
            $non_mixed = false;
            foreach ($v['types'] as $t) {
                if ($t != 'mixed') {
                    $non_mixed = true;
                }
            }
            if ((!$non_mixed) && (!empty($v['types']))) {
                log_warning('Solely mixed variable: ' . $name, $v['first_mention']);
            }
        }

        // Check for non-used variables
        if (($GLOBALS['FILENAME'] != 'sources\phpstub.php') && ($v['references'] == 0) && ($name != '__return') && ($name != '_') && (!$v['is_global']) && (!in_array($name, ['db', 'file_base', 'table_prefix', 'old_base_dir', 'upgrade_from_hack', 'upgrade_from', 'this', 'GLOBALS', 'http_response_header',/*'_GET','_POST','_REQUEST','_COOKIE','_SERVER','_ENV', These are intentionally removed as they should only be used at one point in the code*/'_SESSION', '_FILES']))) {
            if (!$v['unused_value']) {
                log_warning('Non-used ' . ($v['unused_value'] ? 'value' : 'variable') . ' (\'' . $name . '\')', $v['first_mention']);
            }
        }
    }
}

function check_command($command, $depth, $function_guard = '', $nogo_parameters = [], $jump_structures = [])
{
    global $LOCAL_VARIABLES, $CURRENT_CLASS, $FUNCTION_SIGNATURES;
    foreach ($command as $i => $c) {
        if ($c == []) {
            continue;
        }

        if (is_integer($c[count($c) - 1])) {
            $c_pos = $c[count($c) - 1];
            $or = false;
        } else {
            $c_pos = $c[count($c) - 2];
            $or = true;
        }

        switch ($c[0]) {
            case 'CALL_METHOD':
                check_method($c, $c_pos, $function_guard);
                break;
            case 'CALL_INDIRECT':
                add_variable_reference($c[1][1], $c_pos);
                break;
            case 'VARIABLE':
                check_variable($c, false, $function_guard);
                break;
            case 'CALL_DIRECT':
                if (!empty($GLOBALS['FLAG__PEDANTIC'])) {
                    if ((isset($GLOBALS['NULL_ERROR_FUNCS'][$c[1]])) || (isset($GLOBALS['FALSE_ERROR_FUNCS'][$c[1]]))) {
                        log_warning('Crucial return value was not handled', $c_pos);
                    }
                }
                check_call($c, $c_pos, null, $function_guard);
                break;
            case 'GLOBAL':
                foreach ($c[1] as $v) {
                    if ((isset($LOCAL_VARIABLES[$v[1]])) && (!$LOCAL_VARIABLES[$v[1]]['is_global'])) {
                        log_warning($v[1] . ' was referenced before this globalisation.', $c_pos);
                    }
                    add_variable_reference($v[1], $c_pos, false);
                    $LOCAL_VARIABLES[$v[1]]['is_global'] = true;
                    $LOCAL_VARIABLES[$v[1]]['unused_value'] = true;
                }
                break;
            case 'YIELD_0':
                break;
            case 'YIELD_1':
                check_expression($c[1], false, false, $function_guard);
                break;
            case 'YIELD_2':
                check_expression($c[1], false, false, $function_guard);
                check_expression($c[2], false, false, $function_guard);
                break;
            case 'RETURN':
                if ($c[1] !== null) {
                    $ret_type = check_expression($c[1], false, false, $function_guard);
                    add_variable_reference('__return', $c_pos);
                    set_composr_type('__return', $ret_type);
                    if (!isset($LOCAL_VARIABLES['__return']['mentions'])) {
                        $LOCAL_VARIABLES['__return']['mentions'] = [];
                    }
                    $LOCAL_VARIABLES['__return']['mentions'][] = $c_pos;
                }
                if (count($command) - 1 > $i) {
                    log_warning('There is unreachable code (after a return statement)', $c_pos);
                }
                break;
            case 'SWITCH':
                $switch_type = check_expression($c[1], false, false, $function_guard);
                foreach ($c[2] as $case) {
                    if ($case[0] !== null) {
                        $passes = ensure_type([$switch_type], check_expression($case[0], false, false, $function_guard), $c_pos, 'Switch type inconsistency');
                        if ($passes) {
                            infer_expression_type_to_variable_type($switch_type, $case[0]);
                        }
                    }
                    check_command($case[1], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, ['SWITCH']));
                }
                break;
            case 'STATIC_ASSIGNMENT':
                foreach ($c[1] as $_c) {
                    check_assignment($_c, $c_pos, $function_guard);
                }
                break;
            case 'ASSIGNMENT':
                check_assignment($c, $c_pos, $function_guard);
                break;
            case 'IF':
            case 'IF_ELSE':
                $t = check_expression($c[1], false, false, $function_guard);
                if ($c[0] == 'IF_ELSE') {
                    $passes = ensure_type(['boolean'], $t, $c_pos, 'Conditionals must be boolean (if-else) [is ' . $t . ']', true);
                } else {
                    $passes = ensure_type(['boolean'], $t, $c_pos, 'Conditionals must be boolean (if) [is ' . $t . ']', true);
                }
                if ($passes) {
                    infer_expression_type_to_variable_type('boolean', $c[1]);
                }
                $temp_function_guard = $function_guard;
                foreach ([0, 1] as $function_parameter_pos) {
                    if (($c[1][0] == 'BOOLEAN_NOT') && ($c[1][1][0] == 'CALL_DIRECT') && ($c[1][1][1] == 'php_function_allowed' || strpos($c[1][1][1], '_exists') !== false) && (isset($c[1][1][2][$function_parameter_pos])) && ($c[1][1][2][$function_parameter_pos][0][0][0] == 'LITERAL') && ($c[1][1][2][$function_parameter_pos][1][0][0] == 'STRING') && (($c[2][0][0] == 'BREAK') || ($c[2][0][0] == 'CONTINUE') || ($c[2][0][0] == 'RETURN') || (($c[2][0][0] == 'CALL_DIRECT') && ($c[2][0][1] == 'critical_error')))) {
                        $temp_function_guard .= ',' . $c[1][1][2][$function_parameter_pos][0][1][1] . ',';
                    }
                    if (($c[1][0] == 'CALL_DIRECT') && ($c[1][1] == 'php_function_allowed' || strpos($c[1][1], '_exists') !== false) && (isset($c[1][2][$function_parameter_pos])) && ($c[1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($c[1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                        $temp_function_guard .= ',' . $c[1][2][$function_parameter_pos][0][1][1] . ',';
                    }

                    foreach ([0, 1] as $and_position) { // NB: Can't check 3rd AND position because this is actually nested AND's, so we'd need to write recursive code or more hard-coded checking
                        if (($c[1][0] == 'BOOLEAN_AND') && ($c[1][$and_position + 1][0] == 'CALL_DIRECT') && ($c[1][$and_position + 1][1] == 'php_function_allowed' || strpos($c[1][$and_position + 1][1], '_exists') !== false) && (isset($c[1][$and_position + 1][2][$function_parameter_pos])) && ($c[1][$and_position + 1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($c[1][$and_position + 1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                            $temp_function_guard .= ',' . $c[1][$and_position + 1][2][$function_parameter_pos][0][1][1] . ',';
                        }
                        if (($c[1][0] == 'BOOLEAN_AND') && ($c[1][$and_position + 1][0] == 'PARENTHESISED') && ($c[1][$and_position + 1][1][0] == 'CALL_DIRECT') && ($c[1][$and_position + 1][1][1] == 'php_function_allowed' || strpos($c[1][$and_position + 1][1][1], '_exists') !== false) && (isset($c[1][$and_position + 1][1][2][$function_parameter_pos])) && ($c[1][$and_position + 1][1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($c[1][$and_position + 1][1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                            $temp_function_guard .= ',' . $c[1][$and_position + 1][1][2][$function_parameter_pos][0][1][1] . ',';
                        }
                    }
                }
                check_command($c[2], $depth, $temp_function_guard, $nogo_parameters, $jump_structures);
                if ($c[0] == 'IF_ELSE') {
                    check_command($c[3], $depth, $function_guard, $nogo_parameters, $jump_structures);
                }
                break;
            case 'INNER_FUNCTION':
                $temp = $LOCAL_VARIABLES;
                check_function($c[1]);
                $LOCAL_VARIABLES = $temp;
                break;
            case 'INNER_CLASS':
                $class = $c[1];
                foreach ($class['functions'] as $function) {
                    $temp = $LOCAL_VARIABLES;
                    $LOCAL_VARIABLES['this'] = ['is_global' => false, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['object'], 'references' => 0, 'object_type' => $CURRENT_CLASS, 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false];
                    check_function($function, false, true);
                    $LOCAL_VARIABLES = $temp;
                }
                break;
            case 'TRY':
                check_command($c[1], $depth + 1, $function_guard, $nogo_parameters, $jump_structures); // Goes first so that we get local variables defined inside loop for use in our loop conditional
                foreach ($c[2] as $catch) {
                    add_variable_reference($catch[1][0][1], $c_pos, false);
                    check_command($catch[2], $depth + 1, $function_guard, $nogo_parameters, $jump_structures); // Goes first so that we get local variables defined inside loop for use in our loop conditional
                }
                if ($c[3] !== null) {
                    check_command($c[3], $depth + 1, $function_guard, $nogo_parameters, $jump_structures); // Finally
                }
                break;
            case 'FOREACH_map':
                $passes = ensure_type(['array'], check_expression($c[1], false, false, $function_guard), $c_pos, 'Foreach must take array');
                if ($passes) {
                    infer_expression_type_to_variable_type('array', $c[1]);
                }
                add_variable_reference($c[2][1], $c_pos, false);
                if ($c[3][0] == 'LIST') {
                    foreach ($c[3][1] as $var) {
                        if (empty($var[2])) {
                            add_variable_reference($var[1], $c_pos, false);
                        }
                    }
                } else {
                    add_variable_reference($c[3][1], $c_pos, false);
                }

                if (in_array($c[2][1], $nogo_parameters)) {
                    log_warning('Re-using a loop variable, ' . $c[2][1], $c_pos);
                }
                if (in_array($c[3][1], $nogo_parameters)) {
                    log_warning('Re-using a loop variable, ' . $c[3][1], $c_pos);
                }

                check_command($c[4], $depth + 1, $function_guard, array_merge($nogo_parameters, [$c[2][1], $c[3][1]]), array_merge($jump_structures, ['FOREACH_map']));
                break;
            case 'FOREACH_list':
                $passes = ensure_type(['array'], check_expression($c[1], false, false, $function_guard), $c_pos, 'Foreach must take array');
                if ($passes) {
                    infer_expression_type_to_variable_type('array', $c[1]);
                }
                if ($c[2][0] == 'LIST') {
                    foreach ($c[2][1] as $var) {
                        if (empty($var[2])) {
                            add_variable_reference($var[1], $c_pos, false);
                        }
                    }
                } else {
                    add_variable_reference($c[2][1], $c_pos, false);
                }

                if (in_array($c[2][1], $nogo_parameters)) {
                    log_warning('Re-using a loop variable, ' . $c[2][1], $c_pos);
                }

                check_command($c[3], $depth + 1, $function_guard, array_merge($nogo_parameters, [$c[2][1]]), array_merge($jump_structures, ['FOREACH_list']));
                break;
            case 'FOR':
                if ($c[1] !== null) {
                    check_command([$c[1]], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, ['FOR']));
                }
                check_command([$c[3]], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, ['FOR']));
                $passes = ensure_type(['boolean'], check_expression($c[2], false, false, $function_guard), $c_pos, 'Conditionals must be boolean (for)', true);
                if ($passes) {
                    infer_expression_type_to_variable_type('boolean', $c[2]);
                }
                check_command($c[4], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, ['FOR']));
                break;
            case 'DO':
                check_command($c[2], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, ['DO'])); // Goes first so that we get local variables defined inside loop for use in our loop conditional
                $passes = ensure_type(['boolean'], check_expression($c[1], false, false, $function_guard), $c_pos, 'Conditionals must be boolean (do)', true);
                if ($passes) {
                    infer_expression_type_to_variable_type('boolean', $c[1]);
                }
                break;
            case 'WHILE':
                $passes = ensure_type(['boolean'], check_expression($c[1], false, false, $function_guard), $c_pos, 'Conditionals must be boolean (while)', true);
                if ($passes) {
                    infer_expression_type_to_variable_type('boolean', $c[1]);
                }
                check_command($c[2], $depth + 1, $function_guard, $nogo_parameters, array_merge($jump_structures, ['WHILE']));
                break;
            case 'CONTINUE':
                if (($c[1][0] == 'SOLO') && ($c[1][1][0] == 'LITERAL') && ($c[1][1][1][0] == 'INTEGER')) {
                    $continue_level = $c[1][1][1][1];
                } elseif (($c[1][0] == 'LITERAL') && ($c[1][1][0] == 'INTEGER')) {
                    $continue_level = $c[1][1][1];
                } else {
                    $continue_level = 1;
                }

                if ($continue_level > count($jump_structures)) {
                    if (empty($jump_structures)) {
                        log_warning('Nothing to continue out of', $c_pos);
                    } else {
                        log_warning('Continue level greater than loop/switch depth', $c_pos);
                        log_warning('Continue level greater than loop/switch depth', $c_pos);
                    }
                } else {
                    if ($jump_structures[count($jump_structures) - $continue_level] == 'SWITCH') {
                        log_warning('Cannot continue by level ' . $continue_level . ', as it is a switch statement', $c_pos);
                    }
                }

                $passes = ensure_type(['integer'], check_expression($c[1], false, false, $function_guard), $c_pos, 'Loop/switch control must use integers (continue)');
                if ($passes) {
                    infer_expression_type_to_variable_type('integer', $c[1]);
                }
                break;
            case 'BREAK':
                $passes = ensure_type(['integer'], check_expression($c[1], false, false, $function_guard), $c_pos, 'Loop/switch control must use integers (break)');
                if ($passes) {
                    infer_expression_type_to_variable_type('integer', $c[1]);
                }

                if (empty($jump_structures)) {
                    log_warning('Nothing to break out of', $c_pos);
                }
                break;
            case 'PRE_DEC':
                ensure_type(['integer', 'float'], check_variable($c[1], false, $function_guard), $c_pos, 'Can only decrement numbers');
                break;
            case 'PRE_INC':
                ensure_type(['integer', 'float'], check_variable($c[1], false, $function_guard), $c_pos, 'Can only increment numbers');
                break;
            case 'DEC':
                ensure_type(['integer', 'float'], check_variable($c[1], false, $function_guard), $c_pos, 'Can only decrement numbers');
                break;
            case 'INC':
                ensure_type(['integer', 'float'], check_variable($c[1], false, $function_guard), $c_pos, 'Can only increment numbers');
                break;
            case 'ECHO':
                foreach ($c[1] as $e) {
                    $passes = ensure_type(['string'], check_expression($e, false, false, $function_guard), $c_pos, 'Can only echo strings');
                    if ($passes) {
                        infer_expression_type_to_variable_type('string', $e);
                    }
                }
                break;
        }

        if ($or) {
            check_command([$c[count($c) - 1]], $depth, $function_guard, $nogo_parameters, $jump_structures);
        }
    }
}

function check_method($c, $c_pos, $function_guard = '')
{
    global $LOCAL_VARIABLES, $FUNCTION_SIGNATURES;

    $params = $c[2];

    if ($c[1] !== null) {
        check_variable($c[1], false, $function_guard);

        // Special rule for 'this->db'
        if (($c[1][1] == 'this') && ($c[1][2][1][1] == 'db') && ((!isset($c[1][2][2][0])) || ($c[1][2][2][0] != 'DEREFERENCE'))) {
            $method = $c[1][2][2][1][1];
            $class = 'DatabaseConnector';
            return actual_check_method($class, $method, $params, $c_pos, $function_guard);
        }

        // Special rule for $GLOBALS['?_DB']
        if (($c[1][1] == 'GLOBALS') && (substr($c[1][2][1][1][0], -3) == 'LITERAL') && (substr($c[1][2][1][1][1], -3) == '_DB') && ((!isset($c[1][2][2][2][0])) || ($c[1][2][2][2][0] != 'DEREFERENCE'))) {
            $method = $c[1][2][2][1][1];
            $class = 'DatabaseConnector';
            return actual_check_method($class, $method, $params, $c_pos, $function_guard);
        }

        // Special rule for $GLOBALS['FORUM_DRIVER']
        if (($c[1][1] == 'GLOBALS') && (substr($c[1][2][1][1][0], -3) == 'LITERAL') && ($c[1][2][1][1][1] == 'FORUM_DRIVER')) {
            $method = $c[1][2][2][1][1];
            $class = 'Forum_driver_base';
            return actual_check_method($class, $method, $params, $c_pos, $function_guard);
        }

        if ((isset($c[1][2][0])) && ($c[1][2][0] == 'DEREFERENCE') && (empty($c[1][2][2]))) {
            $object = $c[1][1];
            $method = $c[1][2][1][1];
            add_variable_reference($object, $c_pos);

            if (((count($LOCAL_VARIABLES[$object]['types']) == 1) && ($LOCAL_VARIABLES[$object]['types'][0] == 'Tempcode')) || (isset($FUNCTION_SIGNATURES[$LOCAL_VARIABLES[$object]['object_type']]))) { // Construction
                $class = substr($LOCAL_VARIABLES[$object]['types'][0], 6);
                return actual_check_method($class, $method, $params, $c_pos, $function_guard);
            } else {
                // Parameters
                foreach ($params as $e) {
                    check_expression($e[0], false, false, $function_guard);
                }

                return 'mixed';
            }
        }

        scan_extractive_expressions($c[1][2]);
    }
    // Parameters
    foreach ($params as $e) {
        check_expression($e[0], false, false, $function_guard);
    }

    return 'mixed';
}

function actual_check_method($class, $method, $params, $c_pos, $function_guard = '')
{
    // Parameters
    foreach ($params as $e) {
        check_expression($e[0], false, false, $function_guard);
    }

    return check_call(['CALL_DIRECT', $method, $params], $c_pos, $class, $function_guard);
}

function check_call($c, $c_pos, $class = null, $function_guard = '')
{
    global $CURRENT_CLASS;
    if ($class === null) {
        $class = '__global';
    }
    $class = preg_replace('#^(\?|~|object-)*#', '', $class);

    $params = $c[2];

    if ((!empty($GLOBALS['FLAG__PEDANTIC'])) && (array_key_exists(3, $c)) && (!$c[3])) {
        if (((isset($GLOBALS['VAR_ERROR_FUNCS'][$c[1]])) && (@$params[1][0][0] == 'VARIABLE')) || (isset($GLOBALS['ERROR_FUNCS'][$c[1]]))) {
            log_warning('Check this call is error-caught', $c_pos);
        }
    }

    $function = $c[1];
    if (!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) {
        global $EXT_FUNCS;
        if (isset($EXT_FUNCS[$function])) {
            log_warning('Check for function_exists usage around ' . $function, $c_pos);
        }
    }
    $_function = ($class == '__global') ? $function : ($class . '.' . $function);
    if (!empty($GLOBALS['FLAG__SECURITY'])) {
        global $INSECURE_FUNCTIONS;
        if (in_array($_function, $INSECURE_FUNCTIONS)) {
            log_warning('Call to insecure function (' . $_function . ')', $c_pos);
        }
    }
    global $FUNCTION_SIGNATURES, $STRUCTURE;
    $ret = null;
    $found = false;

    if (isset($FUNCTION_SIGNATURES[$class]['functions'][$function])) {
        $potential = $FUNCTION_SIGNATURES[$class]['functions'][$function];
    } else {
        $potential = null;
    }
    if (($potential === null) && ($class == 'Forum_driver_base')) {
        $class = 'Forum_driver_cns';
        if (isset($FUNCTION_SIGNATURES[$class]['functions'][$function])) {
            $potential = $FUNCTION_SIGNATURES[$class]['functions'][$function];
        } else {
            $potential = null;
        }
    }

    if ($class == 'DatabaseConnector') {
        if ((count($params) >= 2) && ($params[0][0][0] == 'LITERAL')) {
            $table = $params[0][0][1][1];
            if ($function == 'query_insert') {
                $map = check_db_map($table, $params[1][0], $c_pos, true);
            }
            if ($function == 'query_update') {
                check_db_map($table, $params[1][0], $c_pos);
                if (isset($params[2][0])) {
                    check_db_map($table, $params[2][0], $c_pos);
                }
            }
            if ($function == 'query_select') {
                check_db_fields($table, $params[1][0], $c_pos);
                if (isset($params[2][0])) {
                    check_db_map($table, $params[2][0], $c_pos);
                }
            }
            if (($function == 'query_select_value') || ($function == 'query_select_value_if_there')) {
                check_db_field($table, $params[1][0], $c_pos);
                if (isset($params[2][0])) {
                    check_db_map($table, $params[2][0], $c_pos);
                }
            }
        }
    }

    foreach ($params as $param) {
        if ($param[0][0] == 'VARIABLE_REFERENCE') {
            log_warning('Call by reference to function \'' . $function . '\'', $c_pos);
        }
    }

    if ($potential !== null) {
        $found = true;
        if (isset($potential['return'])) {
            $ret = $potential['return'];
        }
        foreach ($potential['parameters'] as $i => $param) {
            if ((!isset($params[$i])) && ((empty($params)) || (!$params[count($params) - 1][1]/*not a vadiadic call*/)) && (!$param['is_variadic']/*not variadic parameter spot*/) && (!array_key_exists('default', $param))) {
                log_warning('Insufficient parameters to function \'' . $function . '\'', $c_pos);
                break;
            }
            if (isset($params[$i])) {
                $temp = $params[$i][0];

                // Can't pass references
                if (($temp[0] == 'SOLO') && (is_array($temp[1])) && ($temp[1][0] == 'VARIABLE_REFERENCE')) {
                    log_warning('Reference parameter passed to function \'' . $function . '\'', $c_pos);
                    break;
                }

                // If it is a referenced parameter then we must pass a variable expression not a general expression
                if ((@$param['ref']) && ($temp[0] != 'VARIABLE')) {
                    log_warning('A referenced parameter for \'' . $function . '\' was given a non-variable expression', $c_pos);
                    break;
                }

                $t = check_expression($temp, false, false, $function_guard);
                $passes = ensure_type([$param['type']], $t, $c_pos, 'Parameter type error for ' . $function . '/' . ($i + 1) . ' (should be ' . $param['type'] . ' not ' . $t . ')');
                if (($t === 'float') && ($function === 'strval')) {
                    log_warning('Floats should not be used with strval, use float_to_raw_string or float_format', $c_pos);
                }
                if (!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) {
                    if (($t === 'mixed') && ($function === 'count') && (strpos($c[2][0][1], 'rows') === false)) {
                        log_warning('Make sure that count parameter is definitely counting an array', $c_pos);
                    }
                }
                if ($passes) {
                    infer_expression_type_to_variable_type($param['type'], $temp);
                }
            } else {
                break;
            }
        }
        if ((count($potential['parameters']) < count($params)) && (!empty($potential['parameters'])) && (!$potential['parameters'][count($potential['parameters']) - 1]['is_variadic']/*not a variadic function*/)) {
            log_warning('Too many parameters to function \'' . $function . '\'', $c_pos);
        }

        // Look for file-creators, and give notice that chmoding might be required to allow it to be deleted via FTP
        if (in_array('creates-file', $potential['flags'])) {
            if (!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) {
                if (($function == 'fopen') && (in_array(@$params[1][0][1][1][0], ['w', 'a']))) {
                    log_warning('Call to \'' . $function . '\' that may create a file/folder. Check that the code chmods it so that FTP can delete it.', $c_pos);
                }
            }

            if (($function == 'fopen') && (in_array(@$params[1][0][1][1][0], ['t']))) {
                log_warning('Call to \'' . $function . '\' uses a text flag. This creates platform-dependent text files, which we do not want.', $c_pos);
            }
        }
    }

    if (($function == 'isset') && (@$params[0][0][0] != 'VARIABLE')) {
        log_warning('Can only pass variables to ' . $function, $c_pos);
    }

    if (($function == 'tempnam') && (@$params[0][0][0] == 'LITERAL') && (substr(@$params[0][0][1][1], 0, 4) == '/tmp')) {
        log_warning('Don\'t assume you can write to the shared temp directory', $c_pos);
    }
    if (($function == 'strpos') && (@$params[0][0][0] == 'LITERAL') && (@$params[1][0][0] != 'LITERAL')) {
        log_warning('Looks like strpos parameters are the wrong way around; you fell for a common API anomaly: unlike most functions like in_array, strpos is haystack followed by needle', $c_pos);
    }
    if ((($function == 'sprintf') || ($function == 'printf')) && (@$params[0][0][0] == 'LITERAL')) {
        $matches = [];
        $num_matches = preg_match_all('#\%[+-]?.?-?\d*(\.\d+)?(\$[bcdefuFodsxX])?#', $params[0][0][1][1], $matches);
        if ($num_matches + 1 != count($params)) {
            log_warning('Looks like the wrong number of parameters were sent to this [s]printf function, got ' . integer_format(count($params)) . ', expected, ' . integer_format($num_matches + 1), $c_pos);
        }
    }
    if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && ($function == 'tempname')) {
        log_warning('Make sure temporary files are deleted', $c_pos);
    }
    //if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && ($function == 'fopen')) log_warning('Make sure opened files are closed', $c_pos);  Not going to actually cause problems, as PHP'll close it when the script finishes
    if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && ($function == 'define') && (@strtoupper($params[0][0][1]) != $params[0][0][1])) {
        log_warning('Constants should be upper case', $c_pos);
    }
    if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && ($function == 'get_username') && (@$params[0][0][1] != 'get_member')) {
        log_warning('Make sure guests/deleted-members are handled properly', $c_pos);
    }
    if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && ($function == 'get_url')) {
        log_warning('Make sure that deleting the entry for this file/URL deletes the disk file', $c_pos);
    }
    if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && (in_array($function, ['query_insert', 'insert_lang', 'insert_lang_comcode']))) {
        log_warning('Make sure that deleting the entry (or uninstalling) for this row deletes the row (if applicable)', $c_pos);
    }
    if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && ($function == 'query_delete') && (!array_key_exists(2, $params))) {
        log_warning('Check that non-singular modification is wanted for this query', $c_pos);
    }
    if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && ($function == 'query_update') && (!array_key_exists(3, $params))) {
        log_warning('Check that non-singular modification is wanted for this query', $c_pos);
    }
    if (($function == 'implode' || $function == 'explode')) {
        if ($params[0][0][0] != 'LITERAL' && $params[1][0][0] == 'LITERAL') {
            log_warning('You have almost certainly got the ' . $function . ' parameters the wrong way around', $c_pos);
        }
    }
    if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && ($function == 'unlink')) {
        log_warning('Be very careful that shared URLs cannot be deleted (check upload dir, and staff access)', $c_pos);
    }
    if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && (!empty($GLOBALS['FLAG__PEDANTIC'])) && (in_array($function, ['query_update', 'query_delete']))) {
        log_warning('Check log_it/cat-entry-handling/delete_lang', $c_pos);
    }
    //if ((!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) && (!empty($GLOBALS['FLAG__PEDANTIC'])) && ($function == 'query_select')) log_warning('Check that non-singular select is wanted for this query', $c_pos);  This is REALLY pedantic ;) I'm sure MySQL is clever enough to see that only one row can match against a key

    if ($ret !== null) {
        return $ret['type'];
    }
    if (!$found) {
        if ((isset($FUNCTION_SIGNATURES['__global'])) && (empty($FUNCTION_SIGNATURES['__global']['functions']))) {
            static $warned_missing_api_once = false;
            if (!$warned_missing_api_once) {
                log_warning('No API function metabase available', $c_pos);
            }
            $warned_missing_api_once = true;
        } elseif (!empty($GLOBALS['FLAG__API'])) {
            if (
                (
                    ($GLOBALS['OK_EXTRA_FUNCTIONS'] === null) ||
                    (
                        (preg_match('#^(' . $GLOBALS['OK_EXTRA_FUNCTIONS'] . ')#', $function) == 0) &&
                        (($class === null) || (preg_match('#^(' . $GLOBALS['OK_EXTRA_FUNCTIONS'] . ')#', $class) == 0))
                    )
                )
                &&
                (strpos($function_guard, ',' . $function . ',') === false) &&
                (!isset($GLOBALS['KNOWN_EXTRA_FUNCTIONS'][$function]))
                &&
                (
                    ($class === null)
                    ||
                    (
                        (strpos($function_guard, ',' . $class . ',') === false) &&
                        (!in_array($class, ['mixed', '?mixed', 'object', '?object', ''/*Dynamic*/])) &&
                        (!isset($GLOBALS['KNOWN_EXTRA_CLASSES'][$class]))
                    )
                )
            ) {
                if (($class === null) || ($class == '__global')) {
                    if ($function != '' && $function != 'ocp_mark_as_escaped' && $function != 'ocp_is_escaped'/*These aren't checked with function_exists, checked with a global, for performance reasons*/) {
                        log_warning('Could not find function \'' . $function . '\'', $c_pos);
                    }
                } else {
                    if (!isset($FUNCTION_SIGNATURES[$class])) {
                        if (in_array($class, ['integer', 'float', 'string', 'boolean', 'boolean-false', 'null'])) {
                            log_warning('Mixing variable type', $c_pos);
                        } else {
                            log_warning('Could not find class \'' . $class . '\'', $c_pos);
                        }
                    } elseif (empty($STRUCTURE['classes'][$class]['superclass'])) {
                        //@var_dump($FUNCTION_SIGNATURES[$class]['functions']);exit(); Useful for debugging
                        log_warning('Could not find method \'' . $class . '->' . $function . '\'', $c_pos);
                    }
                }
            }
        }
        foreach ($params as $param) {
            check_expression($param[0], false, false, $function_guard);
        }
        return 'mixed';
    }
    return $ret;
}

function check_db_map($table, $expr_map, $c_pos, $must_be_complete = false)
{
    $map = [];
    $arr_count = 0;
    if ($expr_map[0] == 'CREATE_ARRAY') {
        foreach ($expr_map[1] as $passing) {
            if (count($passing) == 1) {
                log_warning('Map required, list given', $c_pos);
            } else {
                if ($passing[0][0] == 'LITERAL') {
                    $type = check_expression($passing[1]);
                    if ($type == 'array') {
                        $arr_count++;
                    }
                    $map[$passing[0][1][1]] = $type;
                }
            }
        }
    }
    if ($arr_count == count($map)) {
        if ((!isset($GLOBALS['TABLE_FIELDS'][$table])) && ($GLOBALS['TABLE_FIELDS'] !== null)) {
            if ((strpos($table, ' ') === false) && (!empty($GLOBALS['FLAG__MANUAL_CHECKS']))) {
                log_warning('Unknown table referenced (' . $table . ')', $c_pos);
            }
        }
    } else {
        foreach ($map as $field => $type) {
            _check_db_field($table, $field, $c_pos, $type);
        }
    }
    if (($must_be_complete) && (isset($GLOBALS['TABLE_FIELDS'][$table])) && ($GLOBALS['TABLE_FIELDS'] !== null)) {
        if ((isset($GLOBALS['TABLE_FIELDS'][$table]['fields']['id'])) && (strpos($GLOBALS['TABLE_FIELDS'][$table]['fields']['id'], 'AUTO') !== false)) {
            $map['id'] = 'integer'; // Auto
        }
        $missing = implode(', ', array_diff(array_keys($GLOBALS['TABLE_FIELDS'][$table]['fields']), array_keys($map)));
        if (($missing != '') && (!empty($GLOBALS['FLAG__MANUAL_CHECKS']))) {
            log_warning('Field map for ' . $table . ' table may be incomplete (unsure, but can\'t see: ' . $missing . ' )', $c_pos);
        }
    }
    return $map;
}

function check_db_fields($table, $expr_map, $c_pos)
{
    if ($expr_map[0] == 'CREATE_ARRAY') {
        foreach ($expr_map[1] as $passing) {
            if (count($passing) == 2) {
                log_warning('The selection array must be a list, not a map', $c_pos);
            } else {
                check_db_field($table, $passing[0], $c_pos);
            }
        }
    }
}

function check_db_field($table, $expr_map, $c_pos)
{
    if (($expr_map[0] == 'LITERAL') && ($GLOBALS['TABLE_FIELDS'] !== null)) {
        _check_db_field($table, $expr_map[1][1], $c_pos);
    }
}

function _check_db_field($table, $field, $c_pos, $type = null)
{
    if ($GLOBALS['TABLE_FIELDS'] === null) {
        return;
    }

    if (!isset($GLOBALS['TABLE_FIELDS'][$table])) {
        if ((strpos($table, ' ') === false) && (!empty($GLOBALS['FLAG__MANUAL_CHECKS']))) {
            log_warning('Unknown table referenced (' . $table . ')', $c_pos);
        }
        return;
    }

    $field = str_replace('DISTINCT ', '', $field);
    $field = preg_replace('# AS .*#', '', $field);
    $field = preg_replace('#MAX\((.*)\)#', '${1}', $field);
    $field = preg_replace('#MIN\((.*)\)#', '${1}', $field);
    $field = preg_replace('#SUM\((.*)\)#', '${1}', $field);
    if (strpos($field, '*') !== false) {
        return;
    }

    if ((!isset($GLOBALS['TABLE_FIELDS'][$table]['fields'][$field])) && (strpos($field, '(') === false) && (!empty($GLOBALS['FLAG__MANUAL_CHECKS']))) {
        log_warning('Unknown field (' . $field . ') referenced', $c_pos);
        return;
    }

    if ($type !== null) {
        if (isset($GLOBALS['TABLE_FIELDS'][$table]['fields'][$field])) {
            $expected_type = str_replace('*', '', $GLOBALS['TABLE_FIELDS'][$table]['fields'][$field]);
            if (!empty($GLOBALS['FLAG__MANUAL_CHECKS'])) {
                ensure_type([$expected_type], $type, $c_pos, 'DB field ' . $field . ' should be ' . $expected_type . ', not ' . $type);
            }
        }
    }
}

/*
Demonstration of all the assignment checks we could make:

$foo += 'a';

$bar = 1;
$bar[] = 'a';

list($a) = 1;

$b = 1;
$b[3] = 'a';
*/
function check_assignment($c, $c_pos, $function_guard = '')
{
    global $LOCAL_VARIABLES;
    $GLOBALS['MADE_CALL'] = null;
    $e_type = check_expression($c[3], true, false, $function_guard);
    $made_call = $GLOBALS['MADE_CALL'];
    $GLOBALS['MADE_CALL'] = null;
    $op = $c[1];
    $target = $c[2];

    check_for_equivalent_operands($c[2], $c[3]);

    // Special assignment operational checks
    if (in_array($op, ['CONCAT_EQUAL'])) {
        $passes = ensure_type(['string'], $e_type, $c_pos, 'Can only concatenate onto and with strings (not ' . $e_type . ')');
        if ($passes) {
            infer_expression_type_to_variable_type('string', $c[3]);
        }
        if ($c[3][0] == 'VARIABLE_REFERENCE') {
            log_warning('Cannot append a reference', $c_pos);
        }
        if ($target[0] == 'VARIABLE') {
            $v_type = get_variable_type($target);
            ensure_type(['string'], $v_type, $c_pos, 'Can only concatenate onto and with strings (not ' . $v_type . ')');
        }
    }
    if (in_array($op, ['PLUS_EQUAL'])) {
        ensure_type(['array', 'integer', 'float'], $e_type, $c_pos, 'Can only perform addition to arrays or numbers (not ' . $e_type . ')');
        if ($target[0] == 'VARIABLE') {
            $v_type = get_variable_type($target);
            ensure_type(['array', 'integer', 'float'], $v_type, $c_pos, 'Can only perform addition to arrays or numbers (not ' . $v_type . ')');
        }
    }
    if (in_array($op, ['DIV_EQUAL', 'MUL_EQUAL', 'MINUS_EQUAL'])) {
        ensure_type(['integer', 'float'], $e_type, $c_pos, 'Can only perform relative arithmetic with numbers (not ' . $e_type . ')');
        if ($target[0] == 'VARIABLE') {
            $v_type = get_variable_type($target);
            ensure_type(['integer', 'float'], $v_type, $c_pos, 'Can only perform relative arithmetic with numbers (not ' . $v_type . ')');
        }
    }

    // Special assignment target checks
    if ($target[0] == 'LIST') {
        $passes = ensure_type(['array'], $e_type, $c_pos, 'Can only list from an array (not ' . $e_type . ')');
        if ($passes) {
            infer_expression_type_to_variable_type('array', $c[3]);
        }
        foreach ($target[1] as $var) {
            if (empty($var[2])) {
                add_variable_reference($var[1], $c_pos, false);
            }
        }
        return 'array';
    }
    if ($target[0] == 'ARRAY_APPEND') {
        if (empty($target[2][2])) { // Simple variable, meaning we can test to see if it's an array
            $v_type = check_variable($target[1], true, $function_guard);
            $passes = ensure_type(['array'], $v_type, $c_pos, 'Can only append to an array (not ' . $v_type . ')');
            if ($passes) {
                infer_expression_type_to_variable_type('array', $c[3]);
            }
        }
        return 'array';
    }

    // check_variable will do the internalised checks. Type conflict checks will be done at the end of the function, based on all the types the variable has been set with. Variable type usage checks are done inside expressions.
    if ($target[0] == 'VARIABLE') {
        if (($op == 'EQUAL') && (empty($target[2]))) {
            add_variable_reference($target[1], $c_pos, false);
            $v = $LOCAL_VARIABLES[$target[1]];
            if (($made_call !== null) && (((!$v['conditioned_null']) && (isset($GLOBALS['NULL_ERROR_FUNCS'][$made_call]))) || ((!$v['conditioned_false']) && (isset($GLOBALS['FALSE_ERROR_FUNCS'][$made_call]))))) {
                $LOCAL_VARIABLES[$target[1]]['conditioner'][] = $made_call;
            }
            if ($e_type == '*MIXED*') {
                global $LOCAL_VARIABLES;
                $LOCAL_VARIABLES[$c[2][1]]['mixed_tag'] = true;
                $e_type = '?mixed';
            }
            /*elseif (($e_type == 'boolean-false') && ($c[3][0] == 'LITERAL')) { No, it'll give a mixed type error
                global $LOCAL_VARIABLES;
                $LOCAL_VARIABLES[$c[2][1]]['types'][] = 'boolean';
            }*/
            set_composr_type($target[1], $e_type);
        } else {
            if (($made_call !== null) && (((isset($GLOBALS['NULL_ERROR_FUNCS'][$made_call]))) || ((isset($GLOBALS['FALSE_ERROR_FUNCS'][$made_call]))))) {
                if (!empty($GLOBALS['FLAG__PEDANTIC'])) {
                    log_warning('Result probably wasn\'t error checked', $c_pos);
                }
            }
        }
        $type = check_variable($target, false, $function_guard);
        return $type;
    }

    // Should never get here
    return 'mixed';
}

function check_for_equivalent_operands($a, $b)
{
    $c_pos = $a[count($a) - 1];
    _nullify_final_integers($a);
    _nullify_final_integers($b);
    if (serialize($a) == serialize($b)) {
        log_warning('Two operands are the same', $c_pos);
    }
}

function _nullify_final_integers(&$x)
{
    if (is_array($x)) {
        $cnt = count($x);
        foreach ($x as $i => &$_x) {
            if (is_array($_x)) {
                _nullify_final_integers($_x);
            }
            if (($i == $cnt - 1) && (is_integer($_x))) {
                $_x = null;
            }
        }
    }
}

function check_expression($e, $assignment = false, $equate_false = false, $function_guard = '')
{
    $c_pos = $e[count($e) - 1];
    if ($e[0] == 'VARIABLE_REFERENCE') {
        $e = $e[1];
    }
    if ($e[0] == 'SOLO') {
        $type = check_expression($e[1], false, false, $function_guard);
        return $type;
    }
    if ($e[0] == 'EXPRESSION_CHAINING') {
        return check_variable($e, false, $function_guard);
    }
    if ((in_array($e[0], ['DIVIDE', 'REMAINDER', 'DIV_EQUAL'])) && ($e[2][0] != 'LITERAL')) {
        if (($assignment) && (@is_array($e[2][1][2])) && (empty($e[2][1][2]))) {
            $GLOBALS['LOCAL_VARIABLES'][$e[2][1][1]]['conditioner'][] = '_divide_';
        } elseif (!empty($GLOBALS['FLAG__PEDANTIC'])) {
            log_warning('Divide by zero un-handled', $c_pos);
        }
    }
    if ($e[0] == 'TERNARY_IF') {
        if (($e[1][0] == 'CALL_DIRECT') && ($e[1][1] == 'php_function_allowed' || strpos($e[1][1], '_exists') !== false/*function_exists or method_exists or class_exists*/) && ($e[1][2][0][0][0] == 'LITERAL') && ($e[1][2][0][0][1][0] == 'STRING')) {
            $function_guard .= ',' . $e[1][2][0][0][1][1] . ',';
        }
        $passes = ensure_type(['boolean'], check_expression($e[1], false, false, $function_guard), $c_pos, 'Conditionals must be boolean (ternary)');
        if ($passes) {
            infer_expression_type_to_variable_type('boolean', $e[1]);
        }
        $type_a = check_expression($e[2][0], false, false, $function_guard);
        $type_b = check_expression($e[2][1], false, false, $function_guard);
        check_for_equivalent_operands($e[2][0], $e[2][1]);
        if (($type_a != 'null') && ($type_b != 'null')) {
            $passes = ensure_type([$type_a, 'mixed'/*imperfect, but useful for performance*/], $type_b, $c_pos, 'Type symettry error in ternary operator');
            if ($passes) {
                infer_expression_type_to_variable_type($type_a, $e[2][1]);
            }
        }

        // Common most egregious cases of horribly ugly code
        if (in_array($e[1][0], ['TERNARY_IF', 'IS_EQUAL', 'IS_NOT_EQUAL', 'IS_IDENTICAL', 'IS_NOT_IDENTICAL', 'IS_SMALLER', 'IS_SMALLER_OR_EQUAL', 'IS_GREATER', 'IS_GREATER_OR_EQUAL', 'INSTANCEOF', 'BOOLEAN_XOR', 'BOOLEAN_OR', 'BOOLEAN_AND'])) {
            log_warning('Raw complex expression in ternary syntax boolean controller should be parenthesised, for clarity', $c_pos);
        }
        $bad_ops = ['TERNARY_IF', 'BOOLEAN_XOR', 'BOOLEAN_OR', 'BOOLEAN_AND', 'REFERENCE', 'BW_OR', 'BW_XOR', 'BW_AND', 'IS_EQUAL', 'IS_NOT_EQUAL', 'IS_IDENTICAL', 'INSTANCEOF', 'IS_NOT_IDENTICAL', 'IS_SMALLER', 'IS_SMALLER_OR_EQUAL', 'IS_GREATER', 'IS_GREATER_OR_EQUAL', 'SL', 'SR', 'ADD', 'SUBTRACT', 'CONC', 'MULTIPLY', 'DIVIDE', 'REMAINDER', 'EXPONENTIATION'];
        if ((in_array($e[2][0][0], $bad_ops)) || (in_array($e[2][1][0], $bad_ops))) {
            log_warning('Raw complex expression in ternary syntax should be parenthesised, for clarity', $c_pos);
        }

        return $type_a;
    }
    if (in_array($e[0], ['BOOLEAN_AND', 'BOOLEAN_OR', 'BOOLEAN_XOR'])) {
        foreach ([0, 1] as $function_parameter_pos) {
            foreach ([0, 1] as $and_position) {
                if (($e[0] == 'BOOLEAN_AND') && ($e[1][0] == 'PARENTHESISED') && ($e[1][$and_position + 1][0] == 'CALL_DIRECT') && ($e[1][$and_position + 1][1] == 'php_function_allowed' || strpos($e[1][$and_position + 1][1], '_exists') !== false) && (isset($e[1][$and_position + 1][2][$function_parameter_pos])) && ($e[1][$and_position + 1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($e[1][$and_position + 1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                    $function_guard .= ',' . $e[1][1][2][$function_parameter_pos][0][1][1] . ',';
                }
                if (($e[0] == 'BOOLEAN_AND') && ($e[2][0] == 'BOOLEAN_AND') && ($e[2][1][0] == 'PARENTHESISED') && ($e[2][1][$and_position + 1][0] == 'CALL_DIRECT') && ($e[2][1][$and_position + 1][1] == 'php_function_allowed' || strpos($e[2][1][$and_position + 1][1], '_exists') !== false) && (isset($e[2][1][$and_position + 1][2][$function_parameter_pos][0])) && ($e[2][1][$and_position + 1][2][$function_parameter_pos][0][0] == 'LITERAL') && ($e[2][1][$and_position + 1][2][$function_parameter_pos][0][1][0] == 'STRING')) {
                    $function_guard .= ',' . $e[2][1][1][2][$function_parameter_pos][0][1][1] . ',';
                }
            }
        }
        $passes = ensure_type(['boolean'], check_expression($e[1], false, false, $function_guard), $c_pos - 1, 'Can only use boolean combinators with booleans');
        if ($passes) {
            infer_expression_type_to_variable_type('boolean', $e[1]);
        }
        $passes = ensure_type(['boolean'], check_expression($e[2], false, false, $function_guard), $c_pos, 'Can only use boolean combinators with booleans');
        if ($passes) {
            infer_expression_type_to_variable_type('boolean', $e[2]);
        }
        check_for_equivalent_operands($e[1], $e[2]);
        return 'boolean';
    }
    if (in_array($e[0], ['SL', 'SR', 'REMAINDER'])) {
        $passes = ensure_type(['integer'], check_expression($e[1], false, false, $function_guard), $c_pos - 1, 'Can only use integer combinators with integers');
        if ($passes) {
            infer_expression_type_to_variable_type('integer', $e[1]);
        }
        $passes = ensure_type(['integer'], check_expression($e[2], false, false, $function_guard), $c_pos, 'Can only use integer combinators with integers');
        if ($passes) {
            infer_expression_type_to_variable_type('integer', $e[2]);
        }
        return 'integer';
    }
    if (in_array($e[0], ['CONC'])) {
        $type_a = check_expression($e[1], false, false, $function_guard);
        $type_b = check_expression($e[2], false, false, $function_guard);
        $passes = ensure_type(['string'], $type_a, $c_pos - 1, 'Can only use string combinators with strings (1) (not ' . $type_a . ')');
        if ($passes) {
            infer_expression_type_to_variable_type('string', $e[1]);
        }
        $passes = ensure_type(['string'], $type_b, $c_pos, 'Can only use string combinators with strings (2) (not ' . $type_b . ')');
        if ($passes) {
            infer_expression_type_to_variable_type('string', $e[2]);
        }
        return 'string';
    }
    if (in_array($e[0], ['SUBTRACT', 'MULTIPLY', 'DIVIDE', 'EXPONENTIATION'])) {
        $type_a = check_expression($e[1], false, false, $function_guard);
        $t = check_expression($e[2], false, false, $function_guard);
        ensure_type(['integer', 'float'], $type_a, $c_pos - 1, 'Can only use arithmetical combinators with numbers (1) (not ' . $type_a . ')');
        ensure_type(['integer', 'float'], $t, $c_pos, 'Can only use arithmetical combinators with numbers (2) (not ' . $t . ')');
        return ($e[0] == 'DIVIDE') ? 'float' : $type_a;
    }
    if (in_array($e[0], ['ADD'])) {
        $type_a = check_expression($e[1], false, false, $function_guard);
        $t = check_expression($e[2], false, false, $function_guard);
        ensure_type(['integer', 'float', 'array'], $type_a, $c_pos - 1, 'Can only use + combinator with numbers/arrays (1) (not ' . $type_a . ')');
        ensure_type(['integer', 'float', 'array'], $t, $c_pos, 'Can only use + combinator with numbers/arrays (2) (not ' . $t . ')');
        return $type_a;
    }
    if (in_array($e[0], ['IS_GREATER_OR_EQUAL', 'IS_SMALLER_OR_EQUAL', 'IS_GREATER', 'IS_SMALLER'])) {
        $type_a = check_expression($e[1], false, false, $function_guard);
        $type_b = check_expression($e[2], false, false, $function_guard);
        check_for_equivalent_operands($e[1], $e[2]);
        ensure_type(['integer', 'float', 'string'], $type_a, $c_pos - 1, 'Can only use arithmetical comparators with numbers or strings');
        ensure_type(['integer', 'float', 'string'], $type_b, $c_pos, 'Can only use arithmetical comparators with numbers or strings');
        ensure_type([$type_a], $type_b, $c_pos, 'Comparators must have type symmetric operands (' . $type_a . ' vs ' . $type_b . ')');
        return 'boolean';
    }
    if (in_array($e[0], ['IS_EQUAL', 'IS_IDENTICAL', 'IS_NOT_IDENTICAL', 'IS_NOT_EQUAL'])) {
        $type_a = check_expression($e[1], false, (in_array($e[0], ['IS_IDENTICAL', 'IS_NOT_IDENTICAL'])) && ($e[2][0] == 'LITERAL') && ($e[2][1][0] == 'BOOLEAN') && (!$e[2][1][1]), $function_guard);
        $type_b = check_expression($e[2], false, false, $function_guard);
        check_for_equivalent_operands($e[1], $e[2]);
        $x = $e;
        if ($x[1][0] == 'EMBEDDED_ASSIGNMENT') {
            $x = $e[1];
        }
        if (($x[1][0] == 'VARIABLE') && (@is_array($x[1][1][2])) && (empty($x[1][1][2])) && ($e[2][0] == 'LITERAL')) {
            if (in_array($e[0], ['IS_IDENTICAL', 'IS_NOT_IDENTICAL'])) {
                if (($e[2][1][0] == 'BOOLEAN') && (!$e[2][1][1])) {
                    $GLOBALS['LOCAL_VARIABLES'][$x[1][1][1]]['conditioned_false'] = true;
                } elseif ($e[2][1][0] == 'null') {
                    $GLOBALS['LOCAL_VARIABLES'][$x[1][1][1]]['conditioned_null'] = true;
                }
            }
            if (($e[2][1][0] == 'INTEGER') && ($e[2][1][1] == 0)) {
                $GLOBALS['LOCAL_VARIABLES'][$x[1][1][1]]['conditioned_zero'] = true;
            }
        }
        if (($e[0] == 'IS_EQUAL') && ($e[2][0] == 'LITERAL') && ($e[2][1][0] == 'BOOLEAN')) {
            log_warning('It\'s redundant to equate to truths', $c_pos);
        }
        if (strpos($e[0], 'IDENTICAL') === false) {
            if ($type_b == 'null') {
                log_warning('Comparing to null is considered bad', $c_pos);
            }
            $passes = ensure_type([$type_a], $type_b, $c_pos, 'Comparators must have type symmetric operands (' . $type_a . ' vs ' . $type_b . ')');
            if ($passes) {
                infer_expression_type_to_variable_type($type_a, $e[2]);
            }
        }
        return 'boolean';
    }

    $inner = $e;
    switch ($inner[0]) {
        case 'CLOSURE':
            global $LOCAL_VARIABLES;
            $temp = $LOCAL_VARIABLES;
            check_function($inner[1] + ['name' => '(closure)'], true);
            $LOCAL_VARIABLES = $temp;
            return null;
        case 'EMBEDDED_ASSIGNMENT':
            $ret = check_assignment($inner, $c_pos, $function_guard);
            return $ret;
        case 'CALL_METHOD':
            $ret = check_method($inner, $c_pos, $function_guard);
            if ($ret === null) {
                log_warning('Method that returns no value used in an expression', $c_pos);
                return 'mixed';
            }
            return $ret;
        case 'CALL_INDIRECT':
            add_variable_reference($inner[1][1], $c_pos);
            return 'mixed';
        case 'CALL_DIRECT':
            $ret = check_call($inner, $c_pos, null, $function_guard);
            if ($ret === null) {
                log_warning('Function (\'' . $inner[1] . '\') that returns no value used in an expression', $c_pos);
                return 'mixed';
            }
            if ($inner[1] == 'mixed') {
                return '*MIXED*';
            }
            if ($assignment) {
                $GLOBALS['MADE_CALL'] = $inner[1];
                if ((@$e[2][0][0] == 'VARIABLE') && (@is_array($e[2][0][1][2])) && (empty($e[2][0][1][2])) && ($e[1] == 'is_null')) {
                    $GLOBALS['LOCAL_VARIABLES'][$e[2][0][1][1]]['conditioned_null'] = true;
                }
            } else {
                if (!empty($GLOBALS['FLAG__PEDANTIC'])) {
                    if (isset($GLOBALS['NULL_ERROR_FUNCS'][$inner[1]])) {
                        log_warning('Crucial error value un-handled', $c_pos);
                    }
                    if ((isset($GLOBALS['FALSE_ERROR_FUNCS'][$inner[1]])) && (!$equate_false)) {
                        log_warning('Crucial error value un-handled', $c_pos);
                    }
                }
            }
            return $ret;
        case 'CASTED':
            check_expression($inner[2], false, false, $function_guard);
            return strtolower($inner[1]);
        case 'PARENTHESISED':
            return check_expression($inner[1], false, false, $function_guard);
        case 'BOOLEAN_NOT':
            $passes = ensure_type(['boolean'], check_expression($inner[1], false, false, $function_guard), $c_pos, 'Can only \'NOT\' a boolean', true);
            if ($passes) {
                infer_expression_type_to_variable_type('boolean', $inner[1]);
            }
            return 'boolean';
        case 'BW_NOT':
            $passes = ensure_type(['integer'], check_expression($inner[1], false, false, $function_guard), $c_pos, 'Can only \'BITWISE-NOT\' an integer', true);
            if ($passes) {
                infer_expression_type_to_variable_type('integer', $inner[1]);
            }
            return 'integer';
        case 'BW_OR':
        case 'BW_XOR':
        case 'BW_AND':
            $exp_1 = check_expression($inner[1], false, false, $function_guard);
            $exp_2 = check_expression($inner[2], false, false, $function_guard);
            check_for_equivalent_operands($inner[1], $inner[2]);

            $string_mode = ensure_type(['string'], $exp_1, $c_pos, '', true, true) || ensure_type(['string'], $exp_2, $c_pos, '', true, true);
            $integer_mode = ensure_type(['integer'], $exp_1, $c_pos, '', true, true) || ensure_type(['integer'], $exp_2, $c_pos, '', true, true);
            $arg_type = ($string_mode ? 'string' : 'integer');

            $passes = ensure_type([$arg_type], $exp_1, $c_pos, 'Can only bitwise 2 integers or 2 1-byte strings (got ' . $exp_1 . ')', true);

            if (($passes) && ($string_mode || $integer_mode)) {
                infer_expression_type_to_variable_type($arg_type, $inner[1]);
                infer_expression_type_to_variable_type($arg_type, $inner[2]);
            }

            return $arg_type;
        case 'NEGATE':
            $type = check_expression($inner[1], false, false, $function_guard);
            ensure_type(['integer', 'float'], $type, $c_pos, 'Can only negate a number');
            return $type;
        case 'LITERAL':
            $type = check_literal($inner[1]);
            return $type;
        case 'NEW_OBJECT':
            $class = preg_replace('#^.*\\\\#', '', $inner[1]); // We strip out namespaces from the name, and just look at the actual class name
            global $FUNCTION_SIGNATURES;
            if ((!isset($FUNCTION_SIGNATURES[$class])) && ($FUNCTION_SIGNATURES != []) && (strpos($function_guard, ',' . $class . ',') === false)) {
                if ((($GLOBALS['OK_EXTRA_FUNCTIONS'] === null) || (preg_match('#^' . $GLOBALS['OK_EXTRA_FUNCTIONS'] . '#', $class) == 0))) {
                    if (!isset($GLOBALS['KNOWN_EXTRA_CLASSES'][$class]) && $class != '') {
                        if ($class !== null) {
                            log_warning('Could not find class, ' . $class, $c_pos);
                        }
                    }
                }
            }
            foreach ($inner[2] as $param) {
                check_expression($param, false, false, $function_guard);
            }
            if (!empty($inner[2])) {
                check_call(['CALL_METHOD', '__construct', $inner[2]], $c_pos, $inner[1], $function_guard);
            }
            return 'object-' . $inner[1];
        case 'CLONE_OBJECT':
            // $a = clone $b will make a shallow copy of the object $, so we just
            // return $b's type
            return check_expression($inner[1], false, false, '');
        case 'CREATE_ARRAY':
            foreach ($inner[1] as $param) {
                check_expression($param[0], false, false, $function_guard);
                if (isset($param[1])) {
                    check_expression($param[1], false, false, $function_guard);
                }
            }
            return 'array';
        case 'ASSIGNMENT':
            check_assignment($inner, $c_pos, $function_guard);
            break;
        case 'PRE_DEC':
            ensure_type(['integer', 'float'], check_variable($inner[1], false, $function_guard), $c_pos, 'Can only decrement numbers');
            break;
        case 'PRE_INC':
            ensure_type(['integer', 'float'], check_variable($inner[1], false, $function_guard), $c_pos, 'Can only increment numbers');
            break;
        case 'DEC':
            ensure_type(['integer', 'float'], check_variable($inner[1], false, $function_guard), $c_pos, 'Can only decrement numbers');
            break;
        case 'INC':
            ensure_type(['integer', 'float'], check_variable($inner[1], false, $function_guard), $c_pos, 'Can only increment numbers');
            break;
        case 'VARIABLE':
            return check_variable($inner, true, $function_guard);
    }
    return 'mixed';
}

function check_variable($variable, $reference = false, $function_guard = '')
{
    $identifier = $variable[1];

    if ($identifier === null) {
        return null;
    }

    if (!is_array($identifier)) {
        global $LOCAL_VARIABLES;
        if ((!isset($LOCAL_VARIABLES[$identifier])) && !((is_array($identifier) && (in_array($identifier[0], ['CALL_METHOD']))))) {
            // We skip this check if the "variable" is coming from a function/method
            // (in which case we have a function/method call rather than a variable)
            log_warning('Variable \'' . $identifier . '\' referenced before initialised', $variable[3]);
        }

        // Add to reference count if: this specifically is a reference, or it's complex therefore the base is explicitly a reference, or we are forced to add it because it is yet unseen
        if (($reference) || (!empty($variable[2])) || (!isset($LOCAL_VARIABLES[$identifier]))) {
            add_variable_reference($identifier, $variable[count($variable) - 1], ($reference) || (!empty($variable[2])));
        }

        $variable_stem = $variable;
        $variable_stem[2] = [];
        $type = get_variable_type($variable_stem);
    } else {
        $type = check_expression($variable[1]);
    }

    $next = $variable[2];
    while ($next != []) { // Complex: we must perform checks to make sure the base is of the correct type for the complexity to be valid. We must also note any deep variable references used in array index / string extract expressions
        /*if ($next[0] == 'CHAR_OF_STRING') {    Deprecated syntax
            check_expression($next[1]);
            $passes = ensure_type(['string'], check_variable(['VARIABLE', $identifier, []]), $variable[3], 'Variable \'' . $identifier . '\' must be a string due to dereferencing');
            if ($passes) {
                infer_expression_type_to_variable_type('string', $next[1]);
            }
            return 'string';
        }*/

        if ($next[0] == 'ARRAY_AT') {
            if (($identifier == 'GLOBALS') && ($next[1][0] == 'SOLO') && ($next[1][1][0] == 'LITERAL')) {
                $gid = $next[1][1][1][1];
                add_variable_reference($gid, $variable[count($variable) - 1]);
                $LOCAL_VARIABLES[$gid]['is_global'] = true;
            }
            check_expression($next[1]);
            $passes = ensure_type(['array', 'string'], $type, $variable[3], 'Variable must be an array/string due to dereferencing');
            if (ensure_type(['string'], $type, $variable[3], '', true, true) == 'string') {
                $type_2 = check_expression($variable[2][1]);
                $passes = ensure_type(['integer'], $type_2, $variable[3], 'String character extraction must use an integer');
            }
            //if ($passes) infer_expression_type_to_variable_type('array', $next[1]);
            $type = 'mixed'; // We don't know the array data types

            $next = $next[2];
        } elseif ($next[0] == 'DEREFERENCE') {
            // Special rule for 'this->db'
            if (($variable[1] == 'this') && ($variable[2][1][1] == 'db') && ((!isset($variable[2][2][0])) || ($variable[2][2][0] != 'DEREFERENCE'))) {
                $type = 'DatabaseConnector';
            }

            // Special rule for $GLOBALS['?_DB']
            if (($variable[1] == 'GLOBALS') && ($variable[2][1][1][0] == 'STRING') && (substr($variable[2][1][1][1], -3) == '_DB') && ((!isset($variable[2][2][2][0])) || ($variable[2][2][2][0] != 'DEREFERENCE'))) {
                $type = 'DatabaseConnector';
            }

            // Special rule for $GLOBALS['FORUM_DRIVER']
            if (($variable[1] == 'GLOBALS') && ($variable[2][1][1][0] == 'STRING') && ($variable[2][1][1][1] == 'FORUM_DRIVER')) {
                $type = 'Forum_driver_base';
            }

            ensure_type(['object', 'resource'], $type, $variable[3], 'Variable must be an object due to dereferencing');
            if (($next[2] != []) && ($next[2][0] == 'CALL_METHOD')) {
                $type = actual_check_method($type/*class*/, $next[1][1]/*method*/, $next[2][2]/*params*/, $next[3]/*line number*/, $function_guard);
                $next = $next[2][5];
            } else {
                $type = 'mixed';
                $next = $next[2];
            }
        } else {
            $next = [];
        }
    }

    return $type;
}

function scan_extractive_expressions($variable)
{
    if (!is_array($variable)) {
        return;
    }
    if ($variable == []) {
        return;
    }

    if (($variable[0] == 'ARRAY_AT') || ($variable[0] == 'CHAR_OF_STRING')) {
        check_expression($variable[1]);
    }

    if ((($variable[0] == 'ARRAY_AT') || ($variable[0] == 'DEREFERENCE')) && (!empty($variable[2]))) {
        scan_extractive_expressions($variable[2]);
    }
}

function get_variable_type($variable)
{
    global $LOCAL_VARIABLES;

    $identifier = $variable[1];

    if (!empty($variable[2])) {
        return 'mixed'; // Too complex
    }

    if (!isset($LOCAL_VARIABLES[$identifier])) {
        return 'mixed';
    }

    if (empty($LOCAL_VARIABLES[$identifier]['types'])) {
        return 'mixed'; // There is a problem, but it will be identified elsewhere.
    }

    $temp = array_unique(array_values(array_diff($LOCAL_VARIABLES[$identifier]['types'], ['null'])));
    if ($temp == ['boolean-false', 'boolean']) {
        return 'boolean';
    }
    if (!empty($temp)) {
        return is_array($temp[0]) ? $temp[0][0] : $temp[0]; // We'll assume the first set type is the actual type
    }
    return 'mixed';
}

function check_literal($literal)
{
    if ($literal[0] == 'NEGATE') {
        $type = check_literal($literal[1]);
        ensure_type(['integer', 'float'], $type, $literal[count($literal) - 1], 'Can only negate a number');
        return $type;
    }
    if ($literal[0] == 'INTEGER') {
        return 'integer';
    }
    if ($literal[0] == 'FLOAT') {
        return 'float';
    }
    if ($literal[0] == 'STRING') {
        return 'string';
    }
    if ($literal[0] == 'BOOLEAN') {
        if (!$literal[1]) {
            return 'boolean-false';
        }
        return 'boolean';
    }
    if ($literal[0] == 'null') {
        return 'null';
    }
    return 'mixed';
}

function set_composr_type($identifier, $type)
{
    if (is_array($type)) {
        $type = $type[0];
    }

    global $LOCAL_VARIABLES;
    $LOCAL_VARIABLES[$identifier]['types'][] = $type;
    if (substr($type, 0, 7) == 'object-') {
        $LOCAL_VARIABLES[$identifier]['object_type'] = substr($type, 7);
    }
    if (($type == 'mixed') || ($type == '?mixed')) {
        $LOCAL_VARIABLES[$identifier]['mixed_tag'] = true;
    }

    return true;
}

function add_variable_reference($identifier, $first_mention, $reference = true)
{
    $unused_value = !$reference; // May have some problems with loops - as we may use the value in a prior command

    global $LOCAL_VARIABLES;
    if (!isset($LOCAL_VARIABLES[$identifier])) {
        $LOCAL_VARIABLES[$identifier] = ['is_global' => false, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => [], 'references' => $reference ? 1 : 0, 'object_type' => '', 'unused_value' => $unused_value, 'first_mention' => $first_mention, 'mixed_tag' => false];
    } else {
        if ($reference) {
            $LOCAL_VARIABLES[$identifier]['references']++;
        }
        $LOCAL_VARIABLES[$identifier]['unused_value'] = $unused_value;
    }
}

function reinitialise_local_variables($inside_class = false)
{
    $ret = [
        'http_response_header' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
        '_GET' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
        '_POST' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
        '_REQUEST' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
        '_COOKIE' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
        '_SERVER' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
        '_ENV' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
        '_SESSION' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
        '_FILES' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
        'GLOBALS' => ['is_global' => true, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['array'], 'references' => 0, 'object_type' => '', 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false],
    ];
    if ($inside_class) {
        global $CURRENT_CLASS;
        $ret['this'] = ['is_global' => false, 'conditioner' => [], 'conditioned_zero' => false, 'conditioned_false' => false, 'conditioned_null' => false, 'types' => ['object'], 'references' => 0, 'object_type' => $CURRENT_CLASS, 'unused_value' => false, 'first_mention' => 0, 'mixed_tag' => false];
    }
    return $ret;
}

// If the given expression is a direct variable expression, this function will infer the type as the given type. This therefore allows type infering on usage as well as on assignment
function infer_expression_type_to_variable_type($type, $expression)
{
    /*if (($expression[0] == 'VARIABLE') && (empty($expression[1][2]))) {      Not reliable
        $identifier = $expression[1][1];
        set_composr_type($identifier, $type);
    }*/
}

function ensure_type($_allowed_types, $actual_type, $pos, $alt_error = null, $extra_strict = false, $mixed_as_fail = false)
{
    if (is_array($actual_type)) {
        $actual_type = $actual_type[0];
    }

    if ((ltrim($actual_type, '~?') == 'mixed') && (!$mixed_as_fail)) {
        return true; // We can't check it
    }

    // Tidy up our allow list to be a nice map
    if ((!$extra_strict) && ((in_array('boolean', $_allowed_types)) || (in_array('?boolean', $_allowed_types)))) {
        $_allowed_types[] = 'boolean-false';
    }
    if (($extra_strict) && ($_allowed_types == ['boolean'])) {
        $_allowed_types[] = 'boolean-false';
    }
    $allowed_types = [];
    foreach ($_allowed_types as $type) {
        if (is_array($type)) {
            $type = $type[0];
        }

        if ((ltrim($type, '~?') == 'mixed') || (ltrim($type, '~?') == 'resource')) {
            return true; // Anything works!
        }
        if (strpos($type, '?') !== false) {
            $type = str_replace('?', '', $type);
            $allowed_types['null'] = true;
        }
        if (strpos($type, '~') !== false) {
            $type = str_replace('~', '', $type);
            $allowed_types['boolean-false'] = true;
        }
        if (substr($type, 0, 6) == 'object') {
            $type = 'object';
        }
        if ($type == 'REAL') {
            $allowed_types['float'] = true;
        }
        if (in_array($type, ['AUTO', 'INTEGER', 'UINTEGER', 'SHORT_TRANS', 'LONG_TRANS', 'SHORT_TRANS__COMCODE', 'LONG_TRANS__COMCODE', 'MEMBER', 'MEMBER', 'SHORT_INTEGER', 'AUTO_LINK', 'BINARY', 'GROUP', 'TIME'])) {
            $allowed_types['integer'] = true;
        }
        if (in_array($type, ['LONG_TEXT', 'SHORT_TEXT', 'MINIID_TEXT', 'ID_TEXT', 'LANGUAGE_NAME', 'URLPATH', 'PATH', 'IP', 'EMAIL'])) {
            $allowed_types['string'] = true;
        }
        if (in_array($type, ['Tempcode'])) {
            $allowed_types['object'] = true;
        }
        if (in_array($type, ['list', 'map'])) {
            $allowed_types['array'] = true;
        }
        $allowed_types[$type] = true;
    }

    // Special cases for our actual type
    if (strpos($actual_type, '?') !== false) {
        //if (isset($allowed_types['null'])) return true;    We can afford not to give this liberty due to is_null
        $actual_type = str_replace('?', '', $actual_type);
    }
    if (strpos($actual_type, '~') !== false) {
        if (isset($allowed_types['boolean-false'])) {
            return true;
        }
        $actual_type = str_replace('~', '', $actual_type);
    }
    if (substr($actual_type, 0, 6) == 'object') {
        $actual_type = 'object';
    }

    // The check
    if (isset($allowed_types[$actual_type])) {
        return true;
    }
    if ($actual_type == 'REAL') {
        if (isset($allowed_types['float'])) {
            return true;
        }
    }
    if (in_array($actual_type, ['AUTO', 'INTEGER', 'UINTEGER', 'SHORT_TRANS', 'LONG_TRANS', 'SHORT_TRANS__COMCODE', 'LONG_TRANS__COMCODE', 'MEMBER', 'MEMBER', 'SHORT_INTEGER', 'AUTO_LINK', 'BINARY', 'GROUP', 'TIME'])) {
        if (isset($allowed_types['integer'])) {
            return true;
        }
    }
    if (in_array($actual_type, ['LONG_TEXT', 'SHORT_TEXT', 'MINIID_TEXT', 'ID_TEXT', 'LANGUAGE_NAME', 'URLPATH', 'PATH', 'IP', 'EMAIL'])) {
        if (isset($allowed_types['string'])) {
            return true;
        }
    }
    if (in_array($actual_type, ['Tempcode'])) {
        if (isset($allowed_types['object'])) {
            return true;
        }
    }
    if (in_array($actual_type, ['list', 'map'])) {
        if (isset($allowed_types['array'])) {
            return true;
        }
    }

    if ($alt_error != '') {
        log_warning(($alt_error === null) ? 'Type mismatch' : $alt_error, $pos);
    }

    return false;
}

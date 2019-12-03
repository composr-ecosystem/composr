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

// Tracking
global $COMPOSR_PATH, $START_TIME, $MADE_CALL;
$START_TIME = time();
$MADE_CALL = null;

// Customise PHP environment
ini_set('memory_limit', '-1');
ini_set('display_errors', '1');
ini_set('pcre.jit', '0'); // Compatibility issue in PHP 7.3, "JIT compilation failed: no more memory"
if (function_exists('set_time_limit')) {
    @set_time_limit(10000);
}

// Handle options
$available_options = [
    // CQC flags
    'api' => [ // We probably want this
        'auto_global' => true,
        'takes_value' => false,
    ],
    'todo' => [ // We probably want this
        'auto_global' => true,
        'takes_value' => false,
    ],
    'mixed' => [
        'auto_global' => true,
        'takes_value' => false,
    ],
    'pedantic' => [
        'auto_global' => true,
        'takes_value' => false,
    ],
    'security' => [
        'auto_global' => true,
        'takes_value' => false,
    ],
    'manual_checks' => [
        'auto_global' => true,
        'takes_value' => false,
    ],
    'spelling' => [
        'auto_global' => true,
        'takes_value' => false,
    ],
    'codesniffer' => [
        'auto_global' => true,
        'takes_value' => false,
    ],

    // What to test
    'test' => [
        'auto_global' => false,
        'takes_value' => true,
    ],
    'subdir' => [
        'auto_global' => false,
        'takes_value' => true,
    ],
    'enable_custom' => [
        'auto_global' => false,
        'takes_value' => false,
    ],
    'filter' => [
        'auto_global' => false,
        'takes_value' => true,
    ],
    'avoid' => [
        'auto_global' => false,
        'takes_value' => true,
    ],
    'filter_avoid' => [
        'auto_global' => false,
        'takes_value' => true,
    ],
    'start' => [
        'auto_global' => false,
        'takes_value' => true,
    ],

    // Other settings
    'base_path' => [
        'auto_global' => false,
        'takes_value' => true,
    ],
];
if (empty($_GET)) { // CLI
    $longopts = [];
    foreach ($available_options as $key => $settings) {
        $longopts[] = $key . ($settings['takes_value'] ? '::' : '');
    }
    $optind = 1;
    $options = getopt('', $longopts, $optind);
    $files_to_check = array_slice($_SERVER['argv'], $optind); // All remaining arguments are file paths
} else {
    $options = $_GET;
    unset($options['to_use']);
    $files_to_check = isset($_GET['to_use']) ? $_GET['to_use'] : []; // ?to_use[0]=a.php&to_use[1]=b.php&...
    if (!is_array($files_to_check)) {
        $files_to_check = [$files_to_check];
    }
}
foreach (array_keys($options) as $key) {
    if ($available_options[$key]['auto_global']) {
        $GLOBALS['FLAG__' . strtoupper($key)] = true;
    }
}
if (array_key_exists('base_path', $options)) {
    $COMPOSR_PATH = $options['base_path'];
} else {
    $COMPOSR_PATH = '.';
}

// Load code
require_once('check.php');
require_once('tests.php');
require_once('lex.php');
require_once('parse.php');
require_once('lib.php');

// To get it started...

if (!empty($options['test'])) {
    // Checking an internal test (by web URL)...

    $GLOBALS['FLAG__MANUAL_CHECKS'] = 1;

    $GLOBALS['FLAG__API'] = 1;
    load_function_signatures();

    $tests = get_tests();
    $parsed = parse(lex('<' . '?php' . "\n" . $tests[$_GET['test']] . "\n"));
    check($parsed);
}
if (empty($files_to_check)) {
    // Search for stuff to check using subdir/enable_custom/filter/avoid/filter_avoid/start (by web URL)...

    $avoid = [];
    if (!empty($options['avoid'])) {
        $avoid = explode(',', $options['avoid']);
    }
    $filter = [];
    if (!empty($options['filter'])) {
        $filter = explode(',', $options['filter']);
    }
    $filter_avoid = [];
    if (!empty($options['filter_avoid'])) {
        $filter_avoid = explode(',', $options['filter_avoid']);
    }
    $enable_custom = !empty($options['enable_custom']);
    $subdir = empty($options['subdir']) ? '' : $options['subdir'];
    $files_to_check = do_dir($COMPOSR_PATH . (($subdir != '') ? ('/' . $subdir) : ''), $subdir, $enable_custom, true, $avoid, $filter, $filter_avoid);
    $start = empty($options['start']) ? 0 : intval($options['start']);
    foreach ($files_to_check as $i => $to_use) {
        if ($i < $start) {
            continue; // Set to largest number we know so far work
        }

        $full_path = $COMPOSR_PATH . '/' . $to_use;

        if (strpos(file_get_contents($full_path), '/*CQC:' . ' No check*/') !== false) {
            //echo 'SKIP: ' . $to_use;
            continue;
        }

        try {
            check(parse_file($full_path, false, false, $i, count($files_to_check)));
        } catch (Exception $e) {
            echo $e->getMessage() . cnl();
        }
    }
} else {
    // Given list of things to check...

    foreach ($files_to_check as $to_use) {
        $full_path = $COMPOSR_PATH . '/' . $to_use;

        if (strpos(file_get_contents($full_path), '/*CQC:' . ' No check*/') !== false) {
            echo 'SKIP: ' . $to_use . cnl();
            continue;
        }

        try {
            $structure = parse_file($full_path);
            if ($structure !== null) {
                check($structure);
            }
        } catch (Exception $e) {
            echo $e->getMessage() . cnl();
        }
    }
}

echo 'FINAL Done!';

function parse_file($to_use, $verbose = false, $very_verbose = false, $i = null, $count = null)
{
    global $TOKENS, $TEXT, $FILENAME, $COMPOSR_PATH;
    $FILENAME = $to_use;

    if (($COMPOSR_PATH != '') && (substr($FILENAME, 0, strlen($COMPOSR_PATH)) == $COMPOSR_PATH)) {
        $FILENAME = substr($FILENAME, strlen($COMPOSR_PATH));
        if (substr($FILENAME, 0, 1) == '/') {
            $FILENAME = substr($FILENAME, 1);
        }
        if (substr($FILENAME, 0, 1) == '/') {
            $FILENAME = substr($FILENAME, 1);
        }
    }

    $codesniffer_only = null; // For debugging

    if ($codesniffer_only === null) {
        $TEXT = str_replace("\r", '', file_get_contents($to_use));
        if (substr($TEXT, 0, 4) == hex2bin('efbbbf')) { // Strip a utf-8 BOM
            $TEXT = substr($TEXT, 4);
        }
    }

    if ($verbose) {
        echo '<hr /><p>DOING ' . $to_use . '</p>';
    }
    if ($verbose) {
        echo '<pre>';
    }
    if ($codesniffer_only === null) {
        if ($very_verbose) {
            echo '<b>Our code...</b>' . "\n";
            echo htmlentities($TEXT);
        }
    }

    if ($verbose) {
        echo "\n\n" . '<b>Starting lexing...</b>' . "\n";
    }
    if ($codesniffer_only === null) {
        $TOKENS = lex();
        if ($very_verbose) {
            print_r($TOKENS);
        }
        if ($very_verbose) {
            echo strval(count($TOKENS)) . ' tokens';
        }
    }

    if ($verbose) {
        echo "\n\n" . '<b>Starting parsing...</b>' . "\n";
    }
    if ($codesniffer_only === null) {
        $structure = parse();
        if ($very_verbose) {
            print_r($structure);
        }
    } else {
        $structure = null;
    }

    if ($verbose) {
        echo '</pre>';
    }
    /*echo 'DONE ' . $FILENAME;
    if ($i !== null) {
        echo ' - ' . $i . ' of ' . $count;
    }
    echo cnl();*/

    if (!empty($GLOBALS['FLAG__CODESNIFFER'])) {
        if (strpos(shell_exec('phpcs --version'), 'PHP_CodeSniffer') !== false) {
            $cmd = trim(shell_exec('which phpcs'));
            if ($cmd == '') {
                $cmd = 'phpcs';
            }
        } elseif (strpos(shell_exec('php phpcs.phar --version'), 'PHP_CodeSniffer') !== false) {
            $cmd = trim(shell_exec('which php'));
            if ($cmd == '') {
                $cmd = 'php';
            }
 
            $where = trim(shell_exec('where phpcs.phar'));
            if ($where == '') {
                $where = 'phpcs.phar';
            }
 
            $cmd .= ' ' . $where;
        } else {
            echo "Cannot find PHP CodeSniffer in the path\n";
        }

        $cmd_line = $cmd . ' ' . $to_use . ' -s -q --report-width=10000';
        //$cmd_line .= ' --standard=PSR12'; Better to just disable sniffs we don't like
        $skip_tests_broad = [
            // In standards we don't support
            'PEAR.NamingConventions.ValidClassName',
            'PEAR.NamingConventions.ValidFunctionName',
            'PEAR.NamingConventions.ValidVariableName',
        ];
        if (!empty($skip_tests_broad)) {
            $cmd_line .= ' --exclude=' . implode(',', $skip_tests_broad);
        }
        if ($codesniffer_only !== null) {
            $cmd_line .= ' --sniffs=' . implode(',', $codesniffer_only);
        }
        $out = shell_exec($cmd_line . ' 2>&1');
        $pending_out = null;
        $matches = [];
        foreach (explode("\n", $out) as $msg_line) {
            if (preg_match('#^\s*(\d+)\s*\|\s*(\w+)\s*\|\s*(.*)$#', $msg_line, $matches) != 0) {
                if ($pending_out !== null) {
                    if (!filtered_codesniffer_result($pending_out)) {
                        echo $pending_out . cnl();
                    }
                    $pending_out = null;
                }

                $line = $matches[1];
                $pos = '0';
                $message_type = $matches[2];
                $message = $matches[3];

                $pending_out = 'PHPCS-' . $message_type . ' "' . $FILENAME . '" ' . $line . ' ' . $pos . ' ' . $message;
            } elseif (preg_match('#^\s*\|\s*\|\s*(.*)$#', $msg_line, $matches) != 0) {
                $pending_out .= ' ' . $matches[1];
            }
        }
        if ($pending_out !== null) {
            if (!filtered_codesniffer_result($pending_out)) {
                echo $pending_out . cnl();
            }
            $pending_out = null;
        }
    }

    return $structure;
}

function filtered_codesniffer_result($message)
{
    $skip_tests = [
        'Generic.Files.LineLength.TooLong', // We don't follow this standard strictly, although we try and avoid long lines when reasonable
        'Generic.WhiteSpace.ScopeIndent.Incorrect', // Composr has its own check, and this one fails on switch structures with no break
        'Generic.WhiteSpace.ScopeIndent.IncorrectExact', // Composr has its own check, and this one fails on switch structures with no break
        'Squiz.Classes.ValidClassName.NotCamelCaps', // This is not even in PSR-1
        'Squiz.Functions.MultiLineFunctionDeclaration.EmptyLine', // May split if across multiple lines
        'PSR1.Classes.ClassDeclaration.MissingNamespace', // No namespaces
        'PSR1.Classes.ClassDeclaration.MultipleClasses', // We don't follow this standard strictly
        'PSR1.Files.SideEffects.FoundWithSymbols', // Blunt test
        'PSR1.Methods.CamelCapsMethodName.NotCamelCaps', // This is not even in PSR-1
        'PSR2.Classes.PropertyDeclaration.Underscore', // This is not a failure, should not be treated as such
        'PSR2.Methods.FunctionCallSignature.EmptyLine', // May split if across multiple lines
        'PSR2.Methods.MethodDeclaration.Underscore', // This is not a failure, should not be treated as such

        // In standards we don't support
        'Generic.Commenting.DocComment.ContentAfterOpen',
        'Generic.Commenting.DocComment.ContentBeforeClose',
        'Generic.Commenting.DocComment.LongNotCapital',
        'Generic.Commenting.DocComment.MissingShort',
        'Generic.Commenting.DocComment.NonParamGroup',
        'Generic.Commenting.DocComment.ParamNotFirst',
        'Generic.Commenting.DocComment.ShortNotCapital',
        'Generic.Commenting.DocComment.SpacingBeforeShort',
        'Generic.Commenting.DocComment.SpacingBeforeTags',
        'Generic.Commenting.DocComment.TagsNotGrouped',
        'Generic.Commenting.DocComment.TagValueIndent',
        'Generic.PHP.DisallowShortOpenTag.EchoFound',
        'PEAR.Commenting.ClassComment.InvalidPackage',
        'PEAR.Commenting.ClassComment.InvalidVersion',
        'PEAR.Commenting.ClassComment.MissingAuthorTag',
        'PEAR.Commenting.ClassComment.MissingCategoryTag',
        'PEAR.Commenting.ClassComment.MissingLicenseTag',
        'PEAR.Commenting.ClassComment.MissingLinkTag',
        'PEAR.Commenting.ClassComment.MissingPackageTag',
        'PEAR.Commenting.ClassComment.WrongStyle',
        'PEAR.Commenting.FileComment.IncompleteCopyright',
        'PEAR.Commenting.FileComment.InvalidPackage',
        'PEAR.Commenting.FileComment.LicenseTagOrder',
        'PEAR.Commenting.FileComment.MissingAuthorTag',
        'PEAR.Commenting.FileComment.MissingCategoryTag',
        'PEAR.Commenting.FileComment.MissingLinkTag',
        'PEAR.Commenting.FileComment.MissingVersion',
        'PEAR.Commenting.FileComment.PackageTagOrder',
        'PEAR.Commenting.FileComment.WrongStyle',
        'PEAR.Commenting.FunctionComment.Missing',
        'PEAR.Commenting.FunctionComment.SpacingAfter',
        'PEAR.Commenting.FunctionComment.SpacingAfterParamName',
        'PEAR.Commenting.FunctionComment.SpacingAfterParamType',
        'PEAR.Commenting.FunctionComment.MissingReturn', // Assumes every function has a return!
        'PEAR.ControlStructures.MultiLineCondition.Alignment',
        'PEAR.ControlStructures.MultiLineCondition.CloseBracketNewLine',
        'PEAR.ControlStructures.MultiLineCondition.SpacingAfterOpenBrace',
        'PEAR.ControlStructures.MultiLineCondition.StartWithBoolean',
        'PEAR.Files.IncludingFile.BracketsNotRequired',
        'PEAR.Files.IncludingFile.UseInclude',
        'PEAR.Files.IncludingFile.UseIncludeOnce',
        'PEAR.Files.IncludingFile.UseRequire',
        'PEAR.Formatting.MultiLineAssignment.EqualSignLine',
        'PEAR.Functions.FunctionCallSignature.CloseBracketLine',
        'PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket',
        'PEAR.Functions.FunctionCallSignature.EmptyLine',
        'PEAR.Functions.FunctionCallSignature.OpeningIndent',
        'PEAR.Functions.FunctionDeclaration.EmptyLine',
        'PEAR.WhiteSpace.ScopeIndent.Incorrect',
        'PEAR.WhiteSpace.ScopeIndent.IncorrectExact',

        // If there is no function comment, the file comment will be moved down, triggering these -- Composr will pick up on them anyway
        'PEAR.Commenting.FunctionComment.MissingParamTag',
        'PEAR.Commenting.FunctionComment.WrongStyle',
    ];

    foreach ($skip_tests as $skip_test) {
        if (strpos($message, '(' . $skip_test . ')') !== false) {
            return true;
        }
    }
    return false;
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

function unit_testing_run()
{
    global $SCREEN_TEMPLATE_CALLED;
    $SCREEN_TEMPLATE_CALLED = '';

    @header('Content-Type: text/html');

    safe_ini_set('ocproducts.type_strictness', '0');
    safe_ini_set('ocproducts.xss_detect', '0');

    require_code('_tests/simpletest/unit_tester.php');
    require_code('_tests/simpletest/web_tester.php');
    require_code('_tests/simpletest/mock_objects.php');
    require_code('_tests/simpletest/collector.php');
    require_code('_tests/cmstest/cms_test_case.php');

    if ((isset($_SERVER['argv'])) && (in_array('debug', $_SERVER['argv']))) {
        $_GET['debug'] = '1';
    }

    $id = get_param_string('id', null);
    if (($id === null) && (isset($_SERVER['argv'][1]))) {
        $id = $_SERVER['argv'][1];
        $cli = true;
    } else {
        $cli = false;
    }
    if (!is_null($id)) {
        if (!$cli) {
            testset_do_header('Running test set: ' . escape_html($id));
        }
        run_testset($id);
        if (!$cli) {
            testset_do_footer();
        }

        return;
    }

    testset_do_header('Choose a test set');

    $sets = find_testsets();
    echo '(The ones starting <kbd>_</kbd> should be run individually [no concurrency], and also only occasionally except for <kbd>_cqc__function_sigs</kbd> and <kbd>_installer</kbd> which are crucial and to be run first)';
    echo '<ul>';
    foreach ($sets as $set) {
        echo '<li><a href="?id=' . escape_html(urlencode($set)) . '&amp;close_if_passed=1">' . escape_html($set) . '</a></li>' . "\n";
    }
    echo '</ul>';

    testset_do_footer();
}

function find_testsets($dir = '')
{
    $tests = array();
    $dh = opendir(get_file_base() . '/_tests/tests' . $dir);
    while (($file = readdir($dh))) {
        if ((is_dir(get_file_base() . '/_tests/tests' . $dir . '/' . $file)) && (substr($file, 0, 1) != '.')) {
            $tests = array_merge($tests, find_testsets($dir . '/' . $file));
        } else {
            if (substr($file, -4) == '.php') {
                $tests[] = substr($dir . '/' . basename($file, '.php'), 1);
            }
        }
    }
    sort($tests);
    return $tests;
}

function run_testset($testset)
{
    require_code('_tests/tests/' . filter_naughty($testset) . '.php');

    $loader = new SimpleFileLoader();
    $suite = $loader->createSuiteFromClasses(
        $testset,
        array(basename($testset) . '_test_set'));
    /*$result=*/
    $suite->run(new DefaultReporter());
}

function testset_do_header($title)
{
    echo <<<END
<!DOCTYPE html>
    <html lang="EN">
    <head>
        <title>{$title}</title>
        <link rel="icon" href="../themes/default/images/favicon.ico" type="image/x-icon" />

        <style>
END;
    @print(file_get_contents(css_enforce('global', 'default')));
    echo <<<END
            .screen_title { text-decoration: underline; display: block; background: url('../themes/default/images/icons/48x48/menu/_generic_admin/tool.png') top left no-repeat; min-height: 42px; padding: 10px 0 0 60px; }
            a[target="_blank"], a[onclick$="window.open"] { padding-right: 0; }
        </style>
    </head>
    <body class="website_body"><div class="global_middle">
        <h1 class="screen_title">{$title}</h1>
END;
    if (@ob_end_flush() !== false) {
        @ob_start(); // Push out and recreate buffer
    }
    flush();
}

function testset_do_footer()
{
    echo <<<END
        <hr />
        <p>Composr test set tool, based on SimpleTest.</p>
    </div></body>
</html>
END;
}

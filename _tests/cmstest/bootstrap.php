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

function unit_testing_run()
{
    global $SCREEN_TEMPLATE_CALLED;
    $SCREEN_TEMPLATE_CALLED = '';

    @header('Content-Type: text/html');

    cms_ini_set('ocproducts.type_strictness', '0');
    cms_ini_set('ocproducts.xss_detect', '0');

    require_code('_tests/simpletest/unit_tester.php');
    require_code('_tests/simpletest/web_tester.php');
    require_code('_tests/simpletest/mock_objects.php');
    require_code('_tests/simpletest/collector.php');
    require_code('_tests/cmstest/cms_test_case.php');

    $id = get_param_string('id', null);
    if (($id === null) && (isset($_SERVER['argv'][1]))) {
        $id = $_SERVER['argv'][1];
        $cli = true;

        if (strpos($id, '/') === false) {
            $id = 'unit_tests/' . $id;
        }
    } else {
        $cli = false;
    }
    if ($id !== null) {
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

    echo "
    <div>
        <p class=\"lonely-label\">Notes:</p>
        <ul>
            <li>The ones starting <kbd>_</kbd> should be run individually, and also only occasionally except for <kbd>_cqc__function_sigs</kbd> and <kbd>_installer</kbd> which are crucial and to be run first; this may be due to slowness, unreliability, lack of concurrency support, or some expectation of false-positives</li>
            <li>Some need running on the command line, in which case a note will be included in the test's code</li>
            <li>Some support a 'debug' GET/CLI parameter, to dump out debug information</li>
            <li>Many support GET parameters for limiting the scope of the test (look in the test's code); this is useful for tests that are really complex to get to pass, or really slow</li>
        </ul>
    </div>";
    echo '<div style="float: left; width: 40%">
        <p class="lonely-label">Tests:</p>
        <ul>';

    foreach ($sets as $set) {
        $url = 'index.php?id=' . urlencode($set);
        if (get_param_integer('keep_safe_mode', 0) == 1) {
            $url .= '&keep_safe_mode=1';
        }
        echo '<li><a href="' . escape_html($url) . '">' . escape_html($set) . '</a></li>' . "\n";
    }
    echo '
        </ul>
    </div>';

    $cnt = 0;
    foreach ($sets as $set) {
        if (strpos($set, '/_') === false) {
            $cnt++;
        }
    }
    echo '
    <div>
        <p class="lonely-label">Running tests concurrently:</p>
        <select id="select-list" multiple="multiple" size="' . escape_html(integer_format($cnt)) . '">';
    foreach ($sets as $set) {
        if (strpos($set, '/_') === false) {
            echo '<option>' . escape_html($set) . '</option>' . "\n";
        }
    }
    $proceed_icon = do_template('ICON', array('_GUID' => 'a68405d9206defe034d950fbaab1c336', 'NAME' => 'buttons/proceed'));
    echo "
        </select>
        <p><button class=\"btn btn-primary btn-scr buttons--proceed\" type=\"button\"id=\"select-button\" />{$proceed_icon} Call selection</button></p>
        <script nonce=\"" . $GLOBALS['CSP_NONCE'] . "\" id=\"select-list\">
            var list = document.getElementById('select-list');
            var button = document.getElementById('select-button');
            button.onclick = function() {
                for (var i = 0; i < list.options.length; i++) {
                    if (list.options[i].selected) {
                        var url = 'index.php?id=' + list.options[i].value + '&close_if_passed=1" . ((get_param_integer('keep_safe_mode', 0) == 1) ? '&keep_safe_mode=1' : '') . "';
                        window.open(url);
                    }
                }
            };
        </script>
    </div>
    <br style=\"clear: both\" />";

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
    closedir($dh);
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
    /*$result=*/$suite->run(new DefaultReporter());
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
    @print(cms_file_get_contents_safe(css_enforce('global', 'default'), FILE_READ_LOCK));
    echo <<<END
            .screen-title { text-decoration: underline; display: block; background: url('../themes/default/images/icons/admin/tool.svg') top left no-repeat; background-size: 48px 48px; min-height: 42px; padding: 10px 0 0 60px; }
            a[target="_blank"], a[onclick$="window.open"] { padding-right: 0; }
        </style>
    </head>
    <body class="website-body"><div class="global-middle container-fluid">
        <h1 class="screen-title">{$title}</h1>
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

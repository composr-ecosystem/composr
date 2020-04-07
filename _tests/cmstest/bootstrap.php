<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

require_once(get_file_base() . '/_tests/simpletest/unit_tester.php');
require_once(get_file_base() . '/_tests/simpletest/web_tester.php');
require_once(get_file_base() . '/_tests/simpletest/mock_objects.php');
require_once(get_file_base() . '/_tests/simpletest/collector.php');
require_once(get_file_base() . '/_tests/cmstest/cms_test_case.php');

function unit_testing_run()
{
    global $SCREEN_TEMPLATE_CALLED;
    $SCREEN_TEMPLATE_CALLED = '';

    @header('Content-Type: text/html');

    cms_ini_set('ocproducts.xss_detect', '0');

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

        $result = run_testset($id);

        if (!$cli) {
            testset_do_footer();
        }

        if ($result && !empty($_GET['close_if_passed'])) {
            echo "
                <script " . csp_nonce_html() . ">
                    if (typeof window.history!='undefined' && typeof window.history.length!='undefined' && window.history.length==1) {
                        window.close();
                    }
                </script>
            ";
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
    $proceed_icon = static_evaluate_tempcode(do_template('ICON', ['_GUID' => 'a68405d9206defe034d950fbaab1c336', 'NAME' => 'buttons/proceed']));
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
    $tests = [];
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
        [basename($testset) . '_test_set']
    );
    if (is_cli()) {
        $reporter = new DefaultReporter();
    } else {
        $reporter = new CMSHtmlReporter(get_charset(), false);
    }
    return $suite->run($reporter);
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
            .fail { background-color: inherit; color: red; }
            .pass { background-color: inherit; color: green; }
             pre { background-color: lightgray; color: inherit; }
        </style>
    </head>
    <body class="website-body"><div class="global-middle container-fluid">
        <h1 class="screen-title">{$title}</h1>
END;
    cms_flush_safe();
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

/**
 * Based on HtmlReporter, but simplified to work with our custom frontend.
 */
class CMSHtmlReporter extends SimpleReporter
{
    /**
     * Paints the end of the test with a summary of the passes and failures.
     *
     * @param string $test_name        Name class of test.
     */
    public function paintFooter($test_name)
    {
        $colour = (($this->getFailCount() + $this->getExceptionCount() > 0) ? 'red' : 'green');
        echo '<div style="';
        echo "padding: 8px; margin-top: 1em; background-color: $colour; color: white;";
        echo '">';
        echo strval($this->getTestCaseProgress()) . '/' . strval($this->getTestCaseCount());
        echo " test cases complete:\n";
        echo '<strong>' . strval($this->getPassCount()) . '</strong> passes, ';
        echo '<strong>' . strval($this->getFailCount()) . '</strong> fails and ';
        echo '<strong>' . strval($this->getExceptionCount()) . '</strong> exceptions.';
        echo "</div>\n";
    }

    /**
     * Paints the test failure with a breadcrumbs trail
     * of the nesting test suites below the top level test.
     *
     * @param string $message    Failure message displayed in the context of the other tests.
     */
    public function paintFail($message)
    {
        parent::paintFail($message);
        echo '<span class="fail">Fail</span>: ';
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        echo implode(' -&gt; ', $breadcrumb);
        echo ' -&gt; ' . escape_html($message) . "<br />\n";
    }

    /**
     * Paints a PHP error.
     *
     * @param string $message        Message is ignored.
     */
    public function paintError($message)
    {
        parent::paintError($message);
        echo '<span class="fail">Exception</span>: ';
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        echo implode(' -&gt; ', $breadcrumb);
        echo ' -&gt; <strong>' . escape_html($message) . "</strong><br />\n";
    }

    /**
     * Paints a PHP exception.
     *
     * @param Exception $exception        Exception to display.
     */
    public function paintException($exception)
    {
        parent::paintException($exception);
        echo '<span class="fail">Exception</span>: ';
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        echo implode(' -&gt; ', $breadcrumb);
        $message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message [' . $exception->getMessage() .
                '] in [' . $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        echo ' -&gt; <strong>' . escape_html($message) . "</strong><br />\n";
    }

    /**
     * Prints the message for skipping tests.
     *
     * @param string $message    Text of skip condition.
     */
    public function paintSkip($message)
    {
        parent::paintSkip($message);
        echo '<span class="pass">Skipped</span>: ';
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        echo implode(' -&gt; ', $breadcrumb);
        echo ' -&gt; ' . escape_html($message) . "<br />\n";
    }

    /**
     * Paints formatted text such as dumped privateiables.
     *
     * @param string $message        Text to show.
     */
    public function paintFormattedMessage($message)
    {
        echo '<pre>' . escape_html($message) . '</pre>';
    }
}

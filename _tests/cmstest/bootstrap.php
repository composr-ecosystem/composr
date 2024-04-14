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
            <li>The one(s) starting <kbd>___</kbd> should only be run through the command line / PHP cli (the command is indicated in a comment in the file). These tests cannot be run with the concurrent tool.
            <li>The ones starting <kbd>__</kbd> should be run occasionally with discretion, and one at a time, due to significant slowness, the expectation of false-positives, or the need for an API key file. These tests cannot be run with the concurrent tool.
            <li>The ones starting <kbd>_</kbd> should be run one at a time due to slowness or conflicting with other tests. They will run one at a time after other tests finish in the concurrent tool.</li>
            <li>Some support a 'debug' GET/CLI parameter, to dump out debug information</li>
            <li>Some support an 'only' GET parameter (or initial CLI argument) for limiting the scope of the test (look in the test's code); this is useful for tests that are really complex to get to pass, or really slow</li>
        </ul>
    </div>";

    $cnt = 0;
    foreach ($sets as $set) {
        if (strpos($set, '/__') === false) {
            $cnt++;
        }
    }
    echo '
    <div style="float: right; width: 40%">
        <p class="lonely-label">Running tests concurrently:</p>
        <select id="select-list" multiple="multiple" size="' . escape_html(integer_format($cnt)) . '">';
    foreach ($sets as $set) {
        if (strpos($set, '/__') === false) {
            echo '<option>' . escape_html($set) . '</option>' . "\n";
        }
    }
    $proceed_icon = static_evaluate_tempcode(do_template('ICON', ['_GUID' => 'a68405d9206defe034d950fbaab1c336', 'NAME' => 'buttons/proceed']));

    echo "
        </select>
        <p><button class=\"btn btn-primary btn-scr buttons--proceed\" type=\"button\"id=\"select-button\" />{$proceed_icon} Call selection</button></p>
        <p>Status of tests will appear on the left. Click a status to view its output at the bottom of the page.</p>
        <script nonce=\"" . $GLOBALS['CSP_NONCE'] . "\" id=\"select-list\">
            var max_slots = 2; // Set to the maximum number of tests to run concurrently. Careful as too many tests could result in some timing out.

            var process_urls_process;
            var test_urls;
            var on_hold_iframes;
            var navigated_iframes;
            var actual_max_slots = 0;

            var list = document.getElementById('select-list');
            var button = document.getElementById('select-button');
            button.onclick = function() {
                button.disabled = true;
                actual_max_slots = max_slots;

                var test_statuses = document.querySelectorAll('.js_test_status');
                test_statuses.forEach(function initTestStatus(test_status) {
                    test_status.innerHTML = '';
                    test_status.addEventListener('click', function() {
                        console.dir(test_status);
                        var test_iframes = document.querySelectorAll('.js_test_iframe');
                        for (var j = 0; j < test_iframes.length; j++) {
                            if (test_iframes[j].id === test_status.id.replace('status-', 'iframe-')) {
                                test_iframes[j].style.display = 'block';
                            } else {
                                test_iframes[j].style.display = 'none';
                            }
                        }
                    });
                });

                test_urls = [];
                on_hold_iframes = [];
                for (var i = 0; i < list.options.length; i++) {
                    if (list.options[i].selected) {
                        var name = list.options[i].value;
                        var url = 'index.php?id=' + name + '" . ((get_param_integer('keep_safe_mode', 0) == 1) ? '&keep_safe_mode=1' : '') . "';
                        var existing_iframe = document.getElementById('iframe-' + name.replace('/', '__'));
                        var test_status = document.getElementById('status-' + name.replace('/', '__'));
                        if (existing_iframe == null) {
                            var url_iframe = document.createElement('iframe');
                            url_iframe.classList.add('js_test_iframe');
                            url_iframe.id = 'iframe-' + name.replace('/', '__');
                            url_iframe.style.display = 'none';
                            url_iframe.style.width = '100%';
                            url_iframe.style.height = '768px';
                            document.body.appendChild(url_iframe);
                            if (name.includes('/_')) { // These tests must be run one at a time
                                on_hold_iframes.push([url, url_iframe, name.replace('/', '__')]);
                            } else {
                                test_urls.push([url, url_iframe, name.replace('/', '__')]);
                            }
                            (function(url_iframe_b, test_status_b, url_b, name_b) {
                                url_iframe.addEventListener('error', function(event) {
                                    if (actual_max_slots > 1) {
                                        test_status_b.innerHTML = '<span style=\"color: DarkRed;\">Error thrown by iframe; pending second attempt</span>';
                                        on_hold_iframes.push([url_b, url_iframe_b, name_b]);

                                        // Allow us to reload the source later
                                        url_iframe_b.src = 'about:blank';
                                    } else {
                                        test_status_b.innerHTML = '<span style=\"color: Red;\">Error thrown by iframe! Will not try again.</span>';
                                    }
                                });
                            })(url_iframe, test_status, url, name);
                        } else {
                            existing_iframe.src = 'about:blank';
                            if (name.includes('/_')) { // These tests must be run one at a time
                                on_hold_iframes.push([url, existing_iframe, name.replace('/', '__')]);
                            } else {
                                test_urls.push([url, existing_iframe, name.replace('/', '__')]);
                            }
                        }
                        test_status.innerHTML = '<span style=\"color: DimGrey;\">Queued</span>';
                    }
                }

                process_urls_process = window.setInterval(process_urls, 1000);

                navigated_iframes = [];
            };

            function process_urls()
            {
                var navigated_iframes_cleaned = [];
                var active_iframes = 0;
                for (var i = 0; i < navigated_iframes.length; i++) {
                    var url = navigated_iframes[i][0];
                    var url_iframe = navigated_iframes[i][1];
                    var name = navigated_iframes[i][2];
                    var test_status = document.getElementById('status-' + name);
                    try {
                        if (url_iframe && url_iframe.contentDocument && (url_iframe.contentDocument.readyState !== 'complete' || url_iframe.contentDocument.URL === 'about:blank' || url_iframe.contentDocument.URL === '')) {
                            navigated_iframes_cleaned.push([url, url_iframe, name]);
                            test_status.innerHTML = '<span style=\"color: BlueViolet;\">Running test...</span>';
                            active_iframes++;
                        } else {
                            console.log('Concluded ' + name);
                            if (url_iframe.contentDocument.body.innerText.includes('0 fails and 0 exceptions')) {
                                test_status.innerHTML = '<span style=\"color: Green;\">Finished; all tests passed</span>';
                            } else if (url_iframe.contentDocument.body.innerText.includes('fails')) {
                                test_status.innerHTML = '<span style=\"color: Coral;\">Finished; some tests failed</span>';
                            } else if (actual_max_slots > 1) {
                                test_status.innerHTML = '<span style=\"color: DarkRed;\">Failed to run; pending second attempt</span>';
                                on_hold_iframes.push([url, url_iframe, name]);

                                // Allow us to reload the source later
                                url_iframe.src = 'about:blank';
                            } else {
                                test_status.innerHTML = '<span style=\"color: Red;\">Failed to run! Will not try again.</span>';
                            }
                        }
                    } catch {
                        navigated_iframes_cleaned.push([url, url_iframe, name]); // Consider errors as the test is still open but not running
                        test_status.innerHTML = '<span style=\"color: Red;\">An error occurred checking status!</span>';
                    }
                }
                navigated_iframes = navigated_iframes_cleaned;

                var free_slots = actual_max_slots - active_iframes;
                while ((free_slots > 0) && (test_urls.length > 0)) {
                    var url = test_urls[0][0];
                    var url_iframe = test_urls[0][1];
                    var name = test_urls[0][2];
                    var test_status = document.getElementById('status-' + name);

                    console.log('Loading ' + name);

                    url_iframe.src = url;

                    navigated_iframes.push([url, url_iframe, name]);

                    test_urls.splice(0, 1); // Delete array element

                    free_slots--;

                    test_status.innerHTML = '<span style=\"color: BlueViolet;\">Running test...</span>';
                }

                if (navigated_iframes.length === 0) {
                    if (on_hold_iframes.length > 0) { // Tests queued to run one at a time and after concurrent ones
                        actual_max_slots = 1;
                        test_urls = on_hold_iframes;

                        console.log('Finished testing; synchronously re-loading tests that failed to run, and running individual tests');
                    } else {
                        button.disabled = false;
                        window.clearInterval(process_urls_process);

                        console.log('Finished testing');
                    }
                }
            }
        </script>
    </div>";

    echo '<div>
        <p class="lonely-label">Tests:</p>
        <ul>';

    foreach ($sets as $set) {
        $url = 'index.php?id=' . urlencode($set);
        if (get_param_integer('keep_safe_mode', 0) == 1) {
            $url .= '&keep_safe_mode=1';
        }
        echo '<li><a href="' . escape_html($url) . '">' . escape_html($set) . '</a> <span style="padding-left: 2em; cursor: pointer;" class="js_test_status" id="status-' . escape_html(str_replace('/', '__', $set)) . '"></span></span></li>' . "\n";
    }
    echo "
        </ul>
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
    foreach (['_base', '_colours', 'global'] as $css_file) {
        $css_path = css_enforce($css_file, 'default');
        if ($css_path != '') {
            @print(cms_file_get_contents_safe($css_path, FILE_READ_LOCK | FILE_READ_BOM));
        }
    }
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
}

function testset_do_footer()
{
    echo <<<END
        <hr />
        <p>Composr test set tool, based on SimpleTest.</p>
        <p>When running concurrent tests, click its status to view its output below:</p>
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

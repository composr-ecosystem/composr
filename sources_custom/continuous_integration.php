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

/*EXTRA FUNCTIONS: shell_exec*/

/*
Potential future work....

As more is maintained officially (maintenance_status.csv), we can fo the following:
1) add $context processing to handle different testing.
2) inject testing with different contexts, for HEAD, on a timer (as testing each commit would be excessive)
3) potentially have multiple CI servers doing different sets of contexts (e.g. a separate MS Windows one)
*/

function init__continuous_integration()
{
    if (!defined('COMPOSR_GITLAB_PROJECT_ID')) {
        define('COMPOSR_GITLAB_PROJECT_ID', '14182874');

        $status = cms_version_branch_status();
        define('CI_EXCLUDED_TESTS', [
            // Will be added back to run first
            'unit_tests/cqc__function_sigs',

            // Very slow
            'unit_tests/_actionlog',
            'unit_tests/___bash_parser',
            'unit_tests/__backups',
            'unit_tests/__broken_links',
            'unit_tests/_images',
            'unit_tests/__installer_xml_db', // (Messes with _config.php too)
            'unit_tests/_tutorial_quality',
            'unit_tests/_special_links',
            'unit_tests/_newsletters',
            'unit_tests/_http_timeouts',
            'unit_tests/_web_resources',
            'unit_tests/_oembed',
            'unit_tests/_feeds_and_podcasts',
            'unit_tests/_filter_xml',
            'unit_tests/_lang_no_unused',
            // 'unit_tests/__installer', Important enough to spend the time on it (Messes with _config.php but we do not run in parallel)

            // Excessively complex and make not always succeed depending on test site context
            'unit_tests/___performance',
            'unit_tests/___resource_fs',
            'unit_tests/___blob_slowdown',
            'unit_tests/_commandr_fs',
            'unit_tests/___database_integrity',
            'unit_tests/__installer_forum_drivers', // (Messes with _config.php too)
            'unit_tests/_tasks',
            'unit_tests/_template_previews',
            'unit_tests/_lang_spelling_epic',

            // Is not expected to pass
            'unit_tests/___health_check',

            // Messes with _config.php but we do not run in parallel
            /*'unit_tests/__rate_limiting',
            'unit_tests/__critical_error_display',
            'unit_tests/___static_caching',
            'unit_tests/__extra_logging',*/

            // Can not run over HTTPS
            ($status == VERSION_ALPHA || $status == VERSION_BETA) ? '_copyright' : null,
            ($status == VERSION_ALPHA || $status == VERSION_BETA) ? '_tracker_categories' : null,
        ]);

        define('CI_COMMIT_QUEUE_PATH', get_custom_file_base() . '/data_custom/ci_queue.bin');
    }

    require_code('files');
    require_code('files2');
    require_code('failure');
}

// e.g. To queue http://localhost/composr/data_custom/continuous_integration.php?ci_password=test&commit_id=HEAD&verbose=1&dry_run=1&limit_to=awards&immediate=0&output=1
// e.g. To process queue http://localhost/composr/data_custom/continuous_integration.php?ci_password=test&ignore_lock=1&output=1
function continuous_integration_script()
{
    set_throw_errors(true);

    try {
        authenticate_ci_request();

        $cli = is_cli();

        // Handle request to enqueue a commit
        if ((isset($_SERVER['HTTP_X_GITLAB_EVENT'])) && ($_SERVER['HTTP_X_GITLAB_EVENT'] == 'Push Hook')) {
            $request = json_decode(file_get_contents('php://input'), true);
            if ($request['project_id'] != COMPOSR_GITLAB_PROJECT_ID) {
                throw new Exception('Web hook call is for the wrong project');
            }
            $commit_id = $request['after']; // Technically we're checking all commits in a push, but we just check against the last one
        } else {
            $commit_id = ($cli ? 'HEAD' : get_param_string('commit_id', null)); // Can be HEAD
        }
        if ($commit_id !== null) {
            $verbose = (get_param_integer('verbose', 0) == 1) || ($cli);
            $dry_run = (get_param_integer('dry_run', 0) == 1) || ($cli);
            $output = (get_param_integer('output', 0) == 1);
            $_limit_to = get_param_string('limit_to', '');
            $limit_to = ($_limit_to == '') ? null : explode(',', $_limit_to);
            if ($limit_to !== null) {
                foreach ($limit_to as $i => $_limit_to) {
                    if (strpos($_limit_to, '/') === false) {
                        $limit_to[$i] = 'unit_tests/' . $_limit_to;
                    }
                }
            }
            $context = json_decode(get_param_string('context', '{}'), true);
            enqueue_testable_commit($commit_id, $verbose, $dry_run, $limit_to, $context, $output);

            $immediate = (get_param_integer('immediate', 0) == 1) || ($cli);
        } else {
            $immediate = true;
        }

        // Process queue
        if ($immediate) {
            $output = (get_param_integer('output', 0) == 1) || ($cli);
            $ignore_lock = (get_param_integer('ignore_lock', 0) == 1) || ($cli);

            process_ci_queue($output, $ignore_lock, $cli);
        }
    } catch (Exception $e) {
        fatal_exit($e->getMessage());
    }
}

function authenticate_ci_request()
{
    if (is_cli()) {
        return; // No authentication needed
    }

    global $SITE_INFO;
    if (!isset($SITE_INFO['ci_password'])) {
        throw new Exception('No CI password defined in _config.php');
    }
    $real_password = $SITE_INFO['ci_password'];

    if (isset($_SERVER['HTTP_X_GITLAB_TOKEN'])) {
        $given_password = $_SERVER['HTTP_X_GITLAB_TOKEN'];
        if ($given_password != $real_password) {
            throw new Exception('Incorrect CI password');
        }
        return;
    }

    $given_password = get_param_string('ci_password', null);
    if ($given_password !== null) {
        if ($given_password != $real_password) {
            throw new Exception('Incorrect CI password');
        }
        return;
    }

    throw new Exception('No CI password given');
}

function load_ci_queue()
{
    $blank_queue = [
        'queue' => [],
        'lock_timestamp' => null,
    ];

    if (is_file(CI_COMMIT_QUEUE_PATH)) {
        $commit_queue = @unserialize(cms_file_get_contents_safe(CI_COMMIT_QUEUE_PATH, FILE_READ_LOCK));
        if ($commit_queue === false) {
            $commit_queue = $blank_queue;
        }
    } else {
        $commit_queue = $blank_queue;
    }
    return $commit_queue;
}

function enqueue_testable_commit($commit_id, $verbose, $dry_run, $limit_to, $context, $output)
{
    $commit_queue = load_ci_queue();

    // Write to queue
    $queue_item = ['commit_id' => $commit_id, 'verbose' => $verbose, 'dry_run' => $dry_run, 'limit_to' => $limit_to, 'context' => $context];
    $commit_queue['queue'][] = $queue_item;
    cms_file_put_contents_safe(CI_COMMIT_QUEUE_PATH, serialize($commit_queue));

    if ($output) {
        @header('Content-Type: text/plain; charset=' . get_charset());
        cms_ini_set('ocproducts.xss_detect', '0');
        echo 'Enqueued';
    }
}

function process_ci_queue($output, $ignore_lock = false, $lifo = false)
{
    if ($output) {
        @header('Content-Type: text/plain; charset=' . get_charset());
        cms_ini_set('ocproducts.xss_detect', '0');
    }

    $commit_queue = load_ci_queue();
    $prior_lock_timestamp = $commit_queue['lock_timestamp'];

    if (($prior_lock_timestamp === null) || ($ignore_lock)) {
        if ($lifo) {
            $next_commit_details = array_pop($commit_queue['queue']);
        } else {
            $next_commit_details = array_shift($commit_queue['queue']);
        }
        if ($next_commit_details !== null) {
            // Lock (and remove from queue)
            $commit_queue['lock_timestamp'] = time();
            cms_file_put_contents_safe(CI_COMMIT_QUEUE_PATH, serialize($commit_queue));

            // Process
            $commit_id = $next_commit_details['commit_id'];
            $verbose = $next_commit_details['verbose'];
            $dry_run = $next_commit_details['dry_run'];
            $limit_to = $next_commit_details['limit_to'];
            $context = $next_commit_details['context'];
            $results = test_commit($output, $commit_id, $verbose, $dry_run, $limit_to, $context);

            // Unlock
            $commit_queue['lock_timestamp'] = null;
            cms_file_put_contents_safe(CI_COMMIT_QUEUE_PATH, serialize($commit_queue));
        } else {
            if ($output) {
                echo 'Queue is empty';
            }
        }
    } else {
        if ($output) {
            echo 'Queue is locked';
        }

        if ($prior_lock_timestamp < time() - 60 * 60) {
            throw new Exception('CI queue has been locked for over an hour');
        }
    }
}

function test_commit($output, $commit_id, $verbose, $dry_run, $limit_to, $context)
{
    // We do not currently do anything with $context. Future work would be to be able to use it to switch main configuration (e.g. database backend).

    $old_branch = git_repos();

    if ($commit_id != 'HEAD') {
        shell_exec('git fetch 2>&1');
        $msg = shell_exec('git checkout ' . escapeshellarg($commit_id) . ' 2>&1');
        if (trim(shell_exec('git rev-parse HEAD 2>&1')) != $commit_id) {
            throw new Exception('Failed to checkout commit ' . $commit_id . ': ' . $msg);
        }
    }

    $results = run_all_applicable_tests($output, $commit_id, $verbose, $dry_run, $limit_to);

    if (($commit_id != 'HEAD') && ($commit_id != $old_branch)) {
        shell_exec('git checkout ' . $old_branch . ' 2>&1');
    }

    return $results;
}

function run_all_applicable_tests($output, $commit_id, $verbose, $dry_run, $limit_to)
{
    cms_disable_time_limit();

    chdir(get_file_base());

    $successes = [];
    $fails = [];

    $_before = microtime(true);

    $tests = find_all_applicable_tests($limit_to);
    foreach ($tests as $test) {
        $before = microtime(true);
        $result = trim(shell_exec(PHP_BINARY . ' _tests/index.php ' . escapeshellarg($test) . ' 2>&1'));
        $after = microtime(true);
        $time = $after - $before;

        $success = (strpos($result, 'Failures: 0, Exceptions: 0') !== false);
        $details = ['result' => $result, 'time' => $time, 'stub' => ' [time = ' . float_format($time) . ' seconds]'];
        if ($success) {
            $successes[$test] = $details;
        } else {
            $fails[$test] = $details;
        }

        if ($output) {
            echo $result . "\n" . 'Completed ' . $test . $details['stub'] . "\n";
        }
    }

    $_after = microtime(true);
    $_time = $_after - $_before;
    if ($output) {
        echo 'FINISHED [time = ' . float_format($_time) . ' seconds]' . "\n";
    }

    $results = '';

    if ((!empty($fails)) || ($verbose)) {
        $results .= 'Errors while running automated tests (CI server)...' . "\n\n";

        if (!empty($fails)) {
            foreach ($fails as $test => $details) {
                $result = $details['result'];
                if (strlen($result) > 1000) {
                    $result = substr($result, 0, 100) . '...';
                }

                $results .= $test . $details['stub'] . "\n" . str_repeat('=', strlen($test)) . "\n\n" . $result . "\n\n";
            }
        } else {
            $results .= '(none)' . "\n\n";
        }

        if ($verbose) {
            $results .= 'Passed tests...' . "\n\n";

            if (!empty($successes)) {
                foreach ($successes as $test => $details) {
                    $results .= $test . $details['stub'] . "\n";
                }
            } else {
                $results .= '(none)' . "\n\n";
            }
        }
    }

    if ($results != '') {
        if (!$dry_run) {
            post_results_to_commit($commit_id, $results);
        }
    }

    return $results;
}

function find_all_applicable_tests($limit_to = null)
{
    $_tests = [];
    $tests = get_directory_contents(get_file_base() . '/_tests/tests', '', 0, true, true, ['php']);
    foreach ($tests as $test) {
        $_test = preg_replace('#\.php$#', '', $test);
        if ((!@in_array($_test, CI_EXCLUDED_TESTS)) && (($limit_to === null) || (in_array($_test, $limit_to)))) {
            $_tests[] = $_test;
        }
    }
    sort($_tests);
    $_tests = array_merge(['unit_tests/cqc__function_sigs']/*Must run first*/, $_tests);
    return $_tests;
}

function post_results_to_commit($commit_id, $note)
{
    $project_id = '14182874';

    $url = 'https://gitlab.com/api/v4/projects/' . COMPOSR_GITLAB_PROJECT_ID . '/repository/commits/' . $commit_id . '/comments';

    global $SITE_INFO;
    if (!isset($SITE_INFO['gitlab_personal_token'])) {
        throw new Exception('No gitlab_personal_token defined in _config.php');
    }
    $token = $SITE_INFO['gitlab_personal_token'];

    $result = cms_http_request($url, ['timeout' => 100.0, 'trigger_errors' => false, 'extra_headers' => ['Private-Token' => $token], 'post_params' => ['note' => $note]]);
    if (substr($result->message, 0, 1) != '2') {
        throw new Exception($result->data);
    }
}

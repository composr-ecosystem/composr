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
    if (!defined('CI_EXCLUDED_TESTS')) {
        $status = cms_version_branch_status();
        define('CI_EXCLUDED_TESTS', [
            // Very slow
            'sync_tests/actionlog',
            'cli_tests/_bash_parser',
            'cli_tests/_backups',
            'cli_tests/_broken_links',
            'sync_tests/_images',
            'cli_tests/_installer_xml_db', // (Messes with _config.php too)
            'sync_tests/_tutorial_quality',
            'sync_tests/__special_links',
            'sync_tests/http_timeouts',
            'sync_tests/web_resources',
            'sync_tests/oembed',
            'sync_tests/feeds_and_podcasts',
            'sync_tests/filter_xml',
            'sync_tests/_lang_no_unused',
            // 'cli_tests/installer', Important enough to spend the time on it (Messes with _config.php but we do not run in parallel

            // Excessively complex and may not always succeed depending on test site context
            'cli_tests/__performance',
            'cli_tests/__resource_fs',
            'sync_tests/__blob_slowdown',
            'sync_tests/commandr_fs',
            'async_tests/_database_integrity',
            'async_tests/__database_fields',
            'cli_tests/_installer_forum_drivers', // (Messes with _config.php too)
            'sync_tests/tasks',
            'sync_tests/_template_previews',
            'sync_tests/__lang_spelling_epic',
            'async_tests/_email_spam_check', // May utilise APIs which we don't want to spam

            // Is not expected to pass
            'cli_tests/__health_check',
            'sync_tests/__debrand_epic',

            // Messes with _config.php but we do not run in parallel
            /*'sync_tests/rate_limiting',
            'sync_tests/critical_error_display',
            'sync_tests/static_caching',
            'sync_tests/extra_logging',*/

            // Not reasonable to run in pre-release versions
            ($status == VERSION_ALPHA || $status == VERSION_BETA) ? 'sync_tests/_copyright' : null,
            ($status == VERSION_ALPHA || $status == VERSION_BETA) ? 'async_tests/tracker_categories' : null,
        ]);

        define('CI_COMMIT_QUEUE_PATH', get_custom_file_base() . '/data_custom/ci_queue.bin');
    }

    require_code('files');
    require_code('files2');
    require_code('failure');
    require_code('gitlab');
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
        if (($commit_id !== null) && (!$cli)) {
            $verbose = (get_param_integer('verbose', 0) == 1) || ($cli);
            $dry_run = (get_param_integer('dry_run', 0) == 1) || ($cli);
            $output = (get_param_integer('output', 0) == 1) || ($cli);
            $_limit_to = get_param_string('limit_to', '');
            $limit_to = ($_limit_to == '') ? null : explode(',', $_limit_to);
            $context = [];
            foreach ($_GET as $name => $value) {
                if (strpos($name, 'context__') === 0) {
                    $context[str_replace('context__', '', $name)] = get_param_string($name);
                }
            }
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
        'lock_commit' => null,
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

function enqueue_testable_commit($commit_id, $verbose, $dry_run, $limit_to, $context, $output, $lock_to_commit = null)
{
    $commit_queue = load_ci_queue();
    if (is_string($lock_to_commit)) {
        $commit_queue['lock_timestamp'] = null;
        $commit_queue['lock_commit'] = $lock_to_commit;
    }

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
    $prior_lock_commit = $commit_queue['lock_commit'];

    if (($prior_lock_timestamp === null) || ($ignore_lock)) {
        if (is_string($prior_lock_commit)) {
            $next_commit_index = array_search($prior_lock_commit, collapse_1d_complexity('commit_id', $commit_queue['queue']));
            if ($next_commit_index !== false) {
                $next_commit_details = $commit_queue['queue'][$next_commit_index];
                unset($commit_queue['queue'][$next_commit_index]);
            } else {
                $next_commit_details = array_pop($commit_queue['queue']);
            }
        } else {
            $next_commit_details = array_pop($commit_queue['queue']);
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

            if ($output) {
                echo "\n" . 'Running test on commit ' . $commit_id;
            }

            $results = test_commit($output, $commit_id, $verbose, $dry_run, $limit_to, $context);

            // Unlock
            if ($results !== false) {
                $commit_queue = load_ci_queue();
                $commit_queue['lock_timestamp'] = null;
                $commit_queue['lock_commit'] = null;
                cms_file_put_contents_safe(CI_COMMIT_QUEUE_PATH, serialize($commit_queue));
            }
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

function test_commit($output, $commit_id, $verbose, $dry_run, $limit_to, &$context)
{
    if (!isset($context['old_branch'])) {
        $old_branch = git_repos();
        $context['old_branch'] = $old_branch;
    } else {
        $old_branch = $context['old_branch'];
    }

    if (!process_ci_context_hooks('before_checkout', $output, $commit_id, $verbose, $dry_run, $limit_to, $context)) {
        enqueue_testable_commit($commit_id, $verbose, $dry_run, $limit_to, $context, $output, $commit_id);
        if ($output) {
            echo "\n" . 'Need to defer continuous integration to another process / iteration. Re-added to queue.';
        }
        return false;
    }

    if (($commit_id != 'HEAD') && ((!isset($context['do_first_checkout'])) || ($context['do_first_checkout'] !== false))) {
        if ($output) {
            echo "\n" . 'Checking out commit';
        }
        shell_exec('git stash push 2>&1');
        shell_exec('git fetch 2>&1');
        $msg = shell_exec('git checkout ' . escapeshellarg($commit_id) . ' 2>&1');
        if (trim(shell_exec('git rev-parse HEAD 2>&1')) != $commit_id) {
            shell_exec('git stash pop 2>&1');
            throw new Exception('Failed to checkout commit ' . $commit_id . ': ' . $msg);
        }
        $context['do_first_checkout'] = false;
    }

    if (!process_ci_context_hooks('before', $output, $commit_id, $verbose, $dry_run, $limit_to, $context)) {
        enqueue_testable_commit($commit_id, $verbose, $dry_run, $limit_to, $context, $output, $commit_id);
        if ($output) {
            echo "\n" . 'Need to defer continuous integration to another process / iteration. Re-added to queue.';
        }
        return false;
    }

    if (!isset($context['results'])) {
        $results = run_all_applicable_tests($output, $commit_id, $verbose, $dry_run, $limit_to);
    } else {
        $results = $context['results'];
    }

    if (!process_ci_context_hooks('after', $output, $commit_id, $verbose, $dry_run, $limit_to, $context)) {
        $context['results'] = $results;
        enqueue_testable_commit($commit_id, $verbose, $dry_run, $limit_to, $context, $output, $commit_id);
        if ($output) {
            echo "\n" . 'Need to defer continuous integration to another process / iteration. Re-added to queue.';
        }
        return false;
    }

    if (($commit_id != 'HEAD') && ((!isset($context['do_last_checkout'])) || ($context['do_last_checkout'] !== false))) {
        if (($commit_id != $old_branch)) {
            shell_exec('git checkout ' . $old_branch . ' 2>&1');
        }
        shell_exec('git stash pop 2>&1');
        $context['do_last_checkout'] = false;
    }

    if (!process_ci_context_hooks('after_checkout', $output, $commit_id, $verbose, $dry_run, $limit_to, $context)) {
        $context['results'] = $results;
        enqueue_testable_commit($commit_id, $verbose, $dry_run, $limit_to, $context, $output, $commit_id);
        if ($output) {
            echo "\n" . 'Need to defer continuous integration to another process / iteration. Re-added to queue.';
        }
        return false;
    }

    return $results;
}

function run_all_applicable_tests($output, $commit_id, $verbose, $dry_run, $limit_to)
{
    cms_disable_time_limit();

    require_code('files2');

    chdir(get_file_base());

    $successes = [];
    $fails = [];

    $_before = microtime(true);

    $tests = find_all_applicable_tests($limit_to);
    foreach ($tests as $test) {
        $before = microtime(true);
        $result = trim(shell_exec('"' . find_php_path() . '" _tests/index.php ' . escapeshellarg($test) . ' 2>&1'));
        $after = microtime(true);
        $time = $after - $before;

        $success = (strpos($result, 'Failures: 0, Exceptions: 0') !== false);

        $matches = [];
        $result_count = '';
        if (preg_match_all('/^.*Test cases run.*$/m', $result, $matches)) {
            if (array_key_exists(0, $matches)) {
                foreach ($matches[0] as $match) {
                    if ($result_count != '') {
                        $result_count .= ' | ';
                    }
                    $result_count .= str_replace("\n", '', $match);
                }
            } else {
                $result_count = $success ? 'Success (unknown counts)' : 'Failure (unknown counts)';
            }
        } else {
            $result_count = $success ? 'Success (unknown counts)' : 'Failure (unknown counts)';
        }

        $details = ['result' => $result, 'count' => $result_count, 'time' => $time, 'stub' => ' [time = ' . float_format($time) . ' seconds]'];
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
                if ($verbose) {
                    $result = $details['result'];
                    if (strlen($result) > 900) {
                        $result = substr($result, 0, 900) . '...' . "\n\n" . $details['count'];
                    }
                    $results .= $test . $details['stub'] . "\n" . str_repeat('=', strlen($test)) . "\n\n" . $result . "\n\n";
                } else {
                    $result = $details['count'];
                    $results .= $test . $details['stub'] . '... ' . $result . "\n\n";
                }
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
            gitlab_add_commit_comment($commit_id, $results);
        }
    }

    return $results;
}

function find_all_applicable_tests($limit_to = null)
{
    $first_tests = [];
    $other_tests = [];
    $tests = get_directory_contents(get_file_base() . '/_tests/tests', '', 0, true, true, ['php']);
    foreach ($tests as $test) {
        $_test = preg_replace('#\.php$#', '', $test);
        if ((!@in_array($_test, CI_EXCLUDED_TESTS)) && (($limit_to === null) || (in_array($_test, $limit_to)))) {
            if ((strpos($_test, 'first_tests/') !== false)) {
                $first_tests[] = $_test;
            } else {
                $other_tests[] = $_test;
            }
        }
    }
    sort($first_tests);
    sort($other_tests);

    return array_merge($first_tests, $other_tests);
}

function process_ci_context_hooks($method, $output, $commit_id, $verbose, $dry_run, $limit_to, &$context)
{
    foreach ($context as $hook => $data) {
        $ob = get_hook_ob('systems', 'continuous_integration', $hook, 'Hook_ci_', true);
        if ($ob === null) {
            continue;
        }

        if (method_exists($ob, $method)) {
            $can_continue = call_user_func_array([$ob, $method], [$output, $commit_id, $verbose, $dry_run, $limit_to, &$context]);
            if (!$can_continue) {
                return false;
            }
        }
    }

    return true;
}

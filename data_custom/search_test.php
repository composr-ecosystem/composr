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

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!file_exists($FILE_BASE . '/sources/global.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
global $IN_SELF_ROUTING_SCRIPT;
$IN_SELF_ROUTING_SCRIPT = true;
if (!file_exists($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n" . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="EN" lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://composr.app">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

cms_ini_set('ocproducts.xss_detect', '0');

search_test_script();

function search_test_script()
{
    if (!addon_installed('cns_forum')) {
        warn_exit('cns_forum needed');
    }

    global $LAST_SEARCH_QUERY, $LAST_COUNT_QUERY, $TOTAL_RESULTS, $SEARCH_CONFIG_OVERRIDE;

    if (!addon_installed('search')) {
        warn_exit('Search not installed');
    }
    if (!addon_installed('cns_forum')) {
        warn_exit('Forum not installed');
    }

    $download = (get_param_integer('csv', 0) == 1);
    $single = (get_param_integer('single', 0) == 1);
    $filter = get_param_string('filter', '');
    $filter_searches = get_param_string('filter_searches', '');
    $delays = (get_param_integer('delays', 1) == 1);
    $warmup = (get_param_integer('warmup', 1) == 1);
    $iterations = get_param_integer('iterations', 1);
    $problematic_only = (get_param_integer('problematic_only', 0) == 1);

    $test_searches = [
        'foobar',
        'foobar search',
        '"foobar search"',
    ];

    // Warm up database
    if ($warmup) {
        $GLOBALS['FORUM_DB']->query_select_value('f_posts_fulltext_index', 'COUNT(*)', ['i_lang' => fallback_lang()]);
        $GLOBALS['FORUM_DB']->query_select_value('f_posts', 'COUNT(*)', ['p_cache_forum_id' => 6]);
        $GLOBALS['FORUM_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . "f_posts WHERE MATCH (p_post) AGAINST ('yohoho')");
    }

    $out = rows_header($download);

    require_code('search');
    require_code('database_search');
    require_code('hooks/modules/search/cns_posts');
    $ob = new Hook_search_cns_posts();
    $info = $ob->info();

    $row_header = [
        'Search query',
        'Search engine',
        'Search mode',
        'Time in seconds',
    ];
    if ($iterations > 1) {
        $row_header[] = 'Iteration';
    }
    $row_header = array_merge($row_header, [
        'Results count on first pagination page',
        'Total results',
        'Underlying queries',
    ]);

    row_header($download, $out, $row_header);

    foreach ($test_searches as $keyword_i => $search_query) {
        if (($filter_searches != '') && ($filter_searches != $search_query)) {
            continue;
        }

        for ($search_engine = 0; $search_engine <= 3; $search_engine++) {
            switch ($search_engine) {
                case 0:
                    $search_engine_label = 'MySQL full-text natural';
                    $_GET['keep_composr_fast_custom_index'] = '0';
                    list($content_where) = build_content_where($search_query);
                    break;

                case 1:
                    $search_engine_label = 'MySQL full-text boolean';
                    $_GET['keep_composr_fast_custom_index'] = '0';
                    list($content_where) = build_content_where(preg_replace('#(^| )#', '$1+', $search_query));
                    break;

                case 2:
                    $search_engine_label = 'Composr full-text (fuzzy search enabled)';
                    $SEARCH_CONFIG_OVERRIDE = ['composr_fast_custom_index__allow_fuzzy_search' => '1'];
                    $_GET['keep_composr_fast_custom_index'] = '1';
                    list($content_where) = build_content_where($search_query);
                    continue 2; // Too slow actually

                case 3:
                    $search_engine_label = 'Composr full-text (fuzzy search disabled)';
                    $SEARCH_CONFIG_OVERRIDE = ['composr_fast_custom_index__allow_fuzzy_search' => '0'];
                    $_GET['keep_composr_fast_custom_index'] = '1';
                    list($content_where) = build_content_where($search_query);
                    break;
            }

            for ($mode = 1; $mode <= 4; $mode++) {
                switch ($mode) {
                    case 1:
                        $search_mode_label = 'Straight search';
                        $author = '';
                        $author_id = null;
                        $cutoff = null;
                        $search_under = '-1';
                        break;

                    case 2:
                        $search_mode_label = 'Search by submitter';
                        $author = 'Chris Graham';
                        $author_id = 30912;
                        $cutoff = null;
                        $search_under = '-1';
                        break;

                    case 3:
                        $search_mode_label = 'Search within 30 days';
                        $author = '';
                        $author_id = null;
                        $cutoff = time() - 60 * 60 * 24;
                        $search_under = '-1';
                        break;

                    case 4:
                        $search_mode_label = 'Search under a forum';
                        $author = '';
                        $author_id = null;
                        $cutoff = null;
                        $search_under = '6';
                        break;
                }

                if (($filter != '') && (stripos($search_engine_label, $filter) === false) && (stripos($search_mode_label, $filter) === false)) {
                    continue;
                }

                if (($search_under != '!') && ($search_under != '-1') && (array_key_exists('category', $info))) {
                    $cats = explode(',', $search_under);
                    $where_clause = '(';
                    foreach ($cats as $cat) {
                        if (trim($cat) == '') {
                            continue;
                        }

                        if ($where_clause != '(') {
                            $where_clause .= ' OR ';
                        }
                        if ($info['integer_category']) {
                            $where_clause .= ((strpos($info['category'], '.') !== false) ? '' : 'r.') . $info['category'] . '=' . strval(intval($cat));
                        } else {
                            $where_clause .= db_string_equal_to(((strpos($info['category'], '.') !== false) ? '' : 'r.') . $info['category'], $cat);
                        }
                    }
                    $where_clause .= ')';
                } else {
                    $where_clause = '';
                }

                for ($i = 1; $i <= $iterations; $i++) {
                    $before_time = microtime(true);
                    $results = $ob->run(
                        $search_query,
                        $content_where,
                        $where_clause,
                        $search_under,
                        false,
                        false,
                        30,
                        0,
                        'contextual_relevance',
                        'DESC',
                        $author,
                        $author_id,
                        $cutoff
                    );
                    $after_time = microtime(true);
                    $time = $after_time - $before_time;

                    $row = [
                        $search_query,
                        $search_engine_label,
                        $search_mode_label,
                        float_format($time),
                    ];
                    if ($iterations > 1) {
                        $row[] = integer_format($i);
                    }
                    $row = array_merge($row, [
                        strval(count($results)),
                        integer_format($TOTAL_RESULTS),
                        @strval($LAST_SEARCH_QUERY) . ' LIMIT 30;' . "\n\n" . @strval($LAST_COUNT_QUERY),
                    ]);

                    if ((!$problematic_only) || ($time > 5.0)) {
                        row($download, $out, $row, $keyword_i % 2 == 1);
                        flush();
                    }
                    if (($delays) && (php_function_allowed('usleep'))) {
                        usleep(100000); // Give it 100ms to recover
                    }

                    // Reset these
                    $TOTAL_RESULTS = null;
                    $LAST_SEARCH_QUERY = null;
                    $LAST_COUNT_QUERY = null;
                    $SEARCH_CONFIG_OVERRIDE = null;
                }
            }
        }

        if ($single) {
            break;
        }
    }

    rows_footer($download, $out);
}

function rows_header($download)
{
    if ($download) {
        header('Content-Type: text/plain');

        $out = fopen('php://output', 'wb');
        return $out;
    }

    echo '<table>';
    return null;
}

function row_header($download, $out, $header_row)
{
    if ($download) {
        fputcsv($out, $header_row);
        return;
    }

    echo '<thead>';
    echo '<tr>';
    foreach ($header_row as $val) {
        echo '<th>' . escape_html($val) . '</th>';
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
}

function row($download, $out, $row, $even)
{
    if ($download) {
        fputcsv($out, $row);
        return;
    }

    echo '<tr>';
    foreach ($row as $i => $val) {
        $style = '';
        if ($i == 1) {
            if (stripos($val, 'mysql') !== false) {
                $style .= '; font-weight: bold';
            } else {
                $style .= '; font-style: italic';
            }
        }
        if ($i == 3) {
            if (floatval($val) > 5.0) {
                $style .= '; color: red';
            } elseif (floatval($val) > 1.0) {
                $style .= '; color: orange';
            } else {
                $style .= '; color: green';
            }
        }
        if ($even) {
            $style .= '; background-color: #EEE';
        } else {
            $style .= '; background-color: #DDD';
        }
        echo '<td style="' . $style . '">' . nl2br(escape_html($val)) . '</td>';
    }
    echo '</tr>';
}

function rows_footer($download, $out)
{
    if ($download) {
        fclose($out);
        return;
    }

    echo '</tbody>';
    echo '</table>';
}

function csv_escape($in)
{
    return str_replace('', '', $in);
}

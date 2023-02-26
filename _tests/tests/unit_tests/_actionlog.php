<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _actionlog_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();

        require_code('actionlog');
        require_code('content');
    }

    public function testNoCrashes()
    {
        if ($this->only !== null) {
            return;
        }

        $hook_obs = find_all_hook_obs('systems', 'actionlog', 'Hook_actionlog_');
        foreach ($hook_obs as $hook => $ob) {
            $handlers = $ob->get_handlers();
            foreach (array_keys($handlers) as $handler) {
                $actionlog_row = [
                    'the_type' => $handler,
                    'param_a' => '12345',
                    'param_b' => '12345',
                ];
                $ob->get_extended_actionlog_data($actionlog_row);
            }
        }
    }

    public function testPageLinks()
    {
        $session_id = $this->establish_admin_callback_session();

        $hook_obs = find_all_hook_obs('systems', 'actionlog', 'Hook_actionlog_');
        foreach ($hook_obs as $hook => $ob) {
            $handlers = $ob->get_handlers();
            foreach ($handlers as $handler => $mappings) {
                if (($handler == 'CHARGE_CUSTOMER') && (strpos(get_db_type(), 'mysql') === false)) {
                    continue;
                }
                if ($handler == 'SET_URL_REDIRECTS') {
                    continue; // Sometimes returns a configuration error
                }
                if ($handler == 'ADD_ZONE' || $handler == 'COMCODE_PAGE_EDIT') {
                    continue; // Points to non-existent zones
                }

                if (($this->only !== null) && ($this->only != $handler)) {
                    continue;
                }

                // Basic checks
                foreach ($mappings['followup_page_links'] as $page_link) {
                    if ((is_string($page_link)) && (strpos($page_link, '{') === false)) {
                        list($zone, $attributes) = page_link_decode($page_link);
                        if (array_key_exists('page', $attributes)) {
                            $found_zone = ($attributes['page'] == DEFAULT_ZONE_PAGE_NAME) ? $zone : get_page_zone($attributes['page'], false);
                            $this->assertTrue($found_zone === $zone, 'Could not find page ' . $attributes['page'] . ' in ' . $page_link); // We want everything searchable
                        }
                    }
                }

                // Real check
                $actionlog_row = [
                    'the_type' => $handler,
                    'param_a' => strval(db_get_first_id() + 1),
                    'param_b' => strval(db_get_first_id() + 1),
                ];
                $mappings_final = $ob->get_extended_actionlog_data($actionlog_row);
                if ($mappings_final !== false) {
                    foreach ($mappings_final['followup_urls'] as $url) {
                        if (is_object($url)) {
                            $url = $url->evaluate();
                        }

                        if (($this->only !== null) && ($this->only != $hook)) {
                            continue;
                        }

                        static $done_urls = [];

                        if (!array_key_exists($url, $done_urls)) {
                            $http_result = cms_http_request($url, ['byte_limit' => 0, 'trigger_error' => false, 'cookies' => [get_session_cookie() => $session_id, 'ignore_http_status' => true]]);
                            $ok = in_array($http_result->message, ['200', '404']);
                            $this->assertTrue($ok, 'Unexpected HTTP response, ' . $http_result->message . ', for ' . $url . ' from ' . $handler);
                            if ($this->debug && !$ok) {
                                var_dump($http_result);
                            }

                            $done_urls[$url] = true;
                        }
                    }
                }
            }
        }
    }

    public function testLangStringReferences()
    {
        if ($this->only !== null) {
            return;
        }

        $hook_obs = find_all_hook_obs('systems', 'actionlog', 'Hook_actionlog_');
        foreach ($hook_obs as $hook => $ob) {
            $handlers = $ob->get_handlers();
            foreach ($handlers as $handler => $mappings) {
                $this->assertTrue(do_lang($handler, null, null, null, null, false) !== null, 'Cannot find: ' . $handler);

                foreach ($mappings['followup_page_links'] as $lang_string => $page_link) {
                    $this->assertTrue(do_lang($lang_string, null, null, null, null, false) !== null, 'Cannot find: ' . $lang_string);
                }
            }
        }
    }

    public function testCMAHookReferences()
    {
        if ($this->only !== null) {
            return;
        }

        $hook_obs = find_all_hook_obs('systems', 'actionlog', 'Hook_actionlog_');
        foreach ($hook_obs as $hook => $ob) {
            $handlers = $ob->get_handlers();
            foreach ($handlers as $handler => $mappings) {
                if ($mappings['cma_hook'] !== null) {
                    $this->assertTrue(get_content_object($mappings['cma_hook']) !== null, $mappings['cma_hook'] . ' not found');
                }
            }
        }
    }

    public function testAllActionsCovered()
    {
        if ($this->only !== null) {
            return;
        }

        // Gather data...

        $handlers = [];
        $hook_obs = find_all_hook_obs('systems', 'actionlog', 'Hook_actionlog_');
        foreach ($hook_obs as $hook => $ob) {
            $handlers += $ob->get_handlers();
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('files');
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN);
        $all_code = '';
        foreach ($files as $f) {
            if (substr($f, -4) == '.php') {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $f);
                $all_code .= $c;
            }
        }

        // Check no missing handlers...

        if (get_forum_type() == 'cns') {
            $matches = [];
            $num_matches = preg_match_all('#log_it\(\'([^\']*)\'#', $all_code, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $action = $matches[1][$i];
                $this->assertTrue(array_key_exists($action, $handlers), 'Could not find actionlog hook handling for ' . $action);
            }
        }

        // Check no missing log_it calls...

        foreach (array_keys($handlers) as $handler) {
            if (in_array($handler, [
                'BAN_MEMBER_AUTOMATIC',
            ])) {
                continue;
            }

            $look_for = 'log_it(\'' . $handler . '\'';
            $this->assertTrue(strpos($all_code, $look_for), 'Could not find log_it call for ' . $handler);
        }
    }
}

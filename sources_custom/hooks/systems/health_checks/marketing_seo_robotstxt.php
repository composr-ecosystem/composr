<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    health_check
 */

/**
 * Hook class.
 */
class Hook_health_check_marketing_seo_robotstxt extends Hook_Health_Check
{
    /*
    Hook covers both SEO and Security issues in robots.txt
    */

    protected $category_label = 'robots.txt';

    /**
     * Standard hook run function to run this category of health checks.
     *
     * @param  ?array $sections_to_run Which check sections to run (null: all)
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     * @return array A pair: category label, list of results
     */
    public function run($sections_to_run, $check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        $this->process_checks_section('testRobotsTxtValidity', 'robots.txt validity', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testRobotsTxtCorrectness', 'robots.txt correctness', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testRobotsTxtCompleteness', 'robots.txt completeness', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testRobotsTxtSitemapLinkage', 'Sitemap linkage', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

        return array($this->category_label, $this->results);
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testRobotsTxtValidity($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $rules = $this->robots_parse(null, true);

        $this->assert_true($rules !== null, '[tt]robots.txt[/tt] not found on domain root or not web accessible');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testRobotsTxtCorrectness($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $rules = $this->robots_parse(null, true);
        if ($rules === null) {
            $this->state_check_skipped('No robots.txt file');
        }

        $url = $this->get_page_url();

        $google_allowed = $this->robots_allowed($url, 'Googlebot', true);
        $other_allowed = $this->robots_allowed($url, 'Googlebot', false); // We'll still check for Google, just with the other way of doing precedence

        if ($check_context == CHECK_CONTEXT__TEST_SITE) {
            if ($this->is_localhost_domain()) {
                return; // Google cannot access anyway
            }

            if ($google_allowed == $other_allowed) {
                $this->assert_true(!$google_allowed, 'Site not blocked by [tt]robots.txt[/tt]');
            } else {
                $this->assert_true(!$google_allowed, 'Site not blocked on Google by [tt]robots.txt[/tt] as per Google\'s way of implementing robots standard');
                $this->assert_true(!$other_allowed, 'Site not blocked on Google by [tt]robots.txt[/tt] as per standard (non-Google) way of implementing robots standard');
            }
        } else {
            if ($google_allowed == $other_allowed) {
                $this->assert_true($google_allowed, 'Site blocked by [tt]robots.txt[/tt]');
            } else {
                $this->assert_true($google_allowed, 'Site blocked on Google by [tt]robots.txt[/tt] as per Google\'s way of implementing robots standard');
                $this->assert_true($other_allowed, 'Site blocked on Google by [tt]robots.txt[/tt] as per standard (non-Google) way of implementing robots standard');
            }
        }

        /*
        This shows how the inconsistency works...

        Standard block:
        User-Agent: *
        Disallow: /
        Allow: /composr
        (Disallow takes precedence due to order of rules)

        Google block:
        User-Agent: *
        Allow: /
        Disallow: /composr
        (Disallow takes precedence due to specificity)

        Consistent block:
        User-Agent: *
        Disallow: /composr
        Allow: /
        (Disallow takes precedence both due due to order of rules and specificity)
        */
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testRobotsTxtCompleteness($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $rules = $this->robots_parse(null, true);
        if ($rules === null) {
            $this->state_check_skipped('No robots.txt file');
        }

        $scripts = array( // Really bad if these get indexed on Google
            'adminzone/',
            'code_editor.php',
            'config_editor.php',
            'data/cron_bridge.php',
            'data/upgrader2.php',
            'install.php',
            'rootkit_detection.php',
            'uninstall.php',
            'upgrader.php',
        );
        foreach ($scripts as $script) {
            $url = get_base_url() . '/' . $script;
            $allowed = $this->robots_allowed($url, 'Googlebot', true);
            $this->assert_true(!$allowed, 'robots.txt should be blocking [tt]' . $script . '[/tt]');
        }
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testRobotsTxtSitemapLinkage($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $rules = $this->robots_parse(null);

        if ($rules === null) {
            $this->state_check_skipped('No robots.txt file');
            return;
        }

        $found = array();
        foreach ($rules as $_rule) {
            list($key, $rule) = $_rule;

            if ($key == 'sitemap') {
                $found[] = $rule;
            }
        }

        $expected_sitemap_url = get_base_url() . '/data_custom/sitemaps/index.xml';

        $ok = false;
        $sitemap_rule = null;
        foreach ($found as $i => $rule) {
            $this->assert_true(strpos($rule, '://') !== false, 'Sitemap URL is relative, should be absolute');
            $ok_here = ($rule == $expected_sitemap_url);

            if ($ok_here) {
                $sitemap_rule = $rule;
            }

            $ok = $ok || $ok_here;
        }
        if ($sitemap_rule !== null) {
            $this->assert_true($ok, 'Sitemap URL is ' . $sitemap_rule . ' but we expected ' . $expected_sitemap_url);
        }

        if ($check_context == CHECK_CONTEXT__TEST_SITE) {
            $this->assert_true($found === array(), 'Sitemap directive found in robots.txt but this is a test site and we should not have one');
        }

        if ($check_context == CHECK_CONTEXT__LIVE_SITE) {
            $this->assert_true($found !== array(), 'No Sitemap directive found in robots.txt');
        }
    }

    /**
     * Find whether the robots.txt allows a URL to be indexed.
     *
     * @param  URLPATH $url The URL
     * @param  string $user_agent The user-agent
     * @param  boolean $google_style Whether to evaluate robots.txt like Google would (Google is slightly non-standard)
     * @return boolean Whether robots access is allowed
     */
    protected function robots_allowed($url, $user_agent, $google_style)
    {
        $rules = $this->robots_parse($user_agent);

        if ($rules === null) {
            return true;
        }

        $url_path = parse_url($url, PHP_URL_PATH);

        $best_precedence = 0;
        $allowed = true;
        foreach ($rules as $_rule) {
            list($key, $rule) = $_rule;

            switch ($key) {
                case 'allow':
                case 'disallow':
                    if ($rule == '') {
                        continue 2; // Ignored rule
                    }

                    if (preg_match('#^' . $rule . '#', $url_path) != 0) {
                        if ($google_style) {
                            if (strlen($rule) > $best_precedence) {
                                $allowed = ($key == 'allow');
                                $best_precedence = strlen($rule);
                            }
                        } else {
                            return ($key == 'allow');
                        }
                    }

                    break;
            }
        }
        return $allowed;
    }

    /**
     * Parse our domain's robots.txt.
     *
     * @param  string $user_agent The user-agent
     * @param  boolean $error_messages Show error messages for any parsing issues
     * @return ?array List of rules (null: could not parse)
     */
    protected function robots_parse($user_agent, $error_messages = false)
    {
        // The best specification is by Google now:
        //  https://developers.google.com/search/reference/robots_txt

        $base_url = get_base_url();
        $base_url_path = parse_url($base_url, PHP_URL_PATH);
        $robots_url = preg_replace('#' . preg_quote($base_url_path, '#') . '$#', '', $base_url) . '/robots.txt';

        $agents_regexp = preg_quote('*');
        if ($user_agent !== null) {
            $agents_regexp .= '|' . preg_quote($user_agent, '#');
        }

        $contents = http_download_file($robots_url, null, false);
        if ($contents === null) {
            return null;
        }
        $robots_lines = explode("\n", $contents);

        // Go through lines
        $rules = array();
        $following_rules_apply = false;
        $best_following_rules_apply = 0;
        $just_did_ua_line = false;
        $did_some_ua_line = false;
        foreach ($robots_lines as $line) {
            $line = trim($line);

            // Skip blank lines
            if ($line == '') {
                continue;
            }

            // Skip comment lines
            if ($line[0] == '#') {
                continue;
            }

            // The following rules only apply if the User-Agent matches
            $matches = array();
            if (preg_match('#^User-Agent:(.*)#i', $line, $matches) != 0) {
                $agent_spec = $matches[1];
                $_following_rules_apply = (preg_match('#(' . $agents_regexp . ')#i', $agent_spec) != 0); // It's a bit weird how "googlebot-xxx" would match but "google" would not, but that's the standard (and there's justification when you think about it)
                if ($_following_rules_apply) {
                    if (strlen($agent_spec) >= $best_following_rules_apply) {
                        $following_rules_apply = true;
                        $best_following_rules_apply = strlen($agent_spec);
                        $rules = array(); // Reset rules, as now this is the best scoring rules section (we don't merge sections)
                    }
                } elseif (!$just_did_ua_line) {
                    $following_rules_apply = false;
                }

                $just_did_ua_line = true;
                $did_some_ua_line = true;

                continue;
            }

            // Record rules
            if (preg_match('#^([\w-]+):\s*(.*)\s*$#i', $line, $matches) != 0) {
                $key = strtolower($matches[1]);
                $value = trim($matches[2]);

                $core_rule = ($key == 'allow') || ($key == 'disallow');

                if ($error_messages) {
                    $this->assert_true(in_array($key, array('allow', 'disallow', 'sitemap', 'crawl-delay')), 'Unrecognised [tt]robots.txt[/tt] rule:' . $key);

                    if ($core_rule) {
                        $this->assert_true($did_some_ua_line, 'Floating [tt]' . ucwords($key) . '[/tt] outside of any User-Agent section of [tt]robots.txt[/tt]');
                    }
                }

                if ($following_rules_apply) {
                    // Add rules that apply to array for testing
                    if ($core_rule) {
                        $rule = addcslashes($value, '#\+?^[](){}|-'); // Escape things that are in regexps but should be literal here
                        $rule = str_replace('*', '.*', $rule); // * wild cards are ".*" in a regexp
                        // "$" remains unchanged

                        $rules[] = array($key, $rule);
                    } else {
                        $rules[] = array($key, $value);
                    }
                }

                $just_did_ua_line = false;

                continue;
            }

            // Unrecognised line
            if ($error_messages) {
                $this->assert_true(false, 'Unrecognised line in [tt]robots.txt[/tt]:' . $line);
            }
        }

        return $rules;
    }
}

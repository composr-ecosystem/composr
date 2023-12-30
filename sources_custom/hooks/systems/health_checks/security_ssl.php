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
class Hook_health_check_security_ssl extends Hook_Health_Check
{
    protected $category_label = 'SSL';

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
        $this->process_checks_section('testManualSSLGrading', 'SSL grading', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testIncorrectHTTPSEmbedding', 'Insecure embedding', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testIncorrectHTTPSLinking', 'Insecure linking', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testSSLCorrectness', 'SSL correctness', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testManualSSLGrading($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!$manual_checks) {
            return;
        }

        $this->state_check_manual('Check for [url="SSL security issues"]https://www.ssllabs.com/ssltest/[/url] (take warnings with a pinch of salt, not every suggestion is appropriate)');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testIncorrectHTTPSEmbedding($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $page_links = $this->process_urls_into_page_links();

        foreach ($page_links as $page_link) {
            $url = page_link_to_url($page_link);
            $protocol = parse_url($url, PHP_URL_SCHEME);
            if (($protocol === null) || ($protocol == 'http')) {
                continue;
            }

            $data = $this->get_page_content($page_link);
            if ($data === null) {
                $this->state_check_skipped('Could not download page from website');

                continue;
            }

            $urls = $this->get_embed_urls_from_data($data);

            foreach ($urls as $url) {
                // Check
                $this->assert_true(preg_match('#^http://#', $url) == 0, 'Embedding HTTP resources on HTTPS page: [url="' . $url . '"]' . $url . '[/url] (on "' . $page_link . '")');
            }
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
    public function testIncorrectHTTPSLinking($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        global $SITE_INFO;

        if (empty($SITE_INFO['base_url'])) {
            $this->state_check_skipped('Base URL not configured');
            return;
        }

        $protocol = parse_url($SITE_INFO['base_url'], PHP_URL_SCHEME);
        if (($protocol === null) || ($protocol == 'http')) {
            return;
        }

        $domains = $this->get_domains();

        $page_links = $this->process_urls_into_page_links();

        foreach ($page_links as $page_link) {
            $data = $this->get_page_content($page_link);
            if ($data === null) {
                $this->state_check_skipped('Could not download page from website');

                continue;
            }

            $urls = $this->get_link_urls_from_data($data, false);

            foreach ($urls as $url) {
                // Check
                $regexp = '#^http://(';
                foreach ($domains as $i => $domain) {
                    if ($i != 0) {
                        $regexp .= '|';
                    }
                    $regexp .= preg_quote($domain, '#');
                }
                $regexp .= ')[:/]#';
                $this->assert_true(preg_match($regexp, $url) == 0, 'Linking to a local HTTP page on all-HTTPS site: [url="' . $url . '"]' . $url . '[/url] (on "' . $page_link . '")');
            }
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
    public function testSSLCorrectness($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        if ((addon_installed('ssl')) || (substr(get_base_url(), 0, 7) == 'https://')) {
            // If it's a problem with SSL verification in general
            for ($i = 0; $i < 3; $i++) { // Try a few times in case of some temporary network issue or Google issue
                $data = http_download_file('https://www.google.com/', null, false);

                $ok = (($data !== null) && (strpos($data, '<html') !== false));
                if ($ok) {
                    break;
                }
                if (php_function_allowed('usleep')) {
                    usleep(5000000);
                }
            }
            $this->assert_true($ok, 'Problem downloading HTTP requests by SSL');

            if ($ok) {
                // If it's a problem with SSL verification on our domain specifically
                $domains = $this->get_domains();
                foreach ($domains as $domain) {
                    if (get_value('disable_ssl_for__' . $domain) !== '1') {
                        $test_url = get_base_url(true) . '/uploads/index.html';

                        delete_value('disable_ssl_for__' . $domain);
                        $data = http_download_file($test_url, null, false);
                        $ok1 = (($data !== null) && (strpos($data, '<html') !== false));

                        $msg = 'Problem detected with the [tt]' . $domain . '[/tt] SSL certificate';
                        if (!$ok1) {
                            set_value('disable_ssl_for__' . $domain, '1');
                            $data = http_download_file($test_url, null, false);
                            $ok2 = (($data !== null) && (strpos($data, '<html') !== false));

                            $this->assert_true(!$ok2, $msg); // Issue with our SSL but not if verify is disabled, suggesting the problem is with verify

                            delete_value('disable_ssl_for__' . $domain);
                        } else {
                            $this->assert_true(true, $msg); // No issue with our SSL
                        }
                    }
                }
            }
        }
    }
}

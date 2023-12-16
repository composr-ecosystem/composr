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
class Hook_health_check_security extends Hook_Health_Check
{
    protected $category_label = 'Security';

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
        $this->process_checks_section('testMalware', 'Malware', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testDirectorySecuring', 'Directory securing', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testSiteOrphaned', 'Site orphaning', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testMalware($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        // API https://developers.google.com/safe-browsing/v4/

        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            // Google can't index a non-live site, and thus won't report Safe Browsing results
            return;
        }

        if ($use_test_data_for_pass === null) {
            $key = get_option('hc_google_safe_browsing_api_key');
        } else {
            $key = 'AIzaSyBJyvgYzg-moqMRBZwhiivNxhYvafqMWas';
        }
        if ($key == '') {
            $this->state_check_skipped('Google Safe Browsing API key not configured');
            return;
        }

        require_code('json');

        if ($use_test_data_for_pass === null) {
            if ($this->is_localhost_domain()) {
                $this->state_check_skipped('Could not scan for Malware on local domain');
                return;
            }

            $page_links = $this->process_urls_into_page_links();

            $urls = array();
            foreach ($page_links as $page_link) {
                $_url = page_link_to_url($page_link);
                if (!empty($_url)) {
                    $urls[] = array('url' => $_url);
                }
            }
        } else {
            if ($use_test_data_for_pass) {
                $urls = array(array('url' => 'http://example.com'));
            } else {
                $urls = array(array('url' => 'http://www23.omrtw.com'));
            }
        }

        $url = 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=' . urlencode(trim($key));

        require_code('version2');
        $data = array(
            'client' => array(
                'clientId' => 'Composr',
                'clientVersion' => get_version_dotted(),
            ),
            'threatInfo' => array(
                'threatTypes' => array('MALWARE', 'SOCIAL_ENGINEERING'),
                'platformTypes' => array('ANY_PLATFORM'),
                'threatEntryTypes' => array('URL'),
                'threatEntries' => $urls,
            ),
        );

        for ($i = 0; $i < 3; $i++) { // Try a few times in case of some temporary network issue or Google issue
            $_result = http_download_file($url, null, false, false, 'Composr', array(json_encode($data)), null, null, null, null, null, null, null, 200.0, true, null, null, null, 'application/json');

            if ($_result !== null) {
                break;
            }
            if (php_function_allowed('usleep')) {
                usleep(5000000);
            }
        }

        $this->assert_true(!in_array($GLOBALS['HTTP_MESSAGE'], array('401', '403')), 'Error with our Google Safe Browsing API key (' . $GLOBALS['HTTP_MESSAGE'] . ')');
        $this->assert_true(!in_array($GLOBALS['HTTP_MESSAGE'], array('400', '501', '503', '504')), 'Internal error with our Google Safe Browsing check (' . $GLOBALS['HTTP_MESSAGE'] . '); only works on pages that have been indexed by Google');

        $ok = in_array($GLOBALS['HTTP_MESSAGE'], array('200'));
        if ($ok) {
            $result = json_decode($_result, true);

            if (empty($result['matches'])) {
                $this->assert_true(true, 'Malware advisory provided by [url="Google"]https://developers.google.com/safe-browsing/v3/advisory[/url]');
            } else {
                foreach ($result['matches'] as $match) {
                    $this->assert_true(false, 'Malware advisory provided by [url="Google"]https://developers.google.com/safe-browsing/v3/advisory[/url], ' . json_encode($match));
                }
            }
        } else {
            $this->state_check_skipped('Failed calling Google Safe Browsing API');
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
    public function testDirectorySecuring($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $to_check = array(
            'data_custom/ecommerce.log',
            'caches/test.txt',
            'safe_mode_temp/test.txt',
        );
        foreach ($to_check as $c) {
            $full_path = get_custom_file_base() . '/' . $c;
            $exists = is_file($full_path);
            if (!$exists) {
                cms_file_put_contents_safe($full_path, '');
            }
            http_download_file(get_custom_base_url() . '/' . $c, null, false);
            $this->assert_true($GLOBALS['HTTP_MESSAGE'] == '403' || $GLOBALS['HTTP_MESSAGE'] == '404', 'Should not be able to download [tt]' . $c . '[/tt], should be secured by some kind of server configuration');
            if (!$exists) {
                @unlink($full_path);
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
    public function testSiteOrphaned($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        $path = 'uploads/website_specific/orphaned-test.txt';
        require_code('crypt');
        $data = get_rand_password();
        cms_file_put_contents_safe(get_custom_file_base() . '/' . $path, $data);
        $result = http_download_file(get_custom_base_url() . '/' . $path, null, false);
        $this->assert_true($result === $data, 'Website does not seem to be running on the base URL that is configured');
        @unlink(get_custom_file_base() . '/' . $path);

        if (!$this->is_localhost_domain()) {
            if (php_function_allowed('shell_exec')) {
                $domains = $this->get_domains();

                foreach ($domains as $domain) {
                    $regexp = '#\nName:\s+' . $domain . '\nAddress:\s+(.*)\n#';

                    $matches_local = array();
                    $dns_lookup_local = shell_exec('nslookup ' . $domain);
                    if (!is_string($dns_lookup_local)) {
                        $dns_lookup_local = '';
                    }
                    $matched_local = preg_match($regexp, $dns_lookup_local, $matches_local);
                    $matches_remote = array();
                    $dns_lookup_remote = shell_exec('nslookup ' . $domain . ' 8.8.8.8');
                    if (!is_string($dns_lookup_remote)) {
                        $dns_lookup_remote = '';
                    }
                    $matched_remote = preg_match($regexp, $dns_lookup_remote, $matches_remote);
                    if (($matched_local != 0) && ($matched_remote != 0)) {
                        $this->assert_true($matches_local[1] == $matches_remote[1], 'DNS lookup for our domain seems to be looking up differently ([tt]' . $matches_local[1] . '[/tt] vs [tt]' . $matches_remote[1] . '[/tt])');
                    } else {
                        $this->state_check_skipped('Failed to get a recognisable DNS resolution via the command line for [tt]' . $domain . '[/tt]');
                    }
                }
            } else {
                $this->state_check_skipped('PHP shell_exec function not available');
            }
        }
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class Hook_health_check_mistakes_user_ux extends Hook_Health_Check
{
    protected $category_label = 'User-experience for mistakes';

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
        $this->process_checks_section('test404Pages', '404 pages', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testWWWRedirection', 'www redirection', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testHTTPSRedirection', 'HTTPS redirection', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function test404Pages($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        /*  Currently can't work
        $url = get_base_url() . '/testing-for-404.html';
        $data = http_download_file($url, null, false); // In v11 set the parameter to return output even for 404
        $this->assert_true(($data === null) || (strpos($data, '<link') !== false) || (strpos($data, '<a ') !== false), '[tt]404[/tt] status page is too basic looking, probably not helpful, suggest to display a sitemap');
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
    public function testWWWRedirection($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        $domains = $this->get_domains(false);

        foreach ($domains as $zone => $domain) {
            if ($domain == 'localhost') {
                continue;
            }

            $parts = explode('.', $domain);

            if (substr($parts[0], 0, 3) == 'www') {
                array_shift($parts);
                $wrong_domain = implode('.', $parts);
            } else {
                $wrong_domain = 'www.' . $domain;
            }

            $lookup = @gethostbyname($wrong_domain);
            $ok = ($lookup != $wrong_domain);
            $this->assert_true($ok, 'Could not lookup [tt]' . $wrong_domain . '[/tt], should exist for it to redirect from [tt]' . $domain . '[/tt]');
            if (!$ok) {
                return;
            }

            //$url = preg_replace('#(://.*)/.*$#U', '$1/uploads/index.html', $this->get_page_url(':'));
            if ($zone == '') {
                $url = $this->get_page_url($zone . ':privacy');
            } else {
                $url = $this->get_page_url($zone . ':');
            }
            $wrong_url = str_replace('://' . $domain, '://' . $wrong_domain, $url);

            global $HTTP_DOWNLOAD_URL, $HTTP_MESSAGE;

            http_download_file($wrong_url, null, false);
            $redirected = ($HTTP_DOWNLOAD_URL != $wrong_url);
            $this->assert_true($redirected, 'Domain [tt]' . $wrong_domain . '[/tt] is not redirecting to [tt]' . $domain . '[/tt]');

            if ($redirected) {
                $ok = ($HTTP_DOWNLOAD_URL == $url);
                $this->assert_true($ok, 'Domain [tt]' . $wrong_domain . '[/tt] is not redirecting to deep URLs of [tt]' . $domain . '[/tt]');

                http_download_file($wrong_url, null, false, true);
                $ok = ($HTTP_MESSAGE == '301');
                $this->assert_true($ok, 'Domain [tt]' . $wrong_domain . '[/tt] is not redirecting to [tt]' . $domain . '[/tt] with a [tt]301[/tt] code ([tt]' . $HTTP_MESSAGE . '[/tt] code used)');
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
    public function testHTTPSRedirection($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context != CHECK_CONTEXT__LIVE_SITE) {
            return;
        }

        global $HTTP_DOWNLOAD_URL, $HTTP_MESSAGE, $SITE_INFO;

        if (empty($SITE_INFO['base_url'])) {
            return;
        }

        if (strpos(get_option('ip_forwarding'), '://') !== false) {
            return; // Will mess up protocol
        }

        $protocol = parse_url($SITE_INFO['base_url'], PHP_URL_SCHEME);

        if ($protocol == 'http') {
            return;
        }

        $wrong_protocol = 'http';

        $url = $this->get_page_url(':privacy');
        $wrong_url = str_replace($protocol . '://', $wrong_protocol . '://', $url);

        http_download_file($wrong_url, null, false);
        $redirected = ($HTTP_DOWNLOAD_URL != $wrong_url);
        $this->assert_true($redirected, 'Protocol [tt]' . $wrong_protocol . '[/tt] is not redirecting to [tt]' . $protocol . '[/tt] protocol');

        if ($redirected) {
            $ok = ($HTTP_DOWNLOAD_URL == $url);
            $this->assert_true($ok, 'Protocol [tt]' . $wrong_protocol . '[/tt] is not redirecting to deep URLs of [tt]' . $protocol . '[/tt] protocol');

            http_download_file($wrong_url, null, false, true);
            $ok = ($HTTP_MESSAGE == '301');
            $this->assert_true($ok, 'Protocol [tt]' . $wrong_protocol . '[/tt] is not redirecting to [tt]' . $protocol . '[/tt] protocol with a [tt]301[/tt] code ([tt]' . $HTTP_MESSAGE . '[/tt] code used)');
        }
    }
}

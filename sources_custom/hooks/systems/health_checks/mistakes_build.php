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
class Hook_health_check_mistakes_build extends Hook_Health_Check
{
    protected $category_label = 'Build mistakes';

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
        $this->process_checks_section('testManualWebStandards', 'Manual checks for web standards', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testGuestAccess', 'Guest access', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testBrokenLinks', 'Broken links (slow)', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testIncompleteContent', 'Incomplete content', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testLocalLinking', 'Local linking', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testManualWebStandards($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!$manual_checks) {
            return;
        }

        $this->state_check_manual('Check [url="HTML5 validation"]https://validator.w3.org/[/url] (take warnings with a pinch of salt, not every suggestion is appropriate)');
        $this->state_check_manual('Check [url="CSS validation"]https://jigsaw.w3.org/css-validator/[/url] (take warnings with a pinch of salt, not every suggestion is appropriate)');
        $this->state_check_manual('Check [url="WCAG validation"]https://achecker.achecks.ca/checker/index.php[/url] (take warnings with a pinch of salt, not every suggestion is appropriate)');

        $this->state_check_manual('Check [url="schema.org/microformats validation"]https://developers.google.com/search/docs/advanced/structured-data[/url] on any key pages you want to be semantic');
        $this->state_check_manual('Check [url="OpenGraph metadata"]https://developers.facebook.com/tools/debug/[/url] on any key pages you expect to be shared');

        $this->state_check_manual('Do a [url="general check"]https://www.woorank.com/[/url] (take warnings with a pinch of salt, not every suggestion is appropriate)');
        $this->state_check_manual('Do a [url="general check"]https://website.grader.com/[/url] (take warnings with a pinch of salt, not every suggestion is appropriate)');

        $this->state_check_manual('Test in Firefox');
        $this->state_check_manual('Test in Google Chrome');
        $this->state_check_manual('Test in IE10');
        $this->state_check_manual('Test in IE11');
        $this->state_check_manual('Test in Microsoft Edge');
        $this->state_check_manual('Test in Safari');
        $this->state_check_manual('Test in Google Chrome (mobile)');
        $this->state_check_manual('Test in Safari (mobile)');

        $this->state_check_manual('Check the website would look good if printed');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testGuestAccess($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $page_links = $this->process_urls_into_page_links();

        foreach ($page_links as $page_link) {
            $data = $this->get_page_content($page_link);

            $this->assert_true(!in_array($GLOBALS['HTTP_MESSAGE'], array('401', '403')), '"' . $page_link . '" page is not allowing guest access');
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
    public function testBrokenLinks($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $page_links = $this->process_urls_into_page_links();

        $urls = array();
        foreach ($page_links as $page_link) {
            $data = $this->get_page_content($page_link);
            if ($data === null) {
                $this->state_check_skipped('Could not download page from website');

                continue;
            }

            $urls = array_merge($urls, $this->get_embed_urls_from_data($data));
            $urls = array_merge($urls, $this->get_link_urls_from_data($data));
        }
        $urls = array_unique($urls);

        $_urls = array();
        foreach ($urls as $url) {
            if (substr($url, 0, 2) == '//') {
                $url = 'http:' . $url;
            }

            // Don't check local URLs, we're interested in broken remote links (local validation is too much)
            if (substr($url, 0, strlen(get_base_url(false)) + 1) == get_base_url(false) . '/') {
                continue;
            }
            if (substr($url, 0, strlen(get_base_url(true)) + 1) == get_base_url(true) . '/') {
                continue;
            }
            if (strpos($url, '://') === false) {
                continue;
            }

            $_urls[] = $url;
        }

        foreach ($_urls as $url) {
            // Check
            $exists = check_url_exists($url, 60 * 60 * 24 * 1);
            if (!$exists) {
                $exists = check_url_exists($url, 0); // Re-try without caching, maybe we fixed a scanner bug or it's erratic
            }
            $this->assert_true($exists, 'Broken link: [tt]' . $url . '[/tt]');
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
    public function testIncompleteContent($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!$manual_checks) {
            $this->state_check_skipped('Will not check automatically because there could be false positives');
            return;
        }

        $page_links = $this->process_urls_into_page_links();

        foreach ($page_links as $page_link) {
            $data = $this->get_page_content($page_link);

            if ($data === null) {
                $this->state_check_skipped('Could not download page from website');
                return;
            }

            $check_for = array('TODO', 'FIXME', 'Lorem Ipsum');
            foreach ($check_for as $c) {
                $this->assert_true(strpos($data, $c) === false, 'Found a suspicious "' . $c . '" on "' . $page_link . '" page');
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
    public function testLocalLinking($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if ($this->is_localhost_domain()) {
            return;
        }

        if (!$manual_checks) {
            $this->state_check_skipped('Will not check automatically because we do not know intent, a live site could be pointing to an Intranet');
            return;
        }

        $page_links = $this->process_urls_into_page_links();

        foreach ($page_links as $page_link) {
            $data = $this->get_page_content($page_link);

            if ($data === null) {
                $this->state_check_skipped('Could not download page from website');
                return;
            }

            $c = '#https?://(localhost|127\.|192\.168\.|10\.)#';
            $this->assert_true(preg_match($c, $data) == 0, 'Found links to a local URL on "' . $page_link . '" page');
        }
    }
}

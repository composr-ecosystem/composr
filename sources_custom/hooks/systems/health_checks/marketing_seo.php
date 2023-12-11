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
class Hook_health_check_marketing_seo extends Hook_Health_Check
{
    protected $category_label = 'SEO';

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
        $this->process_checks_section('testManualSEO', 'Manual SEO checks', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testMetaDescription', 'Meta description', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testMetaKeywords', 'Meta keywords', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testPageTitle', 'Page titles', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testH1Tags', 'H1 tags', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);
        $this->process_checks_section('testXMLSitemap', 'XML Sitemap', $sections_to_run, $check_context, $manual_checks, $automatic_repair, $use_test_data_for_pass);

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
    public function testManualSEO($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!$manual_checks) {
            return;
        }

        $this->state_check_manual('Check for [url="SEO issues"]https://seositecheckup.com/[/url] (take warnings with a pinch of salt, not every suggestion is appropriate)');
        $this->state_check_manual('Check for [url="search issues in Google Search Console"]https://search.google.com/search-console/welcome[/url]');
        $this->state_check_manual('Analyse what [url="popular keywords you rank for"]https://www.thehoth.com/[/url] and [url="what you could try and rank for"]https://www.authoritylabs.com/ranking-tool/[/url]');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testMetaDescription($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $data = $this->get_page_content();
        if ($data === null) {
            $this->state_check_skipped('Could not download page from website');
            return;
        }

        $meta_description = null;
        $matches = array();
        if (preg_match('#<meta\s+[^<>]*name="description"[^<>]*content="([^"]*)"#is', $data, $matches) != 0) {
            $meta_description = html_entity_decode($matches[1], ENT_QUOTES);
        } elseif (preg_match('#<meta\s+[^<>]*content="([^"]*)"[^<>]*name="description"#is', $data, $matches) != 0) {
            $meta_description = html_entity_decode($matches[1], ENT_QUOTES);
        }

        $ok = ($meta_description !== null);
        $this->assert_true($ok, 'Could not find a meta description');
        if ($ok) {
            $len = strlen($meta_description);
            $min_threshold = 40;
            $max_threshold = 155;
            $this->assert_true($len >= $min_threshold, 'Meta description length is under ' . strval($min_threshold) . ' @ ' . strval(integer_format($len)) . ' characters');
            $this->assert_true($len <= $max_threshold, 'Meta description length is over ' . strval($max_threshold) . ' @ ' . strval(integer_format($len)) . ' characters');
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
    public function testMetaKeywords($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $data = $this->get_page_content();
        if ($data === null) {
            $this->state_check_skipped('Could not download page from website');
            return;
        }

        $meta_keywords = null;
        $matches = array();
        if (preg_match('#<meta\s+[^<>]*name="keywords"[^<>]*content="([^"]*)"#is', $data, $matches) != 0) {
            $meta_keywords = array_map('trim', explode(',', $matches[1]));
        } elseif (preg_match('#<meta\s+[^<>]*content="([^"]*)"[^<>]*name="keywords"#is', $data, $matches) != 0) {
            $meta_keywords = array_map('trim', explode(',', $matches[1]));
        }

        $ok = ($meta_keywords !== null);
        $this->assert_true($ok, 'Could not find any meta keywords');
        if ($ok) {
            $count = count($meta_keywords);
            $min_threshold = 4;
            $max_threshold = 20;
            $this->assert_true($count >= $min_threshold, 'Meta keyword count is under ' . strval($min_threshold) . ' @ ' . strval(integer_format($count)));
            $this->assert_true($count <= $max_threshold, 'Meta keyword count is over ' . strval($max_threshold) . ' @ ' . strval(integer_format($count)));
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
    public function testPageTitle($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $data = $this->get_page_content();
        if ($data === null) {
            $this->state_check_skipped('Could not download page from website');
            return;
        }

        $title = null;
        $matches = array();
        if (preg_match('#<title[^<>]*>([^<>]*)</title>#is', $data, $matches) != 0) {
            $title = html_entity_decode($matches[1], ENT_QUOTES);
        }

        $ok = ($title !== null);
        $this->assert_true($ok, 'Could not find any <title>');
        if ($ok) {
            $len = strlen($title);
            $min_threshold = 4;
            $max_threshold = 70;
            $this->assert_true($len >= $min_threshold, '<title> length is under ' . strval($min_threshold) . ' @ ' . strval(integer_format($len)));
            $this->assert_true($len <= $max_threshold, '<title> length is over ' . strval($max_threshold) . ' @ ' . strval(integer_format($len)));
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
    public function testH1Tags($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        $data = $this->get_page_content();
        if ($data === null) {
            $this->state_check_skipped('Could not download page from website');
            return;
        }

        $header = null;
        $matches = array();
        if (preg_match('#<h1[^<>]*>(.*)</h1>#is', $data, $matches) != 0) {
            $header = $matches[1];
        }

        $ok = ($header !== null);
        $this->assert_true($ok, 'Could not find any <h1>');
    }

    /**
     * Run a section of health checks.
     *
     * @param  integer $check_context The current state of the website (a CHECK_CONTEXT__* constant)
     * @param  boolean $manual_checks Mention manual checks
     * @param  boolean $automatic_repair Do automatic repairs where possible
     * @param  ?boolean $use_test_data_for_pass Should test data be for a pass [if test data supported] (null: no test data)
     */
    public function testXMLSitemap($check_context, $manual_checks = false, $automatic_repair = false, $use_test_data_for_pass = null)
    {
        if ($check_context == CHECK_CONTEXT__INSTALL) {
            return;
        }

        if (!cron_installed()) {
            $this->state_check_skipped('CRON not running');
            return;
        }

        $path = get_custom_file_base() . '/data_custom/sitemaps';

        $last_updated_file = @filemtime($path . '/index.xml');
        $ok = ($last_updated_file !== false);
        $this->assert_true($ok, 'XML Sitemap does not seem to be building');

        if ($ok) {
            $last_updated_file = 0;
            $dh = opendir($path);
            while (($f = readdir($dh)) !== false) {
                if (preg_match('#^set_\d+\.xml$#', $f) != 0) {
                    $last_updated_file = max($last_updated_file, filemtime($path . '/' . $f));
                }
            }
            closedir($dh);
            $last_updated = $GLOBALS['SITE_DB']->query_select_value_if_there('sitemap_cache', 'MAX(last_updated)', array(), ' AND last_updated<' . strval(time() - 60 * 60 * 25));
            if ($last_updated !== null) {
                $this->assert_true($last_updated_file > $last_updated - 60 * 60 * 24, 'XML Sitemap does not seem to be updating');
            } else {
                $this->state_check_skipped('Nothing queued to go into the XML Sitemap old enough to know it should be there');
            }
        }
    }
}

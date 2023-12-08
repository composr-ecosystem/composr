<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _performance_test_set extends cms_test_case
{
    private $log_file;
    private $log_warnings_file;

    private $page_links = array();
    private $page_links_warnings = array();

    // Config
    private $quick = true; // Times will be less accurate if they're fast enough, focus on finding slow pages only
    private $threshold = 0.50; // If loading times exceed this a page is considered slow
    private $start_page_link = '';
    private $whitelist = null;
    private $blacklist = array(
        // These are non-bundled tooling screens that are irrevocably slow
        'adminzone:string_scan',
        'adminzone:sql_dump',
        'adminzone:tar_dump',
        'adminzone:admin_generate_bookmarks',
        'adminzone:build_addons',
        'adminzone:css_check',
        'adminzone:doc_index_build',
        'adminzone:plug_guid',
        'adminzone:static_export',

        // Irrevocably slow for some other reason
        'adminzone:admin_addons:addon_export', // Does full file-system scan, particularly slow on a full git clone
    );
    private $whitelist_zone = null;

    public function setUp()
    {
        parent::setUp();

        $this->establish_admin_session();

        $this->log_file = fopen(get_file_base() . '/data_custom/performance.log', 'wb');
        flock($this->log_file, LOCK_EX);
        $this->log_warnings_file = fopen(get_file_base() . '/data_custom/performance_warnings.log', 'wb');
        flock($this->log_warnings_file, LOCK_EX);
    }

    public function testSitemapNodes()
    {
        require_code('sitemap');
        retrieve_sitemap_node($this->start_page_link, array($this, '_test_screen_performance'), null, null, null, SITEMAP_GEN_CHECK_PERMS);
    }

    public function _test_screen_performance($node)
    {
        @set_time_limit(0);

        $page_link = $node['page_link'];

        if ($this->whitelist !== null && !in_array($page_link, $this->whitelist)) {
            return;
        }

        if ($this->blacklist !== null && in_array($page_link, $this->blacklist)) {
            return;
        }

        if ($this->whitelist_zone !== null) {
            list($zone) = page_link_decode($page_link);
            if ($zone != $this->whitelist_zone) {
                return;
            }
        }

        $url = page_link_to_url($page_link);

        $times = array();
        for ($i = 0; $i < 3; $i++) { // We can do it multiple times so that caches are primed for final time
            $before = microtime(true);
            $result = http_download_file($url, null, false/*we're not looking for errors - we may get some under normal conditions, e.g. for site:authors which is 404 until you add your profile*/, false, 'Composr', null, array(get_session_cookie() => get_session_id()), null, null, null, null, null, null, 60.0);
            $after = microtime(true);
            $time = $after - $before;
            $times[] = $time;

            if ($time < $this->threshold && $this->quick) {
                break;
            }
        }

        sort($times);
        $time = $times[0];

        $slow = ($time > $this->threshold);
        $this->page_links[$page_link] = $time;
        if ($slow) {
            $this->page_links_warnings[$page_link] = $time;
        }
        $this->assertTrue(!$slow, 'Too slow on ' . $page_link . ' (' . float_format($time) . ' seconds)');

        $message = $page_link . ' (' . $url . '): ' . float_format($time) . ' seconds';
        fwrite($this->log_file, $message . "\n");
        if ($slow) {
            fwrite($this->log_warnings_file, $message . "\n");
        }
    }

    public function tearDown()
    {
        flock($this->log_file, LOCK_UN);
        flock($this->log_warnings_file, LOCK_UN);
        fclose($this->log_file);
        fclose($this->log_warnings_file);

        parent::tearDown();
    }
}

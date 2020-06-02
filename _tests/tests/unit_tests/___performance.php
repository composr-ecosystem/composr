<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// php _tests/index.php ___performance

/**
 * Composr test case class (unit testing).
 */
class ___performance_test_set extends cms_test_case
{
    protected $log_file;
    protected $log_warnings_file;

    protected $page_links = [];
    protected $page_links_warnings = [];

    // Config
    protected $quick = true; // Times will be less accurate if they're fast enough, focus on finding slow pages only
    protected $threshold = 0.50; // If loading times exceed this a page is considered slow
    protected $start_page_link = '';
    protected $whitelist = null;
    protected $blacklist = [
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
    ];
    protected $whitelist_zone = null;

    public function setUp()
    {
        parent::setUp();

        $this->establish_admin_session();

        require_code('files');
        $this->log_file = cms_fopen_text_write(get_custom_file_base() . '/data_custom/performance.log', true);
        $this->log_warnings_file = cms_fopen_text_write(get_custom_file_base() . '/data_custom/performance_warnings.log', true);
    }

    public function testSitemapNodes()
    {
        require_code('sitemap');
        retrieve_sitemap_node($this->start_page_link, [$this, '_test_screen_performance'], null, null, null, SITEMAP_GEN_CHECK_PERMS);
    }

    public function _test_screen_performance($node)
    {
        cms_disable_time_limit();

        $session_id = $this->establish_admin_callback_session();

        $page_link = $node['page_link'];

        if (($this->whitelist !== null) && (!in_array($page_link, $this->whitelist))) {
            return;
        }

        if (($this->blacklist !== null) && (in_array($page_link, $this->blacklist))) {
            return;
        }

        if ($this->whitelist_zone !== null) {
            list($zone) = page_link_decode($page_link);
            if ($zone != $this->whitelist_zone) {
                return;
            }
        }

        $url = page_link_to_url($page_link);

        $times = [];
        for ($i = 0; $i < 3; $i++) { // We can do it multiple times so that caches are primed for final time
            $before = microtime(true);
            $result = http_get_contents($url, ['trigger_error' => false/*we're not looking for errors - we may get some under normal conditions, e.g. for site:authors which is 404 until you add your profile*/, 'timeout' => 60.0, 'cookies' => [get_session_cookie() => $session_id]]);
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

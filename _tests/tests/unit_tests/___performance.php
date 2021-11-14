<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

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
    protected $page_links = [];
    protected $page_links_warnings = [];

    // Config
    protected $quick = true; // Times will be less accurate if they're fast enough, focus on finding slow pages only
    protected $threshold = 0.50; // If loading times exceed this a page is considered slow
    protected $start_page_link = '';
    protected $inclusion_list = null;
    protected $exclusion_list = [
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
        'adminzone:admin_addons:addon_export', // Does full file-system scan, particularly slow on a full Git clone
    ];
    protected $only_zone = null;

    public function setUp()
    {
        parent::setUp();

        $this->establish_admin_session();
    }

    public function testSitemapNodes()
    {
        require_code('sitemap');
        retrieve_sitemap_node($this->start_page_link, [$this, '_test_screen_performance'], null, null, null, SITEMAP_GEN_CHECK_PERMS);
    }

    public function _test_screen_performance($node)
    {
        $old_limit = cms_set_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $session_id = $this->establish_admin_callback_session();

        $page_link = $node['page_link'];

        if (($this->inclusion_list !== null) && (!in_array($page_link, $this->inclusion_list))) {
            return;
        }

        if (($this->exclusion_list !== null) && (in_array($page_link, $this->exclusion_list))) {
            return;
        }

        if ($this->only_zone !== null) {
            list($zone) = page_link_decode($page_link);
            if ($zone != $this->only_zone) {
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
        CMSLoggers::performance()->info($message);
        if ($slow) {
            CMSLoggers::performance_warnings()->info($message);
        }

        cms_set_time_limit($old_limit);
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}

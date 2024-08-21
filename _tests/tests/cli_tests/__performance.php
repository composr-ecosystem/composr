<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/*

Execute on the command line to prevent timeouts:
php _tests/index.php cli_tests/__performance

Can track progress with:
SELECT * FROM cms11_stats ORDER BY date_and_time DESC LIMIT 1;
(if using the command line, progress will be returned)

*/

/**
 * Composr test case class (unit testing).
 */
class __performance_test_set extends cms_test_case
{
    protected $log_file;
    protected $log_warnings_file;

    protected $page_links = [];
    protected $page_links_warnings = [];

    // Config
    protected $quick = true; // Times will be less accurate if they're fast enough, focus on finding slow pages only
    protected $threshold = 0.50; // If loading times exceed this a page is considered slow
    protected $start_page_link = '';
    protected $inclusion_list = null;
    protected $exclusion_list = [
        // Irrevocably slow for some other reason
        'adminzone:admin_addons:addon_export', // Does full file-system scan, particularly slow on a full Git clone
        'adminzone:string_scan', // Does deep scan
    ];
    protected $only_zone = null; // Set this for debugging a narrower set of pages (or use &only=(page-name-prefix)

    public function setUp()
    {
        parent::setUp();

        if (!is_cli()) {
            warn_exit('This test should be run on the command line: php _tests/index.php cli_tests/__performance');
        }

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
        $old_limit = cms_set_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $session_id = $this->establish_admin_callback_session();

        $page_link = $node['page_link'];

        if (($this->inclusion_list !== null) && (!in_array($page_link, $this->inclusion_list))) {
            return;
        }

        if (($this->exclusion_list !== null) && (in_array($page_link, $this->exclusion_list))) {
            return;
        }

        list($zone, $attributes) = page_link_decode($page_link);
        if (($this->only_zone !== null) && ($zone != $this->only_zone)) {
            return;
        }
        if (($this->only !== null) && (substr($attributes['page'], 0, strlen($this->only)) != $this->only)) {
            return;
        }

        $url = page_link_to_url($page_link);

        if (is_cli()) {
            echo 'TESTING ' . $url . '... ';
        }

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

        if (is_cli()) {
            echo '(' . float_format($time) . ' seconds)' . "\n";
        }

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

        cms_set_time_limit($old_limit);
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

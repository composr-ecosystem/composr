<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _feeds_and_podcasts_test_set extends cms_test_case
{
    protected $session_id = null;

    public function setUp()
    {
        parent::setUp();

        $this->establish_admin_session();
        $this->session_id = $this->establish_admin_callback_session();

        require_code('galleries2');
        require_code('xml');
        require_code('permissions2');

        $GLOBALS['SITE_DB']->query_delete('galleries', ['name' => 'podcast'], '', 1);
        add_gallery('podcast', 'Podcast', '', '', 'root');
        set_global_category_access('galleries', 'podcast');

        add_video('ABC', 'podcast', '', 'http://localhost/example.mp3', 'http://localhost/example.png', 1, 1, 1, 1, '', 100, 100, 10);
    }

    public function testXML()
    {
        if ($this->only !== null) {
            return;
        }

        $url = find_script('backend');
        $data = http_get_contents($url, ['cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '</opml>') !== false, 'Failed on ' . $url);
        $parsed = new CMS_simple_xml_reader($data);

        $url = find_script('backend') . '?type=xslt-opml';
        $data = http_get_contents($url, ['cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '</xsl:stylesheet>') !== false, 'Failed on ' . $url);
        $parsed = new CMS_simple_xml_reader($data);

        $url = find_script('backend') . '?type=xslt-atom';
        $data = http_get_contents($url, ['cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '</xsl:stylesheet>') !== false, 'Failed on ' . $url);
        $parsed = new CMS_simple_xml_reader($data);

        $url = find_script('backend') . '?type=xslt-rss';
        $data = http_get_contents($url, ['cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '</xsl:stylesheet>') !== false, 'Failed on ' . $url);
        $parsed = new CMS_simple_xml_reader($data);
    }

    public function testFeeds()
    {
        $_feeds = find_all_hooks('systems', 'rss');
        $feeds = [];
        foreach (array_keys($_feeds) as $feed) {
            if ((substr($feed, 0, 4) == 'cns_') && (get_forum_type() != 'cns')) {
                continue;
            }
            if (($feed == 'tickets') && (get_forum_type() != 'cns')) {
                continue; // Maybe forum has
            }

            if (($this->only !== null) && ($this->only != $feed)) {
                continue;
            }

            foreach (['RSS2', 'Atom'] as $type) {
                $url = find_script('backend') . '?type=' . $type . '&mode=' . $feed . '&days=30&max=100';
                $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'cookies' => [get_session_cookie() => $this->session_id]]);

                $data = str_replace(['http://localhost/', 'https://localhost/'], ['http://example.com/', 'http://example.com/'], $data); // Workaround validator bug

                $result = http_get_contents('https://validator.w3.org/feed/check.cgi', ['timeout' => 30.0, 'convert_to_internal_encoding' => true, 'post_params' => ['rawdata' => $data]]);

                $ok = (strpos($result, 'Congratulations!') !== false);
                if (!$ok) {
                    if ($this->debug) {
                        @var_dump($data);
                        @var_dump($result);
                    }
                }
                $this->assertTrue($ok, 'Failed W3C validation on ' . $url);
            }
        }
    }

    public function testDayFilter()
    {
        if ($this->only !== null) {
            return;
        }

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=100&select=podcast';
        $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '<item>') !== false, 'Failed on ' . $url);

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=100&select=abcxxx';
        $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '<item>') === false, 'Failed on ' . $url);
    }

    public function testMaxFilter()
    {
        if ($this->only !== null) {
            return;
        }

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=1';
        $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '<item>') !== false, 'Failed on ' . $url);

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=0';
        $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '<item>') === false, 'Failed on ' . $url);
    }

    public function testDaysFilter()
    {
        if ($this->only !== null) {
            return;
        }

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=1';
        $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '<item>') !== false, 'Failed on ' . $url);

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=0';
        $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '<item>') === false, 'Failed on ' . $url);
    }

    public function testPodcast()
    {
        if ($this->only !== null) {
            return;
        }

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=100&select=podcast';
        $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, 'itunes:') === false);

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=100&filter=podcast';
        $data = http_get_contents($url, ['convert_to_internal_encoding' => true, 'ua' => 'iTunes/9.0.3 (Macintosh; U; Intel Mac OS X 10_6_2; en-ca)', 'cookies' => [get_session_cookie() => $this->session_id]]);
        $this->assertTrue(strpos($data, '<itunes:author>') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:owner>') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:name>') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:email>') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:image') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:category') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:keywords>') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:summary>') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:author>') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:image') !== false, 'Failed on ' . $url);
        $this->assertTrue(strpos($data, '<itunes:duration>') !== false, 'Failed on ' . $url);
    }

    public function tearDown()
    {
        delete_gallery('podcast');

        parent::tearDown();
    }
}

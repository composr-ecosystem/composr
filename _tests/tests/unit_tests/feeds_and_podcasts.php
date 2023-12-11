<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class feeds_and_podcasts_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        $this->establish_admin_session();

        require_code('galleries2');
        require_code('xml');
        require_code('permissions2');

        $GLOBALS['SITE_DB']->query_delete('galleries', array('name' => 'podcast'), '', 1);
        add_gallery('podcast', 'Podcast', '', '', 'root');
        set_global_category_access('galleries', 'podcast');

        add_video('ABC', 'podcast', '', 'http://localhost/example.mp3', 'http://localhost/example.png', 1, 1, 1, 1, '', 100, 100, 10);
    }

    public function testXML()
    {
        $url = find_script('backend');
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '</opml>') !== false, 'Failed on ' . $url);
        $parsed = new CMS_simple_xml_reader($data);

        $url = find_script('backend') . '?type=xslt-opml';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '</xsl:stylesheet>') !== false, 'Failed on ' . $url);
        $parsed = new CMS_simple_xml_reader($data);

        $url = find_script('backend') . '?type=xslt-atom';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '</xsl:stylesheet>') !== false, 'Failed on ' . $url);
        $parsed = new CMS_simple_xml_reader($data);

        $url = find_script('backend') . '?type=xslt-rss';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '</xsl:stylesheet>') !== false, 'Failed on ' . $url);
        $parsed = new CMS_simple_xml_reader($data);
    }

    public function testFeeds()
    {
        $_feeds = find_all_hooks('systems', 'rss');
        $feeds = array();
        foreach (array_keys($_feeds) as $feed) {
            foreach (array('RSS2', 'Atom') as $type) {
                $url = find_script('backend') . '?type=' . $type . '&mode=' . $feed . '&days=30&max=100';
                $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));

                $data = str_replace(array('http://localhost/', 'https://localhost/'), array('http://example.com/', 'http://example.com/'), $data); // Workaround validator bug

                $result = http_download_file('https://validator.w3.org/feed/check.cgi', null, true, false, 'Composr', array('rawdata' => $data));

                $success = strpos($result, 'Congratulations!') !== false;

                if (!empty($_GET['debug'])) {
                    if (!$success) {
                        var_dump($url);
                        var_dump($result);
                        exit();
                    }
                }

                $this->assertTrue($success, 'Failed on ' . $url);
            }
        }
    }

    public function testDayFilter()
    {
        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=100&select=podcast';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '<item>') !== false, 'Failed on ' . $url);

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=100&select=abcxxx';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '<item>') === false, 'Failed on ' . $url);
    }

    public function testMaxFilter()
    {
        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=1';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '<item>') !== false, 'Failed on ' . $url);

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=0';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '<item>') === false, 'Failed on ' . $url);
    }

    public function testDaysFilter()
    {
        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=1';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '<item>') !== false, 'Failed on ' . $url);

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=0';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, '<item>') === false, 'Failed on ' . $url);
    }

    public function testPodcast()
    {
        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=100&select=podcast';
        $data = http_download_file($url, null, true, false, 'Composr', null, array(get_session_cookie() => get_session_id()));
        $this->assertTrue(strpos($data, 'itunes:') === false);

        $url = find_script('backend') . '?type=RSS2&mode=galleries&days=30&max=100&filter=podcast';
        $data = http_download_file($url, null, true, false, 'iTunes/9.0.3 (Macintosh; U; Intel Mac OS X 10_6_2; en-ca)', null, array(get_session_cookie() => get_session_id()));
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

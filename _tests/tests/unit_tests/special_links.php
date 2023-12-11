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
class special_links_test_set extends cms_test_case
{
    public function testISBN()
    {
        /*
        Disabled as it seems to heavily slow down most user agents

        $data = http_download_file('https://www.bookfinder.com/search/?isbn=0241968984&mode=isbn&st=sr&ac=qr', null, false);
        $this->assertTrue(($data !== null) && (strpos($data, 'No Place to Hide') !== false), 'External link not working, fix test and use within Composr (separate)');

        $data = http_download_file('https://www.bookfinder.com/search/?isbn=978-0241968987&mode=isbn&st=sr&ac=qr', null, false);
        $this->assertTrue(($data !== null) && (strpos($data, 'No Place to Hide') !== false), 'External link not working, fix test and use within Composr (separate)');
        */
    }

    public function testChicklets()
    {
        $data = http_download_file('http://add.my.yahoo.com/content?url=' . urlencode('http://example.com'), null, false);
        $this->assertTrue(is_string($data), 'External link not working, fix test and use within Composr (separate)');
    }

    public function testLookupLinks()
    {
        $data = http_download_file('https://ip.me/ip/12.34.56.78', null, false);
        $this->assertTrue(($data !== null) && (strpos($data, 'AT&amp;T Business') !== false), 'External link (https://ip.me/ip/12.34.56.78) not working, fix test and use within Composr (separate) [LOOKUP_SCREEN.tpl, COMMANDR_WHOIS.tpl]');

        $data = http_download_file('https://ping.eu/ping/?host=12.34.56.78', null, false);
        $this->assertTrue(($data !== null) && (strpos($data, 'Ping') !== false), 'External link (https://ping.eu/ping/?host=12.34.56.78) not working, fix test and use within Composr (separate) [LOOKUP_SCREEN.tpl, COMMANDR_WHOIS.tpl]');

        $data = http_download_file('https://ping.eu/traceroute/?host=12.34.56.78', null, false);
        $this->assertTrue(($data !== null) && (strpos($data, 'Traceroute') !== false), 'External link (https://ping.eu/traceroute/?host=12.34.56.78) not working, fix test and use within Composr (separate) [LOOKUP_SCREEN.tpl, COMMANDR_WHOIS.tpl]');
    }

    public function testWhoIsLink()
    {
        $data = http_download_file('http://whois.domaintools.com/compo.sr', null, false);
        $this->assertTrue(($data !== null) && (stripos($data, 'whois') !== false), 'External link (http://whois.domaintools.com/compo.sr) not working, fix test and use within Composr (separate) [WARN_SPAM_URLS.tpl]');
    }

    public function testHealthCheckLinks()
    {
        global $HTTP_DOWNLOAD_URL;
        $urls = array(
            'https://seositecheckup.com/' => true,
            'https://search.google.com/search-console/welcome' => false,
            'https://www.thehoth.com/' => true,
            'https://www.authoritylabs.com/ranking-tool/' => true,
            'https://validator.w3.org/' => true,
            'https://jigsaw.w3.org/css-validator/' => true,
            'https://achecker.achecks.ca/checker/index.php' => true,
            'https://developers.google.com/search/docs/advanced/structured-data' => true,
            'https://developers.facebook.com/tools/debug/' => true,
            'https://www.woorank.com/' => true,
            'https://website.grader.com/' => true,
            'https://pagespeed.web.dev/' => true,
            'https://www.ssllabs.com/ssltest/' => true,
        );
        foreach ($urls as $url => $test_no_redirecting) {
            $data = http_download_file($url, null, false);
            $this->assertTrue(is_string($data), 'External link (' . $url . ') not working, fix test and use within Composr (separate)');
            if ($test_no_redirecting) {
                $this->assertTrue($HTTP_DOWNLOAD_URL == $url, 'External link (' . $url . ') redirecting elsewhere, fix test and use within Composr (separate)');
            }
        }
    }
}

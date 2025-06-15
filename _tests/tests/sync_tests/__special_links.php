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

// Not all sites are expected to pass because some may block the test suite for being a bot

/**
 * Composr test case class (unit testing).
 */
class __special_links_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL);

        $message = 'Some tests may fail if sites block your server for being a bot.';
        $this->dump($message, 'INFO:');
    }

    public function testISBN()
    {
        if (($this->only !== null) && ($this->only != 'testISBN')) {
            return;
        }

        $c = http_get_contents('https://www.bookfinder.com/search/?isbn=0241968984&mode=isbn&st=sr&ac=qr', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'ignore_http_status' => true, 'timeout' => 20.0, 'ua' => false]); // seems to dislike bots
        $this->assertTrue(strpos($c, 'No Place to Hide') !== false, 'External link not working, fix test and use within Composr (separate)');

        if (php_function_allowed('usleep')) {
            usleep(1000000);
        }

        $c = http_get_contents('https://www.bookfinder.com/search/?isbn=978-0241968987&mode=isbn&st=sr&ac=qr', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'ignore_http_status' => true, 'timeout' => 20.0, 'ua' => false]); // seems to dislike bots
        $this->assertTrue(strpos($c, 'No Place to Hide') !== false, 'External link not working, fix test and use within Composr (separate)');
    }

    public function testLookupLinks()
    {
        if (($this->only !== null) && ($this->only != 'testLookupLinks')) {
            return;
        }

        // Failing due to a Cloudflare CAPTCHA only
        //$c = http_get_contents('https://whatismyipaddress.com/ip/12.34.56.78', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'ignore_http_status' => true, 'timeout' => 20.0]);
        //$this->assertTrue(strpos($c, 'AT&T Services') !== false, 'External link not working, fix test and use within Composr (separate) [LOOKUP_SCREEN.tpl, COMMANDR_WHOIS.tpl]');

        $c = http_get_contents('https://ping.eu/ping/?host=12.34.56.78', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'ignore_http_status' => true, 'timeout' => 20.0]);
        $this->assertTrue(strpos($c, 'Ping') !== false, 'External link not working, fix test and use within Composr (separate) [LOOKUP_SCREEN.tpl, COMMANDR_WHOIS.tpl]');

        $c = http_get_contents('https://ping.eu/traceroute/?host=12.34.56.78', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'ignore_http_status' => true, 'timeout' => 20.0]);
        $this->assertTrue(strpos($c, 'Traceroute') !== false, 'External link not working, fix test and use within Composr (separate) [LOOKUP_SCREEN.tpl, COMMANDR_WHOIS.tpl]');
    }

    public function testWhoIsLink()
    {
        if (($this->only !== null) && ($this->only != 'testWhoIsLink')) {
            return;
        }

        $c = http_get_contents('https://whois.domaintools.com/composr.app', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'ignore_http_status' => true, 'timeout' => 20.0]);
        $this->assertTrue(stripos($c, 'whois') !== false, 'External link not working, fix test and use within Composr (separate) [WARN_SPAM_URLS.tpl]');
    }

    public function testHealthCheckLinks()
    {
        if (($this->only !== null) && ($this->only != 'testHealthCheckLinks')) {
            return;
        }

        $urls = [
            'https://seositecheckup.com/' => true,
            //'https://www.google.com/webmasters/tools/home?pli=1' => false,        Only works if logged in
            'https://www.bing.com/webmasters/about/' => false,
            'https://webmaster.yandex.com/welcome/' => false,
            'https://www.thehoth.com/' => true,
            'https://www.authoritylabs.com/ranking-tool/' => true,
            'https://moz.com/' => true,
            //'https://serps.com/tools/' => true,   Unreliable
            'https://validator.w3.org/' => true,
            'https://jigsaw.w3.org/css-validator/' => true,
            'https://achecks.org/checker/index.php' => true,
            'https://www.bing.com/webmasters/help/url-inspection-55a30305' => true,
            'https://developers.google.com/search/docs/appearance/structured-data' => true,
            'https://webmaster.yandex.com/tools/microtest/' => false,
            'https://developers.facebook.com/tools/debug/' => true,
            'https://www.woorank.com/' => true,
            'https://website.grader.com/' => true,
            'https://pagespeed.web.dev/' => true,
            'https://www.ssllabs.com/ssltest/' => true,
            'https://glockapps.com/spam-testing/' => true,
        ];
        foreach ($urls as $url => $test_no_redirecting) {
            if ($test_no_redirecting) {
                $result = cms_http_request($url, ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'timeout' => 20.0]);
                $this->assertTrue(is_string($result->data), 'External link (' . $url . ') not working (' . $result->message . '), fix test and use within Composr (separate)');
                $this->assertTrue($result->download_url == $url, 'External link (' . $url . ') redirecting elsewhere (' . $result->download_url . '), fix test and use within Composr (separate)');
            } else {
                require_code('urls2');
                $message = '';
                $exists = check_url_exists($url, null, false, 3, $message);
                $this->assertTrue($exists, 'External link (' . $url . ') not working (' . $message . '), fix test and use within Composr (separate)');
            }
        }
    }

    public function testMiscLinks()
    {
        if (($this->only !== null) && ($this->only != 'testMiscLinks')) {
            return;
        }

        $urls = [
            'http://www.google.com/search?as_lq=' . urlencode('http://example.com/'),
            'https://duckduckgo.com/?q=tile+background&iax=images&ia=images',
            'https://curl.haxx.se/ca/cacert.pem',
            'https://www.iplists.com/google.txt',
            'https://www.iplists.com/misc.txt',
            'https://www.iplists.com/non_engines.txt',
            'https://www.cloudflare.com/ips-v4',
            'https://www.cloudflare.com/ips-v6',
            'https://download.db-ip.com/free/dbip-country-lite-' . date('Y-m') . '.csv.gz',
            'https://euvatrates.com/rates.json',
        ];
        foreach ($urls as $url) {
            require_code('urls2');
            $message = '';
            $exists = check_url_exists($url, null, false, 3, $message);
            $this->assertTrue($exists, 'External link (' . $url . ') not working (' . $message . '), fix test and use within Composr (separate)');
        }
    }
}

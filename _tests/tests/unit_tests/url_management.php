<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class url_management_test_set extends cms_test_case
{
    public function testUrlToPageLink()
    {
        $zone_pathed = (get_option('single_public_zone') == '1') ? '' : 'site/';
        $zone = (get_option('single_public_zone') == '1') ? '' : 'site';

        $expected = $zone . ':downloads:browse:testxx123:foo=bar';

        $test = url_to_page_link(get_base_url() . '/' . $zone_pathed . 'pg/downloads/browse/testxx123?foo=bar', false, false);
        $this->assertTrue($test == $expected, 'Got wrong page-link for decode on PG scheme (' . $test . '), ' . $expected . ' expected');

        $test = url_to_page_link(get_base_url() . '/' . $zone_pathed . 'downloads/browse/testxx123?foo=bar', false, false);
        $this->assertTrue($test == $expected, 'Got wrong page-link for decode on SIMPLE scheme (' . $test . '), ' . $expected . ' expected');

        $test = url_to_page_link(get_base_url() . '/' . $zone_pathed . 'downloads/browse/testxx123.htm?foo=bar', false, false);
        $this->assertTrue($test == $expected, 'Got wrong page-link for decode on HTM scheme (' . $test . '), ' . $expected . ' expected');

        $test = url_to_page_link(get_base_url() . '/' . $zone_pathed . 'index.php?page=downloads&type=browse&id=testxx123&&foo=bar', false, false);
        $this->assertTrue($test == $expected, 'Got wrong page-link for decode on RAW scheme (' . $test . '), ' . $expected . ' expected');
    }

    public function testCycle()
    {
        global $CAN_TRY_URL_SCHEMES_CACHE, $URL_REMAPPINGS;
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'PLAIN');

        $test_zone = 'adminzone';
        $test_attributes = ['page' => DEFAULT_ZONE_PAGE_NAME, 'type' => 'bar', 'x' => 'y'];
        $test_hash = 'fish';

        $test_url = build_url($test_attributes, $test_zone, [], false, false, true, $test_hash);
        $test_page_link = $test_zone . ':' . DEFAULT_ZONE_PAGE_NAME . ':bar:x=y#' . $test_hash;

        $_url = $test_url->evaluate();
        $page_link = url_to_page_link($_url);
        $this->assertTrue($page_link == $test_page_link, $page_link . ' vs ' . $test_page_link);

        list($zone, $attributes, $hash) = page_link_decode($test_page_link);
        $this->assertTrue($zone == $test_zone);
        $this->assertTrue($attributes == $test_attributes);
        $this->assertTrue($hash == $test_hash);
    }

    public function testUrlScheme()
    {
        global $CAN_TRY_URL_SCHEMES_CACHE, $URL_REMAPPINGS, $SITE_INFO;

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'RAW');
        $url = build_url(['page' => 'a'], '');
        $this->assertTrue(strpos($url->evaluate(), '.php') !== false);

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'PG');
        $url = build_url(['page' => 'b'], '');
        $this->assertTrue(strpos($url->evaluate(), '/pg/') !== false);

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'HTM');
        $url = build_url(['page' => 'c'], '');
        $this->assertTrue(strpos($url->evaluate(), '.htm') !== false);

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'SIMPLE');
        $url = build_url(['page' => 'd'], '');
        $this->assertTrue(strpos($url->evaluate(), '.htm') === false && strpos($url->evaluate(), '.php') === false);

        $SITE_INFO['block_url_schemes'] = '1';
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'HTM');
        $url = build_url(['page' => 'e'], '');
        $this->assertTrue(strpos($url->evaluate(), '.php') !== false);
    }
}

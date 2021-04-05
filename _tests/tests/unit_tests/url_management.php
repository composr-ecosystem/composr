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

        set_option('url_scheme', 'RAW');
        $test = url_to_page_link(get_base_url() . '/' . $zone_pathed . 'index.php?page=downloads&type=browse&id=testxx123&&foo=bar', false, false);
        $this->assertTrue($test == $expected, 'Got wrong page-link for decode on RAW scheme (' . $test . '), ' . $expected . ' expected');

        set_option('url_scheme', 'PG');
        $test = url_to_page_link(get_base_url() . '/' . $zone_pathed . 'pg/downloads/browse/testxx123?foo=bar', false, false);
        $this->assertTrue($test == $expected, 'Got wrong page-link for decode on PG scheme (' . $test . '), ' . $expected . ' expected');

        set_option('url_scheme', 'HTM');
        $test = url_to_page_link(get_base_url() . '/' . $zone_pathed . 'downloads/browse/testxx123.htm?foo=bar', false, false);
        $this->assertTrue($test == $expected, 'Got wrong page-link for decode on HTM scheme (' . $test . '), ' . $expected . ' expected');

        set_option('url_scheme', 'SIMPLE');
        $test = url_to_page_link(get_base_url() . '/' . $zone_pathed . 'downloads/browse/testxx123?foo=bar', false, false);
        $this->assertTrue($test == $expected, 'Got wrong page-link for decode on SIMPLE scheme (' . $test . '), ' . $expected . ' expected');
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
        set_option('url_scheme_omit_default_zone_pages', '0');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'site/index.php?page=examplepage&type=exampletype&id=exampleid&foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'index.php?page=examplepage&type=exampletype&id=exampleid&foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'index.php?page=examplepage&id=exampleid&foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == 'index.php?page=examplepage&foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'index.php?page=examplepage', 'Got: ' . $got);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME, 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME, 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME . '&foo=bar', 'Got: ' . $got);

        set_option('url_scheme_omit_default_zone_pages', '1');
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'site/index.php?page=examplepage', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'index.php?page=examplepage', 'Got: ' . $got);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == '', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == '', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/true, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME . '&type=exampletype', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME . '&foo=bar', 'Got: ' . $got);

        // --

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'PG');
        set_option('url_scheme_omit_default_zone_pages', '0');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'site/pg/examplepage/exampletype/exampleid?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'pg/examplepage/exampletype/exampleid?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'pg/examplepage/browse/exampleid?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == 'pg/examplepage?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'pg/examplepage', 'Got: ' . $got);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'pg/' . DEFAULT_ZONE_PAGE_NAME, 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'pg/' . DEFAULT_ZONE_PAGE_NAME, 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == 'pg/' . DEFAULT_ZONE_PAGE_NAME . '?foo=bar', 'Got: ' . $got);

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme_omit_default_zone_pages', '1');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'site/pg/examplepage', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'pg/examplepage', 'Got: ' . $got);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == '', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == '', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/true, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'pg/' . DEFAULT_ZONE_PAGE_NAME . '/exampletype', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == 'pg/home?foo=bar', 'Got: ' . $got);

        // --

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'HTM');
        set_option('url_scheme_omit_default_zone_pages', '0');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'site/examplepage/exampletype/exampleid.htm?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'examplepage/exampletype/exampleid.htm?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'examplepage/browse/exampleid.htm?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == 'examplepage.htm?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'examplepage.htm', 'Got: ' . $got);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME . '.htm', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME . '.htm', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME . '.htm?foo=bar', 'Got: ' . $got);

        set_option('url_scheme_omit_default_zone_pages', '1');
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'site/examplepage.htm', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'examplepage.htm', 'Got: ' . $got);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == '', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == '', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/true, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME . '/exampletype.htm', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME . '.htm?foo=bar', 'Got: ' . $got);

        // --

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'SIMPLE');
        set_option('url_scheme_omit_default_zone_pages', '0');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'site/examplepage/exampletype/exampleid?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'examplepage/exampletype/exampleid?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'examplepage/browse/exampleid?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == 'examplepage?foo=bar', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'examplepage', 'Got: ' . $got);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME, 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME, 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME . '?foo=bar', 'Got: ' . $got);

        set_option('url_scheme_omit_default_zone_pages', '1');
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'site/examplepage', 'Got: ' . $got);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == 'examplepage', 'Got: ' . $got);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == '', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == '', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/true, /*$has_id*/false, /*$has_arb_params*/false);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME . '/exampletype', 'Got: ' . $got);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $this->assertTrue($got == DEFAULT_ZONE_PAGE_NAME . '?foo=bar', 'Got: ' . $got);

        // --

        $SITE_INFO['block_url_schemes'] = '1';
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'HTM');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $this->assertTrue($got == 'site/index.php?page=examplepage&type=exampletype&id=exampleid&foo=bar', 'Got: ' . $got);
    }

    protected function url_builder($zone, $page, $has_type, $has_id, $has_arb_params)
    {
        $url_map = [];
        if ($page !== null) {
            $url_map['page'] = $page;
        }
        if ($has_type) {
            $url_map['type'] = 'exampletype';
        }
        if ($has_id) {
            $url_map['id'] = 'exampleid';
        }
        if ($has_arb_params) {
            $url_map['foo'] = 'bar';
        }
        $url = build_url($url_map, $zone);
        return preg_replace('#^' . preg_quote(get_base_url() . '/', '#') . '#', '', $url->evaluate());
    }
}

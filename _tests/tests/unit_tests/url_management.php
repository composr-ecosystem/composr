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

/**
 * Composr test case class (unit testing).
 */
class url_management_test_set extends cms_test_case
{
    protected $backup_url_scheme;

    public function setUp()
    {
        parent::setUp();

        $GLOBALS['SITE_INFO']['block_url_schemes'] = '0';
    }

    public function testUrlToPageLink()
    {
        $zone_pathed = (get_option('single_public_zone') == '1') ? '' : 'site/';
        $zone = (get_option('single_public_zone') == '1') ? '' : 'site';

        $expected = $zone . ':downloads:browse:testxx123:foo=bar';

        $this->backup_url_scheme = get_option('url_scheme');

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

        unset($_GET['keep_devtest']);

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'RAW');
        set_option('url_scheme_omit_default_zone_pages', '0');

        $zone_prefix = (get_option('single_public_zone') == '1') ? '' : 'site/';

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = $zone_prefix . 'index.php?page=examplepage&type=exampletype&id=exampleid&foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = 'index.php?page=examplepage&type=exampletype&id=exampleid&foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = 'index.php?page=examplepage&id=exampleid&foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = 'index.php?page=examplepage&foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'index.php?page=examplepage';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME;
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME;
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME . '&foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        set_option('url_scheme_omit_default_zone_pages', '1');
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = $zone_prefix . 'index.php?page=examplepage';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'index.php?page=examplepage';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = '';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = '';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/true, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME . '&type=exampletype';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = 'index.php?page=' . DEFAULT_ZONE_PAGE_NAME . '&foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        // --

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'PG');
        set_option('url_scheme_omit_default_zone_pages', '0');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = $zone_prefix . 'pg/examplepage/exampletype/exampleid?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = 'pg/examplepage/exampletype/exampleid?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = 'pg/examplepage/browse/exampleid?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = 'pg/examplepage?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'pg/examplepage';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'pg/' . DEFAULT_ZONE_PAGE_NAME;
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'pg/' . DEFAULT_ZONE_PAGE_NAME;
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = 'pg/' . DEFAULT_ZONE_PAGE_NAME . '?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme_omit_default_zone_pages', '1');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = $zone_prefix . 'pg/examplepage';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'pg/examplepage';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = '';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = '';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/true, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'pg/' . DEFAULT_ZONE_PAGE_NAME . '/exampletype';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = 'pg/home?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        // --

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'HTM');
        set_option('url_scheme_omit_default_zone_pages', '0');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = $zone_prefix . 'examplepage/exampletype/exampleid.htm?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = 'examplepage/exampletype/exampleid.htm?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = 'examplepage/browse/exampleid.htm?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = 'examplepage.htm?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'examplepage.htm';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = DEFAULT_ZONE_PAGE_NAME . '.htm';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = DEFAULT_ZONE_PAGE_NAME . '.htm';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = DEFAULT_ZONE_PAGE_NAME . '.htm?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        set_option('url_scheme_omit_default_zone_pages', '1');
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = $zone_prefix . 'examplepage.htm';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'examplepage.htm';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = '';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = '';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/true, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = DEFAULT_ZONE_PAGE_NAME . '/exampletype.htm';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = DEFAULT_ZONE_PAGE_NAME . '.htm?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        // --

        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'SIMPLE');
        set_option('url_scheme_omit_default_zone_pages', '0');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = $zone_prefix . 'examplepage/exampletype/exampleid?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = 'examplepage/exampletype/exampleid?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = 'examplepage/browse/exampleid?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = 'examplepage?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'examplepage';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = DEFAULT_ZONE_PAGE_NAME;
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = DEFAULT_ZONE_PAGE_NAME;
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = DEFAULT_ZONE_PAGE_NAME . '?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        set_option('url_scheme_omit_default_zone_pages', '1');
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = $zone_prefix . 'examplepage';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', 'examplepage', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = 'examplepage';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', '', /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = '';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = '';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/true, /*$has_id*/false, /*$has_arb_params*/false);
        $expected = DEFAULT_ZONE_PAGE_NAME . '/exampletype';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        $got = $this->url_builder('', null, /*$has_type*/false, /*$has_id*/false, /*$has_arb_params*/true);
        $expected = DEFAULT_ZONE_PAGE_NAME . '?foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);

        // --

        $SITE_INFO['block_url_schemes'] = '1';
        $CAN_TRY_URL_SCHEMES_CACHE = null;
        $URL_REMAPPINGS = null;
        set_option('url_scheme', 'HTM');

        $got = $this->url_builder('site', 'examplepage', /*$has_type*/true, /*$has_id*/true, /*$has_arb_params*/true);
        $expected = $zone_prefix . 'index.php?page=examplepage&type=exampletype&id=exampleid&foo=bar';
        $this->assertTrue($got == $expected, 'Got: ' . $got . ' ; Expected: ' . $expected);
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

        $_url = preg_replace('#^' . preg_quote(get_base_url() . '/', '#') . '#', '', $url->evaluate());
        $_url = preg_replace('#[&?]keep_devtest=1#', '', $_url);

        return $_url;
    }

    public function tearDown()
    {
        set_option('url_scheme', $this->backup_url_scheme);
    }
}

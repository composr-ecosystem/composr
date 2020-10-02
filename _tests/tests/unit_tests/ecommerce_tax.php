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
class ecommerce_tax_test_set extends cms_test_case
{
    public function setUp()
    {
        require_code('ecommerce');
        require_code('config2');

        set_option('ecommerce_test_mode', '1');

        set_option('taxcloud_api_key', '78C9B361-6015-480D-9956-D75F9A8C06EA');
        set_option('taxcloud_api_id', '447BBD0');
    }

    public function testEUTax()
    {
        set_option('currency', 'GBP');

        set_option('business_street_address', '1234 Scope');
        set_option('business_city', 'Hope');
        set_option('business_county', '');
        set_option('business_state', '');
        set_option('business_post_code', 'HO1 234');
        set_option('business_country', 'GB');

        // This test will break if tax rates change, so correct it if that happens...

        $_POST['shipping_street_address'] = '1234 Scope';
        $_POST['shipping_city'] = 'Hope';
        $_POST['shipping_county'] = '';
        $_POST['shipping_state'] = '';
        $_POST['shipping_post_code'] = 'HO1 234';
        $_POST['shipping_country'] = 'GB';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, 'EU', 100.00);
        $this->assertTrue($tax == 20.0);

        $_POST['shipping_country'] = 'DE';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, 'EU', 100.00);
        $this->assertTrue($tax == 19.0);
    }

    /*public function testUSTax()
    {
        set_option('currency', 'USD');

        set_option('business_street_address', '1444 S. Alameda Street');
        set_option('business_city', 'Los Angeles');
        set_option('business_county', '');
        set_option('business_state', 'CA');
        set_option('business_post_code', '90021');
        set_option('business_country', 'US');

        // This test will break if tax rates change, so correct it if that happens...

        $_POST['shipping_street_address'] = '1444 S. Alameda Street';
        $_POST['shipping_city'] = 'Los Angeles';
        $_POST['shipping_county'] = '';
        $_POST['shipping_state'] = 'CA';
        $_POST['shipping_post_code'] = '90021';
        $_POST['shipping_country'] = 'US';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, 'TIC:00000', 100.00);
        $this->assertTrue($tax == 9.50, 'Expected 9.50 but got ' . float_format($tax));

        $_POST['shipping_street_address'] = '1234 Scope';
        $_POST['shipping_city'] = 'Hope';
        $_POST['shipping_county'] = '';
        $_POST['shipping_state'] = '';
        $_POST['shipping_post_code'] = 'HO1 234';
        $_POST['shipping_country'] = 'GB';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, 'TIC:00000', 100.00);
        $this->assertTrue($tax == 0.0);
    }*/

    public function testFlatTax()
    {
        set_option('tax_country_regexp', '');
        $_POST['shipping_country'] = 'US';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, float_to_raw_string(18.0), 100.00);
        $this->assertTrue($tax == 18.0);

        set_option('tax_country_regexp', '^(AT|BE|BG|HR|CY|CZ|DK|EE|FI|FR|DE|GR|HU|IE|IT|LV|LT|LU|MT|NL|PL|PT|RO|SK|SI|ES|SE|UK|AX)$'); // Europe only
        $_POST['shipping_country'] = 'US';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, float_to_raw_string(18.0), 100.00);
        $this->assertTrue($tax == 0.0);
    }
}

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
class _api_ecommerce_tax_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('ecommerce');

        set_option('ecommerce_test_mode', '1');
    }

    public function testEUTax()
    {
        if (($this->only !== null) && ($this->only != 'testEUTax')) {
            return;
        }

        set_option('currency', 'GBP');

        set_option('business_street_address', '1234 Scope');
        set_option('business_city', 'Hope');
        set_option('business_county', '');
        set_option('business_state', '');
        set_option('business_post_code', 'HO1 234');
        set_option('business_country', 'GB');

        // This test will break if tax rates change, so correct it if that happens...

        $_POST['shipping_address1'] = '1234 Scope';
        $_POST['shipping_city'] = 'Hope';
        $_POST['shipping_county'] = '';
        $_POST['shipping_state'] = '';
        $_POST['shipping_postalcode'] = 'HO1 234';
        $_POST['shipping_country'] = 'GB';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, 'EU', 100.00);
        $this->assertTrue($tax == 20.0);

        $_POST['shipping_country'] = 'DE';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, 'EU', 100.00);
        $this->assertTrue($tax == 19.0);
    }

    public function testUSTaxTICList()
    {
        if (($this->only !== null) && ($this->only != 'testUSTaxTICList')) {
            return;
        }

        $_data = http_get_contents('https://taxcloud.com/tic/json/', ['convert_to_internal_encoding' => true, 'timeout' => 20.0]);
        $data = @json_decode($_data, true);
        $this->assertTrue(isset($data['tic_list'][0]));
    }

    /* Disabled as you need to register on TaxCloud with a real business ID
    public function testUSTax()
    {
        if (($this->only !== null) && ($this->only != 'testUSTax')) {
            return;
        }

        set_option('currency', 'USD');

        set_option('business_street_address', '1444 S. Alameda Street');
        set_option('business_city', 'Los Angeles');
        set_option('business_county', '');
        set_option('business_state', 'CA');
        set_option('business_post_code', '90021');
        set_option('business_country', 'US');

        $this->load_key_options('taxcloud');

        $post = $_POST;

        $_POST['shipping_address1'] = '1444 S. Alameda Street';
        $_POST['shipping_city'] = 'Los Angeles';
        $_POST['shipping_county'] = '';
        $_POST['shipping_state'] = 'CA';
        $_POST['shipping_postalcode'] = '90021';
        $_POST['shipping_country'] = 'US';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, 'TIC:00000', 100.00);
        $this->assertTrue($tax > 0.0, 'Expected non-zero but got ' . float_format($tax));

        $_POST['shipping_address1'] = '1234 Scope';
        $_POST['shipping_city'] = 'Hope';
        $_POST['shipping_county'] = '';
        $_POST['shipping_state'] = '';
        $_POST['shipping_postalcode'] = 'HO1 234';
        $_POST['shipping_country'] = 'GB';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, 'TIC:00000', 100.00);
        $this->assertTrue($tax == 0.0, 'Expected 0.00 but got ' . float_format($tax));

        $_POST = $post;
    }
    */

    public function testTaxCloudPing()
    {
        if (($this->only !== null) && ($this->only != 'testTaxCloudPing')) {
            return;
        }

        $this->load_key_options('taxcloud');
        $this->load_key_options('business');

        $this->run_health_check('API connections', 'Tax Services');
    }

    public function testFlatTax()
    {
        if (($this->only !== null) && ($this->only != 'testFlatTax')) {
            return;
        }

        set_option('tax_country_regexp', '');
        $_POST['shipping_state'] = 'WI';
        $_POST['shipping_postalcode'] = '53534';
        $_POST['shipping_country'] = 'US';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, float_to_raw_string(18.0), 100.00);
        $this->assertTrue($tax == 18.0);

        set_option('tax_country_regexp', '^(AT|BE|BG|HR|CY|CZ|DK|EE|FI|FR|DE|GR|HU|IE|IT|LV|LT|LU|MT|NL|PL|PT|RO|SK|SI|ES|SE|UK|AX)$'); // Europe only
        $_POST['shipping_state'] = 'WI';
        $_POST['shipping_postalcode'] = '53534';
        $_POST['shipping_country'] = 'US';
        list($tax_derivation, $tax, $tax_tracking, $shipping_tax) = calculate_tax_due(null, float_to_raw_string(18.0), 100.00);
        $this->assertTrue($tax == 0.0);
    }
}

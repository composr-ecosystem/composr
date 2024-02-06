<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// Also see _api_ecommerce_shipping

/**
 * Composr test case class (unit testing).
 */
class ecommerce_shipping_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('ecommerce');

        set_option('ecommerce_test_mode', '1');

        set_option('shipping_density', '5000.0');
        set_option('shipping_weight_units', 'Kg');
        set_option('shipping_distance_units', 'Cm');
        set_option('shipping_tax_code', '0%');
        set_option('shipping_cost_base', '10.00');
        set_option('shipping_cost_factor', '1.20');

        set_option('shipping_shippo_api_test', '');
        set_option('shipping_shippo_api_live', '');

        set_option('business_street_address', '1234 Scope');
        set_option('business_city', 'Hope');
        set_option('business_county', '');
        set_option('business_state', '');
        set_option('business_post_code', 'HO1 234');
        set_option('business_country', 'GB');
    }

    public function testShippingSplitAddress()
    {
        $this->assertTrue(split_street_address("123 A B\nFoobar Area", 2) == ['123 A B', 'Foobar Area']);

        $this->assertTrue(split_street_address("123 A B\nFoobar Area", 3, true) == ['', '123 A B', 'Foobar Area']);

        $this->assertTrue(split_street_address("123 A B\nFoobar Area", 3) == ['123 A B', 'Foobar Area', '']);

        $this->assertTrue(split_street_address("123 A B", 3, true) == ['', '123 A B', '']);

        $this->assertTrue(split_street_address("123 A B", 3) == ['123 A B', '', '']);

        $this->assertTrue(split_street_address("123 A B\nFoobar Area\nYo Yo", 3, true) == ['', '123 A B', 'Foobar Area, Yo Yo']);

        $this->assertTrue(split_street_address("123 A B\nFoobar Area\nYo Yo", 3) == ['123 A B', 'Foobar Area', 'Yo Yo']);

        $this->assertTrue(split_street_address("Foo Corps\n123 A B", 3, true) == ['Foo Corps', '123 A B', '']);

        $this->assertTrue(split_street_address("Foo Corps\n123 A B", 2) == ['Foo Corps', '123 A B']);

        $this->assertTrue(split_street_address("Foo Corps\n123 A B\nFoobar Area", 3, true) == ['Foo Corps', '123 A B', 'Foobar Area']);
    }

    public function testShippingNormalisation()
    {
        // Make sure dimensions calculate properly if weight known
        $product_weight = 10.0;
        $product_length = null;
        $product_width = null;
        $product_height = null;
        calculate_shipping_cost(null, null, $product_weight, $product_length, $product_width, $product_height);
        //$expected_product_volume = $product_weight * intval(get_option('shipping_density')) = 10.0 * 5000.0 = 50000.0 cm3;
        //$product_length = $product_width = $product_height = pow(50000.0, 1.0 / 3.0) = 36.84 cm;
        $this->assertTrue(round($product_length, 2) == 36.84);
        $this->assertTrue($product_length == $product_width);
        $this->assertTrue($product_length == $product_height);

        // Now the reverse, Make sure weight calculates properly if dimensions known
        $product_weight = null;
        $product_length = 36.84;
        $product_width = null;
        $product_height = null;
        calculate_shipping_cost(null, null, $product_weight, $product_length, $product_width, $product_height);
        $this->assertTrue(round($product_weight, 2) == 10.00);
    }

    public function testShippingCalculationsInternal()
    {
        set_option('shipping_shippo_api_test', '');

        // Test actual shipping cost
        $product_weight = 10.0;
        $product_length = 36.84;
        $product_width = 36.84;
        $product_height = 36.84;
        $cost = calculate_shipping_cost(null, null, $product_weight, $product_length, $product_width, $product_height);
        $this->assertTrue($cost == 10.00 + (10.0 * 1.20));
    }
}

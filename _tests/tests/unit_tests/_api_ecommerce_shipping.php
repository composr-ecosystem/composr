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

// Also see ecommerce_shipping

/**
 * Composr test case class (unit testing).
 */
class _api_ecommerce_shipping_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('ecommerce');

        $this->load_key_options('shippo', false);

        set_option('ecommerce_test_mode', '1');
        set_option('business_street_address', '34661 Lyndon B Johnson Fwy');
        set_option('business_city', 'Dallas');
        set_option('business_county', '');
        set_option('business_state', 'TX');
        set_option('business_post_code', '75241');
        set_option('business_country', 'US');
    }

    public function testShippingCalculationsShippo()
    {
        $product_weight = 10.0;
        $product_length = 36.84;
        $product_width = 36.84;
        $product_height = 36.84;
        $cost = calculate_shipping_cost(null, null, $product_weight, $product_length, $product_width, $product_height);
        $this->assertTrue(($cost > 10.00) && ($cost < 200.00), 'Shipping cost for a test product seems wrong');
    }

    public function testShippoConnection()
    {
        require_code('health_check');
        $this->run_health_check('API connections', 'Shippo', CHECK_CONTEXT__LIVE_SITE, true);
    }
}

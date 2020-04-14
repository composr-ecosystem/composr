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

        $this->load_key_options('shippo');

        set_option('ecommerce_test_mode', '1');

        set_option('business_street_address', '1234 Scope');
        set_option('business_city', 'Hope');
        set_option('business_county', '');
        set_option('business_state', '');
        set_option('business_post_code', 'HO1 234');
        set_option('business_country', 'GB');
    }

    public function testShippingCalculationsShippo()
    {
        $this->run_health_check('API connections', 'Shippo');
    }
}

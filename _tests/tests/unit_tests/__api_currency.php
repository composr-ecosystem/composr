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
class __api_currency_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('currency');

        $this->load_key_options('currency');
    }

    public function testCountryToCurrency()
    {
        $this->assertTrue(country_to_currency('GB') == 'GBP');
    }

    public function testTypesOk()
    {
        $test_a = currency_convert(10.00, 'USD', 'GBP');
        $test_b = currency_convert(10, 'USD', 'GBP');
        $this->assertTrue(gettype($test_a) == gettype($test_b) && $test_a > $test_b - 0.005 && $test_b > $test_a - 0.005, 'Got ' . serialize($test_a) . ' and ' . serialize($test_b)); // Floats and integers should convert the same
    }

    public function testCurrencyViaConvAPI()
    {
        $this->run_health_check('API connections', 'Currency conversions');
    }
}

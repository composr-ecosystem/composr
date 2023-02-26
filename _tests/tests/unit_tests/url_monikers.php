<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class url_monikers_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('urls2');
    }

    public function testMonikerGeneration()
    {
        $cases = [
            // Stop-word removal
            'This is a test for the feature' => 'feature',
            'This is a better test for the feature' => 'better-feature',

            // Edge cases
            'This is' => 'this-is', // All stop-words, so leave them
            '*' => 'untitled', // Gets fully stripped
            'x*(y)' => 'x-y', // Double symbols
            'I went to the woods today and found a surprise' => 'went-woods-today-found', // Long, shortened
        ];

        foreach ($cases as $title => $expected_moniker) {
            $result_moniker = _generate_moniker($title);
            $this->assertTrue($result_moniker == $expected_moniker, 'Failed on case: ' . $title . ' (got ' . $result_moniker . ', expected ' . $expected_moniker . ')');
        }
    }
}

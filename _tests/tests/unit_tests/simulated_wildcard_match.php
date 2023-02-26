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
class simulated_wildcard_match_test_set extends cms_test_case
{
    public function testWildcards()
    {
        // Full cover
        $this->assertTrue(simulated_wildcard_match('Test sentence', 'Test', false));
        $this->assertTrue(!simulated_wildcard_match('Test sentence', 'Test', true));

        // Normal syntax
        $this->assertTrue(simulated_wildcard_match('Test sentence', 'Test*', true));
        $this->assertTrue(!simulated_wildcard_match('Test sentence', 'X', true));
        $this->assertTrue(!simulated_wildcard_match('Test sentence', '*X*', true));
        $this->assertTrue(!simulated_wildcard_match('Test sentence', '?X?', true));
        $this->assertTrue(simulated_wildcard_match('Test sentence', '*sentence', true));
        $this->assertTrue(simulated_wildcard_match('Test sentence', '???? sentence', true));

        // SQL syntax
        $this->assertTrue(simulated_wildcard_match('Test sentence', 'Test%', true));
        $this->assertTrue(!simulated_wildcard_match('Test sentence', 'X', true));
        $this->assertTrue(!simulated_wildcard_match('Test sentence', '%X%', true));
        $this->assertTrue(!simulated_wildcard_match('Test sentence', '_X_', true));
        $this->assertTrue(simulated_wildcard_match('Test sentence', '%sentence', true));
        $this->assertTrue(simulated_wildcard_match('Test sentence', '____ sentence', true));

        // Complexity
        $this->assertTrue(simulated_wildcard_match('Test sentence', 'Test \\sentence', false)); // Because we strip "\" from patterns
        $this->assertTrue(!simulated_wildcard_match('Test sentence', 'Test Xsentence', false));
    }
}

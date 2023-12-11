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
class php_versioning_test_set extends cms_test_case
{
    public function testPhpVersionChecking()
    {
        require_code('version2');

        $v = strval(PHP_MAJOR_VERSION) . '.' . strval(PHP_MINOR_VERSION);
        $this->assertTrue(is_php_version_supported($v) !== null);

        // This needs updating occasionally. The key thing is Composr itself will update itself, and this just checks that automatic updating works as we'd expect.
        $this->assertTrue(is_php_version_supported(float_to_raw_string(7.1, 1))); // Normally supported
        $this->assertTrue(is_php_version_supported(float_to_raw_string(9.0, 1))); // Future, assume supported
        $this->assertTrue(!is_php_version_supported(float_to_raw_string(5.5, 1))); // Known unsupported
        $this->assertTrue(!is_php_version_supported(float_to_raw_string(4.4, 1))); // Known unsupported and too old to be tracked
    }
}

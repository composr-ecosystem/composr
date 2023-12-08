<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
        $this->assertTrue((is_php_version_supported($v) !== null), 'Your PHP version (' . $v . ') is no longer supported.');

        // This needs updating occasionally. The key thing is Composr itself will update itself, and this just checks that automatic updating works as we'd expect.
        $this->assertTrue(is_php_version_supported(float_to_raw_string(8.2, 1)), 'Expected PHP 8.2 to be supported, but it is not.'); // Normally supported
        $this->assertTrue(is_php_version_supported(float_to_raw_string(9.0, 1)), 'Expected PHP 9.0 to be supported, but it is not.'); // Future, assume supported
        $this->assertTrue(!is_php_version_supported(float_to_raw_string(7.4, 1)), 'Expected PHP 7.4 to NOT be supported, but it is.'); // Known unsupported
        $this->assertTrue(!is_php_version_supported(float_to_raw_string(4.4, 1)), 'Expected PHP 4.4 to NOT be supported, but it is.'); // Known unsupported and too old to be tracked
    }
}

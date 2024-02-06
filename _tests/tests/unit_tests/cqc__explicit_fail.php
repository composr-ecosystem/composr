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

/**
 * Composr test case class (unit testing).
 */
class cqc__explicit_fail_test_set extends cms_test_case
{
    public function testCQCTestsStillWork()
    {
        $url = get_base_url() . '/_tests/codechecker/codechecker.php?test=10&somewhat_pedantic=1';
        $result = http_get_contents($url, ['convert_to_internal_encoding' => true]);
        $this->assertTrue(strpos($result, 'Bad return type') !== false, $result);
    }

    public function testCQCFailuresStillWork()
    {
        $path = get_file_base() . '/temp/temp.php';
        require_code('files');
        cms_file_put_contents_safe($path, "<" . "?= foo() . 1 + ''\n");
        $url = get_base_url() . '/_tests/codechecker/codechecker.php?to_use=temp/temp.php&api=1&somewhat_pedantic=1';
        $result = http_get_contents($url, ['convert_to_internal_encoding' => true, 'timeout' => 10000.0]);
        unlink($path);

        $this->assertTrue(strpos($result, 'Could not find function') !== false, 'Should have an error but does not (' . $result . ')');
    }
}

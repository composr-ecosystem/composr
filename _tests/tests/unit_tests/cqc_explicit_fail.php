<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class cqc_explicit_fail_test_set extends cms_test_case
{
    public function testDatabase()
    {
        cms_disable_time_limit();
        $path = get_file_base() . '/temp/temp.php';
        require_code('files');
        cms_file_put_contents_safe($path, "<" . "?= foo() . 1 + ''\n");
        $result = http_get_contents(get_base_url() . '/_tests/codechecker/code_quality.php?subdir=temp&api=1', array('timeout' => 10000.0)); // TODO #3467
        unlink($path);

        $this->assertTrue(strpos($result, 'Could not find function') !== false, 'Should have an error but does not (' . $result . ')');
    }
}

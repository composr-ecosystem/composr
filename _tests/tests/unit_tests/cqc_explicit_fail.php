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
class cqc_explicit_fail_test_set extends cms_test_case
{
    public function testDatabase()
    {
        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }
        $path = get_file_base() . '/safe_mode_temp/temp.php';
        require_code('files');
        cms_file_put_contents_safe($path, '<?php echo foo().1+\'\';');
        $result = http_download_file(get_base_url() . '/_tests/codechecker/code_quality.php?subdir=safe_mode_temp&api=1', null, true, false, 'Composr', null, null, null, null, null, null, null, null, 10000.0);
        unlink($path);

        $this->assertTrue(strpos($result, 'Could not find function') !== false, 'Should have an error but does not (' . $result . ')');
    }
}

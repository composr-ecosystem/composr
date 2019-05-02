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
class override_notes_consistency_test_set extends cms_test_case
{
    public function testOverrideNotesConsistency()
    {
        require_code('files2');

        $files = get_directory_contents(get_file_base(), '', IGNORE_FLOATING, true, true, array('php'));
        $files[] = 'install.php';
        foreach ($files as $path) {
            if (file_exists(dirname($path) . '/index.php')) {
                continue; // Zone directory, no override support
            }

            if (preg_match('#(^sources|/modules|^data)(_custom)?/#', $path) == 0) {
                continue;
            }

            $c = file_get_contents(get_file_base() . '/' . $path);

            if (strpos($c, 'CQC: No check') !== false) {
                continue;
            }
            if (strpos($c, 'CQC: No API check') !== false) {
                continue;
            }

            $pos = strpos($c, 'NOTE TO PROGRAMMERS:');
            if (strpos($path, '_custom/') === false) {
                $ok = ($pos !== false) && ($pos < 200);
                $this->assertTrue($ok, 'Missing "NOTE TO PROGRAMMERS:" in ' . $path);
            } else {
                $ok = ($pos === false) || ($pos > 200);
                $this->assertTrue($ok, 'Undesirable "NOTE TO PROGRAMMERS:" in ' . $path);
            }
        }
    }
}

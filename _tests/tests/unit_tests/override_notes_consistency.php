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
class override_notes_consistency_test_set extends cms_test_case
{
    public function testOverrideNotesConsistency()
    {
        require_code('files');
        require_code('files2');
        $files = get_directory_contents(get_file_base());
        $files[] = 'install.php';
        foreach ($files as $file) {
            if (substr($file, -4) != '.php') {
                continue;
            }

            if (should_ignore_file($file, IGNORE_NONBUNDLED_VERY_SCATTERED | IGNORE_CUSTOM_DIR_SUPPLIED_CONTENTS | IGNORE_CUSTOM_DIR_GROWN_CONTENTS)) {
                continue;
            }

            if (file_exists(dirname($file) . '/index.php')) {
                continue; // Zone directory, no override support
            }

            $contents = file_get_contents(get_file_base() . '/' . $file);

            if (strpos($contents, 'CQC: No check') !== false) {
                continue;
            }
            if (strpos($contents, 'NO_API_CHECK') !== false) {
                continue;
            }
            if (strpos($file, '/diff/') !== false) {
                continue;
            }

            if (strpos($file, '_custom/') === false) {
                $this->assertTrue(strpos($contents, 'NOTE TO PROGRAMMERS:') !== false, 'Missing "NOTE TO PROGRAMMERS:" in ' . $file);
            } else {
                $this->assertFalse(strpos($contents, 'NOTE TO PROGRAMMERS:') !== false, 'Undesirable "NOTE TO PROGRAMMERS:" in ' . $file);
            }
        }
    }
}

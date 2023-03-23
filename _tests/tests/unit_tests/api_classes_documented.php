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
class api_classes_documented_test_set extends cms_test_case
{
    public function testAPIClassesDocumented()
    {
        /*
        NB: This only bothers with stuff we are going to include in the PHPDocumentor scan. Otherwise we don't care as Composr doesn't (packages work on a file level, this isn't Java).
        */

        foreach (['sources', 'sources/database', 'sources/database/shared', 'sources/forum'] as $d) {
            $path = get_file_base() . '/' . $d;
            $dh = @opendir($path);
            if ($dh !== false) {
                while (($file = readdir($dh)) !== false) {
                    if (substr($file, -4) != '.php') {
                        continue;
                    }

                    $c = cms_file_get_contents_safe($path . '/' . $file);

                    if (strpos($c, 'CQC: No check') !== false) {
                        continue;
                    }
                    if (strpos($c, 'CQC: No API check') !== false) {
                        continue;
                    }

                    $matches = [];
                    $num_matches = preg_match_all('#\n\t*(abstract\s+)?class (\w+)#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $this->assertTrue(
                            preg_match('# +\* @package\s+\w+\n\t* +\*/\n\t*(abstract\s+)?class ' . preg_quote($matches[2][$i], '#') . '#', $c) != 0,
                            'Undefined package for PHPDocumentor-exposed class: ' . $d . '/' . $file . ' (' . $matches[2][$i] . ')'
                        );
                    }
                }

                closedir($dh);
            }
        }
    }
}

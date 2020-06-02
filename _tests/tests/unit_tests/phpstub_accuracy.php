<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class phpstub_accuracy_test_set extends cms_test_case
{
    public function testFunctionsNeeded()
    {
        $phpstub = cms_file_get_contents_safe(get_file_base() . '/sources_custom/phpstub.php', FILE_READ_LOCK);
        $matches = [];
        $num_matches = preg_match_all('#^function (\w+)\(#m', $phpstub, $matches);
        $declared_functions = [];
        for ($i = 0; $i < $num_matches; $i++) {
            $function = $matches[1][$i];
            $declared_functions[] = $function;
        }
        sort($declared_functions);

        $c = cms_file_get_contents_safe(get_file_base() . '/sources/hooks/systems/health_checks/install_env_php_lock_down.php', FILE_READ_LOCK);
        $num_matches = preg_match_all('#<<<END(.*)END;#Us', $c, $matches);
        $c = '';
        for ($i = 0; $i < $num_matches; $i++) {
            $c .= $matches[1][$i] . "\n";
        }
        $c = str_replace("\n", ' ', $c);
        $c = trim(preg_replace('#\s+#', ' ', $c));
        $required_functions = explode(' ', $c);
        sort($required_functions);

        foreach ($declared_functions as $function) {
            $this->assertTrue(in_array($function, $required_functions), 'Missing from install_env_php_lock_down.php? ' . $function);
        }

        foreach ($required_functions as $function) {
            $this->assertTrue((in_array($function, $declared_functions)) || (strpos($phpstub, "\n" . $function . "\n") !== false), 'Missing from phpstub.php? ' . $function);
        }

        if (get_param_integer('dev_check', 0) == 1) { // This extra switch let's us automatically find new functions in PHP we aren't coding for
            $will_never_define = [
                // Extensions, inconsistent prefix
                'read_exif_data',
                'hash',

                // FreeType needed
                'imagefttext',
                'imageftbbox',
            ];

            $defined = get_defined_functions();
            foreach ($defined['internal'] as $function) {
                if (!in_array($function, $will_never_define)) {
                    // Extensions
                    if (preg_match('#^(pdo|dom|exif|token|apache|zip|xmlwriter|xml|ocp|simplexml|session|pspell|posix|mysqli|imap|hash|ftp|filter|finfo|curl|ctype|libxml)_#', $function) != 0) {
                        continue;
                    }
                    if (preg_match('#^(bz|mb)#', $function) != 0) {
                        continue;
                    }

                    $this->assertTrue((in_array($function, $declared_functions)) || (strpos($phpstub, "\n" . $function . "\n") !== false), 'Should be defined? ' . $function);
                }
            }
        }
    }
}

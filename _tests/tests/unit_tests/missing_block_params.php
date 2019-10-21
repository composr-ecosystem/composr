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
class missing_block_params_test_set extends cms_test_case
{
    public function testMissingBlockParams()
    {
        require_code('files2');

        $need = array();

        $files = get_directory_contents(get_file_base() . '/sources/blocks', get_file_base() . '/sources/blocks', null, false, true, array('php'));
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe($path);
            $matches = array();
            $count = preg_match_all('/\$map\[\'([^\']+)\'\]/', $c, $matches);
            for ($i = 0; $i < $count; $i++) {
                if ($matches[1][$i] == 'block') {
                    continue;
                }
                if ($matches[1][$i] == 'cache') {
                    continue;
                }

                // Check param defined in block definition
                if ((preg_match('/\$info\[\'parameters\'\]\s*=\s*array\([^\)]*\'' . preg_quote($matches[1][$i]) . '\'[^\)]*\);/', $c) == 0)) {
                    $this->assertTrue(false, 'Missing block param... ' . basename($path, '.php') . ': ' . $matches[1][$i]);
                }

                // Check lang strings are all there
                $need[] = 'BLOCK_TRANS_NAME_' . basename($path, '.php');
                $need[] = 'BLOCK_' . basename($path, '.php') . '_DESCRIPTION';
                $need[] = 'BLOCK_' . basename($path, '.php') . '_USE';
                $need[] = 'BLOCK_' . basename($path, '.php') . '_PARAM_' . $matches[1][$i] . '_TITLE';
                $need[] = 'BLOCK_' . basename($path, '.php') . '_PARAM_' . $matches[1][$i];

                // Check for caching
                if (
                    (strpos($c, '$info[\'cache_on\']') !== false) && /* Has caching */
                    (strpos($c, '$info[\'cache_on\'] = array(') === false) && /* Has expression-based caching (not function-based caching) */
                    (strpos($c, '$info[\'cache_on\'] = \'$map\';') === false) && /* Doesn't just cache all parameters together */
                    (strpos($c, '$info[\'cache_on\'] = \'(count($_POST)==0) ? $map : null\';') === false) /* " */
                ) {
                    $pattern_1 = '#\$info\[\'cache_on\'\] = \'[^;]*array\([^;]*\\\\\'' . preg_quote($matches[1][$i]) . '\\\\\'#';
                    $pattern_2 = '#\$info\[\'cache_on\'\] = <<<\'PHP[^;]*array\([^;]*\'' . preg_quote($matches[1][$i]) . '\'#s';
                    if ((preg_match($pattern_1, $c) == 0) && (preg_match($pattern_2, $c) == 0)) {
                        $this->assertTrue(false, 'Block param (apparently) not cached... ' . basename($path, '.php') . ': ' . $matches[1][$i]);
                    }
                }
            }
        }

        $files = get_directory_contents(get_file_base() . '/lang/EN', get_file_base() . '/lang/EN', null, false, true, array('ini'));
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_BOM);

            foreach ($need as $i => $x) {
                if (strpos($c, $x . '=') !== false) {
                    unset($need[$i]);
                }
            }
        }

        foreach ($need as $i => $x) {
            $this->assertTrue(false, 'Missing language string: ' . $x);
        }
    }
}

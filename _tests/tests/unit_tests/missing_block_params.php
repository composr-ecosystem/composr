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
class missing_block_params_test_set extends cms_test_case
{
    public function testMissingBlockParams()
    {
        $need = array();
        $dh = opendir(get_file_base() . '/sources/blocks');
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.php') {
                $contents = file_get_contents(get_file_base() . '/sources/blocks/' . $f);
                $matches = array();
                $count = preg_match_all('/\$map\[\'([^\']+)\'\]/', $contents, $matches);
                for ($i = 0; $i < $count; $i++) {
                    if ($matches[1][$i] == 'block') {
                        continue;
                    }
                    if ($matches[1][$i] == 'cache') {
                        continue;
                    }

                    // Check param defined in block definition
                    if ((preg_match('/\$info\[\'parameters\'\]\s*=\s*array\([^\)]*\'' . preg_quote($matches[1][$i]) . '\'[^\)]*\);/', $contents) == 0)) {
                        $this->assertTrue(false, 'Missing block param... ' . basename($f, '.php') . ': ' . $matches[1][$i]);
                    }

                    // Check lang strings are all there
                    $need[] = 'BLOCK_TRANS_NAME_' . basename($f, '.php');
                    $need[] = 'BLOCK_' . basename($f, '.php') . '_DESCRIPTION';
                    $need[] = 'BLOCK_' . basename($f, '.php') . '_USE';
                    $need[] = 'BLOCK_' . basename($f, '.php') . '_PARAM_' . $matches[1][$i] . '_TITLE';
                    $need[] = 'BLOCK_' . basename($f, '.php') . '_PARAM_' . $matches[1][$i];

                    // Check for caching
                    if ((strpos($contents, '$info[\'cache_on\']') !== false) && (strpos($contents, '$info[\'cache_on\'] = array(') === false) && (strpos($contents, '$info[\'cache_on\'] = \'(count($_POST)==0)?$map:null\';') === false) && (strpos($contents, '$info[\'cache_on\'] = \'$map\';') === false)) {
                        $pattern = '/\$info\[\'cache_on\'\]\s*=\s*\'[^;]*\\\\\'' . preg_quote($matches[1][$i]) . '\\\\\'/';
                        if (preg_match($pattern, $contents) == 0) {
                            $this->assertTrue(false, 'Block param not cached... ' . basename($f, '.php') . ': ' . $matches[1][$i]);
                        }
                    }
                }
            }
        }

        $dh = opendir(get_file_base() . '/lang/EN');
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.ini') {
                $contents = file_get_contents(get_file_base() . '/lang/EN/' . $f);

                foreach ($need as $i => $x) {
                    if (strpos($contents, $x . '=') !== false) {
                        unset($need[$i]);
                    }
                }
            }
        }
        foreach ($need as $i => $x) {
            $this->assertTrue(false, 'Missing language string: ' . $x);
        }
    }
}

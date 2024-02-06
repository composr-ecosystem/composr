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
class missing_block_params_test_set extends cms_test_case
{
    public function testUnguardedBlockParams()
    {
        require_code('files2');

        $need = [];

        $files = [];
        $files = array_merge($files, get_directory_contents(get_file_base() . '/sources/blocks', get_file_base() . '/sources/blocks', null, false, true, ['php']));
        $files = array_merge($files, get_directory_contents(get_file_base() . '/sources_custom/blocks', get_file_base() . '/sources_custom/blocks', null, false, true, ['php']));
        $files = array_merge($files, get_directory_contents(get_file_base() . '/sources/miniblocks', get_file_base() . '/sources/miniblocks', null, false, true, ['php']));
        $files = array_merge($files, get_directory_contents(get_file_base() . '/sources_custom/miniblocks', get_file_base() . '/sources_custom/miniblocks', null, false, true, ['php']));
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe($path);

            $matches = [];
            $count = preg_match_all('/\$map\[\'([^\']+)\'\]/', $c, $matches);
            for ($i = 0; $i < $count; $i++) {
                $param = $matches[1][$i];

                $substrings = [
                    'isset($map[\'' . $param . '\'])',
                    'array_key_exists(\'' . $param . '\', $map)',
                    'empty($map[\'' . $param . '\'])',
                    '@cms_empty_safe($map[\'' . $param . '\'])',
                ];
                $has_guard = false;
                foreach ($substrings as $substring) {
                    if (strpos($c, $substring) !== false) {
                        $has_guard = true;
                    }
                }

                $this->assertTrue($has_guard, 'Unguarded param ' . $param . ' in ' . $path);
            }
        }
    }

    public function testUndefinedOrUndocumentedBlockParams()
    {
        require_code('files2');

        $need = [];

        $files = [];
        $files = array_merge($files, get_directory_contents(get_file_base() . '/sources/blocks', get_file_base() . '/sources/blocks', null, false, true, ['php']));
        $files = array_merge($files, get_directory_contents(get_file_base() . '/sources_custom/blocks', get_file_base() . '/sources_custom/blocks', null, false, true, ['php']));
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe($path);

            $need[] = 'BLOCK_TRANS_NAME_' . basename($path, '.php');
            $need[] = 'BLOCK_' . basename($path, '.php') . '_DESCRIPTION';
            $need[] = 'BLOCK_' . basename($path, '.php') . '_USE';

            $matches = [];
            $count = preg_match_all('/\$map\[\'([^\']+)\'\]/', $c, $matches);
            $params = [];
            for ($i = 0; $i < $count; $i++) {
                if ($matches[1][$i] == 'block') {
                    continue;
                }
                if ($matches[1][$i] == 'cache') {
                    continue;
                }
                $params[] = $matches[1][$i];
            }
            $params = array_unique($params);

            foreach ($params as $param) {
                // Check param defined in block definition
                if ((preg_match('/\$info\[\'parameters\'\]\s*=\s*\[[^\]]*\'' . preg_quote($param) . '\'[^\]]*];/', $c) == 0)) {
                    $this->assertTrue(false, 'Missing block param... ' . basename($path, '.php') . ': ' . $param);
                }

                // Check lang strings are all there
                $need[] = 'BLOCK_' . basename($path, '.php') . '_PARAM_' . $param . '_TITLE';
                $need[] = 'BLOCK_' . basename($path, '.php') . '_PARAM_' . $param;

                // Check for caching
                if (
                    (strpos($c, '$info[\'cache_on\']') !== false) && /* Has caching */
                    (strpos($c, '$info[\'cache_on\'] = [') === false) && /* Has expression-based caching (not function-based caching) */
                    (strpos($c, '$info[\'cache_on\'] = \'$map\';') === false) && /* Doesn't just cache all parameters together */
                    (strpos($c, '$info[\'cache_on\'] = \'(count($_POST)==0) ? $map : null\';') === false) /* " */
                ) {
                    $pattern_1 = '#\$info\[\'cache_on\'\] = \'[^;]*\[[^;]*\\\\\'' . preg_quote($param) . '\\\\\'#';
                    $pattern_2 = '#\$info\[\'cache_on\'\] = <<<\'PHP[^;]*\[[^;]*\'' . preg_quote($param) . '\'#s';
                    $pattern_3 = '#\$info\[\'cache_on\'\] = \'[^;]*[^;]*\$map\n#';
                    $pattern_4 = '#\$info\[\'cache_on\'\] = <<<\'PHP[^;]*[^;]*\$map[\n,]#s';
                    if ((preg_match($pattern_1, $c) == 0) && (preg_match($pattern_2, $c) == 0) && (preg_match($pattern_3, $c) == 0) && (preg_match($pattern_4, $c) == 0)) {
                        $this->assertTrue(false, 'Block param (apparently) not cached... ' . basename($path, '.php') . ': ' . $param);
                    }
                }
            }
        }

        $need = array_unique($need);

        $files = get_directory_contents(get_file_base() . '/lang/EN', get_file_base() . '/lang/EN', null, false, true, ['ini']);
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_BOM);

            foreach ($need as $i => $x) {
                if (strpos($c, $x . '=') !== false) {
                    unset($need[$i]);
                }
            }
        }

        foreach ($need as $i => $x) {
            if (strpos($path, '_custom') !== false) {
                $this->assertTrue(false, 'Missing language string: ' . $x);
            }
        }
    }
}

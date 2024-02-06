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
class blocks_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('zones2');

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);
    }

    public function testBlockCacheSignatureParsing()
    {
        $blocks = find_all_blocks();
        $map = ['param' => ''];
        foreach (array_keys($blocks) as $codename) {
            if (($this->only !== null) && ($this->only != $codename)) {
                continue;
            }

            list($object) = do_block_hunt_file($codename, $map);
            if ((is_object($object)) && (method_exists($object, 'caching_environment'))) {
                if ($this->debug) {
                    var_dump($codename);
                }

                $info = $object->caching_environment($map);
                if ((isset($info['cache_on'])) && (is_string($info['cache_on']))) {
                    $cache_on = $info['cache_on'];

                    if ($this->debug) {
                        var_dump($info['cache_on']);
                    }

                    $result = @eval('/* Evaluating for ' . $codename . ' */ return ' . $info['cache_on'] . ';');
                    $this->assertTrue(is_array($result) || $result === null, 'Failed block cache signature: ' . $codename . '... ' . $info['cache_on']); // Will always pass actually, as if there's a parse error eval will crash with a fatal error, all other errors are suppressed
                }
            }
        }
    }

    public function testBlocksNotExiting()
    {
        $blocks = find_all_blocks();
        foreach (array_keys($blocks) as $block) {
            if (($this->only !== null) && ($this->only != $block)) {
                continue;
            }

            $path = _get_block_path($block);
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_BOM);
            $this->assertTrue(strpos($c, 'warn_exit(') === false, 'warn_exit detected in ' . $path . '; should return do_template RED_ALERT instead.');
        }
    }

    public function testBlocksNotOverDefined()
    {
        require_code('caches3');
        erase_block_cache();

        require_all_lang();

        $standard = get_standard_block_parameters();

        $blocks = find_all_blocks();
        foreach ($blocks as $block => $type) {
            if (($this->only !== null) && ($this->only != $block)) {
                continue;
            }

            $parameters = get_block_parameters($block, true);
            $this->assertTrue(count(array_unique($parameters)) == count($parameters), 'Duplicated parameters in ' . $block);

            foreach ($standard as $param) {
                $str = 'BLOCK_' . $block . '_PARAM_' . $param . '_TITLE';
                $this->assertTrue(do_lang($str, null, null, null, null, false) === null, $str . ' missing');
            }
        }
    }
}

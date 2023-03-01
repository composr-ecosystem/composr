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

/*EXTRA FUNCTIONS: str_word_count*/

/**
 * Composr test case class (unit testing).
 */
class string_functions_test_set extends cms_test_case
{
    public function testASCIIConsistency()
    {
        require_code('global4');

        $str = 'this is a Test string';

        $funcs = [
            'cms_urlencode' => 'urlencode',
            'cms_rawurlencode' => 'rawurlencode',
            'cms_escapeshellarg' => 'escapeshellarg',
            'cms_date' => 'date',
            'cms_mb_strlen' => 'strlen',
            'cms_mb_strtolower' => 'strtolower',
            'cms_mb_strtoupper' => 'strtoupper',
            'cms_mb_ucfirst' => 'ucfirst',
            'cms_mb_ucwords' => 'ucwords',
            'cms_mb_chunk_split' => 'chunk_split',
            'cms_mb_str_split' => 'str_split',
            'cms_strtoupper_ascii' => 'strtoupper',
            'cms_strtolower_ascii' => 'strtolower',
            'cms_lcfirst_ascii' => 'lcfirst',
            'cms_ucfirst_ascii' => 'ucfirst',
            'cms_ucwords_ascii' => 'ucwords',
        ];
        foreach ($funcs as $func_a => $func_b) {
            $a = call_user_func($func_a, $str);
            $b = call_user_func($func_b, $str);
            $this->assertTrue($a == $b, 'Inconsistency in ' . $func_a . '; ' . json_encode($a) . ' vs ' . json_encode($b));
        }

        $a = cms_mb_str_word_count($str, 0);
        $b = str_word_count($str, 0);
        $this->assertTrue($a == $b, json_encode($a) . ' vs ' . json_encode($b));
        $a = cms_mb_str_word_count($str, 1);
        $b = str_word_count($str, 1);
        $this->assertTrue($a == $b, json_encode($a) . ' vs ' . json_encode($b));
        $a = cms_mb_str_word_count($str, 2);
        $b = str_word_count($str, 2);
        $this->assertTrue($a == $b, json_encode($a) . ' vs ' . json_encode($b));

        $this->assertTrue(cms_mb_substr($str, 3, 2) == substr($str, 3, 2));

        // String comparison isn't identical so we just do a set of simple cases
        $str_a = 'AbC';
        $str_b = 'Abd';
        $a = cms_mb_strcmp($str_a, $str_b);
        $b = $this->clamp_cmp_returns(strcmp($str_a, $str_b));
        $this->assertTrue($a == $b, strval($a) . ' vs ' . strval($b));
        $a = cms_mb_strcmp($str_b, $str_a);
        $b = $this->clamp_cmp_returns(strcmp($str_b, $str_a));
        $this->assertTrue($a == $b, strval($a) . ' vs ' . strval($b));
        $a = cms_mb_strcmp($str_a, $str_a);
        $b = $this->clamp_cmp_returns(strcmp($str_a, $str_a));
        $this->assertTrue($a == $b, strval($a) . ' vs ' . strval($b));
        $a = cms_mb_strnatcmp($str_a, $str_b);
        $b = $this->clamp_cmp_returns(strnatcmp($str_a, $str_b));
        $this->assertTrue($a == $b, strval($a) . ' vs ' . strval($b));
        $a = cms_mb_strnatcmp($str_b, $str_a);
        $b = $this->clamp_cmp_returns(strnatcmp($str_b, $str_a));
        $this->assertTrue($a == $b, strval($a) . ' vs ' . strval($b));
        $a = cms_mb_strnatcmp($str_a, $str_a);
        $b = $this->clamp_cmp_returns(strnatcmp($str_a, $str_a));
        $this->assertTrue($a == $b, strval($a) . ' vs ' . strval($b));
    }

    protected function clamp_cmp_returns($cmp)
    {
        if ($cmp <= -1) {
            return -1;
        }
        if ($cmp >= 1) {
            return 1;
        }
        return 0;
    }
}

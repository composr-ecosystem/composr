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
class lang_grammar_test_set extends cms_test_case
{
    public function testUnbalancedSmartQuotes()
    {
        require_code('lang2');
        require_code('lang_compile');

        $lang_files = get_lang_files(fallback_lang());
        foreach (array_keys($lang_files) as $lang_file) {
            $map = get_lang_file_map(fallback_lang(), $lang_file, false, false) + get_lang_file_map(fallback_lang(), $lang_file, true, false);
            foreach ($map as $key => $value) {
                $this->assertTrue(preg_match('#&ldquo;[^&]*&ldquo;|&lsquo;[^&]*&lsquo;#', $value) == 0, 'Unmatched smart quotes in ' . $lang_file . ':' . $key);
            }
        }
    }
}

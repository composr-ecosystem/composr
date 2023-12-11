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
class lang_descriptions_test_set extends cms_test_case
{
    public function testDescriptionsMatch()
    {
        require_code('lang_compile');
        require_code('lang2');

        $lang_files = get_lang_files(fallback_lang());
        foreach (array_keys($lang_files) as $file) {
            $path = get_file_base() . '/lang/EN/' . $file . '.ini';
            if (!is_file($path)) {
                $path = get_file_base() . '/lang_custom/EN/' . $file . '.ini';
            }

            $strings = array();
            _get_lang_file_map($path, $strings, 'strings', false, true, fallback_lang());

            $descriptions = array();
            _get_lang_file_map($path, $descriptions, 'descriptions', false, true, fallback_lang());

            foreach (array_keys($descriptions) as $key) {
                $this->assertTrue(array_key_exists($key, $strings), 'Unrecognised description key, ' . $key);
            }
        }
    }
}

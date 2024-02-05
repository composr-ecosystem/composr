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
class lang_administrative_split_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('string_scan');
        require_code('lang_compile');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);
    }

    public function testSplitStringScanComplete()
    {
        $lang = fallback_lang();
        list($just_lang_strings_admin, $just_lang_strings_non_admin, $lang_strings_shared, $lang_strings_unknown, $all_strings_in_lang) = string_scan($lang);

        foreach (array_merge($lang_strings_shared, $lang_strings_unknown) as $str) {
            $this->assertTrue(false, $str . ': either not defined as administrative/non-administrative, or defined as both');
        }
    }

    public function testSplitStringScanInconsistencies()
    {
        $lang = fallback_lang();
        list($just_lang_strings_admin, $just_lang_strings_non_admin, , , $all_strings_in_lang) = string_scan($lang, false, false);

        foreach ($just_lang_strings_admin as $str) {
            $this->assertTrue(array_key_exists($str, $all_strings_in_lang), 'string_scan specifies a lang string which does not exist in a bundled addon: ' . $str);
        }
        foreach ($just_lang_strings_non_admin as $str) {
            $this->assertTrue(array_key_exists($str, $all_strings_in_lang), 'string_scan specifies a lang string which does not exist in a bundled addon: ' . $str);
        }
    }

    public function testNoEmptyLangStrings()
    {
        $d = get_file_base() . '/lang/' . fallback_lang();
        $dh = opendir($d);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.ini') {
                $strings = get_lang_file_map(fallback_lang(), basename($file, '.ini'), true);
                foreach ($strings as $key => $val) {
                    if (in_array($key, ['date_withinweek_joiner', '_HTTP_REDIRECT_PROBLEM_INSTALLING']/*We'll allow these ones*/)) {
                        continue;
                    }

                    $this->assertTrue(trim($val) != '', 'Transifex does not support empty language strings, ' . $key . ' in ' . $file);
                }
            }
        }
        closedir($dh);
    }
}

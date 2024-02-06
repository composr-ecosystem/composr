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
class lang_string_special_validity_test_set extends cms_test_case
{
    public function testValidity()
    {
        require_all_lang();

        $langs = find_all_langs();

        $regexp_pairs = [
            ['TICKET_SIMPLE_SUBJECT_regexp', ['TICKET_SIMPLE_SUBJECT_reply', 'x', 'x', 'x']],
            ['TICKET_SIMPLE_MAIL_reply_regexp', ['TICKET_SIMPLE_MAIL_reply', 'x', 'x', 'x']],
            ['MAILING_LIST_SIMPLE_SUBJECT_new_regexp', ['MAILING_LIST_SIMPLE_SUBJECT_new', 'x', 'x', 'x']],
            ['MAILING_LIST_SIMPLE_SUBJECT_reply_regexp', ['MAILING_LIST_SIMPLE_SUBJECT_reply', 'x', 'x', 'x']],
            ['MAILING_LIST_SIMPLE_MAIL_regexp', ['MAILING_LIST_SIMPLE_MAIL_new', 'x', 'x', 'x']],
            ['MAILING_LIST_SIMPLE_MAIL_regexp', ['MAILING_LIST_SIMPLE_MAIL_reply', 'x', 'x', 'x']],
        ];
        foreach (array_keys($langs) as $lang) {
            foreach ($regexp_pairs as $_parts) {
                list($_regexp, $_str) = $_parts;

                $regexp = $this->do_lang($_regexp, null, null, null, $lang);
                if ($regexp === null) {
                    continue; // Not defined for language
                }
                if (is_array($_str)) {
                    $_str[] = $lang;
                    $str = call_user_func_array([$this, 'do_lang'], $_str);
                } else {
                    $str = $this->do_lang($_str, null, null, null, $lang);
                }
                if ($str === null) {
                    continue; // Not defined for language
                }
                $this->assertTrue(preg_match('#' . $regexp . '#', $str) != 0, $_regexp . ' (' . $regexp . ') did not match ' . $str . ', for ' . $lang);
            }
        }

        $substring_pairs = [
            ['BLOCK_IND_EITHER', ['BLOCK_PARAM_cache', null, null, null]],
            ['BLOCK_IND_HOOKTYPE', ['BLOCK_main_custom_gfx_PARAM_param', null, null, null]],
            //['BLOCK_IND_DEFAULT', 'default: test'],
            ['BLOCK_IND_SUPPORTS_COMCODE', 'COMCODE_TAG_indent_EMBED'],
            ['BLOCK_IND_ADVANCED', 'COMCODE_TAG_box_PARAM_options'],
            ['BLOCK_IND_WHETHER', 'COMCODE_TAG_codebox_PARAM_numbers'],
            ['SPACER_POST_MATCHER', 'SPACER_POST'],
        ];
        foreach (array_keys($langs) as $lang) {
            foreach ($substring_pairs as $_parts) {
                list($_substring, $_str) = $_parts;

                $substring = $this->do_lang($_substring, null, null, null, $lang);
                if ($substring === null) {
                    continue; // Not defined for language
                }
                if (is_array($_str)) {
                    $_str[] = $lang;
                    $str = call_user_func_array([$this, 'do_lang'], $_str);
                } else {
                    $str = $this->do_lang($_str, null, null, null, $lang);
                }
                if ($str === null) {
                    continue; // Not defined for language
                }
                $this->assertTrue(stripos($str, $substring) !== false, $_substring . ' (' . $substring . ') was not found in ' . $str . ', for ' . $lang);
            }
        }

        $short_strings = [
            'JANUARY_SHORT',
            'FEBRUARY_SHORT',
            'MARCH_SHORT',
            'APRIL_SHORT',
            'MAY_SHORT',
            'JUNE_SHORT',
            'JULY_SHORT',
            'AUGUST_SHORT',
            'SEPTEMBER_SHORT',
            'OCTOBER_SHORT',
            'NOVEMBER_SHORT',
            'DECEMBER_SHORT',
            'MONDAY_SHORT',
            'TUESDAY_SHORT',
            'WEDNESDAY_SHORT',
            'THURSDAY_SHORT',
            'FRIDAY_SHORT',
            'SATURDAY_SHORT',
            'SUNDAY_SHORT',
        ];
        foreach (array_keys($langs) as $lang) {
            foreach ($short_strings as $_str) {
                $str = $this->do_lang($_str, null, null, null, $lang);
                if ($str === null) {
                    continue; // Not defined for language
                }
                $this->assertTrue(cms_mb_strlen($str) <= 4, $_str . ' is too long, for ' . $lang);
            }
        }

        foreach (array_keys($langs) as $lang) {
            $result = $this->do_lang('charset', null, null, null, $lang);
            if ($result === null) {
                continue; // Not defined for language
            }
            $this->assertTrue($result == 'utf-8', 'charset is not in utf-8, for ' . $lang);

            $result = $this->do_lang('dir', null, null, null, $lang);
            if ($result === null) {
                continue; // Not defined for language
            }
            $this->assertTrue($result == 'ltr' || $result == 'rtl', 'dir is not a valid value, for ' . $lang);

            $result = $this->do_lang('en_left', null, null, null, $lang);
            if ($result === null) {
                continue; // Not defined for language
            }
            $this->assertTrue($result == 'left' || $result == 'right', 'en_left is not a valid value, for ' . $lang);

            $result = $this->do_lang('en_right', null, null, null, $lang);
            if ($result === null) {
                continue; // Not defined for language
            }
            $this->assertTrue($result == 'left' || $result == 'right', 'en_right is not a valid value, for ' . $lang);
        }
    }

    protected function do_lang($str, $a, $b, $c, $lang)
    {
        $ret = do_lang($str, $a, $b, $c, $lang);
        if (($lang != fallback_lang()) && ($ret == do_lang($str, $a, $b, $c, fallback_lang()))) {
            return null; // Not defined for language
        }
        return $ret;
    }
}

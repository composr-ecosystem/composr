<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class lang_duplication_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('lang_compile');
        require_code('lang2');
    }

    public function testLangDuplication()
    {
        $verbose = false;

        $vals = [];

        $num = 0;

        $all_keys = [];

        $exceptions = [
            'GOOGLE_MAP',
            'GOOGLE_MAP_KEY',
        ];

        $lang_files = get_lang_files(fallback_lang());
        foreach (array_keys($lang_files) as $file) {
            $path = get_file_base() . '/lang/EN/' . $file . '.ini';
            if (!is_file($path)) {
                $path = get_file_base() . '/lang_custom/EN/' . $file . '.ini';
            }

            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

            $c = preg_replace('#^.*\[strings\]#s', '', $c); // Remove descriptions section

            $input = [];
            _get_lang_file_map($path, $input, 'strings', false, true, 'EN');

            foreach ($input as $key => $val) {
                if (in_array($key, $exceptions)) {
                    continue;
                }

                if (isset($vals[$val])) {
                    if ($this->debug) {
                        @print('<p><strong>' . escape_html($val) . '</strong>:<br />' . escape_html($file . ':' . $key . ' = ' . implode(' = ', $vals[$val])) . '</p>');
                    }
                } else {
                    $vals[$val] = [];
                }
                $vals[$val][] = $file . ':' . $key;

                $this->assertTrue(!isset($all_keys[$key]), 'Duplication for key ' . $key . ' string');

                $all_keys[$key] = true;

                // Check for duplication within the file...
                $this->assertTrue(substr_count($c, "\n" . $key . '=') == 1, 'Duplication for key ' . $key . ' string within a single file');
            }

            $num += count($input);
        }

        $num_unique = count($vals);

        $percentage_duplicated = 100.0 - 100.0 * floatval($num_unique) / floatval($num);

        $this->assertTrue($percentage_duplicated < 9.0, 'Overall heavy duplication'); // Ideally we'd lower it, but 6% is what it was when this test was written. We're testing it's not getting worse.

        // Find if there is any unnecessary underscoring
        /*foreach (array_keys($all_keys) as $key) {     Was useful once, but there are reasonable cases remaining
            if ((substr($key, 0, 1) == '_') && (cms_strtoupper_ascii($key) == $key) && (!isset($all_keys[substr($key, 1)]))) {
                $this->assertTrue(false, 'Unnecessary prefixing of ' . $key);
            }
        }*/

        // Find out what is duplicated
        foreach ($vals as $val => $multiple) {
            if (count($multiple) == 1) {
                unset($vals[$val]);
            } else {
                if (count(array_unique($vals[$val])) != count($vals[$val])) {
                    $this->assertTrue(false, 'Exact duplication of key&val ' . $val . ' string');
                }
            }
        }
        //@var_dump($vals);exit();
    }
}

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
class lang_duplication_test_set extends cms_test_case
{
    public function setUp()
    {
        require_code('lang_compile');
        require_code('lang2');

        parent::setUp();
    }

    public function testLangDuplication()
    {
        $verbose = false;

        $vals = array();

        $num = 0;

        $all_keys = array();

        $exceptions = array(
            'GOOGLE_MAP',
            'GOOGLE_MAP_KEY',
            'CONFIG_OPTION_google_map_key',
        );

        $lang_files = get_lang_files(fallback_lang());
        foreach (array_keys($lang_files) as $file) {
            $path = get_file_base() . '/lang/EN/' . $file . '.ini';
            if (!is_file($path)) {
                $path = get_file_base() . '/lang_custom/EN/' . $file . '.ini';
            }

            $c = file_get_contents($path);

            $c = preg_replace('#^.*\[strings\]#s', '', $c); // Remove descriptions section

            $input = array();
            _get_lang_file_map($path, $input, 'strings', false, true, 'EN');

            foreach ($input as $key => $val) {
                if (in_array($key, $exceptions)) {
                    continue;
                }

                if (isset($vals[$val])) {
                    if (get_param_integer('debug', 0) == 1) {
                        @print('<p><strong>' . escape_html($val) . '</strong>:<br />' . escape_html($file . ':' . $key . ' = ' . implode(' = ', $vals[$val])) . '</p>');
                    }
                } else {
                    $vals[$val] = array();
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

        $this->assertTrue($percentage_duplicated < 8.2, 'Overall heavy duplication'); // Ideally we'd lower it, but 6% is what it was when this test was written. We're testing it's not getting worse.

        // Find if there is any unnecessary underscoring
        /*foreach (array_keys($all_keys) as $key) {     Was useful once, but there are reasonable cases remaining
            if ((substr($key, 0, 1) == '_') && (strtoupper($key) == $key) && (!isset($all_keys[substr($key, 1)]))) {
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

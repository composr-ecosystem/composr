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
class lang_missing_parameters_test_set extends cms_test_case
{
    public function testNoMissingParams()
    {
        disable_php_memory_limit();

        require_code('files2');

        require_all_lang();

        // Can only test simple call patterns

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        foreach ($files as $path) {
            $c = file_get_contents(get_file_base() . '/' . $path);

            // Regexps cannot do nesting, so strip common stuff that gets in our way
            $c = preg_replace('#\[\'\w+\'\]#', '', $c);
            $c = preg_replace('#is_object\(\$\w+\) \? \$\w+->evaluate\(\) : (\$\w+)#', '$1', $c);
            if (strpos($c, '()') !== false) {
                $c = str_replace('()', '', $c);
            }
            foreach (['get_username', 'integer_format', 'float_format', 'strval', 'strip_comcode', 'escape_html'] as $strippable_func) {
                if (strpos($c, $strippable_func) !== false) {
                    $c = preg_replace('#' . $strippable_func . '\(([^()]*)\)#', '$1', $c);
                }
            }

            $matches = [];
            $num_matches = preg_match_all('#do_lang(_tempcode)?\(\'(\w+)\'(, [^(),]*)?(, [^(),]*)?(, [^(),]*)?\)#', $c, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            for ($i = 0; $i < $num_matches; $i++) {
                $num_params = 0;
                if (!empty($matches[$i][3][0])) {
                    $num_params++;
                    if (!empty($matches[$i][4][0])) {
                        $num_params++;
                        if (!empty($matches[$i][5][0])) {
                            $num_params += 40; // May be an array, so assume lots in it
                        }
                    }
                }
                $pass = $this->_test_lang_string($matches[$i][2][0], $num_params, $matches[$i][0][1], $c, $path);
                if (($this->debug) && (!$pass)) {
                    exit();
                }
            }

            $matches = [];
            $num_matches = preg_match_all('#do_lang(_tempcode)?\(\'(\w+)\', [^(),]*, [^(),]*, \[([^(),]*(, [^(),]*)*)\]\)#s', $c, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            for ($i = 0; $i < $num_matches; $i++) {
                $num_params = 2 + substr_count($matches[$i][3][0], ',') + 1;
                $pass = $this->_test_lang_string($matches[$i][2][0], $num_params, $matches[$i][0][1], $c, $path);
                if (($this->debug) && (!$pass)) {
                    exit();
                }
            }
        }
    }

    protected function _test_lang_string($string_codename, $num_params, $offset, $c, $path)
    {
        // Exceptions
        if (in_array($string_codename, [
            'AUTO_SPACER_TAKE_RESPONSIBILITY',
            'FLOOD_CONTROL_BLOCKED',
            'INTRO_POST_DEFAULT',
        ])) {
            return true;
        }

        $line_num = substr_count($c, "\n", 0, $offset) + 1;

        $params = array_fill(1, $num_params, 'xxx');
        $params[0] = $string_codename;
        ksort($params);

        $result = call_user_func_array('do_lang', $params);

        $pass = preg_match('#\{\d+\}#', $result) == 0;
        $this->assertTrue($pass, 'Issue with call to \'' . $string_codename . '\' in ' . $path . ' on line ' . strval($line_num));

        return $pass;
    }
}

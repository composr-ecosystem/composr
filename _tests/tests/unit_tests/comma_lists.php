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
class comma_lists_test_set extends cms_test_case
{
    public function testSerialize()
    {
        // Empty test
        $map = [
        ];
        $str = '';
        $got = comma_list_arr_to_str($map);
        $this->assertTrue($got == $str, 'Got ' . $got);

        // Test various cases
        $map = [
            'a' => 'b', // Simple
            '' => 'lorem', // Blank key
            'foo' => 'foo,bar', // Comma
            3 => 'hello=this=that', // Equals
            4 => 'foobar', // Numeric key
            5 => '', // Totally blank
        ];
        $str = '=lorem,a=b,foo=foo\,bar,hello=this\\=that,4=foobar,5=';
        $got = comma_list_arr_to_str($map);
        $this->assertTrue($got == $str, 'Got ' . $got);
    }

    public function testDeserialize()
    {
        // Empty test
        $map = [
        ];
        $str = '';
        $got = comma_list_str_to_arr($str);
        ksort($map);
        ksort($got);
        $this->assertTrue($got == $map, 'Got ' . var_export($got, true));

        // Test various cases with $block_symbol_style off
        $str = '=lorem,a=b,foo=foo\,bar,3=hello=this,4=foobar,,=lorem2,x=';
        $map = [
            'a' => 'b', // Simple
            '' => 'lorem', // Blank key
            'foo' => 'foo,bar', // Comma
            3 => 'hello=this', // Equals
            4 => 'foobar', // Numeric key
            5 => '', // Totally blank
            6 => 'lorem2', // Multiple blank keys
            'x' => '', // Blank value
        ];
        $got = comma_list_str_to_arr($str);
        ksort($map);
        ksort($got);
        $this->assertTrue($got == $map, 'Got ' . var_export($got, true));

        // Test various cases with $block_symbol_style on
        $str = '=lorem,a=b,foo=foo\,bar,3=hello=this,4=foobar,,=lorem2,x=';
        $map = [
            'a=b', // Simple
            '=lorem', // Blank key
            'foo=foo\,bar', // Comma
            '3=hello=this', // Equals
            '4=foobar', // Numeric key
            '', // Totally blank
            '=lorem2', // Multiple blank keys
            'x=', // Blank value
        ];
        $got = comma_list_str_to_arr($str, true);
        sort($map);
        sort($got);
        $this->assertTrue($got == $map, 'Got ' . var_export($got, true));
    }
}

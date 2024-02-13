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
class database_query_parameterised_test_set extends cms_test_case
{
    public function testParameterisation()
    {
        if (strpos(get_db_type(), 'mysql') === false) {
            $this->assertTrue(false, 'Test only works on MySQL database backends');
            return;
        }

        $parameters = [
            'int' => 1,
            'float' => 1.34,
            'bool' => true,
            'null' => null,
            'string_dangerous_1' => "let's be unsafe",
            'string_dangerous_2' => 'a\b',
        ];

        $tests = [
            // Table prefix
            "SELECT * FROM {prefix}foobar" => "SELECT * FROM " . get_table_prefix() . "foobar",

            // Quotes Omitted
            "SELECT * FROM foobar WHERE x={int}" => "SELECT * FROM foobar WHERE x=1",
            "SELECT * FROM foobar WHERE x={float}" => "SELECT * FROM foobar WHERE x=1.3400000000",
            "SELECT * FROM foobar WHERE x={bool}" => "SELECT * FROM foobar WHERE x=1",
            "SELECT * FROM foobar WHERE x={null}" => "SELECT * FROM foobar WHERE x=NULL",
            "SELECT * FROM foobar WHERE x={string_dangerous_1}" => "SELECT * FROM foobar WHERE x='let\'s be unsafe'",
            "SELECT * FROM foobar WHERE x={string_dangerous_2}" => "SELECT * FROM foobar WHERE x='a\\\\b'",

            // Quotes given
            "SELECT * FROM foobar WHERE x='{int}'" => "SELECT * FROM foobar WHERE x='1'",
            "SELECT * FROM foobar WHERE x='{float}'" => "SELECT * FROM foobar WHERE x='1.3400000000'",
            "SELECT * FROM foobar WHERE x='{bool}'" => "SELECT * FROM foobar WHERE x='1'",
            "SELECT * FROM foobar WHERE x='{null}'" => "SELECT * FROM foobar WHERE x=NULL",
            "SELECT * FROM foobar WHERE x='{string_dangerous_1}'" => "SELECT * FROM foobar WHERE x='let\'s be unsafe'",
            "SELECT * FROM foobar WHERE x='{string_dangerous_2}'" => "SELECT * FROM foobar WHERE x='a\\\\b'",

            // Mixing
            "SELECT * FROM foobar WHERE x='{string_dangerous_1}' OR x={string_dangerous_2} OR 1=1" => "SELECT * FROM foobar WHERE x='let\'s be unsafe' OR x='a\\\\b' OR 1=1",
            "SELECT * FROM foobar WHERE a='b' OR x='{string_dangerous_2}'" => "SELECT * FROM foobar WHERE a='b' OR x='a\\\\b'",

            // Missing params
            "SELECT * FROM foobar WHERE x={missing}" => "SELECT * FROM foobar WHERE x={missing}",
            "SELECT * FROM foobar WHERE x='{missing}'" => "SELECT * FROM foobar WHERE x='{missing}'",
        ];

        foreach ($tests as $before => $expected) {
            $got = $GLOBALS['SITE_DB']->_query_parameterised($before, $parameters);
            $this->assertTrue($got == $expected, 'Incorrect result for: ' . $before . '; got: ' . $got);
        }
    }
}

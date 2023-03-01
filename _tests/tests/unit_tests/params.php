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
class params_test_set extends cms_test_case
{
    public function testPostParamInteger()
    {
        require_code('failure');
        set_throw_errors(true);

        $tests = [
            'ppi_number_no_default' => [
                'in' => '123',
                'default' => false,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'ppi_numberish_no_default' => [
                'in' => '123xxx',
                'default' => false,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'ppi_missing_no_default' => [
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Missing input
            ],
            'ppi_empty_string_no_default' => [
                'in' => '',
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Missing input
            ],
            'ppi_invalid_string_no_default' => [
                'in' => 'xxx',
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Invalid input
            ],

            'ppi_number_null_default' => [
                'in' => '123',
                'default' => null,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'ppi_numberish_null_default' => [
                'in' => '123xxx',
                'default' => null,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'ppi_missing_null_default' => [
                'default' => null,
                'expect_out' => null,
                'expect_exception' => false,
            ],
            'ppi_empty_string_null_default' => [
                'in' => '',
                'default' => null,
                'expect_out' => null,
                'expect_exception' => false,
            ],
            'ppi_invalid_string_null_default' => [
                'in' => 'xxx',
                'default' => null,
                'expect_out' => null,
                'expect_exception' => true, // Invalid input
            ],

            'ppi_number_numeric_default' => [
                'in' => '123',
                'default' => 456,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'ppi_numberish_numeric_default' => [
                'in' => '123xxx',
                'default' => 456,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'ppi_missing_numeric_default' => [
                'default' => 456,
                'expect_out' => 456,
                'expect_exception' => false,
            ],
            'ppi_empty_string_numeric_default' => [
                'in' => '',
                'default' => 456,
                'expect_out' => 456,
                'expect_exception' => false,
            ],
            'ppi_invalid_string_numeric_default' => [
                'in' => 'xxx',
                'default' => 456,
                'expect_out' => null,
                'expect_exception' => true, // Invalid input
            ],
        ];

        foreach ($tests as $param_name => $test_details) {
            unset($_POST[$param_name]);
            if (array_key_exists('in', $test_details)) {
                $_POST[$param_name] = $test_details['in'];
            }

            $got_exception = false;
            try {
                $got = post_param_integer($param_name, $test_details['default']);
                if (!$test_details['expect_exception']) {
                    $this->assertTrue($got === $test_details['expect_out'], 'Got unexpected output for ' . $param_name . ' test (' . var_export($got, true) . ')');
                }
            } catch (Exception $e) {
                $got_exception = true;
            }

            if ($test_details['expect_exception']) {
                $this->assertTrue($got_exception, 'Missing expected exception for ' . $param_name . ' test');
            } else {
                $this->assertTrue(!$got_exception, 'Got unexpected exception for ' . $param_name . ' test');
            }
        }
    }

    public function testGetParamInteger()
    {
        require_code('failure');
        set_throw_errors(true);

        $tests = [
            'gpi_number_no_default' => [
                'in' => '123',
                'default' => false,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'gpi_numberish_no_default' => [
                'in' => '123xxx',
                'default' => false,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'gpi_missing_no_default' => [
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Missing input
            ],
            'gpi_empty_string_no_default' => [
                'in' => '',
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Missing input
            ],
            'gpi_invalid_string_no_default' => [
                'in' => 'xxx',
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Invalid input
            ],

            'gpi_number_null_default' => [
                'in' => '123',
                'default' => null,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'gpi_numberish_null_default' => [
                'in' => '123xxx',
                'default' => null,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'gpi_missing_null_default' => [
                'default' => null,
                'expect_out' => null,
                'expect_exception' => false,
            ],
            'gpi_empty_string_null_default' => [
                'in' => '',
                'default' => null,
                'expect_out' => null,
                'expect_exception' => false,
            ],
            'gpi_invalid_string_null_default' => [
                'in' => 'xxx',
                'default' => null,
                'expect_out' => null,
                'expect_exception' => true, // Invalid input
            ],

            'gpi_number_numeric_default' => [
                'in' => '123',
                'default' => 456,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'gpi_numberish_numeric_default' => [
                'in' => '123xxx',
                'default' => 456,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'gpi_missing_numeric_default' => [
                'default' => 456,
                'expect_out' => 456,
                'expect_exception' => false,
            ],
            'gpi_empty_string_numeric_default' => [
                'in' => '',
                'default' => 456,
                'expect_out' => 456,
                'expect_exception' => false,
            ],
            'gpi_invalid_string_numeric_default' => [
                'in' => 'xxx',
                'default' => 456,
                'expect_out' => null,
                'expect_exception' => true, // Invalid input
            ],
        ];

        foreach ($tests as $param_name => $test_details) {
            unset($_GET[$param_name]);
            if (array_key_exists('in', $test_details)) {
                $_GET[$param_name] = $test_details['in'];
            }

            $got_exception = false;
            try {
                $got = get_param_integer($param_name, $test_details['default']);
                if (!$test_details['expect_exception']) {
                    $this->assertTrue($got === $test_details['expect_out'], 'Got unexpected output for ' . $param_name . ' test (' . var_export($got, true) . ')');
                }
            } catch (Exception $e) {
                $got_exception = true;
            }

            if ($test_details['expect_exception']) {
                $this->assertTrue($got_exception, 'Missing expected exception for ' . $param_name . ' test');
            } else {
                $this->assertTrue(!$got_exception, 'Got unexpected exception for ' . $param_name . ' test');
            }
        }
    }

    public function testEitherParamInteger()
    {
        require_code('failure');
        set_throw_errors(true);

        $tests = [
            'epi_number_no_default' => [
                'in' => '123',
                'default' => false,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'epi_numberish_no_default' => [
                'in' => '123xxx',
                'default' => false,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'epi_missing_no_default' => [
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Missing input
            ],
            'epi_empty_string_no_default' => [
                'in' => '',
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Missing input
            ],
            'epi_invalid_string_no_default' => [
                'in' => 'xxx',
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Invalid input
            ],

            'epi_number_null_default' => [
                'in' => '123',
                'default' => null,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'epi_numberish_null_default' => [
                'in' => '123xxx',
                'default' => null,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'epi_missing_null_default' => [
                'default' => null,
                'expect_out' => null,
                'expect_exception' => false,
            ],
            'epi_empty_string_null_default' => [
                'in' => '',
                'default' => null,
                'expect_out' => null,
                'expect_exception' => false,
            ],
            'epi_invalid_string_null_default' => [
                'in' => 'xxx',
                'default' => null,
                'expect_out' => null,
                'expect_exception' => true, // Invalid input
            ],

            'epi_number_numeric_default' => [
                'in' => '123',
                'default' => 456,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'epi_numberish_numeric_default' => [
                'in' => '123xxx',
                'default' => 456,
                'expect_out' => 123,
                'expect_exception' => false,
            ],
            'epi_missing_numeric_default' => [
                'default' => 456,
                'expect_out' => 456,
                'expect_exception' => false,
            ],
            'epi_empty_string_numeric_default' => [
                'in' => '',
                'default' => 456,
                'expect_out' => 456,
                'expect_exception' => false,
            ],
            'epi_invalid_string_numeric_default' => [
                'in' => 'xxx',
                'default' => 456,
                'expect_out' => null,
                'expect_exception' => true, // Invalid input
            ],
        ];

        foreach ($tests as $param_name => $test_details) {
            unset($_POST[$param_name . '__POST']);
            unset($_GET[$param_name . '__GET']);
            if (array_key_exists('in', $test_details)) {
                $_POST[$param_name . '__POST'] = $test_details['in'];
            }

            $got_exception = false;
            try {
                $got = either_param_integer($param_name . '__POST', $test_details['default']);
                if (!$test_details['expect_exception']) {
                    $this->assertTrue($got === $test_details['expect_out'], 'Got unexpected output for ' . $param_name . '__POST test');
                }
            } catch (Exception $e) {
                $got_exception = true;
            }

            if ($test_details['expect_exception']) {
                $this->assertTrue($got_exception, 'Missing expected exception for ' . $param_name . '__POST test');
            } else {
                $this->assertTrue(!$got_exception, 'Got unexpected exception for ' . $param_name . '__POST test');
            }

            unset($_POST[$param_name . '__POST']);
            unset($_GET[$param_name . '__GET']);
            if (array_key_exists('in', $test_details)) {
                $_GET[$param_name . '__GET'] = $test_details['in'];
            }

            $got_exception = false;
            try {
                $got = either_param_integer($param_name . '__GET', $test_details['default']);
                if (!$test_details['expect_exception']) {
                    $this->assertTrue($got === $test_details['expect_out'], 'Got unexpected output for ' . $param_name . '__GET test (' . var_export($got, true) . ')');
                }
            } catch (Exception $e) {
                $got_exception = true;
            }

            if ($test_details['expect_exception']) {
                $this->assertTrue($got_exception, 'Missing expected exception for ' . $param_name . '__GET test');
            } else {
                $this->assertTrue(!$got_exception, 'Got unexpected exception for ' . $param_name . '__GET test');
            }
        }
    }

    public function testPostParamString()
    {
        require_code('failure');
        set_throw_errors(true);

        $tests = [
            'pps_missing_no_default' => [
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Missing input
            ],
            'pps_empty_string_no_default' => [
                'in' => '',
                'default' => false,
                'expect_out' => '',
                'expect_exception' => false,
            ],
            'pps_non_empty_string_no_default' => [
                'in' => 'xxx',
                'default' => false,
                'expect_out' => 'xxx',
                'expect_exception' => false,
            ],

            'pps_missing_null_default' => [
                'default' => null,
                'expect_out' => null,
                'expect_exception' => false,
            ],
            'pps_empty_string_null_default' => [
                'in' => '',
                'default' => null,
                'expect_out' => '',
                'expect_exception' => false,
            ],
            'pps_non_empty_string_null_default' => [
                'in' => 'xxx',
                'default' => null,
                'expect_out' => 'xxx',
                'expect_exception' => false,
            ],

            'pps_missing_with_default' => [
                'default' => 'yyy',
                'expect_out' => 'yyy',
                'expect_exception' => false,
            ],
            'pps_empty_string_with_default' => [
                'in' => '',
                'default' => 'yyy',
                'expect_out' => '',
                'expect_exception' => false,
            ],
            'pps_non_empty_string_with_default' => [
                'in' => 'xxx',
                'default' => 'yyy',
                'expect_out' => 'xxx',
                'expect_exception' => false,
            ],
        ];

        foreach ($tests as $param_name => $test_details) {
            unset($_POST[$param_name]);
            if (array_key_exists('in', $test_details)) {
                $_POST[$param_name] = $test_details['in'];
            }

            $got_exception = false;
            try {
                $got = post_param_string($param_name, $test_details['default']);
                if (!$test_details['expect_exception']) {
                    $this->assertTrue($got === $test_details['expect_out'], 'Got unexpected output for ' . $param_name . ' test (' . var_export($got, true) . ')');
                }
            } catch (Exception $e) {
                $got_exception = true;
            }

            if ($test_details['expect_exception']) {
                $this->assertTrue($got_exception, 'Missing expected exception for ' . $param_name . ' test');
            } else {
                $this->assertTrue(!$got_exception, 'Got unexpected exception for ' . $param_name . ' test');
            }
        }
    }

    public function testGetParamString()
    {
        require_code('failure');
        set_throw_errors(true);

        $tests = [
            'gps_missing_no_default' => [
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Missing input
            ],
            'gps_empty_string_no_default' => [
                'in' => '',
                'default' => false,
                'expect_out' => '',
                'expect_exception' => false,
            ],
            'gps_non_empty_string_no_default' => [
                'in' => 'xxx',
                'default' => false,
                'expect_out' => 'xxx',
                'expect_exception' => false,
            ],

            'gps_missing_null_default' => [
                'default' => null,
                'expect_out' => null,
                'expect_exception' => false,
            ],
            'gps_empty_string_null_default' => [
                'in' => '',
                'default' => null,
                'expect_out' => '',
                'expect_exception' => false,
            ],
            'gps_non_empty_string_null_default' => [
                'in' => 'xxx',
                'default' => null,
                'expect_out' => 'xxx',
                'expect_exception' => false,
            ],

            'gps_missing_with_default' => [
                'default' => 'yyy',
                'expect_out' => 'yyy',
                'expect_exception' => false,
            ],
            'gps_empty_string_with_default' => [
                'in' => '',
                'default' => 'yyy',
                'expect_out' => '',
                'expect_exception' => false,
            ],
            'gps_non_empty_string_with_default' => [
                'in' => 'xxx',
                'default' => 'yyy',
                'expect_out' => 'xxx',
                'expect_exception' => false,
            ],
        ];

        foreach ($tests as $param_name => $test_details) {
            unset($_GET[$param_name]);
            if (array_key_exists('in', $test_details)) {
                $_GET[$param_name] = $test_details['in'];
            }

            $got_exception = false;
            try {
                $got = get_param_string($param_name, $test_details['default']);
                if (!$test_details['expect_exception']) {
                    $this->assertTrue($got === $test_details['expect_out'], 'Got unexpected output for ' . $param_name . ' test (' . var_export($got, true) . ')');
                }
            } catch (Exception $e) {
                $got_exception = true;
            }

            if ($test_details['expect_exception']) {
                $this->assertTrue($got_exception, 'Missing expected exception for ' . $param_name . ' test');
            } else {
                $this->assertTrue(!$got_exception, 'Got unexpected exception for ' . $param_name . ' test');
            }
        }
    }

    public function testEitherParamString()
    {
        require_code('failure');
        set_throw_errors(true);

        $tests = [
            'eps_missing_no_default' => [
                'default' => false,
                'expect_out' => null,
                'expect_exception' => true, // Missing input
            ],
            'eps_empty_string_no_default' => [
                'in' => '',
                'default' => false,
                'expect_out' => '',
                'expect_exception' => false,
            ],
            'eps_non_empty_string_no_default' => [
                'in' => 'xxx',
                'default' => false,
                'expect_out' => 'xxx',
                'expect_exception' => false,
            ],

            'eps_missing_null_default' => [
                'default' => null,
                'expect_out' => null,
                'expect_exception' => false,
            ],
            'eps_empty_string_null_default' => [
                'in' => '',
                'default' => null,
                'expect_out' => '',
                'expect_exception' => false,
            ],
            'eps_non_empty_string_null_default' => [
                'in' => 'xxx',
                'default' => null,
                'expect_out' => 'xxx',
                'expect_exception' => false,
            ],

            'eps_missing_with_default' => [
                'default' => 'yyy',
                'expect_out' => 'yyy',
                'expect_exception' => false,
            ],
            'eps_empty_string_with_default' => [
                'in' => '',
                'default' => 'yyy',
                'expect_out' => '',
                'expect_exception' => false,
            ],
            'eps_non_empty_string_with_default' => [
                'in' => 'xxx',
                'default' => 'yyy',
                'expect_out' => 'xxx',
                'expect_exception' => false,
            ],
        ];

        foreach ($tests as $param_name => $test_details) {
            unset($_POST[$param_name . '__POST']);
            unset($_GET[$param_name . '__GET']);
            if (array_key_exists('in', $test_details)) {
                $_POST[$param_name . '__POST'] = $test_details['in'];
            }

            $got_exception = false;
            try {
                $got = either_param_string($param_name . '__POST', $test_details['default']);
                if (!$test_details['expect_exception']) {
                    $this->assertTrue($got === $test_details['expect_out'], 'Got unexpected output for ' . $param_name . '__POST test');
                }
            } catch (Exception $e) {
                $got_exception = true;
            }

            if ($test_details['expect_exception']) {
                $this->assertTrue($got_exception, 'Missing expected exception for ' . $param_name . '__POST test');
            } else {
                $this->assertTrue(!$got_exception, 'Got unexpected exception for ' . $param_name . '__POST test');
            }

            unset($_POST[$param_name . '__POST']);
            unset($_GET[$param_name . '__GET']);
            if (array_key_exists('in', $test_details)) {
                $_GET[$param_name . '__GET'] = $test_details['in'];
            }

            $got_exception = false;
            try {
                $got = either_param_string($param_name . '__GET', $test_details['default']);
                if (!$test_details['expect_exception']) {
                    $this->assertTrue($got === $test_details['expect_out'], 'Got unexpected output for ' . $param_name . '__GET test (' . var_export($got, true) . ')');
                }
            } catch (Exception $e) {
                $got_exception = true;
            }

            if ($test_details['expect_exception']) {
                $this->assertTrue($got_exception, 'Missing expected exception for ' . $param_name . '__GET test');
            } else {
                $this->assertTrue(!$got_exception, 'Got unexpected exception for ' . $param_name . '__GET test');
            }
        }
    }
}

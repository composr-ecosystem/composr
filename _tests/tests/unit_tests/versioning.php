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
class versioning_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('version2');
    }

    public function testGetDotted()
    {
        $tests = [
            '3 alpha 1' => '3.alpha1',
            '3.1 alpha 1' => '3.1.alpha1',
            '3.1.0 alpha 1' => '3.1.alpha1',
            '3.1.1 alpha 1' => '3.1.1.alpha1',

            '3 beta1' => '3.beta1',
            '3.1 beta1' => '3.1.beta1',
            '3.1.0 beta1' => '3.1.beta1',
            '3.1.1 beta1' => '3.1.1.beta1',

            'Composr 3RC1' => '3.RC1',
            'Composr version 3.1RC1' => '3.1.RC1',
            'Version 3.1.0RC1' => '3.1.RC1',
            '3.1.1RC1' => '3.1.1.RC1',

            '3-gold' => '3',
            '3.1-gold' => '3.1',
            '3.1.0-gold' => '3.1',
            '3.1.1-gold' => '3.1.1',

            '3' => '3',
            '3.1' => '3.1',
            '3.1.0' => '3.1',
            '3.1.1' => '3.1.1',
        ];
        foreach ($tests as $from => $to) {
            $got = get_version_dotted__from_anything($from);
            $this->assertTrue($got == $to, 'Failed on ' . $from);
        }
    }

    public function testGetComponents()
    {
        $v = '3.1.0.beta1';

        list($basis_dotted_number, $qualifier, $qualifier_number, $long_dotted_number, $general_number, $long_dotted_number_with_qualifier) = get_version_components__from_dotted($v);

        $this->assertTrue($basis_dotted_number == '3.1');
        $this->assertTrue($qualifier == 'beta');
        $this->assertTrue($qualifier_number == 1);
        $this->assertTrue($long_dotted_number == '3.1.0');
        $this->assertTrue($general_number == 3.1);
        $this->assertTrue($long_dotted_number_with_qualifier == '3.1.0.beta1');

        $v = '3.1.beta1';

        list($basis_dotted_number, $qualifier, $qualifier_number, $long_dotted_number, $general_number, $long_dotted_number_with_qualifier) = get_version_components__from_dotted($v);

        $this->assertTrue($basis_dotted_number == '3.1');
        $this->assertTrue($qualifier == 'beta');
        $this->assertTrue($qualifier_number == 1);
        $this->assertTrue($long_dotted_number == '3.1.0');
        $this->assertTrue($general_number == 3.1);
        $this->assertTrue($long_dotted_number_with_qualifier == '3.1.0.beta1');
    }

    public function testGetPretty()
    {
        $this->assertTrue(get_version_pretty__from_dotted('3.1.1.beta1') == '3.1.1 beta1');

        $this->assertTrue(get_version_pretty__from_dotted('3.1.0.beta1') == '3.1 beta1');

        $this->assertTrue(get_version_pretty__from_dotted('3.1.beta1') == '3.1 beta1');
    }

    public function testIsSubstantial()
    {
        $this->assertTrue(is_substantial_release('3.beta1'));
        $this->assertTrue(is_substantial_release('3.RC1'));
        $this->assertTrue(is_substantial_release('3'));

        $this->assertTrue(is_substantial_release('3.0.beta1'));
        $this->assertTrue(is_substantial_release('3.0.RC1'));
        $this->assertTrue(is_substantial_release('3.0'));

        $this->assertTrue(is_substantial_release('3.0.0.beta1'));
        $this->assertTrue(is_substantial_release('3.0.0.RC1'));
        $this->assertTrue(is_substantial_release('3.0.0'));

        $this->assertTrue(is_substantial_release('3.1.beta1'));
        $this->assertTrue(is_substantial_release('3.1.RC1'));
        $this->assertTrue(is_substantial_release('3.1'));

        $this->assertTrue(is_substantial_release('3.1.0.beta1'));
        $this->assertTrue(is_substantial_release('3.1.0.RC1'));
        $this->assertTrue(is_substantial_release('3.1.0'));

        $this->assertTrue(!is_substantial_release('3.1.alpha1'));
        $this->assertTrue(!is_substantial_release('3.1.1.alpha1'));
        $this->assertTrue(!is_substantial_release('3.1.1.beta1'));
        $this->assertTrue(!is_substantial_release('3.1.1.RC1'));
        $this->assertTrue(!is_substantial_release('3.1.1'));
        $this->assertTrue(!is_substantial_release('3.1.alpha2'));
        $this->assertTrue(!is_substantial_release('3.1.beta2'));
        $this->assertTrue(!is_substantial_release('3.1.RC2'));
    }
}

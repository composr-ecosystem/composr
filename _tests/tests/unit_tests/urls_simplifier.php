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
class urls_simplifier_test_set extends cms_test_case
{
    protected $ob;

    public function setUp()
    {
        parent::setUp();

        set_value('urls_simplifier', '1');

        require_code('uploads');

        require_code('urls_simplifier');
        $this->ob = new HarmlessURLCoder();
    }

    public function testRecode()
    {
        $from = 'x%20%D0%B8%D1%81%D0%BF%D1%8B%D1%82%D0%B0%D0%BD%D0%B8%D0%B5';
        $got = cms_rawurlrecode($from, true);
        $expected = 'x%20' . hex2bin('D0B8D181D0BFD18BD182D0B0D0BDD0B8D0B5');
        $this->assertTrue($got == $expected, 'Got ' . $got . '; expected ' . $expected);
    }

    public function testProceedAsExpected()
    {
        $tests = [
            // encoded -> decoded
            'http://example.com/foo.jpg' => 'http://example.com/foo.jpg', // No changes desirable
            'http://example.com:8080/foo.jpg' => 'http://example.com:8080/foo.jpg', // No changes desirable
            'http://example.com/foo%20bar.jpg' => 'http://example.com/foo bar.jpg', // We can decode spaces
            'http://example.com/foo%27s.jpg' => 'http://example.com/foo\'s.jpg', // We can decode "'"
            'http://example.com/foo.jpg#blah' => 'http://example.com/foo.jpg#blah', // We cannot decode "#"
            'http://example.com/foo%25.jpg' => 'http://example.com/foo%25.jpg', // We cannot decode percentages
        ];

        foreach ($tests as $from => $expected) {
            $got = $this->ob->decode($from);
            $this->assertTrue($got == $expected, 'Incorrectly decoded ' . $from . '; got ' . $got . '; expected ' . $expected);

            if ($got == $expected) {
                // Try double decoding
                $got = $this->ob->decode($expected);
                $this->assertTrue($got == $expected, 'Double decoding failed ' . $from);
            }
        }

        foreach ($tests as $expected => $from) {
            $got = $this->ob->encode($from);
            $this->assertTrue($got == $expected, 'Incorrectly encoded ' . $from . '; got ' . $got . '; expected ' . $expected);

            if ($got == $expected) {
                // Try double encoding
                $got = $this->ob->encode($expected);
                $this->assertTrue($got == $expected, 'Double encoding failed ' . $from);
            }
        }
    }

    public function testPunycode()
    {
        if ((function_exists('idn_to_utf8')) && (get_charset() == 'utf-8')) {
            $tests = [
                'http://xn--mnchen-3ya' => 'http://münchen',
                'http://xn--mnchen-3ya:8080' => 'http://münchen:8080',
            ];

            foreach ($tests as $from => $expected) {
                $got = $this->ob->decode($from);
                $this->assertTrue($got == $expected, 'Got ' . $got . ', expected ' . $expected);
            }

            foreach ($tests as $expected => $from) {
                $got = $this->ob->encode($from);
                $this->assertTrue($got == $expected, 'Got ' . $got . ', expected ' . $expected);
            }
        }
    }
}

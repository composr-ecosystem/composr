<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2018

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class unpack_test_set extends cms_test_case
{
    public function testBitwise()
    {
        $test_data = array(
            // Lower 1-byte
            array(chr(0x0A), 0x0A, 0x0A),
            array(chr(0x0B), 0x0B, 0x0B),

            // Upper 1-byte
            array(chr(0xFE), 0xFE, 0xFE),
            array(chr(0xFF), 0xFF, 0xFF),

            // Lower 2-bytes
            array(chr(0x0A) . chr(0x0B), 0x0A0B, 0x0A0B),
            array(chr(0x0B) . chr(0x0A), 0x0B0A, 0x0B0A),

            // Upper 2-bytes
            array(chr(0xFE) . chr(0xFF), 0xFEFF, 0xFEFF),
            array(chr(0xFF) . chr(0xFE), 0xFFFE, 0xFFFE),
        );

        foreach ($test_data as $_parts) {
            list($str, $hex, $expected) = $_parts;

            $output = (cms_unpack_to_uinteger($str) & $hex);
            $this->assertTrue($output == $expected);
        }
    }
}

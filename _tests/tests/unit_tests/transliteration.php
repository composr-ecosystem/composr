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
class transliteration_test_set extends cms_test_case
{
    public function testTransliterationAddon()
    {
        if (!addon_installed('transliteration')) {
            $this->assertTrue(false, 'The transliteration addon must be installed for this test to run');
            return;
        }

        require_code('character_sets');

        $expect = [
            ['foo', ['foo']],
            ["gl\u{00FC}ckliche", ['gluckliche']],
            ["caf\u{00E9}", ['cafe']],
            ["\u{6E90}\u{660C}\u{9686}\u{5496}\u{5561}\u{5E97}", ['yuan chang long ka fei dian', 'yuan-chang-long-ka-pei-dian']],
        ];

        foreach ($expect as $_) {
            list($from, $to) = $_;

            $got = transliterate_string($from);
            $this->assertTrue(in_array($got, $to), 'Failed to get one of ' . json_encode($to) . ', got ' . $got);
        }
    }
}

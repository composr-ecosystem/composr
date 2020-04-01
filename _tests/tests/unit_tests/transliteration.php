<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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
class transliteration_test_set extends cms_test_case
{
    public function testTransliterationAddon()
    {
        if (!addon_installed('transliteration')) {
            $this->assertTrue(false, 'The transliteration addon must be installed for this test to run');
            return;
        }

        $expect = [
            'foo' => 'foo',
            'glückliche' => 'gluckliche',
            'café' => 'cafe',
            '咖啡店' => 'ka fei dian',
        ];

        foreach ($expect as $from => $to) {
            $got = transliterate_string($from);
            $this->assertTrue($got == $to, 'Failed to get ' . $to);
        }
    }
}

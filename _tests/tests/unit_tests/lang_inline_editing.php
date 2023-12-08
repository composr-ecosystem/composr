<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class lang_inline_editing_test_set extends cms_test_case
{
    public function testInlineLanguageEditingWorks()
    {
        if (!$GLOBALS['SEMI_DEV_MODE']) {
            return; // Only works in semi-dev mode
        }

        $result = comcode_to_tempcode('{!testy_test:FOOBAR=Test}', null, true);
        $this->assertTrue($result->evaluate() == 'Test');

        $expected_path = get_custom_file_base() . '/lang_custom/EN/testy_test.ini';
        $ok = is_file($expected_path);

        $this->assertTrue($ok);

        if ($ok) {
            $this->assertTrue(strpos(file_get_contents($expected_path), "[strings]\nFOOBAR=Test\n") !== false);

            unlink($expected_path);
        }
    }
}

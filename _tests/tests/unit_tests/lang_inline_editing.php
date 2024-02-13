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
class lang_inline_editing_test_set extends cms_test_case
{
    public function testInlineLanguageEditingWorks()
    {
        if (!$GLOBALS['SEMI_DEV_MODE']) {
            $this->assertTrue(false, 'Cannot run unless in semi-dev-mode');
            return;
        }

        $result = comcode_to_tempcode('{!testy_test:FOOBAR=Test}', null, true);
        $this->assertTrue($result->evaluate() == 'Test');

        $expected_path = get_custom_file_base() . '/lang_custom/EN/testy_test.ini';
        $ok = is_file($expected_path);

        $this->assertTrue($ok);

        if ($ok) {
            $this->assertTrue(strpos(cms_file_get_contents_safe($expected_path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM), "[strings]\nFOOBAR=Test\n") !== false);

            unlink($expected_path);
        }
    }
}

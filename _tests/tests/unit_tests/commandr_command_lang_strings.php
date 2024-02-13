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
class commandr_command_lang_strings_test_set extends cms_test_case
{
    public function testStringsAllDefined()
    {
        require_code('commandr');
        require_code('commandr_fs');
        $fs = new Commandr_fs();
        $hooks = find_all_hook_obs('systems', 'commandr_commands', 'Hook_commandr_command_');
        foreach ($hooks as $hook => $ob) {
            if ($hook == 'help') {
                continue;
            }

            $ret = $ob->run(['h' => 1], [], $fs);
            $this->assertTrue(strlen($ret[1]->evaluate()) > 0, 'Missing Commandr help for ' . $hook);
            $this->assertTrue(count($ret) == 4, 'Unexpected returned values for ' . $hook);
        }
    }
}

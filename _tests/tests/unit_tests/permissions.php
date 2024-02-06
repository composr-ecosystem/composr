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
class permissions_test_set extends cms_test_case
{
    public function testPrivilegeLangStrings()
    {
        require_all_lang();
        $privileges = $GLOBALS['SITE_DB']->query_select('privilege_list', ['*']);
        foreach ($privileges as $p) {
            $this->assertTrue(do_lang('PRIVILEGE_' . $p['the_name'], null, null, null, null, false) !== null, 'Could not find language string PRIVILEGE_' . $p['the_name']);
        }
    }
}

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
class httpauth_test_set extends cms_test_case
{
    public function testHttpAuth()
    {
        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        $pwd = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'm_pass_hash_salted', ['m_username' => 'admin']);
        if ($pwd !== '') {
            return; // Test only works with blank admin password
        }

        $url = build_url(['page' => 'members', 'type' => 'view'], get_module_zone('members'));

        set_option('httpauth_is_enabled', '1');

        $data = http_get_contents($url->evaluate(), ['convert_to_internal_encoding' => true, 'timeout' => 20.0, 'auth' => ['admin', '']]);

        set_option('httpauth_is_enabled', '0');

        if ($this->debug) {
            var_dump($data);
        }

        $this->assertTrue(strpos($data, '<span class="fn nickname">admin</span>') !== false);
    }
}

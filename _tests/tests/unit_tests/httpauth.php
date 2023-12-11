<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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
class httpauth_test_set extends cms_test_case
{
    public function testHttpAuth()
    {
        $pwd = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'm_pass_hash_salted', array('m_username' => 'admin'));
        if ($pwd !== '') {
            return; // Test only works with blank admin password
        }

        $url = build_url(array('page' => 'members', 'type' => 'view'), get_module_zone('members'));

        set_option('httpauth_is_enabled', '1');

        $data = http_download_file($url->evaluate(), null, true, false, 'Composr', null, null, null, null, null, null, null, array('admin', ''));

        set_option('httpauth_is_enabled', '0');

        $this->assertTrue(strpos($data, '<span class="fn nickname">admin</span>') !== false);
    }
}

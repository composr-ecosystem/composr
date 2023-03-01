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
class cpfs_test_set extends cms_test_case
{
    public function testCPFFullCycle()
    {
        if (get_forum_type() != 'cns') {
            return;
        }

        require_code('cpf_install');

        $member_id = $this->get_canonical_member_id('admin');

        install_name_fields();

        $GLOBALS['FORUM_DRIVER']->set_custom_field($member_id, 'firstname', 'Foobar');

        $fields = cns_get_custom_field_mappings($member_id);
        $this->assertTrue(strpos(serialize($fields), 'Foobar') !== false);

        $looked_up = get_cms_cpf('firstname', $member_id);
        $this->assertTrue($looked_up == 'Foobar', 'Got ' . $looked_up . ', expected Foobar');
    }
}

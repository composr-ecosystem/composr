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
class adminzone_search_test_set extends cms_test_case
{
    public function testAdminZoneSearch()
    {
        require_code('adminzone/pages/modules/admin.php');
        $ob = new Module_admin();
        $_GET['type'] = 'search';
        $_GET['content'] = 'test';
        if (method_exists($ob, 'pre_run')) {
            $ob->pre_run();
        }
        $ob->run();
    }
}

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
class optimisations_fragile_test_set extends cms_test_case
{
    public function testSymbols2Optimisation()
    {
        global $SYMBOLS2_CAUSE;

        $GLOBALS['SITE_DB']->query_insert('group_zone_access', ['zone_name' => 'forum', 'group_id' => db_get_first_id()], false, true); // errors suppressed in case already there

        $db = $GLOBALS[(get_forum_type() == 'cns') ? 'FORUM_DB' : 'SITE_DB'];
        $db->query_insert('group_category_access', [
            'module_the_name' => 'forums',
            'category_name' => strval(db_get_first_id()),
            'group_id' => db_get_first_id(),
        ], false, true);

        require_code('site');

        if (get_forum_type() == 'cns') {
            $_GET['id'] = strval(db_get_first_id());
            $out = load_module_page('forum/pages/modules/forumview.php', 'forumview');
            unset($_GET['id']);
            require_lang('cns');
            $this->assertTrue(strpos($out->evaluate(), do_lang('ROOT_FORUM')) !== false);
            $this->assertTrue(empty($SYMBOLS2_CAUSE), 'symbols2.php used on forumview (' . implode(', ', $SYMBOLS2_CAUSE) . ')');
        }

        require_code('failure');
        set_throw_errors(true);

        $modules = find_all_pages('site', 'modules');
        foreach (array_keys($modules) as $module) {
            if ($this->debug) {
                @var_dump($module);
            }

            try {
                $out = load_module_page('site/pages/modules/' . $module . '.php', $module);
            } catch (Exception $e) {
            }
            $bad = function_exists('ecv2_MAKE_URL_ABSOLUTE');
            $this->assertTrue(!$bad, 'Loaded symbols2.php in module ' . $module);
            $this->assertTrue(empty($SYMBOLS2_CAUSE), 'Loaded symbols2.php in module ' . $module . ' (' . implode(', ', $SYMBOLS2_CAUSE) . ')');
            if ($bad) {
                break;
            }
        }
    }
}

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
class optimisations_fragile_test_set extends cms_test_case
{
    public function testSymbols2Optimisation()
    {
        global $SYMBOLS2_CAUSE;

        require_code('site');
        $_GET['id'] = strval(db_get_first_id());
        $out = load_module_page('forum/pages/modules/forumview.php', 'forumview');
        $this->assertTrue(strpos($out->evaluate(), do_lang('DEFAULT_FORUM_TITLE')) !== false);
        $this->assertTrue(empty($SYMBOLS2_CAUSE), 'symbols2.php used on forumview (' . implode(', ', $SYMBOLS2_CAUSE) . ')');

        require_code('failure');
        set_throw_errors(true);

        $modules = find_all_pages('site', 'modules');
        foreach (array_keys($modules) as $module) {
            if (!empty($_GET['debug'])) { // TODO: Change in v11
                @var_dump($module);
            }

            try {
                $out = load_module_page('site/pages/modules/' . $module . '.php', $module);
            }
            catch (Exception $e) {
            }
            $this->assertTrue(empty($SYMBOLS2_CAUSE), 'symbols2.php used on ' . $module . ' module (' . implode(', ', $SYMBOLS2_CAUSE) . ')');
        }
    }
}

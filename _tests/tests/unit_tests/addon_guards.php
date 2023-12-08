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
class addon_guards_test_set extends cms_test_case
{
    public function testAddonGuards()
    {
        $files_in_addons = array();

        $addons = find_all_hooks('systems', 'addon_registry');
        foreach (array_keys($addons) as $addon) {
            require_code('hooks/systems/addon_registry/' . $addon);
            $ob = object_factory('Hook_addon_registry_' . $addon);

            $files = $ob->get_file_list();
            foreach ($files as $file) {
                $files_in_addons[$file] = $addon;
            }
        }

        foreach (array_keys($addons) as $addon) {
            $ob = object_factory('Hook_addon_registry_' . $addon);

            $files = $ob->get_file_list();

            $dependencies = $ob->get_dependencies();
            $requires = $dependencies['requires'];

            foreach ($files as $file) {
                if ($file == 'data_custom/execute_temp.php') {
                    continue;
                }

                if (substr($file, -4) == '.ini') {
                    $c = file_get_contents(get_file_base() . '/' . $file);

                    $this->assertTrue(strpos($c, 'require_lang(\'' . basename($file, '.ini') . '\')') === false, 'Unnecessary require_lang call for ' . $file . ' in ' . $addon);
                }

                if ((substr($file, -4) == '.php') && (preg_match('#(^\_tests/|^data\_custom/stress\_test\_loader\.php$|^sources/hooks/modules/admin\_import/)#', $file) == 0)) {
                    $c = file_get_contents(get_file_base() . '/' . $file);

                    $matches = array();
                    $num_matches = preg_match_all('#(require\_lang|require\_code|require\_css|require\_javascript|do\_template)\(\'([^\']*)\'[\),]#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $include = $matches[2][$i];

                        $type = $matches[1][$i];
                        switch ($type) {
                            case 'require_lang':
                                $included_file = 'lang/EN/' . $include . '.ini';
                                break;
                            case 'require_code':
                                $included_file = 'sources/' . $include . '.php';
                                break;
                            case 'require_css':
                                $included_file = 'themes/default/css/' . $include . '.css';
                                break;
                            case 'require_javascript':
                                $included_file = 'themes/default/javascript/' . $include . '.js';
                                break;
                            case 'do_template':
                                $included_file = 'themes/default/templates/' . $include . '.tpl';
                                break;
                        }

                        if (isset($files_in_addons[$included_file])) {
                            $file_in_addon = $files_in_addons[$included_file];
                            if (
                                ($file_in_addon != $addon) &&
                                (substr($file_in_addon, 0, 5) != 'core_') &&
                                ($file_in_addon != 'core') &&
                                (strpos($file, $file_in_addon) === false/*looks like a hook for this addon*/) &&
                                ((!in_array($file_in_addon, $requires)) && ((!in_array('news', $requires)) || ($file_in_addon != 'news_shared')))
                            ) {
                                $search_for = 'addon_installed(\'' . $file_in_addon . '\')';
                                $ok = (strpos($c, $search_for) !== false);
                                if (!$ok) {
                                    if ($file_in_addon == 'news_shared') {
                                        $search_for = 'addon_installed(\'news\')';
                                        $ok = (strpos($c, $search_for) !== false);
                                    }
                                }

                                $error_message = 'Cannot find a guard for the ' . $file_in_addon . ' addon in ' . $file . ' [' . $addon . '], due to ' . $matches[0][$i];

                                if (in_array($error_message, array(
                                    'Cannot find a guard for the google_appengine addon in sources/global.php [core], due to require_code(\'google_appengine\')',
                                    'Cannot find a guard for the chat addon in sources/global2.php [core], due to require_code(\'chat_poller\')',
                                    'Cannot find a guard for the catalogues addon in sources/crud_module.php [core], due to require_javascript(\'catalogues\')',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_calendar.php [calendar], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_calendar.php [calendar], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_calendar.php [calendar], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_calendar.php [calendar], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_calendar.php [calendar], due to do_template(\'BLOCK_SIDE_STATS_SECTION\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_chat.php [chat], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_chat.php [chat], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_chat.php [chat], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_chat.php [chat], due to do_template(\'BLOCK_SIDE_STATS_SECTION\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_downloads.php [downloads], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_downloads.php [downloads], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_downloads.php [downloads], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_downloads.php [downloads], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_downloads.php [downloads], due to do_template(\'BLOCK_SIDE_STATS_SECTION\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_filedump.php [filedump], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_filedump.php [filedump], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_filedump.php [filedump], due to do_template(\'BLOCK_SIDE_STATS_SECTION\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_galleries.php [galleries], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_galleries.php [galleries], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_galleries.php [galleries], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_galleries.php [galleries], due to do_template(\'BLOCK_SIDE_STATS_SECTION\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_news.php [news], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_news.php [news], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_news.php [news], due to do_template(\'BLOCK_SIDE_STATS_SECTION\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_quiz.php [quizzes], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_quiz.php [quizzes], due to do_template(\'BLOCK_SIDE_STATS_SECTION\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_wiki.php [wiki], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_wiki.php [wiki], due to do_template(\'BLOCK_SIDE_STATS_SUBLINE\',',
                                    'Cannot find a guard for the stats_block addon in sources/hooks/blocks/side_stats/stats_wiki.php [wiki], due to do_template(\'BLOCK_SIDE_STATS_SECTION\',',
                                    'Cannot find a guard for the catalogues addon in sources/crud_module.php [core], due to do_template(\'CATALOGUE_ADDING_SCREEN\',',
                                    'Cannot find a guard for the catalogues addon in sources/crud_module.php [core], due to do_template(\'CATALOGUE_EDITING_SCREEN\',',
                                    'Cannot find a guard for the backup addon in sources/minikernel.php [core], due to do_template(\'RESTORE_HTML_WRAP\',',
                                    'Cannot find a guard for the installer addon in sources/minikernel.php [core], due to do_template(\'INSTALLER_HTML_WRAP\',',
                                    'Cannot find a guard for the backup addon in sources/minikernel.php [core], due to do_template(\'RESTORE_HTML_WRAP\',',
                                    'Cannot find a guard for the installer addon in sources/minikernel.php [core], due to do_template(\'INSTALLER_HTML_WRAP\',',
                                    'Cannot find a guard for the cns_post_templates addon in sources/cns_general_action.php [core_cns], due to require_lang(\'cns_post_templates\')',
                                    'Cannot find a guard for the welcome_emails addon in sources/cns_general_action.php [core_cns], due to require_lang(\'cns_welcome_emails\')',
                                    'Cannot find a guard for the cns_post_templates addon in sources/cns_general_action2.php [core_cns], due to require_lang(\'cns_post_templates\')',
                                    'Cannot find a guard for the cns_post_templates addon in sources/cns_general_action2.php [core_cns], due to require_lang(\'cns_post_templates\')',
                                    'Cannot find a guard for the welcome_emails addon in sources/cns_general_action2.php [core_cns], due to require_lang(\'cns_welcome_emails\')',
                                    'Cannot find a guard for the welcome_emails addon in sources/cns_general_action2.php [core_cns], due to require_lang(\'cns_welcome_emails\')',
                                ))) {
                                    continue; // Exceptions
                                }

                                $this->assertTrue($ok, $error_message);
                            }
                        }
                    }
                }
            }
        }
    }
}

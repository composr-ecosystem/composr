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
class addon_guards_test_set extends cms_test_case
{
    // We don't need to (and shouldn't) do addon_installed checks in these hook types for the given addon, as it's implied to already exist (nothing else using the hooks)
    protected $hook_ownership = [
        'blocks/main_custom_gfx' => 'custom_comcode',
        'blocks/side_stats' => 'stats_block',
        'modules/admin_import' => 'import',
        'modules/admin_import_types' => 'import',
        'modules/admin_newsletter' => 'newsletter',
        'modules/admin_setupwizard' => 'setupwizard',
        'modules/admin_setupwizard_installprofiles' => 'setupwizard',
        'modules/admin_stats' => 'stats',
        'modules/admin_stats_redirects' => 'stats',
        'modules/admin_themewizard' => 'themewizard',
        'modules/admin_unvalidated' => 'unvalidated',
        'modules/chat_bots' => 'chat',
        'modules/galleries_users' => 'galleries',
        'modules/search' => 'search',
        'systems/commandr_commands' => 'commandr',
        'systems/commandr_fs' => 'commandr',
        'systems/commandr_fs_extended_config' => 'commandr',
        'systems/commandr_fs_extended_member' => 'commandr',
        'systems/ecommerce' => 'ecommerce',
        'systems/health_checks' => 'health_check',
        'systems/payment_gateway' => 'ecommerce',
        'systems/realtime_rain' => 'realtime_rain',
        'systems/referrals' => 'referrals',
        'systems/points' => 'points',
        'systems/ecommerce_tax' => 'ecommerce',
    ];

    public function testAddonGuardsCohesion()
    {
        require_code('files2');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $exceptions = [
            '(sources|sources_custom)/hooks/systems/addon_registry/\w+\.php',
            '(sources|sources_custom)/hooks/systems/ajax_tree/\w+\.php',

            // These contain no code
            '(sources|sources_custom)/hooks/systems/disposable_values/\w+\.php',
            '(sources|sources_custom)/hooks/systems/non_active_urls/\w+\.php',
            '(sources|sources_custom)/hooks/systems/points/\w+\.php',

            // Not an actual PHP script
            'data_custom/errorlog.php',
        ];

        $hooks_files = get_directory_contents(get_file_base() . '/sources/hooks', 'sources/hooks', null, true, true, ['php']);
        $hooks_custom_files = get_directory_contents(get_file_base() . '/sources_custom/hooks', 'sources_custom/hooks', null, true, true, ['php']);
        $blocks_files = get_directory_contents(get_file_base() . '/sources/blocks', 'sources/blocks', null, true, true, ['php']);
        $blocks_custom_files = get_directory_contents(get_file_base() . '/sources_custom/blocks', 'sources_custom/blocks', null, true, true, ['php']);
        $miniblocks_custom_files = get_directory_contents(get_file_base() . '/sources_custom/miniblocks', 'sources_custom/miniblocks', null, true, true, ['php']);

        $modules_files = [];
        $zones = find_all_zones();
        foreach ($zones as $zone) {
            $modules_files = get_directory_contents(get_file_base() . '/' . $zone . '/pages', $zone . '/pages', null, true, true, ['php']);
        }

        $files = array_merge($hooks_files, $hooks_custom_files, $blocks_files, $blocks_custom_files, $miniblocks_custom_files, $modules_files);

        foreach ($files as $path) {
            $matches_hook_details = [];
            if (preg_match('#^\w+/hooks/(\w+)/(\w+)/\w+\.php$#', $path, $matches_hook_details) != 0) {
                $hook_type = $matches_hook_details[1];
                $hook_subtype = $matches_hook_details[2];
            } else {
                $hook_type = null;
                $hook_subtype = null;
            }

            // Exceptions
            foreach ($exceptions as $exception) {
                if (preg_match('#^' . $exception . '$#', $path) != 0) {
                    continue 2;
                }
            }

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $matches = [];
            if (preg_match('#@package\s+(\w+)#', $c, $matches) == 0) {
                $this->assertTrue(false, 'Could not detect addon in ' . $path);
                continue;
            }
            $addon_name = $matches[1];

            $has = (strpos($c, 'addon_installed(\'' . addslashes($addon_name) . '\')') !== false) || (strpos($c, 'addon_installed__messaged(\'' . addslashes($addon_name) . '\'') !== false);

            if (
                ($addon_name == 'core') || // No checks needed for core
                (substr($addon_name, 0, 5) == 'core_') || // No checks needed for core
                (($hook_type !== null) && (array_key_exists($hook_type . '/' . $hook_subtype, $this->hook_ownership)) && ($addon_name == $this->hook_ownership[$hook_type . '/' . $hook_subtype])) // No checks needed for self-ownership of hooks of particular addons
            ) {
                $this->assertTrue(!$has, 'No need to do addon check for ' . $addon_name . ' in ' . $path);
            } else {
                $this->assertTrue($has, 'Missing addon check for ' . $addon_name . ' in ' . $path);
            }
        }
    }

    public function testAddonGuardsImplicitCodeCalls()
    {
        $files_in_addons = [];

        $addons = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($addons as $addon_name => $ob) {
            $files = $ob->get_file_list();
            foreach ($files as $path) {
                $files_in_addons[$path] = $addon_name;
            }
        }

        foreach ($addons as $addon_name => $ob) {
            $files = $ob->get_file_list();

            $dependencies = $ob->get_dependencies();
            $requires = $dependencies['requires'];

            foreach ($files as $path) {
                if ($path == 'data_custom/execute_temp.php') {
                    continue;
                }
                if ($path == 'data_custom/errorlog.php') {
                    continue;
                }

                if (!is_file(get_file_base() . '/' . $path)) {
                    continue;
                }

                if ((substr($path, -4) == '.php') && (preg_match('#(^_tests/|^data_custom/stress_test_loader\.php$|^sources/hooks/modules/admin_import/)#', $path) == 0)) {
                    $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

                    $matches = [];
                    $num_matches = preg_match_all('#(require_lang|require_code|require_css|require_javascript|do_template)\(\'([^\']*)\'[\),]#', $c, $matches);
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
                                ($file_in_addon != $addon_name) &&
                                (substr($file_in_addon, 0, 5) != 'core_') &&
                                ($file_in_addon != 'core') &&
                                (strpos($path, $file_in_addon) === false) && // looks like a hook for this addon
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
                                if (!$ok) {
                                    $matches_hook_details = [];
                                    if (preg_match('#^\w+/hooks/(\w+)/(\w+)/\w+\.php$#', $path, $matches_hook_details) != 0) {
                                        $hook_type = $matches_hook_details[1];
                                        $hook_subtype = $matches_hook_details[2];

                                        if ((array_key_exists($hook_type . '/' . $hook_subtype, $this->hook_ownership)) && ($file_in_addon == $this->hook_ownership[$hook_type . '/' . $hook_subtype])) {
                                            $ok = true;
                                        }
                                    }
                                }

                                $error_message = 'Cannot find a guard for the ' . $file_in_addon . ' addon in ' . $path . ' [' . $addon_name . '], due to ' . $matches[0][$i];

                                if (in_array($error_message, [
                                    'Cannot find a guard for the google_appengine addon in sources/global.php [core], due to require_code(\'google_appengine\')',
                                    'Cannot find a guard for the chat addon in sources/global2.php [core], due to require_code(\'chat_poller\')',
                                    'Cannot find a guard for the catalogues addon in sources/crud_module.php [core], due to require_javascript(\'catalogues\')',
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
                                ])) {
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

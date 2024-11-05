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
        'modules/admin_validation' => 'validation',
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
        'systems/ecommerce_tax' => 'ecommerce',
    ];

    public function setUp()
    {
        parent::setUp();

        require_code('phpdoc');
    }

    public function testAddonGuardsCohesion()
    {
        $info = 'testAddonGuardsCohesion: This is an aggressive test; consider reviewing the defined exceptions periodically for accuracy.';
        $this->dump($info, 'INFO');

        require_code('files2');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        // Full file exceptions
        $exceptions = [
            // Purely informational
            '(sources|sources_custom)/hooks/systems/addon_registry/\w+\.php',

            // Not an actual PHP script
            'data_custom/errorlog.php',

            // Belongs to multiple addons; would be too complicated to check
            'sources_custom/hooks/systems/fields/float.php',
        ];

        /*
            This is a map of class expressions to a comma-delimited list of functions which control whether any of the
            other functions in the class are used. If a class matches one or more expressions and has defined at least
            one of the given function names, then we will *only* check addon guards on those specified functions.
            If none of the specified functions are defined in the class, we will check all functions instead as normal.
        */
        $class_controllers = [
            'Hook_\w+' => 'info',
            'Module_\w+' => 'pre_run', // We do not want get_entry_points because that will mess things up when pre_run is not defined; check get_entry_points manually

            'Hook_actionlog_\w+' => 'get_handlers',
            'Hook_commandr_fs_\w+' => 'is_active',
            'Hook_config_\w+' => 'get_default',
            'Hook_ecommerce_\w+' => 'is_available,get_product_category',
            'Hook_preview_\w+' => 'applies',
            'Hook_profiles_tabs_\w+' => 'is_active',
            'Hook_rss_\w+' => 'has_access', // Careful! Make sure run is calling this.
            'Hook_sitemap_\w+' => 'is_active',
            'Hook_cdn_transfer_\w+' => 'is_enabled',
            'Hook_media_rendering_\w+' => 'recognises_url',
        ];

        // Function exceptions, defined as a regex of class::function (class is __global if none). Takes priority over class controllers.
        $function_exceptions = [
            // Should never check on class constructs
            '\w+\:\:__construct',

            // Also should never check global functions starting with init__ as they act like __construct for non-classes.
            '__global\:\:init__\w+',

            // We expect get_details to always return even if the addon is not installed
            'Hook_config_\w+\:\:get_details',

            // Unnecessary to use addon guards on these field functions
            'Hook_fields_\w+\:\:get_field_value_row_bits',
            'Hook_fields_\w+\:\:privacy_field_type',

            // Info-based notification functions
            'Hook_notification_\w+\:\:get_initial_setting',
            'Hook_notification_\w+\:\:supports_categories',
            'Hook_notification_\w+\:\:list_members_who_have_enabled',
            'Hook_notification_\w+\:\:allowed_settings',
            'Hook_notification_\w+\:\:member_could_potentially_enable',
            'Hook_notification_\w+\:\:member_has_enabled',

            // Special block functions that could still return if an addon is not installed
            'Block_\w+\:\:caching_environment',

            // Info / install / uninstall functions
            '(Block|Module|Hook_addon_registry)_\w+\:\:(info|install|uninstall)',

            'Hook_sitemap_\w+\:\:get_privilege_page',
            'Hook_contentious_overrides_nested_cpf_spreadsheet_lists::injectFormSelectChainingForm' // JavaScript within PHP
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

            // File exceptions
            foreach ($exceptions as $exception) {
                if (preg_match('#^' . $exception . '$#', $path) != 0) {
                    continue 2;
                }
            }

            $file_api = get_php_file_api($path, true, false, false, true);
            if (!isset($file_api['__global']) || !isset($file_api['__global']['package'])) {
                $this->assertTrue(false, 'No addon defined (as @package) in ' . $path);
                continue;
            }
            $addon_name = $file_api['__global']['package'];

            foreach ($file_api as $class_name => $class_info) {
                if (!isset($class_info['functions']) || empty($class_info['functions'])) {
                    continue;
                }

                // Class controller exceptions; first, get matched functions which are actually defined.
                $check_only_on_funcs = [];
                foreach ($class_controllers as $class_exp => $controllers) {
                    if (preg_match('#^' . $class_exp . '$#', $class_name) != 0) {
                        $funcs = explode(',', $controllers);
                        foreach ($funcs as $func) {
                            if (isset($class_info['functions'][$func])) {
                                $check_only_on_funcs[] = $func;
                            }
                        }
                    }
                }

                // If we have at least one defined function, we only want to check against the defined functions.
                if (!empty($check_only_on_funcs)) {
                    $temp = $class_info['functions'];
                    $class_info['functions'] = [];

                    foreach ($check_only_on_funcs as $func) {
                        $class_info['functions'][$func] = $temp[$func];
                    }

                    unset($temp);
                }

                foreach ($class_info['functions'] as $function_name => $function_info) {
                    // Ignore functions without code defined
                    if ((!isset($function_info['code'])) || ($function_info['code'] === null)) {
                        continue;
                    }

                    // Skip non-public functions because we assume they will never be called if the addon is not installed.
                    if (isset($function_info['visibility']) && ($function_info['visibility'] != 'public')) {
                        continue;
                    }

                    // Exceptions by class::function expressions
                    foreach ($function_exceptions as $exception) {
                        if (preg_match('#^' . $exception . '$#', $class_name . '::' . $function_name) != 0) {
                            continue 2;
                        }
                    }

                    // At this point, we just want the function body contents, not the entire function definition
                    $open_brace = strpos($function_info['code'], '{');
                    $close_brace = strrpos($function_info['code'], '}');
                    $code = trim(substr($function_info['code'], ($open_brace + 1), ($close_brace - $open_brace - 1)));

                    // Ignore empty functions
                    if ($code == '') {
                        continue;
                    }

                    // Ignore functions that always return null or void
                    if (($code == 'return null;') || ($code == 'return;')) {
                        continue;
                    }

                    // Ignore functions that always return what we consider to be empty or nothing, unless the function itself can return null
                    if (($function_info['php_return_type_nullable'] === false) && in_array($code, ['return [];', 'return \'\';', 'return 0;', 'return new Tempcode();', 'return false;'])) {
                        continue;
                    }

                    $has = (strpos($code, 'addon_installed(\'' . addslashes($addon_name) . '\')') !== false) || (strpos($code, 'addon_installed__messaged(\'' . addslashes($addon_name) . '\'') !== false);

                    // For cns_forum, it is also acceptable to run a get_forum_type() check on 'cns' as the guard.
                    if (($addon_name == 'cns_forum') && ((strpos($code, 'get_forum_type() != \'cns\'') !== false) || (strpos($code, 'get_forum_type() == \'cns\'') !== false))) {
                        $has = true;
                    } else {
                        // Check if our guard is a negative guard
                        if ($has) {
                            $has_not = (strpos($code, '!addon_installed(\'' . addslashes($addon_name) . '\')') !== false) || (strpos($code, '!addon_installed__messaged(\'' . addslashes($addon_name) . '\'') !== false);

                            // Exception: if the addon_guard is part of a return statement, it's acceptable we do not have a negative guard.
                            if ($has_not === false) {
                                $prev_newline = false;
                                $has_line = strpos($code, 'addon_installed(\'' . addslashes($addon_name) . '\')');
                                if ($has_line === false) {
                                    $has_line = strpos($code, 'addon_installed__messaged(\'' . addslashes($addon_name) . '\')');
                                }
                                if ($has_line !== false) {
                                    $prev_newline = strrpos($code, "\n", $has_line);
                                    if ($prev_newline === false) {
                                        $prev_newline = 0;
                                    }
                                    if (strpos(trim(substr($code, $prev_newline, ($has_line - $prev_newline))), 'return') === 0) {
                                        $has_not = true;
                                    }
                                }
                            }

                            $this->assertTrue($has_not, 'Should probably have a negative addon guard for ' . $addon_name . ' towards the top of the function, not a positive one, in ' . $path . ' for ' . $class_name . '::' . $function_name);
                        }
                    }

                    if (
                        ($addon_name == 'core') || // No checks needed for core
                        (substr($addon_name, 0, 5) == 'core_') || // No checks needed for core
                        (($hook_type !== null) && (array_key_exists($hook_type . '/' . $hook_subtype, $this->hook_ownership)) && ($addon_name == $this->hook_ownership[$hook_type . '/' . $hook_subtype])) // No checks needed for self-ownership of hooks of particular addons
                    ) {
                        $this->assertTrue(!$has, 'No need to do addon check for ' . $addon_name . ' in ' . $path . ', in function ' . $class_name . '::' . $function_name);
                    } else {
                        $this->assertTrue($has, 'Missing addon check for ' . $addon_name . ' in ' . $path . ', in function ' . $class_name . '::' . $function_name);
                    }
                }
            }

            unset($file_api);
        }
    }

    public function testAddonGuardsImplicitCodeCalls()
    {
        // TODO: add checks for template symbols
        // TODO: make code scanning more concise; scan by function, not by entire file, so we are less likely to miss a missing guard because one exists in another function
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
                    $num_matches = preg_match_all('#(require_lang|require_code|require_css|require_javascript|do_template|get_option)\(\'([^\']*)\'(,[^\),]*)?#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $include = $matches[2][$i];
                        $extra = $matches[3][$i];

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
                            case 'get_option':
                                if (strpos($extra, 'true') !== false) { // We specified missing is okay
                                    continue 2;
                                }
                                $included_file = 'sources/hooks/systems/config/' . $include . '.php';
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
                                    if ($file_in_addon == 'news_shared') { // news_shared should also accept news
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

                                $error_message = 'Cannot find a guard for the ' . $file_in_addon . ' addon in ' . $path . ' [' . $addon_name . '], due to ' . $matches[1][$i] . '(\'' . $matches[2][$i] . '\'';

                                if (in_array($error_message, [
                                    'Cannot find a guard for the google_appengine addon in sources/global.php [core], due to require_code(\'google_appengine\'',
                                    'Cannot find a guard for the chat addon in sources/global2.php [core], due to require_code(\'chat_poller\'',
                                    'Cannot find a guard for the catalogues addon in sources/crud_module.php [core], due to require_javascript(\'catalogues\'',
                                    'Cannot find a guard for the catalogues addon in sources/crud_module.php [core], due to do_template(\'CATALOGUE_ADDING_SCREEN\'',
                                    'Cannot find a guard for the catalogues addon in sources/crud_module.php [core], due to do_template(\'CATALOGUE_EDITING_SCREEN\'',
                                    'Cannot find a guard for the backup addon in sources/minikernel.php [core], due to do_template(\'RESTORE_HTML_WRAP\'',
                                    'Cannot find a guard for the installer addon in sources/minikernel.php [core], due to do_template(\'INSTALLER_HTML_WRAP\'',
                                    'Cannot find a guard for the backup addon in sources/minikernel.php [core], due to do_template(\'RESTORE_HTML_WRAP\'',
                                    'Cannot find a guard for the installer addon in sources/minikernel.php [core], due to do_template(\'INSTALLER_HTML_WRAP\'',
                                    'Cannot find a guard for the cns_post_templates addon in sources/cns_general_action.php [core_cns], due to require_lang(\'cns_post_templates\'',
                                    'Cannot find a guard for the welcome_emails addon in sources/cns_general_action.php [core_cns], due to require_lang(\'cns_welcome_emails\'',
                                    'Cannot find a guard for the cns_post_templates addon in sources/cns_general_action2.php [core_cns], due to require_lang(\'cns_post_templates\'',
                                    'Cannot find a guard for the cns_post_templates addon in sources/cns_general_action2.php [core_cns], due to require_lang(\'cns_post_templates\'',
                                    'Cannot find a guard for the welcome_emails addon in sources/cns_general_action2.php [core_cns], due to require_lang(\'cns_welcome_emails\'',
                                    'Cannot find a guard for the welcome_emails addon in sources/cns_general_action2.php [core_cns], due to require_lang(\'cns_welcome_emails\'',
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

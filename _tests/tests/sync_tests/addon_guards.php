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

    // A list of expressions of class::function where we require use of !addon_installed__messaged, not !addon_installed.
    protected $must_use_messaged = [
        'Block_\w+\:\:run',
        'Module_\w+\:\:(pre_run|run_start|run)',
        '__global:\:\w+_script', // We assume entry point scripts always use a *_script function
    ];

    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('phpdoc');
    }

    public function testAddonGuardsCohesion()
    {
        $info = 'testAddonGuardsCohesion: This is an aggressive test; consider reviewing the defined exceptions periodically for accuracy.';
        $this->dump($info, 'INFO');

        $info = 'testAddonGuardsCohesion: This test does not check entry-point scripts to ensure use of !addon_installed__messaged; you must check those manually.';
        $this->dump($info, 'NOTICE');

        require_code('files2');

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
            'Hook_payment_gateway_\w+' => 'is_available',
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

        // Function exceptions which should allow positive guards, defined as a regex of class::function (class is __global if none).
        $function_positive_guard_exceptions = [
             // Complex return structures
            'Hook_health_check_\w+\:\:run',
            'Hook_preview_\w+\:\:applies',
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

                    // Check for improper use of !addon_installed regardless of addon
                    foreach ($this->must_use_messaged as $check) {
                        if (preg_match('#^' . $check . '$#', $class_name . '::' . $function_name) != 0) {
                            $ok = (strpos($code, '!addon_installed(') === false);
                            $this->assertTrue($ok, 'Must use !addon_installed__messaged, not !addon_installed, in ' . $path . ' for ' . $class_name . '::' . $function_name);
                        }
                    }

                    $has = (strpos($code, 'addon_installed(\'' . addslashes($addon_name) . '\')') !== false) || (strpos($code, 'addon_installed__messaged(\'' . addslashes($addon_name) . '\'') !== false);

                    // For cns_forum, it is also acceptable to run a get_forum_type() check on 'cns' as the guard.
                    if (($addon_name == 'cns_forum') && ((strpos($code, 'get_forum_type() != \'cns\'') !== false) || (strpos($code, 'get_forum_type() == \'cns\'') !== false))) {
                        $has = true;
                    } else {
                        // Exceptions by class::function expressions
                        $skip_negative_check = false;
                        foreach ($function_positive_guard_exceptions as $exception) {
                            if (preg_match('#^' . $exception . '$#', $class_name . '::' . $function_name) != 0) {
                                $skip_negative_check = true;
                            }
                        }

                        // Check if our guard is a negative guard
                        if (($has === true) && ($skip_negative_check === false)) {
                            $has_not = (strpos($code, '!addon_installed(\'' . addslashes($addon_name) . '\')') !== false) || (strpos($code, '!addon_installed__messaged(\'' . addslashes($addon_name) . '\'') !== false);

                            // Exception: if the addon_guard is part of a return statement, it's acceptable we do not have a negative guard.
                            if ($has_not === false) {
                                $prev_newline = false;
                                $has_line = strpos($code, 'addon_installed(\'' . addslashes($addon_name) . '\')');
                                if ($has_line === false) {
                                    $has_line = strpos($code, 'addon_installed__messaged(\'' . addslashes($addon_name) . '\')');
                                }
                                if ($has_line !== false) {
                                    $offset = 0;
                                    $prev_newline = strrpos($code, "\n", $has_line);
                                    if (is_numeric($prev_newline)) { // NB: No idea why but IDEs and code checker is extremely picky; we have to force the value to int
                                        $offset = intval($prev_newline);
                                    }
                                    if (strpos(trim(substr($code, $offset, ($has_line - $offset))), 'return') === 0) {
                                        $has_not = true;
                                    }
                                }
                            }

                            $this->assertTrue($has_not, 'Package addon guard ' . $addon_name . ' should be a negative guard (not a positive one) placed towards the top of the function, which returns if the addon is not installed, in ' . $path . ' for ' . $class_name . '::' . $function_name);
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
        $info = 'testAddonGuardsImplicitCodeCalls: This is an aggressive test; consider reviewing the defined exceptions periodically for accuracy. Also note this test does not support short form if statements.';
        $this->dump($info, 'INFO');

        // Functions that we must check to ensure they are addon-guarded (be sure to modify $included_file code if you change this)
        $functions_needing_guarded = [
            'require_lang',
            'require_code',
            'require_css',
            'require_javascript',
            'do_template',
            'get_option',
        ];

        /*
            This is a map of class expressions to a comma-delimited list of functions where we define
            globally-applicable addon guards. It is assumed that any / all addon guards defined in
            any of the given functions for matched classes, regardless how they are asserted, will
            completely disable the rest of the class if the addon is not installed. Therefore,
            if an addon guard is defined in any matched functions, it will make tests in all other
            functions for that class (and for that addon) pass automatically.
        */
        $class_controllers = [
            'Hook_\w+' => 'info',
            'Module_\w+' => 'pre_run,run,run_start', // Not perfect; may need manual review

            'Hook_profiles_tabs_\w+' => 'is_active',
            'Hook_preview_\w+' => 'applies',
            'Hook_ecommerce_\w+' => 'is_available',
            'Module_admin_setupwizard' => 'has_themewizard_step', // Does not actually disable the class, but we use it in place of checking for the themewizard addon
            'Hook_rss_\w+' => 'has_access', // Careful! Make sure run is calling this.
        ];

        // Map of path::class_name::function_name to an array of addons which we always consider guarded even if not explicitly defined
        $exceptions = [
            // global3 isn't initialised yet so we cannot check addon_installed
            'sources/global2.php::__global::init__global2' => ['chat'],

            // TODO: cannot yet get test to work correctly for $missing_ok
            'sources/hooks/systems/page_groupings/core.php::Hook_page_groupings_core::run' => ['actionlog'],
            'sources/cns_install.php::__global::install_cns' => ['cns_forum'],
            'sources/cns_posts.php::__global::cns_may_edit_post_by' => ['tickets'],
            'sources/cns_posts.php::__global::cns_may_delete_post_by' => ['tickets'],

            'sources/minikernel.php::__global::fatal_exit' => ['backup', 'installer'], // Loaded explicitly
            'sources/minikernel.php::__global::warn_exit' => ['backup', 'installer'], // Loaded explicitly
            'sources/cns_welcome_emails.php::__global::cns_prepare_welcome_email' => ['newsletter'], // Too complex to refactor for the test
        ];

        // TODO: add checks for template symbols
        $files_in_addons = [];

        $addons = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($addons as $addon_name => $ob) {
            $files = $ob->get_file_list();
            foreach ($files as $path) {
                $files_in_addons[$path] = $addon_name;
            }
        }

        $debug = [];

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

                // TODO: this will not even work in exceptions, so we have to explicitly define it here and skip it
                if ($path == 'sources/hooks/modules/admin_setupwizard_installprofiles/community.php') {
                    continue;
                }

                if (!is_file(get_file_base() . '/' . $path)) {
                    continue;
                }

                if ((substr($path, -4) == '.php') && (preg_match('#(^_tests/|^data_custom/stress_test_loader\.php$|^sources/hooks/modules/admin_import/)#', $path) == 0)) {
                    $file_api = get_php_file_api($path, true, false, false, true);

                    foreach ($file_api as $class_name => $class_info) {
                        // Skip classes with no defined functions (e.g. abstracts)
                        if (!isset($class_info['functions']) || empty($class_info['functions'])) {
                            continue;
                        }

                        // Find global addon guards
                        $global_guards = [];
                        foreach ($class_controllers as $class_exp => $controllers) {
                            if (preg_match('#^' . $class_exp . '$#', $class_name) != 0) {
                                $funcs = explode(',', $controllers);
                                foreach ($funcs as $func) {
                                    if (isset($class_info['functions'][$func])) {
                                        // Ignore functions without code defined (e.g. abstracts or just empty functions)
                                        if ((!isset($class_info['functions'][$func]['code'])) || ($class_info['functions'][$func]['code'] === null)) {
                                            continue;
                                        }
                                        $c = '<' . '?php ' . "\n" . $class_info['functions'][$func]['code']; // Must be valid PHP code when we tokenise it

                                        $debug[$path . '::' . $class_name . '::' . $func][] = 'SEARCHING for global addon_guards';

                                        // Tokenise and prepare
                                        $tokens = token_get_all($c);
                                        $tracking_call = '';
                                        foreach ($tokens as $token) {
                                            if (is_array($token)) {
                                                list($token_id, $token_text) = $token;

                                                if (($token_id == T_STRING)) { // could be an addon_installed check
                                                    if ($tracking_call == '') {
                                                        if (($token_text == 'addon_installed') || ($token_text == 'addon_installed__messaged') || ($token_text == '!addon_installed') || ($token_text == '!addon_installed__messaged')) {
                                                            $tracking_call = 'addon_installed';
                                                            $debug[$path . '::' . $class_name . '::' . $func][] = 'In addon guard call';
                                                        }
                                                        if (($token_text == 'get_forum_type')) { // cns checks also work for cns_forum
                                                            $tracking_call = 'get_forum_type';
                                                            $debug[$path . '::' . $class_name . '::' . $func][] = 'In get_forum_type call';
                                                        }
                                                    }
                                                } elseif (($token_id == T_CONSTANT_ENCAPSED_STRING)) { // could be the addon in a guard check
                                                    if ($tracking_call == 'addon_installed') {
                                                        $addon = preg_replace('/\'(\w+)\'/', '$1', $token_text);
                                                        $debug[$path . '::' . $class_name . '::' . $func][] = 'FOUND global addon guard ' . $addon;
                                                        $global_guards[] = $addon;

                                                        // news is also an acceptable guard for news_shared
                                                        if ($addon == 'news') {
                                                            $global_guards[] = 'news_shared';
                                                        }

                                                        $tracking_call = '';
                                                    }
                                                    if (($tracking_call == 'get_forum_type') && (preg_replace('/\'(\w+)\'/', '$1', $token_text) == 'cns')) { // cns checks also work for cns_forum
                                                        $addon = 'cns_forum';
                                                        $debug[$path . '::' . $class_name . '::' . $func][] = 'FOUND global addon guard ' . $addon;
                                                        $global_guards[] = $addon;
                                                        $tracking_call = '';
                                                    }
                                                    $tracking_call = ''; // False positive
                                                }
                                            } else {
                                                $token_text = $token;
                                                if (($tracking_call == 'addon_installed') && ($token_text == ')')) { // False positive
                                                    $tracking_call = '';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        foreach ($class_info['functions'] as $function_name => $function_info) {
                            // Ignore functions without code defined (e.g. abstracts or just empty functions)
                            if ((!isset($function_info['code'])) || ($function_info['code'] === null)) {
                                continue;
                            }
                            $c = '<' . '?php ' . "\n" . $function_info['code']; // Must be valid PHP code when we tokenise it

                            // Save time and memory by skipping this function if we aren't calling anything that needs a guard
                            $matches = [];
                            $num_matches = preg_match_all('#(' . implode('|', $functions_needing_guarded) . ')\(\'([^\']*)\'#', $c, $matches);
                            if ($num_matches == 0) {
                                continue;
                            }

                            // Tokenise and prepare
                            $tokens = token_get_all($c);
                            $active_guards = [];
                            $guard_buffer = [];
                            $condition_buffer = '';
                            $in_if = false;
                            $active_call_braces = 0;
                            $active_call = '';
                            $active_call_param = '';
                            $active_call_param_2 = '';

                            $debug[$path . '::' . $class_name . '::' . $function_name] = [];

                            foreach ($tokens as $token) {
                                if (is_array($token)) {
                                    list($token_id, $token_text) = $token;

                                    // Start of an if or elseif; reset things and begin tracking conditions
                                    if (($token_id == T_IF) || ($token_id == T_ELSEIF)) {
                                        $in_if = true;
                                        $guard_buffer = [];
                                        $condition_buffer = '';
                                        $debug[$path . '::' . $class_name . '::' . $function_name][] = 'START_IF_COND (code block stack is ' . strval(count($active_guards)) . ' items)';
                                    } elseif ($in_if) { // Still tracking if conditions; add to buffer
                                        $condition_buffer .= $token_text;
                                    } elseif (($token_id == T_STRING)) { // could be a call to a function we want to track
                                        if (in_array($token_text, $functions_needing_guarded)) {
                                            $active_call_braces = 0;
                                            $active_call = $token_text;
                                            $active_call_param = '';
                                            $active_call_param_2 = '';
                                            $debug[$path . '::' . $class_name . '::' . $function_name][] = 'TRACKING ' . $token_text;
                                        }
                                    } elseif (($token_id == T_CONSTANT_ENCAPSED_STRING)) { // could be a parameter to a function we are tracking
                                        if ($active_call != '') {
                                            if ($active_call_param == '') {
                                                $active_call_param = preg_replace('/\'(\w+)\'/', '$1', $token_text);
                                                $debug[$path . '::' . $class_name . '::' . $function_name][] = 'TRACKING PARAM 1: ' . $token_text;
                                            } elseif ($active_call_param_2 == '') {
                                                $value = preg_replace('/\'(\w+)\'/', '$1', $token_text);
                                                if (($active_call == 'get_option') && (!in_array($value, ['false', 'true']))) { // If we got weirdness in get_option then ignore it
                                                    $active_call = '';
                                                    $debug[$path . '::' . $class_name . '::' . $function_name][] = 'IGNORED get_option as we either have a complex parameter 1 or an invalid parameter 2';
                                                } else {
                                                    $active_call_param_2 = $value;
                                                    $debug[$path . '::' . $class_name . '::' . $function_name][] = 'TRACKING PARAM 2: ' . $token_text;
                                                }
                                            }
                                        }
                                    } else {
                                        $debug[$path . '::' . $class_name . '::' . $function_name][] = 'generic ' . token_name($token_id) . ' ' . $token_text;
                                    }
                                } else {
                                    $token_text = $token;

                                    if ($token_text == '{') {
                                        if (trim($condition_buffer) != '') { // We probably just finished up collecting if conditions, so process them
                                            // Search for addon_installed and addon_installed__messaged conditions and note the addons contained in them
                                            $expressions = ['/(addon|\!addon)_installed\(\'(\w+)\'/', '/(addon|\!addon)_installed__messaged\(\'(\w+)\'/'];
                                            foreach ($expressions as $expression) {
                                                $matches = [];
                                                if (preg_match_all($expression, $condition_buffer, $matches) > 0) {
                                                    foreach ($matches[0] as $i => $match) {
                                                        if (strpos($match, '!') !== 0) {
                                                            $guard_buffer[] = $matches[2][$i];

                                                            // news_shared can also be guarded with news
                                                            if ($matches[2][$i] == 'news') {
                                                                $guard_buffer[] = 'news_shared';
                                                            }
                                                        } elseif (count($active_guards) > 0) { // We assume a negated guard will always return, so this behaves the same as a positive guard on its parent
                                                            $active_guards[count($active_guards) - 1][] = $matches[2][$i];
                                                            // news_shared can also be guarded with news
                                                            if ($matches[2][$i] == 'news') {
                                                                $active_guards[count($active_guards) - 1][] = 'news_shared';
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            // special: get_forum_type() == 'cns' is also a guard for cns_forum
                                            if (strpos($condition_buffer, 'get_forum_type() == \'cns\'') !== false) {
                                                $guard_buffer[] = 'cns_forum';
                                            }
                                            if ((count($active_guards) > 0) && (strpos($condition_buffer, 'get_forum_type() != \'cns\'') !== false)) { // We assume a negated guard will always return, so this behaves the same as a positive guard on its parent
                                                $active_guards[count($active_guards) - 1][] = 'cns_forum';
                                            }

                                            $debug[$path . '::' . $class_name . '::' . $function_name][] = 'START_IF_CODE_BLOCK (buffer): ' . $condition_buffer;
                                            $debug[$path . '::' . $class_name . '::' . $function_name][] = 'START_IF_CODE_BLOCK (active guards): ' . json_encode($active_guards, JSON_PRETTY_PRINT);
                                        } else {
                                            $debug[$path . '::' . $class_name . '::' . $function_name][] = 'START_CODE_BLOCK';
                                        }

                                        // Push active guards for this code block and then reset buffers
                                        $condition_buffer = '';
                                        $active_guards[] = $guard_buffer;
                                        $guard_buffer = [];
                                        $in_if = false;
                                    } elseif ($token_text == '}') {
                                        // Pop off the end of the active guards array as these guards are not in effect anymore
                                        if (!empty($active_guards)) {
                                            $debug[$path . '::' . $class_name . '::' . $function_name][] = 'END_CODE_BLOCK';
                                            array_pop($active_guards);
                                        }
                                    } elseif ($in_if) { // Still tracking if conditions; add to buffer
                                        $condition_buffer .= $token_text;
                                    } elseif ($active_call != '') { // Tracking a function call
                                        if ($token_text == '(') {
                                            $active_call_braces++;
                                        } elseif ($token_text == ')') {
                                            $active_call_braces--;
                                            if ($active_call_braces == 0) {
                                                $debug[$path . '::' . $class_name . '::' . $function_name][] = 'EXECUTE ADDON GUARD TEST ON ' . $active_call;
                                                $included_file = '';
                                                switch ($active_call) {
                                                    case 'require_lang':
                                                        $included_file = 'lang/EN/' . $active_call_param . '.ini';
                                                        break;
                                                    case 'require_code':
                                                        $included_file = 'sources/' . $active_call_param . '.php';
                                                        break;
                                                    case 'require_css':
                                                        $included_file = 'themes/default/css/' . $active_call_param . '.css';
                                                        break;
                                                    case 'require_javascript':
                                                        $included_file = 'themes/default/javascript/' . $active_call_param . '.js';
                                                        break;
                                                    case 'do_template':
                                                        $included_file = 'themes/default/templates/' . $active_call_param . '.tpl';
                                                        break;
                                                    case 'get_option':
                                                        if (strpos($active_call_param_2, 'true') === false) { // We did not specify missing is okay
                                                            $included_file = 'sources/hooks/systems/config/' . $active_call_param . '.php';
                                                        }
                                                        break;
                                                }

                                                if (($included_file != '') && isset($files_in_addons[$included_file])) {
                                                    $file_in_addon = $files_in_addons[$included_file];
                                                    $debug[$path . '::' . $class_name . '::' . $function_name][] = 'BELONGS to addon ' . $file_in_addon;
                                                    if (
                                                        ($file_in_addon != $addon_name) && // No need to guard against itself
                                                        (substr($file_in_addon, 0, 5) != 'core_') && // Core addons will always be installed
                                                        ($file_in_addon != 'core') && // Core addons will always be installed
                                                        (
                                                            (!in_array($file_in_addon, $requires)) && // No need to guard if the addon was marked required
                                                            ((!in_array('news', $requires)) || ($file_in_addon != 'news_shared')) // news / news_shared
                                                        )
                                                    ) {
                                                        $found_guard = false;

                                                        // Explicit exceptions
                                                        if (isset($exceptions[$path . '::' . $class_name . '::' . $function_name]) && (in_array($file_in_addon, $exceptions[$path . '::' . $class_name . '::' . $function_name]))) {
                                                            $debug[$path . '::' . $class_name . '::' . $function_name][] = 'EXCEPTION defined for addon';
                                                            $found_guard = true;
                                                        }

                                                        // Look in global addon guards
                                                        if (!$found_guard) {
                                                            foreach ($global_guards as $active_guard) {
                                                                if ($file_in_addon == $active_guard) {
                                                                    $debug[$path . '::' . $class_name . '::' . $function_name][] = 'GUARDED globally';
                                                                    $found_guard = true;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        // Look within our active addon guard stack
                                                        if (!$found_guard) {
                                                            foreach ($active_guards as $_active_guards) {
                                                                if (in_array($file_in_addon, $_active_guards)) {
                                                                    $debug[$path . '::' . $class_name . '::' . $function_name][] = 'GUARDED';
                                                                    $found_guard = true;
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                        // Check explicit hook ownership
                                                        if (!$found_guard) {
                                                            $matches_hook_details = [];
                                                            if (preg_match('#^\w+/hooks/(\w+)/(\w+)/\w+\.php$#', $path, $matches_hook_details) != 0) {
                                                                $hook_type = $matches_hook_details[1];
                                                                $hook_subtype = $matches_hook_details[2];

                                                                if ((array_key_exists($hook_type . '/' . $hook_subtype, $this->hook_ownership)) && ($file_in_addon == $this->hook_ownership[$hook_type . '/' . $hook_subtype])) {
                                                                    $debug[$path . '::' . $class_name . '::' . $function_name][] = 'GUARDED via hook ownership';
                                                                    $found_guard = true;
                                                                }
                                                            }
                                                        }

                                                        if (!$found_guard) {
                                                            $debug[$path . '::' . $class_name . '::' . $function_name][] = 'NOT GUARDED!!!';
                                                        }

                                                        $this->assertTrue($found_guard, 'The call to ' . $active_call . '(\'' . $active_call_param . '\') in ' . $path . '::' . $class_name . '::' . $function_name . ' seems to be missing an addon guard for ' . $file_in_addon);
                                                    } else {
                                                        $debug[$path . '::' . $class_name . '::' . $function_name][] = 'NO GUARD NEEDED; either is itself, a core addon, or listed as a required dependency';
                                                    }
                                                }

                                                // Reset function tracking
                                                $active_call = '';
                                                $active_call_param = '';
                                                $active_call_param_2 = '';
                                            }
                                        }
                                    } else {
                                        $debug[$path . '::' . $class_name . '::' . $function_name][] = 'generic2 ' . $token_text;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        if ($this->debug) {
            $this->dump($debug, 'DEBUG');
        }
    }
}

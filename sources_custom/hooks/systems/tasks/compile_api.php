<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_tutorials
 */

/**
 * Hook class.
 */
class Hook_task_compile_api
{
    /**
     * Run the task hook.
     *
     * @return ?array A tuple of at least 2: Return mime-type, content (either Tempcode, or a string, or a filename and file-path pair to a temporary file), map of HTTP headers if transferring immediately, map of ini_set commands if transferring immediately (null: show standard success message)
     */
    public function run() : ?array
    {
        disable_php_memory_limit();
        $old = cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL); // Can be very, very slow

        /* Re-compile function signatures */

        $file_classes = [];

        require_code('phpdoc');
        require_code('files');
        require_code('files2');

        $bitmask = IGNORE_ACCESS_CONTROLLERS | IGNORE_HIDDEN_FILES | IGNORE_EDITFROM_FILES | IGNORE_REVISION_FILES | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_ALIEN | IGNORE_UPLOADS | IGNORE_CUSTOM_ZONES | IGNORE_CUSTOM_DIRS | IGNORE_NONBUNDLED;

        // Get all the files we need to scan based on addon hooks
        $addons = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        $files_to_process = ['sources_custom/phpstub.php']; // We also want to process the phpstub which is normally ignored by the bitmask
        foreach ($addons as $addon_name => $ob) {
            $files = $ob->get_file_list();
            foreach ($files as $path) {
                // We only want to scan bundled PHP files
                if (should_ignore_file($path, $bitmask) || substr($path, -4) != '.php') {
                    continue;
                }

                // This is bundled third-party code; we do not want this in our API documentation
                if (strpos($path, 'sources/diff/') !== false) {
                    continue;
                }

                $files_to_process[] = $path;
            }
        }

        // Process our files
        foreach ($files_to_process as $path) {
            // Skip files that do not exist
            if (!is_file(get_file_base() . '/' . $path)) {
                continue;
            }

            // Get our API structure, and generate a GUID for this file
            $file_api = get_php_file_api($path, false, false, false, true);

            // We need to separately track the classes defined in this file so we can cross-link them on the Comcode pages
            foreach ($file_api as $class_name => $class_data) {
                if (!isset($file_classes[$class_name])) {
                    $file_classes[$class_name] = [];
                }
                $file_classes[$class_name][$path] = $class_data;
            }
        }

        /* Save our API data in the database */

        push_db_scope_check(false);
        push_query_limiting(false);

        // First, delete everything we have right now in the database as it will be replaced
        $GLOBALS['SITE_DB']->query_delete('api_classes');
        $GLOBALS['SITE_DB']->query_delete('api_functions');
        $GLOBALS['SITE_DB']->query_delete('api_function_params');

        // Iterate over our API structure to save into the database
        foreach ($file_classes as $class_name => $class_definitions) {
            foreach ($class_definitions as $class_path => $class_data) {
                // Save class definition into the database
                $class_id = $GLOBALS['SITE_DB']->query_insert('api_classes', [
                    'c_name' => $class_name,
                    'c_source_url' => $class_path,
                    'c_is_abstract' => ((isset($class_data['is_abstract'])) && ($class_data['is_abstract'] === true) ? 1 : 0),
                    'c_implements' => isset($class_data['implements']) ? implode(',', $class_data['implements']) : '',
                    'c_traits' => isset($class_data['traits']) ? implode(',', $class_data['traits']) : '',
                    'c_extends' => isset($class_data['extends']) ? $class_data['extends'] : '',
                    'c_package' => isset($class_data['package']) ? $class_data['package'] : '',
                    'c_type' => isset($class_data['type']) ? $class_data['type'] : 'class',
                    'c_comment' => ((isset($class_data['comment'])) && ($class_data['comment'] === true) ? 1 : 0),

                    'c_edit_date' => time(), // TODO: crude
                ], true);

                // Process each function on the class
                if (isset($class_data['functions'])) {
                    foreach ($class_data['functions'] as $function_name => $function_data) {
                        // Save the function definition in the database
                        $function_id = $GLOBALS['SITE_DB']->query_insert('api_functions', [
                            'class_id' => $class_id,
                            'class_name' => $class_name,
                            'f_name' => $function_name,
                            'f_php_return_type' => isset($function_data['php_return_type']) ? $function_data['php_return_type'] : '',
                            'f_php_return_type_nullable' => ((isset($function_data['php_return_type_nullable'])) && ($function_data['php_return_type_nullable'] === true) ? 1 : 0),
                            'f_description' => isset($function_data['description']) ? $function_data['description'] : '',
                            'f_flags' => isset($function_data['flags']) ? implode(',', $function_data['flags']) : '',
                            'f_is_static' => ((isset($function_data['is_static'])) && ($function_data['is_static'] === true) ? 1 : 0),
                            'f_is_abstract' => ((isset($function_data['is_abstract'])) && ($function_data['is_abstract'] === true) ? 1 : 0),
                            'f_is_final' => ((isset($function_data['is_final'])) && ($function_data['is_final'] === true) ? 1 : 0),
                            'f_visibility' => isset($function_data['visibility']) ? $function_data['visibility'] : 'public',
                            'f_return_type' => isset($function_data['return']['type']) ? $function_data['return']['type'] : '',
                            'f_return_description' => isset($function_data['return']['description']) ? $function_data['return']['description'] : '',
                            'f_return_set' => isset($function_data['return']['set']) ? $function_data['return']['set'] : '',
                            'f_return_range' => isset($function_data['return']['range']) ? $function_data['return']['range'] : '',

                            'f_edit_date' => time(), // TODO: crude
                        ], true);

                        // Process parameters on each function, if they exist
                        if (isset($function_data['parameters'])) {
                            foreach ($function_data['parameters'] as $parameter) {
                                // Oops!
                                if (!isset($parameter['name'])) {
                                    continue;
                                }

                                // Save each parameter into the database
                                $GLOBALS['SITE_DB']->query_insert('api_function_params', [
                                    'function_id' => $function_id,
                                    'p_name' => $parameter['name'],
                                    'p_php_type' => isset($parameter['php_type']) ? $parameter['php_type'] : '',
                                    'p_php_type_nullable' => ((isset($parameter['php_type_nullable'])) && ($parameter['php_type_nullable'] === true) ? 1 : 0),
                                    'p_type' => isset($parameter['type']) ? $parameter['type'] : '',
                                    'p_set' => isset($parameter['set']) ? $parameter['set'] : '',
                                    'p_range' => isset($parameter['range']) ? $parameter['range'] : '',
                                    'p_ref' => ((isset($parameter['ref'])) && ($parameter['ref'] === true) ? 1 : 0),
                                    'p_is_variadic' => ((isset($parameter['is_variadic'])) && ($parameter['is_variadic'] === true) ? 1 : 0),
                                    'p_default' => array_key_exists('default', $parameter) ? serialize($parameter['default']) : '',
                                    'p_description' => isset($parameter['description']) ? $parameter['description'] : '',
                                ]);
                            }
                        }
                    }
                }
            }
        }

        pop_db_scope_check();
        pop_query_limiting();

        cms_set_time_limit($old);

        return null;
    }
}

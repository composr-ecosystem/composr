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

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('composr_tutorials', $error_msg)) {
    return $error_msg;
}
if (!addon_installed__messaged('testing_platform', $error_msg)) {
    return $error_msg;
}

require_lang('tutorials');

// Prompt for confirmation
if (post_param_integer('confirm', 0) == 0) {
    $preview = do_lang_tempcode('COMPILE_API_CONFIRM');
    $title = get_screen_title(do_lang('COMPILE_API_TITLE'), false);
    $url = get_self_url(false, false);
    return do_template('CONFIRM_SCREEN', ['_GUID' => 'aab9607451ae5c18a9e7a0756766453d', 'TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => form_input_hidden('confirm', '1'), 'URL' => $url]);
}

$title = get_screen_title(do_lang('COMPILE_API_TITLE'), false);
$title->evaluate_echo();

disable_php_memory_limit();
cms_extend_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);

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

/* Save our API data in a structured format according to class and function */

// First, delete everything we have right now so we can clean up anything that does not exist anymore
$save_path = get_file_base() . '/data_custom/modules/api';
deldir_contents($save_path, true);

foreach ($file_classes as $class_name => $class_definitions) {
    // Make a directory for each class
    $api_dir = $save_path . '/' . $class_name;
    make_missing_directory($api_dir, true);

    // Parse function and class definitions for this class
    $function_definitions = [];
    foreach ($class_definitions as $class_path => &$data) {
        if (isset($data['functions'])) {
            foreach ($data['functions'] as $function_name => $function_data) {
                if (!isset($function_definitions[$function_name])) {
                    $function_definitions[$function_name] = [];
                }
                $function_definitions[$function_name][$class_path] = $function_data;
            }
        }

        // Make sure function definitions are not included when we later save class definition files
        unset($data['functions']);
    }

    // Make a data file for each class::function
    foreach ($function_definitions as $function_name => $definitions) {
        cms_file_put_contents_safe($api_dir . '/' . $function_name . '.bin', serialize($definitions));
    }

    // Make a data file for each class definition
    $api_dir = $save_path . '/' . $class_name;
    cms_file_put_contents_safe($api_dir . '/___class_definitions.bin', serialize($class_definitions));
}

return do_lang_tempcode('SUCCESS');

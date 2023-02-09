<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class hooks_test_set extends cms_test_case
{
    public function setup()
    {
        parent::setup();

        disable_php_memory_limit();
    }

    public function testClassNames()
    {
        require_code('files2');

        // Hook type/subtypes to ignore
        $exceptions_hooks = [
            // These hooks do not have classes
            'systems/disposable_values',
            'systems/non_active_urls',
        ];

        // Prefixes that will be ignored. Key is prefix, value is hook type/subtype.
        $exceptions_prefixes = [
            'Hx_health_check_', // systems/health_checks; Hx extends the original Hook_health_check_ class in a sources_custom override
        ];

        // Grab the hook files
        $files = get_directory_contents(get_file_base() . '/sources/hooks', get_file_base() . '/sources/hooks', 0, true, true, ['php']);
        $files = array_merge($files, get_directory_contents(get_file_base() . '/sources_custom/hooks', get_file_base() . '/sources_custom/hooks', 0, true, true, ['php']));

        $hook_structure = [];

        foreach ($files as $path) {
            // Determine hook type and subtype from the path
            $path_parts = explode('/', $path);
            $hook_type = $path_parts[count($path_parts) - 3];
            $hook_subtype = $path_parts[count($path_parts) - 2];
            $hook_name = str_replace('.php', '', $path_parts[count($path_parts) - 1]);

            // Skip hooks to be ignored
            if (in_array($hook_type . '/' . $hook_subtype, $exceptions_hooks)) {
                continue;
            }

            // Initialize hook structure
            if (!isset($hook_structure[$hook_type . '/' . $hook_subtype])) {
                $hook_structure[$hook_type . '/' . $hook_subtype] = [];
            }

            // Get the hook file contents
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

            // Get the defined class name from the hook file
            $class_preg = [];
            if (preg_match('#^\s*class ([\w]+)(\n|\s)#m', $c, $class_preg) === 0) {
                if (preg_match('#^\s*function init__(.*)+#m', $c) === 0) { // Might be missing a class because it is a simple hook code override; ignore these
                    $this->assertTrue(false, 'Could not find class name for ' . $path);
                }
                continue;
            } elseif (!array_key_exists(1, $class_preg)) {
                $this->assertTrue(false, 'Could not find class name for ' . $path);
                continue;
            } else {
                // Get the class name minus the hook name; the hook name might occur more than once in the class name, so ensure we only remove it from the end
                $suffix_pos = strrpos($class_preg[1], $hook_name);
                if ($suffix_pos !== false) {
                    $hook_prefix = substr_replace($class_preg[1], '', $suffix_pos, strlen($hook_name));
                } else {
                    $hook_prefix = $class_preg[1];
                }

                // Put the class name prefix in our structure array if it is not already in there and not to be ignored
                if (!in_array($hook_prefix, $hook_structure[$hook_type . '/' . $hook_subtype]) && !in_array($hook_prefix, $exceptions_prefixes)) {
                    $hook_structure[$hook_type . '/' . $hook_subtype][] = $hook_prefix;
                }
            }
        }

        // Loop over each structure to see if there are multiple prefixes (inconsistency)
        foreach ($hook_structure as $hook_type => $hook_prefixes) {
            $this->assertTrue((count($hook_prefixes) < 2), 'Multiple class prefixes used for the ' . $hook_type . ' hooks: ' . implode(', ', $hook_prefixes));
        }
    }
}

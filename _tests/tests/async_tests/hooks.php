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

        // Hook type/sub-types to ignore
        $exceptions_hooks = [
            // These hooks do not have classes
            'systems/disposable_values',
            'systems/non_active_urls',
        ];

        // Remap the key prefix to the value prefix.
        $prefix_remaps = [
            'Hx_' => 'Hook_',
        ];

        // Grab the hook files
        $files = get_directory_contents(get_file_base() . '/sources/hooks', get_file_base() . '/sources/hooks', 0, true, true, ['php']);
        $files = array_merge($files, get_directory_contents(get_file_base() . '/sources_custom/hooks', get_file_base() . '/sources_custom/hooks', 0, true, true, ['php']));

        $hook_structure = [];

        foreach ($files as $path) {
            // Determine hook type and sub-type from the path
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

                // Remap the prefix if necessary
                if (array_key_exists($hook_prefix, $prefix_remaps)) {
                    $hook_prefix = $prefix_remaps[$hook_prefix];
                }

                // Put the class name prefix in our structure array if it is not already in there and not to be ignored
                if (!in_array($hook_prefix, $hook_structure[$hook_type . '/' . $hook_subtype])) {
                    $hook_structure[$hook_type . '/' . $hook_subtype][] = $hook_prefix;
                }
            }
        }

        $prefix_to_hook_type = [];
        foreach ($hook_structure as $hook_type => $hook_prefixes) {
            // Check that the hook type is not using multiple prefixes
            $this->assertTrue((count($hook_prefixes) < 2), 'Multiple class prefixes are being used for the ' . $hook_type . ' hooks: ' . implode(', ', $hook_prefixes));

            // Check that a prefix is not being used by multiple hook types/sub-types
            foreach ($hook_prefixes as $hook_prefix) {
                $already_used = (isset($prefix_to_hook_type[$hook_prefix]) && $prefix_to_hook_type[$hook_prefix] != $hook_type);
                $this->assertTrue(!$already_used, 'The class prefix ' . $hook_prefix . ' is being used in hooks across multiple types/subtypes.');

                if (!$already_used) {
                    $prefix_to_hook_type[$hook_prefix] = $hook_type;
                }
            }
        }
    }
}

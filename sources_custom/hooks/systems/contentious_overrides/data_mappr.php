<?php /*

Composr
Copyright (c) Christopher Graham, 2004-2024

See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    data_mappr
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_data_mappr
{
    public function compile_included_code($path, $codename, &$code)
    {
        if ($codename != 'catalogues2') {
            return;
        }

        if (!addon_installed('data_mappr')) {
            return;
        }

        if ($code === null) {
            $code = clean_php_file_for_eval(file_get_contents($path));
        }

        require_code('override_api');

        $functions = [
            'actual_edit_catalogue',
            'actual_edit_catalogue_category',
            'actual_delete_catalogue_category',
            'actual_add_catalogue_entry',
            'actual_edit_catalogue_entry',
            'actual_delete_catalogue_entry'
        ];
        foreach ($functions as $function) {
            insert_code_after__by_command(
                $code,
                $function,
                'delete_cache_entry(\'main_cc_embed\');',
                'delete_cache_entry(\'main_google_map\');',
                1,
                true
            );
        }
    }
}

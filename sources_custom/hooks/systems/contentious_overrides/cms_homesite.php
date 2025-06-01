<?php /*

Composr
Copyright (c) Christopher Graham, 2004-2024

See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_contentious_overrides_cms_homesite
{
    public function compile_included_code($path, $codename, &$code)
    {
        if (!addon_installed('cms_homesite')) {
            return;
        }

        if (strpos($path, 'sources_custom/') !== false) {
            return;
        }

        switch ($codename) {
            // Homesite addon management
            case 'hooks/systems/ajax_tree/choose_download':
                require_code('override_api');
                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path), $path);
                }

                insert_code_after__by_command(
                    $code,
                    'run',
                    'require_code(\'global4\');',
                    "
                    if ((!is_numeric(\$id)) && (\$id != '')) {
                        \$_id = null;
                        if (substr(\$id, 0, 8) == 'Version ') {
                            \$id_float = floatval(substr(\$id, 8));
                            do {
                                \$str = 'Version ' . float_to_raw_string(\$id_float, 2, true);
                                \$parent = \$GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', [\$GLOBALS['SITE_DB']->translate_field_ref('category') => 'Addons']);
                                if (\$parent === null) {
                                    break;
                                }
                                \$_id = \$GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => \$parent, \$GLOBALS['SITE_DB']->translate_field_ref('category') => \$str]);
                                if (\$_id === null) {
                                    \$id_float -= 0.1;
                                }
                            } while ((\$_id === null) && (\$id_float > 0.0));
                        } else {
                            \$_id = \$GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', [\$GLOBALS['SITE_DB']->translate_field_ref('category') => \$id]);
                        }
                        if (\$_id === null) {
                            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'download_category'));
                        }
                        \$id = strval(\$_id);
                    }"
                );
                break;
        }
    }
}

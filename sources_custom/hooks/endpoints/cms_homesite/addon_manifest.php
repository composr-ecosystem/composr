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
class Hook_endpoint_cms_homesite_addon_manifest
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Info about the hook
     */
    public function info(?string $type, ?string $id) : array
    {
        return [
            'authorization' => false,
        ];
    }

    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        if (!addon_installed('cms_homesite')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite')));
        }
        if (!addon_installed('downloads')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('downloads')));
        }
        if (!addon_installed('news')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('news')));
        }

        if ($id === null) {
            $id = get_param_string('version');
        }

        $id_float = floatval($id);
        $_id = null;
        do {
            $str = 'Version ' . /*preg_replace('#\.0$#', '', */float_to_raw_string($id_float, 2, true)/*)*/;
            $__id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', [$GLOBALS['SITE_DB']->translate_field_ref('category') => 'Addons']);
            if ($__id === null) {
                break;
            }
            $_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $__id, $GLOBALS['SITE_DB']->translate_field_ref('category') => $str]);
            if ($_id === null) {
                $id_float -= 0.1;
            }
        } while (($_id === null) && ($id_float >= 0.0));

        if ($_id === null) {
            if ($id === '_LEGACY_') { // LEGACY
                echo serialize([]);
                exit;
            }
            return [];
        }

        require_code('selectcode');

        $addon_times = [];

        $filter_sql = selectcode_to_sqlfragment(strval($_id) . '*', 'id', 'download_categories', 'parent_id', 'category_id', 'id');

        $name_remap = [];

        foreach (array_keys($_POST) as $x) {
            if (substr($x, 0, 6) == 'addon_') {
                $addon_name = post_param_string($x);
                $addon_name_titled = titleify($addon_name);
                $name_remap[$addon_name_titled] = $addon_name;
                $query = 'SELECT d.id,url,name,edit_date,add_date FROM ' . get_table_prefix() . 'download_downloads d WHERE ' . db_string_equal_to($GLOBALS['SITE_DB']->translate_field_ref('name'), $addon_name_titled) . ' AND (' . $filter_sql . ')';
                $result = $GLOBALS['SITE_DB']->query($query, null, 0, false, true, ['name' => 'SHORT_TRANS']);

                $addon_times[intval(substr($x, 6))] = [null, null, null, $addon_name];

                if (array_key_exists(0, $result)) {
                    $url = $result[0]['url'];

                    $hash = null;
                    if (url_is_local($url)) {
                        $last_date = @filemtime(get_custom_file_base() . '/' . rawurldecode($url));
                        $hash = @hash_file('crc32', get_custom_file_base() . '/' . rawurldecode($url));
                    } else {
                        $last_date = @filemtime($url);
                        $hash = @hash_file('crc32', $url);
                    }
                    if ($last_date === false) {
                        $last_date = $result[0]['edit_date'] !== null ? intval($result[0]['edit_date']) : false;
                    }
                    if ($last_date === false) {
                        $last_date = $result[0]['add_date'] !== null ? intval($result[0]['add_date']) : false;
                    }
                    if ($last_date === false) {
                        continue;
                    }
                    if ($hash === false) {
                        $hash = null;
                    }

                    $name_titled = get_translated_text($result[0]['name']);
                    if (array_key_exists($name_titled, $name_remap)) {
                        $name = $name_remap[$name_titled];
                        $url = $result[0]['url'];
                        $id = $result[0]['id'];
                        $addon_times[intval(substr($x, 6))] = [$last_date, $id, $url, $name, $hash];
                    }
                }
            }
        }

        if ($id === '_LEGACY_') { // LEGACY
            echo serialize($addon_times);
            exit;
        }

        return $addon_times;
    }
}

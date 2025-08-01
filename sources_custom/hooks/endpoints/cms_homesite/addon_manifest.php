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
     * @return ?array Info about the hook (null: endpoint is disabled)
     */
    public function info(?string $type, ?string $id) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }
        if (!addon_installed('downloads')) {
            return null;
        }
        if (!addon_installed('news')) {
            return null;
        }

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
        if ($id === null) {
            $id = get_param_string('version');
        }

        // Locate our addon download categories for the provided software version (in ID)
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

        // No categories? Return empty.
        if ($_id === null) {
            if ($id === '_LEGACY_') { // LEGACY
                echo serialize([]);
                exit();
            }
            return [];
        }

        $addon_manifest = [];

        require_code('selectcode');

        $filter_sql = selectcode_to_sqlfragment(strval($_id) . '*', 'id', 'download_categories', 'parent_id', 'category_id', 'id');

        $name_remap = [];

        foreach (array_keys($_POST) as $x) {
            if (substr($x, 0, 6) == 'addon_') {
                // Query for the addon download
                $addon_name = post_param_string($x);
                $addon_name_titled = titleify($addon_name);
                $name_remap[$addon_name_titled] = $addon_name;
                $query = 'SELECT d.id,url,name,edit_date,add_date,additional_details FROM ' . get_table_prefix() . 'download_downloads d WHERE ' . db_string_equal_to($GLOBALS['SITE_DB']->translate_field_ref('name'), $addon_name_titled) . ' AND (' . $filter_sql . ')';
                $result = $GLOBALS['SITE_DB']->query($query, null, 0, false, true, ['name' => 'SHORT_TRANS', 'additional_details' => 'LONG_TRANS__COMCODE']);
                $addon_id = intval(substr($x, 6));

                // Prepare addon entry in our output
                $addon_manifest[$addon_id] = [
                    'name' => $addon_name,
                    'download_id' => null,
                    'download_guid' => null,
                    'download_url' => null,
                    'version' => null,
                    'updated' => null,
                    'hash' => null,
                ];

                // Populate our manifest with what we found in the database (if we found anything)
                if (array_key_exists(0, $result)) {
                    $name_titled = get_translated_text($result[0]['name']);
                    $additional_details = get_translated_text($result[0]['additional_details']);
                    $addon_manifest[$addon_id]['download_id'] = $result[0]['id'];
                    $addon_manifest[$addon_id]['download_url'] = $result[0]['url'];

                    // Security: We use resource GUID if Commandr is installed to prevent content scraping
                    if (addon_installed('commandr')) {
                        require_code('resource_fs');

                        $_id = find_guid_via_id('download', strval($result[0]['id']));
                        if ($_id === null) {
                            return ['success' => 'false', 'error_details' => do_lang('INTERNAL_ERROR', comcode_escape('TODO'))];
                        }
                        $addon_manifest[$addon_id]['download_guid'] = $_id;
                    } else {
                        $addon_manifest[$addon_id]['download_guid'] = strval($id);
                    }

                    // Determine the CRC32 hash
                    if (url_is_local($result[0]['url'])) {
                        $hash = @hash_file('crc32', get_custom_file_base() . '/' . rawurldecode($result[0]['url']));
                    } else {
                        $hash = @hash_file('crc32', $result[0]['url']);
                    }
                    if (is_string($hash)) {
                        $addon_manifest[$addon_id]['hash'] = $hash;
                    }

                    // Determine the updated date / time in this order: download edit date, file mtime, download add date
                    $last_date = (($result[0]['edit_date'] !== null) ? intval($result[0]['edit_date']) : false);
                    if ($last_date === false) {
                        if (url_is_local($result[0]['url'])) {
                            $last_date = @filemtime(get_custom_file_base() . '/' . rawurldecode($result[0]['url']));
                        } else {
                            $last_date = @filemtime($result[0]['url']);
                        }
                    }
                    if ($last_date === false) {
                        $last_date = (($result[0]['add_date'] !== null) ? intval($result[0]['add_date']) : false);
                    }
                    if ($last_date !== false) {
                        $addon_manifest[$addon_id]['updated'] = $last_date;
                    }

                    // Determine the addon version from the download additional details
                    $matches = [];
                    if (preg_match('/^Addon version ([\S]*)/', $additional_details, $matches) != 0) {
                        $addon_manifest[$addon_id]['version'] = strval($matches[1]);
                    }

                    // Did the addon name change?
                    if (array_key_exists($name_titled, $name_remap)) { // Integrity check
                        $addon_manifest[$addon_id]['name'] = $name_remap[$name_titled];
                    }
                }
            }
        }

        if ($id === '_LEGACY_') { // LEGACY
            echo serialize($addon_manifest);
            exit();
        }

        return $addon_manifest;
    }
}

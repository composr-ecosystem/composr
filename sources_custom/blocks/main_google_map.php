<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    data_mappr
 */

/**
 * Block class.
 */
class Block_main_google_map
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Kamen / Chris Graham / temp1024';
        $info['organisation'] = 'Miscellaneous';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = ['select', 'filter', 'title', 'region', 'cluster', 'geolocate_user', 'latfield', 'longfield', 'catalogue', 'width', 'height', 'zoom', 'center', 'latitude', 'longitude', 'show_links', 'min_latitude', 'max_latitude', 'min_longitude', 'max_longitude', 'star_entry', 'max_results', 'extra_sources', 'icon', 'guid'];
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled)
     */
    public function caching_environment() : ?array
    {
        $info = [];
        $info['cache_on'] = <<<'PHP'
        $map
PHP;
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT;
        $info['ttl'] = 60 * 24 * 7;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters
     * @return Tempcode The result of execution
     */
    public function run(array $map) : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('data_mappr', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('catalogues')) {
            return do_template('RED_ALERT', ['_GUID' => 'xulrf07ih9l80h4vjwdi4uxjgfg41hor', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('catalogues'))]);
        }

        require_code('catalogues');
        require_lang('google_map');
        require_lang('locations');

        $block_id = get_block_id($map);

        // Set up config/defaults
        if (!isset($map['title'])) {
            $map['title'] = '';
        }
        if (!isset($map['region'])) {
            $map['region'] = '';
        }
        if (!isset($map['latitude'])) {
            $map['latitude'] = '0';
        }
        if (!isset($map['longitude'])) {
            $map['longitude'] = '0';
        }
        $map_width = isset($map['width']) ? $map['width'] : '100%';
        if (is_numeric($map_width)) {
            $map_width .= 'px';
        }
        $map_height = isset($map['height']) ? $map['height'] : '300px';
        if (is_numeric($map_height)) {
            $map_height .= 'px';
        }
        $set_zoom = isset($map['zoom']) ? $map['zoom'] : '3';
        $set_center = isset($map['center']) ? $map['center'] : '0';
        $set_show_links = isset($map['show_links']) ? $map['show_links'] : '1';
        $cluster = isset($map['cluster']) ? $map['cluster'] : '0';
        if (!isset($map['catalogue'])) {
            $map['catalogue'] = '';
        }
        if (!isset($map['longfield'])) {
            $map['longfield'] = 'Longitude';
        }
        if (!isset($map['latfield'])) {
            $map['latfield'] = 'Latitude';
        }
        $min_latitude = isset($map['min_latitude']) ? $map['min_latitude'] : '';
        $max_latitude = isset($map['max_latitude']) ? $map['max_latitude'] : '';
        $min_longitude = isset($map['min_longitude']) ? $map['min_longitude'] : '';
        $max_longitude = isset($map['max_longitude']) ? $map['max_longitude'] : '';
        $longitude_key = $map['longfield'];
        $latitude_key = $map['latfield'];
        $catalogue_name = $map['catalogue'];
        $star_entry = isset($map['star_entry']) ? $map['star_entry'] : '';
        $max_results = empty($map['max_results']) ? 300 : intval($map['max_results']);
        $icon = isset($map['icon']) ? $map['icon'] : '';
        if (!isset($map['select'])) {
            $map['select'] = '';
        }
        $filter = isset($map['filter']) ? $map['filter'] : '';
        $guid = isset($map['guid']) ? $map['guid'] : '';

        $data = [];

        // Info about our catalogue
        $catalogue_row = null;
        if ($catalogue_name != '') {
            $catalogue_row = load_catalogue_row($catalogue_name, true);
            if ($catalogue_row === null) {
                return paragraph(do_lang_tempcode('_MISSING_RESOURCE', escape_html($catalogue_name), 'catalogue'), '0zyrq2x4iusrqcm33xmd38v6zl0mdo5q', 'nothing-here');
            }
        }

        $hooks_to_use = explode('|', isset($map['extra_sources']) ? $map['extra_sources'] : '');
        $hooks = find_all_hook_obs('blocks', 'main_google_map', 'Hook_Map_');
        $entries_to_load = [];
        foreach ($hooks as $hook => $ob) {
            if (in_array($hook, $hooks_to_use)) {
                $hook_results = $ob->get_data($map, $max_results, $min_latitude, $max_latitude, $min_longitude, $max_longitude, $latitude_key, $longitude_key, $catalogue_row, $catalogue_name);
                $data = array_merge($data, $hook_results[0]);
                $entries_to_load = $hook_results[1] + $entries_to_load;
            }
        }

        if ($star_entry != '') { // Ensure this entry loads
            $entries_to_load[intval($star_entry)] = true;
        }

        if ($catalogue_name != '') {
            // Preparing for data query
            $where = 'r.ce_validated=1 AND ' . db_string_equal_to('r.c_name', $catalogue_name);
            $join = '';

            // Selecting and Filtering
            $where .= ' AND (1=1';
            if ($map['select'] != '') {
                if ($map['select'] == '/') {
                    $where .= ' AND (0=1)';
                } else {
                    require_code('selectcode');
                    $where .= ' AND (' . selectcode_to_sqlfragment($map['select'], 'r.id', 'catalogue_categories', 'cc_parent_id', 'cc_id', 'r.id') . ')';
                }
            }
            if ($filter != '') {
                // Convert the filters to SQL
                require_code('filtercode');
                list($extra_join, $extra_where) = filtercode_to_sql($GLOBALS['SITE_DB'], parse_filtercode($filter), 'catalogue_entry', $catalogue_name);
                $join .= implode('', $extra_join);
                $where .= $extra_where;
            }
            $where .= ')';
            foreach ($entries_to_load as $entry_id => $allow) {
                if ($allow) {
                    $where .= ' OR r.id=' . strval($entry_id);
                }
            }

            // Privacy
            $privacy_join = '';
            $privacy_where = '';
            if (addon_installed('content_privacy')) {
                require_code('content_privacy');
                list($privacy_join, $privacy_where) = get_privacy_where_clause('catalogue_entry', 'r');
            }

            // Finishing data query
            $query = 'SELECT DISTINCT r.* FROM ' . $GLOBALS['SITE_DB']->get_table_prefix() . 'catalogue_entries r' . $join . $privacy_join . ' WHERE ' . $where . $privacy_where;

            // Get results
            $entries_to_show = [];
            if (($map['select'] == '/') && (empty($entries_to_load))) {
                $ce_entries = [];
            } else {
                $ce_entries = $GLOBALS['SITE_DB']->query($query . ' ORDER BY ce_add_date DESC', $max_results);
            }
            $entries_to_show = array_merge($entries_to_show, $ce_entries);
            if ((empty($entries_to_show)) && (($min_latitude == '') || ($max_latitude == '') || ($min_longitude == '') || ($max_longitude == ''))) { // If there's nothing to show and no given bounds
                //return paragraph(do_lang_tempcode('NO_ENTRIES'), 'g5z3aykmphx1zyhg47zu7ahwv8ajfmj1', 'nothing-here');
            }

            // Find long/lat fields
            global $CAT_FIELDS_CACHE;
            if (isset($CAT_FIELDS_CACHE[$catalogue_name])) {
                $fields = $CAT_FIELDS_CACHE[$catalogue_name];
            } else {
                $fields = $GLOBALS['SITE_DB']->query_select('catalogue_fields', ['*'], ['c_name' => $catalogue_name], 'ORDER BY cf_order,' . $GLOBALS['SITE_DB']->translate_field_ref('cf_name'));
            }
            $CAT_FIELDS_CACHE[$catalogue_name] = $fields;
            $_latitude_key = 'FIELD_1';
            $_longitude_key = 'FIELD_2';
            foreach ($fields as $field) {
                if (get_translated_text($field['cf_name']) == $latitude_key) {
                    $_latitude_key = '_FIELD_' . strval($field['id']);
                }
                if (get_translated_text($field['cf_name']) == $longitude_key) {
                    $_longitude_key = '_FIELD_' . strval($field['id']);
                }
            }

            $has_guid = !empty($map['guid']);
            $star_entry_int = ($star_entry == '') ? null : intval($star_entry);

            // Make marker data JavaScript-friendly
            foreach ($entries_to_show as $i => $entry_row) {
                $breadcrumbs = null;
                $details = get_catalogue_entry_map($entry_row, $catalogue_row, 'CATEGORY', $catalogue_name, null, null, null, false, false, null, $breadcrumbs, true);

                $latitude = $details[$_latitude_key . '_PURE'];
                $longitude = $details[$_longitude_key . '_PURE'];

                if ((is_numeric($latitude)) && (is_numeric($longitude))) {
                    $details['LATITUDE'] = $latitude;
                    $details['LONGITUDE'] = $longitude;

                    $entry_title = $details['FIELD_0'];
                    if (is_object($entry_title)) {
                        $entry_title = $entry_title->evaluate();
                    }
                    $details['ENTRY_TITLE'] = $entry_title;

                    if ($has_guid) {
                        $details['_GUID'] = $map['guid'];
                    }

                    $entry_content = do_template('CATALOGUE_' . $catalogue_name . '_FIELDMAP_ENTRY_WRAP', $details + ['GIVE_CONTEXT' => false], null, false, 'CATALOGUE_DEFAULT_FIELDMAP_ENTRY_WRAP');
                    $details['ENTRY_CONTENT'] = $entry_content;

                    $details['STAR'] = (($entry_row['id'] === $star_entry_int)) ? '1' : '0';
                    $details['CC_ID'] = strval($entry_row['cc_id']);
                    $details['ICON'] = '';

                    $data[] = $details;
                }
            }
        }

        $uniqid = uniqid('', true);
        $div_id = 'div_' . $catalogue_name . '_' . $uniqid;

        return do_template('BLOCK_MAIN_GOOGLE_MAP', [
            '_GUID' => ($guid == '') ? '939dd8fe2397bba0609fba129a8a3bfd' : $guid,
            'BLOCK_ID' => $block_id,
            'TITLE' => $map['title'],
            'ICON' => $icon,
            'MIN_LATITUDE' => $min_latitude,
            'MAX_LATITUDE' => $max_latitude,
            'MIN_LONGITUDE' => $min_longitude,
            'MAX_LONGITUDE' => $max_longitude,
            'DATA' => $data,
            'SHOW_LINKS' => $set_show_links,
            'DIV_ID' => $div_id,
            'CLUSTER' => $cluster,
            'REGION' => $map['region'],
            'WIDTH' => $map_width,
            'HEIGHT' => $map_height,
            'LATITUDE' => $map['latitude'],
            'LONGITUDE' => $map['longitude'],
            'ZOOM' => $set_zoom,
            'CENTER' => $set_center,
        ]);
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    galleries
 */

/**
 * Block class.
 */
class Block_main_hero_slider
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Salman Abbas';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['parameters'] = array(
            'gallery_name', 'effect', 'fullscreen', 'show_indicators', 'show_scroll_down', 'interval', 'check_perms', 'blank_if_empty'
        );

        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled)
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = <<<'PHP'
            array(
                isset($map['blank_if_empty']) ? !empty($map['blank_if_empty']) : false,
                !empty($map['effect']) ? $map['effect'] : 'slide',
                !empty($map['fullscreen']),
                !empty($map['show_indicators']),
                !empty($map['show_scroll_down']),
                isset($map['interval']) ? strval(intval($map['interval'])) : false,
                isset($map['check_perms']) ? !empty($map['check_perms']) : true,
                isset($map['gallery_name']) ? $map['gallery_name'] : '',
            )
PHP;
        $info['ttl'] = (get_value('disable_block_timeout') === '1') ? (60 * 60 * 24 * 365 * 5/*5 year timeout*/) : 60;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters
     * @return Tempcode The result of execution
     */
    public function run($map)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $blank_if_empty = isset($map['blank_if_empty']) ? !empty($map['blank_if_empty']) : false;

        if (!addon_installed('galleries')) {
            return $blank_if_empty ? new Tempcode() : do_template('RED_ALERT', array('_GUID' => '8692692a208449e3862d6ff482dce94b', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('galleries'))));
        }

        if (@cms_empty_safe($map['gallery_name'])) {
            return $blank_if_empty ? new Tempcode() : do_template('RED_ALERT', array('_GUID' => 'a9a5d583cdc145df840b40bdeb6577cd', 'TEXT' => escape_html('Block main_hero_slider: Parameter "gallery_name" is required.')));
        }

        $block_id = get_block_id($map);

        $effect = !empty($map['effect']) ? $map['effect'] : 'slide'; // Valid values: 'fade' or 'slide'

        $fullscreen = !empty($map['fullscreen']);

        $show_indicators = !empty($map['show_indicators']);

        $show_scroll_down = !empty($map['show_scroll_down']);

        $interval = isset($map['interval']) ? strval(intval($map['interval'])) : false;

        $check_perms = isset($map['check_perms']) ? !empty($map['check_perms']) : true;

        // Check if the gallery exists
        $gallery_name = $GLOBALS['SITE_DB']->query_select_value_if_there('galleries', 'name', array('name' => $map['gallery_name']));

        if ($gallery_name === null) {
            // Maybe the user specified a gallery's full name instead of the code name?
            $gallery_name = $GLOBALS['SITE_DB']->query_select_value_if_there('galleries', 'name', array('fullname' => $map['gallery_name']));
        }

        if ($gallery_name === null) {
            return $blank_if_empty ? new Tempcode() : do_template('RED_ALERT', array('_GUID' => '19737ba0c9c84c36b92690b7c896040d', 'TEXT' => escape_html('Block main_hero_slider: Gallery named "' . $map['gallery_name'] . '" not found.')));
        }

        if ($gallery_name === 'root') {
            $cat_select = db_string_equal_to('cat', $gallery_name);
        } else {
            require_code('selectcode');
            $cat_select = selectcode_to_sqlfragment($gallery_name, 'cat', 'galleries', 'parent_id', 'cat', 'name', false, false);
        }

        $extra_join_image = '';
        $extra_join_video = '';
        $extra_where_image = '';
        $extra_where_video = '';

        if (addon_installed('content_privacy')) {
            require_code('content_privacy');
            $as_guest = array_key_exists('as_guest', $map) ? ($map['as_guest'] == '1') : false;
            $viewing_member_id = $as_guest ? $GLOBALS['FORUM_DRIVER']->get_guest_id() : null;
            list($privacy_join_video, $privacy_where_video) = get_privacy_where_clause('video', 'r', $viewing_member_id);
            list($privacy_join_image, $privacy_where_image) = get_privacy_where_clause('image', 'r', $viewing_member_id);
            $extra_join_image .= $privacy_join_image;
            $extra_join_video .= $privacy_join_video;
            $extra_where_image .= $privacy_where_image;
            $extra_where_video .= $privacy_where_video;
        }

        if (get_option('filter_regions') == '1') {
            require_code('locations');
            $extra_where_image .= sql_region_filter('image', 'r.id');
            $extra_where_video .= sql_region_filter('video', 'r.id');
        }

        $extra_join_sql = '';
        $where_sup = '';
        if ((!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) && ($check_perms)) {
            $extra_join_sql .= get_permission_join_clause('gallery', 'cat');
            $where_sup .= get_permission_where_clause(get_member(), get_permission_where_clause_groups(get_member()));
        }

        $image_rows = $GLOBALS['SITE_DB']->query('SELECT r.*,\'image\' AS content_type,cat FROM ' . get_table_prefix() . 'images r ' . $extra_join_image . $extra_join_sql . ' WHERE ' . $cat_select . $extra_where_image . $where_sup . ' AND validated=1 ORDER BY title ASC', 100/*reasonable amount*/, 0, false, true, array('title' => 'SHORT_TRANS', 'the_description' => 'LONG_TRANS'));
        $video_rows = $GLOBALS['SITE_DB']->query('SELECT r.*,\'video\' AS content_type,cat FROM ' . get_table_prefix() . 'videos r ' . $extra_join_video . $extra_join_sql . ' WHERE ' . $cat_select . $extra_where_video . $where_sup . ' AND validated=1 ORDER BY title ASC', 100/*reasonable amount*/, 0, false, true, array('title' => 'SHORT_TRANS', 'the_description' => 'LONG_TRANS'));

        $all_rows = array_merge($image_rows, $video_rows);

        require_code('images');

        if (count($all_rows) == 0) {
            if ($blank_if_empty) {
                return new Tempcode();
            }

            $submit_url = null;
            if ((has_actual_page_access(null, 'cms_galleries', null, null)) && (has_submit_permission('mid', get_member(), get_ip_address(), 'cms_galleries', array('galleries', $gallery_name))) && (can_submit_to_gallery($gallery_name))) {
                $submit_url = build_url(array('page' => 'cms_galleries', 'type' => 'add', 'cat' => $gallery_name, 'redirect' => protect_url_parameter(SELF_REDIRECT_RIP)), get_module_zone('cms_galleries'));
            }
            return do_template('BLOCK_NO_ENTRIES', array(
                '_GUID' => '27fb27331dc5471f977781b1c505b307',
                'TITLE' => do_lang_tempcode('GALLERY'),
                'MESSAGE' => do_lang_tempcode('NO_ENTRIES', 'image'),
                'ADD_NAME' => do_lang_tempcode('ADD_IMAGE'),
                'SUBMIT_URL' => $submit_url,
            ));
        }

        $items = array();
        foreach ($all_rows as $row) {
            $full_url = $row['url'];
            if (url_is_local($full_url)) {
                $full_url = get_custom_base_url() . '/' . $full_url;
            }

            $thumb_url = $row['thumb_url'];
            if (url_is_local($thumb_url)) {
                $thumb_url = get_custom_base_url() . '/' . $thumb_url;
            }

            $just_media_row = db_map_restrict($row, array('id', 'the_description'));

            $description = get_translated_tempcode($row['content_type'] . 's', $just_media_row, 'the_description');

            $edit_url = null;
            if ((has_actual_page_access(get_member(), 'cms_galleries')) && (has_submit_permission('mid', get_member(), get_ip_address(), 'cms_galleries', array('galleries', $gallery_name)))) {
                $url_type = '_edit';
                if ($row['content_type'] === 'video') {
                    $url_type = '_edit_other';
                }
                $edit_url = build_url(array('page' => 'cms_galleries', 'type' => $url_type, 'id' => $row['id']), get_module_zone('cms_galleries'));
            }

            $items[] = array(
                'BACKGROUND_ID' => strval($row['id']),
                'BACKGROUND_TYPE' => $row['content_type'],
                'BACKGROUND_URL' => $full_url,
                'BACKGROUND_THUMB_URL' => $thumb_url,
                'CONTENT_HTML' => $description,
                'EDIT_URL' => $edit_url,
            );
        }

        require_css('galleries');

        return do_template('BLOCK_MAIN_HERO_SLIDER', array(
            '_GUID' => '3afebc2955314f1fbc1b2d4935e998e4',
            'BLOCK_ID' => $block_id,
            'EFFECT' => $effect,
            'FULLSCREEN' => $fullscreen,
            'SHOW_INDICATORS' => $show_indicators,
            'SHOW_SCROLL_DOWN' => $show_scroll_down,
            'INTERVAL' => $interval,
            'ITEMS' => $items,
            'HAS_MULTIPLE_ITEMS' => (count($items) > 1) ? '1' : '0',
        ));
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    image_slider
 */

/**
 * Block class.
 */
class Block_main_image_slider
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array('param', 'time', 'zone', 'order', 'as_guest', 'transitions', 'width', 'height');
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = '$map';
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_PERMISSIVE_GROUPS;
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 60;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        require_css('skitter');
        require_javascript('jquery');
        require_javascript('skitter');

        require_code('galleries');
        require_lang('galleries');

        $cat = empty($map['param']) ? 'root' : $map['param'];
        $mill = array_key_exists('time', $map) ? intval($map['time']) : 8000; // milliseconds between animations
        $zone = array_key_exists('zone', $map) ? $map['zone'] : get_module_zone('galleries');
        $order = array_key_exists('order', $map) ? $map['order'] : '';

        $width = empty($map['width']) ? '750px' : $map['width'];
        if (is_numeric($width)) {
            $width .= 'px';
        }
        $height = empty($map['height']) ? '300px' : $map['height'];
        if (is_numeric($height)) {
            $height .= 'px';
        }

        $_transitions = array_key_exists('transitions', $map) ? $map['transitions'] : 'cube|cubeRandom|block|cubeStop|cubeHide|cubeSize|horizontal|showBars|showBarsRandom|tube|fade|fadeFour|paralell|blind|blindHeight|blindWidth|directionTop|directionBottom|directionRight|directionLeft|cubeStopRandom|cubeSpread|cubeJelly|glassCube|glassBlock|circles|circlesInside|circlesRotate|cubeShow|upBars|downBars|hideBars|swapBars|swapBarsBack|swapBlocks|cut|random|randomSmart';
        $transitions = ($_transitions == '') ? array() : explode('|', $_transitions);

        if ($cat == 'root') {
            $cat_select = db_string_equal_to('cat', 'root');
        } else {
            require_code('selectcode');
            $cat_select = selectcode_to_sqlfragment($cat, 'cat', 'galleries', 'parent_id', 'cat', 'name', false, false);
        }

        $extra_join_image = '';
        $extra_join_video = '';
        $extra_where_image = '';
        $extra_where_video = '';

        if (addon_installed('content_privacy')) {
            require_code('content_privacy');
            $as_guest = array_key_exists('as_guest', $map) ? ($map['as_guest'] == '1') : false;
            $viewing_member_id = $as_guest ? $GLOBALS['FORUM_DRIVER']->get_guest_id() : mixed();
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

        $image_rows = $GLOBALS['SITE_DB']->query('SELECT r.id,thumb_url,url,title,description,\'image\' AS content_type FROM ' . get_table_prefix() . 'images r ' . $extra_join_image . ' WHERE ' . $cat_select . $extra_where_image . ' AND validated=1 ORDER BY add_date ASC', 100/*reasonable amount*/, null, false, true, array('title' => 'SHORT_TRANS', 'description' => 'LONG_TRANS__COMCODE'));
        $video_rows = $GLOBALS['SITE_DB']->query('SELECT r.id,thumb_url,thumb_url AS url,title,description,\'video\' AS content_type FROM ' . get_table_prefix() . 'videos r ' . $extra_join_video . ' WHERE ' . $cat_select . $extra_where_video . ' AND validated=1 ORDER BY add_date ASC', 100/*reasonable amount*/, null, false, true, array('title' => 'SHORT_TRANS', 'description' => 'LONG_TRANS__COMCODE'));
        $all_rows = array();
        if ($order != '') {
            foreach (explode(',', $order) as $o) {
                $num = substr($o, 1);

                if (is_numeric($num)) {
                    switch (substr($o, 0, 1)) {
                        case 'i':
                            foreach ($image_rows as $i => $row) {
                                if ($row['id'] == intval($num)) {
                                    $all_rows[] = $row;
                                    unset($image_rows[$i]);
                                }
                            }
                            break;
                        case 'v':
                            foreach ($video_rows as $i => $row) {
                                if ($row['id'] == intval($num)) {
                                    $all_rows[] = $row;
                                    unset($video_rows[$i]);
                                }
                            }
                            break;
                    }
                }
            }
        }

        $all_rows = array_merge($all_rows, $image_rows, $video_rows);

        require_code('images');

        $images = array();
        foreach ($all_rows as $i => $row) {
            $url = $row['thumb_url'];
            if (url_is_local($url)) {
                $url = get_custom_base_url() . '/' . $url;
            }

            $full_url = $row['url'];
            if (url_is_local($full_url)) {
                $full_url = get_custom_base_url() . '/' . $full_url;
            }

            $view_url = build_url(array('page' => 'galleries', 'type' => $row['content_type'], 'id' => $row['id']), $zone);

            $just_media_row = db_map_restrict($row, array('id', 'description'));

            $title = get_translated_text($row['title']);
            $description = get_translated_tempcode($row['content_type'] . 's', $just_media_row, 'description');

            $images[] = array(
                'URL' => $url,
                'FULL_URL' => $full_url,
                'VIEW_URL' => $view_url,
                'TITLE' => $title,
                'DESCRIPTION' => $description,
                'TRANSITION_TYPE' => isset($transitions[$i]) ? $transitions[$i] : '',
            );
        }

        if (count($images) == 0) {
            $submit_url = mixed();
            if ((has_actual_page_access(null, 'cms_galleries', null, null)) && (has_submit_permission('mid', get_member(), get_ip_address(), 'cms_galleries', array('galleries', $cat))) && (can_submit_to_gallery($cat))) {
                $submit_url = build_url(array('page' => 'cms_galleries', 'type' => 'add', 'cat' => $cat, 'redirect' => SELF_REDIRECT), get_module_zone('cms_galleries'));
            }
            return do_template('BLOCK_NO_ENTRIES', array(
                '_GUID' => '8b92cd992508e55bfe4139b5c09475c2',
                'HIGH' => false,
                'TITLE' => do_lang_tempcode('GALLERY'),
                'MESSAGE' => do_lang_tempcode('NO_ENTRIES', 'image'),
                'ADD_NAME' => do_lang_tempcode('ADD_IMAGE'),
                'SUBMIT_URL' => $submit_url,
            ));
        }

        $nice_cat = str_replace('*', '', $cat);
        if (preg_match('#^[' . URL_CONTENT_REGEXP . ']+$#', $nice_cat) == 0) {
            $nice_cat = 'root';
        }
        $gallery_url = build_url(array('page' => 'galleries', 'type' => 'browse', 'id' => $nice_cat), $zone);

        return do_template('BLOCK_MAIN_IMAGE_SLIDER', array(
            '_GUID' => '264a178c1ea7fa719ac53af07129a38c',
            'GALLERY_URL' => $gallery_url,
            'IMAGES' => $images,
            'MILL' => strval($mill),
            'WIDTH' => $width,
            'HEIGHT' => $height,
        ));
    }
}

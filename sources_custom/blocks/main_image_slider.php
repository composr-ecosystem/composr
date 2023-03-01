<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

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
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = ['param', 'time', 'zone', 'order', 'as_guest', 'transitions', 'width', 'height', 'check'];
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
        [
            empty($map['param']) ? 'root' : $map['param'],
            array_key_exists('time', $map) ? intval($map['time']) : 8000,
            array_key_exists('zone', $map) ? $map['zone'] : get_module_zone('galleries'),
            array_key_exists('order', $map) ? $map['order'] : '',
            empty($map['width']) ? '750px' : $map['width'],
            empty($map['height']) ? '300px' : $map['height'],
            array_key_exists('as_guest', $map) ? ($map['as_guest'] == '1') : false,
            array_key_exists('transitions', $map) ? $map['transitions'] : 'cube|cubeRandom|block|cubeStop|cubeHide|cubeSize|horizontal|showBars|showBarsRandom|tube|fade|fadeFour|paralell|blind|blindHeight|blindWidth|directionTop|directionBottom|directionRight|directionLeft|cubeStopRandom|cubeSpread|cubeJelly|glassCube|glassBlock|circles|circlesInside|circlesRotate|cubeShow|upBars|downBars|hideBars|swapBars|swapBarsBack|swapBlocks|cut|random|randomSmart',
            array_key_exists('check', $map) ? ($map['check'] == '1') : true,
        ]
PHP;
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_PERMISSIVE_GROUPS;
        $info['ttl'] = 60;
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
        if (!addon_installed__messaged('image_slider', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('galleries')) {
            return do_template('RED_ALERT', ['_GUID' => 'cer2r1bqzio6b98ksizgsptqnvidthq6', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('galleries'))]);
        }

        require_css('skitter');
        require_javascript('jquery');
        require_javascript('skitter');
        require_javascript('image_slider');

        require_code('galleries');
        require_lang('galleries');
        require_code('images');
        require_code('content');

        $block_id = get_block_id($map);

        $check_perms = array_key_exists('check', $map) ? ($map['check'] == '1') : true;

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

        $as_guest = array_key_exists('as_guest', $map) ? ($map['as_guest'] == '1') : false;

        $_transitions = array_key_exists('transitions', $map) ? $map['transitions'] : 'cube|cubeRandom|block|cubeStop|cubeHide|cubeSize|horizontal|showBars|showBarsRandom|tube|fade|fadeFour|paralell|blind|blindHeight|blindWidth|directionTop|directionBottom|directionRight|directionLeft|cubeStopRandom|cubeSpread|cubeJelly|glassCube|glassBlock|circles|circlesInside|circlesRotate|cubeShow|upBars|downBars|hideBars|swapBars|swapBarsBack|swapBlocks|cut|random|randomSmart';
        $transitions = ($_transitions == '') ? [] : explode('|', $_transitions);

        if ($cat == 'root') {
            $cat_select = db_string_equal_to('cat', 'root');
        } else {
            require_code('selectcode');
            $cat_select = selectcode_to_sqlfragment($cat, 'cat', 'galleries', 'parent_id', 'cat', 'name', false, false);
        }

        $extra_where = ' AND ' . $cat_select;

        list($rows, $max_rows) = content_rows_for_multi_type(['image', 'video'], null, $extra_where, '', 'recent ASC', 0, 100, '', '', '', $check_perms);

        $all_rows = [];
        if ($order != '') {
            foreach (explode(',', $order) as $o) {
                $content_type = (substr($o, 0, 1) == 'v') ? 'video' : 'image';
                $_id = substr($o, 1);

                if (is_numeric($_id)) {
                    $id = intval($_id);
                    foreach ($rows as $i => $row) {
                        if ((($content_type == 'video') && ($row['content_type'] == 'video') && ($row['id'] == $id)) || (($content_type == 'image') && ($row['content_type'] == 'image') && ($row['id'] == $id))) {
                            $all_rows[] = $row;
                            unset($rows[$i]);
                        }
                    }
                }
            }
        }
        $all_rows = array_merge($all_rows, $rows);

        $images = [];
        foreach ($all_rows as $i => $row) {
            if ($row['content_type'] == 'video') {
                $image_url = $row['thumb_url'];
            } else {
                $image_url = $row['url'];
            }
            if (url_is_local($image_url)) {
                $image_url = get_custom_base_url() . '/' . $image_url;
            }

            $view_url = build_url(['page' => 'galleries', 'type' => $row['content_type'], 'id' => $row['id']], $zone);

            $just_media_row = db_map_restrict($row, ['id', 'the_description']);

            $title = get_translated_text($row['title']);
            $description = get_translated_tempcode($row['content_type'] . 's', $just_media_row, 'the_description');

            $images[] = [
                'IMAGE_URL' => $image_url,
                'VIEW_URL' => $view_url,
                'TITLE' => $title,
                'DESCRIPTION' => $description,
                'TRANSITION_TYPE' => isset($transitions[$i]) ? $transitions[$i] : '',
            ];
        }

        if (empty($images)) {
            $submit_url = null;
            if ((has_actual_page_access(null, 'cms_galleries', null, null)) && (has_submit_permission('mid', get_member(), get_ip_address(), 'cms_galleries', ['galleries', $cat])) && (can_submit_to_gallery($cat))) {
                $submit_url = build_url(['page' => 'cms_galleries', 'type' => 'add', 'cat' => $cat, 'redirect' => protect_url_parameter(SELF_REDIRECT_RIP)], get_module_zone('cms_galleries'));
            }
            return do_template('BLOCK_NO_ENTRIES', [
                '_GUID' => '8b92cd992508e55bfe4139b5c09475c2',
                'BLOCK_ID' => $block_id,
                'HIGH' => false,
                'TITLE' => do_lang_tempcode('GALLERY'),
                'MESSAGE' => do_lang_tempcode('NO_ENTRIES', 'image'),
                'ADD_NAME' => do_lang_tempcode('ADD_IMAGE'),
                'SUBMIT_URL' => $submit_url,
            ]);
        }

        $nice_cat = str_replace('*', '', $cat);
        if (preg_match('#^[' . URL_CONTENT_REGEXP . ']+$#', $nice_cat) == 0) {
            $nice_cat = 'root';
        }
        $gallery_url = build_url(['page' => 'galleries', 'type' => 'browse', 'id' => $nice_cat], $zone);

        return do_template('BLOCK_MAIN_IMAGE_SLIDER', [
            '_GUID' => '264a178c1ea7fa719ac53af07129a38c',
            'BLOCK_ID' => $block_id,
            'GALLERY_URL' => $gallery_url,
            'IMAGES' => $images,
            'MILL' => strval($mill),
            'WIDTH' => $width,
            'HEIGHT' => $height,
        ]);
    }
}

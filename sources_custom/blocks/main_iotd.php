<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Block class.
 */
class Block_main_iotd
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
        $info['parameters'] = array('param', 'zone');
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
        $info['cache_on'] = 'array(array_key_exists(\'param\',$map)?$map[\'param\']:\'current\',array_key_exists(\'zone\',$map)?$map[\'zone\']:get_module_zone(\'iotds\'))';
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_PERMISSIVE_GROUPS;
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 60 * 24;
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

        require_lang('iotds');
        require_css('iotds');
        $mode = array_key_exists('param', $map) ? $map['param'] : 'current';
        $zone = array_key_exists('zone', $map) ? $map['zone'] : get_module_zone('iotds');

        if ((has_actual_page_access(null, 'cms_iotds', null, null)) && (has_submit_permission('mid', get_member(), get_ip_address(), 'cms_iotds'))) {
            $submit_url = build_url(array('page' => 'cms_iotds', 'type' => 'add', 'redirect' => SELF_REDIRECT), get_module_zone('cms_iotds'));
        } else {
            $submit_url = new Tempcode();
        }

        if ($mode == 'current') {
            $iotd = $GLOBALS['SITE_DB']->query_select('iotd', array('*'), array('is_current' => 1), 'ORDER BY id DESC', 1);
        } elseif (is_numeric($mode)) {
            $iotd = $GLOBALS['SITE_DB']->query_select('iotd', array('*'), array('id' => intval($mode)), '', 1);
            if (!array_key_exists(0, $iotd)) {
                return do_template('BLOCK_NO_ENTRIES', array('_GUID' => '55cff098a0ff91416e6c0e52228ca02d', 'HIGH' => true, 'TITLE' => do_lang_tempcode('IOTD'), 'MESSAGE' => do_lang_tempcode('NO_ENTRIES', 'iotd'), 'ADD_NAME' => do_lang_tempcode('ADD_IOTD'), 'SUBMIT_URL' => $submit_url));
            }
        } else {
            $cnt = $GLOBALS['SITE_DB']->query_select_value('iotd', 'COUNT(*)', array('used' => 1));
            if ($cnt == 0) {
                return do_template('BLOCK_NO_ENTRIES', array('_GUID' => '3fe3dbbf8966b80cf3037f6dd914867d', 'HIGH' => true, 'TITLE' => do_lang_tempcode('IOTD'), 'MESSAGE' => do_lang_tempcode('NO_ENTRIES', 'iotd'), 'ADD_NAME' => do_lang_tempcode('ADD_IOTD'), 'SUBMIT_URL' => $submit_url));
            }
            $at = mt_rand(0, $cnt - 1);
            $iotd = $GLOBALS['SITE_DB']->query_select('iotd', array('*'), array('used' => 1), '', 1, $at);
        }
        if (!array_key_exists(0, $iotd)) {
            return do_template('BLOCK_NO_ENTRIES', array('_GUID' => '62baa388e068d4334f7a6c6093ead56a', 'HIGH' => true, 'TITLE' => do_lang_tempcode('IOTD'), 'MESSAGE' => do_lang_tempcode('NO_ENTRIES', 'iotd'), 'ADD_NAME' => do_lang_tempcode('ADD_IOTD'), 'SUBMIT_URL' => $submit_url));
        }
        $myrow = $iotd[0];

        $image_url = $myrow['url'];
        if (url_is_local($image_url)) {
            $image_url = get_custom_base_url() . '/' . $image_url;
        }

        $view_url = build_url(array('page' => 'iotds', 'type' => 'view', 'id' => $myrow['id']), $zone);

        $i_title = get_translated_tempcode('iotd', $myrow, 'i_title');
        $caption = get_translated_tempcode('iotd', $myrow, 'caption');

        require_code('images');
        $thumb_url = ensure_thumbnail($myrow['url'], $myrow['thumb_url'], 'iotds', 'iotd', $myrow['id']);
        $image = do_image_thumb($thumb_url, do_lang('IOTD'));

        $archive_url = build_url(array('page' => 'iotds', 'type' => 'browse'), $zone);

        $map2 = array('_GUID' => 'd710da3675a1775867168ae37db02ad4', 'CURRENT' => ($mode == 'current'), 'VIEW_URL' => $view_url, 'IMAGE_URL' => $image_url, 'THUMB_URL' => $thumb_url, 'SUBMITTER' => strval($myrow['submitter']), 'ID' => strval($myrow['id']), 'I_TITLE' => $i_title, 'CAPTION' => $caption, 'IMAGE' => $image, 'ARCHIVE_URL' => $archive_url, 'SUBMIT_URL' => $submit_url);
        if ((get_option('is_on_comments') == '1') && (get_forum_type() != 'none') && ($myrow['allow_comments'] >= 1)) {
            $map2['COMMENT_COUNT'] = '1';
        }
        return do_template('BLOCK_MAIN_IOTD', $map2);
    }
}

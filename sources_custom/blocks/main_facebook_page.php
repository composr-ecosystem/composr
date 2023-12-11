<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

/**
 * Block class.
 */
class Block_main_facebook_page
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
        $info['parameters'] = array('page_name', 'width', 'height', 'show_cover_photo', 'show_fans', 'show_posts');
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
        $info['cache_on'] = 'array(empty($map[\'page_name\']) ? get_site_name() : $map[\'page_name\'], empty($map[\'width\']) ? \'340\' : $map[\'width\'], empty($map[\'height\']) ? \'500\' : $map[\'height\'], array_key_exists(\'show_cover_photo\', $map) ? $map[\'show_cover_photo\'] : \'0\', array_key_exists(\'show_fans\', $map) ? $map[\'show_fans\'] : \'0\', array_key_exists(\'show_posts\', $map) ? $map[\'show_posts\'] : \'0\')';
        $info['ttl'] = 60 * 5;
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

        require_lang('facebook');
        require_code('facebook_connect');

        $appid = get_option('facebook_appid');
        if ($appid == '') {
            return new Tempcode();
        }

        $page_name = empty($map['page_name']) ? get_site_name() : $map['page_name'];
        $width = empty($map['width']) ? '340' : $map['width'];
        $height = empty($map['height']) ? '500' : $map['height'];
        $show_cover_photo = array_key_exists('show_cover_photo', $map) ? $map['show_cover_photo'] : '0';
        $show_fans = array_key_exists('show_fans', $map) ? $map['show_fans'] : '0';
        $show_posts = array_key_exists('show_posts', $map) ? $map['show_posts'] : '0';

        return do_template('BLOCK_MAIN_FACEBOOK_PAGE', array(
            '_GUID' => '5f4dc97379346496d8b8152a56a9ec84',
            'PAGE_NAME' => $page_name,
            'WIDTH' => $width,
            'HEIGHT' => $height,
            'SHOW_COVER_PHOTO' => ($show_cover_photo == '1'),
            'SHOW_FANS' => ($show_fans == '1'),
            'SHOW_POSTS' => ($show_posts == '1'),
        ));
    }
}

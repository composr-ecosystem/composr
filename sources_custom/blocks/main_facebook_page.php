<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'facebook_support';
        $info['parameters'] = ['page_name', 'width', 'height', 'show_cover_photo', 'show_fans', 'show_posts'];
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
            empty($map['page_name']) ? get_site_name() : $map['page_name'],
            empty($map['width']) ? '340' : $map['width'],
            empty($map['height']) ? '500' : $map['height'],
            array_key_exists('show_cover_photo', $map) ? $map['show_cover_photo'] : '0',
            array_key_exists('show_fans', $map) ? $map['show_fans'] : '0',
            array_key_exists('show_posts', $map) ? $map['show_posts'] : '0',
        ]
PHP;
        $info['ttl'] = 60 * 5;
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
        if (!addon_installed__messaged('facebook_support', $error_msg)) {
            return $error_msg;
        }

        if (!function_exists('curl_init')) {
            return do_template('RED_ALERT', ['_GUID' => 'gyh8q02fa42oidxizj593su3fs6qc9zm', 'TEXT' => do_lang_tempcode('NO_CURL_ON_SERVER')]);
        }
        if (!function_exists('session_status')) {
            return do_template('RED_ALERT', ['_GUID' => '3kii599c4uz2g43k69ogp53wob87650o', 'TEXT' => 'PHP session extension missing']);
        }

        require_lang('facebook');

        $block_id = get_block_id($map);

        $appid = get_option('facebook_appid');
        if ($appid == '') {
            return do_template('RED_ALERT', ['_GUID' => 'ty2jeraub73e8bu0bo57m01s4hxby4h1', 'TEXT' => do_lang_tempcode('API_NOT_CONFIGURED', 'Facebook')]);
        }

        $page_name = empty($map['page_name']) ? get_site_name() : $map['page_name'];
        $width = empty($map['width']) ? '340' : $map['width'];
        $height = empty($map['height']) ? '500' : $map['height'];
        $show_cover_photo = array_key_exists('show_cover_photo', $map) ? $map['show_cover_photo'] : '0';
        $show_fans = array_key_exists('show_fans', $map) ? $map['show_fans'] : '0';
        $show_posts = array_key_exists('show_posts', $map) ? $map['show_posts'] : '0';

        return do_template('BLOCK_MAIN_FACEBOOK_PAGE', [
            '_GUID' => '5f4dc97379346496d8b8152a56a9ec84',
            'BLOCK_ID' => $block_id,
            'PAGE_NAME' => $page_name,
            'WIDTH' => $width,
            'HEIGHT' => $height,
            'SHOW_COVER_PHOTO' => ($show_cover_photo == '1'),
            'SHOW_FANS' => ($show_fans == '1'),
            'SHOW_POSTS' => ($show_posts == '1'),
        ]);
    }
}

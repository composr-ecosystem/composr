<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class Block_main_facebook_like
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Naveen';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'facebook_support';
        $info['parameters'] = [];
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
            return do_template('RED_ALERT', ['_GUID' => '5ldkzkb0bo25j30gmw653k5t7keqb1nv', 'TEXT' => do_lang_tempcode('NO_CURL_ON_SERVER')]);
        }
        if (!function_exists('session_status')) {
            return do_template('RED_ALERT', ['_GUID' => '4kii599c4uz2g43k69ogp53wob87650o', 'TEXT' => 'PHP session extension missing']);
        }

        $block_id = get_block_id($map);

        $appid = get_option('facebook_appid');
        if ($appid == '') {
            return do_template('RED_ALERT', ['_GUID' => 'k3k28zaoxf1llhy49dvuihh0ptemcmd8', 'TEXT' => do_lang_tempcode('API_NOT_CONFIGURED', 'Facebook')]);
        }

        return do_template('BLOCK_MAIN_FACEBOOK_LIKE', [
            '_GUID' => '09de0fd4bc8b3f57d4f9238b798bfcbf',
            'BLOCK_ID' => $block_id,
        ]);
    }
}

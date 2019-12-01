<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

/**
 * Block class.
 */
class Block_main_facebook_comments
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info()
    {
        $info = [];
        $info['author'] = 'Naveen';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = [];
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

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('facebook_support', $error_msg)) {
            return $error_msg;
        }

        if (!function_exists('curl_init')) {
            return do_template('RED_ALERT', ['_GUID' => 'b5fbhxvxzef9uon2oc9q6ihh6xq0ccig', 'TEXT' => do_lang_tempcode('NO_CURL_ON_SERVER')]);
        }
        if (!function_exists('session_status')) {
            return do_template('RED_ALERT', ['_GUID' => '65sskjtfq1qowxewn7s087hg7zrd8aoa', 'TEXT' => 'PHP session extension missing']);
        }

        require_code('facebook_connect');

        $block_id = get_block_id($map);

        $appid = get_option('facebook_appid');
        if ($appid == '') {
            return do_template('RED_ALERT', ['_GUID' => 'mcpdkezerhqqwxa3qas36jainejuetb8', 'TEXT' => do_lang_tempcode('API_NOT_CONFIGURED', 'Facebook')]);
        }
        return do_template('BLOCK_MAIN_FACEBOOK_COMMENTS', [
            '_GUID' => '99de0fd4bc8b3f57d4f9238b798bfcbf',
            'BLOCK_ID' => $block_id,
            'URL' => 'http://developers.facebook.com/docs/reference/plugins/like-box',
        ]);
    }
}

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
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
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Naveen';
        $info['organisation'] = 'Composr';
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
            return do_template('RED_ALERT', ['_GUID' => 'e03a52a9ff035dfa9916be2134673904', 'TEXT' => do_lang_tempcode('NO_CURL_ON_SERVER')]);
        }
        if (!function_exists('session_status')) {
            return do_template('RED_ALERT', ['_GUID' => 'b32914479b935519b84b14883a5a8e30', 'TEXT' => 'PHP session extension missing']);
        }

        $block_id = get_block_id($map);

        $appid = get_option('facebook_appid');
        if ($appid == '') {
            return do_template('RED_ALERT', ['_GUID' => '2f69086d0dac56b49a24d1e56a3a0abe', 'TEXT' => do_lang_tempcode('API_NOT_CONFIGURED', 'Facebook')]);
        }
        return do_template('BLOCK_MAIN_FACEBOOK_COMMENTS', [
            '_GUID' => '99de0fd4bc8b3f57d4f9238b798bfcbf',
            'BLOCK_ID' => $block_id,
        ]);
    }
}

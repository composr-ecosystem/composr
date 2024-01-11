<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * Block class.
 */
class Block_main_activity_feed_state
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Warburton';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'activity_feed';
        $info['parameters'] = ['param'];
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
        if (!addon_installed__messaged('activity_feed', $error_msg)) {
            return $error_msg;
        }

        require_lang('activity_feed');
        require_css('activity_feed');
        require_javascript('activity_feed');
        require_javascript('jquery');

        $block_id = get_block_id($map);

        $title = array_key_exists('param', $map) ? $map['param'] : do_lang('STATUS_UPDATE');

        return do_template('BLOCK_MAIN_ACTIVITY_FEED_STATE', [
            '_GUID' => 'ad41b611db430c58189aa28e96a2712e',
            'BLOCK_ID' => $block_id,
            'TITLE' => $title,
        ]);
    }
}

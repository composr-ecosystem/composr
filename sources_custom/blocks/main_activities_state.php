<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * Block class.
 */
class Block_main_activities_state
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Warburton';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['parameters'] = array('param');
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

        require_lang('activities');
        require_css('activities');
        require_javascript('activities');
        require_javascript('activities_state');
        require_javascript('jquery');

        $title = array_key_exists('param', $map) ? $map['param'] : do_lang('STATUS_UPDATE');

        return do_template('BLOCK_MAIN_ACTIVITIES_STATE', array('_GUID' => 'ad41b611db430c58189aa28e96a2712e', 'TITLE' => $title));
    }
}

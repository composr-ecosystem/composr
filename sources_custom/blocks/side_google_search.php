<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    google_search
 */

/**
 * Block class.
 */
class Block_side_google_search
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Kamen / Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array('param', 'page_name');
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
        $info['cache_on'] = 'array(array_key_exists(\'page_name\',$map)?$map[\'page_name\']:\'google_search\',array_key_exists(\'param\',$map)?$map[\'param\']:\'\')';
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 60 * 5;
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

        require_lang('google_search');

        $page_name = !empty($map['page_name']) ? $map['page_name'] : '_google_search';

        if (empty($map['param'])) {
            return do_lang_tempcode('NO_PARAMETER_SENT', 'param');
        }
        $id = $map['param'];

        return do_template('BLOCK_SIDE_GOOGLE_SEARCH', array('_GUID' => 'e42a949234538f8c2f9a8e96bc43056d', 'TITLE' => do_lang_tempcode('BLOCK_GOOGLE_TITLE'), 'PAGE_NAME' => $page_name, 'ID' => $id));
    }
}

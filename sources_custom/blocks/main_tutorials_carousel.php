<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

/**
 * Block class.
 */
class Block_main_tutorials_carousel
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
        $info['version'] = 1;
        $info['locked'] = false;
        $info['parameters'] = array();
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
        $info['cache_on'] = '$map';
        $info['ttl'] = 60;
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

        if (!module_installed('tutorials')) {
            require_code('zones2');
            reinstall_module('docs', 'tutorials');
        }

        $criteria = empty($map['param']) ? '' : $map['param'];
        if ($criteria == '') {
            $criteria = 'recent';
        }

        require_code('tutorials');
        $tutorials = list_tutorials_by($criteria);
        $_tutorials = templatify_tutorial_list($tutorials, true);

        return do_template('BLOCK_MAIN_TUTORIALS_CAROUSEL', array('_GUID' => '07b265b808abd02cc8abae7e3fe6992d', 'TUTORIALS' => $_tutorials));
    }
}

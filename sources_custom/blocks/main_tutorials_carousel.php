<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

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
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'composr_tutorials';
        $info['parameters'] = ['param'];
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
            empty($map['param']) ? '' : $map['param'],
        ]
PHP;
        $info['ttl'] = 60;
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
        if (!addon_installed__messaged('composr_tutorials', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('composr_homesite')) {
            return do_template('RED_ALERT', ['_GUID' => '66yohru6brfru3mhqkebyak0jujo1l89', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite'))]);
        }
        if (!addon_installed('composr_homesite_support_credits')) {
            return do_template('RED_ALERT', ['_GUID' => 'vknpkcimw04ix5guh0s9q7ckk0zadjz7', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite_support_credits'))]);
        }
        if (!addon_installed('composr_release_build')) {
            return do_template('RED_ALERT', ['_GUID' => 'q6ab12xk41unkypf88f8ktrp6pbavevi', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_release_build'))]);
        }

        $block_id = get_block_id($map);

        $criteria = empty($map['param']) ? '' : $map['param'];
        if ($criteria == '') {
            $criteria = 'recent';
        }

        require_code('tutorials');
        $tutorials = list_tutorials_by($criteria);
        $_tutorials = templatify_tutorial_list($tutorials, true);

        return do_template('BLOCK_MAIN_TUTORIALS_CAROUSEL', [
            '_GUID' => '07b265b808abd02cc8abae7e3fe6992d',
            'BLOCK_ID' => $block_id,
            'TUTORIALS' => $_tutorials,
        ]);
    }
}

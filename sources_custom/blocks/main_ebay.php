<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    ebay_store
 */

/**
 * Block class.
 */
class Block_main_ebay
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info()
    {
        $info = [];
        $info['author'] = 'Babu';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = ['seller', 'query', 'max_entries', 'image_size', 'domain'];
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled)
     */
    public function caching_environment()
    {
        $info = [];
        $info['cache_on'] = <<<'PHP'
        array(
            empty($map['title']) ? do_lang('BLOCK_EBAY_TITLE') : $map['title'],
            empty($map['max_entries']) ? 4 : intval($map['max_entries']),
            empty($map['image_size']) ? 80 : intval($map['image_size']),
            empty($map['domain']) ? 0 : intval(preg_replace('#=.*$#', '', $map['domain'])),
            isset($map['seller']) ? $map['seller'] : '',
            empty($map['query']) ? '' : preg_replace('#\s#', '+', $map['query']),
        )
PHP;
        $info['ttl'] = (get_value('disable_block_timeout') === '1') ? (60 * 60 * 24 * 365 * 5/*5 year timeout*/) : 15;
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
        if (!addon_installed__messaged('ebay_store', $error_msg)) {
            return $error_msg;
        }

        require_lang('ebay');

        $block_id = get_block_id($map);

        if (!array_key_exists('seller', $map)) {
            return do_lang_tempcode('NO_PARAMETER_SENT', 'seller');
        }

        $title = empty($map['title']) ? do_lang_tempcode('BLOCK_EBAY_TITLE') : $map['title'];
        $max_entries = empty($map['max_entries']) ? 4 : intval($map['max_entries']);
        $image_size = empty($map['image_size']) ? 80 : intval($map['image_size']);
        $domain = empty($map['domain']) ? 0 : intval(preg_replace('#=.*$#', '', $map['domain']));
        $seller = $map['seller'];
        $query = empty($map['query']) ? '' : preg_replace('#\s#', '+', $map['query']); // e.g. Gadgets

        return do_template('BLOCK_MAIN_EBAY', [
            '_GUID' => 'ffda4477bf08164f80dd45ef2985dfe9',
            'BLOCK_ID' => $block_id,
            'TITLE' => $title,
            'MAX_ENTRIES' => strval($max_entries),
            'IMAGE_SIZE' => strval($image_size),
            'DOMAIN' => strval($domain),
            'SELLER' => $seller,
            'QUERY' => $query,
        ]);
    }
}

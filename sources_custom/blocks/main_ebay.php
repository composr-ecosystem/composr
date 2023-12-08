<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Babu';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array('seller', 'query', 'max_entries', 'image_size', 'domain');
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
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 15;
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

        require_lang('ebay');

        if (!array_key_exists('seller', $map)) {
            return do_lang_tempcode('NO_PARAMETER_SENT', 'seller');
        }

        $title = (isset($map['title']) && $map['title'] != '') ? $map['title'] : do_lang_tempcode('BLOCK_EBAY_TITLE');
        $max_entries = (isset($map['max_entries']) && $map['max_entries'] != '') ? intval($map['max_entries']) : 4;
        $image_size = (isset($map['image_size']) && $map['image_size'] != '') ? intval($map['image_size']) : 80;
        $domain = (isset($map['domain']) && $map['domain'] != '') ? intval(preg_replace('#=.*$#', '', $map['domain'])) : 0;
        $seller = $map['seller'];
        $query = (isset($map['query']) && strlen($map['query']) > 0) ? preg_replace('#\s#', '+', $map['query']) : ''; // e.g. Gadgets

        return do_template('BLOCK_MAIN_EBAY', array(
            '_GUID' => 'ffda4477bf08164f80dd45ef2985dfe9',
            'TITLE' => $title,
            'MAX_ENTRIES' => strval($max_entries),
            'IMAGE_SIZE' => strval($image_size),
            'DOMAIN' => strval($domain),
            'SELLER' => $seller,
            'QUERY' => $query,
        ));
    }
}

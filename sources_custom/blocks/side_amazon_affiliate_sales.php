<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    amazon_affiliate_sales
 */

/**
 * Block class.
 */
class Block_side_amazon_affiliate_sales
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Kamen Blaginov';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array('associates_id', 'product_line', 'subject_keywords', 'items_number', 'region');
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled)
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = <<<'PHP'
        array(
            isset($map['associates_id']) ? $map['associates_id'] : '',
            isset($map['product_line']) ? $map['product_line'] : '',
            isset($map['subject_keywords']) ? $map['subject_keywords'] : '',
            (isset($map['items_number']) && intval($map['items_number']) >= 2) ? intval($map['items_number']) : 2,
            isset($map['region']) ? $map['region'] : 'US',
        )
PHP;
        $info['ttl'] = (get_value('disable_block_timeout') === '1') ? (60 * 60 * 24 * 365 * 5/*5 year timeout*/) : (60 * 5);
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
        if (!addon_installed__messaged('amazon_affiliate_sales', $error_msg)) {
            return $error_msg;
        }

        require_lang('amazon');

        $block_id = get_block_id($map);

        if (!array_key_exists('associates_id', $map)) {
            return do_lang_tempcode('NO_PARAMETER_SENT', 'associates_id');
        }

        $associates_id = $map['associates_id'];
        $product_line = isset($map['product_line']) ? $map['product_line'] : '';
        $subject_keywords = isset($map['subject_keywords']) ? $map['subject_keywords'] : '';
        $items_number = (isset($map['items_number']) && intval($map['items_number']) >= 2) ? intval($map['items_number']) : 2;
        $region = isset($map['region']) ? $map['region'] : 'US';

        return do_template('BLOCK_SIDE_AMAZON_AFFILIATE_SALES', array(
            '_GUID' => '5edc2fd386f1688fca8e0e6eefa5f455',
            'BLOCK_ID' => $block_id,
            'ASSOCIATES_ID' => $associates_id,
            'PRODUCT_LINE' => $product_line,
            'SUBJECT_KEYWORDS' => $subject_keywords,
            'ITEMS_NUMBER' => strval($items_number),
            'REGION' => $region,
        ));
    }
}

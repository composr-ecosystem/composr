<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

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
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Kamen Blaginov';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = ['associates_id', 'product_line', 'subject_keywords', 'height', 'region'];
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
        if (!addon_installed__messaged('amazon_affiliate_sales', $error_msg)) {
            return $error_msg;
        }

        require_lang('amazon');

        $block_id = get_block_id($map);

        if (empty($map['associates_id'])) {
            return do_template('RED_ALERT', ['TEXT' => do_lang_tempcode('NO_PARAMETER_SENT', 'associates_id')]);
        }

        if (empty($map['subject_keywords'])) {
            return do_template('RED_ALERT', ['TEXT' => do_lang_tempcode('NO_PARAMETER_SENT', 'subject_keywords')]);
        }

        $associates_id = $map['associates_id'];
        $product_line = isset($map['product_line']) ? $map['product_line'] : '';
        $subject_keywords = isset($map['subject_keywords']) ? $map['subject_keywords'] : '';
        $region = empty($map['region']) ? 'US' : $map['region'];
        $height = empty($map['height']) ? '150' : $map['height'];

        load_csp(['csp_allow_inline_js' => '1']);

        return do_template('BLOCK_SIDE_AMAZON_AFFILIATE_SALES', [
            '_GUID' => '5edc2fd386f1688fca8e0e6eefa5f455',
            'BLOCK_ID' => $block_id,
            'ASSOCIATES_ID' => $associates_id,
            'PRODUCT_LINE' => $product_line,
            'SUBJECT_KEYWORDS' => $subject_keywords,
            'REGION' => $region,
            'HEIGHT' => $height,
        ]);
    }
}

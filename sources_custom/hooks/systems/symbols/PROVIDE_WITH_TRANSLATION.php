<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    translation
 */

/**
 * Hook class.
 */
class Hook_symbol_PROVIDE_WITH_TRANSLATION
{
    /**
     * Run function for symbol hooks. Searches for tasks to perform.
     *
     * @param  array $param Symbol parameters
     * @return string Result
     */
    public function run($param)
    {
        require_code('translation');

        $text = isset($param[0]) ? $param[0] : '';

        if (!defined('TRANS_TEXT_CONTEXT_html_block')) {
            return $text; // Somehow startup hook didn't run
        }

        if ((isset($param[1])) && (is_numeric($param[1]))) {
            $context = intval($param[1]);
        } else {
            $context = TRANS_TEXT_CONTEXT_html_block;
        }

        $from = empty($param[2]) ? null : $param[2];

        $text_translated = translate_text($text, $context, $from, user_lang());

        if ($text_translated === null) {
            return $text;
        }

        switch ($context) {
            case TRANS_TEXT_CONTEXT_plain:
                $text .= ' (' . $text_translated . ')';
                break;

            case TRANS_TEXT_CONTEXT_html_block:
                $text .= '<br /><div class="box box__translation"><div class="box_inner">' . $text_translated . '</div></div>'; // Comes with a "Powered by" message
                break;

            case TRANS_TEXT_CONTEXT_html_inline:
                $text .= ' (' . $text_translated . ')';
                break;
        }

        return $text;
    }
}

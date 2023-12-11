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
class Hook_startup_translation
{
    public function run()
    {
        define('TRANS_TEXT_CONTEXT_plain', 0);
        define('TRANS_TEXT_CONTEXT_html_block', 1);
        define('TRANS_TEXT_CONTEXT_html_inline', 2);
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    password_censor
 */

/**
 * Hook class.
 */
class Hook_comcode_encrypt
{
    /**
     * Run function for Comcode hooks. They find the custom-comcode-row-like attributes of the tag.
     *
     * @return ?array Fake Custom Comcode row (null: disabled)
     */
    public function get_tag() : ?array
    {
        if (!addon_installed('password_censor')) {
            return null;
        }

        // You may wonder how a Comcode tag can change itself.
        //  The _password_censor function interactively is changing submitted Comcode.

        return [
            'tag_title' => 'Encrypt',
            'tag_description' => 'Store the contents of the tag as encrypted in the database.',
            'tag_example' => '[encrypt]Text to encrypt[/encrypt]',
            'tag_tag' => 'encrypt',
            'tag_replace' => cms_file_get_contents_safe(get_file_base() . '/themes/default/templates_custom/COMCODE_ENCRYPT.tpl', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM),
            'tag_parameters' => '',
            'tag_block_tag' => 1,
            'tag_textual_tag' => 1,
            'tag_dangerous_tag' => 0,
        ];
    }
}

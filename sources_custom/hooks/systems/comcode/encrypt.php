<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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
    public function get_tag()
    {
        if (!addon_installed('password_censor')) {
            return null;
        }

        return array(
            'tag_title' => 'Encrypt',
            'tag_description' => 'Store the contents of the tag as encrypted in the database.',
            'tag_example' => '[encrypt]Text to encrypt[/encrypt]',
            'tag_tag' => 'encrypt',
            'tag_replace' => file_get_contents(get_file_base() . '/themes/default/templates_custom/COMCODE_ENCRYPT.tpl'),
            'tag_parameters' => '',
            'tag_block_tag' => 1,
            'tag_textual_tag' => 1,
            'tag_dangerous_tag' => 0,
        );
    }
}

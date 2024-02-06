<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class Hook_comcode_self_destruct
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

        return [
            'tag_title' => 'Self-destruct',
            'tag_description' => 'The contents will not appear in notifications, and, for private topic and support ticket posts, will self-destruct after 30 days.',
            'tag_example' => '[self_destruct]Text to self-destruct[/self_destruct]',
            'tag_tag' => 'self_destruct',
            'tag_replace' => cms_file_get_contents_safe(get_file_base() . '/themes/default/templates_custom/COMCODE_SELF_DESTRUCT.tpl', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM),
            'tag_parameters' => '',
            'tag_block_tag' => 1,
            'tag_textual_tag' => 1,
            'tag_dangerous_tag' => 0,
        ];
    }
}

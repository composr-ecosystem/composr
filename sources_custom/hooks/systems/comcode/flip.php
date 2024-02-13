<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    comcode_flip_tag
 */

/**
 * Hook class.
 */
class Hook_comcode_flip
{
    /**
     * Run function for Comcode hooks. They find the custom-comcode-row-like attributes of the tag.
     *
     * @return ?array Fake Custom Comcode row (null: disabled)
     */
    public function get_tag() : ?array
    {
        if (!addon_installed('comcode_flip_tag')) {
            return null;
        }

        return [
            'tag_title' => 'Flip',
            'tag_description' => 'Provide two-sided square flip spots.',
            'tag_example' => '[flip="Back"]Front[/flip]',
            'tag_tag' => 'flip',
            'tag_replace' => cms_file_get_contents_safe(get_file_base() . '/themes/default/templates_custom/COMCODE_FLIP.tpl', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM),
            'tag_parameters' => 'param,final_color=DDDDDD,speed=1000,width=140,height=140',
            'tag_block_tag' => 0,
            'tag_textual_tag' => 1,
            'tag_dangerous_tag' => 0,
        ];
    }
}

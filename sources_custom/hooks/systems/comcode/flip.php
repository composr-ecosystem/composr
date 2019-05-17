<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    public function get_tag()
    {
        if (!addon_installed('comcode_flip_tag')) {
            return null;
        }

        return array(
            'tag_title' => 'Flip',
            'tag_description' => 'Provide two-sided square flip spots.',
            'tag_example' => '[flip="Back"]Front[/flip]',
            'tag_tag' => 'flip',
            'tag_replace' => file_get_contents(get_file_base() . '/themes/default/templates_custom/COMCODE_FLIP.tpl'),
            'tag_parameters' => 'param,final_color=DDDDDD,speed=1000',
            'tag_block_tag' => 0,
            'tag_textual_tag' => 1,
            'tag_dangerous_tag' => 0,
        );
    }
}

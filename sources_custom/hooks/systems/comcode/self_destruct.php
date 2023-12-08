<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
     * Run function for comcode hooks. They find the custom-comcode-row-like attributes of the tag.
     *
     * @return array Fake custom Comcode row
     */
    public function get_tag()
    {
        return array(
            'tag_title' => 'Self-destruct',
            'tag_description' => 'The contents will not appear in notifications, and, for private topic and support ticket posts, will self-destruct after 30 days.',
            'tag_example' => '[self_destruct]Text to self-destruct[/self_destruct]',
            'tag_tag' => 'self_destruct',
            'tag_replace' => file_get_contents(get_file_base() . '/themes/default/templates_custom/COMCODE_SELF_DESTRUCT.tpl'),
            'tag_parameters' => '',
            'tag_block_tag' => 1,
            'tag_textual_tag' => 1,
            'tag_dangerous_tag' => 0,
        );
    }
}

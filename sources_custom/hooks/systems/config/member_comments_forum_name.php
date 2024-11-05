<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    member_comments
 */

/**
 * Hook class.
 */
class Hook_config_member_comments_forum_name
{
    /**
     * Gets the details relating to the config option.
     *
     * @return array The details
     */
    public function get_details() : array
    {
        return [
            'human_name' => 'MEMBER_COMMENTS_FORUM_NAME',
            'type' => 'forum',
            'category' => 'USER_INTERACTION',
            'group' => 'MEMBER_COMMENTS',
            'explanation' => 'CONFIG_OPTION_member_comments_forum_name',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 2,
            'required' => true,
            'public' => false,
            'addon' => 'member_comments',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default() : ?string
    {
        if (!addon_installed('member_comments')) {
            return null;
        }

        return do_lang('member_comments:MEMBER_COMMENTS_FORUM_NAME', '', '', '', get_site_default_lang());
    }
}

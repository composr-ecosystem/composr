<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    git_status
 */

/**
 * Hook class.
 */
class Hook_config_git_live_branch
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        if (!addon_installed('git_status')) {
            return null;
        }

        return [
            'human_name' => 'GIT_LIVE_BRANCH',
            'type' => 'line',
            'category' => 'FEATURE',
            'group' => 'GIT_STATUS',
            'explanation' => 'CONFIG_OPTION_git_live_branch',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 1,
            'required' => true,
            'public' => false,
            'addon' => 'git_status',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        return 'master';
    }
}

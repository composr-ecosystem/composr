<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    gallery_syndication
 */

/**
 * Hook class.
 */
class Hook_config_video_sync_transcoding
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return [
            'human_name' => 'VIDEO_SYNC_TRANSCODING',
            'type' => 'special',
            'category' => 'GALLERY',
            'group' => 'GALLERY_SYNDICATION',
            'explanation' => 'CONFIG_OPTION_video_sync_transcoding',
            'shared_hosting_restricted' => '0',
            'list_options' => '',
            'order_in_category_group' => 3,
            'required' => false,
            'public' => false,

            'addon' => 'gallery_syndication',
        ];
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        if (!addon_installed('gallery_syndication')) {
            return null;
        }

        require_lang('gallery_syndication');
        return do_lang('SYND_LOCAL', null, null, null, fallback_lang());
    }

    /**
     * Field inputter (because the_type=special).
     *
     * @param  ID_TEXT $name The config option name
     * @param  array $myrow The config row
     * @param  Tempcode $human_name The field title
     * @param  Tempcode $explanation The field description
     * @return Tempcode The inputter
     */
    public function field_inputter($name, $myrow, $human_name, $explanation)
    {
        $list = '';
        $list .= static_evaluate_tempcode(form_input_list_entry(do_lang('OTHER', null, null, null, fallback_lang())));

        $hooks = find_all_hook_obs('modules', 'video_syndication', 'Hook_video_syndication_');
        foreach ($hooks as $hook => $ob) {
            $label = $ob->get_service_title();

            $list .= static_evaluate_tempcode(form_input_list_entry($hook, $hook == get_option($name), $label));
        }

        return form_input_list($human_name, $explanation, 'video_sync_transcoding', make_string_tempcode($list));
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    gallery_syndication
 */

/**
 * Hook class.
 */
class Hook_page_groupings_gallery_syndication
{
    /**
     * Run function for do_next_menu hooks. They find links to put on standard navigation menus of the system.
     *
     * @param  ?MEMBER $member_id Member ID to run as (null: current member)
     * @param  boolean $extensive_docs Whether to use extensive documentation tooltips, rather than short summaries
     * @return array List of tuple of links (page grouping, icon, do-next-style linking data), label, help (optional) and/or nulls
     */
    public function run($member_id = null, $extensive_docs = false)
    {
        $menu_items = array();

        $hooks = find_all_hooks('modules', 'video_syndication');

        foreach (array_keys($hooks) as $hook) {
            require_code('hooks/modules/video_syndication/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_video_syndication_' . filter_naughty_harsh($hook));

            $service_title = $ob->get_service_title();

            $menu_items[] = array(
                'setup',
                'menu/rich_content/galleries',
                array($hook . '_oauth', array(), get_page_zone($hook . '_oauth', false, 'adminzone')),
                do_lang_tempcode('oauth:OAUTH_TITLE_UNDER_SETUP', escape_html($service_title)),
                comcode_to_tempcode(do_lang('oauth:DOC_OAUTH_SETUP', $service_title))
            );
        }

        return $menu_items;
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Hook class.
 */
class Hook_startup_tapatalk
{
    public function run()
    {
        if (!addon_installed('cns_tapatalk')) {
            return;
        }

        if (!addon_installed('cns_forum')) {
            return;
        }

        if (get_forum_type() != 'cns') {
            return;
        }

        if ((get_option('tapatalk_promote_from_website') === '1') && (running_script('index'))) {
            $before = ini_get('ocproducts.type_strictness');
            cms_ini_set('ocproducts.type_strictness', '0');

            $board_url = get_base_url(); // NOT the URL to the main forum, used by JS smartbanner to find the mobiquo directory
            $app_forum_name = get_site_name();
            $api_key = get_option('tapatalk_api_key');

            $app_ads_enable = 0;
            $app_banner_enable = 1;
            $is_mobile_skin = is_mobile() ? 1 : 0;
            $page = get_page_name();

            process_url_monikers(true, true, $page);

            $page_type = 'other';
            $start = null;
            $max = null;
            $extra = '';
            switch (get_page_name()) {
                case 'topicview':
                    $page_type = 'topic';
                    $start = get_param_integer('start', 0);
                    $default_max = intval(get_option('forum_posts_per_page'));
                    $max = get_param_integer('max', $default_max);
                    $id = get_param_integer('id', null);
                    if ($id !== null) {
                        $extra = '&tid=' . strval($id);
                    }
                    break;
                case 'forumview':
                    switch (get_param_string('type', 'browse')) {
                        case 'pt':
                            require_code('templates_pagination');
                            require_code('cns_forumview');
                            list($max, , , , , $start) = get_keyset_pagination_settings('forum_max', intval(get_option('private_topics_per_page')), 'forum_start', 'kfs', 'forum_sort', 'last_post', 'get_forum_sort_order');

                            $page_type = 'message';
                            $id = get_param_integer('id', null);
                            if ($id !== null) {
                                $extra = '&mid=' . strval($id);
                            }
                            break;
                        case 'browse':
                            $id = get_param_integer('id', db_get_first_id());

                            require_code('templates_pagination');
                            require_code('cns_forumview');
                            list($max, , , , , $start) = get_keyset_pagination_settings('forum_max', intval(get_option('forum_topics_per_page')), 'forum_start', 'kfs' . strval($id), 'forum_sort', 'last_post', 'get_forum_sort_order');

                            if ($id == db_get_first_id()) {
                                $page_type = 'home';
                            } else {
                                $page_type = 'forum';
                                $extra = '&fid=' . strval($id);
                            }
                            break;
                    }
                    break;
                case 'search':
                    $page_type = 'search';
                    break;
                case 'members':
                    $page_type = 'profile';
                    $_id = get_param_string('id', strval(get_member()));
                    if (!is_numeric($_id)) {
                        $_id = strval($GLOBALS['FORUM_DRIVER']->get_member_from_username($_id));
                    }
                    $extra = '&uid=' . $_id;
                    break;
                case 'users_online':
                    $page_type = 'online';
                    break;
                case 'topics':
                    $page_type = 'post';
                    break;
            }

            $app_location_url = get_base_url() . '?';
            if (!is_guest()) {
                $app_location_url .= 'user_id=' . strval(get_member()) . '&';
            }
            $app_location_url .= 'location=' . $page_type;
            if ($start !== null) {
                $app_location_url .= '&page=' . strval(intval(floor($start / $max)) + 1) . '&perpage=' . strval($max);
            }
            $app_location_url .= $extra;
            $app_location_url = preg_replace('/^(https|http)/isU', 'tapatalk', $app_location_url);

            $tapatalk_dir_url = get_base_url();

            global $app_head_include;

            require(get_file_base() . '/mobiquo/smartbanner/head.inc.php');

            attach_to_screen_header($app_head_include);

            cms_ini_set('ocproducts.type_strictness', $before);
        }
    }
}

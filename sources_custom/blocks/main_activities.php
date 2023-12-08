<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    activity_feed
 */

/**
 * Block class.
 */
class Block_main_activities
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Warburton';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['parameters'] = array('max', 'start', 'param', 'member', 'mode', 'grow', 'refresh_time');
        return $info;
    }

    /**
     * Uninstall the block.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('activities');

        delete_privilege('syndicate_site_activity');
    }

    /**
     * Install the block.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        if (is_null($upgrade_from)) {
            $GLOBALS['SITE_DB']->create_table('activities', array(
                'id' => '*AUTO',
                'a_member_id' => '*MEMBER',
                'a_also_involving' => '?MEMBER',
                'a_language_string_code' => '*ID_TEXT',
                'a_label_1' => 'SHORT_TEXT',
                'a_label_2' => 'SHORT_TEXT',
                'a_label_3' => 'SHORT_TEXT',
                'a_page_link_1' => 'SHORT_TEXT',
                'a_page_link_2' => 'SHORT_TEXT',
                'a_page_link_3' => 'SHORT_TEXT',
                'a_time' => 'TIME',
                'a_addon' => 'ID_TEXT',
                'a_is_public' => 'BINARY'
            ));

            $GLOBALS['SITE_DB']->create_index('activities', 'a_member_id', array('a_member_id'));
            $GLOBALS['SITE_DB']->create_index('activities', 'a_also_involving', array('a_also_involving'));
            $GLOBALS['SITE_DB']->create_index('activities', 'a_time', array('a_time'));
            $GLOBALS['SITE_DB']->create_index('activities', 'a_filtered_ordered', array('a_member_id', 'a_time'));

            require_code('activities_submission');
            log_newest_activity(0, 1000, true);

            add_privilege('SUBMISSION', 'syndicate_site_activity', false);
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 2)) {
            $GLOBALS['SITE_DB']->alter_table_field('activities', 'a_pagelink_1', 'SHORT_TEXT', 'a_page_link_1');
            $GLOBALS['SITE_DB']->alter_table_field('activities', 'a_pagelink_2', 'SHORT_TEXT', 'a_page_link_2');
            $GLOBALS['SITE_DB']->alter_table_field('activities', 'a_pagelink_3', 'SHORT_TEXT', 'a_page_link_3');

            $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'activities SET a_language_string_code=REPLACE(a_language_string_code,\'ocf:\',\'cns:\') WHERE a_language_string_code LIKE \'ocf:%\'');
        }
    }

    // CACHE MESSES WITH POST REMOVAL AND PAGINATION LINKS
    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    /*function caching_environment()
    {
        $info = array();
        $info['cache_on'] = 'array(array_key_exists(\'grow\',$map)?($map['grow']==\'1\'):true,array_key_exists(\'max\',$map)?intval($map[\'max\']):10,array_key_exists(\'refresh_time\',$map)?intval($map[\'refresh_time\']):30,array_key_exists(\'param\',$map)?$map[\'param\']:do_lang(\'activities:ACTIVITY\'),array_key_exists(\'mode\',$map)?$map[\'mode\']:\'all\',get_member())';
        $info['ttl'] = 3;
        return $info;
    }*/

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        require_lang('activities');
        require_css('activities');
        require_javascript('activities');
        require_javascript('jquery');
        require_javascript('base64');

        $refresh_time = array_key_exists('refresh_time', $map) ? intval($map['refresh_time']) : 30;
        $grow = array_key_exists('grow', $map) ? ($map['grow'] == '1') : true;

        // See if we're displaying for a specific member
        if ((array_key_exists('member', $map)) && ($map['member'] != '')) {
            $member_ids = array_map('intval', explode(',', $map['member']));
        } else {
            // No specific member. Use ourselves.
            $member_ids = array(get_member());
        }

        require_lang('activities');
        require_code('activities');
        require_code('addons');

        $mode = array_key_exists('mode', $map) ? $map['mode'] : 'all';

        $viewing_member = get_member();

        list($proceed_selection, $whereville) = get_activity_querying_sql($viewing_member, $mode, $member_ids);

        $can_remove_others = has_zone_access($viewing_member, 'adminzone');

        $content = array();

        $block_id = get_block_id($map);

        $max = get_param_integer($block_id . '_max', array_key_exists('max', $map) ? intval($map['max']) : 10);
        $start = get_param_integer($block_id . '_start', array_key_exists('start', $map) ? intval($map['start']) : 0);

        if ($proceed_selection) {
            $max_rows = $GLOBALS['SITE_DB']->query_value_if_there('SELECT COUNT(*) FROM ' . get_table_prefix() . 'activities WHERE ' . $whereville, false, true);

            require_code('templates_pagination');
            $pagination = pagination(do_lang('ACTIVITY'), $start, $block_id . '_start', $max, $block_id . '_max', $max_rows, false, 5, null, 'tab__activities');

            $activities = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'activities WHERE ' . $whereville . ' ORDER BY a_time DESC', $max, $start, false, true);

            foreach ($activities as $row) {
                list($message, $member_avatar, $datetime, $member_url, $lang_string, $is_public) = render_activity($row);

                $username = $GLOBALS['FORUM_DRIVER']->get_username($row['a_member_id']);
                if (is_null($username)) {
                    $username = do_lang('UNKNOWN');
                }

                $content[] = array(
                    'IS_PUBLIC' => $is_public,
                    'LANG_STRING' => $lang_string,
                    'ADDON' => $row['a_addon'],
                    'ADDON_ICON' => (($row['a_addon'] == '') || (!addon_installed($row['a_addon']))) ? '' : find_addon_icon($row['a_addon']),
                    'MESSAGE' => $message,
                    'AVATAR' => $member_avatar,
                    'MEMBER_ID' => strval($row['a_member_id']),
                    'USERNAME' => $username,
                    'MEMBER_URL' => $member_url,
                    'DATETIME' => strval($datetime),
                    'LIID' => strval($row['id']),
                    'ALLOW_REMOVE' => (($row['a_member_id'] == $viewing_member) || $can_remove_others),
                );
            }
        } else {
            $pagination = new Tempcode();
        }

        return do_template('BLOCK_MAIN_ACTIVITIES', array(
            '_GUID' => 'b4de219116e1b8107553ee588717e2c9',
            'BLOCK_PARAMS' => block_params_arr_to_str(array('block_id' => $block_id) + $map),
            'MODE' => $mode,
            'MEMBER_IDS' => implode(',', $member_ids),
            'CONTENT' => $content,
            'GROW' => $grow,
            'PAGINATION' => $pagination,
            'REFRESH_TIME' => strval($refresh_time),

            'START' => strval($start),
            'MAX' => strval($max),
            'START_PARAM' => $block_id . '_start',
            'MAX_PARAM' => $block_id . '_max',
            'EXTRA_GET_PARAMS' => (get_param_integer($block_id . '_max', null) === null) ? null : ('&' . $block_id . '_max=' . urlencode(strval($max))),
        ));
    }
}

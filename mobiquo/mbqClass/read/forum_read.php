<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    cns_tapatalk
 */

/**
 * Composr API helper class.
 */
class CMSForumRead
{
    /**
     * Load the details/status of the forum structure at a particular level of children.
     *
     * @param  AUTO_LINK $forum_id Forum ID we are collating children for
     * @param  boolean $full_tree Recurse the full tree (rather than just one level)
     * @param  boolean $return_description Include forum descriptions
     * @param  ?boolean $order_sub_alpha Whether to order alphabetically (null: lookup)
     * @param  ?array $all_groupings Forum groupings map (all of them) (null: lookup)
     * @param  integer $recursion_depth Recursion depth
     * @return object Forum details/status; recursive Mobiquo structure
     */
    public function forum_recursive_load($forum_id, $full_tree, $return_description, $order_sub_alpha = null, $all_groupings = null, $recursion_depth = 0)
    {
        cms_verify_parameters_phpdoc();

        if (!has_category_access(get_member(), 'forums', strval($forum_id))) {
            access_denied('I_ERROR');
        }

        if (is_null($order_sub_alpha)) {
            $order_sub_alpha = ($GLOBALS['FORUM_DB']->query_select_value('f_forums', 'f_order_sub_alpha', array('id' => $forum_id)) == 1);
        }

        if (is_null($all_groupings)) {
            $all_groupings = list_to_map('id', $GLOBALS['FORUM_DB']->query_select('f_forum_groupings', array('*')));
        }

        if (($forum_id == -2) && (!$full_tree)) {
            $_children = array(); // Announcements virtual forum
        } else {
            $_children = $this->get_forum($forum_id, $order_sub_alpha);
        }

        $_forums_with_groupings = array();
        foreach ($_children as $child) {
            $grouping_id = $child['f_forum_grouping_id'];
            if (!isset($_forums_with_groupings[$grouping_id])) {
                $_forums_with_groupings[$grouping_id] = array();
            }
            $_forums_with_groupings[$grouping_id][] = $child;
        }

        $forums_with_groupings = array();
        foreach ($_forums_with_groupings as $grouping_id => $forums_in_grouping) {
            if (!isset($all_groupings[$grouping_id])) {
                $all_groupings[$grouping_id] = $all_groupings[db_get_first_id()];
            }

            if (count($forums_in_grouping) == 1) {
                $pseudo_parent = ($forum_id == db_get_first_id()) ? '-1' : strval($forum_id);
            } else {
                $pseudo_parent = 'grouping_' . strval($forum_id) . '_' . strval($grouping_id);
            }

            $forums = array();

            // Do we need a virtual forum for the root forum? As we don't show an actual root forum in Tapatalk
            if (($forum_id == db_get_first_id()) && (count($forums_with_groupings) == 0) && ($GLOBALS['FORUM_DB']->query_select_value('f_topics', 'COUNT(*)', array('t_forum_id' => $forum_id)) > 1)) {
                $unread_count = get_num_unread_topics(db_get_first_id());
                $new_post = ($unread_count > 0);

                require_code('notifications');
                $is_subscribed = notifications_enabled('cns_topic', 'forum:' . strval(db_get_first_id()));

                $arr = array(
                    'forum_id' => mobiquo_val('-2', 'string'),
                    'forum_name' => mobiquo_val(do_lang('TAPATALK_ROOT_FORUM_NAME'), 'base64'),
                    'parent_id' => mobiquo_val($pseudo_parent, 'string'),
                    'logo_url' => mobiquo_val('', 'string'),
                    'new_post' => mobiquo_val($new_post, 'boolean'),
                    'unread_count' => mobiquo_val($unread_count, 'int'),
                    'is_protected' => mobiquo_val(false, 'boolean'),
                    'can_subscribe' => mobiquo_val(!is_guest(get_member()), 'boolean'),
                    'is_subscribed' => mobiquo_val($is_subscribed, 'boolean'),
                    'url' => mobiquo_val('', 'string'),
                    'sub_only' => mobiquo_val(false, 'boolean'), // Forum
                    'child' => mobiquo_val(array(), 'array'),
                );
                if ($return_description) {
                    $arr['description'] = mobiquo_val('', 'base64');
                }

                $forums[] = mobiquo_val($arr, 'struct');
            }

            foreach ($forums_in_grouping as $forum) {
                $unread_count = get_num_unread_topics($forum['forum_id']);
                $new_post = ($unread_count > 0);

                $url = $forum['f_redirection'];

                require_code('notifications');
                $is_subscribed = notifications_enabled('cns_topic', 'forum:' . strval($forum['id']));

                if ($full_tree || $recursion_depth == 0) {
                    $children = $this->forum_recursive_load($forum['id'], $full_tree, $return_description, $forum['f_order_sub_alpha'] == 1, $all_groupings, $recursion_depth + 1);
                } else {
                    $children = array();
                }

                $arr = array(
                    'forum_id' => mobiquo_val(strval($forum['forum_id']), 'string'),
                    'forum_name' => mobiquo_val($forum['f_name'], 'base64'),
                    'parent_id' => mobiquo_val($pseudo_parent, 'string'),
                    'logo_url' => mobiquo_val('', 'string'),
                    'new_post' => mobiquo_val($new_post, 'boolean'),
                    'unread_count' => mobiquo_val($unread_count, 'int'),
                    'is_protected' => mobiquo_val(false, 'boolean'),
                    'can_subscribe' => mobiquo_val(!is_guest(get_member()), 'boolean'),
                    'is_subscribed' => mobiquo_val($is_subscribed, 'boolean'),
                    'url' => mobiquo_val($url, 'string'),
                    'sub_only' => mobiquo_val(false, 'boolean'), // Forum
                    'child' => $children,
                );
                if ($return_description) {
                    $arr['description'] = mobiquo_val(get_translated_text($forum['f_description'], $GLOBALS['FORUM_DB']), 'base64');
                }

                $forums[] = mobiquo_val($arr, 'struct');
            }

            if (count($forums_in_grouping) == 1) {
                $forums_with_groupings = array_merge($forums_with_groupings, $forums); // Actually could just be assignment, but array_merge allows for future code complexity growth
            } else {
                $arr = array(
                    'forum_id' => mobiquo_val($pseudo_parent, 'string'),
                    'forum_name' => mobiquo_val($all_groupings[$grouping_id]['c_title'], 'base64'),
                    'parent_id' => mobiquo_val(($forum_id == db_get_first_id()) ? '-1' : strval($forum_id), 'string'),
                    'logo_url' => mobiquo_val('', 'string'),
                    'new_post' => mobiquo_val(false, 'boolean'),
                    'unread_count' => mobiquo_val(0, 'int'),
                    'is_protected' => mobiquo_val(false, 'boolean'),
                    'can_subscribe' => mobiquo_val(false, 'boolean'),
                    'is_subscribed' => mobiquo_val(false, 'boolean'),
                    'url' => mobiquo_val('', 'string'),
                    'sub_only' => mobiquo_val(true, 'boolean'), // Forum grouping
                    'child' => mobiquo_val($forums, 'array'),
                );
                if ($return_description) {
                    $arr['description'] = mobiquo_val($all_groupings[$grouping_id]['c_description'], 'base64');
                }

                $forums_with_groupings[] = mobiquo_val($arr, 'struct');
            }
        }

        return mobiquo_val($forums_with_groupings, 'array');
    }

    /**
     * Find child forums of a particular forum.
     *
     * @param  AUTO_LINK $forum_id Forum ID
     * @param  boolean $order_sub_alpha Whether to order alphabetically
     * @return array List of forum rows
     */
    private function get_forum($forum_id, $order_sub_alpha)
    {
        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $_forum_conditions = array();

        $_forum_conditions[] = 'f.f_parent_forum=' . strval($forum_id);

        $_forum_conditions[] = 'f.id IN (' . get_allowed_forum_sql() . ')';

        $_forum_conditions[] = '(f_redirection=\'\' OR f_redirection LIKE \'%://%\')';

        $forum_conditions = implode(' AND ', $_forum_conditions);

        $query = 'SELECT *,id as forum_id FROM ' . $table_prefix . 'f_forums f WHERE ' . $forum_conditions;

        if ($order_sub_alpha) {
            $query .= ' ORDER BY f_name';
        } else {
            $query .= ' ORDER BY f_position,f_name';
        }
        return (get_allowed_forum_sql() == '') ? array() : $GLOBALS['FORUM_DB']->query($query);
    }

    /**
     * Get details/status of participated forums (forums the current user has participated in).
     *
     * @return array A pair: Number of forums participated in, Details of forums
     */
    public function get_participated_forums()
    {
        cms_verify_parameters_phpdoc();

        if (is_guest()) {
            return array(0, array());
        }

        $member_id = get_member();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();
        $table = 'f_forums f JOIN ' . $table_prefix . 'f_posts p ON f.id=p.p_cache_forum_id';

        $select = array('f.id', 'f_name');

        $where = array('p_poster' => $member_id);

        $extra = ' AND f.f_parent_forum IN (' . get_allowed_forum_sql() . ') GROUP BY f.id ORDER BY MAX(p_time) DESC';

        $participated = (get_allowed_forum_sql() == '') ? array() : $GLOBALS['FORUM_DB']->query_select($table, $select, $where, $extra);

        $forums = array();
        foreach ($participated as $forum) {
            $forums[] = array(
                'forum_id' => $forum['id'],
                'forum_name' => $forum['f_name'],
                'new_post' => get_num_unread_topics($forum['id']),
            );
        }

        return array(count($participated), $forums);
    }

    /**
     * Get basic details/status of a set of forums.
     *
     * @param  array $forum_ids List of forum IDs
     * @return array Details of forums
     */
    public function get_forum_status($forum_ids)
    {
        cms_verify_parameters_phpdoc();

        $table_prefix = $GLOBALS['FORUM_DB']->get_table_prefix();

        $_forum_ids = implode(',', array_map('strval', $forum_ids));
        $sql = 'SELECT id,f_name FROM ' . $table_prefix . 'f_forums WHERE id IN (' . $_forum_ids . ') AND id IN (' . get_allowed_forum_sql() . ')';
        $forum_details = (get_allowed_forum_sql() == '') ? array() : $GLOBALS['FORUM_DB']->query($sql);

        $forums = array();
        foreach ($forum_details as $forum) {
            $forums[] = array(
                'forum_id' => $forum['id'],
                'forum_name' => $forum['f_name'],
                'logo_url' => '',
                'is_protected' => false,
                'new_post' => is_forum_unread($forum['id']),
            );
        }

        return $forums;
    }

    /**
     * Get details of emoticons.
     *
     * @return array List of emoticon categories and emoticon details within
     */
    public function get_smilies()
    {
        cms_verify_parameters_phpdoc();

        $where = array();
        if (!has_privilege(get_member(), 'use_special_emoticons')) {
            $where['e_is_special'] = 0;
        }

        $rows = $GLOBALS['FORUM_DB']->query_select('f_emoticons', array('*'), $where);
        $smiley_categories = array();
        foreach ($rows as $row) {
            $url = find_theme_image($row['e_theme_img_code']);

            $category = 'default';
            switch ($row['e_relevance_level']) {
                case 0:
                    $category = do_lang('EMOTICON_GROUP_0');
                    break;
                case 1:
                    $category = do_lang('EMOTICON_GROUP_1');
                    break;
                case 2:
                    $category = do_lang('EMOTICON_GROUP_2');
                    break;
                case 3:
                    $category = do_lang('EMOTICON_GROUP_3');
                    break;
                case 4: // Unused
                    continue 2;
            }

            if (!isset($smiley_categories[$category])) {
                $smiley_categories[$category] = array();
            }

            $smiley_categories[$category][] = array(
                'code' => $row['e_code'],
                'url' => $url,
            );
        }
        return $smiley_categories;
    }
}

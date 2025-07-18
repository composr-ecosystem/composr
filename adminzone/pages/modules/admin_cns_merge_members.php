<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_cns
 */

/**
 * Module page class.
 */
class Module_admin_cns_merge_members
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions.
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user).
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name).
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled).
     */
    public function get_entry_points($check_perms = true, $member_id = null, $support_crosslinks = true, $be_deferential = false)
    {
        if (get_forum_type() != 'cns') {
            return null;
        }

        if ($be_deferential || $support_crosslinks) {
            return null;
        }

        return array(
            'browse' => array('MERGE_MEMBERS', 'menu/adminzone/tools/users/merge_members'),
        );
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        $type = get_param_string('type', 'browse');

        require_lang('cns');
        require_css('cns_admin');

        if ($type == 'browse') {
            inform_non_canonical_parameter('from');
            inform_non_canonical_parameter('to');

            breadcrumb_set_parents(array(array('_SEARCH:admin_cns_members:browse', do_lang_tempcode('MEMBERS'))));
        }

        if ($type == 'actual') {
            breadcrumb_set_parents(array(array('_SEARCH:admin_cns_members:browse', do_lang_tempcode('MEMBERS')), array('_SELF:_SELF:browse', do_lang_tempcode('MERGE_MEMBERS'))));
            breadcrumb_set_self(do_lang_tempcode('DONE'));
        }

        set_helper_panel_tutorial('tut_adv_members');

        $this->title = get_screen_title('MERGE_MEMBERS');

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        } else {
            cns_require_all_forum_stuff();
        }

        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            return $this->gui();
        }
        if ($type == 'actual') {
            return $this->actual();
        }

        return new Tempcode();
    }

    /**
     * The UI for choosing members to merge.
     *
     * @return Tempcode The UI
     */
    public function gui()
    {
        $fields = new Tempcode();

        require_code('form_templates');

        $from = get_param_string('from', '', true);
        $to = get_param_string('to', '', true);

        $fields->attach(form_input_username(do_lang_tempcode('FROM'), do_lang_tempcode('DESCRIPTION_MEMBER_FROM'), 'from', $from, true));
        $fields->attach(form_input_username(do_lang_tempcode('TO'), do_lang_tempcode('DESCRIPTION_MEMBER_TO'), 'to', $to, true));

        if (is_on_multi_site_network()) {
            $fields->attach(form_input_tick(do_lang_tempcode('MERGING_ON_MSN'), do_lang_tempcode('DESCRIPTION_MERGING_ON_MSN'), 'keep', true));
        }

        $submit_name = do_lang_tempcode('MERGE_MEMBERS');
        $post_url = build_url(array('page' => '_SELF', 'type' => 'actual'), '_SELF');
        $text = do_lang_tempcode('MERGE_MEMBERS_TEXT');
        return do_template('FORM_SCREEN', array('_GUID' => '6f6b18d90bbe9550303ab41be0a26dcb', 'SKIP_WEBSTANDARDS' => true, 'TITLE' => $this->title, 'URL' => $post_url, 'FIELDS' => $fields, 'HIDDEN' => '', 'TEXT' => $text, 'SUBMIT_ICON' => 'menu___generic_admin__merge', 'SUBMIT_NAME' => $submit_name));
    }

    /**
     * The actualiser for merging members.
     *
     * @return Tempcode The UI
     */
    public function actual()
    {
        $to_username = post_param_string('to');
        $to_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($to_username);
        if ((is_null($to_id)) || (is_guest($to_id))) {
            warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($to_username)));
        }
        $from_username = post_param_string('from');
        $from_id = $GLOBALS['FORUM_DRIVER']->get_member_from_username($from_username);

        if ((is_null($from_id)) || (is_guest($from_id))) {
            warn_exit(do_lang_tempcode('_MEMBER_NO_EXIST', escape_html($from_username)));
        }

        if ($to_id == $from_id) {
            warn_exit(do_lang_tempcode('MERGE_SAME'));
        }

        // Reassign submitter field values
        $meta = $GLOBALS['SITE_DB']->query('SELECT m_table,m_name FROM ' . get_table_prefix() . 'db_meta WHERE ' . db_string_equal_to('m_type', 'MEMBER') . ' OR ' . db_string_equal_to('m_type', '?MEMBER') . ' OR ' . db_string_equal_to('m_type', '*MEMBER'));
        foreach ($meta as $m) {
            $db = (substr($m['m_table'], 0, 2) == 'f_') ? $GLOBALS['FORUM_DB'] : $GLOBALS['SITE_DB'];
            $db->query_update($m['m_table'], array($m['m_name'] => $to_id), array($m['m_name'] => $from_id), '', null, null, false, true);
        }

        // Reassign poster usernames
        $GLOBALS['FORUM_DB']->query_update('f_posts', array('p_poster_name_if_guest' => $to_username), array('p_poster' => $from_id));

        // Merge in post count caching
        $new_post_count = $GLOBALS['FORUM_DRIVER']->get_member_row_field($from_id, 'm_cache_num_posts') + $GLOBALS['FORUM_DRIVER']->get_member_row_field($to_id, 'm_cache_num_posts');
        $GLOBALS['FORUM_DB']->query_update('f_members', array('m_cache_num_posts' => $new_post_count), array('id' => $to_id), '', 1);

        // Reassign personal galleries
        if (addon_installed('galleries')) {
            $personal_galleries = $GLOBALS['SITE_DB']->query('SELECT name FROM ' . get_table_prefix() . 'galleries WHERE name LIKE \'member_' . strval($from_id) . '\_%\'');
            foreach ($personal_galleries as $gallery) {
                $old_gallery_name = $gallery['name'];
                $new_gallery_name = preg_replace('#^member_\d+_#', 'member_' . strval($to_id) . '_', $old_gallery_name);

                $GLOBALS['SITE_DB']->query_update('galleries', array('parent_id' => $new_gallery_name), array('parent_id' => $old_gallery_name));
                $GLOBALS['SITE_DB']->query_update('images', array('cat' => $new_gallery_name), array('cat' => $old_gallery_name));
                $GLOBALS['SITE_DB']->query_update('videos', array('cat' => $new_gallery_name), array('cat' => $old_gallery_name));

                $test = $GLOBALS['SITE_DB']->query_select_value_if_there('galleries', 'name', array('name' => $new_gallery_name));
                if ($test === null) { // Rename
                    $GLOBALS['SITE_DB']->query_update('galleries', array('name' => $new_gallery_name), array('name' => $old_gallery_name), '', 1);
                } else { // Delete
                    require_code('galleries2');
                    delete_gallery($old_gallery_name);
                }
            }
        }

        require_code('cns_members_action');
        require_code('cns_members_action2');

        // Merge in CPFs
        $custom_fields = cns_get_all_custom_fields_match_member($from_id);
        foreach ($custom_fields as $details) {
            if ($details['RAW'] != '') {
                cns_set_custom_field($to_id, intval($details['FIELD_ID']), $details['RAW']);
            }
        }

        // Delete old member
        if (post_param_integer('keep', 0) != 1) {
            cns_delete_member($from_id);
        }

        // Cache emptying ...
        cns_require_all_forum_stuff();

        require_code('cns_posts_action');
        require_code('cns_posts_action2');
        require_code('cns_topics_action2');
        require_code('cns_forums_action2');

        // Members
        cns_force_update_member_post_count($to_id);
        $num_warnings = $GLOBALS['FORUM_DB']->query_select_value('f_warnings', 'COUNT(*)', array('w_member_id' => $to_id));
        $GLOBALS['FORUM_DB']->query_update('f_members', array('m_cache_warnings' => $num_warnings), array('id' => $to_id), '', 1);

        // Topics and posts
        require_code('cns_topics_action');
        $topics = $GLOBALS['FORUM_DB']->query_select('f_topics', array('id', 't_forum_id'), array('t_cache_first_member_id' => $from_id));
        foreach ($topics as $topic) {
            cns_force_update_topic_caching($topic['id'], null, true, true);
        }
        $topics = $GLOBALS['FORUM_DB']->query_select('f_topics', array('id', 't_forum_id'), array('t_cache_last_member_id' => $from_id));
        foreach ($topics as $topic) {
            cns_force_update_topic_caching($topic['id'], null, true, true);
        }

        // Forums
        require_code('cns_posts_action2');
        $forums = $GLOBALS['FORUM_DB']->query_select('f_forums', array('id'), array('f_cache_last_member_id' => $from_id));
        foreach ($forums as $forum) {
            cns_force_update_forum_caching($forum['id']);
        }

        // ---

        log_it('MERGE_MEMBERS', $from_username, $to_username);

        $username = $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($to_id, false, '', false);
        return inform_screen($this->title, do_lang_tempcode('MERGED_MEMBERS', $username));
    }
}

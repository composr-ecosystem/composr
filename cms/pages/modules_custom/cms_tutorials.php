<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_cms_tutorials extends Standard_crud_module
{
    public $lang_type = 'TUTORIAL';
    public $special_edit_frontend = true;
    public $archive_entry_point = '_SEARCH:tutorials';
    public $user_facing = true;
    public $check_validation = false;
    public $permissions_require = 'low';
    public $menu_label = 'TUTORIALS';
    public $select_name = 'TUTORIALS';
    public $orderer = 't_title';
    public $table = 'tutorials_external';
    public $do_preview = null;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @param  boolean $top_level Whether this is running at the top level, prior to having sub-objects called.
     * @param  ?ID_TEXT $type The screen type to consider for metadata purposes (null: read from environment).
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run($top_level = true, $type = null)
    {
        $type = get_param_string('type', 'browse');

        require_lang('tutorials');

        set_helper_panel_text(comcode_lang_string('DOC_TUTORIALS'));

        return parent::pre_run($top_level);
    }

    /**
     * Standard crud_module run_start.
     *
     * @param  ID_TEXT $type The type of module execution
     * @return Tempcode The output of the run
     */
    public function run_start($type)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        require_code('tutorials');

        if (!module_installed('tutorials')) {
            require_code('zones2');

            $test = $GLOBALS['SITE_DB']->query_select_value_if_there('zones', 'zone_header_text', array('zone_name' => 'docs'));
            if (is_null($test)) {
                actual_add_zone('docs', do_lang('TUTORIALS'), 'tutorials');
            }

            reinstall_module('docs', 'tutorials');
        }

        if ($type == 'browse') {
            return $this->browse();
        }

        return new Tempcode();
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
        return array(
            'browse' => array('tutorials:TUTORIALS', 'menu/pages/help'),
        ) + parent::get_entry_points();
    }

    /**
     * The do-next manager for before content management.
     *
     * @return Tempcode The UI
     */
    public function browse()
    {
        require_code('templates_donext');
        require_code('fields');
        return do_next_manager(get_screen_title('TUTORIALS'), new Tempcode(),
            array(
                has_privilege(get_member(), 'submit_lowrange_content', 'cms_tutorials') ? array('menu/_generic_admin/add_one', array('_SELF', array('type' => 'add'), '_SELF'), do_lang('ADD_TUTORIAL')) : null,
                has_privilege(get_member(), 'edit_own_lowrange_content', 'cms_tutorials') ? array('menu/_generic_admin/edit_one', array('_SELF', array('type' => 'edit'), '_SELF'), do_lang('EDIT_TUTORIAL')) : null,
            ),
            do_lang('TUTORIALS')
        );
    }

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen.
     * @return array A quartet: The choose table, Whether reordering is supported from this screen, Search URL, Archive URL.
     */
    public function create_selection_list_choose_table($url_map)
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 't_add_date DESC', true);
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = array(
            't_title' => do_lang_tempcode('TITLE'),
            't_author' => do_lang_tempcode('AUTHOR'),
            't_submitter' => do_lang_tempcode('SUBMITTER'),
            't_media_type' => do_lang_tempcode('MEDIA'),
            't_add_date' => do_lang_tempcode('DATE'),
            't_pinned' => do_lang_tempcode('cns:PINNED'),
        );
        if (((strtoupper($sort_order) != 'ASC') && (strtoupper($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $fh = array(
            do_lang_tempcode('TITLE'),
            do_lang_tempcode('URL'),
            do_lang_tempcode('MEDIA'),
            do_lang_tempcode('AUTHOR'),
            do_lang_tempcode('USERNAME'),
            do_lang_tempcode('DATE'),
        );
        $fh[] = do_lang_tempcode('ACTIONS');

        $header_row = results_field_title($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        require_code('form_templates');
        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_link = build_url($url_map + array('id' => $row['id']), '_SELF');

            $username = $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($row['t_submitter'], false, '', false);

            $map = array(
                $row['t_title'],
                $row['t_url'],
                $row['t_media_type'],
                $row['t_author'],
                protect_from_escaping($username),
                get_timezoned_date($row['t_add_date']),
            );

            $map[] = protect_from_escaping(hyperlink($edit_link, do_lang_tempcode('EDIT'), false, true, do_lang('EDIT') . ' #' . strval($row['id'])));

            $fields->attach(results_entry($map, true));
        }

        $search_url = null;
        $archive_url = null;

        return array(results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', get_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false, $search_url, $archive_url);
    }

    /**
     * Get Tempcode for an external tutorial adding/editing form.
     *
     * @param  ?AUTO_LINK $id ID (null: not added yet)
     * @param  URLPATH $url URL
     * @param  SHORT_TEXT $title Title
     * @param  LONG_TEXT $summary Summary
     * @param  URLPATH $icon Icon
     * @param  ?ID_TEXT $media_type Media type (null: default)
     * @set document video audio slideshow book
     * @param  ID_TEXT $difficulty_level Difficulty level
     * @set novice regular expert
     * @param  BINARY $pinned Whether is pinned
     * @param  ID_TEXT $author Author
     * @param  ?array $tags List of tags (null: default)
     * @return array A pair: the Tempcode for the visible fields, and the Tempcode for the hidden fields
     */
    public function get_form_fields($id = null, $url = '', $title = '', $summary = '', $icon = '', $media_type = null, $difficulty_level = 'regular', $pinned = 0, $author = '', $tags = null)
    {
        if ($author == '') {
            $author = $GLOBALS['FORUM_DRIVER']->get_username(get_member());
            if ($GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
                $author .= ', ocProducts';
            }
        }

        if ($media_type === null) {
            $media_type = get_param_string('media_type', 'document');
        }

        if ($tags === null || $icon == '') {
            $tag = get_param_string('tag', null);
            if ($tag !== null) {
                if ($tags === null) {
                    $tags = array($tag);
                }
                if ($icon == '') {
                    $icon = 'tutorial_icons/' . strtolower($tag);
                }
            }
        }
        if ($tags === null) {
            $tags = array();
        }

        $fields = new Tempcode();

        $hidden = new Tempcode();

        $fields->attach(form_input_url('URL', 'The direct URL to the tutorial (be it on a private website, on the forum, on YouTube, etc).', 'url', $url, true));

        $fields->attach(form_input_line('Title', 'The title to the tutorial.', 'title', $title, true));

        $fields->attach(form_input_text('Summary', 'A short paragraph describing the tutorial.', 'summary', $summary, true));

        require_code('themes2');
        $ids = get_all_image_ids_type('tutorial_icons');
        $fields->attach(form_input_theme_image('Icon', 'Icon for the tutorial.', 'icon', $ids, null, $icon));

        $content = new Tempcode();
        foreach (array('document', 'video', 'audio', 'slideshow', 'book') as $_media_type) {
            $content->attach(form_input_list_entry($_media_type, $_media_type == $media_type, titleify($_media_type)));
        }
        $fields->attach(form_input_list('Media type', 'What kind of media is the tutorial presented in.', 'media_type', $content));

        $content = new Tempcode();
        foreach (array('novice', 'regular', 'expert') as $_difficulty_level) {
            $content->attach(form_input_list_entry($_difficulty_level, $_difficulty_level == $difficulty_level, titleify($_difficulty_level)));
        }
        $fields->attach(form_input_list('Difficulty level', 'A realistic assessment of who should be reading this. If it has different sections for different levels, choose the lowest, but make sure the tutorial somehow specifies which are the hard bits.', 'difficulty_level', $content));

        if ($GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
            $fields->attach(form_input_tick('Pinned', 'Whether the tutorial is pinned (curated onto front page).', 'pinned', $pinned == 1));
        }

        $fields->attach(form_input_author('Author', 'Who wrote this tutorial.', 'author', $author, true));

        $content = new Tempcode();
        foreach (list_tutorial_tags() as $_tag) {
            $content->attach(form_input_list_entry($_tag, in_array($_tag, $tags), $_tag));
        }
        $fields->attach(form_input_multi_list('Tags', 'At least one tag to classify the tutorial. Use as many as are appropriate.', 'tags', $content, null, 5, true));

        return array($fields, $hidden);
    }

    /**
     * Standard crud_module submitter getter.
     *
     * @param  ID_TEXT $id The entry for which the submitter is sought
     * @return array The submitter, and the time of submission (null submission time implies no known submission time)
     */
    public function get_submitter($id)
    {
        $rows = $GLOBALS['SITE_DB']->query_select('tutorials_external', array('t_submitter', 't_add_date'), array('id' => intval($id)), '', 1);
        if (!array_key_exists(0, $rows)) {
            return array(null, null);
        }
        return array($rows[0]['t_submitter'], $rows[0]['t_add_date']);
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return array A pair: the Tempcode for the visible fields, and the Tempcode for the hidden fields
     */
    public function fill_in_edit_form($id)
    {
        $rows = $GLOBALS['SITE_DB']->query_select('tutorials_external', array('*'), array('id' => intval($id)));
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $tags = collapse_1d_complexity('t_tag', $GLOBALS['SITE_DB']->query_select('tutorials_external_tags', array('t_tag'), array(
            't_id' => intval($id),
        )));

        return $this->get_form_fields($myrow['id'], $myrow['t_url'], $myrow['t_title'], $myrow['t_summary'], $myrow['t_icon'], $myrow['t_media_type'], $myrow['t_difficulty_level'], $myrow['t_pinned'], $myrow['t_author'], $tags);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return ID_TEXT The entry added
     */
    public function add_actualisation()
    {
        $url = post_param_string('url');
        $title = post_param_string('title');
        $summary = post_param_string('summary');
        $icon = post_param_string('icon');
        $media_type = post_param_string('media_type');
        $difficulty_level = post_param_string('difficulty_level');
        $pinned = post_param_integer('pinned', 0);
        if (!$GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
            $pinned = 0;
        }
        $author = post_param_string('author');
        $tags = empty($_POST['tags']) ? array() : $_POST['tags'];

        $id = $GLOBALS['SITE_DB']->query_insert('tutorials_external', array(
            't_url' => $url,
            't_title' => $title,
            't_summary' => $summary,
            't_icon' => $icon,
            't_media_type' => $media_type,
            't_difficulty_level' => $difficulty_level,
            't_pinned' => $pinned,
            't_author' => $author,
            't_submitter' => get_member(),
            't_views' => 0,
            't_add_date' => time(),
            't_edit_date' => time(),
        ), true);

        foreach ($tags as $tag) {
            $GLOBALS['SITE_DB']->query_insert('tutorials_external_tags', array(
                't_id' => $id,
                't_tag' => $tag,
            ));
        }

        log_it('ADD_TUTORIAL', strval($id), $title);

        $username = $GLOBALS['FORUM_DRIVER']->get_username(get_member());

        require_code('notifications');
        $subject = 'New tutorial added, ' . $title;
        $mail = 'A new tutorial, [url="' . $title . '"]' . $url . '[/url], has been added by ' . $username . '.';
        dispatch_notification('tutorial_added', null, $subject, $mail);

        @unlink(get_custom_file_base() . '/uploads/website_specific/tutorial_sigs.bin');

        return strval($id);
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     */
    public function edit_actualisation($_id)
    {
        $id = intval($_id);

        $url = post_param_string('url');
        $title = post_param_string('title');
        $summary = post_param_string('summary');
        $icon = post_param_string('icon');
        $media_type = post_param_string('media_type');
        $difficulty_level = post_param_string('difficulty_level');
        $pinned = post_param_integer('pinned', 0);
        if (!$GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
            $pinned = 0;
        }
        $author = post_param_string('author');
        $tags = empty($_POST['tags']) ? array() : $_POST['tags'];

        $GLOBALS['SITE_DB']->query_update('tutorials_external', array(
            't_url' => $url,
            't_title' => $title,
            't_summary' => $summary,
            't_icon' => $icon,
            't_media_type' => $media_type,
            't_difficulty_level' => $difficulty_level,
            't_pinned' => $pinned,
            't_author' => $author,
            't_edit_date' => time(),
        ), array('id' => $id), '', 1);

        $GLOBALS['SITE_DB']->query_delete('tutorials_external_tags', array('t_id' => $id));
        foreach ($tags as $tag) {
            $GLOBALS['SITE_DB']->query_insert('tutorials_external_tags', array(
                't_id' => $id,
                't_tag' => $tag,
            ));
        }

        log_it('EDIT_TUTORIAL', strval($id), $title);

        @unlink(get_custom_file_base() . '/uploads/website_specific/tutorial_sigs.bin');
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation($_id)
    {
        $id = intval($_id);

        $title = $GLOBALS['SITE_DB']->query_select_value_if_there('tutorials_external', 't_title', array('id' => $id));
        if ($title === null) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        $GLOBALS['SITE_DB']->query_delete('tutorials_external', array('id' => $id), '', 1);
        $GLOBALS['SITE_DB']->query_delete('tutorials_external_tags', array('t_id' => $id));

        log_it('DELETE_TUTORIAL', strval($id), $title);

        @unlink(get_custom_file_base() . '/uploads/website_specific/tutorial_sigs.bin');
    }
}

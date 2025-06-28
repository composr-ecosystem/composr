<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_tutorials
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_cms_tutorials extends Standard_crud_module
{
    protected $lang_type = 'TUTORIAL';
    protected $special_edit_frontend = true;
    protected $archive_entry_point = '_SEARCH:tutorials';
    protected $user_facing = true;
    protected $check_validation = false;
    protected $permissions_require = 'low';
    protected $menu_label = 'TUTORIALS';
    protected $select_name = 'TUTORIALS';
    protected $orderer = 't_title';
    protected $table = 'tutorials_external';
    protected $do_preview = null;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @param  boolean $top_level Whether this is running at the top level, prior to having sub-objects called
     * @param  ?ID_TEXT $type The screen type to consider for metadata purposes (null: read from environment)
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run(bool $top_level = true, ?string $type = null) : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('composr_tutorials', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('cms_homesite')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite')));
        }
        if (!addon_installed('cms_release_build')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('cms_release_build')));
        }

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
    public function run_start(string $type) : object
    {
        $this->edit_this_label = do_lang_tempcode('EDIT_THIS_TUTORIAL');

        require_code('tutorials');

        if ($type == 'browse') {
            return $this->browse();
        }

        return new Tempcode();
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('composr_tutorials')) {
            return null;
        }

        return [
            'browse' => ['tutorials:TUTORIALS', 'help'],
        ] + parent::get_entry_points();
    }

    /**
     * The do-next manager for before content management.
     *
     * @return Tempcode The UI
     */
    public function browse() : object
    {
        require_code('templates_donext');
        require_code('fields');
        return do_next_manager(
            get_screen_title('TUTORIALS'),
            new Tempcode(),
            [
                has_privilege(get_member(), 'submit_lowrange_content', 'cms_tutorials') ? ['admin/add', ['_SELF', ['type' => 'add'], '_SELF'], do_lang('ADD_TUTORIAL')] : null,
                has_privilege(get_member(), 'edit_own_lowrange_content', 'cms_tutorials') ? ['admin/edit', ['_SELF', ['type' => 'edit'], '_SELF'], do_lang('EDIT_TUTORIAL')] : null,
            ],
            do_lang('TUTORIALS')
        );
    }

    /**
     * Get Tempcode for an adding form.
     *
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function get_form_fields_for_add()
    {
        return $this->get_form_fields();
    }

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen.
     * @return array A quartet: The choose table, Whether reordering is supported from this screen, Search URL, Archive URL.
     */
    public function create_selection_list_choose_table(array $url_map) : array
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 't_add_date DESC', INPUT_FILTER_GET_COMPLEX);
        $sortables = [
            't_title' => do_lang_tempcode('TITLE'),
            't_author' => do_lang_tempcode('AUTHOR'),
            't_submitter' => do_lang_tempcode('SUBMITTER'),
            't_media_type' => do_lang_tempcode('MEDIA'),
            't_add_date' => do_lang_tempcode('DATE'),
            't_pinned' => do_lang_tempcode('cns:PINNED'),
        ];
        list($sql_sort, $sort_order, $sortable) = process_sorting_params('tutorials_external', $current_ordering);

        $fh = [
            do_lang_tempcode('TITLE'),
            do_lang_tempcode('URL'),
            do_lang_tempcode('MEDIA'),
            do_lang_tempcode('AUTHOR'),
            do_lang_tempcode('USERNAME'),
            do_lang_tempcode('DATE'),
        ];
        $fh[] = do_lang_tempcode('ACTIONS');

        $header_row = results_header_row($fh, $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        require_code('form_templates');
        list($rows, $max_rows) = $this->get_entry_rows(false, $sql_sort);
        foreach ($rows as $row) {
            $edit_link = build_url($url_map + ['id' => $row['id']], '_SELF');

            $username = $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($row['t_submitter'], '', false);

            $map = [
                $row['t_title'],
                $row['t_url'],
                $row['t_media_type'],
                $row['t_author'],
                protect_from_escaping($username),
                get_timezoned_date($row['t_add_date']),
            ];

            $map[] = protect_from_escaping(hyperlink($edit_link, do_lang_tempcode('EDIT'), false, true, do_lang('EDIT') . ' #' . strval($row['id'])));

            $fields->attach(results_entry($map, true));
        }

        $search_url = null;
        $archive_url = null;

        return [results_table(do_lang($this->menu_label), get_param_integer('start', 0), 'start', get_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false, $search_url, $archive_url];
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
     * @return array A pair: The input fields, Hidden fields
     */
    public function get_form_fields(?int $id = null, string $url = '', string $title = '', string $summary = '', string $icon = '', ?string $media_type = null, string $difficulty_level = 'regular', int $pinned = 0, string $author = '', ?array $tags = null) : array
    {
        if ($author == '') {
            $author = $GLOBALS['FORUM_DRIVER']->get_username(get_member());
            if ($GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
                $author .= ', Core Development Team';
            }
        }

        if ($media_type === null) {
            $media_type = get_param_string('media_type', 'document');
        }

        if ($tags === null || $icon == '') {
            $tag = get_param_string('tag', null);
            if ($tag !== null) {
                if ($tags === null) {
                    $tags = [$tag];
                }
                if ($icon == '') {
                    $icon = _find_tutorial_image_for_tag($tag);
                }
            }
        }
        if ($tags === null) {
            $tags = [];
        }

        $fields = new Tempcode();

        $hidden = new Tempcode();

        $fields->attach(form_input_url('URL', 'The direct URL to the tutorial (be it on a private website, on the forum, on Vimeo, on YouTube, etc).', 'url', $url, true));

        $fields->attach(form_input_line('Title', 'The title to the tutorial.', 'title', $title, true));

        $fields->attach(form_input_text('Summary', 'A short paragraph describing the tutorial.', 'summary', $summary, true, false));

        require_code('themes2');
        $ids = get_all_image_ids_type('icons', true);
        $fields->attach(form_input_theme_image('Icon', 'Icon for the tutorial.', 'icon', $ids, null, $icon));

        $content = new Tempcode();
        foreach (['document', 'video', 'audio', 'slideshow', 'book'] as $_media_type) {
            $content->attach(form_input_list_entry($_media_type, $_media_type == $media_type, titleify($_media_type)));
        }
        $fields->attach(form_input_list('Media type', 'What kind of media is the tutorial presented in.', 'media_type', $content));

        $content = new Tempcode();
        foreach (['novice', 'regular', 'expert'] as $_difficulty_level) {
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

        return [$fields, $hidden];
    }

    /**
     * Standard crud_module submitter getter.
     *
     * @param  ID_TEXT $id The entry for which the submitter is sought
     * @return array The submitter, and the time of submission (null submission time implies no known submission time)
     */
    public function get_submitter(string $id) : array
    {
        $rows = $GLOBALS['SITE_DB']->query_select('tutorials_external', ['t_submitter', 't_add_date'], ['id' => intval($id)], '', 1);
        if (!array_key_exists(0, $rows)) {
            return [null, null];
        }
        return [$rows[0]['t_submitter'], $rows[0]['t_add_date']];
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function fill_in_edit_form(string $id)
    {
        $rows = $GLOBALS['SITE_DB']->query_select('tutorials_external', ['*'], ['id' => intval($id)]);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $tags = collapse_1d_complexity('t_tag', $GLOBALS['SITE_DB']->query_select('tutorials_external_tags', ['t_tag'], [
            't_id' => intval($id),
        ]));

        return $this->get_form_fields($myrow['id'], $myrow['t_url'], $myrow['t_title'], $myrow['t_summary'], $myrow['t_icon'], $myrow['t_media_type'], $myrow['t_difficulty_level'], $myrow['t_pinned'], $myrow['t_author'], $tags);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        $url = post_param_string('url', false, INPUT_FILTER_URL_GENERAL);
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
        $tags = empty($_POST['tags']) ? [] : $_POST['tags'];

        $id = $GLOBALS['SITE_DB']->query_insert('tutorials_external', [
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
        ], true);

        foreach ($tags as $tag) {
            $GLOBALS['SITE_DB']->query_insert('tutorials_external_tags', [
                't_id' => $id,
                't_tag' => $tag,
            ]);
        }

        log_it('ADD_TUTORIAL', strval($id), $title);

        $username = $GLOBALS['FORUM_DRIVER']->get_username(get_member());

        require_code('notifications');
        $subject = 'New tutorial added, ' . $title;
        $mail = 'A new tutorial, [url="' . $title . '"]' . $url . '[/url], has been added by ' . $username . '.';
        dispatch_notification('tutorial_added', null, $subject, $mail);

        require_code('caches');
        delete_cache_entry('tutorials');

        return [strval($id), null];
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return ?Tempcode Description about usage (null: none)
     */
    public function edit_actualisation(string $_id) : ?object
    {
        $id = intval($_id);

        $url = post_param_string('url', false, INPUT_FILTER_URL_GENERAL);
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
        $tags = empty($_POST['tags']) ? [] : $_POST['tags'];

        $GLOBALS['SITE_DB']->query_update('tutorials_external', [
            't_url' => $url,
            't_title' => $title,
            't_summary' => $summary,
            't_icon' => $icon,
            't_media_type' => $media_type,
            't_difficulty_level' => $difficulty_level,
            't_pinned' => $pinned,
            't_author' => $author,
            't_edit_date' => time(),
        ], ['id' => $id], '', 1);

        $GLOBALS['SITE_DB']->query_delete('tutorials_external_tags', ['t_id' => $id]);
        foreach ($tags as $tag) {
            $GLOBALS['SITE_DB']->query_insert('tutorials_external_tags', [
                't_id' => $id,
                't_tag' => $tag,
            ]);
        }

        log_it('EDIT_TUTORIAL', strval($id), $title);

        require_code('caches');
        delete_cache_entry('tutorials');

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation(string $_id)
    {
        $id = intval($_id);

        $title = $GLOBALS['SITE_DB']->query_select_value_if_there('tutorials_external', 't_title', ['id' => $id]);
        if ($title === null) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        $GLOBALS['SITE_DB']->query_delete('tutorials_external', ['id' => $id], '', 1);
        $GLOBALS['SITE_DB']->query_delete('tutorials_external_tags', ['t_id' => $id]);

        log_it('DELETE_TUTORIAL', strval($id), $title);

        require_code('caches');
        delete_cache_entry('tutorials');
    }
}

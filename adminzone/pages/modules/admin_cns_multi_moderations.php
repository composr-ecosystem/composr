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
 * @package    cns_multi_moderations
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_admin_cns_multi_moderations extends Standard_crud_module
{
    public $lang_type = 'MULTI_MODERATION';
    public $select_name = 'NAME';
    public $archive_entry_point = '_SEARCH:forumview';
    public $archive_label = 'SECTION_FORUMS';
    public $menu_label = 'MULTI_MODERATIONS';
    public $table = 'f_multi_moderations';
    public $orderer = 'mm_name';
    public $title_is_multi_lang = true;
    public $donext_entry_content_type = 'multi_moderation';
    public $donext_category_content_type = null;

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
            'browse' => array(do_lang_tempcode('menus:ITEMS_HERE', do_lang_tempcode('MULTI_MODERATIONS'), make_string_tempcode(escape_html(integer_format($GLOBALS['FORUM_DB']->query_select_value_if_there('f_multi_moderations', 'COUNT(*)', null, '', true))))), 'menu/adminzone/structure/forum/multi_moderations'),
        ) + parent::get_entry_points();
    }

    public $title;

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

        require_lang('cns');
        require_lang('cns_multi_moderations');
        require_css('cns_admin');

        set_helper_panel_tutorial('tut_moderation');

        if ($type == 'browse') {
            breadcrumb_set_parents(array(array('_SEARCH:admin_cns_forums:browse', do_lang_tempcode('MANAGE_FORUMS'))));
        } elseif ($type == 'import') {
            breadcrumb_set_parents(array(array('_SEARCH:admin_cns_forums:browse', do_lang_tempcode('MANAGE_FORUMS')), array('_SELF:_SELF:browse', do_lang_tempcode('MULTI_MODERATIONS'))));
        } elseif ($type == '_import') {
            breadcrumb_set_parents(array(array('_SEARCH:admin_cns_forums:browse', do_lang_tempcode('MANAGE_FORUMS')), array('_SELF:_SELF:browse', do_lang_tempcode('MULTI_MODERATIONS')), array('_SELF:_SELF:import', do_lang_tempcode('IMPORT_STOCK_RESPONSES'))));
        } else {
            breadcrumb_set_parents(array(array('_SEARCH:admin_cns_forums:browse', do_lang_tempcode('MANAGE_FORUMS')), array('_SELF:_SELF:browse', do_lang_tempcode('MULTI_MODERATIONS'))));
        }

        if ($type == 'import' || $type == '_import') {
            $this->title = get_screen_title('IMPORT_STOCK_RESPONSES');
        }

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
        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        } else {
            cns_require_all_forum_stuff();
        }
        require_code('cns_moderation_action');
        require_code('cns_moderation_action2');
        require_code('cns_general_action2');

        $this->add_one_label = do_lang_tempcode('ADD_MULTI_MODERATION');
        $this->edit_this_label = do_lang_tempcode('EDIT_THIS_MULTI_MODERATION');
        $this->edit_one_label = do_lang_tempcode('EDIT_MULTI_MODERATION');

        if ($type == 'browse') {
            return $this->browse();
        }
        if ($type == 'import') {
            return $this->import();
        }
        if ($type == '_import') {
            return $this->_import();
        }
        return new Tempcode();
    }

    /**
     * The do-next manager for before content management.
     *
     * @return Tempcode The UI
     */
    public function browse()
    {
        require_code('templates_donext');
        return do_next_manager(get_screen_title('MULTI_MODERATIONS'), comcode_lang_string('DOC_MULTI_MODERATIONS'),
            array(
                array('menu/_generic_admin/add_one', array('_SELF', array('type' => 'add'), '_SELF'), do_lang('ADD_MULTI_MODERATION')),
                array('menu/_generic_admin/edit_one', array('_SELF', array('type' => 'edit'), '_SELF'), do_lang('EDIT_MULTI_MODERATION')),
                array('menu/_generic_admin/import', array('_SELF', array('type' => 'import'), '_SELF'), do_lang('IMPORT_STOCK_RESPONSES')),
            ),
            do_lang('MULTI_MODERATIONS')
        );
    }

    /**
     * The UI to import in bulk from an archive file.
     *
     * @return Tempcode The UI
     */
    public function import()
    {
        require_code('form_templates');

        $post_url = build_url(array('page' => '_SELF', 'type' => '_import', 'uploading' => 1), '_SELF');

        $fields = new Tempcode();

        $supported = 'tar';
        if ((function_exists('zip_open')) || (get_option('unzip_cmd') != '')) {
            $supported .= ', zip';
        }
        $fields->attach(form_input_upload_multi(do_lang_tempcode('UPLOAD'), do_lang_tempcode('DESCRIPTION_ARCHIVE_TEXT_FILES', escape_html($supported), escape_html('txt')), 'file', true, null, null, true, 'txt,' . $supported));

        if (addon_installed('tickets')) {
            require_code('tickets');
            $ticket_forum_id = get_ticket_forum_id();
        } else {
            $ticket_forum_id = mixed();
        }
        require_code('cns_general_action2');
        $fields->attach(cns_get_forum_multi_code_field(is_null($ticket_forum_id) ? '' : ('+' . strval($ticket_forum_id))));

        $text = paragraph(do_lang_tempcode('DESCRIPTION_IMPORT_STOCK_RESPONSES'));

        return do_template('FORM_SCREEN', array('_GUID' => 'bd30d8b0077567e3caf9239ed64204e5', 'TITLE' => $this->title, 'FIELDS' => $fields, 'SUBMIT_ICON' => 'menu___generic_admin__import', 'SUBMIT_NAME' => do_lang_tempcode('IMPORT_STOCK_RESPONSES'), 'URL' => $post_url, 'TEXT' => $text, 'HIDDEN' => ''));
    }

    /**
     * The actualiser to import in bulk from an archive file.
     *
     * @return Tempcode The UI
     */
    public function _import()
    {
        require_lang('dearchive');

        require_code('files2');

        require_code('uploads');
        is_plupload(true);

        set_mass_import_mode();

        $target_forum = read_multi_code('forum_multi_code');

        $multi_mods = $GLOBALS['FORUM_DB']->query_select('f_multi_moderations', array('id'), array('mm_move_to' => null, 'mm_pin_state' => null, 'mm_sink_state' => null, 'mm_open_state' => null, 'mm_title_suffix' => '', 'mm_forum_multi_code' => $target_forum));
        require_code('cns_moderation_action2');
        foreach ($multi_mods as $multi_mod) {
            cns_delete_multi_moderation($multi_mod['id']);
        }

        foreach ($_FILES as $attach_name => $__file) {
            $tmp_name = $__file['tmp_name'];
            $file = $__file['name'];
            switch (get_file_extension($file)) {
                case 'zip':
                    if ((!function_exists('zip_open')) && (get_option('unzip_cmd') == '')) {
                        warn_exit(do_lang_tempcode('ZIP_NOT_ENABLED'));
                    }
                    if (!function_exists('zip_open')) {
                        require_code('m_zip');
                        $mzip = true;
                    } else {
                        $mzip = false;
                    }
                    $myfile = zip_open($tmp_name);
                    if (!is_integer($myfile)) {
                        while (false !== ($entry = zip_read($myfile))) {
                            // Load in file
                            zip_entry_open($myfile, $entry);

                            $filename = zip_entry_name($entry);

                            if (!check_extension($filename, false, null, true)) {
                                continue;
                            }

                            if ((strtolower(substr($filename, -4)) == '.txt') && (!should_ignore_file($filename))) {
                                $data = '';
                                do {
                                    $more = zip_entry_read($entry);
                                    if ($more !== false) {
                                        $data .= $more;
                                    }
                                } while (($more !== false) && ($more != ''));

                                $this->_import_stock_response($filename, $data, $target_forum);
                            }

                            zip_entry_close($entry);
                        }

                        zip_close($myfile);
                    } else {
                        require_code('failure');
                        warn_exit(zip_error($myfile, $mzip));
                    }
                    break;
                case 'tar':
                    require_code('tar');
                    $myfile = tar_open($tmp_name, 'rb');
                    if ($myfile !== false) {
                        $directory = tar_get_directory($myfile);
                        foreach ($directory as $entry) {
                            $filename = $entry['path'];

                            if (!check_extension($filename, false, null, true)) {
                                continue;
                            }

                            if ((strtolower(substr($filename, -4)) == '.txt') && (!should_ignore_file($filename))) {
                                // Load in file
                                $_in = tar_get_file($myfile, $entry['path'], false);

                                $this->_import_stock_response($filename, $_in['data'], $target_forum);
                            }
                        }

                        tar_close($myfile);
                    }
                    break;
                default:
                    if (!check_extension($file, false, null, true)) {
                        break;
                    }

                    if (strtolower(substr($file, -4)) == '.txt') {
                        $this->_import_stock_response($file, file_get_contents($tmp_name), $target_forum);
                    } else {
                        attach_message(do_lang_tempcode('BAD_ARCHIVE_FORMAT'), 'warn');
                    }
            }
        }

        log_it('IMPORT_STOCK_RESPONSES');

        return $this->do_next_manager($this->title, do_lang_tempcode('SUCCESS'), null);
    }

    /**
     * Import a stock response.
     *
     * @param  PATH $path Path of the file (not on disk, just for reference as a title).
     * @param  string $data Data.
     * @param  SHORT_TEXT $target_forum The forum multicode identifying where the multi-moderation is applicable
     */
    public function _import_stock_response($path, $data, $target_forum)
    {
        require_code('cns_moderation_action');

        $name = do_lang('STOCK_RESPONSE', ucwords(str_replace(array('/', '\\'), array(': ', ': '), preg_replace('#\.txt$#', '', $path))));

        $data = fix_bad_unicode($data);

        cns_make_multi_moderation($name, $data, null, null, null, null, $target_forum, '');
    }

    /**
     * Get Tempcode for adding/editing form.
     *
     * @param  SHORT_TEXT $name The name of the multi moderation
     * @param  LONG_TEXT $post_text The text to place as a post in the topic when the multi moderation is performed
     * @param  ?AUTO_LINK $move_to Move the topic to this forum (null: don't move)
     * @param  ?BINARY $pin_state What to change the pin state to (null: don't change)
     * @param  ?BINARY $open_state What to change the open state to (null: don't change)
     * @param  ?BINARY $sink_state What to change the sink state to (null: don't change)
     * @param  SHORT_TEXT $forum_multi_code The forum multicode identifying where the multi-moderation is applicable
     * @param  SHORT_TEXT $title_suffix The title suffix
     * @return array A pair: The input fields, Hidden fields
     */
    public function get_form_fields($name = '', $post_text = '', $move_to = null, $pin_state = null, $open_state = null, $sink_state = null, $forum_multi_code = '*', $title_suffix = '')
    {
        require_code('cns_forums2');

        $fields = new Tempcode();
        $fields->attach(form_input_line(do_lang_tempcode('NAME'), do_lang_tempcode('DESCRIPTION_NAME'), 'name', $name, true));
        $fields->attach(form_input_text_comcode(do_lang_tempcode('FORUM_POST'), do_lang_tempcode('DESCRIPTION_MULTI_MODERATION_POST'), 'post_text', $post_text, false));
        $fields->attach(form_input_tree_list(do_lang_tempcode('DESTINATION'), do_lang_tempcode('DESCRIPTION_DESTINATION_FORUM'), 'move_to', null, 'choose_forum', array(), false, is_null($move_to) ? null : strval($move_to)));
        $pin_state_list = new Tempcode();
        $pin_state_list->attach(form_input_radio_entry('pin_state', '-1', is_null($pin_state), do_lang_tempcode('NA_EM')));
        $pin_state_list->attach(form_input_radio_entry('pin_state', '0', $pin_state === 0, do_lang_tempcode('UNPIN_TOPIC')));
        $pin_state_list->attach(form_input_radio_entry('pin_state', '1', $pin_state === 1, do_lang_tempcode('PIN_TOPIC')));
        $fields->attach(form_input_radio(do_lang_tempcode('PIN_STATE'), do_lang_tempcode('DESCRIPTION_PIN_STATE'), 'pin_state', $pin_state_list));
        $open_state_list = new Tempcode();
        $open_state_list->attach(form_input_radio_entry('open_state', '-1', is_null($open_state), do_lang_tempcode('NA_EM')));
        $open_state_list->attach(form_input_radio_entry('open_state', '0', $open_state === 0, do_lang_tempcode('CLOSE_TOPIC')));
        $open_state_list->attach(form_input_radio_entry('open_state', '1', $open_state === 1, do_lang_tempcode('OPEN_TOPIC')));
        $fields->attach(form_input_radio(do_lang_tempcode('OPEN_STATE'), do_lang_tempcode('DESCRIPTION_OPEN_STATE'), 'open_state', $open_state_list));
        $sink_state_list = new Tempcode();
        $sink_state_list->attach(form_input_radio_entry('sink_state', '-1', is_null($sink_state), do_lang_tempcode('NA_EM')));
        $sink_state_list->attach(form_input_radio_entry('sink_state', '0', $sink_state === 0, do_lang_tempcode('SINK_TOPIC')));
        $sink_state_list->attach(form_input_radio_entry('sink_state', '1', $sink_state === 1, do_lang_tempcode('UNSINK_TOPIC')));
        $fields->attach(form_input_radio(do_lang_tempcode('SINK_STATE'), do_lang_tempcode('DESCRIPTION_SINK_STATE'), 'sink_state', $sink_state_list));
        $fields->attach(cns_get_forum_multi_code_field($forum_multi_code));
        $fields->attach(form_input_line(do_lang_tempcode('TITLE_SUFFIX'), do_lang_tempcode('DESCRIPTION_TITLE_SUFFIX'), 'title_suffix', $title_suffix, false));

        return array($fields, new Tempcode());
    }

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen.
     * @return array A pair: The choose table, Whether reordering is supported from this screen.
     */
    public function create_selection_list_choose_table($url_map)
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'mm_name ASC');
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = array(
            'mm_name' => do_lang_tempcode('NAME'),
            'mm_pin_state' => do_lang_tempcode('PIN_STATE'),
            'mm_open_state' => do_lang_tempcode('OPEN_STATE'),
            'mm_sink_state' => do_lang_tempcode('SINK_STATE'),
        );
        if (((strtoupper($sort_order) != 'ASC') && (strtoupper($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $header_row = results_field_title(array(
            do_lang_tempcode('NAME'),
            do_lang_tempcode('DESTINATION'),
            do_lang_tempcode('PIN_STATE'),
            do_lang_tempcode('OPEN_STATE'),
            do_lang_tempcode('SINK_STATE'),
            do_lang_tempcode('ACTIONS'),
        ), $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        require_code('form_templates');
        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $pin_state = do_lang_tempcode('NA_EM');
            if (!is_null($row['mm_pin_state'])) {
                switch ($row['mm_pin_state']) {
                    case 0:
                        $pin_state = do_lang_tempcode('UNPIN_TOPIC');
                        break;
                    case 1:
                        $pin_state = do_lang_tempcode('PIN_TOPIC');
                        break;
                }
            }
            $open_state = do_lang_tempcode('NA_EM');
            if (!is_null($row['mm_open_state'])) {
                switch ($row['mm_open_state']) {
                    case 0:
                        $open_state = do_lang_tempcode('CLOSE_TOPIC');
                        break;
                    case 1:
                        $open_state = do_lang_tempcode('OPEN_TOPIC');
                        break;
                }
            }
            $sink_state = do_lang_tempcode('NA_EM');
            if (!is_null($row['mm_sink_state'])) {
                switch ($row['mm_sink_state']) {
                    case 0:
                        $sink_state = do_lang_tempcode('SINK_TOPIC');
                        break;
                    case 1:
                        $sink_state = do_lang_tempcode('UNSINK_TOPIC');
                        break;
                }
            }

            $destination = is_null($row['mm_move_to']) ? null : $GLOBALS['FORUM_DB']->query_select_value_if_there('f_forums', 'f_name', array('id' => $row['mm_move_to']));
            if (is_null($destination)) {
                $destination = do_lang_tempcode('NA_EM');
            }

            $edit_link = build_url($url_map + array('id' => $row['id']), '_SELF');

            $fields->attach(results_entry(array(get_translated_text($row['mm_name'], $GLOBALS['FORUM_DB']), $destination, $pin_state, $open_state, $sink_state, protect_from_escaping(hyperlink($edit_link, do_lang_tempcode('EDIT'), false, false, do_lang('EDIT') . ' #' . strval($row['id'])))), true));
        }

        return array(results_table(do_lang($this->menu_label), either_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false);
    }

    /**
     * Standard crud_module list function.
     *
     * @return Tempcode The selection list
     */
    public function create_selection_list_entries()
    {
        $_m = $GLOBALS['FORUM_DB']->query_select('f_multi_moderations', array('id', 'mm_name'));
        $entries = new Tempcode();
        foreach ($_m as $m) {
            $entries->attach(form_input_list_entry(strval($m['id']), false, get_translated_text($m['mm_name'], $GLOBALS['FORUM_DB'])));
        }

        return $entries;
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return array A pair: The input fields, Hidden fields
     */
    public function fill_in_edit_form($id)
    {
        $m = $GLOBALS['FORUM_DB']->query_select('f_multi_moderations', array('*'), array('id' => intval($id)), '', 1);
        if (!array_key_exists(0, $m)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE', 'multi_moderation'));
        }
        $r = $m[0];

        return $this->get_form_fields(get_translated_text($r['mm_name'], $GLOBALS['FORUM_DB']), $r['mm_post_text'], $r['mm_move_to'], $r['mm_pin_state'], $r['mm_open_state'], $r['mm_sink_state'], $r['mm_forum_multi_code'], $r['mm_title_suffix']);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return ID_TEXT The entry added
     */
    public function add_actualisation()
    {
        $pin_state = mixed();
        $pin_state = post_param_integer('pin_state', 0);
        if ($pin_state == -1) {
            $pin_state = null;
        }

        $sink_state = mixed();
        $sink_state = post_param_integer('sink_state', 0);
        if ($sink_state == -1) {
            $sink_state = null;
        }

        $open_state = mixed();
        $open_state = post_param_integer('open_state', 0);
        if ($open_state == -1) {
            $open_state = null;
        }

        require_code('form_templates');
        return strval(cns_make_multi_moderation(post_param_string('name'), post_param_string('post_text'), post_param_integer('move_to', null), $pin_state, $sink_state, $open_state, read_multi_code('forum_multi_code'), post_param_string('title_suffix')));
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $id The entry being edited
     */
    public function edit_actualisation($id)
    {
        $pin_state = mixed();
        $pin_state = post_param_integer('pin_state', 0);
        if ($pin_state == -1) {
            $pin_state = null;
        }

        $sink_state = mixed();
        $sink_state = post_param_integer('sink_state', 0);
        if ($sink_state == -1) {
            $sink_state = null;
        }

        $open_state = mixed();
        $open_state = post_param_integer('open_state', 0);
        if ($open_state == -1) {
            $open_state = null;
        }

        require_code('form_templates');
        cns_edit_multi_moderation(intval($id), post_param_string('name'), post_param_string('post_text'), post_param_integer('move_to', null), $pin_state, $sink_state, $open_state, read_multi_code('forum_multi_code'), post_param_string('title_suffix'));
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $id The entry being deleted
     */
    public function delete_actualisation($id)
    {
        cns_delete_multi_moderation(intval($id));
    }
}

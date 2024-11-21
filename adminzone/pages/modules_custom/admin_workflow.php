<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    workflows
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_admin_workflow extends Standard_crud_module
{
    protected $lang_type = 'WORKFLOW';
    protected $select_name = 'NAME';
    protected $menu_label = 'WORKFLOW';
    protected $appended_actions_already = true;
    protected $do_preview = null;

    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Warburton';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'workflows';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'workflows',
            'workflow_permissions',
            'workflow_approval_points',
            'workflow_content',
            'workflow_content_status',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        // Create required database structures

        require_lang('workflows');

        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('workflows', [
                'id' => '*AUTO', // ID
                'workflow_name' => 'SHORT_TRANS', // The name (and ID) of this approval point
                'is_default' => 'BINARY',
            ]);

            // The workflow_approval_points table records which workflows require which points to approve
            $GLOBALS['SITE_DB']->create_table('workflow_approval_points', [
                'id' => '*AUTO', // ID for reference
                'workflow_id' => 'AUTO_LINK', // The name (and ID) of this workflow
                'workflow_approval_name' => 'SHORT_TRANS', // The name (and ID) of the approval point to require in this workflow
                'the_position' => 'INTEGER', // The position of this approval point in the workflow (ie. any approval can be given at any time, but encourage users into a prespecified order)
            ]);

            // The workflow_permissions table stores which usergroups are allowed to approve which points
            $GLOBALS['SITE_DB']->create_table('workflow_permissions', [
                'id' => '*AUTO', // ID for reference
                'workflow_approval_point_id' => 'AUTO_LINK', // The ID of the approval point
                'usergroup' => 'GROUP', // The usergroup to give permission to
            ]);

            // The workflow_content table records which site resources are in which workflows, along with any notes made during the approval process
            $GLOBALS['SITE_DB']->create_table('workflow_content', [
                'id' => '*AUTO', // ID for reference
                'content_type' => 'ID_TEXT', // The content-meta-aware type we'd find this content in
                'content_id' => 'ID_TEXT', // The ID of the content, wherever it happens to be
                'workflow_id' => 'AUTO_LINK', // The ID of the workflow this content is in
                'notes' => 'LONG_TEXT', // No point translating the notes, since they're transient
                'original_submitter' => 'MEMBER', // Save this here since there's no standard way to discover it later (e.g. through content-meta-aware hooks)
            ]);

            // The workflow_content_status table records the status of each approval point for a piece of content and the member who approved the point (if any)
            $GLOBALS['SITE_DB']->create_table('workflow_content_status', [
                'id' => '*AUTO', // ID for reference. Larger IDs will override smaller ones if they report a different status (nondeterministic for non-incremental IDs!)
                'workflow_content_id' => 'AUTO_LINK', // The ID of this content in the workflow_content table
                'workflow_approval_point_id' => 'AUTO_LINK', // The ID of the approval point
                'status_code' => 'SHORT_INTEGER', // A code indicating the status
                'approved_by_member' => 'MEMBER', // Remember who set this status, if the need arises to investigate this later
            ]);
        }

        if (($upgrade_from !== null) && ($upgrade_from < 2)) { // LEGACY: 11.beta1
            // Database integrity fixes
            $GLOBALS['SITE_DB']->alter_table_field('workflow_content_status', 'approved_by', 'MEMBER', 'approved_by_member');
        }
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
        if (!addon_installed('workflows')) {
            return null;
        }

        return [
            'browse' => ['MANAGE_WORKFLOWS', 'spare/workflows'],
        ] + parent::get_entry_points();
    }

    public $title;
    public $doing;

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
        if (!addon_installed__messaged('workflows', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('validation')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('validation')));
        }

        $type = get_param_string('type', 'browse');

        require_code('workflows');
        require_code('workflows2');

        set_helper_panel_tutorial('sup_set_up_a_workflow_in_composr');

        if ($type == '_add') {
            $doing = 'ADD_' . $this->lang_type;

            $this->title = get_screen_title($doing);

            $this->doing = $doing;
        }

        if ($type == '__edit') {
            $delete = post_param_integer('delete', 0);
            if ($delete == 1) {
                $doing = 'DELETE_' . $this->lang_type;

                $this->title = get_screen_title($doing);
            } else {
                $doing = 'EDIT_' . $this->lang_type;

                $this->title = get_screen_title($doing);
            }

            $this->doing = $doing;
        }

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
        if ($type == 'browse') {
            return $this->browse();
        }
        return new Tempcode();
    }

    /**
     * The do-next manager for before content management.
     *
     * @return Tempcode The UI
     */
    public function browse() : object
    {
        require_code('templates_donext');
        return do_next_manager(
            get_screen_title('MANAGE_WORKFLOWS'),
            comcode_to_tempcode(do_lang('DOC_WORKFLOWS'), null, true),
            [
                ['admin/add', ['_SELF', ['type' => 'add'], '_SELF'], do_lang('ADD_WORKFLOW')],
                ['admin/edit', ['_SELF', ['type' => 'edit'], '_SELF'], do_lang('EDIT_WORKFLOW')],
            ],
            do_lang('MANAGE_WORKFLOWS')
        );
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function fill_in_edit_form(string $id)
    {
        return $this->get_form_fields(intval($id));
    }

    /**
     * Get a list of point names specified.
     *
     * @return array List of point names
     */
    public function get_points_in_edited_workflow() : array
    {
        // Grab all of the requested points
        $point_names = array_map('trim', explode("\n", post_param_string('points')));
        $temp_point_names = [];
        foreach ($point_names as $p) {
            if ($p != '') {
                $temp_point_names[] = $p;
            }
        }
        return $temp_point_names;
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
     * Get Tempcode for a adding/editing form.
     *
     * @param  ?integer $id The workflow being edited (null: adding, not editing)
     * @return array A pair: The input fields, Hidden fields
     */
    public function get_form_fields(?int $id = null) : array
    {
        // These will hold our form elements, visible & hidden
        $fields = new Tempcode();
        $hidden = new Tempcode();

        // Grab all of the data we can about this workflow
        // Make some assumptions first
        $default = false;
        $approval_points = [];
        $workflow_name = '';

        // Now overwrite those assumptions if they're wrong
        if ($id !== null) {
            $workflows = $GLOBALS['SITE_DB']->query_select('workflows', ['*'], ['id' => $id], '', 1);
            if (!array_key_exists(0, $workflows)) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }
            $workflow = $workflows[0];

            // We can grab the name straight away
            $workflow_name = get_translated_text($workflow['workflow_name']);

            // Now see if we're the default
            $default_workflow = get_default_workflow();
            if (($default_workflow !== null) && ($id == $default_workflow)) {
                $default = true;
            }
            // Get the approval points in workflow order
            $approval_points = get_all_approval_points($id);
        } else {
            $id = null;
        }

        ////////////////////
        // Build the form //
        ////////////////////

        // First we must be given a name (defaulting to the given name if it has been passed). We want to show the user which names are unavailable, so search the database for this information.
        $workflows = get_all_workflows();
        if (!empty($workflows)) {
            $defined_names = do_lang_tempcode('DEFINED_WORKFLOWS', escape_html(implode(', ', $workflows)));
        } else {
            $defined_names = do_lang_tempcode('NO_DEFINED_WORKFLOWS');
        }

        $fields->attach(form_input_line(do_lang_tempcode('NAME'), do_lang_tempcode('WORKFLOW_NAME_DESCRIPTION', $defined_names), 'workflow_name', $workflow_name, true));

        $all_points = ($id === null) ? [] : get_all_approval_points($id); // We need to display which points are available
        if (empty($all_points)) {
            $points_list = do_lang('APPROVAL_POINTS_DESCRIPTION_EMPTY_LIST');
        } else {
            $points_list = do_lang('APPROVAL_POINTS_DESCRIPTION_LIST', escape_html(implode(', ', $all_points)));
        }

        // Now add the approval point lines
        $fields->attach(form_input_text(do_lang('WORKFLOW_APPROVAL_POINTS'), do_lang('APPROVAL_POINTS_DESCRIPTION', escape_html($points_list)), 'points', implode("\n", $approval_points), true, false, null, true));

        // Add an option to make this default
        $fields->attach(form_input_tick(do_lang('DEFAULT_WORKFLOW'), do_lang('DEFAULT_WORKFLOW_DESCRIPTION'), 'is_default', $default));

        // Add an option to redefine the approval permissions
        $fields2 = new Tempcode();
        if ($id !== null) {
            // Actions
            $fields2->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => 'e578cf36d2552947dfe406f38a2e2877', 'TITLE' => do_lang_tempcode('ACTIONS')]));

            $fields2->attach(form_input_tick(do_lang('REDEFINE_WORKFLOW_POINTS'), do_lang('REDEFINE_WORKFLOW_POINTS_DESC'), 'redefine_points', false));
        }

        return [$fields, $hidden, new Tempcode(), '', false, '', $fields2];
    }

    /**
     * Tells us if more information is needed from the user. This is required since the user may create a workflow out of predefined components, which
     * requires no further information, or they may want to define new approval points, which requires more information.
     *
     * @return boolean Whether more information is needed from the user
     */
    public function need_second_screen() : bool
    {
        if (post_param_integer('redefined', 0) == 1) {
            return false;
        }

        // We need to show the second screen if it has been specifically requested via the edit form
        if (post_param_integer('redefine_points', 0) == 1) {
            return true;
        }

        // Otherwise the only reason we might need more information is if there are approval points specified that haven't been defined.

        $point_names = $this->get_points_in_edited_workflow();

        // Find any points which are already defined
        $workflow_id = get_param_integer('id', null);
        $all_points = ($workflow_id === null) ? [] : get_all_approval_points($workflow_id);

        // See if we need to define any
        foreach ($point_names as $p) {
            if (!in_array($p, $all_points)) { // This point has not been defined previously...
                return true;
            }
        }

        // If we've reached here then there's nothing to do
        return false;
    }

    /**
     * Renders a screen for setting permissions on approval points.
     *
     * @return Tempcode The UI
     */
    public function second_screen() : object
    {
        $point_names = $this->get_points_in_edited_workflow();

        // Find any points which are already defined
        $workflow_id = get_param_integer('id', null);
        $all_points = ($workflow_id === null) ? [] : get_all_approval_points($workflow_id);

        // This will hold new points
        $clarify_points = [];

        // This will hold existing points we're redefining
        $redefine_points = [];

        // See if we need to define any
        foreach ($point_names as $seq_id => $p) {
            if (!in_array($p, $all_points)) {
                // Found an undefined point. We need more information.
                $clarify_points[$seq_id] = $p;
            } else {
                $redefine_points[$seq_id] = $p;
            }
        }

        // These will hold our form fields
        $fields = new Tempcode();
        $hidden = new Tempcode();

        // Pass through the previous screen's data
        foreach (['points', 'is_default', 'workflow_name'] as $n) {
            $hidden->attach(form_input_hidden($n, post_param_string($n, '')));
        }
        $hidden->attach(form_input_hidden('redefined', '1'));
        if ($workflow_id !== null) {
            $hidden->attach(form_input_hidden('id', strval($workflow_id)));
        }

        // We need a list of groups so that the user can choose those to give permission to
        $usergroups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(true, true, false, [], null);

        // Add the form elements for each section
        if (!empty($clarify_points)) {
            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', [
                '_GUID' => '956a8b51ebbbd5e581092520534bd332',
                'TITLE' => do_lang_tempcode('DEFINE_WORKFLOW_POINTS'),
                'HELP' => do_lang_tempcode('DEFINE_WORKFLOW_POINTS_HELP', escape_html(implode(', ', $clarify_points))),
            ]));
            foreach ($clarify_points as $seq_id => $p) {
                // Now add a list of the groups to allow
                $content = [];
                foreach ($usergroups as $group_id => $group_name) {
                    $content[] = [$group_name, 'groups_' . strval($seq_id) . '[' . strval($group_id) . ']', false, ''];
                }
                $fields->attach(form_input_various_ticks(
                    $content,
                    do_lang_tempcode('WORKFLOW_POINT_GROUPS_DESC', escape_html($p)),
                    null,
                    do_lang_tempcode('WORKFLOW_POINT_GROUPS', escape_html($p)),
                    true
                ));
            }
        }

        if (!empty($redefine_points)) {
            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', [
                '_GUID' => '1670e74ade97bd18b8dc798033a14f36',
                'TITLE' => do_lang_tempcode('REDEFINE_WORKFLOW_POINTS'),
                'HELP' => do_lang_tempcode('REDEFINE_WORKFLOW_POINTS_HELP'),
            ]));

            // These points already exist, so look them up
            $all_points = ($workflow_id === null) ? [] : array_flip(get_all_approval_points($workflow_id));

            foreach ($redefine_points as $seq_id => $p) {
                // Now add a list of the groups to allow, defaulting to those which already have permission
                $groups = get_usergroups_for_approval_point($all_points[$p]);

                $content = [];
                foreach ($usergroups as $group_id => $group_name) {
                    $content[] = [$group_name, 'groups_' . strval($seq_id) . '[' . strval($group_id) . ']', in_array($group_id, $groups), ''];
                }
                $fields->attach(form_input_various_ticks(
                    $content,
                    do_lang_tempcode('WORKFLOW_POINT_GROUPS_DESC', escape_html($p)),
                    null,
                    do_lang_tempcode('WORKFLOW_POINT_GROUPS', escape_html($p)),
                    true
                ));
            }
        }

        $self_url = get_self_url();

        $title = get_screen_title('DEFINE_WORKFLOW_POINTS');

        return do_template('FORM_SCREEN', [
            '_GUID' => '31a56dccccdf5d7691439f79d120ffcb',
            'TITLE' => $title,
            'FIELDS' => $fields,
            'TEXT' => '',
            'HIDDEN' => $hidden,
            'URL' => $self_url,
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
        ]);
    }

    /**
     * Standard crud_module list function.
     *
     * @return Tempcode The selection list
     */
    public function create_selection_list_entries() : object
    {
        $fields = new Tempcode();
        $rows = get_all_workflows();
        foreach ($rows as $id => $name) {
            $fields->attach(form_input_list_entry($id, false, $name));
        }

        return $fields;
    }

    /**
     * Standard crud_module delete possibility checker.
     *
     * @param  ID_TEXT $id The entry being potentially deleted
     * @return boolean Whether it may be deleted
     */
    public function may_delete_this(string $id) : bool
    {
        // Workflows are optional, so we can always delete them
        return true;
    }

    /**
     * Read in data posted by an add/edit form.
     *
     * @param  boolean $insert_if_needed Whether to insert unknown workflows into the database. For adding this should be true, otherwise false (the default)
     * @return array (workflow_id, workflow_name, [approval point IDs=>names], default)
     */
    public function read_in_data(bool $insert_if_needed = false) : array
    {
        $name = post_param_string('workflow_name');
        $is_default = (post_param_integer('is_default', 0) == 1);

        $workflow_id = either_param_integer('id', null);
        $map = ['is_default' => $is_default ? 1 : 0];
        if ($workflow_id === null) {
            if ($insert_if_needed) { // Adding
                $map += insert_lang('workflow_name', $name, 3);
                $workflow_id = $GLOBALS['SITE_DB']->query_insert('workflows', $map, true);
            } else {
                warn_exit(do_lang_tempcode('INTERNAL_ERROR', escape_html('8136afcdad9455dc9bec1505e4062106')));
            }
        } else {
            $old_name = $GLOBALS['SITE_DB']->query_select_value('workflows', 'workflow_name', ['id' => $workflow_id]);
            $map += lang_remap('workflow_name', $old_name, $name);
            $GLOBALS['SITE_DB']->query_update('workflows', $map, ['id' => $workflow_id]);
        }

        $point_names = $this->get_points_in_edited_workflow();

        // Find any points and new settings for them
        $all_points = get_all_approval_points($workflow_id);
        $point_ids = [];
        foreach ($point_names as $seq_id => $point_name) {
            $point_id = array_search($point_name, $all_points);
            if ($point_id !== false) {
                // This already exists. Use the existing version.
                $point_ids[$point_id] = $point_name;

                // Clear previous permissions for this approval point
                $GLOBALS['SITE_DB']->query_delete('workflow_permissions', ['workflow_approval_point_id' => $point_id]);
            } else {
                // This doesn't exist yet. Create it.

                $point_id = add_approval_point_to_workflow(insert_lang('workflow_approval_name', $point_name, 3), $workflow_id);
                $point_ids[$point_id] = $point_name;
            }

            // Insert the new permissions for this approval point
            if (array_key_exists('groups_' . strval($seq_id), $_POST)) {
                foreach (array_keys($_POST['groups_' . strval($seq_id)]) as $group_id) {
                    $GLOBALS['SITE_DB']->query_insert('workflow_permissions', [
                        'usergroup' => intval($group_id),
                        'workflow_approval_point_id' => $point_id,
                    ]);
                }
            }
        }

        // Now we remove those points which are not wanted. We have to do this after the insertions, since we need to keep at least one approval point for the workflow in order for it to exist.
        $sql = 'DELETE FROM ' . get_table_prefix() . 'workflow_approval_points WHERE workflow_id=' . strval($workflow_id) . ' AND id NOT IN (' . implode(',', array_map('strval', array_keys($point_ids))) . ')';
        $GLOBALS['SITE_DB']->query($sql);

        return [$workflow_id, $name, $point_ids, $is_default];
    }

    /**
     * Standard CRUD-module UI/actualiser to add an entry.
     *
     * @return Tempcode The UI
     */
    public function _add() : object
    {
        // We override the add screen here so that we can provide multiple screens

        $test = $this->handle_confirmations($this->title);
        if ($test !== null) {
            return $test;
        }

        // Interrupt standard CRUD stuff, so we can choose our screen
        if ($this->need_second_screen()) {
            // We need more info from the user. Ask for it here.
            return $this->second_screen();
        }

        // If we reach here, the form is complete so we resume the CRUD process

        list($id) = $this->add_actualisation();

        $description = do_lang_tempcode('SUCCESS');

        $url = get_param_string('redirect', '', INPUT_FILTER_URL_INTERNAL);
        if ($url != '') {
            return redirect_screen($this->title, $url, $description);
        }

        return $this->do_next_manager($this->title, $description, $id);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        // Grab our data. We pass true so that it will create non-existent content for us (workflow and approval points)
        list($id, $workflow_name, $approval_points, $is_default) = $this->read_in_data(true);

        log_it('ADD_WORKFLOW', strval($id), $workflow_name);

        return [strval($id), null];
    }

    /**
     * Standard CRUD-module UI/actualiser to edit an entry.
     *
     * @return Tempcode The UI
     */
    public function __edit() : object
    {
        // We override the standard CRUD edit actualiser in order to redirect to a
        // second edit screen if certain conditions are met. Other than this, the
        // rest of this method's code is copypasta'd from the standard CRUD module

        // CRUD stuff to begin with

        $id = strval(get_param_integer('id'));

        $delete = post_param_integer('delete', 0);
        if ($delete == 1) {
            $test = $this->handle_confirmations($this->title);
            if ($test !== null) {
                return $test;
            }

            $this->delete_actualisation($id);

            $description = do_lang_tempcode('SUCCESS');

            return $this->do_next_manager($this->title, $description);
        } else {
            $test = $this->handle_confirmations($this->title);
            if ($test !== null) {
                return $test;
            }

            // Here we interrupt the regular CRUD code see if we should redirect to
            // a second data entry screen
            if ($this->need_second_screen()) {
                return $this->second_screen();
            }

            $this->edit_actualisation($id);

            $description = do_lang_tempcode('SUCCESS');
        }

        return $this->do_next_manager($this->title, $description, $id);
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return ?Tempcode Description about usage (null: none)
     */
    public function edit_actualisation(string $id) : ?object
    {
        list($workflow_id, $workflow_name, $approval_points, $is_default) = $this->read_in_data(false);

        log_it('EDIT_WORKFLOW', strval($workflow_id), $workflow_name);

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $id The entry being deleted
     */
    public function delete_actualisation(string $id)
    {
        $workflow_name = $GLOBALS['SITE_DB']->query_select_value_if_there('workflows', 'workflow_name', ['id' => $id]);
        if ($workflow_name === null) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        delete_workflow(intval($id));

        log_it('DELETE_WORKFLOW', $id, $workflow_name);
    }
}

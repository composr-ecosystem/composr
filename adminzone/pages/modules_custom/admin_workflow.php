<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    workflows
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_admin_workflow extends Standard_crud_module
{
    public $lang_type = 'WORKFLOW';
    public $select_name = 'NAME';
    public $menu_label = 'WORKFLOW';
    public $appended_actions_already = true;
    public $do_preview = null;

    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Warburton';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = 'Patrick Schmalstig';
        $info['hack_version'] = 1;
        $info['version'] = 1;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        // Remove database tables
        $GLOBALS['SITE_DB']->drop_table_if_exists('workflows');
        $GLOBALS['SITE_DB']->drop_table_if_exists('workflow_permissions');
        $GLOBALS['SITE_DB']->drop_table_if_exists('workflow_approval_points');
        $GLOBALS['SITE_DB']->drop_table_if_exists('workflow_content');
        $GLOBALS['SITE_DB']->drop_table_if_exists('workflow_content_status');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        // Create required database structures

        require_lang('workflows');

        $GLOBALS['SITE_DB']->create_table('workflows', array(
            'id' => '*AUTO', // ID
            'workflow_name' => 'SHORT_TRANS', // The name (and ID) of this approval point
            'is_default' => 'BINARY',
        ));

        // The workflow_approval_points table records which workflows require which points to approve
        $GLOBALS['SITE_DB']->create_table('workflow_approval_points', array(
            'id' => '*AUTO', // ID for reference
            'workflow_id' => 'AUTO_LINK', // The name (and ID) of this workflow
            'workflow_approval_name' => 'SHORT_TRANS', // The name (and ID) of the approval point to require in this workflow
            'the_position' => 'INTEGER', // The position of this approval point in the workflow (ie. any approval can be given at any time, but encourage users into a prespecified order)
        ));

        // The workflow_permissions table stores which usergroups are allowed to approve which points
        $GLOBALS['SITE_DB']->create_table('workflow_permissions', array(
            'id' => '*AUTO', // ID for reference
            'workflow_approval_point_id' => 'AUTO_LINK', // The ID of the approval point
            'usergroup' => 'GROUP', // The usergroup to give permission to
        ));

        // The workflow_content table records which site resources are in which workflows, along with any notes made during the approval process
        $GLOBALS['SITE_DB']->create_table('workflow_content', array(
            'id' => '*AUTO', // ID for reference
            'content_type' => 'ID_TEXT', // The content-meta-aware type we'd find this content in
            'content_id' => 'ID_TEXT', // The ID of the content, wherever it happens to be
            'workflow_id' => 'AUTO_LINK', // The ID of the workflow this content is in
            'notes' => 'LONG_TEXT', // No point translating the notes, since they're transient
            'original_submitter' => 'MEMBER', // Save this here since there's no standard way to discover it later (eg. through content-meta-aware hooks)
        ));

        // The workflow_content_status table records the status of each approval point for a piece of content and the member who approved the point (if any)
        $GLOBALS['SITE_DB']->create_table('workflow_content_status', array(
            'id' => '*AUTO', // ID for reference. Larger IDs will override smaller ones if they report a different status (nondeterministic for non-incremental IDs!)
            'workflow_content_id' => 'AUTO_LINK', // The ID of this content in the workflow_content table
            'workflow_approval_point_id' => 'AUTO_LINK', // The ID of the approval point
            'status_code' => 'SHORT_INTEGER', // A code indicating the status
            'approved_by' => 'MEMBER', // Remember who set this status, if the need arises to investigate this later
        ));
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
            'browse' => array('MANAGE_WORKFLOWS', 'menu/workflows'),
        ) + parent::get_entry_points();
    }

    public $title;
    public $doing;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @param  boolean $top_level Whether this is running at the top level, prior to having sub-objects called.
     * @param  ?ID_TEXT $type The screen type to consider for metadata purposes (null: read from environment).
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run($top_level = true, $type = null)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $type = get_param_string('type', 'browse');

        require_lang('workflows');

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
    public function run_start($type)
    {
        require_lang('workflows');
        require_code('workflows');
        require_code('workflows2');

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
    public function browse()
    {
        require_code('templates_donext');
        return do_next_manager(get_screen_title('MANAGE_WORKFLOWS'), comcode_to_tempcode(do_lang('DOC_WORKFLOWS'), null, true),
            array(
                /*  type                     page    params                                       zone   */
                array('menu/_generic_admin/add_one', array('_SELF', array('type' => 'add'), '_SELF'), do_lang('ADD_WORKFLOW')),
                array('menu/_generic_admin/edit_one', array('_SELF', array('type' => 'edit'), '_SELF'), do_lang('EDIT_WORKFLOW')),
            ),
            do_lang('MANAGE_WORKFLOWS')
        );
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return array A triple: fields, hidden-fields, delete-fields
     */
    public function fill_in_edit_form($id)
    {
        return $this->get_form_fields(intval($id));
    }

    /**
     * Get a list of point names specified.
     *
     * @return array List of point names
     */
    public function get_points_in_edited_workflow()
    {
        // Grab all of the requested points
        $point_names = array_map('trim', explode("\n", post_param_string('points')));
        $temp_point_names = array();
        foreach ($point_names as $p) {
            if ($p != '') {
                $temp_point_names[] = $p;
            }
        }
        return $temp_point_names;
    }

    /**
     * Get Tempcode for a adding/editing form.
     *
     * @param  ?integer $id The workflow being edited (null: adding, not editing)
     * @return array A pair: The input fields, Hidden fields
     */
    public function get_form_fields($id = null)
    {
        require_code('form_templates');

        // These will hold our form elements, visible & hidden
        $fields = new Tempcode();
        $hidden = new Tempcode();

        // Grab all of the data we can about this workflow
        // Make some assumptions first
        $default = false;
        $approval_points = array();
        $workflow_name = '';

        // Now overwrite those assumptions if they're wrong
        if (!is_null($id)) {
            $workflows = $GLOBALS['SITE_DB']->query_select('workflows', array('*'), array('id' => $id), '', 1);
            if (!array_key_exists(0, $workflows)) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }
            $workflow = $workflows[0];

            // We can grab the name straight away
            $workflow_name = get_translated_text($workflow['workflow_name']);

            // Now see if we're the default
            $default_workflow = get_default_workflow();
            if (!is_null($default_workflow) && $id == $default_workflow) {
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
        if (count($workflows) > 0) {
            $defined_names = do_lang_tempcode('DEFINED_WORKFLOWS', escape_html(implode(', ', $workflows)));
        } else {
            $defined_names = do_lang_tempcode('NO_DEFINED_WORKFLOWS');
        }

        $fields->attach(form_input_line(do_lang_tempcode('NAME'), do_lang_tempcode('WORKFLOW_NAME_DESCRIPTION', $defined_names), 'name', $workflow_name, true));

        $all_points = is_null($id) ? array() : get_all_approval_points($id); // We need to display which points are available
        if ($all_points == array()) {
            $points_list = do_lang_tempcode('APPROVAL_POINTS_DESCRIPTION_EMPTY_LIST');
        } else {
            $points_list = do_lang_tempcode('APPROVAL_POINTS_DESCRIPTION_LIST', escape_html(implode(', ', $all_points)));
        }

        // Now add the approval point lines
        $fields->attach(form_input_text(do_lang_tempcode('WORKFLOW_APPROVAL_POINTS'), do_lang_tempcode('APPROVAL_POINTS_DESCRIPTION', $points_list), 'points', implode("\n", $approval_points), true, null, true));

        // Add an option to make this default
        $fields->attach(form_input_tick(do_lang('DEFAULT_WORKFLOW'), do_lang('DEFAULT_WORKFLOW_DESCRIPTION'), 'is_default', $default));

        // Add an option to redefine the approval permissions
        $fields2 = new Tempcode();
        if ($id !== null) {
            // Actions
            $fields2->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => 'e578cf36d2552947dfe406f38a2e2877', 'TITLE' => do_lang_tempcode('ACTIONS'))));

            $fields2->attach(form_input_tick(do_lang('REDEFINE_WORKFLOW_POINTS'), do_lang('REDEFINE_WORKFLOW_POINTS_DESC'), 'redefine_points', false));
        }

        return array($fields, $hidden, new Tempcode(), '', false, '', $fields2);
    }

    /**
     * Tells us if more information is needed from the user. This is required since the user may create a workflow out of predefined components, which
     * requires no further information, or they may want to define new approval points, which requires more information.
     *
     * @return boolean Whether more information is needed from the user.
     */
    public function need_second_screen()
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
        $all_points = ($workflow_id === null) ? array() : get_all_approval_points($workflow_id);

        // See if we need to define any
        foreach ($point_names as $p) {
            if (!in_array($p, $all_points)) {// This point has not been defined previously...
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
    public function second_screen()
    {
        require_code('form_templates');

        $point_names = $this->get_points_in_edited_workflow();

        // Find any points which are already defined
        $workflow_id = get_param_integer('id', null);
        $all_points = ($workflow_id === null) ? array() : get_all_approval_points($workflow_id);

        // This will hold new points
        $clarify_points = array();

        // This will hold existing points we're redefining
        $redefine_points = array();

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
        foreach (array('points', 'is_default', 'name') as $n) {
            $hidden->attach(form_input_hidden($n, post_param_string($n, '')));
        }
        $hidden->attach(form_input_hidden('redefined', '1'));
        if (!is_null($workflow_id)) {
            $hidden->attach(form_input_hidden('id', strval($workflow_id)));
        }

        // We need a list of groups so that the user can choose those to give permission to
        $usergroups = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(true, true, false, null, null);

        // Add the form elements for each section
        if (count($clarify_points) > 0) {
            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array(
                '_GUID' => '956a8b51ebbbd5e581092520534bd332',
                'TITLE' => do_lang_tempcode('DEFINE_WORKFLOW_POINTS'),
                'HELP' => do_lang_tempcode('DEFINE_WORKFLOW_POINTS_HELP', escape_html(implode(', ', $clarify_points))),
            )));
            foreach ($clarify_points as $seq_id => $p) {
                // Now add a list of the groups to allow
                $content = array();
                foreach ($usergroups as $group_id => $group_name) {
                    $content[] = array($group_name, 'groups_' . strval($seq_id) . '[' . strval($group_id) . ']', false, '');
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

        if (count($redefine_points) > 0) {
            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array(
                '_GUID' => '1670e74ade97bd18b8dc798033a14f36',
                'TITLE' => do_lang_tempcode('REDEFINE_WORKFLOW_POINTS'),
                'HELP' => do_lang_tempcode('REDEFINE_WORKFLOW_POINTS_HELP'),
            )));

            // These points already exist, so look them up
            $all_points = ($workflow_id === null) ? array() : array_flip(get_all_approval_points($workflow_id));

            foreach ($redefine_points as $seq_id => $p) {
                // Now add a list of the groups to allow, defaulting to those which already have permission
                $groups = get_usergroups_for_approval_point($all_points[$p], false);

                $content = array();
                foreach ($usergroups as $group_id => $group_name) {
                    $content[] = array($group_name, 'groups_' . strval($seq_id) . '[' . strval($group_id) . ']', in_array($group_id, $groups), '');
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

        return do_template('FORM_SCREEN', array(
            '_GUID' => '31a56dccccdf5d7691439f79d120ffcb',
            'TITLE' => $title,
            'FIELDS' => $fields,
            'TEXT' => '',
            'HIDDEN' => $hidden,
            'URL' => $self_url,
            'SUBMIT_ICON' => 'buttons__proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
        ));
    }

    /**
     * Standard crud_module list function.
     *
     * @return Tempcode The selection list
     */
    public function create_selection_list_entries()
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
    public function may_delete_this($id)
    {
        // Workflows are optional, so we can always delete them
        return true;
    }

    /**
     * Read in data posted by an add/edit form
     *
     * @param  boolean $insert_if_needed Whether to insert unknown workflows into the database. For adding this should be true, otherwise false (the default)
     * @return array (workflow_id, workflow_name, array(approval point IDs=>names), default)
     */
    public function read_in_data($insert_if_needed = false)
    {
        $name = post_param_string('name');
        $is_default = (post_param_integer('is_default', 0) == 1);

        $workflow_id = either_param_integer('id', null);
        $map = array('is_default' => $is_default ? 1 : 0);
        if ($workflow_id === null) {
            if ($insert_if_needed) { // Adding
                $map += insert_lang('workflow_name', $name, 3);
                $workflow_id = $GLOBALS['SITE_DB']->query_insert('workflows', $map, true);
            } else {
                warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
            }
        } else {
            $old_name = $GLOBALS['SITE_DB']->query_select_value('workflows', 'workflow_name', array('id' => $workflow_id));
            $map += lang_remap('workflow_name', $old_name, $name);
            $GLOBALS['SITE_DB']->query_update('workflows', $map, array('id' => $workflow_id));
        }

        $point_names = $this->get_points_in_edited_workflow();

        // Find any points and new settings for them
        $all_points = get_all_approval_points($workflow_id);
        $point_ids = array();
        foreach ($point_names as $seq_id => $point_name) {
            $point_id = array_search($point_name, $all_points);
            if ($point_id !== false) {
                // This already exists. Use the existing version.
                $point_ids[$point_id] = $point_name;

                // Clear previous permissions for this approval point
                $GLOBALS['SITE_DB']->query_delete('workflow_permissions', array('workflow_approval_point_id' => $point_id));
            } else {
                // This doesn't exist yet. Create it.

                $point_id = add_approval_point_to_workflow(insert_lang('workflow_approval_name', $point_name, 3), $workflow_id);
                $point_ids[$point_id] = $point_name;
            }

            // Insert the new permissions for this approval point
            if (array_key_exists('groups_' . strval($seq_id), $_POST)) {
                foreach (array_keys($_POST['groups_' . strval($seq_id)]) as $group_id) {
                    $GLOBALS['SITE_DB']->query_insert('workflow_permissions', array(
                        'usergroup' => intval($group_id),
                        'workflow_approval_point_id' => $point_id,
                    ));
                }
            }
        }

        // Now we remove those points which are not wanted. We have to do this after the insertions, since we need to keep at least one approval point for the workflow in order for it to exist.
        $sql = 'DELETE FROM ' . get_table_prefix() . 'workflow_approval_points WHERE workflow_id=' . strval($workflow_id) . ' AND id NOT IN (' . implode(',', array_map('strval', array_keys($point_ids))) . ')';
        $GLOBALS['SITE_DB']->query($sql);

        return array($workflow_id, $name, $point_ids, $is_default);
    }

    /**
     * Standard CRUD-module UI/actualiser to add an entry.
     *
     * @return Tempcode The UI
     */
    public function _add()
    {
        // We override the add screen here so that we can provide multiple screens

        $test = $this->handle_confirmations($this->title);
        if (!is_null($test)) {
            return $test;
        }

        // Interrupt standard CRUD stuff, so we can choose our screen
        if ($this->need_second_screen()) {
            // We need more info from the user. Ask for it here.
            return $this->second_screen();
        }

        // If we reach here, the form is complete so we resume the CRUD process

        $id = $this->add_actualisation();

        $description = do_lang_tempcode('SUCCESS');

        $url = get_param_string('redirect', null);
        if (!is_null($url)) {
            return redirect_screen($this->title, $url, $description);
        }

        return $this->do_next_manager($this->title, $description, $id);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return ID_TEXT The entry added
     */
    public function add_actualisation()
    {
        // Grab our data. We pass true so that it will create non-existent content for us (workflow and approval points)
        list($workflow_id, $workflow_name, $approval_points, $is_default) = $this->read_in_data(true);

        return strval($workflow_id);
    }

    /**
     * Standard CRUD-module UI/actualiser to edit an entry.
     *
     * @return Tempcode The UI
     */
    public function __edit()
    {
        // We override the standard CRUD edit actualiser in order to redirect to a
        // second edit screen if certain conditions are met. Other than this, the
        // rest of this method's code is copypasta'd from the standard CRUD module

        // CRUD stuff to begin with

        $id = strval(get_param_integer('id'));

        $delete = post_param_integer('delete', 0);
        if ($delete == 1) {
            $test = $this->handle_confirmations($this->title);
            if (!is_null($test)) {
                return $test;
            }

            $this->delete_actualisation($id);

            $description = do_lang_tempcode('SUCCESS');

            return $this->do_next_manager($this->title, $description, null);
        } else {
            $test = $this->handle_confirmations($this->title);
            if (!is_null($test)) {
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
     * @return ?Tempcode Confirm message (null: continue)
     */
    public function edit_actualisation($id)
    {
        list($workflow_id, $workflow_name, $approval_points, $is_default) = $this->read_in_data(false);
        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $id The entry being deleted
     */
    public function delete_actualisation($id)
    {
        delete_workflow(intval($id));
    }
}

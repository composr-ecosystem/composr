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

/**
 * Returns whether the given user (default: current member) can choose the
 * workflow to apply to some content they're submitting/editing.
 *
 * @param  ?MEMBER $member Member (null: current member)
 * @return boolean Whether the user has permission or not
 */
function can_choose_workflow($member = null)
{
    // Sort out the user
    if (is_null($member)) {
        $member = get_member();
    }

    // We currently use access to the workflow management page as the defining
    // criterion
    return has_actual_page_access($member, 'admin_workflow', get_module_zone('admin_workflow'));
}

/**
 * Returns a form field to choose the desired workflow (if there is more than
 * one in the system).
 *
 * @param  boolean $include_inherit Whether to include an option for inheriting
 * @param  boolean $include_current Whether to include an option for leaving it alone
 * @return Tempcode The UI for choosing a workflow (if needed)
 */
function workflow_choose_ui($include_inherit = false, $include_current = false)
{
    // Grab the necessary code
    require_code('workflows');
    require_lang('workflows');

    // Find out which workflows are available
    $all_workflows = get_all_workflows();

    // Only give an option to select a workflow if there is more
    // than one available
    if (count($all_workflows) > 1) {
        // Grab the default workflow
        $def = get_default_workflow();
        $workflows = new Tempcode();

        // If we've been asked to show a "current" option then add that
        if ($include_current) {
            $workflows->attach(form_input_list_entry('wf_-2', true, do_lang('KEEP_WORKFLOW'), false, false));
        }

        // If we've been asked to show an "inherit" option then add that
        if ($include_inherit) {
            $workflows->attach(form_input_list_entry('wf_-1', !$include_current, do_lang('INHERIT_WORKFLOW'), false, false));
        }

        // Get all of the workflows we have
        foreach ($all_workflows as $id => $workflow) {
            $workflows->attach(form_input_list_entry('wf_' . strval($id), (!$include_inherit && !$include_current && $id == $def), $workflow, false, false));
        }

        // Return a list entry to choose from
        $help = $include_inherit ? do_lang('INHERIT_WORKFLOW_HELP') : '';
        $help .= $include_current ? do_lang('CURRENT_WORKFLOW_HELP') : '';
        return form_input_list(do_lang_tempcode('USE_WORKFLOW'), do_lang_tempcode('USE_WORKFLOW_DESCRIPTION', $help), 'workflow', $workflows, null, false);
    } elseif (count($all_workflows) == 1) {
        return form_input_hidden('workflow', 'wf_' . strval(current(array_keys($all_workflows))));
    } else {
        return new Tempcode();
    }
}

/**
 * Returns all of the workflows which are currently defined.
 *
 * @return array The workflows which are defined. Empty if none are defined.
 */
function get_all_workflows()
{
    $workflows = $GLOBALS['SITE_DB']->query_select('workflows', array('id', 'workflow_name'));
    $output = array();
    foreach ($workflows as $w) {
        $output[$w['id']] = get_translated_text($w['workflow_name']);
    }
    return $output;
}

/**
 * Get the system's default workflow. If there is only one workflow this
 * will return it, otherwise (multiple with no default specified, or no
 * workflows at all) it will give null.
 *
 * @return ?AUTO_LINK The ID of the default workflow. (null: if none set)
 */
function get_default_workflow()
{
    // Grab every workflow ID
    $workflows = get_all_workflows();
    // Only bother doing anything more complicated if we've got multiple workflows to dig through
    if (count($workflows) > 1) {
        // Look for those which are set as default
        $defaults = $GLOBALS['SITE_DB']->query_select('workflows', array('id'), array('is_default' => 1));
        // If there aren't any then we can't presume to know which should
        // be returned, so return an empty array
        if ($defaults == array()) {
            return null;
        }
        // Likewise we cannot choose between multiple defaults, so return an empty array
        elseif (count($defaults) > 1) {
            return null;
        }
        // If we're here then we have one default, so return it
        return $defaults[0]['id'];
    } // Otherwise just give back what we've found (singleton or empty)
    elseif (count($workflows) == 1) {
        $keys = array_keys($workflows);
        return $keys[0];
    } else {
        return null;
    }
}

/**
 * Get a workflow name.
 *
 * @param  AUTO_LINK $workflow_id The workflow ID
 * @return string The workflow name
 */
function get_workflow_name($workflow_id)
{
    return get_translated_text($GLOBALS['SITE_DB']->query_select_value('workflows', 'workflow_name', array('id' => $workflow_id)));
}

/**
 * Find out who submitted a piece of content from a workflow.
 *
 * @param  AUTO_LINK $content_id The workflow content ID
 * @return ?MEMBER The submitter (null: if unknown)
 */
function get_submitter_of_workflow_content($content_id)
{
    // Exit on misuse
    if (is_null($content_id)) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    // Find out the author straight from the workflow_content table
    $submitter = $GLOBALS['SITE_DB']->query_select('workflow_content', array('original_submitter'), array('id' => $content_id));
    if ($submitter == array()) {
        // Exit if we can't find the given resource
        warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html('workflow_content->' . strval($content_id))));
    }
    // Now extract the submitter (if there is one)
    return $submitter[0]['original_submitter'];
}

/**
 * Get the Tempcode for viewing/editing the workflow status of the given content
 *
 * @param  AUTO_LINK $workflow_content_id The ID of this content in the workflow_content table
 * @return Tempcode The form for this content
 */
function get_workflow_form($workflow_content_id)
{
    // Load our prerequisites
    require_lang('workflows');
    require_code('form_templates');
    require_code('users');

    // Do not let guests edit the status of content
    if (is_guest()) {
        return new Tempcode();
    }

    //////////////////////////////////////////////////
    // Gather the details we need to build the form //
    //////////////////////////////////////////////////

    // These will hold the form code
    $workflow_fields = new Tempcode();
    $workflow_hidden = new Tempcode();

    // We already know the content ID
    $workflow_hidden->attach(form_input_hidden('content_id', strval($workflow_content_id)));

    // Check if this is a valid piece of content for a workflow
    $rows = $GLOBALS['SITE_DB']->query_select('workflow_content', array('*'), array('id' => $workflow_content_id), '', 1);
    if (count($rows) == 0) {
        warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html(strval($workflow_content_id)), do_lang_tempcode('WORKFLOW')));
    }

    $row = $rows[0];

    // If so then find the workflow for it
    $relevant_workflow = $row['workflow_id'];

    $workflow_hidden->attach(form_input_hidden('workflow_id', strval($relevant_workflow)));

    // Make sure there are some points to approve
    $approval_points = get_all_approval_points($relevant_workflow);
    if ($approval_points == array()) {
        warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html(strval($workflow_content_id)), do_lang_tempcode('WORKFLOW')));
    }

    /////////////////////////
    // Approval tick boxes //
    /////////////////////////

    $available_groups = $GLOBALS['FORUM_DRIVER']->get_members_groups(get_member()); // What groups our user is in
    $existing_status = array(); // This shows the current approval status, but is not editable
    $approval_status = array(); // This list of tuples holds tick-boxes for editing the approval status
    $send_next = array(); // This map of group names to tuples holds the details for who to send this to next
    $approve_count = 0; // This keeps each approval box distinct
    $send_count = 0; // This keeps the 'send next' boxes distinct
    $groups_shown = array(); // This keeps track of the groups we've already shown
    $group_names = $GLOBALS['FORUM_DRIVER']->get_usergroup_list(); // Allows us to find readable group names
    $permission_limit = false; // Points are added in order, so take notice when we no longer have permission (so we can send to whoever does)
    $next_point = null; // This will store the next workflow point for which we don't have the permission to approve

    // Get the status of each point
    $statuses = array();
    $_statuses = $GLOBALS['SITE_DB']->query_select('workflow_content_status', array('workflow_approval_point_id', 'status_code'), array('workflow_content_id' => $workflow_content_id));
    foreach ($_statuses as $status) {
        $statuses[$status['workflow_approval_point_id']] = $status['status_code'];
    }

    // Assume we can't do anything
    $have_permission_over_a_point = false;

    // Now loop through all approval points in workflow order
    foreach ($approval_points as $point => $approval_point_name) {
        // Only show one tick box for each approval point, if any
        $approval_shown = false;

        // Go through each group allowed to modify this value...
        foreach (get_usergroups_for_approval_point($point) as $allowed_group) {
            // ... and check whether the user is in it
            if (!$approval_shown && in_array($allowed_group, $available_groups)) {
                // If so then remember that we've handled this point
                $approval_shown = true;

                // Remember that we can tick something
                $have_permission_over_a_point = true;

                // Add the details to the editable tick-box values
                $approval_status[$approve_count] = array();
                $approval_status[$approve_count][] = $approval_point_name; // Pretty name
                $approval_status[$approve_count][] = 'approval_' . strval($point); // Name
                $approval_status[$approve_count][] = array_key_exists($point, $statuses) ? $statuses[$point] : 0; // The value (defaults to 0)
                $approval_status[$approve_count][] = do_lang_tempcode('APPROVAL_TICK_DESCRIPTION', escape_html($approval_point_name)); // Description
                $approval_status[$approve_count][] = false; // Not disabled, since we have permission

                // Add the details to the uneditable, existing status values
                $existing_status[$approve_count] = array();
                $existing_status[$approve_count][] = $approval_point_name; // Pretty name
                $existing_status[$approve_count][] = 'existing_' . strval($point); // Name
                if (array_key_exists($point, $statuses) && ($statuses[$point] == 1)) {
                    $existing_status[$approve_count][] = 1; // The value (1 due to our if condition)
                    $existing_status[$approve_count][] = do_lang_tempcode('ALREADY_APPROVED', escape_html($approval_point_name)); // Description
                } else {
                    $existing_status[$approve_count][] = 0; // The value defaults to 0
                    $existing_status[$approve_count][] = do_lang_tempcode('NOT_YET_APPROVED', escape_html($approval_point_name)); // Description
                }
                $existing_status[$approve_count][] = true; // Disabled, since this is for informative purposes only

                // Increment the unique ID for the array elements
                $approve_count++;
            }

            // We want the send boxes to name usergroups, rather than approval points, so we may as well add those here.
            // Take care not to add groups we've already got!
            if (!in_array($allowed_group, $groups_shown)) {
                $send_next[$allowed_group] = array();
                $send_next[$allowed_group][] = $group_names[$allowed_group]; // Pretty name
                $send_next[$allowed_group][] = 'send_' . strval($allowed_group); // Name
                // Set the default value. We want groups allowed to approve the next+1 point ticked (assuming we're approving the next one)
                // For simplicity, let's keep these unticked for now.
                //if (in_array($allowed_group['usergroup'], $groups_shown))
                //{
                $send_next[$allowed_group][] = false;
                //}
                $send_next[$allowed_group][] = do_lang_tempcode('NEXT_APPROVAL_DESCRIPTION', $group_names[$allowed_group]); // Description
                $groups_shown[] = $allowed_group;
            }
        }

        // If it's not handled at this point, we don't have permission
        if (!$approval_shown) {
            // Thus we should show a disabled check box
            $existing_status[$approve_count] = array();
            $existing_status[$approve_count][] = $approval_point_name; // Pretty name
            $existing_status[$approve_count][] = 'approval_' . strval($point); // Name
            $existing_status[$approve_count][] = array_key_exists($point, $statuses) ? $statuses[$point] : 0; // Value (defaults to 0)
            $existing_status[$approve_count][] = do_lang_tempcode('APPROVAL_TICK_DESCRIPTION', escape_html($approval_point_name)); // Description
            $existing_status[$approve_count][] = true; // Disabled, we have no permission
            // Increment the unique ID for the array elements
            $approve_count++;

            // If this is the first entry we're not permitted to change and it is not already ticked...
            if (!$permission_limit && (!array_key_exists($point, $statuses) || ($statuses[$point] == 0))) {
                // ... remember it (since we can send-to those who are permitted)
                $next_point = $point;
                $permission_limit = true;
            }
        }
    }

    // If we have no control over this workflow then don't bother showing it
    if (!$have_permission_over_a_point) {
        return new Tempcode();
    }

    ///////////////////
    // Send-to boxes //
    ///////////////////

    // Now we attempt to impose some sort of order to the send-to boxes.
    // The order we're going to use is to collect groups where the sum of the workflow positions they can approve is equal, and put the
    // collections in ascending order. The collections are ordered based on the group's average workflow approval position. Any further ambiguity is not worth considering.
    $group_scores = array(); // Track the sum of positions for each group
    $group_counts = array(); // Track the number of points each group can approve (so we can calculate the average)
    $active_groups = array(); // Note which groups have permission to approve the next workflow point we can't do ourselves
    // Get all of the approval points and all of the groups
    foreach ($approval_points as $point => $approval_point_name) {
        foreach (get_usergroups_for_approval_point($point) as $group) {
            // See whether this group should be active
            if ($next_point == $point) {
                $active_groups[] = $group;
            }
            // Add on the point's position to this group's score
            if (array_key_exists($group, $group_scores)) {
                $group_scores[$group] = $group_scores[$group] + get_approval_point_position($point, $relevant_workflow); // Otherwise give it a new score equal to this point's position
            } else {
                $group_scores[$group] = get_approval_point_position($point, $relevant_workflow);
            }
            // Now increment the group's approval point count
            if (array_key_exists($group, $group_counts)) {
                $group_counts[$group]++;
            } else {
                $group_counts[$group] = 1;
            }
        }
    }
    // Now we can order the groups:
    $group_order = array(); // This will store collections of groups in order of score
    foreach ($group_scores as $group_id => $score) {
        // Make a new collection if we've not got one
        if (!array_key_exists($score, $group_order)) {
            $group_order[$score] = array();
        }
        // Add this group to the collection
        $group_order[$score][] = $group_id;
    }

    // Finally we can get the tick-box details we found earlier and put them in the right order
    $send_to_boxes = array();
    foreach ($group_order as $score => $list_of_groups) {
        // Add the group if its the only one
        if (count($list_of_groups) == 1) {
            // Before adding the tick box, see if it should be active
            if (in_array($list_of_groups[0], $active_groups)) {
                $send_next[$list_of_groups[0]][2] = 1;
            }
            $send_to_boxes[] = $send_next[$list_of_groups[0]];
        } else {
            // Otherwise order the collection first
            $list_of_groups_order = array();
            foreach ($list_of_groups as $group_id) {
                // Work out the average position of each group's approval points (we need an int for use as an index, but add a couple of orders of magnitude to give us 2 more sig figs)
                $group_average = intval((floatval($score) / floatval($group_counts[$group_id])) * 100);
                // Make a new collection if we've not got one
                if (!array_key_exists($group_average, $list_of_groups_order)) {
                    $list_of_groups_order[$group_average] = array();
                }
                // Add this group to the average collection
                $list_of_groups_order[$group_average][] = $group_id;
            }
            // Now we can add them in order of average
            foreach ($list_of_groups_order as $average_collection) {
                foreach ($average_collection as $group_id) {
                    // Before adding the tick box, see if it should be active
                    if (in_array($group_id, $active_groups)) {
                        $send_next[$group_id][2] = 1;
                    }
                    $send_to_boxes[] = $send_next[$group_id];
                }
            }
        }
    }

    // Now tack the original submitter onto the end of the send-to list, if we know who it is
    $submitter = get_submitter_of_workflow_content($workflow_content_id);
    if (!is_null($submitter)) {
        $submitter_details = array(); // Build the array independently
        $submitter_details[] = $GLOBALS['FORUM_DRIVER']->get_username($submitter) . ' (' . do_lang('SUBMITTER') . ')';
        $submitter_details[] = 'send_author'; // Name
        $submitter_details[] = false; // Value
        $username = $GLOBALS['FORUM_DRIVER']->get_username($submitter);
        if (is_null($username)) {
            $username = do_lang('DELETED');
        }
        $submitter_details[] = do_lang_tempcode('NEXT_APPROVAL_AUTHOR', escape_html($username)); // Description
        $send_to_boxes[] = $submitter_details; // Then tack it on the end
    }

    ////////////////////////
    // Now build the form //
    ////////////////////////

    // Bail out if there's nothing to the workflow
    if ($approval_status == array()) {
        return new Tempcode();
    }

    // Attach the title to the form first, along with usage info
    $workflow_fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', array('_GUID' => 'f0a8a4aabdd2f42bf7612f88b52b32b6', 'TITLE' => null, 'HELP' => do_lang_tempcode('WORKFLOW_USAGE'))));

    // Show the current status next
    $workflow_fields->attach(form_input_various_ticks($existing_status, '', null, do_lang_tempcode('CURRENT_APPROVAL_STATUS'), false));

    // Attach the workflow tick boxes next
    $workflow_fields->attach(form_input_various_ticks($approval_status, '', null, do_lang_tempcode('POINTS_TO_APPROVE'), false));

    // Add the 'live' tickbox
    // NOTE: Not used at the moment; content becomes live once all points have been approved
    //$workflow_fields->attach($live_box);

    // Add a section for notes
    $notes = $GLOBALS['SITE_DB']->query_select('workflow_content', array('notes'), array('id' => $workflow_content_id));
    $workflow_fields->attach(form_input_huge(do_lang('NOTES'), do_lang('WORKFLOW_NOTES_DESCRIPTION'), 'workflow_notes', $notes[0]['notes'], false, null, 6, '', true));

    // Set who to send it to next
    $workflow_fields->attach(form_input_various_ticks($send_to_boxes, do_lang_tempcode('NEXT_APPROVAL_SIDE_DESCRIPTION'), null, do_lang_tempcode('NEXT_APPROVAL'), false));

    // Set the URL for handling the response to this form
    $post_url = build_url(array('page' => '_SELF', 'type' => 'workflow'), '_SELF');

    // Set the URL to return to after the handling has taken place
    $workflow_hidden->attach(form_input_hidden('return_url', get_self_url(true)));

    // Add all of these to the form
    $workflow_form = do_template('FORM', array(
        '_GUID' => '9eb9a74add2b4fea737d0af7b65a2d85',
        'FIELDS' => $workflow_fields,
        'HIDDEN' => $workflow_hidden,
        'TEXT' => '',
        'URL' => $post_url,
        'SUBMIT_NAME' => do_lang_tempcode('SUBMIT_WORKFLOW_CHANGES'),
        'SKIP_REQUIRED' => true,
        'SUBMIT_ICON' => 'menu__adminzone__audit__unvalidated',
    ));

    // Then pass it to whoever wanted it
    return do_template('WORKFLOW_BOX', array('_GUID' => 'cc80db735825a058c0d90e40e783ed30', 'FORM' => $workflow_form));
}

/**
 * Handler for workflow form submission.
 *
 * @return Tempcode Either an error page or a success message
 */
function workflow_update_handler()
{
    require_lang('workflows');
    require_code('database');

    $success_message = do_lang('APPROVAL_UNCHANGED');

    /////////////////////////////////////////
    // Grab everything we need from $_POST //
    /////////////////////////////////////////

    $workflow_id = post_param_integer('workflow_id');
    $content_id = post_param_string('content_id');
    $workflow_notes = post_param_string('workflow_notes');

    // Grab the names of our workflow approval points
    $approval_points = get_all_approval_points($workflow_id);
    if ($approval_points == array()) {
        warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html(strval($content_id)), do_lang_tempcode('WORKFLOW')));
    }

    // Find out which approvals have been given
    $approvals = array();
    foreach (array_keys($approval_points) as $approval_id) {
        $approvals[$approval_id] = (post_param_integer('approval_' . strval($approval_id), 0) == 1);
    }

    ////////////////////////
    // Get member details //
    ////////////////////////

    // Find out which groups/members to inform, starting with the
    // original submitter
    $send_to_members = array();
    if (post_param_integer('send_author', 0) == 1) {
        $submitter = get_submitter_of_workflow_content($content_id);
        if (!is_null($submitter)) {
            $send_to_members[$submitter] = 1;
        }
    }

    // Now get the groups
    $group_ids = array(); // Only remember 1 copy of each group
    foreach (array_keys($approval_points) as $point_id) {
        foreach (get_usergroups_for_approval_point($point_id) as $group) {
            if (!in_array($group, $group_ids)) {
                if (post_param_integer('send_' . strval($group), 0) == 1) {
                    $group_ids[] = $group;
                }
            }
        }
    }

    // From the groups we can get the members, and from there the emails
    foreach ($GLOBALS['FORUM_DRIVER']->member_group_query($group_ids) as $member) {
        $send_to_members[$member['id']] = 1;
    }

    ////////////////////////////////////////////
    // Now play with the database as required //
    ////////////////////////////////////////////

    // See which values need updating (ie. approvals have been given/withdrawn)
    $updated_approvals = array();
    $all_approval_statuses = array();

    // Grab each point's status from the database
    $old_values = $GLOBALS['SITE_DB']->query_select('workflow_content_status', array('workflow_approval_point_id', 'status_code'), array('workflow_content_id' => $content_id));

    $accounted_for_statuses = array();

    // Look for any differences we need to make
    foreach ($old_values as $old_value) {
        $noted = false;        // Keep a note of each value for including in emails

        // Only compare against values which we've been given
        if (array_key_exists($old_value['workflow_approval_point_id'], $approvals)) {
            $accounted_for_statuses[] = $old_value['workflow_approval_point_id'];

            // See if the database status is the same status as the given status
            if (($old_value['status_code'] ? true : false) != $approvals[$old_value['workflow_approval_point_id']]) {
                // If not then see if we have permission to change it
                $members_with_permission = array();
                foreach ($GLOBALS['FORUM_DRIVER']->member_group_query(get_usergroups_for_approval_point($old_value['workflow_approval_point_id'])) as $permitted) {
                    $members_with_permission[] = $permitted['id'];
                }
                if (in_array(get_member(), $members_with_permission)) {
                    // Remember that this needs to be changed
                    $updated_approvals[$old_value['workflow_approval_point_id']] = $approvals[$old_value['workflow_approval_point_id']];
                    // Make a note of this value in the array of all statuses
                    $all_approval_statuses[$old_value['workflow_approval_point_id']] = $approvals[$old_value['workflow_approval_point_id']];
                    $noted = true;
                }
            }
        }
        if (!$noted) {
            // If we're here then this status has either not been passed or it does not need modifying. Either way we can grab a valid status from the database.
            $all_approval_statuses[$old_value['workflow_approval_point_id']] = ($old_value['status_code'] ? true : false);
        }
    }
    // Now add any unaccounted-for points to those which need updating
    $new_approvals = array();
    foreach (array_keys($approvals) as $a) {
        if (!in_array($a, $accounted_for_statuses)) {
            $updated_approvals[$a] = $approvals[$a];
            $new_approvals[] = $a;
        }
    }

    // Now we know which fields to update, let's do so
    foreach ($updated_approvals as $approval_id => $status_code) {
        $success_message = do_lang('APPROVAL_CHANGED_DESCRIPTION');
        if (in_array($approval_id, $new_approvals)) {
            $GLOBALS['SITE_DB']->query_insert('workflow_content_status', array('status_code' => ($status_code ? 1 : 0), 'approved_by' => get_member(), 'workflow_content_id' => $content_id, 'workflow_approval_point_id' => $approval_id));
        } else {
            $GLOBALS['SITE_DB']->query_update('workflow_content_status', array('status_code' => ($status_code ? 1 : 0), 'approved_by' => get_member()), array('workflow_content_id' => $content_id, 'workflow_approval_point_id' => $approval_id), '', 1);
        }
    }

    // Update the notes (this is done destructively, but is simplest)
    // We append a timestamped log of the action taken
    $notes_approved = array();
    $notes_disapproved = array();
    foreach ($updated_approvals as $approval_id => $status_code) {
        if ($status_code) {
            $notes_approved[] = $approval_points[$approval_id];
        } else {
            // Just because it's not approved, doesn't mean that it was unticked.
            // It may have just been added to the workflow.
            if (!in_array($approval_id, $new_approvals)) {
                $notes_disapproved[] = $approval_points[$approval_id];
            }
        }
    }
    if (count($notes_approved) + count($notes_disapproved) > 0) {
        $note_title = get_timezoned_date(time(), true, false, false, true) . ' ' . $GLOBALS['FORUM_DRIVER']->get_username(get_member());
        $workflow_notes = $workflow_notes . "\n\n" . $note_title . "\n" . str_repeat('-', strlen($note_title));

        if (count($notes_approved) > 0) {
            $workflow_notes .= "\n" . do_lang('WORKFLOW_APPROVED') . ': ' . implode(', ', $notes_approved);
        }
        if (count($notes_disapproved) > 0) {
            $workflow_notes .= "\n" . do_lang('WORKFLOW_DISAPPROVED') . ': ' . implode(', ', $notes_disapproved);
        }
    }
    $GLOBALS['SITE_DB']->query_update('workflow_content', array('notes' => $workflow_notes), array('id' => $content_id), '', 1);

    /////////////////////////////
    // See if we're going live //
    /////////////////////////////

    // Validation is stored, for the most part, in a 'validated' field of the content's table. Those which store it elsewhere must specify this via their content-meta-aware info.

    // Grab lookup data from the workflows database
    $content_details = $GLOBALS['SITE_DB']->query_select('workflow_content', array('content_type', 'content_id'), array('id' => $content_id), '', 1);
    if ($content_details == array()) {
        warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html('workflow_content->' . strval($content_id))));
    }

    // Now use it to find this content's validation field...

    require_code('content');
    $ob = get_content_object($content_details[0]['content_type']);

    // Grab information about the hook
    $info = $ob->info();
    if ($info === null) {
        fatal_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }
    $content_table = $info['table'];
    $content_field = $info['id_field'];
    if (array_key_exists('validated_field', $info)) {
        $content_validated_field = $info['validated_field'];
    } else {
        // Fall back to 'validated' if nothing is specified
        $content_validated_field = 'validated';
    }

    // Now we have the details required to lookup this entry, wherever it is. Let's get its current validation status and compare to what the workflow would have it be
    $content_is_validated = $GLOBALS['SITE_DB']->query_select($content_table, array($content_validated_field), array($content_field => $info['id_field_numeric'] ? intval($content_details[0]['content_id']) : $content_details[0]['content_id']), '', 1);

    // Make sure we've actually found something
    if ($content_is_validated == array()) {
        $content_id = $content_details[0]['content_id'];
        $validated_field = $content_id->content_validated_field;
        warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html($content_table . '->' . $content_field . '->' . $validated_field)));
    }

    // In order for content to go live all points must be approved. See if all points have been approved. If so, none will have status 0
    $all_points_approved = false;
    if ($GLOBALS['SITE_DB']->query_select('workflow_content_status', array('workflow_approval_point_id'), array('workflow_content_id' => $content_id, 'status_code' => 0)) == array()) {
        $all_points_approved = true;
    }

    // We need to act if the validation status is different to the total completion of the workflow
    if (($content_is_validated[0][$content_validated_field] == 1) != $all_points_approved) {
        $success_message = $all_points_approved ? do_lang('APPROVAL_COMPLETE') : do_lang('APPROVAL_REVOKED');
        $GLOBALS['SITE_DB']->query_update($content_table, array($content_validated_field => $all_points_approved ? 1 : 0), array($content_field => $info['id_field_numeric'] ? intval($content_details[0]['content_id']) : $content_details[0]['content_id']), '', 1);
    }

    ///////////////////////////////////////////
    // Now inform members about this content //
    ///////////////////////////////////////////

    // Make a nicely formatted list of the statuses
    $status_list = '';
    foreach ($all_approval_statuses as $point => $status) {
        if ($status_list != '') {
            $status_list .= ', ';
        }
        $status_list .= $approval_points[$point] . ': ';
        $status_list .= ($status == 1) ? 'approved' : 'not approved';
    }

    // We cannot pass send_to_members to the notification dispatcher as it's in the incorrect format; fix this.
    $actual_send_to_members = array();
    foreach ($send_to_members as $member_id => $status) {
        if ($status != 1) {
            continue;
        }
        $actual_send_to_members[] = intval($member_id);
    }

    // Send notifications
    require_code('notifications');
    if (count($send_to_members) > 0) {
        $success_message .= do_lang('APPROVAL_CHANGED_NOTIFICATIONS');
    }
    $subject = do_lang('APPROVAL_EMAIL_SUBJECT',/*TODO: Should pass title in, for unique email subject line*/null, null, null, get_site_default_lang());
    $body = do_notification_lang('APPROVAL_EMAIL_BODY', str_replace('&keep_devtest=1', '', post_param_string('http_referer', cms_srv('HTTP_REFERER'))), $status_list, $workflow_notes, get_site_default_lang());
    dispatch_notification('workflow_step', strval($workflow_id), $subject, $body, $actual_send_to_members);

    // Finally return a success message
    $return_url = post_param_string('return_url');
    return redirect_screen(new Tempcode(), $return_url, $success_message);
}

/**
 * Adds the specified content (image, video, gallery, etc.) to the
 * specified workflow.
 *
 * @param  ID_TEXT $content_type The content-meta-aware name that applies to this content.
 * @param  ID_TEXT $content_id The ID of this content. Must be a string. Integers will be extracted from the string if needed.
 * @param  ?AUTO_LINK $workflow_id The ID of the desired workflow. (null: system default)
 * @param  boolean $remove_existing Whether to remove any existing workflows from this content beforehand (current permissions must allow this)
 * @return ?AUTO_LINK The content's ID in the workflow_content table. (null: if not added (eg. told to use default when there isn't one))
 */
function add_content_to_workflow($content_type = '', $content_id = '', $workflow_id = null, $remove_existing = false)
{
    require_code('content');

    // Have we been given a valid workflow to use? If not, use system default
    if (is_null($workflow_id)) {
        $default_workflow = get_default_workflow();
        if (is_null($default_workflow)) {
            // No default, so don't apply any
            return null;
        } else {
            $workflow_id = $default_workflow;
        }
    }

    $ob = get_content_object($content_type);

    // Grab information about the hook
    $info = $ob->info();
    if ($info === null) {
        return null;
    }
    $content_table = $info['table'];
    $content_field = $info['id_field'];

    // Now we have the information required to access the content.
    // However, we still don't know if the provided ID is valid, so we have to check that too!
    // Need different paths based on ID type, to prevent breaking strict databases

    // Query the database for content matching the found parameters
    if ($GLOBALS['SITE_DB']->query_select($content_table, array($content_field), array($content_field => $info['id_field_numeric'] ? $content_id : intval($content_id)), '', 1) == array()) {
        // This content doesn't exist, bail out
        warn_exit(do_lang_tempcode('_MISSING_RESOURCE', escape_html($content_table . '/' . $content_field . '/' . $content_id)));
    }

    // If we've made it this far then we have been asked to apply a valid workflow to a valid piece of content, so let's go ahead

    // Remove existing associations if we've been asked to
    if ($remove_existing) {
        $wf = get_workflow_of_content($content_type, $content_id);
        if (!is_null($wf)) {
            $workflow_content_ids = $GLOBALS['SITE_DB']->query_select('workflow_content', array('id'), array('content_type' => $content_type, 'content_id' => $content_id));
            foreach ($workflow_content_ids as $workflow_content_id) {
                $GLOBALS['SITE_DB']->query_delete('workflow_content', array('id' => $workflow_content_id['id']));
                $GLOBALS['SITE_DB']->query_delete('workflow_content_status', array('workflow_content_id' => $workflow_content_id['id']));
            }
        }
    }

    // Add to workflow
    $map = array(
        'content_type' => $content_type,
        'content_id' => $content_id,
        'workflow_id' => $workflow_id,
        'notes' => '',
        'original_submitter' => get_member(),
    );
    $id = $GLOBALS['SITE_DB']->query_insert('workflow_content', $map, true);

    // Set the workflow status to 0 for each point
    foreach (array_keys(get_all_approval_points($workflow_id)) as $approval_point_id) {
        $GLOBALS['SITE_DB']->query_insert('workflow_content_status', array('workflow_content_id' => $id, 'workflow_approval_point_id' => $approval_point_id, 'status_code' => 0, 'approved_by' => get_member()));
    }

    return $id;
}

/**
 * Returns all of the approval point which are currently defined. Indices are IDs, values are names.
 *
 * @param  AUTO_LINK $workflow_id The workflow ID.
 * @return array The approval points which are defined. Empty if none are defined.
 */
function get_all_approval_points($workflow_id)
{
    $workflow_approval_points = $GLOBALS['SITE_DB']->query_select('workflow_approval_points', array('id', 'workflow_approval_name'), array('workflow_id' => $workflow_id), 'ORDER BY the_position');
    $approval_points = array();
    foreach ($workflow_approval_points as $r) {
        $approval_points[$r['id']] = get_translated_text($r['workflow_approval_name']);
    }
    return $approval_points;
}

/**
 * Gets an array of the group IDs allowed to approve the given point
 *
 * @param  AUTO_LINK $approval_id The ID of the approval point
 * @return array The IDs of the groups allowed to signoff on it
 */
function get_usergroups_for_approval_point($approval_id)
{
    if (is_null($approval_id)) {
        warn_exit(do_lang_tempcode('_MISSING_RESOURCE', 'null approval'));
    }
    $groups = $GLOBALS['SITE_DB']->query_select('workflow_permissions', array('usergroup'), array('workflow_approval_point_id' => $approval_id));
    $raw_names = array();
    foreach ($groups as $group) {
        $raw_names[] = $group['usergroup'];
    }
    return $raw_names;
}

/**
 * Gets the position of the given approval point in the given workflow.
 *
 * @param  AUTO_LINK $approval_point_id The ID of the approval point
 * @return ?integer The position of the approval point in this case (null: if not found)
 */
function get_approval_point_position($approval_point_id)
{
    $found = $GLOBALS['SITE_DB']->query_select('workflow_approval_points', array('the_position'), array('id' => $approval_point_id), 'ORDER BY the_position ASC');
    if ($found != array()) {
        return $found[0]['the_position'];
    }
    return null;
}

/**
 * Get the workflow content ID for the given piece of content.
 *
 * @param  string $content_type The type of the content (eg. download, gallery, etc.)
 * @param  string $content_id The ID of the specific piece of content (if numeric, pass as a string anyway)
 * @return AUTO_LINK The workflow_content_id
 */
function get_workflow_content_id($content_type, $content_id)
{
    // Grab the specified content's ID
    $content = $GLOBALS['SITE_DB']->query_select('workflow_content', array('id'), array('content_type' => $content_type, 'content_id' => $content_id), '', 1);
    if ($content == array()) {
        return null;
    }
    return $content[0]['id'];
}

/**
 * Returns the workflow that the given content is in. This is useful for putting
 * a workflow on a container, like a gallery, and applying it to its contents, like images.
 *
 * @param  string $type The type of content (as specified when it was entered into the workflow system)
 * @param  string $id The ID of the content (as specified when it was entered into the workflow system)
 * @return ?AUTO_LINK The ID of the workflow that this content is in (null: not found)
 */
function get_workflow_of_content($type, $id)
{
    return $GLOBALS['SITE_DB']->query_select_value_if_there('workflow_content', 'workflow_id', array('content_type' => $type, 'content_id' => $id));
}

/**
 * Approves the given point for the given piece of content.
 *
 * @param  AUTO_LINK $workflow_content_id The *workflow content* ID (NOT the gallery, category, etc. ID!)
 * @param  AUTO_LINK $approval_point_id The approval point ID
 */
function approve_content_for_point($workflow_content_id, $approval_point_id)
{
    $GLOBALS['SITE_DB']->query_update('workflow_content_status', array('status_code' => 1), array('workflow_content_id' => $workflow_content_id, 'workflow_approval_point_id' => $approval_point_id), '', 1);
}

/**
 * This will remove the given content from the workflow system. This is useful
 * to call from content deletion functions. NOTE: This is not the same as
 * approving the content, since the validation will remain unchanged.
 *
 * @param  string $type The type of the content, as defined in the workflow_content table
 * @param  string $id The ID of the content, as defined in the workflow content table
 */
function remove_content_from_workflows($type, $id)
{
    $content_id = get_workflow_content_id($type, $id);
    $GLOBALS['SITE_DB']->query_delete('workflow_content', array('id' => $content_id));
    $GLOBALS['SITE_DB']->query_delete('workflow_content_status', array('workflow_content_id' => $content_id));
}

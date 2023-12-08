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

function init__galleries2($code)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!$GLOBALS['SITE_DB']->table_exists('workflow_content')) { // Not installed
        return $code;
    }

    // We want to inject our workflow handling code into add_image...
    $code = override_str_replace_exactly(
        "log_it('ADD_IMAGE', strval(\$id), \$title);",
        "
        if (\$validated == 0) {
            require_code('workflows');
            require_lang('workflows');

            // See if we have a specific workflow to use
            \$workflow_id = intval(str_replace('wf_', '', either_param_string('workflow', 'wf_-1')));
            // If we have been given a specific workflow, but we do not have access to choose workflows, fall back to the default
            if ((\$workflow_id != -1) && (!can_choose_workflow())) {
                \$workflow_id =- 1;
            }

            if (\$workflow_id == -1) {
                // Look for the workflow of the containing gallery
                \$workflow_id = get_workflow_of_content('gallery', \$title);
                if (\$workflow_id === null) {
                    // Use the default if it has none
                    \$workflow_id = get_default_workflow();
                } else {
                    // The parent has a workflow. Copy it for this.
                }
            } else {
                // Use the specific ID provided
            }

            if (\$workflow_id !== null) {
                add_content_to_workflow('image', strval(\$id), \$workflow_id);
                attach_message(do_lang_tempcode('CONTENT_NOW_IN_WORKFLOW', escape_html(get_workflow_name(\$workflow_id))), 'inform');
            }
        }

        <ditto>
        ",
        $code
    );

    // ...and add_video...
    $code = override_str_replace_exactly(
        "log_it('ADD_VIDEO', strval(\$id), \$title);",
        "
        if (\$validated == 0) {
            require_code('workflows');
            require_lang('workflows');

            // See if we have a specific workflow to use
            \$workflow_id = intval(str_replace('wf_', '', either_param_string('workflow', 'wf_-1')));
            // If we have been given a specific workflow, but we do not have access to choose workflows, fall back to the default
            if ((\$workflow_id != -1) && (!can_choose_workflow())) {
                \$workflow_id =- 1;
            }

            if (\$workflow_id == -1) {
                // Look for the workflow of the containing gallery
                \$workflow_id = get_workflow_of_content('gallery', \$title);
                if (\$workflow_id === null) {
                    // Use the default if it has none
                    \$workflow_id = get_default_workflow();
                } else {
                    // The parent has a workflow. Copy it for this.
                }
            } else {
                // Use the specific ID provided
            }

            if (\$workflow_id !== null) {
                add_content_to_workflow('video', strval(\$id), \$workflow_id);
                attach_message(do_lang_tempcode('CONTENT_NOW_IN_WORKFLOW', escape_html(get_workflow_name(\$workflow_id))), 'inform');
            }
        }

        <ditto>
        ",
        $code
    );

    // ...and add gallery...
    $code = override_str_replace_exactly(
        "log_it('ADD_GALLERY', \$name, \$fullname);",
        "
        require_code('workflows');
        require_lang('workflows');

        // See if we have a specific workflow to use
        \$workflow_id = intval(str_replace('wf_', '', either_param_string('workflow', 'wf_-1')));
        // If we have been given a specific workflow, but we do not have access to choose workflows, fall back to the default
        if ((\$workflow_id != -1) && (!can_choose_workflow())) {
            \$workflow_id = -1;
        }

        if (\$workflow_id == -1) {
            // Look for the workflow of the containing gallery
            \$workflow_id = get_workflow_of_content('gallery', \$parent_id);
            if (\$workflow_id === null) {
                // Use the default if it has none
                add_content_to_workflow('gallery', \$name);
            } else {
                // The parent has a workflow. Copy it for this.
                add_content_to_workflow('gallery', \$name, \$workflow_id);
            }
        } else {
            // Use the specific ID provided
            add_content_to_workflow('gallery', \$name, \$workflow_id);
        }
        <ditto>
        ",
        $code
    );

    // Editing is a bit different; we switch the workflow if needed.

    // Do this for images...
    $code = override_str_replace_exactly(
        "log_it('EDIT_IMAGE', strval(\$id), \$title);",
        "
        if (\$validated == 0) {
            require_code('workflows');
            require_lang('workflows');

            // See if we have a specific workflow to use
            \$edit_workflow = array_key_exists('workflow', \$_REQUEST) && (either_param_string('workflow') != 'wf_-2');
            \$current_workflow = get_workflow_of_content('image', strval(\$id));
            if (\$edit_workflow) {
                \$workflow_id = intval(str_replace('wf_', '', either_param_string('workflow', 'wf_-1')));
                // If we have been given a specific workflow, but we do not have access to choose workflows, fail
                if ((\$workflow_id != -1) && (!can_choose_workflow())) {
                    \$edit_workflow = false;
                }
            }
            if (\$edit_workflow && (\$workflow_id == -1)) {
                // Look for the workflow of the containing gallery
                \$workflow_id = get_workflow_of_content('gallery', \$title);
                if (\$workflow_id === null)
                {
                    // Use the default if it has none
                    \$default_workflow_id = get_default_workflow();
                    if (\$current_workflow != \$default_workflow_id) {
                        add_content_to_workflow('image', strval(\$id), \$default_workflow_id, true);
                        attach_message(do_lang_tempcode('CONTENT_NOW_IN_WORKFLOW', escape_html(get_workflow_name(\$default_workflow_id))), 'inform');
                    }
                } else {
                    // The parent has a workflow. Copy it for this.
                    if (\$workflow_id != \$current_workflow) {
                        add_content_to_workflow('image', strval(\$id), \$workflow_id, true);
                        attach_message(do_lang_tempcode('CONTENT_NOW_IN_WORKFLOW', escape_html(get_workflow_name(\$workflow_id))), 'inform');
                    }
                }
            }
            elseif (\$edit_workflow) {
                // Use the specific ID provided
                if (\$workflow_id != \$current_workflow)
                {
                    add_content_to_workflow('image', strval(\$id), \$workflow_id);
                    attach_message(do_lang_tempcode('CONTENT_NOW_IN_WORKFLOW', escape_html(get_workflow_name(\$workflow_id))), 'inform');
                }
            }
        }
        <ditto>
        ",
        $code
    );

    // ...videos...
    $code = override_str_replace_exactly(
        "log_it('EDIT_VIDEO', strval(\$id), \$title);",
        "
        if (\$validated == 0)
        {
            require_code('workflows');
            require_lang('workflows');

            // See if we have a specific workflow to use
            \$edit_workflow = array_key_exists('workflow', \$_REQUEST) && (either_param_string('workflow') != 'wf_-2');
            \$current_workflow = get_workflow_of_content('video', strval(\$id));
            if (\$edit_workflow) {
                \$workflow_id = intval(str_replace('wf_', '', either_param_string('workflow', 'wf_-1')));
                // If we have been given a specific workflow, but we do not have access to choose workflows, fail
                if ((\$workflow_id != -1) && (!can_choose_workflow())) {
                    \$edit_workflow = false;
                }
            }
            if ((\$edit_workflow) && (\$workflow_id == -1)) {
                // Look for the workflow of the containing gallery
                \$workflow_id = get_workflow_of_content('gallery', \$title);
                if (\$workflow_id === null) {
                    // Use the default if it has none
                    \$default_workflow_id = get_default_workflow();
                    if (\$current_workflow != \$default_workflow_id) {
                        add_content_to_workflow('video', strval(\$id), \$default_workflow_id, true);
                        attach_message(do_lang_tempcode('CONTENT_NOW_IN_WORKFLOW', escape_html(get_workflow_name(\$default_workflow_id))), 'inform');
                    }
                } else {
                    // The parent has a workflow. Copy it for this.
                    if (\$workflow_id != \$current_workflow) {
                        add_content_to_workflow('video', strval(\$id), \$workflow_id, true);
                        attach_message(do_lang_tempcode('CONTENT_NOW_IN_WORKFLOW', escape_html(get_workflow_name(\$workflow_id))), 'inform');
                    }
                }
            }
            elseif (\$edit_workflow) {
                // Use the specific ID provided
                if (\$workflow_id != \$current_workflow) {
                    add_content_to_workflow('video', strval(\$id), \$workflow_id);
                    attach_message(do_lang_tempcode('CONTENT_NOW_IN_WORKFLOW', escape_html(get_workflow_name(\$workflow_id))), 'inform');
                }
            }
        }
        <ditto>
        ",
        $code
    );

    // ...and galleries
    $code = override_str_replace_exactly(
        "log_it('EDIT_GALLERY', \$name, \$fullname);",
        "
        require_code('workflows');
        require_lang('workflows');

        // See if we have a specific workflow to use
        \$edit_workflow = array_key_exists('workflow', \$_REQUEST) && (post_param_string('workflow') != 'wf_-2');
        \$current_workflow = get_workflow_of_content('gallery', \$name);
        if (\$edit_workflow) {
            \$workflow_id = intval(str_replace('wf_', '', either_param_string('workflow', 'wf_-1')));
            // If we have been given a specific workflow, but we do not have access to choose workflows, fail
            if ((\$workflow_id != -1) && (!can_choose_workflow())) {
                \$edit_workflow = false;
            }
        }
        if (\$edit_workflow && \$workflow_id == -1) {
            // Look for the workflow of the containing gallery
            \$workflow_id = get_workflow_of_content('gallery', \$parent_id);
            if (\$workflow_id === null)
            {
                // Use the default if it has none
                \$default_workflow_id = get_default_workflow();
                if (\$current_workflow != \$default_workflow_id) {
                    add_content_to_workflow('gallery', \$name, \$default_workflow_id, true);
                }
            } else {
                // The parent has a workflow. Copy it for this.
                if (\$workflow_id != \$current_workflow) {
                    add_content_to_workflow('gallery', \$name, \$workflow_id, true);
                }
            }
        }
        elseif (\$edit_workflow) {
            // Use the specific ID provided
            if (\$workflow_id != \$current_workflow) {
                add_content_to_workflow('gallery', \$name, \$workflow_id, true);
            }
        }
        <ditto>
        ",
        $code
    );

    // Now we add removal code for the delete functions.
    // We do this for images...
    $code = override_str_replace_exactly(
        "log_it('DELETE_IMAGE', strval(\$id), get_translated_text(\$title));",
        "
        <ditto>
        require_code('workflows');
        require_lang('workflows');
        if (get_workflow_of_content('image', strval(\$id)) !== null) {
            remove_content_from_workflows('image', strval(\$id));
        }
        ",
        $code
    );

    // ...videos...
    $code = override_str_replace_exactly(
        "log_it('DELETE_VIDEO', strval(\$id), get_translated_text(\$title));",
        "
        <ditto>
        require_code('workflows');
        require_lang('workflows');
        if (get_workflow_of_content('video', strval(\$id)) !== null) {
            remove_content_from_workflows('video', strval(\$id));
        }
        ",
        $code
    );

    // ...and galleries.
    $code = override_str_replace_exactly(
        "log_it('DELETE_GALLERY', \$name, get_translated_text(\$rows[0]['fullname']));",
        "
        <ditto>
        require_code('workflows');
        require_lang('workflows');
        if (get_workflow_of_content('gallery', \$name) !== null) {
            remove_content_from_workflows('gallery', \$name);
        }
        ",
        $code
    );

    return $code;
}

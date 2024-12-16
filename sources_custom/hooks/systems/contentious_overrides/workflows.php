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

/**
 * Hook class.
 */
class Hook_contentious_overrides_workflows
{
    public function compile_included_code($path, $codename, &$code)
    {
        if (!addon_installed('workflows')) { // Not installed
            return;
        }

        if (!addon_installed('validation')) {
            return;
        }

        require_code('override_api');

        switch ($codename) {
            case 'galleries2':
                if (strpos($path, 'sources_custom/') !== false) {
                    return;
                }

                if (!addon_installed('galleries')) {
                    return;
                }

                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

                // We want to inject our workflow handling code into add_image...
                insert_code_before__by_command(
                    $code,
                    'add_image',
                    "log_it('ADD_IMAGE', strval(\$id), \$title);",
                    "
                    require_code('workflows');
                    handle_position_in_workflow_auto(\$validated, 'image', strval(\$id), 'gallery', \$cat, \$title);
                    ");

                // ...and add_video...
                insert_code_before__by_command(
                    $code,
                    'add_video',
                    "log_it('ADD_VIDEO', strval(\$id), \$title);",
                    "
                    require_code('workflows');
                    handle_position_in_workflow_auto(\$validated, 'video', strval(\$id), 'gallery', \$cat, \$title);
                    ");

                // ...and add gallery...
                insert_code_before__by_command(
                    $code,
                    'add_gallery',
                    "log_it('ADD_GALLERY', \$name, \$fullname);",
                    "
                    require_code('workflows');
                    handle_position_in_workflow_auto(1, 'gallery', \$name, 'gallery', \$parent_id, \$fullname);
                    ");

                // Editing is a bit different; we switch the workflow if needed.

                // Do this for images...
                insert_code_before__by_command(
                    $code,
                    'edit_image',
                    "log_it('EDIT_IMAGE', strval(\$id), \$title);",
                    "
                    require_code('workflows');
                    handle_position_in_workflow_edit(\$validated, 'image', strval(\$id), 'gallery', \$cat, \$title);
                    ");

                // ...videos...
                insert_code_before__by_command(
                    $code,
                    'edit_video',
                    "log_it('EDIT_VIDEO', strval(\$id), \$title);",
                    "
                    require_code('workflows');
                    handle_position_in_workflow_edit(\$validated, 'video', strval(\$id), 'gallery', \$cat, \$title);
                    ");

                // ...and galleries
                insert_code_before__by_command(
                    $code,
                    'edit_gallery',
                    "log_it('EDIT_GALLERY', \$name, \$fullname);",
                    "
                    require_code('workflows');
                    handle_position_in_workflow_edit(1, 'gallery', \$name, 'gallery', \$parent_id, \$fullname);
                    ");

                // Now we add removal code for the delete functions.
                // We do this for images...
                insert_code_after__by_command(
                    $code,
                    'delete_image',
                    "log_it('DELETE_IMAGE', strval(\$id), get_translated_text(\$title));",
                    "
                    require_code('workflows');
                    if (get_workflow_of_content('image', strval(\$id)) !== null) {
                        remove_content_from_workflows('image', strval(\$id));
                    }
                    ");

                // ...videos...
                insert_code_after__by_command(
                    $code,
                    'delete_video',
                    "log_it('DELETE_VIDEO', strval(\$id), get_translated_text(\$title));",
                    "
                    require_code('workflows');
                    if (get_workflow_of_content('video', strval(\$id)) !== null) {
                        remove_content_from_workflows('video', strval(\$id));
                    }
                    ");

                // ...and galleries.
                insert_code_after__by_command(
                    $code,
                    'delete_gallery',
                    "log_it('DELETE_GALLERY', \$name, get_translated_text(\$rows[0]['fullname']));",
                    "
                    require_code('workflows');
                    if (get_workflow_of_content('gallery', \$name) !== null) {
                        remove_content_from_workflows('gallery', \$name);
                    }
                    ");
                break;
            case 'hooks/systems/content_meta_aware/image':
                if (strpos($path, 'sources_custom/') !== false) {
                    return;
                }

                if (!addon_installed('galleries')) {
                    return;
                }

                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

                $code = override_str_replace_exactly(
                    "'table' => 'images',",
                    "'table' => 'images', 'uses_workflow' => true,",
                    $code
                );
                break;
            case 'hooks/systems/content_meta_aware/video':
                if (strpos($path, 'sources_custom/') !== false) {
                    return;
                }

                if (!addon_installed('galleries')) {
                    return;
                }

                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

                $code = override_str_replace_exactly(
                    "'table' => 'videos',",
                    "'table' => 'videos', 'uses_workflow' => true,",
                    $code
                );
                break;
            case 'adminzone/pages/modules/admin_validation.php':
                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

                $code = override_str_replace_exactly(
                    '$info[\'edit_page_link_pattern\']',
                    'empty($info[\'uses_workflow\']) ? $info[\'edit_page_link_pattern\'] : $info[\'view_page_link_pattern\']',
                    $code
                );

                $code = override_str_replace_exactly(
                    '$object->get_edit_url(null, false, \':validated=1\')',
                    'empty($info[\'uses_workflow\']) ? $object->get_edit_url(null, false, \':validated=1\') : $object->get_view_url(null, false, \':validated=1\')',
                    $code
                );
                break;
            case 'cms/pages/modules/cms_galleries.php':
                if (!addon_installed('galleries')) {
                    return;
                }

                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

                // NOTE: There are many classes defined in the cms_galleries file. We need to make all work.

                // Replace the validation field for images and videos with a workflow field.
                $code = override_str_replace_exactly(
                    "\$validated_field = new Tempcode();",
                    "
                    <ditto>
                    require_code('workflows');
                    if (!isset(\$adding)) {
                        \$adding = (\$url == '');
                    }
                    if (can_choose_workflow()) {
                        \$fields->attach(workflow_choose_ui(false, !\$adding)); // Set the first argument to true to show 'inherit from parent'
                    } else {
                        if (\$adding) {
                            \$hidden->attach(form_input_hidden('workflow', 'wf_-1'));
                        }
                    }
                    ",
                    $code,
                    2
                );
                $code = override_str_replace_exactly(
                    "\$fields->attach(\$validated_field);",
                    "require_code('workflows'); if (empty(get_all_workflows())) { <ditto> }",
                    $code,
                    3
                );

                // Now we add a workflow selection to the gallery creation form. This is a
                // little complicated, since galleries should inherit the workflow of their
                // parent by default, but their parent is chosen on the form. Thus we add an
                // option to inherit the parent's workflow
                $code = override_str_replace_exactly(
                    "\$fields->attach(form_input_radio(do_lang_tempcode('LAYOUT_MODE'), do_lang_tempcode('DESCRIPTION_LAYOUT_MODE'), 'layout_mode', \$radios));",
                    "
                    <ditto>
                    require_code('workflows');
                    if (can_choose_workflow()) {
                        \$fields->attach(workflow_choose_ui(false, \$name != ''));
                    } else {
                        \$fields->attach(form_input_hidden('workflow', 'wf_-1'));
                    }
                    ",
                    $code
                );
                break;
            case 'site/pages/modules/galleries.php':
                if (!addon_installed('galleries')) {
                    return;
                }

                if ($code === null) {
                    $code = clean_php_file_for_eval(file_get_contents($path));
                }

                require_code('override_api');

                // Add a redirection for the workflow handling
                insert_code_before__by_command(
                    $code,
                    "run",
                    "return new Tempcode();",
                    "if (\$type == 'workflow') {
                        require_code('workflows'); // Load workflow-related code
                        return workflow_update_handler();
                    }",
                    1,
                    true
                );

                // Add workflow warnings to carousel mode galleries. This has to be done for images...
                insert_code_before__by_command(
                    $code,
                    "do_gallery_carousel_mode",
                    "\$current_entry = do_template('GALLERY_CAROUSEL_MODE_IMAGE'",
                    "// Add the workflow form if this entry is not validated
                    if (\$row['validated'] == 0) {
                        require_code('workflows');

                        \$wf = get_workflow_of_content('image', strval(\$row['id']));
                        if (\$wf !== null) {
                            \$workflow_content_id = get_workflow_content_id('image', strval(\$row['id']));
                            if (\$workflow_content_id !== null) {
                                \$warning_details->attach(get_workflow_form(\$workflow_content_id));
                            }
                        }
                    }",
                    1,
                    true
                );

                // ...and videos separately.
                insert_code_before__by_command(
                    $code,
                    "do_gallery_carousel_mode",
                    "\$current_entry = do_template('GALLERY_CAROUSEL_MODE_VIDEO'",
                    "// Add the workflow form if this entry is not validated
                    if (\$row['validated'] == 0) {
                        require_code('workflows');

                        \$wf = get_workflow_of_content('video', strval(\$row['id']));
                        if (\$wf !== null) {
                            \$workflow_content_id = get_workflow_content_id('video', strval(\$row['id']));
                            if (\$workflow_content_id !== null) {
                                \$warning_details->attach(get_workflow_form(\$workflow_content_id));
                            }
                        }
                    }",
                    1,
                    true
                );

                // Add workflow warnings to images
                insert_code_before__by_command(
                    $code,
                    "show_image",
                    "\$add_date = get_timezoned_date_time(\$myrow['add_date']);",
                    "if (\$myrow['validated'] == 0) {
                        require_code('workflows');

                        \$workflow_content_id = get_workflow_content_id('image', strval(\$myrow['id']));
                        if (\$workflow_content_id !== null) {
                            \$warning_details->attach(get_workflow_form(\$workflow_content_id));
                        }
                    }",
                    1,
                    true
                );

                // ...and videos separately.
                insert_code_before__by_command(
                    $code,
                    "show_video",
                    "\$add_date = get_timezoned_date_time(\$myrow['add_date']);",
                    "if (\$myrow['validated'] == 0) {
                        require_code('workflows');

                        \$workflow_content_id = get_workflow_content_id('video', strval(\$myrow['id']));
                        if (\$workflow_content_id !== null) {
                            \$warning_details->attach(get_workflow_form(\$workflow_content_id));
                        }
                    }",
                    1,
                    true
                );
                break;
        }
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    workflows
 */

function init__galleries2($code)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('workflows')) { // Not installed
        return $code;
    }

    if (!addon_installed('validation')) {
        return $code;
    }

    // We want to inject our workflow handling code into add_image...
    $code = override_str_replace_exactly(
        "log_it('ADD_IMAGE', strval(\$id), \$title);",
        "
        require_code('workflows');
        handle_position_in_workflow_auto(\$validated, 'image', strval(\$id), 'gallery', \$cat, \$title);

        <ditto>
        ",
        $code
    );

    // ...and add_video...
    $code = override_str_replace_exactly(
        "log_it('ADD_VIDEO', strval(\$id), \$title);",
        "
        require_code('workflows');
        handle_position_in_workflow_auto(\$validated, 'video', strval(\$id), 'gallery', \$cat, \$title);

        <ditto>
        ",
        $code
    );

    // ...and add gallery...
    $code = override_str_replace_exactly(
        "log_it('ADD_GALLERY', \$name, \$fullname);",
        "
        require_code('workflows');
        handle_position_in_workflow_auto(1, 'gallery', \$name, 'gallery', \$parent_id, \$fullname);

        <ditto>
        ",
        $code
    );

    // Editing is a bit different; we switch the workflow if needed.

    // Do this for images...
    $code = override_str_replace_exactly(
        "log_it('EDIT_IMAGE', strval(\$id), \$title);",
        "
        require_code('workflows');
        handle_position_in_workflow_edit(\$validated, 'image', strval(\$id), 'gallery', \$cat, \$title);

        <ditto>
        ",
        $code
    );

    // ...videos...
    $code = override_str_replace_exactly(
        "log_it('EDIT_VIDEO', strval(\$id), \$title);",
        "
        require_code('workflows');
        handle_position_in_workflow_edit(\$validated, 'video', strval(\$id), 'gallery', \$cat, \$title);

        <ditto>
        ",
        $code
    );

    // ...and galleries
    $code = override_str_replace_exactly(
        "log_it('EDIT_GALLERY', \$name, \$fullname);",
        "
        require_code('workflows');
        handle_position_in_workflow_edit(1, 'gallery', \$name, 'gallery', \$parent_id, \$fullname);
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
        if (get_workflow_of_content('gallery', \$name) !== null) {
            remove_content_from_workflows('gallery', \$name);
        }
        ",
        $code
    );

    return $code;
}

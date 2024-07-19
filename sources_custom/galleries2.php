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

function init__galleries2($code)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('workflows')) { // Not installed
        return $code;
    }

    if (!addon_installed('validation')) {
        return $code;
    }

    require_code('override_api');

    // We want to inject our workflow handling code into add_image...
    insert_code_before__by_command(
        $code,
        'add_image',
        "log_it('ADD_IMAGE', strval(\$id), \$title);",
        "
        require_code('workflows');
        handle_position_in_workflow_auto(\$validated, 'image', strval(\$id), 'gallery', \$cat, \$title);

        ",
    );

    // ...and add_video...
    insert_code_before__by_command(
        $code,
        'add_video',
        "log_it('ADD_VIDEO', strval(\$id), \$title);",
        "
        require_code('workflows');
        handle_position_in_workflow_auto(\$validated, 'video', strval(\$id), 'gallery', \$cat, \$title);

        ",
    );

    // ...and add gallery...
    insert_code_before__by_command(
        $code,
        'add_gallery',
        "log_it('ADD_GALLERY', \$name, \$fullname);",
        "
        require_code('workflows');
        handle_position_in_workflow_auto(1, 'gallery', \$name, 'gallery', \$parent_id, \$fullname);

        ",
    );

    // Editing is a bit different; we switch the workflow if needed.

    // Do this for images...
    insert_code_before__by_command(
        $code,
        'edit_image',
        "log_it('EDIT_IMAGE', strval(\$id), \$title);",
        "
        require_code('workflows');
        handle_position_in_workflow_edit(\$validated, 'image', strval(\$id), 'gallery', \$cat, \$title);

        ",
    );

    // ...videos...
    insert_code_before__by_command(
        $code,
        'edit_video',
        "log_it('EDIT_VIDEO', strval(\$id), \$title);",
        "
        require_code('workflows');
        handle_position_in_workflow_edit(\$validated, 'video', strval(\$id), 'gallery', \$cat, \$title);

        ",
    );

    // ...and galleries
    insert_code_before__by_command(
        $code,
        'edit_gallery',
        "log_it('EDIT_GALLERY', \$name, \$fullname);",
        "
        require_code('workflows');
        handle_position_in_workflow_edit(1, 'gallery', \$name, 'gallery', \$parent_id, \$fullname);

        ",
    );

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
        ",
    );

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
        ",
    );

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
        ",
    );

    return $code;
}

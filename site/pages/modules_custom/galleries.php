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
 * Inject workflow code into the galleries view.
 *
 * @return  string Altered code
 */
function init__site__pages__modules_custom__galleries($code)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('workflows')) { // Not installed
        return $code;
    }

    if (!addon_installed('validation')) {
        return $code;
    }

    if (!addon_installed('galleries')) {
        return $code;
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
        }"
    );

    // Add workflow warnings to carousel mode galleries. This has to be done for images...
    insert_code_before__by_command(
        $code,
        "do_gallery_carousel_mode",
        "\$current_entry = do_template('GALLERY_CAROUSEL_MODE_IMAGE'",
        "// Add the workflow form if this entry is non-validated
        if (\$row['validated'] == 0) {
            require_code('workflows');

            \$wf = get_workflow_of_content('image', strval(\$row['id']));
            if (\$wf !== null) {
                \$workflow_content_id = get_workflow_content_id('image', strval(\$row['id']));
                if (\$workflow_content_id !== null) {
                    \$warning_details->attach(get_workflow_form(\$workflow_content_id));
                }
            }
        }"
    );

    // ...and videos separately.
    insert_code_before__by_command(
        $code,
        "do_gallery_carousel_mode",
        "\$current_entry = do_template('GALLERY_CAROUSEL_MODE_VIDEO'",
        "// Add the workflow form if this entry is non-validated
        if (\$row['validated'] == 0) {
            require_code('workflows');

            \$wf = get_workflow_of_content('video', strval(\$row['id']));
            if (\$wf !== null) {
                \$workflow_content_id = get_workflow_content_id('video', strval(\$row['id']));
                if (\$workflow_content_id !== null) {
                    \$warning_details->attach(get_workflow_form(\$workflow_content_id));
                }
            }
        }"
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
        }"
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
        }"
    );

    return $code;
}

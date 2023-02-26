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

    if (!addon_installed('unvalidated')) {
        return $code;
    }

    if (!addon_installed('galleries')) {
        return $code;
    }

    // Add a redirection for the workflow handling
    $code = override_str_replace_exactly(
        "// What are we doing?\n        \$type = get_param_string('type', 'browse');",
        "
        <ditto>
        if (\$type == 'workflow') {
            require_code('workflows'); // Load workflow-related code
            return workflow_update_handler();
        }
        ",
        $code
    );

    // Add workflow warnings to carousel mode galleries. This has to be done for images...
    $code = override_str_replace_exactly(
        "\$current_entry = do_template('GALLERY_CAROUSEL_MODE_IMAGE'",
        "
        // Add the workflow form if this entry is non-validated
        if (\$row['validated'] == 0) {
            require_code('workflows');

            \$wf = get_workflow_of_content('image', strval(\$row['id']));
            if (\$wf !== null) {
                \$workflow_content_id = get_workflow_content_id('image', strval(\$row['id']));
                if (\$workflow_content_id !== null) {
                    \$warning_details->attach(get_workflow_form(\$workflow_content_id));
                }
            }
        }
        <ditto>
        ",
        $code
    );

    // ...and videos separately.
    $code = override_str_replace_exactly(
        "\$current_entry = do_template('GALLERY_CAROUSEL_MODE_VIDEO'",
        "
        // Add the workflow form if this entry is non-validated
        if (\$row['validated'] == 0) {
            require_code('workflows');

            \$wf = get_workflow_of_content('video', strval(\$row['id']));
            if (\$wf !== null) {
                \$workflow_content_id = get_workflow_content_id('video', strval(\$row['id']));
                if (\$workflow_content_id !== null) {
                    \$warning_details->attach(get_workflow_form(\$workflow_content_id));
                }
            }
        }
        <ditto>
        ",
        $code
    );

    // Add workflow warnings to images
    $code = override_str_replace_exactly(
        "} else {
            \$warning_details = new Tempcode();
        }

        if ((has_actual_page_access(null, 'cms_galleries', null, null)) && (has_edit_permission('mid', get_member(), \$myrow['submitter'], 'cms_galleries', ['galleries', \$cat]))) {
            \$edit_url = build_url(['page' => 'cms_galleries', 'type' => '_edit', 'id' => \$id], get_module_zone('cms_galleries'));
        } else {
            \$edit_url = new Tempcode();
        }",
        "
        <ditto>

        if (\$myrow['validated'] == 0) {
            require_code('workflows');

            \$workflow_content_id = get_workflow_content_id('image', strval(\$myrow['id']));
            if (\$workflow_content_id !== null) {
                \$warning_details->attach(get_workflow_form(\$workflow_content_id));
            }
        }
        ",
        $code
    );

    // ...and videos separately.
    $code = override_str_replace_exactly(
        "} else {
            \$warning_details = new Tempcode();
        }

        if ((has_actual_page_access(null, 'cms_galleries', null, null)) && (has_edit_permission('mid', get_member(), \$myrow['submitter'], 'cms_galleries', ['galleries', \$cat]))) {
            \$edit_url = build_url(['page' => 'cms_galleries', 'type' => '_edit_other', 'id' => \$id], get_module_zone('cms_galleries'));
        } else {
            \$edit_url = new Tempcode();
        }",
        "
        <ditto>

        if (\$myrow['validated'] == 0) {
            require_code('workflows');

            \$workflow_content_id = get_workflow_content_id('video', strval(\$myrow['id']));
            if (\$workflow_content_id !== null) {
                \$warning_details->attach(get_workflow_form(\$workflow_content_id));
            }
        }
        ",
        $code
    );

    return $code;
}

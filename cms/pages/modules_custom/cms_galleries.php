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
 * Inject workflow code into the galleries module.
 *
 * @param  string $code Original code
 * @return string Altered code
 */
function init__cms__pages__modules_custom__cms_galleries(string $code) : string
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

    return $code;
}

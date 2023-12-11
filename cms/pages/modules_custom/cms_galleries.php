<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    workflows
 */

/**
 * Inject workflow code into the galleries module.
 *
 * @param   string $code Original code
 * @return  string Altered code
 */
function init__cms__pages__modules_custom__cms_galleries($code)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!$GLOBALS['SITE_DB']->table_exists('workflow_content')) { // Not installed
        return $code;
    }

    // NOTE: There are many classes defined in the cms_galleries file. We need to make all work.

    // Replace the validation field for images and videos with a workflow field.
    // We start a comment to disable the regular validation steps.
    $code = override_str_replace_exactly(
        "\$thumb_width = get_option('thumb_width');",
        "
        <ditto>
        require_code('workflows');
        require_lang('workflows');
        if (!isset(\$adding)) {
            \$adding = (\$url == '');
        }
        if (can_choose_workflow()) {
            \$fields->attach(workflow_choose_ui(false, !\$adding)); // Set the first argument to true to show 'inherit from parent'
        } else {
            if (\$adding) {
                \$fields->attach(form_input_hidden('workflow', 'wf_-1'));
            }
        }
        ",
        $code,
        2
    );

    // Here we end the comment we started above, both for images...
    $code = override_str_replace_exactly(
        "\$fields->attach(form_input_tick(do_lang_tempcode('VALIDATED'), do_lang_tempcode(\$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member()) ? 'DESCRIPTION_VALIDATED_SIMPLE' : 'DESCRIPTION_VALIDATED', 'image'), 'validated', \$validated == 1));",
        "require_code('workflows'); if (count(get_all_workflows()) == 0) { <ditto> }",
        $code
    );
    // ...and videos.
    $code = override_str_replace_exactly(
        "\$validated_field = form_input_tick(do_lang_tempcode('VALIDATED'), do_lang_tempcode(\$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member()) ? 'DESCRIPTION_VALIDATED_SIMPLE' : 'DESCRIPTION_VALIDATED', 'video'), 'validated', \$validated == 1);",
        "require_code('workflows'); if (count(get_all_workflows()) == 0) { <ditto> } else { \$validated_field = new Tempcode(); }",
        $code
    );

    // Now we add a workflow selection to the gallery creation form. This is a
    // little complicated, since galleries should inherit the workflow of their
    // parent by default, but their parent is chosen on the form. Thus we add an
    // option to inherit the parent's workflow
    $code = override_str_replace_exactly(
        "\$fields->attach(form_input_tick(do_lang_tempcode('FLOW_MODE_INTERFACE'), do_lang_tempcode('DESCRIPTION_FLOW_MODE_INTERFACE'), 'flow_mode_interface', \$flow_mode_interface == 1));",
        "
        <ditto>
        require_code('workflows');
        require_lang('workflows');
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

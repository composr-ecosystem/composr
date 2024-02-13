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
 * Inject workflow code into the admin_validation module.
 *
 * @return  string Altered code
 */
function init__adminzone__pages__modules_custom__admin_validation($code)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('workflows')) { // Not installed
        return $code;
    }

    if (!addon_installed('validation')) {
        return $code;
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

    return $code;
}

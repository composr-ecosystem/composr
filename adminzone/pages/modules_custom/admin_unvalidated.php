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
 * Inject workflow code into the admin_unvalidated module.
 *
 * @return  string Altered code
 */
function init__adminzone__pages__modules_custom__admin_unvalidated($code)
{
    i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

    if (!addon_installed('workflows')) { // Not installed
        return $code;
    }

    if (!addon_installed('unvalidated')) {
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

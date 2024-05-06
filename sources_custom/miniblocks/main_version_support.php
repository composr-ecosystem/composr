<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('cms_homesite')) {
    return do_template('RED_ALERT', ['_GUID' => 'g4l3co9a92o8mbdsq1luqo3mqdftv7x8', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite'))]);
}

require_code('cms_homesite');
$branches = get_composr_branches();

$_branches = [];
foreach ($branches as $branch) {
    $_branches[] = [
        'GIT_BRANCH' => $branch['git_branch'],
        'BRANCH' => $branch['branch'],
        'STATUS' => $branch['status'],
    ];
}

return do_template('CMS_BLOCK_MAIN_VERSION_SUPPORT', ['_GUID' => 'd712250b9397ccfca98f09227cb891f6', 'BRANCHES' => $_branches]);

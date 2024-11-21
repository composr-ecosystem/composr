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
    return do_template('RED_ALERT', ['_GUID' => '20e69ec0a5965c4a9c3c8a8d55943b0e', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite'))]);
}

require_code('cms_homesite');
require_code('temporal');
require_code('version');

$branches = get_composr_branches();
if ((!$GLOBALS['DEV_MODE']) && (count($branches) == 0)) {
    // We want this condition to be logged instead of just returning a red alert
    attach_message('Expected branch maintenance status data but did not get any. Is the git repository initialized or the GitLab credentials specified and valid?', 'warn', false, true);
    return do_template('RED_ALERT', ['_GUID' => '23a59bf5854a5b7a8d0eeeb93889911a', 'TEXT' => do_lang_tempcode('INTERNAL_ERROR', escape_html('012c072cc29f58d9b4f66c6830af0bdb'))]);
}

// LEGACY: since some people still use v9
// FUDGE
$branches[] = [
    'git_branch' => '',
    'branch' => '9.x',
    'status' => 'EOL',
    'version' => '9.0.37',
    'version_time' => null
];

$_branches = [];

foreach ($branches as $branch) {
    switch ($branch['status']) {
        case VERSION_ALPHA:
        case VERSION_BETA:
            $class = 'error';
            break;
        case VERSION_MAINLINE:
        case VERSION_SUPPORTED:
            $class = 'debug';
            break;
        case VERSION_LTM:
            $class = 'warning';
            break;
        case VERSION_EOL:
            $class = 'disabled';
            break;
        default:
            $class = '';
    }

    $_branches[] = [
        'GIT_BRANCH' => $branch['git_branch'],
        'BRANCH' => $branch['branch'],
        'STATUS' => $branch['status'],
        'VERSION' => $branch['version'],
        'VERSION_TIME' => (($branch['version_time'] !== null) ? get_timezoned_date_time_tempcode($branch['version_time']) : new Tempcode()),
        'ROW_CLASS' => $class,
    ];
}

return do_template('CMS_BLOCK_MAIN_VERSION_SUPPORT', ['_GUID' => 'd712250b9397ccfca98f09227cb891f6', 'BRANCHES' => $_branches]);

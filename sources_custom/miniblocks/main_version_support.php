<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

require_code('composr_homesite');
$branches = get_composr_branches();

$_branches = array();
foreach ($branches as $branch) {
    if ($branch['eol'] === null) {
        $_eol = '';
    } else {
        $_eol = get_timezoned_date($branch['eol']);
    }

    $_branches[] = array(
        'GIT_BRANCH' => $branch['git_branch'],
        'BRANCH' => $branch['branch'],
        'STATUS' => $branch['status'],
        'EOL' => $_eol,
    );
}

return do_template('CMS_BLOCK_MAIN_VERSION_SUPPORT', array('_GUID' => 'd99184e051d70d4118d89a8669ec8773', 'BRANCHES' => $_branches));

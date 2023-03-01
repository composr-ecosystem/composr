<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class file_naming_test_set extends cms_test_case
{
    public function testFileNamingConvention()
    {
        require_code('files2');
        require_code('third_party_code');

        $ignore_substrings = [
            '/-logo.png',
        ];

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_FLOATING | IGNORE_REVISION_FILES | IGNORE_EDITFROM_FILES | IGNORE_CUSTOM_THEMES | IGNORE_UPLOADS | IGNORE_CUSTOM_DIR_FLOATING_CONTENTS);
        foreach ($files as $path) {
            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
                'data_custom/images/lolcats',
                'data_custom/webfonts',
                'themes/default/images/realtime_rain',
                'themes/default/images/cns_emoticons',
                'themes/default/images/jquery_ui',
                'themes/default/images/skitter',
                'data/mediaelement',
                'data/plupload',
                'data/fonts',
                'test-a',
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
                '_config.php.template',
                'themes/default/images/mediaelement/mejs-controls.svg',
                'themes/default/javascript/jsdoc-conf.json',
                'data/modules/admin_backup/restore.php.pre',
                'data/robots.txt.template',
                '.user.ini',
                'aps/APP-META.xml',
                'aps/APP-LIST.xml',
                'aps/scripts/templates/_config.php.in',
                'data_custom/images/docs/tut_intl_maintenance/continents-975936_1280.jpg',
                'data_custom/execute_temp.php.bundle',
                'sources_custom/user_sync__customise.php.example',
                'data_custom/images/causes/w3c-xhtml.gif',
                'data_custom/images/causes/w3c-css.gif',
                'data/plupload/Moxie.swf',
                'data/plupload/Moxie.xap',
                'themes/default/images/loading.gif.png',
            ]);
            if (in_array($path, $exceptions)) {
                continue;
            }
            foreach ($ignore_substrings as $substring) {
                if (strpos($path, $substring) !== false) {
                    continue 2;
                }
            }

            $ok = preg_match('#^[\w/]*(\.\w+)?(\.(gz|br))?$#', $path);

            $this->assertTrue($ok, 'File naming not matching convention for: ' . $path);
        }
    }
}

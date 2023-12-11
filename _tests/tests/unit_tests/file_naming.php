<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

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
        require_code('files');
        require_code('files2');

        $ignore_stubs = array(
            'test-a/',
            'aps/',
            'data/ckeditor/',
            'data/curl-ca-bundle.crt',
            'data/jwplayer.flash.swf',
            'data/modules/admin_backup/restore.php.pre',
            'data/modules/admin_cleanup/page_stats.php.pre',
            'data/modules/google_appengine/php.gae.ini',
            'data/plupload/',
            'data_custom/execute_temp.php.bundle',
            'data_custom/images/',
            'data_custom/images/lolcats/',
            'data_custom/modules/buildr/docs/',
            'data_custom/modules/docs/',
            'data_custom/sitemaps/',
            'data_custom/upload-crop/',
            'docs/standards-checklist.xls',
            'mobiquo/',
            'sources_custom/geshi/',
            'sources_custom/getid3/',
            'sources_custom/photobucket/',
            'sources_custom/php-crossword/',
            'sources_custom/programe/',
            'sources_custom/sabredav/',
            'sources_custom/spout/',
            'sources_custom/swift_mailer/',
            'sources_custom/user_sync__customise.php.example',
            'themes/default/images/jquery_ui/',
            'themes/default/images/realtime_rain/',
            'themes/default/images/select2/',
            'themes/default/images_custom/openid/',
            'themes/default/images_custom/skitter/',
            'themes/default/javascript_custom/mediaelement-and-player.js',
            'tracker/',
            '_config.php.template',
            '_tests/codechecker/codechecker.app/',
            '_tests/codechecker/netbeans/',
            'data_custom/pdf_viewer/',
            '_tests/codechecker/target/',

            'themes/default/images/cns_default_avatars/default_set/cartoons/half-life.jpg'
        );

        $ignore_substrings = array(
            '/-logo.png',
            '/checklist/checklist-.png',
        );

        $files = get_directory_contents(get_file_base());
        foreach ($files as $file) {
            if (preg_match('#^themes/_unnamed_/#', $file) != 0) {
                continue;
            }

            if (should_ignore_file($file, IGNORE_USER_CUSTOMISE | IGNORE_UPLOADS | IGNORE_CUSTOM_DIR_GROWN_CONTENTS | IGNORE_REVISION_FILES | IGNORE_EDITFROM_FILES)) {
                continue;
            }

            foreach ($ignore_substrings as $substring) {
                if (strpos($file, $substring) !== false) {
                    continue 2;
                }
            }

            foreach ($ignore_stubs as $stub) {
                if (substr($file, 0, strlen($stub)) == $stub) {
                    continue 2;
                }
            }

            $ok = preg_match('#^[\w/]*(\.\w+)?$#', $file);

            $this->assertTrue($ok, 'File naming not matching convention for: ' . $file);
        }
    }
}

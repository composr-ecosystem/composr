<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

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

        $ignore_stubs = [
            '.user.ini',
            'themes/default/javascript/jsdoc-conf.json',
            'aps/',
            'data/polyfills/',
            'data/ace/',
            'data/ckeditor/',
            'data/curl-ca-bundle.crt',
            'data/modules/admin_backup/restore.php.pre',
            'data/modules/google_appengine/php.gae.ini',
            'data/plupload/',
            'data_custom/execute_temp.php.bundle',
            'data_custom/images/',
            'data_custom/images/lolcats/',
            'data_custom/modules/buildr/docs/',
            'data_custom/modules/docs/',
            'data_custom/sitemaps/',
            'data_custom/upload-crop/',
            'mobiquo/',
            'sources_custom/aws_ses/',
            'sources_custom/geshi/',
            'sources_custom/getid3/',
            'sources_custom/photobucket/',
            'sources_custom/facebook/vendor/facebook/',
            'sources_custom/programe/',
            'sources_custom/sabredav/',
            'sources_custom/spout/',
            'sources_custom/swift_mailer/',
            'sources_custom/user_sync__customise.php.example',
            'themes/default/images/jquery_ui/',
            'themes/default/images/realtime_rain/',
            'themes/default/images/select2/',
            'themes/default/images/skitter/',
            'themes/default/javascript/mediaelement-and-player.js',
            'themes/default/images/cns_emoticons/',
            'tracker/',
            'vendor/',
            '_tests/simpletest',
            '_config.php.template',
            '_tests/codechecker/codechecker.app/',
            'test-a',
            'themes/default/templates_cached/EN/',
            'themes/admin/templates_cached/EN/',
            '_tests/codechecker/target/maven-status/maven-compiler-plugin/compile/default-compile/createdFiles.lst',
            '_tests/codechecker/target/maven-status/maven-compiler-plugin/compile/default-compile/inputFiles.lst',
            'data/mediaelement/mediaelement-flash-audio-ogg.swf',
            'data/mediaelement/mediaelement-flash-audio.swf',
            'data/mediaelement/mediaelement-flash-video-hls.swf',
            'data/mediaelement/mediaelement-flash-video-mdash.swf',
            'data/mediaelement/mediaelement-flash-video.swf',
            'data/robots.txt.template',
            'data_custom/webfonts/adgs-icons.eot',
            'data_custom/webfonts/adgs-icons.svg',
            'data_custom/webfonts/adgs-icons.ttf',
            'data_custom/webfonts/adgs-icons.woff',
            'themes/default/images/mediaelement/mejs-controls.svg',
            '_tests/assets/text/utf-8.txt',
            '_tests/assets/text/utf-16.txt',
            '_tests/assets/text/utf-16be.txt',
            '_tests/assets/text/iso-8859-1.txt',
            '_tests/assets/spreadsheets/test-tsv.txt',
            '_tests/assets/spreadsheets/test-scsv.txt',
            'data_custom/pdf_viewer/',
            '_tests/codechecker/target/maven-archiver/pom.properties',
        ];

        $ignore_substrings = [
            '/-logo.png',
        ];

        $files = get_directory_contents(get_file_base(), '', IGNORE_FLOATING | IGNORE_REVISION_FILES | IGNORE_EDITFROM_FILES | IGNORE_CUSTOM_THEMES | IGNORE_UPLOADS | IGNORE_CUSTOM_DIR_FLOATING_CONTENTS);
        foreach ($files as $path) {
            foreach ($ignore_substrings as $substring) {
                if (strpos($path, $substring) !== false) {
                    continue 2;
                }
            }

            foreach ($ignore_stubs as $stub) {
                if (substr($path, 0, strlen($stub)) == $stub) {
                    continue 2;
                }
            }

            $ok = preg_match('#^[\w/]*(\.\w+)?(\.(gz|br))?$#', $path);

            $this->assertTrue($ok, 'File naming not matching convention for: ' . $path);
        }
    }
}

<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

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
class basic_code_formatting_test_set extends cms_test_case
{
    protected $files;
    protected $text_formats;

    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND_crawl);

        require_code('files2');

        $this->files = get_directory_contents(get_file_base(), '', IGNORE_FLOATING | IGNORE_CUSTOM_DIR_FLOATING_CONTENTS | IGNORE_UPLOADS | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_CUSTOM_THEMES);
        $this->files[] = 'install.php';

        $this->text_formats = array();
        $path = get_file_base() . '/.gitattributes';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);
        $matches = array();
        $num_matches = preg_match_all('#^\*\.(\w+) text#m', $c, $matches);
        $found = array();
        for ($i = 0; $i < $num_matches; $i++) {
            $ext = $matches[1][$i];
            $this->text_formats[$ext] = true;
        }
    }

    public function testNoBomMarkers()
    {
        if (($this->only !== null) && ($this->only != 'testNoBomMarkers')) {
            return;
        }

        $boms = _get_boms();

        foreach ($this->files as $path) {
            $myfile = fopen(get_file_base() . '/' . $path, 'rb');
            $magic_data = fread($myfile, 4);
            fclose($myfile);

            foreach ($boms as $charset => $bom) {
                $this->assertTrue(substr($magic_data, strlen($bom)) != $bom, $charset . ' byte-order mark found in ' . $path . ': we do not want them');
            }
        }
    }

    public function testTabbing()
    {
        if (($this->only !== null) && ($this->only != 'testTabbing')) {
            return;
        }

        $file_types_spaces = array(
            'js',
            'php',
        );

        $file_types_tabs = array(
            'css',
            'tpl',
            'xml',
            'sh',
            'txt',
        );

        $exceptions = array(
            '_tests/tests/unit_tests/tempcode.php',
            '_tests/tests/unit_tests/xss.php',
            '_tests/libs/mf_parse.php',
            'data_custom/sitemaps/news_sitemap.xml',
            'manifest.xml',
            'mobiquo/lib/xmlrpc.php',
            'mobiquo/lib/xmlrpcs.php',
            'mobiquo/smartbanner/appbanner.css',
            'mobiquo/smartbanner/appbanner.js',
            'mobiquo/tapatalkdetect.js',
            'parameters.xml',
            'site/pages/comcode/EN/userguide_comcode.txt',
            'sources_custom/twitter.php',
            'themes/default/css/widget_color.css',
            'themes/default/css/widget_select2.css',
            'themes/default/css/mediaelementplayer.css',
            'themes/default/javascript/jquery.js',
            'themes/default/javascript/jquery_ui.js',
            'themes/default/javascript/plupload.js',
            'themes/default/javascript/select2.js',
            'themes/default/javascript/theme_colours.js',
            'themes/default/javascript/widget_date.js',
            'themes/default/javascript_custom/columns.js',
            'themes/default/javascript_custom/jquery_flip.js',
            'themes/default/javascript/mediaelement-and-player.js',
            'themes/default/javascript_custom/skitter.js',
            'themes/default/javascript_custom/sortable_tables.js',
            'themes/default/javascript_custom/unslider.js',
            'themes/default/javascript_custom/charts.js',
        );

        foreach ($this->files as $path) {
            if (in_array($path, $exceptions)) {
                continue;
            }

            if (preg_match('#^(data_custom/sitemap|sources_custom/sabredav|docs/pages/comcode_custom/EN|data_custom/modules/admin_stats|data/polyfills|aps|_tests/codechecker/netbeans|data/ace|data/ckeditor|sources_custom/composr_mobile_sdk|tracker|sources_custom/ILess|sources_custom/spout|sources_custom/getid3|sources_custom/programe|_tests/simpletest)/#', $path) != 0) {
                continue;
            }

            $ext = get_file_extension(get_file_base() . '/' . $path);

            if ((in_array($ext, $file_types_spaces)) || (in_array($ext, $file_types_tabs))) {
                $c = cms_file_get_contents_safe($path);

                $contains_tabs = strpos($c, "\t");
                $contains_spaced_tabs = strpos($c, '    ');

                if (in_array($ext, $file_types_spaces)) {
                    $this->assertTrue(!$contains_tabs, 'Tabs are in ' . $path);
                } elseif (in_array($ext, $file_types_tabs)) {
                    $this->assertTrue(!$contains_spaced_tabs, 'Spaced tabs are in ' . $path);
                }
            }
        }
    }

    public function testNoTrailingWhitespace()
    {
        if (($this->only !== null) && ($this->only != 'testNoTrailingWhitespace')) {
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND_slow);

        foreach ($this->files as $path) {
            $exceptions = array(
                '_tests/assets/text',
                '_tests/assets/spreadsheets',
                '_tests/codechecker/netbeans',
                '_tests/simpletest',
                'data/ace',
                'data/ckeditor/plugins/codemirror',
                'data/polyfills',
                'mobiquo/lib',
                'mobiquo/smartbanner',
                'sources_custom/aws',
                'sources_custom/Cloudinary',
                'sources_custom/composr_mobile_sdk/ios',
                'sources_custom/photobucket',
                'sources_custom/programe',
                'sources_custom/sabredav',
                'tracker',
            );
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }

            $exceptions = array(
                'text/unbannable_ips.txt',
                'sources_custom/twitter.php',
                'user.sql',
                'themes/default/javascript/mediaelement-and-player.js',
                'themes/default/javascript_custom/skitter.js',
                'themes/default/javascript_custom/sortable_tables.js',
                'themes/default/javascript_custom/unslider.js',
                'themes/default/javascript_custom/charts.js',
                'themes/default/javascript_custom/confluence2.js',
                'themes/default/templates/BREADCRUMB_SEPARATOR.tpl',
                'data_custom/rate_limiter.php',
            );
            if (in_array($path, $exceptions)) {
                continue;
            }

            $ext = get_file_extension(get_file_base() . '/' . $path);

            if ((isset($this->text_formats[$ext])) && ($ext != 'svg') && ($ext != 'ini')) {
                $c = cms_file_get_contents_safe($path);

                $ok = (preg_match('#[ \t]$#m', $c) == 0);
                $this->assertTrue($ok, 'Has trailing whitespace in ' . $path);
            }
        }
    }

    public function testNoNonAscii()
    {
        if (($this->only !== null) && ($this->only != 'testNoNonAscii')) {
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND_slow);

        foreach ($this->files as $path) {
            $exceptions = array(
                '_tests/assets/text',
                '_tests/assets/spreadsheets',
                '_tests/simpletest',
                'data/ckeditor',
                'sources_custom/aws',
                'sources_custom/geshi',
                'sources_custom/getid3',
                'sources_custom/ILess',
                'sources_custom/sabredav',
                'sources_custom/spout',
                'sources_custom/swift_mailer',
                'tracker',
                'lang_custom/(?!EN)\w+',
                'text_custom/(?!EN)\w+',
                'comcode_custom/(?!EN)\w+',
            );
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }

            $exceptions = array(
                'data/curl-ca-bundle.crt',
                'lang/langs.ini',
                'mobiquo/license_agreement.txt',
                'themes/default/css_custom/confluence.css',
                'data_custom/webfonts/adgs-icons.svg',
                'themes/default/javascript_custom/confluence.js',
                'themes/default/javascript_custom/confluence2.js',
                'sources_custom/Cloudinary/cacert.pem',
            );
            if (in_array($path, $exceptions)) {
                continue;
            }

            $ext = get_file_extension(get_file_base() . '/' . $path);

            if (isset($this->text_formats[$ext])) {
                $c = cms_file_get_contents_safe($path);

                if ($ext == 'php' || $ext == 'css' || $ext == 'js') {
                    // Strip comments, which often contain people's non-English names
                    $c = preg_replace('#/\*.*\*/#Us', '', $c);
                    $c = preg_replace('#//.*#', '', $c);
                }

                if ($ext == 'ini') {
                    // We will allow utf-8 data in language files as a special exception
                    continue;
                }

                $ok = (preg_match('#[^\x00-\x7f]#', $c) == 0);
                $this->assertTrue($ok, 'Has non-ASCII data in ' . $path . '; find in your editor with this regexp [^\x00-\x7F]');
            }
        }
    }

    public function testCorrectLineTerminationAndLineFormat()
    {
        if (($this->only !== null) && ($this->only != 'testCorrectLineTerminationAndLineFormat')) {
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND_slow);

        foreach ($this->files as $path) {
            if (filesize(get_file_base() . '/' . $path) == 0) {
                continue;
            }

            $exceptions = array(
                '_tests/assets/text',
                '_tests/assets/spreadsheets',
                'tracker',
                'data/ace',
                'data/ckeditor',
                'sources_custom/composr_mobile_sdk/ios/ApnsPHP',
                'sources_custom/sabredav',
                'sources_custom/spout',
                'sources_custom/photobucket',
                'sources_custom/ILess',
                'sources_custom/facebook',
                'sources_custom/aws/Aws',
                'docs/jsdoc',
            );
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            if ($path == '_tests/codechecker/checker.ini') {
                continue;
            }

            $ext = get_file_extension(get_file_base() . '/' . $path);

            if (isset($this->text_formats[$ext])) {
                $c = cms_file_get_contents_safe($path);

                $this->assertTrue(strpos($c, "\r") === false, 'Windows text format detected for ' . $path);

                if ($ext == 'svg') {
                    continue;
                }

                $term_breaks = strlen($c) - strlen(rtrim($c, "\n"));

                $expected_breaks = 1;

                if (in_array($path, array(
                    'data_custom/latest_activity.txt',
                    'themes/admin/templates/MENU_BRANCH_dropdown.tpl',
                    'themes/default/templates/BREADCRUMB_SEPARATOR.tpl',
                    'themes/default/templates/CHATCODE_EDITOR_BUTTON.tpl',
                    'themes/default/templates/CHATCODE_EDITOR_MICRO_BUTTON.tpl',
                    'themes/default/templates/COMCODE_CURRENCY.tpl',
                    'themes/default/templates/COMCODE_EDITOR_BUTTON.tpl',
                    'themes/default/templates/COMCODE_EDITOR_MICRO_BUTTON.tpl',
                    'themes/default/templates/COMCODE_IMG.tpl',
                    'themes/default/templates/FRACTIONAL_EDIT.tpl',
                    'themes/default/templates/MENU_BRANCH_dropdown.tpl',
                    'themes/default/templates/MENU_BRANCH_popup.tpl',
                    'themes/default/templates/NOTIFICATION_PT_DESKTOP.tpl',
                    'themes/default/templates/NOTIFICATION_WEB_DESKTOP.tpl',
                    'themes/default/templates/PAGINATION_CONTINUE.tpl',
                    'themes/default/templates/PAGINATION_CONTINUE_FIRST.tpl',
                    'themes/default/templates/PAGINATION_CONTINUE_LAST.tpl',
                    'themes/default/templates/PAGINATION_NEXT.tpl',
                    'themes/default/templates/PAGINATION_NEXT_LINK.tpl',
                    'themes/default/templates/PAGINATION_PAGE_NUMBER.tpl',
                    'themes/default/templates/PAGINATION_PAGE_NUMBER_LINK.tpl',
                    'themes/default/templates/PAGINATION_PREVIOUS.tpl',
                    'themes/default/templates/PAGINATION_PREVIOUS_LINK.tpl',
                    'themes/default/templates/CURRENCY.tpl',
                    'themes/default/templates/CROP_TEXT_MOUSE_OVER_INLINE.tpl',
                    'themes/default/templates/CROP_TEXT_MOUSE_OVER.tpl',
                    'themes/default/templates/CNS_USER_MEMBER.tpl',
                    'themes/default/templates/ICON.tpl',
                    'themes/default/templates/MENU_LINK_PROPERTIES.tpl',
                ))) {
                    $expected_breaks = 0;
                }

                $this->assertTrue($term_breaks == $expected_breaks, 'Wrong number of terminating line breaks (got ' . integer_format($term_breaks) . ', expects ' . integer_format($expected_breaks) . ') for ' . $path);
            }
        }
    }
}

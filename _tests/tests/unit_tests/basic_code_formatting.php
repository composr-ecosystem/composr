<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

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
class basic_code_formatting_test_set extends cms_test_case
{
    protected $files;
    protected $text_formats;

    public function setUp()
    {
        parent::setUp();

        cms_extend_time_limit(TIME_LIMIT_EXTEND__CRAWL);

        require_code('third_party_code');
        require_code('files2');

        $this->files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_FLOATING | IGNORE_CUSTOM_DIR_FLOATING_CONTENTS | IGNORE_UPLOADS | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_CUSTOM_THEMES);
        $this->files[] = 'install.php';

        $this->text_formats = [];
        $path = get_file_base() . '/.gitattributes';
        $c = cms_file_get_contents_safe($path, FILE_READ_LOCK);
        $matches = [];
        $num_matches = preg_match_all('#^\*\.(\w+) text#m', $c, $matches);
        $found = [];
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

        $file_types_spaces = [
            'js',
            'php',
        ];

        $file_types_tabs = [
            'css',
            'tpl',
            'xml',
            'sh',
            'txt',
        ];

        foreach ($this->files as $path) {
            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
                'aps',
                'data_custom/sitemap',
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
                'data_custom/sitemaps/news_sitemap.xml',
                'site/pages/comcode/EN/userguide_comcode.txt',
                '_tests/tests/unit_tests/tempcode.php',
                '_tests/tests/unit_tests/xss.php',
                'text/unbannable_ips.txt',
            ]);
            if (in_array($path, $exceptions)) {
                continue;
            }

            $ext = get_file_extension($path);

            if ((in_array($ext, $file_types_spaces)) || (in_array($ext, $file_types_tabs))) {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

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

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        foreach ($this->files as $path) {
            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
                'data_custom/rate_limiter.php',
                'text/unbannable_ips.txt',
                'themes/default/templates/BREADCRUMB_SEPARATOR.tpl',
            ]);
            if (in_array($path, $exceptions)) {
                continue;
            }

            $ext = get_file_extension($path);

            if ((isset($this->text_formats[$ext])) && ($ext != 'svg') && ($ext != 'ini')) {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

                $ok = (preg_match('#[ \t]$#m', $c) == 0);
                $this->assertTrue($ok, 'Has trailing whitespace in ' . $path . '; grep for [ \t]+$');
            }
        }
    }

    public function testNoNonAsciiOrControlCharacters()
    {
        if (($this->only !== null) && ($this->only != 'testNoNonAscii')) {
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        foreach ($this->files as $path) {
            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
                'comcode_custom/(?!EN)\w+',
                'lang_custom/(?!EN)\w+',
                'text_custom/\w+',
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
                'lang/langs.ini',
                'docs/THANKS.md',
            ]);
            if (in_array($path, $exceptions)) {
                continue;
            }

            $ext = get_file_extension($path);

            if (isset($this->text_formats[$ext])) {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

                if ($ext == 'php' || $ext == 'css' || $ext == 'js') {
                    // Strip comments, which often contain people's non-English names
                    $c = preg_replace('#/\*.*\*/#Us', '', $c);
                    $c = preg_replace('#//.*#', '', $c);
                }

                if ($ext == 'ini') {
                    // We will allow utf-8 data in language files as a special exception
                    continue;
                }

                $regexp = '[^\x00-\x7f]';
                $ok = (preg_match('#' . $regexp . '#', $c) == 0);
                $this->assertTrue($ok, 'Has non-ASCII data in ' . $path . '; find in your editor with this regexp: ' . $regexp);

                $regexp = '[\x00\x01\x02\x03\x04\x05\x06\x07\x08\x0B\x0C\x0E\x0F\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F\x7F]';
                $ok = (preg_match('#' . $regexp . '#', $c) == 0);
                $this->assertTrue($ok, 'Has unexpected control characters in ' . $path . '; find in your editor with this regexp: ' . $regexp);
            }
        }
    }

    public function testCorrectLineTerminationAndLineFormat()
    {
        if (($this->only !== null) && ($this->only != 'testCorrectLineTerminationAndLineFormat')) {
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        foreach ($this->files as $path) {
            if (filesize(get_file_base() . '/' . $path) == 0) {
                continue;
            }

            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
                'text_custom/\w+',
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
                'data_custom/latest_activity.txt', // LEGACY
            ]);
            if (in_array($path, $exceptions)) {
                continue;
            }

            $ext = get_file_extension($path);

            if (isset($this->text_formats[$ext])) {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

                $this->assertTrue(strpos($c, "\r") === false, 'Windows text format detected for ' . $path . '. This may be expected when using git for Windows. But make sure you commit and build in Linux/UNIX format.');

                if ($ext == 'svg') {
                    continue;
                }

                $num_line_breaks_total = substr_count($c, "\n");
                if (($this->debug) && ($num_line_breaks_total == 1) && ($ext == 'tpl')) {
                    var_dump('Single line template: ' . $path);
                }

                $num_term_breaks = strlen($c) - strlen(rtrim($c, "\n"));

                // We expect all text files to end with one single line break, which is a long standing unix convention.
                //  Some templates need to be loaded with no terminating line, as white-space may cause an issue.
                //  Composr accommodates for this with a special rule - a terminating line break is stripped from any one-line templates.
                $expected_term_breaks = 1;
                if (in_array(basename($path), ['FRACTIONAL_EDIT.tpl'])) {
                    $expected_term_breaks = 0;
                }

                $this->assertTrue($num_term_breaks == $expected_term_breaks, 'Wrong number of terminating line breaks (got ' . integer_format($num_term_breaks) . ', expects ' . integer_format($expected_term_breaks) . ') for ' . $path);
            }
        }
    }
}

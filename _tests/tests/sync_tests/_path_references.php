<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _path_references_test_set extends cms_test_case
{
    public function testPathReferences()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $regexps = [
            '#\'([^\'/:]+/[^\'/:]+\.\w+)\'#', // Matches single-quoted paths
            '#\"([^\"/:]+/[^\"/:]+\.\w+)\"#', // Matches double-quoted paths
        ];

        require_code('third_party_code');
        require_code('files2');
        require_code('themes');
        require_code('themes2');

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_FLOATING | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE, true, true, ['php', 'tpl']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            // Exceptions
            $exceptions = array_merge(list_untouchable_third_party_directories(), [
                'sources/diff/Diff',
            ]);
            if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
                continue;
            }
            $exceptions = array_merge(list_untouchable_third_party_files(), [
                '_tests/tests/async_tests/file_security.php', // Tests against invalid paths
                '_tests/tests/async_tests/should_ignore_file.php', // Also tests against an invalid file
                '_tests/tests/async_tests/tar.php', // Testing a zero-byte file which does not exist
                '_tests/tests/async_tests/emoticons.php', // Testing emoticons which do not exist
                '_tests/tests/async_tests/gallery_images.php', // Testing gallery images which do not exist
                '_tests/tests/async_tests/tutorial_image_consistency.php', // Does its own path splitting
                '_tests/tests/async_tests/url_management.php', // Invalid URL test
                '_tests/tests/async_tests/zip.php', // Creating ZIP files
                '_tests/tests/sync_tests/standard_dir_files.php',

                'sources/mime_types.php', // Not actual file paths
                'sources/aggregate_types.php',

                // Third party imports referencing files relative to their own forum
                'sources/hooks/modules/admin_import/mybb.php',
                'sources/hooks/modules/admin_import/vb3.php',
            ]);
            if (in_array($path, $exceptions)) {
                continue;
            }

            // Reference directory exceptions
            $r_directory_exceptions = [
                'caches', // Temporary
                'temp', // Temporary
            ];

            // Reference exceptions (full)
            $r_exceptions = [
                // Files that do not exist by default in a Composr install
                'data_custom/execute_temp.php',
                'data_custom/latest_activity.txt',
                'data_custom/rate_limiter.php',
                'sources_custom/critical_errors.php',
                'Could not find data_custom/upgrader.cms.tmp',
                'lang_custom/langs.ini',

                // Wildcard files that cannot be tested
                'text_custom/*.txt',

                // Paths with variables in them, so cannot be tested
                '0;\' . escape_html(basename($dir_name)) . \'/browse.htm',

                // Paths that exist, but not relative to Composr and are not special template / image codes
                'Text/Diff.php',

                // Rewrite URLs, not paths
                's/ID.htm',
                'PAGE/TYPE.htm',

                // Not actually files
                'HTTP/1.1',
                'Alexa/Archive.org',
                'You have a [tt]backdoor_ip[/tt] setting left defined in _config.php',
                'access.usergroup / access.member',
                'privilege.usergroup / privilege.member',
                'Parts of the implementation are copyright to Quoord systems (the Tapatalk company). See mobiquo/license_agreement.txt',
            ];

            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $matches = [];
            foreach ($regexps as $regexp) {
                $num_matches = preg_match_all($regexp, $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $_path = trim(urldecode($matches[1][$i]));
                    if (preg_match('#^(' . implode('|', $r_directory_exceptions) . ')/#', $_path) != 0) {
                        continue;
                    }

                    if (in_array($_path, $r_exceptions)) {
                        continue;
                    }

                    // Directory traversal; cannot test for these because we do not necessarily know the file base in these contexts
                    if ((strpos($_path, './') === 0) || (strpos($_path, '../') === 0)) {
                        continue;
                    }

                    // Log files are always temporary and not created by default
                    if (strpos($_path, '.log') !== false) {
                        continue;
                    }

                    // Replacements for common file path Tempcode and variables
                    $rep = [
                        // Base URLs; should always point to the root of the software, so just remove them
                        '{$BASE_URL*}' => '',
                        '{$BRAND_BASE_URL*}' => '',
                        '{RESOURCE_BASE_URL*}' => '', // installer
                        '\' . get_base_url() . \'' => '',
                        '\' . get_brand_base_url() . \'' => '',

                        '{FROM*}' => 'data/polyfills/', // themes/default/templates/HTML_HEAD_POLYFILLS.tpl
                    ];
                    foreach ($rep as $search => $replace) {
                        $_path = str_replace($search, $replace, $_path);
                    }

                    // Replacements for *only* the start of a string
                    /*
                    $rep_start = [
                    ];
                    foreach ($rep_start as $search => $replace) {
                        if (strpos($_path, $search) === 0) {
                            $_path = $replace . substr($_path, strlen($search));
                        }
                    }
                    */

                    // First check if the file exists as-is
                    $_full_path = get_file_base() . '/' . $_path;
                    $ok = file_exists($_full_path);

                    // Maybe it's a theme image code? (Only check default and admin themes)
                    if (!$ok) {
                        $image = find_theme_image($_path, true, true, 'default', 'EN', null, true);
                        $ok = (($image !== null) && file_exists($image));
                    }
                    if (!$ok) {
                        $image = find_theme_image($_path, true, true, 'admin', 'EN', null, true);
                        $ok = (($image !== null) && file_exists($image));
                    }

                    // Maybe it's a template code? (Only check default and admin themes)
                    if (!$ok) {
                        $template = find_template_path(basename($_path), dirname($_path), 'default');
                        $ok = (($template !== null) && file_exists($template));
                    }
                    if (!$ok) {
                        $template = find_template_path(basename($_path), dirname($_path), 'admin');
                        $ok = (($template !== null) && file_exists($template));
                    }

                    // It probably does not exist at this point
                    if (!$ok) {
                        $this->assertTrue(!file_exists(get_file_base() . '/' . cms_strtolower_ascii($_path)) && !file_exists(get_file_base() . '/' . cms_strtoupper_ascii($_path)), $_path . ' has case sensitivity issues');
                        $this->assertTrue(false, 'Possible missing file referenced: ' . $_path . ', in ' . $path);
                    }
                }
            }
        }
    }
}

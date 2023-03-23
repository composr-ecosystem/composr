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

// This test checks the chmod files are consistent.
// Here's a similar and related thing:
// If you need the rewrite rule files to be consistent, you need to run data_custom/build_rewrite_rules.php to rebuild them.
// Also, manifests are built-in make_release.php.

/**
 * Composr test case class (unit testing).
 */
class chmod_consistency_test_set extends cms_test_case
{
    public function testConsistency()
    {
        $places = [
            [
                'docs/pages/comcode_custom/EN/tut_install_permissions.txt',
                false, // Windows-slashes
                true, // Wildcard-support
                false, // Existing run-time files also

                '[tt]',
                '[/tt]',
            ],

            [
                'aps/APP-META.xml',
                false, // Windows-slashes
                false, // Wildcard-support
                false, // Existing run-time files also

                '<mapping url="',
                '">',
            ],
        ];

        // Check everything contains what is defined in canonical source
        require_code('file_permissions_check');
        foreach ($places as $place_parts) {
            if (count($place_parts) == 6) {
                list($place, $windows_slashes, $wildcard_support, $runtime_too, $pre, $post) = $place_parts;
            } else {
                list($place, $windows_slashes, $wildcard_support, $runtime_too, $pre_dir, $post_dir, $pre_file, $post_file) = $place_parts;
            }

            $place_path = get_file_base() . '/' . $place;
            $place_path_exists = file_exists($place_path);
            $this->assertTrue($place_path_exists, $place . ' is missing, cannot check it');

            if ($place_path_exists) {
                $c = cms_file_get_contents_safe($place_path, FILE_READ_LOCK | FILE_READ_BOM);

                // Special cleanup
                if ($place == 'docs/pages/comcode_custom/EN/tut_install_permissions.txt') {
                    $c = preg_replace('#<for-each-\w+>#', '*', $c);
                }

                $c_stripped = $c;

                $slash = $windows_slashes ? '\\' : '/';
                $chmod_array = get_chmod_array($runtime_too, false);
                foreach ($chmod_array as $item) {
                    $path = get_file_base() . '/' . $item;

                    $exists = (strpos($path, '*') !== false) || (file_exists($path));
                    $this->assertTrue($exists || $item == 'data_custom/errorlog.php', 'Chmod item does not exist: ' . $item);

                    if ($exists) {
                        if (count($place_parts) == 6) {
                            $yoyo = [[$pre, $post]];
                        } else {
                            if (is_file($path)) {
                                $pre = $pre_file;
                                $post = $post_file;
                            } else {
                                $pre = $pre_dir;
                                $post = $post_dir;
                            }
                            $yoyo = [[$pre_dir, $post_dir], [$pre_file, $post_file]];
                        }

                        if (strpos($path, '*') === false) {
                            $dir = is_dir($path);
                            $file = is_file($path);

                            $this->assertTrue($dir || $file, 'Chmod item is neither a file nor a directory: ' . $item);
                        }

                        $_item = str_replace('/', $slash, $item);

                        if ($wildcard_support) {
                            // Wildcard support meaning wildcards may come up literally, or with * instead of **
                            $search = $pre . $_item . $post;
                            $there = (strpos($c, $search) !== false);
                            if ($there) {
                                $c_stripped = str_replace($search, '', $c_stripped); // So we can check for no alien stuff; trim is because pre and post may overlap with shared spaces
                            } else {
                                $there = (strpos($c, str_replace('**', '*', $search)) !== false);
                                if ($there) {
                                    $c_stripped = str_replace(str_replace('**', '*', $search), '', $c_stripped); // So we can check for no alien stuff; trim is because pre and post may overlap with shared spaces
                                }
                            }
                        } else {
                            // No wildcard support meaning what wildcards are used now may come up as tedious non-wildcarded expansions
                            $search_regexp = preg_quote($pre, '#') . str_replace(['\*\*', '\*'], ['.*', '.*'], preg_quote($_item, '#')) . preg_quote($post, '#');
                            $there = (preg_match('#' . $search_regexp . '#', $c) != 0);
                            if ($there) {
                                $c_stripped = preg_replace('#' . $search_regexp . '#', '', $c_stripped); // So we can check for no alien stuff; trim is because pre and post may overlap with shared spaces
                            }
                        }
                        $this->assertTrue($there, 'Chmod item is missing from ' . $place . ': ' . $item);
                    }
                }

                // Make sure no alien (old chmod entries that no longer should be there - or ones missing from file_permissions_check.php, potentially)
                foreach ($yoyo as $bits) {
                    list($_pre, $_post) = $bits;

                    $matches = [];
                    $num_matches = preg_match_all('#' . preg_quote($_pre, '#') . '(\w+[/\\\\][\w/\\\\]+)' . preg_quote($_post, '#') . '#', $c_stripped, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $this->assertTrue(false, 'Unexpected remaining path in ' . $place . ': ' . $matches[1][$i]);
                    }
                }
            }
        }
    }
}

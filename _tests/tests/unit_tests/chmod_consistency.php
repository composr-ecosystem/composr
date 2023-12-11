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

// This test checks the chmod files are consistent.
// Here's a similar and related thing:
// If you need the rewrite rule files to be consistent, you need to run data_custom/build_rewrite_rules.php to rebuild them.
// Also, manifests are built in make_release.php.

/**
 * Composr test case class (unit testing).
 */
class chmod_consistency_test_set extends cms_test_case
{
    public function testConsistency()
    {
        $places = array(
            array(
                'fixperms.bat',
                true, // Windows-slashes
                true, // Wildcard-support
                true, // Existing run-time files also

                'icacls ',
                ' /grant %user%:(M)',
            ),

            array(
                'fixperms.sh',
                false, // Windows-slashes
                true, // Wildcard-support
                true, // Existing run-time files also

                ' ',
                ' ',
            ),

            array(
                'docs/pages/comcode_custom/EN/tut_adv_install.txt',
                false, // Windows-slashes
                true, // Wildcard-support
                true, // Existing run-time files also

                '[tt]',
                '[/tt]',
            ),

            array(
                'parameters.xml',
                false, // Windows-slashes
                false, // Wildcard-support
                false, // Existing run-time files also

                '" description="Sets the ACL on the right file" defaultValue="{Application Path}/',
                '" tags="Hidden">',
            ),

            array(
                'parameters.xml',
                false, // Windows-slashes
                false, // Wildcard-support
                false, // Existing run-time files also

                '<parameterEntry type="ProviderPath" scope="setAcl" match="composr/',
                '" />',
            ),

            array(
                'manifest.xml',
                false, // Windows-slashes
                false, // Wildcard-support
                false, // Existing run-time files also

                // Directory
                '<setAcl path="composr/',
                '" setAclAccess="Modify" setAclUser="anonymousAuthenticationUser" />',

                // Files
                '<setAcl path="composr/',
                '" setAclResourceType="File" setAclAccess="Modify" setAclUser="anonymousAuthenticationUser" />',
            ),

            array(
                'aps/APP-META.xml',
                false, // Windows-slashes
                false, // Wildcard-support
                false, // Existing run-time files also

                '<mapping url="',
                '">',
            ),
        );

        $place_files = array();
        $place_files_stripped = array();
        foreach ($places as $place_parts) {
            list($place) = $place_parts;
            $place_path = get_custom_file_base() . '/' . $place;
            $place_path_exists = file_exists($place_path);
            $this->assertTrue($place_path_exists, $place . ' is missing, cannot check it');

            if ($place_path_exists) {
                $place_files[$place_path] = file_get_contents($place_path);
                $place_files_stripped[$place_path] = $place_files[$place_path];

                // Special checks
                switch ($place) {
                    case 'parameters.xml':
                        $matches = array();
                        $found = array();
                        $num_matches = preg_match_all('#SetAclParameter(\d+)#', file_get_contents($place_path), $matches);
                        for ($i = 0; $i < $num_matches; $i++) {
                            $x = $matches[1][$i];
                            $this->assertTrue(!in_array($x, $found), 'Multiple defined parameters.xml parameters: ' . $matches[0][$i]);
                            $found[] = $x;
                        }
                        break;
                }
            }
        }

        require_code('inst_special');
        $chmod_array = get_chmod_array(fallback_lang(), true);
        foreach ($chmod_array as $item) {
            $path = get_custom_file_base() . '/' . $item;

            $is_runtime = (strpos($path, '*') !== false);

            $exists = $is_runtime || file_exists($path);
            $this->assertTrue($exists, 'Chmod item does not exist: ' . $item);

            if ($exists) {
                if ($is_runtime) {
                    $dir = false;
                    $file = true;
                } else {
                    $dir = is_dir($path);
                    $file = is_file($path);
                }

                $this->assertTrue($dir || $file, 'Chmod item is neither a file nor a directory: ' . $item);

                foreach ($places as $place_parts) {
                    if (count($place_parts) == 6) {
                        list($place, $windows_slashes, $wildcard_support, $runtime_too, $pre, $post) = $place_parts;
                    } else {
                        if ($dir) {
                            list($place, $windows_slashes, $wildcard_support, $runtime_too, $pre, $post, , ) = $place_parts;
                        } else {
                            list($place, $windows_slashes, $wildcard_support, $runtime_too, , , $pre, $post) = $place_parts;
                        }
                    }

                    if ($place == 'fixperms.sh' && preg_match('#^uploads/\w+/\*$#', $item) != 0) {
                        // Special case, handled with a "find" command due to wildcard expansion limit
                        continue;
                    }

                    if (!$runtime_too && $is_runtime) {
                        continue;
                    }

                    $place_path = get_custom_file_base() . '/' . $place;

                    if (file_exists($place_path)) {
                        $c = $place_files[$place_path];
                        $c_stripped = &$place_files_stripped[$place_path];
                        if ($wildcard_support) {
                            $c = preg_replace('#<for-each-\w+>#', '*', $c);
                            $c_stripped = preg_replace('#<for-each-\w+>#', '*', $c_stripped);
                        }

                        if (($is_runtime) && ($place != 'fixperms.bat')) {
                            $wildcard_support = false; // Literal comparison of wildcards
                        }

                        $slash = $windows_slashes ? '\\' : '/';
                        $_item = str_replace('/', $slash, $item);
                        $search = $pre . $_item . $post;

                        $there = strpos($c, $search) !== false;
                        if ($there) {
                            $c_stripped = str_replace(trim($search), '', $c_stripped); // So we can check for no alien stuff; trim is because pre and post may overlap with shared spaces (fixperms.sh)
                        } else {
                            if ($wildcard_support) {
                                $search_regexp = preg_quote($pre, '#');
                                $path_components = explode($slash, $_item);
                                foreach ($path_components as $i => $__item) {
                                    if ($i != 0) {
                                        $search_regexp .= preg_quote($slash, '#');
                                    }
                                    $possibilities_for_term = array(
                                        '\*',
                                        preg_quote($__item, '#'),
                                    );
                                    if (($place == 'fixperms.bat') && ($__item == '*')) {
                                        if ($i != count($path_components) - 1) {
                                            $possibilities_for_term[] = '.*'; // For fixperms.bat we cannot have wildcards as path components
                                        } else {
                                            $possibilities_for_term[] = '\*\.\w+'; // For fixperms.bat we are sometimes more specific about file extensions
                                        }
                                    }
                                    if (substr($__item, -strlen('_custom')) == '_custom') {
                                        $possibilities_for_term[] = '\*_custom';
                                    }
                                    $search_regexp .= '(' . implode('|', $possibilities_for_term) . ')';
                                }
                                $search_regexp .= preg_quote($post, '#');
                                $there = (preg_match('#' . $search_regexp . '#', $c) != 0);
                                if ($there) {
                                    $c_stripped = preg_replace('#' . trim($search_regexp) . '#', '', $c_stripped); // So we can check for no alien stuff; trim is because pre and post may overlap with shared spaces (fixperms.sh)
                                }
                            }
                        }
                        $this->assertTrue($there, 'Chmod item is missing from ' . $place . ': ' . $item);
                    }
                }
            }
        }

        // Make sure no alien (old chmod entries that no longer should be there - or ones missing from inst_special.php, potentially)
        foreach ($places as $place_parts) {
            if (count($place_parts) == 6) {
                list($place, $windows_slashes, $wildcard_support, $runtime_too, $pre, $post) = $place_parts;
                $yoyo = array(array($pre, $post));
            } else {
                list($place, $windows_slashes, $wildcard_support, $runtime_too, $pre_dir, $post_dir, $pre_file, $post_file) = $place_parts;
                $yoyo = array(array($pre_dir, $post_dir), array($pre_file, $post_file));
            }

            foreach ($yoyo as $bits) {
                list($_pre, $_post) = $bits;

                $place_path = get_file_base() . '/' . $place;
                if (file_exists($place_path)) {
                    $c = $place_files_stripped[$place_path];
                    $matches = array();
                    $num_matches = preg_match_all('#' . preg_quote($_pre, '#') . '(\w+[/\\\\][\w/\\\\]+)' . preg_quote($_post, '#') . '#', $c, $matches);
                    for ($i = 0; $i < $num_matches; $i++) {
                        $this->assertTrue(false, 'Unexpected remaining path in ' . $place . ': ' . $matches[1][$i]);
                    }
                }
            }
        }
    }
}

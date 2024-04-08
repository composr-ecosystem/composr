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
class _privilege_existence_test_set extends cms_test_case
{
    public function setUp()
    {
        disable_php_memory_limit();
    }

    public function testCode()
    {
        require_code('files2');

        $matches = [];
        $done_privileges = [];
        $done_pages = [];

        $privileges = array_flip(collapse_1d_complexity('the_name', $GLOBALS['SITE_DB']->query_select('privilege_list', ['the_name'])));

        $pages = [];
        $zones = find_all_zones(true);
        foreach ($zones as $zone) {
            $pages += find_all_pages_wrap($zone);
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_CUSTOM_THEMES);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $file_type = get_file_extension($path);

            if ($file_type == 'php') {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

                $num_matches = preg_match_all('#add_privilege\(\'[^\']+\', \'([^\']+)\'#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $privilege = $matches[1][$i];

                    $privileges[$privilege] = true;
                }
            }
        }

        foreach ($files as $path) {
            $file_type = get_file_extension($path);

            if ($file_type == 'php') {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

                $num_matches = preg_match_all('#has_privilege\((get_member\(\)|\$\w+), \'([^\']+)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $privilege = $matches[2][$i];

                    if (isset($done_privileges[$privilege])) {
                        continue;
                    }

                    $this->assertTrue(isset($privileges[$privilege]), 'Missing referenced privilege (.php): ' . $privilege);

                    $done_privileges[$privilege] = true;
                }

                $num_matches = preg_match_all('#has_(actual_)?page_access\((get_member\(\)|\$\w+), \'([^\']+)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $page = $matches[3][$i];

                    if (isset($done_pages[$page])) {
                        continue;
                    }

                    if (get_forum_type() != 'cns') {
                        if (in_array($page, [
                            'topicview',
                            'forumview',
                            'topics',
                            'vforums',
                        ])) {
                            continue;
                        }
                    }

                    $this->assertTrue(isset($pages[$page]), 'Missing referenced page (.php): ' . $page);

                    $done_pages[$page] = true;
                }

                $num_matches = preg_match_all('#get_(page|module)_zone\(\'([^\']+)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $page = $matches[2][$i];

                    if (isset($done_pages[$page])) {
                        continue;
                    }

                    if (get_forum_type() != 'cns') {
                        if (in_array($page, [
                            'topicview',
                            'forumview',
                            'topics',
                            'vforums',
                        ])) {
                            continue;
                        }
                    }

                    $this->assertTrue(isset($pages[$page]), 'Missing referenced page (.php): ' . $page);

                    $done_pages[$page] = true;
                }
            }

            if ($file_type == 'tpl' || $file_type == 'txt') {
                $c = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                $num_matches = preg_match_all('#\{\$HAS_PRIVILEGE,(\w+)\}#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $privilege = $matches[1][$i];

                    if (isset($done_privileges[$privilege])) {
                        continue;
                    }

                    $this->assertTrue(isset($privileges[$privilege]), 'Missing referenced privilege (' . $file_type . '): ' . $privilege);

                    $done_privileges[$privilege] = true;
                }

                $num_matches = preg_match_all('#\{\$HAS_(ACTUAL_)?PAGE_ACCESS,(\w+)\}#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $page = $matches[2][$i];

                    if (isset($done_pages[$page])) {
                        continue;
                    }

                    $this->assertTrue(isset($pages[$page]), 'Missing referenced page (' . $file_type . '): ' . $page);

                    $done_pages[$page] = true;
                }
            }
        }
    }
}

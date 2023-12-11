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
class privilege_existence_test_set extends cms_test_case
{
    public function testCode()
    {
        $matches = array();
        $done_privileges = array();
        $done_pages = array();

        $privileges = array_flip(collapse_1d_complexity('the_name', $GLOBALS['SITE_DB']->query_select('privilege_list', array('the_name'))));

        $pages = array();
        $zones = find_all_zones(true);
        foreach ($zones as $zone) {
            $pages += find_all_pages_wrap($zone);
        }

        require_code('files2');
        $contents = get_directory_contents(get_file_base());

        foreach ($contents as $f) {
            $file_type = substr($f, -4);

            if ($file_type == '.php') {
                $c = file_get_contents(get_file_base() . '/' . $f);

                $num_matches = preg_match_all('#add\_privilege\(\'[^\']+\', \'([^\']+)\'#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $privilege = $matches[1][$i];

                    $privileges[$privilege] = true;
                }
            }
        }

        foreach ($contents as $f) {
            $file_type = substr($f, -4);

            if ($file_type == '.php') {
                $c = file_get_contents(get_file_base() . '/' . $f);

                $num_matches = preg_match_all('#has\_privilege\((get_member\(\)|\$\w+), \'([^\']+)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $privilege = $matches[2][$i];

                    if (isset($done_privileges[$privilege])) {
                        continue;
                    }

                    $this->assertTrue(isset($privileges[$privilege]), 'Missing referenced privilege (.php): ' . $privilege);

                    $done_privileges[$privilege] = true;
                }

                $num_matches = preg_match_all('#has\_(actual\_)?page\_access\((get_member\(\)|\$\w+), \'([^\']+)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $page = $matches[3][$i];

                    if (isset($done_pages[$page])) {
                        continue;
                    }

                    $this->assertTrue(isset($pages[$page]), 'Missing referenced page (.php): ' . $page);

                    $done_pages[$page] = true;
                }

                $num_matches = preg_match_all('#get\_(page|module)\_zone\(\'([^\']+)\'\)#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $page = $matches[2][$i];

                    if (isset($done_pages[$page])) {
                        continue;
                    }

                    $this->assertTrue(isset($pages[$page]), 'Missing referenced page (.php): ' . $page);

                    $done_pages[$page] = true;
                }
            }

            if ($file_type == '.tpl' || $file_type == '.txt') {
                $c = file_get_contents(get_file_base() . '/' . $f);

                $num_matches = preg_match_all('#\{\$HAS\_PRIVILEGE,(\w+)\}#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $privilege = $matches[1][$i];

                    if (isset($done_privileges[$privilege])) {
                        continue;
                    }

                    $this->assertTrue(isset($privileges[$privilege]), 'Missing referenced privilege (' . $file_type . '): ' . $privilege);

                    $done_privileges[$privilege] = true;
                }

                $num_matches = preg_match_all('#\{\$HAS\_(ACTUAL\_)?PAGE\_ACCESS,(\w+)\}#', $c, $matches);
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

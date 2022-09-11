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
class database_unsupported_sql_test_set extends cms_test_case
{
    public function testSQL()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $files = get_directory_contents(get_file_base(), '', IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_NONBUNDLED, true, true, ['php']);
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $num_matches = preg_match_all('#(query|sql).*\'[^\'\n]*(SUM|COUNT|AVG|MIN|MAX)\([\s*.\w]*[^\'\s*.\w][\s*.\w]*\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $this->assertTrue(false, 'Unsupported SQL aggregate syntax (expression within aggregate function) in ' . $path);
            }

            $num_matches = preg_match_all('#(query|sql)[^\'\n]*\'.*(SUM|COUNT|AVG|MIN|MAX)\([^\'()]*.\)\s*[+\-*/]\s*(SUM|COUNT|AVG|MIN|MAX)\([^\'()]*.\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $this->assertTrue(false, 'Unsupported SQL aggregate syntax (expression between aggregate functions) in ' . $path);
            }
        }
    }
}

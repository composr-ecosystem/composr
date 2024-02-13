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
class database_unsupported_sql_test_set extends cms_test_case
{
    public function testSQL()
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_NONBUNDLED, true, true, ['php']);
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $matches = [];

            $num_matches = preg_match_all('#(query|sql).*\'[^\'\n]*(SUM|COUNT|AVG|MIN|MAX)\([\s*.\w]*[^\'\s*.\w][\s*.\w]*\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $this->assertTrue(false, 'Unsupported SQL aggregate syntax (expression within aggregate function) in ' . $path);
            }

            $num_matches = preg_match_all('#(query|sql)[^\'\n]*\'.*(SUM|COUNT|AVG|MIN|MAX)\([^\'()]*.\)\s*[+\-*/]\s*(SUM|COUNT|AVG|MIN|MAX)\([^\'()]*.\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $this->assertTrue(false, 'Unsupported SQL aggregate syntax (expression between aggregate functions) in ' . $path);
            }

            // Exceptions...
            if (in_array($path, [
                'rootkit_detection.php',
                'tracker/bug_sponsorship_list_view_inc.php',
                'tracker/core/authentication_api.php',
                'tracker/core/news_api.php',
                'tracker/vendor/adodb/adodb-php/drivers/adodb-ibase.inc.php',
                'tracker/vendor/adodb/adodb-php/drivers/adodb-postgres64.inc.php',
                'sources/database_repair.php',
                'sources/database/oracle.php',
                'sources/database/shared/sqlserver.php',
                'sources/database/shared/mysql.php',
                'sources/database/postgresql.php',
            ])) {
                continue;
            }

            $num_matches = preg_match_all('#WHERE.*\w+(<>|=)\\\\\'#i', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $this->assertTrue(false, 'SQL equal/not-equal operations have to use db_string_equal_to/db_string_not_equal_to for Oracle, in ' . $path);
            }
        }
    }
}

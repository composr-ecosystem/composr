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

/**
 * Composr test case class (unit testing).
 */
class form_reserved_names_test_set extends cms_test_case
{
    public function testReservedNames()
    {
        require_code('files2');

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        $reserved_names = [ // Also see .eslintrc.json
            'method',
            'action',
            'target',
        ];

        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_NONBUNDLED | IGNORE_FLOATING, true, true, ['php', 'tpl']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $matches = [];
            if (substr($path, -4) == '.php') {
                $does_match = (preg_match('#form_input_(.*)\(.*\'(' . implode('|', $reserved_names) . ')\'#', $c, $matches) != 0);
                $this->assertTrue(!$does_match, 'Reserved field name input (' . @strval($matches[2]) . ', in ' . $path . ')');

                $does_match = (preg_match('#post_param(.*)\(.*\'(' . implode('|', $reserved_names) . ')\'#', $c, $matches) != 0);
                $this->assertTrue(!$does_match, 'Reserved field name read (' . @strval($matches[2]) . ', in ' . $path . ')');
            } elseif (substr($path, -4) == '.tpl') {
                $does_match = (preg_match('#(name|id)="(' . implode('|', $reserved_names) . ')"#', $c, $matches) != 0);
                $this->assertTrue(!$does_match, 'Reserved field name input (' . @strval($matches[2]) . ', in ' . $path . ')');
            }
        }
    }
}

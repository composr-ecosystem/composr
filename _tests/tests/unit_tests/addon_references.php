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
class addon_references_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();

        require_code('files');
        require_code('files2');
        $this->contents = get_directory_contents(get_file_base());
    }

    public function testPHP()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.php') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#addon_installed\(\'([^\']*)\'\)#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $addon = $matches[1][$i];
                    $this->assertTrue(addon_installed($addon), 'Could not find PHP-referenced addon, ' . $addon);
                }
                unset($_c);
            }
        }
    }

    public function testTemplates()
    {
        foreach ($this->contents as $c) {
            if ((substr($c, -4) == '.tpl') && (!should_ignore_file($c, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $_c = file_get_contents(get_file_base() . '/' . $c);
                $matches = array();
                $num_matches = preg_match_all('#\{\$ADDON_INSTALLED,(\w+)\}#', $_c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $addon = $matches[1][$i];
                    $this->assertTrue(addon_installed($addon), 'Could not find template-referenced addon, ' . $addon);
                }
                unset($_c);
            }
        }
    }
}

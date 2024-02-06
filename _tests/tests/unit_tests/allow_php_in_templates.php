<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class allow_php_in_templates_test_set extends cms_test_case
{
    public function testPHPRuns()
    {
        require_code('tempcode_compiler');

        $test = 'a<?' . 'php echo "hello"; ?' . '>b';

        set_value('allow_php_in_templates', '1');
        $eval = static_evaluate_tempcode(template_to_tempcode($test));
        $this->assertTrue($eval == 'ahellob', 'Template PHP evaluation not working when should');

        set_value('allow_php_in_templates', '0');
        $eval = static_evaluate_tempcode(template_to_tempcode($test));
        $this->assertTrue($eval == 'a' . do_lang('NO_PHP_IN_TEMPLATES') . 'b', 'Template PHP evaluation working when should not');
    }
}

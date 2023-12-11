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
class hyperlink_targets_test_set extends cms_test_case
{
    public function testHyperlinkTargets()
    {
        require_code('files2');
        $dirs = array(
            'themes/default/templates',
            'themes/default/templates_custom',
            'lang/' . fallback_lang(),
            'lang_custom/' . fallback_lang(),
        );
        foreach ($dirs as $path) {
            $files = get_directory_contents(get_file_base() . '/' . $path, get_file_base() . '/' . $path);
            foreach ($files as $file) {
                $c = file_get_contents($file);
                $this->assertTrue(strpos($c, ' target="blank"') === false, 'Uses a "blank" target rather than a "_blank" target');
            }
        }
    }
}

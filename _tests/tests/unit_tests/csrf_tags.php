<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class csrf_tags_test_set extends cms_test_case
{
    public function testTemplates()
    {
        $dirs = array(
            get_file_base() . '/themes/default/templates',
            get_file_base() . '/themes/default/templates_custom',
        );
        foreach ($dirs as $dir) {
            $dh = opendir($dir);
            while (($file = readdir($dh)) !== false) {
                if (($file === '.') || ($file === '..')) {
                    continue;
                }

                $c = cms_file_get_contents_safe($dir . '/' . $file); // TODO #3467
                if (strpos($c, '<form') !== false) {
                    if (strpos($c, 'button-hyperlink') !== false) {
                        continue;
                    }

                    if (strpos($c, 'method="get"') !== false) {
                        continue;
                    }

                    if (strpos($c, 'action="#"') !== false) {
                        continue;
                    }

                    $c = preg_replace('#<input[^<>]* type="(button|submit|image)"[^<>]*>#', '', $c);
                    if ((strpos($c, '<input') === false) && (strpos($c, '<select') === false) && (strpos($c, '<textarea') === false)) {
                        continue;
                    }

                    if (in_array($file, array(
                        'INSTALLER_STEP_1.tpl',
                        'INSTALLER_STEP_2.tpl',
                        'INSTALLER_STEP_3.tpl',
                    ))) {
                        continue;
                    }
                    if (preg_match('#^ECOM_.*_VIA_.*#', $file) != 0) {
                        continue;
                    }

                    $this->assertTrue(strpos($c, '{$INSERT_SPAMMER_BLACKHOLE') !== false, $file);
                }
            }
            closedir($dh);
        }
    }
}

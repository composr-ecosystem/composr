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
class csrf_tags_test_set extends cms_test_case
{
    public function testTemplates()
    {
        $dirs = [
            get_file_base() . '/themes/default/templates',
            get_file_base() . '/themes/default/templates_custom',
        ];
        foreach ($dirs as $dir) {
            $dh = opendir($dir);
            while (($file = readdir($dh)) !== false) {
                if (($file === '.') || ($file === '..')) {
                    continue;
                }

                $c = cms_file_get_contents_safe($dir . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
                if (strpos($c, '<form') !== false) {
                    if (strpos($c, 'button-hyperlink') !== false) {
                        continue;
                    }

                    if (strpos($c, 'method="get"') !== false) {
                        continue;
                    }

                    if (strpos($c, 'login-username') !== false) {
                        continue;
                    }

                    if (strpos($c, 'action="#"') !== false) {
                        continue;
                    }

                    $c = preg_replace('#<input[^<>]* type="(button|submit|image)"[^<>]*>#', '', $c);
                    if ((strpos($c, '<input') === false) && (strpos($c, '<select') === false) && (strpos($c, '<textarea') === false)) {
                        continue;
                    }

                    if (in_array($file, [
                        'INSTALLER_STEP_1.tpl',
                        'INSTALLER_STEP_2.tpl',
                        'INSTALLER_STEP_3.tpl',
                        'INSTALLER_STEP_10.tpl',
                        'TEMPCODE_TESTER_SCREEN.tpl',
                    ])) {
                        continue;
                    }
                    if (preg_match('#^ECOM_.*_VIA_.*#', $file) != 0) {
                        continue;
                    }

                    $this->assertTrue(strpos($c, '{$INSERT_FORM_POST_SECURITY') !== false, $file);
                }
            }
            closedir($dh);
        }
    }
}

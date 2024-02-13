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
class tutorials_codebox_test_set extends cms_test_case
{
    public function testTutorialCodeLangSpecified()
    {
        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file[0] == '.') {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                $this->assertTrue(strpos($c, '[code]') === false, 'Has non-specified [code]-tag language in ' . $file);
                $this->assertTrue(strpos($c, '[codebox]') === false, 'Has non-specified [codebox]-tag language in ' . $file);
            }
        }
        closedir($dh);
    }

    public function testTutorialLangConsistency()
    {
        $allowed_langs = [
            'PHP',
            'HTML',
            'CSS',
            'SQL',
            'MySQL',
            'PostgreSQL',
            'tsql',
            'Commandr',
            'Bash',
            'INI',
            'robots',
            'Tempcode',
            'Comcode',
            'JavaScript',
            'XML',
            'BAT',
            'Selectcode',
            'Filtercode',
            'Maths',
            'YAML',
            'htaccess',
            'Page-link',
            'URL',
            'objc',
            'nginx',
            'Diff',

            // Use this if nothing else (or [font="Courier"]...[/font])
            'Text',
        ];

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file[0] == '.') {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                $matches = [];
                $num_matches = preg_match_all('#\[(code|codebox)="([^"]*)"\]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $lang = $matches[2][$i];

                    $this->assertTrue(in_array($lang, $allowed_langs), 'Non-recognised [code]-tag language (' . $lang . ') in ' . $file);
                }
            }
        }
        closedir($dh);
    }
}

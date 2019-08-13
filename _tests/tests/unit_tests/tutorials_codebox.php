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
                $c = file_get_contents($path . '/' . $file);

                $this->assertTrue(strpos($c, '[code]') === false, 'Has non-specified [code]-tag language in ' . $file);
                $this->assertTrue(strpos($c, '[codebox]') === false, 'Has non-specified [codebox]-tag language in ' . $file);
            }
        }
        closedir($dh);
    }

    public function testTutorialLangConsistency()
    {
        $allowed_langs = array(
            'PHP',
            'HTML',
            'CSS',
            'SQL',
            'MySQL',
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
        );

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file[0] == '.') {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                $c = file_get_contents($path . '/' . $file);

                $matches = array();
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

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
class tutorials_codebox_test_set extends cms_test_case
{
    public function testTutorialCodeLangSpecified()
    {
        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if ($f[0] == '.') {
                continue;
            }

            if (substr($f, -4) == '.txt') {
                $contents = file_get_contents($path . '/' . $f);

                $this->assertTrue(strpos($contents, '[code]') === false, 'Has non-specified [code]-tag language in ' . $f);
                $this->assertTrue(strpos($contents, '[codebox]') === false, 'Has non-specified [codebox]-tag language in ' . $f);
            }
        }
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
        while (($f = readdir($dh)) !== false) {
            if ($f[0] == '.') {
                continue;
            }

            if (substr($f, -4) == '.txt') {
                $contents = file_get_contents($path . '/' . $f);

                $matches = array();
                $num_matches = preg_match_all('#\[(code|codebox)="([^"]*)"\]#', $contents, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $lang = $matches[2][$i];

                    $this->assertTrue(in_array($lang, $allowed_langs), 'Non-recognised [code]-tag language (' . $lang . ') in ' . $f);
                }
            }
        }
    }
}

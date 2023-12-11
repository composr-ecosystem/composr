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
class tutorial_title_structure_test_set extends cms_test_case
{
    public function testDuplicateTitles()
    {
        $titles = array();

        $path = get_custom_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.txt') {
                $c = file_get_contents($path . '/' . $f);

                $last_level = 1;

                $matches = array();
                $num_matches = preg_match_all('#\[title sub="[^"]*"\](Composr (Supplementary|Tutorial): )?(.*)(?![/title])\[/title\]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $title = $matches[3][$i];

                    $this->assertTrue(!isset($titles[$title]), 'Duplicated title: ' . $title);

                    $titles[$title] = true;
                }
            }
        }
    }

    public function testTitlesAscendence()
    {
        $path = get_custom_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.txt') {
                $c = file_get_contents($path . '/' . $f);

                $last_level = 1;

                $matches = array();
                $num_matches = preg_match_all('#\[title="(\d+)"\](.*)(?![/title])\[/title\]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $title_level = intval($matches[1][$i]);
                    $title = $matches[2][$i];

                    $this->assertTrue($title_level - $last_level <= 1, 'Incorrect levels for ' . $title . ' (' . integer_format($last_level) . ' to ' . integer_format($title_level) . ') in ' . $f);

                    $last_level = $title_level;
                }
            }
        }
    }

    public function testTitlesNoEmptySections()
    {
        $path = get_custom_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.txt') {
                $c = file_get_contents($path . '/' . $f);

                $matches = array();
                $test = (preg_match('#\[title="(\d+)"\](.*)(?![/title])\[/title\]\s*\[title="\\1"\](.*)(?![/title])\[/title\]#', $c, $matches) == 0);
                $this->assertTrue($test, 'There seems to be an empty title section; likely it\'s misnumering, ' . $f . ', ' . @$matches[2]);
            }
        }
    }
}

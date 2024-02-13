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
class tutorial_title_structure_test_set extends cms_test_case
{
    public function testDuplicateTitles()
    {
        $titles = [];

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                $last_level = 1;

                $matches = [];
                $num_matches = preg_match_all('#\[title sub="[^"]*"\](Composr (Supplementary|Tutorial): )?(.*)(?![/title])\[/title\]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $title = $matches[3][$i];

                    $this->assertTrue(!isset($titles[$title]), 'Duplicated title: ' . $title);

                    $titles[$title] = true;
                }
            }
        }
        closedir($dh);
    }

    public function testTitlesAscendence()
    {
        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                $last_level = 1;

                $matches = [];
                $num_matches = preg_match_all('#\[title="(\d+)"\](.*)(?![/title])\[/title\]#', $c, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $title_level = intval($matches[1][$i]);
                    $title = $matches[2][$i];

                    $this->assertTrue($title_level - $last_level <= 1, 'Incorrect levels for ' . $title . ' (' . integer_format($last_level) . ' to ' . integer_format($title_level) . ') in ' . $file);

                    $last_level = $title_level;
                }
            }
        }
        closedir($dh);
    }

    public function testTitlesNoEmptySections()
    {
        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                $matches = [];
                $test = (preg_match('#\[title="(\d+)"\](.*)(?![/title])\[/title\]\s*\[title="\\1"\](.*)(?![/title])\[/title\]#', $c, $matches) == 0);
                $this->assertTrue($test, 'There seems to be an empty title section; likely it\'s misnumering, ' . $file . ', ' . @strval($matches[2]));
            }
        }
        closedir($dh);
    }
}

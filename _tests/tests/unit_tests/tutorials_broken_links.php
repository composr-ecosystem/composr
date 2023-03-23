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
class tutorials_broken_links_test_set extends cms_test_case
{
    protected $path;
    protected $pages;

    public function setUp()
    {
        parent::setUp();

        $this->path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($this->path);
        $this->pages = [];
        while (($file = readdir($dh)) !== false) {
            if ($file[0] == '.') {
                continue;
            }

            if (substr($file, -4) == '.txt') {
                $this->pages[basename($file, '.txt')] = true;
            }
        }
        closedir($dh);
    }

    public function testSelfLinks()
    {
        foreach (array_keys($this->pages) as $f) {
            $c = cms_file_get_contents_safe($this->path . '/' . $f . '.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

            $this->assertTrue(strpos($c, ':' . $f . '"]') === false, 'Seems to have a self-linking situation in ' . $f);
        }
    }

    public function testLinksFromCode()
    {
        require_code('files2');
        $files = get_directory_contents(get_file_base(), '', IGNORE_ALIEN | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE | IGNORE_FLOATING, true, true, ['php']);
        $files[] = 'install.php';
        foreach ($files as $path) {
            $c = cms_file_get_contents_safe(get_file_base() . '/' . $path);

            $matches = [];
            $num_matches = preg_match_all('#(get_tutorial_url|set_helper_panel_tutorial)\(\'([^\']+)\'\)#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $tutorial = $matches[2][$i];

                if ($tutorial == 'tutorials') {
                    continue;
                }

                $this->assertTrue(isset($this->pages[$tutorial]), 'Code link to a missing tutorial: ' . $tutorial . ' in ' . $path);
            }
        }
    }

    public function testTutorialBrokenLinks()
    {
        foreach (array_keys($this->pages) as $f) {
            $c = cms_file_get_contents_safe($this->path . '/' . $f . '.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

            $matches = [];
            $num_matches = preg_match_all('#\[page="(_SEARCH|_SELF|docs):([^"]+)"\]#', $c, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $page = preg_replace('/#.*$/', '', $matches[2][$i]);

                if ((substr($page, 0, 4) != 'sup_') && (substr($page, 0, 4) != 'tut_') && (!isset($this->pages['sup_' . $page])) && (!isset($this->pages['tut_' . $page]))) {
                    // We don't concern ourselves with non-tutorial links, but we do first check it wasn't a tutorial link with a missing prefix
                    continue;
                }

                $this->assertTrue(isset($this->pages[$page]), 'Bad tutorial link to ' . $page . ' from ' . $f);
            }
        }
    }
}

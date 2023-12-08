<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class tutorials_broken_links_test_set extends cms_test_case
{
    public function setUp()
    {
        $this->path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($this->path);
        $this->pages = array();
        while (($f = readdir($dh)) !== false) {
            if ($f[0] == '.') {
                continue;
            }

            if (substr($f, -4) == '.txt') {
                $this->pages[basename($f, '.txt')] = true;
            }
        }

        parent::setUp();
    }

    public function testSelfLinks()
    {
        foreach (array_keys($this->pages) as $f) {
            $contents = file_get_contents($this->path . '/' . $f . '.txt');

            $this->assertTrue(strpos($contents, ':' . $f . '"]') === false, 'Seems to have a self-linking situation in ' . $f);
        }
    }

    public function testLinksFromCode()
    {
        require_code('files2');
        $files = get_directory_contents(get_file_base());
        foreach ($files as $file) {
            if ((substr($file, -4) == '.php') && (!should_ignore_file($file, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS))) {
                $contents = file_get_contents(get_file_base() . '/' . $file);

                $matches = array();
                $num_matches = preg_match_all('#(get_tutorial_url|set_helper_panel_tutorial)\(\'([^\']+)\'\)#', $contents, $matches);
                for ($i = 0; $i < $num_matches; $i++) {
                    $tutorial = $matches[2][$i];

                    if ($tutorial == 'tutorials') {
                        continue;
                    }

                    $this->assertTrue(isset($this->pages[$tutorial]), 'Code link to a missing tutorial: ' . $tutorial . ' in ' . $file);
                }
            }
        }
    }

    public function testTutorialBrokenLinks()
    {
        foreach (array_keys($this->pages) as $f) {
            $contents = file_get_contents($this->path . '/' . $f . '.txt');

            $matches = array();
            $num_matches = preg_match_all('#\[page="(_SEARCH|_SELF|docs):([^"]+)"\]#', $contents, $matches);
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

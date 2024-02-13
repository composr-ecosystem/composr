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
class tutorials_all_linked_test_set extends cms_test_case
{
    protected $tutorials;

    public function setUp()
    {
        parent::setUp();

        if (in_safe_mode()) {
            $this->assertTrue(false, 'Cannot work in safe mode');
            return;
        }

        require_code('tutorials');

        $_GET['keep_tutorial_test'] = '1';

        $this->tutorials = list_tutorials();
    }

    public function testAllAddons()
    {
        $_addons = find_all_hooks('systems', 'addon_registry');
        foreach ($_addons as $addon_name => $place) {
            if ($place == 'sources') {
                require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($addon_name));
                $ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($addon_name));

                $tutorials = $ob->get_applicable_tutorials();
                $this->assertTrue(!empty($tutorials), 'No tutorial defined for addon: ' . $addon_name);
            }
        }
    }

    public function testAddonLinkage()
    {
        if (in_safe_mode()) {
            return;
        }

        foreach ($this->tutorials as $tutorial_name => $tutorial) {
            if (is_numeric($tutorial_name)) {
                continue;
            }

            if (substr($tutorial_name, 0, 4) == 'sup_') {
                continue;
            }

            foreach ($tutorial['tags'] as $tag) {
                if (cms_mb_strtolower($tag) == $tag) { // Addon tag
                    require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($tag, true));
                    $ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($tag, true));
                    $tutorials = $ob->get_applicable_tutorials();
                    $this->assertTrue(in_array($tutorial_name, $tutorials), $tag . ' addon_registry hook not referring back to ' . $tutorial_name);
                }
            }
        }

        $hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($hooks as $hook => $ob) {
            if (preg_match('#^language_[A-Z]+$#', $hook) != 0) {
                continue;
            }

            $tutorials = $ob->get_applicable_tutorials();
            foreach ($tutorials as $tutorial_name) {
                $tutorial = get_tutorial_metadata($tutorial_name);
                $this->assertTrue(in_array($hook, $tutorial['tags']), $tutorial_name . ' tutorial tag not referring back to addon_registry hook ' . $hook);
            }
        }
    }

    public function testNotSelfLinking()
    {
        if (in_safe_mode()) {
            return;
        }

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.txt') {
                if ($file == 'panel_top.txt') {
                    continue;
                }

                $this->assertTrue(strpos(cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_BOM), '"_SEARCH:' . $file . '"') === false, $file . ' is self linking');
            }
        }
        closedir($dh);
    }

    public function testHasSomePinned()
    {
        if (in_safe_mode()) {
            return;
        }

        $count = 0;
        foreach ($this->tutorials as $tutorial_name => $tutorial) {
            if ($tutorial['pinned']) {
                $count++;
            }
        }

        $desired = 6;
        $this->assertTrue($count == $desired, 'You should pin exactly ' . integer_format($desired) . ' tutorials, you have pinned ' . integer_format($count));
    }

    public function testTagSet()
    {
        if (in_safe_mode()) {
            return;
        }

        $tags = list_tutorial_tags(true);
        foreach ($tags as $tag) {
            $this->assertTrue(find_theme_image(_find_tutorial_image_for_tag($tag), true) != '', 'Tag ' . $tag . ' has no defined image');
        }
    }

    public function testCorrectSeeAlsoTitling()
    {
        if (in_safe_mode()) {
            return;
        }

        $path = get_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if (substr($file, -4) == '.txt') {
                $c = cms_file_get_contents_safe($path . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                if (get_param_integer('full', 0) == 1) {
                    $see_also_pos = 0;
                } else {
                    $see_also_pos = strpos($c, '[title="2"]See also[/title]');
                }

                if ($see_also_pos !== false) {
                    $matches = [];
                    if (get_param_integer('full', 0) == 1) {
                        $regexp = '#^\[page="_SEARCH:(\w+)"\](.*)\[/page\]#';
                    } else {
                        $regexp = '#^ - \[page="_SEARCH:(\w+)"\](.*)\[/page\]#m';
                    }
                    $num_matches = preg_match_all($regexp, $c, $matches);

                    for ($i = 0; $i < $num_matches; $i++) {
                        $page_name = $matches[1][$i];
                        $title = $matches[2][$i];

                        $c2 = cms_file_get_contents_safe($path . '/' . $page_name . '.txt', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
                        $regexp = '\[title sub="[^"]*"\]([^:]*: )?([^\[\]]*)\[/title\]';
                        $matches2 = [];
                        if (preg_match('#' . $regexp . '#', $c2, $matches2) != 0) {
                            $tutorial_title = $matches2[2];
                            $correct = ($tutorial_title == $title);

                            $this->assertTrue($correct, '"' . $page_name . '" linked to with "' . $title . '" but should be "' . $tutorial_title . '" in ' . $file);

                            if (get_param_integer('fix', 0) == 1) {
                                $c = str_replace($title, $tutorial_title, $c);
                                file_put_contents($path . '/' . $file, $c);
                            }
                        } else {
                            $this->assertTrue(false, 'Missing title for ' . $page_name);
                        }
                    }
                }
            }
        }
        closedir($dh);
    }
}

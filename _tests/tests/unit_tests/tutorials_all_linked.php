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
class tutorials_all_linked_test_set extends cms_test_case
{
    public $tutorials;

    public function setUp()
    {
        require_code('tutorials');

        $_GET['keep_tutorial_test'] = '1';

        $this->tutorials = list_tutorials();

        parent::setUp();
    }

    public function testAddonLinkage()
    {
        foreach ($this->tutorials as $tutorial_name => $tutorial) {
            if (is_numeric($tutorial_name)) {
                continue;
            }

            if (substr($tutorial_name, 0, 4) == 'sup_') {
                continue;
            }

            foreach ($tutorial['tags'] as $tag) {
                if (strtolower($tag) == $tag) { // Addon tag
                    require_code('hooks/systems/addon_registry/' . $tag);
                    $ob = object_factory('Hook_addon_registry_' . $tag);
                    $tutorials = $ob->get_applicable_tutorials();
                    $this->assertTrue(in_array($tutorial_name, $tutorials), $tag . ' addon_registry hook not referring back to ' . $tutorial_name);
                }
            }
        }

        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach (array_keys($hooks) as $hook) {
            if (preg_match('#^language\_[A-Z]+$#',  $hook) != 0) {
                continue;
            }

            require_code('hooks/systems/addon_registry/' . $hook);
            $ob = object_factory('Hook_addon_registry_' . $hook);
            $tutorials = $ob->get_applicable_tutorials();
            foreach ($tutorials as $tutorial_name) {
                $tutorial = get_tutorial_metadata($tutorial_name);
                $this->assertTrue(in_array($hook, $tutorial['tags']), $tutorial_name . ' tutorial tag not referring back to addon_registry hook ' . $hook);
            }
        }
    }

    public function testIconLinkage()
    {
        $icons_used = array();
        foreach ($this->tutorials as $tutorial_name => $tutorial) {
            $icons_used[] = $tutorial['icon'];
        }

        /*
        This isn't going to pass. We don't have that many icons.
        $_icons_used = array_count_values($icons_used);
        foreach ($_icons_used as $icon => $count) {
            $this->assertTrue($count <= 3, basename($icon, '.png') . ' tag icon used too much (' . integer_format($count) . ' times), try a more specific tag at the front of the tag list');
        }
        */

        $dh = opendir(get_custom_file_base() . '/themes/default/images_custom/tutorial_icons');
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.png') {
                $icon = basename($f, '.png');
                if (!in_array($icon, array('css', 'javascript', 'php', 'video' /*Very generic ones we aren't using yet*/))) {
                    $this->assertTrue(in_array(find_theme_image('tutorial_icons/' . $icon), $icons_used), $icon . ' icon is not used');
                }
            }
        }
        closedir($dh);
    }

    public function testNotSelfLinking()
    {
        $path = get_custom_file_base() . '/docs/pages/comcode_custom/EN';
        $dh = opendir($path);
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.txt') {
                if ($f == 'panel_top.txt') {
                    continue;
                }

                $this->assertTrue(strpos(file_get_contents($path . '/' . $f), '"_SEARCH:' . $f . '"') === false, $f . ' is self linking');
            }
        }
    }

    public function testHasSomePinned()
    {
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
        $tags = list_tutorial_tags(true);
        foreach ($tags as $tag) {
            $this->assertTrue(find_theme_image('tutorial_icons/' . _find_tutorial_image_for_tag($tag), true) != '', 'Tag ' . $tag . ' has no defined image');
        }
    }
}

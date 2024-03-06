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

// php _tests/index.php __sitemap

/**
 * Composr test case class (unit testing).
 */
class __sitemap_test_set extends cms_test_case
{
    protected $sitemap;
    protected $flattened;
    protected $options;
    protected $meta_gather;

    public function setUp()
    {
        parent::setUp();

        if (!is_cli()) {
            warn_exit('This test should be run on the command line: php _tests/index.php __sitemap.');
        }

        if ($GLOBALS['SITE_DB']->query_select_value('sitemap_cache', 'COUNT(*)') > 3000) {
            $this->assertTrue(false, 'Test will not work on databases with a huge sitemap');
            return;
        }

        disable_php_memory_limit();

        $this->establish_admin_session();

        require_code('sitemap');

        set_option('confluence_subdomain', ''); // Gathering sitemap for Confluence too slow

        $page_link = '';
        $callback = null;
        $valid_node_types = null;
        $child_cutoff = null;
        $max_recurse_depth = null;
        $this->options = SITEMAP_GEN_NONE | SITEMAP_GEN_KEEP_FULL_STRUCTURE | SITEMAP_GEN_MACHINE_SITEMAP;
        $zone = '_SEARCH';
        $this->meta_gather = SITEMAP_GATHER__ALL;

        $this->sitemap = retrieve_sitemap_node($page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $this->options, $zone, $this->meta_gather);
        $this->flattened = $this->flatten_sitemap($this->sitemap);
    }

    protected function flatten_sitemap($sitemap)
    {
        if ($sitemap['page_link'] == 'forum:' || $sitemap['page_link'] == 'buildr:') {
            return [];
        }

        $children = isset($sitemap['children']) ? $sitemap['children'] : null;
        unset($sitemap['children']);
        $ret = [$sitemap['page_link'] => $sitemap];

        if ($children !== null) {
            foreach ($children as $c) {
                $_c = $this->flatten_sitemap($c);
                foreach ($_c as $k => $__c) {
                    if ($k != '') {
                        $this->assertTrue(!isset($ret[$k]), 'Duplicated page: ' . $k);
                    }

                    $ret[$k] = $__c;
                }
            }
        }
        return $ret;
    }

    public function testIsSearchable()
    {
        if (($this->only !== null) && ($this->only != 'testIsSearchable')) {
            return;
        }

        if ($this->sitemap === null) {
            return null;
        }

        $properties_compare = [
            'content_type',
            'content_id',
            'page_link',
            'permissions',
            'privilege_page',
        ];

        foreach ($this->flattened as $page_link => $node) {
            if ($page_link == '') {
                continue;
            }

            $node_searched = retrieve_sitemap_node($page_link, null, null, null, null, $this->options, '_SEARCH', $this->meta_gather);
            $this->assertTrue($node_searched !== null, 'Could not do a search for ' . $page_link);
            if ($node_searched !== null) {
                foreach ($properties_compare as $property) {
                    $node_property = isset($node[$property]) ? $node[$property] : null;
                    $node_searched_property = isset($node_searched[$property]) ? $node_searched[$property] : null;
                    $this->assertTrue($node_property === $node_searched_property, 'Search produced different node for ' . $page_link . ' (comparing ' . $property . '; ' . serialize($node_property) . '; vs ' . serialize($node_searched_property) . ')');
                    if ($node_property !== $node_searched_property) {
                        if ($this->debug) {
                            var_dump($node_property);
                            var_dump($node_searched_property);
                        }
                    }
                }
                if (!isset($node['title'])) {
                    $this->assertTrue(false, 'Null title for ' . $page_link);
                } elseif (!isset($node_searched['title'])) {
                    $this->assertTrue(false, 'Null title on search result for ' . $page_link);
                } else {
                    $a = $node['title']->evaluate();
                    $b = $node_searched['title']->evaluate();
                    $this->assertTrue($a == $b, 'Search produced different node for ' . $page_link . ' (comparing title; ' . serialize($a) . '; vs ' . serialize($b) . ')');
                }
            }
        }
    }

    public function testIsConclusive()
    {
        if (($this->only !== null) && ($this->only != 'testIsConclusive')) {
            return;
        }

        if ($this->sitemap === null) {
            return;
        }

        // Test we have an arbitrary entry-point, just to ensure things are still generating deeply
        $this->assertTrue(isset($this->flattened['adminzone:admin_config:base']));

        // Test we have an arbitrary resource, just to ensure things are still generating deeply
        $this->assertTrue(isset($this->flattened[get_module_zone('calendar') . ':calendar:browse:int_2=1']));
    }

    public function testPageGroupingHelpDocsDefined()
    {
        if (($this->only !== null) && ($this->only != 'testPageGroupingHelpDocsDefined')) {
            return;
        }

        if ($this->sitemap === null) {
            return;
        }

        $applicable_page_groupings = [
            'audit',
            'security',
            'structure',
            'style',
            'setup',
            'tools',
            'cms',
            '',
        ];

        require_code('site');

        $page_groupings = get_page_grouping_links();
        foreach ($page_groupings as $link) {
            if ($link === null) {
                continue;
            }

            if (in_array($link[0], $applicable_page_groupings)) {
                if (($link[0] == '') && (is_array($link[2])) && ((!isset($link[2][1]['type'])) || (!in_array($link[2][1]['type'], $applicable_page_groupings)))) {
                    continue;
                }

                if (!is_object($link[3])) {
                    $this->assertTrue(isset($link[4]), 'Should be Tempcode for ' . serialize($link));
                } else {
                    if (!is_array($link[2])) {
                        continue;
                    }

                    $test = __request_page($link[2][0], $link[2][2]);
                    if ($test === false) {
                        $this->assertTrue(true, 'Cannot locate page ' . $link[2][0]);
                    } else {
                        if (strpos($test[0], '_CUSTOM') === false) {
                            $has_help_defined = isset($link[4]);
                            $this->assertTrue($has_help_defined, 'No help defined for ' . $link[3]->evaluate() . ' (' . $link[2][2] . ':' . $link[2][0] . ')');
                        }
                    }
                }
            }
        }
    }

    public function testHasIcons()
    {
        if (($this->only !== null) && ($this->only != 'testHasIcons')) {
            return;
        }

        if ($this->sitemap === null) {
            return;
        }

        foreach ($this->flattened as $k => $c) {
            if (preg_match('#^\w*:(\w*(:\w*)?)?$#', $k) != 0) {
                if (in_array($k, [ // Exceptions
                    // Would be filtered out by _is_page_omitted_from_sitemap
                    ':sitemap',
                    ':privacy',
                    ':feedback',
                    ':404',
                    ':popup_blockers',
                    ':panel_top',
                    ':panel_bottom',
                    ':panel_left',
                    ':panel_right',
                    'site:panel_left',
                    'site:panel_right',
                    'adminzone:_modsecurity',
                    ':_rules',

                    // Handled specially
                    ':home',
                    'site:userguide_chatcode',
                    'site:userguide_comcode',
                    'site:popup_blockers',
                    ':userguide_chatcode',
                    ':userguide_comcode',
                    ':popup_blockers',
                ])) {
                    continue;
                }

                list($zone, $page) = explode(':', $k);
                $test = _request_page($page, $zone);
                if ($test === false) {
                    continue;
                }

                if ((strpos($test[0], '_CUSTOM') === false) && (!in_array($k, ['adminzone:admin_config:base', ':keymap']))) {
                    $this->assertTrue($c['extra_meta']['image'] != '', 'Missing image for: ' . $k);
                }
            }
        }
    }

    public function testNoOrphans()
    {
        if (($this->only !== null) && ($this->only != 'testNoOrphans')) {
            return;
        }

        if ($this->sitemap === null) {
            return;
        }

        foreach ($this->flattened as $c) {
            $this->assertTrue(!isset($c['is_unexpected_orphan']), 'Not tied in via page grouping ' . $c['title']->evaluate());
        }
    }

    public function testNoIncompleteNodes()
    {
        if (($this->only !== null) && ($this->only != 'testNoIncompleteNodes')) {
            return;
        }

        if ($this->sitemap === null) {
            return;
        }

        $props = [
            'title',
            'content_type',
            'content_id',
            'modifiers',
            'only_on_page',
            'page_link',
            'url',
            'extra_meta',
            'permissions',
            'has_possible_children',
            'sitemap_priority',
            'sitemap_refreshfreq',
        ];
        $props_meta = [
            'description',
            'image',
            'icon',
            'add_time',
            'edit_time',
            'submitter',
            'views',
            'rating',
            'meta_keywords',
            'meta_description',
            'categories',
            'validated',
            'db_row',
        ];

        foreach ($this->flattened as $k => $c) {
            foreach ($props as $prop) {
                $this->assertTrue(array_key_exists($prop, $c), 'Missing property: ' . $prop . ' (for ' . $k . ')');
            }
            if (isset($c['extra_meta'])) {
                foreach ($props_meta as $prop) {
                    $this->assertTrue(array_key_exists($prop, $c['extra_meta']), 'Missing meta property: ' . $prop . ' (for ' . $k . ')');
                }
            }
        }
    }
}

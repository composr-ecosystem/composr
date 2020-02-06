<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

// php _tests/index.php sitemap

/**
 * Composr test case class (unit testing).
 */
class sitemap_test_set extends cms_test_case
{
    protected $sitemap;
    protected $flattened;

    public function setUp()
    {
        parent::setUp();

        if ($GLOBALS['SITE_DB']->query_select_value('sitemap_cache', 'COUNT(*)') > 3000) {
            $this->assertTrue(false, 'Test will not work on databases with a huge sitemap');
            return;
        }

        $this->establish_admin_session();

        require_code('sitemap');

        $page_link = '';
        $callback = null;
        $valid_node_types = null;
        $child_cutoff = null;
        $max_recurse_depth = null;
        $options = SITEMAP_GEN_NONE | SITEMAP_GEN_MACHINE_SITEMAP;
        $zone = '_SEARCH';
        $meta_gather = SITEMAP_GATHER__ALL;

        $this->sitemap = retrieve_sitemap_node($page_link, $callback, $valid_node_types, $child_cutoff, $max_recurse_depth, $options, $zone, $meta_gather);
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

    public function testIsConclusive()
    {
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

                    $test = _request_page($link[2][0], $link[2][2]);
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
                ])) {
                    continue;
                }

                list($zone, $page) = explode(':', $k);
                $test = _request_page($page, $zone);
                if ($test === false) {
                    continue;
                }

                if ((strpos($test[0], '_CUSTOM') === false) && (!in_array($k, ['adminzone:admin_config:base', ':keymap']))) {
                    $this->assertTrue($c['extra_meta']['image'] != '', 'Missing icon for: ' . $k);
                }
            }
        }
    }

    public function testNoOrphans()
    {
        if ($this->sitemap === null) {
            return;
        }

        foreach ($this->flattened as $c) {
            $this->assertTrue(!isset($c['is_unexpected_orphan']), 'Not tied in via page grouping ' . $c['title']->evaluate());
        }
    }

    public function testNoIncompleteNodes()
    {
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

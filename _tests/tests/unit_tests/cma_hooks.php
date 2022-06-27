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
class cma_hooks_test_set extends cms_test_case
{
    public $all_cma_info = array();
    public $all_keys = array();

    public function setUp()
    {
        parent::setUp();

        require_code('content');

        $this->all_keys = array();

        $cma_hooks = find_all_hooks('systems', 'content_meta_aware') + find_all_hooks('systems', 'resource_meta_aware');
        foreach (array_keys($cma_hooks) as $content_type) {
            $cma_ob = get_content_object($content_type);
            $cma_info = $cma_ob->info();

            if ($cma_info !== null) {
                $this->all_cma_info[$content_type] = $cma_info;
                $this->all_keys = array_unique(array_merge($this->all_keys, array_keys($cma_info)));
            }
        }
    }

    public function testAllPropertiesDefined()
    {
        // Too specific to want to define it all for each hook
        $may_be_unset_properties = array(
            'edit_page_link_field',
            'edit_page_link_pattern_post',
            'title_field_post',
            'filtercode',
            'filtercode_protected_fields',
            'parent_category_field__resource_fs',
            'title_field_dereference__resource_fs',
            'title_field_supports_comcode',
            'title_field__resource_fs',
            'where',
        );

        foreach ($this->all_cma_info as $content_type => $cma_info) {
            foreach ($this->all_keys as $key) {
                if (in_array($key, $may_be_unset_properties)) {
                    continue;
                }

                $this->assertTrue(array_key_exists($key, $cma_info), $key . ' not defined for ' . $content_type);
            }
        }
    }
}

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
class tracker_categories_test_set extends cms_test_case
{
    public function testHasAddons()
    {
        $brand_base_url = get_brand_base_url();
        $post = [];
        $_categories = http_get_contents($brand_base_url . '/data/endpoint.php/cms_homesite/tracker_categories', ['convert_to_internal_encoding' => true, 'ua' => 'Composr Test Platform']);
        $categories = json_decode($_categories, true);
        $addons = find_all_hooks('systems', 'addon_registry');
        foreach ($addons as $addon_name => $place) {
            if ($place == 'sources') {
                $this->assertTrue(in_array($addon_name, $categories['response_data']), $addon_name);
            }
        }
    }

    public function testNoUnknownAddons()
    {
        $brand_base_url = get_brand_base_url();
        $_categories = http_get_contents($brand_base_url . '/data/endpoint.php/cms_homesite/tracker_categories', ['convert_to_internal_encoding' => true, 'ua' => 'Composr Test Platform']);
        $categories = json_decode($_categories, true);
        $addons = find_all_hooks('systems', 'addon_registry');
        foreach ($categories['response_data'] as $category) {
            if (cms_strtolower_ascii($category) != $category) {
                continue; // Only lower case must correspond to addons
            }

            $this->assertTrue(array_key_exists($category, $addons), $category);
        }
    }
}

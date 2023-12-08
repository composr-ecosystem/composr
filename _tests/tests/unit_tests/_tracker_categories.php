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
class _tracker_categories_test_set extends cms_test_case
{
    public function testHasAddons()
    {
        $brand_base_url = get_brand_base_url();
        $post = array();
        $_categories = http_download_file($brand_base_url . '/data_custom/composr_homesite_web_service.php?call=get_tracker_categories', null, true, false, 'Composr Test Platform', $post);
        $categories = json_decode($_categories, true);
        $addons = find_all_hooks('systems', 'addon_registry');
        foreach ($addons as $addon => $place) {
            if ($place == 'sources') {
                if ($addon == 'cns_reported_posts' || $addon == 'staff_messaging') { // LEGACY
                    continue;
                }

                $this->assertTrue(in_array($addon, $categories), $addon);
            }
        }
    }

    public function testNoUnknownAddons()
    {
        $brand_base_url = get_brand_base_url();
        $post = array();
        $_categories = http_download_file($brand_base_url . '/data_custom/composr_homesite_web_service.php?call=get_tracker_categories', null, true, false, 'Composr Test Platform', $post);
        $categories = json_decode($_categories, true);
        $addons = find_all_hooks('systems', 'addon_registry');
        foreach ($categories as $category) {
            if (strtolower($category) != $category) {
                continue; // Only lower case must correspond to addons
            }

            $this->assertTrue(array_key_exists($category, $addons), $category);
        }
    }
}

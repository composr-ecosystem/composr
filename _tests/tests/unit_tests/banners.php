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
class banners_test_set extends cms_test_case
{
    public $banner_type;
    public $banner_name;
    public $banner_url;
    public $title_text;
    public $caption;

    public function setUp()
    {
        parent::setUp();

        require_code('banners');
        require_code('banners2');

        set_option('admin_banners', '1');

        $this->banner_type = 'test_banner_type';

        $this->banner_name = 'test_banner';
        $this->banner_url = get_brand_base_url() . '/themes/default/images/EN/logo/standalone_logo.png';
        $this->title_text = 'Good morning';
        $this->caption = 'This is a sample banner';

        // Cleanup possible old failed test
        if ($GLOBALS['SITE_DB']->query_select_value_if_there('banner_types', 'id', array('id' => $this->banner_type)) !== null) {
            delete_banner_type($this->banner_type);
        }
        if ($GLOBALS['SITE_DB']->query_select_value_if_there('banners', 'name', array('name' => $this->banner_name)) !== null) {
            delete_banner($this->banner_name);
        }

        // Add banner type
        add_banner_type($this->banner_type, 0, 550, 115, 100, 1);
        add_banner($this->banner_name, $this->banner_url, $this->title_text, $this->caption, '', 10, get_brand_base_url(), 3, 'Test notes', BANNER_PERMANENT, 1329153480, get_member(), 1, $this->banner_type);
    }

    public function testAddedBannerType()
    {
        // Test the banner was actually created
        $width = $GLOBALS['SITE_DB']->query_select_value('banner_types', 't_image_width', array('id' => $this->banner_type));
        $this->assertTrue($width == 550);
    }

    public function testEditBannerType()
    {
        // Test the banner type details modification
        edit_banner_type($this->banner_type, $this->banner_type, 0, 550, 115, 200, 0);

        // Test the width is updated to 200 for the banner "Welcome"
        $saved_max_file_size = $GLOBALS['SITE_DB']->query_select_value('banner_types', 't_max_file_size', array('id' => $this->banner_type));
        $this->assertTrue($saved_max_file_size == 200);
    }

    public function testAddedBanner()
    {
        // Make sure the banner is created with given name
        $saved_url = $GLOBALS['SITE_DB']->query_select_value('banners', 'img_url', array('name' => $this->banner_name));
        $this->assertTrue($saved_url == $this->banner_url);
    }

    public function testComcodePresence()
    {
        $comcode = 'This is some [b]sample Comcode[/b], ' . $this->title_text . '.';
        $eval = static_evaluate_tempcode(comcode_to_tempcode($comcode));
        $this->assertTrue(strpos($eval, $this->caption) !== false);
    }

    public function tearDown()
    {
        delete_banner_type($this->banner_type);
        delete_banner($this->banner_name);

        parent::tearDown();
    }
}

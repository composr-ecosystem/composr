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
class gallery_images_test_set extends cms_test_case
{
    protected $image_id;

    public function setUp()
    {
        parent::setUp();

        require_code('galleries');
        require_code('galleries2');

        $this->image_id = add_image('', '', '', 'images/test.jpg', 0, 0, 0, 0, '', null, null, null, 0, null);

        $this->assertTrue('images/test.jpg' == $GLOBALS['SITE_DB']->query_select_value('images', 'url', ['id' => $this->image_id]));
    }

    public function testEditGalleryImage()
    {
        edit_image($this->image_id, '', '', '', 'images/sample.jpg', 0, 0, 0, 0, '', '', '');

        $this->assertTrue('images/sample.jpg' == $GLOBALS['SITE_DB']->query_select_value('images', 'url', ['id' => $this->image_id]));
    }

    public function tearDown()
    {
        delete_image($this->image_id, false);

        parent::tearDown();
    }
}

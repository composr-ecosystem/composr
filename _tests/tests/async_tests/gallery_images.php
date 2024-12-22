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
class gallery_images_test_set extends cms_test_case
{
    protected $image_id;

    public function setUp()
    {
        parent::setUp();

        require_code('galleries');
        require_code('galleries2');

        $this->image_id = add_image('', '', '', 'themes/default/images/no_image.png', 0, 0, 0, 0, '', null, null, null, 0, null);

        // The software copies local images not in the galleries upload folder to the galleries upload folder
        $actual_path = $GLOBALS['SITE_DB']->query_select_value('images', 'url', ['id' => $this->image_id]);
        $this->assertTrue('uploads/galleries/no_image.png' == $actual_path, 'Wrong path: Got ' . $actual_path);
    }

    public function testEditGalleryImage()
    {
        edit_image($this->image_id, '', '', '', 'themes/default/images/blank.gif', 0, 0, 0, 0, '', '', '');

        // The software copies local images not in the galleries upload folder to the galleries upload folder
        $actual_path = $GLOBALS['SITE_DB']->query_select_value('images', 'url', ['id' => $this->image_id]);
        $this->assertTrue('uploads/galleries/blank.gif' == $actual_path, 'Wrong path: Got ' . $actual_path);
    }

    public function tearDown()
    {
        delete_image($this->image_id, false);

        parent::tearDown();
    }
}

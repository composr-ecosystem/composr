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
class gallery_images_test_set extends cms_test_case
{
    public $image_id;

    public function setUp()
    {
        parent::setUp();

        require_code('galleries');
        require_code('galleries2');

        $this->image_id = add_image('', '', '', 'http://www.msn.com', 'images/test.jpg', 0, 0, 0, 0, '', null, null, null, 0, null);

        $this->assertTrue('http://www.msn.com' == $GLOBALS['SITE_DB']->query_select_value('images', 'url', array('id' => $this->image_id)));
    }

    public function testEditGalleryImage()
    {
        edit_image($this->image_id, '', '', '', 'http://www.google.com', 'images/sample.jpg', 0, 0, 0, 0, '', '', '');

        $this->assertTrue('http://www.google.com' == $GLOBALS['SITE_DB']->query_select_value('images', 'url', array('id' => $this->image_id)));
    }

    public function tearDown()
    {
        delete_image($this->image_id, false);
        parent::tearDown();
    }
}

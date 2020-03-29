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

/**
 * Composr test case class (unit testing).
 */
class cloudinary_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        // Do not use these details yourself
        set_option('cloudinary_cloud_name', 'ocportal');
        set_option('cloudinary_api_key', '838118582411864');
        set_option('cloudinary_api_secret', '3r6jPysJqtpAajSQqcgrk0DDiOo');
        set_option('cloudinary_test_mode', '0');
        set_option('cloudinary_transfer_directories', '_tests/assets/images');
    }

    public function testCloudinaryTransfer()
    {
        if (!addon_installed('cloudinary')) {
            $this->assertTrue(false, 'The cloudinary addon must be installed for this test to run');
            return;
        }

        require_code('uploads');
        require_code('hooks/systems/cdn_transfer/cloudinary');
        $ob = new Hook_cdn_transfer_cloudinary();
        $id = null;
        $url = $ob->transfer_upload(get_file_base() . '/_tests/assets/images/exifrotated.jpg', '_tests/assets/images', 'exifrotated.jpg', 0, false, $id);
        $this->assertTrue(strpos($url, 'res.cloudinary.com') !== false);
        $ob->delete_image_upload($id);
    }
}

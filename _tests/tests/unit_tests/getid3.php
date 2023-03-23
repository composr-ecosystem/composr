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
class getid3_test_set extends cms_test_case
{
    public function testGetId3()
    {
        if (!addon_installed('getid3')) {
            $this->assertTrue(false, 'The getid3 addon must be installed for this test to run');
            return;
        }

        require_code('galleries2');
        $result = get_video_details_from_file(get_file_base() . '/_tests/assets/images/crop_both_32x18_16x9.png', 'crop_both_32x18_16x9.png');
        $this->assertTrue($result[0] == 32);
        $this->assertTrue($result[1] == 18);
    }
}

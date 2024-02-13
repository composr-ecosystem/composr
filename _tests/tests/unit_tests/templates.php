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
class templates_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('themes2');
    }

    public function testTemplateParameterDetectionViaPreview()
    {
        $parameters = find_template_parameters('templates/DOWNLOAD_BOX.tpl');
        $this->assertTrue(in_array('EDIT_DATE_RAW', $parameters)); // Template scan would not find EDIT_DATE_RAW, as not used by default
    }

    public function testTemplateParameterDetectionViaScan()
    {
        $parameters = find_template_parameters('templates/ACTIVITY_FEED_ACTIVITY.tpl'); // Has no preview, so will have to do a template scan
        $this->assertTrue(in_array('TIMESTAMP', $parameters));
    }

    public function testTemplateGUIDs()
    {
        $guids = find_template_guids('templates/INDEX_SCREEN.tpl');
        $this->assertTrue(count($guids) > 1);
    }
}

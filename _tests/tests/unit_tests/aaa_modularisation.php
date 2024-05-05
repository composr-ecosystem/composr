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

// "aaa" as we want it to run first, else files not correctly modularised won't be tested.
// You can also run the admin_modularisation module to fix some issues via a UI (and to organise addon registry files alphabetically); you should still run this test though.
// If this test is edited, you may need to also edit the code in sources_custom/modularisation.

/**
 * Composr test case class (unit testing).
 */
class aaa_modularisation_test_set extends cms_test_case
{
    protected $stricter_checking = false; // Set to true to check against non-bundled and third-party files (false positives expected; review carefully!)

    public function setUp()
    {
        parent::setUp();

        if (!addon_installed('cms_release_build')) {
            $this->assertTrue(false, 'This test requires the cms_release_build addon to be installed.');
            return;
        }

        $message = 'You can run the modularisation tool in the admin_modularisation module (under Admin Zone > Tools) to fix most of these issues automatically.';
        $this->dump($message, 'INFO:');

        require_lang('cms_release_build');
        require_code('modularisation');
    }

    public function testModularisation()
    {
        if (!addon_installed('cms_release_build')) {
            $this->assertTrue(false, 'This test requires the cms_release_build addon to be installed.');
            return;
        }

        $results = scan_modularisation(false, $this->stricter_checking);
        foreach ($results as $result) {
            list($issue, $file, $addon, $info) = $result;
            $this->assertTrue(false, $file . ' (' . $addon . '): ' . do_lang($issue));
        }
    }
}

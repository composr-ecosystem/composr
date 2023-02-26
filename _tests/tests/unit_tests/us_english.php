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
class us_english_test_set extends cms_test_case
{
    public function testUSEnglish()
    {
        // Test British English
        set_option('yeehaw', '0');
        require_code('caches3');
        erase_cached_language();
        $this->clear_caches();
        $this->assertTrue(do_lang('COLOUR') == 'Colour', 'Failed with ' . do_lang('COLOUR') . ', expected British English');

        // Test US English
        set_option('yeehaw', '1');
        require_code('caches3');
        erase_cached_language();
        $this->clear_caches();
        $this->assertTrue(do_lang('COLOUR') == 'Color', 'Failed with ' . do_lang('COLOUR') . ', expected US English');
    }

    protected function clear_caches()
    {
        global $ALLOW_DOUBLE_DECACHE;
        $ALLOW_DOUBLE_DECACHE = true;

        // Flush main caches
        require_code('caches3');
        erase_persistent_cache();
        erase_cached_language();
    }
}

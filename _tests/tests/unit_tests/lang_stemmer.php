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
class lang_stemmer_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('lang_stemmer_EN');
    }

    public function testStemmer()
    {
        $this->assertTrue(Stemmer_EN::stem('testing') == 'test');
    }
}

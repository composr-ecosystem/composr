<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

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
class seo_tracking_test_set extends cms_test_case
{
    public function testAlexa()
    {
        require_code('stats');

        $alexa = get_alexa_rank('http://yahoo.com');
        list($rank, $links) = $alexa;
        $this->assertTrue($rank !== null);
        //$this->assertTrue($links !== null); Alexa seem not to provide anymore
    }
}

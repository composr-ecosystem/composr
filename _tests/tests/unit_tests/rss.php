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
class rss_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('rss');
    }

    public function testRSS()
    {
        $rssfeed = get_file_base() . '/_tests/tests/testrss.cms';
        $atomfeed = get_file_base() . '/_tests/tests/testatom.cms';
        $rss = new CMS_RSS($rssfeed, true);
        $atom = new CMS_RSS($atomfeed, true);
        $rsstitle = $rss->gleamed_items[0]['title'];
        $atomtitle = $atom->gleamed_items[0]['title'];
        $this->assertTrue('Item Example' == $rsstitle);
        $this->assertTrue('Atom-Powered Robots Run Amok' == $atomtitle);
    }
}

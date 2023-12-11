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
class authors_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('authors');

        add_author('author1', 'www.google.com', 3, 'happy', 'play', '', '');

        add_author('author2', 'www.yahoo.com', 3, 'welcome', 'drama', '', '');

        $this->assertTrue('author1' == $GLOBALS['SITE_DB']->query_select_value('authors', 'author', array('author' => 'author1')));
    }

    public function testMergeauthors()
    {
        merge_authors('author1', 'author2');
    }

    public function tearDown()
    {
        delete_author('author2');
        parent::tearDown();
    }
}

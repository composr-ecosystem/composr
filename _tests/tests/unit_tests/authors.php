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
class authors_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('authors');

        add_author('author1', 'https://duckduckgo.com/', 3, 'Happy', 'Play', '', '');

        add_author('author2', 'http://www.yahoo.com/', 3, 'Welcome', 'Drama', '', '');

        $this->assertTrue('author1' == $GLOBALS['SITE_DB']->query_select_value('authors', 'author', ['author' => 'author1']));
    }

    public function testMergeAuthors()
    {
        merge_authors('author1', 'author2');
    }

    public function tearDown()
    {
        delete_author('author2');

        parent::tearDown();
    }
}

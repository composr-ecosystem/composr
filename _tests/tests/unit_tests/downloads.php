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
class downloads_test_set extends cms_test_case
{
    public $dwn_id;

    public function setUp()
    {
        parent::setUp();

        $GLOBALS['NO_QUERY_LIMIT'] = true;

        require_code('downloads');
        require_code('downloads2');

        $this->dwn_id = add_download(db_get_first_id(), '111', 'http://www.google.com', 'Testing download', 'sujith', '', 0, 1, 1, 1, 0, '', 'apple.jpeg', 110, 0, 0, null, null, 0, 0, null, null, null);

        $this->assertTrue('http://www.google.com' == $GLOBALS['SITE_DB']->query_select_value('download_downloads', 'url', array('id' => $this->dwn_id)));
    }

    public function testEditDownloads()
    {
        edit_download($this->dwn_id, db_get_first_id(), '222', 'http://www.yahoo.com', 'edited download', 'sujith', '', 0, 0, 1, 1, 1, 0, '', 'fruit.jpeg', 210, 0, 0, null, '', '');

        $this->assertTrue('http://www.yahoo.com' == $GLOBALS['SITE_DB']->query_select_value('download_downloads', 'url', array('id' => $this->dwn_id)));
    }

    public function tearDown()
    {
        delete_download($this->dwn_id, false);
        parent::tearDown();
    }
}

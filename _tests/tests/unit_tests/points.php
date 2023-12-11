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
class points_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_once(get_file_base() . "/_tests/simpletest/browser.php");
    }

    /*

    All these tests are poor - the set up needed is not done and they are too sensitive to version/language changes!

    //   Test #88
    function testPoints()
    {
        $browser = new SimpleBrowser();
        $browser->get(get_base_url() . '/site/index.php?page=points');

        $browser->setField('login_username', 'test');
        $browser->setField('password', 'welcome');
        $browser->clickSubmit('Login');

        $browser->setField('username', 'abcxyz123');
        $page1 = $browser->clickSubmitById('submit_button');

        $browser->get(get_base_url() . '/site/index.php?page=points');
        $browser->setField('username', '123abcxyz');
        $page2 = $browser->clickSubmitById('submit_button');

        $this->assertTrue($page1 == $page2);
    }

    //   Test #96
    function testProfiles()
    {
        $browser = new SimpleBrowser();
        $browser->get(get_base_url() . '/site/index.php?page=points');

        $browser->setField('login_username', 'test');
        $browser->setField('password', 'welcome');
        $browser->clickSubmit('Login');

        $browser->setField('username', '*');
        $browser->clickSubmitById('submit_button');

        $browser->click('admin');
        $title1 = $browser->getTitle();

        $browser->back();

        $browser->click('test');
        $title2 = $browser->getTitle();

        $this->assertTrue($title1 == "Member profile of admin &ndash; Composr 4.2" && $title2 == "Member profile of test &ndash; Composr 4.2");
    }

    //   Test #116
    function testBanner()
    {
        $browser = new SimpleBrowser();
        $browser->get(get_base_url() . '/site/index.php?page=pointstore&type=browse');
        $title1 = $browser->getTitle();
        $this->assertTrue($title1 == "Login &ndash; Composr 4.2");
    }

    //   Test #132
    function testAddbanner_error()
    {
        $browser = new SimpleBrowser();
        $browser->get(get_base_url() . '/site/index.php?page=pointstore&type=bannerinfo&id=banners');

        $browser->setField('login_username', 'test');
        $browser->setField('password', 'welcome');
        $browser->clickSubmit('Login');

        $browser->click('Activate banner');

        $browser->setField('name', 'Search');
        $browser->setField('site_url', 'http://www.google.com/');
        $browser->setField('notes', 'Just notes');
        $browser->setField('file_old', 'log.txt');
        $page = $browser->clickSubmit('Add banner');

        $this->assertTrue(preg_match("/Unfortunately you do not have enough points to be able to afford this./", $page));
    }

    //   Test #1149
    function testAddEventError()
    {
        $browser = new SimpleBrowser();
        echo $browser->get(get_base_url() . '/site/index.php?page=calendar');

        //$browser->setField('login_username', 'test');
        //$browser->setField('password', 'welcome');
        //$browser->clickSubmit('Login');

        //echo $browser->get(get_base_url().'/site/index.php?page=calendar');

        //$browser->clickImage('Add event');
    }
    */
}

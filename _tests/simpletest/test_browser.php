<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license		http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright	ocProducts Ltd
 * @package		simpletesting
 */
require_once('unit_tester.php');
require_once('reporter.php');
require_once('browser.php');

class test_browser extends UnitTestCase
{
	function testCookie()
	{
		$new_brswr=new SimpleBrowser();
		$new_brswr->setCookie('test', 'Nothing', false, '/', false);
		$cur_cookie=$new_brswr->getCookieValue(false, '/', 'test');
		$this->assertEqual('Nothing',$cur_cookie);
	}

	function testPage()
	{
		$browser1=new SimpleBrowser();
		$old_page=$browser1->get('http://www.google.com/');
		$old_title=$browser1->getTitle();

		$browser2=new SimpleBrowser();
		$browser2->get('http://www.google.com/');
		$new_title=$browser2->getTitle();
		$this->assertEqual($old_title,$new_title);
	}

	function testBack()
	{
		$browser4=new SimpleBrowser();
		$browser4->get("http://www.google.com/");
		$browser4->get("http://www.yahoo.com/");
		$browser4->back();
		$browser4->getTitle();
		$this->assertEqual($browser4->getTitle(),'Google');
	}
}

$test=new test_browser();
$test->run(new HtmlReporter());

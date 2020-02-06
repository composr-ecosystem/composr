<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license		http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright	ocProducts Ltd
 * @package		testing_platform
 */

require_once('unit_tester.php');
require_once('reporter.php');

class TestOfsample extends UnitTestCase
{
	function testSample() {
		require_once('browser.php');
		$browser = new SimpleBrowser();
		$browser->get('http://www.google.com/');
		$browser->setField('q', 'php');
		$browser->click('reporting bugs');
		$page=$browser->clickSubmitByName('btnG');
		$this->assertTrue($page);
	}


	function testHtml()
	{
		$expectations=array(" - foo"=>"<ul><li>foo</li></ul>");
		foreach ($expectations as $comcode=>$html)
		{
			$actual=comcode_to_tempcode($comcode);
            $this->assertTrue(preg_replace('#\s#','',$html)==preg_replace('#\s#','',$actual->evaluate()));
		}
	}
}

$browser=new TestOfsample();
$browser->run(new HtmlReporter());

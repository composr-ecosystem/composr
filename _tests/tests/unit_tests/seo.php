<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

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
class seo_test_set extends cms_test_case
{
    public function testKeywordGeneration()
    {
        require_code('seo2');

        // Test coverage for many cases:
        //  words differing only in case
        //  Proper Nouns
        //  apostrophes
        //  hyphenation
        //  no word repetition
        //  no stop words
        //  first word detected
        list($keywords) = _seo_meta_find_data(array('hello Mr Tester this Is a world-renowned luxorious test. Epic epic testing, it shan\'t fail.'), '');
        $this->assertTrue($keywords == 'hello,Mr Tester,world-renowned,luxorious,epic,testing,shan\'t,fail', 'Got: ' . $keywords);

        // Test last word detected
        list($keywords) = _seo_meta_find_data(array('Epic'), '');
        $this->assertTrue($keywords == 'Epic', 'Got: ' . $keywords);

        // Test unicode too; also capitalised stop words still stripped
        $emoji = chr(hexdec('F0')) . chr(hexdec('9F')) . chr(hexdec('98')) . chr(hexdec('81'));
        list($keywords) = _seo_meta_find_data(array('This is epic' . $emoji), '');
        $this->assertTrue($keywords == 'epic', 'Got: ' . $keywords);

        // Test filtering
        list($keywords) = _seo_meta_find_data(array('Hello [attachment]new_1[/attachment] [media]uploads/downloads/example.png[/media] [b]World[/b] [Example]'), '');
        $this->assertTrue($keywords == 'Hello,World,Example', 'Got: ' . $keywords);
    }
}

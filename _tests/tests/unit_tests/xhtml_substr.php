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
class xhtml_substr_test_set extends cms_test_case
{
    public function setUp()
    {
        require_code('xhtml');
    }

    public function testMisc1()
    {
        $this->assertTrue(xhtml_substr('test', 0, null) == 'test');
    }

    public function testMisc2()
    {
        $this->assertTrue(xhtml_substr('test', 0, 4) == 'test');
    }

    public function testMisc3()
    {
        $this->assertTrue(xhtml_substr('test', 0, 3) == 'tes');
    }

    public function testMisc4()
    {
        $this->assertTrue(xhtml_substr('test', 1, 3) == 'est');
    }

    public function testMisc5()
    {
        $this->assertTrue(xhtml_substr('test', 1, 2) == 'es');
    }

    public function testMisc6()
    {
        $this->assertTrue(xhtml_substr('test', -3) == 'est');
    }

    public function testMisc7()
    {
        $this->assertTrue(xhtml_substr('test', -2) == 'st');
    }

    public function testMisc8()
    {
        $this->assertTrue(xhtml_substr('<i>test</i>', 0, null) == '<i>test</i>');
    }

    public function testMisc9()
    {
        $this->assertTrue(xhtml_substr('<i>test</i>', 0, 4) == '<i>test</i>');
    }

    public function testMisc10()
    {
        $this->assertTrue(xhtml_substr('<i>test</i>', 0, 3) == '<i>tes</i>');
    }

    public function testMisc11()
    {
        $this->assertTrue(xhtml_substr('<i>test</i>', 1, 3) == '<i>est</i>');
    }

    public function testMisc12()
    {
        $this->assertTrue(xhtml_substr('<i>test</i>', 1, 2) == '<i>es</i>');
    }

    public function testMisc13()
    {
        $this->assertTrue(xhtml_substr('<i>test</i>', -3) == '<i>est</i>');
    }

    public function testMisc14()
    {
        $this->assertTrue(xhtml_substr('<i>test</i>', -2) == '<i>st</i>');
    }

    public function testMisc15()
    {
        $this->assertTrue(xhtml_substr('<a><br /><x><i foo="bar">test</i>', -2) == '<a><x><i foo="bar">st</i></x></a>');
    }

    public function testGrammar1()
    {
        $this->assertTrue(xhtml_substr('At least complete the first sentence for me. Second sentence that goes on and on and on so far as to block paragraph completion.', 0, 33, false, false, 0.4) == 'At least complete the first sentence for me.');
    }

    public function testGrammar2()
    {
        $this->assertTrue(xhtml_substr('<p>At least complete the first paragraph for me. Second sentence.</p><p>Next paragraph.</p>', 0, 50, false, false, 0.4) == '<p>At least complete the first paragraph for me. Second sentence.</p>');
    }

    public function testGrammar3()
    {
        $this->assertTrue(xhtml_substr('At least complete any open words. Second sentence that goes on and on and on so far as to block paragraph completion.', 0, 10, false, false, 0.4) == 'At least complete');
    }

    public function testSimple()
    {
        $before = '<div>foobar</div>';
        $after = xhtml_substr($before, 0, 3, false, false, 0.0);
        $expected = '<div>foo</div>';
        $this->assertTrue($after == $expected);
    }

    public function testWords1()
    {
        $before = '<div>foobar</div>';
        $after = xhtml_substr($before, 0, 3, false, false, 1.5);
        $expected = '<div>foobar</div>';
        $this->assertTrue($after == $expected);
    }

    public function testWords2()
    {
        $before = '<div>foobar</div><div>myfoo</div>';
        $after = xhtml_substr($before, 0, 7, false, false, 0.0);
        $expected = '<div>foobar</div><div>m</div>';
        $this->assertTrue($after == $expected);
    }

    public function testImage_1()
    {
        $before = '<a href="www.google.com">My</a><div>foobar<img alt = "kevin" src="' . get_base_url() . '/themes/default/images/cns_emoticons/cheeky.png" />afterfoo </div>';
        $after = xhtml_substr($before, 0, 3, false, false, 0.0);
        $expected = '<a href="www.google.com">My</a><div>f</div>';
        $this->assertTrue($after == $expected);
    }

    public function testImage_2()
    {
        $before = '<a href="www.google.com">My</a><div>foobar<img alt = "kevin" src="' . get_base_url() . '/themes/default/images/cns_emoticons/cheeky.png" />afterfoo </div>';
        $after = xhtml_substr($before, 0, 2, false, false, 0.0);
        $expected = '<a href="www.google.com">My</a>';
        $this->assertTrue($after == $expected);
    }

    public function testImage_3()
    {
        $before = '<a href="www.google.com">My</a><div>foobar<img alt = "kevin" src="' . get_base_url() . '/themes/default/images/cns_emoticons/cheeky.png" />afterfoo </div>';
        $after = xhtml_substr($before, 0, 12, false, false, 0.0);
        $expected = '<a href="www.google.com">My</a><div>foobar<img alt = "kevin" src="' . get_base_url() . '/themes/default/images/cns_emoticons/cheeky.png" />aft</div>';
        $this->assertTrue($after == $expected);
    }

    public function testImage_4()
    {
        $before = '<a href="www.google.com">My</a><div>foobar<img alt = "kevin" src="' . get_base_url() . '/themes/default/images/cns_emoticons/cheeky.pngthumb_nail.jpg" />afterfoo </div>';
        $after = xhtml_substr($before, 0, 12, false, false, 0.0);
        $expected = '<a href="www.google.com">My</a><div>foobar<img alt = "kevin" src="' . get_base_url() . '/themes/default/images/cns_emoticons/cheeky.pngthumb_nail.jpg" /></div>';
        $this->assertTrue($after == $expected);
    }

    public function testAttachmentDoesNotSpoil()
    {
        require_code('lorem');
        require_code('files');

        $tpl = do_template('MEDIA_IMAGE_WEBSAFE', array(
            '_GUID' => '54bb099d48cbae06decc3b479d9e1eaa',
            'URL' => placeholder_url(),
            'REMOTE_ID' => placeholder_id(),
            'THUMB_URL' => placeholder_image_url(),
            'FILENAME' => lorem_word(),
            'MIME_TYPE' => lorem_word(),
            'CLICK_URL' => placeholder_url(),

            'WIDTH' => placeholder_number(),
            'HEIGHT' => placeholder_number(),

            'LENGTH' => placeholder_number(),

            'FILESIZE' => placeholder_number(),
            'CLEAN_FILESIZE' => clean_file_size(intval(placeholder_number())),

            'THUMB' => true,
            'FRAMED' => true,
            'WYSIWYG_EDITABLE' => true,
            'NUM_DOWNLOADS' => placeholder_number(),
            'DESCRIPTION' => '',
        ));

        $before = $tpl->evaluate();
        $after = xhtml_substr($before, 0, 5, false, false, 0.0);

        $expected = $before;
        $this->assertTrue(preg_replace('#\s#', '', $after) == preg_replace('#\s#', '', $expected));
    }

    public function testNoBreak()
    {
        $before = '<div class="xhtml_substr_no_break">Blah blah blah</div>';
        $after = xhtml_substr($before, 0, 5, false, false, 0.0);

        $expected = $before;
        $this->assertTrue($after == $expected);
    }

    public function testDoesBreak()
    {
        $before = '<div class="blah">Blah blah blah</div>';
        $after = xhtml_substr($before, 0, 5, false, false, 0.0);

        $expected = $before;
        $this->assertTrue($after != $expected);
    }
}

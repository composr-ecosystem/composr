<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class comcode_wysiwyg_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('comcode');
        require_code('comcode_compiler');
        require_code('comcode_renderer');
    }

    // All the "not semihtml" cases are commented out, as actually it never works like that

    public function testRequireCSSTag() // WYSIWYG_COMCODE__STANDOUT_INLINE
    {
        $this->assertTrue(wysiwyg_comcode_markup_style('require_css') == WYSIWYG_COMCODE__STANDOUT_INLINE);

        // Not semihtml...

        //$in = "a\nb";
        //$out = "&#8203;<kbd title=\"require_css\" class=\"cms-keep\">[require_css]a<br />\nb[/require_css]</kbd>&#8203;";

        //$got = add_wysiwyg_comcode_markup('require_css', [], make_string_tempcode($in), /*$semihtml*/false);
        //$this->assertTrue($out == $got);

        // semihtml...

        $in = "a<br />\nb";
        $out = "&#8203;<kbd title=\"require_css\" class=\"cms-keep\">[require_css]a<br />\nb[/require_css]</kbd>&#8203;";

        $got = add_wysiwyg_comcode_markup('require_css', [], make_string_tempcode($in), /*$semihtml*/true);
        $this->assertTrue($out == $got);
    }

    /*public function testNA() // WYSIWYG_COMCODE__STANDOUT_BLOCK
    {
        $this->assertTrue(wysiwyg_comcode_markup_style('na') == WYSIWYG_COMCODE__STANDOUT_BLOCK);
    }*/

    public function testQuoteTag() // WYSIWYG_COMCODE__XML_BLOCK
    {
        $this->assertTrue(wysiwyg_comcode_markup_style('quote') == WYSIWYG_COMCODE__XML_BLOCK);

        // Not semihtml...

        //$in = "a\nb";
        //$out = "<comcode-quote>a<br />\nb</comcode-quote>";

        //$got = add_wysiwyg_comcode_markup('quote', [], make_string_tempcode($in), /*$semihtml*/false);
        //$this->assertTrue($out == $got);

        // semihtml...

        $in = "a<br />\nb";
        $out = "<comcode-quote>a<br />\nb</comcode-quote>";

        $got = add_wysiwyg_comcode_markup('quote', [], make_string_tempcode($in), /*$semihtml*/true);
        $this->assertTrue($out == $got);
    }

    public function testStaffNoteTag() // WYSIWYG_COMCODE__XML_BLOCK
    {
        $this->assertTrue(wysiwyg_comcode_markup_style('staff_note') == WYSIWYG_COMCODE__XML_BLOCK);

        // Not semihtml...

        //$in = "a\nb";
        //$out = "<comcode-staff_note>a<br />\nb</comcode-staff_note>";

        //$got = add_wysiwyg_comcode_markup('staff_note', [], make_string_tempcode($in), /*$semihtml*/false);
        //$this->assertTrue($out == $got);

        // semihtml...

        $in = "a<br />\nb";
        $out = "<comcode-staff_note>a<br />\nb</comcode-staff_note>";

        $got = add_wysiwyg_comcode_markup('staff_note', [], make_string_tempcode($in), /*$semihtml*/true);
        $this->assertTrue($out == $got);
    }

    public function testCodeTag() // WYSIWYG_COMCODE__XML_BLOCK
    {
        $this->assertTrue(wysiwyg_comcode_markup_style('code') == WYSIWYG_COMCODE__XML_BLOCK);

        // Not semihtml...

        //$in = "a<br />\nb";
        //$out = "<comcode-code>a<br />\nb</comcode-code>";

        //$got = add_wysiwyg_comcode_markup('code', [], make_string_tempcode($in), /*$semihtml*/false);
        //$this->assertTrue($out == $got);

        // semihtml...

        $in = "a<br />\nb";
        $out = "<comcode-code>a<br />\nb</comcode-code>";

        $got = add_wysiwyg_comcode_markup('code', [], make_string_tempcode($in), /*$semihtml*/true);
        $this->assertTrue($out == $got);
    }

    public function testIfInGroupTag() // WYSIWYG_COMCODE__XML_INLINE
    {
        $this->assertTrue(wysiwyg_comcode_markup_style('if_in_group') == WYSIWYG_COMCODE__XML_INLINE);

        // Not semihtml...

        //$in = "a\nb";
        //$out = "&#8203;<comcode-if_in_group>a<br />\nb</comcode-if_in_group>&#8203;";

        //$got = add_wysiwyg_comcode_markup('if_in_group', [], make_string_tempcode($in), /*$semihtml*/false);
        //$this->assertTrue($out == $got);

        // semihtml...

        $in = "a<br />\nb";
        $out = "&#8203;<comcode-if_in_group>a<br />\nb</comcode-if_in_group>&#8203;";

        $got = add_wysiwyg_comcode_markup('if_in_group', [], make_string_tempcode($in), /*$semihtml*/true);
        $this->assertTrue($out == $got);
    }

    public function testBlockTag() // WYSIWYG_COMCODE__BUTTON
    {
        $this->assertTrue(wysiwyg_comcode_markup_style('block') == WYSIWYG_COMCODE__BUTTON);

        // Not semihtml...

        //$in = "a\nb";
        //$out = '<input class="cms-keep-ui-controlled" size="45" title="[block]a' . "\n" . 'b[/block]" type="button" value="a' . "\n" . 'b Comcode tag (dbl-click to edit/delete)" />';

        //$got = add_wysiwyg_comcode_markup('block', [], make_string_tempcode($in), /*$semihtml*/false);
        //$this->assertTrue($out == $got);

        // semihtml...

        $in = "a<br />\nb";
        $out = '<input class="cms-keep-ui-controlled" size="45" title="[block]a&lt;br /&gt;' . "\n" . 'b[/block]" type="button" value="a' . "\n" . do_lang('comcode:COMCODE_EDITABLE_BLOCK', 'b') . '" />';

        $got = add_wysiwyg_comcode_markup('block', [], make_string_tempcode($in), /*$semihtml*/true);
        $this->assertTrue($out == $got);
    }

    public function testBTag() // Reversible WYSIWYG_COMCODE__HTML
    {
        $this->assertTrue(wysiwyg_comcode_markup_style('b') == WYSIWYG_COMCODE__HTML);

        // Not semihtml...

        //$in = "a\nb";

        //$got = add_wysiwyg_comcode_markup('b', [], make_string_tempcode($in), /*$semihtml*/false);
        //$this->assertTrue($got === null); // null-op

        // semihtml...

        $in = "a<br />\nb";

        $got = add_wysiwyg_comcode_markup('b', [], make_string_tempcode($in), /*$semihtml*/true);
        $this->assertTrue($got === null); // null-op
    }

    public function tearDown()
    {
        parent::tearDown();
    }
}

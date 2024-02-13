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
class comcode_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('comcode');
        require_code('comcode_from_html');
    }

    public function testContentsTag()
    {
        if (($this->only !== null) && ($this->only != 'testContentsTag')) {
            return;
        }

        // From Comcode...

        $comcode = '
[contents][/contents]

[title="2"]Foo[/title]

[title="3"]Bar[/title]

[title="2"]Test[/title]
';
        $actual = comcode_to_tempcode($comcode);
        $_actual = $actual->evaluate();

        $this->assertTrue(strpos($_actual, '>Foo</a>') !== false);
        $this->assertTrue(strpos($_actual, '>Bar</a>') !== false);
        $this->assertTrue(strpos($_actual, '>Test</a>') !== false);

        // From semihtml with eager_wysiwyg...

        set_option('eager_wysiwyg', '1');

        $comcode = '[semihtml]
[contents][/contents]

<h2>Foo</h2>

<h3>Bar</h3>

<h2>Test</h2>
[/semihtml]
';
        $actual = comcode_to_tempcode(semihtml_to_comcode($comcode));
        $_actual = $actual->evaluate();

        $this->assertTrue(strpos($_actual, '>Foo</a>') !== false);
        $this->assertTrue(strpos($_actual, '>Bar</a>') !== false);
        $this->assertTrue(strpos($_actual, '>Test</a>') !== false);

        // From semihtml without eager_wysiwyg...

        set_option('eager_wysiwyg', '0');

        $comcode = '[semihtml]
[contents][/contents]

<h2>Foo</h2>

<h3>Bar</h3>

<h2>Test</h2>
[/semihtml]
';
        $actual = comcode_to_tempcode(semihtml_to_comcode($comcode));
        $_actual = $actual->evaluate();

        $this->assertTrue(strpos($_actual, '>Foo</a>') !== false);
        $this->assertTrue(strpos($_actual, '>Bar</a>') !== false);
        $this->assertTrue(strpos($_actual, '>Test</a>') !== false);

        // From semihtml coming direct from WYSIWYG...

        set_option('eager_wysiwyg', '0');

        $comcode = '
<input class="cms-keep-ui-controlled" size="45" title="[contents][/contents]" type="button" value="contents Comcode tag (dbl-click to edit/delete)" />

<h2>Foo</h2>
';
        $actual = comcode_to_tempcode(semihtml_to_comcode('[semihtml]' . $comcode . '[/semihtml]'));
        $_actual = $actual->evaluate();

        $this->assertTrue(strpos($_actual, '>Foo</a>') !== false);
    }

    public function testEmoticons()
    {
        if (($this->only !== null) && ($this->only != 'testEmoticons')) {
            return;
        }

        $actual = comcode_to_tempcode(':)');
        $this->assertTrue(strpos($actual->evaluate(), '<img') !== false);
    }

    public function testRules()
    {
        if (($this->only !== null) && ($this->only != 'testRules')) {
            return;
        }

        $actual = comcode_to_tempcode('-----');
        $this->assertTrue(strpos($actual->evaluate(), '<hr />') !== false);
    }

    public function testLinks()
    {
        if (($this->only !== null) && ($this->only != 'testLinks')) {
            return;
        }

        $actual = comcode_to_tempcode('http://example.com');
        $this->assertTrue(strpos($actual->evaluate(), '<a') !== false);
    }

    public function testMemberLinks()
    {
        if (($this->only !== null) && ($this->only != 'testMemberLinks')) {
            return;
        }

        $username = $this->get_canonical_username('admin');
        $actual = comcode_to_tempcode('{{' . $username . '}}');
        $this->assertTrue(strpos($actual->evaluate(), '<a') !== false);
    }

    public function testWiki()
    {
        if (($this->only !== null) && ($this->only != 'testWiki')) {
            return;
        }

        $actual = comcode_to_tempcode('[[Home]]');
        $this->assertTrue(strpos($actual->evaluate(), '<a') !== false);
    }

    public function testShortcode()
    {
        if (($this->only !== null) && ($this->only != 'testShortcode')) {
            return;
        }

        $actual = comcode_to_tempcode('-|-');
        $this->assertTrue(strpos($actual->evaluate(), '&dagger;') !== false);
    }

    public function testTable()
    {
        if (($this->only !== null) && ($this->only != 'testTable')) {
            return;
        }

        $actual = comcode_to_tempcode('{| wide 20%:80% class=foobar id=xxx Summary
! a
! b
|-
| a || b
|}');
        $_actual = $actual->evaluate();
        $this->assertTrue(substr_count($_actual, '<table') == 1);
        $this->assertTrue(substr_count($_actual, '<col style="') == 2);
        $this->assertTrue(substr_count($_actual, 'wide-table"') == 1);
        $this->assertTrue(substr_count($_actual, 'id="xxx"') == 1);
        $this->assertTrue(substr_count($_actual, 'foobar') == 1);
        $this->assertTrue(substr_count($_actual, '<thead>') == 1);
        $this->assertTrue(substr_count($_actual, '<tr>') == 2);
        $this->assertTrue(substr_count($_actual, '<tbody>') == 1);
        $this->assertTrue(substr_count($_actual, '<th>') == 2);
        $this->assertTrue(substr_count($_actual, '<td>') == 2);

        $actual = comcode_to_tempcode('{| fake_table wide 20%:80% class=foobar id=xxx Summary
! a
! b
|-
| a
| b
|}');
        $_actual = $actual->evaluate();
        $this->assertTrue(substr_count($_actual, '<table') == 0);
        $this->assertTrue(substr_count($_actual, '<div') > 0);
    }

    public function testCodeTags()
    {
        if (($this->only !== null) && ($this->only != 'testCodeTags')) {
            return;
        }

        $expects_no_parse = [
            '[tt]{$IMG,under_construction_animated}[/tt]',
            '[no_parse]{$IMG,under_construction_animated}[/no_parse]',
            '[code]{$IMG,under_construction_animated}[/code]',
            '[codebox]{$IMG,under_construction_animated}[/codebox]',
            '[html]{$IMG,under_construction_animated}[/html]',
            semihtml_to_comcode('<tt>{$IMG,under_construction_animated}</tt>', true), // Should convert to 'tt' tag
            semihtml_to_comcode('<kbd>{$IMG,under_construction_animated}</kbd>', true), // Should convert to 'tt' tag
            semihtml_to_comcode('<code>{$IMG,under_construction_animated}</code>', true), // Should convert to 'code' tag
            semihtml_to_comcode('[code]{$IMG,under_construction_animated}[/code]', true),
        ];
        foreach ($expects_no_parse as $comcode) {
            $actual = comcode_to_tempcode($comcode);
            $this->assertTrue(strpos($actual->evaluate(), '{$IMG') !== false, 'Tempcode was parsed when it should not have been, in (1): ' . $comcode . '; produced: ' . $actual->evaluate());
        }

        $expects_no_parse = [
            '[tt]{$IMG,under_construction_animated}[/tt]',
            '[no_parse]{$IMG,under_construction_animated}[/no_parse]',
            '[code]{$IMG,under_construction_animated}[/code]',
            '[codebox]{$IMG,under_construction_animated}[/codebox]',
        ];
        foreach ($expects_no_parse as $comcode) {
            $actual = comcode_to_tempcode($comcode, null, false, null, null, COMCODE_IS_ALL_SEMIHTML);
            $this->assertTrue((strpos($actual->evaluate(), '{$IMG') !== false) || (strpos($actual->evaluate(), '&#123;$IMG') !== false), 'Tempcode was parsed when it should not have been, in (2): ' . $comcode . '; got: ' . $actual->evaluate());
        }

        $expects_parse = [
            '{$IMG,under_construction_animated}',
            '[semihtml]{$IMG,under_construction_animated}[/semihtml]',
            '[url]{$IMG,under_construction_animated}[/url]',
        ];
        foreach ($expects_parse as $comcode) {
            $actual = comcode_to_tempcode($comcode);
            $this->assertTrue(strpos($actual->evaluate(), '{$IMG') === false, 'Tempcode was not parsed when it should have been, in: ' . $comcode);
        }
    }

    public function testComcode()
    {
        if (($this->only !== null) && ($this->only != 'testComcode')) {
            return;
        }

        $expectations = [" - foo  " => "<ul><li>foo</li></ul>", " - foo\n - bar" => "<ul><li>foo</li><li>bar</li></ul>", " - foo - bar" => " - foo - bar", "" => " ", " -foo" => "-foo", "-foo" => "-foo", "--foo" => "&ndash;foo", "[b]bar[/b]" => "<strong class=\"comcode-bold\">bar</strong>"];

        foreach ($expectations as $comcode => $html) {
            $actual = comcode_to_tempcode($comcode);

            $actual_altered = str_replace("&nbsp;", "", preg_replace('#\s#', '', $actual->evaluate()));

            $matches = preg_replace('#\s#', '', $html) == $actual_altered;

            $this->assertTrue($matches, '"' . $comcode . '" produced instead of "' . $actual_altered . '" "' . $html . '"');
        }
    }

    public function testMentions()
    {
        if (($this->only !== null) && ($this->only != 'testMentions')) {
            return;
        }

        if (get_forum_type() != 'cns') {
            $this->assertTrue(false, 'Test only works with Conversr');
            return;
        }

        global $MEMBER_MENTIONS_IN_COMCODE;

        $test_username = $this->get_canonical_username('test');

        $tests = [];

        // Positives
        $tests['@' . $test_username] = true;
        $tests[' @' . $test_username] = true;
        $tests['@' . $test_username . ' '] = true;
        $tests[' @' . $test_username . ' '] = true;
        $tests['@' . $test_username . ','] = true;

        // Negatives
        $tests[',@' . $test_username] = false; // Must be preceded by white-space or nothing
        $tests[',@' . $test_username . ','] = false; // "
        $tests['x@' . $test_username . ' '] = false; // "
        $tests['@' . $test_username . 'xppp'] = false; // Must not have junk on tail-end

        foreach ($tests as $test => $expected) {
            $MEMBER_MENTIONS_IN_COMCODE = [];
            comcode_to_tempcode($test);

            if ($expected) {
                $this->assertTrue(count($MEMBER_MENTIONS_IN_COMCODE) == 1, 'Expected to see a mention for: "' . $test . '"');
            } else {
                $this->assertTrue(empty($MEMBER_MENTIONS_IN_COMCODE), 'Expected to NOT see a mention for: "' . $test . '"');
            }
        }
    }
}

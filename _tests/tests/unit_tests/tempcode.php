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

/*EXTRA FUNCTIONS: diff_simple_text*/

/**
 * Composr test case class (unit testing).
 */
class Tempcode_test_set extends cms_test_case
{
    public function testTempcode()
    {
        $nonempty = paragraph('ignore');
        $tpl = do_template('tempcode_test', [
            '_GUID' => '1f31d5279dd6a15a9fdba9296e1b7597',
            'EMPTY1' => '',
            'EMPTY2' => new Tempcode(),
            'NONEMPTY' => $nonempty,
            'PASSED' => 'This is a passed parameter',
            'SIMPLE_ARRAY' => ['1', '2', '3'],
            'ARRAY' => [
                ['A' => 'A1', 'B' => 'B1', 'C' => 'C1'],
                ['A' => 'A2', 'B' => 'B2', 'C' => 'C2'],
                ['A' => 'A3', 'B' => 'B3', 'C' => 'C3'],
                ['A' => 'A4', 'B' => 'B4', 'C' => 'C4'],
            ],
        ], null, false, null, '.txt', 'text');
        $got = $tpl->evaluate();

        $expected = '<h1>Tempcode tests</h1>

<h2>Escaping</h2>

<p>
	\\\'Hello\\\'
</p>
<p>
	\"Hello\"
</p>
<p>
	Hello World
</p>
<p>
	Hello\n World
</p>
<p>
	\[html]\[/html]
</p>
<p>
	foo%3Dbar
</p>
<!--
<p>
	<strong>Test<\/strong></strong>
</p>
-->
<h2>Environmental variables</h2>

<table class="map-table"><tbody>
	<tr>
		<th>$MOBILE</th>
		<td>0</td>
	</tr>
</tbody></table>

<h2>Computational variables</h2>

<table class="map-table"><tbody>
	<tr>
		<th>$CYCLE,my_cycle,1,2</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$CYCLE,my_cycle,1,2</th>
		<td>2</td>
	</tr>
	<tr>
		<th>$CYCLE,my_cycle,1,2</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$FROM_TIMESTAMP*,Y-m-d,33424322</th>
		<td>1971-01-22</td>
	</tr>
	<tr>
		<th>$IS_NON_EMPTY,</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$IS_NON_EMPTY,a</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$IS_EMPTY,</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$IS_EMPTY,a</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$NEGATE,3</th>
		<td>-3</td>
	</tr>
	<tr>
		<th>$OBFUSCATE,chris@example.com (check HTML source to see if it is obfuscated)</th>
		<td>&#99;&#x68;&#114;&#x69;&#115;&#x40;&#101;&#x78;&#97;&#x6d;&#112;&#x6c;&#101;&#x2e;&#99;&#x6f;&#109;</td>
	</tr>
	<tr>
		<th>$GET,foobar</th>
		<td></td>
	</tr>
</tbody></table>

<h2>Array variables</h2>

<table class="map-table"><tbody>
	<tr>
		<th>+START,COUNT,SIMPLE_ARRAY+END</th>
		<td>3</td>
	</tr>
	<tr>
		<th>+START,IMPLODE, ,SIMPLE_ARRAY+END</th>
		<td>1 2 3</td>
	</tr>
	<tr>
		<th>+START,OF,SIMPLE_ARRAY,1+END</th>
		<td>2</td>
	</tr>
</tbody></table>

<h2>String variables</h2>

<table class="map-table"><tbody>
	<tr>
		<th>$WCASE,I am a Mushroom</th>
		<td>I Am A Mushroom</td>
	</tr>
	<tr>
		<th>$LCASE,I am a Mushroom</th>
		<td>i am a mushroom</td>
	</tr>
	<tr>
		<th>$UCASE,I am a Mushroom</th>
		<td>I AM A MUSHROOM</td>
	</tr>
	<tr>
		<th>$REPLACE,a,b,apple</th>
		<td>bpple</td>
	</tr>
	<tr>
		<th>$AT,apple,3</th>
		<td>l</td>
	</tr>
	<tr>
		<th>$SUBSTR,apple,1,2</th>
		<td>pp</td>
	</tr>
	<tr>
		<th>$LENGTH,apple</th>
		<td>5</td>
	</tr>
	<tr>
		<th>$WORDWRAP,i love to eat cheese,5</th>
		<td>i<br />love<br />to<br />eat<br />cheese</td>
	</tr>
	<tr>
		<th>$TRUNCATE_LEFT,i love to eat cheese,5</th>
		<td>i&hellip;</td>
	</tr>
	<tr>
		<th>$TRUNCATE_RIGHT,i love to eat cheese,5</th>
		<td>&hellip;se</td>
	</tr>
	<tr>
		<th>$TRUNCATE_SPREAD,i love to eat cheese,5</th>
		<td>i&hellip;se</td>
	</tr>
	<tr>
		<th>$ESCAPE,Bill &amp; Julie,ENTITY_ESCAPED</th>
		<td>Bill &amp; Julie</td>
	</tr>
</tbody></table>

<h2>Arithmetical variables</h2>

<table class="map-table"><tbody>
	<tr>
		<th>$MULT,2,3</th>
		<td>6</td>
	</tr>
	<tr>
		<th>$ROUND,3.23,1</th>
		<td>3.2</td>
	</tr>
	<tr>
		<th>$ROUND,3.29,1</th>
		<td>3.3</td>
	</tr>
	<tr>
		<th>$ROUND,36.66,0</th>
		<td>37</td>
	</tr>
    <tr>
		<th>$MAX,3,2</th>
		<td>3</td>
	</tr>
	<tr>
		<th>$MIN,3,2</th>
		<td>2</td>
	</tr>
	<tr>
		<th>$MOD,-2</th>
		<td>2</td>
	</tr>
	<tr>
		<th>$MOD,2</th>
		<td>2</td>
	</tr>
	<tr>
		<th>$REM,3,2</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$DIV,7,2</th>
		<td>3</td>
	</tr>
	<tr>
		<th>$SUBTRACT,3,2</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$ADD,3,2</th>
		<td>5</td>
	</tr>
</tbody></table>

<h2>Logical variables</h2>

<table class="map-table"><tbody>
	<tr>
		<th>$NOT,1</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$OR,1,0</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$OR,0,0</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$AND,1,0</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$AND,1,1</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$EQ,3,3</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$EQ,3,2</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$NEQ,3,3</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$NEQ,3,2</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$LT,1,2</th>
		<td>1</td>
	</tr>
	<tr>
		<th>$LT,2,1</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$GT,1,2</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$GT,2,1</th>
		<td>1</td>
	</tr>
</tbody></table>

<h2>Variable variables</h2>

<table class="map-table"><tbody>
	<tr>
		<th>$ISSET,test</th>
		<td>0</td>
	</tr>
	<tr>
		<th>$INIT,test,2</th>
		<td></td>
	</tr>
	<tr>
		<th>$GET,test</th>
		<td>2</td>
	</tr>
	<tr>
		<th>$INIT,test,3</th>
		<td></td>
	</tr>
	<tr>
		<th>$GET,test</th>
		<td>2</td>
	</tr>
	<tr>
		<th>$SET,test,3</th>
		<td></td>
	</tr>
	<tr>
		<th>$GET,test</th>
		<td>3</td>
	</tr>
	<tr>
		<th>$INC,test</th>
		<td></td>
	</tr>
	<tr>
		<th>$GET,test</th>
		<td>4</td>
	</tr>
	<tr>
		<th>$DEC,test</th>
		<td></td>
	</tr>
	<tr>
		<th>$GET,test</th>
		<td>3</td>
	</tr>
	<tr>
		<th>$ISSET,test</th>
		<td>1</td>
	</tr>
</tbody></table>

<p>
	Putting #anchor onto URL should cause jump to here.<a id="anchor"></a>
</p>

<h2>Directives</h2>


	<p>
		IF_PASSED true positive (good)
	</p>




	<p>
		IF_PASSED true negative (good)
	</p>



	<p>
		IF_EMPTY true positive (string) (good)
	</p>




	<p>
		IF_EMPTY false negative (tempcode) (good)
	</p>




bar


	<p>
		IF true positive (good)
	</p>



{$BASE_URL}







	<p>
		Should see this text 3 times.

	</p>

	<p>
		Should see this text 3 times.

	</p>

	<p>
		Should see this text 3 times.

	</p>


<table class="layout-table"><tbody>
<tr>
	<td>Blah</td>
	<td>
		{a} {b} {c}
	</td>

	<td>Blah</td>
	<td>
		{a} {b} {c}
	</td>
</tr><tr>
	<td>Blah</td>
	<td>
		{a} {b} {c}
	</td>

	<td>Blah</td>
	<td>
		{a} {b} {c}
	</td>
</tr>
</tbody></table>';

        $ok = preg_replace('#\s#', '', $got) == preg_replace('#\s#', '', $expected);

        $this->assertTrue($ok);
        if (!$ok) {
            require_code('diff');
            echo '<code style="white-space: pre">' . diff_simple_text($got, $expected) . '</code>';
        }
    }
}

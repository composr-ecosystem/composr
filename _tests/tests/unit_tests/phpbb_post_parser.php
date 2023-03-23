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
class phpbb_post_parser_test_set extends cms_test_case
{
    public function testParsing()
    {
        require_code('forum/phpbb3');

        $tests = [
            '<r>dss<B><s>[b]</s>d<e>[/b]</e></B>sd</r>' => 'dss[b]d[/b]sd', // BBCode enabled
            '<t>dss[b]d[/b]sd</t>' => 'dss[semihtml]&#91;[/semihtml]b]d[semihtml]&#91;[/semihtml]/b]sd', // BBCode disabled
            '<r><E>:D</E></r>' => ':D', // Emoticons enabled
            '<t>:D</t>' => '[semihtml]:D[/semihtml]', // Emoticons disabled
        ];
        foreach ($tests as $in => $expected_comcode) {
            $got_comcode = _phpbb3_post_text_to_comcode($in);
            $this->assertTrue($got_comcode == $expected_comcode, 'Expected ' . $expected_comcode . ' but got ' . $got_comcode);
        }

        $tests = [
            '<r>blah<ATTACHMENT filename="sample-logo.png" index="0"><s>[attachment=0]</s>sample-logo.png<e>[/attachment]</e></ATTACHMENT>blah</r>' => 'blah[attachment]12345[/attachment]blah', // With attachment (inline)
        ];
        foreach ($tests as $in => $expected_comcode) {
            $got_comcode = _phpbb3_post_text_to_comcode($in, [12345]);
            $this->assertTrue($got_comcode == $expected_comcode, 'Expected ' . $expected_comcode . ' but got ' . $got_comcode);
        }
    }
}

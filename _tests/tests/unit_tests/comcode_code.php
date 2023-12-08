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
class comcode_code_test_set extends cms_test_case
{
    public function testComcodeCodeTags()
    {
        require_code('database_action');
        require_code('comcode_from_html');

        set_option('eager_wysiwyg', '0');

        set_privilege(1, 'allow_html', true);

        $cases = array();

        // Vanilla non-WYSIWYG
        $from = '[code]Food & Drink[/code]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">Food &amp; Drink</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[0] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // This is INCORRECT, and we therefore expect it to not parse correctly
        $from = '[code]Food &amp; Drink[/code]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">Food &amp;amp; Drink</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[1] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Vanilla WYSIWYG
        $from = '[semihtml][code]Food &amp; Drink[/code][/semihtml]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">Food &amp; Drink</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[2] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // The entity error will be auto-fixed within parser behaviours
        $from = '[semihtml][code]Food & Drink[/code][/semihtml]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">Food & Drink</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[3] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // This is INCORRECT, and we therefore expect it to not parse correctly
        $from = '[html][code]Food &amp; Drink[/code][/html]';
        $to = '[code]Food &amp; Drink[/code]';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[4] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Complex WYSIWYG (HTML within code tag adjusted to Comcode-Textcode-formatting)
        $from = '[semihtml][code]<div>Food &amp; Drink</div><br /><div>On the house</div>[/code][/semihtml]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><div class="comcode_code_inner"><div>Food &amp; Drink</div><br /><div>On the house</div></div></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[5] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Complex WYSIWYG (exotic HTML within code tag carries through)
        $from = '[semihtml][code]<composr-test>Food &amp; Drink</composr-test><br /><composr-test>On the house</composr-test>[/code][/semihtml]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner"><composr-test>Food &amp; Drink</composr-test><br /><composr-test>On the house</composr-test></code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[6] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Security: It's essential that unsafe code is stripped, as it would be easy to accidentally let it leak through within code tag parsing (we read it in verbatim where we normally apply the security and have to filter it later, depending on the output transformations required by the particular code type)
        $from = '[semihtml][code]Food &amp; Drink<script>window.alert("!");</script>[/code][/semihtml]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">Food &amp; Drink<span>window.alert("!");</script></code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = false;
        $cases[7] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // White-space preservation for non-WYSIWYG
        $from = "[code] x  y\nz[/code]";
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">&nbsp;x &nbsp;y<br />z</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[8] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // White-space preservation for WYSIWYG
        $from = "[semihtml][code]&nbsp;x &nbsp;y<br />z[/code][/semihtml]";
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">&nbsp;x &nbsp;y<br />z</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[9] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // HTML Code tags for WYSIWYG, without forcing to Comcode
        $from = "[semihtml]<code>&nbsp;x &nbsp;y<br />z</code>[/semihtml]";
        $to = '<code>&nbsp;x &nbsp;y<br />z</code>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[10] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // HTML Code tags for WYSIWYG, with forcing to Comcode
        $from = "[semihtml]<code>&nbsp;x &nbsp;y<br />z</code>[/semihtml]";
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">' . hex2bin('c2a0') . 'x ' . hex2bin('c2a0') . 'y<br />z</code></div></div></div>';
        $forced_html_to_comcode = true;
        $do_for_admin_too = true;
        $cases[11] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Nested tags for non-WYSIWYG
        $from = '[code][html]a[/html][semihtml]b[/semihtml][code]c[/code][codebox]d[/codebox][/code]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">[html]a[/html][semihtml]b[/semihtml][code]c[/code][codebox]d[/codebox]</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[12] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Nested tags for WYSIWYG
        $from = '[semihtml][code][html]a[/html][semihtml]b[/semihtml][code]c[/code][codebox]d[/codebox][/code][/semihtml]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">[html]a[/html][semihtml]b[/semihtml][code]c[/code][codebox]d[/codebox]</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[13] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        if (file_exists(get_file_base() . '/sources_custom/geshi/')) {
            // Vanilla non-WYSIWYG via GeSHI
            $from = '[code="PHP"]echo "food & drink";[/code]';
            $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code (<kbd>PHP</kbd>)</h4><div class="webstandards_checker_off"><div class="comcode_code_inner"><div class="php" style="font-family:monospace;"><span style="color: #b1b100;">echo</span><span style="color: #0000ff;">&quot;food &amp; drink&quot;</span><span style="color: #339933;">;</span></div></div></div></div></div>';
            $forced_html_to_comcode = false;
            $do_for_admin_too = true;
            $cases[14] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);
        }

        // No textual syntax for WYSIWYG
        $from = "[semihtml][code]\n--\n[/code][/semihtml]";
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">--</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[15] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // No textual syntax for non-WYSIWYG
        $from = "[code]\n--\n[/code]";
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner">--<br /></code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[16] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // But textual syntax still working
        $from = "--\n";
        $to = '<hr />';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[17] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Security: Strict filtering working
        set_privilege(1, 'allow_html', false);
        $from = '[semihtml][code]<example>foo</example>[/code][/semihtml]';
        $to = '<div class="comcode_code_wrap"><div class="comcode_code"><h4>Code</h4><div class="webstandards_checker_off"><code class="comcode_code_inner"><!--filtered; no Subject to a more liberal HTML filter-->foo<!--filtered; no Subject to a more liberal HTML filter--></code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = false;
        $cases[18] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        $only = get_param_integer('only', null);
        foreach ($cases as $i => $_bits) {
            if (($only !== null) && ($only != $i)) {
                continue;
            }

            list($from, $to, $forced_html_to_comcode, $do_for_admin_too) = $_bits;

            if ($forced_html_to_comcode) {
                $_from = semihtml_to_comcode($from, true);
            } else {
                $_from = $from;
            }

            $ret = static_evaluate_tempcode(comcode_to_tempcode($_from, $GLOBALS['FORUM_DRIVER']->get_guest_id()));
            $got = $this->html_simplify($ret);
            $this->assertTrue($got == $to, 'Failed to properly evaluate Comcode CASE#' . strval($i) . '; FROM: ' . $from . '; EXPECTED: ' . $to . '; GOT: ' . $got . ' [AS GUEST]');

            if ($do_for_admin_too) {
                $ret = static_evaluate_tempcode(comcode_to_tempcode($_from, null, true));
                $got = $this->html_simplify($ret);
                $this->assertTrue($got == $to, 'Failed to properly evaluate Comcode CASE#' . strval($i) . '; FROM:' . $from . '; EXPECTED: ' . $to . '; GOT: ' . $got . ' [AS ADMIN]');
            }
        }
    }

    protected function html_simplify($code)
    {
        $code = trim($code);
        $code = preg_replace('#\s*(<[^<>]*>)\s*#', '$1', $code); // No white-space around tags
        $code = preg_replace('#\s+#', ' ', $code); // Only single spaces for any white-space

        return $code;
    }
}

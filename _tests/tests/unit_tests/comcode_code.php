<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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
        set_option('eager_wysiwyg', '0');

        require_code('comcode_from_html');

        $cases = array();

        // Vanilla non-WYSIWYG
        $from = '[code]Food & Drink[/code]';
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">Food &amp; Drink</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[0] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // This is INCORRECT, and we therefore expect it to not parse correctly
        $from = '[code]Food &amp; Drink[/code]';
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">Food &amp;amp; Drink</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[1] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Vanilla WYSIWYG
        $from = '[semihtml][code]Food &amp; Drink[/code][/semihtml]';
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">Food &amp; Drink</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[2] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // The entity error will be auto-fixed within parser behaviours
        $from = '[semihtml][code]Food & Drink[/code][/semihtml]';
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">Food &amp; Drink</code></div></div></div>';
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
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">Food &amp; Drink<br /><br />On the house<br /></code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[5] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Complex WYSIWYG (exotic HTML within code tag carries through)
        $from = '[semihtml][code]<composr-test>Food &amp; Drink</composr-test><br /><composr-test>On the house</composr-test>[/code][/semihtml]';
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner"><composr-test>Food &amp; Drink</composr-test><br /><composr-test>On the house</composr-test></code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[6] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Security: It's essential that unsafe code is stripped, as it would be easy to accidentally let it leak through within code tag parsing (we read it in verbatim where we normally apply the security and have to filter it later, depending on the output transformations required by the particular code type)
        $from = '[semihtml][code]Food &amp; Drink<script>window.alert("!");</script>[/code][/semihtml]';
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">Food &amp; Drinkwindow.alert(&quot;!&quot;);</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = false;
        $cases[7] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // White-space preservation for non-WYSIWYG
        $from = "[code] x  y\nz[/code]";
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">&nbsp;x &nbsp;y<br />z</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[8] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // White-space preservation for WYSIWYG
        $from = "[semihtml][code]&nbsp;x &nbsp;y<br />z[/code][/semihtml]";
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">&nbsp;x &nbsp;y<br />z</code></div></div></div>';
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
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">&nbsp;x &nbsp;y<br />z</code></div></div></div>';
        $forced_html_to_comcode = true;
        $do_for_admin_too = true;
        $cases[11] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Nested tags for non-WYSIWYG
        $from = '[code][html]a[/html][semihtml]b[/semihtml][code]c[/code][codebox]d[/codebox][/code]';
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">[html]a[/html][semihtml]b[/semihtml][code]c[/code][codebox]d[/codebox]</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[12] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        // Nested tags for WYSIWYG
        $from = '[semihtml][code][html]a[/html][semihtml]b[/semihtml][code]c[/code][codebox]d[/codebox][/code][/semihtml]';
        $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code</h4><div class="webstandards-checker-off"><code class="comcode-code-inner">&#091;html]a&#091;/html]&#091;semihtml]b&#091;/semihtml]&#091;code]c&#091;/code]&#091;codebox]d&#091;/codebox]</code></div></div></div>';
        $forced_html_to_comcode = false;
        $do_for_admin_too = true;
        $cases[13] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);

        if (file_exists(get_file_base() . '/sources_custom/geshi/')) {
            // Vanilla non-WYSIWYG via GeSHI
            $from = '[code="PHP"]echo "food & drink";[/code]';
            $to = '<div class="comcode-code-wrap"><div class="comcode-code"><h4>Code (<kbd>PHP</kbd>)</h4><div class="webstandards-checker-off"><div class="comcode-code-inner"><div class="php" style="font-family:monospace;"><span style="color: #b1b100;">echo</span><span style="color: #0000ff;">&quot;food &amp; drink&quot;</span><span style="color: #339933;">;</span></div></div></div></div></div>';
            $forced_html_to_comcode = false;
            $do_for_admin_too = true;
            $cases[14] = array($from, $to, $forced_html_to_comcode, $do_for_admin_too);
        }

        foreach ($cases as $i => $_bits) {
            if (($this->only !== null) && ($this->only != $i)) {
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

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
class xss_test_set extends cms_test_case
{
    public function testComcodeHTMLFilter()
    {
        require_code('database_action');

        // This won't check everything, but will make sure we don't accidentally regress our overall checking
        // To do a better check, find a manual XSS test blob, and try pasting it into a news post (using html tags) to preview -- and ensure no JS alerts come up

        set_privilege(1, 'allow_html', true);

        $comcode = '[html]<iframe></iframe><Iframe></iframe>test<test>test</test><script></script><span/onclick=""></span><span onclick=""></span><span onClick=""></span><span on' . chr(0) . 'click=""></span><a href="&#115;cript:">x</a><a href="&#0115;cript:">x</a><a href="&#x73;cript:">x</a><a href="&#x73cript:">x</a><a href="j	a	v	a	s	c	r	i	p	t	:">x</a>[/html]';

        $parsed = strtolower(static_evaluate_tempcode(comcode_to_tempcode($comcode, $GLOBALS['FORUM_DRIVER']->get_guest_id())));

        $this->assertTrue(strpos($parsed, '<iframe') === false);

        $this->assertTrue(strpos($parsed, '<script') === false);

        $this->assertTrue(strpos($parsed, 'onclick') === false);

        $this->assertTrue(strpos($parsed, 'on' . chr(0) . 'click') === false);

        $this->assertTrue(strpos($parsed, '&#115;cript') === false);

        $this->assertTrue(strpos($parsed, '&#0115;cript') === false);

        $this->assertTrue(strpos($parsed, '&#x73;cript') === false);

        $this->assertTrue(strpos($parsed, '&#x73cript') === false);

        $this->assertTrue(strpos($parsed, 'j	a	v	a	s	c	r	i	p	t	:') === false);

        $this->assertTrue(strpos($parsed, '<test') !== false); // So it does work, in general, not just stripping all HTML/XML tags

        set_privilege(1, 'allow_html', false);

        $parsed = strtolower(static_evaluate_tempcode(comcode_to_tempcode($comcode, $GLOBALS['FORUM_DRIVER']->get_guest_id())));

        $this->assertTrue(strpos($parsed, '<test') === false); // Not white-listed

        // target="_blank" attack (assumes browser has implemented https://github.com/whatwg/html/issues/4078)...

        set_privilege(1, 'allow_html', true);

        $comcode = '[semihtml]<a href="http://evilsite.com/" target="_blank" rel="opener">test</a> <a href="http://evilsite.com/" target="_blank"' . "\t" . 'rel=\'foo' . "\t" . 'opener' . "\t" . 'bar\'>test</a> <a href="http://evilsite.com/" target=_blank' . "\n" . 'rel=opener>test</a> [url rel="opener"]test[/url][/semihtml]';

        $parsed = strtolower(static_evaluate_tempcode(comcode_to_tempcode($comcode, $GLOBALS['FORUM_DRIVER']->get_guest_id())));

        $parsed = str_replace('noopener', '', $parsed);
        $this->assertTrue(preg_match('#rel=.*opener#', $parsed) == 0);

        // Some more hard-core stuff, where no white-list check needed...

        set_privilege(1, 'allow_html', true);

        $comcode = '<scr<script>';

        $parsed = strtolower(static_evaluate_tempcode(comcode_to_tempcode($comcode, $GLOBALS['FORUM_DRIVER']->get_guest_id())));

        $this->assertTrue(strpos($parsed, '<script') === false);

        set_privilege(1, 'allow_html', false);
    }

    public function testInputFilter()
    {
        global $FORCE_INPUT_FILTER_FOR_ALL;
        $FORCE_INPUT_FILTER_FOR_ALL = true;

        global $MEMBER_CACHED;
        $MEMBER_CACHED = 1;
        global $PRIVILEGE_CACHE;
        $PRIVILEGE_CACHE[get_member()]['unfiltered_input'][''][''][''] = 0;

        $_GET['foo'] = '_config.php';
        $this->assertTrue(strpos(get_param_string('foo'), '_config.php') === false);

        $_GET['foo'] = '<script>';
        $this->assertTrue(strpos(get_param_string('foo'), '<script') === false);

        $_GET['redirect'] = 'http://example.com/';
        $this->assertTrue(strpos(get_param_string('foo'), 'http://example.com/') === false);
    }

    private $found_error = null;

    public function _temp_handler($errornum, $errormsg)
    {
        $this->found_error = $errormsg;
        return false;
    }

    public function testXSSDetectorOnAndWorking()
    {
        $php_errormsg = mixed();
        $this->found_error = null;
        $temp = set_error_handler(array($this, '_temp_handler'));

        safe_ini_set('ocproducts.xss_detect', '1');

        ob_start();
        @print(get_param_string('id')); // Print an unverified input parameter, but suppress our XSS error
        ob_end_clean();

        safe_ini_set('ocproducts.xss_detect', '0');

        set_error_handler($temp);

        $setting = ini_get('ocproducts.xss_detect');
        if ($setting !== false) {
            $this->assertTrue(strpos($php_errormsg, 'XSS vulnerability') !== false, ($setting === false) ? 'ocProducts PHP not running' : '%s');
        }
    }

    public function testXSSDetectorOnAndWorkingComplex1()
    {
        $php_errormsg = mixed();
        $this->found_error = null;
        $temp = set_error_handler(array($this, '_temp_handler'));

        safe_ini_set('ocproducts.xss_detect', '1');

        ob_start();
        $tpl = do_template('PARAGRAPH', array('_GUID' => '8bca69a1088b0ca260321cd3117aabbe', 'TEXT' => get_param_string('id')));
        @$tpl->evaluate_echo();
        ob_end_clean();

        safe_ini_set('ocproducts.xss_detect', '0');

        set_error_handler($temp);

        $setting = ini_get('ocproducts.xss_detect');
        if ($setting !== false) {
            $this->assertTrue(strpos($this->found_error, 'XSS vulnerability') !== false, ($setting === false) ? 'ocProducts PHP not running' : '%s');
        }
    }

    public function testXSSDetectorOnAndWorkingComplex2()
    {
        $php_errormsg = mixed();
        $this->found_error = null;
        $temp = set_error_handler(array($this, '_temp_handler'));

        safe_ini_set('ocproducts.xss_detect', '1');

        ob_start();
        $_tpl = do_template('PARAGRAPH', array('_GUID' => '809e41570771a797998d59f8e3dc7a0b', 'TEXT' => get_param_string('id')));
        $tpl = do_template('PARAGRAPH', array('_GUID' => '89dd3a60565dab73c2796c8a754095ba', 'TEXT' => $_tpl));
        @$tpl->evaluate_echo();
        ob_end_clean();

        safe_ini_set('ocproducts.xss_detect', '0');

        set_error_handler($temp);

        $setting = ini_get('ocproducts.xss_detect');
        if ($setting !== false) {
            $this->assertTrue(strpos($this->found_error, 'XSS vulnerability') !== false, ($setting === false) ? 'ocProducts PHP not running' : '%s');
        }
    }

    public function testXSSDetectorOnAndWorkingComplex3()
    {
        $php_errormsg = mixed();
        $this->found_error = null;
        $temp = set_error_handler(array($this, '_temp_handler'));

        safe_ini_set('ocproducts.xss_detect', '1');

        ob_start();
        $_tpl = new Tempcode();
        $_tpl->attach(do_template('PARAGRAPH', array('_GUID' => 'a9e8285ac5ef71d93eedaaf6b81f4384', 'TEXT' => get_param_string('id'))));
        $tpl = do_template('PARAGRAPH', array('_GUID' => 'd2e942317451ffed9a5d75c13c85a350', 'TEXT' => $_tpl));
        @$tpl->evaluate_echo();
        ob_end_clean();

        safe_ini_set('ocproducts.xss_detect', '0');

        set_error_handler($temp);

        $setting = ini_get('ocproducts.xss_detect');
        if ($setting !== false) {
            $this->assertTrue(strpos($this->found_error, 'XSS vulnerability') !== false, ($setting === false) ? 'ocProducts PHP not running' : '%s');
        }
    }
}

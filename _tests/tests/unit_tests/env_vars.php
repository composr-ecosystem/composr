<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

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
class env_vars_test_set extends cms_test_case
{
    protected $bak;

    public function __construct()
    {
        unset($_GET['keep_devtest']);

        // Test assumes data is correct on this server, and is in $_SERVER -- only weird servers don't comply, and we're making sure we can support those weird servers
        $this->bak = $_SERVER;
    }

    protected function wipe_data($blankify)
    {
        foreach (['DOCUMENT_ROOT', 'PHP_SELF', /*Derived in front controller 'SCRIPT_FILENAME', */'SCRIPT_NAME', 'REQUEST_URI', 'QUERY_STRING'] as $var) {
            if ($blankify) {
                $_SERVER[$var] = '';
                $_ENV[$var] = '';
            } else {
                unset($_SERVER[$var]);
                unset($_ENV[$var]);
            }
        }
    }

    protected function default_doc_normalise($url)
    {
        return str_replace('index.php', '', $url);
    }

    public function testMissing_DOCUMENT_ROOT()
    {
        if (is_cli()) {
            return;
        }

        // We know we cannot accurately rebuild the document root if we are running under a symlink
        //  This is inaccurate, but will bail out if it finds a top level symlink to our install under the document root
        $d = $_SERVER['DOCUMENT_ROOT'];
        $dh = @opendir($d);
        if ($dh !== false) {
            while (($f = readdir($dh)) !== false) {
                if ((is_link($d . '/' . $f)) && (readlink($d . '/' . $f) == get_file_base())) {
                    closedir($dh);
                    return;
                }
            }
            closedir($dh);
        }

        $this->wipe_data(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['DOCUMENT_ROOT'] == $this->bak['DOCUMENT_ROOT'], 'Fixed DOCUMENT_ROOT to ' . $_SERVER['DOCUMENT_ROOT'] . ', expected ' . $this->bak['DOCUMENT_ROOT']);

        $this->wipe_data(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['DOCUMENT_ROOT'] == $this->bak['DOCUMENT_ROOT'], 'Fixed DOCUMENT_ROOT to ' . $_SERVER['DOCUMENT_ROOT'] . ', expected ' . $this->bak['DOCUMENT_ROOT']);
    }

    public function testMissing_PHP_SELF()
    {
        if (is_cli()) {
            return;
        }

        $this->wipe_data(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['PHP_SELF'] == $this->bak['PHP_SELF'], 'Fixed PHP_SELF to ' . $_SERVER['PHP_SELF'] . ', expected ' . $this->bak['PHP_SELF']);

        $this->wipe_data(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['PHP_SELF'] == $this->bak['PHP_SELF'], 'Fixed PHP_SELF to ' . $_SERVER['PHP_SELF'] . ', expected ' . $this->bak['PHP_SELF']);
    }

    public function testMissing_SCRIPT_FILENAME()
    {
        $this->wipe_data(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['SCRIPT_FILENAME'] == $this->bak['SCRIPT_FILENAME'], 'Fixed SCRIPT_FILENAME to ' . $_SERVER['SCRIPT_FILENAME'] . ', expected ' . $this->bak['SCRIPT_FILENAME']);

        $this->wipe_data(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['SCRIPT_FILENAME'] == $this->bak['SCRIPT_FILENAME'], 'Fixed SCRIPT_FILENAME to ' . $_SERVER['SCRIPT_FILENAME'] . ', expected ' . $this->bak['SCRIPT_FILENAME']);
    }

    public function testMissing_SCRIPT_NAME()
    {
        if (is_cli()) {
            return;
        }

        $this->wipe_data(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['SCRIPT_NAME'] == $this->bak['SCRIPT_NAME'], 'Fixed SCRIPT_NAME to ' . $_SERVER['SCRIPT_NAME'] . ', expected ' . $this->bak['SCRIPT_NAME']);

        $this->wipe_data(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['SCRIPT_NAME'] == $this->bak['SCRIPT_NAME'], 'Fixed SCRIPT_NAME to ' . $_SERVER['SCRIPT_NAME'] . ', expected ' . $this->bak['SCRIPT_NAME']);
    }

    public function testMissing_REQUEST_URI()
    {
        if (is_cli()) {
            return;
        }

        $this->wipe_data(true);
        fixup_bad_php_env_vars();
        $got = urldecode($this->default_doc_normalise($_SERVER['REQUEST_URI']));
        $expected = urldecode($this->default_doc_normalise($this->bak['REQUEST_URI']));
        $this->assertTrue($got == $expected, 'Fixed REQUEST_URI to ' . $got . ' (after decoding), expected ' . $expected);

        $this->wipe_data(false);
        fixup_bad_php_env_vars();
        $got = urldecode($this->default_doc_normalise($_SERVER['REQUEST_URI']));
        $expected = urldecode($this->default_doc_normalise($this->bak['REQUEST_URI']));
        $this->assertTrue($got == $expected, 'Fixed REQUEST_URI to ' . $got . ' (after decoding), expected ' . $expected);
    }

    public function testMissing_QUERY_STRING()
    {
        if (is_cli()) {
            return;
        }

        $this->wipe_data(true);
        fixup_bad_php_env_vars();
        $this->assertTrue(urldecode($_SERVER['QUERY_STRING']) == urldecode($this->bak['QUERY_STRING']), 'Fixed QUERY_STRING to ' . $_SERVER['QUERY_STRING'] . ', expected ' . $this->bak['QUERY_STRING']);

        $this->wipe_data(false);
        fixup_bad_php_env_vars();
        $this->assertTrue(urldecode($_SERVER['QUERY_STRING']) == urldecode($this->bak['QUERY_STRING']), 'Fixed QUERY_STRING to ' . $_SERVER['QUERY_STRING'] . ', expected ' . $this->bak['QUERY_STRING']);
    }
}

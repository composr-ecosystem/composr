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
class env_vars_test_set extends cms_test_case
{
    protected $bak;

    public function __construct()
    {
        unset($_GET['keep_devtest']);

        // Test assumes data is correct on this server, and is in $_SERVER -- only weird servers don't comply, and we're making sure we can support those weird servers
        $this->bak = $_SERVER;
    }

    protected function wipeData($blankify)
    {
        foreach (array('DOCUMENT_ROOT', 'PHP_SELF', /*Derived in front controller 'SCRIPT_FILENAME', */'SCRIPT_NAME', 'REQUEST_URI', 'QUERY_STRING') as $var) {
            if ($blankify) {
                $_SERVER[$var] = '';
                $_ENV[$var] = '';
            } else {
                unset($_SERVER[$var]);
                unset($_ENV[$var]);
            }
        }
    }

    protected function defaultDocNormalise($url)
    {
        return str_replace('index.php', '', $url);
    }

    public function testMissing_DOCUMENT_ROOT()
    {
        $this->wipeData(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['DOCUMENT_ROOT'] == $this->bak['DOCUMENT_ROOT'], 'DOCUMENT_ROOT is missing after wipe_data blankify');

        $this->wipeData(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['DOCUMENT_ROOT'] == $this->bak['DOCUMENT_ROOT'], 'DOCUMENT_ROOT is missing after wipe_data non-blankify');
    }

    public function testMissing_PHP_SELF()
    {
        $this->wipeData(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['PHP_SELF'] == $this->bak['PHP_SELF'], 'PHP_SELF is missing after wipe_data blankify');

        $this->wipeData(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['PHP_SELF'] == $this->bak['PHP_SELF'], 'PHP_SELF is missing after wipe_data non-blankify');
    }

    public function testMissing_SCRIPT_FILENAME()
    {
        $this->wipeData(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['SCRIPT_FILENAME'] == $this->bak['SCRIPT_FILENAME'], 'SCRIPT_FILENAME is missing after wipe_data blankify');

        $this->wipeData(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['SCRIPT_FILENAME'] == $this->bak['SCRIPT_FILENAME'], 'SCRIPT_FILENAME is missing after wipe_data non-blankify');
    }

    public function testMissing_SCRIPT_NAME()
    {
        $this->wipeData(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['SCRIPT_NAME'] == $this->bak['SCRIPT_NAME'], 'SCRIPT_NAME is missing after wipe_data blankify');

        $this->wipeData(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['SCRIPT_NAME'] == $this->bak['SCRIPT_NAME'], 'SCRIPT_FILENAME is missing after wipe_data non-blankify');
    }

    public function testMissing_REQUEST_URI()
    {
        $this->wipeData(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($this->defaultDocNormalise($_SERVER['REQUEST_URI']) == $this->defaultDocNormalise($this->bak['REQUEST_URI']), 'REQUEST_URI is missing after wipe_data blankify');

        $this->wipeData(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($this->defaultDocNormalise($_SERVER['REQUEST_URI']) == $this->defaultDocNormalise($this->bak['REQUEST_URI']), 'REQUEST_URI is missing after wipe_data non-blankify');
    }

    public function testMissing_QUERY_STRING()
    {
        $this->wipeData(true);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['QUERY_STRING'] == $this->bak['QUERY_STRING'], 'QUERY_STRING is missing after wipe_data blankify');

        $this->wipeData(false);
        fixup_bad_php_env_vars();
        $this->assertTrue($_SERVER['QUERY_STRING'] == $this->bak['QUERY_STRING'], 'QUERY_STRING is missing after wipe_data non-blankify');
    }
}

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

    protected function runTest($param_name, $normalise) {
        $this->wipeData(true);
        fixup_bad_php_env_vars();
        $test1 = $normalise ? $this->defaultDocNormalise($_SERVER[$param_name]) : $_SERVER[$param_name];
        $test2 = $normalise ? $this->defaultDocNormalise($this->bak[$param_name]) : $this->bak[$param_name];
        $this->assertTrue($test1 == $test2, $param_name . ' changed or is missing after wipe_data blankify + fixup_bad_php_env_vars');
        if (isset($_GET['debug'])) {
            @var_dump($test1, $test2);
        }

        $this->wipeData(false);
        fixup_bad_php_env_vars();
        $test1 = $normalise ? $this->defaultDocNormalise($_SERVER[$param_name]) : $_SERVER[$param_name];
        $test2 = $normalise ? $this->defaultDocNormalise($this->bak[$param_name]) : $this->bak[$param_name];
        $this->assertTrue($test1 == $test2, $param_name . ' changed or is missing after wipe_data non-blankify + fixup_bad_php_env_vars');
        if (isset($_GET['debug'])) {
            @var_dump($test1, $test2);
        }
    }

    public function testBadEnvVars()
    {
        $params = array(
            'DOCUMENT_ROOT' => false,
            'PHP_SELF' => false,
            'SCRIPT_FILENAME' => false,
            'SCRIPT_NAME' => false,
            'REQUEST_URI' => true,
            'QUERY_STRING' => false,
        );
        foreach ($params as $param => $normalise) {
            $this->runTest($param, $normalise);
        }
    }
}

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
class http_obscure_cases_test_set extends cms_test_case
{
    // COR is very easy to accidentally break due to it running early in bootstrap and not being used much
    public function testCOR()
    {
        global $HTTP_HEADERS;

        $extra_headers = array('Origin' => 'http://example.com');
        $http_verb = 'GET';
        $test = http_download_file(static_evaluate_tempcode(build_url(array('page' => ''), '')), null, true, false, 'Composr', null, null, null, null, null, null, null, null, 6.0, false, null, $extra_headers, $http_verb);
        if (!empty($_GET['debug'])) { // TODO: Change in v11
            var_dump($HTTP_HEADERS);
        }
        $this->assertTrue($test != '');
        $this->assertTrue(!$this->hasCORHeader('Access-Control-Allow-Origin'));
        $this->assertTrue(!$this->hasCORHeader('Access-Control-Allow-Credentials'));

        $extra_headers = array('Origin' => get_base_url());
        $http_verb = 'GET';
        $test = http_download_file(static_evaluate_tempcode(build_url(array('page' => ''), '')), null, true, false, 'Composr', null, null, null, null, null, null, null, null, 6.0, false, null, $extra_headers, $http_verb);
        if (!empty($_GET['debug'])) { // TODO: Change in v11
            var_dump($HTTP_HEADERS);
        }
        $this->assertTrue($test != '');
        $this->assertTrue($this->hasCORHeader('Access-Control-Allow-Origin'));
        $this->assertTrue(!$this->hasCORHeader('Access-Control-Allow-Credentials'));

        $extra_headers = array('Origin' => get_base_url());
        $http_verb = 'OPTIONS';
        $test = http_download_file(static_evaluate_tempcode(build_url(array('page' => ''), '')), null, false, false, 'Composr', null, null, null, null, null, null, null, null, 6.0, false, null, $extra_headers, $http_verb);
        if (!empty($_GET['debug'])) { // TODO: Change in v11
            var_dump($HTTP_HEADERS);
        }
        $this->assertTrue($test == '');
        $this->assertTrue($this->hasCORHeader('Access-Control-Allow-Origin'));
        $this->assertTrue($this->hasCORHeader('Access-Control-Allow-Credentials'));
    }

    protected function hasCORHeader($header)
    {
        global $HTTP_HEADERS;
        $found = false;
        foreach ($HTTP_HEADERS as $line) {
            if (preg_match("#^" . $header . ": .*#i", $line) != 0) {
                $found = true;
            }
        }
        return $found;
    }
}

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
class http_obscure_cases_test_set extends cms_test_case
{
    // COR is very easy to accidentally break due to it running early in bootstrap and not being used much
    public function testCOR()
    {
        $extra_headers = ['Origin' => 'http://example.com'];
        $http_verb = 'GET';
        $response = cms_http_request(get_base_url() . '/index.php', ['extra_headers' => $extra_headers, 'http_verb' => $http_verb, 'timeout' => 20.0]);
        $this->assertTrue($response->data != '');
        if ($this->debug) {
            var_dump($response);
        }
        $this->assertTrue(!$this->has_cor_header($response, 'Access-Control-Allow-Origin'), 'Unexpected allowed origin (example.com)');
        $this->assertTrue(!$this->has_cor_header($response, 'Access-Control-Allow-Credentials'), 'Unexpected credentials header (example.com)');

        $extra_headers = ['Origin' => get_base_url()];
        $http_verb = 'GET';
        $response = cms_http_request(get_base_url() . '/index.php', ['extra_headers' => $extra_headers, 'http_verb' => $http_verb, 'timeout' => 20.0]);
        if ($this->debug) {
            var_dump($response);
        }
        $this->assertTrue($response->data != '');
        $this->assertTrue($this->has_cor_header($response, 'Access-Control-Allow-Origin'), 'Should have allowed origin [GET]');
        $this->assertTrue(!$this->has_cor_header($response, 'Access-Control-Allow-Credentials'), 'Unexpected credentials header [GET]');

        $extra_headers = ['Origin' => get_base_url()];
        $http_verb = 'OPTIONS';
        $response = cms_http_request(get_base_url() . '/index.php', ['extra_headers' => $extra_headers, 'http_verb' => $http_verb, 'timeout' => 20.0]);
        if ($this->debug) {
            var_dump($response);
        }
        $this->assertTrue($response->data == '');
        $this->assertTrue($this->has_cor_header($response, 'Access-Control-Allow-Origin'), 'Should have allowed origin [OPTIONS]');
        $this->assertTrue($this->has_cor_header($response, 'Access-Control-Allow-Credentials'), 'Unexpected credentials header [OPTIONS]');
    }

    protected function has_cor_header($response, $header)
    {
        $found = false;
        foreach ($response->headers as $line) {
            $matches = [];
            if (preg_match("#^" . $header . ": .*#i", $line) != 0) {
                $found = true;
            }
        }
        return $found;
    }
}

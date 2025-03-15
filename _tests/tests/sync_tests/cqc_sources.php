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

/**
 * Composr test case class (unit testing).
 */
class cqc_sources_test_set extends cms_test_case
{
    public function testSources()
    {
        $message = 'You may need to re-run _cqc_function_sigs after making changes to function signatures or PHPDocs.';
        $this->dump($message, 'INFO:');

        cms_set_time_limit(120);
        $url = get_base_url() . '/_tests/codechecker/codechecker.php?subdir=sources&filter_avoid=cns_.*&avoid=forum,database,hooks,blocks,persistent_caching,diff,isocodes,imap';
        $url = $this->extend_cqc_call($url);
        $result = http_get_contents($url, ['timeout' => 10000.0]);
        foreach (explode('<br />', $result) as $line) {
            $this->assertTrue($this->should_filter_cqc_line($line), $line);
        }
    }
}

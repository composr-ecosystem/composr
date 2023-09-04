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
class ___form_to_email_test_set extends cms_test_case
{
    public function testFormToEmail()
    {
        global $SITE_INFO;
        if ((isset($SITE_INFO['no_email_output'])) && (($SITE_INFO['no_email_output'] == '1') || (!empty($SITE_INFO['redirect_email_output'])))) {
            $this->assertTrue(false, 'Test will not work if no_email_output or redirect_email_output is set');
            return;
        }

        $GLOBALS['SITE_DB']->query_delete('logged_mail_messages');

        $bak = get_option('mail_queue_debug');
        set_option('mail_queue_debug', '1');
        $url = find_script('form_to_email');
        $result = cms_http_request($url, ['trigger_error' => false, 'post_params' => ['foo' => 'bar']]);
        set_option('mail_queue_debug', $bak);

        if ($this->debug) {
            @var_dump($result);
        }

        $rows = $GLOBALS['SITE_DB']->query_select('logged_mail_messages', ['*'], [], 'ORDER BY m_date_and_time DESC', 2);
        foreach ($rows as $row) {
            $this->assertTrue(strpos($row['m_message'], 'bar') !== false);
        }

        $this->assertTrue((count($rows) == 1), 'Expected 1 but got ' . strval(count($rows)));
    }
}

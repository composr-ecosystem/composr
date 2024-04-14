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
class _setupwizard_test_set extends cms_test_case
{
    public function testFinalStep()
    {
        if ((get_db_type() == 'xml') && (multi_lang_content())) {
            $this->assertTrue(false, 'Test cannot run on XML database driver with multi-lang-content, too slow');
            return;
        }

        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);

        $session_id = $this->establish_admin_callback_session();

        $post_params = [
            'skip_9' => '0',
            'skip_8' => '0',
            'skip_7' => '0',
            'skip_6' => '0',
            'skip_5' => '0',
            'skip_4' => '1',
            'skip_3' => '0',
            'installprofile' => '',
            'site_name' => '(testing)',
            'description' => '',
            'site_scope' => 'defaultness',
            'keywords' => 'default, defaultness, celebration, community',
            'google_analytics' => '',
            'have_default_banners_donation' => '1',
            'have_default_banners_advertising' => '1',
            'have_default_catalogues_projects' => '1',
            'have_default_catalogues_faqs' => '1',
            'have_default_catalogues_links' => '1',
            'have_default_catalogues_contacts' => '1',
            'rank_set' => 'fun',
            'have_default_full_emoticon_set' => '1',
            'have_default_cpf_set' => '1',
            'keep_news_categories' => '1',
            'have_default_wordfilter' => '1',
            'show_screen_actions' => '1',
            'block_SITE_main_content' => 'YES',
            'block_SITE_main_newsletter_signup' => 'PANEL_RIGHT',
            'block_SITE_side_stats' => 'PANEL_RIGHT',
            'block_SITE_side_users_online' => 'PANEL_RIGHT',
            'rules' => 'balanced',
            'seed_hex' => '#784468',
            'label_for__site_closed' => 'Closed site',
            'site_closed' => '1',
            'tick_on_form__site_closed' => '0',
            'require__site_closed' => '0',
            'label_for__closed' => 'Message',
            'closed' => 'This site is currently closed because it is still being created. The webmaster(s) will open it up when they are ready.',
            'pre_f_closed' => '1',
            'require__closed' => '0',
            'security_level' => 'low',
            'timezone' => 'Europe/London',
        ];
        require_code('csrf_filter');
        $post_params['csrf_token'] = generate_csrf_token();

        $url = build_url(['page' => 'admin_setupwizard', 'type' => 'step10', 'keep_fatalistic' => 1], 'adminzone');

        $http = cms_http_request($url->evaluate(), ['convert_to_internal_encoding' => true, 'ignore_http_status' => true, 'timeout' => 20.0, 'trigger_error' => false, 'post_params' => $post_params, 'cookies' => [get_session_cookie() => $session_id]]);

        $ok = ($http->message == '200');

        if ((!$ok) && ($this->debug)) {
            @var_dump($http->data);
        }

        $this->assertTrue($ok, 'Failed to execute final Setup Wizard step');
    }
}

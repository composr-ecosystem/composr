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

// php _tests/index.php _setupwizard

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
            'site_name' => '(unnamed)',
            'description' => '',
            'site_scope' => 'defaultness',
            'header_text' => '',
            'copyright' => 'Copyright &copy; $CURRENT_YEAR=' . date('Y') . ' (unnamed)',
            'staff_address' => 'staff@localhost',
            'keywords' => 'default, defaultness, celebration, community',
            'google_analytics' => '',
            'fixed_width' => '1',
            'have_default_banners_donation' => '1',
            'have_default_banners_advertising' => '1',
            'have_default_catalogues_projects' => '1',
            'have_default_catalogues_faqs' => '1',
            'have_default_catalogues_links' => '1',
            'have_default_catalogues_contacts' => '1',
            'have_default_rank_set' => '1',
            'have_default_full_emoticon_set' => '1',
            'have_default_cpf_set' => '1',
            'keep_news_categories' => '1',
            'have_default_wordfilter' => '1',
            'show_screen_actions' => '1',
            'block_SITE_main_awards' => 'NO',
            'block_SITE_main_forum_news' => 'NO',
            'block_SITE_main_forum_topics' => 'NO',
            'block_SITE_main_image_fader' => 'NO',
            'block_SITE_main_news' => 'YES',
            'block_SITE_main_poll' => 'YES_CELL',
            'block_SITE_main_quotes' => 'NO',
            'block_SITE_main_rss' => 'NO',
            'block_SITE_main_newsletter_signup' => 'PANEL_RIGHT',
            'block_SITE_main_search' => 'PANEL_NONE',
            'block_SITE_side_calendar' => 'PANEL_RIGHT',
            'block_SITE_side_cns_private_topics' => 'PANEL_NONE',
            'block_SITE_side_forum_news' => 'PANEL_NONE',
            'block_SITE_side_galleries' => 'PANEL_NONE',
            'block_SITE_side_news' => 'PANEL_NONE',
            'block_SITE_side_news_archive' => 'PANEL_NONE',
            'block_SITE_side_news_categories' => 'PANEL_RIGHT',
            'block_SITE_side_rss' => 'PANEL_NONE',
            'block_SITE_side_shoutbox' => 'PANEL_RIGHT',
            'block_SITE_side_stats' => 'PANEL_RIGHT',
            'block_SITE_side_tag_cloud' => 'PANEL_RIGHT',
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

        $http = cms_http_request($url->evaluate(), ['convert_to_internal_encoding' => true, 'ignore_http_status' => true, 'timeout' => 300.0, 'trigger_error' => false, 'post_params' => $post_params, 'cookies' => [get_session_cookie() => $session_id]]);

        $ok = ($http->message == '200');

        if ((!$ok) && ($this->debug)) {
            @var_dump($http->data);
        }

        $this->assertTrue($ok, 'Failed to execute final Setup Wizard step');
    }
}

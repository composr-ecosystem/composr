<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    language_ES
 */

/**
 * Hook class.
 */
class Hook_addon_registry_language_ES
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Translations';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'brandziel, jhmorenof, blasfe, Killyhop, ruben.alfonsin';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array();
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Translation into Español.

Completeness: 47% (29% typically means translated fully apart from administrative strings).

This addon was automatically bundled from community contributions provided on Transifex and will be routinely updated alongside new Composr patch releases.

Translations may also be downloaded directly from Transifex.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array('tut_intl', 'tut_intl_users');
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/adminzone/style/language/language.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/language_ES.php',
            'adminzone/pages/comcode_custom/ES/netlink.txt',
            'adminzone/pages/comcode_custom/ES/comcode_whitelist.txt',
            'adminzone/pages/comcode_custom/ES/insults.txt',
            'adminzone/pages/comcode_custom/ES/referrals.txt',
            'buildr/pages/comcode_custom/ES/docs.txt',
            'buildr/pages/comcode_custom/ES/rules.txt',
            'buildr/pages/comcode_custom/ES/start.txt',
            'collaboration/pages/comcode_custom/ES/about.txt',
            'collaboration/pages/comcode_custom/ES/start.txt',
            'data_custom/modules/cms_comcode_pages/ES/rules_balanced.txt',
            'data_custom/modules/cms_comcode_pages/ES/rules_corporate.txt',
            'data_custom/modules/cms_comcode_pages/ES/rules_liberal.txt',
            'pages/comcode_custom/ES/404.txt',
            'pages/comcode_custom/ES/feedback.txt',
            'pages/comcode_custom/ES/privacy.txt',
            'pages/comcode_custom/ES/sitemap.txt',
            'pages/comcode_custom/ES/start.txt',
            'site/pages/comcode_custom/ES/help.txt',
            'site/pages/comcode_custom/ES/start.txt',
            'site/pages/comcode_custom/ES/userguide_chatcode.txt',
            'site/pages/comcode_custom/ES/userguide_comcode.txt',
            'text_custom/ES/too_common_words.txt',
            'text_custom/ES/insults.txt',
            'lang_custom/ES/banners.ini',
            'lang_custom/ES/ecommerce.ini',
            'lang_custom/ES/javascript.ini',
            'lang_custom/ES/staff.ini',
            'lang_custom/ES/zones.ini',
            'lang_custom/ES/wordfilter.ini',
            'lang_custom/ES/points.ini',
            'lang_custom/ES/commandr.ini',
            'lang_custom/ES/news.ini',
            'lang_custom/ES/trackbacks.ini',
            'lang_custom/ES/cns_multi_moderations.ini',
            'lang_custom/ES/authors.ini',
            'lang_custom/ES/cleanup.ini',
            'lang_custom/ES/menus.ini',
            'lang_custom/ES/profiling.ini',
            'lang_custom/ES/calendar.ini',
            'lang_custom/ES/cns_member_directory.ini',
            'lang_custom/ES/addons.ini',
            'lang_custom/ES/errorlog.ini',
            'lang_custom/ES/locations.ini',
            'lang_custom/ES/import.ini',
            'lang_custom/ES/pointstore.ini',
            'lang_custom/ES/supermembers.ini',
            'lang_custom/ES/quiz.ini',
            'lang_custom/ES/leader_board.ini',
            'lang_custom/ES/security.ini',
            'lang_custom/ES/cns_welcome_emails.ini',
            'lang_custom/ES/lang.ini',
            'lang_custom/ES/cns_warnings.ini',
            'lang_custom/ES/cns_special_cpf.ini',
            'lang_custom/ES/backups.ini',
            'lang_custom/ES/blocks.ini',
            'lang_custom/ES/tickets.ini',
            'lang_custom/ES/mail.ini',
            'lang_custom/ES/cns_config.ini',
            'lang_custom/ES/galleries.ini',
            'lang_custom/ES/catalogues.ini',
            'lang_custom/ES/metadata.ini',
            'lang_custom/ES/encryption.ini',
            'lang_custom/ES/permissions.ini',
            'lang_custom/ES/config.ini',
            'lang_custom/ES/email_log.ini',
            'lang_custom/ES/aggregate_types.ini',
            'lang_custom/ES/webstandards.ini',
            'lang_custom/ES/shopping.ini',
            'lang_custom/ES/comcode.ini',
            'lang_custom/ES/chat.ini',
            'lang_custom/ES/password_rules.ini',
            'lang_custom/ES/lookup.ini',
            'lang_custom/ES/tasks.ini',
            'lang_custom/ES/decision_tree.ini',
            'lang_custom/ES/group_member_timeouts.ini',
            'lang_custom/ES/cns_lurkers.ini',
            'lang_custom/ES/filedump.ini',
            'lang_custom/ES/critical_error.ini',
            'lang_custom/ES/version.ini',
            'lang_custom/ES/bookmarks.ini',
            'lang_custom/ES/abstract_file_manager.ini',
            'lang_custom/ES/upgrade.ini',
            'lang_custom/ES/ssl.ini',
            'lang_custom/ES/quotes.ini',
            'lang_custom/ES/themes.ini',
            'lang_custom/ES/downloads.ini',
            'lang_custom/ES/wiki.ini',
            'lang_custom/ES/awards.ini',
            'lang_custom/ES/messaging.ini',
            'lang_custom/ES/rss.ini',
            'lang_custom/ES/notifications.ini',
            'lang_custom/ES/captcha.ini',
            'lang_custom/ES/dearchive.ini',
            'lang_custom/ES/unvalidated.ini',
            'lang_custom/ES/debrand.ini',
            'lang_custom/ES/stats.ini',
            'lang_custom/ES/realtime_rain.ini',
            'lang_custom/ES/tips.ini',
            'lang_custom/ES/actionlog.ini',
            'lang_custom/ES/custom_comcode.ini',
            'lang_custom/ES/dates.ini',
            'lang_custom/ES/polls.ini',
            'lang_custom/ES/content_privacy.ini',
            'lang_custom/ES/do_next.ini',
            'lang_custom/ES/filtercode.ini',
            'lang_custom/ES/global.ini',
            'lang_custom/ES/installer.ini',
            'lang_custom/ES/staff_checklist.ini',
            'lang_custom/ES/content_reviews.ini',
            'lang_custom/ES/fields.ini',
            'lang_custom/ES/newsletter.ini',
            'lang_custom/ES/submitban.ini',
            'lang_custom/ES/search.ini',
            'lang_custom/ES/cns_post_templates.ini',
            'lang_custom/ES/redirects.ini',
            'lang_custom/ES/cns_privacy.ini',
            'lang_custom/ES/sms.ini',
            'lang_custom/ES/upload_syndication.ini',
            'lang_custom/ES/cns.ini',
            'lang_custom/ES/recommend.ini',
            'lang_custom/ES/cns_components.ini',
            'lang_custom/ES/tapatalk.ini',
            'lang_custom/ES/facebook.ini',
            'lang_custom/ES/composr_homesite.ini',
            'lang_custom/ES/activities.ini',
            'lang_custom/ES/twitter.ini',
            'lang_custom/ES/google_map_users.ini',
            'lang_custom/ES/google_map.ini',
            'lang_custom/ES/sites.ini',
            'lang_custom/ES/customers.ini',
            'lang_custom/ES/tutorials.ini',
        );
    }
}


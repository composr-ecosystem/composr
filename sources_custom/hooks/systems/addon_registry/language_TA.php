<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    language_TA
 */

/**
 * Hook class.
 */
class Hook_addon_registry_language_TA
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
        return 'unknown';
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
        return 'Translation into Tamil.

Completeness: 29% (29% typically means translated fully apart from administrative strings).

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
            'sources_custom/hooks/systems/addon_registry/language_TA.php',
            'sources_custom/lang_filter_TA.php',
            'data_custom/modules/cms_comcode_pages/TA/rules_balanced.txt',
            'data_custom/modules/cms_comcode_pages/TA/rules_corporate.txt',
            'data_custom/modules/cms_comcode_pages/TA/rules_liberal.txt',
            'pages/comcode_custom/TA/404.txt',
            'pages/comcode_custom/TA/_rules.txt',
            'pages/comcode_custom/TA/feedback.txt',
            'pages/comcode_custom/TA/keymap.txt',
            'pages/comcode_custom/TA/privacy.txt',
            'pages/comcode_custom/TA/sitemap.txt',
            'pages/comcode_custom/TA/start.txt',
            'site/pages/comcode_custom/TA/help.txt',
            'site/pages/comcode_custom/TA/popup_blockers.txt',
            'site/pages/comcode_custom/TA/userguide_chatcode.txt',
            'site/pages/comcode_custom/TA/userguide_comcode.txt',
            'text_custom/TA/too_common_words.txt',
            'lang_custom/TA/banners.ini',
            'lang_custom/TA/ecommerce.ini',
            'lang_custom/TA/javascript.ini',
            'lang_custom/TA/staff.ini',
            'lang_custom/TA/zones.ini',
            'lang_custom/TA/wordfilter.ini',
            'lang_custom/TA/points.ini',
            'lang_custom/TA/commandr.ini',
            'lang_custom/TA/news.ini',
            'lang_custom/TA/trackbacks.ini',
            'lang_custom/TA/cns_multi_moderations.ini',
            'lang_custom/TA/authors.ini',
            'lang_custom/TA/cleanup.ini',
            'lang_custom/TA/menus.ini',
            'lang_custom/TA/profiling.ini',
            'lang_custom/TA/calendar.ini',
            'lang_custom/TA/cns_member_directory.ini',
            'lang_custom/TA/addons.ini',
            'lang_custom/TA/errorlog.ini',
            'lang_custom/TA/locations.ini',
            'lang_custom/TA/import.ini',
            'lang_custom/TA/pointstore.ini',
            'lang_custom/TA/supermembers.ini',
            'lang_custom/TA/quiz.ini',
            'lang_custom/TA/leader_board.ini',
            'lang_custom/TA/security.ini',
            'lang_custom/TA/cns_welcome_emails.ini',
            'lang_custom/TA/lang.ini',
            'lang_custom/TA/cns_warnings.ini',
            'lang_custom/TA/cns_special_cpf.ini',
            'lang_custom/TA/backups.ini',
            'lang_custom/TA/blocks.ini',
            'lang_custom/TA/tickets.ini',
            'lang_custom/TA/mail.ini',
            'lang_custom/TA/cns_config.ini',
            'lang_custom/TA/galleries.ini',
            'lang_custom/TA/catalogues.ini',
            'lang_custom/TA/metadata.ini',
            'lang_custom/TA/encryption.ini',
            'lang_custom/TA/permissions.ini',
            'lang_custom/TA/config.ini',
            'lang_custom/TA/email_log.ini',
            'lang_custom/TA/aggregate_types.ini',
            'lang_custom/TA/webstandards.ini',
            'lang_custom/TA/shopping.ini',
            'lang_custom/TA/comcode.ini',
            'lang_custom/TA/chat.ini',
            'lang_custom/TA/password_rules.ini',
            'lang_custom/TA/lookup.ini',
            'lang_custom/TA/tasks.ini',
            'lang_custom/TA/decision_tree.ini',
            'lang_custom/TA/group_member_timeouts.ini',
            'lang_custom/TA/cns_lurkers.ini',
            'lang_custom/TA/filedump.ini',
            'lang_custom/TA/critical_error.ini',
            'lang_custom/TA/version.ini',
            'lang_custom/TA/bookmarks.ini',
            'lang_custom/TA/abstract_file_manager.ini',
            'lang_custom/TA/upgrade.ini',
            'lang_custom/TA/ssl.ini',
            'lang_custom/TA/quotes.ini',
            'lang_custom/TA/themes.ini',
            'lang_custom/TA/downloads.ini',
            'lang_custom/TA/wiki.ini',
            'lang_custom/TA/awards.ini',
            'lang_custom/TA/messaging.ini',
            'lang_custom/TA/rss.ini',
            'lang_custom/TA/notifications.ini',
            'lang_custom/TA/captcha.ini',
            'lang_custom/TA/dearchive.ini',
            'lang_custom/TA/unvalidated.ini',
            'lang_custom/TA/debrand.ini',
            'lang_custom/TA/stats.ini',
            'lang_custom/TA/realtime_rain.ini',
            'lang_custom/TA/tips.ini',
            'lang_custom/TA/actionlog.ini',
            'lang_custom/TA/custom_comcode.ini',
            'lang_custom/TA/dates.ini',
            'lang_custom/TA/polls.ini',
            'lang_custom/TA/content_privacy.ini',
            'lang_custom/TA/do_next.ini',
            'lang_custom/TA/filtercode.ini',
            'lang_custom/TA/global.ini',
            'lang_custom/TA/installer.ini',
            'lang_custom/TA/staff_checklist.ini',
            'lang_custom/TA/content_reviews.ini',
            'lang_custom/TA/fields.ini',
            'lang_custom/TA/newsletter.ini',
            'lang_custom/TA/submitban.ini',
            'lang_custom/TA/search.ini',
            'lang_custom/TA/cns_post_templates.ini',
            'lang_custom/TA/redirects.ini',
            'lang_custom/TA/cns_privacy.ini',
            'lang_custom/TA/sms.ini',
            'lang_custom/TA/upload_syndication.ini',
            'lang_custom/TA/cns.ini',
            'lang_custom/TA/recommend.ini',
            'lang_custom/TA/cns_components.ini',
        );
    }
}


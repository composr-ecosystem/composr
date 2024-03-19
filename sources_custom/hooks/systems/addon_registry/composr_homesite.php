<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite
 */

/**
 * Hook class.
 */
class Hook_addon_registry_composr_homesite
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for (used in generating the TAR filename).
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return null;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Development';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'This addon contains various aspects of composr.app:
 - Composr CMS release management and upgrading
 - composr.app addon management scripts
 - Composr CMS download scripts
 - The Composr deployment/hosting platform (Demonstratr)
 - Error message explainer system for Composr
 - Various other scripts for running composr.app

This addon does not contain the composr.app install code and the overall site and theme. That is not categorised into an addon, but is in the composr_homesite Git branch.
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [
                //'core_all_icons',
            ],
            'recommends' => [
                'downloads',
                'news',
                'tickets',
                'newsletter',
                'MySQL',
                'composr_homesite_support_credits',
                'composr_release_build',
                'composr_tutorials',
                'hybridauth',
                'patreon',
            ],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/tool.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'adminzone/pages/minimodules_custom/_make_release.php',
            'adminzone/pages/modules_custom/admin_cmsusers.php',
            'cmscore.rdf',
            'data_custom/composr_homesite_web_service.php',
            'data_custom/demonstratr_build_database.php',
            'data_custom/demonstratr_upgrade.php',
            'data_custom/download_composr.php',
            'data_custom/keys/.htaccess',
            'data_custom/keys/index.html',
            'lang_custom/EN/composr_homesite.ini',
            'lang_custom/EN/sites.ini',
            'pages/minimodules_custom/contact.php',
            'pages/minimodules_custom/licence.php',
            'site/pages/comcode_custom/EN/maintenance_status.txt',
            'site/pages/minimodules_custom/themeing_changes.php',
            'site/pages/modules_custom/sites.php',
            'sources_custom/cns_forumview.php',
            'sources_custom/composr_homesite.php',
            'sources_custom/errorservice.php',
            'sources_custom/hooks/blocks/main_staff_checklist/composr_homesite.php',
            'sources_custom/hooks/systems/addon_registry/composr_homesite.php',
            'sources_custom/hooks/systems/cron/site_cleanup.php',
            'sources_custom/hooks/systems/page_groupings/composr_homesite.php',
            'sources_custom/hooks/systems/privacy/composr_homesite.php',
            'sources_custom/hooks/systems/startup/composr_homesite__for_outdated_version.php',
            'sources_custom/hooks/systems/symbols/COMPOSR_HOMESITE_ID_COMMUNITY_SITES_CATEGORY.php',
            'sources_custom/hooks/systems/symbols/COMPOSR_HOMESITE_ID_LATEST_ADDONS.php',
            'sources_custom/hooks/systems/symbols/COMPOSR_HOMESITE_ID_LATEST_THEMES.php',
            'sources_custom/hooks/systems/symbols/COMPOSR_HOMESITE_ID_LATEST_TRANSLATIONS.php',
            'sources_custom/miniblocks/composr_homesite_download.php',
            'sources_custom/miniblocks/composr_homesite_featuretray.php',
            'sources_custom/miniblocks/composr_homesite_make_upgrader.php',
            'sources_custom/miniblocks/composr_maintenance_status.php',
            'sources_custom/miniblocks/main_version_support.php',
            'themes/default/images_custom/icons/composr_homesite/index.html',
            'themes/default/images_custom/icons/composr_homesite/theme_upgrade.svg',
            'themes/default/images_custom/icons/composr_homesite/translations_rough.svg',
            'themes/default/images_custom/icons_monochrome/composr_homesite/index.html',
            'themes/default/images_custom/icons_monochrome/composr_homesite/theme_upgrade.svg',
            'themes/default/images_custom/icons_monochrome/composr_homesite/translations_rough.svg',
            'themes/default/templates_custom/BLOCK_COMPOSR_MAINTENANCE_STATUS.tpl',
            'themes/default/templates_custom/CMS_BLOCK_MAIN_VERSION_SUPPORT.tpl',
            'themes/default/templates_custom/CMS_DOWNLOAD_BLOCK.tpl',
            'themes/default/templates_custom/CMS_DOWNLOAD_RELEASES.tpl',
            'themes/default/templates_custom/CMS_HOSTING_COPY_SUCCESS_SCREEN.tpl',
            'themes/default/templates_custom/CMS_SITES_SCREEN.tpl',
            'uploads/website_specific/composr.app/.htaccess',
            'uploads/website_specific/composr.app/banners.zip',
            'uploads/website_specific/composr.app/demonstratr/servers/index.html',
            'uploads/website_specific/composr.app/demonstratr/template.sql',
            'uploads/website_specific/composr.app/demonstratr/template.tar',
            'uploads/website_specific/composr.app/errorservice.csv',
            'uploads/website_specific/composr.app/facebook.html',
            'uploads/website_specific/composr.app/index.html',
            'uploads/website_specific/composr.app/logos/a.png',
            'uploads/website_specific/composr.app/logos/b.png',
            'uploads/website_specific/composr.app/logos/choice.php',
            'uploads/website_specific/composr.app/logos/default.png',
            'uploads/website_specific/composr.app/logos/index.html',
            'uploads/website_specific/composr.app/scripts/addon_manifest.php',
            'uploads/website_specific/composr.app/scripts/api.php',
            'uploads/website_specific/composr.app/scripts/build_personal_upgrader.php',
            'uploads/website_specific/composr.app/scripts/errorservice.php',
            'uploads/website_specific/composr.app/scripts/fetch_release_details.php',
            'uploads/website_specific/composr.app/scripts/goto_release_notes.php',
            'uploads/website_specific/composr.app/scripts/index.html',
            'uploads/website_specific/composr.app/scripts/newsletter_join.php',
            'uploads/website_specific/composr.app/scripts/testing.php',
            'uploads/website_specific/composr.app/scripts/user.php',
            'uploads/website_specific/composr.app/scripts/version.php',
            'uploads/website_specific/composr.app/upgrades/full/index.html',
            'uploads/website_specific/composr.app/upgrades/index.html',
            'uploads/website_specific/composr.app/upgrades/make_upgrader.php',
            'uploads/website_specific/composr.app/upgrades/sample_data/index.html',
            'uploads/website_specific/composr.app/upgrades/tar_build/index.html',
            'uploads/website_specific/composr.app/upgrades/tars/index.html',
        ];
    }
}

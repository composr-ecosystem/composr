<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_addon_registry_cms_homesite
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
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_md5.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.2'; // addon_version_auto_update c278ecd3eea5422aa73198f2db2d4453
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
        return 11.9;
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
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'This addon contains various aspects of the homesite:
 - CMS release management and upgrading
 - homesite addon management scripts
 - CMS download scripts
 - Telemetry service
 - Various other scripts for running the homesite

This addon does not contain the homesite install code and the overall site and theme. That is not categorised into an addon, but is in the Git branch.
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
                'addon_publish',
                'news',
                'tickets',
                'newsletter',
                'MySQL',
                'cms_release_build',
                'composr_tutorials',
                'hybridauth',
                'patreon',
            ],
            'conflicts_with' => [],
            'previously_in_addon' => ['composr_homesite'],
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
            'adminzone/pages/modules_custom/admin_telemetry.php',
            'cmscore.rdf',
            'data_custom/redirect_release_notes.php',
            'lang_custom/EN/cms_homesite.ini',
            'pages/minimodules_custom/contact.php',
            'pages/minimodules_custom/licence.php',
            'pages/modules_custom/telemetry.php',
            'site/pages/comcode_custom/EN/maintenance_status.txt',
            'site/pages/minimodules_custom/themeing_changes.php',
            'sources_custom/cms_homesite.php',
            'sources_custom/cms_homesite_make_upgrader.php',
            'sources_custom/cns_forumview.php',
            'sources_custom/errorservice.php',
            'sources_custom/hooks/blocks/main_staff_checklist/cms_homesite.php',
            'sources_custom/hooks/endpoints/cms_homesite/.htaccess',
            'sources_custom/hooks/endpoints/cms_homesite/addon_manifest.php',
            'sources_custom/hooks/endpoints/cms_homesite/dummy_data.php',
            'sources_custom/hooks/endpoints/cms_homesite/errorservice.php',
            'sources_custom/hooks/endpoints/cms_homesite/forum_posts.php',
            'sources_custom/hooks/endpoints/cms_homesite/forum_topics.php',
            'sources_custom/hooks/endpoints/cms_homesite/http_status_check.php',
            'sources_custom/hooks/endpoints/cms_homesite/index.html',
            'sources_custom/hooks/endpoints/cms_homesite/newsletter.php',
            'sources_custom/hooks/endpoints/cms_homesite/personal_upgrader.php',
            'sources_custom/hooks/endpoints/cms_homesite/ping.php',
            'sources_custom/hooks/endpoints/cms_homesite/release_details.php',
            'sources_custom/hooks/endpoints/cms_homesite/release_discontinued.php',
            'sources_custom/hooks/endpoints/cms_homesite/telemetry.php',
            'sources_custom/hooks/endpoints/cms_homesite/user_stats.php',
            'sources_custom/hooks/endpoints/cms_homesite/version.php',
            'sources_custom/hooks/modules/admin_import/cms_merge.php',
            'sources_custom/hooks/modules/admin_stats/cms_homesite.php',
            'sources_custom/hooks/systems/addon_registry/cms_homesite.php',
            'sources_custom/hooks/systems/cron/cmsusers.php',
            'sources_custom/hooks/systems/health_checks/gitlab.php',
            'sources_custom/hooks/systems/page_groupings/cms_homesite.php',
            'sources_custom/hooks/systems/privacy/cms_homesite.php',
            'sources_custom/hooks/systems/startup/cms_homesite__for_outdated_version.php',
            'sources_custom/hooks/systems/symbols/CMS_REPOS_URL.php',
            'sources_custom/miniblocks/cms_homesite_download.php',
            'sources_custom/miniblocks/cms_homesite_featuretray.php',
            'sources_custom/miniblocks/cms_homesite_make_upgrader.php',
            'sources_custom/miniblocks/cms_maintenance_status.php',
            'sources_custom/miniblocks/main_version_support.php',
            'sources_custom/points_escrow__sponsorship.php',
            'themes/default/images_custom/icons/cms_homesite/index.html',
            'themes/default/images_custom/icons/cms_homesite/theme_upgrade.svg',
            'themes/default/images_custom/icons/cms_homesite/translations_rough.svg',
            'themes/default/images_custom/icons_monochrome/cms_homesite/index.html',
            'themes/default/images_custom/icons_monochrome/cms_homesite/theme_upgrade.svg',
            'themes/default/images_custom/icons_monochrome/cms_homesite/translations_rough.svg',
            'themes/default/templates_custom/BLOCK_CMS_MAINTENANCE_STATUS.tpl',
            'themes/default/templates_custom/CMS_BLOCK_MAIN_VERSION_SUPPORT.tpl',
            'themes/default/templates_custom/CMS_DOWNLOAD_BLOCK.tpl',
            'themes/default/templates_custom/CMS_DOWNLOAD_RELEASES.tpl',
            'uploads/website_specific/cms_homesite/.htaccess',
            'uploads/website_specific/cms_homesite/banners.zip',
            'uploads/website_specific/cms_homesite/errorservice.csv',
            'uploads/website_specific/cms_homesite/facebook.html',
            'uploads/website_specific/cms_homesite/index.html',
            'uploads/website_specific/cms_homesite/logos/a.png',
            'uploads/website_specific/cms_homesite/logos/b.png',
            'uploads/website_specific/cms_homesite/logos/choice.php',
            'uploads/website_specific/cms_homesite/logos/default.png',
            'uploads/website_specific/cms_homesite/logos/index.html',
            'uploads/website_specific/cms_homesite/upgrades/index.html',
            'uploads/website_specific/cms_homesite/upgrades/sample_data/index.html',
            'uploads/website_specific/cms_homesite/upgrades/tar_build/index.html',
            'uploads/website_specific/cms_homesite/upgrades/tars/index.html',
        ];
    }

    /**
     * Install the addon.
     *
     * @param  ?float $upgrade_major_minor From what major/minor version we are upgrading (null: new install)
     * @param  ?integer $upgrade_patch From what patch version of $upgrade_major_minor we are upgrading (null: new install)
     */
    public function install(?float $upgrade_major_minor = null, ?int $upgrade_patch = null)
    {
        if (addon_installed('downloads') && addon_installed('addon_publish')) {
            // For software releases, we need our specialised download category to exist
            $download_category = brand_name() . ' Releases';
            $releases_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => db_get_first_id(), $GLOBALS['SITE_DB']->translate_field_ref('category') => $download_category]);
            if ($releases_category_id === null) {
                require_code('downloads2');
                require_code('permissions2');
                require_code('addon_publish');

                $releases_category_id = add_download_category($download_category, db_get_first_id(), $download_category);
                set_global_category_access('downloads', $releases_category_id);
                set_privilege_access('downloads', strval($releases_category_id), 'submit_midrange_content', false);
            }

            // We also need our addons category
            $addons_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => db_get_first_id(), $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Addons']);
            if ($addons_id === null) {
                require_code('addon_publish');
                $addons_id = find_addon_category_download_category('Addons'); // This will auto-create it
            }
        }
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        if (addon_installed('downloads')) {
            // Delete our download category for software releases
            $download_category = brand_name() . ' Releases';
            $releases_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => db_get_first_id(), $GLOBALS['SITE_DB']->translate_field_ref('category') => $download_category]);
            if ($releases_category_id !== null) {
                require_code('downloads2');
                delete_download_category($releases_category_id);
            }

            // Also delete addons category
            $addons_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => db_get_first_id(), $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Addons']);
            if ($addons_id !== null) {
                require_code('downloads2');
                delete_download_category($addons_id);
            }
        }
    }
}

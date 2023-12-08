<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/**
 * Hook class.
 */
class Hook_addon_registry_composr_homesite
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
        return 'Development';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
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
        return 'This addon contains various aspects of compo.sr:
 - Composr CMS release management and upgrading
 - compo.sr addon management scripts
 - Composr CMS download scripts
 - The Composr deployment/hosting platform (Demonstratr)
 - Error message explainer system for Composr
 - Various other scripts for running compo.sr

This addon does not contain the compo.sr install code and the overall site and theme. That is not categorised into an addon, but is in the composr_homesite git branch.
';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'downloads',
                'ecommerce',
                'news',
                'mysql',
            ),
            'recommends' => array(
                'composr_homesite_support_credits',
                'composr_release_build',
                'composr_tutorials',
            ),
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
        return 'themes/default/images/icons/48x48/menu/_generic_admin/tool.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/composr_homesite.php',
            'adminzone/pages/modules_custom/admin_cmsusers.php',
            'adminzone/pages/minimodules_custom/_make_release.php',
            'sources_custom/hooks/systems/page_groupings/composr_homesite.php',
            'data_custom/composr_homesite_web_service.php',
            'lang_custom/EN/sites.ini',
            'lang_custom/EN/composr_homesite.ini',
            'pages/minimodules_custom/licence.php',
            'site/pages/modules_custom/sites.php',
            'data_custom/download_composr.php',
            'uploads/website_specific/compo.sr/scripts/build_personal_upgrader.php',
            'sources_custom/composr_homesite.php',
            'sources_custom/hooks/systems/cron/site_cleanup.php',
            'uploads/website_specific/compo.sr/demonstratr/template.sql',
            'uploads/website_specific/compo.sr/demonstratr/template.tar',
            'uploads/website_specific/compo.sr/upgrades/tars/index.html',
            'data_custom/demonstratr_upgrade.php',
            'sources_custom/cns_forumview.php',
            'sources_custom/errorservice.php',
            'data_custom/patreons.csv',
            'data_custom/patreons.php',
            'sources_custom/patreons.php',
            'sources_custom/miniblocks/main_patreon_sponsors.php',
            'sources_custom/miniblocks/main_version_support.php',
            'themes/default/templates_custom/BLOCK_MAIN_PATREON_SPONSORS.tpl',
            'uploads/website_specific/compo.sr/errorservice.csv',
            'sources_custom/miniblocks/composr_homesite_featuretray.php',
            'sources_custom/miniblocks/composr_homesite_download.php',
            'themes/default/images_custom/help_small.gif',
            'sources_custom/miniblocks/composr_homesite_make_upgrader.php',
            'themes/default/templates_custom/CMS_DOWNLOAD_RELEASES.tpl',
            'themes/default/templates_custom/CMS_DOWNLOAD_BLOCK.tpl',
            'themes/default/templates_custom/CMS_HOSTING_COPY_SUCCESS_SCREEN.tpl',
            'themes/default/templates_custom/CMS_SITE.tpl',
            'themes/default/templates_custom/CMS_SITES_SCREEN.tpl',
            'themes/default/templates_custom/CMS_BLOCK_MAIN_VERSION_SUPPORT.tpl',
            'site/pages/minimodules_custom/themeing_changes.php',
            'uploads/website_specific/compo.sr/.htaccess',
            'uploads/website_specific/compo.sr/logos/a.png',
            'uploads/website_specific/compo.sr/logos/b.png',
            'uploads/website_specific/compo.sr/logos/choice.php',
            'uploads/website_specific/compo.sr/logos/default.png',
            'uploads/website_specific/compo.sr/logos/index.html',
            'uploads/website_specific/compo.sr/scripts/addon_manifest.php',
            'uploads/website_specific/compo.sr/scripts/errorservice.php',
            'uploads/website_specific/compo.sr/scripts/goto_release_notes.php',
            'uploads/website_specific/compo.sr/scripts/fetch_release_details.php',
            'uploads/website_specific/compo.sr/scripts/api.php',
            'uploads/website_specific/compo.sr/scripts/newsletter_join.php',
            'uploads/website_specific/compo.sr/scripts/user.php',
            'uploads/website_specific/compo.sr/scripts/testing.php',
            'uploads/website_specific/compo.sr/scripts/version.php',
            'uploads/website_specific/compo.sr/banners.zip',
            'uploads/website_specific/compo.sr/upgrades/make_upgrader.php',
            'uploads/website_specific/compo.sr/facebook.html',
            'uploads/website_specific/compo.sr/index.html',
            'cmscore.rdf',
            'pages/minimodules_custom/contact.php',
            'sources_custom/hooks/systems/symbols/COMPOSR_HOMESITE_ID_LATEST_ADDONS.php',
            'sources_custom/hooks/systems/symbols/COMPOSR_HOMESITE_ID_LATEST_THEMES.php',
            'sources_custom/hooks/systems/symbols/COMPOSR_HOMESITE_ID_LATEST_TRANSLATIONS.php',
            'sources_custom/hooks/systems/symbols/COMPOSR_HOMESITE_ID_COMMUNITY_SITES_CATEGORY.php',
            'sources_custom/hooks/systems/startup/composr_homesite__for_outdated_version.php',
        );
    }
}

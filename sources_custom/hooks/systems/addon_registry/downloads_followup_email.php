<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    downloads_followup_email
 */

/**
 * Hook class.
 */
class Hook_addon_registry_downloads_followup_email
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
        return 'New Features';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Jason Verhagen';
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
        return 'No License/No copyright asserted';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Follow-up email functionality to the Composr Downloads module.

By default it will scan your [tt]download_logging[/tt] database table for downloads for approximately the past 24 hours and send a follow-up email to each member that has downloaded any files.

Members can enable, disable, or change the notification type and also do it on a per-category basis from their Profile page->Edit tab->Notifications tab and making the desired changes to the \'Downloads follow-up email\' notification in the Content section of the Notifications tab.

Admins can force the follow-up emails and/or private topics and prevent the members from changing the settings by making the necessary changes in the Admin Zone->Setup->Notification Lock-down->\'Downloads follow-up email\' in the Content section.';
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
            ),
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
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/downloads_followup_email.php',
            'lang_custom/EN/downloads_followup_email.ini',
            'sources_custom/hooks/systems/cron/downloads_followup_email.php',
            'sources_custom/hooks/systems/notifications/downloads_followup_email.php',
            'themes/default/templates_custom/DOWNLOADS_FOLLOWUP_EMAIL.tpl',
            'themes/default/templates_custom/DOWNLOADS_FOLLOWUP_EMAIL_DOWNLOAD_LIST.tpl',
        );
    }
}

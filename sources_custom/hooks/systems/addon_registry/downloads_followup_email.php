<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    downloads_followup_email
 */

/**
 * Hook class.
 */
class Hook_addon_registry_downloads_followup_email
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
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11'; // addon_version_auto_update 24cdc3e550c4620ba5cf3df8534df54f
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
        return 'New Features';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Jason Verhagen';
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
        return 'No License/No copyright asserted';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Follow-up email functionality to the Composr Downloads module.

By default it will scan your [tt]download_logging[/tt] database table for downloads for approximately the past 24 hours and send a follow-up email to each member that has downloaded any files.

Members can enable, disable, or change the notification type and also do it on a per-category basis from their Profile page->Edit tab->Notifications tab and making the desired changes to the \'Downloads follow-up email\' notification in the Content section of the Notifications tab.

Admins can force the follow-up emails and/or private topics and prevent the members from changing the settings by making the necessary changes in the Admin Zone->Setup->Notification Lock-down->\'Downloads follow-up email\' in the Content section.';
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
                'downloads',
            ],
            'recommends' => [],
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
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'lang_custom/EN/downloads_followup_email.ini',
            'sources_custom/hooks/systems/addon_registry/downloads_followup_email.php',
            'sources_custom/hooks/systems/cron/downloads_followup_email.php',
            'sources_custom/hooks/systems/notifications/downloads_followup_email.php',
            'themes/default/templates_custom/DOWNLOADS_FOLLOWUP_EMAIL.tpl',
            'themes/default/templates_custom/DOWNLOADS_FOLLOWUP_EMAIL_DOWNLOAD_LIST.tpl',
        ];
    }
}

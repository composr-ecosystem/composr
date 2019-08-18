<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sugarcrm
 */

/**
 * Hook class.
 */
class Hook_addon_registry_sugarcrm
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Third Party Integration';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array('Asa Kusuma');
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Provides a number of Composr integrations with SugarCRM:
1) Sync new accounts into SugarCRM (Accounts, and Contacts and/or Leads)
2) Sync contact form messages into SugarCRM (Cases or Leads)
3) Insert URLs into Contacts to view Composr metadata of corresponding members

Form messages are taken from [tt]main_contact_simple[/tt] block, [tt]main_contact_catalogues[/tt] block, and [tt]form_to_email.php[/tt].

You can define mappings between:
 - Custom Profile Fields and SugarCRM fields
 - Contact form fields and SugarCRM fields

Mappings are always defined like:
[code]
Some Composr Field=Some SugarCRM field
Another Composr Field=Another SugarCRM field
(Some static text)=A SugarCRM field
[some_posted_field]=A SugarCRM field
[/code]
Parenthesised fields on the left-hand-side always define static text to place into a SugarCRM field.
Square-bracketed fields on the left-hand-side always define request field parameters (order: POST, GET, COOKIE) to place into a SugarCRM field.
Fields on either the left-hand-side or right-hand-side do not need to exist, and will simply be skipped if so. The same will happen if the Composr field is blank. This system makes it feasible to have multiple very different contact forms all synching with SugarCRM.
If multiple mappings are made to the same SugarCRM field then this will either:
1) append multiple fields together using a multi-line labelling system (on contact form sync, with some exceptions defined below)
2) use the last non-blank Composr field (on account sync, or for the following special fields [tt]priority[/tt], [tt]email1[/tt], [tt]lead_source[/tt], [tt]name[/tt], [tt]first_name[/tt], [tt]last_name[/tt])

CRM software supported:
 - Tested with SugarCRM 6.2, 6.5 and 7.9.
 - Also works with SuiteCRM, which is a SugarCRM fork. Tested with SuiteCRM 7.9.
 - Does not work with vTiger, another SugarCRM fork.
';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(
                'PHP curl extension',
            ),
            'recommends' => array(
                'securitylogging',
            ),
            'conflicts_with' => array(),
        );
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/admin/sync.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/sugarcrm.php',
            'sources_custom/hooks/systems/privacy/sugarcrm.php',
            'sources_custom/hooks/systems/health_checks/sugarcrm.php',
            'sources_custom/sugarcrm.php',
            'sources_custom/hooks/systems/contact_forms/sugarcrm.php',
            'sources_custom/hooks/systems/upon_query/sugarcrm.php',
            'lang_custom/EN/sugarcrm.ini',

            'sources_custom/curl.php',
            'sources_custom/sugar_crm_lib.php',

            'sources_custom/hooks/systems/config/sugarcrm_username.php',
            'sources_custom/hooks/systems/config/sugarcrm_password.php',
            'sources_custom/hooks/systems/config/sugarcrm_base_url.php',

            'sources_custom/hooks/systems/config/sugarcrm_default_company.php',
            'sources_custom/hooks/systems/config/sugarcrm_composr_company_field.php',

            'sources_custom/hooks/systems/config/sugarcrm_messaging_sync_type.php',
            'sources_custom/hooks/systems/config/sugarcrm_messaging_mappings.php',
            'sources_custom/hooks/systems/config/sugarcrm_exclusive_messaging.php',

            'sources_custom/hooks/systems/config/sugarcrm_member_sync_types.php',
            'sources_custom/hooks/systems/config/sugarcrm_member_mappings.php',
            'sources_custom/hooks/systems/config/sugarcrm_contact_metadata_field.php',
            'sources_custom/hooks/systems/config/sugarcrm_lead_metadata_field.php',

            'sources_custom/hooks/systems/config/sugarcrm_skip_string.php',

            'sources_custom/hooks/systems/tasks/sugarcrm_sync_message.php',
            'sources_custom/hooks/systems/tasks/sugarcrm_sync_member.php',
            'sources_custom/hooks/systems/tasks/sugarcrm_sync_contact_metadata.php',
            'sources_custom/hooks/systems/tasks/sugarcrm_sync_lead_metadata.php',

            'sources_custom/hooks/systems/config/days_to_keep__sugarcrm_log.php',
            'sources_custom/hooks/systems/logs/index.html',
            'sources_custom/hooks/systems/logs/sugarcrm.php',

            'data_custom/user_metadata_display.php',
            'sources_custom/user_metadata_display.php',
            'sources_custom/hooks/systems/cron/sugarcrm_sync_contact_metadata.php',
            'sources_custom/hooks/systems/cron/sugarcrm_sync_lead_metadata.php',
        );
    }
}

/*
Resources for vTiger integration, if this was ever added...

http://community.vtiger.com/help/vtigercrm/developers/third-party-app-integration.html
https://github.com/Kayoti/Vtiger-API-PHP
https://github.com/sumocoders/vtiger-api
https://github.com/vdespa/Vtiger-Web-Services-PHP-Client-Library
https://github.com/salaros/vtwsclib-php
*/

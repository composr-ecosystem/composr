<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    karma
 */

/**
 * Module page class.
 */
class Module_admin_karma
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Patrick Schmalstig';
        $info['organisation'] = 'PDStig, LLC';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'karma';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        // Custom fields
        $GLOBALS['FORUM_DRIVER']->install_delete_custom_field('good_karma');
        $GLOBALS['FORUM_DRIVER']->install_delete_custom_field('bad_karma');

        // Privileges
        delete_privilege(['view_others_karma', 'view_bad_karma', 'has_karmic_influence', 'has_additional_karmic_influence', 'moderate_karma']);

        // Database
        $GLOBALS['SITE_DB']->drop_table_if_exists('karma');
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        require_lang('karma');

        if ($upgrade_from === null) {
            // Custom fields
            $GLOBALS['FORUM_DRIVER']->install_create_custom_field('good_karma', 11, /*locked=*/1, /*viewable=*/0, /*settable=*/0, /*required=*/1, '', 'integer', 0, '0');
            $GLOBALS['FORUM_DRIVER']->install_create_custom_field('bad_karma', 11, /*locked=*/1, /*viewable=*/0, /*settable=*/0, /*required=*/1, '', 'integer', 0, '0');

            // Privileges
            add_privilege('KARMA', 'view_others_karma', true); // Default: Everyone can view each other's karma
            add_privilege('KARMA', 'view_bad_karma'); // Default: only staff can see bad karma
            add_privilege('KARMA', 'has_karmic_influence', true, false, true); // Default: Everyone except probation members can influence each other's karma
            add_privilege('KARMA', 'has_additional_karmic_influence'); // Default: Only staff are given additional influence
            add_privilege('KARMA', 'moderate_karma');

            // Database
            $GLOBALS['SITE_DB']->create_table('karma', [
                'id' => '*AUTO',
                'k_type' => 'ID_TEXT', // good|bad
                'k_member_from' => 'MEMBER',
                'k_member_to' => 'MEMBER',
                'k_amount' => 'INTEGER',
                'k_reason' => 'SHORT_TRANS__COMCODE',
                'k_content_type' => 'ID_TEXT',
                'k_content_id' => 'ID_TEXT',
                'k_date_and_time' => 'TIME',
                'k_reversed' => 'BINARY'
            ]);
            $GLOBALS['SITE_DB']->create_index('karma', 'karmamember', ['k_member_from', 'k_member_to']);
            $GLOBALS['SITE_DB']->create_index('karma', 'karmasystem', ['k_member_to']);
            $GLOBALS['SITE_DB']->create_index('karma', 'karmacontent', ['k_content_type', 'k_content_id']);
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('karma')) {
            return null;
        }

        return [
            // TODO
        ];
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('karma', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        require_lang('karma');

        $this->title = get_screen_title('KARMA');

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        $error_msg = new Tempcode();
        if (!addon_installed__messaged('karma', $error_msg)) {
            return $error_msg;
        }

        require_code('karma');
        require_code('form_templates');

        $type = get_param_string('type', 'browse');
        if ($type == 'browse') {
            return $this->browse();
        }
        return new Tempcode();
    }

    /**
     * Karma records interface.
     *
     * @return Tempcode The result of execution
     */
    public function browse() : object
    {
        // TODO

        return new Tempcode();
    }
}

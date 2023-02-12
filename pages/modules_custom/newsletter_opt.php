<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    sugarcrm
 */

/**
 * Module page class.
 */
class Module_newsletter_opt
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
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'mail_opt_sync_queue',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('mail_opt_sync_queue', [
                'id' => '*AUTO',

                'email_address' => 'SHORT_TEXT',
                'opt' => 'ID_TEXT', // opt-in or opt-out
                'add_time' => 'TIME',
                'processed_time' => '?TIME',
            ]);

            $GLOBALS['SITE_DB']->create_index('mail_opt_sync_queue', 'email_address', ['email_address']);
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
        return []; // This is a hidden module
    }

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        require_lang('sugarcrm');

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        $type = get_param_string('type', 'opt-out');
        $email_address = get_param_string('email');

        if (($type != 'opt-out') && ($type != 'opt-in')) {
            warn_exit(do_lang_tempcode('SUGARCRM_NEWSLETTER_OPT_INVALID_TYPE'));
        }

        $already_exists = $GLOBALS['SITE_DB']->query_select_value_if_there('mail_opt_sync_queue', 'id', ['email_address' => $email_address, 'opt' => $type]);

        if ($already_exists === null) {
            // Delete existing records
            $GLOBALS['SITE_DB']->query_delete('mail_opt_sync_queue', ['email_address' => $email_address]);

            // Add the new record
            $GLOBALS['SITE_DB']->query_insert('mail_opt_sync_queue', [
                'email_address' => $email_address,
                'opt' => $type,
                'add_time' => time(),
            ]);

            inform_exit(do_lang_tempcode('SUGARCRM_NEWSLETTER_OPT_SUCCESS', escape_html($type), escape_html(get_site_name()), escape_html($email_address)));
        } else {
            warn_exit(do_lang_tempcode('SUGARCRM_NEWSLETTER_OPT_ERROR', escape_html($type), escape_html(get_site_name()), escape_html($email_address)));
        }
        return new Tempcode();
    }
}

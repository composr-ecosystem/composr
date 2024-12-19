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
 * Module page class.
 */
class Module_telemetry
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
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'cms_homesite';
        return $info;
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
        return null; // No direct access
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
        if (!addon_installed__messaged('cms_homesite', $error_msg)) {
            return $error_msg;
        }

        $id = get_param_string('type');

        require_lang('cms_homesite');

        $this->title = get_screen_title('RELAYED_ERROR', true, [escape_html($id)]);

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        require_lang('cms_homesite');
        return $this->view();
    }

    /**
     * The UI to show the status of a telemetry entry.
     *
     * @return Tempcode The UI
     */
    public function view() : object
    {
        $id = get_param_string('type');
        $lang = get_param_string('lang', get_site_default_lang());

        // LEGACY: remove when v11 hits stable
        if (is_numeric($id)) {
            warn_exit('Telemetry now utilises random GUIDs which are more secure than ID numbers. Composr 11 beta6 will properly generate GUID-based telemetry links. Errors are still being received on our end despite this change.');
        }

        $_error = $GLOBALS['SITE_DB']->query_select('relayed_errors', ['*'], ['e_guid' => $id]);
        if (($_error === null) || !array_key_exists(0, $_error)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $error = $_error[0];

        $issue_tracker = hyperlink(get_base_url() . '/tracker/', 'Issue Tracker', true, true);

        require_code('templates_map_table');
        require_code('temporal');

        $fields = [
            'STATUS' => (($error['e_resolved'] == 1) ? do_lang_tempcode('RELAYED_ERROR_CLOSED', protect_from_escaping($issue_tracker)) : do_lang_tempcode('RELAYED_ERROR_OPEN')),
            'FIRST_REPORTED' => get_timezoned_date_time($error['e_first_date_and_time'], false),
            'LAST_REPORTED' => get_timezoned_date_time($error['e_last_date_and_time'], false),
            'TIMES_REPORTED' => integer_format($error['e_error_count']),
            'NOTES' => get_translated_tempcode('relayed_errors', $error, 'e_note', null, $lang),
        ];

        $text = do_lang_tempcode('DESCRIPTION_RELAYED_ERROR', protect_from_escaping($issue_tracker));

        return map_table_screen($this->title, $fields, true, $text, null, true);
    }
}

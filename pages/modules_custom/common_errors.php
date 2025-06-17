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
class Module_common_errors
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
        return [
            'browse' => ['COMMON_ERRORS', 'menu/adminzone/audit/errorlog'],
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
        if (!addon_installed__messaged('cms_homesite', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        require_lang('cms_homesite');

        switch ($type) {
            case 'browse':
                $this->title = get_screen_title('COMMON_ERRORS', true);
                break;
        }

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

        $type = get_param_string('type', 'browse');

        switch ($type) {
            case 'browse':
                return $this->view();
        }

        return new Tempcode();
    }

    /**
     * The UI to show common error messages.
     *
     * @return Tempcode The UI
     */
    public function view() : object
    {
        $errors = [];

        require_code('files_spreadsheets_read');

        // Read in the errorservice file
        $sheet_reader = spreadsheet_open_read(get_custom_file_base() . '/uploads/website_specific/cms_homesite/errorservice.csv');
        while (($row = $sheet_reader->read_row()) !== false) {
            $message = $row['Message'];
            $summary = $row['Summary'];
            $how = $row['How did this happen?'];
            $solution = $row['How do I fix it?'];

            $errors[] = [
                'ERROR_MESSAGE' => $message,
                'ERROR_SUMMARY' => $summary,
                'ERROR_CAUSE' => $how,
                'ERROR_RESOLUTION' => $solution,
            ];
        }
        $sheet_reader->close();

        // Return our page
        return do_template('COMMON_ERRORS_SCREEN', [
            '_GUID' => 'e3eaef71347e50ec95a8ad33d6d00db8',
            'TITLE' => $this->title,
            'ERRORS' => $errors,
        ]);
    }
}

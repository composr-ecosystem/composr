<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_release_build
 */

/**
 * Module page class.
 */
class Module_admin_modularisation
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
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'cms_release_build';
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
        if (!addon_installed('cms_release_build')) {
            return null;
        }

        require_lang('cms_release_build');

        return [
            'browse' => ['RELEASE_TOOLS_MODULARISATION', 'admin/tool'],
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
        if (!addon_installed__messaged('cms_release_build', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        require_lang('cms_release_build');

        switch ($type) {
            case 'browse':
                $this->title = get_screen_title('MODULARISATION_TITLE_BROWSE');
                break;
            case 'fix':
                $this->title = get_screen_title('MODULARISATION_TITLE_FIX');
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
        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            return $this->browse();
        }
        if ($type == 'fix') {
            return $this->fix();
        }

        return new Tempcode();
    }

    /**
     * Scan for modularisation issues and render a UI for resolving them.
     * This code should also be updated in the modularisation test if edited.
     *
     * @return Tempcode The UI
     */
    public function browse() : object
    {
        require_code('modularisation');

        $problems = scan_modularisation(false, true);

        if (count($problems) == 0) {
            inform_exit('NO_ENTRIES');
        }

        sort_maps_by($problems, 0);

        require_code('form_templates');

        $current_section = '';
        $fields = new Tempcode();
        $list = [];
        $list_action = null;
        $count = 0;
        $max = 1000; // Save on resource use and POST size
        foreach ($problems as $i => $problem) {
            list($issue, $file, $addon, $description) = $problem;

            // New section
            if ($current_section != $issue) {
                // Finalise list from previous section
                $this->finalise_section($list, $current_section, $list_action, $fields);
                $list = [];

                // Set current section info
                $current_section = $issue;
                $list_action = do_lang_tempcode($issue . '_ACTION');
            }

            $list[] = [$file . '::' . $addon . '::' . $issue, ($description != '') ? do_lang_tempcode('MODULARISATION_ITEM_WITH_DESCRIPTION', escape_html($file), escape_html($description)) : do_lang_tempcode('MODULARISATION_ITEM', escape_html($file))];

            $count++;
            if ($count >= $max) {
                attach_message(do_lang_tempcode('MODULARISATION_TOO_MANY_ENTRIES', strval($max)), 'warn');
                break;
            }
        }

        // Finalise last tick section
        $this->finalise_section($list, $current_section, $list_action, $fields);

        // Action items
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '26e5b6da4589c54e94deb8940c26d657', 'TITLE' => do_lang_tempcode('ACTION')]));
        $addons = new Tempcode();
        $hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($hooks as $hook => $ob) {
            $addons->attach(form_input_list_entry($hook));
        }
        $fields->attach(form_input_list(do_lang_tempcode('MODULARISATION_RESPONSIBLE_ADDON'), do_lang_tempcode('DESCRIPTION_MODULARISATION_RESPONSIBLE_ADDON'), 'responsible_addon', $addons));

        $post_url = build_url(['page' => '_SELF', 'type' => 'fix'], '_SELF');

        return do_template('FORM_SCREEN', [
            '_GUID' => 'a5f5f42573b836c30dcdf2c0747f513c',
            'GET' => false,
            'SKIP_WEBSTANDARDS' => true,
            'HIDDEN' => new Tempcode(),
            'TITLE' => $this->title,
            'TEXT' => do_lang_tempcode('MODULARISATION_TEXT_BROWSE'),
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'FIELDS' => $fields,
            'URL' => $post_url,
            'MODSECURITY_WORKAROUND' => true,
        ]);
    }

    /**
     * Finalise a modularisation action section.
     *
     * @param  array $list Tuple list entries [value, text]
     * @param  ID_TEXT $current_section The name of the current section which should be an issue language string
     * @param  Tempcode $list_action The text describing the action that will be taken for selected items
     * @param  Tempcode $fields The fields for the UI, passed by reference
     */
    protected function finalise_section($list, $current_section, $list_action, &$fields)
    {
        if (count($list) <= 0) { // Nothing to do
            return;
        }

        // Issues that can safely be resolved right away, so the resolution will be set to all except selected
        $safe_to_action_now = [
            'MODULARISATION_DOUBLE_REFERENCED_ADDON',
            'MODULARISATION_ICON_NOT_IN_CORE',
            'MODULARISATION_FILE_MISSING',
        ];

        // Issues that can be resolved through the UI. There should exist an _fix_modularisation__ISSUE function in the modularisation2 code.
        $actionable = [
            'MODULARISATION_DOUBLE_REFERENCED_ADDON',
            'MODULARISATION_DOUBLE_REFERENCED',
            'MODULARISATION_ICON_NOT_IN_CORE',
            'MODULARISATION_CORE_ICON_NOT_IN_ADDON',
            'MODULARISATION_WRONG_PACKAGE',
            'MODULARISATION_WRONG_ADDON_INFO',
            'MODULARISATION_UNKNOWN_ADDON',
            'MODULARISATION_FILE_MISSING',
            'MODULARISATION_ALIEN_FILE',
        ];

        // Add divider
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '872b56cb285e63f2906f5f2e646596a9', 'TITLE' => do_lang_tempcode($current_section), 'HELP' => do_lang_tempcode($current_section . '_TEXT')]));

        // Add fields
        $_list = new Tempcode();
        if (in_array($current_section, $actionable)) {
            foreach ($list as $item) {
                list($value, $text) = $item;
                $_list->attach(form_input_list_entry($value, in_array($current_section, $safe_to_action_now), $text));
            }
            $fields->attach(form_input_multi_list($list_action, do_lang_tempcode($current_section . '_TEXT'), 'modularisation_action_' . $current_section, $_list, null, 10));
        } else { // Not actionable; just display issues as text
            $_list->attach('<ul>');
            foreach ($list as $item) {
                list($value, $text) = $item;
                $_list->attach('<li>' . $text . '</li>');
            }
            $_list->attach('</ul>');
            $fields->attach(form_input_text(do_lang_tempcode($current_section . '_ACTION'), do_lang_tempcode($current_section . '_TEXT'), 'modularisation_' . $current_section, $_list, false, true));
        }
    }

    /**
     * The actualiser to fix modularisation issues.
     *
     * @return Tempcode Results of execution
     */
    public function fix() : object
    {
        require_code('modularisation2');

        $responsible_addon = post_param_string('responsible_addon');

        // Process each actionable issue from the multiselect fields, and output the result of execution.
        $out = new Tempcode();
        $out->attach('<ul>');
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'modularisation_action_') !== 0) {
                continue;
            }

            foreach ($value as $action) {
                list($file, $addon, $issue) = explode('::', $action);

                $results = fix_modularisation($issue, $file, $addon, $responsible_addon);
                if ($results === null) {
                    continue;
                }

                $out->attach('<li>');
                $out->attach($results);
                $out->attach('</li>');
            }
        }
        $out->attach('</ul>');

        // Finalise
        $out->attach(fix_modularisation_finished());

        // Output next steps
        $out->attach(paragraph(do_lang_tempcode('MODULARISATION_RESCAN')));

        return $out;
    }
}

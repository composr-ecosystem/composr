<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_release_build
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
        $info['addon'] = 'composr_release_build';
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
        if (!addon_installed('composr_release_build')) {
            return null;
        }

        require_lang('composr_release_build');

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
        if (!addon_installed__messaged('composr_release_build', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        require_lang('composr_release_build');

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
     * This code should also be updated in the aaa_modularisation test if edited.
     *
     * @return Tempcode The UI
     */
    public function browse() : object
    {
        // Issues that can safely be resolved right away
        $ticked_by_default = [
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

        require_code('modularisation');

        $problems = scan_modularisation();

        if (count($problems) == 0) {
            inform_exit('NO_ENTRIES');
        }

        sort_maps_by($problems, 0);

        require_code('form_templates');

        $current_section = '';
        $fields = new Tempcode();
        foreach ($problems as $i => $problem) {
            list($issue, $file, $addon, $params) = $problem;

            // New section; add a divider
            if ($current_section != $issue) {
                $current_section = $issue;
                $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['TITLE' => do_lang_tempcode($issue), 'HELP' => do_lang_tempcode($issue . '_TEXT')]));
            }

            $fields->attach(form_input_tick(do_lang_tempcode('MODULARISATION_FILE_ITEM__' . strval(count($params)), $file, null, $params), do_lang_tempcode('DESCRIPTION_MODULARISATION_FILE_ITEM'), strtolower($issue) . '__' . strval($i), in_array($issue, $ticked_by_default), null, $file . '::' . $addon . '::' . $issue, false, !in_array($issue, $actionable)));
        }

        // Action items
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['TITLE' => do_lang_tempcode('ACTION')]));
        $addons = new Tempcode();
        $hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
        foreach ($hooks as $hook => $ob) {
            $addons->attach(form_input_list_entry($hook));
        }
        $fields->attach(form_input_list(do_lang_tempcode('MODULARISATION_RESPONSIBLE_ADDON'), do_lang_tempcode('DESCRIPTION_MODULARISATION_RESPONSIBLE_ADDON'), 'responsible_addon', $addons));

        $post_url = build_url(['page' => '_SELF', 'type' => 'fix'], '_SELF');

        return do_template('FORM_SCREEN', [
            'GET' => false,
            'SKIP_WEBSTANDARDS' => true,
            'HIDDEN' => new Tempcode(),
            'TITLE' => $this->title,
            'TEXT' => do_lang_tempcode('MODULARISATION_TEXT_BROWSE'),
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'FIELDS' => $fields,
            'URL' => $post_url,
        ]);
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

        // Process each actionable tick box
        $out = new Tempcode();
        $out->attach('<ul>');

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'tick_on_form__') !== 0) {
                $value = post_param_string(str_replace('tick_on_form__', '', $key), null);
                if ($value === null) {
                    continue;
                }

                list($file, $addon, $issue) = explode('::', $value);

                $results = fix_modularisation($issue, $file, $addon, $responsible_addon);
                if ($results === null) {
                    continue;
                }

                $out->attach('<li>');
                $out->attach($results);
                $out->attach('</li>');
            }
        }

        // Finalise
        fix_modularisation_finished();

        $out->attach('</ul>');
        return $out;
    }
}

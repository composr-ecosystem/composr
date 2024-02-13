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
class Module_admin_make_release
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
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
            'step1' => ['RELEASE_TOOLS_MAKE_RELEASE', 'admin/tool'],
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

        $type = get_param_string('type', 'step1');

        require_lang('composr_release_build');

        switch ($type) {
            case 'step1':
                $this->title = get_screen_title('MAKE_RELEASE_TITLE', true, [escape_html('1')]);
                break;
            case 'step2':
                $this->title = get_screen_title('MAKE_RELEASE_TITLE', true, [escape_html('2')]);
                break;
            case 'step3':
                $this->title = get_screen_title('MAKE_RELEASE_TITLE', true, [escape_html('3')]);
                break;
            case 'step4':
                $this->title = get_screen_title('MAKE_RELEASE_TITLE', true, [escape_html('4')]);
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
        $type = get_param_string('type', 'step1');

        if ($type == 'step1') {
            return $this->step1();
        }
        if ($type == 'step2') {
            return $this->step2();
        }
        if ($type == 'step3') {
            return $this->step3();
        }
        if ($type == 'step4') {
            return $this->step4();
        }

        return new Tempcode();
    }

    /**
     * Get the previous version of the software from git tags.
     *
     * @return ?string The previous software version (null: could not determine)
     */
    protected function get_previous_version() : ?string
    {
        require_code('version2');

        $previous_version = null;
        $previous_tag = shell_exec('git describe --tags');
        $matches = [];
        if (preg_match('#^(.*)-\w+-\w+$#', $previous_tag, $matches) != 0) {
            $previous_version = $matches[1];
        }

        return $previous_version;
    }

    /**
     * Get the current version of the software.
     * This will return what was passed for the new version on step 1, or the on-disk version if a new version has not been passed in.
     *
     * @return string The current version of the software
     */
    protected function get_new_version() : string
    {
        require_code('version2');

        $new_version = post_param_string('version', get_version_dotted());

        return get_version_dotted__from_anything($new_version);
    }

    /**
     * The UI for step 1: gathering version and URL information.
     *
     * @return Tempcode The UI
     */
    public function step1() : object
    {
        require_code('form_templates');
        require_code('version');

        $text = do_lang_tempcode('MAKE_RELEASE_STEP1_TEXT', escape_html(get_base_url() . '/_tests'));

        $fields = new Tempcode();

        // Version
        $previous_version = $this->get_previous_version();
        $current_version = $this->get_new_version();
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['TITLE' => do_lang_tempcode('MAKE_RELEASE_STEP1_VERSION'), 'HELP' => do_lang_tempcode('DESCRIPTON_MAKE_RELEASE_STEP1_VERSION')]));
        $fields->attach(form_input_line(do_lang_tempcode('MAKE_RELEASE_STEP1_OLD_VERSION'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP1_OLD_VERSION'), 'previous_version', ($previous_version !== null) ? $previous_version : '', false));
        $fields->attach(form_input_line(do_lang_tempcode('MAKE_RELEASE_STEP1_NEW_VERSION'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP1_NEW_VERSION'), 'version', $current_version, true));

        // URLs
        $tracker_url = get_brand_base_url() . '/tracker';
        $web_service_url = get_brand_base_url() . '/data_custom/composr_homesite_web_service.php';
        $make_release_url = get_brand_base_url() . '/adminzone/index.php?page=-make-release';
        $profile_url = get_brand_base_url() . '/members/view';
        $git_url = COMPOSR_REPOS_URL;
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['TITLE' => do_lang_tempcode('MAKE_RELEASE_STEP1_URLS'), 'HELP' => do_lang_tempcode('DESCRIPTON_MAKE_RELEASE_STEP1_URLS')]));
        $fields->attach(form_input_url(do_lang_tempcode('MAKE_RELEASE_STEP1_TRACKER_URL'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP1_TRACKER_URL'), 'tracker_url', $tracker_url, true));
        $fields->attach(form_input_integer(do_lang_tempcode('MAKE_RELEASE_STEP1_TRACKER_PROJECT'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP1_TRACKER_PROJECT'), 'project_id', 1, true));
        $fields->attach(form_input_url(do_lang_tempcode('MAKE_RELEASE_STEP1_WEBSERVICE_URL'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP1_WEBSERVICE_URL'), 'web_service_url', $web_service_url, true));
        $fields->attach(form_input_url(do_lang_tempcode('MAKE_RELEASE_STEP1_MAKE_RELEASE_URL'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP1_MAKE_RELEASE_URL'), 'make_release_url', $make_release_url, true));
        $fields->attach(form_input_url(do_lang_tempcode('MAKE_RELEASE_STEP1_GIT_URL'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP1_GIT_URL'), 'git_url', $git_url, true));
        $fields->attach(form_input_url(do_lang_tempcode('MAKE_RELEASE_STEP1_PROFILE_URL'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP1_PROFILE_URL'), 'profile_url', $profile_url, true));

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('csrf_token_preserve', '1'));

        $post_url = build_url(['page' => '_SELF', 'type' => 'step2', 'skip' => strval(get_param_integer('skip', 0))], '_SELF');

        return do_template('FORM_SCREEN', [
            'GET' => false,
            'SKIP_WEBSTANDARDS' => true,
            'HIDDEN' => $hidden,
            'TITLE' => $this->title,
            'TEXT' => $text,
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'FIELDS' => $fields,
            'URL' => $post_url,
        ]);
    }

    /**
     * Actualise editing version.php.
     */
    protected function edit_version()
    {
        $new_version = $this->get_new_version();
        $previous_version = $this->get_previous_version();

        // Update version.php
        if (($new_version !== $previous_version)) {
            $version_file = cms_file_get_contents_safe(get_file_base() . '/sources/version.php');
            if (!$version_file) {
                fatal_exit('Failed to get sources/version.php file contents for editing.');
            }

            list(, , , , $general_number, $long_dotted_number_with_qualifier) = get_version_components__from_dotted($new_version);

            // Determine if this is a major release, and update version times accordingly
            if (preg_match('#^\d+\.0\.0(\.beta1|\.RC1|)$#', $long_dotted_number_with_qualifier) != 0) { // e.g. 3.0.0 or 3.0.0.beta1 or 3.0.0.RC1
                // cms_version_time() and cms_version_time_major()
                $version_file = preg_replace('/\d{10}/', strval(time()), $version_file, 2);
            } else {
                // Just cms_version_time()
                $version_file = preg_replace('/\d{10}/', strval(time()), $version_file, 1);
            }

            // Update cms_version_number()
            $_replacement = $general_number;
            $pattern = '/function cms_version_number\(\) : float\s*{\s*return\s*(.*?)\;\s*}/s';
            $replacement = "function cms_version_number() : float\n{\n    return " . float_to_raw_string($_replacement, 1) . ";\n}";
            $version_file = preg_replace($pattern, $replacement, $version_file);

            // Update cms_version_minor(); first we must remove the major version part.
            $parts = explode('.', $new_version);
            array_shift($parts);
            $_replacement = implode('.', $parts);
            if ($_replacement == '') { // No minor version defined? We should define 0 as minor version.
                $_replacement = '0';
            }
            $pattern = '/function cms_version_minor\(\) : string\s*{\s*return\s*\'(.*?)\'\;\s*}/s';
            $replacement = "function cms_version_minor() : string\n{\n    return '" . $_replacement . "';\n}";
            $version_file = preg_replace($pattern, $replacement, $version_file);

            // Update branch status flag
            if (strpos($new_version, 'alpha') !== false) {
                $_replacement = 'VERSION_ALPHA';
            } elseif (strpos($new_version, 'beta') !== false) {
                $_replacement = 'VERSION_BETA';
            } elseif (strpos($new_version, 'RC') !== false) {
                $_replacement = 'VERSION_SUPPORTED';
            } else {
                $_replacement = 'VERSION_MAINLINE';
            }
            $pattern = '/function cms_version_branch_status\(\) : string\s*{\s*return\s*(.*?)\;\s*}/s';
            $replacement = "function cms_version_branch_status() : string\n{\n    return " . $_replacement . ";\n}";
            $version_file = preg_replace($pattern, $replacement, $version_file);

            // Save the updated file
            require_code('files');
            cms_file_put_contents_safe(get_file_base() . '/sources/version.php', $version_file, FILE_WRITE_SYNC_FILE | FILE_WRITE_FIX_PERMISSIONS);
        }
    }

    /**
     * Generate a changelog of changes made since the previous version.
     *
     * @return string The changelog as Comcode
     */
    protected function generate_changelog() : string
    {
        $new_version = $this->get_new_version();
        $previous_version = $this->get_previous_version();

        $git_authors = [];
        $tracker_reporters = [];
        $tracker_handlers = [];
        $changes = new Tempcode();
        if ($previous_version !== null) {
            $_changes = shell_exec('git log --pretty=format:"%H :: %cn :: %s" HEAD...refs/tags/' . $previous_version);
            $discovered_tracker_issues = []; // List of issues referenced on Git to pull from Mantis
            $__changes = [];
            $dig_deep = false;
            foreach (explode("\n", $_changes) as $change) {
                $parts = explode(' :: ', $change, 3);
                if (count($parts) == 3) {
                    $change_label = $parts[2];
                    $git_id = $parts[0];

                    $matches = [];
                    if (preg_match('#MANTIS-(\d+)#', $change_label, $matches) != 0) {
                        $tracker_id = $matches[1];
                        if ($tracker_id != '0') {
                            $discovered_tracker_issues[$tracker_id] = true;
                        } else {
                            $dig_deep = true; // Somehow an ID was zero, so we need to search tracker for what this may have been
                        }
                    } else {
                        // In Git only
                        $__changes[$git_id] = $change_label;
                        if (!in_array($parts[1], $git_authors)) {
                            $git_authors[] = $parts[1];
                        }

                        $regexp = '/^(Fixed MANTIS-\d+|Implementing MANTIS-\d+|Implemented MANTIS-\d+|Security fix for MANTIS-\d+|New build|Merge branch .*)/';
                        if (preg_match($regexp, $change_label) == 0) {
                            $dig_deep = true; // We want to search tracker for what this may have been
                        }
                    }
                }
            }

            $api_url = post_param_string('web_service_url') . '?call=get_tracker_issues';
            $_discovered_tracker_issues = implode(',', array_keys($discovered_tracker_issues));
            $_result = http_get_contents($api_url, ['post_params' => ['parameters' => [$_discovered_tracker_issues, $new_version, $dig_deep ? $previous_version : null]]]);
            $tracker_issues = json_decode($_result, true);

            $new_version_parts = explode('.', $new_version);
            $last = count($new_version_parts) - 1;
            $new_version_parts[$last] = strval(intval($new_version_parts[$last]) - 1);
            $new_version_previous = implode('.', $new_version_parts);

            $tracker_url = post_param_string('tracker_url') . '/search.php?project_id=' . strval(post_param_integer('project_id'));
            if (($new_version_parts[$last] >= 0) && (substr_count($new_version, '.') == 2)) {
                $tracker_url .= '&product_version=' . urlencode($new_version_previous);
            }

            // Start populating changes
            if (count($tracker_issues) > 0) {
                $changes->attach(do_lang_tempcode('CHANGELOG_HEADER_TRACKER', escape_html($tracker_url), escape_html($previous_version)));
                ksort($tracker_issues); // Sort by tracker ID (usually results in oldest to newest sorting)
                foreach ($tracker_issues as $key => $data) {
                    list($summary, $reporter, $handler) = $data;
                    if (strpos($summary, '[[All Projects] General]') === false) { // Only ones in the main Composr project
                        $url = post_param_string('tracker_url') . '/view.php?id=' . substr($key, 1);
                        $changes->attach("\n");
                        $changes->attach(do_lang_tempcode('CHANGELOG_ITEM', comcode_escape(escape_html($summary)), escape_html($url)));
                        if (($reporter) && !in_array($reporter, $tracker_reporters)) {
                            $tracker_reporters[] = $reporter;
                        }
                        if (($handler) && !in_array($handler, $tracker_handlers)) {
                            $tracker_handlers[] = $handler;
                        }
                    }
                }
                $changes->attach("\n\n");
            }

            // Show Git-only commits
            if (count($__changes) > 0) {
                $git_url = post_param_string('git_url');
                $changes->attach(do_lang_tempcode('CHANGELOG_HEADER_GIT', escape_html($git_url), escape_html($previous_version)));
                $__changes = array_reverse($__changes, true); // Sort by commit time, oldest to newest
                foreach ($__changes as $git_id => $change_label) {
                    // Some required substitutions
                    $change_label = str_replace('<script>', 'script', $change_label);

                    $url = $git_url . '/commit/' . $git_id;
                    $changes->attach("\n");
                    $changes->attach(do_lang_tempcode('CHANGELOG_ITEM', comcode_escape(escape_html($change_label)), escape_html($url)));
                }
                $changes->attach("\n\n");
            }

            // Show contributors
            if (count($tracker_handlers) > 0) {
                $changes->attach(do_lang_tempcode('CHANGELOG_HEADER_TRACKER_HANDLERS'));
                $base_member_url = post_param_string('profile_url');
                foreach ($tracker_handlers as $handler) {
                    $member_label = $handler;
                    $member_url = $base_member_url . '/' . $handler . '.htm';
                    $changes->attach("\n");
                    $changes->attach(do_lang_tempcode('CHANGELOG_ITEM', comcode_escape(escape_html($member_url)), escape_html($member_label)));
                }
                $changes->attach("\n\n");
            }
            if (count($tracker_reporters) > 0) {
                $changes->attach(do_lang_tempcode('CHANGELOG_HEADER_TRACKER_REPORTERS'));
                $base_member_url = post_param_string('profile_url');
                foreach ($tracker_reporters as $reporter) {
                    $member_label = $reporter;
                    $member_url = $base_member_url . '/' . $reporter . '.htm';
                    $changes->attach("\n");
                    $changes->attach(do_lang_tempcode('CHANGELOG_ITEM', comcode_escape(escape_html($member_url)), escape_html($member_label)));
                }
                $changes->attach("\n\n");
            }
            if (count($git_authors) > 0) {
                $changes->attach(do_lang_tempcode('CHANGELOG_HEADER_GIT_CONTRIBUTORS'));
                foreach ($git_authors as $author) {
                    $changes->attach("\n");
                    $changes->attach(do_lang_tempcode('CHANGELOG_ITEM_NOURL', escape_html($author)));
                }
            }
        } else {
            $changes = do_lang_tempcode('CHANGELOG_DEFAULT');
        }

        return $changes->evaluate();
    }

    /**
     * The UI for step 2: changelog and release information.
     * This also actualises step 1 by updating version.php.
     *
     * @return Tempcode The UI
     */
    public function step2() : object
    {
        $this->edit_version();

        require_code('form_templates');

        $text = do_lang_tempcode('MAKE_RELEASE_STEP2_TEXT');

        $new_version = $this->get_new_version();

        if (strpos($new_version, 'alpha') !== false) {
            $release_description = do_lang_tempcode('DESCRIPTION_RELEASE_ALPHA');
            $default_necessity = 'unrecommended';
        } elseif (strpos($new_version, 'beta') !== false) {
            $release_description = do_lang_tempcode('DESCRIPTION_RELEASE_BETA');
            $default_necessity = 'unrecommended';
        } elseif (strpos($new_version, 'RC') !== false) {
            $release_description = do_lang_tempcode('DESCRIPTION_RELEASE_RC');
            $default_necessity = 'unrecommended';
        } elseif (substr_count($new_version, '.') == 2) {
            $release_description = do_lang_tempcode('DESCRIPTION_RELEASE_PATCH');
            $default_necessity = 'not-needed';
        } elseif (substr_count($new_version, '.') == 1) {
            $release_description = do_lang_tempcode('DESCRIPTION_RELEASE_MINOR');
            $default_necessity = 'advised';
        } else {
            $release_description = do_lang_tempcode('DESCRIPTION_RELEASE_GOLD');
            $default_necessity = 'advised';
        }

        $hidden = build_keep_post_fields();
        $hidden->attach(form_input_hidden('csrf_token_preserve', '1'));

        $fields = new Tempcode();

        // Release description and Changelog
        $changelog = $this->generate_changelog();
        $fields->attach(form_input_line(do_lang_tempcode('MAKE_RELEASE_STEP2_RELEASE_DESCRIPTION'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP2_RELEASE_DESCRIPTION'), 'descrip', $release_description, true));
        $fields->attach(form_input_text_comcode(do_lang_tempcode('MAKE_RELEASE_STEP2_CHANGELOG'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP2_CHANGELOG'), 'changes', $changelog, true, null, false, '', null, true, 25));

        // Upgrade necessity, criteria, and justification
        $radios = new Tempcode();
        $radios->attach(form_input_radio_entry('needed', 'unrecommended', $default_necessity == 'unrecommended', do_lang_tempcode('MAKE_RELEASE_STEP2_UPGRADE_NECESSITY_UNRECOMMENDED')));
        $radios->attach(form_input_radio_entry('needed', 'not-needed', $default_necessity == 'not-needed', do_lang_tempcode('MAKE_RELEASE_STEP2_UPGRADE_NECESSITY_NOT_NEEDED')));
        $radios->attach(form_input_radio_entry('needed', 'suggested', $default_necessity == 'suggested', do_lang_tempcode('MAKE_RELEASE_STEP2_UPGRADE_NECESSITY_SUGGESTED')));
        $radios->attach(form_input_radio_entry('needed', 'advised', $default_necessity == 'advised', do_lang_tempcode('MAKE_RELEASE_STEP2_UPGRADE_NECESSITY_ADVISED')));
        $fields->attach(form_input_radio(do_lang_tempcode('MAKE_RELEASE_STEP2_UPGRADE_NECESSITY'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP2_UPGRADE_NECESSITY'), 'needed', $radios, true));
        $fields->attach(form_input_line(do_lang_tempcode('MAKE_RELEASE_STEP2_CRITERIA'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP2_CRITERIA'), 'criteria', ($default_necessity == 'unrecommended') ? do_lang_tempcode('CRITERIA_LIVE_SITES') : '', false));
        $fields->attach(form_input_line(do_lang_tempcode('MAKE_RELEASE_STEP2_JUSTIFICATION'), do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP2_JUSTIFICATION'), 'justification', '', false));

        // Build options
        $fields->attach(form_input_various_ticks([
            [
                do_lang_tempcode('BUILD_OPTIONS_SKIP'),
                'skip',
                (get_param_integer('skip', 0) == 1) ? '1' : '0',
                '',
                false
            ],
            [
                do_lang_tempcode('BUILD_OPTIONS_BLEEDING_EDGE'),
                'bleeding_edge',
                ((strpos($new_version, 'alpha') !== false) || (strpos($new_version, 'beta') !== false)) ? '1' : '0',
                '',
                false
            ],
            [
                do_lang_tempcode('BUILD_OPTIONS_OLDER_TREE'),
                'old_tree',
                '0',
                '',
                false
            ],
            [
                do_lang_tempcode('BUILD_OPTIONS_MAKE_OMNI_UPGRADER'),
                'make_omni_upgrader',
                '0',
                '',
                false
            ],
            [
                do_lang_tempcode('BUILD_OPTIONS_REBUILD_SQL'),
                'rebuild_sql',
                '0',
                '',
                false
            ],
        ], do_lang_tempcode('DESCRIPTION_MAKE_RELEASE_STEP2_BUILD_OPTIONS'), null, do_lang_tempcode('MAKE_RELEASE_STEP2_BUILD_OPTIONS'), true));

        $post_url = build_url(['page' => '_SELF', 'type' => 'step3'], '_SELF');

        return do_template('FORM_SCREEN', [
            'GET' => false,
            'SKIP_WEBSTANDARDS' => true,
            'HIDDEN' => $hidden,
            'TITLE' => $this->title,
            'TEXT' => $text,
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'FIELDS' => $fields,
            'URL' => $post_url,
        ]);
    }

    /**
     * Generate a UI warning about a substantial release before actually proceeding with step 3.
     *
     * @return Tempcode a confirm screen UI
     */
    public function step3_pre() : object
    {
        $plug_guid_url = static_evaluate_tempcode(build_url(['page' => 'plug_guid'], get_page_zone('plug_guid')));
        $legacy_issue_url = post_param_string('tracker_url') . '/view.php?id=1305'; // FUDGE
        $advanced_testing_issue_url = post_param_string('tracker_url') . '/view.php?id=3383'; // FUDGE
        $preview = do_lang_tempcode('MAKE_RELEASE_SUBSTANTIAL', escape_html($plug_guid_url), escape_html($legacy_issue_url), [escape_html($advanced_testing_issue_url)]);

        return do_template('CONFIRM_SCREEN', [
            'TITLE' => $this->title,
            'PREVIEW' => $preview,
            'URL' => get_self_url(false, false, ['intermediary_tasks' => 1]),
            'FIELDS' => build_keep_post_fields(),
        ]);
    }

    /**
     * Build the release.
     *
     * @return Tempcode The results of the build process
     */
    public function step3() : object
    {
        require_code('form_templates');

        $new_version = $this->get_new_version();

        $is_bleeding_edge = (post_param_integer('bleeding_edge', 0) == 1);
        $is_substantial = is_substantial_release($new_version);

        // Show additional required tasks for major releases
        if ((get_param_integer('intermediary_tasks', 0) == 0) && ($is_substantial) && (!$is_bleeding_edge)) {
            return $this->step3_pre();
        }

        // Build the release
        if (post_param_integer('skip', 0) == 0) {
            require_code('make_release');
            $text = make_string_tempcode(make_installers(get_param_integer('keep_skip_file_grab', 0) == 1));
        } else { // Just skip to step 4 if we are not building anything
            $this->title = get_screen_title('MAKE_RELEASE_TITLE', true, [escape_html('4')]);
            return $this->step4();
        }

        $hidden = build_keep_post_fields();
        $hidden->attach(form_input_hidden('csrf_token_preserve', '1'));

        $post_url = build_url(['page' => '_SELF', 'type' => 'step4'], '_SELF');

        return do_template('FORM_SCREEN', [
            'GET' => false,
            'SKIP_WEBSTANDARDS' => true,
            'HIDDEN' => $hidden,
            'TITLE' => $this->title,
            'TEXT' => $text,
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'FIELDS' => new Tempcode(),
            'URL' => $post_url,
        ]);
    }

    /**
     * Step 4: render a UI explaining what to do next with the built installers.
     *
     * @return Tempcode the UI
     */
    public function step4() : object
    {
        require_code('form_templates');
        require_code('version');

        $criteria = post_param_string('criteria');
        if (substr($criteria, -1) == '.') {
            $criteria = substr($criteria, 0, strlen($criteria) - 1);
        }
        if ($criteria != '') {
            $criteria = ' for ' . $criteria;
        }

        $justification = post_param_string('justification');
        if (substr($justification, -1) == '.') {
            $justification = substr($justification, 0, strlen($justification) - 1);
        }
        if ($justification != '') {
            $justification = ' due to ' . $justification;
        }

        $needed = post_param_string('needed');
        $descrip = post_param_string('descrip');
        $descrip = rtrim($descrip, '.');

        require_code('version2');

        $new_version = get_version_dotted();
        $version_branch = get_version_branch();
        $version_number = float_to_raw_string(cms_version_number(), 2, true);
        $is_bleeding_edge = (post_param_integer('bleeding_edge', 0) == 1);
        $is_old_tree = (post_param_integer('old_tree', 0) == 1);
        $is_substantial = is_substantial_release($new_version);

        $push_url = post_param_string('make_release_url') . '&version=' . urlencode($new_version) . '&is_bleeding_edge=' . ($is_bleeding_edge ? '1' : '0') . '&is_old_tree=' . ($is_old_tree ? '1' : '0') . '&descrip=' . urlencode($descrip) . '&needed=' . urlencode($needed) . '&criteria=' . urlencode($criteria) . '&justification=' . urlencode($justification);

        if (strpos(PHP_OS, 'Darwin') !== false) {
            $command_to_try = 'open';
        } elseif (strpos(PHP_OS, 'WIN') !== false) {
            $command_to_try = 'start';
        } else {
            $command_to_try = 'nautilus';
        }
        $command_to_try .= ' ' . get_custom_file_base() . '/exports/builds/' . $new_version . '/';

        $text = do_lang_tempcode('MAKE_RELEASE_STEP4_TEXT');

        return do_template('MAKE_RELEASE_STEP4_SCREEN', [
            'TITLE' => $this->title,
            'TEXT' => $text,
            'NEW_VERSION_DOTTED' => $new_version,
            'NEW_VERSION_FLOAT' => $version_number,
            'NEW_VERSION_BRANCH' => $version_branch,
            'NEW_VERSION_MAJOR' => strval(intval(cms_version_number())),
            'IS_SUBSTANTIAL' => $is_substantial,
            'IS_BLEEDING_EDGE' => $is_bleeding_edge,
            'IS_OLD_TREE' => $is_old_tree,
            'COMMAND_TO_TRY' => $command_to_try,
            'PUSH_URL' => $push_url,
            'BRAND_DOMAIN' => cms_parse_url_safe(normalise_idn_url(get_brand_base_url()), PHP_URL_HOST),
            'TRACKER_URL' => post_param_string('tracker_url'),
            'PROJECT_ID' => post_param_string('project_id'),
            'STABLE_BRANCH_NAME' => STABLE_BRANCH_NAME,
        ]);
    }
}

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

/*
If you get Git errors about missing username/email, you may need to set your Git config system-wide...

sudo git config --system user.name <user>
sudo git config --system user.email <user>@<domain>

If it is not pushing, you may need to tell Git about your key directly (as it may not have access to environment settings)...
sudo git config --system core.sshCommand "ssh -i /home/you/.ssh/id_rsa -F /dev/null"
Your key will have to be not encrypted. A key can be decrypted with:
openssl rsa -in /home/you/.ssh/id_rsa -out /home/you/.ssh/id_rsa
Only do this if you have secure file permissions on the key file and are very confident nobody can get into your filesystem.
*/

/*
Testing params...

keep_testing - set this to simulate any connection to composr.app
include_push_bugfix
full_scan
*/

/**
 * Module page class.
 */
class Module_admin_push_bugfix
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
            'step1' => ['RELEASE_TOOLS_PUSH_BUGFIX', 'admin/tool'],
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

        $type = get_param_string('type', 'step1');

        require_lang('cms_release_build');
        require_code('version');
        require_code('version2');
        require_code('files2');

        global $REMOTE_BASE_URL;
        $REMOTE_BASE_URL = post_param_string('remote_base_url', get_brand_base_url());

        global $GIT_PATH;
        $GIT_PATH = 'git';
        $git_result = shell_exec($GIT_PATH . ' --help 2>&1');
        if (strpos($git_result, 'git: command not found') !== false) {
            if (file_exists('/usr/local/git/bin/git')) {
                $GIT_PATH = '/usr/local/git/bin/git';
            } elseif (file_exists('C:\\Program Files (x86)\\Git\\bin\\git.exe')) {
                $GIT_PATH = '"C:\\Program Files (x86)\\Git\\bin\\git.exe"';
            }
        }

        switch ($type) {
            case 'step1':
                $this->title = get_screen_title('PUSH_BUGFIX_TITLE', true, [escape_html(strval(1))]);
                break;
            case 'step2':
                $this->title = get_screen_title('PUSH_BUGFIX_TITLE', true, [escape_html(strval(2))]);
                break;
            case 'step3':
                $this->title = get_screen_title('PUSH_BUGFIX_TITLE', true, [escape_html(strval(3))]);
                break;
            case 'step4':
                $this->title = get_screen_title('PUSH_BUGFIX_TITLE', true, [escape_html(strval(4))]);
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
     * The UI for pushing a bugfix: API URLs.
     *
     * @return Tempcode The UI
     */
    public function step1() : object
    {
        require_code('form_templates');

        global $REMOTE_BASE_URL;

        $text = do_lang_tempcode('PUSH_BUGFIX_TEXT');
        $fields = new Tempcode();

        // URLs
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => 'c71105a3e00c55cb7ef29a3e23e43033', 'TITLE' => do_lang_tempcode('PUSH_BUGFIX_URLS'), 'HELP' => do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_URLS')]));
        $fields->attach(form_input_url(do_lang_tempcode('PUSH_BUGFIX_REMOTE_BASE_URL'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_REMOTE_BASE_URL'), 'remote_base_url', $REMOTE_BASE_URL, true));

        // Full scan
        $fields->attach(form_input_tick(do_lang_tempcode('PUSH_BUGFIX_ISSUE_FULL_SCAN'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_FULL_SCAN'), 'full_scan', false));

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('csrf_token_preserve', '1'));

        $post_url = build_url(['page' => '_SELF', 'type' => 'step2'], '_SELF');

        return do_template('FORM_SCREEN', [
            '_GUID' => '6639e8eb46b0904f42f5f0dcee5ae155',
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
     * The UI for pushing a bugfix: The issue contents.
     *
     * @return Tempcode The UI
     */
    public function step2() : object
    {
        require_code('form_templates');

        global $REMOTE_BASE_URL;

        if (!is_suexec_like()) {
            require_code('global4');
            list($username, $suexec) = get_exact_usernames_and_suexec();
            attach_message(do_lang_tempcode('NOT_RUNNING_SUEXEC', escape_html($username)), 'warn');
        }

        $on_disk_version = get_version_dotted();

        $git_found = $this->git_find_uncommitted_files(get_param_integer('include_push_bugfix', 0) == 1);
        $do_full_scan = (post_param_integer('full_scan', 0) == 1);
        if (($do_full_scan) || (empty($git_found))) {
            $files = $this->push_bugfix_do_dir($git_found, 24 * 60 * 60);
            if (empty($files)) {
                $checkout_seconds = time() - website_creation_time();
                $days = min(14, intval(round($checkout_seconds / (60 * 60 * 24) - 1)));
                $files = $this->push_bugfix_do_dir($git_found, 24 * 60 * 60 * $days);
            }
        } else {
            $files = array_keys($git_found);
        }

        $projects = [ // TODO: might need changed when starting to use composr.app
            1 => 'Composr',
            10 => 'Composr alpha bug reports',
            8 => 'Composr build tools',
            7 => 'Composr documentation',
            5 => 'Composr downloadable themes',
            9 => 'Composr testing platform',
            3 => 'Composr website (composr.app)',
            4 => 'Composr non-bundled addons',
        ];
        if (in_array(cms_version_branch_status(), [VERSION_ALPHA, VERSION_BETA])) {
            $default_project_id = 10;
        } else {
            $default_project_id = 1;
        }

        $categories = $this->get_tracker_categories();
        if ($categories === null) {
            warn_exit(do_lang_tempcode('PUSH_BUGFIX_FAILED_TO_CONNECT', escape_html($REMOTE_BASE_URL)));
        }

        $severity = [
            10 => ['Feature-request', do_lang('PUSH_BUGFIX_ISSUE_SEVERITY_FEATURE')],
            20 => ['Trivial-bug', do_lang('PUSH_BUGFIX_ISSUE_SEVERITY_TRIVIAL')],
            50 => ['Minor-bug', do_lang('PUSH_BUGFIX_ISSUE_SEVERITY_MINOR')],
            60 => ['Major-bug', do_lang('PUSH_BUGFIX_ISSUE_SEVERITY_MAJOR')],
            95 => ['Security-hole', do_lang('PUSH_BUGFIX_SECURITY_PROTOCOL', escape_html($REMOTE_BASE_URL))],
        ];

        $text = do_lang_tempcode('PUSH_BUGFIX_TEXT');

        $fields = new Tempcode();

        // Description
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '58ef93bddc7d2b92db5449855d706544', 'TITLE' => do_lang_tempcode('PUSH_BUGFIX_DESCRIPTION'), 'HELP' => do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_DESCRIPTION')]));
        $fields->attach(form_input_line(do_lang_tempcode('PUSH_BUGFIX_ISSUE_SUMMARY'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_SUMMARY'), 'title', null, true));
        $fields->attach(form_input_text(do_lang_tempcode('PUSH_BUGFIX_ISSUE_DESCRIPTION'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_DESCRIPTION'), 'notes', '', true));
        $fields->attach(form_input_line(do_lang_tempcode('PUSH_BUGFIX_ISSUE_AFFECTS'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_AFFECTS'), 'affects', null, false));

        // Files
        if (!cms_empty_safe($files)) {
            $file_fields = new Tempcode();
            foreach ($files as $path) {
                $git_dirty = isset($git_found[$path]);
                $file_fields->attach(form_input_list_entry(escape_html($path), $git_dirty));
            }
            $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '7af7c8c39cc5ab65909e42e54e9784ae', 'TITLE' => do_lang_tempcode('PUSH_BUGFIX_FIX'), 'HELP' => do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_FIX')]));
            $fields->attach(form_input_multi_list(do_lang_tempcode('PUSH_BUGFIX_ISSUE_FILES'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_FILES'), 'fixed_files', $file_fields));
        }

        // Classification
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '8b1fa487f622ec2aca46b7ee87119312', 'TITLE' => do_lang_tempcode('PUSH_BUGFIX_CLASSIFICATION'), 'HELP' => do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_CLASSIFICATION')]));
        $fields->attach(form_input_line(do_lang_tempcode('PUSH_BUGFIX_ISSUE_VERSION'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_VERSION'), 'version', $on_disk_version, true));
        $project_fields = new Tempcode();
        foreach ($projects as $project_id => $project_title) {
            $project_fields->attach(form_input_list_entry(strval($project_id), ($project_id == $default_project_id), escape_html($project_title)));
        }
        $fields->attach(form_input_list(do_lang_tempcode('PUSH_BUGFIX_ISSUE_PROJECT'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_PROJECT'), 'project', $project_fields));
        $category_fields = new Tempcode();
        foreach ($categories as $category_id => $category_title) {
            $category_fields->attach(form_input_list_entry(escape_html(strval($category_id)), false, escape_html($category_title)));
        }
        $fields->attach(form_input_list(do_lang_tempcode('PUSH_BUGFIX_ISSUE_CATEGORY'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_CATEGORY'), 'category', $category_fields));
        $severity_fields = new Tempcode();
        foreach ($severity as $severity_id => $severity_info) {
            list($severity_title, $severity_description) = $severity_info;
            $severity_fields->attach(form_input_radio_entry('severity', escape_html(strval($severity_id)), false, escape_html($severity_title), null, protect_from_escaping($severity_description)));
        }
        $fields->attach(form_input_radio(do_lang_tempcode('PUSH_BUGFIX_ISSUE_SEVERITY'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_SEVERITY'), 'severity', $severity_fields, true));

        // Post to
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '7a5c24f3b9478a83d42ccaa40bb1611a', 'TITLE' => do_lang_tempcode('PUSH_BUGFIX_POST_TO'), 'HELP' => do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_POST_TO')]));
        $fields->attach(form_input_integer(do_lang_tempcode('PUSH_BUGFIX_ISSUE_TRACKER'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_TRACKER'), 'tracker_id', null, false));
        $fields->attach(form_input_tick(do_lang_tempcode('PUSH_BUGFIX_ISSUE_TRACKER_CLOSE'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_TRACKER_CLOSE'), 'close_issue', false));
        $fields->attach(form_input_line(do_lang_tempcode('PUSH_BUGFIX_ISSUE_GIT_COMMIT'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_GIT_COMMIT'), 'git_commit_id', null, cms_empty_safe($files)));
        $fields->attach(form_input_integer(do_lang_tempcode('PUSH_BUGFIX_ISSUE_FORUM_POST'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_FORUM_POST'), 'post_id', null, false));

        // Submission
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '70d9a04e0e791d1eb14cf7a67efd1c73', 'TITLE' => do_lang_tempcode('PUSH_BUGFIX_SUBMISSION'), 'HELP' => do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_SUBMISSION')]));
        $submit_to_fields = new Tempcode();
        $submit_to_fields->attach(form_input_radio_entry('submit_to', 'test', true, do_lang_tempcode('PUSH_BUGFIX_SUBMIT_TO_TEST')));
        $submit_to_fields->attach(form_input_radio_entry('submit_to', 'live', false, do_lang_tempcode('PUSH_BUGFIX_SUBMIT_TO_LIVE')));
        $fields->attach(form_input_radio(do_lang_tempcode('PUSH_BUGFIX_ISSUE_SUBMIT_TO'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_SUBMIT_TO'), 'submit_to', $submit_to_fields, true));
        $fields->attach(form_input_line(do_lang_tempcode('PUSH_BUGFIX_ISSUE_USERNAME'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_USERNAME'), 'username', null, true));
        $fields->attach(form_input_password(do_lang_tempcode('PUSH_BUGFIX_ISSUE_PASSWORD'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_PASSWORD'), 'password', true));

        // Confirmation
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '6cf847d883b093f78225685d6f2d38b3', 'TITLE' => do_lang_tempcode('PUSH_BUGFIX_CONFIRMATION'), 'HELP' => do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_CONFIRMATION')]));
        $fields->attach(form_input_tick(do_lang_tempcode('PUSH_BUGFIX_ISSUE_TESTED'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_TESTED'), 'tested', false));
        // TODO: validation for this tick

        $hidden = build_keep_post_fields();

        $post_url = build_url(['page' => '_SELF', 'type' => 'step3'], '_SELF');

        return do_template('ADMIN_PUSH_BUGFIX_STEP2', [
            '_GUID' => '19171b994773116d54ab5b6b8c80e827',
            'GET' => false,
            'SKIP_WEBSTANDARDS' => true,
            'HIDDEN' => $hidden,
            'TITLE' => $this->title,
            'TEXT' => $text,
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'FIELDS' => $fields,
            'URL' => $post_url,

            'REMOTE_BASE_URL' => post_param_string('remote_base_url'),
            'GIT_FOUND' => $git_found,
            'DEFAULT_PROJECT_ID' => strval($default_project_id),
        ]);
    }

    /**
     * The Actualiser for pushing a bugfix and prompting for a hotfix.
     *
     * @return Tempcode The UI
     */
    public function step3() : object
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);

        global $GIT_PATH;

        $git_commit_id = post_param_string('git_commit_id', '');
        $_fixed_files = post_param_string('fixed_files', null);

        // Validation for files / git
        if (($git_commit_id == '') && ($_fixed_files === null)) {
            warn_exit(do_lang_tempcode('PUSH_BUGFIX_NO_GIT_OR_FILES'));
        }

        // Validation for testing
        $tested = post_param_integer('tested', 0);
        if ($tested != 1) {
            warn_exit(do_lang_tempcode('PUSH_BUGFIX_MUST_TEST_FIX'));
        }

        // Parse other inputs
        $submit_to = post_param_string('submit_to');
        $version_dotted = post_param_string('version');
        $title = post_param_string('title');
        $notes = post_param_string('notes', '');
        $affects = post_param_string('affects', '');

        global $REMOTE_BASE_URL;
        $REMOTE_BASE_URL = ($submit_to == 'live') ? $REMOTE_BASE_URL : get_base_url();

        $done = [];

        // Determine fixed files
        if ($_fixed_files !== null) {
            $fixed_files = explode(',', $_fixed_files);
        } else {
            $git_command = $GIT_PATH . ' show --pretty="format:" --name-only ' . $git_commit_id;
            $git_result = shell_exec($git_command . ' 2>&1');
            $__fixed_files = explode("\n", $git_result);

            $fixed_files = [];
            if ($__fixed_files !== false) {
                foreach ($__fixed_files as $file) {
                    if ($file != '') {
                        $fixed_files[] = $file;
                    }
                }
            }
        }

        // Find what addons are involved with this
        require_code('addons2');
        $addons_involved = [];
        $hooks = find_all_hooks('systems', 'addon_registry');
        foreach ($fixed_files as $file) {
            foreach ($hooks as $addon_name => $place) {
                if ($place == 'sources_custom') {
                    $addon_info = read_addon_info($addon_name);
                    if (in_array($file, $addon_info['files'])) {
                        $addons_involved[] = $addon_name;
                    }
                }
            }
        }

        // If no tracker issue number was given, one is made
        $tracker_id = post_param_integer('tracker_id', null);
        $tracker_title = $title;
        $tracker_message = $notes;
        $tracker_project = post_param_integer('project');
        $tracker_category = post_param_integer('category');
        $tracker_severity = post_param_integer('severity');
        $tracker_additional = '';
        if ($affects != '') {
            $tracker_additional = 'Affects: ' . $affects;
        }
        $is_new_on_tracker = ($tracker_id === null);
        if ($is_new_on_tracker) {
            // Make tracker issue
            $tracker_id = $this->create_tracker_issue($version_dotted, $tracker_title, $tracker_message, $tracker_additional, $tracker_severity, $tracker_category, $tracker_project);
            if ($tracker_id !== null) {
                $tracker_url = $REMOTE_BASE_URL . '/tracker/view.php?id=' . strval($tracker_id);
                $done[do_lang('PUSH_BUGFIX_CREATED_ISSUE')] = $tracker_url;
            } else {
                $tracker_url = null;
                $done[do_lang('PUSH_BUGFIX_CREATED_ISSUE_FAILED')] = null;
            }
        } else {
            // Make tracker comment
            $tracker_comment_message = do_lang('PUSH_BUGFIX_TRACKER_COMMENT_MESSAGE', escape_html($tracker_title), escape_html($tracker_message), escape_html($tracker_additional));
            $tracker_post_id = $this->create_tracker_post($tracker_id, $tracker_comment_message, $version_dotted, $tracker_severity, $tracker_category, $tracker_project);
            if ($tracker_post_id !== null) {
                $tracker_url = $REMOTE_BASE_URL . '/tracker/view.php?id=' . strval($tracker_id);
                $done[do_lang('PUSH_BUGFIX_RESPONDED_TO_TRACKER_ISSUE')] = $tracker_url;
            } else {
                $tracker_url = null;
                $done[do_lang('PUSH_BUGFIX_RESPONDED_TO_TRACKER_ISSUE_FAILED')] = null;
            }
        }

        // A Git commit and push happens on the changed files, with the ID number of the tracker issue in it
        $git_commit_command_data = '';
        $git_url = CMS_REPOS_URL . '/commit/' . $git_commit_id;
        if ($git_commit_id == '') {
            if ($tracker_id !== null) {
                if ($tracker_severity == 95) {
                    $git_commit_message = do_lang('PUSH_BUGFIX_GIT_MESSAGE_SECURITY_FIX', strval($tracker_id), escape_html($title));
                } elseif ($tracker_severity > 10) {
                    $git_commit_message = do_lang('PUSH_BUGFIX_GIT_MESSAGE_FIX', strval($tracker_id), escape_html($title));
                } else {
                    $git_commit_message = do_lang('PUSH_BUGFIX_GIT_MESSAGE_IMPLEMENT', strval($tracker_id), escape_html($title));
                }
                if ($submit_to == 'live') {
                    $git_commit_id = $this->do_git_commit($git_commit_message, $fixed_files, $git_commit_command_data);
                    if ($git_commit_id !== null) {
                        $git_url = CMS_REPOS_URL . '/commit/' . $git_commit_id;
                        $done[do_lang('PUSH_BUGFIX_COMMITTED_TO_GIT')] = $git_url;
                    } else {
                        $done[do_lang('PUSH_BUGFIX_COMMITTED_TO_GIT_FAILED', escape_html($git_commit_command_data))] = null;
                    }
                }
            }
        }

        // Make tracker comment with fix link
        $tracker_comment_message = '';
        if ($git_commit_id !== null) {
            $tracker_comment_message .= do_lang('PUSH_BUGFIX_TRACKER_COMMENT_MESSAGE_GIT', escape_html($git_commit_id), escape_html($git_url));
            if ($tracker_id !== null) {
                $update_post_id = $this->create_tracker_post($tracker_id, $tracker_comment_message);
                if ($update_post_id !== null) {
                    $done[do_lang('PUSH_BUGFIX_TRACKER_UPDATE_POST')] = null;
                } else {
                    $done[do_lang('PUSH_BUGFIX_TRACKER_UPDATE_POST_FAILED')] = null;
                }
            }
        }

        // The tracker issue gets closed
        $close_issue = (post_param_integer('close_issue', 0) == 1);
        if (($close_issue) && ($tracker_id !== null)) {
            $close_success = $this->close_tracker_issue($tracker_id);
            if ($close_success) {
                $done[do_lang('PUSH_BUGFIX_TRACKER_CLOSED')] = null;
            } else {
                $done[do_lang('PUSH_BUGFIX_TRACKER_CLOSED_FAILED')] = null;
            }
        }

        // If a forum post ID was given, an automatic reply is given pointing to the tracker issue
        $post_id = post_param_integer('post_id', null);
        if (($post_id !== null) && ($tracker_id !== null)) {
            $post_reply_title = do_lang('PUSH_BUGFIX_FORUM_POST_TITLE');
            $post_reply_message = do_lang('PUSH_BUGFIX_FORUM_POST_MESSAGE', escape_html($is_new_on_tracker ? 'as' : 'in'), strval($tracker_id), [$tracker_url]);
            $post_important = 1;
            $reply_id = $this->create_forum_post($post_id, $post_reply_title, $post_reply_message, $post_important);
            if ($reply_id !== null) {
                $reply_url = $REMOTE_BASE_URL . '/forum/topicview/findpost/' . strval($reply_id) . '.htm';
                $done[do_lang('PUSH_BUGFIX_FORUM_POSTED_REPLY')] = $reply_url;
            } else {
                $done[do_lang('PUSH_BUGFIX_FORUM_POSTED_REPLY_FAILED')] = null;
            }
        }

        // Show progress
        $out = new Tempcode();
        $out->attach('<ol>');
        foreach ($done as $done_title => $done_url) {
            if ($done_url === null) {
                $out->attach('<li>' . $done_title . '</li>');
            } else {
                $out->attach('<li>');
                $out->attach(hyperlink($done_url, $done_title, true, true));
                $out->attach('</li>');
            }
        }
        $out->attach('</ol>');

        if (!empty($addons_involved)) {
            $addons_involved = array_unique($addons_involved);
            $out->attach(paragraph(do_lang_tempcode('PUSH_BUGFIX_FOR_ADDON', protect_from_escaping(hyperlink(escape_html(get_base_url()) . '/adminzone/index.php?page=build_addons&amp;addon_limit=' . escape_html(urlencode(implode(',', $addons_involved))), do_lang_tempcode('PUSH_BUGFIX_ADDON_UPDATE_SCRIPT'), false, true)))));
        }

        if ($tracker_id === null) {
            return $out;
        }

        require_code('form_templates');

        $_username = escape_html(post_param_string('username', false, INPUT_FILTER_POST_IDENTIFIER));
        $_password = escape_html(post_param_string('password', false, INPUT_FILTER_PASSWORD));

        $_tracker_id = escape_html(strval($tracker_id));

        $fields = new Tempcode();

        // Add files
        $fields->attach(do_template('FORM_SCREEN_FIELD_SPACER', ['_GUID' => '5cf81230cfa9e0e2aea4914a74740772', 'TITLE' => do_lang_tempcode('PUSH_BUGFIX_HOTFIX'), 'HELP' => do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_HOTFIX')]));
        $file_fields = new Tempcode();
        foreach ($fixed_files as $fixed_file) {
            $selected = true;

            // Exceptions
            if (preg_match('#^docs/#', $fixed_file) != 0) {
                $selected = false;
            }
            if (in_array($fixed_file, [
                'sources_custom/string_scan.php',
                'data_custom/functions.bin',
            ])) {
                $selected = false;
            }

            $file_fields->attach(form_input_list_entry(escape_html($fixed_file), $selected));
        }
        $fields->attach(form_input_multi_list(do_lang_tempcode('PUSH_BUGFIX_ISSUE_FILES'), do_lang_tempcode('DESCRIPTION_PUSH_BUGFIX_ISSUE_FILES'), 'fixed_files', $file_fields, null, 5, true));
        $post_url = get_self_url(true, false, ['type' => 'step4']);

        $hidden = new Tempcode();
        $hidden->attach(form_input_hidden('username', $_username));
        $hidden->attach(form_input_hidden('password', $_password));
        $hidden->attach(form_input_hidden('tracker_id', $_tracker_id));
        $hidden->attach(form_input_hidden('submit_to', $submit_to));
        $hidden->attach(form_input_hidden('remote_base_url', $REMOTE_BASE_URL));

        return do_template('FORM_SCREEN', [
            '_GUID' => 'dd3e26c3820aa94146e3d71b804abf29',
            'GET' => false,
            'SKIP_WEBSTANDARDS' => true,
            'HIDDEN' => $hidden,
            'TITLE' => $this->title,
            'TEXT' => $out,
            'SUBMIT_ICON' => 'buttons/proceed',
            'SUBMIT_NAME' => do_lang_tempcode('PROCEED'),
            'FIELDS' => $fields,
            'URL' => $post_url,
        ]);
    }

    /**
     * The Actualiser for pushing a hotfix.
     *
     * @return Tempcode The results
     */
    public function step4() : object
    {
        cms_extend_time_limit(TIME_LIMIT_EXTEND__MODEST);

        $_username = escape_html(post_param_string('username', false, INPUT_FILTER_POST_IDENTIFIER));
        $_password = escape_html(post_param_string('password', false, INPUT_FILTER_PASSWORD));
        $submit_to = post_param_string('submit_to');

        global $REMOTE_BASE_URL;
        $REMOTE_BASE_URL = ($submit_to == 'live') ? $REMOTE_BASE_URL : get_base_url();

        $tracker_id = post_param_integer('tracker_id');

        $fixed_files = $_POST['fixed_files'];

        $done = [];

        // A TAR of fixed files is uploaded to the tracker issue (correct relative file paths intact)
        $file_id = $this->upload_to_tracker_issue($tracker_id, $this->create_hotfix_tar($tracker_id, $fixed_files));
        if ($file_id !== null) {
            $tracker_comment_message = do_lang('PUSH_BUGFIX_TRACKER_COMMENT_HOTFIX');
            $tracker_post_id = $this->create_tracker_post($tracker_id, $tracker_comment_message);
            $done[do_lang('PUSH_BUGFIX_UPLOADED_HOTFIX')] = null;
        } else {
            $done[do_lang('PUSH_BUGFIX_UPLOADED_HOTFIX_FAILED')] = null;
        }

        // Show progress
        $out = new Tempcode();
        $out->attach('<ol>');
        foreach ($done as $done_title => $done_url) {
            if ($done_url === null) {
                $out->attach('<li>' . $done_title . '</li>');
            } else {
                $out->attach('<li>');
                $out->attach(hyperlink($done_url, $done_title, true, true));
                $out->attach('</li>');
            }
        }
        $out->attach('</ol>');

        return $out;
    }

    /**
     * Find files which have not been committed to git (have been changed).
     *
     * @param  boolean $include_push_bugfix Whether to include the push_bugfix.php file
     * @return array Files found for committing
     */
    protected function git_find_uncommitted_files(bool $include_push_bugfix) : array
    {
        global $GIT_PATH;

        chdir(get_file_base());
        $git_command = $GIT_PATH . ' status';
        $git_result = shell_exec($git_command . ' 2>&1');
        $lines = explode("\n", $git_result);
        $git_found = [];
        foreach ($lines as $line) {
            $matches = [];
            if (preg_match('#\t(both modified|modified|new file|deleted):\s+(.*)$#', $line, $matches) != 0) {
                if (($matches[2] != 'data/files.bin') && ((basename($matches[2]) != 'admin_push_bugfix.php') || ($include_push_bugfix))) {
                    $file_addon = $GLOBALS['SITE_DB']->query_select_value_if_there('addons_files', 'addon_name', ['filepath' => $matches[2]]);
                    if ($file_addon !== null) {
                        if (!is_file(get_file_base() . '/sources/hooks/systems/addon_registry/' . $file_addon . '.php')) {
                            $file_addon = null;
                        }
                    }
                    $git_found[$matches[2]] = $file_addon;
                }
            }
        }
        if ((empty($git_found)) && (!$include_push_bugfix)) {
            return $this->git_find_uncommitted_files(true);
        }
        return $git_found;
    }

    /**
     * Process a commit to git.
     *
     * @param  string $git_commit_message The message to add to the commit
     * @param  array $files Array of files to commit
     * @param  string $git_commit_command_data Data returned from the git commands (passed by reference)
     * @return ?ID_TEXT The commit ID (null: did not commit)
     */
    protected function do_git_commit(string $git_commit_message, array $files, string &$git_commit_command_data) : ?string
    {
        if (get_param_integer('keep_testing', 0) == 1) {
            return 'xyz';
        }

        global $GIT_PATH;

        chdir(get_file_base());

        $git_commit_command_data = '';

        // Current status
        $cmd = $GIT_PATH . ' status';
        $git_status_data = shell_exec($cmd . ' 2>&1');
        $is_unpushed_prior = (strpos($git_status_data, 'Your branch is ahead') !== false);

        // Add
        $cmd = $GIT_PATH . ' add';
        foreach ($files as $path) {
            $cmd .= ' ' . escapeshellarg($path);
        }
        $git_commit_command_data .= shell_exec($cmd . ' 2>&1');

        // Commit
        $cmd = $GIT_PATH . ' commit';
        foreach ($files as $path) {
            $cmd .= ' ' . cms_escapeshellarg($path);
        }
        $cmd .= ' -m ' . cms_escapeshellarg($git_commit_message);
        $git_commit_command_data .= shell_exec($cmd . ' 2>&1');

        $matches = [];
        if (preg_match('# ([\da-z]+)\]#', $git_commit_command_data, $matches) != 0) {
            if (!$is_unpushed_prior) {
                // Success, do a push too
                $cmd = $GIT_PATH . ' push';
                $git_commit_command_data .= shell_exec($cmd . ' 2>&1');
            }

            return $matches[1];
        }

        // Error
        $git_commit_command_data = do_lang('PUSH_BUGFIX_GIT_COMMIT_ERROR', escape_html($git_commit_command_data), escape_html($cmd));
        return null;
    }

    /**
     * Get a list of addon categories from the software tracker
     *
     * @return ?array An array of categories (null: error)
     */
    protected function get_tracker_categories() : ?array
    {
        // LEGACY
        $call = 'tracker_categories';
        global $REMOTE_BASE_URL;
        if ($REMOTE_BASE_URL == 'https://compo.sr') {
            $call = __FUNCTION__;
        }

        $result = $this->make_call($call, null);
        return @json_decode($result, true);
    }

    /**
     * Make a call to the software tracker to create a tracker issue.
     *
     * @param  ID_TEXT $version_dotted The dotted version number of the software
     * @param  string $tracker_title The title / summary of the issue
     * @param  string $tracker_message The description of the issue
     * @param  string $tracker_additional The additional details of the issue
     * @param  integer $tracker_severity The Mantis severity level of this issue
     * @param  integer $tracker_category The category ID for this issue
     * @param  integer $tracker_project The project ID for this issue
     * @return ?AUTO_LINK The ID of the new tracker issue (null: error)
     */
    protected function create_tracker_issue(string $version_dotted, string $tracker_title, string $tracker_message, string $tracker_additional, int $tracker_severity, int $tracker_category, int $tracker_project) : ?int
    {
        if (get_param_integer('keep_testing', 0) == 1) {
            return 123;
        }

        $post = [
            'version_dotted' => $version_dotted,
            'tracker_title' => $tracker_title,
            'tracker_message' => $tracker_message,
            'tracker_additional' => $tracker_additional,
            'tracker_severity' => $tracker_severity,
            'tracker_category' => $tracker_category,
            'tracker_project' => $tracker_project
        ];

        // LEGACY
        $call = 'tracker_issues';
        global $REMOTE_BASE_URL;
        if ($REMOTE_BASE_URL == 'https://compo.sr') {
            $call = __FUNCTION__;
        }

        $result = $this->make_call($call, $post);
        if (cms_empty_safe($result)) {
            return null;
        }

        // LEGACY
        if (is_numeric($result)) {
            return intval($result);
        }

        return intval($result['id']);
    }

    /**
     * Add a comment to a tracker issue.
     *
     * @param  AUTO_LINK $tracker_id The ID of the tracker issue to comment
     * @param  string $tracker_comment_message The comment to post
     * @param  ?string $version_dotted The software version for this issue (null: do not change)
     * @param  ?integer $tracker_severity The Mantis severity level for the issue (null: do not change)
     * @param  ?integer $tracker_category The category ID for this tracker issue (null: do not change)
     * @param  ?integer $tracker_project The project ID of this tracker issue (null: do not change)
     * @return ?AUTO_LINK The comment ID (null: error)
     */
    protected function create_tracker_post(int $tracker_id, string $tracker_comment_message, ?string $version_dotted = null, ?int $tracker_severity = null, ?int $tracker_category = null, ?int $tracker_project = null) : ?int
    {
        if (get_param_integer('keep_testing', 0) == 1) {
            return 123;
        }

        $post = [
            'tracker_id' => $tracker_id,
            'tracker_comment_message' => $tracker_comment_message,
            'version_dotted' => $version_dotted,
            'tracker_severity' => $tracker_severity,
            'tracker_category' => $tracker_category,
            'tracker_project' => $tracker_project
        ];

        // LEGACY
        $call = 'tracker_posts';
        global $REMOTE_BASE_URL;
        if ($REMOTE_BASE_URL == 'https://compo.sr') {
            $call = __FUNCTION__;
        }

        $result = $this->make_call($call, $post);
        if (cms_empty_safe($result)) {
            return null;
        }

        // LEGACY
        if (is_numeric($result)) {
            return intval($result);
        }

        return intval($result['id']);
    }

    /**
     * Upload a TAR file to a tracker issue.
     *
     * @param  AUTO_LINK $tracker_id The tracker issue ID
     * @param  PATH $tar_path The path to the TAR file to upload
     * @return ?AUTO_LINK The resource ID (null: error)
     */
    protected function upload_to_tracker_issue(int $tracker_id, string $tar_path) : ?int
    {
        if (get_param_integer('keep_testing', 0) == 1) {
            return 123;
        }

        $put = [
            'PUT_id' => $tracker_id,
            // Upload will be filled in by make_call
        ];

        // LEGACY
        $call = 'tracker_issues';
        global $REMOTE_BASE_URL;
        if ($REMOTE_BASE_URL == 'https://compo.sr') {
            $call = __FUNCTION__;
        }

        $result = $this->make_call($call, $put, $tar_path);
        if (cms_empty_safe($result)) {
            return null;
        }

        // LEGACY
        if (is_numeric($result)) {
            return intval($result);
        }

        if (!isset($result['upload'])) {
            return null;
        }
        return intval($result['upload']);
    }

    /**
     * Close a tracker issue.
     *
     * @param  AUTO_LINK $tracker_id The issue ID to close
     * @return boolean Whether it was closed successfully
     */
    protected function close_tracker_issue(int $tracker_id) : bool
    {
        if (get_param_integer('keep_testing', 0) == 1) {
            return true;
        }

        $put = [
            'PUT_id' => $tracker_id,
            'close' => 1
        ];

        // LEGACY
        $call = 'tracker_issues';
        global $REMOTE_BASE_URL;
        if ($REMOTE_BASE_URL == 'https://compo.sr') {
            unset($put['close']);
            $call = __FUNCTION__;
        }

        $result = $this->make_call($call, $put);
        if (cms_empty_safe($result)) {
            return false;
        }

        // LEGACY
        if (is_numeric($result)) {
            return (intval($result) === 1);
        }

        return $result['success'];
    }

    /**
     * Create a forum post reply on the software homesite.
     *
     * @param  AUTO_LINK $replying_to_post The ID of the post which we are replying
     * @param  string $post_reply_title The title of our reply
     * @param  string $post_reply_message The post text
     * @param  BINARY $post_important Whether this post should be marked as important
     * @return ?AUTO_LINK The ID of the post (null: error)
     */
    protected function create_forum_post(int $replying_to_post, string $post_reply_title, string $post_reply_message, int $post_important) : ?int
    {
        if (get_param_integer('keep_testing', 0) == 1) {
            return 123;
        }

        $post = [
            'replying_to_post' => $replying_to_post,
            'post_reply_title' => $post_reply_title,
            'post_reply_message' => $post_reply_message,
            'post_important' => $post_important,
        ];

        // LEGACY
        $call = 'forum_posts';
        global $REMOTE_BASE_URL;
        if ($REMOTE_BASE_URL == 'https://compo.sr') {
            $call = __FUNCTION__;
        }

        $result = $this->make_call($call, $post);
        if (cms_empty_safe($result)) {
            return null;
        }

        // LEGACY
        if (is_numeric($result)) {
            return intval($result);
        }

        return intval($result['id']);
    }

    /**
     * Create a TAR file for a hotfix.
     *
     * @param  AUTO_LINK $tracker_id The ID of the relevant tracker issue
     * @param  array $files Array of files to add to the TAR
     * @return PATH The file path to the new TAR
     */
    protected function create_hotfix_tar(int $tracker_id, array $files) : string
    {
        disable_php_memory_limit();

        require_code('make_release');
        require_code('tar');

        $builds_path = get_builds_path();
        $hotfix_path = $builds_path . '/builds/hotfixes';

        if (!file_exists($hotfix_path)) {
            make_missing_directory($hotfix_path);
        }

        $prior_hotfixes = get_directory_contents($hotfix_path, '', 0, false);
        foreach ($prior_hotfixes as $prior_hotfix) {
            if (preg_match('#^hotfix-' . strval($tracker_id) . ', #', $prior_hotfix) != 0) {
                $tar_file = tar_open($hotfix_path . '/' . $prior_hotfix, 'rb', true);
                $tar_directory = tar_get_directory($tar_file);
                foreach ($tar_directory as $tar_entry) {
                    $files[] = $tar_entry['path'];
                }
                tar_close($tar_file);
            }
        }

        $tar_path = $hotfix_path . '/hotfix-' . strval($tracker_id) . ', ' . date('Y-m-d ga') . '.tar';
        $tar_file = tar_open($tar_path, 'wb');
        foreach (array_unique($files) as $path) {
            $file_fullpath = get_file_base() . '/' . $path;
            if (is_file($file_fullpath)) { // If it's a deletion, obviously we cannot put it into a hotfix
                tar_add_file($tar_file, $path, $file_fullpath, 0644, filemtime($file_fullpath), true);
            }
        }
        tar_close($tar_file);

        sync_file($tar_path);
        fix_permissions($tar_path);

        return $tar_path;
    }

    /**
     * Make an API call to the software homesite.
     * TODO: After removing legacy code, assign type ?array as return.
     *
     * @param  ID_TEXT $call The function to call
     * @param  ?array $post_params POST parameters to send with the request (PUT_id: use a PUT request) (null: none, and use a GET request)
     * @param  ?PATH $file The path to the file to upload with the request (null: do not upload a file)
     * @return ?mixed The results of the call (null: error)
     */
    protected function make_call(string $call, ?array $post_params, ?string $file = null)
    {
        global $REMOTE_BASE_URL;

        // LEGACY
        if ($REMOTE_BASE_URL == 'https://compo.sr') {
            if ($post_params !== null) {
                $post_params = ['parameters' => array_values($post_params)];
                foreach ($post_params as $key => $param) {
                    if (is_array($param)) {
                        $first_null_index = 0;
                        foreach ($param as $i => $val) {
                            if ($val !== null) {
                                $first_null_index = ($i + 1);
                            }
                        }
                        foreach ($param as $i => $val) {
                            if ($i >= $first_null_index) {
                                break;
                            }
                            $post_params[$key . '[' . strval($i) . ']'] = @strval($val);
                        }
                        unset($post_params[$key]);
                    }
                }
            }

            $_username = post_param_string('username', null, INPUT_FILTER_POST_IDENTIFIER);
            if ($_username !== null) {
                $_password = post_param_string('password', '', INPUT_FILTER_PASSWORD);
                if ($post_params === null) {
                    $post_params = [];
                }
                $post_params['password'] = ($_username == '') ? $_password : ($_username . ':' . $_password);
            }

            global $REMOTE_BASE_URL;
            $call_url = $REMOTE_BASE_URL . '/data_custom/composr_homesite_web_service.php?call=' . urlencode($call);

            $files = ($file === null) ? null : ['upload' => $file];

            $result = cms_http_request($call_url, ['post_params' => $post_params, 'files' => $files, 'trigger_error' => false]);
            if (substr($result->message, 0, 1) !== '2') {
                return null;
            }

            return $result->data;
        }

        $type = 'GET';
        $id = null;
        if ($post_params !== null) {
            $type = 'POST';
            foreach ($post_params as $key => $param) {
                if ($key == 'PUT_id') {
                    $type = 'PUT';
                    $id = $param;
                    unset($post_params['PUT_id']);
                    if (count($post_params) == 0) {
                        $post_params = null;
                    }
                    continue;
                }
                if (is_array($param)) {
                    foreach ($param as $i => $val) {
                        $post_params[$key . '[' . strval($i) . ']'] = @strval($val);
                    }
                    unset($post_params[$key]);
                }
            }
        }


        $auth = null;
        $_username = post_param_string('username', null, INPUT_FILTER_POST_IDENTIFIER);
        $_password = post_param_string('password', '', INPUT_FILTER_PASSWORD);
        if ($_username !== null) { // Member auth
            $auth = [$_username, $_password];
        } elseif ($_password != '') { // Master password auth
            $auth = [STRING_MAGIC_NULL_BASE64, $_password];
        }

        $call_url = $REMOTE_BASE_URL . '/data/endpoint.php/cms_homesite/' . urlencode($call);
        if ($id !== null) {
            $call_url .= '/' . urlencode(strval($id));
        }

        $files = ($file === null) ? null : ['upload' => $file];

        $result = cms_http_request($call_url, ['http_verb' => $type, 'post_params' => $post_params, 'files' => $files, 'auth' => $auth, 'trigger_error' => false]);
        if (substr($result->message, 0, 1) !== '2') {
            return null;
        }

        $ret = @json_decode($result->data, true);
        if ($ret === null) {
            return null;
        }
        if ($ret['success'] === false) {
            return null;
        }

        return $ret['response_data'];
    }

    /**
     * Search a directory for files that can be pushed.
     *
     * @param  array $git_found Files found by git
     * @param  int $seconds_since Search for files modified within the last given number of seconds
     * @param  PATH $subdir Search within the given sub-directory (blank: none)
     * @return array Array of files found
     */
    protected function push_bugfix_do_dir(array $git_found, int $seconds_since, string $subdir = '') : array
    {
        require_code('files');

        $stub = get_file_base() . '/' . (($subdir == '') ? '' : ($subdir . '/'));
        $out = [];
        $dh = @opendir($stub);
        if ($dh !== false) {
            while (($file = readdir($dh)) !== false) {
                if (!should_ignore_file($stub . $file, IGNORE_CUSTOM_DIR_FLOATING_CONTENTS | IGNORE_UPLOADS | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_HIDDEN_FILES | IGNORE_NONBUNDLED | IGNORE_FLOATING | IGNORE_SHIPPED_VOLATILE | IGNORE_UNSHIPPED_VOLATILE)) {
                    $full_path = $stub . $file;
                    $path = (($subdir == '') ? '' : ($subdir . '/')) . $file;

                    if (is_file($full_path)) {
                        if ((filemtime($full_path) < time() - $seconds_since) && (!isset($git_found[$path]))) {
                            continue;
                        }
                        $out[] = $path;
                    } elseif (is_dir($full_path)) {
                        $out = array_merge($out, $this->push_bugfix_do_dir($git_found, $seconds_since, $path));
                    }
                }
            }
            closedir($dh);
        }
        sort($out);
        return $out;
    }
}

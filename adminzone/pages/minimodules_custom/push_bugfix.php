<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_release_build
 */

/*
If you get git errors about missing username/email, you may need to set your git config system-wide...

sudo git config --system user.name <user>
sudo git config --system user.email <user>@<domain>

If it is not pushing, you may need to tell git about your key directly (as it may not have access to environment settings)...
sudo git config --system core.sshCommand "ssh -i /home/you/.ssh/id_rsa -F /dev/null"
Your key will have to be not encrypted. A key can be decrypted with:
openssl rsa -in /home/you/.ssh/id_rsa -out /home/you/.ssh/id_rsa
Only do this if you have secure file permissions on the key file and are very confident nobody can get into your filesystem.
*/

/*EXTRA FUNCTIONS: shell_exec|escapeshellarg*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

restrictify();
safe_ini_set('ocproducts.xss_detect', '0');

if (!is_suexec_like()) {
    require_code('global4');
    list($username, $suexec) = get_exact_usernames_and_suexec();
    attach_message('Warning: Not running an suEXEC-like environment (web user is ' . $username . '), your file permissions will likely get mangled.', 'warn');
}

$_title = get_screen_title('Composr bugfix tool', false);
$_title->evaluate_echo();

$type = isset($_GET['type']) ? $_GET['type'] : '0';

require_code('version2');
require_code('files2');

global $REMOTE_BASE_URL;
$REMOTE_BASE_URL = get_brand_base_url();

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

// Actualisation
// =============

if (cms_srv('REQUEST_METHOD') == 'POST') {
    $git_commit_id = post_param_string('git_commit_id', '');

    $done = array();
    $version_dotted = post_param_string('version');
    $title = post_param_string('title');
    $notes = post_param_string('notes', '');
    $affects = post_param_string('affects', '');
    if (!is_null(post_param_string('fixed_files', null))) {
        $fixed_files = explode(',', post_param_string('fixed_files'));
    } else {
        $fixed_files = array();
        $git_command = $GIT_PATH . ' show --pretty="format:" --name-only ' . $git_commit_id;
        $git_result = shell_exec($git_command . ' 2>&1');
        $_fixed_files = explode("\n", $git_result);
        $fixed_files = array();
        foreach ($_fixed_files as $file) {
            if ($file != '') {
                $fixed_files[] = $file;
            }
        }
    }

    // Find what addons are involved with this
    require_code('addons2');
    $addons_involved = array();
    $hooks = find_all_hooks('systems', 'addon_registry');
    foreach ($fixed_files as $file) {
        foreach ($hooks as $addon => $place) {
            if ($place == 'sources_custom') {
                $addon_info = read_addon_info($addon);
                if (in_array($file, $addon_info['files'])) {
                    $addons_involved[] = $addon;
                }
            }
        }
    }

    $submit_to = post_param_string('submit_to');
    global $REMOTE_BASE_URL;
    $REMOTE_BASE_URL = ($submit_to == 'live') ? get_brand_base_url() : get_base_url();

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
        $tracker_id = create_tracker_issue($version_dotted, $tracker_title, $tracker_message, $tracker_additional, $tracker_severity, $tracker_category, $tracker_project);
        if ($tracker_id !== null) {
            $tracker_url = $REMOTE_BASE_URL . '/tracker/view.php?id=' . strval($tracker_id);
            $done['Created new tracker issue'] = $tracker_url;
        } else {
            $tracker_url = null;
            $done['Failed to create tracker issue'] = null;
        }
    } else {
        // Make tracker comment
        $tracker_comment_message = 'Automated response: ' . $tracker_title . "\n\n" . $tracker_message . "\n\n" . $tracker_additional;
        $tracker_post_id = create_tracker_post($tracker_id, $tracker_comment_message, $version_dotted, $tracker_severity, $tracker_category, $tracker_project);
        if ($tracker_id !== null) {
            $tracker_url = $REMOTE_BASE_URL . '/tracker/view.php?id=' . strval($tracker_id);
            $done['Responded to existing tracker issue'] = $tracker_url;
        } else {
            $tracker_url = null;
            $done['Failed to respond to existing tracker issue'] = null;
        }
    }

    // A git commit and push happens on the changed files, with the ID number of the tracker issue in it
    $git_commit_command_data = '';
    if ($git_commit_id == '') {
        if ($tracker_id !== null) {
            if ($tracker_severity == 95) {
                $git_commit_message = 'Security fix for MANTIS-' . strval($tracker_id) . ' (' . $title . ')';
            } else {
                $git_commit_message = 'Fixed MANTIS-' . strval($tracker_id) . ' (' . $title . ')';
            }
            if ($submit_to == 'live') {
                $git_commit_id = do_git_commit($git_commit_message, $fixed_files, $git_commit_command_data);
                if ($git_commit_id !== null) {
                    echo '<!-- ' . $git_commit_command_data . ' -->';
                    $git_url = COMPOSR_REPOS_URL . '/commit/' . $git_commit_id;
                    $done['Committed to git'] = $git_url;
                } else {
                    $git_url = null;
                    $done['Failed to commit to git, ' . $git_commit_command_data] = null;
                }
            } else {
                $git_url = null;
                $git_commit_id = 'justtesting';
            }
        }
    } else {
        $git_url = COMPOSR_REPOS_URL . '/commit/' . $git_commit_id;
    }

    // Make tracker comment with fix link
    $create_hotfix = (post_param_integer('create_hotfix', 0) == 1);
    $tracker_comment_message = '';
    if ($git_commit_id !== null) {
        $tracker_comment_message .= 'Fixed in git commit ' . escape_html($git_commit_id) . ' (' . escape_html($git_url) . ' - link will become active once code pushed to GitLab)';
        if ($create_hotfix) {
            $tracker_comment_message .= "\n\n";
        }
    }
    if ($create_hotfix) {
        $tracker_comment_message .= 'A hotfix (a TAR of files to upload) has been uploaded to this issue. These files are made to the latest intra-version state (i.e. may roll in earlier fixes too if made to the same files) - so only upload files newer than what you have already. If there are files in a hot-fix that you don\'t have then they probably relate to addons that you don\'t have installed and should be skipped. Always take backups of files you are replacing or keep a copy of the manual installer for your version, and only apply fixes you need. These hotfixes are not necessarily reliable or well supported. Not sure how to extract TAR files to your Windows computer? Try 7-zip (http://www.7-zip.org/).';
    }
    if ($tracker_id !== null) {
        $update_post_id = create_tracker_post($tracker_id, $tracker_comment_message);
        if ($update_post_id !== null) {
            $done['Created update post on tracker'] = null;
        } else {
            $done['Failed to create update post on tracker'] = null;
        }
    }
    // A TAR of fixed files is uploaded to the tracker issue (correct relative file paths intact)
    if (($create_hotfix) && ($tracker_id !== null)) {
        $file_id = upload_to_tracker_issue($tracker_id, create_hotfix_tar($tracker_id, $fixed_files));
        if ($file_id !== null) {
            $done['Uploaded hotfix'] = null;
        } else {
            $done['Failed to upload hotfix'] = null;
        }
    }
    // The tracker issue gets closed
    $close_issue = (post_param_integer('close_issue', 0) == 1);
    if (($close_issue) && ($tracker_id !== null)) {
        $close_success = close_tracker_issue($tracker_id);
        if ($close_success) {
            $done['Closed tracker issue'] = null;
        } else {
            $done['Failed to close tracker issue'] = null;
        }
    }

    // If a forum post ID was given, an automatic reply is given pointing to the tracker issue
    $post_id = post_param_integer('post_id', null);
    if (($post_id !== null) && ($tracker_id !== null)) {
        $post_reply_title = 'Automated fix message';
        $post_reply_message = 'This issue has now been filed on the tracker ' . ($is_new_on_tracker ? 'as' : 'in') . ' issue [url="#' . strval($tracker_id) . '"]' . $tracker_url . '[/url], with a fix.';
        $post_important = 1;
        $reply_id = create_forum_post($post_id, $post_reply_title, $post_reply_message, $post_important);
        $reply_url = $REMOTE_BASE_URL . '/forum/topicview/findpost/' . strval($reply_id) . '.htm';
        $done['Posted reply on forum'] = $reply_url;
    }

    // Show progress
    echo '<ol>';
    foreach ($done as $done_title => $done_url) {
        if ($done_url === null) {
            echo '<li>' . $done_title . '</li>';
        } else {
            echo '<li><a href="' . escape_html($done_url) . '">' . $done_title . '</a></li>';
        }
    }
    echo '</ol>';

    if (count($addons_involved) != 0) {
        $addons_involved = array_unique($addons_involved);
        echo '<p><strong>This was for a non-bundled addon.</strong> Remember to run <a href="' . escape_html(get_base_url()) . '/adminzone/index.php?page=build_addons&amp;addon_limit=' . escape_html(urlencode(implode(',', $addons_involved))) . '">the addon update script</a>, and then upload the appropriate addon TARs and post the has-updated comments (or when the next patch release if this is what is currently preferred).</p>';
    }

    return;
}

// UI
// ==

require_code('version2');
$on_disk_version = get_version_dotted();

$git_found = git_find_uncommitted_files(get_param_integer('include_push_bugfix', 0) == 1);
$do_full_scan = (get_param_integer('full_scan', 0) == 1);
if (($do_full_scan) || (count($git_found) == 0)) {
    $files = push_bugfix_do_dir($git_found, 24 * 60 * 60);
    if (count($files) == 0) {
        $days = min(14, round(floatval(time() - filemtime(get_file_base() . '/index.php')) / (60 * 60 * 24) - 1));
        $files = push_bugfix_do_dir($git_found, 24 * 60 * 60 * $days);
    }
} else {
    $files = array_keys($git_found);
}

$git_status = 'placeholder="optional"';
$git_status_2 = ' <span style="font-size: 0.8em">(if not entered a new one will be made)</span>';
$git_status_3 = 'Git commit ID';
$choose_files_label = 'Choose files';

if ((count($git_found) == 0) && (!$do_full_scan)) {
    echo '<p><em>Found no changed files so done a full filesystem scan (rather than relying on git). You can enter a git ID or select files.</p>';
    $git_status_3 = 'Git commit ID';
    $choose_files_label = 'Choose files';
}

$post_url = escape_html(static_evaluate_tempcode(get_self_url()));

$spammer_blackhole = static_evaluate_tempcode(symbol_tempcode('INSERT_SPAMMER_BLACKHOLE'));

$categories = get_tracker_categories();
if ($categories === null) {
    warn_exit('Failed to connect to compo.sr');
}
$categories_list = '<option selected="selected"></option>';
foreach ($categories as $category_id => $category_title) {
    $categories_list .= '<option value="' . escape_html(strval($category_id)) . '">' . escape_html($category_title) . '</option>';
}

$projects = array(
    1 => 'Composr',
    2 => 'Composr alpha testing',
    8 => 'Composr build tools',
    7 => 'Composr documentation',
    5 => 'Composr downloadable themes',
    9 => 'Composr testing platform',
    10 => 'Composr website (compo.sr)',
    4 => 'Composr non-bundled addons',
);
if (in_array(cms_version_branch_status(), array(VERSION_ALPHA, VERSION_BETA))) {
    $default_project_id = 2;
} else {
    $default_project_id = 1;
}
$projects_list = '';
foreach ($projects as $project_id => $project_title) {
    $projects_list .= '<option' . (($project_id == $default_project_id) ? ' selected="selected"' : '') . ' value="' . strval($project_id) . '" >' . escape_html($project_title) . '</option>';
}

echo <<<END
<p>This script will push individual bug fixes to all the right places. Run it after you've developed a fix, and tell it how to link the fix in and what the fix is.</p>

<style>
#bugfix_form>fieldset>div>label {
    float: left;
    width: 430px;
}

#bugfix_form>fieldset>div>div>label {
    clear: both;
    float: left;
    margin-left: 430px;
}
</style>

<form action="{$post_url}" method="post" id="bugfix_form">
    {$spammer_blackhole}

    <fieldset>
        <legend>Description</legend>

        <div>
            <label for="title">Bug summary</label>
            <input size="60" required="required" name="title" id="title" type="text" value="" />
        </div>

        <div>
            <label for="notes">Notes / Description</label>
            <textarea cols="40" rows="7" required="required" name="notes" id="notes"></textarea>
        </div>

        <div>
            <label for="affects">Affects</label>
            <input size="40" name="affects" id="affects" type="text" value="" placeholder="optional" />
        </div>
    </fieldset>
END;

if (count($files) != 0) {
    echo <<<END
    <fieldset>
        <legend>Fix</legend>

        <div>
            <label for="fixed_files">{$choose_files_label}</label>
            <select size="15" required="required" multiple="multiple" name="fixed_files[]" id="fixed_files" onchange="update_automatic_category();">
END;
    foreach ($files as $file) {
        $git_dirty = isset($git_found[$file]);
        echo '<option' . ($git_dirty ? ' selected="selected"' : '') . '>' . escape_html($file) . '</option>';
    }
    $_git_found = json_encode($git_found);
    echo <<<END
            </select>
        </div>
    </fieldset>

    <script>
        function update_automatic_category()
        {
            // See if we can match all the selected files to a particular category
            var fixed_files = [];
            var fixed_files_e = document.getElementById('fixed_files');
            var file_addons = {$_git_found};
            var category_title = null;
            for (var i = 0; i < fixed_files_e.options.length; i++) {
                if (fixed_files_e.options[i].selected) {
                    fixed_files.push(fixed_files_e.options[i].value);
                }
            }
            for (var i = 0; i < fixed_files.length; i++) {
                var filename = fixed_files[i];
                if ((typeof file_addons[filename] != 'undefined') && (file_addons[filename] !== null)) {
                    if (category_title === null) {
                        category_title = file_addons[filename]; // Nice match to a bundled addon
                    } else if ((file_addons[filename] != category_title) && (!file_addons[filename].match(/^core(_.*)?$/))) {
                        category_title = 'core'; // Conflict with something other than core, so bump it back to core as a generalisation
                        break; // ... and stop trying
                    }
                }
            }
            if (category_title === null) {
                category_title = 'General'; // Must be from non-bundled addon
            }

            // Find some special general matches
            var is_all_tests = true;
            var is_all_documentation = true;
            var is_all_build_tools = true;
            for (var i = 0; i < fixed_files.length; i++) {
                var filename = fixed_files[i];
                if (!filename.match(/^_tests\//)) {
                    is_all_tests = false;
                }
                if (!filename.match(/^docs\//)) {
                    is_all_documentation = false;
                }
                if (!['sources_custom/make_release.php', 'adminzone/pages/minimodules_custom/make_release.php', 'adminzone/pages/minimodules_custom/push_bugfix.php'].includes(filename)) {
                    is_all_build_tools = false;
                }

            }
            var correct_general_project = '4';
            if (is_all_tests) {
                correct_general_project = '9';
            }
            if (is_all_documentation) {
                correct_general_project = '7';
            }
            if (is_all_build_tools) {
                correct_general_project = '8';
            }

            // Now select that category
            var category_e = document.getElementById('category');
            for (var i = 0; i < category_e.options.length; i++) {
                if (category_e.options[i].text == category_title) {
                    category_e.selectedIndex = i;
                    break;
                }
            }

            // Now select the corresponding project
            var project_e = document.getElementById('project');
            for (var i = 0; i < project_e.options.length; i++) {
                if (((project_e.options[i].value == '{$default_project_id}') && (category_title != 'General')) || ((project_e.options[i].value == correct_general_project) && (category_title == 'General'))) {
                    project_e.selectedIndex = i;
                    break;
                }
            }
        }

        add_event_listener_abstract(window,'load',function() {
            update_automatic_category();
        });

        function security_hole_radio()
        {
            var ob = document.getElementById('severity_95');
            document.getElementById('security-process').style.display = ob.checked ? 'inline' : 'none';
        }
    </script>
END;
}

echo <<<END
    <fieldset>
        <legend>Classification</legend>

        <div>
            <label for="version">Version</label>
            <input step="0.1" size="8" required="required" name="version" id="version" type="text" value="{$on_disk_version}" />
        </div>

        <div>
            <label for="project">Project</label>
            <select id="project" name="project" required="required">
                {$projects_list}
            </select>
        </div>

        <div>
            <label for="category">Category</label>
            <select id="category" name="category" required="required">
                {$categories_list}
            </select>
        </div>

        <div>
            <label>Severity</label>

            <div>
                <label for="severity_10">
                    <input type="radio" id="severity_10" name="severity" value="10" onchange="return security_hole_radio(this);" />
                    Feature-request
                </label>
                <label for="severity_20">
                    <input type="radio" id="severity_20" name="severity" value="20" onchange="return security_hole_radio(this);" checked="checked" />
                    Trivial-bug
                </label>
                <label for="severity_50">
                    <input type="radio" id="severity_50" name="severity" value="50" onchange="return security_hole_radio(this);" />
                    Minor-bug
                </label>
                <label for="severity_60">
                    <input type="radio" id="severity_60" name="severity" value="60" onchange="return security_hole_radio(this);" />
                    Major-bug
                </label>
                <label for="severity_95">
                    <input type="radio" id="severity_95" name="severity" value="95" onchange="return security_hole_radio(this);" />
                    Security-hole

                    <span style="display: none" id="security-process">
                        &ndash; Follow the <a target="_blank" title="Security policy (this link will open in a new window)" href="{$REMOTE_BASE_URL}/docs/tut-software-feedback.htm#title__46">security policy</a>.
                    </span>
                </label>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend>Post to</legend>

        <div>
            <label for="tracker_id">Tracker ID to attach to <span style="font-size: 0.8em">(if not entered a new one will be made)</span></label>
            <input name="tracker_id" id="tracker_id" size="5" type="number" value="" placeholder="optional" />

            <div style="float: right">
                <div style="display: inline-block">
                    <input name="close_issue" id="close_issue" type="checkbox" value="1" checked="checked" />
                    <label for="close_issue">Close issue?</label>
                </div>

                <div style="display: inline-block">
                    <input name="create_hotfix" id="create_hotfix" type="checkbox" value="1" checked="checked" />
                    <label for="create_hotfix">Create hotfix?</label>
                </div>
            </div>
        </div>

        <div>
            <label for="git_commit_id">{$git_status_3}{$git_status_2}</label>
            <input onchange="document.getElementById('fixed_files').required=(this.value=='');" name="git_commit_id" id="git_commit_id" type="text" value="" {$git_status} />
        </div>

        <div>
            <label for="post_id">Forum post ID to reply to</label>
            <input name="post_id" id="post_id" size="8" type="number" value="" placeholder="optional" />
        </div>
    </fieldset>
END;

echo <<<END
    <fieldset>
        <legend>Submission</legend>

        <div>
            <label for="username">Username</label>
            <input autocomplete="autocomplete" name="username" id="username" type="text" value="" />
        </div>

        <div>
            <label for="password">Password</label>
            <input autocomplete="autocomplete" required="required" name="password" id="password" type="password" value="" />
        </div>

        <div>
            <label for="submit_to_test">
                Submit to localhost test site
                <input name="submit_to" id="submit_to_test" type="radio" value="test" />
            </label>

            <label for="submit_to_live">
                Submit to live site
                <input name="submit_to" id="submit_to_live" type="radio" value="live" checked="checked" />
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend>Confirmations</legend>

        <div>
            <label for="tested">Has been tested?</label>
            <input name="tested" id="tested" type="checkbox" required="required" value="1" />
        </div>
    </fieldset>

    <p style="margin-left: 440px;">
        <input class="buttons__proceed button_screen" type="submit" value="Submit fix" />
    </p>

    <p>
        <em>Once submitted, fixes to the fix should be handled using this tool again, to submit to the tracker ID that had been auto-created the first time.</em>
    </p>
</form>
END;

// API
// ===

function git_find_uncommitted_files($include_push_bugfix)
{
    global $GIT_PATH;

    chdir(get_file_base());
    $git_command = $GIT_PATH . ' status';
    $git_result = shell_exec($git_command . ' 2>&1');
    $lines = explode("\n", $git_result);
    $git_found = array();
    foreach ($lines as $line) {
        $matches = array();
        if (preg_match('#\t(both modified|modified|new file|deleted):\s+(.*)$#', $line, $matches) != 0) {
            if (($matches[2] != 'data/files.bin') && ((basename($matches[2]) != 'push_bugfix.php') || ($include_push_bugfix))) {
                $file_addon = $GLOBALS['SITE_DB']->query_select_value_if_there('addons_files', 'addon_name', array('filename' => $matches[2]));
                if (!is_file(get_file_base() . '/sources/hooks/systems/addon_registry/' . $file_addon . '.php')) {
                    $file_addon = null;
                }
                $git_found[$matches[2]] = $file_addon;
            }
        }
    }
    if ((empty($git_found)) && (!$include_push_bugfix)) {
        return git_find_uncommitted_files(true);
    }
    return $git_found;
}

function do_git_commit($git_commit_message, $files, &$git_commit_command_data)
{
    global $GIT_PATH;

    chdir(get_file_base());

    $git_commit_command_data = '';

    // Current status
    $cmd = $GIT_PATH . ' status';
    $git_status_data = shell_exec($cmd . ' 2>&1');
    $is_unpushed_prior = (strpos($git_status_data, 'Your branch is ahead') !== false);

    // Add
    $cmd = $GIT_PATH . ' add';
    foreach ($files as $file) {
        $cmd .= ' ' . escapeshellarg($file);
    }
    $git_commit_command_data .= shell_exec($cmd . ' 2>&1');

    // Commit
    $cmd = $GIT_PATH . ' commit';
    foreach ($files as $file) {
        $cmd .= ' ' . escapeshellarg($file);
    }
    $cmd .= ' -m ' . escapeshellarg($git_commit_message);
    $git_commit_command_data .= shell_exec($cmd . ' 2>&1');

    $matches = array();
    if ((preg_match('# ([\da-z]+)\]#', $git_commit_command_data, $matches) != 0)) {
        if (!$is_unpushed_prior) {
            // Success, do a push too
            $cmd = $GIT_PATH . ' push';
            $git_commit_command_data .= shell_exec($cmd . ' 2>&1');
        }

        return $matches[1];
    }

    // Error
    $git_commit_command_data = 'Failed to make a git commit: ' . escape_html($git_commit_command_data) . '; Command was: ' . escape_html($cmd);
    return null;
}

function get_tracker_categories()
{
    $args = func_get_args();
    $result = make_call(__FUNCTION__, array('parameters' => $args));
    if (empty($result)) {
        return null;
    }
    $ret = @json_decode($result, true);
    if ($ret === false) {
        return null;
    }
    return $ret;
}

function create_tracker_issue($version_dotted, $tracker_title, $tracker_message, $tracker_additional, $tracker_severity, $tracker_category, $tracker_project)
{
    $args = func_get_args();
    $result = make_call(__FUNCTION__, array('parameters' => $args));
    if (empty($result)) {
        return null;
    }
    return intval($result);
}

function create_tracker_post($tracker_id, $tracker_comment_message, $version_dotted = null, $tracker_severity = null, $tracker_category = null, $tracker_project = null)
{
    $args = func_get_args();
    $result = make_call(__FUNCTION__, array('parameters' => $args));
    if (!is_numeric($result)) {
        return null;
    }
    return intval($result);
}

function upload_to_tracker_issue($tracker_id, $tar_path)
{
    $result = make_call('upload_to_tracker_issue', array('parameters' => array($tracker_id)), $tar_path);
    if (!is_numeric($result)) {
        return null;
    }
    return intval($result);
}

function close_tracker_issue($tracker_id)
{
    $args = func_get_args();
    $result = make_call(__FUNCTION__, array('parameters' => $args));
    return ($result === '1');
}

function create_forum_post($replying_to_post, $post_reply_title, $post_reply_message, $post_important)
{
    $args = func_get_args();
    $result = make_call(__FUNCTION__, array('parameters' => $args));
    if (!is_numeric($result)) {
        return null;
    }
    return intval($result);
}

function create_hotfix_tar($tracker_id, $files)
{
    disable_php_memory_limit();

    require_code('make_release');
    require_code('tar');

    $builds_path = get_builds_path();
    $hotfix_path = $builds_path . '/builds/hotfixes';

    if (!file_exists($hotfix_path)) {
        mkdir($hotfix_path, 0777);
        fix_permissions($hotfix_path);
    }

    $prior_hotfixes = get_directory_contents($hotfix_path, '', false, false);
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

    $files_filtered = array();
    foreach ($files as $file) {
        // Exceptions
        if (preg_match('#^docs/#', $file) != 0) {
            continue;
        }
        if (in_array($file, array(
            'sources_custom/string_scan.php',
            'data_custom/functions.bin',
        ))) {
            continue;
        }

        $files_filtered[$file] = true;
    }

    $tar_path = $hotfix_path . '/hotfix-' . strval($tracker_id) . ', ' . date('Y-m-d ga') . '.tar';
    $tar_file = tar_open($tar_path, 'wb');
    foreach (array_keys($files_filtered) as $file) {
        $file_fullpath = get_file_base() . '/' . $file;
        if (is_file($file_fullpath)) { // If it's a deletion, obviously we cannot put it into a hotfix
            tar_add_file($tar_file, $file, $file_fullpath, 0644, filemtime($file_fullpath), true);
        }
    }
    tar_close($tar_file);

    sync_file($tar_path);
    fix_permissions($tar_path);

    return $tar_path;
}

function make_call($call, $post_params, $file = null)
{
    if ($post_params !== null) {
        foreach ($post_params as $key => $param) {
            if (is_array($param)) {
                foreach ($param as $i => $val) {
                    $post_params[$key . '[' . strval($i) . ']'] = @strval($val);
                }
                unset($post_params[$key]);
            }
        }
    }

    $_username = post_param_string('username', null);
    if ($_username !== null) {
        $_password = post_param_string('password', '');
        if ($post_params === null) {
            $post_params = array();
        }
        $post_params['password'] = ($_username == '') ? $_password : ($_username . ':' . $_password);
    }

    global $REMOTE_BASE_URL;
    $call_url = $REMOTE_BASE_URL . '/data_custom/composr_homesite_web_service.php?call=' . urlencode($call);

    $files = ($file === null) ? null : array('upload' => $file);

    $result = http_download_file($call_url, null, true, false, 'Composr', $post_params, null, null, null, null, null, null, null, 6.0, false, $files);

    return $result;
}

function push_bugfix_do_dir($git_found, $seconds_since, $subdir = '')
{
    $stub = get_file_base() . '/' . (($subdir == '') ? '' : ($subdir . '/'));
    $out = array();
    $dh = opendir($stub);
    if ($dh) {
        while (($file = readdir($dh)) !== false) {
            if (!should_ignore_file((($subdir == '') ? '' : ($subdir . '/')) . $file, IGNORE_CUSTOM_DIR_GROWN_CONTENTS | IGNORE_HIDDEN_FILES | IGNORE_USER_CUSTOMISE | IGNORE_BUNDLED_VOLATILE | IGNORE_BUNDLED_UNSHIPPED_VOLATILE)) {
                $full_path = $stub . $file;
                $path = (($subdir == '') ? '' : ($subdir . '/')) . $file;

                if (is_file($full_path)) {
                    if ((filemtime($full_path) < time() - $seconds_since) && (!isset($git_found[$path]))) {
                        continue;
                    }
                    $out[] = $path;
                } elseif (is_dir($full_path)) {
                    $out = array_merge($out, push_bugfix_do_dir($git_found, $seconds_since, $path));
                }
            }
        }
    }
    sort($out);
    return $out;
}

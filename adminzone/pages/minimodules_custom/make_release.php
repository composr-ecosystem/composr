<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_release_build
 */

/*EXTRA FUNCTIONS: sha1_file*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('composr_release_build', $error_msg)) {
    return $error_msg;
}

if (!addon_installed('meta_toolkit')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('meta_toolkit')));
}

if (strpos(get_db_type(), 'mysql') === false) {
    warn_exit('The build tools require MySQL to be the active database.');
}

restrictify();
cms_ini_set('ocproducts.xss_detect', '0');

disable_php_memory_limit();

$type = get_param_string('type', '0');

$title = get_screen_title('Composr release assistance tool - step ' . strval(intval($type) + 1) . '/4', false);
$title->evaluate_echo();

switch ($type) {
    case '0':
        phase_0();
        break;
    case '1':
        phase_1();
        break;
    case '2':
        phase_2();
        break;
    case '3':
        phase_3();
        break;
}

function get_previous_version()
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

function get_new_version() {
    require_code('version2');
    $new_version = post_param_string('version', get_version_dotted());
    $new_version = get_version_dotted__from_anything($new_version);
    return $new_version;
}

// Gather version
function phase_0()
{
    require_code('version2');
    $on_disk_version = get_version_dotted();
    $previous_version = get_previous_version();

    $post_url = static_evaluate_tempcode(get_self_url(false, false, ['type' => '1']));

    echo '
    <p>Here are some things you should do if you have not already:</p>
    <ul>
        <li>Go through the auto-reported error emails, to make sure they are handled (for each: fix if relevant, delete if not).</li>
        <li>Run the <a href="' . escape_html(get_base_url() . '/_tests') . '">unit tests</a><!--, with dev mode on, on the custom Composr PHP version-->.</li>
    </ul>';

    echo '
    <form method="post" action="' . escape_html($post_url) . '">
        ' . static_evaluate_tempcode(symbol_tempcode('INSERT_FORM_POST_SECURITY')) . '

        <p>I am going to ask you some questions which will allow you to quickly make the decisions needed to get the whole release out without any additional thought. If you don\'t like these questions (such as finding them personally intrusive), I don\'t care&hellip; I am merely a machine, a device, working against a precomputed script. Now that is out of the way&hellip;</p>
        <hr />
        <fieldset>
            <legend>OLD version number</legend>
            <label for="version">The old version number was detected below from git (or blank if I could not determine it). The auto-generated changelog on the next page will contain resolved tracker issues and git commits since this version.</label>
            <br />
            <input maxlength="14" size="14" readonly="readonly" disabled="disabled" type="text" name="previous_version" id="previous_version" value="' . escape_html(($previous_version !== null) ? $previous_version : '') . '" />
        </fieldset>
        <br />
        <fieldset>
            <legend>NEW version number</legend>
            <label for="version">What is the full version number (no bloody A, B, C, or D) of the NEW version you are releasing right now? If you are only testing the make release process, you should leave this value alone (use the on-disk version).</label>
            <br />
            <input maxlength="14" size="14" type="text" name="version" id="version" value="' . escape_html($on_disk_version) . '" />
        </fieldset>
        <br />
        <fieldset>
        <legend style="display: none;">Submit</legend>
        <p><input type="submit" class="buttons__proceed button_screen" value="Proceed" /></p>
    </fieldset>
    </form>
    ';
}

// Update version.php and gather other details
function phase_1()
{
    $skip_check = (get_param_integer('skip', 0) == 1) ? 'checked="checked"' : '';

    require_code('version2');
    $new_version = get_new_version();
    $previous_version = get_previous_version();

    // Update version.php
    if (($new_version !== $previous_version)) {
        $version_file = cms_file_get_contents_safe(get_file_base() . '/sources/version.php');
        if (!$version_file) {
            fatal_exit('Failed to get sources/version.php file contents.');
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

    if (strpos($new_version, 'alpha') !== false) {
        $release_description = 'This version is an alpha release of the next major version of Composr';
    } elseif (strpos($new_version, 'beta') !== false) {
        $release_description = 'This version is a beta release of the next major version of Composr';
    } elseif (strpos($new_version, 'RC') !== false) {
        $release_description = 'This version is a release candidate for the next major version of Composr';
    } elseif (substr_count($new_version, '.') == 2) {
        $release_description = 'This version is a patch release that introduces a number of bug fixes since the last release';
    } else {
        $release_description = 'This version is the gold release of the next version of Composr';
    }

    if ($previous_version !== null) {
        $_changes = shell_exec('git log --pretty=oneline HEAD...refs/tags/' . $previous_version);
        $discovered_tracker_issues = []; // List of issues referenced on Git to pull from Mantis
        $__changes = [];
        $dig_deep = false;
        foreach (explode("\n", $_changes) as $change) {
            $parts = explode(' ', $change, 2);
            if (count($parts) == 2) {
                $change_label = $parts[1];
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

                    $regexp = '/^(Fixed MANTIS-\d+|Implementing MANTIS-\d+|Implemented MANTIS-\d+|Security fix for MANTIS-\d+|New build|Merge branch .*)/';
                    if (preg_match($regexp, $change_label) == 0) {
                        $dig_deep = true; // We want to search tracker for what this may have been
                    }
                }
            }
        }

        $api_url = get_brand_base_url() . '/data_custom/composr_homesite_web_service.php?call=get_tracker_issue_titles';
        $_discovered_tracker_issues = implode(',', array_keys($discovered_tracker_issues));
        $_result = http_get_contents($api_url, ['post_params' => ['parameters' => [$_discovered_tracker_issues, $new_version, $dig_deep ? $previous_version : null]]]);
        $tracker_issue_titles = json_decode($_result, true);

        $new_version_parts = explode('.', $new_version);
        $last = count($new_version_parts) - 1;
        $new_version_parts[$last] = strval(intval($new_version_parts[$last]) - 1);
        $new_version_previous = implode('.', $new_version_parts);

        $tracker_url = get_brand_base_url() . '/tracker/search.php?project_id=1';
        if (($new_version_parts[$last] >= 0) && (substr_count($new_version, '.') == 2)) {
            $tracker_url .= '&product_version=' . urlencode($new_version_previous);
        }

        // Start populating changes
        if (count($tracker_issue_titles) > 0) {
            $changes = 'The following [url="tracker issues"]' . $tracker_url . '[/url] have been resolved since version ' . $previous_version . "...\n";
            ksort($tracker_issue_titles); // Sort by tracker ID (usually results in oldest to newest sorting)
            foreach ($tracker_issue_titles as $key => $summary) {
                if (strpos($summary, '[[All Projects] General]') === false) { // Only ones in the main Composr project
                    $url = get_brand_base_url() . '/tracker/view.php?id=' . substr($key, 1);
                    $changes .= ' - [url="' . comcode_escape($summary) . '"]' . $url . '[/url]' . "\n";
                }
            }
            $changes .= "\n";
        }

        // Show Git-only commits
        if (count($__changes) > 0) {
            $changes .= 'The following changes were made via [url="git"]' . COMPOSR_REPOS_URL . '[/url] since version ' . $previous_version . "...\n";            $__changes = array_reverse($__changes, true); // Sort by commit time, oldest to newest
            foreach ($__changes as $git_id => $change_label) {
                // Some required substitutions
                $change_label = str_replace('<script>', 'script', $change_label);

                $url = COMPOSR_REPOS_URL . '/commit/' . $git_id;
                $changes .= ' - [url="' . comcode_escape($change_label) . '"]' . $url . '[/url]' . "\n";
            }
        }
    } else {
        $changes = 'All reported bugs since the last release have been fixed.';
    }

    $post_url = static_evaluate_tempcode(get_self_url(false, false, ['type' => '2']));
    $proceed_icon = do_template('ICON', ['_GUID' => '114667b8c304d0363000bdb3b0869471', 'NAME' => 'buttons/proceed']);

    echo '
    <form method="post" action="' . escape_html($post_url) . '">
        ' . static_evaluate_tempcode(symbol_tempcode('INSERT_FORM_POST_SECURITY')) . '

        <p>Now that I know the new and previous version, I have generated a changelog for you (because work smarter, not harder). Please review it carefully.</p>
        <hr />
        <fieldset>
            <legend>Description</legend>
            <label for="descrip">Release description.</label>
            <input type="text" size="100" name="descrip" id="descrip" value="' . escape_html($release_description) . '" />
        </fieldset>
        <br />
        <fieldset>
            <legend>Changes</legend>
            <label for="changes">For a patch release the default is usually fine (links to our hotfixes and Git history). A list of changes takes many hours to put together. Users should just stay updated regardless, and will know if there is some specific hotfix that was already made available to them. For a major release much more consideration is needed.</label>
            <textarea name="changes" id="changes" style="width: 100%" cols="40" rows="20">' . escape_html($changes) . '</textarea>
            </fieldset>
            <fieldset>
            <legend>Upgrade necessity</legend>
            <p>Upgrading is&hellip;</p>
            <input type="radio" name="needed" id="unrecommended" ' . (((strpos($release_description, 'patch release') === false) && (strpos($release_description, 'gold') === false)) ? 'checked="checked" ' : '') . 'value="not recommended" /><label for="unrecommended">&hellip;not recommended&hellip;</label><br />
            <input type="radio" name="needed" id="not-needed" ' . ((strpos($release_description, 'gold') !== false) ? 'checked="checked" ' : '') . 'value="not necessary" /><label for="not-needed">&hellip;not necessary&hellip;</label><br />
            <input type="radio" name="needed" id="suggested" value="suggested" /><label for="suggested">&hellip;suggested&hellip;</label><br />
            <input type="radio" name="needed" id="advised" ' . ((strpos($release_description, 'patch release') !== false) ? 'checked="checked" ' : '') . 'value="strongly advised" /><label for="advised">&hellip;strongly advised&hellip;</label><br />
            <label for="criteria">&hellip;for</label><input type="text" name="criteria" id="criteria" value="' . (((strpos($release_description, 'patch release') === false) && (strpos($release_description, 'gold') === false)) ? 'live sites' : '') . '" /><br />
            <label for="justification">&hellip;due to</label><input type="text" name="justification" id="justification" value="" />
        </fieldset>
        <br />
        <fieldset>
            <legend style="display: none;">Submit</legend>
            <input type="checkbox" name="skip" id="skip" value="1" ' . $skip_check . ' /><label for="skip">Installer already compiled</label>
            <input type="checkbox" name="bleeding_edge" ' . (((strpos($release_description, 'patch release') === false) && (strpos($release_description, 'gold') === false)) ? 'checked="checked" ' : '') . 'id="bleeding_edge" value="1" /><label for="bleeding_edge">Bleeding-edge release</label>
            <input type="checkbox" name="old_tree" id="old_tree" value="1" /><label for="old_tree">Older-tree maintenance release</label>
            <input type="checkbox" name="make_omni_upgrader" id="make_omni_upgrader" value="1" /><label for="make_omni_upgrader">Make omni-upgrader archive (for easy upgrader testing)</label>
            <input type="checkbox" name="rebuild_sql" id="rebuild_sql" value="1" /><label for="rebuild_sql">Re-build .sql files (if the default database structure/contents has changed)</label>
            <p><button type="submit" class="btn btn-primary btn-scr buttons--proceed">' . $proceed_icon->evaluate() . ' Shake it baby</button></p>
        </fieldset>
        <input type="hidden" name="version" value="' . escape_html($new_version) . '" />
        <input type="hidden" name="previous_version" value="' . escape_html(($previous_version !== null) ? $previous_version : '') . '" />
    </form>
    ';
}

function phase_2_pre()
{
    echo '
    <p>As this is a substantial new release make sure you have done the following (mostly testing):</p>
    <ul>
        <li>Run the <a href="' . escape_html(static_evaluate_tempcode(build_url(['page' => 'plug_guid'], get_page_zone('plug_guid')))) . '" target="_blank">plug_guid</a> tool to build needed GUIDs into the PHP.</li>
    ';
    echo '
        <li>Upgrading prep:<ul>
            <li>Copy <kbd>data/files.bin</kbd> from the most recent past release to <kbd>data/files_previous.bin</kbd> in the new release (the hosted upgrade generator does this for upgrade TARs dynamically, but we want our main release to have the correct metadata also)</li>
            <li>Make sure any ocProducts themes are up-to-date (CSS changes, template changes, theme image changes).</li>
        </ul></li>
        <li>Look for <a target="_blank" title="LEGACY comments (this link will open in a new window)" href="https://compo.sr/tracker/view.php?id=1305">LEGACY comments</a> in the code and remove/update stuff as appropriate.</li>
        <li>Consider moving Composr to a fresh repository, so you can have a clean history and a clean set of branches; update the <kbd>COMPOSR_REPOS_URL</kbd> constant if you do this</li>
        <li>Go through <a href="https://compo.sr/tracker/view.php?id=3383">advanced testing</a>.</li>
    </ul>
    ';

    $post_url = static_evaluate_tempcode(get_self_url(false, false, ['type' => '2']));

    echo '
        <form action="' . escape_html($post_url) . '" method="post">
            ' . static_evaluate_tempcode(symbol_tempcode('INSERT_FORM_POST_SECURITY')) . '

            <input type="hidden" name="intermediary_tasks" value="1" />
    ';
    foreach ($_POST as $key => $val) {
        if (is_integer($key)) {
            $key = strval($key);
        }

        echo '
            <input type="hidden" name="' . escape_html($key) . '" value="' . escape_html($val) . '" />
        ';
    }
    echo '
            <button class="btn btn-primary btn-scr buttons--yes" type="submit">Okay, I\'ve done these</button>
        </form>
    ';
}

// Build release files
function phase_2()
{
    require_code('version2');

    $new_version = get_new_version();
    $previous_version = get_previous_version();

    $is_bleeding_edge = (post_param_integer('bleeding_edge', 0) == 1);
    $is_old_tree = (post_param_integer('old_tree', 0) == 1);
    $is_substantial = is_substantial_release($new_version);

    if ((post_param_integer('intermediary_tasks', 0) == 0) && ($is_substantial) && (!$is_bleeding_edge)) {
        phase_2_pre();
        return;
    }

    require_code('make_release');

    $needed = post_param_string('needed');
    $criteria = post_param_string('criteria');
    $justification = post_param_string('justification');
    $changes = post_param_string('changes');
    $descrip = post_param_string('descrip');
    if (substr($descrip, -1) == '.') {
        $descrip = substr($descrip, 0, strlen($descrip) - 1);
    }
    $bleeding_edge = ($is_bleeding_edge ? '1' : '0');
    $old_tree = ($is_old_tree ? '1' : '0');

    if (post_param_integer('skip', 0) == 0) {
        echo make_installers(get_param_integer('keep_skip_file_grab', 0) == 1);
    }

    $post_url = static_evaluate_tempcode(get_self_url(false, false, ['type' => '3']));

    $proceed_icon = do_template('ICON', ['_GUID' => '11cce82f514c6707e2ad35926b81c6c6', 'NAME' => 'buttons/proceed']);
    echo '
        <form action="' . escape_html($post_url) . '" method="post">
            ' . static_evaluate_tempcode(symbol_tempcode('INSERT_FORM_POST_SECURITY')) . '

            <input type="hidden" name="needed" value="' . escape_html($needed) . '" />
            <input type="hidden" name="criteria" value="' . escape_html($criteria) . '" />
            <input type="hidden" name="justification" value="' . escape_html($justification) . '" />
            <input type="hidden" name="version" value="' . escape_html($new_version) . '" />
            <input type="hidden" name="previous_version" value="' . escape_html(($previous_version !== null) ? $previous_version : '') . '" />
            <input type="hidden" name="bleeding_edge" value="' . escape_html($bleeding_edge) . '" />
            <input type="hidden" name="old_tree" value="' . escape_html($old_tree) . '" />
            <input type="hidden" name="changes" value="' . escape_html($changes) . '" />
            <input type="hidden" name="descrip" value="' . escape_html($descrip) . '" />

            <button type="submit" class="btn btn-primary btn-scr buttons--proceed">' . $proceed_icon->evaluate() . ' Move on to instructions about how to release this</button>
        </form>
    ';
}

// Provide exacting instructions for making the release
function phase_3()
{
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
    $changes = post_param_string('changes');
    $descrip = post_param_string('descrip');

    require_code('version2');

    $new_version = get_version_dotted();
    $version_branch = get_version_branch();
    $version_number = float_to_raw_string(cms_version_number(), 2, true);
    $is_bleeding_edge = (post_param_integer('bleeding_edge', 0) == 1);
    $is_old_tree = (post_param_integer('old_tree', 0) == 1);
    $is_substantial = is_substantial_release($new_version);

    $push_url = get_brand_base_url() . '/adminzone/index.php?page=-make-release&version=' . urlencode($new_version) . '&is_bleeding_edge=' . ($is_bleeding_edge ? '1' : '0') . '&is_old_tree=' . ($is_old_tree ? '1' : '0') . '&descrip=' . urlencode($descrip) . '&needed=' . urlencode($needed) . '&criteria=' . urlencode($criteria) . '&justification=' . urlencode($justification);

    echo '
    <p>Here\'s a list of things for you to do. Get to it!</p>
    <ol>
    ';
    if (strpos(PHP_OS, 'Darwin') !== false) {
        $command_to_try = 'open';
    } elseif (strpos(PHP_OS, 'WIN') !== false) {
        $command_to_try = 'start';
    } else {
        $command_to_try = 'nautilus';
    }
    $command_to_try .= ' ' . get_custom_file_base() . '/exports/builds/' . $new_version . '/';
    echo '
        <li>
            <strong>Upload</strong>: Upload all built files (in <a href="#" onclick="fauxmodal_alert(\'&lt;kbd&gt;' . escape_html($command_to_try) . '&lt;/kbd&gt;\',null,\'Command to open folder\',true);"><kbd>exports/builds/' . escape_html($new_version) . '</kbd></a>) to compo.sr server (<a target="_blank" href="sftp://web1@compo.sr/composr/uploads/downloads"><kbd>uploads/downloads</kbd></a>)
        </li>
        <li>
            Tag the release with <kbd>git commit -a -m "New build"; git push; git tag ' . escape_html(str_replace(' ', '-', $new_version)) . ' ; git push origin ' . escape_html(str_replace(' ', '-', $new_version)) . '</kbd>
        </li>
        <li>
            <strong>Add to compo.sr</strong>: Run the <form target="_blank" style="display: inline" action="' . escape_html($push_url) . '" method="post">' . static_evaluate_tempcode(symbol_tempcode('INSERT_FORM_POST_SECURITY')) . '<input type="hidden" name="changes" value="' . escape_html($changes) . '" /><button class="hyperlink-button" type="submit">compo.sr setup script</button></form>. Note if you are re-releasing, this will still work &ndash; it will update existing entries appropriately.
        </li>
        <li>
            <strong>Test</strong>: Go to <a target="_blank" href="https://compo.sr/download.htm">Composr download page</a> to ensure the right packages are there and no error messages display.
        </li>
    ';

    if ((!$is_bleeding_edge) && (!$is_old_tree)) {
        echo '
            <li><strong>Installatron</strong>: Go into <a target="_blank" href="http://installatron.com/editor">Installatron</a>, login with the privileged management account, and setup a new release with the new version number (Main tab), update the URL (Version Info tab, use "Installatron installer (direct download)") and scroll down and click "Save all changes", and Publish (Publisher tab).</li>
            <li><strong>Other integrations</strong>: E-mail <a href="mailto:?bcc=punit@softaculous.com,brijesh@softaculous.com&amp;subject=New Composr release&amp;body=Hi, this is an automated notification that a new release of Composr has been released - regards, the Composr team.">integration partners</a></li>
            <li>Update <a target="_blank" href="https://en.wikipedia.org/w/index.php?title=Composr_CMS&action=edit">listing on Wikipedia</a> ("latest release version" and "latest release date")</li>
        ';
    }

    echo '
        <li><strong>Addons</strong>:<ul>
            <li>Generate the new addon set (<a target="_blank" href="' . escape_html(static_evaluate_tempcode(build_url(['page' => 'build_addons'], get_page_zone('build_addons')))) . '">build_addons minimodule</a>)</li>
    ';
    if ($is_substantial && !$is_bleeding_edge) {
        echo '
            <li>Add them (<a target="_blank" href="https://compo.sr/adminzone/publish-addons-as-downloads.htm?cat=Version%20&amp;' . escape_html(urlencode($version_number)) . '&amp;version_branch=' . escape_html(urlencode($version_branch)) . '">publish_addons_as_downloads</a> minimodule)</li>
        ';
    }
    echo '
        </ul></li>
    ';

    if ($is_substantial) {
        echo '
            <li>Create an <kbd>errors_final' . strval(intval(cms_version_number())) . '@compo.sr</kbd> e-mail account and assign someone to handle it.</li>
        ';
    }

    if ($is_substantial && !$is_bleeding_edge) {
        echo '
            <li><strong>Transifex</strong>: Import language strings into Transifex<ul>
                <li>Push new language data by calling <kbd>data_custom/transifex_push.php</kbd></li>
                <li>Update <kbd>_api_transifex</kbd> test if required</li>
            </ul></li>
        ';
    } else {
        echo '
            <li><strong>Transifex</strong> (<em>Optional</em>): Import language strings into Transifex<ul>
                <li><a target="_blank" href="' . find_script('transifex_push') . '">Push up language strings</a></li>
                <li><a target="_blank" href="' . find_script('transifex_pull') . '">Pull in translations</a></li>
            </ul></li>
        ';
    }

    echo '
        <li>Clients (<em>Optional</em>): Where applicable upgrade client/our-own sites running Composr to the new version (see <kbd>ocProducts documents/support &amp; security accounts/clients_to_upgrade.txt</kbd> and <kbd>ocProducts documents/support &amp; security accounts/clients_to_give_security_advice_to.txt</kbd>).</li>
    ';

    if ($is_substantial && !$is_bleeding_edge) {
        echo '
            <li><strong>Tracker</strong>: <a target="_blank" href="https://compo.sr/tracker/manage_proj_edit_page.php?project_id=1">Add to tracker configuration</a> (under "Versions") and also define any new addons in tracker (although an automated test should have told you already if they are missing)</li>

            <li><strong>Documentation</strong>:<ul>
                <li>Build new addon tutorial index (<a target="_blank" href="' . get_base_url() . '/adminzone/index.php?page=doc-index-build&amp;keep_devtest=1">doc_index_build minimodule</a>)</li>
                <li>Git: Commit/push</li>
                <li>Create <a target="_blank" href="https://compo.sr/adminzone/admin-zones.htm?type=add">docs' . strval(intval(cms_version_number())) . ' zone</a> (Codename "docs' . strval(intval(cms_version_number())) . '", Title "Documentation (version ' . strval(intval(cms_version_number())) . ')", Theme "ocProducts", Default page "tutorials")</li>
                <li>Do these commands in a Linux shell on the compo.sr server (before updating compo.sr for the new version!):<ul>
                    <li>Previous version docs no longer symlinked to latest docs: <kbd>rm docs' . strval(intval(cms_version_number()) - 1) . '</kbd></li>
                    <li>Archive current latest docs as the docs folder of previous version: <kbd>cp -r docs docs' . strval(intval(cms_version_number()) - 1) . '</kbd></li>
                    <li>Symlink latest docs for new version: <kbd>ln -s docs' . strval(intval(cms_version_number())) . ' docs</kbd></li>
                </ul></li>
            </ul></li>

            <li>ERD (<em>Optional</em>): Compile new ERD diagrams&hellip;<ul>
                <li>Install <a target="_blank" href="https://www.mysql.com/products/workbench/">MySQL Workbench</a></li>
                <li>Get <a target="_blank" href="' . get_base_url() . '/adminzone/index.php?page=sql-schema-generate-by-addon&amp;keep_devtest=1">exported SQL</a></li>
                <li>Extract to a directory</li>
                <li>Import into separate databases; to convert a directory listing into commands use something like <kbd>/s/(.*).sql/mysql -e "CREATE DATABASE $1" ; mysql $1 < $1.sql</kbd></li>
                <li>For each:<ul>
                    <li>"Database &rarr; Reverse Engineer" from inside MySQL Workbench</li>
                    <li>Tweak the spatial arrangement</li>
                    <li>Save as a graphic file, "File &rarr; Export &rarr; Export as PNG"</li>
                </ul></li>
                <li>Zip the graphics into <kbd>erd_rendered__by_addon.zip</kbd></li>
                <li>Put <kbd>erd_rendered__by_addon.zip</kbd> and <kbd>erd_sql__by_addon.zip</kbd> into <kbd>docs</kbd>)</li>
                <li>Get <a target="_blank" href="' . get_base_url() . '/adminzone/index.php?page=sql-show-tables-by-addon&amp;keep_devtest=1">table details</a> and update <kbd>docs/codebook_data_dictionary.docx</kbd></li>
                <li>Git: Commit/push</li>
            </ul></li>

            <li>API docs (<em>Optional</em>): Recompile the API docs&hellip;<ul>
                <li><a href="http://graphviz.org/Download..php">Install Graphviz</a></li>
                <li>Make sure you have a very high PHP memory limit in <kbd>php.ini</kbd>; 1024M is good</li>
                <li>Install PEAR if you don\'t have it already, with something like: <kbd>curl http://pear.php.net/go-pear.phar &gt; go-pear.php ; sudo php -q go-pear.php</kbd></li>
                <li>Install phpdocumentor if you don\'t have it already, with something like: <kbd>sudo pear channel-discover pear.phpdoc.org ; sudo pear install phpdoc/phpDocumentor</kbd></li>
                <li>In your phpdocumentor\'s <kbd>data/templates</kbd> directory, create a symbolic link to your Composr <kbd>docs/composr-api-template</kbd> directory (e.g. <kbd>sudo ln -s `pwd`/docs/composr-api-template /usr/share/pear/phpDocumentor/data/templates</kbd>)</li>
                <li>Build documentation with <kbd><!--rm -rf docs/api 2&lt; /dev/null ; -->phpdoc --sourcecode --force --template composr-api-template</kbd></li>
                <li>Git: Add/commit/push</li>
            </ul></li>

            <li><strong>Update compo.sr</strong>:<ul>
                <li>Do a Git pull/checkout to get to the <kbd>composr_homesite</kbd> branch</li>
                <li>Do a Git merge of the ' . STABLE_BRANCH_NAME . ' branch to update the branch</li>
                <li>Make sure the site still works, as you may have just upgraded compo.sr to a new Composr CMS version; common sense needed</li>
                <li>Git commit/push the updated branch
                <li>Close the site on the server</li>
                <li>Do a Git pull of the latest branch onto the server</li>
                <li>Make sure things are working on the server</li>
                <li>Re-open the site on the server</li>
                <li>Make sure the history on the vision page is up-to-date</li>
            </ul>

            <li><strong>Addons</strong>:<ul>
                <li>Generate the new addon set (<a target="_blank" href="https://compo.sr/adminzone/build-addons">build_addons minimodule</a>)</li>
                <li>Add them (<a target="_blank" href="https://compo.sr/adminzone/publish-addons-as-downloads.htm?cat=Version%20&amp;' . escape_html(urlencode($version_number)) . '&amp;version_branch=' . escape_html(urlencode($version_branch)) . '">publish_addons_as_downloads</a> minimodule)</li>
            </ul></li>

            <li><strong>History</strong>: Update release history details on the compo.sr <kbd>vision</kbd> page</li>

            <li><strong>Wikipedia</strong>: <form target="_blank" style="display: inline" action="https://compo.sr/forum/forumview.htm" method="post"><input type="hidden" name="title" value="Wikipedia listing needs updating (for version ' . strval(intval(cms_version_number())) . ')" /><input type="hidden" name="post" value="(This is a standard post we make each time a new major release comes out)&#10;&#10;As Composr version ' . strval(intval(cms_version_number())) . ' is out now, ideally someone will update the [url=&quot;Composr Wikipedia page&quot;]http://en.wikipedia.org/wiki/Composr_CMS[/url]. The developers don\'t maintain this because it\'d be inappropriate for us to maintain our own Wikipedia entry (neutrality reasons). The version details need updating, but generally it is worth reviewing the page is still accurate and up-to-date.&#10;&#10;Thanks to anyone who helps here, it\'s important we keep the outside world updated on Composr." /><button class="hyperlink-button" type="submit">Get someone to update our release history on Wikipedia</button></form></li>

            <li><strong>Syndication</strong>: Syndicate news to these sites (<a href="' . get_brand_base_url() . '/tracker/view.php?id=2085" target="_blank">Passwords</a>):<ul>
                <li>Add <a target="_blank" href="http://cmsreport.com/submit-story">news on CMS Report</a></li>
                <li>Add <a target="_blank" href="http://cmscritic.com/">news on CMS Critic</a> (may mean emailing the story in)</li>
                <li>Update <a target="_blank" href="http://www.cmsmatrix.org/">listing on CMS Matrix</a></li>
            </ul></li>

            <li>Newsletter (<em>Optional</em>): Send <a target="_blank" href="https://compo.sr/adminzone/admin-newsletter.htm">newsletter</a></li>

            <li><a target="_blank" href="https://compo.sr/docs/sup-professional-upgrading.htm">Upgrade users</a></li>
        ';
    }

    if ($is_substantial && $is_bleeding_edge) {
        echo '
            <li><strong>VIPs</strong>: Contact VIPs with sneak previews? (If this is needed, don\'t post as a public release&hellip;)</li>
        ';
    }

    echo '
    </ol>
    ';
}

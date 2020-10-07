<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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

$title = get_screen_title('Composr release assistance tool - step ' . strval(intval($type) + 1) . '/3', false);
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
}

// Gather details
function phase_0()
{
    $skip_check = (get_param_integer('skip', 0) == 1) ? 'checked="checked"' : '';

    require_code('version2');
    $on_disk_version = get_version_dotted();

    if (strpos($on_disk_version, 'alpha') !== false) {
        $release_description = 'This version is an alpha release of the next major version of Composr';
    } elseif (strpos($on_disk_version, 'beta') !== false) {
        $release_description = 'This version is a beta release of the next major version of Composr';
    } elseif (strpos($on_disk_version, 'RC') !== false) {
        $release_description = 'This version is a release candidate for the next major version of Composr';
    } elseif (substr_count($on_disk_version, '.') == 2) {
        $release_description = 'This version is a patch release that introduces a number of bug fixes since the last release';
    } else {
        $release_description = 'This version is the gold release of the next version of Composr';
    }

    $previous_version = null;
    $previous_tag = shell_exec('git describe --tags');
    $matches = [];
    if (preg_match('#^(.*)-\w+-\w+$#', $previous_tag, $matches) != 0) {
        $previous_version = $matches[1];
    }
    if ($previous_version !== null) {
        $changes = "The following changes have been made since version " . $previous_version . "...\n";
        $_changes = shell_exec('git log --pretty=oneline HEAD...refs/tags/' . $previous_version);
        $discovered_tracker_issues = [];
        $__changes = [];
        foreach (explode("\n", $_changes) as $change) {
            $parts = explode(' ', $change, 2);
            if (count($parts) == 2) {
                $change_label = $parts[1];
                $id = $parts[0];

                if (preg_match('#MANTIS-(\d+)#', $change_label, $matches) != 0) {
                    $id = $matches[1];
                    if (isset($discovered_tracker_issues[$id])) {
                        continue;
                    }
                    $discovered_tracker_issues[$id] = true;
                }

                $__changes[$id] = $change_label;
            }
        }

        $api_url = get_brand_base_url() . '/data_custom/composr_homesite_web_service.php?call=get_tracker_issue_titles';
        $_discovered_tracker_issues = implode(',', array_keys($discovered_tracker_issues));
        $_result = http_download_file($api_url, null, true, false, 'Composr', ['parameters' => [$_discovered_tracker_issues, $on_disk_version]]);
        $tracker_issue_titles = json_decode($_result, true);
        foreach ($tracker_issue_titles as $key => $summary) {
            if (strpos($summary, '[General]') === false) { // Only ones in the main Composr project
                $url = get_brand_base_url() . '/tracker/view.php?id=' . substr($key, 1);
                $changes .= ' - [url="' . comcode_escape($summary) . '"]' . $url . '[/url]' . "\n";
            }
        }

        foreach ($__changes as $id => $change_label) {
            if (!is_numeric($id)) {
                $url = COMPOSR_REPOS_URL . '/commit/' . $id;
                $changes .= ' - [url="' . comcode_escape($change_label) . '"]' . $url . '[/url]' . "\n";
            }
        }
    } else {
        $changes = 'All reported bugs since the last release have been fixed.';
    }

    $on_disk_version_parts = explode('.', $on_disk_version);
    $last = count($on_disk_version_parts) - 1;
    $on_disk_version_parts[$last] = strval(intval($on_disk_version_parts[$last]) - 1);
    $on_disk_version_previous = implode('.', $on_disk_version_parts);

    $tracker_url = 'https://compo.sr/tracker/search.php?project_id=1';
    if ((intval($on_disk_version_parts[$last]) >= 0) && (substr_count($on_disk_version, '.') == 2)) {
        $tracker_url .= '&product_version=' . urlencode($on_disk_version_previous);
    }

    $post_url = static_evaluate_tempcode(get_self_url(false, false, ['type' => '1']));

    echo '
    <p>Here are some things you should do if you have not already:</p>
    <ul>
        <li>Go through the auto-reported error emails, to make sure they are handled (for each: fix if relevant, delete if not).</li>
        <li>Run the <a href="' . escape_html(get_base_url() . '/_tests') . '">automated tests</a><!--, with dev mode on, on the custom Composr PHP version-->.</li>
    </ul>';


    $proceed_icon = do_template('ICON', ['_GUID' => '114667b8c304d0363000bdb3b0869471', 'NAME' => 'buttons/proceed']);
    echo '
    <form method="post" action="' . escape_html($post_url) . '">
        ' . static_evaluate_tempcode(symbol_tempcode('INSERT_SPAMMER_BLACKHOLE')) . '

        <p>I am going to ask you some questions which will allow you to quickly make the decisions needed to get the whole release out without any additional thought. If you don\'t like these questions (such as finding them personally intrusive), I don\'t care&hellip; I am merely a machine, a device, working against a precomputed script. Now that is out of the way&hellip;</p>
        <hr />
        <fieldset>
            <legend>Version number</legend>
            <label for="version">What is the full version number (no bloody A, B, C, or D)?</label>
            <input maxlength="14" size="14" readonly="readonly" type="text" name="version" id="version" value="' . escape_html($on_disk_version) . '" />
        </fieldset>
        <br />
        <fieldset>
            <legend>Description</legend>
            <label for="descrip">Release description.</label>
            <input type="text" size="100" name="descrip" id="descrip" value="' . escape_html($release_description) . '" />
        </fieldset>
        <br />
        <fieldset>
            <legend>Changes</legend>
            <label for="changes">For a patch release the default is usually fine (links to our hotfixes and Git history). A list of changes is rarely of much use and takes many hours to put together. Users should just stay updated regardless, and will know if there is some specific hotfix that was already made available to them. For a major release much more consideration is needed.</label>
            <textarea name="changes" id="changes" style="width: 100%" cols="40" rows="20">' . escape_html($changes) . '</textarea>
            </fieldset>
            <fieldset>
            <legend>Upgrade necessity</legend>
            <p>Upgrading is&hellip;</p>
            <input type="radio" name="needed" id="unrecommended" ' . (((strpos($release_description, 'patch release') === false) && (strpos($release_description, 'gold') === false)) ? 'checked="checked" ' : '') . 'value="not recommended for live sites" /><label for="unrecommended">&hellip;not recommended for live sites&hellip;</label><br />
            <input type="radio" name="needed" id="not-needed" ' . ((strpos($release_description, 'gold') !== false) ? 'checked="checked" ' : '') . 'value="not necessary" /><label for="not-needed">&hellip;not necessary&hellip;</label><br />
            <input type="radio" name="needed" id="suggested" value="suggested" /><label for="suggested">&hellip;suggested&hellip;</label><br />
            <input type="radio" name="needed" id="advised" ' . ((strpos($release_description, 'patch release') !== false) ? 'checked="checked" ' : '') . 'value="strongly advised" /><label for="advised">&hellip;strongly advised&hellip;</label><br />
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
    </form>
    ';
}

function phase_1_pre()
{
    echo '
    <p>As this is a substantial new release make sure you have done the following (mostly testing):</p>
    <ul>
        <li>Run the <a href="' . escape_html(static_evaluate_tempcode(build_url(['page' => 'plug_guid'], 'adminzone'))) . '" target="_blank">plug_guid</a> tool to build needed GUIDs into the PHP.</li>
    ';
    echo '
        <li>Upgrading prep:<ul>
            <li>Copy <kbd>data/files.dat</kbd> from the most recent past release to <kbd>data/files_previous.dat</kbd> in the new release (the hosted upgrade generator does this for upgrade TARs dynamically, but we want our main release to have the correct metadata also)</li>
            <li>Make sure any ocProducts themes are up-to-date (CSS changes, template changes, theme image changes).</li>
        </ul></li>
        <li>Look for <a target="_blank" title="LEGACY comments (this link will open in a new window)" href="https://compo.sr/tracker/view.php?id=1305">LEGACY comments</a> in the code and remove/update stuff as appropriate.</li>
        <li>Consider moving Composr to a fresh repository, so you can have a clean history and a clean set of branches; update the <kbd>COMPOSR_REPOS_URL</kbd> constant if you do this</li>
        <li>Go through <a href="https://compo.sr/tracker/view.php?id=3383">advanced testing</a>.</li>
    </ul>
    ';

    $post_url = static_evaluate_tempcode(get_self_url(false, false, ['type' => '1']));

    echo '
        <form action="' . escape_html($post_url) . '" method="post">
            ' . static_evaluate_tempcode(symbol_tempcode('INSERT_SPAMMER_BLACKHOLE')) . '

            <input type="hidden" name="intermediary_tasks" value="1" />
    ';
    foreach ($_POST as $key => $val) {
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
function phase_1()
{
    require_code('version2');

    $version_dotted = get_version_dotted();

    $is_bleeding_edge = (post_param_integer('bleeding_edge', 0) == 1);
    $is_old_tree = (post_param_integer('old_tree', 0) == 1);
    $is_substantial = is_substantial_release($version_dotted);

    if ((post_param_integer('intermediary_tasks', 0) == 0) && ($is_substantial) && (!$is_bleeding_edge)) {
        phase_1_pre();
        return;
    }

    require_code('make_release');

    $needed = post_param_string('needed');
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

    $post_url = static_evaluate_tempcode(get_self_url(false, false, ['type' => '2']));

    $proceed_icon = do_template('ICON', ['_GUID' => '11cce82f514c6707e2ad35926b81c6c6', 'NAME' => 'buttons/proceed']);
    echo '
        <form action="' . escape_html($post_url) . '" method="post">
            ' . static_evaluate_tempcode(symbol_tempcode('INSERT_SPAMMER_BLACKHOLE')) . '

            <input type="hidden" name="needed" value="' . escape_html($needed) . '" />
            <input type="hidden" name="justification" value="' . escape_html($justification) . '" />
            <input type="hidden" name="version" value="' . escape_html($version_dotted) . '" />
            <input type="hidden" name="bleeding_edge" value="' . escape_html($bleeding_edge) . '" />
            <input type="hidden" name="old_tree" value="' . escape_html($old_tree) . '" />
            <input type="hidden" name="changes" value="' . escape_html($changes) . '" />
            <input type="hidden" name="descrip" value="' . escape_html($descrip) . '" />

            <button type="submit" class="btn btn-primary btn-scr buttons--proceed">' . $proceed_icon->evaluate() . ' Move on to instructions about how to release this</button>
        </form>
    ';
}

// Provide exacting instructions for making the release
function phase_2()
{
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

    $version_dotted = get_version_dotted();
    $version_branch = get_version_branch();
    $version_number = float_to_raw_string(cms_version_number(), 2, true);
    $is_bleeding_edge = (post_param_integer('bleeding_edge', 0) == 1);
    $is_old_tree = (post_param_integer('old_tree', 0) == 1);
    $is_substantial = is_substantial_release($version_dotted);

    $push_url = get_brand_base_url() . '/adminzone/index.php?page=-make-release&version=' . urlencode($version_dotted) . '&is_bleeding_edge=' . ($is_bleeding_edge ? '1' : '0') . '&is_old_tree=' . ($is_old_tree ? '1' : '0') . '&descrip=' . urlencode($descrip) . '&needed=' . urlencode($needed) . '&justification=' . urlencode($justification);

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
    $command_to_try .= ' ' . get_custom_file_base() . '/exports/builds/' . $version_dotted . '/';
    echo '
        <li>
            <strong>Upload</strong>: Upload all built files (in <a href="#" onclick="fauxmodal_alert(\'&lt;kbd&gt;' . escape_html($command_to_try) . '&lt;/kbd&gt;\',null,\'Command to open folder\',true);"><kbd>exports/builds/' . escape_html($version_dotted) . '</kbd></a>) to compo.sr server (<a target="_blank" href="sftp://web1@compo.sr/composr/uploads/downloads"><kbd>uploads/downloads</kbd></a>)
        </li>
        <li>
            Tag the release with <kbd>git commit -a -m "New build"; git push; git tag ' . escape_html(str_replace(' ', '-', $version_dotted)) . ' ; git push origin ' . escape_html(str_replace(' ', '-', $version_dotted)) . '</kbd>
        </li>
        <li>
            <strong>Add to compo.sr</strong>: Run the <form target="_blank" style="display: inline" action="' . escape_html($push_url) . '" method="post">' . static_evaluate_tempcode(symbol_tempcode('INSERT_SPAMMER_BLACKHOLE')) . '<input type="hidden" name="changes" value="' . escape_html($changes) . '" /><button class="hyperlink-button" type="submit">compo.sr setup script</button></form>. Note if you are re-releasing, this will still work &ndash; it will update existing entries appropriately.
        </li>
        <li>
            <strong>Test</strong>: Go to <a target="_blank" href="https://compo.sr/download.htm">Composr download page</a> to ensure the right packages are there and no error messages display.
        </li>
    ';

    if ((!$is_bleeding_edge) && (!$is_old_tree)) {
        require_code('make_release');
        $builds_path = get_builds_path();
        $webpi = $builds_path . '/builds/' . $version_dotted . '/composr-' . $version_dotted . '-webpi.zip';
        $ms_filesize = number_format(filesize($webpi)) . ' bytes';
        $ms_sha1 = sha1_file($webpi);

        echo '
            <li><strong>Installatron</strong>: Go into <a target="_blank" href="http://installatron.com/editor">Installatron</a>, login with the privileged management account, and setup a new release with the new version number (Main tab), update the URL (Version Info tab, use "Installatron installer (direct download)") and scroll down and click "Save all changes", and Publish (Publisher tab).</li>
            <li><strong>Microsoft Web Platform</strong>: <a target="_blank" href="https://webgallery.microsoft.com/portal">Submit the new MS Web App Gallery file to Microsoft</a> using the privileged management account (chris@compo.sr). Change the \'Version\', the \'Release Date\', the \'Package Location URL\' (use "Microsoft installer (direct download)"), and set the shasum to <kbd>' . escape_html($ms_sha1) . '</kbd>. After submitting automatic checks will run and you have to click Publish again.</li>
            <li><strong>Other integrations</strong>: E-mail <a href="mailto:?bcc=punit@softaculous.com,brijesh@softaculous.com&amp;subject=New Composr release&amp;body=Hi, this is an automated notification that a new release of Composr has been released - regards, the Composr team.">integration partners</a></li>
            <li>Update <a target="_blank" href="https://en.wikipedia.org/w/index.php?title=Composr_CMS&action=edit">listing on Wikipedia</a> ("latest release version" and "latest release date")</li>
        ';
    }

    echo '
        <li><strong>Addons</strong>:<ul>
            <li>Generate the new addon set (<a target="_blank" href="' . escape_html(static_evaluate_tempcode(build_url(['page' => 'build_addons'], 'adminzone'))) . '">build_addons minimodule</a>)</li>
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
                <li>Add news on the <a target="_blank" href="http://members.opensourcecms.com/login.php">Open Source CMS site</a></li>
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

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    meta_toolkit
 */

/*EXTRA FUNCTIONS: diff_simple_text*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('meta_toolkit', $error_msg)) {
    return $error_msg;
}

$title = get_screen_title('Generate an upgrade', false);

$auto_probe = [];
$default_cutoff_days = intval(ceil((time() - filemtime(get_file_base() . '/sources/version.php')) / 60 / 60 / 24));
if ($default_cutoff_days <= 1) {
    $default_cutoff_days = 100;
}
$cutoff_days = post_param_integer('cutoff_days', $default_cutoff_days);

$type = get_param_string('type', 'browse');

if ($type != 'go') {
    $title->evaluate_echo();

    echo '
        <p>This system will generate a package to upgrade a site to the files in this Composr installation. You choose which addons to include (both bundled and non-bundled are supported), and the date to get changed files from (both may be auto-detected from the install location).</p>
    ';
}

$addons = get_addon_structure();

$manual_changes = [];
$manual_changes['maybe_delete'] = [];
$manual_changes['css_diff'] = [];
$manual_changes['install_diff'] = [];

disable_php_memory_limit();

if ($type == 'auto_probe') {
    $probe_dir = post_param_string('probe_dir');

    require_code('files');
    require_code('files2');
    require_code('diff');
    $path = $probe_dir . '/sources/hooks/systems/addon_registry';
    if (file_exists($path)) {
        // Via addon_registry hooks (bundled ones)
        $files = [];
        $files = array_merge($files, get_directory_contents($path, '', 0, false, true, ['php']));
        if (file_exists(str_replace('/sources/', '/sources_custom/', $path))) {
            $files = array_merge($files, get_directory_contents(str_replace('/sources/', '/sources_custom/', $path), '', 0, false, true, ['php']));
        }
        foreach ($files as $file) {
            if (substr($file, -4) == '.php') {
                $auto_probe[] = basename($file, '.php');
            }
        }

        // Via addons table (non-bundled ones)
        global $SITE_INFO;
        $backup = $SITE_INFO;
        require $probe_dir . '/_config.php';
        $linked_db = new DatabaseConnector(get_db_site(), get_db_site_host(), get_db_site_user(), get_db_site_password(), get_table_prefix());
        $auto_probe += collapse_1d_complexity('addon_name', $linked_db->query_select('addons', ['addon_name']));
        $SITE_INFO = $backup;

        // Via filesystem (non-bundled ones)
        foreach ($addons['non_bundled'] as $addon_name => $files) {
            if ($addon_name == 'simplified_emails') {
                continue; // Two common false positives
            }

            foreach ($files as $file) {
                if (file_exists($probe_dir . '/' . $file)) {
                    $auto_probe[] = $addon_name;
                }
            }
        }

        $auto_probe = array_unique($auto_probe);

        // Find oldest modified file that has been modified since
        $cutoff_days = 0;
        $files = get_directory_contents($probe_dir);
        foreach ($files as $file) {
            $time = filemtime($probe_dir . '/' . $file);
            $latest_time = @filemtime(get_file_base() . '/' . $file);
            if ($latest_time !== false) {
                if ($time != $latest_time) {
                    $old = cms_file_get_contents_safe($probe_dir . '/' . $file, FILE_READ_LOCK);
                    $new = cms_file_get_contents_safe(get_file_base() . '/' . $file, FILE_READ_LOCK);

                    if ($old != $new) {
                        if ($time < $latest_time) {
                            $days_diff = intval(ceil(($latest_time - $time) / 60 / 60 / 24));
                            if ($days_diff > $cutoff_days) {
                                $cutoff_days = $days_diff;
                            }
                        }

                        if (preg_match('#^themes/default/(css/[^/]*\.css|templates/[^/]*\.tpl)$#', $file) != 0) {
                            // Looks for theme files which may override
                            $theme_files = get_directory_contents($probe_dir . '/themes', '', IGNORE_ACCESS_CONTROLLERS, false);
                            foreach ($theme_files as $theme_file) {
                                if ($theme_file == 'map.ini' || $theme_file == 'index.html') {
                                    continue;
                                }

                                $override_file = str_replace(
                                    [
                                        'themes/default/templates/',
                                        'themes/default/javascript/',
                                        'themes/default/xml/',
                                        'themes/default/text/',
                                        'themes/default/css/',
                                    ],
                                    [
                                        'themes/' . $theme_file . '/templates_custom/',
                                        'themes/' . $theme_file . '/javascript_custom/',
                                        'themes/' . $theme_file . '/xml_custom/',
                                        'themes/' . $theme_file . '/text_custom/',
                                        'themes/' . $theme_file . '/css_custom/',
                                    ],
                                    $file
                                ) . '.editfrom';

                                if (file_exists($probe_dir . '/' . $override_file)) {
                                    $theme_file_old = cms_file_get_contents_safe($probe_dir . '/' . $override_file, FILE_READ_LOCK | FILE_READ_BOM);
                                    $theme_file_new = $new;
                                    $theme_file_old = preg_replace('#/\*.*\*/#sU', '', $theme_file_old);
                                    $theme_file_new = preg_replace('#/\*.*\*/#sU', '', $theme_file_new);
                                    if ($theme_file_new != $theme_file_old) {
                                        $manual_changes['css_diff'][basename($override_file, 'editfrom')] = diff_simple_text($theme_file_old, $theme_file_new);
                                    }
                                }
                            }
                        }

                        if (substr($file, -4) == '.php') {
                            $matches = [];
                            if (preg_match('#\n(\t*)function install(_cns)?\([^\n]*\)\n\\1\{\n(.*)\n\\1\}#sU', $old, $matches) != 0) {
                                $old_install_code = $matches[3];
                                $new_install_code = '';
                                if (preg_match('#\n(\t*)function install(_cns)?\([^\n]*\)\n\\1\{\n(.*)\n\\1\}#sU', $new, $matches) != 0) {
                                    $new_install_code = $matches[3];
                                }
                                if ($new_install_code != $old_install_code) {
                                    $manual_changes['install_diff'][$file] = diff_simple_text($old_install_code, $new_install_code);
                                }
                            }
                        }
                    }
                }
            } else {
                if (!should_ignore_file($file, IGNORE_CUSTOM_DIRS | IGNORE_UPLOADS | IGNORE_HIDDEN_FILES | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_ZONES | IGNORE_REVISION_FILES | IGNORE_EDITFROM_FILES | IGNORE_SHIPPED_VOLATILE | IGNORE_FLOATING | IGNORE_UNSHIPPED_VOLATILE)) {
                    $manual_changes['maybe_delete'][$file] = null;
                }
            }
        }

        echo '
            <h2>Advice</h2>
        ';
        $advice_parts = [
            'maybe_delete' => 'The following files might need deleting',
            'css_diff' => 'The following CSS/tpl changes have happened (diff; may need applying to overridden templates)',
            'install_diff' => 'The following install code changes have happened (diff) &ndash; isolate to <kbd>data_custom/execute_temp.php</kbd> to make an ad hoc upgrader',
        ];
        foreach ($advice_parts as $d => $message) {
            echo '
                    <p>
                            ' . $message . '&hellip;
                    </p>
            ';
            if (!empty($manual_changes[$d])) {
                echo '<ul>';
                foreach ($manual_changes[$d] as $file => $caption) {
                    echo '<li>';
                    echo '<kbd>' . escape_html($file) . '</kbd>';
                    if ($caption !== null) {
                        echo ':<br /><br />';
                        /*require_code('geshi');   If you want to see it highlighted
                        $geshi = new GeSHi($caption, 'diff');
                        $geshi->set_header_type(GESHI_HEADER_DIV);
                        echo $geshi->parse_code();*/
                        echo '<div style="overflow: auto; width: 100%; white-space: pre">' . $caption . '</div>';
                    }
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '
                            <p class="nothing-here">
                                        None
                            </p>
                ';
            }
        }

        attach_message('Settings have been auto-probed.', 'inform');
    } else {
        attach_message('This was not a Composr directory.', 'warn');
    }
}

if ($type == 'go') {
    $cutoff_point = time() - $cutoff_days * 60 * 60 * 24;

    require_code('tar');
    $generate_filename = 'upgrade-to-git--' . get_timezoned_date(time(), false) . '.tar';
    $gpath = get_custom_file_base() . '/exports/addons/' . $generate_filename;
    $tar = tar_open($gpath, 'wb');

    $probe_dir = post_param_string('probe_dir', '');

    $done = [];

    foreach ($addons['non_bundled'] + $addons['bundled'] as $addon_name => $files) {
        if (post_param_integer('addon_' . $addon_name, 0) == 1) {
            foreach ($files as $path) {
                if ($path != '_config.php') {
                    if (filemtime(get_file_base() . '/' . $path) > $cutoff_point) {
                        $old = @cms_file_get_contents_safe($probe_dir . '/' . $path, FILE_READ_LOCK | FILE_READ_BOM);
                        if ($old === false) {
                            $old = '';
                        }
                        $new = cms_file_get_contents_safe(get_file_base() . '/' . $path, FILE_READ_LOCK | FILE_READ_BOM);
                        if (($probe_dir == '') || ($old !== $new)) {
                            $new_filename = $path;
                            if (((preg_match('#^(lang)_custom/#', $path) != 0) || (strpos($old, 'CUSTOMISED FOR PROJECT') !== false)) && (($probe_dir == '') || ($old != ''))) {
                                $new_filename .= '.quarantine';
                            }
                            if (!isset($done[$new_filename])) {
                                tar_add_file($tar, $new_filename, get_file_base() . '/' . $path, fileperms(get_file_base() . '/' . $path), filemtime(get_file_base() . '/' . $path), true);
                                $done[$new_filename] = true;
                            }
                        }
                    }
                }
            }
        }
    }

    tar_close($tar);

    require_code('mime_types');
    header('Content-Type: ' . get_mime_type('tar', true));
    header('Content-Disposition: inline; filename="' . escape_header($generate_filename, true) . '"');
    cms_ob_end_clean();
    $myfile = fopen($gpath, 'rb');
    fpassthru($myfile);
    fclose($myfile);

    $GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
    exit();
}

$proceed_icon = do_template('ICON', ['_GUID' => '1ca7b77e67c6ba866ca26b77edf36ed9', 'NAME' => 'buttons/proceed']);
echo '
    <form action="' . escape_html(static_evaluate_tempcode(build_url(['page' => '_SELF', 'type' => 'auto_probe'], '_SELF'))) . '" method="post">
        ' . static_evaluate_tempcode(symbol_tempcode('INSERT_FORM_POST_SECURITY')) . '

        <h2>Auto-probe upgrade settings, and give specialised advice</h2>

        <p>
            <label for="probe_dir">
                    Directory
                    <input size="50" type="text" name="probe_dir" id="probe_dir" value="' . dirname(get_file_base()) . '/PROJECT_NAME' . '" />
            </label>
        </p>

        <p class="associated-details">
            Only run this on projects you trust - as _config.php will be executed so as to connect to the project\'s database.
        </p>

        <p class="proceed-button">
            <button class="btn btn-primary btn-scr buttons--proceed" type="submit">' . $proceed_icon->evaluate() . ' Auto-probe</button>
        </p>
    </form>
';

echo '
    <form action="' . escape_html(static_evaluate_tempcode(build_url(['page' => '_SELF', 'type' => 'go'], '_SELF'))) . '" method="post">
        ' . static_evaluate_tempcode(symbol_tempcode('INSERT_FORM_POST_SECURITY')) . '

        <h2>Manually customise upgrade settings</h2>

        <p>
            <label for="cutoff_days">
                    Files modified since (in days)
                    <input style="width: 4em" max="3000" type="number" name="cutoff_days" id="cutoff_days" value="' . strval($cutoff_days) . '" />
            </label>
        </p>
';

if (post_param_string('probe_dir', '') !== '') {
    echo '
        <input type="hidden" name="probe_dir" value="' . escape_html(post_param_string('probe_dir', '')) . '" />
    ';
}

foreach (array_merge(array_keys($addons['bundled']), array_keys($addons['non_bundled'])) as $addon_name) {
    $checked = (substr($addon_name, 0, 5) == 'core_') || ($addon_name == 'core') || (in_array($addon_name, $auto_probe));

    echo '
        <p>
            <label for="addon_' . escape_html($addon_name) . '">
                    <input ' . ($checked ? ' checked="checked"' : '') . 'type="checkbox" value="1" name="addon_' . escape_html($addon_name) . '" id="addon_' . escape_html($addon_name) . '" />
                    ' . escape_html($addon_name) . '
            </label>
        </p>
    ';
}

$proceed_icon = do_template('ICON', ['_GUID' => '0447204919c9b24e76101619f4d54441', 'NAME' => 'buttons/proceed']);
echo '
        <p class="proceed-button">
            <button class="btn btn-primary btn-scr buttons--proceed" type="submit">' . $proceed_icon->evaluate() . ' Generate</button>
        </p>
    </form>
';

function get_addon_structure()
{
    $struct = ['bundled' => [], 'non_bundled' => []];

    $hooks = find_all_hooks('systems', 'addon_registry');
    foreach ($hooks as $hook => $place) {
        require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($hook));
        $hook_ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($hook));

        $file_list = $hook_ob->get_file_list();

        if ($place == 'sources') {
            $struct['bundled'][$hook] = $file_list;
        } else {
            $struct['non_bundled'][$hook] = $file_list;
        }
    }
    ksort($struct['bundled']);
    ksort($struct['non_bundled']);

    return $struct;
}

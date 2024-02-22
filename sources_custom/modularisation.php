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

// If you edit this code (especially scan_modularisation), you may need to edit the unit test as well.

function init__modularisation()
{
    $error = new Tempcode();
    if (!addon_installed__messaged('composr_release_build', $error)) {
        warn_exit($error);
    }

    require_lang('composr_release_build');

    global $MODULARISATION_ADDON_DATA;
    if (!is_array($MODULARISATION_ADDON_DATA)) {
        $MODULARISATION_ADDON_DATA = [];
    }
}

/**
 * Run modularisation scan.
 *
 * @param  boolean $only_populate_data Whether to only populate $MODULARISATION_ADDON_DATA and cache opposed to actually checking
 * @return array Tuple; issue code, file path, array of parameters to pass to the issue code language string
 */
function scan_modularisation($only_populate_data = false) : array
{
    // Increase resource limits
    cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);
    cms_ini_set('memory_limit', '500M');

    // Array duple; issue section, file, addon name (blank: not applicable), array of parameters to pass to language string
    $problems = [];

    // Read in all addons, while checking for default icons and any double referencing within a single hook...

    $addon_data = [];
    $hooks = find_all_hook_obs('systems', 'addon_registry', 'Hook_addon_registry_');
    foreach ($hooks as $hook => $ob) {
        $files = $ob->get_file_list();
        $addon_data[$hook] = $files;

        if ($only_populate_data) {
            continue;
        }

        $counts = array_count_values($files);
        foreach ($counts as $file => $count) {
            if ($count > 1) {
                $problems[] = ['MODULARISATION_DOUBLE_REFERENCED_ADDON', $file, $hook, []];
            }
        }

        $icon_file = $ob->get_default_icon();
        if (!is_file(get_file_base() . '/' . $icon_file)) {
            $problems[] = ['MODULARISATION_DEFAULT_ICON_MISSING', $icon_file, $hook, [$hook]];
        }
    }

    if ($only_populate_data) {
        global $MODULARISATION_ADDON_DATA;
        $MODULARISATION_ADDON_DATA = $addon_data;

        require_code('caches2');
        set_cache_entry('modularisation_addon_data', 15, serialize([]), $addon_data);
        return [];
    }

    // Check for double referencing across addons, and that double referencing IS correctly done for icons...

    $seen = [];
    foreach ($addon_data as $addon_name => $d) {
        if ($addon_name == 'core_all_icons') {
            continue;
        }

        foreach ($d as $path) {
            if (((array_key_exists($path, $seen)) && (strpos($path, '_custom/') === false))) {
                $problems[] = ['MODULARISATION_DOUBLE_REFERENCED', $path, $addon_name, [$seen[$path]]];
            }
            $seen[$path] = $addon_name;

            if (preg_match('#^themes/default/images/(icons|icons_monochrome)/#', $path) != 0) {
                if (!in_array($path, $addon_data['core_all_icons'])) {
                    $problems[] = ['MODULARISATION_ICON_NOT_IN_CORE', $path, $addon_name, []];
                }

                $matches = [];
                if (preg_match('#^themes/default/images/icons/(.*)$#', $path, $matches) != 0) {
                    if (!in_array('themes/default/images/icons_monochrome/' . $matches[1], $d)) {
                        $problems[] = ['MODULARISATION_ICON_NO_MONOCHROME', $path, $addon_name, []];
                    }
                } else {
                    preg_match('#^themes/default/images/icons_monochrome/(.*)$#', $path, $matches);
                    if (!in_array('themes/default/images/icons/' . $matches[1], $d)) {
                        $problems[] = ['MODULARISATION_ICON_NO_NON_MONOCHROME', $path, $addon_name, []];
                    }
                }
            }
        }
    }

    // Check core_all_icons files also in other addons

    foreach ($addon_data['core_all_icons'] as $path) {
        if ($path == 'sources/hooks/systems/addon_registry/core_all_icons.php') {
            continue;
        }
        if (preg_match('#^themes/default/images/(icons|icons_monochrome)/spare/#', $path) != 0) {
            continue;
        }

        $ok = false;

        foreach ($addon_data as $addon_name => $d) {
            if ($addon_name == 'core_all_icons') {
                continue;
            }

            if (in_array($path, $d)) {
                $ok = true;
                break;
            }
        }

        if (!$ok) {
            $problems[] = ['MODULARISATION_CORE_ICON_NOT_IN_ADDON', $path, '', []];
        }
    }

    // Check no symlinks (breaks archive extraction on Windows)...

    foreach ($addon_data as $addon_files) {
        foreach ($addon_files as $_path) {
            if (is_link(get_file_base() . '/' . $_path)) {
                $problems[] = ['MODULARISATION_SYMLINK', $_path, '', []];
            }
        }
    }

    // Check declared packages in files against the addon they're supposed to be within, and for files not including in any addon...

    require_code('files2');
    require_code('third_party_code');
    $unput_files = []; // A map of non-existent packages to a list in them
    $ignore = IGNORE_FLOATING | IGNORE_UPLOADS | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_ZONES | IGNORE_UNSHIPPED_VOLATILE;
    $files = get_directory_contents(get_file_base(), '', $ignore);
    $forum_drivers = get_directory_contents(get_file_base() . '/sources/forum', '', 0, false, true, ['php']);
    foreach ($forum_drivers as &$forum_driver) {
        $forum_driver = basename($forum_driver, '.php');
    }

    $exceptions = [
        'themes/admin/images_custom', // If admin sprites are generated
    ];

    foreach ($files as $path) {
        // Exceptions
        if (preg_match('#^(' . implode('|', $forum_drivers) . ')/#i', $path) != 0) {
            continue;
        }
        if (preg_match('#^(' . implode('|', $exceptions) . ')/#', $path) != 0) {
            continue;
        }

        $found = false;
        foreach ($addon_data as $addon_name => $addon_files) {
            foreach ($addon_files as $fileindex => $_path) {
                if ($_path == $path) {
                    if (substr($_path, -4) == '.php') {
                        $data = cms_file_get_contents_safe(get_file_base() . '/' . $_path);
                        $check_package = _modularisation_should_check_package($data, $path);

                        if ($check_package) {
                            $matches = [];
                            $m_count = preg_match_all('#@package\s+(\w+)#', $data, $matches);
                            $problem = ($m_count != 0) && ($matches[1][0] != $addon_name) && (@$matches[1][1] != $addon_name/*FUDGE: should ideally do a loop, but we'll assume max of 2 packages for now*/);
                            if ($problem) {
                                $problems[] = ['MODULARISATION_WRONG_PACKAGE', $path, $addon_name, [$addon_name]];
                            } elseif ($m_count == 0) {
                                $problems[] = ['MODULARISATION_NO_PACKAGE', $path, $addon_name, [$addon_name]];
                            }
                        }

                        // Prepare for info() function checks on modules and blocks
                        $skip_this_file = true;
                        if ((strpos($path, '/modules/') !== false) || (strpos($path, '/modules_custom/') !== false)) {
                            $skip_this_file = false;
                        }
                        if ((strpos($path, '/minimodules/') !== false) || (strpos($path, '/minimodules_custom/') !== false)) {
                            $skip_this_file = false;
                        }
                        if ((strpos($path, 'sources/blocks/') !== false) || (strpos($path, 'sources_custom/blocks/') !== false)) {
                            $skip_this_file = false;
                        }
                        if ((strpos($path, 'hooks/modules/') !== false) || (strpos($path, 'hooks/blocks/') !== false)) {
                            $skip_this_file = true;
                        }
                        $info_exclusions_files = [
                            'adminzone/pages/minimodules_custom/installprofile_generator.php',
                            'adminzone/pages/modules/admin.php',
                        ];
                        if (!$skip_this_file) {
                            $skip_this_file = (in_array($path, $info_exclusions_files)) || ($m_count == 0);
                        }

                        if (!$skip_this_file) {
                            // Check conformity with the addon $info property
                            $_m_count2 = preg_match_all('#function info\(#', $data);
                            if ($_m_count2 != 0) {
                                $matches2 = [];
                                $m_count2 = preg_match_all('/\\$info\\[\'addon\'\\] = \'([^\']+)\'/', $data, $matches2);
                                $problem2 = ($m_count2 != 0) && (strpos($matches2[1][0], $addon_name) === false);
                                if ($problem2) {
                                    $problems[] = ['MODULARISATION_WRONG_ADDON_INFO', $path, $addon_name, [$addon_name]];
                                }
                                if ($m_count2 == 0) {
                                    $problems[] = ['MODULARISATION_NO_ADDON_INFO', $path, $addon_name, [$addon_name]];
                                }

                                // Check that a min_cms_version property is defined (required as of v11)
                                $m_count2 = preg_match_all('/\\$info\\[\'min_cms_version\'\\] = /', $data, $matches2);
                                if ($m_count2 == 0) {
                                    $problems[] = ['MODULARISATION_NO_MIN_CMS_VERSION', $path, $addon_name, []];
                                }
                            }
                        }
                    }

                    $found = true;

                    unset($addon_files[$fileindex]); // Marks it found for the "List any missing files" check
                    $addon_data[$addon_name] = $addon_files;
                    break 2;
                }
            }
        }
        if (!$found) {
            $data = cms_file_get_contents_safe(get_file_base() . '/' . $path);
            $check_package = _modularisation_should_check_package($data, $path);

            if ($check_package) {
                $matches = [];
                $m_count = preg_match('#@package\s+(\w+)#', $data, $matches);
                if ($m_count != 0) {
                    $unput_files[$matches[1]][] = $path;
                }
            }

            $problems[] = ['MODULARISATION_UNKNOWN_ADDON', $path, '', []];
        }
    }

    // List any missing files...

    foreach ($addon_data as $addon_name => $addon_files) {
        $ok = addon_installed($addon_name, false, false, false);
        if (!$ok) {
            $problems[] = ['MODULARISATION_FILES_MISSING', '', $addon_name, [$addon_name]];
        }
        foreach ($addon_files as $path) {
            if ($path == 'data_custom/execute_temp.php') {
                continue;
            }

            if (!file_exists($path)) {
                $problems[] = ['MODULARISATION_FILE_MISSING', $path, $addon_name, [$addon_name]];
            }
        }
    }

    // List (by addon) any alien files that did actually have a @package...

    ksort($unput_files);
    foreach ($unput_files as $addon_name => $paths) {
        foreach ($paths as $path) {
            $problems[] = ['MODULARISATION_ALIEN_FILE', $path, $addon_name, [$addon_name]];
        }
    }

    // Prevent the need to re-scan when we run fix_modularisation
    global $MODULARISATION_ADDON_DATA;
    $MODULARISATION_ADDON_DATA = $addon_data;

    require_code('caches2');
    set_cache_entry('modularisation_addon_data', 15, serialize([]), $addon_data);

    return $problems;
}

/**
 * Determine if an addon package was developed by the core developers.
 *
 * @param  string $data The addon data
 * @param  string $path The file path
 * @return boolean Whether it was developed by the core developers
 * @ignore
 */
function _modularisation_should_check_package(string $data, string $path) : bool
{
    if (strpos($data, 'Composr') === false) { // TODO: Make more efficient since the change from ocProducts
        return false;
    }

    return true;
}

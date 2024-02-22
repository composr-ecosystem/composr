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

// BE CAREFUL! After running all fix functions, you should call fix_modularisation_finished to finalise / save remaining changes.

function init__modularisation2()
{
    $error = new Tempcode();
    if (!addon_installed__messaged('composr_release_build', $error)) {
        warn_exit($error);
    }

    require_lang('composr_release_build');

    global $MODULARISATION_ADDON_DATA;
    if (!is_array($MODULARISATION_ADDON_DATA)) {
        cms_ini_set('memory_limit', '256M');

        require_code('caches');
        $MODULARISATION_ADDON_DATA = get_cache_entry('modularisation_addon_data', serialize([]));
        if ($MODULARISATION_ADDON_DATA === null) {
            require_code('modularisation');
            scan_modularisation(true); // This will set $MODULARISATION_ADDON_DATA
        }
    }
}

/**
 * Fix a modulerisation issue.
 *
 * @param  ID_TEXT $issue The issue codename
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode The results of execution (null: no action performed)
 */
function fix_modularisation(string $issue, string $file, string $addon, string $responsible_addon) : ?object
{
    if (!addon_installed('composr_release_build')) {
        return do_lang_tempcode('INTERNAL_ERROR');
    }

    if (function_exists('_fix_modularisation__' . $issue)) {
        $result = call_user_func_array('_fix_modularisation__' . $issue, [$file, $addon, $responsible_addon]);
        if ($result === false) {
            return do_lang_tempcode('INTERNAL_ERROR');
        }
        return $result;
    }

    return null;
}

/**
 * Finalise and save changes to addon file lists.
 *
 * @return void
 */
function fix_modularisation_finished()
{
    global $MODULARISATION_ADDON_DATA;
    foreach ($MODULARISATION_ADDON_DATA as $hook => $files) {
        sort($files);

        $addon_file = null;
        $file_text = '';
        foreach ($files as $file) {
            $file_text .= '            \'' . $file . '\',' . "\n";

            if ((($addon_file === null) || (strpos($addon_file, 'sources/') !== false)) && (strpos($file, '/hooks/systems/addon_registry/' . $hook . '.php') !== false)) {
                $addon_file = $file;
            }
        }

        if ($addon_file !== null) {
            $hook_data = cms_file_get_contents_safe(get_file_base() . '/' . $addon_file);
            if (!$hook_data) {
                warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
            }

            $pattern = '/public function get_file_list\(\) : array\s*{\s*return \[\n\s*(.*?)\s*\]\;\s*}/s';
            $replacement = "public function get_file_list() : array\n    {\n        return [\n" . $file_text . "\n        ];\n    }";
            $hook_data_updated = preg_replace($pattern, $replacement, $hook_data);

            require_code('files');
            cms_file_put_contents_safe(get_file_base() . '/' . $addon_file, $hook_data_updated, FILE_WRITE_SYNC_FILE | FILE_WRITE_FIX_PERMISSIONS);
        }
    }
}

/**
 * Fix a double-referenced file in an addon.
 *
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode Results of execution (null: internal error)
 * @ignore
 */
function _fix_modularisation__MODULARISATION_DOUBLE_REFERENCED_ADDON(string $file, string $addon, string $responsible_addon) : ?object
{
    global $MODULARISATION_ADDON_DATA;

    $found = false;
    $actually_found = false;
    foreach ($MODULARISATION_ADDON_DATA[$addon] as $i => $_file) {
        if ($file == $_file) {
            if ($found) {
                unset($MODULARISATION_ADDON_DATA[$addon][$i]);
                $actually_found = true;
            }
            $found = true;
        }
    }

    if ($actually_found) {
        return do_lang_tempcode('SUCCESS');
    }
    return do_lang_tempcode('NA');
}

/**
 * Fix a double-referenced file across multiple addons
 *
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode Results of execution (null: internal error)
 * @ignore
 */
function _fix_modularisation__MODULARISATION_DOUBLE_REFERENCED(string $file, string $addon, string $responsible_addon) : ?object
{
    global $MODULARISATION_ADDON_DATA;

    $actually_found = false;
    foreach ($MODULARISATION_ADDON_DATA as $addon => $files) {
        if ($responsible_addon == $addon) {
            continue;
        }

        foreach ($files as $i => $_file) {
            if ($file == $_file) {
                unset($MODULARISATION_ADDON_DATA[$addon][$i]);
                $actually_found = true;
            }
        }
    }

    if ($actually_found) {
        return do_lang_tempcode('SUCCESS');
    }
    return do_lang_tempcode('NA');
}

/**
 * Fix an icon not listed in core_all_icons.
 *
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode Results of execution (null: internal error)
 * @ignore
 */
function _fix_modularisation__MODULARISATION_ICON_NOT_IN_CORE(string $file, string $addon, string $responsible_addon) : ?object
{
    global $MODULARISATION_ADDON_DATA;

    if (!isset($MODULARISATION_ADDON_DATA['core_all_icons'])) {
        return null;
    }

    if (in_array($file, $MODULARISATION_ADDON_DATA['core_all_icons'])) {
        return do_lang_tempcode('NA');
    }

    $MODULARISATION_ADDON_DATA['core_all_icons'][] = $file;

    return do_lang_tempcode('SUCCESS');
}

/**
 * Fix a core_all_icons icon not listed in another addon.
 *
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode Results of execution (null: internal error)
 * @ignore
 */
function _fix_modularisation__MODULARISATION_CORE_ICON_NOT_IN_ADDON(string $file, string $addon, string $responsible_addon) : ?object
{
    // Technically the same thing as a file without an addon defined
    return _fix_modularisation__MODULARISATION_UNKNOWN_ADDON($file, $addon, $responsible_addon);
}

/**
 * Fix an incorrect package reference.
 *
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode Results of execution (null: internal error)
 * @ignore
 */
function _fix_modularisation__MODULARISATION_WRONG_PACKAGE(string $file, string $addon, string $responsible_addon) : ?object
{
    $contents = cms_file_get_contents_safe(get_file_base() . '/' . $file);
    if (!$contents) {
        return null;
    }

    $pattern = '/@package (.*?)\n/s';
    $replacement = '@package    ' . $addon . "\n";
    $contents_updated = preg_replace($pattern, $replacement, $contents);

    require_code('files');
    $success = cms_file_put_contents_safe(get_file_base() . '/' . $file, $contents_updated, FILE_WRITE_SYNC_FILE | FILE_WRITE_FIX_PERMISSIONS);
    if (!$success) {
        return null;
    }

    return do_lang_tempcode('SUCCESS');
}

/**
 * Fix wrong addon defined in info.
 *
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode Results of execution (null: internal error)
 * @ignore
 */
function _fix_modularisation__MODULARISATION_WRONG_ADDON_INFO(string $file, string $addon, string $responsible_addon) : ?object
{
    $contents = cms_file_get_contents_safe(get_file_base() . '/' . $file);
    if (!$contents) {
        return null;
    }

    $pattern = '/\$info\[\'addon\'\] = \'(.*?)\';/s';
    $replacement = '$info[\'addon\'] = \'' . $addon . '\';';
    $contents_updated = preg_replace($pattern, $replacement, $contents);

    require_code('files');
    $success = cms_file_put_contents_safe(get_file_base() . '/' . $file, $contents_updated, FILE_WRITE_SYNC_FILE | FILE_WRITE_FIX_PERMISSIONS);
    if (!$success) {
        return null;
    }

    return do_lang_tempcode('SUCCESS');
}

/**
 * Fix a file not defined in an addon.
 *
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode Results of execution (null: internal error)
 * @ignore
 */
function _fix_modularisation__MODULARISATION_UNKNOWN_ADDON(string $file, string $addon, string $responsible_addon) : ?object
{
    global $MODULARISATION_ADDON_DATA;

    if (!isset($MODULARISATION_ADDON_DATA[$responsible_addon])) {
        return null;
    }

    if (in_array($file, $MODULARISATION_ADDON_DATA[$responsible_addon])) {
        return do_lang_tempcode('NA');
    }

    $MODULARISATION_ADDON_DATA[$responsible_addon][] = $file;

    return do_lang_tempcode('SUCCESS');
}

/**
 * Fix an addon file that no longer exists.
 *
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode Results of execution (null: internal error)
 * @ignore
 */
function _fix_modularisation__MODULARISATION_FILE_MISSING(string $file, string $addon, string $responsible_addon) : ?object
{
    global $MODULARISATION_ADDON_DATA;

    if (!isset($MODULARISATION_ADDON_DATA[$addon])) {
        return null;
    }

    $index = array_search($file, $MODULARISATION_ADDON_DATA[$addon]);
    if ($index === false) {
        return do_lang_tempcode('NA');
    }

    unset($MODULARISATION_ADDON_DATA[$addon][$index]);

    return do_lang_tempcode('SUCCESS');
}

/**
 * Fix an alien file that has an addon package defined.
 *
 * @param  PATH $file The relevant file
 * @param  ID_TEXT $addon The addon to which the file belongs (blank: none)
 * @param  ID_TEXT $responsible_addon The name of the responsible addon hook, applicable for some issues
 * @return ?Tempcode Results of execution (null: internal error)
 * @ignore
 */
function _fix_modularisation__MODULARISATION_ALIEN_FILE(string $file, string $addon, string $responsible_addon) : ?object
{
    global $MODULARISATION_ADDON_DATA;

    if (!isset($MODULARISATION_ADDON_DATA[$addon])) {
        return null;
    }

    if (in_array($file, $MODULARISATION_ADDON_DATA[$addon])) {
        return do_lang_tempcode('NA');
    }

    $MODULARISATION_ADDON_DATA[$addon][] = $file;

    return do_lang_tempcode('SUCCESS');
}

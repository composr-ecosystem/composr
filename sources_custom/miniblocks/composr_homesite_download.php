<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_homesite
 */

if (!function_exists('do_release')) {
    /**
     * Get template variables for a release.
     *
     * @param  ?SHORT_TEXT $version_pretty Version we want (null: don't care)
     * @param  string $type_wanted Installer/etc type
     * @set "" "manual" "bleeding-edge" "bleeding-edge manual"
     * @param  string $prefix Prefix to put on the template params
     * @param  ?string $version_must_be_newer_than The version this must be newer than (null: no check)
     * @return ?array Map of template variables (null: could not find)
     */
    function do_release(?string $version_pretty, string $type_wanted, string $prefix, ?string $version_must_be_newer_than = null) : ?array
    {
        $latest_version_pretty = get_latest_version_pretty();
        if (($latest_version_pretty === null) && ($GLOBALS['DEV_MODE'])) {
            $latest_version_pretty = '1337';
        }

        $myrow = find_version_download_fast($version_pretty, $type_wanted, $version_must_be_newer_than);
        if ($myrow === null) {
            return $myrow;
        }

        $id = $myrow['d_id'];

        $num_downloads = $myrow['num_downloads'];

        $keep = symbol_tempcode('KEEP');
        $url = find_script('dload') . '?id=' . strval($id) . $keep->evaluate();
        if (($version_pretty == $latest_version_pretty) && ($version_must_be_newer_than === null)) {
            $url = find_script('download_composr') . '?type=' . urlencode($type_wanted) . $keep->evaluate();
        }

        require_code('version2');
        $t = $GLOBALS['DEV_MODE'] ? $myrow['name'] : get_translated_text($myrow['name']);
        $t = preg_replace('# \(.*#', '', $t);
        $version = get_version_pretty__from_dotted(get_version_dotted__from_anything($t));

        require_code('files');
        $filesize = clean_file_size($myrow['file_size']);

        if ($version_must_be_newer_than !== null) {
            if (version_compare($version_must_be_newer_than, $version) == 1) {
                return null;
            }
        }

        $ret = [];
        $ret[$prefix . 'VERSION'] = $version;
        $ret[$prefix . 'FILESIZE'] = $filesize;
        $ret[$prefix . 'NUM_DOWNLOADS'] = integer_format($num_downloads);
        $ret[$prefix . 'URL'] = $url;
        return $ret;
    }
}

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('composr_homesite')) {
    return do_template('RED_ALERT', ['_GUID' => 'o107q0s4tzjnq3djhc3e38wwfdywx1h7', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite'))]);
}

if (!addon_installed('downloads')) {
    return do_template('RED_ALERT', ['_GUID' => 'gal8hu40ptk3dlran4bwiodqrpb41jqs', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('downloads'))]);
}

require_lang('composr_homesite');
require_code('composr_homesite');
require_lang('downloads');

// Put together details about releases
$latest_version_pretty = get_latest_version_pretty();
if (($latest_version_pretty === null) && ($GLOBALS['DEV_MODE'])) {
    $latest_version_pretty = '1337';
}
$releases_tpl_map = [];
$release_quick = null;
$release_manual = null;
if ($latest_version_pretty !== null) {
    $latest = $latest_version_pretty;

    $release_quick = do_release($latest, '', 'QUICK_');
    $release_manual = do_release($latest, 'manual', 'MANUAL_');

    if ($release_quick !== null) {
        $releases_tpl_map += $release_quick;
    }
    if ($release_manual !== null) {
        $releases_tpl_map += $release_manual;
    }
}

$release_bleedingquick = do_release(null, 'bleeding-edge', 'BLEEDINGQUICK_', ($release_quick === null) ? null : $release_quick['QUICK_VERSION']);
$release_bleedingmanual = do_release(null, 'bleeding-edge, manual', 'BLEEDINGMANUAL_', ($release_manual === null) ? null : $release_manual['MANUAL_VERSION']);

if ($release_bleedingquick !== null) {
    $releases_tpl_map += $release_bleedingquick;
}
if ($release_bleedingmanual !== null) {
    $releases_tpl_map += $release_bleedingmanual;
}

if (empty($releases_tpl_map)) {
    $latest = do_lang('NA');
    $releases_tpl = paragraph(do_lang_tempcode('CMS_BETWEEN_VERSIONS'));
} else {
    $releases_tpl = do_template('CMS_DOWNLOAD_RELEASES', $releases_tpl_map);
}

return do_template('CMS_DOWNLOAD_BLOCK', ['_GUID' => '4c4952e40ed96ab52461adce9989832d', 'RELEASES' => $releases_tpl, 'VERSION' => $latest]);

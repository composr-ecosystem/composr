<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

if (!function_exists('do_release')) {
    /**
     * Get template variables for a release.
     *
     * @param  SHORT_TEXT $name_suffix A substring of the title of the download
     * @param  string $prefix Prefix to put on the template params
     * @param  ?string $version_must_be_newer_than The version this must be newer than (null: no check)
     * @return ?array Map of template variables (null: could not find)
     */
    function do_release($name_suffix, $prefix, $version_must_be_newer_than = null)
    {
        if ($GLOBALS['DEV_MODE']) {
            $t = 'Composr version 1337';

            $myrow = [
                'd_id' => 123,
                'num_downloads' => 321,
                'name' => $name_suffix,
                'file_size' => 12345,
            ];
        } else {
            $sql = 'SELECT d.num_downloads,d.name,d.file_size,d.id AS d_id,d.add_date FROM ' . get_table_prefix() . 'download_downloads d' . $GLOBALS['SITE_DB']->prefer_index('download_downloads', 'downloadauthor');
            $sql .= ' WHERE ' . db_string_equal_to('author', 'ocProducts') . ' AND validated=1 AND ' . $GLOBALS['SITE_DB']->translate_field_ref('name') . ' LIKE \'' . db_encode_like('%' . $name_suffix) . '\' ORDER BY add_date DESC';
            $rows = $GLOBALS['SITE_DB']->query($sql, 1, 0, false, false, ['name' => 'SHORT_TRANS']);
            if (!array_key_exists(0, $rows)) {
                return null; // Shouldn't happen, but let's avoid transitional errors
            }

            $myrow = $rows[0];
        }

        if ($version_must_be_newer_than !== null) {
            if (strpos($version_must_be_newer_than, '.') === false) {
                $version_must_be_newer_than .= '.0.0'; // Weird, but PHP won't do version_compare right without it
            }
        }

        $id = $myrow['d_id'];

        $num_downloads = $myrow['num_downloads'];

        $keep = symbol_tempcode('KEEP');
        $url = find_script('dload', 1) . '?id=' . strval($id) . $keep->evaluate();

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
        $ret[$prefix . 'NAME'] = $name_suffix;
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
$t = get_latest_version_pretty();
if (($t === null) && ($GLOBALS['DEV_MODE'])) {
    $t = '1337';
}
$releases_tpl_map = [];
if ($t !== null) {
    $latest = $t;
    $release_quick = do_release('Composr version ' . $latest, 'QUICK_');
    if ($release_quick !== null) {
        $releases_tpl_map += $release_quick;
    }
    $release_manual = do_release('Composr version ' . $latest . ' (manual)', 'MANUAL_');
    if ($release_manual !== null) {
        $releases_tpl_map += $release_manual;
    }
    $release_bleedingquick = do_release('(bleeding-edge)', 'BLEEDINGQUICK_', ($release_quick === null) ? null : $release_quick['QUICK_VERSION']);
    if ($release_bleedingquick !== null) {
        $releases_tpl_map += $release_bleedingquick;
    }
    $release_bleedingmanual = do_release('(bleeding-edge, manual)', 'BLEEDINGMANUAL_', ($release_manual === null) ? null : $release_manual['MANUAL_VERSION']);
    if ($release_bleedingmanual !== null) {
        $releases_tpl_map += $release_bleedingmanual;
    }
}

if (empty($releases_tpl_map)) {
    $latest = do_lang('NA');
    $releases_tpl = paragraph(do_lang_tempcode('CMS_BETWEEN_VERSIONS'));
} else {
    $releases_tpl = do_template('CMS_DOWNLOAD_RELEASES', $releases_tpl_map);
}

return do_template('CMS_DOWNLOAD_BLOCK', ['_GUID' => '4c4952e40ed96ab52461adce9989832d', 'RELEASES' => $releases_tpl, 'VERSION' => $latest]);

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!class_exists('Miniblock_cms_homesite_download')) {
    class Miniblock_cms_homesite_download
    {
        /**
         * Execute the miniblock.
         *
         * @return Tempcode The UI
         */
        public function run()
        {
            if (!addon_installed('cms_homesite')) {
                return do_template('RED_ALERT', ['_GUID' => 'fef3c13ce5045ef28155aa58d25122f4', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('cms_homesite'))]);
            }

            if (!addon_installed('downloads')) {
                return do_template('RED_ALERT', ['_GUID' => '12b0302296c4542a9b4e2a30ac5a7392', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('downloads'))]);
            }

            require_lang('cms_homesite');
            require_code('cms_homesite');
            require_lang('downloads');

            // Put together details about releases
            $latest_version_pretty_possibly_bleeding = get_latest_version_pretty(null, true);
            $latest_version_pretty = get_latest_version_pretty();
            $releases_tpl_map = [];
            $release_quick = null;
            $release_manual = null;
            $latest = null;
            if ($latest_version_pretty !== null) {
                $latest = $latest_version_pretty;

                $release_quick = $this->do_release($latest, 'quick', 'QUICK_');
                $release_manual = $this->do_release($latest, 'manual', 'MANUAL_');

                if ($release_quick !== null) {
                    $releases_tpl_map += $release_quick;
                }
                if ($release_manual !== null) {
                    $releases_tpl_map += $release_manual;
                }
            }

            $release_bleedingquick = $this->do_release($latest_version_pretty_possibly_bleeding, 'quick', 'BLEEDINGQUICK_', ($release_quick === null) ? null : $release_quick['QUICK_VERSION']);
            $release_bleedingmanual = $this->do_release($latest_version_pretty_possibly_bleeding, 'manual', 'BLEEDINGMANUAL_', ($release_manual === null) ? null : $release_manual['MANUAL_VERSION']);

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
        }

        /**
         * @param  ?SHORT_TEXT $version_pretty Version we want (null: latest)
         * @param  string $type_wanted installer type
         * @set manual quick
         * @param  string $prefix Prefix to put on the template params
         * @param  ?string $version_must_be_newer_than The version this must be newer than (null: no check)
         * @return ?array Map of template variables (null: could not find)
         */
        protected function do_release(?string $version_pretty, string $type_wanted, string $prefix, ?string $version_must_be_newer_than = null) : ?array
        {
            if (!addon_installed('cms_homesite')) {
                return null;
            }

            if (!addon_installed('downloads')) {
                return null;
            }

            $latest_version_pretty = get_latest_version_pretty();

            $myrow = find_version_download($version_pretty, $type_wanted);
            if ($myrow === null) {
                return null;
            }

            $id = $myrow['id'];

            $num_downloads = $myrow['num_downloads'];

            $keep = symbol_tempcode('KEEP');
            $url = find_script('dload') . '?id=' . strval($id) . $keep->evaluate();
            if (($version_pretty == $latest_version_pretty) && ($version_must_be_newer_than === null)) {
                $url = find_script('download_composr') . '?type=' . urlencode($type_wanted) . $keep->evaluate();
            }

            require_code('version2');
            $t = get_translated_text($myrow['name']);
            $t = str_replace([(brand_name() . ' Version '), ' (bleeding-edge)'], ['', ''], $t);
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
}

$miniblock = new Miniblock_cms_homesite_download();
$miniblock->run()->evaluate_echo();

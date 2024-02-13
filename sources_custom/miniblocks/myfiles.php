<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    member_filedumps
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('member_filedumps')) {
    return do_template('RED_ALERT', ['_GUID' => '2w60cngyu9f26bseweyg40lix71r65ug', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('member_filedumps'))]);
}

require_code('files2');

$member_id = isset($map['member_id']) ? intval($map['member_id']) : get_member();

$basedir = get_custom_file_base() . '/uploads/filedump/' . $GLOBALS['FORUM_DRIVER']->get_username($member_id);
$baseurl = get_custom_base_url() . '/uploads/filedump/' . rawurlencode($GLOBALS['FORUM_DRIVER']->get_username($member_id));

$files = file_exists($basedir) ? get_directory_contents($basedir) : [];

if (empty($files)) {
    echo '<p class="nothing-here">No files have been uploaded for you yet.</p>';
} else {
    sort($files);
    echo '<table class="wide-table columned-table results-table autosized-table">';
    echo '<thead><tr><th>Filename</th><th>Description</th><th>File size</th></tr></thead>';
    echo '<tbody>';
    foreach ($files as $file) {
        $dbrows = $GLOBALS['SITE_DB']->query_select('filedump', ['the_description', 'the_member'], ['name' => $file, 'subpath' => '/' . $GLOBALS['FORUM_DRIVER']->get_username($member_id) . '/']);
        if (!array_key_exists(0, $dbrows)) {
            $description = do_lang_tempcode('NONE_EM');
        } else {
            $description = make_string_tempcode(get_translated_text($dbrows[0]['the_description']));
        }

        require_code('files');
        echo '
            <tr>
                    <td><a target="_blank" href="' . escape_html($baseurl . '/' . $file) . '">' . escape_html($file) . '</a></td>
                    <td>' . $description->evaluate() . '</td>
                    <td>' . escape_html(clean_file_size(filesize($basedir . '/' . $file))) . '</td>
            </tr>
        ';
    }
    echo '</tbody>';
    echo '</table>';
}

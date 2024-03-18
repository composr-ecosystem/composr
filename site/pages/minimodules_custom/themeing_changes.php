<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

// (Call with &test_mode=1 to use test data)

require_code('composr_homesite');
require_code('files');
require_code('files2');
require_code('diff');

$releases = get_release_tree();

// Unzip all releases
// ==================

foreach ($releases as $version => $release) {
    $full_path = get_file_base() . '/uploads/website_specific/compo.sr/upgrades/full/' . $version;

    if (file_exists($full_path)) {
        continue;
    }

    @mkdir($full_path, 0777, true);
    if (!url_is_local($release['url'])) {
        warn_exit(escape_html('Non-local URL found (' . $release['url'] . '). Unexpected.'));
    }
    recursive_unzip(get_file_base() . '/' . rawurldecode($release['url']), $full_path);
}

// Header
// ======

$title = get_screen_title('Themeing compatibility changes', false);
$title->evaluate_echo();

echo '
<p>
    This auto-generated page shows default theme changes made between versions. You can also look at changes in
    <a href="' . escape_html(static_evaluate_tempcode(build_url(array('page' => '_SELF', 'show_all' => 1), '_SELF'))) . '">all files</a>.
</p>';

// Scope selector
// ==============

$first_version = key($releases);
$last_release = end($releases);
$last_version = key($releases);

$special_file_types = array(
    'Language files' => array('lang/' . fallback_lang(), 'ini'),
    'CSS files' => array('themes/default/css', 'css'),
    'Templates' => array('themes/default/templates', 'tpl'),
    //Excessive 'XML files' => array('', 'xml'),
    'Text templates' => array('themes/default/text', 'txt'),
    'JavaScript' => array('themes/default/javascript', 'js'),
    'Other' => array('', null),
);

// Read defaults
if (cms_srv('REQUEST_METHOD') == 'POST') { // From form
    $files_to_show = array();
    $i = 0;
    foreach ($special_file_types as $label => $_search) {
        if (isset($_POST['file_selector_' . strval($i)])) {
            $files_to_show = array_merge($files_to_show, $_POST['file_selector_' . strval($i)]);
        }
        $i++;
    }
} elseif (isset($_GET['files'])) { // By URL
    $files_to_show = explode(',', $_GET['files']);
} else { // Hard-coded defaults
    $files_to_show = array(
        'themes/default/css/global.css',
        'themes/default/css/cns.css',
        'themes/default/templates/HTML_HEAD.tpl',
        'themes/default/templates/GLOBAL_HTML_WRAP.tpl',
    );
}
if (cms_srv('REQUEST_METHOD') == 'POST') { // From form
    $versions_interested_in = isset($_POST['releases']) ? $_POST['releases'] : array();
} elseif (isset($_GET['releases'])) { // By URL
    $versions_interested_in = explode(',', $_GET['releases']);
} else { // Hard-coded defaults
    $versions_interested_in = array($first_version, $last_version);
}

require_javascript('jquery');
require_javascript('select2');
require_css('widget_select2');

echo '<div class="float_surrounder">';
echo '<form action="' . escape_html(static_evaluate_tempcode(build_url(array('page' => '_SELF', 'show_all' => get_param_integer('show_all', 0)), '_SELF'))) . '" method="post">';
echo static_evaluate_tempcode(symbol_tempcode('INSERT_SPAMMER_BLACKHOLE'));

// Output file selectors
$i = 0;
$all_files_processed = array();
foreach ($special_file_types as $label => $_search) {
    list($search_path, $search_ext) = $_search;

    $path = get_file_base() . '/uploads/website_specific/compo.sr/upgrades/full/' . $last_version;
    $deep_path = $path . (($search_path == '') ? '' : ('/' . $search_path));

    $_files = get_directory_contents($deep_path);
    $files = array();
    foreach ($_files as $f) {
        if (should_ignore_file((($search_path == '') ? '' : ($search_path . '/')) . $f, IGNORE_HIDDEN_FILES | IGNORE_ACCESS_CONTROLLERS | IGNORE_BUNDLED_VOLATILE)) {
            continue;
        }

        if (is_dir($deep_path . '/' . $f)) {
            continue;
        }

        if (($search_ext === null) || (get_file_extension($f) == $search_ext)) {
            $files[] = $f;
        }
    }
    sort($files);

    if (($i % 4 == 0) && ($i != 0)) {
        echo '<br style="clear: both" /><br />';
    }

    echo '<div style="width: 14em; float: left;' . (($i % 4 == 0) ? '' : ' margin-left: 0.5em;') . '">';
    echo '<label style="padding-bottom: 3px; display: block" for="file_selector_' . strval($i) . '">' . $label . ':</label>';
    echo '<select name="file_selector_' . strval($i) . '[]" class="file_selector" size="8" multiple="multiple" style="width: 100%">';

    foreach ($files as $file) {
        // Filter out excess
        $ext = get_file_extension($file);
        if ((in_array($ext, array('php', 'png', 'gif', 'jpg'))) && (get_param_integer('show_all', 0) == 0)) {
            continue;
        }
        if (preg_match('#^(data/ckeditor/|data/editarea/|data/sounds/|data/fonts/)#', $file) != 0) {
            continue;
        }

        $fpath = (($search_path == '') ? '' : ($search_path . '/')) . $file;

        // Don't duplicate it across sections
        if (isset($all_files_processed[$fpath])) {
            continue;
        }
        $all_files_processed[$fpath] = true;

        // List option
        if ($search_ext === null) {
            $_file = $file;
        } else {
            $_file = basename($file, '.' . $search_ext);
        }
        $selected = in_array($fpath, $files_to_show);
        echo '<option' . ($selected ? ' selected="selected"' : '') . ' value="' . escape_html($fpath) . '">' . escape_html($_file) . '</option>';
    }

    echo '</select>';
    echo '</div>';

    $i++;
}

// Output release selector
echo '<div style="width: 14em; float: left;' . (($i % 4 == 0) ? '' : ' margin-left: 0.5em;') . '">';
echo '<label style="padding-bottom: 3px; display: block" for="releases">';
echo '<abbr title="The selected releases will be compared. You can select as many as you want and diffs will be shown across each jump.">Compare points</abbr>:';
echo '</label>';
echo '<select name="releases[]" class="file_selector" size="8" multiple="multiple" style="width: 100%">';
foreach (array_reverse($releases) as $version => $release_details) {
    $selected = in_array($version, $versions_interested_in);

    echo '<option value="' . escape_html($version) . '"' . ($selected ? ' selected="selected"' : '') . '>' . escape_html($version . ' (' . get_timezoned_date($release_details['add_date'], false) . ')') . '</option>';
}
echo '</select>';
echo '</div>';

echo '<input style="margin-left: 8px; margin-top: 15px" type="submit" value="Filter" class="button_screen buttons__proceed" />';
echo '</form>';
echo '</div>';
echo '
<script>
    if (typeof $(\'.file_selector\').select2!=\'undefined\')
    {
        $(\'.file_selector\').select2({
            dropdownAutoWidth: true
        });
    }
</script>
';

// Diff display
// ============

if (count($versions_interested_in) < 2) {
    echo '<p class="nothing_here">You must select at least two versions.</p>';
    return;
}

$out = '';

// Do diffs between all releases
$path_old = null;
$version_old = null;
foreach (array_keys($releases) as $version) {
    if (!in_array($version, $versions_interested_in)) {
        continue;
    }

    $path_new = get_file_base() . '/uploads/website_specific/compo.sr/upgrades/full/' . $version;
    $version_new = $version;

    if ($path_old !== null) {
        $out_for_jump = '';

        $files = get_directory_contents($path_new);
        sort($files);

        foreach ($files as $f) {
            if ((in_array($f, $files_to_show)) && (is_file($path_old . '/' . $f))) {
                $contents_old = file_get_contents($path_old . '/' . $f);
                $contents_new = file_get_contents($path_new . '/' . $f);
                if ($contents_old != $contents_new) {
                    if (preg_match('#([\x00-\x08\x0B-\x0C\x0E-\x1F])#', $contents_old) != 0) {
                        $diff = null;
                    } else {
                        $diff = diff_simple_text($contents_old, $contents_new, true);
                    }
                } else {
                    $diff = '';
                }
                if ($diff === null) {
                    $out_for_jump .= '<h3>' . escape_html($f) . '</h3>';
                    $out_for_jump .= '<p style="color: red">This binary file differs.</p>';
                } elseif (trim($diff) != '' && trim($diff) != '<br />') {
                    $out_for_jump .= '<h3>' . escape_html($f) . '</h3>';
                    $out_for_jump .= '<div style="overflow: auto;">';
                    $out_for_jump .= '<code style="white-space: pre">';
                    $out_for_jump .= $diff;
                    $out_for_jump .= '</code>';
                    $out_for_jump .= '</div>';
                } elseif (count($files_to_show) == 1) {
                    $out_for_jump .= '<h3>' . escape_html($f) . '</h3>';
                    $out_for_jump .= '<p class="nothing_here">No changes</p>';
                }
            }
        }

        if ($out_for_jump != '') {
            $out_for_jump = '<h2>' . escape_html($version_old) . ' &rarr; ' . escape_html($version_new) . '</h2>' . $out_for_jump;

            $out = $out_for_jump . $out;
        }
    }

    $path_old = $path_new;
    $version_old = $version_new;
}

if ($out == '') {
    $out = '<p class="nothing_here">Nothing to show</p>';
}

echo $out;

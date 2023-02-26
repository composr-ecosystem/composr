<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('composr_tutorials', $error_msg)) {
    return $error_msg;
}

if (!addon_installed('composr_homesite')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite')));
}
if (!addon_installed('composr_homesite_support_credits')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_homesite_support_credits')));
}
if (!addon_installed('composr_release_build')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_release_build')));
}

if (post_param_integer('confirm', 0) == 0) {
    $preview = 'Make addon tutorial index';
    $title = get_screen_title($preview, false);
    $url = get_self_url(false, false);
    return do_template('CONFIRM_SCREEN', ['_GUID' => '15320e95a4b92c021e7e9853c1585f7d', 'TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => form_input_hidden('confirm', '1'), 'URL' => $url]);
}

// Prepare for synonyms
require_code('lang_stemmer_' . user_lang());
$stemmer = object_factory('Stemmer_' . user_lang());
require_code('adminzone/pages/modules/admin.php');
$admin = object_factory('Module_admin');
$synonyms = $admin->_synonyms();

// Find details about addons
require_code('addons');
$addons = [];
$all_tutorials_referenced = [];
$_addons = find_all_hooks('systems', 'addon_registry');
ksort($_addons);
foreach ($_addons as $addon_name => $place) {
    if ($place == 'sources') {
        require_code('hooks/systems/addon_registry/' . filter_naughty_harsh($addon_name));
        $ob = object_factory('Hook_addon_registry_' . filter_naughty_harsh($addon_name));

        $tutorials = $ob->get_applicable_tutorials();
        $all_tutorials_referenced = array_merge($all_tutorials_referenced, $tutorials);
        if (empty($tutorials)) {
            warn_exit('No tutorial defined for addon: ' . $addon_name);
        }

        $dependencies = $ob->get_dependencies();

        $pretty = titleify($addon_name);

        $stemmed_addon = cms_strtolower_ascii($stemmer->stem($pretty));
        $_synonyms = [];
        foreach ($synonyms as $ss) {
            if (in_array($ss[0], ['export', 'permission'])) {
                continue;
            }

            $_ss = array_map([$stemmer, 'stem'], $ss);
            if (in_array($stemmed_addon, $_ss)) {
                $_synonyms = array_merge($_synonyms, $ss);
                $test = array_search($stemmed_addon, $_synonyms);
                if ($test !== false) {
                    unset($_synonyms[$test]);
                }
                $test = array_search($addon_name, $_synonyms);
                if ($test !== false) {
                    unset($_synonyms[$test]);
                }
            }
        }

        $addons[$addon_name] = [
            'pretty' => $pretty,
            'icon' => find_addon_icon($addon_name, false),
            'description' => $ob->get_description(),
            'core' => (substr($addon_name, 0, 4) == 'core'),
            'dependencies' => $dependencies['requires'],
            'tutorials' => $tutorials,
            'synonyms' => $_synonyms,
            'tracker_url' => 'https://compo.sr/tracker/search.php?project_id=1&category=' . urlencode($addon_name) . '&status_id=10',
        ];
    }
}

// Find unreferenced tutorials
$tutorials = [];
$unreferenced_tutorials = [];
$dh = opendir(get_file_base() . '/docs/pages/comcode_custom/EN');
while (($f = readdir($dh)) !== false) {
    if (substr($f, -4) == '.txt') {
        $tutorial = basename($f, '.txt');
        if ((!in_array($tutorial, $all_tutorials_referenced)) && (substr($tutorial, 0, 4) == 'tut_') && ($tutorial != 'tut_addon_index')) {
            $unreferenced_tutorials[] = $tutorial;
        }
    }
}
closedir($dh);

// Output it all...

$out = '
<table class="wide-table columned-table results-table autosized-table">
    <thead>
        <tr>
            <th>Addon</th>
            <th>Description</th>
            <th>Tutorials</th>
            <th>Synonyms</th>
            <th>Feature suggestions</th>
        </tr>
    </thead>
';

foreach ($addons as $addon_name => $addon_info) {
    $tutorials = '';
    foreach ($addon_info['tutorials'] as $tutorial) {
        if ($tutorials != '') {
            $tutorials .= '<br /><br />';
        }

        $tutorial_title = get_tutorial_title($tutorial);
        $tutorials .= '<a href="' . escape_html(get_tutorial_url($tutorial)) . '">' . escape_html($tutorial_title) . '</a>';
    }

    if ($addon_info['icon'] == '') {
        $icon = '';
    } else {
        $icon = '<img class="right" src="' . escape_html($addon_info['icon']) . '" />';
    }

    $out .= '
        <tr>
            <td>
                ' . $icon . escape_html($addon_info['pretty']) . '<br />(<kbd>' . escape_html($addon_name) . '</kbd>)<br /><br />
                <strong>Core</strong>: ' . escape_html($addon_info['core'] ? 'Yes' : 'No') . '<br /><br />
                <strong>Dependencies</strong>: ' . (empty($addon_info['dependencies']) ? '<em>None</em>' : ('<kbd>' . implode('</kbd>, <kbd>', array_map('escape_html', $addon_info['dependencies'])) . '</kbd>')) . '
            </td>
            <td>' . escape_html($addon_info['description']) . '</td>
            <td>' . $tutorials . '</td>
            <td>' . implode('<br /><br />', array_map('escape_html', $addon_info['synonyms'])) . '</td>
            <td><a href="' . escape_html($addon_info['tracker_url']) . '">Link</a></td>
        </tr>
    ';
}

$out .= '
</table>
';

$out .= '[title="2"]Other tutorials[/title]<p>The following tutorials do not link into any specific addon:</p><ul>';
foreach ($unreferenced_tutorials as $tutorial) {
    $out .= '<li>';
    $tutorial_title = get_tutorial_title($tutorial);
    $out .= '<a href="' . escape_html(get_tutorial_url($tutorial)) . '">' . escape_html($tutorial_title) . '</a>';
    $out .= '</li>';
}
$out .= '</ul>';

// Write out
$path = get_file_base() . '/docs/pages/comcode_custom/EN/tut_addon_index.txt';
$addon_index_file = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_BOM);
$marker = '[staff_note]Automatic code inserts after this[/staff_note]';
$pos = strpos($addon_index_file, $marker);
$addon_index_file = substr($addon_index_file, 0, $pos + strlen($marker)) . '[semihtml]' . str_replace(get_custom_base_url(), get_brand_base_url(), $out) . '[/semihtml]';
$addon_index_file .= "\n\n" . '{$SET,tutorial_tags,Addon,Introduction,novice}{$SET,tutorial_add_date,Oct 2013}{$SET,tutorial_summary,An index showing what addons are available, and linking to relevant tutorials.}[block]main_tutorial_rating[/block]' . "\n";
require_code('files');
cms_file_put_contents_safe($path, $addon_index_file, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE | FILE_WRITE_BOM);

echo static_evaluate_tempcode(comcode_to_tempcode($addon_index_file));

function get_tutorial_title($tutorial)
{
    $contents = cms_file_get_contents_safe(get_file_base() . '/docs/pages/comcode_custom/EN/' . $tutorial . '.txt', FILE_READ_LOCK | FILE_READ_BOM);
    $matches = [];
    preg_match('#\[title[^\[\]]*\](?-U)(Composr (Tutorial|Supplementary): )?(?U)(.*)\[/title\]#Us', $contents, $matches);
    return $matches[3];
}

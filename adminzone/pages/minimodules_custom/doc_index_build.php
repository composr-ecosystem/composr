<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

// Prepare for synonyms
require_code('lang_stemmer_' . user_lang());
$stemmer = object_factory('Stemmer_' . user_lang());
require_code('adminzone/pages/modules/admin.php');
$admin = object_factory('Module_admin');
$synonyms = $admin->_synonyms();

// Find details about addons
require_code('addons');
$addons = array();
$all_tutorials_referenced = array();
$_addons = find_all_hooks('systems', 'addon_registry');
ksort($_addons);
foreach ($_addons as $addon => $place) {
    if ($place == 'sources') {
        require_code('hooks/systems/addon_registry/' . $addon);
        $ob = object_factory('Hook_addon_registry_' . $addon);

        $tutorials = $ob->get_applicable_tutorials();
        $all_tutorials_referenced = array_merge($all_tutorials_referenced, $tutorials);
        if (count($tutorials) == 0) {
            warn_exit('No tutorial defined for addon: ' . $addon);
        }

        $dependencies = $ob->get_dependencies();

        $pretty = titleify($addon);

        $stemmed_addon = strtolower($stemmer->stem($pretty));
        $_synonyms = array();
        foreach ($synonyms as $ss) {
            if (in_array($ss[0], array('export', 'permission'))) {
                continue;
            }

            $_ss = array_map(array($stemmer, 'stem'), $ss);
            if (in_array($stemmed_addon, $_ss)) {
                $_synonyms = array_merge($_synonyms, $ss);
                $test = array_search($stemmed_addon, $_synonyms);
                if ($test !== false) {
                    unset($_synonyms[$test]);
                }
                $test = array_search($addon, $_synonyms);
                if ($test !== false) {
                    unset($_synonyms[$test]);
                }
            }
        }

        $addons[$addon] = array(
            'pretty' => $pretty,
            'icon' => find_addon_icon($addon, false),
            'description' => $ob->get_description(),
            'core' => (substr($addon, 0, 4) == 'core'),
            'dependencies' => $dependencies['requires'],
            'tutorials' => $tutorials,
            'synonyms' => $_synonyms,
            'tracker_url' => 'http://compo.sr/tracker/search.php?project_id=1&category=' . urlencode($addon) . '&status_id=10',
        );
    }
}

// Find unreferenced tutorials
$tutorials = array();
$unreferenced_tutorials = array();
$dh = opendir(get_file_base() . '/docs/pages/comcode_custom/EN');
while (($f = readdir($dh)) !== false) {
    if (substr($f, -4) == '.txt') {
        $tutorial = basename($f, '.txt');
        if ((!in_array($tutorial, $all_tutorials_referenced)) && (substr($tutorial, 0, 4) == 'tut_') && ($tutorial != 'tut_addon_index')) {
            $unreferenced_tutorials[] = $tutorial;
        }
    }
}

// Output it all...

$out = '
<div class="wide_table_wrap"><table class="wide_table columned_table results_table autosized_table">
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

foreach ($addons as $addon => $addon_details) {
    $tutorials = '';
    foreach ($addon_details['tutorials'] as $tutorial) {
        if ($tutorials != '') {
            $tutorials .= '<br /><br />';
        }

        $tutorial_title = get_tutorial_title($tutorial);
        $tutorials .= '<a href="' . escape_html(get_tutorial_url($tutorial)) . '">' . escape_html($tutorial_title) . '</a>';
    }

    if ($addon_details['icon'] == '') {
        $icon = '';
    } else {
        $icon = '<img class="right" src="' . escape_html($addon_details['icon']) . '" />';
    }

    $out .= '
        <tr>
            <td>
                ' . $icon . escape_html($addon_details['pretty']) . '<br />(<kbd>' . escape_html($addon) . '</kbd>)<br /><br />
                <strong>Core</strong>: ' . escape_html($addon_details['core'] ? 'Yes' : 'No') . '<br /><br />
                <strong>Dependencies</strong>: ' . (($addon_details['dependencies'] == array()) ? '<em>None</em>' : '<kbd>' . implode('</kbd>, <kbd>', array_map('escape_html', $addon_details['dependencies'])) . '</kbd>') . '
            </td>
            <td>' . escape_html($addon_details['description']) . '</td>
            <td>' . $tutorials . '</td>
            <td>' . implode('<br /><br />', array_map('escape_html', $addon_details['synonyms'])) . '</td>
            <td><a href="' . escape_html($addon_details['tracker_url']) . '">Link</a></td>
        </tr>
    ';
}

$out .= '
</table></div>
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
$path = get_custom_file_base() . '/docs/pages/comcode_custom/EN/tut_addon_index.txt';
$addon_index_file = file_get_contents($path);
$marker = '[staff_note]Automatic code inserts after this[/staff_note]';
$pos = strpos($addon_index_file, $marker);
$addon_index_file = substr($addon_index_file, 0, $pos + strlen($marker)) . '[semihtml]' . str_replace(get_custom_base_url(), get_brand_base_url(), $out) . '[/semihtml]';
$addon_index_file .= "\n\n" . '{$SET,tutorial_tags,Addon,Introduction,novice}{$SET,tutorial_add_date,Oct 2013}{$SET,tutorial_summary,An index showing what addons are available, and linking to relevant tutorials.}[block]main_tutorial_rating[/block]';
require_code('files');
cms_file_put_contents_safe($path, $addon_index_file, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);

echo static_evaluate_tempcode(comcode_to_tempcode($addon_index_file));

function get_tutorial_title($tutorial)
{
    $contents = file_get_contents(get_custom_file_base() . '/docs/pages/comcode_custom/EN/' . $tutorial . '.txt');
    $matches = array();
    preg_match('#\[title[^\[\]]*\](?-U)(Composr (Tutorial|Supplementary): )?(?U)(.*)\[/title\]#Us', $contents, $matches);
    return $matches[3];
}

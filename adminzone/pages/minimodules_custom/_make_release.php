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

/*EXTRA FUNCTIONS: shell_exec*/

/* To be called by make_release.php - not directly linked from menus */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

if (!addon_installed('downloads')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('downloads')));
}
if (!addon_installed('news')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('news')));
}

$error_msg = new Tempcode();
if (!addon_installed__messaged('composr_homesite', $error_msg)) {
    return $error_msg;
}

$title = get_screen_title('Publish new Composr release', false);
$title->evaluate_echo();

set_mass_import_mode(); // We will be adding multiple categories of the same name

restrictify();
require_code('permissions2');
require_code('composr_homesite');

// Version info / plan

$version_dotted = get_param_string('version');
require_code('version2');
$version_pretty = get_version_pretty__from_dotted(get_version_dotted__from_anything($version_dotted));

$is_substantial = is_substantial_release($version_dotted);

$is_old_tree = get_param_integer('is_old_tree') == 1;

$is_bleeding_edge = get_param_integer('is_bleeding_edge') == 1;
if (!$is_bleeding_edge) {
    $bleeding1 = '';
    $bleeding2 = '';
} else {
    $bleeding1 = ' (bleeding-edge)';
    $bleeding2 = 'bleeding-edge, ';
}

$changes = post_param_string('changes', '');

$descrip = get_param_string('descrip', '', INPUT_FILTER_GET_COMPLEX);

$needed = get_param_string('needed', '', INPUT_FILTER_GET_COMPLEX);
$criteria = get_param_string('criteria', '', INPUT_FILTER_GET_COMPLEX);
$justification = get_param_string('justification', '', INPUT_FILTER_GET_COMPLEX);

$urls = [];

// Bugs list

if (!$is_bleeding_edge) {
    $urls['Bugs'] = 'https://compo.sr/tracker/search.php?project_id=1&product_version=' . urlencode($version_dotted);
}

// Add downloads (assume uploaded already)

require_code('downloads2');
$releases_category_id = $GLOBALS['SITE_DB']->query_select_value('download_categories', 'id', ['parent_id' => db_get_first_id(), $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Composr Releases']);
// ^ Result must return, composr_homesite_install.php added the category

$release_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Version ' . strval(intval($version_dotted))]);
if ($release_category_id === null) {
    $release_category_id = add_download_category('Version ' . strval(intval($version_dotted)), $releases_category_id, '', '');
    set_global_category_access('downloads', $release_category_id);
    set_privilege_access('downloads', $release_category_id, 'submit_midrange_content', 0);
}
// NB: We don't add addon categories. This is done in publish_addons_as_downloads.php

$installatron_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Installatron integration']);
if ($installatron_category_id === null) {
    $installatron_category_id = add_download_category('Installatron integration', $releases_category_id, '', '');
    set_global_category_access('downloads', $installatron_category_id);
    set_privilege_access('downloads', $installatron_category_id, 'submit_midrange_content', 0);
}

$microsoft_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Microsoft integration']);
if ($microsoft_category_id === null) {
    $microsoft_category_id = add_download_category('Microsoft integration', $releases_category_id, '', '');
    set_global_category_access('downloads', $microsoft_category_id);
    set_privilege_access('downloads', $microsoft_category_id, 'submit_midrange_content', 0);
}

$aps_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'APS integration']);
if ($aps_category_id === null) {
    $aps_category_id = add_download_category('APS integration', $releases_category_id, '', '');
    set_global_category_access('downloads', $aps_category_id);
    set_privilege_access('downloads', $aps_category_id, 'submit_midrange_content', 0);
}

$all_downloads_to_add = [
    [
        'name' => "Composr Version {$version_pretty}{$bleeding1}",
        'description' => "This is version {$version_pretty}.",
        'filename' => 'composr_quick_installer-' . $version_dotted . '.zip',
        'additional_details' => ($is_bleeding_edge || $is_old_tree) ? '' : 'This is the latest version.',
        'category_id' => $release_category_id,
        'internal_name' => 'Quick installer',
    ],

    [
        'name' => "Composr Version {$version_pretty} ({$bleeding2}manual)",
        'description' => "Manual installer (as opposed to the regular quick installer). Please note this isn't documentation.",
        'filename' => 'composr_manualextraction_installer-' . $version_dotted . '.zip',
        'additional_details' => '',
        'category_id' => $release_category_id,
        'internal_name' => 'Manual installer',
    ],

    [
        'name' => "Composr {$version_pretty}",
        'description' => "This archive is designed for webhosting control panels that integrate Composr. It contains an SQL dump for a fresh install, and a config-file-template. It is kept up-to-date with the most significant releases of Composr.",
        'filename' => 'composr-' . $version_dotted . '.tar.gz',
        'additional_details' => '',
        'category_id' => $installatron_category_id,
        'internal_name' => 'Installatron installer',
    ],

    [
        'name' => "Composr {$version_pretty}",
        'description' => "This is an APS package of Composr. APS is a standardised package format potentially supported by multiple vendors, including Plesk. We will update this routinely when we release new versions, and update the APS catalog.\n\nIt can be manually installed into Plesk using the Application Vault interface available to administrators.",
        'filename' => 'composr-' . $version_dotted . '.app.zip',
        'additional_details' => '',
        'category_id' => $aps_category_id,
        'internal_name' => 'Plesk APS package',
    ],
];

foreach ($all_downloads_to_add as $i => $d) {
    $full_local_path = get_custom_file_base() . '/uploads/downloads/' . $d['filename'];
    $d['full_local_path'] = $full_local_path;
    if (!file_exists($full_local_path)) {
        echo '<p>Could not find file <kbd>uploads/downloads/' . escape_html($d['filename']) . '</kbd></p>';
        continue;
    }
    $all_downloads_to_add[$i] = $d;
}

foreach ($all_downloads_to_add as $i => $d) {
    if (!isset($d['full_local_path'])) {
        continue; // Could not find file above
    }

    $full_local_path = $d['full_local_path'];
    $file_size = filesize($full_local_path);
    $original_filename = $d['filename'];
    $name = $d['name'];
    $url = 'uploads/downloads/' . rawurlencode($d['filename']);
    $description = $d['description'];
    $additional_details = $d['additional_details'];
    $category_id = $d['category_id'];

    $download_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'id', ['category_id' => $category_id, $GLOBALS['SITE_DB']->translate_field_ref('name') => $name]);
    if ($download_id === null) {
        $download_id = add_download($category_id, $name, $url, $description, 'Core Developer Team', $additional_details, null, 1, 0, 0, 0, '', $original_filename, $file_size, 0, 0);
    } else {
        edit_download($download_id, $category_id, $name, $url, $description, 'Core Developer Team', $additional_details, null, 0, 1, 0, 0, 0, '', $original_filename, $file_size, 0, 0, null, '', '');
    }

    $d['download_id'] = $download_id;
    $all_downloads_to_add[$i] = $d;

    $urls[$d['internal_name']] = static_evaluate_tempcode(build_url(['page' => 'downloads', 'type' => 'entry', 'id' => $download_id], get_module_zone('downloads'), [], false, false, true));
    $urls[$d['internal_name'] . ' (direct download)'] = find_script('dload') . '?id=' . strval($download_id);
}

// Edit past download

if ((!$is_bleeding_edge) && (!$is_old_tree) && (isset($all_downloads_to_add[0]['download_id']))) {
    $last_version_str = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'additional_details', [$GLOBALS['SITE_DB']->translate_field_ref('additional_details') => 'This is the latest version.'], ' AND main.id<>' . strval($all_downloads_to_add[0]['download_id']));
    if ($last_version_str !== null) {
        $last_version_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'id', [$GLOBALS['SITE_DB']->translate_field_ref('additional_details') => 'This is the latest version.'], ' AND main.id<>' . strval($all_downloads_to_add[0]['download_id']));
        if ($last_version_id != $all_downloads_to_add[0]['download_id']) {
            $description = "A new version, {$version_pretty} is available. Upgrading to {$version_pretty} is considered {$needed} by the Core Developer Team{$criteria}{$justification}. There may have been other upgrades since {$version_pretty} - see [url=\"the Composr news archive\" target=\"_blank\"]https://compo.sr/site/news.htm[/url].";
            $GLOBALS['SITE_DB']->query_update('download_downloads', lang_remap_comcode('description', $last_version_str, $description), ['id' => $last_version_id], '', 1);
        }
    }
}

// Extract latest download
if ((!$is_bleeding_edge) && (!$is_old_tree)) {
    @unlink('data.cms');
    @unlink('install.php');
    $cmd = 'cd ' . get_custom_file_base() . '/uploads/downloads; unzip -o ' . $all_downloads_to_add[0]['filename'];
    shell_exec($cmd);
}

// News

$major_release = '';
$major_release_1 = '';
if ($is_substantial) {
    $major_release = " As this is more than just a patch release it is crucial that you also choose to run a file integrity scan and a database upgrade.";
    $major_release_1 = "Please [b]make sure you take a backup before uploading your new files![/b]";
    $news_title = 'Composr ' . $version_pretty . ' released!';
} else {
    $news_title = 'Composr ' . $version_pretty . ' released';
}

require_code('news');
require_code('news2');

$summary = "{$version_pretty} released. Read the full article for more information, and upgrade information.";

$article = "Version {$version_pretty} has now been released. {$descrip}. Upgrading to this release is {$needed}{$criteria}{$justification}.

To upgrade follow the steps in your website's [tt]http://mybaseurl/upgrader.php[/tt] script. You will need to copy the URL of the attached file (created via the form below) during step 3.
{$major_release_1}

[block=\"{$version_dotted}\"]composr_homesite_make_upgrader[/block]

{$changes}";

$news_category = $GLOBALS['SITE_DB']->query_select_value_if_there('news_categories', 'id', [$GLOBALS['SITE_DB']->translate_field_ref('nc_title') => 'New releases']);
if ($news_category === null) {
    $news_category = add_news_category('New releases', 'icons/news/general', '');
    set_global_category_access('news', $news_category);
}

$news_id = $GLOBALS['SITE_DB']->query_select_value_if_there('news', 'id', ['news_category' => $news_category, $GLOBALS['SITE_DB']->translate_field_ref('title') => $news_title]);
if ($news_id === null) {
    $news_id = add_news($news_title, $summary, 'Core Developer Team', 1, 0, 1, 0, '', $article, $news_category);
} else {
    edit_news($news_id, $news_title, $summary, 'Core Developer Team', 1, 0, 1, 0, '', $article, $news_category, null, '', '', '');
}
$urls['News: ' . $news_title] = static_evaluate_tempcode(build_url(['page' => 'news', 'type' => 'view', 'id' => $news_id], get_module_zone('news'), [], false, false, true));

// Set 'fixed in' in tracker for any issues referenced

$issues_found = [];

$regexp = '#' . preg_quote(get_base_url(), '#') . '/tracker/view\.php\?id=(\d+)#';
$matches = [];
$num_matches = preg_match_all($regexp, $changes, $matches);
for ($i = 0; $i < $num_matches; $i++) {
    $issues_found[] = intval($matches[1][$i]);
}

if (!empty($issues_found)) {
    $or_list = '';
    foreach ($issues_found as $id) {
        if ($or_list != '') {
            $or_list .= ' OR ';
        }
        $or_list .= 'id=' . strval($id);
    }

    $sql = 'UPDATE mantis_bug_table SET fixed_in_version=\'' . db_escape_string($version_dotted) . '\' WHERE (' . $or_list . ') AND ' . db_string_equal_to('fixed_in_version', '');
    $GLOBALS['SITE_DB']->query($sql);
}

// DONE!

echo '<p>Done version ' . escape_html($version_pretty) . '!</p>';

echo '<ul>';
foreach ($urls as $title => $url) {
    echo '<li><a href="' . escape_html($url) . '">' . $title->evaluate() . '</a></li>';
}
echo '</ul>';

require_code('mantis');
ensure_version_exists_in_tracker($version_dotted);

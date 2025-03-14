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
if (!addon_installed__messaged('cms_homesite', $error_msg)) {
    return $error_msg;
}

$title = get_screen_title('Publish new ' . brand_name() . ' release', false);
$title->evaluate_echo();

set_mass_import_mode(); // We will be adding multiple categories of the same name

restrictify();
require_code('permissions2');
require_code('cms_homesite');

// Version info / plan

$version_dotted = post_param_string('version');
require_code('version2');
$version_pretty = get_version_pretty__from_dotted(get_version_dotted__from_anything($version_dotted));

if (strpos($version_dotted, 'dev') !== false) {
    warn_exit('Development builds are not intended for public release.');
}

$is_substantial = is_substantial_release($version_dotted);

$is_old_tree = post_param_integer('is_old_tree') == 1;

$is_bleeding_edge = post_param_integer('is_bleeding_edge') == 1;
if (!$is_bleeding_edge) {
    $bleeding1 = '';
    $bleeding2 = '';
} else {
    $bleeding1 = ' (bleeding-edge)';
    $bleeding2 = ' (bleeding-edge)';
}

$video_url = post_param_string('video_url', '', INPUT_FILTER_URL_GENERAL);
$changes = post_param_string('changes', '');

if ($video_url != '') {
    $changes = 'Check out the [b]release video[/b] at ' . $video_url . "\n\n" . $changes;
}

$descrip = post_param_string('descrip', '', INPUT_FILTER_GET_COMPLEX);

$needed = post_param_string('needed', '', INPUT_FILTER_GET_COMPLEX);
$criteria = post_param_string('criteria', '', INPUT_FILTER_GET_COMPLEX);
$justification = post_param_string('justification', '', INPUT_FILTER_GET_COMPLEX);
$db_upgrade = post_param_integer('db_upgrade', 0) == 1;

$urls = [];

// Bugs list

if (!$is_bleeding_edge) {
    $urls['Bugs'] = get_brand_base_url() . '/tracker/search.php?project_id=1&product_version=' . urlencode($version_dotted);
}

// Add downloads (assume uploaded already)

require_code('downloads2');
require_code('permissions2');

// Get or create the base releases category
$download_category = brand_name() . ' Releases';
$releases_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => db_get_first_id(), $GLOBALS['SITE_DB']->translate_field_ref('category') => $download_category]);
if ($releases_category_id === null) {
    require_code('downloads2');
    $releases_category_id = add_download_category($download_category, db_get_first_id(), $download_category);
    set_global_category_access('downloads', $releases_category_id);
    set_privilege_access('downloads', strval($releases_category_id), 'submit_midrange_content', false);
}

// Get or create the sub-category for this major/minor version
$release_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Version ' . strval(intval($version_dotted))]);
if ($release_category_id === null) {
    $release_category_id = add_download_category('Version ' . strval(intval($version_dotted)), $releases_category_id, '', '');
    set_global_category_access('downloads', $release_category_id);
    set_privilege_access('downloads', strval($release_category_id), 'submit_midrange_content', false);
}

// Get or create the sub-category for Quick Installer and Manual Installer
$quick_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $release_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Quick Installer']);
if ($quick_category_id === null) {
    $quick_category_id = add_download_category('Quick Installer', $release_category_id, '', '');
    set_global_category_access('downloads', $quick_category_id);
    set_privilege_access('downloads', strval($quick_category_id), 'submit_midrange_content', false);
}
$manual_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $release_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Manual Installer']);
if ($manual_category_id === null) {
    $manual_category_id = add_download_category('Manual Installer', $manual_category_id, '', '');
    set_global_category_access('downloads', $manual_category_id);
    set_privilege_access('downloads', strval($manual_category_id), 'submit_midrange_content', false);
}
// NB: We don't add addon categories. This is done in publish_addons_as_downloads.php

// Get or create the Installatron category
$installatron_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Installatron integration']);
if ($installatron_category_id === null) {
    $installatron_category_id = add_download_category('Installatron integration', $releases_category_id, '', '');
    set_global_category_access('downloads', $installatron_category_id);
    set_privilege_access('downloads', strval($installatron_category_id), 'submit_midrange_content', false);
}

// Get or create the Microsoft category
$microsoft_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'Microsoft integration']);
if ($microsoft_category_id === null) {
    $microsoft_category_id = add_download_category('Microsoft integration', $releases_category_id, '', '');
    set_global_category_access('downloads', $microsoft_category_id);
    set_privilege_access('downloads', strval($microsoft_category_id), 'submit_midrange_content', false);
}

// Get or create the APS category
$aps_category_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', ['parent_id' => $releases_category_id, $GLOBALS['SITE_DB']->translate_field_ref('category') => 'APS integration']);
if ($aps_category_id === null) {
    $aps_category_id = add_download_category('APS integration', $releases_category_id, '', '');
    set_global_category_access('downloads', $aps_category_id);
    set_privilege_access('downloads', strval($aps_category_id), 'submit_midrange_content', false);
}

$major_release = '';
$major_release_1 = '';
$db_upgrade_1 = '';
if ($is_substantial) {
    $major_release = " As this is more than just a patch release it is crucial that you also choose to run a file integrity scan and a database upgrade.";
    $major_release_1 = "Please [b]make sure you take a backup before uploading your new files![/b]";
    $news_title = brand_name() . ' ' . $version_pretty . ' released!';
} else {
    $news_title = brand_name() . ' ' . $version_pretty . ' released';
    if ($db_upgrade) {
        $db_upgrade_1 = 'A database upgrade is required for this release. Be sure to run step 6 ("Do a database upgrade") in the upgrader after step 4 and, if applicable, step 5.';
    }
}

$summary_line = "{$descrip}. Upgrading to this release is {$needed}{$criteria}{$justification}.";
$additional_details = '';
if (!$is_old_tree) {
    if ($is_bleeding_edge) {
        $additional_details = 'This is the latest bleeding-edge version.';
    } else {
        $additional_details = 'This is the latest version.';
    }
}

$all_downloads_to_add = [
    [
        'name' => brand_name() . " Version {$version_pretty}{$bleeding2}",
        'description' => "This is version {$version_pretty}. {$summary_line}\n\n---\n\n{$changes}",
        'filename' => 'composr_quick_installer-' . $version_dotted . '.zip',
        'additional_details' => $additional_details,
        'category_id' => $quick_category_id,
        'internal_name' => 'Quick installer',
    ],

    [
        'name' => brand_name() . " Version {$version_pretty}{$bleeding2}",
        'description' => "This is the manual installer (as opposed to the regular quick installer) for version {$version_pretty}. {$summary_line}\n\n---\n\n{$changes}",
        'filename' => 'composr_manualextraction_installer-' . $version_dotted . '.zip',
        'additional_details' => '',
        'category_id' => $manual_category_id,
        'internal_name' => 'Manual installer',
    ],

    [
        'name' => brand_name() . " {$version_pretty}",
        'description' => "This archive is designed for webhosting control panels that integrate " . brand_name() . ". It contains an SQL dump for a fresh install, and a config-file-template. It is kept up-to-date with the most significant releases of " . brand_name() . ". {$summary_line}\n\n---\n\n{$changes}",
        'filename' => 'composr-' . $version_dotted . '.tar.gz',
        'additional_details' => '',
        'category_id' => $installatron_category_id,
        'internal_name' => 'Installatron installer',
    ],

    [
        'name' => brand_name() . " {$version_pretty}",
        'description' => "This is an APS package of " . brand_name() . ". APS is a standardised package format potentially supported by multiple vendors, including Plesk. We will update this routinely when we release new versions, and update the APS catalog.\nIt can be manually installed into Plesk using the Application Vault interface available to administrators. {$summary_line}\n\n---\n\n{$changes}",
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
    $download_added = false;
    if ($download_id === null) {
        $download_id = add_download($category_id, $name, $url, $description, 'Core Development Team', $additional_details, null, 1, 0, 0, 0, '', $original_filename, $file_size, 0, 0);
        $download_added = true;
    } else {
        edit_download($download_id, $category_id, $name, $url, $description, 'Core Development Team', $additional_details, null, 0, 1, 0, 0, 0, '', $original_filename, $file_size, 0, 0, null, '', '');
    }

    $d['download_id'] = $download_id;
    $all_downloads_to_add[$i] = $d;

    $urls[$d['internal_name']] = static_evaluate_tempcode(build_url(['page' => 'downloads', 'type' => 'entry', 'id' => $download_id], get_module_zone('downloads'), [], false, false, true));
    $urls[$d['internal_name'] . ' (direct download)'] = find_script('dload') . '?id=' . strval($download_id);

    // Edit past download to indicate it is old and replaced by this one

    if ($download_added === true) {
        $_last_version = $GLOBALS['SITE_DB']->query_select('download_downloads', ['add_date', 'additional_details', 'id', 'the_description'], ['category_id' => $category_id], ' AND main.out_mode_id IS NULL AND main.id<>' . strval($all_downloads_to_add[0]['download_id']) . ' ORDER BY add_date DESC', 1);
        if (array_key_exists(0, $_last_version)) {
            $last_version = $_last_version[0];
            if ($last_version['id'] != $all_downloads_to_add[0]['download_id']) {
                $description = "A new version, {$version_pretty} is available. Upgrading to {$version_pretty} is considered {$needed} by the Core Development Team{$criteria}{$justification}. There may have been other upgrades since {$version_pretty} - see [url=\"the software news archive\" target=\"_blank\"]" . get_brand_page_url(['page' => 'news'], 'site') . "[/url].\n\n---\n\n" . get_translated_text($last_version['description']);
                $map = lang_remap_comcode('description', get_translated_text($last_version['the_description']), $description);
                $map += lang_remap_comcode('additional_details', get_translated_text($last_version['additional_details']), '');
                $map['out_mode_id'] = $all_downloads_to_add[0]['download_id'];
                $GLOBALS['SITE_DB']->query_update('download_downloads', $map, ['id' => $last_version['id']], '', 1);
            }
        }
    }
}

// Extract latest download
if ((!$is_bleeding_edge) && (!$is_old_tree)) {
    // Delete unnecessary files that are often created by the release build
    @unlink('data.cms');
    @unlink('install.php');

    $cmd = 'cd ' . get_custom_file_base() . '/uploads/downloads; unzip -o ' . $all_downloads_to_add[0]['filename'];
    shell_exec($cmd);
}

// News

require_code('news');
require_code('news2');

$summary = "{$version_pretty} released. Read the full article for more information, and upgrade information.";

$article = "Version {$version_pretty} has now been released. {$summary_line}

To upgrade follow the steps in your website's [tt]http://mybaseurl/upgrader.php[/tt] script. You will need to copy the URL of the attached file (created via the form below) when running the step to transfer new / updated files.{$major_release}
{$major_release_1}
{$db_upgrade_1}

[block=\"{$version_dotted}\"]cms_homesite_make_upgrader[/block]

{$changes}";

// Get or create the new releases category
$news_category = $GLOBALS['SITE_DB']->query_select_value_if_there('news_categories', 'id', [$GLOBALS['SITE_DB']->translate_field_ref('nc_title') => 'New releases']);
if ($news_category === null) {
    $news_category = add_news_category('New releases', 'icons/news/general', '');
    set_global_category_access('news', $news_category);
}

$news_id = $GLOBALS['SITE_DB']->query_select_value_if_there('news', 'id', ['news_category' => $news_category, $GLOBALS['SITE_DB']->translate_field_ref('title') => $news_title]);
if ($news_id === null) {
    $news_id = add_news($news_title, $summary, 'Core Development Team', 1, 0, 1, 0, '', $article, $news_category);
} else {
    edit_news($news_id, $news_title, $summary, 'Core Development Team', 1, 0, 1, 0, '', $article, $news_category, null, '', '', '');
}
$urls['News: ' . $news_title] = static_evaluate_tempcode(build_url(['page' => 'news', 'type' => 'view', 'id' => $news_id], get_module_zone('news'), [], false, false, true));

// Set 'fixed in' in tracker for any issues referenced

$issues_found = [];

$regexp = '#' . preg_quote(get_brand_base_url(), '#') . '/tracker/view\.php\?id=(\d+)#';
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

foreach ($urls as $_link_title => $link_url) {
    if (is_object($_link_title)) {
        $link_title = $_link_title->evaluate();
    } else {
        $link_title = strval($_link_title);
    }
    echo '<li><a href="' . escape_html($link_url) . '">' . $link_title . '</a></li>';
}
echo '</ul>';

require_code('mantis');
ensure_version_exists_in_tracker($version_dotted);

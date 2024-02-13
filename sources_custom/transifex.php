<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    transifex
 */

function init__transifex()
{
    define('TRANSLATE_PRIORITY_NORMAL', 0);
    define('TRANSLATE_PRIORITY_HIGH', 1);
    define('TRANSLATE_PRIORITY_URGENT', 2);

    define('TRANSLATE_CORE', 'core');
    define('TRANSLATE_ADDON', 'non-bundled addon');

    define('TRANSLATE_ADMINISTRATIVE_YES', 0);
    define('TRANSLATE_ADMINISTRATIVE_NO', 1);
    define('TRANSLATE_ADMINISTRATIVE_MIXED', 2);

    require_code('addons');
    require_code('lang_compile');
    require_code('lang2');
    require_code('files');
    require_code('files2');
    require_code('character_sets');

    global $OVERRIDE_PRIORITY_LANGUAGE_FILES;
    $OVERRIDE_PRIORITY_LANGUAGE_FILES = [
        'global.ini' => TRANSLATE_PRIORITY_URGENT,
        'cns.ini' => TRANSLATE_PRIORITY_URGENT,
        'news.ini' => TRANSLATE_PRIORITY_URGENT,
        'upload_syndication.ini' => TRANSLATE_PRIORITY_NORMAL,
        'trackbacks.ini' => TRANSLATE_PRIORITY_NORMAL,
        'sms.ini' => TRANSLATE_PRIORITY_NORMAL,
        'rss.ini' => TRANSLATE_PRIORITY_NORMAL,
        'import.ini' => TRANSLATE_PRIORITY_NORMAL,
    ];

    // Extra files to send to Transifex (additional to .ini files)
    global $EXTRA_LANGUAGE_FILES;
    $EXTRA_LANGUAGE_FILES = [
        'adminzone/pages/comcode/EN/netlink.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'adminzone/pages/comcode/EN/quotes.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'adminzone/pages/comcode_custom/EN/comcode_safelist.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_ADDON, TRANSLATE_ADMINISTRATIVE_YES],
        'adminzone/pages/comcode_custom/EN/insults.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_ADDON, TRANSLATE_ADMINISTRATIVE_YES],
        'adminzone/pages/comcode_custom/EN/referrals.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_ADDON, TRANSLATE_ADMINISTRATIVE_YES],
        'buildr/pages/comcode_custom/EN/docs.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_ADDON, TRANSLATE_ADMINISTRATIVE_NO],
        'buildr/pages/comcode_custom/EN/rules.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_ADDON, TRANSLATE_ADMINISTRATIVE_NO],
        'data/modules/cms_comcode_pages/EN/about_us.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/advertise.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/article.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/competitor_comparison.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/contact_us.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/donate.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/guestbook.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/landing_page.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/press_release.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/pricing.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/rules_balanced.txt' => [null, TRANSLATE_PRIORITY_URGENT, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'data/modules/cms_comcode_pages/EN/rules_corporate.txt' => [null, TRANSLATE_PRIORITY_URGENT, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'data/modules/cms_comcode_pages/EN/rules_liberal.txt' => [null, TRANSLATE_PRIORITY_URGENT, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'data/modules/cms_comcode_pages/EN/two_column_layout.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'data/modules/cms_comcode_pages/EN/under_construction.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'pages/comcode/EN/404.txt' => [null, TRANSLATE_PRIORITY_HIGH, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'pages/comcode/EN/_rules.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'pages/comcode/EN/feedback.txt' => [null, TRANSLATE_PRIORITY_URGENT, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'pages/comcode/EN/keymap.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'pages/comcode/EN/privacy.txt' => [null, TRANSLATE_PRIORITY_URGENT, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'pages/comcode/EN/recommend_help.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'pages/comcode/EN/rules.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'pages/comcode/EN/sitemap.txt' => [null, TRANSLATE_PRIORITY_URGENT, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'site/pages/comcode/EN/help.txt' => [null, TRANSLATE_PRIORITY_URGENT, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'site/pages/comcode/EN/popup_blockers.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'site/pages/comcode/EN/userguide_chatcode.txt' => [null, TRANSLATE_PRIORITY_HIGH, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'site/pages/comcode/EN/userguide_comcode.txt' => [null, TRANSLATE_PRIORITY_HIGH, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'text/EN/quotes.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'text/EN/synonyms.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES],
        'text/EN/too_common_words.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'text/EN/word_characters.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO],
        'text_custom/EN/insults.txt' => [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_ADDON, TRANSLATE_ADMINISTRATIVE_NO],
    ];
    $EXTRA_LANGUAGE_FILES['adminzone/pages/comcode/EN/' . DEFAULT_ZONE_PAGE_NAME . '.txt'] = [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_YES];
    $EXTRA_LANGUAGE_FILES['buildr/pages/comcode_custom/EN/' . DEFAULT_ZONE_PAGE_NAME . '.txt'] = [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_ADDON, TRANSLATE_ADMINISTRATIVE_NO];
    $EXTRA_LANGUAGE_FILES['pages/comcode/EN/' . DEFAULT_ZONE_PAGE_NAME . '.txt'] = [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO];
    $EXTRA_LANGUAGE_FILES['site/pages/comcode/EN/' . DEFAULT_ZONE_PAGE_NAME . '.txt'] = [null, TRANSLATE_PRIORITY_NORMAL, TRANSLATE_CORE, TRANSLATE_ADMINISTRATIVE_NO];
    foreach ($EXTRA_LANGUAGE_FILES as $file => &$properties) {
        $properties[0] = basename(str_replace('/', '__', $file), '.txt') . (($properties[3] == TRANSLATE_ADMINISTRATIVE_YES) ? '__administrative' : '');
    }

    // Find what language files are in what addons
    $addons = find_all_hooks('systems', 'addon_registry');
    global $LANGUAGE_FILES_ADDON, $EXISTING_LANGUAGE_AUTHORS;
    $LANGUAGE_FILES_ADDON = [];
    $EXISTING_LANGUAGE_AUTHORS = [];
    foreach (array_keys($addons) as $addon_name) {
        $info = read_addon_info($addon_name);
        $matches = [];
        foreach ($info['files'] as $file) {
            if (preg_match('#^lang(_custom)?/' . fallback_lang() . '/(\w+)\.ini$#', $file, $matches) != 0) {
                $LANGUAGE_FILES_ADDON[$matches[2]] = $addon_name;
            }

            if (preg_match('#^(.*/' . fallback_lang() . '/.*\.txt)$#', $file, $matches) != 0) {
                $LANGUAGE_FILES_ADDON[$matches[1]] = $addon_name;
            }
        }
        if (preg_match('#^language_(\w+)$#', $addon_name, $matches) != 0) {
            $EXISTING_LANGUAGE_AUTHORS[$matches[1]] = explode(', ', $info['author']);
        }
    }

    // Find language string descriptions
    global $LANGUAGE_STRING_DESCRIPTIONS;
    $LANGUAGE_STRING_DESCRIPTIONS = get_lang_file_section(fallback_lang(), null, 'descriptions');

    global $LANG_FILES;
    $LANG_FILES = get_lang_files(fallback_lang());
}

function convert_lang_code_to_transifex($lang)
{
    if ($lang == 'ZH-CN') {
        return 'zh'; // We use Google Translate name as a special case
    }

    $lang_parts = explode('_', $lang, 2);
    return cms_strtolower_ascii($lang_parts[0]) . (isset($lang_parts[1]) ? ('_' . $lang_parts[1]) : '');
}

function transifex_push_script()
{
    if (!addon_installed('transifex')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('transifex')));
    }

    header('X-Robots-Tag: noindex');

    $cli = is_cli();
    if (!$cli) {
        if (!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) {
            header('Content-Type: text/plain; charset=utf-8');
            exit('Must run this script on command line, for security reasons -- or be logged in as an administrator');
        }
    }

    @header('Content-Type: text/plain; charset=' . get_charset());
    cms_ini_set('ocproducts.xss_detect', '0');

    $core_only = _transifex_env_setting('core_only');
    $push_cms = _transifex_env_setting('push_cms');
    $push_ini = _transifex_env_setting('push_ini');
    $push_translations = _transifex_env_setting('push_translations');
    $limit_substring = _transifex_env_limit_substring();

    push_to_transifex($core_only, $push_cms, $push_ini, $push_translations, $limit_substring);

    echo 'Done';
}

function push_to_transifex($core_only, $push_cms, $push_ini, $push_translations, $limit_substring)
{
    global $EXISTING_LANGUAGE_AUTHORS, $EXTRA_LANGUAGE_FILES;

    push_query_limiting(false);
    cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

    $project_slug = get_composr_transifex_project();

    // Create project if it does not already exist
    $args = [
        'slug' => $project_slug,
        'name' => 'Composr CMS ' . strval(cms_version()),
        'source_language_code' => convert_lang_code_to_transifex(fallback_lang()),
        'description' => 'Community translation for Composr CMS ' . strval(cms_version()),
        'private' => false,
        'license' => 'permissive_open_source',
        'repository_url' => COMPOSR_REPOS_URL,
        'organization' => 'ocproducts',
        'team' => 39268, // This is a hard-coded known value for the ocProducts organisation
        'fill_up_resources' => true,
        'homepage' => 'https://compo.sr',
        'trans_instructions' => 'See https://compo.sr/docs/tut_intl.htm',
    ];
    $_args = json_encode($args);
    $_args = convert_to_internal_encoding($_args, get_charset(), 'utf-8');
    $test = _transifex('/projects/', 'POST', $_args, false);
    if ($test[1] == '201') { // If creation happened
        // Create translations for all defined languages
        $langs = cms_parse_ini_file_fast(get_file_base() . '/lang/langs.ini');
        $failed_langs = [];
        foreach (array_keys($langs) as $lang) {
            if ($lang == fallback_lang()) {
                continue;
            }

            $test = _transifex('/project/' . $project_slug . '/language/' . convert_lang_code_to_transifex($lang) . '/', 'GET', null, false);
            if ($test[1] == '404') {
                if (isset($EXISTING_LANGUAGE_AUTHORS[$lang])) {
                    $coordinators = $EXISTING_LANGUAGE_AUTHORS[$lang];
                } else {
                    $coordinators = [];
                }
                list($username, $password) = _transifex_credentials();
                $coordinators[] = $username;

                $args = [
                    'language_code' => convert_lang_code_to_transifex($lang),
                    'coordinators' => $coordinators,
                ];
                $_args = json_encode($args);
                $_args = convert_to_internal_encoding($_args, get_charset(), 'utf-8');
                $test = _transifex('/project/' . $project_slug . '/languages/?skip_invalid_username', 'POST', $_args);

                // May not fail, not all languages supported (https://www.transifex.com/languages/)
                if ($test[1] == '400') {
                    $failed_langs[] = $lang; // We don't use this array, but it is useful for debugging. We try and prune any obscure languages from langs.ini that Transifex doesn't support (because very few people will speak them)
                }
            }
        }

        echo "A new project was created. You need to manually edit the organization settings (https://www.transifex.com/ocproducts/settings/tm/) to add it to the translation memory group.\n";
    }

    // Upload special files
    if ($push_cms) {
        foreach ($EXTRA_LANGUAGE_FILES as $path => $extra_file) {
            if (($limit_substring !== null) && (strpos($path, $limit_substring) === false)) {
                continue;
            }

            _push_cms_file_to_transifex($path, $extra_file[0], $project_slug, $extra_file[1], $extra_file[3], $extra_file[2], $push_translations);

            echo "Uploaded CMS file {$path}\n";
            cms_flush_safe();
        }
    }

    // Upload translatable language files
    if ($push_ini) {
        $d = get_file_base() . '/lang/' . fallback_lang();
        $dh = opendir($d);
        $default_lang_files = [];
        while (($f = readdir($dh)) !== false) {
            if (substr($f, -4) == '.ini') {
                if (($limit_substring !== null) && (strpos($f, $limit_substring) === false)) {
                    continue;
                }

                $default_lang_files[$f] = true;
                $result = _push_ini_file_to_transifex($f, $project_slug, false, TRANSLATE_ADMINISTRATIVE_NO, $push_translations);
                if ($result) {
                    _push_ini_file_to_transifex($f, $project_slug, false, TRANSLATE_ADMINISTRATIVE_YES, $push_translations);

                    echo "Uploaded ini (strings) file {$f}\n";
                    cms_flush_safe();
                }
            }
        }
        closedir($dh);
        if (!$core_only) {
            $d = get_file_base() . '/lang_custom/' . fallback_lang();
            $dh = opendir($d);
            while (($f = readdir($dh)) !== false) {
                if ((substr($f, -4) == '.ini') && (!isset($default_lang_files[$f]))) {
                    if (($limit_substring !== null) && (strpos($f, $limit_substring) === false)) {
                        continue;
                    }

                    $result = _push_ini_file_to_transifex($f, $project_slug, true, TRANSLATE_ADMINISTRATIVE_MIXED, $push_translations);

                    if ($result) {
                        echo "Uploaded ini (strings) file {$f}\n";
                        cms_flush_safe();
                    }
                }
            }
            closedir($dh);
        }
    }
}

function _push_cms_file_to_transifex($path, $resource_path, $project_slug, $priority, $administrative, $category, $push_translations)
{
    global $LANGUAGE_FILES_ADDON;

    $full_path = get_file_base() . '/' . $path;
    $c = cms_file_get_contents_safe($full_path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

    // Upload
    $test = _transifex('/project/' . $project_slug . '/resource/' . $resource_path . '/', 'GET', null, false);
    $categories = [$category];
    if ($LANGUAGE_FILES_ADDON[$path] != $category) {
        // Addon name
        $categories[] = $LANGUAGE_FILES_ADDON[$path];
    }
    $args = [
        'slug' => $resource_path,
        'name' => $resource_path,
        'accept_translations' => true,
        'categories' => $categories,
        'priority' => $priority,
    ];
    if ($test[1] == '200') {
        // Edit
        $_args = json_encode($args);
        $_args = convert_to_internal_encoding($_args, get_charset(), 'utf-8');
        $test = _transifex('/project/' . $project_slug . '/resource/' . $resource_path . '/', 'PUT', $_args);
        $args2 = ['content' => $c];
        $_args2 = json_encode($args2);
        $_args2 = convert_to_internal_encoding($_args2, get_charset(), 'utf-8');
        $test = _transifex('/project/' . $project_slug . '/resource/' . $resource_path . '/content/', 'PUT', $_args2);
    } else {
        // Add
        $args2 = $args + ['i18n_type' => 'TXT', 'content' => $c];
        $_args2 = json_encode($args2);
        $_args2 = convert_to_internal_encoding($_args2, get_charset(), 'utf-8');
        $test = _transifex('/project/' . $project_slug . '/resources/', 'POST', $_args2);
    }

    // Upload existing translated files for this language file
    if ($push_translations) {
        foreach (array_keys(find_all_langs()) as $lang) {
            if ($lang != fallback_lang()) {
                $trans_full_path = str_replace('/' . fallback_lang() . '/', '/' . $lang . '/', $full_path);
                if (is_file($trans_full_path)) {
                    $c2 = cms_file_get_contents_safe($trans_full_path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                    $args = ['content' => $c2];
                    _transifex('/project/' . $project_slug . '/resource/' . $resource_path . '/translation/' . convert_lang_code_to_transifex($lang) . '/', 'PUT', json_encode($args));

                    echo "Uploaded translation {$trans_full_path}\n";
                    cms_flush_safe();
                }
            }
        }
    }
}

function _push_ini_file_to_transifex($f, $project_slug, $custom, $administrative, $push_translations)
{
    global $JUST_LANG_STRINGS_ADMIN, $OVERRIDE_PRIORITY_LANGUAGE_FILES, $LANGUAGE_STRING_DESCRIPTIONS, $LANGUAGE_FILES_ADDON;

    require_code('string_scan');
    list($JUST_LANG_STRINGS_ADMIN) = string_scan(fallback_lang(), true);
    $JUST_LANG_STRINGS_ADMIN = array_flip($JUST_LANG_STRINGS_ADMIN);

    if ($custom) {
        $category = TRANSLATE_ADDON;
    } else {
        $category = TRANSLATE_CORE;
    }

    if ((isset($OVERRIDE_PRIORITY_LANGUAGE_FILES[$f])) && ($administrative != TRANSLATE_ADMINISTRATIVE_YES)) {
        $priority = $OVERRIDE_PRIORITY_LANGUAGE_FILES[$f];
    } else {
        if (($custom) || ($administrative == TRANSLATE_ADMINISTRATIVE_YES) && (!in_array($f, ['content_privacy.ini', 'metadata.ini', 'do_next.ini']))) {
            $priority = TRANSLATE_PRIORITY_NORMAL;
        } else {
            $priority = TRANSLATE_PRIORITY_HIGH;
        }
    }

    $_f = basename($f, '.ini');
    $_f_extended = $_f . (($administrative == TRANSLATE_ADMINISTRATIVE_YES) ? '__administrative' : '');

    if (!isset($LANGUAGE_FILES_ADDON[$_f])) {
        echo "Unrecognised language file skipped {$_f}\n";
        return false;
    }

    // Rebuild as a simpler .ini file
    $map = get_lang_file_map(fallback_lang(), $_f, !$custom, false);
    $c = '';
    foreach ($map as $key => $val) {
        if ($administrative == TRANSLATE_ADMINISTRATIVE_YES) {
            if (!isset($JUST_LANG_STRINGS_ADMIN[$key])) {
                continue;
            }
        }

        if ($administrative == TRANSLATE_ADMINISTRATIVE_NO) {
            if (isset($JUST_LANG_STRINGS_ADMIN[$key])) {
                continue;
            }
        }

        $c .= $key . '=' . str_replace("\n", '\n', $val) . "\n";
    }

    // Upload
    $test = _transifex('/project/' . $project_slug . '/resource/' . $_f_extended . '/', 'GET', null, false);
    $categories = [$category];
    if ($LANGUAGE_FILES_ADDON[$_f] != $category) {
        // Addon name
        $categories[] = $LANGUAGE_FILES_ADDON[$_f];
    }
    $args = [
        'slug' => $_f_extended,
        'name' => $_f_extended,
        'accept_translations' => true,
        'priority' => $priority,
    ];
    if (count($categories) == 1) {
        $args['category'] = $categories[0];
    } else {
        $args['categories'] = $categories;
    }
    if ($test[1] == '200') {
        if ($c == '') {
            $test = _transifex('/project/' . $project_slug . '/resource/' . $_f_extended . '/', 'DELETE');
            return true; // Empty, so delete
        }

        // Edit
        $_args = json_encode($args);
        $_args = convert_to_internal_encoding($_args, get_charset(), 'utf-8');
        $test = _transifex('/project/' . $project_slug . '/resource/' . $_f_extended . '/', 'PUT', $_args);
        $test = _transifex('/project/' . $project_slug . '/resource/' . $_f_extended . '/content/', 'PUT', json_encode(['content' => $c]));
    } else {
        if ($c == '') {
            return true; // Empty, so don't add
        }

        // Add
        $args2 = $args + ['i18n_type' => 'INI', 'content' => $c];
        $_args2 = json_encode($args2);
        $_args2 = convert_to_internal_encoding($_args2, get_charset(), 'utf-8');
        $test = _transifex('/project/' . $project_slug . '/resources/', 'POST', $_args2);
    }

    // Set metadata
    foreach ($map as $key => $val) {
        if (isset($LANGUAGE_STRING_DESCRIPTIONS[$key])) {
            $descrip = $LANGUAGE_STRING_DESCRIPTIONS[$key];
            $hash = md5($key . ':');
            $args = ['comment' => $descrip];
            $_args = json_encode($args);
            $_args = convert_to_internal_encoding($_args, get_charset(), 'utf-8');
            $test = _transifex('/project/' . $project_slug . '/resource/' . $_f_extended . '/source/' . $hash . '/', 'PUT', $_args, false/*getting errors recently*/);
        }
    }

    // Upload existing translated files for this language file
    if ($push_translations) {
        $d = get_file_base() . '/lang_custom';
        $dh = opendir($d);
        while (($lang = readdir($dh)) !== false) {
            if ((is_dir($d . '/' . $lang)) && (does_lang_exist($lang)) && ($lang != fallback_lang())) {
                if (is_file($d . '/' . $lang . '/' . $f)) {
                    $map = get_lang_file_map($lang, $_f, false);
                    $c2 = '';
                    foreach ($map as $key => $val) {
                        if (($administrative != TRANSLATE_ADMINISTRATIVE_YES) || (isset($JUST_LANG_STRINGS_ADMIN[$key]))) {
                            $c2 .= $key . '=' . str_replace("\n", '\n', $val) . "\n";
                        }
                    }

                    $args = ['content' => $c2];
                    _transifex('/project/' . $project_slug . '/resource/' . $_f_extended . '/translation/' . convert_lang_code_to_transifex($lang) . '/', 'PUT', json_encode($args));
                }
            }
        }
        closedir($dh);
    }

    return true;
}

function transifex_pull_script()
{
    if (!addon_installed('transifex')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('transifex')));
    }

    header('X-Robots-Tag: noindex');

    $version = _transifex_env_version();
    $lang = _transifex_env_lang();
    $output = _transifex_env_setting('output');
    $core_only = _transifex_env_setting('core_only');

    $cli = is_cli();
    if (!$cli) {
        if (!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) {
            if (!$output) {
                header('Content-Type: text/plain; charset=utf-8');
                exit('You must be logged in as an administrator (or run this script from the command line) if not using output=0');
            }

            if ($lang === null) {
                header('Content-Type: text/plain; charset=utf-8');
                exit('You must be logged in as an administrator (or run this script from the command line) if not setting an output language');
            }
        }
    }

    if ($output) {
        header('Content-Type: application/octet-stream');
        require_code('version2');
        if ($lang === null) {
            $filename = 'languages-' . get_version_branch(floatval(cms_version_number())) . '.tar';
        } else {
            $filename = 'language-' . escape_header($lang) . '-' . get_version_branch(floatval(cms_version_number())) . '.tar';
        }
        header('Content-Disposition: attachment; filename="' . escape_header($filename, true) . '"');
        cms_ini_set('ocproducts.xss_detect', '0');

        require_code('tar');
        $tar_file = tar_open('php://output', 'wb');
    } else {
        $tar_file = null;
    }

    pull_from_transifex($version, $tar_file, $lang, $core_only);

    if ($output) {
        tar_close($tar_file);
        exit();
    } else {
        header('Content-Type: text/plain; charset=' . get_charset());
        cms_ini_set('ocproducts.xss_detect', '0');
        echo 'Done';
    }
}

function get_composr_transifex_project($version = null)
{
    if ($version === null) {
        $version = strval(cms_version());
    }
    return 'composr-cms-' . str_replace('.', '-', $version);
}

function pull_from_transifex($version, $tar_file, $lang, $core_only)
{
    $project_slug = get_composr_transifex_project($version);

    push_query_limiting(false);
    cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

    if ($lang === null) {
        $langs = array_keys(cms_parse_ini_file_fast(get_file_base() . '/lang/langs.ini'));
        foreach ($langs as $lang) {
            if ($lang != fallback_lang()) {
                pull_lang_from_transifex($project_slug, $tar_file, $lang, $core_only, false);
            }
        }
    } else {
        pull_lang_from_transifex($project_slug, $tar_file, $lang, $core_only, true);
    }
}

function pull_lang_from_transifex($project_slug, $tar_file, $lang, $core_only, $definitely_want)
{
    global $EXTRA_LANGUAGE_FILES, $LANG_FILES;

    $test = _transifex('/project/' . $project_slug . '/language/' . convert_lang_code_to_transifex($lang) . '/?details', 'GET', null, false);
    if ($test[1] == '401') {
        warn_exit('Access denied using your username. Does it have write-access to this language?');
    }
    if ($test[1] == '200') {
        $language_details = json_decode($test[0], true);

        if (!$definitely_want) {
            if (floatval($language_details['translated_segments']) / floatval($language_details['total_segments']) < 0.2/*Under 20%*/) {
                // Not translated enough
                return false;
            }
        }

        $files = [];

        // Grab special files
        foreach ($EXTRA_LANGUAGE_FILES as $path => $extra_file) {
            if (($core_only) && ($extra_file[2] != TRANSLATE_CORE)) {
                continue;
            }

            _pull_cms_file_from_transifex($project_slug, $tar_file, $lang, $path, $extra_file, $files);
        }

        // Grab translatable language files
        foreach (array_keys($LANG_FILES) as $_f) {
            if (($core_only) && (!is_file(get_file_base() . '/lang/' . fallback_lang() . '/' . $_f . '.ini'))) {
                continue;
            }

            _pull_ini_file_from_transifex($project_slug, $tar_file, $lang, $_f, $files);
        }

        // Write addon_registry hook
        if (!empty($files)) {
            $path = 'sources_custom/hooks/systems/addon_registry/language_' . $lang . '.php';
            $full_path = get_file_base() . '/' . $path;

            $translators = implode(', ', $language_details['translators']);
            if ($translators == '') {
                $translators = do_lang('UNKNOWN');

                if (is_file($full_path)) {
                    $c = cms_file_get_contents_safe(($full_path), FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

                    $matches = [];
                    if (preg_match('#function get_author\(\)\s*\{\s*return \'([^\']*)\';#', $c, $matches) != 0) {
                        $translators = $matches[1];
                    }
                }
            }

            $percentage = intval(round(100.0 * $language_details['translated_segments'] / $language_details['total_segments'])); // calculate %age

            $language_name = lookup_language_full_name($lang);

            if (is_file('sources_custom/lang_filter_' . $lang . '.php')) {
                $files[] = 'sources_custom/lang_filter_' . $lang . '.php';
            }

            $files_str = '';
            foreach ($files as $file) {
                $files_str .= "\n            '" . $file . "',";
            }

            $description = "Translation into {$language_name}.

Completeness: {$percentage}% (29% typically means translated fully apart from administrative strings).

This addon was automatically bundled from community contributions provided on Transifex and will be routinely updated alongside new Composr patch releases.

Translations may also be downloaded directly from Transifex.";

            $open = '<' . '?php';

            $c = <<<END
{$open} /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    language_{$lang}
 */

/**
 * Hook class.
 */
class Hook_addon_registry_language_{$lang}
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean \$runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool \$runtime = false) : array
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Translations';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return '{$translators}';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return '{$description}';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return ['tut_intl', 'tut_intl_users'];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/menu/adminzone/style/language/language.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/language_{$lang}.php',{$files_str}
        ];
    }
}
END;

            $c = trim($c) . "\n\n";

            if ($tar_file === null) {
                cms_file_put_contents_safe($full_path, $c, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
            } else {
                tar_add_file($tar_file, $path, $c);

                // addon.inf is needed too
                $addon_inf = '';
                $settings = [
                    'name' => $language_name,
                    'author' => $translators,
                    'organisation' => '',
                    'version' => cms_version_number(),
                    'category' => 'Translations',
                    'copyright_attribution' => '',
                    'licence' => '',
                    'description' => $description,
                    'incompatibilities' => '',
                    'dependencies' => '',
                ];
                foreach ($settings as $setting_name => $setting_value) {
                    $addon_inf .= $setting_name . '="' . str_replace("\n", '\n', str_replace('"', '\'', $setting_value)) . '"' . "\n";
                }
                tar_add_file($tar_file, 'addon.inf', $addon_inf, 0644, time());
            }
        }
    }

    return true;
}

function _pull_cms_file_from_transifex($project_slug, $tar_file, $lang, $path, $extra_file, &$files)
{
    $resource_path = $extra_file[0];

    $default_path = str_replace('__', '/', str_replace('__administrative', '', $resource_path)) . '.txt';

    $trans_path = str_replace('/' . fallback_lang() . '/', '/' . $lang . '/', $default_path);
    $trans_path = str_replace('/comcode/', '/comcode_custom/', $trans_path);
    $trans_path = preg_replace('#^text/#', 'text_custom/', $trans_path);
    $trans_path = preg_replace('#^data/#', 'data_custom/', $trans_path);

    $trans_full_path = get_file_base() . '/' . $trans_path;

    $limit_substring = _transifex_env_limit_substring();
    if (($limit_substring !== null) && (strpos($path, $limit_substring) === false)) {
        $files[] = $trans_path;

        return;
    }

    $test = _transifex('/project/' . $project_slug . '/resource/' . $resource_path . '/translation/' . convert_lang_code_to_transifex($lang) . '/', 'GET', null, false);
    if ($test[1] == '200') {
        $data = json_decode($test[0], true);
        $c = $data['content'];
        $c = _transifex_decode_content($c);
        $c .= "\n";

        if (is_file($default_path) && trim($c) == trim(cms_file_get_contents_safe($default_path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM))) {
            return; // Not changed
        }

        if ($tar_file === null) {
            cms_file_put_contents_safe($trans_full_path, $c, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE | FILE_WRITE_BOM);
        } else {
            tar_add_file($tar_file, $trans_path, $c);
        }

        $files[] = $trans_path;
    }
}

function _pull_ini_file_from_transifex($project_slug, $tar_file, $lang, $_f, &$files)
{
    $trans_path = 'lang_custom/' . $lang . '/' . $_f . '.ini';
    $trans_full_path = get_file_base() . '/' . $trans_path;

    $limit_substring = _transifex_env_limit_substring();
    if (($limit_substring !== null) && (strpos($_f, $limit_substring) === false)) {
        $files[] = $trans_path;

        return;
    }

    $test_a = _transifex('/project/' . $project_slug . '/resource/' . $_f . '/translation/' . convert_lang_code_to_transifex($lang) . '/', 'GET', null, false);
    $test_b = _transifex('/project/' . $project_slug . '/resource/' . $_f . '__administrative/translation/' . convert_lang_code_to_transifex($lang) . '/', 'GET', null, false);
    if ($test_a[1] == '200' || $test_b[1] == '200') {
        if ($test_a[1] == '200') {
            $data_a = json_decode($test_a[0], true);
            $data_a['content'] = _transifex_decode_content($data_a['content']);
        } else {
            $data_a = ['content' => ''];
        }
        if ($test_b[1] == '200') {
            $data_b = json_decode($test_b[0], true);
            $data_b['content'] = _transifex_decode_content($data_b['content']);
        } else {
            $data_b = ['content' => ''];
        }

        $write_out = trim(preg_replace('#^\# .*\n#m', '', $data_a['content'] . "\n" . $data_b['content'])) . "\n";

        // Fix some common mistakes people make
        if ($_f == 'global') {
            $write_out = preg_replace('#^en_left=((?!left$)(?!right$).)*$#m', 'en_left=left', $write_out);
            $write_out = preg_replace('#^en_right=((?!right$)(?!left$).)*$#m', 'en_right=right', $write_out);
            $write_out = preg_replace('#^dir=((?!ltr$)(?!rtl$).)*$#m', 'dir=ltr', $write_out);
        }

        $c = "[strings]\n" . trim($write_out) . "\n";

        if ($tar_file === null) {
            cms_file_put_contents_safe($trans_full_path, $c, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE | FILE_WRITE_BOM);
        } else {
            tar_add_file($tar_file, $trans_path, $c);
        }

        $files[] = $trans_path;
    }
}

function _transifex($call, $http_verb, $params = [], $trigger_error = true, $text = true)
{
    list($username, $password) = _transifex_credentials();

    if (substr($call, 0, 1) != '/') {
        warn_exit('Calls must start with /');
    }
    if ((substr($call, -1) != '/') && (strpos($call, '/?') === false)) {
        warn_exit('Calls must end with /');
    }

    if (is_array($params)) {
        $raw_content_type = 'multipart/form-data';
    } else {
        if ($params !== null) {
            $params = convert_to_internal_encoding($params, get_charset(), 'utf-8');
        }

        $raw_content_type = 'application/json';
    }

    $url = 'https://www.transifex.com/api/2' . $call;
    $auth = [$username, $password];
    $options = [
        'trigger_error' => $trigger_error,
        'ignore_http_status' => true,
        'post_params' => $params,
        'auth' => $auth,
        'timeout' => 30.0,
        'http_verb' => $http_verb,
        'raw_content_type' => $raw_content_type,
    ];
    if ($text) {
        $options['convert_to_internal_encoding'] = true;
    }

    $http_result = cms_http_request($url, $options);

    if ($http_result->message[0] == '2') {
        $result = $http_result->data;
    } else {
        $result = '';

        if ($trigger_error) {
            $arr = @json_decode($http_result->data, true);
            if (!empty($arr['errors'])) {
                $errormsg = 'Transifex error: ' . $arr['errors'][0]['detail'];
            } else {
                $errormsg = 'Transifex error: ' . $http_result->message;
            }
            require_code('failure');
            cms_error_log($errormsg, 'error_occurred_api');
            warn_exit($errormsg);
        }
    }

    if (is_cli()) {
        @print('Done call to ' . $url . ' [' . $http_result->message . ']' . "\n");
    }

    // Meet rate limit requirement
    /*
    Commented out because Transifex API is too slow for it to make a difference anyway!!
    if ($http_verb == 'GET') {
        usleep(200000);
    } else {
        usleep(500000);
    }*/

    return [$result, $http_result->message];
}

function _transifex_credentials()
{
    if (isset($_SERVER['argv'][1])) {
        $username = $_SERVER['argv'][1];
    } else {
        $username = get_param_string('username', get_value('transifex_username', null, true), INPUT_FILTER_GET_IDENTIFIER);
    }
    if (isset($_SERVER['argv'][2])) {
        $password = $_SERVER['argv'][2];
    } else {
        $password = get_param_string('password', get_value('transifex_password', null, true), INPUT_FILTER_PASSWORD);
    }

    if (cms_empty_safe($username)) {
        warn_exit(do_lang('API_NOT_CONFIGURED', 'Transifex') . ' Username must be set with :set_value(\'transifex_username\', \'...\', true); or passed as the first CLI parameter', true);
    }
    if (cms_empty_safe($password)) {
        warn_exit(do_lang('API_NOT_CONFIGURED', 'Transifex') . ' Transifex password must be set with :set_value(\'transifex_password\', \'...\', true); or passed as the second CLI parameter', true);
    }

    return [$username, $password];
}

function _transifex_env_limit_substring()
{
    if (isset($_SERVER['argv'][3])) {
        $limit_substring = $_SERVER['argv'][3];

        if (in_array($limit_substring, [ // These are actually env_settings
            'core_only',
            'push_cms',
            'push_ini',
            'push_translations',
            'output',
            'core_only',
        ])) {
            return null;
        }

        if (preg_match('#^[A-Z]{2}(_[a-z]{2})?$#', $limit_substring) != 0) { // This is a language
            return null;
        }

        if (preg_match('#^\d+\.\d+$#', $limit_substring) != 0) { // This is a version
            return null;
        }
    } else {
        $limit_substring = get_param_string('limit_substring', null, INPUT_FILTER_GET_COMPLEX);
    }
    return $limit_substring;
}

function _transifex_env_setting($setting)
{
    return ((isset($_SERVER['argv'])) && (in_array($setting, $_SERVER['argv']))) || (get_param_integer($setting, 0) == 1);
}

function _transifex_env_version()
{
    if (isset($_SERVER['argv'][3])) {
        for ($i = 3; $i < count($_SERVER['argv']); $i++) {
            if (preg_match('#^\d+\.\d+$#', $_SERVER['argv'][$i]) != 0) {
                return $_SERVER['argv'][$i];
            }
        }
    }

    return get_param_string('version', strval(cms_version()));
}

function _transifex_env_lang()
{
    if (isset($_SERVER['argv'][3])) {
        for ($i = 3; $i < count($_SERVER['argv']); $i++) {
            if (preg_match('#^[A-Z]{2}(_[a-z]{2})?$#', $_SERVER['argv'][$i]) != 0) {
                return $_SERVER['argv'][$i];
            }
        }
    }

    $lang = get_param_string('lang', null);
    if (($lang !== null) && (!does_lang_exist($lang))) {
        $lang = null;
    }
    return $lang;
}

function _transifex_decode_content($in)
{
    $out = $in;
    $out = str_replace('&quot;', '"', $out); // Transifex uses non-standard escaping within JSON
    $out = str_replace('\\\\', '\\', $out); // Transifex adds slashes around slashes
    return unixify_line_format($out);
}

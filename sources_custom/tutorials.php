<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_tutorials
 */

/*

Tags defined in .txt files...

pinned
document video audio slideshow book
novice regular expert
<names of addons>
Classifications, for which we have icons

Tags correspond also to icons, if one matches. Earliest match.

*/

function init__tutorials()
{
    define('TUTORIAL_VIRTUAL_FIELD__PAGE_NAME', 't_page_name');
}

function list_tutorial_tags($skip_addons_and_specials = false)
{
    $tags = [];
    $tutorials = list_tutorials();

    foreach ($tutorials as $tutorial) {
        foreach ($tutorial['tags'] as $tag) {
            if ($skip_addons_and_specials) {
                if (cms_mb_strtolower($tag) != $tag) {
                    $tags[] = $tag;
                }
            } else {
                $tags[] = $tag;
            }
        }
    }

    $tags = array_unique($tags);

    // We can't store mixed case in the database, let's just have one set of tags
    foreach ($tags as $tag) {
        if (preg_match('#^[A-Z]#', $tag) != 0) {
            $at = array_search(cms_mb_strtolower($tag), $tags);
            if ($at !== false) {
                unset($tags[$at]);
            }
        }
    }

    cms_mb_sort($tags, SORT_NATURAL | SORT_FLAG_CASE);
    return $tags;
}

function list_tutorials_by($criteria, $tag = null)
{
    $tutorials = null;

    switch ($criteria) {
        case 'pinned':
            $_tutorials = list_tutorials();
            shuffle($_tutorials);

            $tutorials = [];
            foreach ($_tutorials as $tutorial) {
                if ($tutorial['pinned']) {
                    $tutorials[] = $tutorial;
                }
            }

            break;

        case 'recent':
            $tutorials = list_tutorials();
            shuffle($tutorials);
            sort_maps_by($tutorials, '!add_date');
            break;

        case 'likes':
            $tutorials = list_tutorials();
            shuffle($tutorials);
            sort_maps_by($tutorials, '!likes');
            break;

        case 'likes_recent':
            $tutorials = list_tutorials();
            shuffle($tutorials);
            sort_maps_by($tutorials, '!likes_recent');
            break;

        case 'rating':
            $tutorials = list_tutorials();
            shuffle($tutorials);
            sort_maps_by($tutorials, '!rating');
            break;

        case 'rating_recent':
            $tutorials = list_tutorials();
            shuffle($tutorials);
            sort_maps_by($tutorials, '!rating_recent');
            break;

        case 'views':
            $tutorials = list_tutorials();
            shuffle($tutorials);
            sort_maps_by($tutorials, '!views');
            break;

        case 'title':
            $tutorials = list_tutorials();
            shuffle($tutorials);
            sort_maps_by($tutorials, 'title', false, true);
            break;
    }

    if ($tag !== null) {
        $_tutorials = $tutorials;

        $tutorials = [];
        foreach ($_tutorials as $tutorial) {
            if (in_array($tag, $tutorial['tags'])) {
                $tutorials[] = $tutorial;
            }
        }
    }

    return $tutorials;
}

function list_tutorials()
{
    $tutorials = [];

    $cache_path = get_custom_file_base() . '/uploads/website_specific/tutorial_sigs.bin';
    if ((is_file($cache_path)) && (filemtime($cache_path) > time() - 60 * 60/*1hr cache*/) && (get_param_integer('keep_tutorial_test', 0) == 0)) {
        return unserialize(cms_file_get_contents_safe($cache_path, FILE_READ_LOCK));
    }

    push_query_limiting(false);

    $_tags = $GLOBALS['SITE_DB']->query_select('tutorials_external_tags', ['t_id', 't_tag']);
    $external = $GLOBALS['SITE_DB']->query_select('tutorials_external t', ['t.*', tutorial_sql_rating(db_cast('t.id', 'CHAR')), tutorial_sql_rating_recent(db_cast('t.id', 'CHAR')), tutorial_sql_likes(db_cast('t.id', 'CHAR')), tutorial_sql_likes_recent(db_cast('t.id', 'CHAR'))]);
    foreach ($external as $e) {
        $tags = [];
        foreach ($_tags as $tag) {
            if ($tag['t_id'] == $e['id']) {
                $tags[] = $tag['t_tag'];
            }
        }

        $tutorials[] = get_tutorial_metadata(strval($e['id']), $e, $tags);
    }

    $internal = list_to_map('t_page_name', $GLOBALS['SITE_DB']->query_select('tutorials_internal t', ['t.*', tutorial_sql_rating(TUTORIAL_VIRTUAL_FIELD__PAGE_NAME), tutorial_sql_rating_recent(TUTORIAL_VIRTUAL_FIELD__PAGE_NAME), tutorial_sql_likes(TUTORIAL_VIRTUAL_FIELD__PAGE_NAME), tutorial_sql_likes_recent(TUTORIAL_VIRTUAL_FIELD__PAGE_NAME)]));
    $dh = opendir(get_file_base() . '/docs/pages/comcode_custom/EN');
    while (($f = readdir($dh)) !== false) {
        if (substr($f, -4) == '.txt' && $f != 'panel_top.txt') {
            $page_name = basename($f, '.txt');
            $tutorials[$page_name] = get_tutorial_metadata($page_name, isset($internal[$page_name]) ? $internal[$page_name] : false);
        }
    }
    closedir($dh);

    //sort_maps_by($tutorials, 'title', false, true);    Breaks keys

    require_code('files');
    cms_file_put_contents_safe($cache_path, serialize($tutorials), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);

    return $tutorials;
}

function templatify_tutorial_list($tutorials, $simple = false)
{
    $_tutorials = [];

    foreach ($tutorials as $metadata) {
        $_tutorials[] = templatify_tutorial($metadata, $simple);
    }

    return $_tutorials;
}

function templatify_tutorial($metadata, $simple = false)
{
    $tags = [];
    foreach ($metadata['tags'] as $tag) {
        if (cms_mb_strtolower($tag) != $tag) {
            $tags[] = $tag;
        }
    }

    $tutorial = [
        'NAME' => $metadata['name'],
        'URL' => $metadata['url'],
        'TITLE' => $metadata['title'],
        'ICON' => $metadata['icon'],
    ];
    if (!$simple) {
        require_code('feedback');

        $tutorial += [
            'SUMMARY' => $metadata['summary'],
            'TAGS' => $tags,
            'MEDIA_TYPE' => $metadata['media_type'],
            'DIFFICULTY_LEVEL' => $metadata['difficulty_level'],
            'CORE' => $metadata['core'],
            'AUTHOR' => $metadata['author'],
            'ADD_DATE' => get_timezoned_date($metadata['add_date'], false),
            'EDIT_DATE' => get_timezoned_date($metadata['edit_date'], false),
            'RATING_TPL' => display_rating($metadata['url'], $metadata['title'], 'tutorial', $metadata['name'], 'RATING_INLINE_DYNAMIC'),
        ];
    }

    return $tutorial;
}

function get_tutorial_metadata($tutorial_name, $db_row = null, $tags = null)
{
    if (is_numeric($tutorial_name)) {
        // From database

        if ($db_row === null) {
            $db_rows = $GLOBALS['SITE_DB']->query_select('tutorials_external t', ['t.*', tutorial_sql_rating('t.id'), tutorial_sql_rating_recent('t.id'), tutorial_sql_likes('t.id'), tutorial_sql_likes_recent('t.id')], ['id' => intval($tutorial_name)], '', 1);
            if (!isset($db_rows[0])) {
                warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
            }
            $db_row = $db_rows[0];
        }

        if ($tags === null) {
            $_tags = $GLOBALS['SITE_DB']->query_select('tutorials_external_tags', ['t_tag'], ['t_id' => intval($tutorial_name)]);
            $tags = collapse_1d_complexity('t_tag', $_tags);
        }

        $raw_tags = array_merge($tags, [$db_row['t_media_type']], [$db_row['t_difficulty_level']]);
        if ($db_row['t_pinned'] == 1) {
            $raw_tags[] = 'pinned';
        }

        return [
            'name' => $tutorial_name,

            'url' => $db_row['t_url'],
            'title' => $db_row['t_title'],
            'summary' => $db_row['t_summary'],
            'icon' => looks_like_url($db_row['t_icon']) ? $db_row['t_icon'] : find_tutorial_image($db_row['t_icon'], $raw_tags),
            'raw_tags' => $raw_tags,
            'tags' => $tags,
            'media_type' => $db_row['t_media_type'],
            'difficulty_level' => $db_row['t_difficulty_level'],
            'core' => false,
            'pinned' => $db_row['t_pinned'] == 1,
            'author' => $db_row['t_author'],
            'views' => $db_row['t_views'],
            'add_date' => $db_row['t_add_date'],
            'edit_date' => $db_row['t_edit_date'],

            'rating' => (($db_row['rating'] !== null) ? intval(round($db_row['rating'])) : null),
            'rating_recent' => (($db_row['rating_recent'] !== null) ? intval(round($db_row['rating_recent'])) : null),
            'likes' => (($db_row['likes'] !== null) ? intval(round($db_row['likes'])) : null),
            'likes_recent' => (($db_row['likes_recent'] !== null) ? intval(round($db_row['likes_recent'])) : null),
        ];
    }

    // From Git

    if ($db_row === null) {
        $db_rows = $GLOBALS['SITE_DB']->query_select('tutorials_internal t', ['t.*', tutorial_sql_rating(TUTORIAL_VIRTUAL_FIELD__PAGE_NAME), tutorial_sql_rating_recent(TUTORIAL_VIRTUAL_FIELD__PAGE_NAME), tutorial_sql_likes(TUTORIAL_VIRTUAL_FIELD__PAGE_NAME), tutorial_sql_likes_recent(TUTORIAL_VIRTUAL_FIELD__PAGE_NAME)], ['t_page_name' => $tutorial_name], '', 1);
        if (isset($db_rows[0])) {
            $db_row = $db_rows[0];
        } else {
            $db_row = false;
        }
    }

    if ($db_row === false) {
        $db_row = [
            't_page_name' => $tutorial_name,
            't_views' => 0,

            'rating' => null,
            'rating_recent' => null,
            'likes' => null,
            'likes_recent' => null,
        ];
        $GLOBALS['SITE_DB']->query_insert('tutorials_internal', [
            't_page_name' => $tutorial_name,
            't_views' => 0,
        ]);
    }

    $tutorial_path = get_file_base() . '/docs/pages/comcode_custom/EN/' . $tutorial_name . '.txt';
    $c = remove_code_block_contents(cms_file_get_contents_safe($tutorial_path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM));
    $matches = [];

    if (preg_match('#\[title sub="Written by ([^"]*)"\]([^\[\]]*)\[/title\]#', $c, $matches) != 0) {
        $title = preg_replace('#^Composr (Tutorial|Supplementary): #', '', $matches[2]);
        $author = $matches[1];
    } else {
        $title = '';
        $author = '';
    }

    if (preg_match('#\{\$SET,tutorial_tags,([^{}]*)\}#', $c, $matches) != 0) {
        $raw_tags = ($matches[1] == '') ? [] : explode(',', $matches[1]);
    } else {
        $raw_tags = [];
    }
    $tags = array_diff($raw_tags, ['document', 'video', 'audio', 'slideshow', 'book', 'novice', 'regular', 'expert', 'pinned']);

    if (preg_match('#\{\$SET,tutorial_summary,([^{}]*)\}#', $c, $matches) != 0) {
        $summary = $matches[1];
    } else {
        $summary = '';
    }

    if (preg_match('#\{\$SET,tutorial_add_date,([^{}]*)\}#', $c, $matches) != 0) {
        $add_date = strtotime($matches[1]);
    } else {
        $add_date = filectime($tutorial_path);
    }

    $url = build_url(['page' => $tutorial_name], '_SEARCH', [], false, false, true);

    $media_type = 'document';
    if (in_array('audio', $raw_tags)) {
        $media_type = 'audio';
    }
    if (in_array('video', $raw_tags)) {
        $media_type = 'video';
    }
    if (in_array('slideshow', $raw_tags)) {
        $media_type = 'slideshow';
    }
    if (in_array('audio', $raw_tags)) {
        $media_type = 'audio';
    }
    if (in_array('book', $raw_tags)) {
        $media_type = 'book';
    }
    $difficulty_level = in_array('expert', $raw_tags) ? 'expert' : (in_array('novice', $raw_tags) ? 'novice' : 'regular');

    return [
        'name' => $tutorial_name,

        'url' => static_evaluate_tempcode($url),
        'title' => $title,
        'summary' => $summary,
        'icon' => find_tutorial_image('', $raw_tags),
        'tags' => $tags,
        'raw_tags' => $raw_tags,
        'media_type' => $media_type,
        'difficulty_level' => $difficulty_level,
        'core' => (preg_match('#^sup_#', $tutorial_name) == 0),
        'pinned' => in_array('pinned', $raw_tags),
        'author' => $author,
        'views' => $db_row['t_views'],
        'add_date' => $add_date,
        'edit_date' => filemtime($tutorial_path),

        'rating' => $db_row['rating'],
        'rating_recent' => $db_row['rating_recent'],
        'likes' => $db_row['likes'],
        'likes_recent' => $db_row['likes_recent'],
    ];
}

function tutorial_sql_rating($field)
{
    return '(SELECT AVG(rating) FROM ' . get_table_prefix() . 'rating WHERE ' . db_string_equal_to('rating_for_type', 'tutorial') . ' AND rating_for_id=' . $field . ') AS rating';
}

function tutorial_sql_rating_recent($field)
{
    return '(SELECT AVG(rating) FROM ' . get_table_prefix() . 'rating WHERE ' . db_string_equal_to('rating_for_type', 'tutorial') . ' AND rating_for_id=' . $field . ' AND rating_time>' . strval(time() - 60 * 60 * 24 * 31) . ') AS rating_recent';
}

function tutorial_sql_likes($field)
{
    return '(SELECT COUNT(*) FROM ' . get_table_prefix() . 'rating WHERE ' . db_string_equal_to('rating_for_type', 'tutorial') . ' AND rating_for_id=' . $field . ' AND rating=10) AS likes';
}

function tutorial_sql_likes_recent($field)
{
    return '(SELECT COUNT(*) FROM ' . get_table_prefix() . 'rating WHERE ' . db_string_equal_to('rating_for_type', 'tutorial') . ' AND rating_for_id=' . $field . ' AND rating=10 AND rating_time>' . strval(time() - 60 * 60 * 24 * 31) . ') AS likes_recent';
}

function find_tutorial_image($icon, $tags, $get_theme_image = false)
{
    if ($icon != '') {
        $ret = find_theme_image($icon);
        if ($ret != '') {
            return $ret;
        }
    }

    foreach ($tags as $tag) {
        $theme_image = _find_tutorial_image_for_tag($tag);
        $img = find_theme_image($theme_image, true);
        if ($img != '') {
            if ($get_theme_image) {
                return $theme_image;
            }
            return $img;
        }
    }

    $theme_image = 'icons/spare/advice_and_guidance';
    $img = find_theme_image($theme_image);
    if ($get_theme_image) {
        return $theme_image;
    }
    return $img;
}

function _find_tutorial_image_for_tag($tag)
{
    $tag = str_replace(' ', '_', $tag);
    $tag = str_replace('+', '', $tag); // E.g. Wiki+
    $tag = str_replace('&', 'and', $tag);
    $tag = cms_strtolower_ascii($tag);

    switch ($tag) {
        case 'addon':
            return 'icons/menu/adminzone/structure/addons';
        case 'banners':
            return 'icons/menu/cms/banners';
        case 'calendar':
            return 'icons/menu/rich_content/calendar';
        case 'catalogues':
            return 'icons/menu/rich_content/catalogues/catalogues';
        case 'chatrooms':
            return 'icons/menu/social/chat/chat';
        case 'configuration':
            return 'icons/menu/adminzone/setup/config/config';
        case 'design_and_themeing':
            return 'icons/menu/adminzone/style';
        case 'downloads':
            return 'icons/menu/rich_content/downloads';
        case 'ecommerce':
            return 'icons/menu/adminzone/audit/ecommerce/ecommerce';
        case 'forum':
            return 'icons/buttons/forum';
        case 'galleries':
            return 'icons/menu/rich_content/galleries';
        case 'members':
            return 'icons/menu/social/members';
        case 'news':
            return 'icons/menu/rich_content/news';
        case 'newsletters':
            return 'icons/menu/site_meta/newsletters';
        case 'pages':
            return 'icons/menu/pages';
        case 'security':
            return 'icons/menu/adminzone/security';
        case 'support':
            return 'icons/help';
        case 'structure_and_navigation':
            return 'icons/menu/adminzone/structure';
        case 'upgrading':
            return 'icons/menu/adminzone/tools/upgrade';
        case 'wiki':
            return 'icons/menu/rich_content/wiki';
    }

    return 'icons/spare/' . $tag;
}

function remove_code_block_contents($code)
{
    $code = preg_replace('#(\[code=[^\[\]]*\]).*(\[/code\])#Us', '$1$2', $code);
    $code = preg_replace('#(\[codebox=[^\[\]]*\]).*(\[/codebox\])#Us', '$1$2', $code);
    return $code;
}

/**
 * Create an abbr tag explaining the given type.
 *
 * @param  ?ID_TEXT $type The type (null: none)
 * @param  ?boolean $nullable Whether this can be null (null: find out from the type)
 * @return string The HTML
 */
function get_api_type_tooltip(?string $type, ?bool $nullable = null) : string
{
    if ($type === null) {
        return '<em>N/A</em>';
    }

    $abbr_title = do_lang('API_DOC_TYPE__' . str_replace(['?', '~', '*'], ['', '', ''], $type), null, null, null, null, false);
    if ($abbr_title === null) {
        return $type;
    }

    $ret = '<abbr title="' . $abbr_title;
    if (($nullable === true) || (($nullable === null) && strpos($type, '?') !== false)) {
        $ret .= do_lang('API_DOC_TYPE_NULLABLE');
    }
    if (strpos($type, '~') !== false) {
        $ret .= do_lang('API_DOC_TYPE_FALSEABLE');
    }
    $ret .= '">' . $type . '</abbr>';
    return $ret;
}

function prepare_api_class_for_render(array $row) : array
{
    $class_implements = [];
    foreach (explode(',', $row['c_implements']) as $implements) {
        if ($implements == '') {
            continue;
        }
        $class_implements[] = hyperlink(build_url(['page' => 'api', 'type' => $implements], get_module_zone('api')), $implements, false, true);
    }

    $class_traits = [];
    foreach (explode(',', $row['c_traits']) as $trait) {
        if ($trait == '') {
            continue;
        }
        $class_traits[] = $trait;
    }

    $class_extends = null;
    if ($row['c_extends'] != '') {
        $class_extends = hyperlink(build_url(['page' => 'api', 'type' => $row['c_extends']], get_module_zone('api')), $row['c_extends'], false, true);
    }

    // PhpDoc

    $preview = '[code="PHP"]';

    // class declaration
    if ($row['c_is_abstract'] == 1) {
        $preview .= 'abstract ';
    }
    $preview .= 'class ' . $row['c_name'];

    // extends
    if ($row['c_extends'] != '') {
        $preview .= ' extends ' . $row['c_extends'];
    }

    // implements
    if (trim($row['c_implements']) != '') {
        $preview .= ' implements ' . str_replace(',', ', ', $row['c_implements']);
    }

    $preview .= "\n" . '[/code]';

    return [
        'PATH' => $row['c_source_url'],
        'IS_ABSTRACT' => ($row['c_is_abstract'] == 1) ? do_lang('YES') : do_lang('NO'),
        'IMPLEMENTS' => $class_implements,
        'TRAITS' => $class_traits,
        'EXTENDS' => $class_extends,
        'TYPE' => $row['c_type'],
        'PACKAGE' => $row['c_package'],
        'PREVIEW' => comcode_to_tempcode($preview),
    ];
}

/**
 * Give an API function row from the database and get a template-ready map of details about the function.
 *
 * @param  array $db_function The api_functions database row
 * @param  integer $i Parameter iteration, passed by reference
 * @return array Template-ready map of details
 */
function prepare_api_function_for_render(array $db_function, int &$i) : array
{
    require_code('templates');
    require_code('templates_results_table');
    require_lang('tutorials');

    $parameters = null;

    $preview_params_phpdoc = '';
    $preview_params = '';

    $start = get_param_integer('param_' . strval($i) . '_start', 0);
    $max = get_param_integer('param_' . strval($i) . '_start', 25);

    $count_db_parameters = $GLOBALS['SITE_DB']->query_select_value('api_function_params', 'COUNT(*)', ['function_id' => $db_function['id']]);
    $db_parameters = $GLOBALS['SITE_DB']->query_select('api_function_params', ['*'], ['function_id' => $db_function['id']], ' ORDER BY id', $max, $start);
    if ($count_db_parameters > 0) {
        $header = [
            do_lang_tempcode('NAME'),
            do_lang_tempcode('TYPE'),
            do_lang_tempcode('API_DOC_REF'),
            do_lang_tempcode('API_DOC_VARIADIC'),
            do_lang_tempcode('DEFAULT'),
            do_lang_tempcode('SET'),
            do_lang_tempcode('API_DOC_RANGE'),
            do_lang_tempcode('DESCRIPTION'),
        ];
        $header_row = results_header_row($header);

        $rows = new Tempcode();
        foreach ($db_parameters as $parameter) {
            $preview_params_phpdoc .= "\n" . ' * @param  ' . $parameter['p_type'] . ' $' . $parameter['p_name'] . ' ' . $parameter['p_description'];

            // We need to convey the default value in such a way we can differentiate between a literal value and something else
            $preview_param_default = '';
            if ($parameter['p_default'] != '') {
                $_param_default = unserialize($parameter['p_default']);
                if ($_param_default === false) {
                    $param_default = do_lang_tempcode('API_DOC_FALSE');
                    $preview_param_default = 'false';
                } elseif ($_param_default === true) {
                    $param_default = do_lang_tempcode('API_DOC_TRUE');
                    $preview_param_default = 'true';
                } elseif ($_param_default === null) {
                    $param_default = do_lang_tempcode('API_DOC_NULL');
                    $preview_param_default = 'null';
                } elseif (is_array($_param_default)) {
                    $param_default = protect_from_escaping(json_encode($_param_default, JSON_PRETTY_PRINT));
                    $preview_param_default = json_encode($_param_default);
                } elseif (is_object($_param_default)) {
                    $param_default = protect_from_escaping('<em>Object</em>');
                    $preview_param_default = 'object';
                } elseif (is_string($_param_default)) {
                    $param_default = protect_from_escaping(escape_html(strval($_param_default)));
                    $preview_param_default = "'" . str_replace("'", "\'", strval($_param_default)) . "'";
                } elseif (is_numeric($_param_default)) {
                    $param_default = protect_from_escaping(escape_html(strval($_param_default)));
                    $preview_param_default = strval($_param_default);
                }
                if ($param_default->is_empty()) {
                    $param_default = do_lang_tempcode('API_DOC_BLANK');
                }
            } else {
                $param_default = do_lang_tempcode('API_DOC_REQUIRED_PARAMETER');
            }

            if ($parameter['p_set'] != '') {
                $preview_params_phpdoc .= "\n" . ' * @set ' . $parameter['p_set'];
                $param_set = escape_html($parameter['p_set']);
            } else {
                $param_set = '<em>N/A</em>';
            }

            if ($parameter['p_range'] != '') {
                $preview_params_phpdoc .= "\n" . ' * @range ' . $parameter['p_range'];
                $param_range = escape_html($parameter['p_range']);
            } else {
                $param_range = '<em>N/A</em>';
            }

            $display_actual = '';
            if ($parameter['p_ref'] == 1) {
                $display_actual .= '&';
            }
            if ($parameter['p_is_variadic'] == 1) {
                $display_actual .= '...';
            }
            $display_actual .= '$' . $parameter['p_name'];

            $map = [
                escape_html($display_actual),
                get_api_type_tooltip($parameter['p_type']),
                ($parameter['p_ref'] == 1) ? do_lang('YES') : do_lang('NO'),
                ($parameter['p_is_variadic'] == 1) ? do_lang('YES') : do_lang('NO'),
                $param_default,
                $param_set,
                $param_range,
                escape_html($parameter['p_description']),
            ];
            $rows->attach(results_entry($map, false));

            if ($preview_params != '') {
                $preview_params .= ', ';
            }
            if ($parameter['p_php_type'] != '') {
                if ($parameter['p_php_type_nullable'] == 1) {
                    $preview_params .= '?';
                }
                $preview_params .= $parameter['p_php_type'] . ' ';
            }
            $preview_params .= $display_actual;
            if ($preview_param_default != '') {
                $preview_params .= ' = ' . $preview_param_default;
            }
        }

        $parameters = results_table(do_lang_tempcode('API_DOC_PARAMETERS'), $start, 'param_' . strval($i) . '_start', $max, 'param_' . strval($i) . '_max', $count_db_parameters, $header_row, $rows);
    }

    $function_flags = [];
    foreach (explode(',', $db_function['f_flags']) as $flag) {
        if ($flag == '') {
            continue;
        }

        $function_flags[] = $flag;
    }

    $map = [
        'PATH' => $GLOBALS['SITE_DB']->query_select_value('api_classes', 'c_source_url', ['id' => $db_function['class_id']]),
        'DESCRIPTION' => $db_function['f_description'],
        'RETURN_TYPE' => get_api_type_tooltip($db_function['f_php_return_type'], ($db_function['f_php_return_type_nullable'] == 1)),
        'FLAGS' => $function_flags,
        'IS_STATIC' => ($db_function['f_is_static'] == 1) ? do_lang('YES') : do_lang('NO'),
        'IS_ABSTRACT' => ($db_function['f_is_abstract'] == 1) ? do_lang('YES') : do_lang('NO'),
        'IS_FINAL' => ($db_function['f_is_final'] == 1) ? do_lang('YES') : do_lang('NO'),
        'VISIBILITY' => $db_function['f_visibility'],
        'PARAMETERS' => $parameters,
    ];

    if ($db_function['f_return_type'] != '') {
        $preview_params_phpdoc .= "\n" . ' * @return ' . $db_function['f_return_type'] . ' ' . $db_function['f_return_description'];

        if ($db_function['f_return_set'] != '') {
            $preview_params_phpdoc .= "\n" . ' * @set ' . $db_function['f_return_set'];
            $param_set = escape_html($db_function['f_return_set']);
        } else {
            $param_set = '<em>N/A</em>';
        }

        if ($db_function['f_return_range'] != '') {
            $preview_params_phpdoc .= "\n" . ' * @range ' . $db_function['f_return_range'];
            $param_range = escape_html($db_function['f_return_range']);
        } else {
            $param_range = '<em>N/A</em>';
        }

        $map['RETURN_TYPE_CMS'] = get_api_type_tooltip($db_function['f_return_type']);
        $map['RETURN_SET'] = $param_set;
        $map['RETURN_RANGE'] = $param_range;
        $map['RETURN_DESCRIPTION'] = $db_function['f_return_description'];
    } else {
        $map['RETURN_TYPE_CMS'] = null;
        $map['RETURN_SET'] = null;
        $map['RETURN_RANGE'] = null;
        $map['RETURN_DESCRIPTION'] = null;
    }

    $preview = '[code="PHP"]';

    // PhpDoc
    $preview .= "\n" . '/**';
    foreach (explode("\n", $db_function['f_description']) as $desc_line) {
        $preview .= "\n" . ' * ' . $desc_line;
    }
    $preview .= "\n" . ' *';
    $preview .= $preview_params_phpdoc;
    $preview .= "\n" . ' */' . "\n";

    // Function declaration
    if ($db_function['f_is_abstract'] == 1) {
        $preview .= 'abstract ';
    } elseif ($db_function['f_is_final'] == 1) {
        $preview .= 'final ';
    }
    if ($db_function['class_name'] != '__global') {
        $preview .= $db_function['f_visibility'] . ' ';
    }
    if ($db_function['f_is_static'] == 1) {
        $preview .= 'static ';
    }
    $preview .= 'function ' . $db_function['f_name'];

    // Parameters
    $preview .= '(' . $preview_params . ')';

    // Return
    if ($db_function['f_php_return_type'] != '') {
        $preview .= ' : ';
        if ($db_function['f_php_return_type_nullable'] == 1) {
            $preview .= '?';
        }
        $preview .= $db_function['f_php_return_type'];
    }

    $preview .= "\n" . '[/code]';
    $map['PREVIEW'] = comcode_to_tempcode($preview);

    $i++;

    return $map;
}

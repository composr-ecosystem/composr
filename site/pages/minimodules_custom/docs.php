<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    confluence
 */

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('confluence', $error_msg)) {
    return $error_msg;
}

load_csp(['csp_enabled' => '0']);

require_code('confluence');
require_css('confluence');

/*
If you want to host multiple spaces you can clone this module and add some extra code like the below.

global $CONFLUENCE_SPACE;
$CONFLUENCE_SPACE = 'EXAMPLE';
*/

// Special index, useful for debugging
if (get_param_string('type', '') == 'index') {
    return do_block('menu', ['type' => 'sitemap', 'param' => get_zone_name() . ':docs']);
}

list($content_type, $id, $posting_day) = confluence_current_page_id();

if ($content_type == 'page') {
    $query = 'content/' . strval($id) . '?expand=body.view,container';
    $full = confluence_query($query);
    if (isset($full['results'])) {
        $html = $full['results'][0]['body']['view']['value'];
    } else {
        $html = $full['body']['view']['value'];
    }

    $title = get_screen_title($full['title'], false);
} else {
    $query = 'content?expand=body.view,container,history&type=' . urlencode($content_type) . '&title=' . urlencode($id) . '&postingDay=' . urlencode($posting_day);
    $full = confluence_query($query);
    if (!isset($full['results'][0])) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    $html = $full['results'][0]['body']['view']['value'];

    $sub = null;
    if ($content_type == 'blogpost') {
        $sub = do_lang('BLOG_POST_BY', $full['results'][0]['history']['createdBy']['username']);
    }

    $title = get_screen_title($full['results'][0]['title'], false, [], null, [], true, $sub);
}


$html = confluence_clean_page($html);

$root_id = confluence_root_id();

$breadcrumbs = confluence_breadcrumbs($id);
if ($breadcrumbs !== null) {
    breadcrumb_set_parents($breadcrumbs);
}

return do_template('CONFLUENCE_SCREEN', [
    '_GUID' => '33a65e7f6832fac49cbb1f8e77a9c7b0',
    'TITLE' => $title,
    'HTML' => $html,
    'ROOT_ID' => strval($root_id),
    'BREADCRUMBS' => ($breadcrumbs === null) ? new Tempcode() : breadcrumb_segments_to_tempcode($breadcrumbs),
]);

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    confluence
 */

function handle_confluence_page_error_result($id, $result, $http_message, $http_message_b)
{
    if ($http_message == '404') {
        $title = get_screen_title('Could not find documentation page', false);
        $url = build_url(['page' => '_SELF'], '_SELF');
        attach_message('Could not find documentation page ' . confluence_current_page() . ', redirected to main page', 'notice');
        $tpl = redirect_screen($title, $url);
        $tpl->evaluate_echo();
        return;
    }

    warn_exit($http_message_b);
}

// We reference remote media, which Confluence often moves around - so we cannot trust static caching to not create broken images sporadically
global $INVALIDATED_FAST_SPIDER_CACHE;
$INVALIDATED_FAST_SPIDER_CACHE = true;

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

list($content_type, $id, $posting_day, $is_canonical_format) = confluence_current_page_id();

// We prefer slugs
if (!$is_canonical_format) {
    $url = build_confluence_id_url($id);
    set_http_status_code(301);
    header('Location: ' . $url->evaluate());
    exit();
}

// Canonical URL is just raw docs page for root
if (($content_type === null) && (confluence_current_page_raw() != '') && ($id === confluence_root_id())) {
    set_http_status_code(301);
    header('Location: ' . static_evaluate_tempcode(build_url(['page' => '_SELF'], '_SELF')));
    exit();
}

$http_message = null;
$http_message_b = null;

if ($posting_day !== null) {
    if ($content_type != 'blogpost') {
        warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
    }

    // Searches for a blog post. The only way we can reference these is via $blog_title+$posting_day query, as we cannot get IDs for direct querying through a confluence_get_mappings search
    $blog_title = $id;
    $query = 'content?expand=body.view,container,history&type=blogpost&title=' . urlencode($blog_title) . '&postingDay=' . urlencode($posting_day);
    $full = confluence_query($query, false, $http_message, $http_message_b);

    if (($full === null) || ($http_message == '404')) {
        handle_confluence_page_error_result($id, $full, $http_message, $http_message_b);
        return;
    }

    if (!isset($full['results'][0])) {
        warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
    }
    $result = $full['results'][0];
} else {
    // Search by ID
    $query = 'content/' . strval($id) . '?expand=body.view,container,history';
    $result = confluence_query($query, false, $http_message, $http_message_b);

    if (($result === null) || ($http_message == '404')) {
        handle_confluence_page_error_result($id, $result, $http_message, $http_message_b);
        return;
    }
}

$html_pre = null;

switch ($result['type']) {
    case 'blogpost':
        $sub = 'Blog post by ' . $result['history']['createdBy']['username'];
        $title = get_screen_title($result['title'], false, [], null, [], true, $sub);
        break;

    case 'attachment':
        $filename_pre = $result['title'];

        if (isset($result['_links']['download'])) {
            $url_pre = rewrite_confluence_url($result['_links']['download'], true);

            $attributes = [];
            if ((isset($result['metadata']['mediaType'])) && ($result['metadata']['mediaType'] == 'application/pdf')) {
                $attributes += ['width' => '1020', 'height' => '700'];
            }

            require_code('media_renderer');
            $html_pre = render_media_url($url_pre, $url_pre, $attributes, true, null, MEDIA_TYPE_ALL, is_mobile() ? 'download' : null, null, $filename_pre);
        }

        if ($result['container']['type'] == 'page') {
            // Render actual page normally underneath
            $query = 'content/' . strval($result['container']['id']) . '?expand=body.view,container';
            $result = confluence_query($query);

            $sub = $filename_pre;
            $title = get_screen_title($result['title'], false, [], null, [], true, $sub);
        } else {
            $title = get_screen_title($result['title'], false);
        }

        break;

    default:
        $title = get_screen_title($result['title'], false);
        break;
}

$html = $result['body']['view']['value'];
$html = confluence_clean_page($html);

$root_id = confluence_root_id();

$breadcrumbs = confluence_breadcrumbs($id);
if ($breadcrumbs !== null) {
    breadcrumb_set_parents($breadcrumbs);
}

return do_template('CONFLUENCE_SCREEN', [
    '_GUID' => '33a65e7f6832fac49cbb1f8e77a9c7b0',
    'TITLE' => $title,
    'HTML_PRE' => $html_pre,
    'HTML' => $html,
    'ROOT_ID' => strval($root_id),
    'BREADCRUMBS' => ($breadcrumbs === null) ? new Tempcode() : breadcrumb_segments_to_tempcode($breadcrumbs),
]);

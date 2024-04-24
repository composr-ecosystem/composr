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

function init__confluence()
{
    require_lang('confluence');

    global $CONFLUENCE_SUBDOMAIN, $CONFLUENCE_SPACE, $CONFLUENCE_USERNAME, $CONFLUENCE_PASSWORD, $CONFLUENCE_CACHE_TIME;
    $CONFLUENCE_SUBDOMAIN = get_option('confluence_subdomain');
    $CONFLUENCE_SPACE = get_option('confluence_space');
    if (($CONFLUENCE_SUBDOMAIN == '') || ($CONFLUENCE_SPACE == '')) {
        warn_exit(do_lang_tempcode('API_NOT_CONFIGURED', 'Confluence'));
    }
    $CONFLUENCE_USERNAME = get_option('confluence_username');
    $CONFLUENCE_PASSWORD = get_option('confluence_password');
    $CONFLUENCE_CACHE_TIME = intval(get_option('confluence_cache_time'));

    if (get_option('url_scheme') == 'RAW') {
        warn_exit('Confluence integration needs a URL scheme configured.');
    }
}

function get_confluence_base_url()
{
    global $CONFLUENCE_SUBDOMAIN;

    if (strpos($CONFLUENCE_SUBDOMAIN, '.') !== false) {
        $url = 'https://' . $CONFLUENCE_SUBDOMAIN;
    } else {
        $url = 'https://' . $CONFLUENCE_SUBDOMAIN . '.atlassian.net/wiki';
    }

    return $url;
}

function get_local_confluence_url($type = null)
{
    if ($type === null) {
        $_url = build_url(['page' => '_SELF'], '_SELF');
        $url = $_url->evaluate();
    } else {
        $_url = build_url(['page' => '_SELF', 'type' => 'xxxyyyzzz'], '_SELF');
        $url = $_url->evaluate();
        $url = str_replace('xxxyyyzzz', $type, $url);
    }

    return $url;
}

function confluence_current_page_id()
{
    $current_page = confluence_current_page();

    // Special case: Already a number
    if (is_numeric($current_page)) {
        $id = intval($current_page);
        $content_type = null; // Unknown. Probably 'page', but not guaranteed
        $posting_day = null;

        $mappings = list_to_map('id', confluence_get_mappings());

        return [$content_type, $id, $posting_day, $current_page == strval(confluence_root_id()) || !isset($mappings[$current_page])/*Only canonical if we cannot find a slug*/];
    }

    // Special case: Is a blog post (which confluence_get_mappings cannot find btw)
    $matches = [];
    if (preg_match('#^(\d\d\d\d)/(\d\d)/(\d\d)/(.*)$#', $current_page, $matches) != 0) {
        $id = $matches[4];
        $content_type = 'blogpost';
        $posting_day = $matches[1] . '-' . $matches[2] . '-' . $matches[3];

        return [$content_type, $id, $posting_day, true];
    }

    // Is a slug...

    $mappings = confluence_get_mappings();
    $mappings_by_complex_id = list_to_map('slug', $mappings);
    if (!isset($mappings_by_complex_id[$current_page])) {
        $mappings_by_complex_id = list_to_map('title', $mappings);
        if (!isset($mappings_by_complex_id[$current_page])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
    }

    $id = $mappings_by_complex_id[$current_page]['id'];
    $content_type = $mappings_by_complex_id[$current_page]['type'];
    $posting_day = null;

    return [$content_type, $id, $posting_day, true];
}

function confluence_current_page_raw()
{
    $current_url_path = $_SERVER['REQUEST_URI'];
    $current_url_path = _strip_url_path($current_url_path);

    $current_url_path = preg_replace('#%25([0-9A-F]{2})#', '%$1', $current_url_path); // Fix buggy user agents that do double-encoding

    $root_url_path = cms_parse_url_safe(get_local_confluence_url(), PHP_URL_PATH);
    $root_url_path = _strip_url_path($root_url_path);

    $current_page = substr($current_url_path, strlen($root_url_path));
    if (substr($current_page, 0, 1) == '/') {
        $current_page = cms_urldecode_post_process(urldecode(substr($current_page, 1)));
    }

    return $current_page;
}

function confluence_current_page()
{
    $current_page = confluence_current_page_raw();

    if ($current_page == '') {
        $current_page = strval(confluence_root_id());
    }

    return $current_page;
}

function confluence_root_id()
{
    static $root_id = null;
    if ($root_id !== null) {
        return $root_id;
    }

    global $CONFLUENCE_SPACE;
    $space = confluence_query('space?spaceKey=' . $CONFLUENCE_SPACE);
    if (!isset($space['results'][0])) {
        warn_exit('Could not find Confluence root page for the space');
    }
    $root_id = intval(preg_replace('#^/rest/api/content/#', '', $space['results'][0]['_expandable']['homepage']));
    return $root_id;
}

function _strip_url_path($path)
{
    return preg_replace('#(^/pg/|\.htm(\?.*)?$)#', '', $path);
}

function confluence_get_mappings()
{
    static $mappings = null;
    if ($mappings !== null) {
        return $mappings;
    }

    $mappings = [];

    global $CONFLUENCE_SPACE;
    $pages = confluence_query('content?spaceKey=' . $CONFLUENCE_SPACE . '&limit=1000000&expand=ancestors', false);
    if (empty($pages['results'])) {
        return $mappings; // Observed response if Confluence has some glitch
    }
    foreach ($pages['results'] as $page) {
        $id = $page['id'];
        $title = $page['title'];
        $type = $page['type'];
        $slug = substr($page['_links']['webui'], strlen('/display/' . $CONFLUENCE_SPACE . '/'));

        $parent_id = null;
        $parent_position = $page['extensions']['position'];
        foreach ($page['ancestors'] as $ancestor) {
            if ($ancestor['status'] == 'current') {
                $parent_id = $ancestor['id'];
                break;
            }
        }

        $url = get_local_confluence_url($slug);

        $mappings[$id] = [
            'id' => $id,
            'title' => $title,
            'type' => $type,

            'slug' => $slug,
            'url' => $url,

            'parent_id' => $parent_id,
            '_parent_position' => $parent_position,
            'children' => [],
        ];
    }

    foreach ($mappings as $id => $mapping) {
        $parent_id = $mapping['parent_id'];
        $parent_position = $mapping['_parent_position'];
        if ($parent_id !== null) {
            if (isset($mappings[$parent_id])) {
                if (isset($mappings[$parent_id]['children'][$parent_position])) {
                    $mappings[$parent_id]['children'][] = $id;
                } else {
                    $mappings[$parent_id]['children'][$parent_position] = $id;
                }
            }
        }

        unset($mappings[$id]['_parent_position']);
    }

    foreach ($mappings as $id => $mapping) {
        ksort($mapping['children']);
        $mappings[$id] = $mapping;
    }

    return $mappings;
}

function create_selection_list_confluence($selected_page_id = null, $under = null, $prefix = '')
{
    $mappings = confluence_get_mappings();

    if ($under === null) {
        $under = $mappings[confluence_root_id()];
    }

    $out = new Tempcode();

    foreach ($under['children'] as $child) {
        $out->attach('<option value="' . strval($child) . '"' . (($selected_page_id == $child) ? ' selected="selected"' : '') . '>' . escape_html($prefix . $mappings[$child]['title']) . '</option>'); // XHTMLXHTML
        $out->attach(create_selection_list_confluence($selected_page_id, $mappings[$child], $prefix . $mappings[$child]['title'] . ' > '));
    }

    return $out;
}

/**
 * Get a formatted HTML string of the route back to the specified root, from the specified category.
 *
 * @param  AUTO_LINK $page_id The page we are finding for
 * @param  boolean $no_link_for_me_sir Whether to include category links at this level (the recursed levels will always contain links - the top level is optional, hence this parameter)
 * @return ?array The breadcrumb segments (null: lost)
 */
function confluence_breadcrumbs(int $page_id, bool $no_link_for_me_sir = true) : ?array
{
    $mappings = confluence_get_mappings();

    $zone = get_page_zone('docs');

    $map = _build_confluence_id_url($page_id);
    $page_link = build_page_link($map, $zone);

    if (!array_key_exists($page_id, $mappings)) {
        return null;
    }

    $title = $mappings[$page_id]['title'];

    if ($page_id == confluence_root_id()) {
        if ($no_link_for_me_sir) {
            return [];
        }
        return [[$page_link, $title]];
    }

    $segments = [];
    if (!$no_link_for_me_sir) {
        $segments[] = [$page_link, $title];
    }

    $below = confluence_breadcrumbs(intval($mappings[$page_id]['parent_id']), false);

    if ($below === null) {
        return $segments;
    }
    return array_merge($below, $segments);
}

function _build_confluence_id_url($id)
{
    $mappings = confluence_get_mappings();
    $mappings_by_complex_id = list_to_map('id', $mappings);

    if (isset($mappings_by_complex_id[$id])) {
        $slug = urldecode($mappings_by_complex_id[$id]['slug']);
        $map = ['page' => 'docs', 'type' => $slug];
    } else {
        $map = ['page' => 'docs', 'type' => $id];
    }
    return $map;
}

function build_confluence_id_url($id)
{
    $map = _build_confluence_id_url($id);
    return build_url($map, get_page_zone('docs'));
}

function confluence_clean_page($html)
{
    // Fix embedded images, which may need httpauth via our proxy
    $html = preg_replace('#\ssrc="([^"]+)"#', ' src="' . find_script('confluence_proxy') . '?$1"', $html);

    // Fix internal links, which should be served under our docs module
    $html = preg_replace_callback('#\shref="\s*([^"]+)"#', function ($matches) {
        $url = rewrite_confluence_url(html_entity_decode($matches[1], ENT_QUOTES, get_charset()));
        return ' href="' . escape_html($url) . '"';
    }, $html);

    // Strip out links to usernames
    $html = preg_replace('#<a [^<>]*href="' . preg_quote(get_confluence_base_url(), '#') . '/display/~[^<>]*>(.*)</a>#Us', '$1', $html);

    // Strip out some special links that should not be there
    $html = preg_replace('#<a\s[^<>]*>(Edit|Show More)</a>#Us', '', $html);

    // Remove HTML tags we should not have
    $tags_to_remove = [
        'html' => false,
        'head' => false,
        'title' => true,
        'base' => true,
        'body' => false,
    ];
    foreach ($tags_to_remove as $tag_to_remove => $strip_contents) {
        $html = preg_replace('#<' . $tag_to_remove . '(\s[^<>]*)?' . '>(.*)</' . $tag_to_remove . '>#Us', $strip_contents ? '' : '$2', $html);
        $html = preg_replace('#<' . $tag_to_remove . '(\s[^<>]*)?/>#Us', '', $html);
    }

    // Add missing CSS classes and IDs that the real site has, but Confluence misses out
    $html = '<div id="content" class="page view"><div id="main-content" class="wiki-content">' . $html . '</div></div>';

    // Excessive blank lines
    $html = preg_replace('#(<p>\s*(<br\s*/>|&nbsp;)*\s*</p>)+#s', '<p><br/>', $html);

    // More table styles
    $html = preg_replace_callback('#(<div class="table-wrap)(">\s*<table[^<>]*>)#s', function ($matches) {
        if (strpos($matches[2], 'width:') === false || true) {
            return $matches[1] . ' table-wrap-simple' . $matches[2];
        }
        return $matches[0];
    }, $html);

    // Fix incorrect defined header
    $tag_regexp = '(</?(p|div|code|strong|em|a|br)[^<>]*>)';
    $html = preg_replace('#(</colgroup>\s*|<table[^<>]*>\s*)<tbody( style="[^"]*")?' . '>((\s*<tr[^<>]*>(\s*<th[^<>]*>[^<>]*' . $tag_regexp . '*[^<>]*' . $tag_regexp . '*[^<>]*</th>)+\s*</tr>)+)#s', '$1<thead>$3</thead><tbody>', $html);

    // Responsive tables
    do { // We have to loop as our regex doesn't handle nested tables well
        $html_before = $html;
        $html = preg_replace_callback('#(<table class="((wrapped |relative-table )*)confluenceTable)("[^<>]*>(\s*<colgroup>.*</colgroup>)?\s*<thead.*</table>)#Us', function ($matches) {
            // ^ The last ".*</table>" bit is so we can detect the colspans

            if (strpos(str_replace('colspan="1"', '', $matches[0]), 'colspan="') !== false) { // colspan will screw up responsive tables
                return $matches[0];
            }

            return $matches[1] . ' responsive-table' . $matches[4];
        }, $html);
    } while ($html != $html_before);

    // Clickable images so to allow zoom on mobile
    if (is_mobile()) {
        $html = preg_replace_callback('#(<img [^<>]*class="[^"]*(confluence-embedded-image|gliffy-image)[^"]*"[^<>]* src=")([^"]*)("[^<>]*>)#s', function ($matches) use ($html) {
            $cleaned_leadup = preg_replace('#<a [^<>]*>.*</a>#U', '', substr($html, 0, strpos($html, $matches[0])));
            if (strpos($cleaned_leadup, '<a ') !== false) { // We're inside a link already
                return $matches[0];
            }

            return '<a href="' . $matches[3] . '">' . $matches[1] . $matches[3] . $matches[4] . '</a>';
        }, $html);
    }

    return $html;
}

function rewrite_confluence_url($url, $force_general_proxy = false)
{
    $url = qualify_url($url, get_confluence_base_url());
    $url = str_replace('http://', 'https://', $url);

    // These are being proxied
    global $CONFLUENCE_SPACE;
    $stub_stem_slug = get_confluence_base_url() . '/display/' . $CONFLUENCE_SPACE . '/';
    $stub_stem_raw = get_confluence_base_url() . '/pages/viewpage.action?pageId=';
    $stub_stems = [
        $stub_stem_slug,
        $stub_stem_raw,
    ];
    foreach ($stub_stems as $stub_stem) {
        if (substr($url, 0, strlen($stub_stem)) == $stub_stem) {
            $localised_url = get_local_confluence_url(substr($url, strlen($stub_stem)));
            return $localised_url;
        }
    }

    if ($force_general_proxy) {
        return find_script('external_url_proxy') . '?url=' . urlencode($url);
    }

    // These have to be done remotely, we're not proxying these kinds of pages
    return $url;
}

function confluence_query($query, $trigger_error = true, &$http_message = null, &$http_message_b = null)
{
    $url = get_confluence_base_url() . '/rest/api/' . $query;
    list($json, , , , $http_message, $http_message_b) = confluence_call_url($url, $trigger_error, true);

    if (empty($json)) {
        if (!$trigger_error) {
            return null;
        }

        warn_exit('Internal error processing query ' . $url);
    }

    $ret = @json_decode($json, true);

    if (!is_array($ret)) {
        if (!$trigger_error) {
            return null;
        }

        require_code('failure');
        cms_error_log('Confluence: ERROR ' . $json . ' @ ' . $url, 'error_occurred_api');

        warn_exit('Internal error processing query ' . $url);
    }

    return $ret;
}

function confluence_call_url($url, $trigger_error = true, $text = false)
{
    global $CONFLUENCE_USERNAME, $CONFLUENCE_PASSWORD;
    if (($CONFLUENCE_USERNAME == '') || (substr($url, 0, strlen(get_confluence_base_url() . '/')) != get_confluence_base_url() . '/')) {
        $auth = null;
    } else {
        $auth = [$CONFLUENCE_USERNAME, $CONFLUENCE_PASSWORD];
    }

    global $CONFLUENCE_CACHE_TIME;
    $options = ['trigger_error' => false, 'ignore_http_status' => true];
    if ($auth !== null) {
        $options['auth'] = $auth;
    }
    if ($text) {
        $options['convert_to_internal_encoding'] = true;
    }

    require_code('http');
    $ret = cache_and_carry('cms_http_request', [$url, $options], $CONFLUENCE_CACHE_TIME);

    if ($ret[0] == '') { // Revert to cache if we could not get a response
        if ($trigger_error) {
            $options['trigger_error'] = true; // Set trigger_error to true now though, as if there's no caching then we want http_download_file to be called and die this time
            $ret = cache_and_carry('cms_http_request', [$url, $options]);
        }
    }

    return $ret;
}

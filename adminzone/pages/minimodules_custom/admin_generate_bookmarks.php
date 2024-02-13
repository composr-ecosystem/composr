<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    browser_bookmarks
 */

/*
Implementation notes...

The output HTML is a mess, but it has to be.
If you try and put p's in the right place, or close tags, Chrome won't import the bookmarks.
p's go before and after each dl tag, and are not associated with dt tags.
Neither dl nor dt tags should close.
Folders can't themselves be links, so a node may have both a link and a separate folder (if it has children).
*/

i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

$error_msg = new Tempcode();
if (!addon_installed__messaged('browser_bookmarks', $error_msg)) {
    return $error_msg;
}

if (post_param_integer('confirm', 0) == 0) {
    $preview = 'Generate bookmarks';
    $title = get_screen_title($preview, false);
    $url = get_self_url(false, false);
    return do_template('CONFIRM_SCREEN', ['_GUID' => '482f08734460fc2a48ce19d19127f40f', 'TITLE' => $title, 'PREVIEW' => $preview, 'FIELDS' => form_input_hidden('confirm', '1'), 'URL' => $url]);
}

if (get_param_integer('debug', 0) != 1) {
    header('Content-Type: text/html; charset=' . get_charset());
    header('Content-Disposition: attachment; filename="bookmarks.html"');
}

$site_name = escape_html(get_site_name());

cms_ini_set('ocproducts.xss_detect', '0');

echo <<<END
<!DOCTYPE NETSCAPE-Bookmark-file-1>
<!-- This is an automatically generated file.
     It will be read and overwritten.
     DO NOT EDIT! -->
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<TITLE>Bookmarks</TITLE>
<H1>Bookmarks Menu</H1>
<DL><p>
  <DT><H3>{$site_name}</H3>
  <DL><p>
END;

require_code('sitemap');

$root = retrieve_sitemap_node(
    '',
    null,
    ['root', 'zone', 'page_grouping', 'page', 'comcode_page'],
    null,
    null,
    SITEMAP_GEN_CHECK_PERMS,
    '_SEARCH'
);

if (isset($root['children'])) {
    foreach ($root['children'] as $child) {
        bookmarks_process_node($child);
    }
}

function bookmarks_process_node($node)
{
    if ($node['page_link'] !== null) {
        $url = page_link_to_url($node['page_link'], true);
    } else {
        $url = $node['url'];
    }
    $title = $node['title']->evaluate();
    if ($url !== null) {
        echo '<DT><A HREF="' . escape_html($url) . '">' . escape_html($title) . '</A>' . "\n";
    }

    if (!empty($node['children'])) {
        echo '<DT><H3>' . escape_html($title) . '</H3>' . "\n";
        echo '<DL><p>' . "\n";
        foreach ($node['children'] as $child) {
            bookmarks_process_node($child);
        }
        echo '</DL><p>' . "\n";
    }
}

$GLOBALS['SCREEN_TEMPLATE_CALLED'] = '';
exit();

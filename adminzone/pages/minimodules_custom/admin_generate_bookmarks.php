<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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

if (get_param_integer('debug', 0) != 1) {
    header('Content-type: text/html; charset=' . get_charset());
    header('Content-Disposition: attachment; filename="bookmarks.html"');
}

$site_name = escape_html(get_site_name());

safe_ini_set('ocproducts.xss_detect', '0');

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
    /*$page_link=*/'',
    /*$callback=*/null,
    /*$valid_node_types=*/array('root', 'zone', 'page_grouping', 'page', 'comcode_page'),
    /*$child_cutoff=*/null,
    /*$max_recurse_depth=*/null,
    /*$options=*/SITEMAP_GEN_CHECK_PERMS,
    /*$zone=*/'_SEARCH'
);

if (isset($root['children'])) {
    foreach ($root['children'] as $child) {
        bookmarks_process_node($child);
    }
}

function bookmarks_process_node($node)
{
    if (!is_null($node['page_link'])) {
        list($zone, $attributes, $hash) = page_link_decode($node['page_link']);
        $url = _build_url($attributes, $zone, null, false, false, true, $hash);
    } else {
        $url = $node['url'];
    }
    $title = $node['title']->evaluate();
    if (!is_null($url)) {
        echo '<DT><A HREF="' . escape_html($url) . '">' . escape_html($title) . '</A>' . "\n";
    }

    if ((isset($node['children'])) && (count($node['children']) > 0)) {
        echo '<DT><H3>' . escape_html($title) . '</H3>' . "\n";
        echo '<DL><p>' . "\n";
        foreach ($node['children'] as $child) {
            bookmarks_process_node($child);
        }
        echo '</DL><p>' . "\n";
    }
}

exit();

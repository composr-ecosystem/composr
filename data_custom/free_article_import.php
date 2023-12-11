<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    free_article_import
 */

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $FILE_BASE = $_SERVER['SCRIPT_FILENAME']; // this is with symlinks-unresolved (__FILE__ has them resolved); we need as we may want to allow zones to be symlinked into the base directory without getting path-resolved
    $FILE_BASE = dirname($FILE_BASE);
    if (!is_file($FILE_BASE . '/sources/global.php')) {
        $RELATIVE_PATH = basename($FILE_BASE);
        $FILE_BASE = dirname($FILE_BASE);
    } else {
        $RELATIVE_PATH = '';
    }
}
@chdir($FILE_BASE);

global $NON_PAGE_SCRIPT;
$NON_PAGE_SCRIPT = true;
global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . chr(10) . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (php_function_allowed('set_time_limit')) {
    @set_time_limit(0);
}

require_code('news');
require_code('news2');
require_code('seo2');

if (!$GLOBALS['FORUM_DRIVER']->is_super_admin(get_member())) {
    access_denied();
}

// Create default news categories
$categories_path = get_custom_file_base() . '/data_custom/free_article_import__categories.txt';
$categories_default = is_file($categories_path) ? explode("\n", file_get_contents($categories_path)) : array();
$categories_existing = collapse_2d_complexity('id', 'nc_title', $GLOBALS['SITE_DB']->query_select('news_categories', array('id', 'nc_title')));
foreach ($categories_existing as $id => $nc_title) {
    $categories_existing[$id] = get_translated_text($nc_title);
}
foreach ($categories_default as $category) {
    $category = trim($category);
    if ($category == '') {
        continue;
    }

    if (!in_array($category, $categories_existing)) {
        $id = add_news_category($category, '', '');
        $categories_existing[$id] = $category;

        require_code('permissions2');
        set_global_category_access('news', $id);
    }
}

// Import news
$done = 0;
$csvfile = fopen(get_custom_file_base() . '/data_custom/free_article_import__articles.csv', 'rt');
fgetcsv($csvfile, 1024000); // Skip header row
while (($r = fgetcsv($csvfile, 1024000)) !== false) {
    $url = $r[1];

    if ($r[5] == '') {
        $parsed_url = parse_url($url);
        switch ($parsed_url['host']) {
            case 'ezinearticles.com':
                $r = parse_ezinearticles($r);
                break;
            case 'www.articlesbase.com':
                $r = parse_articlesbase($r);
                break;
            case 'www.articletrader.com':
                $r = parse_articletrader($r);
                break;
            default:
                warn_exit('No screen-scraping code written for articles on ' . $parsed_url['host']);
        }
    }

    if ((empty($r[0])) || (empty($r[2])) || (empty($r[3])) || (empty($r[5]))) {
        warn_exit('Failed to get full data for ' . $url);
    }

    $main_news_category = array_search($r[0], $categories_existing);
    if ($main_news_category === false) {
        $id = add_news_category($r[0], '', '');
        $categories_existing[$id] = $r[0];
        $main_news_category = $id;

        require_code('permissions2');
        set_global_category_access('news', $id);
    }
    $author = trim($r[2]);
    $title = trim($r[3]);
    $time = ($r[4] == '') ? time() : strtotime($r[4]);
    $r[5] = trim($r[5]);
    $r[5] = cms_preg_replace_safe('#.*<body[^<>]*>\s*#si', '', $r[5]);
    $r[5] = cms_preg_replace_safe('#\s*<h1[^<>]*>[^<>]*</h1>\s*#si', '', $r[5]);
    $r[5] = cms_preg_replace_safe('#\s*</body>\s*</html>#si', '', $r[5]);
    $news_article = '[html]' . $r[5] . '[/html]';
    $news = empty($r[6]) ? '' : $r[6]; // Summary

    $test = $GLOBALS['SITE_DB']->query_select_value_if_there('news', 'id', array($GLOBALS['SITE_DB']->translate_field_ref('title') => $title, 'date_and_time' => $time));
    if (is_null($test)) { // If does not exist yet
        $id = add_news($title, $news, $author, 1, 1, 1, 1, '', $news_article, $main_news_category, null, $time);
        seo_meta_set_for_explicit('news', strval($id), $r[7], $news);

        $done++;
    }
}
fclose($csvfile);

@header('Content-type: text/plain; charset=' . get_charset());
echo 'Imported ' . integer_format($done) . ' news articles.';

function parse_ezinearticles($r)
{
    // NB: You'll get security errors on this occasionally. You need to open up the URL manually, solve the CAPTCHA, then refresh.
    // The inbuilt cache will ensure the script can get to the end of the process.

    $f = http_download_file_cached($r[1]);

    $matches = array();
    preg_match('#&id=(\d+)#s', $r[1], $matches);
    $id = $matches[1];

    $matches = array();
    preg_match('#Submitted On (.*)\.#Us', $f, $matches);
    $date = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    $matches = array();
    cms_preg_match_safe('#<a href="[^"]*" rel="author" class="author-name" title="[^"]*">\s*(.*)\s*</a>#Us', $f, $matches);
    $author = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    $matches = array();
    preg_match('#<h1>(.*)</h1>#Us', $f, $matches);
    $title = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    $f = http_download_file_cached('http://ezinearticles.com/ezinepublisher/?id=' . urlencode($id), $r[1]);

    $matches = array();
    preg_match('#<textarea id="formatted-article" wrap="physical" style="width:98%;height:200px;" readonly>(.*)</textarea>#Us', $f, $matches);
    $body = $matches[1];
    $body = cms_preg_replace_safe('#.*<body[^<>]*>\s*#si', '', $body);
    $body = cms_preg_replace_safe('#\s*<h1[^<>]*>[^<>]*</h1>\s*#si', '', $body);
    $body = cms_preg_replace_safe('#\s*</body>\s*</html>#si', '', $body);
    $body = cms_preg_replace_safe('#^\s*<p>.*<br>\s*By .*</p>#U', '', $body);

    $matches = array();
    preg_match('#<textarea rows="5" id="article-summary" cols="50" wrap="physical" readonly>(.*)</textarea>#Us', $f, $matches);
    $summary = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    $matches = array();
    preg_match('#<input type="text" id="article-keywords" size="75" value="([^"]*)" readonly>#Us', $f, $matches);
    $keywords = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    return array(
        $r[0], // Category
        $r[1], // URL
        $author,
        $title,
        $date,
        $body,
        $summary,
        $keywords,
    );
}

function parse_articlesbase($r)
{
    $cookies = array_map('urldecode', array(
        'SPSI' => '8209dce6e2947e79c6bf67fb7022ad39',
    ));

    $f = http_download_file_cached($r[1], $r[1], $cookies);

    $matches = array();
    preg_match('#-(\d+)\.html$#Us', $r[1], $matches);
    $id = $matches[1];

    $matches = array();
    preg_match('#<span class="date">(.*)</span>#Us', $f, $matches);
    $date = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    $matches = array();
    if ((cms_preg_match_safe('#rel="author" itemprop="author">.*</a>\s*</strong>\s*<p>(.*) is #Us', $f, $matches) != 0) && (strlen($matches[1]) < 20)) {
        $author = html_entity_decode($matches[1], ENT_QUOTES, get_charset());
    } else {
        preg_match('#rel="author" itemprop="author">(.*)</a>#Us', $f, $matches);
        $author = html_entity_decode($matches[1], ENT_QUOTES, get_charset());
    }

    $matches = array();
    preg_match('#<h1 class="atitle" itemprop="name">(.*)</h1>#Us', $f, $matches);
    $title = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    $f = http_download_file_cached('http://www.articlesbase.com/ezine/' . $id, $r[1], $cookies);

    $matches = array();
    preg_match('#<textarea id="ezine_html" onclick="\$\(this\).select\(\)">(.*)</textarea>#Us', $f, $matches);
    $body = $matches[1];
    $body = cms_preg_replace_safe('#\s*<h1[^<>]*>[^<>]*</h1>\s*#si', '', $body);
    $body = cms_preg_replace_safe('#^\s*<strong>Author: .*</strong><br />#U', '', $body);

    $matches = array();
    preg_match('#<textarea class="summary" id="ezine_summary">(.*)</textarea>#Us', $f, $matches);
    $summary = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    $matches = array();
    preg_match('#<input type="text" value="([^"]*)" />#Us', $f, $matches);
    $keywords = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    return array(
        $r[0], // Category
        $r[1], // URL
        $author,
        $title,
        $date,
        $body,
        $summary,
        $keywords,
    );
}

function parse_articletrader($r)
{
    $f = http_download_file_cached($r[1]);

    $matches = array();
    preg_match('#<a rel="nofollow" href=\'([^\']*)\'>Get Html Code</a>#Us', $f, $matches);
    $synd_url = $matches[1];

    $matches = array();
    cms_preg_match_safe("#<div style='font-size:80%;margin-top:0px'>Submitted by <a href='[^']*'>(.*)</a><br>\s*(.*)</div>#Us", $f, $matches);
    $author = html_entity_decode($matches[1], ENT_QUOTES, get_charset());
    $date = html_entity_decode($matches[2], ENT_QUOTES, get_charset());

    $matches = array();
    preg_match('#<h1 style="margin-bottom:3px">(.*)</h1>#Us', $f, $matches);
    $title = html_entity_decode($matches[1], ENT_QUOTES, get_charset());

    $f = http_download_file_cached('http://www.articletrader.com' . $synd_url, $r[1]);

    $matches = array();
    preg_match('#<textarea style="width:99%" rows=30>(.*)</textarea>#Us', $f, $matches);
    $body = html_entity_decode($matches[1], ENT_QUOTES, get_charset());
    $body = str_replace("\n", '<br />', $body);

    $summary = '';

    $keywords = '';

    return array(
        $r[0], // Category
        $r[1], // URL
        $author,
        $title,
        $date,
        $body,
        $summary,
        $keywords,
    );
}

function http_download_file_cached($url, $referer = '', $cookies = null)
{
    require_code('files');
    $dir = get_custom_file_base() . '/data_custom/free_article_import_cache';
    if (@mkdir($dir, 0777) !== false) {
        fix_permissions($dir);
    }
    $cache_file = get_custom_file_base() . '/data_custom/free_article_import_cache/' . md5($url) . '.htm';
    if (is_file($cache_file)) {
        $data = file_get_contents($cache_file);
    } else {
        if (php_function_allowed('usleep')) {
            usleep(3000000);
        }

        require_code('files');
        $data = http_download_file($url, null, true, false, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36', null, $cookies, 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', null, 'en-US,en;q=0.8', null, $referer);
        cms_file_put_contents_safe($cache_file, $data, FILE_WRITE_FIX_PERMISSIONS);
    }
    return $data;
}

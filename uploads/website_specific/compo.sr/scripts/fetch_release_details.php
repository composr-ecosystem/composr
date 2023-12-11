<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 You may not distribute a modified version of this file, unless it is solely as a Composr modification.
 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = realpath(__FILE__);
$deep = 'uploads/website_specific/compo.sr/scripts/';
$FILE_BASE = str_replace($deep, '', $FILE_BASE);
$FILE_BASE = str_replace(str_replace('/', '\\', $deep), '', $FILE_BASE);
if (substr($FILE_BASE, -4) == '.php') {
    $a = strrpos($FILE_BASE, '/');
    $b = strrpos($FILE_BASE, '\\');
    $FILE_BASE = dirname($FILE_BASE);
}
$RELATIVE_PATH = '';
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = true;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<html><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

$news_id = get_param_integer('news_id');

header('Content-type: text/plain; charset=' . get_charset());

$news_rows = $GLOBALS['SITE_DB']->query_select('news', array('*'), array('validated' => 1, 'id' => $news_id), '', 1);
if ((array_key_exists(0, $news_rows)) && (has_category_access($GLOBALS['FORUM_DRIVER']->get_guest_id(), 'news', $news_rows[0]['news_category']))) {
    $_news_html = get_translated_tempcode('news', $news_rows[0], 'news_article'); // To force it to evaluate, so we can know the TAR URL
    $news_html = $_news_html->evaluate();
    $news = static_evaluate_tempcode(comcode_to_tempcode(get_translated_text($news_rows[0]['news_article']), null, true));

    $matches = array();
    preg_match('#"(https?://compo.sr/upgrades/[^"]*\.cms)"#', $news_html, $matches);
    $tar_url = array_key_exists(1, $matches) ? $matches[1] : '';
    $changes = '';
    if (preg_match('#<br />([^>]*the following.*:<br /><ul>)#U', $news_html, $matches) != 0) {
        $offset = strpos($news_html, $matches[1]);
        $changes = substr($news_html, $offset, strrpos($news_html, '</ul>') - $offset + 5);
        $news_html = substr($news_html, 0, $offset);
    }
    $news_html = preg_replace('#To upgrade follow.*during step 3.#s', '', $news_html);
    $news_html = preg_replace('#(<div[^>]*>[\s\n]*)+<h4[^>]*>Your upgrade to version.*</form>([\s\n]*</div>)+#s', '', $news_html);
    $news_html = preg_replace('#(<div[^>]*>[\s\n]*)+<h4[^>]*>Your upgrade to version.*download upgrade directly</a>\s+\([^\)]*\)\.([\s\n]*</div>)+#s', '', $news_html);
    $news_html = preg_replace('#<a class="hide_button" href="\#" onclick="event\.returnValue=false; hideTag\(this\.parentNode\.parentNode\); return false;"><img alt="Expand" title="Expand" src="' . escape_html(find_theme_image('1x/trays/expand')) . '" srcset="' . escape_html(find_theme_image('2x/trays/expand')) . ' 2x" /></a>#', '', $news_html);
    $news_html = preg_replace('#(\s*<br />)+#', '<br />', $news_html);
    $news_html = str_replace('display: none', 'display: block', $news_html);
    $notes = $news_html;

    echo serialize(array($notes, $tar_url, $changes));
} else {
    echo serialize(array('', '', ''));
}

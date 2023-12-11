<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

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

$version = get_param_string('version'); // This is a 'pretty' version number, rather than a 'dotted' one

$id_float = floatval($version);
do {
    $str = 'Version ' . /*preg_replace('#\.0$#', '', */float_to_raw_string($id_float, 1)/*)*/
    ;
    $_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_categories', 'id', array('parent_id' => 3, $GLOBALS['SITE_DB']->translate_field_ref('category') => $str));
    if (is_null($_id)) {
        $id_float -= 0.1;
    }
} while ((is_null($_id)) && ($id_float >= 0.0));

if (is_null($_id)) {
    header('Content-type: text/plain; charset=' . get_charset());
    exit();
}

require_code('selectcode');

$addon_times = array();

$filter_sql = selectcode_to_sqlfragment(strval($_id) . '*', 'id', 'download_categories', 'parent_id', 'category_id', 'id');

foreach (array_keys($_GET) as $x) {
    if (substr($x, 0, 6) == 'addon_') {
        $addon_name = get_param_string($x);
        $query = 'SELECT d.id,url,name FROM ' . get_table_prefix() . 'download_downloads d WHERE ' . db_string_equal_to($GLOBALS['SITE_DB']->translate_field_ref('name'), $addon_name) . ' AND (' . $filter_sql . ')';
        $result = $GLOBALS['SITE_DB']->query($query, null, null, false, true, array('name' => 'SHORT_TRANS'));

        $addon_times[intval(substr($x, 6))] = array(null, null, null, $addon_name);

        if (array_key_exists(0, $result)) {
            $url = $result[0]['url'];

            if (url_is_local($url)) {
                $last_date = @filemtime(get_custom_file_base() . '/' . rawurldecode($url));
            } else {
                $last_date = @filemtime($url);
            }
            if ($last_date === false) {
                continue;
            }

            $name = get_translated_text($result[0]['name']);
            $url = $result[0]['url'];
            $id = $result[0]['id'];
            $addon_times[intval(substr($x, 6))] = array($last_date, $id, $url, $name);
        }
    }
}

header('Content-type: text/plain; charset=' . get_charset());
echo serialize($addon_times);

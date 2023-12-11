<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    transcoder
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
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>ocProducts maintains full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="http://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by ocProducts.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

// Put code that you temporarily want executed into the function. DELETE THE CODE WHEN YOU'RE DONE.
// This is useful when performing quick and dirty upgrades (e.g. adding tables to avoid a reinstall)

require_code('database_action');
require_code('config2');
require_code('menus2');

run();

function run()
{
    $file = basename(rawurldecode($_GET['url']));

    // get old media file data
    $get_old_file = $GLOBALS['SITE_DB']->query('SELECT url FROM ' . get_table_prefix() . 'videos WHERE url LIKE \'uploads/galleries/' . rawurlencode(basename(basename($file, '.m4v'), '.mp3')) . '%\'');
    $type = 'galleries';
    if (!array_key_exists(0, $get_old_file)) {
        $get_old_file = $GLOBALS['SITE_DB']->query('SELECT a_url AS url FROM ' . get_table_prefix() . 'attachments WHERE a_url LIKE \'uploads/attachments/' . rawurlencode(basename(basename($file, '.m4v'), '.mp3')) . '%\'');
        $type = 'attachments';
        if (!array_key_exists(0, $get_old_file)) {
            $get_old_file = $GLOBALS['SITE_DB']->query('SELECT cv_value AS url FROM ' . get_table_prefix() . 'catalogue_efv_short WHERE cv_value LIKE \'uploads/catalogues/' . rawurlencode(basename(basename($file, '.m4v'), '.mp3')) . '%\'');
            $type = 'catalogues';
        }
    }

    require_code('files');
    $file_handle = @fopen(get_custom_file_base() . '/uploads/' . $type . '/' . $file, 'wb') or intelligent_write_error(get_custom_file_base() . '/uploads/' . $type . '/' . $file);
    flock($file_handle, LOCK_EX);
    http_download_file($_GET['url'], null, false, false, 'Composr', null, null, null, null, null, $file_handle, null, null, 6.0);
    flock($file_handle, LOCK_UN);
    fclose($file_handle);

    // move the old media file to the archive directory - '/uploads/' . $type . '/archive/'
    $new_url = 'uploads/' . $type . '/' . rawurlencode($file);
    if (isset($get_old_file[0]['url']) && is_string($get_old_file[0]['url']) && $get_old_file[0]['url'] != $new_url && strlen($get_old_file[0]['url']) > 0) {
        $movedir = dirname(str_replace('/uploads/' . $type . '/', '/uploads/' . $type . '_archive_addon/', str_replace('\\', '/', get_custom_file_base()) . '/' . rawurldecode($get_old_file[0]['url'])));
        @mkdir($movedir, 0777);
        require_code('files');
        fix_permissions($movedir);
        $from_path = str_replace('\\', '/', get_custom_file_base()) . '/' . rawurldecode($get_old_file[0]['url']);
        $to_path = str_replace('/uploads/' . $type . '/', '/uploads/' . $type . '_archive_addon/', str_replace('\\', '/', get_custom_file_base()) . '/' . rawurldecode($get_old_file[0]['url']));
        rename($from_path, $to_path);
        sync_file_move($from_path, $to_path);
    }

    switch ($type) {
        case 'galleries':
            $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'videos SET video_width=600,video_height=400,url=\'' . db_escape_string($new_url) . '\' WHERE url LIKE \'uploads/' . $type . '/' . db_escape_string(rawurlencode(basename(basename($file, '.m4v'), '.mp3'))) . '%\''); // Replaces row that referenced $file without .m4v on the end (the original filename) with row that references the new $file we just copied
            break;

        case 'attachments':
            $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'attachments SET a_url=\'' . db_escape_string($new_url) . '\' WHERE a_url LIKE \'uploads/' . $type . '/' . db_escape_string(rawurlencode(basename(basename($file, '.m4v'), '.mp3'))) . '%\''); // Replaces row that referenced $file without .m4v on the end (the original filename) with row that references the new $file we just copied
            break;

        case 'catalogues':
            $GLOBALS['SITE_DB']->query('UPDATE ' . get_table_prefix() . 'catalogue_efv_short SET cv_value=\'' . db_escape_string($new_url) . '\' WHERE cv_value LIKE \'uploads/' . $type . '/' . db_escape_string(rawurlencode(basename(basename($file, '.m4v'), '.mp3'))) . '%\''); // Replaces row that referenced $file without .m4v on the end (the original filename) with row that references the new $file we just copied
            break;
    }

    $transcoding_server = get_option('transcoding_server');

    http_download_file($transcoding_server . '/move_to_sent.php?file=' . urlencode($_GET['url']));
}

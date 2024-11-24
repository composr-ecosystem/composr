<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    code_editor
 */

/*EXTRA FUNCTIONS: tempnam|ftp_.*|posix_getuid|fileowner*/

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
$FILE_BASE = dirname($FILE_BASE);
if (!is_file($FILE_BASE . '/sources/bootstrap.php')) {
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
@chdir($FILE_BASE);

/**
 * Escape HTML text. Heavily optimised! Ended up with preg_replace after trying lots of things.
 *
 * @param  LONG_TEXT $string The text to escape
 * @return LONG_TEXT The escaped result
 */
function code_editor_escape_html(string $string) : string
{
    if ($string === '') {
        return ''; // Optimisation
    }

    return htmlspecialchars($string, ENT_QUOTES);
}

require_once($FILE_BASE . '/_config.php');

if ((array_key_exists('given_password', $_POST))) {
    $given_password = $_POST['given_password'];
    if (ce_check_maintenance_password($given_password)) {
        if ((!array_key_exists('path', $_POST)) && (!array_key_exists('path', $_GET))) {
            do_get_path($given_password);
        } else {
            if (!isset($_POST['path_new'])) {
                $_POST['path_new'] = '';
            }
            $path = ($_POST['path_new'] != '') ? $_POST['path_new'] : (array_key_exists('path', $_POST) ? $_POST['path'] : $_GET['path']);
            do_page($given_password, $path);
        }
    } else {
        code_editor_do_login();
    }
} else {
    code_editor_do_login();
}
code_editor_do_footer();

/**
 * Output the code editors page header.
 *
 * @param  ID_TEXT $type The type our form clicks are
 * @param  ID_TEXT $target The target our form clicks get sent to
 */
function code_editor_do_header(string $type, string $target = '_top')
{
    echo '
<!DOCTYPE html>
<html lang="EN">
<head>
    <title>Code editor</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="data/sheet.php?sheet=global" />
    <style>';
    echo '
        .screen-title { text-decoration: underline; display: block; background: url(\'themes/default/images/icons/admin/tool.svg\') top left no-repeat; background-size: 48px 48px; min-height: 42px; padding: 10px 0 0 60px; }
    </style>';
    echo '
        <script src="data/ace/ace.js"></script>
        <script src="data/ace/ace_composr.js"></script>

        <meta name="robots" content="noindex, nofollow" />
        ';
    echo '
</head>
<body class="website-body" style="margin: 1em"><div class="global-middle">
<form target="' . $target . '" action="code_editor.php?type=' . $type . '" method="post">
';
}

/**
 * Output the code editors page footer.
 */
function code_editor_do_footer()
{
    echo <<<END
</form>

<script>
if (document.getElementById('file')) {
    aceComposrLoader('file','php');
}
</script>

</div></body>
</html>
END;
}

/**
 * Output a login page.
 */
function code_editor_do_login()
{
    code_editor_do_header('gui');
    if (array_key_exists('path', $_GET)) {
        echo '<input type="hidden" name="path" value="' . code_editor_escape_html($_GET['path']) . '" />';
        echo '<input type="hidden" name="path_new" value="" />';
        if (array_key_exists('line', $_GET)) {
            echo '<input type="hidden" name="line" value="' . code_editor_escape_html($_GET['line']) . '" />';
        }
    }
    global $SITE_INFO;
    $ftp_domain = array_key_exists('ftp_domain', $SITE_INFO) ? $SITE_INFO['ftp_domain'] : 'localhost';
    if (!array_key_exists('ftp_username', $SITE_INFO)) {
        if ((function_exists('posix_getpwuid')) && (strpos(ini_get('disable_functions'), 'posix_getpwuid') === false)) {
            $file_owner = fileowner($GLOBALS['FILE_BASE'] . '/code_editor.php');
            if ($file_owner !== false) {
                $u_info = posix_getpwuid($file_owner);
                if ($u_info !== false) {
                    $ftp_username = $u_info['name'];
                } else {
                    $ftp_username = '';
                }
            } else {
                $ftp_username = '';
            }
        } else {
            $ftp_username = '';
        }
        if ($ftp_username === null) {
            $ftp_username = '';
        }
    } else {
        $ftp_username = $SITE_INFO['ftp_username'];
    }
    if (!array_key_exists('ftp_folder', $SITE_INFO)) {
        $dr = array_key_exists('DOCUMENT_ROOT', $_SERVER) ? $_SERVER['DOCUMENT_ROOT'] : (array_key_exists('DOCUMENT_ROOT', $_ENV) ? $_ENV['DOCUMENT_ROOT'] : '');
        if (strpos($dr, '/') !== false) {
            $dr_parts = explode('/', $dr);
        } else {
            $dr_parts = explode('\\', $dr);
        }
        $webdir_stub = $dr_parts[count($dr_parts) - 1];
        $script_name = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_ENV['SCRIPT_NAME']) ? $_ENV['SCRIPT_NAME'] : '');
        $ftp_folder = '/' . $webdir_stub . str_replace(DIRECTORY_SEPARATOR, '/', dirname($script_name));
    } else {
        $ftp_folder = $SITE_INFO['ftp_folder'];
    }
    echo <<<END
    <h1 class="screen-title">Code Editor</h1>
END;
    if (isset($_POST['given_password'])) {
        echo '<p><strong>Invalid password</strong></p>';
    }
    $_ftp_domain = code_editor_escape_html($ftp_domain);
    $_ftp_folder = code_editor_escape_html($ftp_folder);
    $_ftp_username = code_editor_escape_html($ftp_username);
    echo <<<END
    <p>
        <label for="given_password">Maintenance Password: <input type="password" name="given_password" autocomplete="current-password" id="given_password" class="form-control" /></label>
    </p>
    <hr />
    <p>If you need to edit original software files (rather than overriding or making custom ones), then you probably need to enter FTP details below. This will allow this editor to save via FTP, and if no username is given, it will try and save directly.</p>
    <table>
        <tr><th>FTP Host</th><td><input size="50" type="text" name="ftp_domain" class="form-control" value="{$_ftp_domain}" /></td></tr>
        <tr><th>FTP Path</th><td><input size="50" type="text" name="ftp_folder" class="form-control" value="{$_ftp_folder}" /></td></tr>
        <tr><th>FTP Username</th><td><input size="50" type="text" name="ftp_username" class="form-control" value="{$_ftp_username}" /></td></tr>
        <tr><th>FTP Password</th><td><input size="50" type="password" name="ftp_password" autocomplete="new-password" class="form-control" /></td></tr>
    </table>
    <p>
        <button class="btn btn-primary btn-scr menu--site-meta--user-actions--login" type="submit">Log in</button>
    </p>
    <hr />
    <ul class="actions-list" role="navigation">
        <li><a title="Introduction to programming tutorial (this link will open in a new window)" target="_blank" href="https://composr.app/docs/tut-programming.htm">Read the introduction to programming tutorial</a></li>
END;
    if (array_key_exists('base_url', $SITE_INFO)) {
        $_base_url = code_editor_escape_html($SITE_INFO['base_url']);
        echo <<<END
        <li><a href="{$_base_url}/adminzone/index.php">Go to Admin Zone</a></li>
END;
    }
    echo <<<END
    </ul>
END;
}

/**
 * Search inside a directory for editable files, while favouring the overridden versions.
 *
 * @param  SHORT_TEXT $dir The directory path to search
 * @return array A list of the HTML elements for the list box selection
 */
function ce_do_dir(string $dir) : array
{
    $out = [];
    $_dir = ($dir == '') ? '.' : $dir;
    $dh = @opendir($_dir);
    if ($dh !== false) {
        while (($file = readdir($dh)) !== false) {
            if ($file[0] != '.') {
                if (is_file($_dir . '/' . $file)) {
                    if ((substr($file, -4, 4) == '.php') || (substr($file, -4, 4) == '.ini')) {
                        $path = $dir . (($dir != '') ? '/' : '') . $file;
                        $alt = str_replace('lang/', 'lang_custom/', str_replace('pages/modules/', 'pages/modules_custom/', str_replace('sources/', 'sources_custom/', $path)));
                        if (($alt == $path) || (!file_exists($alt))) {
                            $out[] = '<option>' . code_editor_escape_html($path) . '</option>';
                        }
                    }
                } elseif (is_dir($_dir . '/' . $file)) {
                    $out = array_merge($out, ce_do_dir($dir . (($dir != '') ? '/' : '') . $file));
                }
            }
        }
        closedir($dh);
    }
    return $out;
}

/**
 * Output the file selection page.
 *
 * @param  SHORT_TEXT $given_password The password previously given to authorise our editing
 */
function do_get_path(string $given_password)
{
    // Just to test a connection if one was requested
    $test = open_up_ftp_connection();
    if (is_string($test)) {
        echo '<h1 class="screen-title">An FTP error occurred</h1>';
        echo '<p>' . code_editor_escape_html($test) . '</p>';
        return;
    }

    code_editor_do_header('gui');
    $files = ce_do_dir('');
    sort($files);
    $paths = implode('', $files);
    foreach ($_POST as $key => $val) {
        $_key = code_editor_escape_html($key);
        $_val = code_editor_escape_html($val);
        echo <<<END
<input type="hidden" name="{$_key}" value="{$_val}" />
END;
    }
    echo <<<END
    <h1 class="screen-title">Code Editor</h1>
    <p>
        New File: <input type="text" name="path_new" class="form-control" />
    </p>
    <p>
        OR, existing file: <select name="path" class="form-control">{$paths}</select>
    </p>
    <p class="proceed-button">
        <button class="btn btn-primary btn-scr buttons--save" type="submit">Edit file</button>
    </p>
END;
}

/**
 * Find whether we can get away with natural file access, not messing with AFMs, world-writability, etc.
 * Always will return false on Windows due to missing Posix - but there's no such thing as chmodding files for non-owners on Windows either.
 *
 * @return boolean Whether we have this
 */
function ce_is_suexec_like() : bool
{
    global $FILE_BASE;
    static $answer = null;
    if ($answer === null) {
        $answer = (function_exists('posix_getuid')) &&
            (!isset($_SERVER['HTTP_X_MOSSO_DT'])) &&
            (is_integer(@posix_getuid())) &&
            (posix_getuid() == @fileowner($FILE_BASE . '/sources/bootstrap.php'));
    }
    return $answer;
}

/**
 * Ensure that the specified file/folder is writeable for the FTP user (so that it can be deleted by the system), and should be called whenever a file is uploaded/created, or a folder is made. We call this function assuming we are giving world permissions.
 *
 * @param  PATH $path The full pathname to the file/directory
 * @param  integer $perms The permissions to make (not the permissions are reduced if the function finds that the file is owned by the web user [doesn't need world permissions then])
 */
function ce_fix_permissions(string $path, int $perms = 0666) // We call this function assuming we are giving world permissions
{
    // If the file user is different to the web user, we need to make it world writeable
    if (!ce_is_suexec_like()) {
        if ($perms == 0600) {
            @chmod($path, 0666);
        } else {
            @chmod($path, $perms);
        }
    } else { // Otherwise we do not
        if ($perms == 0666) {
            @chmod($path, 0644);
        } elseif ($perms == 0777) {
            @chmod($path, 0755);
        } else {
            @chmod($path, $perms);
        }
    }
}

/**
 * Open up an FTP connection from POSTed details.
 *
 * @return ?mixed Either an error screen or a connection. (null: not using FTP)
 */
function open_up_ftp_connection()
{
    if (empty($_POST['ftp_username'])) {
        return null;
    }

    $conn = false;
    $domain = $_POST['ftp_domain'];
    $port = 21;
    if (strpos($domain, ':') !== false) {
        list($domain, $_port) = explode(':', $domain, 2);
        $port = intval($_port);
    }
    if (function_exists('ftp_ssl_connect')) {
        $conn = @ftp_ssl_connect($domain, $port);
    }
    $ssl = ($conn !== false);
    if (($ssl) && (@ftp_login($conn, $_POST['ftp_username'], $_POST['ftp_password']) === false)) {
        $conn = false;
        $ssl = false;
    }
    if ($conn === false) {
        $conn = @ftp_connect($domain, $port);
    }
    if ($conn === false) {
        return 'Could not connection to host ' . $_POST['ftp_domain'];
    }

    if ((!$ssl) && (@ftp_login($conn, $_POST['ftp_username'], $_POST['ftp_password']) === false)) {
        return 'Could connect to the FTP server but not log in. [' . code_editor_error_get_last() . ']';
    }

    if (substr($_POST['ftp_folder'], -1) != '/') {
        $_POST['ftp_folder'] .= '/';
    }
    if (@ftp_chdir($conn, $_POST['ftp_folder']) === false) {
        return 'The FTP folder given was invalid or can not otherwise be accessed. [' . code_editor_error_get_last() . ']';
    }
    $files = @ftp_nlist($conn, '.');
    if ($files === false) { // :(. Weird bug on some systems
        $files = [];
        if (@ftp_rename($conn, '_config.php', '_config.php')) {
            $files = ['_config.php'];
        }
    }
    if (!in_array('_config.php', $files)) {
        return 'This does not appear to be the correct FTP directory.';
    }
    return $conn;
}

/**
 * Output the editing page and do the editing.
 *
 * @param  SHORT_TEXT $given_password The password previously given to authorise our editing
 * @param  SHORT_TEXT $path The path of the file we are editing
 */
function do_page(string $given_password, string $path)
{
    if ($path[0] == '/') {
        $path = substr($path, 1);
    }
    if (strpos($path, '..') !== false) {
        exit('Internal error');
    }

    $type = array_key_exists('type', $_GET) ? $_GET['type'] : 'gui';

    code_editor_do_header('edit', 'results');
    if ($type == 'gui') {
        $save_path = convert_to_save_path($path);

        $contents = @file_get_contents($path);
        $lines = substr_count($contents, "\n") + 1;
        $line = (array_key_exists('line', $_POST) ? intval($_POST['line']) : (array_key_exists('line', $_POST) ? intval($_POST['line']) : 0));
        $_path = code_editor_escape_html($path);
        echo <<<END
<h1 class="screen-title"><a onclick="window.back(); return false;" href="code_editor.php">Code Editor</a>: Editing {$_path}</h1>
<input type="hidden" name="path" value="{$_path}" />
END;
        foreach ($_POST as $key => $val) {
            if (($key != 'path') && ($key != 'path_new')) {
                $_key = code_editor_escape_html($key);
                $_val = code_editor_escape_html($val);
                echo <<<END
<input type="hidden" name="{$_key}" value="{$_val}" />
END;
            }
        }
        echo <<<END
<textarea id="file" name="file" class="form-control" rows="35" cols="50" style="width: 100%;">
END;
        echo code_editor_escape_html($contents) . '</textarea>';
        echo <<<END
<script>
    var file=document.getElementById('file');
    file.scrollTop=Math.round((file.scrollHeight/{$lines})*{$line});
</script>
<p>
    Jump to (line number or search phrase): <input name="jmp" type="text" class="form-control" value="" /> <button onclick="var val=form.elements['jmp'].value; if (!(window.parseInt(val)>0)) val=file.value.substr(0,file.value.indexOf(val)).split('\\n').length-1; file.scrollTop=Math.round((file.scrollHeight/{$lines})*window.parseInt(val)); return false;" type="submit">Jump</button>
</p>
END;
        if (strpos($path, '_custom/') !== false) {
            echo <<<END
<p><input id="delete" name="delete" type="checkbox" value="1" /><label for="delete">Delete this override/custom-file. If you choose this, nothing will be edited, only deleted.</label></p>
END;
        } elseif ($save_path == $path) {
            echo <<<END
<p>This file is not overridable - it will be edited directly.</p>
END;
        } else {
            echo <<<END
<p>This file is not yet overridden. <input id="override" name="override" checked="checked" type="checkbox" value="1" /> <label for="override">Use this edit to specify the override (as opposed to saving over the original).</label></p>
END;
        }
        echo <<<END
<p class="proceed-button">
<button type="submit">Edit</button>
</p>
<iframe name="results" src="" style="display: none"></iframe>
END;
    } else {
        $save_path = $_POST['path'];
        if (isset($_POST['override'])) {
            $save_path = convert_to_save_path($save_path);
        }

        $file = $_POST['file'];

        // Make backup
        if (file_exists($save_path)) {
            $backup_path = $save_path . '.' . strval(time()) . '_';
            $backup_path .= substr(md5(random_bytes(13)), 0, 13);
            $c_success = @copy($save_path, $backup_path);
            if ($c_success !== false) {
                ce_sync_file($backup_path);
            }
        }

        $conn = open_up_ftp_connection();

        // Edit
        if (!isset($_POST['delete'])) {
            if ($conn === null) { // Via direct access
                $success = file_put_contents($save_path, $file, LOCK_EX);
                if ($success === false) {
                    echo <<<END
<script>
var msg='Could not write to file. You probably should have specified FTP details.';
if (window.alert !== null) {
    window.alert(msg);
} else {
    console.log(msg+' (popup blocker stopping alert)');
}
</script>
END;
                    return;
                }
            } else { // Via FTP
                $path2 = @tempnam((((ini_get('open_basedir') != '') && (preg_match('#(^|:|;)/tmp($|:|;|/)#', ini_get('open_basedir')) == 0)) ? (get_custom_file_base() . '/temp/') : '/tmp/'), 'cmsce');
                if ($path2 === false) {
                    $path2 = @tempnam(get_custom_file_base() . '/temp/', 'cmsce');
                }

                $h = fopen($path2, 'wb');
                if (fwrite($h, $file) === false) {
                    fclose($h);
                    echo <<<END
<script>
var msg='Could not write to file, out of disk space?';
if (window.alert !== null) {
    window.alert(msg);
} else {
    console.log(msg + ' (popup blocker stopping alert)');
}
</script>
END;
                    return;
                }
                fclose($h);

                $h = fopen($path2, 'rb');
                $ftp_success = @ftp_fput($conn, $save_path, $h, FTP_BINARY);
                if ($ftp_success === false) {
                    $message = addslashes(cms_error_get_last());
                    echo <<<END
<script>
var msg='Could not save via FTP [' . $message . '].';
if (window.alert !== null) {
    window.alert(msg);
} else {
    console.log(msg + ' (popup blocker stopping alert)');
}
</script>
END;
                    return;
                }
                fclose($h);

                unlink($path2);
            }
        } else { // Delete
            unlink($save_path);
        }

        ce_fix_permissions($save_path);
        ce_sync_file($save_path);

        // Make base-hash-thingy
        if (!isset($_POST['delete'])) {
            if (file_exists(str_replace('_custom/', '/', $save_path))) {
                $hash = file_get_contents(str_replace('_custom/', '/', $save_path));
                if ($conn === null) { // Via direct access
                    @file_put_contents($save_path . '.editfrom', $hash, LOCK_EX);
                } else { // Via FTP
                    $path2 = ce_cms_tempnam();

                    file_put_contents($path2, $hash);

                    $h = fopen($path2, 'rb');
                    @ftp_fput($conn, $save_path . '.editfrom', $h, FTP_BINARY);
                    fclose($h);

                    unlink($path2);
                }
            }
        } else {
            @unlink($save_path . '.editfrom');
        }
        ce_fix_permissions($save_path . '.editfrom');
        ce_sync_file($save_path . '.editfrom');

        if (!isset($_POST['delete'])) {
            $message = "Saved " . addslashes(code_editor_escape_html(str_replace('/', DIRECTORY_SEPARATOR, $save_path)) . " (and if applicable, placed a backup in its directory)!");
        } else {
            $message = "Deleted " . addslashes(code_editor_escape_html(str_replace('/', DIRECTORY_SEPARATOR, $save_path)) . ". You may edit to recreate the file if you wish however.");
        }
        echo <<<END
<script>
var msg='{$message}';
if (window.alert !== null) {
    window.alert(msg);
} else {
    console.log(msg + ' (popup blocker stopping alert)');
}
</script>
END;
    }
}

/**
 * Convert a normal path to an overridden save path.
 *
 * @param  string $save_path The normal path
 * @return string The overridden save path
 */
function convert_to_save_path(string $save_path) : string
{
    if (substr($save_path, 0, 8) == 'sources/') {
        $save_path = 'sources_custom/' . substr($save_path, 8);
    } elseif (substr($save_path, 0, 5) == 'lang/') {
        $save_path = 'lang_custom/' . substr($save_path, 5);
    } else {
        $save_path = str_replace('pages/modules/', 'pages/modules_custom/', $save_path);
    }
    return $save_path;
}

/**
 * Provides a hook for file synchronisation between mirrored servers.
 *
 * @param  PATH $filename File/directory name to sync on (may be full or relative path)
 */
function ce_sync_file(string $filename)
{
    global $FILE_BASE;
    if (file_exists($FILE_BASE . '/data_custom/sync_script.php')) {
        require_once($FILE_BASE . '/data_custom/sync_script.php');
        if (substr($filename, 0, strlen($FILE_BASE)) == $FILE_BASE) {
            $filename = substr($filename, strlen($FILE_BASE));
        }
        if (function_exists('master__sync_file')) {
            master__sync_file($filename);
        }
    }
}

/**
 * Provides a hook for file synchronisation between mirrored servers.
 *
 * @param  PATH $old File/directory name to move from (may be full or relative path)
 * @param  PATH $new File/directory name to move to (may be full or relative path)
 */
function ce_sync_file_move(string $old, string $new)
{
    global $FILE_BASE;
    if (file_exists($FILE_BASE . '/data_custom/sync_script.php')) {
        require_once($FILE_BASE . '/data_custom/sync_script.php');
        if (substr($old, 0, strlen($FILE_BASE)) == $FILE_BASE) {
            $old = substr($old, strlen($FILE_BASE));
        }
        if (substr($new, 0, strlen($FILE_BASE)) == $FILE_BASE) {
            $new = substr($new, strlen($FILE_BASE));
        }
        if (function_exists('master__sync_file_move')) {
            master__sync_file_move($old, $new);
        }
    }
}

/**
 * Check the given maintenance password is valid.
 *
 * @param  SHORT_TEXT $password_given Given maintenance password
 * @return boolean Whether it is valid
 */
function ce_check_maintenance_password(string $password_given) : bool
{
    global $FILE_BASE;
    require_once($FILE_BASE . '/sources/crypt_maintenance.php');
    return check_maintenance_password($password_given);
}

/**
 * Create file with unique file name, but works around compatibility issues between servers. Note that the file is NOT automatically deleted. You should also delete it using "@unlink", as some servers have problems with permissions.
 *
 * @param  string $prefix The prefix of the temporary file name
 * @return ~string The name of the temporary file (false: error)
 */
function ce_cms_tempnam(string $prefix = '')
{
    global $FILE_BASE;
    $problem_saving = ((function_exists('get_option')) && (get_option('force_local_temp_dir') == '1')) || ((ini_get('open_basedir') != '') && (preg_match('#(^|:|;)/tmp($|:|;|/)#', ini_get('open_basedir')) == 0));
    $local_path = $FILE_BASE . '/temp/';
    $server_path = '/tmp/';
    $tmp_path = $problem_saving ? $local_path : $server_path;
    if ((function_exists('tempnam')) && (strpos(ini_get('disable_functions'), 'tempnam') === false)) {
        $tempnam = @tempnam($tmp_path, 'tmpfile__' . $prefix);
        if (($tempnam === false) && (!$problem_saving)) {
            $tempnam = @tempnam($local_path, $prefix);
        }
    } else {
        $tempnam = $prefix . strval(mt_rand(0, mt_getrandmax()));
        $myfile = fopen($local_path . '/' . $tempnam, 'wb');
        fclose($myfile);
    }
    return $tempnam;
}

/**
 * Get last error message in the code editor.
 *
 * @return string Error message (blank: none)
 */
function code_editor_error_get_last() : string
{
    $error = error_get_last();
    if ($error === null) {
        return '';
    }

    return '[' . strval($error['type']) . '] ' . $error['message'] . ' in ' . $error['file'] . ' on line ' . strval($error['line']);
}

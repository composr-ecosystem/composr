<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_release_build
 */

/*EXTRA FUNCTIONS: shell_exec|escapeshellarg|DOM.*|pretty_print_dom_document*/

/* See the note at the top of the other make_release.php file for usage on Windows */

function init__make_release()
{
    require_code('files2');

    // Make sure builds folder exists
    get_builds_path();

    // Tracking
    global $MAKE_INSTALLERS__FILE_ARRAY, $MAKE_INSTALLERS__DIR_ARRAY, $MAKE_INSTALLERS__TOTAL_DIRS, $MAKE_INSTALLERS__TOTAL_FILES;
    $MAKE_INSTALLERS__FILE_ARRAY = array();
    $MAKE_INSTALLERS__DIR_ARRAY = array();
    $MAKE_INSTALLERS__TOTAL_DIRS = 0;
    $MAKE_INSTALLERS__TOTAL_FILES = 0;
}

function make_installers($skip_file_grab = false)
{
    foreach (array('zip', 'tar', 'gzip') as $cmd) {
        if (shell_exec($cmd . ' -h 2>&1') == '') {
            warn_exit('Missing command in path: ' . $cmd);
        }
    }

    global $MAKE_INSTALLERS__FILE_ARRAY, $MAKE_INSTALLERS__DIR_ARRAY, $MAKE_INSTALLERS__TOTAL_DIRS, $MAKE_INSTALLERS__TOTAL_FILES;

    require_code('files');

    // Start output
    $out = '';
    $out .= '<p>A Composr build is being compiled and packed up into installation packages.</p>';

    require_code('version2');
    $version_dotted = get_version_dotted();
    $version_branch = get_version_branch();

    // Make necessary directories
    $builds_path = get_builds_path();
    if (!file_exists($builds_path . '/builds/build/')) {
        @mkdir($builds_path . '/builds/build/', 0777) or warn_exit('Could not make temporary build folder');
        fix_permissions($builds_path . '/builds/build/');
    }

    if (!$skip_file_grab) {
        deldir_contents($builds_path . '/builds/build/' . $version_branch . '/');
    }
    if (!file_exists($builds_path . '/builds/build/' . $version_branch . '/')) {
        mkdir($builds_path . '/builds/build/' . $version_branch . '/', 0777) or warn_exit('Could not make branch build folder');
        fix_permissions($builds_path . '/builds/build/' . $version_branch . '/');
    }
    if (!file_exists($builds_path . '/builds/' . $version_dotted . '/')) {
        mkdir($builds_path . '/builds/' . $version_dotted . '/', 0777) or warn_exit('Could not make version build folder');
        fix_permissions($builds_path . '/builds/' . $version_dotted . '/');
    }

    if (!$skip_file_grab) {
        @copy(get_file_base() . '/install.php', $builds_path . '/builds/build/' . $version_branch . '/install.php');
        fix_permissions($builds_path . '/builds/build/' . $version_branch . '/install.php');

        // Get file data array
        $out .= '<ul>';
        $out .= populate_build_files_list();
        $out .= '</ul>';

        make_files_manifest();
        make_database_manifest();
        if (get_param_integer('rebuild_sql', 0) == 1) {
            make_install_sql();
        }
    }

    //header('Content-type: text/plain; charset=' . get_charset());var_dump(array_keys($MAKE_INSTALLERS__FILE_ARRAY));exit(); Useful for testing quickly what files will be built

    // What we'll be building
    $bundled = $builds_path . '/builds/' . $version_dotted . '/composr-' . $version_dotted . '.tar';
    $quick_zip = $builds_path . '/builds/' . $version_dotted . '/composr_quick_installer-' . $version_dotted . '.zip';
    $manual_zip = $builds_path . '/builds/' . $version_dotted . '/composr_manualextraction_installer-' . $version_dotted . '.zip';
    $mszip = $builds_path . '/builds/' . $version_dotted . '/composr-' . $version_dotted . '-webpi.zip'; // Aka msappgallery, related to webmatrix
    $aps_zip = $builds_path . '/builds/' . $version_dotted . '/composr-' . $version_dotted . '.app.zip'; // APS package
    $omni_upgrader = $builds_path . '/builds/' . $version_dotted . '/composr_upgrader-' . $version_dotted . '.cms';

    // Flags
    $make_quick = (get_param_integer('skip_quick', 0) == 0);
    $make_manual = (get_param_integer('skip_manual', 0) == 0);
    $make_bundled = (get_param_integer('skip_bundled', 0) == 0);
    $make_mszip = (get_param_integer('skip_mszip', 0) == 0);
    $make_aps = false; // We don't use it right now and need to speed this all up (get_param_integer('skip_aps', 0) == 0);
    $make_omni_upgrader = (post_param_integer('make_omni_upgrader', 0) == 1);

    if (php_function_allowed('set_time_limit')) {
        @set_time_limit(0);
    }
    disable_php_memory_limit();

    // Build quick installer
    if ($make_quick) {
        // Write out our installer data file
        require_code('tar');
        $data_file = tar_open($builds_path . '/builds/' . $version_dotted . '/data.cms', 'wb');
        $zip_file_array = array();
        $offsets = array();
        $sizes = array();
        foreach ($MAKE_INSTALLERS__FILE_ARRAY as $path => $data) {
            $offsets[$path] = tar_add_file($data_file, $path, $data, 0644, is_file(get_file_base() . '/' . $path) ? filemtime(get_file_base() . '/' . $path) : time());
            $sizes[$path] = strlen($data);
        }
        tar_close($data_file);
        fix_permissions($builds_path . '/builds/' . $version_dotted . '/data.cms');
        $archive_size = filesize($builds_path . '/builds/' . $version_dotted . '/data.cms');
        // The installer does an md5 check to check integrity - prepare for it
        $md5_test_path = 'data/images/advertise_here.png';
        $md5 = md5(file_get_contents($builds_path . '/builds/build/' . $version_branch . '/' . $md5_test_path));

        // Write out our PHP installer file
        $file_count = count($MAKE_INSTALLERS__FILE_ARRAY);
        $size_list = '';
        $offset_list = '';
        $file_list = '';
        foreach (array_keys($MAKE_INSTALLERS__FILE_ARRAY) as $path) { // $MAKE_INSTALLERS__FILE_ARRAY is Current path->contents. We need number->path, so we can count through them without having to have the array with us. We end up with this in string form, as it goes in our file
            $out .= do_build_file_output($path);
            $size_list .= '\'' . $path . '\'=>' . strval($sizes[$path]) . ',' . "\n";
            $offset_list .= '\'' . $path . '\'=>' . strval($offsets[$path]) . ',' . "\n";
            $file_list .= '\'' . $path . '\',';
        }

        // Build install.php, which has to have all our data.cms file offsets put into it (data.cms is an uncompressed zip, but the quick installer cheats - it can't truly read arbitrary zips)
        $code = file_get_contents(get_file_base() . '/install.php');
        $installer_start = "<" . "?php
            /* QUICK INSTALLER CODE starts */

            global \$FILE_ARRAY,\$SIZE_ARRAY,\$OFFSET_ARRAY,\$DIR_ARRAY,\$DATADOTCMS_FILE;
            \$OFFSET_ARRAY = array({$offset_list});
            \$SIZE_ARRAY = array({$size_list});
            \$FILE_ARRAY = array({$file_list});
            \$DATADOTCMS_FILE = @fopen('data.cms','rb');
            if (\$DATADOTCMS_FILE === false) exit('data.cms missing / inaccessible -- make sure you upload it');
            if (filesize('data.cms') != {$archive_size}) warn_exit('data.cms not fully uploaded, or wrong version for this installer');
            if (md5(file_array_get('{$md5_test_path}')) != '{$md5}') warn_exit('data.cms corrupt. Must not be uploaded in text mode');

            function file_array_get(\$path)
            {
                global \$OFFSET_ARRAY,\$SIZE_ARRAY,\$DATADOTCMS_FILE,\$FILE_BASE;

                if (substr(\$path,0,strlen(\$FILE_BASE.'/')) == \$FILE_BASE.'/')
                    \$path = substr(\$path,strlen(\$FILE_BASE.'/'));

                if (!isset(\$OFFSET_ARRAY[\$path])) return;
                \$offset = \$OFFSET_ARRAY[\$path];
                \$size = \$SIZE_ARRAY[\$path];
                if (\$size == 0) return '';
                fseek(\$DATADOTCMS_FILE,\$offset,SEEK_SET);
                if (\$size>1024*1024) {
                    return array(\$size,\$DATADOTCMS_FILE,\$offset);
                }
                \$data = fread(\$DATADOTCMS_FILE,\$size);
                return \$data;
            }

            function file_array_exists(\$path)
            {
                global \$OFFSET_ARRAY;
                return (isset(\$OFFSET_ARRAY[\$path]));
            }

            function file_array_get_at(\$i)
            {
                global \$FILE_ARRAY;
                \$name = \$FILE_ARRAY[\$i];
                return array(\$name,file_array_get(\$name));
            }

            function file_array_count()
            {
                return {$file_count};
            }";
        $installer_start = preg_replace('#^\t{3}#m', '', $installer_start); // Format it correctly
        $auto_installer_code = '';
        $auto_installer_code .= $installer_start;
        global $MAKE_INSTALLERS__DIR_ARRAY;
        foreach ($MAKE_INSTALLERS__DIR_ARRAY as $dir) {
            $auto_installer_code .= '$DIR_ARRAY[]=\'' . $dir . '\';' . "\n";
        }
        $auto_installer_code .= '/* QUICK INSTALLER CODE ends */ ?' . '>';
        $auto_installer_code .= $code;
        cms_file_put_contents_safe($builds_path . '/builds/' . $version_dotted . '/install.php', $auto_installer_code, FILE_WRITE_FIX_PERMISSIONS);

        @unlink($quick_zip);

        chdir($builds_path . '/builds/' . $version_dotted);
        $cmd = 'zip -r -9 ' . escapeshellarg($quick_zip) . ' ' . escapeshellarg('data.cms') . ' ' . escapeshellarg('install.php');
        $cmd_result = shell_exec($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        $out .= do_build_archive_output($quick_zip, $output2);

        chdir(get_file_base() . '/data_custom/builds');
        $cmd = 'zip -r -9 ' . escapeshellarg($quick_zip) . ' ' . escapeshellarg('readme.txt');
        $cmd_result = shell_exec($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        //$out .= do_build_archive_output($quick_zip, $output2);    Don't care

        chdir(get_file_base());
    }

    /*
    The other installers are built up file-by-file...
    */

    // Build manual
    if ($make_manual) {
        @unlink($manual_zip);

        // Do the main work
        chdir($builds_path . '/builds/build/' . $version_branch);
        $cmd = 'zip -r -9 ' . escapeshellarg($manual_zip) . ' *';
        $cmd_result = shell_exec($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        $out .= do_build_archive_output($manual_zip, $output2);

        chdir(get_file_base());
    }

    // Build bundled version (Installatron, Bitnami, ...)
    if ($make_bundled) {
        @unlink($bundled);
        @unlink($bundled . '.gz');

        // Copy some files we need
        copy(get_file_base() . '/install.sql', $builds_path . '/builds/build/' . $version_branch . '/install.sql');
        fix_permissions($builds_path . '/builds/build/' . $version_branch . '/install.sql');
        copy(get_file_base() . '/_config.php.template', $builds_path . '/builds/build/' . $version_branch . '/_config.php.template');
        fix_permissions($builds_path . '/builds/build/' . $version_branch . '/_config.php.template');

        // Do the main work...

        chdir($builds_path . '/builds/build/' . $version_branch);
        $cmd = 'tar -cvf ' . escapeshellarg($bundled) . ' *';
        $cmd_result = shell_exec($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        //$out .= do_build_archive_output($v, $output2);  Don't mention, as will get auto-deleted after gzipping anyway

        chdir(get_file_base() . '/data_custom/builds');
        $cmd = 'tar -rvf ' . escapeshellarg($bundled) . ' readme.txt';
        $cmd_result = shell_exec($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        //$out .= do_build_archive_output($v, $output2);  Don't mention, as will get auto-deleted after gzipping anyway

        chdir($builds_path . '/builds/build/' . $version_branch);
        $cmd = 'gzip -n ' . escapeshellarg($bundled);
        $cmd_result = shell_exec($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            if (is_file($bundled . '.gz')) {
                $cmd_result = '(no output)'; // gzip produces no output normally anyway, which means shell_exec returns null
            } else {
                fatal_exit('Failed to run: ' . $cmd);
            }
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        @unlink($bundled);
        $out .= do_build_archive_output($bundled . '.gz', $output2);

        // Remove those files we copied
        unlink($builds_path . '/builds/build/' . $version_branch . '/install.sql');
        unlink($builds_path . '/builds/build/' . $version_branch . '/_config.php.template');

        chdir(get_file_base());
    }

    // Build Microsoft version
    if ($make_mszip) {
        @unlink($mszip);
        if (file_exists($builds_path . '/builds/build/composr/')) {
            deldir_contents($builds_path . '/builds/build/composr/');
        }

        // Move files out temporarily
        rename($builds_path . '/builds/build/' . $version_branch . '/_config.php', $builds_path . '/builds/build/_config.php');
        rename($builds_path . '/builds/build/' . $version_branch . '/install.php', $builds_path . '/builds/build/install.php');

        // Put temporary files in main folder
        copy(get_file_base() . '/_config.php.template', $builds_path . '/builds/build/' . $version_branch . '/_config.php.template');
        fix_permissions($builds_path . '/builds/build/' . $version_branch . '/_config.php.template');

        // Copy some stuff we need
        for ($i = 1; $i <= 4; $i++) {
            copy(get_file_base() . '/install' . strval($i) . '.sql', $builds_path . '/builds/build/install' . strval($i) . '.sql');
            fix_permissions($builds_path . '/builds/build/install' . strval($i) . '.sql');
        }
        copy(get_file_base() . '/user.sql', $builds_path . '/builds/build/user.sql');
        fix_permissions($builds_path . '/builds/build/user.sql');
        copy(get_file_base() . '/postinstall.sql', $builds_path . '/builds/build/postinstall.sql');
        fix_permissions($builds_path . '/builds/build/postinstall.sql');
        copy(get_file_base() . '/manifest.xml', $builds_path . '/builds/build/manifest.xml');
        fix_permissions($builds_path . '/builds/build/manifest.xml');
        copy(get_file_base() . '/parameters.xml', $builds_path . '/builds/build/parameters.xml');
        fix_permissions($builds_path . '/builds/build/parameters.xml');

        // Temporary renaming
        rename($builds_path . '/builds/build/' . $version_branch, $builds_path . '/builds/build/composr');

        // Do the main work
        chdir($builds_path . '/builds/build');
        $cmd = 'zip -r -9 -v ' . escapeshellarg($mszip) . ' composr manifest.xml parameters.xml install1.sql install2.sql install3.sql install4.sql user.sql postinstall.sql';
        $cmd_result = shell_exec($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        $out .= do_build_archive_output($mszip, $output2);

        // Undo temporary renaming
        rename($builds_path . '/builds/build/composr', $builds_path . '/builds/build/' . $version_branch);

        // Move back files moved out temporarily
        rename($builds_path . '/builds/build/_config.php', $builds_path . '/builds/build/' . $version_branch . '/_config.php');
        rename($builds_path . '/builds/build/install.php', $builds_path . '/builds/build/' . $version_branch . '/install.php');

        // Remove temporary files from main folder
        unlink($builds_path . '/builds/build/' . $version_branch . '/_config.php.template');

        chdir(get_file_base());
    }

    // Build APS package
    if ($make_aps) {
        @unlink($aps_zip);

        if (file_exists($builds_path . '/builds/aps/')) {
            deldir_contents($builds_path . '/builds/aps/');
        }

        // Copy the files we need
        copy_r(get_file_base() . '/aps', $builds_path . '/builds/aps');
        fix_permissions($builds_path . '/builds/aps');
        copy(get_file_base() . '/install.sql', $builds_path . '/builds/aps/scripts/install.sql');
        fix_permissions($builds_path . '/builds/aps/scripts/install.sql');

        // Temporary renaming
        rename($builds_path . '/builds/build/' . $version_branch . '/', $builds_path . '/builds/aps/htdocs/');

        /* Prepare changelog for APP-META.xml*/
        // Load the template APP-META.xml
        $app_meta_doc = new DOMDocument();
        $app_meta_doc->loadXML(file_get_contents(get_file_base() . '/aps/APP-META.xml'));

        $xpath = new DOMXPath($app_meta_doc);
        $xpath->registerNamespace('x', 'http://apstandard.com/ns/1');

        $application_el = $xpath->query('/x:application')->item(0);
        $application_el->setAttribute('packaged', date(DATE_ATOM));

        $version_el = $xpath->query('/x:application/x:version')->item(0);
        $version_el->nodeValue = $version_dotted;

        $changelog_el = $xpath->query('/x:application/x:presentation/x:changelog')->item(0);
        $changelog_previous_version_el = $changelog_el->getElementsByTagName('version')->item(0);

        $previous_version_dotted = $changelog_previous_version_el->getAttribute('version');

        if ($version_dotted !== $previous_version_dotted) {
            $changelog_version_el = $changelog_previous_version_el->cloneNode(1);
            $changelog_version_el->setAttribute('version', $version_dotted);

            $changelog_version_entry_el = $changelog_version_el->getElementsByTagName('entry')->item(0);
            $changelog_version_entry_el->nodeValue = 'Composr ' . $version_dotted . ' release notes: https://compo.sr/uploads/website_specific/compo.sr/scripts/goto_release_notes.php?version=' . urlencode($version_dotted);

            $changelog_el->insertBefore($changelog_version_el, $changelog_previous_version_el);
        }

        $app_meta_doc = pretty_print_dom_document($app_meta_doc);
        // Update the template APP-META.xml
        $app_meta_doc->save(get_file_base() . '/aps/APP-META.xml');
        // Make the build APP-META.xml
        $app_meta_doc->save($builds_path . '/builds/aps/APP-META.xml');

        /* Prepare APP-LIST.xml */
        // Load the template APP-LIST.xml
        $app_list_doc = new DOMDocument();
        $app_list_doc->loadXML(file_get_contents(get_file_base() . '/aps/APP-LIST.xml'));

        $files_el = $app_list_doc->getElementsByTagName('files')->item(0);

        unlink($builds_path . '/builds/aps/APP-LIST.xml'); // Delete the copied template so it's not included in the list

        $success = make_file_elements($app_list_doc, $files_el, $builds_path . '/builds/aps');
        if ($success === false) {
            warn_exit('Failed to build APP-LIST.xml');
        }

        // Save the build APP-LIST.xml
        $app_list_doc = pretty_print_dom_document($app_list_doc);
        $app_list_doc->save($builds_path . '/builds/aps/APP-LIST.xml');

        // Do the main work
        chdir($builds_path . '/builds/aps');
        $cmd = 'zip -r -9 -v ' . escapeshellarg($aps_zip) . ' htdocs images scripts test APP-LIST.xml APP-META.xml';
        $cmd_result = shell_exec($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        $out .= do_build_archive_output($aps_zip, $output2);

        // Undo temporary renaming
        rename($builds_path . '/builds/aps/htdocs/', $builds_path . '/builds/build/' . $version_branch . '/');

        // Delete the copied files
        deldir_contents($builds_path . '/builds/aps/');
        rmdir($builds_path . '/builds/aps/');

        chdir(get_file_base());
    }

    // Build omni-upgrader
    if ($make_omni_upgrader) {
        @unlink($omni_upgrader);

        // Do the main work
        chdir($builds_path . '/builds/build/' . $version_branch);
        $cmd = 'tar --exclude=_config.php --exclude=install.php -cvf ' . escapeshellarg($omni_upgrader) . ' *';
        $cmd_result = shell_exec($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        $out .= do_build_archive_output($omni_upgrader, $output2);

        chdir(get_file_base());
    }

    // We're done, show the result

    $details = '';
    require_code('files');
    if ($make_quick) {
        $details .= '<li>' . $quick_zip . ' file size: ' . clean_file_size(filesize($quick_zip)) . '</li>';
    }
    if ($make_manual) {
        $details .= '<li>' . $manual_zip . ' file size: ' . clean_file_size(filesize($manual_zip)) . '</li>';
    }
    if ($make_mszip) {
        $details .= '<li>' . $mszip . ' file size: ' . clean_file_size(filesize($mszip)) . '</li>';
    }
    if ($make_bundled) {
        $details .= '<li>' . $bundled . '.gz file size: ' . clean_file_size(filesize($bundled . '.gz')) . '</li>';
    }
    if ($make_aps) {
        $details .= '<li>' . $aps_zip . ' file size: ' . clean_file_size(filesize($aps_zip)) . '</li>';
    }
    if ($make_omni_upgrader) {
        $details .= '<li>' . $omni_upgrader . ' file size: ' . clean_file_size(filesize($omni_upgrader)) . '</li>';
    }

    $out .= '
        <h2>Statistics</h2>
        <ul>
            <li>Total files compiled: ' . integer_format($MAKE_INSTALLERS__TOTAL_FILES) . '</li>
            <li>Total directories traversed: ' . integer_format($MAKE_INSTALLERS__TOTAL_DIRS) . '</li>
            ' . $details . '
        </ul>';

    // To stop ocProducts-PHP complaining about non-synched files
    global $_CREATED_FILES, $_MODIFIED_FILES;
    $_CREATED_FILES = array();
    $_MODIFIED_FILES = array();

    return $out;
}

// Used in the APS build process
function make_file_elements(DOMDocument $app_list_doc, DOMElement $files_el, $dir_path)
{
    $entries = scandir($dir_path);
    if ($entries === false) {
        return false;
    }
    $entries = array_diff($entries, array('.', '..'));

    foreach ($entries as $entry) {
        $entry_path = $dir_path . '/' . $entry;

        if (is_dir($entry_path)) {
            $success = make_file_elements($app_list_doc, $files_el, $entry_path);
            if ($success === false) {
                return false;
            }
            continue;
        }

        $name = substr($entry_path, strlen(get_builds_path() . '/builds/aps/')); // Remove base path, we need a relative path

        $el = $app_list_doc->createElement('ns2:file');
        $el->setAttribute('name', $name);
        $el->setAttribute('size', strval(filesize($entry_path)));
        $el->setAttribute('sha256', hash_file('sha256', $entry_path));

        $files_el->appendChild($el);
    }

    return true;
}

// Used in the APS build process
function pretty_print_dom_document(DOMDocument $doc)
{
    $new_doc = new DOMDocument();
    $new_doc->preserveWhiteSpace = false;
    $new_doc->formatOutput = true;
    $new_doc->loadXML($doc->saveXML());

    return $new_doc;
}

function get_builds_path()
{
    $builds_path = get_file_base() . '/exports';
    if (!file_exists($builds_path . '/builds')) {
        mkdir($builds_path . '/builds', 0777) or warn_exit('Could not make master build folder');
        fix_permissions($builds_path . '/builds');
    }
    return $builds_path;
}

function copy_r($path, $dest)
{
    if (is_dir($path)) {
        @mkdir($dest, 0777);
        fix_permissions($dest);
        $objects = scandir($path);
        if (count($objects) > 0) {
            foreach ($objects as $file) {
                if (($file == '.') || ($file == '..')) {
                    continue;
                }

                if (is_dir($path . '/' . $file)) {
                    copy_r($path . '/' . $file, $dest . '/' . $file);
                } else {
                    copy($path . '/' . $file, $dest . '/' . $file);
                    fix_permissions($dest . '/' . $file);
                }
            }
        }
        return true;
    } elseif (is_file($path)) {
        return copy($path, $dest);
    } else {
        return false;
    }
}

function do_build_file_output($path)
{
    global $MAKE_INSTALLERS__TOTAL_FILES;
    $MAKE_INSTALLERS__TOTAL_FILES++;
    return '<li>File "' . escape_html($path) . '" compiled.</li>';
}

function do_build_directory_output($path)
{
    global $MAKE_INSTALLERS__TOTAL_DIRS;
    $MAKE_INSTALLERS__TOTAL_DIRS++;
    return '<li>Directory "' . escape_html($path) . '" traversed.</li>';
}

function do_build_archive_output($file, $new_output)
{
    $version_dotted = get_version_dotted();

    $builds_path = get_builds_path();
    return '
        <div class="zip_surround">
        <h2>Compiling archive file "<a href="' . escape_html($file) . '" title="Download the file.">' . escape_html($builds_path . $version_dotted . '/' . $file) . '</a>"</h2>
        <p>' . nl2br(trim(escape_html($new_output))) . '</p>
        </div>';
}

function populate_build_files_list($dir = '', $pretend_dir = '')
{
    require_code('files');

    disable_php_memory_limit();

    global $MAKE_INSTALLERS__FILE_ARRAY, $MAKE_INSTALLERS__DIR_ARRAY;

    $builds_path = get_builds_path();

    $out = '';

    $version_branch = get_version_branch();

    // Imply files into the root that we would have skipped
    if ($pretend_dir == '') {
        $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . '_config.php'] = '';
    }
    if ($pretend_dir == 'data_custom/') {
        $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . 'execute_temp.php'] = file_get_contents(get_file_base() . '/data_custom/execute_temp.php.bundle');
    }

    // Go over files in the directory
    $full_dir = get_file_base() . '/' . $dir;
    $dh = opendir($full_dir);
    while (($file = readdir($dh)) !== false) {
        $is_dir = is_dir(get_file_base() . '/' . $dir . $file);

        if (should_ignore_file($pretend_dir . $file, IGNORE_NONBUNDLED_SCATTERED | IGNORE_CUSTOM_DIR_SUPPLIED_CONTENTS | IGNORE_CUSTOM_DIR_GROWN_CONTENTS | IGNORE_CUSTOM_ZONES | IGNORE_CUSTOM_THEMES | IGNORE_NON_EN_SCATTERED_LANGS | IGNORE_BUNDLED_UNSHIPPED_VOLATILE | IGNORE_REVISION_FILES, 0)) {
            continue;
        }

        if ($is_dir) {
            $num_files = count($MAKE_INSTALLERS__FILE_ARRAY);
            $MAKE_INSTALLERS__DIR_ARRAY[] = $pretend_dir . $file;
            @mkdir($builds_path . '/builds/build/' . $version_branch . '/' . $pretend_dir . $file, 0777);
            fix_permissions($builds_path . '/builds/build/' . $version_branch . '/' . $pretend_dir . $file);
            $_out = populate_build_files_list($dir . $file . '/', $pretend_dir . $file . '/');
            if ($num_files == count($MAKE_INSTALLERS__FILE_ARRAY)) { // Empty, effectively (maybe was from a non-bundled addon) - don't use it
                array_pop($MAKE_INSTALLERS__DIR_ARRAY);
                rmdir($builds_path . '/builds/build/' . $version_branch . '/' . $pretend_dir . $file);
            } else {
                $out .= $_out;
            }
        } else {
            // Reset volatile files to how they should be by default (see also list in install.php)
            if (($pretend_dir . $file) == '_config.php') {
                $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file] = '';
            } elseif (($pretend_dir . $file) == 'themes/map.ini') {
                $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file] = 'default=default' . "\n";
            } elseif ($pretend_dir . $file == 'data_custom/functions.bin') {
                $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file] = '';
            } elseif ($pretend_dir . $file == 'data_custom/errorlog.php') {
                $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file] = "<" . "?php return; ?" . ">\n";
            } elseif ($pretend_dir . $file == 'data_custom/execute_temp.php') { // So that code can't be executed
                continue; // We'll add this back in later
            } // Update time of version in version.php
            elseif ($pretend_dir . $file == 'sources/version.php') {
                $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file] = preg_replace('/\d{10}/', strval(time()), file_get_contents(get_file_base() . '/' . $dir . $file), 1); // Copy file as-is
            } else {
                $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file] = file_get_contents(get_file_base() . '/' . $dir . $file);
            }

            // Write the file out
            cms_file_put_contents_safe($builds_path . '/builds/build/' . $version_branch . '/' . $pretend_dir . $file, $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file], FILE_WRITE_FIX_PERMISSIONS);
        }
    }

    $out .= do_build_directory_output($pretend_dir);
    return $out;
}

function make_files_manifest() // Builds files.bin, the Composr file manifest (used for integrity checks)
{
    global $MAKE_INSTALLERS__FILE_ARRAY;

    disable_php_memory_limit();

    require_code('version2');

    if (count($MAKE_INSTALLERS__FILE_ARRAY) == 0) {
        populate_build_files_list();
    }

    $files = array();
    foreach ($MAKE_INSTALLERS__FILE_ARRAY as $file => $contents) {
        if ($file == 'data/files.bin') {
            continue;
        }

        if ($file == 'sources/version.php') {
            $contents = preg_replace('/\d{10}/', '', $contents); // Not interested in differences in file time
        }

        $files[$file] = array(sprintf('%u', crc32(preg_replace('#[\r\n\t ]#', '', $contents))));
    }

    require_code('files');

    $file_manifest = serialize($files);

    cms_file_put_contents_safe(get_file_base() . '/data/files.bin', $file_manifest, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);

    $MAKE_INSTALLERS__FILE_ARRAY['data/files.bin'] = $file_manifest;

    // Write the file out
    require_code('version2');
    $version_branch = get_version_branch();
    $builds_path = get_builds_path();
    cms_file_put_contents_safe($builds_path . '/builds/build/' . $version_branch . '/data/files.bin', $file_manifest, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
}

function make_database_manifest() // Builds db_meta.bin, which is used for database integrity checks
{
    require_code('database_relations');

    $GLOBALS['NO_DB_SCOPE_CHECK'] = true;

    // Work out what addons everything belongs to...

    $table_addons = array();
    $index_addons = array();
    $privilege_addons = array();

    require_code('files2');
    $files = get_directory_contents(get_file_base());
    $files[] = 'install.php';
    foreach ($files as $file) {
        if (substr($file, -4) != '.php' && substr($file, -strlen('_custom')) != '_custom') {
            continue;
        }

        $contents = file_get_contents(get_file_base() . '/' . $file);
        $matches = array();
        if (preg_match('#@package\s+(\w+)\r?\n#', $contents, $matches) != 0) {
            $addon = $matches[1];
            if ($addon == 'installer') {
                $addon = 'core';
            }

            $table_regexp = '#->create_table\(\'(\w+)\'#';
            $table_matches = array();
            $table_num_matches = preg_match_all($table_regexp, $contents, $table_matches);
            for ($i = 0; $i < $table_num_matches; $i++) {
                $table_name = $table_matches[1][$i];
                $table_addons[$table_name] = $addon;
            }

            $index_regexp = '#->create_index\(\'(\w+)\',\s*\'([\#\w]+)\'#';
            $index_matches = array();
            $index_num_matches = preg_match_all($index_regexp, $contents, $index_matches);
            for ($i = 0; $i < $index_num_matches; $i++) {
                $table_name = $index_matches[1][$i];
                $index_name = $index_matches[2][$i];
                $universal_index_key = $table_name . '__' . $index_name;
                $index_addons[$universal_index_key] = $addon;
            }

            if ($file == 'sources/cns_install.php') {
                $privilege_regexp = '#\'(\w+)\'#';
            } elseif ($file == 'sources/database_action.php') {
                $privilege_regexp = '#array\(\'\w+\',\s*\'(\w+)\'\)#';
            } else {
                $privilege_regexp = '#add_privilege\(\'\w+\',\s*\'(\w+)\'#';
            }
            $privilege_matches = array();
            $privilege_num_matches = preg_match_all($privilege_regexp, $contents, $privilege_matches);
            for ($i = 0; $i < $privilege_num_matches; $i++) {
                $privilege_name = $privilege_matches[1][$i];
                $privilege_addons[$privilege_name] = $addon;
            }
        }
    }

    // Check we have found everything the database knows about...

    if (get_param_integer('skip_errors', 0) != 1) {
        $all_tables = collapse_1d_complexity('m_table', $GLOBALS['SITE_DB']->query_select('db_meta', array('m_table')));
        foreach ($all_tables as $table_name) {
            if (!array_key_exists($table_name, $table_addons)) {
                if (!table_has_purpose_flag($table_name, TABLE_PURPOSE__NON_BUNDLED | TABLE_PURPOSE__NOT_KNOWN)) {
                    warn_exit('Table ' . $table_name . ' in meta database could not be sourced.');
                }
            }
        }

        $all_indices = $GLOBALS['SITE_DB']->query_select('db_meta_indices', array('i_name', 'i_table'));
        foreach ($all_indices as $index) {
            $table_name = $index['i_table'];
            $index_name = $index['i_name'];

            $universal_index_key = $table_name . '__' . $index_name;

            if (!isset($index_addons[$universal_index_key])) {
                if (!array_key_exists($table_name, $table_addons)) {
                    if (!table_has_purpose_flag($table_name, TABLE_PURPOSE__NON_BUNDLED | TABLE_PURPOSE__NOT_KNOWN)) {
                        warn_exit('Index ' . $index_name . ' in meta database could not be sourced.');
                    }
                } else {
                    $index_addons[$universal_index_key] = $table_addons[$table_name];
                }
            }
        }

        $all_privileges = collapse_1d_complexity('the_name', $GLOBALS['SITE_DB']->query_select('privilege_list', array('the_name')));
        foreach ($all_privileges as $privilege_name) {
            if (!array_key_exists($privilege_name, $privilege_addons)) {
                attach_message('Privilege ' . $privilege_name . ' in meta database could not be sourced.', 'notice');
            }
        }
    }

    // Build up db_meta.bin structure...

    $field_details = $GLOBALS['SITE_DB']->query_select('db_meta', array('*'));
    $tables = array();
    foreach ($field_details as $field) {
        $table_name = $field['m_table'];

        if (!isset($table_addons[$table_name])) {
            continue;
        }

        if (!isset($tables[$table_name])) {
            $tables[$table_name] = array(
                'addon' => $table_addons[$table_name],
                'fields' => array(),
            );
        }
        $tables[$field['m_table']]['fields'][$field['m_name']] = $field['m_type'];
    }

    $index_details = $GLOBALS['SITE_DB']->query_select('db_meta_indices', array('*'));
    $indices = array();
    foreach ($index_details as $index) {
        $table_name = $index['i_table'];
        $index_name = trim($index['i_name'], '#');

        $universal_index_key = $table_name . '__' . $index['i_name'];

        if (!isset($index_addons[$universal_index_key])) {
            continue;
        }

        $indices[$universal_index_key] = array(
            'addon' => $index_addons[$universal_index_key],
            'name' => $index_name,
            'table' => $table_name,
            'fields' => explode(',', preg_replace('#\([^\)]*\)#', '', $index['i_fields'])),
            'is_full_text' => (strpos($index['i_name'], '#') !== false),
        );
    }

    $privilege_details = $GLOBALS['SITE_DB']->query_select('privilege_list', array('*'));
    $privileges = array();
    foreach ($privilege_details as $privilege) {
        if (!isset($privilege_addons[$privilege['the_name']])) {
            continue;
        }

        $privileges[$privilege['the_name']] = array(
            'addon' => $privilege_addons[$privilege['the_name']],
            'section' => $privilege['p_section'],
            'default' => $privilege['the_default'],
        );
    }

    $data = array(
        'tables' => $tables,
        'indices' => $indices,
        'privileges' => $privileges,
    );

    // Save
    require_code('files');
    $path = get_file_base() . '/data/db_meta.bin';
    cms_file_put_contents_safe($path, serialize($data), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);

    $GLOBALS['NO_DB_SCOPE_CHECK'] = false;
}

function make_install_sql()
{
    global $SITE_INFO;

    // Where to build database to
    $database = 'make_release';
    $username = 'root';
    $password = isset($SITE_INFO['mysql_root_password']) ? $SITE_INFO['mysql_root_password'] : '';
    $table_prefix = 'cms_';

    // Build database
    require_code('install_headless');
    //$_GET['debug'] = '1';
    $test = do_install_to($database, $username, $password, $table_prefix, true, 'cns', null, null, null, null, null, null, false);
    if (!$test) {
        warn_exit(protect_from_escaping('Failed to execute installer, while building <kbd>install.sql</kbd>. It\'s likely that recursive write file permissions need setting.'));
    }

    // Get database connection
    $conn = new DatabaseConnector($database, get_db_site_host(), $username, $password, $table_prefix);

    // Remove caching
    require_code('database_relations');
    $table_purposes = get_table_purpose_flags();
    $GLOBALS['NO_DB_SCOPE_CHECK'] = true;
    foreach ($table_purposes as $table => $purpose) {
        if ((table_has_purpose_flag($table, TABLE_PURPOSE__FLUSHABLE)) && ($conn->table_exists($table))) {
            $conn->query_delete($table);
        }
    }
    $GLOBALS['NO_DB_SCOPE_CHECK'] = false;

    // Build SQL dump
    global $HAS_MULTI_LANG_CONTENT;
    $bak = $HAS_MULTI_LANG_CONTENT;
    $HAS_MULTI_LANG_CONTENT = false;
    $out_path = get_file_base() . '/install.sql';
    $out_file = fopen($out_path, 'wb');
    get_sql_dump($out_file, true, false, null, null, $conn);
    fclose($out_file);
    fix_permissions($out_path);
    sync_file($out_path);
    $HAS_MULTI_LANG_CONTENT = $bak;

    // Run some checks to make sure our process is not buggy...

    $contents = cms_file_get_contents_safe(get_file_base() . '/install.sql');

    // Not with forced charsets or other contextual noise
    if (strpos($contents, "\n" . 'SET') !== false) {
        warn_exit('install.sql: Contains unwanted context');
    }
    if (preg_match('#\d+ SET #', $contents) != 0) {
        warn_exit('install.sql: Contains unwanted context');
    }

    // Old way of specifying table types
    if (strpos($contents, ' TYPE=') !== false) {
        warn_exit('install.sql: Change TYPE= to ENGINE=');
    }

    // Not with bundled addons
    if (strpos($contents, 'CREATE TABLE cms_workflow_') !== false) {
        warn_exit('install.sql: Contains non-bundled addons');
    }

    // Not with wrong table prefixes / multiple installs
    if (preg_match('#CREATE TABLE cms\d+\_#', $contents) != 0) {
        warn_exit('install.sql: Contains a version-prefixed install');
    }
    if (preg_match('#CREATE TABLE cms\_#', $contents) == 0) {
        warn_exit('install.sql: Does not contain a standard-prefixed install');
    }

    // Not having been run
    if (preg_match('#INSERT INTO cms\_cache#i', $contents) != 0) {
        warn_exit('install.sql: Contains cache data');
    }
    if (preg_match('#INSERT INTO cms\_stats#i', $contents) != 0) {
        warn_exit('install.sql: Contains stat data - site should not have been loaded ever yet');
    }

    // Out-dated version
    $v = float_to_raw_string(cms_version_number());
    $version_marker = '\'version\', \'' . $v . '\'';
    if (strpos($contents, $version_marker) === false) {
        warn_exit('install.sql: Contains wrong version (you need to rebuild it for each non-patch update)');
    }

    // Do split...

    $split_points = array(
        '',
        'DROP TABLE IF EXISTS cms_db_meta;',
        'DROP TABLE IF EXISTS cms_f_polls;',
        'DROP TABLE IF EXISTS cms_member_privileges;',
    );

    // Check we can find split points
    $contents = cms_file_get_contents_safe(get_file_base() . '/install.sql');
    foreach ($split_points as $p) {
        if ($p != '') {
            if (strpos($contents, $p) === false) {
                warn_exit('install.sql: Cannot find split point ' . $p);
            }
        }
    }
    $froms = array();
    foreach ($split_points as $p) {
        if ($p == '') {
            $from = 0;
        } else {
            $from = strpos($contents, $p);
        }
        $froms[] = $from;
    }
    sort($froms);
    for ($i = 0; $i < 4; $i++) {
        $from = $froms[$i];
        if ($i < 3) {
            $to = $froms[$i + 1];
            $segment = substr($contents, $from, $to - $from);
        } else {
            $segment = substr($contents, $from);
        }
        require_code('files');
        cms_file_put_contents_safe(get_file_base() . '/install' . strval($i + 1) . '.sql', $segment, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    }
}

// See phpdoc_parser.php for functions.bin manifest building

// Also see chmod_consistency.php, and build_rewrite_rules.php

<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_release_build
 */

/*EXTRA FUNCTIONS: shell_exec|DOM.*|pretty_print_dom_document*/

function init__make_release()
{
    require_code('files2');

    // Make sure builds folder exists
    get_builds_path();

    // Tracking
    global $MAKE_INSTALLERS__FILE_ARRAY, $MAKE_INSTALLERS__DIR_ARRAY, $MAKE_INSTALLERS__TOTAL_DIRS, $MAKE_INSTALLERS__TOTAL_FILES;
    $MAKE_INSTALLERS__FILE_ARRAY = [];
    $MAKE_INSTALLERS__DIR_ARRAY = [];
    $MAKE_INSTALLERS__TOTAL_DIRS = 0;
    $MAKE_INSTALLERS__TOTAL_FILES = 0;
}

function make_installers($skip_file_grab = false)
{
    foreach (['zip', 'tar', 'gzip'] as $cmd) {
        $result = _shell_exec_bin($cmd . ' -h 2>&1');
        if (!is_string($result) || ($result == '')) {
            warn_exit('Missing command in path: ' . $cmd);
        }
    }

    cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

    global $MAKE_INSTALLERS__FILE_ARRAY, $MAKE_INSTALLERS__DIR_ARRAY, $MAKE_INSTALLERS__TOTAL_DIRS, $MAKE_INSTALLERS__TOTAL_FILES;

    // Integrity check: files_previous manifest
    require_code('upgrade_integrity_scan');
    $manifest = load_integrity_manifest(true);
    if (cms_empty_safe($manifest)) {
        warn_exit(do_lang_tempcode('MANIFEST_CORRUPT_FILES_PREVIOUS'));
    }
    unset($manifest);

    require_code('files');

    // Start output
    $out = '';
    $out .= '<p>A Composr build is being compiled and packed up into installation packages.</p>';

    require_code('version');
    require_code('version2');
    $version = cms_version_number();
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

        // Generate key pair
        require_code('encryption');
        generate_telemetry_key_pair($version, (post_param_string('overwrite_key_pair', '') != ''));

        // Copy new public key to our static "telemetry" file
        @copy(get_file_base() . '/data_custom/keys/telemetry-' . float_to_raw_string($version, 2, true) . '.pub', get_file_base() . '/data/keys/telemetry.pub');
        fix_permissions(get_file_base() . '/data/keys/telemetry.pub');

        if (post_param_string('skip_data_files', '') == '') {
            download_latest_data_files();
        }

        if (post_param_string('rebuild_sql', '') != '') { // TODO: should database_manifest build from this instead so that it matches what we expect without manually reinstalling the dev install of the software?
            make_install_sql();
        }
        make_database_manifest();

        // Re-generate sprites
        require_code('themes3');
        generate_svg_sprite('default', false, false);
        generate_svg_sprite('default', true, false);

        // Update automatic versions in bundled addons
        if (post_param_integer('bump_addon_versions', 0) == 1) {
            require_code('addons2');
            $hooks = find_all_hooks('systems', 'addon_registry', false);
            foreach ($hooks as $addon_name => $hook_dir) {
                if ($hook_dir == 'sources_custom') {
                    continue;
                }

                update_addon_auto_version($hook_dir, $addon_name);
            }

            $out .= '<ul>Updated automatic versions of bundled addons.</ul>';
        }

        // Get file data array; must be done second-to-last to ensure all updated files are transported to the build
        $out .= '<ul>';
        $out .= populate_build_files_list();
        $out .= '</ul>';

        // Generate file manifest; must be done last so it accounts for all updated files
        make_files_manifest();
    }

    // Integrity check: files manifest
    require_code('upgrade_integrity_scan');
    $manifest = load_integrity_manifest();
    if (cms_empty_safe($manifest)) {
        warn_exit(do_lang_tempcode('MANIFEST_CORRUPT_FILES'));
    }
    unset($manifest);

    //header('Content-Type: text/plain; charset=' . get_charset());var_dump(array_keys($MAKE_INSTALLERS__FILE_ARRAY));exit(); Useful for testing quickly what files will be built

    // What we'll be building
    $bundled = $builds_path . '/builds/' . $version_dotted . '/composr-' . $version_dotted . '.tar';
    $quick_zip = $builds_path . '/builds/' . $version_dotted . '/composr_quick_installer-' . $version_dotted . '.zip';
    $manual_zip = $builds_path . '/builds/' . $version_dotted . '/composr_manualextraction_installer-' . $version_dotted . '.zip';
    $aps_zip = $builds_path . '/builds/' . $version_dotted . '/composr-' . $version_dotted . '.app.zip'; // APS package
    $omni_upgrader = $builds_path . '/builds/' . $version_dotted . '/composr_upgrader-' . $version_dotted . '.cms';

    // Flags
    $make_quick = (get_param_integer('skip_quick', 0) == 0);
    $make_manual = (get_param_integer('skip_manual', 0) == 0);
    $make_bundled = (get_param_integer('skip_bundled', 0) == 0);
    $make_aps = false; // We don't use it right now and need to speed this all up (get_param_integer('skip_aps', 0) == 0);
    $make_omni_upgrader = (post_param_string('make_omni_upgrader', '') != '');
    $force_local = (get_param_integer('force_local', 0) == 1) ? '--force-local ' : ''; // tar flag for Windows

    cms_disable_time_limit();
    disable_php_memory_limit();

    // Build quick installer
    if ($make_quick) {
        // Write out our installer data file
        require_code('tar');
        $data_file = tar_open($builds_path . '/builds/' . $version_dotted . '/data.cms', 'wb');
        $zip_file_array = [];
        $offsets = [];
        $sizes = [];
        foreach ($MAKE_INSTALLERS__FILE_ARRAY as $path => $data) {
            $offsets[$path] = tar_add_file($data_file, $path, $data, 0644, is_file(get_file_base() . '/' . $path) ? filemtime(get_file_base() . '/' . $path) : time());
            $sizes[$path] = strlen($data);
        }
        tar_close($data_file);
        fix_permissions($builds_path . '/builds/' . $version_dotted . '/data.cms');

        // The installer does a size check for upload integrity
        $archive_size = filesize($builds_path . '/builds/' . $version_dotted . '/data.cms');

        // The installer also does an md5 check to check integrity
        $md5_file = md5_file($builds_path . '/builds/' . $version_dotted . '/data.cms');

        // Write out our PHP installer file
        $file_count = count($MAKE_INSTALLERS__FILE_ARRAY);
        $size_list = '';
        $offset_list = '';
        $file_list = '';
        foreach (array_keys($MAKE_INSTALLERS__FILE_ARRAY) as $path) { // $MAKE_INSTALLERS__FILE_ARRAY is Current path->contents. We need number->path, so we can count through them without having to have the array with us. We end up with this in string form, as it goes in our file
            $out .= do_build_file_output($path);
            $size_list .= '\'' . $path . '\'=>' . strval($sizes[$path]) . ',' . "\n";
            $offset_list .= '\'' . $path . '\'=>' . strval($offsets[$path]) . ',' . "\n";
            $file_list .= '\'' . $path . '\',' . "\n";
        }

        // Build install.php, which has to have all our data.cms file offsets put into it (data.cms is an uncompressed ZIP, but the quick installer cheats - it can't truly read arbitrary ZIPs)
        $code = cms_file_get_contents_safe(get_file_base() . '/install.php', FILE_READ_LOCK);
        $code = clean_php_file_for_eval($code);

        if (strpos($code, '/* Do not remove this comment: quick installer injection */') === false) {
            warn_exit('Could not find the comment in install.php to tell us where to inject the quick installer code.');
        }

        $quick_installer = "
            /* QUICK INSTALLER CODE starts */

            global \$FILE_ARRAY,\$SIZE_ARRAY,\$OFFSET_ARRAY,\$DIR_ARRAY,\$DATADOTCMS_FILE;
            \$OFFSET_ARRAY = [{$offset_list}];
            \$SIZE_ARRAY = [{$size_list}];
            \$FILE_ARRAY = [{$file_list}];

            if (!is_file('data.cms')) {
                header('Content-Type: text/plain; charset=utf-8');
                exit('data.cms does not exist -- did you upload it?');
            }
            if (filesize('data.cms') !== " . strval($archive_size) . ") {
                header('Content-Type: text/plain; charset=utf-8');
                exit('data.cms failed file size validation. It might not have been fully uploaded, or you are using the wrong one for this version.');
            }
            if (md5_file('data.cms') !== '{$md5_file}') {
                header('Content-Type: text/plain; charset=utf-8');
                exit('data.cms failed checksum validation; the file is probably corrupt or was tampered. Please re-download the quick installer directly from the homesite and upload again to your server.');
            }

            // NB: We do not want to open the file until we verified MD5 integrity above. This helps reduce security risks.
            \$DATADOTCMS_FILE = @fopen('data.cms','rb');
            if (\$DATADOTCMS_FILE === false) {
                header('Content-Type: text/plain; charset=utf-8');
                exit('Could not open data.cms -- make sure the server has read permissions for it');
            }

            /**
             * Get the raw data of a specific file from data.cms.
             *
             * @param  PATH \$path The relative path to the file
             * @return string The file data (blank: not found)
             */
            function file_array_get(string \$path) : string
            {
                global \$OFFSET_ARRAY,\$SIZE_ARRAY,\$DATADOTCMS_FILE,\$FILE_BASE;

                if (substr(\$path,0,strlen(\$FILE_BASE.'/')) == \$FILE_BASE.'/')
                    \$path = substr(\$path,strlen(\$FILE_BASE.'/'));

                if (!isset(\$OFFSET_ARRAY[\$path])) return '';
                \$offset = \$OFFSET_ARRAY[\$path];
                \$size = \$SIZE_ARRAY[\$path];
                if (\$size == 0) return '';
                fseek(\$DATADOTCMS_FILE,\$offset,SEEK_SET);

                // TODO: Unacceptable return type for this function; must be string
                /*
                if (\$size>1024*1024) {
                    return [\$size,\$DATADOTCMS_FILE,\$offset];
                }
                */

                \$data = fread(\$DATADOTCMS_FILE,\$size);
                return \$data;
            }

            /**
             * Find files within a given directory inside the file array.
             *
             * @param  PATH \$path The relative path to scan
             * @return array Sub-directories and files
             */
            function file_array_scandir(string \$path) : array
            {
                global \$FILE_ARRAY;
                \$ret = [];

                foreach (\$FILE_ARRAY as \$file) {
                    if (strpos(\$file, str_replace('\\\\', '/', \$path)) === 0) {
                        \$_ret = str_replace(\$path, '', \$file); // Strip specified path off of file to make it relative
                        if ((substr(\$path, -1) != '/') && (\$_ret[0] != '/')) { // Actually this is a partial match, not a full match; skip
                            continue;
                        }
                        if (\$_ret[0] == '/') { // Strip leading slash
                            \$_ret = substr(\$_ret, 1);
                        }
                        \$ret[] = \$_ret;
                    }
                }

                return \$ret;
            }

            /**
             * Find whether the given path exists in the archive.
             *
             * @param  PATH \$path The relative path to check
             * @return boolean Whether it exists
             */
            function file_array_exists(string \$path) : bool
            {
                global \$OFFSET_ARRAY;
                return (isset(\$OFFSET_ARRAY[\$path]));
            }

            /**
             * Get a data.cms file at the given file array index / offset.
             *
             * @param  integer \$i The offset
             * @return array A double of the relative path and the contents of the file
             */
            function file_array_get_at(int \$i) : array
            {
                global \$FILE_ARRAY;
                \$name = \$FILE_ARRAY[\$i];
                return [\$name,file_array_get(\$name)];
            }

            /**
             * Get how many files are in the archive.
             *
             * @return integer How many files exist
             */
            function file_array_count() : int
            {
                return " . strval($file_count) . ";
            }

            ";
        $quick_installer = preg_replace('#^\t{3}#m', '', $quick_installer); // Format it correctly
        global $MAKE_INSTALLERS__DIR_ARRAY;
        foreach ($MAKE_INSTALLERS__DIR_ARRAY as $dir) {
            $quick_installer .= '$DIR_ARRAY[]=\'' . $dir . '\';' . "\n";
        }
        $quick_installer .= '/* QUICK INSTALLER CODE ends */';
        $code = str_replace('/* Do not remove this comment: quick installer injection */', $quick_installer, $code);
        $code = "<" . "?php " . $code;
        cms_file_put_contents_safe($builds_path . '/builds/' . $version_dotted . '/install.php', $code, FILE_WRITE_FIX_PERMISSIONS);

        @unlink($quick_zip);

        chdir($builds_path . '/builds/' . $version_dotted);
        $cmd = 'zip -r -9 ' . cms_escapeshellarg($quick_zip) . ' ' . cms_escapeshellarg('data.cms') . ' ' . cms_escapeshellarg('install.php');
        $cmd_result = _shell_exec_bin($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        $out .= do_build_archive_output($quick_zip, $output2);

        chdir(get_file_base() . '/data_custom/builds');
        $cmd = 'zip -r -9 ' . cms_escapeshellarg($quick_zip) . ' ' . cms_escapeshellarg('readme.txt');
        $cmd_result = _shell_exec_bin($cmd . ' 2>&1');
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
        $cmd = 'zip -r -9 ' . cms_escapeshellarg($manual_zip) . ' *';
        $cmd_result = _shell_exec_bin($cmd . ' 2>&1');
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
        $success = copy(get_file_base() . '/install.sql', $builds_path . '/builds/build/' . $version_branch . '/install.sql');
        fix_permissions($builds_path . '/builds/build/' . $version_branch . '/install.sql');
        $success = copy(get_file_base() . '/_config.php.template', $builds_path . '/builds/build/' . $version_branch . '/_config.php.template');
        fix_permissions($builds_path . '/builds/build/' . $version_branch . '/_config.php.template');

        // Do the main work...

        chdir($builds_path . '/builds/build/' . $version_branch);
        if (cms_strtoupper_ascii(substr(PHP_OS, 0, 3)) == 'WIN') {
            $cmd = 'tar -cvf ' . $force_local . cms_escapeshellarg($bundled) . ' *'; // --force-local may be required for Windows style absolute paths https://stackoverflow.com/a/37996249/362006
        } else {
            $cmd = 'tar -cvf ' . cms_escapeshellarg($bundled) . ' *';
        }
        $cmd_result = _shell_exec_bin($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        //$out .= do_build_archive_output($v, $output2);  Don't mention, as will get auto-deleted after gzipping anyway

        chdir(get_file_base() . '/data_custom/builds');
        if (cms_strtoupper_ascii(substr(PHP_OS, 0, 3)) == 'WIN') {
            $cmd = 'tar -rvf ' . $force_local . cms_escapeshellarg($bundled) . ' readme.txt'; // --force-local may be required for Windows style absolute paths https://stackoverflow.com/a/37996249/362006
        } else {
            $cmd = 'tar -rvf ' . cms_escapeshellarg($bundled) . ' readme.txt';
        }
        $cmd_result = _shell_exec_bin($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        //$out .= do_build_archive_output($v, $output2);  Don't mention, as will get auto-deleted after gzipping anyway

        chdir($builds_path . '/builds/build/' . $version_branch);
        $cmd = 'gzip -n ' . cms_escapeshellarg($bundled);
        $cmd_result = _shell_exec_bin($cmd . ' 2>&1');
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
        $app_meta_doc->loadXML(cms_file_get_contents_safe(get_file_base() . '/aps/APP-META.xml', FILE_READ_LOCK | FILE_READ_BOM));

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
            $changelog_version_entry_el->nodeValue = 'Composr ' . $version_dotted . ' release notes: https://composr.app/data_custom/redirect_release_notes.php?version=' . urlencode($version_dotted);

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
        $app_list_doc->loadXML(cms_file_get_contents_safe(get_file_base() . '/aps/APP-LIST.xml', FILE_READ_LOCK | FILE_READ_BOM));

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
        $cmd = 'zip -r -9 -v ' . cms_escapeshellarg($aps_zip) . ' htdocs images scripts test APP-LIST.xml APP-META.xml';
        $cmd_result = _shell_exec_bin($cmd . ' 2>&1');
        if (!is_string($cmd_result)) {
            fatal_exit('Failed to run: ' . $cmd);
        }
        $output2 = $cmd . ':' . "\n" . $cmd_result;
        $out .= do_build_archive_output($aps_zip, $output2);

        // Undo temporary renaming
        rename($builds_path . '/builds/aps/htdocs/', $builds_path . '/builds/build/' . $version_branch . '/');

        // Delete the copied files
        deldir_contents($builds_path . '/builds/aps/', false, true);

        chdir(get_file_base());
    }

    // Build omni-upgrader
    if ($make_omni_upgrader) {
        @unlink($omni_upgrader);

        // Do the main work
        chdir($builds_path . '/builds/build/' . $version_branch);
        if (cms_strtoupper_ascii(substr(PHP_OS, 0, 3)) == 'WIN') {
            $cmd = 'tar --exclude=_config.php --exclude=install.php -cvf ' . $force_local . cms_escapeshellarg($omni_upgrader) . ' *'; // --force-local may be required for Windows style absolute paths https://stackoverflow.com/a/37996249/362006
        } else {
            $cmd = 'tar --exclude=_config.php --exclude=install.php -cvf ' . cms_escapeshellarg($omni_upgrader) . ' *';
        }
        $cmd_result = _shell_exec_bin($cmd . ' 2>&1');
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
    $_CREATED_FILES = [];
    $_MODIFIED_FILES = [];

    return $out;
}

function _shell_exec_bin($cmd)
{
    static $bin_path = null;

    if ($bin_path === null) {
        if ((cms_strtoupper_ascii(substr(PHP_OS, 0, 3)) == 'WIN') && file_exists('C:\cygwin64\bin\\')) {
            $bin_path = 'C:\cygwin64\bin\\';
        } else {
            $bin_path = '';
        }
    }

    return shell_exec($bin_path . $cmd);
}

// Used in the APS build process
function make_file_elements(DOMDocument $app_list_doc, DOMElement $files_el, $dir_path)
{
    $dh = @opendir($dir_path);
    if ($dh === false) {
        return false;
    }
    while (($entry = readdir($dh)) !== false) {
        if ($entry == '.' || $entry == '..') {
            continue;
        }

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
    closedir($dh);

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

        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
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
        closedir($dh);
        return true;
    } elseif (is_file($path)) {
        return copy($path, $dest);
    }

    return false;
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
        <div class="zip-surround">
        <h2>Compiling archive file "<a href="' . escape_html($file) . '" title="Download the file.">' . escape_html($builds_path . $version_dotted . '/' . $file) . '</a>"</h2>
        <p>' . nl2br(trim(escape_html($new_output))) . '</p>
        </div>';
}

function populate_build_files_list($dir = '', $pretend_dir = '')
{
    require_code('files');
    require_code('version');
    require_code('version2');

    disable_php_memory_limit();

    global $MAKE_INSTALLERS__FILE_ARRAY, $MAKE_INSTALLERS__DIR_ARRAY;

    $builds_path = get_builds_path();

    $out = '';

    $version = cms_version_number();
    $version_branch = get_version_branch();

    // Go over files in the directory
    $full_dir = get_file_base() . '/' . $dir;
    $dh = opendir($full_dir);
    while (($file = readdir($dh)) !== false) {
        $is_dir = is_dir(get_file_base() . '/' . $dir . $file);

        if (($dir != 'data_custom') || (should_ignore_file($pretend_dir . $file)) || (!should_ignore_file($pretend_dir . $file, IGNORE_SHIPPED_VOLATILE))) { // If it isn't a violatile data_custom file which we are going to re-write dynamically
            if (($pretend_dir . $file != 'temp') && (($pretend_dir != 'temp/') || !in_array($file, ['.htaccess', 'index.html'])) && should_ignore_file($pretend_dir . $file, IGNORE_NONBUNDLED | IGNORE_FLOATING | IGNORE_CUSTOM_DIRS | IGNORE_UPLOADS | IGNORE_CUSTOM_ZONES | IGNORE_CUSTOM_THEMES | IGNORE_CUSTOM_LANGS | IGNORE_UNSHIPPED_VOLATILE | IGNORE_REVISION_FILES | IGNORE_ALIEN)) {
                continue;
            }
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
                continue; // Actually we do not want _config.php in the build; its presence means we completed installation
            } elseif (($pretend_dir . $file) == 'themes/map.ini') {
                $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file] = 'default=default' . "\n"; // Ensure theme maps are reset to defaults
            } elseif ($pretend_dir . $file == 'data_custom/errorlog.php') {
                continue; // We'll add this back in later as we want to reset its contents
            } elseif ($pretend_dir . $file == 'data_custom/execute_temp.php') {
                continue; // We'll add the bundle version in later
            } elseif ($pretend_dir . $file == 'sources/version.php') { // Update cms_version_time in version.php
                $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file] = preg_replace('/\d{10}/', strval(time()), cms_file_get_contents_safe(get_file_base() . '/' . $dir . $file), 1);
            } else { // Everything else, copy as-is
                $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file] = cms_file_get_contents_safe(get_file_base() . '/' . $dir . $file);
            }

            // Write the file out
            cms_file_put_contents_safe($builds_path . '/builds/build/' . $version_branch . '/' . $pretend_dir . $file, $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . $file], FILE_WRITE_FIX_PERMISSIONS);
        }
    }
    closedir($dh);

    // Write out files we otherwise skipped
    if ($pretend_dir == 'data_custom/') {
        $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . 'execute_temp.php.bundle'] = cms_file_get_contents_safe(get_file_base() . '/data_custom/execute_temp.php.bundle', FILE_READ_LOCK);
        cms_file_put_contents_safe($builds_path . '/builds/build/' . $version_branch . '/' . $pretend_dir . 'execute_temp.php.bundle', $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . 'execute_temp.php.bundle'], FILE_WRITE_FIX_PERMISSIONS);

        // TODO: not sure why this one is getting skipped; it should not be
        $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . 'firewall_rules.txt'] = cms_file_get_contents_safe(get_file_base() . '/data_custom/firewall_rules.txt', FILE_READ_LOCK);
        cms_file_put_contents_safe($builds_path . '/builds/build/' . $version_branch . '/' . $pretend_dir . 'firewall_rules.txt', $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . 'firewall_rules.txt'], FILE_WRITE_FIX_PERMISSIONS);

        $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . 'errorlog.php'] = "<" . "?php return; ?" . ">\n";
        cms_file_put_contents_safe($builds_path . '/builds/build/' . $version_branch . '/' . $pretend_dir . 'errorlog.php', $MAKE_INSTALLERS__FILE_ARRAY[$pretend_dir . 'errorlog.php'], FILE_WRITE_FIX_PERMISSIONS);
    }

    $out .= do_build_directory_output($pretend_dir);
    return $out;
}

function make_files_manifest() // Builds files.bin, the Composr file manifest (used for integrity checks)
{
    global $MAKE_INSTALLERS__FILE_ARRAY;

    disable_php_memory_limit();

    require_code('version2');

    if (empty($MAKE_INSTALLERS__FILE_ARRAY)) {
        populate_build_files_list();
    }

    $files = [];
    foreach ($MAKE_INSTALLERS__FILE_ARRAY as $file => $contents) {
        if ($file == 'data/files.bin') {
            continue;
        }

        if ($file == 'sources/version.php') {
            $contents = preg_replace('/\d{10}/', '', $contents); // Not interested in differences in file time
        }

        $files[$file] = [sprintf('%u', crc32(preg_replace('#[\r\n\t ]#', '', $contents)))];
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
    if (!addon_installed('meta_toolkit')) {
        warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('meta_toolkit')));
    }

    require_code('database_relations');

    push_db_scope_check(false);

    // Work out what addons everything belongs to...

    $table_addons = [];
    $index_addons = [];
    $privilege_addons = [];

    require_code('files2');
    $files = get_directory_contents(get_file_base(), '', null);
    $files[] = 'install.php';
    foreach ($files as $path) {
        if (substr($path, -4) != '.php' && substr($path, -strlen('_custom')) != '_custom') {
            continue;
        }

        // NB: instead of using the database schema (which may be malformed in a development environment), we scan the code directly for db operations.
        $contents = cms_file_get_contents_safe(get_file_base() . '/' . $path);
        $matches = [];
        if (preg_match('#@package\s+(\w+)\r?\n#', $contents, $matches) != 0) {
            $addon_name = $matches[1];
            if ($addon_name == 'installer') {
                $addon_name = 'core';
            }

            $table_regexp = '#->create_table\(\'(\w+)\'#';
            $table_matches = [];
            $table_num_matches = preg_match_all($table_regexp, $contents, $table_matches);
            for ($i = 0; $i < $table_num_matches; $i++) {
                $table_name = $table_matches[1][$i];
                $table_addons[$table_name] = $addon_name;
            }

            $index_regexp = '#->create_index\(\'(\w+)\',\s*\'([\#\w]+)\'#';
            $index_matches = [];
            $index_num_matches = preg_match_all($index_regexp, $contents, $index_matches);
            for ($i = 0; $i < $index_num_matches; $i++) {
                $table_name = $index_matches[1][$i];
                $index_name = $index_matches[2][$i];
                $universal_index_key = $table_name . '__' . $index_name;
                $index_addons[$universal_index_key] = $addon_name;
            }

            if ($path == 'sources/cns_install.php') {
                $privilege_regexp = '#\'(\w+)\'#';
            } elseif ($path == 'sources/permissions3.php') {
                $privilege_regexp = '#\[\'\w+\',\s*\'(\w+)\'\]#';
            } else {
                $privilege_regexp = '#add_privilege\(\'\w+\',\s*\'(\w+)\'#';
            }
            $privilege_matches = [];
            $privilege_num_matches = preg_match_all($privilege_regexp, $contents, $privilege_matches);
            for ($i = 0; $i < $privilege_num_matches; $i++) {
                $privilege_name = $privilege_matches[1][$i];
                $privilege_addons[$privilege_name] = $addon_name;
            }
        }
    }

    // Check we have found everything the database knows about...

    if (get_param_integer('skip_errors', 0) != 1) {
        $all_tables = collapse_1d_complexity('m_table', $GLOBALS['SITE_DB']->query_select('db_meta', ['m_table']));
        foreach ($all_tables as $table_name) {
            if (!array_key_exists($table_name, $table_addons)) {
                if (!table_has_purpose_flag($table_name, TABLE_PURPOSE__NON_BUNDLED | TABLE_PURPOSE__NOT_KNOWN)) {
                    warn_exit('Table ' . $table_name . ' in meta database could not be sourced.');
                }
            }
        }

        $all_indices = $GLOBALS['SITE_DB']->query_select('db_meta_indices', ['i_name', 'i_table']);
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

        $all_privileges = collapse_1d_complexity('the_name', $GLOBALS['SITE_DB']->query_select('privilege_list', ['the_name']));
        foreach ($all_privileges as $privilege_name) {
            if (!array_key_exists($privilege_name, $privilege_addons)) {
                attach_message('Privilege ' . $privilege_name . ' in meta database could not be sourced.', 'warn', false, true);
            }
        }
    }

    // Build up db_meta.bin structure...

    $field_details = $GLOBALS['SITE_DB']->query_select('db_meta', ['*']);
    $tables = [];
    foreach ($field_details as $field) {
        $table_name = $field['m_table'];

        if (!isset($table_addons[$table_name])) {
            continue;
        }

        if (!isset($tables[$table_name])) {
            $tables[$table_name] = [
                'addon' => $table_addons[$table_name],
                'fields' => [],
            ];
        }
        $tables[$field['m_table']]['fields'][$field['m_name']] = $field['m_type'];
    }

    $index_details = $GLOBALS['SITE_DB']->query_select('db_meta_indices', ['*']);
    $indices = [];
    foreach ($index_details as $index) {
        $table_name = $index['i_table'];
        $index_name = trim($index['i_name'], '#');

        $universal_index_key = $table_name . '__' . $index['i_name'];

        if (!isset($index_addons[$universal_index_key])) {
            continue;
        }

        $indices[$universal_index_key] = [
            'addon' => $index_addons[$universal_index_key],
            'name' => $index_name,
            'table' => $table_name,
            'fields' => explode(',', preg_replace('#\([^\)]*\)#', '', $index['i_fields'])),
            'is_full_text' => (strpos($index['i_name'], '#') !== false),
        ];
    }

    $privilege_details = $GLOBALS['SITE_DB']->query_select('privilege_list', ['*']);
    $privileges = [];
    foreach ($privilege_details as $privilege) {
        if (!isset($privilege_addons[$privilege['the_name']])) {
            continue;
        }

        $privileges[$privilege['the_name']] = [
            'addon' => $privilege_addons[$privilege['the_name']],
            'section' => $privilege['p_section'],
            'default' => $privilege['the_default'],
        ];
    }

    $data = [
        'tables' => $tables,
        'indices' => $indices,
        'privileges' => $privileges,
    ];

    // Save
    require_code('files');
    $path = get_file_base() . '/data/db_meta.bin';
    cms_file_put_contents_safe($path, serialize($data), FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);

    pop_db_scope_check();
}

function make_install_sql()
{
    global $SITE_INFO;

    // Where to build database to
    $database = uniqid('cmsmr_', false); // In case make release was previously run, we should randomise the DB name
    $username = 'root';
    $password = isset($SITE_INFO['mysql_root_password']) ? $SITE_INFO['mysql_root_password'] : '';
    $table_prefix = 'cms_'; // DO NOT CHANGE

    // Build database
    require_code('install_headless');
    // Un-comment to show results of install
    //$_GET['debug'] = '1';
    $test = do_install_to($database, $username, $password, $table_prefix, true, 'cns', null, null, null, null, null, [], false);
    if (!$test) {
        warn_exit(protect_from_escaping('Failed to execute installer, while building <kbd>install.sql</kbd>. It\'s likely that recursive write file permissions need setting.'));
    }

    // Get database connector
    $db = new DatabaseConnector($database, get_db_site_host(), $username, $password, $table_prefix);

    // Remove caching
    require_code('database_relations');
    $table_purposes = get_table_purpose_flags();
    push_db_scope_check(false);
    foreach ($table_purposes as $table => $purpose) {
        if ((table_has_purpose_flag($table, (TABLE_PURPOSE__FLUSHABLE | TABLE_PURPOSE__FLUSHABLE_AGGRESSIVE))) && ($db->table_exists($table))) {
            $db->query_delete($table);
        }
    }
    pop_db_scope_check();

    // Build SQL dump
    global $HAS_MULTI_LANG_CONTENT;
    $bak = $HAS_MULTI_LANG_CONTENT;
    $HAS_MULTI_LANG_CONTENT = false;
    require_code('files');
    $out_path = get_file_base() . '/install.sql';
    $out_file = cms_fopen_text_write($out_path);
    get_sql_dump($out_file, true, false, [], null, $db);
    fclose($out_file);
    fix_permissions($out_path);
    sync_file($out_path);
    $HAS_MULTI_LANG_CONTENT = $bak;

    // Run some checks to make sure our process is not buggy...

    $contents = cms_file_get_contents_safe(get_file_base() . '/install.sql', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);

    // Not with forced charsets or other contextual noise
    if (strpos($contents, "\n" . 'SET') !== false) {
        warn_exit('install.sql: Contains unwanted context (SET)');
    }
    if (preg_match('#\d+ SET #', $contents) != 0) {
        warn_exit('install.sql: Contains unwanted context (SET)');
    }

    // Old way of specifying table types
    if (strpos($contents, ' TYPE=') !== false) {
        warn_exit('install.sql: Table types must be defined as ENGINE= not TYPE=');
    }

    // Not with bundled addons
    if (strpos($contents, 'CREATE TABLE cms_workflow_') !== false) {
        warn_exit('install.sql: Detected the workflow non-bundled addon');
    }

    // Not with wrong table prefixes / multiple installs
    if (preg_match('#CREATE TABLE cms\d+_#', $contents) != 0) {
        warn_exit('install.sql: The table prefix must be cms_ not one with a version in it');
    }
    if (preg_match('#CREATE TABLE cms_#', $contents) == 0) {
        warn_exit('install.sql: The table prefix must be cms_');
    }

    // Not having been run
    if (preg_match('#INSERT INTO cms_cache#i', $contents) != 0) {
        warn_exit('install.sql: Attempted to insert cache data into the database; the headless site may have been loaded when it should not have');
    }
    if (preg_match('#INSERT INTO cms_stats#i', $contents) != 0) {
        warn_exit('install.sql: Attempted to insert stats data into the database; the headless site may have been loaded when it should not have');
    }

    // Out-dated version
    $v = float_to_raw_string(cms_version_number());
    $version_marker = '\'version\', \'' . $v . '\'';
    if (strpos($contents, $version_marker) === false) {
        warn_exit('install.sql: Wrong version generated; it should have been ' . $version_marker);
    }

    // Do split...

    $split_points = [
        '',
        'DROP TABLE IF EXISTS cms_db_meta;',
        'DROP TABLE IF EXISTS cms_f_polls;',
        'DROP TABLE IF EXISTS cms_member_privileges;',
    ];

    // Check we can find split points
    $contents = cms_file_get_contents_safe(get_file_base() . '/install.sql', FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
    foreach ($split_points as $p) {
        if ($p != '') {
            if (strpos($contents, $p) === false) {
                warn_exit('install.sql: Cannot find split point ' . $p);
            }
        }
    }
    $froms = [];
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
        $segment = trim($segment) . "\n";
        require_code('files');
        cms_file_put_contents_safe(get_file_base() . '/install' . strval($i + 1) . '.sql', $segment, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
    }
}

function download_latest_data_files()
{
    _download_latest_data_cert();
    _download_latest_data_ip_country();
    _download_latest_data_no_banning();
}

function _download_latest_data_cert()
{
    $data = http_get_contents('https://curl.haxx.se/ca/cacert.pem', ['convert_to_internal_encoding' => true, 'timeout' => 20.0]);
    if (strpos($data, 'BEGIN CERTIFICATE') === false) {
        fatal_exit('Error with certificates');
    }
    cms_file_put_contents_safe(get_file_base() . '/data/curl-ca-bundle.crt', $data, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
}

function _download_latest_data_ip_country()
{
    disable_php_memory_limit();

    $spreadsheet_data = '';

    $tmp_name_gzip = cms_tempnam();
    $myfile = fopen($tmp_name_gzip, 'wb');

    // We use strtotime yesterday because the new month's database might not be published until sometime after midnight UTC.
    cms_http_request('https://download.db-ip.com/free/dbip-country-lite-' . date('Y-m', strtotime('yesterday')) . '.csv.gz', ['convert_to_internal_encoding' => true, 'write_to_file' => $myfile, 'timeout' => 30.0]);

    fclose($myfile);

    $tmp_name_csv = cms_tempnam();
    $cmd = 'gzip -d -c ' . cms_escapeshellarg($tmp_name_gzip) . ' > ' . cms_escapeshellarg($tmp_name_csv);
    _shell_exec_bin($cmd);

    require_code('files_spreadsheets_read');
    $sheet_reader = spreadsheet_open_read($tmp_name_csv, 'IP_Country.txt', CMS_Spreadsheet_Reader::ALGORITHM_RAW);
    while (($record = $sheet_reader->read_row()) !== false) {
        if (!isset($record[2])) {
            continue;
        }

        $from = ip2long($record[0]);
        $to = ip2long($record[1]);

        if (!is_integer($from)) {
            continue;
        }
        if (!is_integer($to)) {
            continue;
        }

        if (($from < 0) || ($to < 0)) {
            attach_message('Running on 32 bit PHP, will not regenerate IP_Country.txt', 'warn');
            return;
        }

        $spreadsheet_data .= strval($from) . ',' . strval($to) . ',' . $record[2] . "\n";
    }

    if (cms_empty_safe($spreadsheet_data)) {
        if (cms_strtoupper_ascii(substr(PHP_OS, 0, 3)) == 'WIN') {
            fatal_exit('Failed to extract MaxMind IP address data - the build process is not regularly tested on Windows - you need to install certain Cygwin tools, even then it may not work');
        }

        fatal_exit('Failed to extract MaxMind IP address data');
    }

    cms_file_put_contents_safe(get_file_base() . '/data/modules/admin_stats/IP_Country.txt', $spreadsheet_data, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);

    @unlink($tmp_name_gzip);
    @unlink($tmp_name_csv);
}

function _download_latest_data_no_banning()
{
    $urls = [
        'http://www.iplists.com/google.txt',
        'http://www.iplists.com/misc.txt',
        'http://www.iplists.com/non_engines.txt',
        'https://www.cloudflare.com/ips-v4',
        'https://www.cloudflare.com/ips-v6',
    ];

    $data = '';
    foreach ($urls as $url) {
        $data .= http_get_contents($url, ['convert_to_internal_encoding' => true, 'timeout' => 30.0]);
    }

    $data = trim($data) . "\n";

    cms_file_put_contents_safe(get_file_base() . '/text/unbannable_ips.txt', $data, FILE_WRITE_FIX_PERMISSIONS | FILE_WRITE_SYNC_FILE);
}

function guid_scan_init()
{
    global $FOUND_GUID, $GUID_LANDSCAPE;
    $FOUND_GUID = [];
    $GUID_LANDSCAPE = [];

    cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

    require_code('files');
    require_code('global4');
}

function guid_scan($path)
{
    // Exceptions
    if (preg_match('#^exports/#', $path) != 0) {
        return null;
    }
    if (in_array($path, [
        'adminzone/pages/minimodules_custom/plug_guid.php', // Don't accidentally try to fix our preg code
        'sources_custom/make_release.php', // Don't accidentally try to fix our preg code
    ])) {
        return null;
    }

    global $GUID_SCAN_PATH, $GUID_ORIGINAL_CONTENTS, $GUID_ERRORS_MISSING, $GUID_ERRORS_DUPLICATE, $GUID_ERRORS_INVALID;

    $GUID_SCAN_PATH = $path;

    $GUID_ORIGINAL_CONTENTS = cms_file_get_contents_safe(get_custom_file_base() . '/' . $path, FILE_READ_LOCK);

    $GUID_ERRORS_MISSING = [];
    $GUID_ERRORS_DUPLICATE = [];
    $GUID_ERRORS_INVALID = [];
    $out = $GUID_ORIGINAL_CONTENTS;

    // Template GUIDs
    $out = preg_replace_callback("#do_template\('([^']*)', \[(\s*)'([^']+)' => ('[^']+')(.*)#", '_guid_scan_callback__template', $out); // First sweep. Bind template calls with a simple initial string value for the first parameter (the GUID hopefully) - finds us all the existing GUIDs
    $out = preg_replace_callback("#do_template\('([^']*)', \[(\s*)'([^']+)()(.*)' => #", '_guid_scan_callback__template', $out); // Second sweep. Bind all remaining template calls - allows us to look at each call

    // Internal error GUIDs
    $out = preg_replace_callback("#do_lang(_tempcode)?\('INTERNAL_ERROR'(?:\s*,\s*(.*?))?\)#", '_guid_scan_callback__internal_error', $out);

    return [
        'errors_missing' => $GUID_ERRORS_MISSING,
        'errors_duplicate' => $GUID_ERRORS_DUPLICATE,
        'errors_invalid' => $GUID_ERRORS_INVALID,
        'new_contents' => $out,
        'changes' => !empty($GUID_ERRORS_MISSING) || !empty($GUID_ERRORS_DUPLICATE) || !empty($GUID_ERRORS_INVALID),
    ];
}

function _guid_scan_callback__template($match)
{
    $full_match_line = $match[0];
    $template_name = $match[1];
    $whitespace = $match[2];
    $first_param_name = $match[3];
    $first_param_value = empty($match[4]) ? null : $match[4];
    $remaining_of_line = $match[5];

    /*
    For debugging:
    echo $full_match_line . '<br />';
    return $full_match_line;
    */

    global $GUID_LANDSCAPE, $GUID_SCAN_PATH, $GUID_ORIGINAL_CONTENTS, $FOUND_GUID, $GUID_ERRORS_MISSING, $GUID_ERRORS_DUPLICATE, $GUID_ERRORS_INVALID;

    $new_guid = str_replace('-', '', generate_guid());

    if (!array_key_exists($template_name, $GUID_LANDSCAPE)) {
        $GUID_LANDSCAPE[$template_name] = [];
    }

    $match_pos = strpos($GUID_ORIGINAL_CONTENTS, $full_match_line);
    if (is_numeric($match_pos)) {
        $line_num = substr_count(substr($GUID_ORIGINAL_CONTENTS, 0, intval($match_pos)), "\n") + 1;
    } else {
        $line_num = substr_count(substr($GUID_ORIGINAL_CONTENTS, 0), "\n") + 1;
    }

    // First sweep
    if (($first_param_value !== null) && ($first_param_name == '_GUID')) {
        $guid_value = str_replace("'", '', $first_param_value);

        // Handle invalid GUIDs
        if (!looks_like_guid($guid_value)) {
            $error_msg = 'Repair invalid GUID needed for ' . $template_name . ' in ' . $GUID_SCAN_PATH . ':' . strval($line_num);
            $GUID_ERRORS_INVALID[] = $error_msg;
            $GUID_LANDSCAPE[$template_name][] = [$GUID_SCAN_PATH, $line_num, $new_guid];
            return "do_template('" . $template_name . "', [" . $whitespace . "'_GUID' => '" . $new_guid . "'" . $remaining_of_line;
        }

        // Handle duplicated GUIDs
        if (array_key_exists($guid_value, $FOUND_GUID)) {
            $error_msg = 'Repair duplicated GUID needed for ' . $template_name . ' in ' . $GUID_SCAN_PATH . ':' . strval($line_num);
            if ($FOUND_GUID[$guid_value] != $template_name) {
                $error_msg .= ' (from a call to the ' . $FOUND_GUID[$guid_value] . ' template)';
            }
            $GUID_ERRORS_DUPLICATE[] = $error_msg;
            $GUID_LANDSCAPE[$template_name][] = [$GUID_SCAN_PATH, $line_num, $new_guid];
            return "do_template('" . $template_name . "', [" . $whitespace . "'_GUID' => '" . $new_guid . "'" . $remaining_of_line;
        }

        // Record GUIDs
        $FOUND_GUID[$guid_value] = $template_name;
        $GUID_LANDSCAPE[$template_name][] = [$GUID_SCAN_PATH, $line_num, $guid_value];
    }

    // Second sweep
    if ($first_param_value === null) {
        if ($first_param_name != '_GUID') {
            $GUID_ERRORS_MISSING[] = 'Insert needed for ' . $template_name . ' in ' . $GUID_SCAN_PATH . ':' . strval($line_num);
            $GUID_LANDSCAPE[$template_name][] = [$GUID_SCAN_PATH, $line_num, $new_guid];
            return "do_template('" . $template_name . "', [" . $whitespace . "'_GUID' => '" . $new_guid . "'," . (($whitespace !== '') ? $whitespace : ' ') . "'" . $first_param_name . $remaining_of_line . "' => ";
        }
    }

    return $full_match_line;
}

function _guid_scan_callback__internal_error($match)
{
    $full_match_line = $match[0];
    $is_tempcode = isset($match[1]) ? ($match[1] === '_tempcode') : false;
    $params = isset($match[2]) ? $match[2] : '';

    /*
    For debugging:
    echo $full_match_line . '<br />';
    return $full_match_line;
    */

    global $GUID_LANDSCAPE, $GUID_SCAN_PATH, $GUID_ORIGINAL_CONTENTS, $FOUND_GUID, $GUID_ERRORS_MISSING, $GUID_ERRORS_DUPLICATE, $GUID_ERRORS_INVALID;

    $new_guid = str_replace('-', '', generate_guid());

    if (!array_key_exists('INTERNAL_ERROR', $GUID_LANDSCAPE)) {
        $GUID_LANDSCAPE['INTERNAL_ERROR'] = [];
    }

    $match_pos = strpos($GUID_ORIGINAL_CONTENTS, $full_match_line);
    if (is_numeric($match_pos)) {
        $line_num = substr_count(substr($GUID_ORIGINAL_CONTENTS, 0, intval($match_pos)), "\n") + 1;
    } else {
        $line_num = substr_count(substr($GUID_ORIGINAL_CONTENTS, 0), "\n") + 1;
    }

    // First sweep (GUID exists)
    $inner_matches = [];
    if ((trim($params) != '') && preg_match_all('/(escape_html|comcode_escape)\(\'([\w-]*)\'/', $params, $inner_matches) != 0) {
        if (array_key_exists(0, $inner_matches[2])) { // We only care about the first parameter
            $guid_value = $inner_matches[2][0];

            // Handle invalid GUIDs
            if (!looks_like_guid($guid_value)) {
                $error_msg = 'Repair invalid GUID needed for an INTERNAL_ERROR in ' . $GUID_SCAN_PATH . ':' . strval($line_num);
                $GUID_ERRORS_INVALID[] = $error_msg;
                $GUID_LANDSCAPE['INTERNAL_ERROR'][] = [$GUID_SCAN_PATH, $line_num, $new_guid];

                // Build up our new call
                $replacement = preg_replace('/(escape_html|comcode_escape)\(\'([\w-]*)\'/', "$1('" . $new_guid . "'", $params, 1);
                return preg_replace('/(escape_html|comcode_escape)\(\'([\w-]*)\'/', $replacement, $full_match_line, 1);
            }

            // Handle duplicated GUIDs
            if (array_key_exists($guid_value, $FOUND_GUID)) {
                $error_msg = 'Repair duplicated GUID needed for an INTERNAL_ERROR in ' . $GUID_SCAN_PATH . ':' . strval($line_num);
                $GUID_ERRORS_DUPLICATE[] = $error_msg;
                $GUID_LANDSCAPE['INTERNAL_ERROR'][] = [$GUID_SCAN_PATH, $line_num, $new_guid];

                // Build up our new call
                $replacement = preg_replace('/(escape_html|comcode_escape)\(\'([\w-]*)\'/', "$1('" . $new_guid . "'", $params, 1);
                return preg_replace('/(escape_html|comcode_escape)\(\'([\w-]*)\'/', $replacement, $full_match_line, 1);
            }

            // Record GUIDs
            $FOUND_GUID[$guid_value] = 'INTERNAL_ERROR';
            $GUID_LANDSCAPE['INTERNAL_ERROR'][] = [$GUID_SCAN_PATH, $line_num, $guid_value];
        }
    }

    // Second sweep (missing GUIDs)
    if (trim($params) == '') {
        $GUID_ERRORS_MISSING[] = 'Insert needed for INTERNAL_ERROR in ' . $GUID_SCAN_PATH . ':' . strval($line_num);
        $GUID_LANDSCAPE['INTERNAL_ERROR'][] = [$GUID_SCAN_PATH, $line_num, $new_guid];

        // Build up our new call
        $ret = 'do_lang';
        if ($is_tempcode) {
            $ret .= '_tempcode';
        }
        $ret .= "('INTERNAL_ERROR', ";
        if ($is_tempcode) {
            $ret .= "escape_html('" . $new_guid . "')";
        } else {
            $ret .= "comcode_escape('" . $new_guid . "')";
        }
        $ret .= ')';
        return $ret;
    }

    return $full_match_line;
}

// See phpdoc_parser.php for functions.bin manifest building

// Also see chmod_consistency.php, and build_rewrite_rules.php

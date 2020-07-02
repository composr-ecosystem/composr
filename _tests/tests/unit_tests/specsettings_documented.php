<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class specsettings_documented_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        if (php_function_allowed('set_time_limit')) {
            @set_time_limit(0);
        }

        disable_php_memory_limit();

        require_code('files2');
    }

    public function testDirectives()
    {
        $directives_file = file_get_contents(get_file_base() . '/sources/symbols.php');

        $tempcode_tutorial = file_get_contents(get_file_base() . '/docs/pages/comcode_custom/EN/tut_tempcode.txt');

        $matches = array();
        $num_matches = preg_match_all('#^            case \'([A-Z_]+)\':#m', $directives_file, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $directive = $matches[1][$i];

            $this->assertTrue(strpos($tempcode_tutorial, $directive) !== false, 'Missing documented directive, ' . $directive);
        }
    }

    public function testSymbols()
    {
        $symbols_file = file_get_contents(get_file_base() . '/sources/symbols.php') . file_get_contents(get_file_base() . '/sources/symbols2.php');

        $tempcode_tutorial = file_get_contents(get_file_base() . '/docs/pages/comcode_custom/EN/tut_tempcode.txt');

        $matches = array();
        $num_matches = preg_match_all('#^function ecv2?_(\w+)\(\$lang, \$escaped, \$param\)#m', $symbols_file, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $symbol = $matches[1][$i];

            if (in_array($symbol, array('TERNARY'))) {
                continue;
            }

            $this->assertTrue(strpos($tempcode_tutorial, '{$' . $symbol) !== false, 'Missing documented symbol, {$' . $symbol . '}');
        }
    }

    public function testSymbolsReverse()
    {
        require_code('symbols2');

        $tempcode_tutorial = file_get_contents(get_file_base() . '/docs/pages/comcode_custom/EN/tut_tempcode.txt');

        $matches = array();
        $num_matches = preg_match_all('#\{\$(\w+)#', $tempcode_tutorial, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $symbol = $matches[1][$i];

            if (in_array($symbol, array('SYMBOL', 'F'))) {
                continue;
            }

            $this->assertTrue(function_exists('ecv_' . $symbol) || function_exists('ecv2_' . $symbol) || is_file(get_file_base() . '/sources/hooks/systems/symbols/' . $symbol . '.php'), 'Missing documented symbol, ' . $symbol);
        }
    }

    public function testInstallOptions()
    {
        $config_editor_code = file_get_contents(get_file_base() . '/config_editor.php');

        $found = array();

        $all_code = '';

        $files = get_directory_contents(get_file_base());
        foreach ($files as $f) {
            if (should_ignore_file($f, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS)) {
                continue;
            }
            if ((substr($f, -4) == '.php') && (basename($f) != 'shared_installs.php') && (strpos($f, '_tests/') === false) && (strpos($f, 'tracker/') === false) && (strpos($f, 'exports/') === false) && (strpos($f, '_custom') === false) && (strpos($f, 'exports/') === false) && ($f != '_config.php') && (basename($f) != 'errorlog.php') && (basename($f) != 'phpstub.php') && (basename($f) != 'permissioncheckslog.php')) {
                $c = file_get_contents(get_file_base() . '/' . $f);
                $all_code .= $c;
            }
        }

        $matches = array();
        $num_matches = preg_match_all('#(\$SITE_INFO|\$GLOBALS\[\'SITE_INFO\'\])\[\'([^\'"]+)\'\]#', $all_code, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $var = $matches[2][$i];
            if (
                (/*Can't just flip so simply*/$var != 'multi_lang_content') &&
                (/*string replace array*/$var != 'reps') &&
                (/*AFM*/strpos($var, 'ftp_') === false) &&
                (/*Demonstratr*/strpos($var, 'throttle_') === false) &&
                (/*Demonstratr*/strpos($var, 'custom_') === false) &&
                (/*Demonstratr*/$var != 'mysql_demonstratr_password') &&
                (/*Demonstratr*/$var != 'mysql_root_password') &&
                (/*Custom domains*/strpos($var, 'ZONE_MAPPING') === false) &&
                (/*Legacy password name*/$var != 'admin_password') &&
                (/*XML dev environment*/strpos($var, '_chain') === false) &&
                (/*forum-driver-specific*/!in_array($var, array('aef_table_prefix', 'bb_forum_number', 'ipb_table_prefix', 'mybb_table_prefix', 'phpbb_table_prefix', 'smf_table_prefix', 'stronghold_cookies', 'vb_table_prefix', 'vb_unique_id', 'vb_version', 'wowbb_table_prefix')))
            ) {
                $found[$var] = true;
            }
        }

        $found = array_keys($found);
        sort($found);

        foreach ($found as $var) {
            $this->assertTrue(strpos($config_editor_code, '\'' . $var . '\' => \'') !== false, 'Missing config_editor UI for ' . $var);
        }

        // Test the reverse too...

        $matches = array();
        $num_matches = preg_match_all('#^        \'(\w+)\' => \'#m', $config_editor_code, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $install_option = $matches[1][$i];

            if (in_array($install_option, array('gae_application', 'gae_bucket_name'))) {
                continue;
            }

            $have_found = (strpos($all_code, '$GLOBALS[\'SITE_INFO\'][\'' . $install_option . '\']') !== false) || (strpos($all_code, '$SITE_INFO[\'' . $install_option . '\']') !== false);
            $this->assertTrue($have_found, 'Documented install option not used (' . $install_option . ')');
        }

        // Test blanking out won't cause a crash...

        $config = '<' . '?php' . "\n" . 'global $SITE_INFO;' . "\n";
        foreach ($found as $key) {
            $config .= '$SITE_INFO[\'' . $key . '\'] = \'\';' . "\n";
        }
        $config .= '?' . '>';
        $old_config = file_get_contents(get_file_base() . '/_config.php');
        $config .= $old_config;
        file_put_contents(get_file_base() . '/_config.php', $config);
        $this->assertTrue(is_string(http_download_file(get_base_url() . '/index.php')));
        file_put_contents(get_file_base() . '/_config.php', $old_config);
        fix_permissions(get_file_base() . '/_config.php');
    }

    public function testValueOptions()
    {
        $codebook_text = file_get_contents(get_file_base() . '/docs/pages/comcode_custom/EN/codebook_3.txt');

        $found = array();

        $all_code = '';

        $files = get_directory_contents(get_file_base());
        foreach ($files as $f) {
            if (should_ignore_file($f, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS)) {
                continue;
            }
            if (((substr($f, -4) == '.php') || (substr($f, -4) == '.tpl') || (substr($f, -3) == '.js')) && (basename($f) != 'upgrade.php') && (basename($f) != 'shared_installs.php') && (strpos($f, '_tests/') === false) && (strpos($f, 'tracker/') === false) && (strpos($f, 'exports/') === false) && (strpos($f, '_custom/') === false) && (strpos($f, 'exports/') === false) && (basename($f) != 'errorlog.php') && (basename($f) != 'phpstub.php') && (basename($f) != 'permissioncheckslog.php')) {
                $c = file_get_contents(get_file_base() . '/' . $f);
                $all_code .= $c;
            }
        }

        $regexps = array(
            '#get\_value\(\'([^\']+)\'\)#',
            '#\{\$VALUE_OPTION[*;/\#]*,([^{},]+)\}#',
        );
        foreach ($regexps as $regexp) {
            $matches = array();
            $num_matches = preg_match_all($regexp, $all_code, $matches);
            for ($i = 0; $i < $num_matches; $i++) {
                $var = $matches[1][$i];
                if ((!file_exists(get_file_base() . '/sources/hooks/systems/disposable_values/' . $var . '.php')) && (/*LEGACY*/$var != 'ocf_version') && ($var != 'user_peak') && ($var != 'user_peak_week') && (substr($var, 0, 5) != 'last_') && (substr($var, 0, 4) != 'ftp_') && ($var != 'uses_ftp') && ($var != 'commandr_watched_chatroom') && (substr($var, 0, 8) != 'delurk__') && (substr($var, 0, 7) != 'backup_') && ($var != 'version') && ($var != 'cns_version') && ($var != 'newsletter_whatsnew') && ($var != 'newsletter_send_time') && ($var != 'site_salt') && ($var != 'sitemap_building_in_progress') && ($var != 'setupwizard_completed') && ($var != 'site_bestmember') && ($var != 'oracle_index_cleanup_last_time') && ($var != 'timezone') && ($var != 'users_online') && ($var != 'ran_once')) {// Quite a few are set in code
                    $found[$var] = true;
                }
            }
        }

        $found = array_keys($found);
        sort($found);

        foreach ($found as $var) {
            $this->assertTrue(strpos($codebook_text, '[tt]' . $var . '[/tt]') !== false, 'Missing Code Book listing for hidden value, ' . $var);
        }

        // Test the reverse too...

        $matches = array();
        $num_matches = preg_match_all('#^  - \[tt\](\w+)\[/tt\] -- #m', $codebook_text, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $value_option = $matches[1][$i];

            if (in_array($value_option, array('webdav_root'))) {
                continue;
            }

            $have_found = (strpos($all_code, '\'' . $value_option . '\'') !== false) || (strpos($all_code, '{$VALUE_OPTION,' . $value_option) !== false);
            $this->assertTrue($have_found, 'Documented value option not used (' . $value_option . ')');
        }
    }

    public function testKeepSettings()
    {
        $codebook_text = file_get_contents(get_file_base() . '/docs/pages/comcode_custom/EN/codebook_3.txt');

        $found = array();

        $all_code = '';

        $files = get_directory_contents(get_file_base());
        $files[] = 'install.php';
        foreach ($files as $f) {
            if ((should_ignore_file($f, IGNORE_BUNDLED_VOLATILE | IGNORE_CUSTOM_DIR_GROWN_CONTENTS)) && ($f != 'install.php')) {
                continue;
            }
            if (((substr($f, -4) == '.php') || (substr($f, -4) == '.tpl')) && (basename($f) != 'shared_installs.php') && (strpos($f, '_tests/') === false) && (strpos($f, 'tracker/') === false) && (strpos($f, 'exports/') === false) && (strpos($f, '_custom/') === false) && (strpos($f, 'sources/forum/') === false) && (basename($f) != 'errorlog.php') && (basename($f) != 'phpstub.php') && (basename($f) != 'permissioncheckslog.php')) {
                $c = file_get_contents($f);
                $all_code .= $c;
            }
        }

        $matches = array();
        $num_matches = preg_match_all('#get\_param(\_integer)?\(\'(keep_[^\']+)\'[,\)]#', $all_code, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $var = $matches[2][$i];
            $found[$var] = true;
        }
        $num_matches = preg_match_all('#\{\$_GET,(keep_\w+)]#', $all_code, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $var = $matches[2][$i];
            $found[$var] = true;
        }

        $found = array_keys($found);
        sort($found);

        foreach ($found as $var) {
            $this->assertTrue(strpos($codebook_text, '[tt]' . $var . '[/tt]') !== false, 'Missing Code Book listing for keep setting, ' . $var);
        }

        // Test the reverse too...

        $matches = array();
        $num_matches = preg_match_all('#^ - \[tt\](keep_\w+)\[/tt\]#m', $codebook_text, $matches);
        for ($i = 0; $i < $num_matches; $i++) {
            $keep_setting = $matches[1][$i];

            $have_found = (strpos($all_code, '\'' . $keep_setting . '\'') !== false) || (strpos($all_code, '{$_GET,' . $keep_setting) !== false);
            $this->assertTrue($have_found, 'Documented keep setting not used (' . $keep_setting . ')');
        }
    }
}
